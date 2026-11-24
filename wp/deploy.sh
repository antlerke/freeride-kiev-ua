#!/usr/bin/env bash
# Розгортання на сервері. Запускається НА СЕРВЕРІ, з теки репозиторію.
#
#   ssh root@сервер 'cd /opt/freeride/wp && ./deploy.sh'
#
# Ідемпотентний: можна ганяти після кожного git pull.
set -euo pipefail
cd "$(dirname "$0")"

if [ ! -f .env ]; then
  echo "Немає .env — скопіюйте .env.example і заповніть." >&2
  exit 1
fi

# Серверна збірка: базовий файл плюс prod. Локальний override навмисно
# не підхоплюється — він відкриває порт назовні.
# Compose буває плагіном (`docker compose`) і окремим бінарником
# (`docker-compose`). Скрипти мають працювати з обома.
if docker compose version >/dev/null 2>&1; then
  DC="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
  DC="docker-compose"
else
  echo "Не знайдено ні docker compose, ні docker-compose" >&2; exit 1
fi

export COMPOSE_FILES="-f docker-compose.yml -f docker-compose.prod.yml"

# Теку завантажень git не несе, а www-data у контейнері має в неї писати
mkdir -p content/uploads
chown -R 33:33 content/uploads

echo "→ образи"
$DC $COMPOSE_FILES pull --quiet
echo "→ контейнери"
$DC $COMPOSE_FILES up -d

./init.sh
./seed.sh

echo
echo "Готово. Перевірити зсередини сервера:"
. ./.env
echo "  curl -sI -H 'Host: ${SITE_URL#http://}' http://127.0.0.1:${WP_PORT:-8081}/ | head -1"
