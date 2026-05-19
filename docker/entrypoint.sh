#!/usr/bin/env bash
set -e

cd /var/www/html

# Tunggu MySQL ready
echo "[entrypoint] Menunggu MySQL siap..."
for i in $(seq 1 60); do
  if php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'ok'; } catch (Exception \$e) { exit(1); }" >/dev/null 2>&1; then
    echo "[entrypoint] MySQL siap."
    break
  fi
  sleep 2
done

# Generate APP_KEY if missing
if ! grep -qE '^APP_KEY=base64:' .env 2>/dev/null; then
  echo "[entrypoint] Generating APP_KEY..."
  php artisan key:generate --force
fi

# Run migrations + seed (idempotent: pakai --force, seed cuma kalau kosong)
echo "[entrypoint] Running migrations..."
php artisan migrate --force

# Seed only if users table is empty
USERS_COUNT=$(php artisan tinker --execute="echo App\\Models\\User::count();" 2>/dev/null | tail -1 | tr -d '[:space:]')
if [ "$USERS_COUNT" = "0" ] || [ -z "$USERS_COUNT" ]; then
  echo "[entrypoint] Seeding initial data..."
  php artisan db:seed --force || true
else
  echo "[entrypoint] Database sudah ada data ($USERS_COUNT users), skip seeding."
fi

# Storage symlink
if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

# Clear & rebuild cache
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Permissions (mounted volumes can reset)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "[entrypoint] Starting: $*"
exec "$@"
