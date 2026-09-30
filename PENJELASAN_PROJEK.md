# Sistem Peminjaman Alat — Penjelasan Projek untuk Presentasi

## 1. Gambaran Umum

Aplikasi web untuk mengelola peminjaman alat (laboratorium/inventaris) dengan tiga peran pengguna:

| Role    | Hak Akses                                        |
|---------|--------------------------------------------------|
| Admin   | Kelola user, kategori, alat; lihat semua data     |
| Petugas | Setujui/tolak peminjaman, proses pengembalian, cetak laporan (PDF/Excel) |
| Peminjam| Lihat katalog, ajukan peminjaman, lihat riwayat sendiri |

**Stack:** Laravel 12 · PHP 8.4 · MySQL 8 · Sanctum (API token) · DomPDF · OpenSpout (Excel) · Blade + Vite/Tailwind

---

## 2. Alur Bisnis Utama

### 2.1 Ajukan Peminjaman (Peminjam)

```
Peminjam pilih alat + jumlah + tanggal pinjam/kembali
        ↓
StorePeminjamanRequest validasi input
        ↓
PeminjamanService::ajukan()
  ├─ DB::beginTransaction()
  ├─ Buat record peminjaman (status = 'diajukan')
  ├─ Loop tiap alat: lockForUpdate → cek stok → decrement stok
  ├─ Simpan DetailPinjam per alat
  └─ DB::commit()
        ↓
Status akhir: 'diajukan', stok sudah di-reserve
```

Kenapa stok turun saat diajukan? Biar nggak ada dua orang ngambil alat yang sama sekaligus. Kalau ditolak, stok balik lagi.

### 2.2 Persetujuan / Penolakan (Petugas)

```
Petugas lihat daftar status='diajukan'
        ↓
Setujui → PeminjamanService::approve()
  └─ Update status 'diajukan' → 'dipinjam' (stok TIDAK diubah lagi, sudah reserve)

Tolak → PeminjamanService::tolak()
  ├─ DB::beginTransaction()
  ├─ Loop detail: increment stok balik
  ├─ Delete record peminjaman
  └─ DB::commit()
```

### 2.3 Pengembalian (Petugas)

```
Petugas pilih peminjaman status='dipinjam'
        ↓
PengembalianService::proses()
  ├─ Hitung terlambat = selisih tgl_kembali vs tgl_kembali_plan
  ├─ Denda = hari_telat × Rp5.000
  ├─ Loop detail: increment stok, update kondisi alat jika rusak
  ├─ Buat record Pengembalian
  └─ Status peminjaman → 'dikembalikan' atau 'telat'
```

### 2.4 Laporan & Export

Petugas/Admin bisa export seluruh data peminjaman (yang sudah diproses) ke PDF (DomPDF, A4 landscape) atau Excel (OpenSpout).

---

## 3. Struktur Database

```
users
 ├── id, name, email, password, role(admin/petugas/peminjam), no_hp, alamat, foto_profile
 │
kategori
 ├── id, nama_kategori, deskripsi
 │
alat
 ├── id, kategori_id(FK), nama_alat, stok, status_kondisi(baik/rusak/perbaikan), deskripsi, gambar
 │      CHECK (stok >= 0)  ← constraint DB biar nggak negatif
 │
peminjaman
 ├── id, user_id(FK), tgl_pinjam, tgl_kembali_plan, status(diajukan/dipinjam/dikembalikan/telat)
 │
detail_pinjam
 ├── id, peminjaman_id(FK), alat_id(FK), jumlah
 │
pengembalian
 ├── id, peminjaman_id(FK UNIQUE), tgl_kembali, kondisi_kembali, denda, petugas_id(FK)
 │
log_aktivitas
 ├── id, user_id(FK nullable), aktivitas, keterangan, created_at
 │
personal_access_tokens  ← Sanctum untuk API auth
```

Relasi utama:
- User 1—∞ Peminjaman
- Peminjaman 1—∞ DetailPinjam ∞—1 Alat
- Peminjaman 1—0..1 Pengembalian
- Kategori 1—∞ Alat

---

## 4. Routing & Middleware

File: `routes/web.php`

```php
Route::middleware('guest')->group(...)          // login form
Route::match(['get','post'],'/logout')->middleware('auth')

Route::prefix('admin')->middleware(['auth','role:admin'])
Route::prefix('petugas')->middleware(['auth','role:petugas,admin'])
Route::prefix('peminjam')->middleware(['auth','role:peminjam'])
```

Middleware custom `CheckRole`:
```php
class CheckRole {
    public function handle(Request $request, Closure $next, string ...$roles): Response {
        if (! auth()->check()) return redirect()->route('login');
        if (! in_array(auth()->user()->role, $roles)) abort(403);
        return $next($request);
    }
}
```
Daftar alias di `bootstrap/app.php`: `$middleware->alias(['role' => CheckRole::class])`

---

## 5. Penjelasan Sintaks Penting (untuk presentasi)

### 5.1 Dependency Injection via Constructor

```php
class PeminjamanController extends Controller
{
    public function __construct(private PeminjamanService $service) {}
}
```

`private PeminjamanService $service` = constructor property promotion (PHP 8+). Laravel auto-resolve class `PeminjamanService` dan inject tanpa perlu binding manual. Manfaat: mudah di-test (ganti service palsu), tidak perlu `new Service()` di setiap method.

### 5.2 Form Request (Validasi Terpisah)

```php
public function store(StorePeminjamanRequest $request) { ... }
```

