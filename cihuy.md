Saya punya project Laravel di direktori ini. Backend/logic sudah saya buat
(controller, model, migration, dsb) tapi FRONTEND/VIEW BELUM ADA SAMA SEKALI —
jadi kamu perlu membuat tampilan dari nol, bukan mengedit tampilan yang sudah ada.

Ada dua file PDF acuan yang tersimpan di root direktori project ini:
1. API-UJIKOM.pdf
2. Membuat_Hak_akses_dan_crud_user_dan_kategori.pdf

Gunakan skill "UI/UX Promax" yang sudah terpasang untuk seluruh pekerjaan desain
di bawah ini.

Tolong lakukan langkah-langkah berikut secara berurutan:

TAHAP 1 — AUDIT PROJECT YANG SUDAH ADA (BACKEND SAJA)
1. Baca dan ekstrak isi API-UJIKOM.pdf untuk memahami spesifikasi/requirement awal
   project ini (struktur database, fitur yang diminta, alur bisnis, dsb).
2. Telusuri struktur backend project (routes, controllers, models, migrations)
   dan bandingkan dengan requirement di PDF tersebut. Konfirmasi juga apakah
   folder resources/views memang masih kosong/minim.
3. Laporkan dalam bentuk checklist:
   - Fitur/logika backend yang SUDAH terimplementasi dengan benar
   - Fitur yang SUDAH ada tapi ada bug/tidak sesuai spesifikasi (sebutkan letak
     filenya dan masalahnya)
   - Fitur yang BELUM diimplementasikan sama sekali
   - Bagian kode yang tergolong "spaghetti code" (logika bercampur, controller
     terlalu gemuk, duplikasi logika, penamaan variabel tidak jelas, dsb.)
4. Jangan mengubah kode apa pun dulu di tahap ini, cukup laporkan hasil audit.

TAHAP 2 — LENGKAPI BACKEND SESUAI PDF KEDUA
Setelah audit selesai dan saya konfirmasi, baca isi
Membuat_Hak_akses_dan_crud_user_dan_kategori.pdf secara lengkap, lalu
implementasikan bagian LOGIKA-nya saja dulu (belum view):

1. Middleware hak akses (CheckRole) — buat middleware role-based, daftarkan
   alias-nya di bootstrap/app.php (Laravel 11/12).
2. Controller per role: AdminController, PetugasController, PeminjamController,
   AuthController — sesuaikan logikanya dengan yang dicontohkan di PDF, tapi
   sesuaikan nama model/kolom dengan struktur database project saya yang
   sebenarnya (cek dulu migration/model yang ada, jangan asal copy).
3. Routing di routes/web.php — kelompokkan route per role dengan middleware
   ['auth','role:...'], termasuk route login/logout dan resource CRUD.
4. Logika CRUD User lengkap (index, create, store, edit, update, destroy)
   dengan fitur search dan pagination.
5. Logika CRUD Kategori lengkap (index, create, store, edit, update, destroy),
   termasuk validasi agar kategori yang masih dipakai alat tidak bisa dihapus,
   plus search & pagination.
6. Jalankan `php artisan route:list` dan cek tidak ada route bentrok/duplikat.

TAHAP 3 — BUAT FRONTEND DARI NOL (TEMA API)
Karena belum ada view sama sekali, buat seluruh tampilan dari awal untuk semua
halaman yang dibutuhkan backend di atas: login, layout utama (sidebar+navbar),
dashboard admin, CRUD user, CRUD kategori, CRUD alat, katalog peminjam, kelola
peminjaman & pengembalian petugas. Ketentuan desain:

1. Tema visual: "Fire theme" — dominan warna merah api, oranye, dan aksen gelap
   (charcoal/hitam) sebagai kontras, dengan gradasi seperti nyala api.
2. Gaya "liquid glass" (glassmorphism cair): elemen kartu/panel memakai efek
   frosted glass (backdrop-blur, transparansi, border tipis bercahaya, shadow
   lembut, sudut membulat), dengan sentuhan highlight seperti pantulan cairan/kaca.
