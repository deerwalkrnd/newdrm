#!/usr/bin/env bash
# End-to-end checks for the in-app backups. Runs inside the app container
# after setup.sh. Talks only to the local mock GitHub (127.0.0.1:9999).
set -uo pipefail
cd /app || exit 1

REMOTE=/work/remote.git
PASS=0
FAIL=0
ok() { echo "  PASS: $*"; PASS=$((PASS + 1)); }
bad() { echo "  FAIL: $*"; FAIL=$((FAIL + 1)); }
check() { local d=$1; shift; if "$@"; then ok "$d"; else bad "$d"; fi; }
section() { echo; echo "=== $* ==="; }
mysql() { command mysql -hdb -uroot -prootpw --default-character-set=utf8mb4 "$@" 2>/dev/null; }
git_r() { git --git-dir=$REMOTE "$@"; }
# Remote state = the branch head + every finished release asset (name, size).
# ".uploading" leftovers of a failed run don't count: they're never served.
head_sha() { echo "$(git_r rev-parse -q --verify refs/heads/main 2>/dev/null || echo none) $(remote_files -l | grep -v '\.uploading ' | md5sum | cut -c1-12)"; }
remote_files() { bash /app/tests/backup-e2e/remote-files.sh "$@"; }
files() { remote_files; }
setenv() { # setenv KEY VALUE (in .env)
    if grep -q "^$1=" .env; then sed -i "s#^$1=.*#$1=$2#" .env; else echo "$1=$2" >> .env; fi
}
run_backup() { php artisan backup:run "$@" 2>&1; }
hc_last() { tail -n1 /work/hc.log | cut -d' ' -f1; }
clone_fresh() { remote_files -c /work/clone; }   # repo + release assets in one tree
mock() { curl -sS -H 'Authorization: Bearer test-token-123' "$@"; }
plant_release() { # plant_release TAG FILE... : a release like an old run's
    local tag=$1 id f; shift
    id=$(mock -X POST http://127.0.0.1:9999/repos/deerwalk-dumps/DMR-dumps/releases \
        -d "{\"tag_name\":\"$tag\",\"target_commitish\":\"main\"}" | php -r 'echo json_decode(stream_get_contents(STDIN), true)["id"];')
    for f in "$@"; do
        mock -o /dev/null -X POST -H 'Content-Type: application/octet-stream' --data-binary @"$f" \
            "http://127.0.0.1:9999/repos/deerwalk-dumps/DMR-dumps/releases/$id/assets?name=$(basename "$f")"
    done
}
tags() { php -r '$f="/work/releases.json"; if (is_file($f)) foreach (json_decode(file_get_contents($f), true)["releases"] as $r) echo $r["tag_name"], "\n";' | sort; }

