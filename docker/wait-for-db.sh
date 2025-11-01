#!/usr/bin/env bash
# Wait for MySQL using credentials read from .env when env vars are not set.
set -e

ENV_FILE="/var/www/html/.env"

get_env_from_file() {
  local key="$1"
  if [ -f "$ENV_FILE" ]; then
    local raw
    raw=$(grep -m1 -E "^${key}=" "$ENV_FILE" || true)
    raw=${raw#${key}=}
    raw=$(echo "$raw" | sed -E 's/^"(.*)"$/\1/; s/^'\''(.*)'\''$/\1/')
    echo "$raw"
  fi
}

DB_HOST="${DB_HOST:-$(get_env_from_file DB_HOST)}"
DB_PORT="${DB_PORT:-$(get_env_from_file DB_PORT)}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USERNAME:-$(get_env_from_file DB_USERNAME)}"
DB_PASS="${DB_PASSWORD:-$(get_env_from_file DB_PASSWORD)}"
DB_NAME="${DB_DATABASE:-$(get_env_from_file DB_DATABASE)}"

RETRIES="${WAIT_DB_RETRIES:-120}"
SLEEP_SEC="${WAIT_DB_SLEEP:-2}"

echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT} (max $((RETRIES * SLEEP_SEC))s)..."
i=0
while true; do
  echo "Attempt=$i: resolving ${DB_HOST}..."
  php -r "echo 'php gethostbyname: '.gethostbyname('${DB_HOST}').PHP_EOL;"

  # TCP socket check (1s timeout)
  if php -r "try { \$sock = @fsockopen('${DB_HOST}', ${DB_PORT}, \$errno, \$errstr, 1); if (\$sock) { fclose(\$sock); echo 'tcp ok'.PHP_EOL; exit(0);} else { echo 'tcp failed'.PHP_EOL; exit(1);} } catch (Exception \$e) { echo 'tcp exception'.PHP_EOL; exit(1); }" >/dev/null 2>&1; then
    echo "TCP reachable for ${DB_HOST}:${DB_PORT}"
  else
    echo "TCP check failed for ${DB_HOST}:${DB_PORT}"
    i=$((i+1))
    if [ "$i" -ge "$RETRIES" ]; then
      echo "Timeout waiting for MySQL at ${DB_HOST}:${DB_PORT}" >&2
      exit 1
    fi
    sleep "${SLEEP_SEC}"
    continue
  fi

  # Try PDO connection with credentials read from .env or env
  php -r "try { \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}'); echo 'PDO ok'.PHP_EOL; exit(0); } catch (Exception \$e) { echo 'PDO error: '.\$e->getMessage().PHP_EOL; exit(1); }" && break || true

  i=$((i+1))
  if [ "$i" -ge "$RETRIES" ]; then
    echo "Timeout waiting for MySQL at ${DB_HOST}:${DB_PORT}" >&2
    exit 1
  fi

  sleep "${SLEEP_SEC}"
done

echo "MySQL is ready at ${DB_HOST}:${DB_PORT}"
exit 0