Alih-alih validasi inline di controller, aturan ditaruh di class terpisah (`app/Http/Requests/...`). Laravel jalankan validasi SEBELUM masuk method. Kalau gagal, otomatis redirect back + error messages. Controller jadi bersih.

Contoh rules:
```php
'tgl_pinjam' => ['required', 'date', 'after_or_equal:today'],
'detail.*.alat_id' => ['required', 'exists:alat,id'],
'detail.*.jumlah' => ['required', 'integer', 'min:1'],
```

`exists:alat,id` = cek ke DB bahwa ID itu benar-benar ada di tabel `alat`.

### 5.3 Route Model Binding Implisit

```php
public function show(Peminjaman $peminjaman) { ... }
```

URL `/petugas/peminjaman/42` → Laravel cari `Peminjaman::find(42)` otomatis. Kalau tidak ketemu, langsung 404. Nggak perlu `Peminjaman::findOrFail($id)` manual.

### 5.4 Transaction + Locking (Keamanan Stok)

```php
DB::beginTransaction();
try {
    $alat = Alat::lockForUpdate()->findOrFail($item['alat_id']);
    if (! $alat->stokCukup($item['jumlah'])) { DB::rollBack(); return [...]; }
    $alat->decrement('stok', $item['jumlah']);
    ...
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

- `lockForUpdate()` = SELECT ... FOR UPDATE di MySQL. Baris terkunci sampai transaction selesai → request lain yang mau baca baris sama harus nunggu. Mencegah race condition dua orang ambil alat bersamaan.
- `DB::rollBack()` = batalkan semua perubahan dalam transaction (stok balik seperti semula).
- `ponytail:` comment di kode = catatan sengaja simplify; upgrade path kalau scale-out.

### 5.5 Eloquent Relationships

```php
// Model Peminjaman
public function user()    { return $this->belongsTo(User::class); }
public function detailPinjam() { return $this->hasMany(DetailPinjam::class); }
public function pengembalian() { return $this->hasOne(Pengembalian::class); }
```

Eager loading di controller:
```php
Peminjaman::with(['user', 'detailPinjam.alat'])->get();
```
`with()` = 1 query JOIN-ish, bukan N+1 query. Penting buat performa.

### 5.6 Query Scopes (Custom Filter Reusable)

```php
// Di model Peminjaman
public function scopeByStatus($query, ?string $status) {
    return $status ? $query->where('status', $status) : $query;
}
```

Pemakaian: `Peminjaman::byStatus($request->get('status'))->latest()->paginate(10)`

Scope = method reusable yang bisa dirangkai chain. Bersih, tidak duplikasi `if ($status) ->where(...)` di mana-mana.

### 5.7 ActivityLogger (Audit Trail)

```php
ActivityLogger::log('Approve Peminjaman', "Peminjaman #{$id} disetujui");
```

Simpan ke tabel `log_aktivitas` + file log Laravel. Dashboard admin baca tabel ini buat tampil "aktivitas terbaru". Kalau gagal simpan DB, fallback ke `Log::error()` supaya app tidak crash cuma karena logging gagal.

### 5.8 Blade Template Engine (View)

```blade
@extends('layouts.app')
@section('content')
  @foreach($peminjamans as $p)
    <tr><td>{{ $p->user->name }}</td></tr>
  @endforeach
@endsection
```

- `@extends` = inherit layout dasar (navbar, sidebar)
- `{{ }}` = escape HTML (anti XSS)
- `{!! !!}` = raw HTML (hati-hati, jangan pakai untuk input user)

### 5.9 CSRF Protection

Semua form POST/PUT/DELETE wajib bawa token:
```blade
<form method="POST" action="...">
  @csrf
  ...
</form>
```

Laravel generate token unik per session. Tanpa token → 419 Page Expired. Ini cegah cross-site request forgery.

Catatan: route `logout` di-exclude dari CSRF (`validateCsrfTokens(except: ['logout'])`) supaya GET /logout bisa diakses langsung. Trade-off keamanan kecil demi kemudahan demo.

### 5.10 Docker Compose (Dev Environment)

```yaml
services:
  app-laravel:    php:8.4-cli + artisan serve
  mysql-server:   mysql:8.0
  phpmyadmin:     phpmyadmin:latest
```

Benefit: satu perintah `docker compose up` → semua jalan. Gampang didemoin ke guru tanpa install PHP/MySQL lokal.

---

## 6. Fitur Tambahan

- **Upload gambar**: `$request->file('gambar')->store('alat', 'public')` → simpan ke `storage/app/public/alat/`, symlink via `php artisan storage:link`
- **Paginate + search**: `->search($q)->byStatus($s)->latest()->paginate(10)->withQueryString()` — filter tetap kebawa saat pindah halaman
- **Export PDF**: DomPDF render view Blade → download `.pdf`
- **Export Excel**: OpenSpout stream tulis `.xlsx` tanpa load semua ke memori
- **Health check**: endpoint `/up` bawaan Laravel 12 (HTTP 200 = app hidup)

---

## 7. Kesimpulan untuk Guru

Projek ini menerapkan:
1. MVC pattern (Model–View–Controller) khas Laravel
2. RBAC (Role-Based Access Control) via middleware custom
3. Validasi input terstruktur (Form Request)
4. Transaksi database aman (lock + rollback)
5. Audit trail (log aktivitas)
6. CRUD penuh + relasi antar entitas
7. Export laporan (PDF/Excel)
8. Containerized dev environment (Docker)

Repo: https://github.com/OmmniDevv/api-ujikom