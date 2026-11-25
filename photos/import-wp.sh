#!/usr/bin/env bash
# Заливає кадри з docs/assets/photos у медіатеку WordPress і розставляє їх
# за wp-map.json. Ідемпотентний: вкладення шукається за метою _fr_shot.
#
#   ./import-wp.sh                            # локальний стенд у wp/
#   REMOTE=root@78.46.233.40 ./import-wp.sh   # тестова копія на сервері
set -euo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
ROOT="$(dirname "$HERE")"
SRC="$ROOT/docs/assets/photos"
[ -d "$SRC" ] || { echo "Немає $SRC — спершу generate.py, потім apply.py" >&2; exit 1; }

if [ -n "${REMOTE:-}" ]; then
  echo "→ Копіюю кадри на $REMOTE"
  ssh "$REMOTE" 'mkdir -p /opt/freeride/photos'
  scp -q "$SRC"/*.jpg "$HERE/wp-map.json" "$HERE/import.php" "$HERE/import-wp.sh" \
      "$REMOTE:/opt/freeride/photos/"
  # на сервері стек піднятий прод-оверлеєм: локальний override публікує
  # порт на 0.0.0.0, а Docker пише власні правила в iptables повз фаєрвол
  ssh "$REMOTE" 'cd /opt/freeride && SRC_OVERRIDE=/opt/freeride/photos \
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.prod.yml" \
    bash photos/import-wp.sh'
  exit 0
fi

[ -n "${SRC_OVERRIDE:-}" ] && SRC="$SRC_OVERRIDE"
cd "$ROOT/wp"
if docker compose version >/dev/null 2>&1; then DC="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then DC="docker-compose"
else echo "Не знайдено ні docker compose, ні docker-compose" >&2; exit 1; fi

# uploads підмонтовано в контейнер, тож кладемо файли через файлову систему хоста
STAGE="$ROOT/wp/content/uploads/_in"
mkdir -p "$STAGE"
cp -f "$SRC"/*.jpg "$STAGE/"
cp -f "$HERE/wp-map.json" "$STAGE/"
cp -f "$HERE/import.php" "$STAGE/"
echo "→ $(ls -1 "$STAGE"/*.jpg | wc -l | tr -d ' ') кадрів підготовлено"

$DC ${COMPOSE_FILES:-} run --rm -T cli wp eval-file \
  /var/www/html/wp-content/uploads/_in/import.php

rm -f "$STAGE"/import.php "$STAGE"/wp-map.json
echo "Готово. Проміжні файли лишилися в wp/content/uploads/_in — можна видалити."
