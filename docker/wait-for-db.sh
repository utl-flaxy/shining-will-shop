#!/bin/sh
set -e

echo "⏳ Waiting for MySQL to be ready..."

for i in $(seq 1 ${WAIT_DB_RETRIES:-60}); do
    if mysql -h"${DB_HOST:-mysql}" -u"${DB_USERNAME:-sail}" -p"${DB_PASSWORD:-password}" -e "SELECT 1;" >/dev/null 2>&1; then
        echo "✅ Database is ready!"
        exec "$@"
    fi
    echo "Retry $i/${WAIT_DB_RETRIES:-60}..."
    sleep ${WAIT_DB_SLEEP:-2}
done

echo "❌ Database not ready after ${WAIT_DB_RETRIES:-60} attempts"
exit 1
