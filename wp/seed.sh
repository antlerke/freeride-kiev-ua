#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
docker-compose cp seed.php cli:/tmp/seed.php
docker-compose exec -T cli wp --allow-root eval-file /tmp/seed.php
