# Panduan Kontribusi

Terima kasih atas minat kamu untuk berkontribusi di **SkripsiTracker**! Dokumen ini menjelaskan alur kerja yang perlu diikuti sebelum mengajukan perubahan.

## Daftar Isi

- [Sebelum Mulai](#sebelum-mulai)
- [Alur Kontribusi (Fork & Pull Request)](#alur-kontribusi-fork--pull-request)
- [Setup Development](#setup-development)
- [Standar Kode](#standar-kode)
- [Menjalankan Test](#menjalankan-test)
- [Konvensi Commit Message](#konvensi-commit-message)
- [Mengajukan Pull Request](#mengajukan-pull-request)
- [Melaporkan Bug / Mengajukan Fitur](#melaporkan-bug--mengajukan-fitur)

---

## Sebelum Mulai

- Pastikan sudah membaca [README.md](./README.md) untuk memahami fitur dan struktur proyek.
- Ikuti [INSTALLATION.md](./INSTALLATION.md) untuk menyiapkan environment development lokal.

## Alur Kontribusi (Fork & Pull Request)

1. **Fork** repository ini ke akun GitHub kamu.
2. **Clone** hasil fork ke komputer lokal:
   ```bash
   git clone https://github.com/USERNAME-KAMU/skripsi-tracker.git
   cd skripsi-tracker
   ```
3. Tambahkan remote upstream agar bisa sinkron dengan repo asli:
   ```bash
   git remote add upstream https://github.com/original-owner/skripsi-tracker.git
   ```
4. Buat branch baru dari `main` untuk setiap perubahan:
   ```bash
   git checkout -b feat/nama-fitur
   ```
   Gunakan prefix yang sesuai jenis perubahan, misal `feat/`, `fix/`, `docs/`, `refactor/`.
5. Lakukan perubahan, commit, lalu push ke fork kamu:
   ```bash
   git push origin feat/nama-fitur
   ```
6. Buka **Pull Request** dari branch di fork kamu ke branch `main` repo asli.

## Setup Development

Lihat [INSTALLATION.md](./INSTALLATION.md) untuk langkah instalasi lengkap.

## Standar Kode

Proyek ini menggunakan [Laravel Pint](https://laravel.com/docs/pint) untuk code style. Jalankan sebelum commit:

```bash
./vendor/bin/pint
```

Beberapa aturan umum:
- Ikuti konvensi penamaan Laravel (PascalCase untuk class, camelCase untuk method/variable).
- Service/business logic diletakkan di `app/Services/`, bukan langsung di Controller/Resource.
- Tambahkan test untuk setiap fitur atau perbaikan bug baru.

## Menjalankan Test

Pastikan seluruh test lulus sebelum mengajukan Pull Request:

```bash
composer test
```

atau jalankan test spesifik:

```bash
php artisan test --filter=NamaTest
```

## Konvensi Commit Message

Gunakan format [Conventional Commits](https://www.conventionalcommits.org/):

```
<tipe>: <deskripsi singkat>
```

Tipe yang umum dipakai:

| Tipe | Kegunaan |
|---|---|
| `feat` | Menambahkan fitur baru |
| `fix` | Memperbaiki bug |
| `docs` | Perubahan dokumentasi saja |
| `refactor` | Perubahan kode tanpa mengubah perilaku |
| `test` | Menambah/memperbaiki test |
| `chore` | Perubahan konfigurasi, dependency, dll |

Contoh:
```
feat: tambah fitur daftar akun (sign up / register)
fix: perbaiki validasi tanggal pada guidance
docs: perbarui panduan instalasi
```

## Mengajukan Pull Request

- Pastikan branch kamu sudah sinkron dengan `main` terbaru (`git pull upstream main`).
- Jelaskan secara singkat apa yang diubah dan alasannya di deskripsi PR.
- Pastikan semua test dan Pint check lulus.
- Satu Pull Request idealnya fokus pada satu perubahan/fitur agar mudah direview.

## Melaporkan Bug / Mengajukan Fitur

Gunakan tab **Issues** di GitHub. Sertakan:
- Langkah untuk mereproduksi bug (jika melaporkan bug).
- Perilaku yang diharapkan vs yang terjadi.
- Screenshot/log jika relevan.

---

Terima kasih sudah membantu mengembangkan SkripsiTracker! 🎉
