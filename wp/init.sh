#!/usr/bin/env bash
# Розгортає локальний WordPress з нуля. Ідемпотентний — можна ганяти повторно.
#   cd wp && docker-compose up -d && ./init.sh
set -euo pipefail
cd "$(dirname "$0")"

wp() { docker-compose exec -T cli wp --allow-root "$@"; }
URL=http://localhost:8080

# Пароль адміністратора — випадковий, локальний, одноразовий. Лежить
# у .env.local (поза git), у виводі не світиться.
if [ ! -f .env.local ]; then
  printf 'WP_ADMIN_USER=dev\nWP_ADMIN_PASS=%s\nWP_ADMIN_EMAIL=dev@freeride.local\n' \
    "$(LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c 24)" > .env.local
  chmod 600 .env.local
fi
# shellcheck disable=SC1091
. ./.env.local

echo "→ чекаю на базу"
for _ in $(seq 1 30); do wp db check >/dev/null 2>&1 && break; sleep 2; done

if ! wp core is-installed >/dev/null 2>&1; then
  echo "→ встановлюю WordPress"
  wp core install --url="$URL" --title="FreeRide — мотошкола" \
    --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASS" \
    --admin_email="$WP_ADMIN_EMAIL" --skip-email
fi

echo "→ мова та формат адрес"
wp language core install uk >/dev/null 2>&1 || true
wp site switch-language uk
wp option update timezone_string 'Europe/Kyiv'
wp option update date_format 'j F Y'
wp option update blogdescription 'Якість перемагає кількість'
wp rewrite structure '/%postname%/' --hard
wp rewrite flush --hard

echo "→ сторонні плагіни"
# secure-custom-fields — поля (форк ACF від Automattic, безкоштовний,
#   той самий API get_field(), тож будь-який майбутній розробник удома)
# polylang — дві мови: українська в корені, англійська під /en/
# wordpress-seo — SEO-поля, які очікує сеошник
for p in secure-custom-fields polylang wordpress-seo; do
  wp plugin is-installed "$p" >/dev/null 2>&1 || wp plugin install "$p"
done
wp plugin activate secure-custom-fields polylang wordpress-seo

echo "→ прибираю зайве з коробки"
wp plugin is-installed akismet >/dev/null 2>&1 && wp plugin delete akismet || true
wp plugin is-installed hello >/dev/null 2>&1 && wp plugin delete hello || true
# Привіт-світівський допис і сторінку «Зразок» теж геть
wp post delete 1 2 --force >/dev/null 2>&1 || true

echo "→ наш плагін і тема"
wp plugin activate freeride-core
wp theme activate freeride

# Стандартні теми зносимо після активації нашої, інакше активну не видалити
for t in twentytwentythree twentytwentyfour twentytwentyfive twentytwentysix; do
  wp theme is-installed "$t" >/dev/null 2>&1 && wp theme delete "$t" || true
done

echo "→ дискусії вимкнено (коментарів на сайті немає)"
wp option update default_comment_status closed
wp option update default_ping_status closed

wp rewrite flush --hard
echo
echo "Готово. Сайт:   $URL"
echo "Адмінка:        $URL/wp-admin/  (логін і пароль — у wp/.env.local)"
