# SiPinjam - Aplikasi Peminjaman Alat

SiPinjam adalah aplikasi web untuk mengelola peminjaman alat laboratorium di sekolah. Ada tiga peran pengguna: Admin mengelola data master (user, kategori, alat), Petugas memproses persetujuan peminjaman dan pengembalian, dan Peminjam melihat katalog lalu mengajukan peminjaman.

Dibangun dengan Laravel 12, MySQL 8, dan Docker Compose supaya bisa dijalankan dari nol di komputer mana pun dengan langkah yang sama.

## Akun bawaan setelah seeding

| Peran | Email | Kata sandi |
|---|---|---|
| Admin | admin@gmail.com | password123 |
| Petugas | petugas@gmail.com | password123 |
| Peminjam | rian@gmail.com | password123 |

Pengguna baru juga bisa mendaftar sendiri lewat halaman Register. Akun hasil register otomatis berperan sebagai Peminjam.

## Cara menjalankan dari clone baru

Yang perlu disiapkan:

- Git
- Docker Desktop yang sudah berjalan, dengan WSL 2 dan Ubuntu aktif bila memakai Windows

Langkahnya:

1. Clone repo ini, lalu masuk ke foldernya.

   ```bash
   git clone https://github.com/OmmniDevv/api-ujikom.git
   cd api-ujikom
   ```

2. Buat file konfigurasi dari contoh yang tersedia di folder `backend`.

   ```bash
   cp backend/.env.example backend/.env
   ```

   Nilai bawaan di `.env.example` sudah disesuaikan dengan Docker Compose (host database `mysql-server`, database `API_ujikom`, user `api_ujikom`), jadi untuk menjalankan secara lokal tidak perlu diubah lagi. File `.env` asli tidak ikut ter-push ke GitHub, jadi langkah ini wajib dilakukan setiap clone baru.

3. Bangun dan jalankan semua container.

   ```bash
   docker compose up -d --build
   ```

   Saat pertama kali start, container aplikasi otomatis menjalankan `composer install`, membuat `APP_KEY` bila belum ada, menjalankan migrasi, mengisi data awal (seeder) satu kali, dan membuat `storage:link`. Tunggu sekitar satu sampai dua menit pada run pertama.

4. Buka aplikasi di browser: http://localhost:8000

   Antarmuka database phpMyAdmin tersedia di http://localhost:8081 bila diperlukan.

## Perintah yang sering dipakai

Semua perintah Artisan dijalankan di dalam container aplikasi. Nama container project ini adalah `laravel-api`.

```bash
docker exec -it laravel-api php artisan migrate:fresh --seed
docker exec -it laravel-api php artisan key:generate
docker exec -it laravel-api php artisan test
```

- `migrate:fresh --seed` mengosongkan database lalu membangun ulang struktur dan data awal. Pakai ini bila ingin mengulang dari keadaan bersih.
- `key:generate` membuat ulang kunci aplikasi di file `.env`.
- `test` menjalankan seluruh pengujian otomatis (autentikasi, registrasi, profil, dan alur peminjaman).

Untuk mematikan aplikasi gunakan `docker compose down`. Bila ingin menghapus data database sekalian, gunakan `docker compose down -v` lalu jalankan lagi dari langkah 3.

## Fitur utama

- Register, Login, Logout, dan halaman profil `/me` untuk melihat dan mengubah biodata pengguna yang sedang login.
- Admin: CRUD User, Kategori, dan Alat, memantau seluruh peminjaman, analitik, serta laporan PDF dan Excel.
- Petugas: menyetujui atau menolak pengajuan peminjaman, mencatat pengembalian beserta denda keterlambatan, dan membuat laporan.
- Peminjam: melihat katalog alat, mengajukan peminjaman banyak alat sekaligus, melihat riwayat, dan mengajukan pengembalian yang kemudian diproses Petugas.
- Skor reputasi peminjam yang berubah mengikuti ketepatan waktu pengembalian, dan skor itu menentukan kuota alat aktif yang boleh dipinjam sekaligus: skor 120 ke atas maksimal 5 alat, 90 sampai 119 maksimal 3 alat, 60 sampai 89 maksimal 2 alat, di bawah 60 maksimal 1 alat. Pengajuan yang melebihi kuota ditolak dengan pesan yang menjelaskan skor dan kuotanya.
- Dashboard Petugas menampilkan daftar peminjaman yang sudah lewat tanggal rencana kembali tetapi belum dikembalikan, lengkap dengan jumlah hari terlambat dan estimasi denda Rp 5.000 per hari, supaya petugas bisa langsung memproses pengembaliannya.

## Struktur repo

- `docker-compose.yml` berisi tiga service: `app-laravel` (container `laravel-api`), `mysql-server`, dan `phpmyadmin`.
- `backend/` berisi seluruh kode aplikasi Laravel, termasuk `Dockerfile`, migrasi, seeder, dan pengujian.
- `install-and-start.sh` adalah skrip bantuan untuk menyiapkan project di WSL.
- `TUTORIAL-WSL-DOCKER.md` berisi panduan pemasangan WSL dan Docker beserta pemecahan masalah yang pernah ditemui.
- `DOKUMENTASI_LENGKAP_ALUR_DAN_LOGIKA.md` menjelaskan alur sistem dan logika bisnisnya.
- `docs/PERANCANGAN.md` berisi diagram perancangan: use case, aktivitas, sequence, class, dan ERD.
