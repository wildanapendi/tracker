# Instalasi & Setup Lokal

Panduan lengkap untuk menyiapkan **SkripsiTracker** di lingkungan development lokal.

## Prasyarat

Pastikan tool berikut sudah terinstall:

- **PHP** ≥ 8.5 dengan ekstensi: `bcmath`, `gd`, `intl`, `mbstring`, `opcache`, `pdo_mysql`, `zip`
- **Composer** ≥ 2.8
- **Node.js** ≥ 22 + **npm** ≥ 10
- **MySQL** ≥ 8.0 (produksi) **atau** SQLite (development)
- **Docker** ≥ 24 (opsional, untuk deployment container)

## Setup Lokal (Development)

```bash
# 1. Clone repository
git clone https://github.com/your-username/skripsi-tracker.git
cd skripsi-tracker

# 2. Install PHP dependencies
composer install

# 3. Salin file environment dan generate application key
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env (lihat bagian Environment Variables di README)
#    Default menggunakan SQLite:
touch database/database.sqlite

# 5. Jalankan migrasi dan seeder
php artisan migrate --seed

# 6. Install Node dependencies dan build asset
npm install
npm run build

# 7. (Opsional) Buat symlink storage untuk file upload publik
php artisan storage:link
```

### Satu Perintah (via Composer Script)

```bash
# Menjalankan semua langkah 2–6 sekaligus
composer setup
```

## Langkah Selanjutnya

- Konfigurasi variabel environment lebih detail: lihat bagian [Environment Variables](./README.md#environment-variables) di README.
- Menjalankan aplikasi setelah setup: lihat bagian [Menjalankan Aplikasi](./README.md#menjalankan-aplikasi) di README.
