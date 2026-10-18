#!/bin/bash
#
# install-and-start.sh — Instalasi & menjalankan SiPinjam otomatis di WSL + Docker Desktop
#
# Cara pakai:
#   chmod +x install-and-start.sh
#   ./install-and-start.sh
#
# Script ini akan:
#   1. Cek Docker & Docker Compose tersedia
#   2. Setup file .env (copy dari template + generate key otomatis via container)
#   3. Build & jalankan semua container
#   4. Tunggu sampai database siap
#   5. Verifikasi aplikasi NYALA
#

set -e

# Warna untuk output
HIJAU='\033[0;32m'
KUNING='\033[1;33m'
MERAH='\033[0;31m'
BIRU='\033[0;34m'
RESET='\033[0m'

info()    { echo -e "${BIRU}[INFO]${RESET} $1"; }
sukses()  { echo -e "${HIJAU}[OK]${RESET} $1"; }
peringatan() { echo -e "${KUNING}[!]${RESET} $1"; }
gagal()   { echo -e "${MERAH}[GAGAL]${RESET} $1"; exit 1; }

echo ""
echo "=============================================="
echo "  SiPinjam — Auto Install & Start (WSL+Docker)"
echo "=============================================="
echo ""

# ── 1. Cek Docker ──────────────────────────────────────────────
info "Mengecek Docker..."

if ! command -v docker &> /dev/null; then
    gagal "Docker tidak ditemukan!

    Pastikan:
    1. Docker Desktop sudah terinstall & berjalan di Windows
    2. Settings → Resources → WSL Integration → Ubuntu tercentang
    3. Tutup terminal ini dan buka lagi, lalu jalankan script sekali lagi."
fi
sukses "Docker ditemukan: $(docker --version)"

if ! docker compose version &> /dev/null; then
    gagal "Docker Compose tidak ditemukan. Update Docker Desktop ke versi terbaru."
fi
sukses "Docker Compose ditemukan: $(docker compose version --short)"

# Cek Docker daemon jalan
if ! docker info &> /dev/null; then
    gagal "Docker daemon tidak jalan!

    Buka Docker Desktop di Windows dan tunggu sampai status 'Running',
    lalu jalankan script ini lagi."
fi
sukses "Docker daemon berjalan"

# ── 2. Cek posisi direktori ────────────────────────────────────
if [ ! -f "docker-compose.yml" ]; then
    gagal "File docker-compose.yml tidak ditemukan!

    Jalankan script ini dari folder api-ujikom:
      cd ~/api-ujikom
      ./install-and-start.sh"
fi
sukses "Berada di folder project yang benar"

# ── 3. Setup .env ──────────────────────────────────────────────
info "Menyiapkan file .env..."

if [ ! -f "backend/.env" ]; then
    cp backend/.env.example backend/.env
    sukses "backend/.env dibuat dari template"
else
    peringatan "backend/.env sudah ada, dilewati (tidak ditimpa)"
fi

# ── 4. Build & jalankan container ──────────────────────────────
info "Menjalankan Docker Compose (ini butuh beberapa menit saat pertama kali)..."

docker compose up -d --build

sukses "Perintah docker compose dikirim"

# ── 5. Tunggu MySQL siap ──────────────────────────────────────
info "Menunggu database MySQL siap..."

SEBELUM=0
for i in $(seq 1 24); do
    if docker compose exec -T mysql-server mysqladmin ping -h localhost -u api_ujikom --password=api_ujikom123 &> /dev/null; then
        echo ""
        sukses "MySQL siap!"
        break
    fi
    printf "."
    sleep 5
    if [ "$i" -eq 24 ]; then
        echo ""
        peringatan "MySQL belum merespon setelah 2 menit. Cek log:"
        echo "  docker compose logs mysql-server"
    fi
done

# ── 6. Tunggu Laravel selesai setup ────────────────────────────
info "Menunggu Laravel selesai install dependency + migration..."
info "(composer install + npm build bisa makan waktu 3-5 menit pertama kali)"

for i in $(seq 1 60); do
    if docker compose exec -T app-laravel php artisan migrate:status &> /dev/null; then
        echo ""
        sukses "Laravel siap & database ter-migrate!"
        break
    fi
    # Tampilkan progress tiap 30 detik
    if [ $((i % 6)) -eq 0 ]; then
        echo ""
        info "Masih proses... (${i}x5 detik)"
    else
        printf "."
    fi
    sleep 5
    if [ "$i" -eq 60 ]; then
        echo ""
        gagal "Laravel tidak siap setelah 5 menit. Cek log:
  docker compose logs app-laravel"
    fi
done

# ── 7. Verifikasi akhir ────────────────────────────────────────
echo ""
echo "=============================================="
echo "  VERIFIKASI"
echo "=============================================="

# Cek APP_KEY
KEY=$(docker compose exec -T app-laravel grep '^APP_KEY=' .env 2>/dev/null | cut -d= -f2)
if [ -n "$KEY" ] && [ "$KEY" != "" ]; then
    sukses "APP_KEY terisi"
else
    peringatan "APP_KEY kosong — generate manual: docker compose exec app-laravel php artisan key:generate"
fi

# Cek HTTP response
sleep 3
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000 2>/dev/null || echo "000")
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
    sukses "Aplikasi merespon HTTP $HTTP_CODE di http://localhost:8000"
else
    peringatan "Aplikasi belum merespon (HTTP $HTTP_CODE). Tunggu 1 menit lagi lalu coba buka browser."
fi

# Cek phpMyAdmin
PMA_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8081 2>/dev/null || echo "000")
if [ "$PMA_CODE" = "200" ]; then
    sukses "phpMyAdmin merespon di http://localhost:8081"
else
    peringatan "phpMyAdmin belum merespon (HTTP $PMA_CODE)"
fi

echo ""
echo "=============================================="
echo -e "  ${HIJAU}SELESAI! Aplikasi SiPinjam sudah NYALA 🎉${RESET}"
echo "=============================================="
echo ""
echo "  Aplikasi   : http://localhost:8000"
echo "  phpMyAdmin : http://localhost:8081"
echo "               user: api_ujikom / pass: api_ujikom123"
echo ""
echo "  Perintah berguna:"
echo "    docker compose ps          # lihat status container"
echo "    docker compose logs -f     # lihat log"
echo "    docker compose down        # matikan (data tersimpan)"
echo ""
