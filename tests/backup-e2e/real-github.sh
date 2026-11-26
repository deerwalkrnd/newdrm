#!/usr/bin/env bash
# Real-GitHub check, run inside the app container after setup.sh, against a
# THROWAWAY private repo with seed data only. Never point this at a real
# dumps repo.
#
#   REAL_REPO=deerwalk-dumps/Test_dump REAL_TOKEN=... bash real-github.sh
set -uo pipefail
cd /app || exit 1

: "${REAL_REPO:?set REAL_REPO}" "${REAL_TOKEN:?set REAL_TOKEN}"
# Safety net: only a repo with "test" in its name, never a real dumps repo.
shopt -s nocasematch
if [[ $REAL_REPO != */*test* || $REAL_REPO == */DMR-dumps ]]; then
    echo "refusing: REAL_REPO must be a throwaway repo with 'test' in its name"; exit 2
fi
shopt -u nocasematch

PASS=0 FAIL=0
ok() { echo "  PASS: $*"; PASS=$((PASS + 1)); }
bad() { echo "  FAIL: $*"; FAIL=$((FAIL + 1)); }
check() { local d=$1; shift; if "$@"; then ok "$d"; else bad "$d"; fi; }
section() { echo; echo "=== $* ==="; }
mysql() { command mysql -hdb -uroot -prootpw --default-character-set=utf8mb4 "$@" 2>/dev/null; }
setenv() { if grep -q "^$1=" .env; then sed -i "s#^$1=.*#$1=$2#" .env; else echo "$1=$2" >> .env; fi; }
api() { curl -sS -H "Authorization: Bearer $REAL_TOKEN" -H 'Accept: application/vnd.github+json' "https://api.github.com/repos/$REAL_REPO$1"; }
committed() { api "/git/trees/main?recursive=1" | php -r '$d=json_decode(stream_get_contents(STDIN),true); foreach(($d["tree"]??[]) as $e) if($e["type"]==="blob") echo $e["path"], "\n";'; }
# release assets as "TAG NAME SIZE"
assets() { api "/releases?per_page=100" | php -r 'foreach((array)json_decode(stream_get_contents(STDIN),true) as $r) foreach(($r["assets"]??[]) as $a) echo $r["tag_name"], " ", $a["name"], " ", $a["size"], "\n";'; }
tags() { api "/releases?per_page=100" | php -r 'foreach((array)json_decode(stream_get_contents(STDIN),true) as $r) echo $r["tag_name"], "\n";'; }
commits() { api "/commits?per_page=30" | php -r 'foreach((array)json_decode(stream_get_contents(STDIN),true) as $c) if(isset($c["commit"])) echo $c["commit"]["author"]["name"], " | ", strtok($c["commit"]["message"], "\n"), "\n";'; }
run() { php artisan backup:run "$@" 2>&1; }

command -v gh >/dev/null || apk add --no-cache github-cli >/dev/null 2>&1
export GH_TOKEN=$REAL_TOKEN

owner=${REAL_REPO%/*}; repo=${REAL_REPO#*/}
setenv DUMP_GITHUB_API_URL https://api.github.com
setenv DUMP_GITHUB_OWNER "$owner"
setenv DUMP_GITHUB_REPO "$repo"
setenv DUMP_GITHUB_TOKEN "$REAL_TOKEN"
setenv DUMP_HEALTHCHECK_URL ''
setenv DUMP_SPLIT_BYTES 1073741824
php artisan config:clear -q

section "0. starting state"
code=$(curl -sS -o /dev/null -w '%{http_code}' -H "Authorization: Bearer $REAL_TOKEN" "https://api.github.com/repos/$REAL_REPO/git/ref/heads/main")
echo "    GET ref -> HTTP $code (409/404 = empty repo)"
if [[ $code == 409 || $code == 404 ]]; then ok "repo starts empty"; else echo "    note: repo not empty (re-run); the empty-repo bootstrap is only tested on a fresh repo"; fi

