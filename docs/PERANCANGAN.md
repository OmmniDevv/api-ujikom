# Perancangan Aplikasi Peminjaman Alat (SiPinjam)

Dokumen ini merangkum perancangan SiPinjam sesuai aplikasi yang benar-benar dibuat: diagram use case, diagram aktivitas, diagram sequence, diagram class, dan ERD. Struktur tabel dan relasi mengikuti migrasi di `backend/database/migrations`, sedangkan aktor dan aktivitas mengikuti route dan peran di aplikasi.

## 1. Diagram Use Case

Tiga aktor memakai sistem dengan hak akses berbeda. Admin mengelola data master, Petugas memproses peminjaman dan pengembalian, Peminjam mengajukan peminjaman.

```mermaid
flowchart LR
    Peminjam([Peminjam])
    Petugas([Petugas])
    Admin([Admin])

    UC1[Register dan Login]
    UC2[Melihat dan mengubah profil /me]
    UC3[Melihat katalog alat]
    UC4[Mengajukan peminjaman]
    UC5[Melihat riwayat peminjaman]
    UC6[Mengajukan pengembalian]
    UC7[Menyetujui atau menolak peminjaman]
    UC8[Mencatat pengembalian dan denda]
    UC9[Membuat laporan PDF dan Excel]
    UC10[CRUD User]
    UC11[CRUD Kategori]
    UC12[CRUD Alat]
    UC13[Melihat analitik]

    Peminjam --> UC1
    Peminjam --> UC2
    Peminjam --> UC3
    Peminjam --> UC4
    Peminjam --> UC5
    Peminjam --> UC6

    Petugas --> UC1
    Petugas --> UC2
    Petugas --> UC7
    Petugas --> UC8
    Petugas --> UC9

    Admin --> UC1
    Admin --> UC2
    Admin --> UC7
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
```

## 2. Diagram Aktivitas

Alur aktivitas pengajuan peminjaman sampai selesai digambarkan sebagai flowchart di `DOKUMENTASI_LENGKAP_ALUR_DAN_LOGIKA.md` bagian 5 (Alur Bisnis Utama). Ringkasnya: Peminjam memilih alat dan tanggal, sistem memeriksa stok dan skor reputasi, Petugas menyetujui atau menolak, alat berstatus dipinjam, lalu Peminjam mengajukan pengembalian dan Petugas mencatat pengembalian beserta denda bila terlambat.

```mermaid
flowchart TD
    A([Peminjam memilih alat di katalog]) --> B[Isi form: tanggal pinjam dan tanggal rencana kembali]
    B --> C{Stok cukup dan reputasi memenuhi syarat?}
    C -- Tidak --> D[Pengajuan gagal, tampil pesan kesalahan]
    C -- Ya --> E[Peminjaman tersimpan dengan status diajukan]
    E --> F{Petugas memeriksa pengajuan}
    F -- Ditolak --> G[Status menjadi ditolak]
    F -- Disetujui --> H[Status menjadi dipinjam, stok berkurang]
    H --> I[Peminjam mengajukan pengembalian]
    I --> J[Petugas mencatat pengembalian dan kondisi alat]
    J --> K{Lewat dari tanggal rencana kembali?}
    K -- Ya --> L[Denda dihitung, status menjadi telat lalu dikembalikan]
    K -- Tidak --> M[Status menjadi dikembalikan]
    L --> N([Stok bertambah, skor reputasi diperbarui])
    M --> N
```

## 3. Diagram Sequence

Urutan interaksi untuk proses peminjaman dari pengajuan sampai pengembalian tercatat.

```mermaid
sequenceDiagram
    actor P as Peminjam
    actor T as Petugas
    participant W as Halaman Web (Blade)
    participant C as PeminjamanController
    participant S as PeminjamanService
    participant DB as Database

    P->>W: Pilih alat, isi tanggal pinjam dan kembali
    W->>C: POST /peminjam/peminjaman
    C->>S: ajukan(user, alat, tanggal)
    S->>DB: Cek stok alat dan status peminjaman aktif
    DB-->>S: Stok tersedia
    S->>DB: Simpan peminjaman (status diajukan) dan detail_pinjam
    S-->>C: Peminjaman tersimpan
    C-->>W: Redirect ke riwayat dengan pesan sukses
    T->>C: POST /petugas/peminjaman/{id}/setujui
    C->>DB: Ubah status menjadi dipinjam, kurangi stok
    DB-->>T: Daftar peminjaman diperbarui
    P->>C: POST /peminjam/peminjaman/{id}/ajukan-kembali
    C->>DB: Ubah status menjadi pengajuan_kembali
    T->>DB: Catat pengembalian, hitung denda, kembalikan stok
    DB-->>T: Status menjadi dikembalikan
```

