# Sikursus — Sistem Informasi Kursus

Aplikasi manajemen kursus dengan **SPP bulanan**, **multi-role** (Admin / Guru / Siswa), dan **sistem pengingat tagihan** otomatis.

🔗 **Live**: `https://yourdomain.example.com` (configure your own domain)

---

## Stack

- Laravel 11
- PHP 8.4
- MySQL 8
- Apache (Docker)
- Cloudflare Tunnel

## Fitur Utama

- 👥 **Manajemen User** — 3 role: Admin, Guru, Siswa
- 🎓 **Kelas & Enrollment** — multi-kelas, riwayat enrollment, tarif per kelas
- 💰 **Tagihan SPP Bulanan** — generate manual per periode, jatuh tempo configurable
- 💳 **Pembayaran** — input pembayaran + status tagihan otomatis
- 📊 **Laporan Bulanan** — rekap pendapatan, tunggakan, statistik
- 🔔 **Sistem Pengingat** — template + setting H-N (sebelum/saat/setelah jatuh tempo)
- 📅 **Scheduler** — kirim pengingat manual atau auto via cron
- 🛎️ **Notifikasi In-App** — bell icon + counter

## Default Demo Credentials

Lihat [`DEMO_USERS.txt`](./DEMO_USERS.txt).

## Deploy

Lihat [`DEPLOY.md`](./DEPLOY.md).

```bash
docker-compose up -d --build
```

## Struktur

```
sikursus/
├── docker-compose.yml      # 3 service: app, db, tunnel
├── Dockerfile              # PHP 8.4 + Apache
├── docker/                 # entrypoint, apache config
└── overlay/                # source Laravel (di-overlay ke /var/www)
    ├── app/
    ├── database/migrations/
    ├── database/seeders/
    └── ...
```

## Note

- Generate tagihan = **manual** via tombol di menu Tagihan (bukan auto cron)
- Notifikasi = **in-app only** (skip WA/Email/Telegram)
- APP_KEY harus persistent di `.env` (bukan auto-generate tiap restart)
