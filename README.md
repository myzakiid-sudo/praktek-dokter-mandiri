# Sistem Informasi Praktek Dokter Mandiri

Proyek Tugas Akhir Mata Kuliah Pemrograman SQL. Aplikasi ini dibangun untuk mendigitalisasi alur operasional klinik mandiri, mulai dari pendaftaran pasien, rekam medis, hingga manajemen apotek dan tagihan.

## 🛠️ Tech Stack

- **Database:** MariaDB/MySQL (InnoDB)
- **Backend:** PHP 8 (Native/MySQLi)
- **Frontend:** HTML5, Tailwind CSS (via CDN)
- **Architecture:** Procedural with Component-based Layouting

## 📂 Struktur Folder Saat Ini

```text
praktek_dokter/
├── assets/                  # Penyimpanan media statis
├── includes/
│   ├── koneksi.php          # Koneksi ke db_praktek_dokter
│   ├── header.php           # Template UI atas & Logika Sesi
│   └── footer.php           # Template UI bawah & Copyright
├── modul/
│   ├── pasien/              # (Draft)
│   ├── kunjungan/           # (Draft)
│   ├── rekam_medis/         # (Draft)
│   ├── resep/               # (Draft)
│   ├── stok/                # (Draft)
│   ├── tagihan/             # (Draft)
│   ├── laporan/             # (Draft)
│   └── user/                # (Draft)
├── dashboard.php            # Halaman Utama (Protected Route)
├── index.php                # Halaman Login
├── logout.php               # Penghancur Sesi
└── README.md                # Dokumentasi Proyek
```
