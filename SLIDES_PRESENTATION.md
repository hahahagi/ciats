# Draft Slide Presentasi - CIATS (Cloud Inventory and Asset Tracking System)

Berikut adalah struktur slide yang disarankan untuk presentasi Ujian Akhir Semester (UAS) Anda.

---

## Slide 1: Judul

**Judul Besar:** CIATS
**Sub-judul:** Cloud Inventory and Asset Tracking System
**Detail:**

-   Nama Mahasiswa: [Nama Anda]
-   NIM: [NIM Anda]
-   Mata Kuliah: [Nama Mata Kuliah]

---

## Slide 2: Apa itu CIATS?

**Deskripsi:**
CIATS (Cloud Inventory and Asset Tracking System) adalah aplikasi berbasis web yang dirancang untuk mempermudah pengelolaan aset IT di dalam organisasi.

**Fungsi Utama:**

-   **Sentralisasi Data:** Menyimpan seluruh data aset komputer dalam satu database yang mudah diakses.
-   **Pelacakan (Tracking):** Memantau lokasi dan status penggunaan aset secara real-time.
-   **Manajemen Peminjaman:** Menggantikan sistem peminjaman manual dengan alur digital yang terstruktur.

---

## Slide 3: Teknologi yang Digunakan (Tech Stack)

**Backend:**

-   **Laravel:** Framework PHP utama untuk logika aplikasi, routing, dan keamanan.
-   **Firebase:** Integrasi untuk fitur real-time atau penyimpanan data tambahan.

**Frontend:**

-   **Blade Templates:** Templating engine bawaan Laravel.
-   **Tailwind CSS:** Framework CSS untuk desain antarmuka yang modern dan responsif.

**Fitur Khusus:**

-   **QR Code Generator:** `simplesoftwareio/simple-qrcode` untuk label aset.
-   **QR Scanner:** Fitur scan via kamera web untuk identifikasi cepat.

---

## Slide 4: Aktor & Hak Akses (User Roles)

Sistem membagi pengguna menjadi 3 level akses:

1. **Admin:**
    - Akses penuh ke seluruh sistem.
    - Manajemen User, Laporan, dan Konfigurasi.
2. **Operator:**
    - Fokus pada operasional harian.
    - Mengelola Data Aset (CRUD), Approval Peminjaman, Proses Check-in/Check-out.
3. **Employee (User):**
    - Melihat katalog aset.
    - Mengajukan permohonan peminjaman.
    - Melihat riwayat peminjaman pribadi.

---

## Slide 5: Fitur Utama - Manajemen Aset

**Visual:** Screenshot halaman daftar aset atau form tambah aset.
**Poin:**

-   Pencatatan detail aset (Nama, Kategori, Lokasi, Spesifikasi).
-   **QR Code Generation:** Setiap aset memiliki QR Code unik yang dapat dicetak dan ditempel.
-   Status Aset: Tersedia, Sedang Dipinjam, Maintenance, Rusak.

---

## Slide 6: Fitur Utama - Alur Peminjaman (Transaction Flow)

**Diagram Alur:**

1. **Request:** Employee memilih aset dari katalog -> Klik "Ajukan Peminjaman".
2. **Approval:** Operator/Admin menerima notifikasi -> Menyetujui (Approve) atau Menolak (Reject).
3. **Check-out:** Jika disetujui, Operator menyerahkan barang -> Klik "Check-out" (Status aset berubah menjadi "Dipinjam").
4. **Check-in:** Employee mengembalikan barang -> Operator melakukan "Check-in" (Status aset kembali "Tersedia").

---

## Slide 7: Fitur Unggulan - QR Code Scanner

**Visual:** Screenshot halaman Scanner (`/scanner`).
**Fungsi:**

-   Memungkinkan Operator untuk memindai label aset menggunakan kamera HP/Laptop.
-   **Aksi Cepat:** Setelah scan, sistem langsung menampilkan detail aset atau halaman proses Check-in/Check-out tanpa perlu mengetik ID aset manual.

---

## Slide 8: Dashboard & Reporting

**Visual:** Screenshot Dashboard Admin/Operator.
**Poin:**

-   **Statistik Real-time:** Jumlah total aset, aset dipinjam, dan request pending.
-   **Audit Logger:** Merekam siapa melakukan apa dan kapan (keamanan data).
-   **Laporan:** Rekapitulasi penggunaan aset untuk manajemen.

---

## Slide 9: Kesimpulan

-   CIATS berhasil mendigitalkan proses manajemen aset IT.
-   Meminimalisir human error dan kehilangan aset.
-   Mempercepat layanan peminjaman bagi karyawan.
-   **Pengembangan Selanjutnya:** Integrasi notifikasi WhatsApp/Email atau Mobile App.

---

## Slide 10: Penutup / Q&A

-   Terima Kasih.
-   Sesi Tanya Jawab.
