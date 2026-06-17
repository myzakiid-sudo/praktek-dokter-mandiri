# Sistem Informasi Praktek Dokter Mandiri

Aplikasi Sistem Informasi Manajemen Klinik dan Praktek Dokter Mandiri berbasis Web. Proyek ini dikembangkan sebagai Tugas Akhir Mata Kuliah Pemrograman SQL.

## 👨‍💻 Pengembang (Kelompok)

- Valentino Febrian Sanggoleo Hamid (Project Manager / QA)
- Fariz Danadyaksa (Database Architect)
- Yohanes Andri Bobola (Database Architect)
- Prabu Rayyansyah R. K. (Backend Developer)
- Muhammad Yusuf Zaki (Frontend Developer)

## 🛠️ Teknologi yang Digunakan

- **Database:** MariaDB / MySQL (InnoDB Storage Engine)
- **Backend:** PHP Native (Versi 7.4 / 8.0+)
- **Frontend:** HTML5 & Tailwind CSS (via CDN)
- **Web Server:** Apache (XAMPP)

## 📦 Fitur Utama (8 Modul Terintegrasi)

1. **Manajemen Pasien:** CRUD data master pasien.
2. **Manajemen Kunjungan:** Pendaftaran antrean pasien ke dokter.
3. **Rekam Medis:** Pencatatan hasil pemeriksaan (anamnesa & diagnosa klinis) menggunakan Transaction.
4. **Manajemen Resep:** Pembuatan resep obat dengan integrasi potong stok otomatis.
5. **Manajemen Stok Obat:** Restock obat dan riwayat mutasi barang.
6. **Tagihan & Pembayaran:** Perhitungan total tagihan otomatis (Auto-calculate via Trigger) dan pelunasan.
7. **Laporan & Analitik:** Rekapitulasi pendapatan dan peringatan stok kritis menggunakan SQL Views.
8. **User Management:** Pembatasan hak akses berbasis Role (Authorization).

## 🚀 Panduan Instalasi (Cara Menjalankan Aplikasi)

1. Pastikan XAMPP (Apache & MySQL) sudah terinstal dan berjalan.
2. Pindahkan seluruh folder proyek ini ke dalam direktori `C:\xampp\htdocs\`.
3. Buka phpMyAdmin (http://localhost/phpmyadmin) atau DBeaver.
4. Buat database baru bernama `db_praktek_dokter`.
5. Import file SQL final (`THP5-db_praktek_dokter.sql`) ke dalam database tersebut.
6. Akses aplikasi melalui browser: `http://localhost/nama_folder_proyek`
7. Gunakan kredensial berikut untuk login:
   - **Username:** admin_yusuf
   - **Password:** rahasia123

## 📂 Struktur File Database (.sql)

Penyimpanan file SQL dibagi menjadi beberapa tahap sesuai instruksi penugasan:

- `THP1-db_praktek_dokter.sql` : Skema DDL dan Insert Data Dummy.
- `THP2-db_praktek_dokter.sql` : DML, Complex Queries, dan Views.
- `THP3-db_praktek_dokter.sql` : Stored Procedures & Functions.
- `THP4-db_praktek_dokter.sql` : Database Triggers.
- `THP5-db_praktek_dokter.sql` : Full Dump Database (Final Release).
