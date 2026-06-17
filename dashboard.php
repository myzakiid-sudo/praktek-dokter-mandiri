<?php
session_start();
require_once 'includes/koneksi.php';

// Proteksi Halaman: Wajib Login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'includes/header.php';
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p class="text-gray-600 mt-2">Ini adalah dashboard utama Sistem Informasi Praktek Dokter Mandiri.</p>
    
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <h3 class="font-bold text-blue-700">Data Pasien</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola data master pasien.</p>
            <a href="modul/pasien/index.php" class="mt-3 inline-block text-sm text-blue-600 font-semibold hover:underline">Buka Modul &rarr;</a>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <h3 class="font-bold text-green-700">Kunjungan</h3>
            <p class="text-sm text-gray-600 mt-1">Pendaftaran pasien hari ini.</p>
            <a href="modul/kunjungan/index.php" class="mt-3 inline-block text-sm text-green-600 font-semibold hover:underline">Buka Modul &rarr;</a>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-100">
            <h3 class="font-bold text-red-700">Rekam Medis</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola riwayat klinis pasien.</p>
            <a href="modul/rekam_medis/index.php" class="mt-3 inline-block text-sm text-red-600 font-semibold hover:underline">Buka Modul &rarr;</a>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <h3 class="font-bold text-yellow-700">Resep Obat</h3>
            <p class="text-sm text-gray-600 mt-1">Pembuatan resep dan potong stok.</p>
            <a href="modul/resep/index.php" class="mt-3 inline-block text-sm text-yellow-600 font-semibold hover:underline">Buka Modul &rarr;</a>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
            <h3 class="font-bold text-purple-700">Manajemen Stok</h3>
            <p class="text-sm text-gray-600 mt-1">Pantau stok obat di apotek.</p>
            <a href="#" class="mt-3 inline-block text-sm text-purple-600 font-semibold hover:underline">Buka Modul &rarr;</a>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>