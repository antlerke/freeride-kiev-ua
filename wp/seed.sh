#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
# Compose буває плагіном (`docker compose`) і окремим бінарником
# (`docker-compose`). Скрипти мають працювати з обома.
if docker compose version >/dev/null 2>&1; then
  DC="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
  DC="docker-compose"
else
  echo "Не знайдено ні docker compose, ні docker-compose" >&2; exit 1
fi
COMPOSE_FILES=${COMPOSE_FILES:-}
$DC $COMPOSE_FILES cp seed.php cli:/tmp/seed.php
$DC $COMPOSE_FILES exec -T cli wp --allow-root eval-file /tmp/seed.php
