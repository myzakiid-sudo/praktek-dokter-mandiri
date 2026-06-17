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

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-4">
    <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p class="text-gray-500 mt-2 text-lg">Ini adalah dashboard utama Sistem Informasi Praktek Dokter Mandiri.</p>
    
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card: Data Pasien -->
        <a href="modul/pasien/index.php" class="block group bg-white p-6 rounded-xl border border-blue-100 shadow-sm hover:shadow-md hover:border-blue-300 transition duration-150 ease-in-out">
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-blue-700 transition duration-150">Data Pasien</h3>
            </div>
            <p class="text-sm text-gray-500 mt-1">Kelola data master pasien.</p>
            <span class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 group-hover:text-blue-800">
                Buka Modul <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        </a>
        
        <!-- Card: Kunjungan -->
        <a href="modul/kunjungan/index.php" class="block group bg-white p-6 rounded-xl border border-green-100 shadow-sm hover:shadow-md hover:border-green-300 transition duration-150 ease-in-out">
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-green-700 transition duration-150">Kunjungan</h3>
            </div>
            <p class="text-sm text-gray-500 mt-1">Pendaftaran pasien hari ini.</p>
            <span class="mt-4 inline-flex items-center text-sm font-semibold text-green-600 group-hover:text-green-800">
                Buka Modul <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        </a>
        
        <!-- Card: Rekam Medis -->
        <a href="modul/rekam_medis/index.php" class="block group bg-white p-6 rounded-xl border border-red-100 shadow-sm hover:shadow-md hover:border-red-300 transition duration-150 ease-in-out">
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-red-700 transition duration-150">Rekam Medis</h3>
            </div>
            <p class="text-sm text-gray-500 mt-1">Kelola riwayat klinis pasien.</p>
            <span class="mt-4 inline-flex items-center text-sm font-semibold text-red-600 group-hover:text-red-800">
                Buka Modul <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        </a>
        
        <!-- Card: Resep Obat -->
        <a href="modul/resep/index.php" class="block group bg-white p-6 rounded-xl border border-yellow-100 shadow-sm hover:shadow-md hover:border-yellow-300 transition duration-150 ease-in-out">
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-yellow-50 text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-yellow-700 transition duration-150">Resep Obat</h3>
            </div>
            <p class="text-sm text-gray-500 mt-1">Pembuatan resep & potong stok.</p>
            <span class="mt-4 inline-flex items-center text-sm font-semibold text-yellow-600 group-hover:text-yellow-800">
                Buka Modul <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        </a>
        
        <!-- Card: Manajemen Stok -->
        <a href="modul/stok/index.php" class="block group bg-white p-6 rounded-xl border border-purple-100 shadow-sm hover:shadow-md hover:border-purple-300 transition duration-150 ease-in-out">
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-purple-700 transition duration-150">Manajemen Stok</h3>
            </div>
            <p class="text-sm text-gray-500 mt-1">Pantau stok obat di apotek.</p>
            <span class="mt-4 inline-flex items-center text-sm font-semibold text-purple-600 group-hover:text-purple-800">
                Buka Modul <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        </a>

        <!-- Card: Tagihan & Pembayaran -->
        <a href="modul/tagihan/index.php" class="block group bg-white p-6 rounded-xl border border-indigo-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition duration-150 ease-in-out">
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-indigo-700 transition duration-150">Tagihan</h3>
            </div>
            <p class="text-sm text-gray-500 mt-1">Kelola tagihan dan status lunas.</p>
            <span class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-600 group-hover:text-indigo-800">
                Buka Modul <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        </a>

        <!-- Card: Laporan & Analitik -->
        <a href="modul/laporan/index.php" class="block group bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-400 transition duration-150 ease-in-out">
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-gray-100 text-gray-700 group-hover:bg-gray-700 group-hover:text-white transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <h3 class="ml-4 text-xl font-bold text-gray-800 group-hover:text-gray-900 transition duration-150">Laporan & Analitik</h3>
            </div>
            <p class="text-sm text-gray-500 mt-1">Rekap keuangan dan notifikasi.</p>
            <span class="mt-4 inline-flex items-center text-sm font-semibold text-gray-700 group-hover:text-gray-900">
                Buka Laporan <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        </a>

        <?php if($_SESSION['role_id'] == 1): ?>
        <!-- Card: Manajemen User -->
        <a href="modul/user/index.php" class="block group bg-slate-800 p-6 rounded-xl border border-slate-700 shadow-sm hover:shadow-lg hover:bg-slate-900 transition duration-150 ease-in-out">
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-slate-700 text-slate-300 group-hover:bg-slate-600 group-hover:text-white transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="ml-4 text-xl font-bold text-white transition duration-150">Manajemen User</h3>
            </div>
            <p class="text-sm text-slate-300 mt-1">Kelola akun dan hak akses pegawai.</p>
            <span class="mt-4 inline-flex items-center text-sm font-semibold text-blue-400 group-hover:text-blue-300">
                Buka Modul <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </span>
        </a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>