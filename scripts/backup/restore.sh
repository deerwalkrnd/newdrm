#!/usr/bin/env bash
# Rebuild a backup from the dumps repo on a trusted machine, check it, and
# print the commands that load it into a NEW database.
#
#   restore.sh [-o OUTDIR] -g [-R owner/repo] [-b branch] REPO_STEM
#       fetch with gh (needs read access): the .sha256 from the repo and the
#       file from that day's release, e.g.
#       -g backups/11-19/newdrm-060000.sql.gz
#
#   restore.sh [-o OUTDIR] LOCAL_STEM
#       files you already have in one folder (the file or its .partNNN
#       parts, plus the .sha256), e.g. ./dl/newdrm-060000.sql.gz
#
# The "Download" button on the Backups page on the app gives you the joined file
# already; check it against the .sha256 with:  restore.sh FILE
set -euo pipefail

repo=deerwalk-dumps/DMR-dumps
branch=main
outdir=.
from_github=0

usage() { sed -n '2,15p' "$0" | sed 's/^# \{0,1\}//'; exit 2; }
die() { echo "restore: $*" >&2; exit 1; }

while getopts 'o:gR:b:h' opt; do
    case $opt in
        o) outdir=$OPTARG ;;
        g) from_github=1 ;;
        R) repo=$OPTARG ;;
        b) branch=$OPTARG ;;
        *) usage ;;
    esac
done
shift $((OPTIND - 1))
[[ $# -eq 1 ]] || usage
stem=$1
name=$(basename "$stem")

case $name in
    *.sql.gz | *.tar.gz) ;;
    *) die "expected a .sql.gz or .tar.gz name, got $name" ;;
esac

mkdir -p "$outdir"
out=$outdir/$name

if ((from_github)); then
    # The .sha256 is committed under backups/MM-DD/; the file (or its parts)
    # is attached to that day's release, tagged backup-YYYY-MM-DD.
    command -v gh >/dev/null || die "gh is not installed"
    work=$(mktemp -d)
    trap 'rm -rf "$work"' EXIT
    md=$(basename "$(dirname "$stem")")
    [[ $md =~ ^[0-9]{2}-[0-9]{2}$ ]] || die "expected backups/MM-DD/<file>, got $stem"
    echo "fetching $stem.sha256" >&2
    gh api -H 'Accept: application/vnd.github.raw+json' \
        "repos/$repo/contents/$stem.sha256?ref=$branch" > "$work/$name.sha256" \
        || die "no $stem.sha256 in $repo (pruned, or never uploaded?)"
    tag=$(gh release list -R "$repo" --limit 100 --json tagName --jq '.[].tagName' \
        | grep -E "^backup-[0-9]{4}-$md$" | sort -r | head -n1 || true)
    [[ -n $tag ]] || die "no release backup-YYYY-$md in $repo"
    echo "fetching $name from release $tag" >&2
    gh release download "$tag" -R "$repo" -D "$work" -p "$name" -p "$name.part[0-9][0-9][0-9]" \
        || die "cannot download $name from release $tag"
    src=$work/$name
else
    src=$stem
fi

shafile=$src.sha256
[[ -f $shafile ]] || die "missing $shafile"

# Join parts (or take the single file). A gap in the numbering is an error.
if [[ -f $src.part000 ]]; then
    tmp=$out.joining
    : > "$tmp"
    i=0
    while [[ -f $(printf '%s.part%03d' "$src" "$i") ]]; do
        cat "$(printf '%s.part%03d' "$src" "$i")" >> "$tmp"
        i=$((i + 1))
    done
    shopt -s nullglob
    all=("$src".part[0-9][0-9][0-9])
    shopt -u nullglob
    ((${#all[@]} == i)) || die "parts are not numbered 000..$(printf '%03d' $((i - 1))) without gaps: a part is missing"
    mv "$tmp" "$out"
    echo "joined $i parts" >&2
elif [[ -f $src ]]; then
    [[ $src -ef $out ]] || cp "$src" "$out"
else
    die "neither $src nor $src.part000 exists"
fi

want=$(awk '{print $1}' "$shafile")
got=$(sha256sum "$out" | awk '{print $1}')
if [[ $want != "$got" ]]; then
    rm -f "$out"
    die "CHECKSUM MISMATCH: expected $want, got $got (a part is damaged or missing)"
fi
echo "checksum OK ($got)" >&2
gzip -t "$out" || die "gzip test failed"

echo
echo "Ready: $out"
case $name in
    *.sql.gz)
        db="newdrm_restore_$(date +%Y%m%d%H%M)"
        cat <<EOT
Load it into a NEW database (never over the live one):

  mysql -e "CREATE DATABASE $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
  gunzip -c '$out' | mysql --default-character-set=utf8mb4 $db
EOT
        ;;
    *.tar.gz)
        cat <<EOT
Unpack into a NEW folder, then copy into storage/app:

  mkdir restored-files && tar -xzf '$out' -C restored-files
EOT
        ;;
esac
