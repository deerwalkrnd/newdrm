# DRM (newdrm) database backups

The app backs **itself** up. It dumps its own MySQL database and uploads the
file to the private repo **`deerwalk-dumps/DMR-dumps`** through the
GitHub API. Nothing runs on the server by itself: there is no cron job, no
shell script, no git clone and no SSH key. Each run is started from outside:

- **at 06:05 and 18:05 Nepal time**, by a GitHub Actions workflow in the
  dumps repo that sends a signed request to the app, plus a retry two hours
  later. The app ignores the request when the slot is already backed up, so
  a late, skipped or repeated request is harmless.
- by a **backup admin** (see below), with **Run now** on the *Database Backups* page.

The copy lives on GitHub, so it survives a dead VPS, a lost hosting account
or a wiped disk.

**Who can open the Backups page:** only HR accounts whose username is in
`dump-github.admins`, which is just **`dmt`** (the DMT account) by default.
Every other user, HR included, gets a 404 and sees no menu link. The page is
at `/database-backups`, and for `dmt` it is linked from the HR menu as
*Database Backups*. To add someone, set `DUMP_ADMIN_USERNAMES=dmt,otheruser`
in `.env` (the only setting that needs `.env`).

| | |
|---|---|
| Layout | the file `newdrm-HH0000.sql.gz` is attached to that day's **release** `backup-YYYY-MM-DD`; its `backups/MM-DD/newdrm-HH0000.sql.gz.sha256` is committed |
| Uploads | `newdrm_files-HH0000.tar.gz` the same way (Sunday 06:00 slot) |
| Slots | 06:00 and 18:00 Asia/Kathmandu. A run belongs to the latest slot that has started (06:12 → `060000`, 02:00 → yesterday's `180000`). Re-running a slot replaces its file. |
| Big files | a release file can be up to 2 GiB; anything bigger than 1 GiB is split into `.part000`, `.part001`, … The `.sha256` covers the whole joined file. (Committing the files themselves would hit the API's ~40 MiB limit.) |
| Commits | `cronjob <cronjob@localhost>`, one per backup: `chore(db): backup MM-DD/<file>` (it commits the `.sha256`) |
| Retention (the roll) | 30 days: day 30 kept, day 31 pruned, one commit each: `chore(db): prune expired backup backups/MM-DD/<file>`, and that day's release is **deleted**, which really frees the space. Git history only ever holds the small checksum files, so the repo stays small for years with no manual cleanup. |
| Monitoring | the Backups page shows the last backup's age, in red when it's over 14 hours old; optional healthchecks.io pings |

> **The backups are not encrypted.** They contain employee personal data
> (addresses, phone numbers, PAN, CVs and photos in the uploads) and password hashes.
> Their only protection is that the repo is private, so:
> - keep the repo private, and set the org's base permission to *No permission*;
> - give access to as few people as possible, and review the list every quarter;
> - treat every clone and every download as sensitive.

**What is *not* in the dumps repo and needs its own backup:** the production
`.env`, especially `APP_KEY`, the DB and mail passwords. Keep a copy in the team password manager. Without `APP_KEY`, a
restored app logs everyone out and invalidates password-reset links. Keep the
GitHub token and the trigger secret in your password manager too; the server
only has them encrypted, and `backup-settings.json` is deliberately left out
of the uploads archive.

Code: `app/Services/Backup/`, `app/Console/Commands/BackupRun.php`,
`config/dump-github.php`, `app/Http/Controllers/BackupTriggerController.php`,
`app/Http/Controllers/Admin/DatabaseBackupController.php`.

---

## 1. Setup

Nothing here needs the server: no shell, no `.env` edit, no migration. Deploy
the code the way you normally do, then `dmt` enters the settings on
the **Database Backups** page. They are stored in `storage/app/backup-settings.json`,
encrypted with the app's `APP_KEY`, and are never shown again. (The same
settings can instead come from `DUMP_*` lines in `.env`, see `.env.example`;
values saved on the page win.)

### 1.1 GitHub token

Use the **existing `dwitrnd` token** that the other Deerwalk apps use; don't
make a new one. It is a fine-grained token on the institution's `dwitrnd`
account (never a personal account):

- Resource owner: **deerwalk-dumps**
- Repository access: **All repositories** (so it covers `DMR-dumps` too)
- Permissions: **Contents: Read and write**, nothing else (Metadata: read is added automatically)
- Expiration: **1 year**, with a calendar reminder a week before it expires.
  When it's renewed, paste the new one into every app's Backups page.

Know what this token means:

- **Anyone who can read the server's files and `APP_KEY` can recover it, and
  push to or delete from the dumps repo.** Git history keeps the old commits,
  so a deleted backup can still be recovered from history unless the repo
  itself is deleted, which this token can't do.
- **When the token expires, backups stop.** Every run then fails with HTTP 401
  and the Backups page turns red after 14 hours (healthchecks.io alerts too,
  if you use it). Make a new token, paste
  it on the Backups page, and press **Run now**.
- **If `APP_KEY` ever changes**, the saved settings can't be decrypted: backups
  stop (and alert) until `dmt` enters them again.

### 1.2 Settings on the Backups page

Log in as `dmt` → HR menu → **Database Backups** → **Settings**:

- **GitHub token**: from 1.1. Saving checks that it can reach the repo.
- **Trigger secret**: 32+ random characters, the same value as the dumps
  repo's `BACKUP_TRIGGER_SECRET` (1.4). Make one with
  `php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'`.
- **healthchecks.io ping URL**: from 1.3.
- **Backups on**: tick it.

The page also shows the trigger URL to put in `BACKUP_TRIGGER_URL`.

### Deploy notes

- The release adds one Composer package (`ifsnop/mysqldump-php`), the
  fallback dumper. If the deploy doesn't run `composer install`, backups still
  work with `mysqldump`; only the fallback is missing, and a run that would
  need it fails with a clear message.
- No migrations: downloads are logged to `storage/logs/backup-downloads.log`.

### Optional: a read-only database user

By default the dump uses the app's own database user. If you can create users
on the database, a read-only one is safer:

```sql
CREATE USER 'drm_backup'@'localhost' IDENTIFIED BY '<long random password>';
GRANT SELECT, SHOW VIEW, TRIGGER, LOCK TABLES ON newdrm.* TO 'drm_backup'@'localhost';

-- MySQL 8.0.20+:
GRANT SHOW_ROUTINE ON *.* TO 'drm_backup'@'localhost';
-- MariaDB, MySQL 5.x and MariaDB 5.5 instead:
GRANT SELECT ON mysql.proc TO 'drm_backup'@'localhost';
```

**Without the routine grant, mysqldump exits 0 but silently leaves out every
procedure and function.** The app catches this: it counts routines and
triggers in the dump and compares them with `information_schema`, counted as
the app's own user. A mismatch fails the run. The read-only user needs
`DUMP_DB_USERNAME` / `DUMP_DB_PASSWORD` in `.env`, so it's the one setting
that can't be made on the page.

### 1.3 Optional: healthchecks.io

Without it, a failed backup is only visible on the Backups page (the red
"overdue" warning) or as a failed run in the dumps repo's Actions tab, so
someone has to look. healthchecks.io emails you instead. To use it, create a
check named `DRM backups`:

- **Period: 12 hours**, grace: 2 hours
- Paste its ping URL into the Backups page settings
- Add the team's email or Slack as an integration

You get an alert when a run fails (`/fail`, with the error in the body) or
when no successful run has arrived for 14 hours: the server is down, the
workflow stopped, or PHP was killed mid-run.

### 1.4 The trigger workflow (in the dumps repo)

1. In `deerwalk-dumps/DMR-dumps`, add two **repository secrets**
   (Settings → Secrets and variables → Actions):
   - `BACKUP_TRIGGER_URL` = `https://<the app's host>/api/backup/trigger`
   - `BACKUP_TRIGGER_SECRET` = the same value as the trigger secret on the Backups page
2. Copy `scripts/backup/github-workflow/backup-trigger.yml` to
   `.github/workflows/backup-trigger.yml` in the dumps repo. Commit it with the
   same token or through the GitHub web UI (Add file → Create new file).
3. Once the app is deployed and its settings saved, add the repository
   **variable** `BACKUP_TRIGGER_ENABLED` = `true` (same page, *Variables*
   tab). Until then the workflow's job is skipped, so it doesn't fail on every run.
4. Actions → *backup-trigger* → **Run workflow** once, and check that it is green.

The workflow runs at 00:20, 02:20, 12:20 and 14:20 UTC (06:05 and 18:05
Nepal time, each with a retry two hours later). GitHub's scheduler can run
late or skip a run when it is busy; the retry catches up. That's about 120
Actions minutes a month. The app answers `202` at
once and does the work after the response, so the workflow never waits for
the dump.

The endpoint:

- checks `X-Backup-Signature: sha256=HMAC-SHA256(secret, "<timestamp>.<body>")`;
- refuses a timestamp more than 5 minutes off, and any signature it has seen before;
- is rate-limited to 12 requests an hour per IP;
- answers 503 when backups are off or the secret is shorter than 32 characters.

### 1.5 First run and a test restore

1. Log in as `dmt` → **Database Backups** → **Run now** (tick *include uploaded
   files* the first time). Reload after a minute. The backup is listed.
2. The page shows "Last database backup: … (a minute ago)" in green.
3. Do a test restore (section 2) on a trusted laptop.

---

## 2. Restoring

Always restore into a **new** database, never over the live one.

**From the app** (`dmt`): Database Backups → **Download** the file you need. The
app streams it from GitHub, already joined. Every download is logged with the
user, IP and time in `storage/logs/backup-downloads.log`; the page shows
the last 20.

**From GitHub** (anyone with read access to the dumps repo, e.g. when the app
is down) with `scripts/backup/restore.sh`:

```bash
# fetch the .sha256 (committed) and the file (from the day's release) with gh,
# join any parts, check the checksum, test the gzip:
scripts/backup/restore.sh -o ~/restore -g backups/11-19/newdrm-060000.sql.gz

# or files you downloaded yourself (Releases page → backup-YYYY-MM-DD → the
# file, plus the .sha256 from the repo), all in one folder:
scripts/backup/restore.sh -o ~/restore ~/Downloads/newdrm-060000.sql.gz
```

It refuses a missing part, a gap in the part numbers and a checksum mismatch.
At the end it prints the commands to load the file:

```bash
mysql -e "CREATE DATABASE newdrm_restore_YYYYMMDDHHMM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
gunzip -c ~/restore/newdrm-060000.sql.gz | mysql --default-character-set=utf8mb4 newdrm_restore_YYYYMMDDHHMM
```

Uploads: `restore.sh` the `_files-060000.tar.gz` the same way, then
`tar -xzf … -C restored-files` and copy the contents into `storage/app/`.

To rebuild the whole app on a new server: deploy the code, restore `.env`
from the password manager (same `APP_KEY`), load the database, unpack the
uploads into `storage/app/`, and run `php artisan storage:link`.

---

## 3. Quarterly restore drill

Once a quarter, someone with access to the dumps repo:

- [ ] The dumps repo has two backups for every day since the last drill
      (and healthchecks.io, if used, shows no missed or failed runs).
- [ ] Restore the latest DB backup into a scratch MySQL. Compare row counts of
      `employees`, `attendances`, `leave_requests` and `users` with production,
      and check that Nepali text displays correctly.
- [ ] Restore the latest uploads archive and open a few employee photos and CVs.
- [ ] Alternate who does it, and how: one quarter through the app's
      **Download**, the next with `restore.sh -g` straight from GitHub.
- [ ] Check the token's expiry date.
- [ ] Review who has access to the dumps repo, and remove anyone who has left
      or no longer needs it.
- [ ] Check the repo size (section 4).
- [ ] Note the date and result in the team's ops log.

---

## 4. Repo growth

Nothing to do. The dumps are release files, and deleting a release (the
prune, after 30 days) really frees its space. Git only holds the small
`.sha256` files and their commits, a few hundred bytes per backup, so the
repo stays at a few MB for years. Releases and their files don't count
towards the repo size, and GitHub doesn't limit their total size, only
each file (2 GiB).

To check:

```bash
gh repo view deerwalk-dumps/DMR-dumps --json diskUsage --jq .diskUsage   # KB, git only
gh release list -R deerwalk-dumps/DMR-dumps                               # ~30 releases
```

(This is why these backups don't commit the dump files themselves,
the way `dwitrnd/ERP_Dump` does: there, pruning deletes the files from the
branch but not from git history, so the repo grows by the full dump size on
every run. It's ~4.9 GB.)

---

## 5. How a run works

1. Take an `flock()` lock on `storage/app/backup.lock`. A second run at the
   same time exits with code 3 and sends no ping. The kernel drops the lock
   if PHP dies, so there is never a stale lock.
2. Read the repo tree. For the scheduled trigger: stop here if this slot's
   `.sha256` is committed and its file is on the day's release (and no
   half-finished upload of it is left over).
3. Ping `/start`. Dump to a temp file in `storage/app/backup-tmp/`:
   - `DUMP_METHOD=auto` uses `mysqldump` (through `proc_open`, no shell) if
     it can run, and otherwise falls back to the built-in PHP dumper
     (`ifsnop/mysqldump-php`). The reason for a fallback is in the success
     ping and the log.
   - mysqldump runs with `--single-transaction --quick --routines --triggers
     --no-tablespaces --hex-blob --default-character-set=utf8mb4` (plus
     `--set-gtid-purged=OFF` when the client supports it). The password goes
     in a 0600 temp option file, never on the command line.
4. Verify: the dump ends with `Dump completed`, and its routine and trigger
   counts match `information_schema`. Then gzip, test the gzip (CRC), hash,
   and split (over 1 GiB only). Every step goes file to file in 1 MiB
   chunks, and uploads stream the file as the request body. A 148 MiB dump
   ran with `memory_limit=64M` in testing (peak 35 MiB).
5. Safety check: refuse any name that isn't a backup artefact, any data file
   that doesn't start with a gzip header, any malformed `.sha256`, and any
   file over the split size.
6. Upload, in an order that never leaves a wrong file behind:
   1. attach the file (or parts) to the day's release `backup-YYYY-MM-DD`
      (created if needed) as `<name>.uploading`;
   2. commit the `.sha256` with the Git Data API (blob → tree → commit →
      fast-forward ref): the `chore(db): backup …` commit;
   3. delete the slot's previous file and rename the new one.

   A failure in 1 or 2 leaves the previous backup and its checksum as they
   were. A failure in 3 leaves an `.uploading` file: the slot then counts as
   not done (the next scheduled call redoes it), and the page won't offer it
   for download. Dropped connections and GitHub 5xx errors are retried
   twice (after 2 s and 6 s); a half-uploaded file is deleted before the
   retry.
7. Prune backups older than 30 days, one commit each, then delete the
   releases older than 30 days (only ones named `backup-YYYY-MM-DD`; other
   releases are never touched). Folder names have no year: on 11-19, `12-25`
   means last December. Release tags carry the year.
8. Ping success. On **any** failure: log it, ping `/fail` with the error, and
   exit non-zero. Nothing is committed until the dump has been verified, so a
   failed run leaves the remote and the previous backup untouched. Temp files
   are always deleted.

**Web runs:** they don't use the queue (no worker is needed), so trigger and Run-now
runs happen in the same PHP process after the response has been sent
(Symfony calls `fastcgi_finish_request()` under PHP-FPM), with
`ignore_user_abort()` and `set_time_limit(0)`. If the host kills long FPM
requests (`request_terminate_timeout`), a big run can be cut off without a
`/fail` ping. The Backups page still turns red after 14 hours. At today's
size a run takes seconds.

**If the server has a shell after all:** `php artisan backup:run
[--with-files] [--if-due]` does the same as the web runs (exit 0 ok,
1 failed, 3 locked).

---

## 6. Things to know

- Only MySQL/MariaDB is implemented, because that's what this app uses.
- The client must suit the server. MariaDB 10.x's mysqldump fails against a
  MariaDB 5.5 server, and MariaDB clients need the `caching_sha2_password`
  plugin to log in to MySQL 8. When mysqldump can't work, `auto` falls back to
  the PHP dumper, which works over the app's own PDO connection.
- The PHP dumper is slower than mysqldump. It reads rows with an unbuffered
  query, so memory stays flat. It dumps tables, views, triggers and routines,
  with binary columns as hex. It doesn't dump events; neither does the
  mysqldump command here, and the app has none.
- The Backups page: `/database-backups` (list, download, run now, settings). The download
  streams the file from GitHub without storing it on the server.
- On GitHub, backups appear in two places: the commits (one per backup and
  per prune, by `cronjob`) and the **Releases** page (one release per day,
  holding that day's files). Releases are marked not-latest; people watching
  the repo may get a notification for each new one.

## 7. Testing

`tests/backup-e2e/run.sh` (needs Docker) starts MySQL 8.0 and PHP 8.1
(PHP-FPM + nginx; 8.1 is the lowest PHP `composer.lock` installs on), seeds Nepali text, emoji, binary data, a trigger, a
procedure and a function, and points the app at a local fake of the GitHub API
backed by a bare git repo, with releases. It never contacts GitHub. It
checks:
- backup → download → restore, with both dump methods;
- splitting, plus tampered and missing parts;
- the roll: prune commits plus deleted releases, day 30 kept, unrelated
  releases untouched;
- slot re-runs, and a run that dies half-way through the swap;
- failures (`/fail`, previous backup kept) and a one-off GitHub 502 retried;
- the safety check, the lock, a dump larger than `memory_limit`;
- the signed endpoint.

`tests/backup-e2e/real-github.sh` runs the same flow against a throwaway
private repo on real GitHub (it refuses any repo without "test" in its
name), using seed data only. That run is how the ~40 MiB limit for
committed files was found.
