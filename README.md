# SkripsiTracker

> Aplikasi manajemen progress skripsi berbasis web yang dibangun dengan **Laravel 13**, **Filament 5**, dan **Livewire 4**. Membantu mahasiswa dan dosen pembimbing memantau kemajuan penulisan skripsi secara terstruktur dan real-time.

---

## Daftar Isi

- [SkripsiTracker](#skripsitracker)
  - [Daftar Isi](#daftar-isi)
  - [Fitur Utama](#fitur-utama)
  - [Tech Stack](#tech-stack)
  - [Struktur Proyek](#struktur-proyek)
  - [Prasyarat](#prasyarat)
  - [Setup Lokal (Development)](#setup-lokal-development)
    - [Satu Perintah (via Composer Script)](#satu-perintah-via-composer-script)
  - [Environment Variables](#environment-variables)
  - [Database \& Seeding](#database--seeding)
    - [Tabel yang Dibuat](#tabel-yang-dibuat)
  - [Menjalankan Aplikasi](#menjalankan-aplikasi)
    - [Development Server](#development-server)
    - [Membuat User Admin (Pertama Kali)](#membuat-user-admin-pertama-kali)
  - [Menjalankan Test Suite](#menjalankan-test-suite)
    - [Cakupan Test](#cakupan-test)
  - [Deployment dengan Docker](#deployment-dengan-docker)
    - [Langkah Cepat](#langkah-cepat)
    - [Menggunakan Docker Compose (Direkomendasikan)](#menggunakan-docker-compose-direkomendasikan)
    - [Isi Direktori `docker/`](#isi-direktori-docker)
  - [Deployment Manual (VPS / Shared Hosting)](#deployment-manual-vps--shared-hosting)
  - [Arsitektur \& Desain](#arsitektur--desain)
    - [Service Layer — `ProgressService`](#service-layer--progressservice)
    - [Filament Panel](#filament-panel)
    - [Multi-Tenancy (Per-User)](#multi-tenancy-per-user)
  - [Lisensi](#lisensi)

---

## Fitur Utama

| Modul | Deskripsi |
|---|---|
| **Dashboard** | Widget ringkasan: overall progress, quick stats, grafik bab, guidance mendatang, dan status milestone |
| **Chapter Management** | CRUD bab skripsi + sub-task per bab dengan tracking status (`pending`, `in_progress`, `done`) |
| **Guidance Log** | Pencatatan sesi bimbingan beserta tanggal, topik, dan status (`scheduled`, `done`, `cancelled`) |
| **Milestone Tracking** | Pencapaian penting (seminar proposal, sidang, dll) dengan upload dokumen pendukung |
| **Kalender** | Tampilan kalender interaktif (FullCalendar) yang mengagregasi semua event dari Guidance & Milestone |
| **Thesis Profile** | Halaman profil skripsi (judul, pembimbing, target selesai, instansi) |
| **Progress Service** | Service layer terpusat untuk kalkulasi progress per-bab, keseluruhan, dan milestone |

---

## Tech Stack

| Layer | Teknologi | Versi |
|---|---|---|
| Backend Framework | Laravel | ~13.15 |
| Admin Panel | Filament | ^5.0 |
| Reactive UI | Livewire + Flux | ^4.1 / ^2.13 |
| Auth | Laravel Fortify | ^1.37 |
| PHP | PHP-FPM | ^8.5 |
| Database | MySQL (prod) / SQLite (dev) | 8.x / 3.x |
| Frontend Build | Vite + Tailwind CSS | ^8 / ^4 |
| Testing | PestPHP | ^4.7 |
| Container | Docker + Nginx + Supervisor | — |

---

## Struktur Proyek

```
app/
├── Enums/                  # TaskStatus, GuidanceStatus, MilestoneStatus
├── Filament/
│   ├── Pages/              # Calendar.php, ThesisProfile.php
│   ├── Resources/          # ChapterResource, GuidanceResource, MilestoneResource
│   └── Widgets/            # 5 dashboard widgets
├── Http/Controllers/       # CalendarController (API endpoint)
├── Models/                 # 8 Eloquent models
├── Providers/Filament/     # AppPanelProvider
└── Services/               # ProgressService
database/
├── migrations/             # 12 migration files
├── factories/              # 7 model factories
└── seeders/                # DefaultMilestoneSeeder, DatabaseSeeder
docker/                     # Docker support files
tests/
├── Feature/                # ChapterResource, GuidanceResource, MilestoneResource,
│                           # CalendarController, ThesisProfile tests
└── Unit/                   # ProgressServiceTest
```

---

## Prasyarat

Pastikan tool berikut sudah terinstall:

- **PHP** ≥ 8.5 dengan ekstensi: `bcmath`, `gd`, `intl`, `mbstring`, `opcache`, `pdo_mysql`, `zip`
- **Composer** ≥ 2.8
- **Node.js** ≥ 22 + **npm** ≥ 10
- **MySQL** ≥ 8.0 (produksi) **atau** SQLite (development)
- **Docker** ≥ 24 (opsional, untuk deployment container)

---

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

# 4. Konfigurasi database di .env (lihat bagian Environment Variables)
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

---

## Environment Variables

Salin `.env.example` sebagai titik awal untuk development, atau `.env.production.example` untuk produksi.

| Variable | Default (dev) | Keterangan |
|---|---|---|
| `APP_NAME` | `SkripsiTracker` | Nama aplikasi yang tampil di UI |
| `APP_ENV` | `local` | `local` / `production` |
| `APP_KEY` | *(kosong)* | **Wajib diisi** — `php artisan key:generate` |
| `APP_DEBUG` | `true` | Set `false` di produksi |
| `APP_URL` | `http://localhost` | URL publik aplikasi |
| `DB_CONNECTION` | `sqlite` | `sqlite` / `mysql` |
| `DB_DATABASE` | *(path SQLite)* | Nama database MySQL di produksi |
| `DB_USERNAME` | — | Username database (MySQL) |
| `DB_PASSWORD` | — | Password database (MySQL) |
| `SESSION_DRIVER` | `database` | `database` / `redis` |
| `QUEUE_CONNECTION` | `database` | `database` / `redis` |
| `CACHE_STORE` | `database` | `database` / `redis` |
| `MAIL_MAILER` | `log` | `log` / `smtp` / `mailgun` |
| `FILESYSTEM_DISK` | `local` | `local` / `s3` |

Lihat [`.env.production.example`](./.env.production.example) untuk daftar lengkap beserta penjelasan per-variabel.

---

## Database & Seeding

```bash
# Jalankan semua migrasi
php artisan migrate

# Rollback dan re-migrate (development)
php artisan migrate:fresh --seed

# Hanya menjalankan seeder
php artisan db:seed
```

### Tabel yang Dibuat

| Tabel | Model | Deskripsi |
|---|---|---|
| `thesis_profiles` | `ThesisProfile` | Profil skripsi per user |
| `chapters` | `Chapter` | Bab skripsi |
| `chapter_tasks` | `ChapterTask` | Sub-task per bab |
| `guidances` | `Guidance` | Log sesi bimbingan |
| `milestones` | `Milestone` | Pencapaian penting |
| `milestone_documents` | `MilestoneDocument` | Dokumen pendukung milestone |
| `calendar_events` | `CalendarEvent` | Event gabungan untuk kalender |

---

## Menjalankan Aplikasi

### Development Server

```bash
# Menjalankan PHP server, Vite, dan Queue Worker secara bersamaan
composer dev

# Atau secara terpisah:
php artisan serve       # Backend (port 8000)
npm run dev             # Vite HMR (port 5173)
php artisan queue:listen --tries=1
```

Akses panel Filament di: **http://localhost:8000/**

### Membuat User Admin (Pertama Kali)

```bash
php artisan tinker

>>> App\Models\User::factory()->create([
...     'name'     => 'Admin',
...     'email'    => 'admin@example.com',
...     'password' => bcrypt('password'),
... ]);
```

---

## Menjalankan Test Suite

```bash
# Menjalankan semua test (Unit + Feature)
php artisan test

# Atau melalui Composer script (termasuk lint check)
composer test

# Menjalankan test suite tertentu
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Filter test berdasarkan nama
php artisan test --filter=ProgressServiceTest

# Dengan laporan coverage (membutuhkan Xdebug atau PCOV)
php artisan test --coverage
```

### Cakupan Test

| Test | Jenis | Cakupan |
|---|---|---|
| `ProgressServiceTest` | Unit | `calculateChapterProgress`, `calculateOverallProgress`, `calculateMilestoneCompletion`, `getQuickStats` |
| `ChapterResourceTest` | Feature | List, Create, Edit, Delete chapter via Filament |
| `GuidanceResourceTest` | Feature | List, Create, Edit, Delete guidance |
| `MilestoneResourceTest` | Feature | List, Create, Edit, Delete milestone + upload dokumen |
| `CalendarControllerTest` | Feature | GET `/api/calendar-events` — autentikasi & respons JSON |
| `ThesisProfileTest` | Feature | Render halaman ThesisProfile Filament |

---

## Deployment dengan Docker

### Langkah Cepat

```bash
# 1. Salin dan edit file environment
cp .env.production.example .env
# Edit .env: isi APP_KEY, DB_*, MAIL_*, dll.

# 2. Build image Docker
docker build -t skripsi-tracker:latest .

# 3. Jalankan container
docker run -d \
  --name skripsi-tracker \
  -p 80:80 \
  --env-file .env \
  -v skripsi-tracker-storage:/var/www/html/storage \
  skripsi-tracker:latest
```

### Menggunakan Docker Compose (Direkomendasikan)

Buat file `docker-compose.yml`:

```yaml
version: "3.9"

services:
  app:
    build: .
    image: skripsi-tracker:latest
    ports:
      - "80:80"
    env_file: .env
    volumes:
      - storage_data:/var/www/html/storage
    depends_on:
      db:
        condition: service_healthy
    restart: unless-stopped

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE:      ${DB_DATABASE}
      MYSQL_USER:          ${DB_USERNAME}
      MYSQL_PASSWORD:      ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 10
    restart: unless-stopped

volumes:
  db_data:
  storage_data:
```

```bash
docker compose up -d
```

### Isi Direktori `docker/`

```
docker/
├── entrypoint.sh           # Bootstrap script (migrate, cache, permission)
├── nginx/
│   └── default.conf        # Nginx server block
├── php/
│   ├── fpm.conf            # PHP-FPM pool
│   ├── opcache.ini         # OPcache tuning
│   └── php.ini             # PHP runtime settings
└── supervisor/
    └── supervisord.conf    # Process manager (php-fpm + nginx + queue)
```

---

## Deployment Manual (VPS / Shared Hosting)

```bash
# 1. Pull kode terbaru
git pull origin main

# 2. Install/update dependencies
composer install --no-dev --optimize-autoloader

# 3. Build frontend assets
npm ci && npm run build

# 4. Jalankan migrasi
php artisan migrate --force

# 5. Clear & rebuild cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Restart queue worker
php artisan queue:restart
```

> **Tips:** Gunakan [Laravel Envoyer](https://envoyer.io) atau [GitHub Actions](https://docs.github.com/en/actions) untuk zero-downtime deployment otomatis.

---

## Arsitektur & Desain

### Service Layer — `ProgressService`

Semua logika kalkulasi progress dienkapsulasi dalam `App\Services\ProgressService` agar dapat diuji secara independen dari UI:

```
ProgressService
├── calculateChapterProgress(Chapter $chapter): float   # % task selesai per bab
├── calculateOverallProgress(User $user): float         # % overall thesis
├── calculateMilestoneCompletion(User $user): array     # jumlah & % milestone
└── getQuickStats(User $user): array                    # ringkasan stats dashboard
```

### Filament Panel

Panel Filament dikonfigurasi di `AppPanelProvider` dengan path `/` agar tidak ada prefix URL. Fitur yang diaktifkan:

- Dark mode
- 5 Dashboard Widgets
- 3 Resources (Chapter, Guidance, Milestone)
- 2 Custom Pages (Calendar, ThesisProfile)

### Multi-Tenancy (Per-User)

Setiap model utama memiliki relasi `belongsTo(User::class)` dan setiap Resource/Query di-scope berdasarkan `auth()->id()` untuk memastikan isolasi data antar pengguna.

---

## Lisensi

Proyek ini berada di bawah lisensi **MIT**. Lihat file [LICENSE](./LICENSE) untuk detail.
