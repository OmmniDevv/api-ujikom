<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // CRUD User
        Route::resource('users', UserController::class);

        // CRUD Kategori
        Route::resource('kategoris', KategoriController::class);

        // CRUD Alat
        Route::resource('alats', AlatController::class);

        // Lihat semua peminjaman (read only untuk admin)
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    });

/*
|--------------------------------------------------------------------------
| PETUGAS ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('petugas')
    ->name('petugas.')
    ->middleware(['auth', 'role:petugas,admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'petugas'])->name('dashboard');

        // Kelola peminjaman — PetugasController (sesuai tugas PDF)
        Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
        Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuiPeminjaman'])->name('peminjaman.setujui');
        Route::post('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])->name('peminjaman.tolak');

        // Detail peminjaman (dari PeminjamanController)
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');

        // Proses pengembalian
        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::get('/pengembalian/create', [PengembalianController::class, 'create'])->name('pengembalian.create');
        Route::post('/pengembalian', [PengembalianController::class, 'store'])->name('pengembalian.store');
        Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show'])->name('pengembalian.show');

        // Cetak Laporan
        Route::get('/laporan', [PetugasController::class, 'laporan'])->name('laporan.index');
        Route::get('/laporan/pdf', [PetugasController::class, 'laporanPdf'])->name('laporan.pdf');
        Route::get('/laporan/excel', [PetugasController::class, 'laporanExcel'])->name('laporan.excel');
    });

/*
|--------------------------------------------------------------------------
| PEMINJAM ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('peminjam')
    ->name('peminjam.')
    ->middleware(['auth', 'role:peminjam'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'peminjam'])->name('dashboard');

        // Katalog alat
        Route::get('/katalog', [AlatController::class, 'katalog'])->name('katalog');

        // Ajukan & riwayat peminjaman
        Route::get('/peminjaman', [PeminjamanController::class, 'riwayat'])->name('peminjaman.riwayat');
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    });

/*
|--------------------------------------------------------------------------
| ROOT REDIRECT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            'peminjam' => redirect()->route('peminjam.dashboard'),
            default => redirect()->route('login'),
        };
    }

    return redirect()->route('login');
});