## 4. Diagram Class

Class utama adalah model Eloquent di `backend/app/Models` beserta relasinya.

```mermaid
classDiagram
    class User {
        +string name
        +string email
        +string password
        +string role
        +int skor_reputasi
        +string no_hp
        +string alamat
        +string foto_profile
    }
    class Kategori {
        +string nama_kategori
        +string deskripsi
        +alat() HasMany
    }
    class Alat {
        +string nama_alat
        +int stok
        +string status_kondisi
        +string deskripsi
        +string gambar
        +kategori() BelongsTo
        +stokCukup(int jumlah) bool
        +masihDipinjam() bool
    }
    class Peminjaman {
        +date tgl_pinjam
        +date tgl_kembali_plan
        +string status
        +user() BelongsTo
        +detailPinjam() HasMany
        +pengembalian() HasOne
    }
    class DetailPinjam {
        +int jumlah
        +peminjaman() BelongsTo
        +alat() BelongsTo
    }
    class Pengembalian {
        +date tgl_kembali
        +string kondisi_kembali
        +int denda
        +string catatan
        +peminjaman() BelongsTo
    }
    class LogAktivitas {
        +string aktivitas
        +string keterangan
        +user() BelongsTo
    }

    User "1" --> "*" Peminjaman : mengajukan
    User "1" --> "*" LogAktivitas : tercatat pada
    Kategori "1" --> "*" Alat : mengelompokkan
    Peminjaman "1" --> "*" DetailPinjam : berisi
    Alat "1" --> "*" DetailPinjam : dipinjam lewat
    Peminjaman "1" --> "0..1" Pengembalian : selesai dengan
```

## 5. ERD (Entity Relationship Diagram)

Tujuh tabel inti hasil migrasi dan relasinya.

```mermaid
erDiagram
    USERS ||--o{ PEMINJAMAN : "mengajukan"
    USERS ||--o{ LOG_AKTIVITAS : "tercatat pada"
    USERS ||--o{ PENGEMBALIAN : "petugas mencatat"
    KATEGORI ||--o{ ALAT : "mengelompokkan"
    PEMINJAMAN ||--o{ DETAIL_PINJAM : "berisi"
    ALAT ||--o{ DETAIL_PINJAM : "dipinjam lewat"
    PEMINJAMAN ||--o| PENGEMBALIAN : "selesai dengan"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin, petugas, peminjam"
        int skor_reputasi
        string no_hp
        text alamat
        string foto_profile
    }
    KATEGORI {
        bigint id PK
        string nama_kategori
        text deskripsi
    }
    ALAT {
        bigint id PK
        bigint kategori_id FK
        string nama_alat
        int stok
        string status_kondisi
        text deskripsi
        string gambar
    }
    PEMINJAMAN {
        bigint id PK
        bigint user_id FK
        date tgl_pinjam
        date tgl_kembali_plan
        enum status "diajukan, dipinjam, dikembalikan, telat, ditolak, pengajuan_kembali"
    }
    DETAIL_PINJAM {
        bigint id PK
        bigint peminjaman_id FK
        bigint alat_id FK
        int jumlah
    }
    PENGEMBALIAN {
        bigint id PK
        bigint peminjaman_id FK
        date tgl_kembali
        string kondisi_kembali
        int denda
        text catatan
        bigint petugas_id FK
    }
    LOG_AKTIVITAS {
        bigint id PK
        bigint user_id FK
        text aktivitas
        text keterangan
    }
```

Status peminjaman mengikuti kolom enum pada migrasi terakhir: diajukan, dipinjam, dikembalikan, telat, ditolak, dan pengajuan_kembali.
