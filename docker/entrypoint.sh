#!/usr/bin/env bash
set -e

# If FIREBASE_CREDENTIALS env var present, write it to storage and set GOOGLE_APPLICATION_CREDENTIALS
if [ -n "${FIREBASE_CREDENTIALS:-}" ]; then
  mkdir -p /var/www/html/storage
  echo "$FIREBASE_CREDENTIALS" > /var/www/html/storage/firebase_credentials.json
  export GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/storage/firebase_credentials.json
fi

# Ensure storage and cache dirs exist and permissions are correct
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# If APP_KEY not set in env, attempt to use existing key or generate a runtime key (not recommended for production)
if [ -z "${APP_KEY:-}" ]; then
  if php artisan --version >/dev/null 2>&1; then
    php artisan key:generate --force || true
  fi
fi

exec "$@"
