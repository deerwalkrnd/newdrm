#!/usr/bin/env bash
# Host side: build, start, set up and run the end-to-end backup tests.
set -euo pipefail
cd "$(dirname "$0")"
docker compose up -d --build
docker compose exec -T app bash /src/tests/backup-e2e/setup.sh
docker compose exec -T app bash /app/tests/backup-e2e/e2e.sh