3. Pastikan tetap readable & aksesibel (kontras teks vs background tetap layak
   baca meski pakai efek transparan).
4. Konsisten di semua role (admin, petugas, peminjam) — buat SATU layout utama
   (resources/views/layouts/app.blade.php) yang dipakai bersama, jangan bikin
   layout terpisah tiap role.
5. Gunakan Tailwind (via CDN atau build, sesuaikan dengan apa yang sudah
   ter-setup di project) dengan variabel warna custom "fire-*" di config theme,
   alih-alih inline class panjang berulang.
6. Buat partial/component Blade untuk elemen yang berulang (card, button,
   badge status, table wrapper, form group, dsb) supaya tidak duplikasi markup.
7. Setelah frontend jadi, sambungkan ke controller (pastikan setiap method
   controller return view yang benar dan variabel yang dikirim sesuai kebutuhan
   Blade-nya).

TAHAP 4 — LOG SERVER
Tambahkan sistem log server agar aktivitas penting tercatat dan mudah dipantau:
1. Pastikan Laravel logging (config/logging.php) aktif dan rapi — gunakan channel
   "daily" agar log terbagi per hari (storage/logs/laravel-YYYY-MM-DD.log).
2. Tambahkan logging eksplisit (Log::info / Log::warning / Log::error) pada
   aksi-aksi penting: login/logout, gagal login, CRUD user, CRUD kategori, CRUD
   alat, pengajuan peminjaman, persetujuan peminjaman, dan proses pengembalian —
   sertakan konteks yang berguna (user id, aksi, waktu) tapi JANGAN pernah log
   password atau data sensitif.
3. Jika ada tabel/model LogAktivitas yang dipakai untuk dashboard admin, pastikan
   log ke file (Laravel log) dan log ke database (LogAktivitas) berjalan
   berdampingan tanpa duplikasi logika — buat satu service/helper kecil (misal
   ActivityLogger) supaya pemanggilannya konsisten dan tidak diulang-ulang di
   tiap controller.
4. Tunjukkan cara saya bisa memantau log ini (tail file log, atau lihat di
   halaman Log Aktivitas admin).

TAHAP 5 — REFAKTOR KUALITAS KODE (ANTI SPAGHETTI, RAMAH PEMULA)
1. Perbaiki controller yang terlalu gemuk: pisahkan logika bisnis yang kompleks
   (misal proses approve peminjaman, proses pengembalian dengan transaksi DB)
   ke dalam Service class atau method model yang jelas namanya, controller
   cukup memanggilnya.
2. Gunakan Form Request class (php artisan make:request) untuk validasi yang
   panjang, alih-alih menumpuk validasi di dalam method controller.
3. Konsisten penamaan: method, variabel, dan relasi model memakai konvensi yang
   jelas dan seragam (camelCase untuk method/variabel, singular/plural yang
   benar untuk relasi).
4. Tambahkan komentar singkat di bagian logika yang non-trivial (transaksi DB,
   pengurangan/pengembalian stok, dsb.) supaya mudah dipahami pemula.
5. Hilangkan duplikasi kode (mis. logika query search+pagination yang mirip di
   beberapa method) dengan menariknya ke scope/method reusable di model, atau
   trait jika dipakai lintas controller.
6. Setelah refactor, jalankan project dan pastikan semua fitur tetap berjalan
   (regresi nol).

ATURAN PENTING:
- Sesuaikan semua nama tabel/kolom/relasi dengan skema database project saya
  yang sebenarnya (jangan asumsikan sama persis dengan contoh di PDF).
- Setelah setiap perubahan besar, jalankan migration/test dasar untuk memastikan
  tidak error (misal `php artisan migrate:status`, buka route penting).
- Tunjukkan diff/ringkasan perubahan tiap file yang kamu edit atau buat.
- Jika ada bagian PDF yang bentrok dengan struktur project saya yang sebenarnya,
  atau ada keputusan desain yang ambigu, tanyakan dulu ke saya sebelum
  memutuskan sendiri.
- Kerjakan tahap demi tahap sesuai urutan di atas, jangan loncat tahap tanpa
  konfirmasi saya.
