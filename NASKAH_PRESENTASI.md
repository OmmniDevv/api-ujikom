# Naskah Presentasi — Sistem Peminjaman Alat (API-UJIKOM)

Durasi: ±10 menit · Bahasa Indonesia formal · Untuk guru pembimbing


---

## PEMBUKAAN (±1 menit)

Assalamualaikum warahmatullahi wabarakatuh. Selamat pagi/siang, Bapak/Ibu Guru Pembimbing.

Perkenalkan, nama saya [NAMA]. Pada kesempatan ini saya akan mempresentasikan proyek akhir yang telah saya kerjakan, yaitu **Sistem Informasi Peminjaman Alat**. Proyek ini dibangun menggunakan framework Laravel sebagai basis pengembangan web.

Sebelum masuk ke pembahasan teknis, izinkan saya menjelaskan latar belakang mengapa sistem ini dibuat.


---

## LATAR BELAKANG & MASALAH (±1,5 menit)

Di lingkungan sekolah maupun laboratorium, kegiatan peminjaman alat masih banyak dilakukan secara manual, misalnya pencatatan di buku besar atau spreadsheet terpisah. Cara seperti ini menimbulkan beberapa permasalahan nyata.

Pertama, pencatatan manual rawan salah dan hilang. Kedua, stok alat tidak terpantau secara langsung sehingga sering terjadi tumpang tindih peminjaman. Ketiga, proses persetujuan lambat karena harus menemui petugas satu per satu. Dan keempat, rekap laporan peminjaman memakan waktu lama ketika dibutuhkan mendadak.

Dari empat masalah tersebut, lahirlah tujuan proyek ini: membangun sebuah sistem digital yang mampu mencatat, memantau stok, menyetujui peminjaman, serta menghasilkan laporan secara otomatis dalam satu wadah terpadu.


---

## SOLUSI YANG SAYA TAWARKAN (±2 menit)

Solusi yang saya bangun adalah aplikasi web berbasis peran pengguna. Artinya, setiap orang memiliki hak akses berbeda sesuai jabatannya. Ada tiga peran utama dalam sistem ini.

Yang pertama adalah **Administrator**. Administrator bertugas mengelola seluruh data induk, yaitu menambah dan mengubah akun pengguna, kategori alat, serta daftar alat beserta stoknya. Administrator juga dapat melihat keseluruhan riwayat peminjaman tanpa ikut campur pada proses operasional harian.

Yang kedua adalah **Petugas**. Petugas merupakan ujung tombak operasional. Perannya adalah meninjau pengajuan dari peminjam, lalu menyetujui atau menolak. Ketika alat dikembalikan, petugaslah yang memproses pengembalian sekaligus menghitung denda apabila terlambat. Petugas juga berwenang mencetak laporan dalam bentuk PDF maupun Excel.

Yang ketiga adalah **Peminjam**, biasanya siswa atau anggota lab. Peminjam dapat melihat katalog alat yang tersedia, mengajukan peminjaman dengan memilih alat dan tanggal, serta memantau status pengajuannya sendiri, apakah masih menunggu, sedang dipinjam, sudah kembali, atau telat.

Ketiga peran ini saling terhubung membentuk satu alur kerja yang utuh, mulai dari pengajuan hingga pelaporan.


---

## ALUR KERJA SISTEM (±2 menit)

Sekarang saya jelaskan bagaimana sistem bekerja dari awal sampai akhir.

Proses dimulai ketika seorang peminjam membuka katalog dan menemukan alat yang ia butuhkan. Ia kemudian mengisi formulir pengajuan, mencakup jenis alat, jumlah unit, tanggal pinjam, serta rencana tanggal kembali. Begitu diajukan, sistem langsung melakukan dua hal penting secara bersamaan: menyimpan data pengajuan dan mengurangi stok alat sejumlah yang diminta. Pengurangan stok ini bersifat sementara, berfungsi sebagai penahanan agar alat tersebut tidak diambil orang lain selama pengajuan masih aktif. Status pengajuan saat ini menjadi "Diajukan", artinya menunggu keputusan petugas.

