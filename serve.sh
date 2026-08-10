#!/usr/bin/env bash
# Start HeartLovePics with multi-image upload limits.
# artisan serve spawns a bare `php -S` child that ignores `php -c`, so we
# start the built-in server ourselves with an explicit ini file.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT"

export PATH="/opt/homebrew/opt/php@8.3/bin:/opt/homebrew/bin:${PATH}"

PHP_BIN="$(command -v php)"
INI="$ROOT/php-local.ini"
HOST="${HOST:-127.0.0.1}"
PORT="${PORT:-8090}"

if [[ ! -f "$INI" ]]; then
  echo "Missing $INI" >&2
  exit 1
fi

# Free the port if a previous server is still bound.
if command -v lsof >/dev/null 2>&1; then
  PIDS="$(lsof -tiTCP:"$PORT" -sTCP:LISTEN 2>/dev/null || true)"
  if [[ -n "${PIDS}" ]]; then
    # shellcheck disable=SC2086
    kill ${PIDS} 2>/dev/null || true
    sleep 0.3
  fi
fi

echo "Using PHP: $PHP_BIN"
"$PHP_BIN" -c "$INI" -r 'echo "  post_max_size=".ini_get("post_max_size")." upload_max_filesize=".ini_get("upload_max_filesize")." max_file_uploads=".ini_get("max_file_uploads").PHP_EOL;'
echo "Server: http://${HOST}:${PORT}"
echo "Press Ctrl+C to stop."
echo

cd "$ROOT/public"
exec "$PHP_BIN" -c "$INI" -S "${HOST}:${PORT}" "$ROOT/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php"
