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
  # Кадри кладемо поза робочою копією: інакше наступний `git pull`
  # на сервері падає через «локальні зміни». Скрипт і карту сервер
  # бере зі свого ж репозиторію, тож їх копіювати не треба.
  echo "→ Копіюю кадри на $REMOTE"
  ssh "$REMOTE" 'mkdir -p /var/tmp/freeride-photos && rm -f /var/tmp/freeride-photos/*.jpg'
  scp -q "$SRC"/*.jpg "$REMOTE:/var/tmp/freeride-photos/"
  # на сервері стек піднятий прод-оверлеєм: локальний override публікує
  # порт на 0.0.0.0, а Docker пише власні правила в iptables повз фаєрвол
  ssh "$REMOTE" 'cd /opt/freeride && SRC_OVERRIDE=/var/tmp/freeride-photos \
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

# саме exec, а не run: у сервісу cli entrypoint "tail -f /dev/null",
# тож run дописав би аргументи до tail і контейнер просто висів би
$DC ${COMPOSE_FILES:-} exec -T cli wp --allow-root eval-file \
  /var/www/html/wp-content/uploads/_in/import.php

rm -f "$STAGE"/import.php "$STAGE"/wp-map.json
echo "Готово. Проміжні файли лишилися в wp/content/uploads/_in — можна видалити."
