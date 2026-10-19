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
10. [Instalasi OpenCode di WSL](#10-instalasi-opencode-di-wsl-ai-coding-assistant)

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

### Password database

Default di `docker-compose.yml`:

| Variabel | Nilai |
|---|---|
| `MYSQL_DATABASE` | `API_ujikom` |
| `MYSQL_USER` / `MYSQL_PASSWORD` | `api_ujikom` / `api_ujikom123` |
| `MYSQL_ROOT_PASSWORD` | `passwordroot` |

Untuk ujikom/lokal, default ini cukup. Jangan lupa ganti kalau deploy ke server.

---

## 6. Jalankan dengan Docker Compose

> **Jalan pintas:** kalau mau otomatis semua, pakai script:
> ```bash
> chmod +x install-and-start.sh
> ./install-and-start.sh
> ```
> Script ini mengerjakan langkah 5–7 otomatis: cek Docker, setup `.env`, `compose up`, tunggu MySQL + Laravel siap, lalu verifikasi sampai nyala. Aman dijalankan ulang.
>
> Atau ikuti langkah manual di bawah:

Di folder `api-ujikom` (sejajar dengan `docker-compose.yml`):

```bash
# Build & jalankan semua container
# Pertama kali 3-5 menit: build image (PHP+ext+composer+node sekali aja via Dockerfile)
# Up berikutnya jauh lebih cepat karena image sudah jadi
docker compose up -d --build
```

Container `app-laravel` akan otomatis:

1. Install dependency Laravel via Composer (cepat kalau `vendor/` sudah ada)
2. Build asset frontend via npm (sekali aja, di-skip kalau `public/build` sudah ada)
3. Generate `APP_KEY` otomatis (kalau masih kosong)
4. Jalankan **migration database**
5. Menjalankan server di port 8000

### Cek status container

```bash
docker compose ps
```

Semua harus berstatus `Up`:

```
NAME             STATUS
MySQL-API        Up
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
# Harus: MySQL-API (Up), laravel-api (Up), PhpMyadmin-API (Up)
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

> **Cara cepat:** 90% masalah selesai dengan urutan ini:
> ```bash
> cd ~/api-ujikom
> docker compose down
> docker compose up -d --build
> docker compose logs -f app-laravel   # lihat apa yang error
> ```

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

### Container `app-laravel` restart terus / Exit 1

```bash
docker compose logs app-laravel --tail 50
```

Penyebab umum + solusi:

| Gejala di log | Penyebab | Solusi |
|---|---|---|
| `Connection refused` / `SQLSTATE[HY000] [2002]` | MySQL belum siap saat Laravel start | Tunggu 1-2 menit, atau `docker compose restart app-laravel` |
| `No application encryption key` | APP_KEY kosong | `docker compose exec app-laravel php artisan key:generate` lalu `docker compose restart app-laravel` |
| `composer install` gagal / timeout | Internet putus saat download | `docker compose up -d --build` ulang |
| `npm install` / `npm run build` gagal | Internet putus atau memori kurang | `docker compose exec app-laravel npm run build` manual setelah internet stabil |
| `Permission denied` saat tulis file | UID container ≠ UID host | Pastikan `user: "1000:1000"` di docker-compose.yml sesuai UID WSL (`id -u`) |

### Error 500 saat buka http://localhost:8000

1. Cek log: `docker compose logs app-laravel --tail 30`
2. Cek APP_KEY: `docker compose exec app-laravel grep '^APP_KEY=' .env` (jangan kosong)
3. Cek koneksi DB: `docker compose exec app-laravel php artisan migrate:status`
4. Bersihkan cache: `docker compose exec app-laravel php artisan config:clear && php artisan cache:clear`

### Migration gagal

**`table already exists`:**
Database sudah punya tabel tapi tracking migration hilang. Reset total:
```bash
docker compose down -v   # HATI-HATI: hapus semua data!
docker compose up -d --build
```

**`Unknown database 'API_ujikom'`:**
MySQL belum selesai init. Tunggu 1 menit lalu:
```bash
docker compose exec app-laravel php artisan migrate --force
```

**Setelah `git pull` ada migration baru:**
```bash
docker compose exec app-laravel php artisan migrate --force
```

### phpMyAdmin tidak bisa login

- Pastikan container mysql sudah `Up`: `docker compose ps`
- Username: `api_ujikom` / Password: `api_ujikom123` (bukan root!)
- Kalau tetap gagal, cek password di `docker-compose.yml` bagian `mysql-server` → `MYSQL_PASSWORD`

### Gambar alat tidak muncul (404)

Storage link belum dibuat atau terhapus:
```bash
docker compose exec app-laravel php artisan storage:link --force
```

### Aplikasi lemot

1. Pastikan repo di-clone di `~/api-ujikom` (filesystem Linux), **bukan** di `/mnt/c/...` (5-10x lebih lambat).
2. Di Docker Desktop → Settings → Resources, naikkan alokasi Memory minimal 4GB.
3. Matikan container yang tidak dipakai: `docker compose down` saat selesai.

### Internet kampus/sekolah memblokir download

- Pakai hotspot HP untuk `docker compose up --build` pertama kali (download image + composer + npm).
- Setelah image terdownload, tidak butuh internet lagi kecuali `composer install`/`npm install` ulang.
- Kalau npm diblokir: `docker compose exec app-laravel npm config set registry https://registry.npmmirror.com` (mirror China, biasanya tidak diblokir).

### Disk penuh (`no space left on device`)

Bersihkan image/container yang tidak dipakai:
```bash
docker system prune -a --volumes
# HATI-HATI: ini hapus SEMUA image & volume yang tidak dipakai, termasuk data MySQL!
# Backup dulu kalau data penting.
```

Versi aman (hanya yang benar-benar sampah):
```bash
docker system prune
```

### Mau mulai dari NOL total (reset pabrik)

```bash
cd ~/api-ujikom
docker compose down -v          # hapus container + volume database
rm -rf backend/vendor backend/node_modules backend/public/build
docker compose up -d --build    # build ulang dari nol
```

### Script `install-and-start.sh` error di tengah jalan

Script ini idempotent (aman dijalankan ulang). Cukup jalankan lagi:
```bash
./install-and-start.sh
```
Dia akan skip langkah yang sudah beres (`.env` tidak ditimpa, `APP_KEY` tidak di-generate ulang).

---

## 10. Instalasi OpenCode di WSL (AI Coding Assistant)

OpenCode adalah AI coding assistant yang jalan di terminal — bisa bantu nulis, debug, dan refactor kode langsung dari WSL. Cocok buat nemenin ngoding ujikom.

### Install (via npm)

Di terminal Ubuntu — pastikan Node.js sudah terinstall:

```bash
# Cek node sudah ada atau belum
node --version

# Kalau belum ada, install dulu:
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash -
sudo apt install -y nodejs

# Install OpenCode global via npm
npm install -g opencode-ai
```

Tunggu sampai selesai. Lalu verifikasi:

```bash
opencode --version
```

### Setup API Key (pilih salah satu)

OpenCode butuh API key dari provider AI. Yang gratis/murah untuk pelajar:

**Opsi A — Google Gemini (gratis, disarankan):**

1. Ambil API key gratis di https://aistudio.google.com/apikey
2. Set di WSL:

```bash
echo 'export GEMINI_API_KEY="isi_key_kamu"' >> ~/.bashrc
source ~/.bashrc
```

**Opsi B — Anthropic / OpenAI (berbayar):**

```bash
echo 'export ANTHROPIC_API_KEY="isi_key_kamu"' >> ~/.bashrc
source ~/.bashrc
```

### Cara Pakai

```bash
# Masuk ke folder project
cd ~/api-ujikom

# Jalankan OpenCode
opencode
```

Perintah dasar di dalam OpenCode:

| Perintah | Fungsi |
|---|---|
| Ketik pertanyaan langsung | Minta AI bantu (misal: "jelaskan fungsi tolak di PeminjamanService") |
| `/help` | Lihat semua perintah |
| `/model` | Ganti model AI |
| `Esc` | Keluar |

### Tips buat ujikom

- Minta OpenCode jelaskan alur kode sebelum presentasi: `"jelaskan alur pengembalian dari controller sampai database"`
- Minta carikan bug: `"cek apakah ada bug di fungsi proses PengembalianService"`
- Jangan copy-paste buta — pahami dulu penjelasannya biar bisa jawab saat ditanya penguji.

---

## Ringkasan Arsitektur

```
┌──────────────────────────────────────────────┐
│  Windows + Docker Desktop (WSL2 backend)      │
│                                              │
│  ┌──────────────┐  ┌──────────────┐          │
│  │ app-laravel  │  │ mysql-server │          │
│  │ (Dockerfile) │──│ mysql:8.0    │          │
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