# Fingerprint of a database: per-table row count + content checksum, and
# the routine / trigger definitions.
fingerprint() {
    local db=$1
    for t in $(mysql -N "$db" -e "SELECT table_name FROM information_schema.tables WHERE table_schema='$db' AND table_type='BASE TABLE' ORDER BY 1"); do
        printf '%s %s %s\n' "$t" "$(mysql -N "$db" -e "SELECT COUNT(*) FROM \`$t\`")" \
            "$(mysql -N "$db" -e "CHECKSUM TABLE \`$t\`" | awk '{print $2}')"
    done
    mysql -N -e "SELECT routine_type, routine_name, MD5(routine_definition) FROM information_schema.routines WHERE routine_schema='$db' ORDER BY 2"
    mysql -N -e "SELECT trigger_name, event_object_table, MD5(action_statement) FROM information_schema.triggers WHERE trigger_schema='$db' ORDER BY 1"
}

restore_into() { # restore_into FILE.sql.gz DB
    mysql -e "DROP DATABASE IF EXISTS $2; CREATE DATABASE $2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    gunzip -c "$1" | mysql --default-character-set=utf8mb4 "$2"
}

compare_restore() { # compare_restore FILE DB label
    restore_into "$1" "$2"
    fingerprint newdrm > /work/fp.orig
    fingerprint "$2" > /work/fp.restored
    if diff -q /work/fp.orig /work/fp.restored > /dev/null; then
        ok "$3: rows, routines and triggers identical ($(wc -l < /work/fp.orig) fingerprint lines)"
    else
        bad "$3: restore differs"; diff /work/fp.orig /work/fp.restored | head -20
    fi
}

NOW=2026-11-19T00:20:00Z   # 06:05 Asia/Kathmandu, Thursday

section "1. static checks"
out=$(find app/Services/Backup app/Console/Commands app/Http config/dump-github.php routes database/migrations tests/backup-e2e/mock-github -name '*.php' -print0 | xargs -0 -n1 php -l 2>&1 | grep -v 'No syntax errors' || true)
[[ -z $out ]] && ok "php -l on changed PHP files" || { bad "php -l"; echo "$out"; }
check "shellcheck (warnings and errors) restore.sh, e2e.sh, setup.sh, run.sh" shellcheck -S warning scripts/backup/restore.sh tests/backup-e2e/*.sh

section "2. first backup into an empty repo (mysqldump)"
out=$(run_backup --now=$NOW); rc=$?
echo "$out" | sed 's/^/    /'
check "exit 0" test $rc -eq 0
check "mysqldump used" grep -q 'dump method: mysqldump' <<<"$out"
files | sed 's/^/    remote: /'
committed=$(git_r ls-tree -r --name-only main | tr '\n' ' ')
echo "    committed: $committed"
check "only README + the .sha256 are committed" test "$committed" = "README.md backups/11-19/newdrm-060000.sql.gz.sha256 "
if [[ $(tags) == *backup-2026-11-19* ]] && remote_files | grep -qx 'backups/11-19/newdrm-060000.sql.gz'; then
    ok "the dump is attached to release backup-2026-11-19"
else
    bad "the dump is attached to release backup-2026-11-19"
fi
git_r log --format='    %an <%ae> | %s' main
check "commit author cronjob <cronjob@localhost>" test "$(git_r log -1 --format='%an <%ae>' main)" = "cronjob <cronjob@localhost>"
check "commit message" test "$(git_r log -1 --format=%s main)" = "chore(db): backup 11-19/newdrm-060000.sql.gz"
check "healthcheck: start then success" test "$(tail -n2 /work/hc.log | cut -d' ' -f1 | tr '\n' ' ')" = "start success "
check "temp dir cleaned" test -z "$(ls -A storage/app/backup-tmp 2>/dev/null)"

section "3. download through the app, restore, compare"
mkdir -p /work/dl
php -r '
require "vendor/autoload.php"; $app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = App\Services\Backup\BackupCatalog::fromConfig(); $b = $c->latest("db");
$out = fopen("/work/dl/" . $b["name"], "wb"); $c->stream($b, $out); fclose($out);
echo "    downloaded ", $b["stem"], " (", $b["size"], " bytes)\n";'
git_r show main:backups/11-19/newdrm-060000.sql.gz.sha256 > /work/dl/newdrm-060000.sql.gz.sha256
out=$(bash scripts/backup/restore.sh -o /work/dl /work/dl/newdrm-060000.sql.gz 2>&1); rc=$?
echo "$out" | sed 's/^/    /'
check "restore.sh verifies the downloaded file" test $rc -eq 0
compare_restore /work/dl/newdrm-060000.sql.gz restore_a "mysqldump backup"
check "Nepali text + emoji survived" test "$(mysql -N restore_a -e "SELECT COUNT(*) FROM employees WHERE first_name LIKE '%राम बहादुर थापा 🎉%'")" -gt 0
check "routine works after restore" test "$(mysql -N restore_a -e "SELECT backup_test_greeting('दिदी')")" = "नमस्ते, दिदी 🙏"
check "trigger works after restore" test "$(mysql -N restore_a -e "INSERT INTO backup_test_blobs(label) VALUES ('x'); SELECT note FROM backup_test_audit ORDER BY id DESC LIMIT 1")" = "थपियो ✅ x"
check "binary blob byte-identical" test "$(mysql -N restore_a -e "SELECT MD5(data) FROM backup_test_blobs WHERE label='all bytes'")" = "$(mysql -N newdrm -e "SELECT MD5(data) FROM backup_test_blobs WHERE label='all bytes'")"

section "3b. phpunit (whole suite; includes HTTP download as dmt)"
BACKUP_E2E=1 php vendor/bin/phpunit --colors=never > /work/phpunit.out 2>&1; rc=$?
tail -n 6 /work/phpunit.out | sed 's/^/    /'
check "phpunit suite" test $rc -eq 0
check "HTTP download == file in repo" cmp -s /work/http-download.gz /work/dl/newdrm-060000.sql.gz

section "4. PHP dumper fallback (mysqldump missing)"
setenv DUMP_MYSQLDUMP_BINARY /nonexistent/mysqldump
out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?   # 18:05 NPT slot
echo "$out" | sed 's/^/    /'
check "exit 0 with fallback" test $rc -eq 0
check "php dumper used" grep -q 'dump method: php' <<<"$out"
clone_fresh
bash scripts/backup/restore.sh -o /work/dl /work/clone/backups/11-19/newdrm-180000.sql.gz > /dev/null 2>&1
compare_restore /work/dl/newdrm-180000.sql.gz restore_b "php-dumper backup"
setenv DUMP_MYSQLDUMP_BINARY mysqldump

section "5. files archive bigger than the split size"
mkdir -p storage/app/employees/cv "storage/app/employees/cv/नेपाली फोल्डर" "storage/app/$(printf 'd%.0s' {1..60})/$(printf 'e%.0s' {1..60})"
head -c 9000000 /dev/urandom > storage/app/employees/cv/photo1.jpg
head -c 5000000 /dev/urandom > "storage/app/employees/cv/नेपाली फोल्डर/तस्बिर 🎉.png"
head -c 1234 /dev/urandom > "storage/app/$(printf 'd%.0s' {1..60})/$(printf 'e%.0s' {1..60})/$(printf 'f%.0s' {1..80}).bin"
printf 'hello' > storage/app/employees/cv/small.txt
setenv DUMP_SPLIT_BYTES 4194304
out=$(run_backup --with-files --now=$NOW); rc=$?
echo "$out" | sed 's/^/    /'
check "exit 0" test $rc -eq 0
files | grep _files | sed 's/^/    remote: /'
nparts=$(files | grep -c '_files-060000.tar.gz.part')
check "files archive split into $nparts parts (>1)" test "$nparts" -gt 1
check "files backup is its own commit" test "$(git_r log -1 --format=%s main)" = "chore(db): backup 11-19/newdrm_files-060000.tar.gz"
clone_fresh
rm -rf /work/files-restore && mkdir -p /work/files-restore
out=$(bash scripts/backup/restore.sh -o /work/files-restore /work/clone/backups/11-19/newdrm_files-060000.tar.gz 2>&1); rc=$?
echo "$out" | sed 's/^/    /'
check "restore.sh joins + verifies files archive" test $rc -eq 0
mkdir -p /work/files-restore/x && tar -xzf /work/files-restore/newdrm_files-060000.tar.gz -C /work/files-restore/x
( cd storage/app && find . -type f ! -path './backup-tmp/*' ! -name backup.lock -exec sha256sum {} + | sort -k2 ) > /work/f.orig
( cd /work/files-restore/x && find . -type f -exec sha256sum {} + | sort -k2 ) > /work/f.restored
if diff -q /work/f.orig /work/f.restored >/dev/null; then ok "restored files identical ($(wc -l < /work/f.orig) files, incl. Nepali/emoji names and a 200+ char path)"; else bad "files differ"; diff /work/f.orig /work/f.restored | head; fi

section "6. tampered and missing parts are caught"
cp -r /work/clone /work/tamper
printf 'X' | dd of=/work/tamper/backups/11-19/newdrm_files-060000.tar.gz.part001 bs=1 seek=100 conv=notrunc 2>/dev/null
out=$(bash scripts/backup/restore.sh -o /work/t1 /work/tamper/backups/11-19/newdrm_files-060000.tar.gz 2>&1); rc=$?
echo "    $out" | tail -n1
check "tampered part: non-zero + checksum mismatch" bash -c "[[ $rc -ne 0 ]] && grep -q 'CHECKSUM MISMATCH' <<<'$out'"
rm -rf /work/tamper && cp -r /work/clone /work/tamper
rm /work/tamper/backups/11-19/newdrm_files-060000.tar.gz.part001
out=$(bash scripts/backup/restore.sh -o /work/t2 /work/tamper/backups/11-19/newdrm_files-060000.tar.gz 2>&1); rc=$?
echo "    $out" | tail -n1
check "missing middle part: non-zero" test $rc -ne 0
rm -rf /work/tamper && cp -r /work/clone /work/tamper
last=$(ls /work/tamper/backups/11-19/newdrm_files-060000.tar.gz.part* | tail -n1); rm "$last"
out=$(bash scripts/backup/restore.sh -o /work/t3 /work/tamper/backups/11-19/newdrm_files-060000.tar.gz 2>&1); rc=$?
echo "    $out" | tail -n1
check "missing last part: non-zero + checksum mismatch" bash -c "[[ $rc -ne 0 ]] && grep -q 'CHECKSUM MISMATCH' <<<'$out'"

section "7. re-running a slot replaces it and removes leftover parts"
setenv DUMP_SPLIT_BYTES 8192
run_backup --now=$NOW > /dev/null; rc=$?
p1=$(files | grep -c 'newdrm-060000.sql.gz.part')
check "split run: $p1 db parts" bash -c "[[ $rc -eq 0 && $p1 -gt 1 ]]"
setenv DUMP_SPLIT_BYTES 1073741824
run_backup --now=2026-11-19T00:59:00Z > /dev/null; rc=$?   # 06:44 NPT, same slot
files | grep '11-19/newdrm-060000' | sed 's/^/    remote: /'
check "same slot re-run: exit 0" test $rc -eq 0
check "no .part files left for the slot" test "$(files | grep -c 'newdrm-060000.sql.gz.part')" -eq 0
check "single file + sha256 for the slot" test "$(files | grep -c '11-19/newdrm-060000')" -eq 2

section "8. pruning (today 11-19): day 30 kept, day 31 and 12-25 removed"
# Plant old backups as earlier runs would have left them.
rm -rf /work/plant && git clone -q $REMOTE /work/plant
for md in 10-20 10-19 12-25 11-20; do
    mkdir -p /work/plant/backups/$md
    echo "old $md" | gzip > /work/plant/backups/$md/newdrm-060000.sql.gz
    (cd /work/plant/backups/$md && sha256sum newdrm-060000.sql.gz > newdrm-060000.sql.gz.sha256)
done
echo "old" | gzip > /work/plant/backups/10-19/newdrm_files-060000.tar.gz.part000
(cd /work/plant && git add -A && git -c user.name=cronjob -c user.email=cronjob@localhost commit -qm 'plant old backups' && git push -q origin main)
plant_release backup-2026-10-20 /work/plant/backups/10-20/newdrm-060000.sql.gz
plant_release backup-2026-10-19 /work/plant/backups/10-19/newdrm-060000.sql.gz /work/plant/backups/10-19/newdrm_files-060000.tar.gz.part000
plant_release backup-2025-12-25 /work/plant/backups/12-25/newdrm-060000.sql.gz
plant_release backup-2025-11-20 /work/plant/backups/11-20/newdrm-060000.sql.gz
plant_release unrelated-release /work/plant/backups/10-20/newdrm-060000.sql.gz
tags | tr '\n' ' ' | sed 's/^/    releases before: /'; echo
before=$(git_r rev-list --count main)
out=$(run_backup --now=$NOW); rc=$?
echo "$out" | grep -i prune | sed 's/^/    /'
check "prune run: exit 0" test $rc -eq 0
git_r log --format='    %s' "main~$(( $(git_r rev-list --count main) - before ))..main"
check "10-20 (day 30) kept" bash -c "bash /app/tests/backup-e2e/remote-files.sh | grep -q '^backups/10-20/'"
check "10-19 (day 31) removed, incl. its files part" bash -c "! bash /app/tests/backup-e2e/remote-files.sh | grep -q '^backups/10-19/'"
check "12-25 (= last December) removed" bash -c "! bash /app/tests/backup-e2e/remote-files.sh | grep -q '^backups/12-25/'"
check "11-20 (= last November, 364 days) removed" bash -c "! bash /app/tests/backup-e2e/remote-files.sh | grep -q '^backups/11-20/'"
nprune=$(git_r log --format=%s "main~$(( $(git_r rev-list --count main) - before ))..main" | grep -c '^chore(db): prune expired backup ')
check "one prune commit per expired backup (4)" test "$nprune" -eq 4
tags | tr '\n' ' ' | sed 's/^/    releases after:  /'; echo
t_after=$(tags | tr '\n' ' ')
check "releases of 10-19, 12-25 (2025) and 11-20 (2025) deleted, 10-20 kept" bash -c "[[ '$t_after' == *backup-2026-10-20* && '$t_after' != *backup-2026-10-19* && '$t_after' != *backup-2025-12-25* && '$t_after' != *backup-2025-11-20* ]]"
check "a release not named backup-YYYY-MM-DD is never touched" bash -c "[[ '$t_after' == *unrelated-release* ]]"

section "9. failures: non-zero, /fail, remote unchanged, previous backup kept"
fail_case() { # label, then command to break things, then command to fix
    local label=$1 h_before h_after out rc
    h_before=$(head_sha)
    : > /work/hc.log
    out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?
    h_after=$(head_sha)
    echo "    $label -> exit $rc: $(echo "$out" | tail -n1 | cut -c1-200)"
    check "$label: non-zero exit" test $rc -ne 0
    check "$label: /fail pinged" grep -q '^fail ' /work/hc.log
    check "$label: remote unchanged" test "$h_before" = "$h_after"
    check "$label: previous backup still there" bash -c "bash /app/tests/backup-e2e/remote-files.sh | grep -q 'backups/11-19/newdrm-180000.sql.gz.sha256'"
}
setenv DB_PASSWORD wrong-password
fail_case "wrong DB password"
setenv DB_PASSWORD rootpw
setenv DUMP_GITHUB_TOKEN bad-token
fail_case "GitHub 401 (bad token)"
setenv DUMP_GITHUB_TOKEN test-token-123
echo '422 POST #/git/trees$#' > /work/fault
fail_case "GitHub 422 on tree create"
echo '422 PATCH #/git/refs/#' > /work/fault
fail_case "GitHub 422 on ref update"
echo '500 POST #/releases/[0-9]+/assets$#' > /work/fault
fail_case "GitHub 500 on file upload"
rm -f /work/fault
echo '502 POST #/releases/[0-9]+/assets$#' > /work/fault-once
out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?
check "a one-off GitHub 502 on an upload is retried: exit 0" test $rc -eq 0
check "  ...and the mock saw the failed attempt" grep -q 'fault-once 502' /work/requests.log

section "9b. a run that dies between the commit and the file swap is redone"
echo '422 PATCH #/releases/assets/#' > /work/fault
out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?
echo "    $(echo "$out" | tail -n1 | cut -c1-160)"
check "swap failure: non-zero exit" test $rc -ne 0
check "leftover .uploading file is on the release" bash -c "bash /app/tests/backup-e2e/remote-files.sh | grep -q '180000.sql.gz.uploading'"
rm -f /work/fault
out=$(run_backup --if-due --now=2026-11-19T12:25:00Z); rc=$?
echo "    $(echo "$out" | head -n1)"
check "next --if-due run redoes the slot (not 'up-to-date')" bash -c "[[ $rc -eq 0 ]] && grep -q 'backup ok' <<<'$(echo "$out" | head -n1)'"
check "no .uploading left; slot has its file again" bash -c "! bash /app/tests/backup-e2e/remote-files.sh | grep -q uploading && bash /app/tests/backup-e2e/remote-files.sh | grep -qx 'backups/11-19/newdrm-180000.sql.gz'"
clone_fresh
bash scripts/backup/restore.sh -o /work/dl /work/clone/backups/11-19/newdrm-180000.sql.gz > /dev/null 2>&1
check "redone slot restores with a matching checksum" test $? -eq 0
check "temp dir cleaned after failures" test -z "$(ls -A storage/app/backup-tmp 2>/dev/null)"

section "10. safety check refuses non-backup paths and non-gzip data"
php -r '
require "vendor/autoload.php"; $app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$s = App\Services\Backup\BackupService::fromConfig();
file_put_contents("/work/plain.sql.gz", "-- not gzip, just SQL\n");
file_put_contents("/work/real.gz", gzencode("x"));
$cases = [
  "wrong path"                 => ["backups/11-19/.env" => "/work/real.gz"],
  "path outside backups/"      => ["app/newdrm-060000.sql.gz" => "/work/real.gz"],
  "wrong slot hour"            => ["backups/11-19/newdrm-070000.sql.gz" => "/work/real.gz"],
  "right name, not gzip"       => ["backups/11-19/newdrm-060000.sql.gz" => "/work/plain.sql.gz"],
  "right name part, not gzip"  => ["backups/11-19/newdrm-060000.sql.gz.part000" => "/work/plain.sql.gz"],
  "bad .sha256 content"        => ["backups/11-19/newdrm-060000.sql.gz.sha256" => "/work/plain.sql.gz"],
];
foreach ($cases as $label => $files) {
  try { $s->safetyCheck($files); echo "ALLOWED $label\n"; }
  catch (App\Services\Backup\BackupException $e) { echo "refused $label: ", $e->getMessage(), "\n"; }
}
try { $s->safetyCheck(["backups/11-19/newdrm-060000.sql.gz" => "/work/real.gz"]); echo "allowed good file\n"; }
catch (Throwable $e) { echo "REFUSED good file\n"; }' > /work/safety.out
sed 's/^/    /' /work/safety.out
check "all 6 bad cases refused, good file allowed" bash -c "[[ \$(grep -c '^refused' /work/safety.out) -eq 6 && \$(grep -c '^allowed good file' /work/safety.out) -eq 1 ]]"

section "11. two runs at once: one refused by the lock"
# The mock stalls run 1's upload for 4 s; run 2 starts once run 1 has pinged
# /start, i.e. while run 1 holds the lock.
: > /work/hc.log
echo 4 > /work/delay
(run_backup --now=2026-11-19T12:20:00Z > /work/run1.out; echo $? > /work/run1.rc) &
for _ in $(seq 1 200); do grep -q '^start' /work/hc.log && break; sleep 0.05; done
(run_backup --now=2026-11-19T12:20:00Z > /work/run2.out; echo $? > /work/run2.rc) &
wait
rm -f /work/delay
check "a locked-out run sends no /fail ping" bash -c "! grep -q '^fail' /work/hc.log"
echo "    run1 exit $(cat /work/run1.rc): $(tail -n1 /work/run1.out)"
echo "    run2 exit $(cat /work/run2.rc): $(tail -n1 /work/run2.out)"
check "one succeeded, the other exited 3 (locked)" bash -c "[[ \$(sort /work/run1.rc /work/run2.rc | tr '\n' ' ') == '0 3 ' ]]"

section "12. dump bigger than memory_limit (streaming)"
mysql newdrm < tests/backup-e2e/bulk.sql
echo "    backup_test_bulk: $(mysql -N newdrm -e 'SELECT COUNT(*) FROM backup_test_bulk') rows"
for m in mysqldump php; do
    setenv DUMP_METHOD $m
    out=$(php -d memory_limit=64M artisan backup:run --now=2026-11-19T12:20:00Z 2>&1); rc=$?
    echo "$out" | grep -E 'peak|method|FAILED|failed' | sed 's/^/    /'
    check "$m with memory_limit=64M: exit 0" test $rc -eq 0
    clone_fresh
    bash scripts/backup/restore.sh -o /work/dl /work/clone/backups/11-19/newdrm-180000.sql.gz > /work/r.out 2>&1
    sz=$(stat -c %s /work/dl/newdrm-180000.sql.gz)
    echo "    compressed dump: $((sz / 1048576)) MiB, $(bash /app/tests/backup-e2e/remote-files.sh | grep -c '180000.sql.gz.part') parts; uncompressed: $(( $(gunzip -c /work/dl/newdrm-180000.sql.gz | wc -c) / 1048576 )) MiB"
    compare_restore /work/dl/newdrm-180000.sql.gz restore_big_$m "$m big dump"
done
setenv DUMP_METHOD auto
mysql newdrm -e 'DROP TABLE backup_test_bulk'

section "12b. 100 MB of uploads: one release file, then 32 MiB parts, memory_limit=64M"
setenv DUMP_SPLIT_BYTES 1073741824   # the default
head -c 104857600 /dev/urandom > storage/app/employees/cv/big.bin
out=$(php -d memory_limit=64M artisan backup:run --with-files --now=2026-11-19T12:20:00Z 2>&1); rc=$?
echo "$out" | grep -E 'peak|files|FAILED|failed' | sed 's/^/    /'
remote_files -l | grep '_files-180000' | sed 's/^/    remote: /'
sz=$(remote_files -s backups/11-19/newdrm_files-180000.tar.gz)
check "exit 0; ~110 MB archive is ONE release file, no 40 MiB limit (${sz} bytes)" bash -c "[[ $rc -eq 0 && ${sz:-0} -gt 100000000 && \$(bash /app/tests/backup-e2e/remote-files.sh | grep -c '_files-180000.tar.gz.part') -eq 0 ]]"
setenv DUMP_SPLIT_BYTES 33554432
out=$(php -d memory_limit=64M artisan backup:run --with-files --now=2026-11-19T12:30:00Z 2>&1); rc=$?
np=$(remote_files | grep -c '_files-180000.tar.gz.part')
p0=$(remote_files -s backups/11-19/newdrm_files-180000.tar.gz.part000)
check "DUMP_SPLIT_BYTES=32MiB: $np parts (>=4), part000 = 33554432 (got $p0), old single file gone" bash -c "[[ $rc -eq 0 && $np -ge 4 && '$p0' == 33554432 ]] && ! bash /app/tests/backup-e2e/remote-files.sh | grep -qx 'backups/11-19/newdrm_files-180000.tar.gz'"
setenv DUMP_SPLIT_BYTES 1073741824
clone_fresh
rm -rf /work/big && mkdir -p /work/big/x
bash scripts/backup/restore.sh -o /work/big /work/clone/backups/11-19/newdrm_files-180000.tar.gz 2>&1 | grep -E 'joined|checksum' | sed 's/^/    /'
tar -xzf /work/big/newdrm_files-180000.tar.gz -C /work/big/x
check "100 MB file restored identical" cmp -s storage/app/employees/cv/big.bin /work/big/x/employees/cv/big.bin
rm -f storage/app/employees/cv/big.bin
rm -rf /work/big /work/clone

section "12c. Sunday 06:00 slot includes uploads without --with-files"
out=$(run_backup --now=2026-11-22T00:20:00Z); rc=$?   # Sunday 06:05 NPT
check "Sunday 06:00: files archive included automatically" bash -c "[[ $rc -eq 0 ]] && bash /app/tests/backup-e2e/remote-files.sh | grep -q '^backups/11-22/newdrm_files-060000.tar.gz'"
out=$(run_backup --now=2026-11-22T12:20:00Z); rc=$?   # Sunday 18:05 NPT
check "Sunday 18:00: no files archive" bash -c "[[ $rc -eq 0 ]] && ! bash /app/tests/backup-e2e/remote-files.sh | grep -q '^backups/11-22/newdrm_files-180000'"

section "13. read-only dump user without the routine grant is caught"
mysql -e "DROP USER IF EXISTS 'bk_noroutine'@'%'; CREATE USER 'bk_noroutine'@'%' IDENTIFIED BY 'pw';
          GRANT SELECT, SHOW VIEW, TRIGGER, LOCK TABLES ON newdrm.* TO 'bk_noroutine'@'%';"
setenv DUMP_DB_USERNAME bk_noroutine; setenv DUMP_DB_PASSWORD pw
h_before=$(head_sha)
out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?
echo "$out" | tail -n1 | cut -c1-300 | sed 's/^/    /'
check "missing routine grant: refused, remote unchanged" bash -c "[[ $rc -ne 0 && '$h_before' == '$(head_sha)' ]]"
mysql -e "GRANT SHOW_ROUTINE ON *.* TO 'bk_noroutine'@'%'"
out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?
echo "$out" | sed "s/^/    /"
check "read-only user with SHOW_ROUTINE: exit 0" test $rc -eq 0
setenv DUMP_DB_USERNAME ''; setenv DUMP_DB_PASSWORD ''

section "14. signed trigger endpoint (nginx + php-fpm)"
php artisan config:clear -q
SECRET=0123456789abcdef0123456789abcdef0123456789abcdef
sign() { printf '%s.%s' "$1" "$2" | openssl dgst -sha256 -hmac "$3" -r | cut -d' ' -f1; }
post() { # ts sig -> http code
    curl -s -o /work/resp -w '%{http_code}' -X POST http://127.0.0.1/api/backup/trigger \
        -H 'Content-Type: application/json' -H "X-Backup-Timestamp: $1" -H "X-Backup-Signature: sha256=$2" --data '{}'
}
ts=$(date +%s)
check "bad signature -> 401" test "$(post "$ts" deadbeef)" = 401
old=$((ts - 400))
check "timestamp 400s old (validly signed) -> 401" test "$(post $old "$(sign $old '{}' $SECRET)")" = 401
check "wrong secret -> 401" test "$(post "$ts" "$(sign "$ts" '{}' wrong-secret-wrong-secret-wrong-secret)")" = 401
# Real "now": remove the current slot from the remote so the call has work to do.
stem=$(php -r 'require "vendor/autoload.php"; $app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Services\Backup\BackupPaths::fromConfig(); echo $p->dbStem($p->slotFor(Carbon\CarbonImmutable::now()));')
echo "    current real slot: $stem"
rm -rf /work/plant && git clone -q $REMOTE /work/plant
(cd /work/plant && git rm -q --ignore-unmatch "$stem" "$stem.sha256" && git -c user.name=t -c user.email=t@t commit -qm 'drop current slot for test' --allow-empty && git push -q origin main)
before=$(git_r rev-list --count main)
ts=$(date +%s); sig=$(sign "$ts" '{}' $SECRET)
start=$(date +%s%3N)
code=$(post "$ts" "$sig")
took=$(( $(date +%s%3N) - start ))
echo "    valid request -> HTTP $code in ${took} ms: $(cat /work/resp)"
check "valid signature -> 202 fast (<3s)" bash -c "[[ $code == 202 && $took -lt 3000 ]]"
check "replayed signature -> 401" test "$(post "$ts" "$sig")" = 401
for _ in $(seq 1 60); do [[ $(git_r rev-list --count main) -gt $before ]] && break; sleep 1; done
# Then wait until the web run has released the lock: on a Sunday 06:00
# slot it is still uploading the files archive after the first commit.
lock_free() { php -r 'exit(flock(fopen("storage/app/backup.lock", "c"), LOCK_EX | LOCK_NB) ? 0 : 1);'; }
for _ in $(seq 1 120); do lock_free && break; sleep 1; done
git_r log -1 --format='    after response: %s' main
check "backup committed after the 202 (slot $stem)" test "$(git_r rev-list --count main)" -gt "$before"
before=$(git_r rev-list --count main)
ts=$(date +%s); code=$(post "$ts" "$(sign "$ts" '{}' $SECRET)")
sleep 5
check "second call same slot -> 202 and no new commit (up to date)" bash -c "[[ $code == 202 && \$(git --git-dir=$REMOTE rev-list --count main) -eq $before ]]"
grep 'backup (github-actions)' storage/logs/laravel.log | tail -n2 | sed 's/^/    log: /'
codes=""; for _ in $(seq 1 14); do codes="$codes $(post 1 x)"; done
echo "    14 rapid bad requests:$codes"
check "rate limit kicks in (429)" grep -q 429 <<<"$codes"

section "15. no DUMP_* secrets in .env: settings saved from the Backups page"
for k in DUMP_ENABLED DUMP_GITHUB_TOKEN DUMP_TRIGGER_SECRET DUMP_HEALTHCHECK_URL; do sed -i "/^$k=/d" .env; done
grep -c '^DUMP_\(ENABLED\|GITHUB_TOKEN\|TRIGGER_SECRET\|HEALTHCHECK_URL\)=' .env | sed 's/^/    DUMP_ secrets left in .env: /'
out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?
check "without settings: refused (disabled)" bash -c "[[ $rc -ne 0 ]] && grep -q 'disabled' <<<'$out'"
NEWSECRET=fedcba9876543210fedcba9876543210fedcba9876543210
php -r 'require "vendor/autoload.php"; $app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
App\Services\Backup\BackupSettings::save(["enabled" => true, "github_token" => "test-token-123",
  "trigger_secret" => $argv[1], "healthcheck_url" => "http://127.0.0.1:9999/hc/test-uuid"]);' -- "$NEWSECRET"
check "settings file encrypted (no token in it)" bash -c "[[ -s storage/app/backup-settings.json ]] && ! grep -q test-token-123 storage/app/backup-settings.json"
: > /work/hc.log
out=$(run_backup --with-files --now=2026-11-19T12:20:00Z); rc=$?
echo "$out" | head -n1 | sed 's/^/    /'
check "backup runs from page settings alone" test $rc -eq 0
check "healthcheck URL from settings used" grep -q '^success' /work/hc.log
clone_fresh
bash scripts/backup/restore.sh -o /work/dl /work/clone/backups/11-19/newdrm_files-180000.tar.gz > /dev/null 2>&1
check "settings file is NOT in the uploads archive" bash -c "! tar -tzf /work/dl/newdrm_files-180000.tar.gz | grep -q backup-settings"
php artisan config:clear -q
stem=$(php -r 'require "vendor/autoload.php"; $app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Services\Backup\BackupPaths::fromConfig(); echo $p->dbStem($p->slotFor(Carbon\CarbonImmutable::now()));')
rm -rf /work/plant && git clone -q $REMOTE /work/plant
(cd /work/plant && git rm -q --ignore-unmatch "$stem" "$stem.sha256" && git -c user.name=t -c user.email=t@t commit -qm 'drop current slot for test' --allow-empty && git push -q origin main)
php artisan cache:clear -q   # section 14 used up the per-IP rate limit
before=$(git_r rev-list --count main)
ts=$(date +%s)
check "endpoint: old secret now refused" test "$(post "$ts" "$(sign "$ts" '{}' $SECRET)")" = 401
ts=$(date +%s)
check "endpoint: secret from settings accepted -> 202" test "$(post "$ts" "$(sign "$ts" '{}' $NEWSECRET)")" = 202
for _ in $(seq 1 60); do [[ $(git_r rev-list --count main) -gt $before ]] && break; sleep 1; done
check "web-triggered backup committed using page settings" test "$(git_r rev-list --count main)" -gt "$before"

section "16. deploy without composer install: clear error, mysqldump still works"
mv vendor/ifsnop /work/ifsnop.bak
composer dump-autoload -q 2>/dev/null
out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?
check "mysqldump path still works without the package" test $rc -eq 0
setenv DUMP_MYSQLDUMP_BINARY /nonexistent/mysqldump
out=$(run_backup --now=2026-11-19T12:20:00Z); rc=$?
echo "$out" | tail -n1 | cut -c1-250 | sed 's/^/    /'
if [[ $rc -ne 0 ]] && grep -q 'did not run composer install' <<<"$out"; then ok "no mysqldump and no package: fails with a clear message"; else bad "no mysqldump and no package: fails with a clear message"; fi
setenv DUMP_MYSQLDUMP_BINARY mysqldump
mv /work/ifsnop.bak vendor/ifsnop && composer dump-autoload -q 2>/dev/null

echo
echo "=== $PASS passed, $FAIL failed ==="
[[ $FAIL -eq 0 ]]
