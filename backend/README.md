# API-UJIKOM

Backend API untuk Sistem Peminjaman Alat (UJIKOM).

## Stack
- Laravel 12
- PHP 8.2+
- MySQL 8.0
- Tailwind CSS 4 + Vite

## Setup
```bash
cd backend
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Docker
```bash
cd ..
docker compose up -d
```

## Structure
- `app/Http/Controllers` — Controllers (Auth, User, Kategori, Alat, Peminjaman, Pengembalian, Dashboard, Petugas)
- `app/Http/Middleware/CheckRole.php` — Role-based access middleware
- `app/Services` — Business logic (ActivityLogger, PeminjamanService, PengembalianService)
- `app/Models` — Eloquent models
- `app/Http/Requests` — Form Request validation
- `resources/views` — Blade templates (admin, petugas, peminjam, auth, layouts)
- `routes/web.php` — Web routes grouped by role middleware