section "1. backup: .sha256 committed, dump attached to release backup-2026-11-19"
out=$(run --now=2026-11-19T00:20:00Z); rc=$?
echo "$out" | sed 's/^/    /'
check "exit 0" test $rc -eq 0
c=$(committed); a=$(assets)
echo "$c" | sed 's/^/    committed: /'
echo "$a" | sed 's/^/    release asset: /'
if grep -qx 'backups/11-19/newdrm-060000.sql.gz.sha256' <<<"$c"; then ok "the .sha256 is committed"; else bad "the .sha256 is committed"; fi
if ! grep -q '^backups/11-19/.*gz$' <<<"$c"; then ok "no dump data committed to git for 11-19"; else bad "no dump data committed to git for 11-19"; fi
if grep -q '^backup-2026-11-19 newdrm-060000.sql.gz ' <<<"$a"; then ok "dump attached to release backup-2026-11-19"; else bad "dump attached to release backup-2026-11-19"; fi
log=$(commits)
check "commit 'chore(db): backup 11-19/newdrm-060000.sql.gz' by cronjob" grep -q '^cronjob | chore(db): backup 11-19/newdrm-060000.sql.gz$' <<<"$log"

section "2. 100 MB uploads archive goes up as ONE release file (no 40 MiB limit)"
mkdir -p storage/app/employees/cv "storage/app/employees/cv/नेपाली"
head -c 104857600 /dev/urandom > storage/app/employees/cv/big.bin
head -c 300000 /dev/urandom > "storage/app/employees/cv/नेपाली/तस्बिर 🎉.png"
start=$(date +%s)
out=$(php -d memory_limit=64M artisan backup:run --with-files --now=2026-11-19T00:20:00Z 2>&1); rc=$?
echo "$out" | grep -E 'peak|files|failed' | sed 's/^/    /'
echo "    took $(( $(date +%s) - start )) s"
check "exit 0 with memory_limit=64M" test $rc -eq 0
sz=$(assets | awk '$2 == "newdrm_files-060000.tar.gz" {print $3}')
check "archive is one release file of ${sz:-0} bytes (> 100 MB)" test "${sz:-0}" -gt 100000000

