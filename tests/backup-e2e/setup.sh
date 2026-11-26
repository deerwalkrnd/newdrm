#!/usr/bin/env bash
# Runs inside the app container: copies the source, installs, starts the
# mock GitHub, php-fpm and nginx, migrates and seeds.
set -euo pipefail

pkill -f router.php || true
pkill -x nginx || true
pkill -f php-fpm || true
sleep 1
rm -rf /app /work && mkdir -p /app /work
# MariaDB client vs MySQL 8's self-signed cert (the app's own cnf does the same).
printf '[client-mariadb]\nskip-ssl-verify-server-cert\n' > /root/.my.cnf
tar -C /src --exclude=./vendor --exclude=./node_modules --exclude=./.env --exclude=./.env.swp \
    --exclude=./storage/logs/'*.log' --exclude=./storage/framework/views/'*.php' \
    --exclude=./storage/app/backup-tmp --exclude=./.git -cf - . | tar -C /app -xf -
cd /app
composer install --no-interaction --no-progress -q
cp tests/backup-e2e/env.testing .env
php artisan key:generate -q

# mock GitHub + healthchecks
git init -q --bare /work/remote.git
: > /work/requests.log
: > /work/hc.log
MOCK_REPO=/work/remote.git MOCK_DIR=/work MOCK_TOKEN=test-token-123 PHP_CLI_SERVER_WORKERS=4 \
    nohup php -d post_max_size=0 -d memory_limit=-1 -S 127.0.0.1:9999 tests/backup-e2e/mock-github/router.php > /work/mock.out 2>&1 &

# php-fpm + nginx, all as root (test container only)
sed -i 's/^user = .*/user = root/; s/^group = .*/group = root/' /usr/local/etc/php-fpm.d/www.conf
php-fpm -R -D
mkdir -p /run/nginx
cp tests/backup-e2e/nginx.conf /etc/nginx/http.d/default.conf
nginx

mysql() { command mysql -hdb -uroot -prootpw --default-character-set=utf8mb4 "$@" 2>/dev/null; }
mysql -e 'DROP DATABASE IF EXISTS newdrm; CREATE DATABASE newdrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
php artisan migrate --seed --force -q
mysql newdrm < tests/backup-e2e/seed.sql
echo "setup done: $(mysql -N newdrm -e 'SELECT COUNT(*) FROM employees') employees, $(mysql -N newdrm -e 'SELECT COUNT(*) FROM users') users"
