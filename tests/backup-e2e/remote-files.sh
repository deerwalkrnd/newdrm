#!/usr/bin/env bash
# What the mock remote holds, as backups/MM-DD/<name> paths: the files
# committed on main plus the release assets (tag backup-YYYY-MM-DD).
#
#   remote-files.sh          paths
#   remote-files.sh -l       paths and sizes
#   remote-files.sh -s PATH  size of one path
#   remote-files.sh -c DIR   copy everything into DIR (a "clone" of the backups)
set -uo pipefail
R=/work/remote.git

assets() { # path size asset-id
    php -r '
        $f = "/work/releases.json";
        if (! is_file($f)) exit;
        foreach (json_decode(file_get_contents($f), true)["releases"] as $r) {
            if (! preg_match("/^backup-\d{4}-(\d\d-\d\d)$/", $r["tag_name"], $m)) continue;
            foreach ($r["assets"] as $a) echo "backups/{$m[1]}/{$a["name"]} {$a["size"]} {$a["id"]}\n";
        }'
}
list() {
    git --git-dir=$R ls-tree -r -l main 2>/dev/null | awk '{print $5, $4}'
    assets | awk '{print $1, $2}'
}

case ${1:-} in
    -l) list | sort ;;
    -s) list | awk -v p="$2" '$1 == p {print $2}' ;;
    -c)
        rm -rf "$2" && git clone -q "$R" "$2"
        assets | while read -r path _ id; do
            [[ $path == *.uploading ]] && continue
            mkdir -p "$2/$(dirname "$path")" && cp "/work/assets/$id" "$2/$path"
        done
        ;;
    *) list | awk '{print $1}' | sort ;;
esac