section "3. download through the app, and restore.sh -g with gh"
mkdir -p /work/real && rm -rf /work/real/*
php -r '
require "vendor/autoload.php"; $app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = App\Services\Backup\BackupCatalog::fromConfig();
foreach (["db", "files"] as $k) { $b = $c->latest($k); $o = fopen("/work/real/" . $b["name"], "wb"); $c->stream($b, $o); fclose($o);
  echo "    app download: ", $b["stem"], " (", $b["size"], " bytes, ", $b["parts"], " part(s))\n"; }'
for f in newdrm-060000.sql.gz newdrm_files-060000.tar.gz; do
    curl -sS -H "Authorization: Bearer $REAL_TOKEN" -H 'Accept: application/vnd.github.raw+json' \
        "https://api.github.com/repos/$REAL_REPO/contents/backups/11-19/$f.sha256" > "/work/real/$f.sha256"
    check "$f: app download matches the committed .sha256" bash -c "cd /work/real && sha256sum -c $f.sha256 >/dev/null"
done
mkdir -p /work/real/gh
out=$(bash scripts/backup/restore.sh -R "$REAL_REPO" -o /work/real/gh -g backups/11-19/newdrm-060000.sql.gz 2>&1); rc=$?
echo "$out" | grep -E 'fetching|checksum|Ready|restore:' | sed 's/^/    /'
check "restore.sh -g (gh, from the release) verifies the checksum" test $rc -eq 0
mysql -e 'DROP DATABASE IF EXISTS real_restore; CREATE DATABASE real_restore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
gunzip -c /work/real/gh/newdrm-060000.sql.gz | mysql real_restore
fp() { for t in $(mysql -N "$1" -e "SELECT table_name FROM information_schema.tables WHERE table_schema='$1' AND table_type='BASE TABLE' ORDER BY 1"); do echo "$t $(mysql -N "$1" -e "SELECT COUNT(*) FROM \`$t\`") $(mysql -N "$1" -e "CHECKSUM TABLE \`$t\`" | awk '{print $2}')"; done; }
fp newdrm > /work/real/fp.a; fp real_restore > /work/real/fp.b
check "restored DB identical to the source ($(wc -l < /work/real/fp.a) tables, rows + checksums)" diff -q /work/real/fp.a /work/real/fp.b
mkdir -p /work/real/x && tar -xzf /work/real/newdrm_files-060000.tar.gz -C /work/real/x
check "restored 100 MB file identical" cmp -s storage/app/employees/cv/big.bin /work/real/x/employees/cv/big.bin
check "restored Nepali/emoji file identical" cmp -s "storage/app/employees/cv/नेपाली/तस्बिर 🎉.png" "/work/real/x/employees/cv/नेपाली/तस्बिर 🎉.png"

section "4. re-run a slot split into parts, then whole again: old files swapped out"
setenv DUMP_SPLIT_BYTES 8192
out=$(run --now=2026-11-19T00:40:00Z); rc=$?
a=$(assets)
n=$(grep -c '^backup-2026-11-19 newdrm-060000.sql.gz.part' <<<"$a")
if [[ $rc -eq 0 && $n -gt 1 ]] && ! grep -q ' newdrm-060000.sql.gz ' <<<"$a"; then ok "split re-run: $n parts on the release, old single file gone"; else bad "split re-run ($rc, $n parts)"; fi
setenv DUMP_SPLIT_BYTES 1073741824
out=$(run --now=2026-11-19T00:50:00Z); rc=$?
a=$(assets)
if [[ $rc -eq 0 ]] && grep -q ' newdrm-060000.sql.gz ' <<<"$a" && ! grep -qE 'sql.gz.part|uploading' <<<"$a"; then ok "whole re-run: one file, no parts, no .uploading"; else bad "whole re-run ($rc)"; fi

section "5. the roll: 31 days later the 11-19 backups and their release are gone"
before_sha=$(api "/commits?per_page=1" | php -r 'echo json_decode(stream_get_contents(STDIN), true)[0]["sha"] ?? "";')
out=$(run --now=2026-12-20T00:20:00Z); rc=$?
echo "$out" | grep -iE 'failed' | cut -c1-200 | sed 's/^/    /' 
echo "$out" | grep -i pruned | sed 's/^/    /'
check "exit 0" test $rc -eq 0
if ! grep -q '^backups/11-19/' <<<"$(committed)"; then ok "backups/11-19 no longer committed"; else bad "backups/11-19 no longer committed"; fi
t=$(tags | tr '\n' ' ')
echo "    releases now: $t"
if [[ $t != *backup-2026-11-19* ]]; then ok "release backup-2026-11-19 deleted (its files freed)"; else bad "release backup-2026-11-19 deleted"; fi
check "tag backup-2026-11-19 deleted" test "$(curl -sS -o /dev/null -w '%{http_code}' -H "Authorization: Bearer $REAL_TOKEN" "https://api.github.com/repos/$REAL_REPO/git/ref/tags/backup-2026-11-19")" = 404
# Only the commits this run made.
log=$(api "/commits?per_page=30" | php -r '$stop=$argv[1]; foreach((array)json_decode(stream_get_contents(STDIN),true) as $c) { if (($c["sha"]??"") === $stop) break; echo $c["commit"]["author"]["name"], " | ", strtok($c["commit"]["message"], "
"), "
"; }' -- "$before_sha")
echo "$log" | sed 's/^/    this run: /'
np=$(grep -c '^cronjob | chore(db): prune expired backup backups/11-19/' <<<"$log")
check "one prune commit per expired backup (2), by cronjob (got $np)" test "$np" -eq 2

rm -f storage/app/employees/cv/big.bin
setenv DUMP_GITHUB_TOKEN ''
echo
echo "=== $PASS passed, $FAIL failed ==="
[[ $FAIL -eq 0 ]]
