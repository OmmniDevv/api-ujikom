# Rencana Fix 2 Bug Temuan Testing (86 test case, 8 Okt 2026)

Status: DRAFT — dikerjakan hanya setelah user bilang "benerin".
Sumber temuan: `~/workspace/your_files/Laporan-Hasil-Testing-SiPinjam.pdf`
Test case yang gagal: `pjm-pos-006`, `kbli-pos-003`.

## Bug 1 — pjm-pos-006: "tolak peminjaman" menghapus data

**Root cause** — `backend/app/Services/PeminjamanService.php`, method `tolak()` (~baris 84–103):
baris ~96 memanggil `$peminjaman->delete();` — record dihapus permanen dari DB, bukan diubah statusnya.

**Kendala tambahan**: kolom `peminjaman.status` adalah ENUM yang TIDAK punya nilai `ditolak`.
Definisi: `backend/database/migrations/2026_07_29_033021_04_create_peminjaman_table.php`
nilai saat ini: `diajukan | dipinjam | dikembalikan | telat`.

**Langkah fix:**
1. Buat migrasi baru `xxxx_add_ditolak_to_peminjaman_status.php`:
   - MySQL 8 (docker-compose): `DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan','dipinjam','dikembalikan','telat','ditolak') DEFAULT 'diajukan'")`
   - SQLite (dev .env): lewati statement ini (SQLite tidak enforce enum; guard dengan `DB::getDriverName()`).
2. Di `PeminjamanService::tolak()`: ganti `$peminjaman->delete();` menjadi
   `$peminjaman->update(['status' => 'ditolak']);`
   Stok restore loop di atasnya TETAP (sudah benar).
3. Tidak perlu ubah view: badge status di `resources/views/petugas/peminjaman/index.blade.php:86`
   me-render status apa pun via `ucfirst()` → "Ditolak" tampil otomatis.
   Tombol Tolak/Setujui di tabel index hanya muncul saat status `diajukan` (baris 67) → aman.
4. `indexPeminjaman()` memfilter `where('status','diajukan')` → record ditolak tidak mengotori
   daftar pengajuan. Laporan (`laporan*`) hanya ambil dipinjam/dikembalikan/telat → tidak berubah.
5. Opsional: tambah filter "Ditolak" di halaman riwayat bila user minta.
6. Re-test: pjm-pos-006 (tolak → status jadi `ditolak`, record tetap ada, stok kembali).

## Bug 2 — kbli-pos-003: form pengembalian tidak ada kolom denda manual

**Root cause** — `backend/app/Services/PengembalianService.php:29`:
`$denda = $terlambatHari * 5000;` selalu otomatis. Form
`backend/resources/views/petugas/pengembalian/create.blade.php` tidak punya input denda
(hanya banner info "Denda otomatis dihitung Rp 5.000/hari", baris ~73).

**Langkah fix** (denda manual = override opsional, default tetap otomatis):
1. `backend/app/Services/PengembalianService.php::proses()`:
   tambah param `?int $dendaManual = null`, ganti baris 29 menjadi
   `$denda = $dendaManual ?? $terlambatHari * 5000;`
2. `backend/app/Http/Requests/Pengembalian/StorePengembalianRequest.php`:
   tambah rule `'denda_manual' => ['nullable', 'integer', 'min:0']`
   + message: `'denda_manual.min' => 'Denda manual harus berupa angka ≥ 0.'`
3. `backend/app/Http/Controllers/PengembalianController.php::store()`:
   teruskan `$request->denda_manual` ke `$this->service->proses(...)` (argumen ke-6).
4. `resources/views/petugas/pengembalian/create.blade.php`:
   tambah input number opsional: "Denda manual (Rp) — kosongkan untuk hitung otomatis Rp 5.000/hari".
5. Activity log: tandai bila denda manual dipakai (bedakan dari otomatis).
6. Re-test: kbli-pos-003 (input denda 200000 → tersimpan 200000).

## Estimasi

Kecil: 2 service + 1 request + 1 controller + 1 blade + 1 migrasi. Tanpa perubahan skema
selain penambahan nilai enum. Tidak menyentuh alur lain.
