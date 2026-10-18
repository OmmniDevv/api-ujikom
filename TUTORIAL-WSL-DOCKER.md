# Tutorial: Menjalankan SiPinjam di WSL + Docker Desktop (Dari Nol)

Panduan lengkap menjalankan aplikasi SiPinjam (Laravel 12) di Windows menggunakan WSL2 + **Docker Desktop**. Ditulis untuk yang sedang ujikom pakai source code ini — dari nol banget, tinggal ikuti langkahnya.

**Estimasi waktu:** 30–45 menit (tergantung kecepatan internet untuk download image Docker).

---

## Daftar Isi

1. [Install WSL2](#1-install-wsl2)
2. [Install Ubuntu di WSL](#2-install-ubuntu-di-wsl)
3. [Setup Docker Desktop + WSL2](#3-setup-docker-desktop--wsl2)
4. [Clone Repository](#4-clone-repository)
5. [Konfigurasi Environment](#5-konfigurasi-environment)
6. [Jalankan dengan Docker Compose](#6-jalankan-dengan-docker-compose)
7. [Akses Aplikasi](#7-akses-aplikasi)
8. [Perintah Sehari-hari](#8-perintah-sehari-hari)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. Install WSL2

WSL (Windows Subsystem for Linux) memungkinkan menjalankan Linux di dalam Windows tanpa dual-boot. Docker Desktop butuh WSL2 sebagai backend-nya.

### Syarat

- Windows 10 versi 2004+ atau Windows 11
- Virtualisasi aktif di BIOS (biasanya sudah aktif di laptop modern)

### Langkah

1. Buka **PowerShell sebagai Administrator** (klik kanan tombol Start → Terminal (Admin)).
2. Jalankan:

```powershell
wsl --install
```

3. Tunggu sampai selesai, lalu **restart komputer** saat diminta.

> Perintah ini menginstall WSL2 + Ubuntu otomatis di Windows terbaru. Kalau Ubuntu tidak ikut terinstall, lanjut ke langkah 2.

### Verifikasi

Buka PowerShell biasa, jalankan:

```powershell
wsl --list --verbose
```

Pastikan ada distro Linux dengan VERSION 2. Contoh:

```
  NAME      STATE           VERSION
* Ubuntu    Running         2
```

---

## 2. Install Ubuntu di WSL

Kalau langkah 1 belum menginstall Ubuntu:

```powershell
wsl --install -d Ubuntu
```

1. Tunggu download selesai.
2. Jendela Ubuntu akan terbuka — buat **username** dan **password** Linux (bebas, misal `dev`).
3. Update paket dasar (di terminal Ubuntu):

```bash
sudo apt update && sudo apt upgrade -y
```

---

## 3. Setup Docker Desktop + WSL2

Karena Om sudah download Docker Desktop, tinggal pastikan konfigurasinya benar.

### 3.1. Pastikan WSL2 jadi backend

1. Buka **Docker Desktop**.
2. Klik ikon **⚙️ Settings** (kanan atas).
3. Masuk ke **General** → pastikan **"Use the WSL 2 based engine"** tercentang.
4. Masuk ke **Resources → WSL Integration**:
   - Aktifkan **"Enable integration with my default WSL distro"**
   - Centang **Ubuntu** di daftar distro.
5. Klik **Apply & restart**.

### 3.2. Verifikasi dari WSL

Buka terminal **Ubuntu** (bukan PowerShell), jalankan:

```bash
docker --version
docker compose version
```

Keduanya harus menampilkan nomor versi **tanpa error**. Kalau muncul `permission denied`, tutup terminal Ubuntu dan buka lagi.

> Docker Desktop otomatis menyediakan perintah `docker` di dalam WSL — tidak perlu install Docker Engine manual.

### 3.3. Pastikan Docker Desktop berjalan

Docker Desktop harus dalam keadaan **running** (ikon paus di system tray tidak merah) setiap kali mau menjalankan project. Atur agar auto-start:

Settings → General → centang **"Start Docker Desktop when you log in"**.

---

## 4. Clone Repository

Di terminal **Ubuntu** (WSL):

```bash
# Masuk ke home directory Linux (PENTING: jangan di /mnt/c, lebih lambat)
cd ~

# Clone repo
git clone https://github.com/OmmniDevv/api-ujikom.git

# Masuk ke folder project
cd api-ujikom
```

> **Kenapa di `~` bukan di `C:\`?** File yang disimpan di filesystem Linux (`~`) diakses 5–10x lebih cepat oleh Docker dibanding file di `/mnt/c`. Untuk ujikom yang kejar waktu, ini ngaruh banget.

---

## 5. Konfigurasi Environment

```bash
# Copy template env
cp backend/.env.example backend/.env
```

> `APP_KEY` akan di-generate otomatis oleh container saat pertama jalan. Tidak perlu manual.

### Isi GEMINI_API_KEY (opsional)

Kalau mau fitur AI (chatbot/rekomendasi) jalan, edit `backend/.env`:

```bash
nano backend/.env
```

Cari baris ini dan isi:

```
GEMINI_API_KEY=isi_api_key_kamu_disini
```

Dapatkan gratis di https://aistudio.google.com/apikey. Kalau dikosongkan, aplikasi tetap jalan — cuma fitur AI-nya nonaktif.

### Password database (opsional)

Default di `docker-compose.yml`:

| Variabel | Default |
|---|---|
| `MYSQL_ROOT_PASSWORD` | `root123` |
| `MYSQL_PASSWORD` | `api_ujikom123` |

Untuk ujikom/lokal, default ini cukup. Jangan lupa ganti kalau deploy ke server.

---

## 6. Jalankan dengan Docker Compose

Di folder `api-ujikom` (sejajar dengan `docker-compose.yml`):

```bash
# Build & jalankan semua container
# Pertama kali agak lama (3-5 menit): download image + install dependency
docker compose up -d --build
```

Container `app-laravel` akan otomatis:

1. Install ekstensi PHP yang dibutuhkan
2. Install Composer + dependency Laravel
3. Install Node.js + build asset frontend
4. Jalankan **migration database** (termasuk enum `ditolak` untuk status tolak)
5. Menjalankan server di port 8000

### Cek status container

```bash
docker compose ps
```

Semua harus berstatus `Up`:

```
NAME             STATUS
MySQL-API        Up (healthy)
laravel-api      Up
PhpMyadmin-API   Up
```

Bisa juga cek lewat **Docker Desktop** → tab Containers — ketiga container harus hijau.

### Lihat log kalau ada masalah

```bash
# Log semua container
docker compose logs -f

# Log container tertentu
docker compose logs -f app-laravel
```

Tekan `Ctrl+C` untuk keluar.

---

## 7. Akses Aplikasi

| Layanan | URL | Keterangan |
|---|---|---|
| Aplikasi SiPinjam | http://localhost:8000 | Laravel |
| phpMyAdmin | http://localhost:8081 | Kelola database via browser |

### ✅ Checklist "Sampai Nyala"

Jalankan satu per satu. Kalau semua ✅, berarti aplikasi sudah nyala sempurna:

```bash
# 1. Semua container jalan?
docker compose ps
# Harus: MySQL-API (healthy), laravel-api (Up), PhpMyadmin-API (Up)
```

```bash
# 2. Laravel bisa konek ke database?
docker compose exec app-laravel php artisan migrate:status
# Harus: daftar migration dengan status [x] Ran (bukan error koneksi)
```

```bash
# 3. APP_KEY sudah terisi?
docker compose exec app-laravel grep '^APP_KEY=' .env
# Harus: APP_KEY=base64:xxxx (bukan kosong)
```

Lalu buka di browser Windows:

- [ ] **http://localhost:8000** → muncul halaman SiPinjam (bukan error 500)
- [ ] **http://localhost:8081** → muncul login phpMyAdmin → login `api_ujikom` / `api_ujikom123` → terlihat database `API_ujikom` dengan tabel-tabelnya

Kalau semua checklist di atas ✅, **aplikasi sudah NYALA** 🎉

**Login phpMyAdmin:**

- Username: `api_ujikom`
- Password: `api_ujikom123`

### Buat akun admin/petugas pertama

```bash
docker compose exec app-laravel php artisan tinker
```

Di dalam tinker:

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@sipinjam.local',
    'password' => bcrypt('admin123'),
    'role' => 'admin',
]);
exit
```

> Sesuaikan `role` dengan nilai yang dipakai aplikasi (cek migration tabel `users`).

---

## 8. Perintah Sehari-hari

```bash
# Masuk ke folder project dulu
cd ~/api-ujikom

# Nyalakan semua container
docker compose up -d

# Matikan semua container (data MySQL tetap tersimpan)
docker compose down

# Matikan + hapus data database (reset total — hati-hati!)
docker compose down -v

# Jalankan migration baru (setelah git pull ada migration baru)
docker compose exec app-laravel php artisan migrate --force

# Masuk ke shell container Laravel
docker compose exec app-laravel bash

# Update kode dari GitHub
git pull
docker compose up -d --build

# Lihat daftar container yang jalan
docker compose ps
```

### Alur kerja ujikom yang disarankan

```bash
# Pagi: nyalakan Docker Desktop, lalu di terminal Ubuntu:
cd ~/api-ujikom
docker compose up -d

# Sore/selesai: matikan untuk hemat baterai
docker compose down
```

---

## 9. Troubleshooting

### Docker Desktop tidak mau start

- Pastikan virtualisasi aktif: Task Manager → Performance → CPU → **Virtualization: Enabled**.
- Pastikan WSL2 terinstall: `wsl --list --verbose` di PowerShell.
- Restart Docker Desktop / restart laptop.

### `docker: command not found` di WSL

WSL Integration belum aktif. Buka Docker Desktop → Settings → Resources → WSL Integration → centang Ubuntu → Apply & restart.

### `permission denied` saat jalankan docker

Tutup terminal Ubuntu dan buka lagi. Docker Desktop mengatur permission otomatis setelah terminal di-restart.

### Port 8000 / 3306 / 8081 sudah dipakai

Matikan aplikasi yang memakai port tersebut (misal XAMPP/MySQL lokal). Atau ubah mapping di `docker-compose.yml`:

```yaml
ports:
  - "8001:8000"   # akses via http://localhost:8001
```

### Container `app-laravel` restart terus / error

```bash
docker compose logs app-laravel --tail 50
```

Penyebab umum:
- MySQL belum siap → tunggu 1–2 menit, healthcheck akan retry otomatis.
- `composer install` gagal (internet putus) → `docker compose up -d --build` ulang.
- `APP_KEY` kosong → generate key sesuai langkah 5.

### Migration gagal: `table already exists`

Database sudah punya tabel tapi tracking migration hilang. Reset lalu ulang:

```bash
docker compose down -v
docker compose up -d --build
```

### Aplikasi lemot

Pastikan repo di-clone di `~/api-ujikom` (filesystem Linux), **bukan** di `/mnt/c/...`.

### Internet kampus/sekolah memblokir download image

Coba pakai hotspot HP untuk langkah `docker compose up --build` pertama kali. Setelah image terdownload, tidak butuh internet lagi.

---

## Ringkasan Arsitektur

```
┌──────────────────────────────────────────────┐
│  Windows + Docker Desktop (WSL2 backend)      │
│                                              │
│  ┌──────────────┐  ┌──────────────┐          │
│  │ app-laravel  │  │ mysql-server │          │
│  │ php:8.4-cli  │──│ mysql:8.0    │          │
│  │ :8000        │  │ :3306        │          │
│  └──────────────┘  └──────────────┘          │
│                                              │
│  ┌──────────────┐                            │
│  │ phpmyadmin   │                            │
│  │ :8081        │                            │
│  └──────────────┘                            │
└──────────────────────────────────────────────┘
       ↕ diakses dari browser Windows via
         http://localhost:8000
```

Selamat ujikom, semoga lancar! 🚀
