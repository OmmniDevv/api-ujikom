# Tutorial: Menjalankan SiPinjam di WSL + Docker (Dari Nol)

Panduan lengkap menjalankan aplikasi SiPinjam (Laravel 12) di Windows menggunakan WSL2 + Docker. Cocok untuk pemula yang belum pernah pakai WSL/Docker sama sekali.

**Estimasi waktu:** 30–45 menit (tergantung kecepatan internet untuk download image Docker).

---

## Daftar Isi

1. [Install WSL2](#1-install-wsl2)
2. [Install Ubuntu di WSL](#2-install-ubuntu-di-wsl)
3. [Install Docker di WSL](#3-install-docker-di-wsl)
4. [Clone Repository](#4-clone-repository)
5. [Konfigurasi Environment](#5-konfigurasi-environment)
6. [Jalankan dengan Docker Compose](#6-jalankan-dengan-docker-compose)
7. [Akses Aplikasi](#7-akses-aplikasi)
8. [Perintah Sehari-hari](#8-perintah-sehari-hari)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. Install WSL2

WSL (Windows Subsystem for Linux) memungkinkan menjalankan Linux di dalam Windows tanpa dual-boot.

### Syarat

- Windows 10 versi 2004+ atau Windows 11
- Virtualisasi aktif di BIOS (biasanya sudah aktif di laptop modern)

### Langkah

1. Buka **PowerShell sebagai Administrator** (klik kanan tombol Start → Terminal (Admin)).
2. Jalankan perintah ini:

```powershell
wsl --install
```

3. Tunggu sampai selesai, lalu **restart komputer** saat diminta.

> Perintah ini menginstall WSL2 + Ubuntu secara otomatis di Windows terbaru. Kalau Ubuntu tidak ikut terinstall, lanjut ke langkah 2.

### Verifikasi

Buka PowerShell biasa (bukan admin), jalankan:

```powershell
wsl --list --verbose
```

Pastikan ada distro Linux dengan VERSION 2.

---

## 2. Install Ubuntu di WSL

Kalau langkah 1 belum menginstall Ubuntu:

```powershell
wsl --install -d Ubuntu
```

1. Tunggu download selesai.
2. Jendela Ubuntu akan terbuka — buat **username** dan **password** Linux (bebas, misal `dev` / `dev123`).
3. Update paket dasar:

```bash
sudo apt update && sudo apt upgrade -y
```

---

## 3. Install Docker di WSL

Kita install **Docker Engine langsung di dalam WSL** (tanpa Docker Desktop — lebih ringan dan gratis).

Di terminal Ubuntu (WSL), jalankan satu per satu:

```bash
# 1. Install dependensi
sudo apt install -y ca-certificates curl gnupg

# 2. Tambah GPG key resmi Docker
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# 3. Tambah repository Docker
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# 4. Install Docker Engine + plugin Compose
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# 5. Agar bisa pakai docker tanpa sudo
sudo usermod -aG docker $USER
```

**Penting:** tutup terminal Ubuntu lalu buka lagi (agar grup `docker` aktif).

### Verifikasi

```bash
docker --version
docker compose version
```

Keduanya harus menampilkan nomor versi tanpa error permission.

### Jalankan Docker saat WSL start (opsional tapi disarankan)

Docker Engine tidak auto-start di WSL. Jalankan manual tiap buka terminal:

```bash
sudo service docker start
```

Atau tambah ke `~/.bashrc` agar otomatis:

```bash
echo 'sudo service docker start > /dev/null 2>&1' >> ~/.bashrc
```

---

## 4. Clone Repository

Di terminal Ubuntu:

```bash
# Masuk ke home directory
cd ~

# Clone repo
git clone https://github.com/OmmniDevv/api-ujikom.git

# Masuk ke folder project
cd api-ujikom
```

---

## 5. Konfigurasi Environment

```bash
# Copy template env
cp backend/.env.example backend/.env

# Generate APP_KEY (wajib)
cd backend
php artisan key:generate --no-interaction 2>/dev/null || echo "PHP belum ada di WSL, key akan di-generate otomatis oleh container"
cd ..
```

> Catatan: container Docker akan menjalankan `composer install` dan setup otomatis, jadi tidak perlu install PHP/Composer di WSL secara manual.

### Sesuaikan password database (opsional)

Password default di `docker-compose.yml`:

| Variabel | Default |
|---|---|
| `MYSQL_ROOT_PASSWORD` | `root123` |
| `MYSQL_PASSWORD` | `api_ujikom123` |

Untuk pemakaian lokal, default ini cukup. Untuk server/production, **wajib diganti**.

---

## 6. Jalankan dengan Docker Compose

Di folder `api-ujikom` (sejajar dengan `docker-compose.yml`):

```bash
# Build & jalankan semua container (pertama kali agak lama: download image + install dependency)
docker compose up -d --build
```

Tunggu 3–5 menit. Container `app-laravel` akan otomatis:

1. Install ekstensi PHP yang dibutuhkan
2. Install Composer + dependency Laravel
3. Install Node.js + build asset frontend
4. Jalankan **migration database** (termasuk enum `ditolak`)
5. Menjalankan server di port 8000

### Cek status container

```bash
docker compose ps
```

Semua harus berstatus `Up` / `healthy`:

```
NAME            STATUS
MySQL-API       Up (healthy)
laravel-api     Up
PhpMyadmin-API  Up
```

### Lihat log kalau ada masalah

```bash
# Log semua
docker compose logs -f

# Log container tertentu
docker compose logs -f app-laravel
```

Tekan `Ctrl+C` untuk keluar dari log.

---

## 7. Akses Aplikasi

| Layanan | URL | Keterangan |
|---|---|---|
| Aplikasi SiPinjam | http://localhost:8000 | Laravel |
| phpMyAdmin | http://localhost:8081 | Kelola database via browser |

**Login phpMyAdmin:**

- Server: `mysql-server` (sudah terisi otomatis)
- Username: `api_ujikom`
- Password: `api_ujikom123`

### Buat akun admin pertama (via tinker)

```bash
docker compose exec app-laravel php artisan tinker
```

Lalu di dalam tinker:

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@sipinjam.local',
    'password' => bcrypt('admin123'),
    'role' => 'admin',
]);
exit
```

> Sesuaikan field dengan struktur tabel `users` jika berbeda (cek migration).

---

## 8. Perintah Sehari-hari

```bash
# Masuk ke folder project dulu
cd ~/api-ujikom

# Nyalakan semua container
docker compose up -d

# Matikan semua container (data MySQL tetap tersimpan di volume)
docker compose down

# Matikan + hapus data database (reset total, hati-hati!)
docker compose down -v

# Jalankan migration baru
docker compose exec app-laravel php artisan migrate --force

# Masuk ke shell container Laravel
docker compose exec app-laravel bash

# Update kode dari GitHub lalu restart
git pull
docker compose up -d --build
```

---

## 9. Troubleshooting

### `permission denied` saat jalankan docker

Grup `docker` belum aktif. Tutup terminal WSL dan buka lagi. Atau logout/login ulang Windows.

### Port 8000 / 3306 / 8081 sudah dipakai

Cek aplikasi yang memakai port tersebut (misal MySQL lokal / XAMPP). Matikan dulu, atau ubah mapping port di `docker-compose.yml`:

```yaml
ports:
  - "8001:8000"   # akses via http://localhost:8001
```

### Container `app-laravel` restart terus

Lihat log untuk tau penyebabnya:

```bash
docker compose logs app-laravel --tail 50
```

Penyebab umum: koneksi ke MySQL belum siap (tunggu healthcheck), atau `composer install` gagal karena internet putus — jalankan ulang `docker compose up -d --build`.

### Migration gagal: `table already exists`

Database sudah punya tabel dari run sebelumnya tapi migration tracking-nya hilang. Reset volume lalu ulang:

```bash
docker compose down -v
docker compose up -d --build
```

### Aplikasi lemot di WSL

Pastikan project disimpan di filesystem Linux (`~/api-ujikom`), **bukan** di `/mnt/c/...`. Akses file Windows dari WSL jauh lebih lambat.

### `docker compose` tidak dikenal

Pastikan plugin terinstall: `docker compose version`. Kalau belum, ulangi langkah 3 bagian install `docker-compose-plugin`.

---

## Ringkasan Arsitektur

```
┌─────────────────────────────────────────────┐
│  WSL2 (Ubuntu)                              │
│                                             │
│  ┌──────────────┐  ┌──────────────┐         │
│  │  app-laravel │  │ mysql-server │         │
│  │  php:8.4-cli │  │ mysql:8.0    │         │
│  │  :8000       │  │ :3306        │         │
│  └──────────────┘  └──────────────┘         │
│                                             │
│  ┌──────────────┐                           │
│  │ phpmyadmin   │                           │
│  │ :8081        │                           │
│  └──────────────┘                           │
└─────────────────────────────────────────────┘
```

Selamat ngoding! 🚀
