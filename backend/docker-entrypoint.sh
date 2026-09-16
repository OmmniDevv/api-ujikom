#!/bin/bash
set -e

echo "======================================"
echo " Laravel Startup Script"
echo "======================================"

# 1. Tunggu MySQL siap (retry sampai 60 detik)
echo "[1/6] Menunggu MySQL siap..."
for i in $(seq 1 30); do
    php -r "
        try {
            \$pdo = new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD'),
                [PDO::ATTR_TIMEOUT => 2]
            );
            echo 'MySQL OK' . PHP_EOL;
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    " && break
    echo "   MySQL belum siap, coba lagi ($i/30)..."
    sleep 2
done

# 2. Generate APP_KEY jika belum ada
echo "[2/6] Cek APP_KEY..."
if php artisan key:show 2>/dev/null | grep -q "base64:"; then
    echo "   APP_KEY sudah ada, skip."
else
    echo "   Generate APP_KEY..."
    php artisan key:generate --force
fi

# 3. Build Vite assets (Tailwind CSS)
echo "[3/6] Build Vite assets..."
npm run build

# 4. Jalankan migrasi
echo "[4/6] Menjalankan migrasi..."
php artisan migrate --force

# 5. Buat symlink storage (public disk)
echo "[5/6] Storage link..."
php artisan storage:link --force 2>/dev/null || true

# 6. Clear & cache config
echo "[6/6] Clear cache..."
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo ""
echo "======================================"
echo " Aplikasi siap di http://localhost:8000"
echo "======================================"
echo ""

# Jalankan Laravel
exec php artisan serve --host=0.0.0.0 --port=8000
