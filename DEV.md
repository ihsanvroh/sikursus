# DEV MODE — Si Kursus

Buat iterasi cepat tanpa rebuild Docker image setiap kali edit code.

## Cara pakai

`docker-compose.override.yml` udah disiapin di repo (gitignored — cuma di disk lokal lo).

```bash
cd /root/hermes-workspace/sikursus

# 1. Recreate container — sekarang dia bind-mount overlay/ langsung
docker-compose stop sikursus-app
docker-compose rm -f sikursus-app
docker-compose up -d sikursus-app

# 2. Clear cache Laravel sekali (setelah ini gak perlu lagi)
docker-compose exec -T sikursus-app php artisan view:clear
docker-compose exec -T sikursus-app php artisan route:clear
docker-compose exec -T sikursus-app php artisan config:clear
```

## Workflow setelah aktif

Edit file di `overlay/` (di host) → **langsung kebaca di browser**. Hard refresh (Ctrl+Shift+R) kalau browser cache CSS lama.

Untuk perubahan tertentu masih perlu clear cache:
- Edit blade view (kebanyakan auto-reload, tapi compiled view kadang stale)
- Edit route → `php artisan route:clear`
- Edit config → `php artisan config:clear`

## Kapan harus rebuild image

- Tambah package composer (`composer require X`)
- Ubah `Dockerfile` / extension PHP
- Ubah `docker-compose.yml` (service-level config)

```bash
docker-compose build sikursus-app
docker-compose stop sikursus-app && docker-compose rm -f sikursus-app
docker-compose up -d sikursus-app
```

## Balik ke production-like build

Kalau mau test "build clean" tanpa bind mount:

```bash
mv docker-compose.override.yml docker-compose.override.yml.disabled
docker-compose stop sikursus-app && docker-compose rm -f sikursus-app
docker-compose up -d sikursus-app
```

Jangan lupa rename balik kalau mau lanjut dev.

## Yang di-bind-mount

```
./overlay/app/        → /var/www/html/app/
./overlay/config/     → /var/www/html/config/
./overlay/database/   → /var/www/html/database/
./overlay/resources/  → /var/www/html/resources/
./overlay/routes/     → /var/www/html/routes/
./overlay/bootstrap/app.php → /var/www/html/bootstrap/app.php
```

`vendor/`, `storage/`, `public/build/` tetep dari image / volume — gak di-overlay.
