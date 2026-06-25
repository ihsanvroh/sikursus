# Si Kursus — Deploy Guide

## Stack
- Laravel 11 (PHP 8.3 + Apache mod_php)
- MySQL 8.0
- Cloudflare Tunnel (bundled)
- URL: `https://yourdomain.example.com` (your configured domain)

## Architecture

```
Internet
   ↓ (https)
Cloudflare Tunnel (cloudflared container)
   ↓ (sikursus_net)
sikursus-app (Apache:80)
   ↓
sikursus-db (MySQL 8)
```

## Pre-deploy: Create Cloudflare Tunnel

1. Buka https://one.dash.cloudflare.com → Networks → Tunnels → Create
2. Pilih **Cloudflared**
3. Name: `sikursus`
4. **Copy token-nya** (string panjang)
5. Tab **Public Hostname** → Add:
   - Subdomain: `yoursubdomain`
   - Domain: `yourdomain.com`
   - Type: HTTP
   - URL: `sikursus-app:80` (internal container name)
6. Save

## Deploy

```bash
cd /root/hermes-workspace/sikursus

# 1. Edit .env, paste TUNNEL_TOKEN
nano .env
# (atau via sed)
# TOKEN='eyJhxxx...'
# sed -i "s|TUNNEL_TOKEN=.*|TUNNEL_TOKEN=$TOKEN|" .env

# 2. Build & up
docker-compose up -d --build

# 3. Wait for first build (10-15 min: PHP image + composer create-project Laravel)
docker-compose logs -f sikursus-app
# Tunggu sampai: "apache2 ... resuming normal operations"

# 4. Verify
docker-compose ps
curl -sI https://yourdomain.example.com
```

## Default Login

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@sikursus.local | admin12345 |
| Guru | guru.baca@sikursus.local | guru12345 |
| Guru | guru.hitung@sikursus.local | guru12345 |
| Guru | guru.bahasa@sikursus.local | guru12345 |
| Siswa | andi@siswa.local | siswa12345 |
| Siswa | budi@siswa.local | siswa12345 |
| Siswa | citra@siswa.local | siswa12345 |
| Siswa | dini@siswa.local | siswa12345 |
| Siswa | eko@siswa.local | siswa12345 |

## Auto Reminder Schedule

Pengingat otomatis dijadwalkan jam 08:00 setiap hari via Laravel Scheduler.
Untuk aktifkan, tambahkan ke host crontab:

```bash
crontab -e
# Tambahkan:
* * * * * cd /root/hermes-workspace/sikursus && docker-compose exec -T sikursus-app php artisan schedule:run >> /dev/null 2>&1
```

Atau test manual:
```bash
docker-compose exec sikursus-app php artisan pengingat:auto --dry-run
docker-compose exec sikursus-app php artisan pengingat:auto
```

## Hardening Production

```bash
# Edit .env compose
sed -i 's|^APP_ENV=.*|APP_ENV=production|; s|^APP_DEBUG=.*|APP_DEBUG=false|' .env
docker-compose restart sikursus-app

# Ganti default password admin
docker-compose exec sikursus-app php artisan tinker --execute="
\\App\\Models\\User::where('email','admin@sikursus.local')->update(['password'=>bcrypt('PasswordKuat123!')]);
"
```

## Reset Full

```bash
docker-compose down -v   # hapus volume DB → next up akan re-seed
docker-compose up -d --build
```

## Troubleshooting

**Build lama (15+ menit)** — wajar di first build (composer create-project Laravel 11). Subsequent builds cepat karena layer caching.

**500 error setelah deploy** — cek log:
```bash
docker-compose exec sikursus-app tail -50 storage/logs/laravel.log
docker-compose logs --tail=50 sikursus-app
```

**Database empty** — entrypoint akan seed otomatis kalau tabel users kosong. Force re-seed:
```bash
docker-compose exec sikursus-app php artisan db:seed --force
```

**Tunnel error 502** — cek `docker-compose logs sikursus-tunnel`. Pastikan dashboard Cloudflare set service ke `sikursus-app:80` (bukan localhost).