Selanjutnya, petugas menerima notifikasi adanya pengajuan baru. Petugas memeriksa kelayakan permintaan tersebut. Apabila disetujui, status berubah menjadi "Dipinjam" tanpa menyentuh stok lagi, karena stok sudah ditahan sejak awal. Namun apabila ditolak, sistem secara otomatis mengembalikan stok yang tadi ditahan, lalu menghapus catatan pengajuan. Dengan mekanisme ini, stok tidak pernah bocor atau berkurang sia-sia akibat pengajuan yang gagal.

Tahap berikutnya adalah pengembalian. Saat alat diserahkan kembali kepada petugas, petugas memasukkan tanggal aktual pengembalian serta kondisi alat. Di sinilah logika perhitungan berjalan. Sistem membandingkan tanggal kembali sebenarnya dengan rencana semula. Jika meleset lebih lambat, selisih hari dihitung sebagai keterlambatan, lalu dikalikan tarif denda tertentu. Selain itu, jika kondisi alat dilaporkan rusak, status alat ikut diperbarui. Setelah semua tercatat, status peminjaman berubah menjadi "Dikembalikan" atau "Telat" tergantung hasil perhitungan.

Terakhir, seluruh data yang terkumpul dapat disajikan sebagai laporan. Petugas cukup menekan tombol untuk mengunduh rekap dalam format PDF atau Excel, lengkap dengan rincian peminjam, alat, tanggal, dan denda. Laporan inilah yang menjawab kebutuhan administrasi yang sebelumnya lambat.


---

## FITUR UNGGULAN & NILAI TAMBAH (±1 menit)

Ada beberapa aspek yang menurut saya menjadi nilai jual sistem ini dibandingkan cara manual.

Satu, pemantauan stok berlangsung real-time berkat penahanan stok otomatis, sehingga risiko kelebihan peminjaman dapat ditekan. Dua, jejak audit lengkap; setiap tindakan login, pengajuan, persetujuan, penolakan, hingga pengembalian terekam dalam log aktivitas, berguna bila suatu saat perlu penelusuran. Tiga, validasi ketat pada setiap input demi menjaga integritas data. Empat, fleksibilitas laporan melalui ekspor dokumen siap cetak. Dan lima, keamanan berlapis dengan proteksi CSRF serta pembatasan akses berdasarkan peran.


---

## TEKNOLOGI YANG DIGUNAKAN (±30 detik)

Untuk sisi teknologi, proyek ini dibangun di atas framework Laravel versi terbaru dengan bahasa pemrograman PHP dan basis data MySQL. Antarmuka depan menggunakan template Blade yang dibantu Tailwind CSS agar tampilan rapi dan responsif. Saya juga memanfaatkan Docker sebagai wadah pengembangan, sehingga lingkungan kerja konsisten dan mudah dijalankan ulang oleh siapa pun tanpa konfigurasi rumit.


---

## PENUTUP (±30 detik)

Demikian presentasi mengenai Sistem Informasi Peminjaman Alat yang telah saya kembangkan. Melalui proyek ini, saya berharap dapat memberikan solusi sederhana namun berdampak bagi efisiensi pengelolaan alat di lingkungan kita.

Saya menyadari sistem ini masih memiliki ruang penyempurnaan, misalnya penambahan fitur notifikasi email atau integrasi pembayaran denda, yang semoga dapat dikembangkan pada masa mendatang.

Demikian yang dapat saya sampaikan. Terima kasih atas perhatian Bapak/Ibu. Saya membuka sesi tanya jawab apabila ada hal yang ingin didalami.

Wassalamualaikum warahmatullahi wabarakatuh.


---

## CATATAN UNTUK DIRI SENDIRI (tidak dibaca saat presentasi)

- Isi tanda [NAMA] sebelum tampil.
- Total durasi target 9–10 menit; beri jeda napas antar bagian.
- Bila guru bertanya "kenapa pakai Laravel?", jawab singkat: ekosistem matang, MVC jelas, keamanan bawaan baik, komunitas luas untuk dukungan jangka panjang.
- Bila ditanya "bagaimana mencegah stok minus?", jawab: stok ditahan saat pengajuan memakai transaksi database dengan kunci baris, jadi tidak bisa berkurang di bawah nol meski banyak peminjam serentak.
- Cetak halaman ini atau simpan di HP sebagai contekan poin per bagian.