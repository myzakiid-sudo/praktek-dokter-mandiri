<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';
include '../../includes/header.php';

// 1. Mengambil Rekap Pendapatan dari View Laporan Tagihan (Hanya yang Lunas)
$pendapatan_query = $koneksi->query("SELECT SUM(total_tagihan) AS total_pendapatan FROM v_laporan_tagihan WHERE status = 'Lunas'");
$pendapatan = $pendapatan_query->fetch_assoc()['total_pendapatan'];

// 2. Mengambil View Stok Obat Kritis (Stok < 50)
$stok_kritis_query = $koneksi->query("SELECT * FROM v_stok_obat_kritis");

// 3. Mengambil View Obat Hampir Kadaluarsa (Tahun Ini)
$kadaluarsa_query = $koneksi->query("SELECT * FROM v_obat_hampir_kadaluarsa");
?>

<div class="mb-4">
    <nav class="flex text-sm text-gray-500 font-medium" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="../../dashboard.php" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Laporan & Analitik</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-2">
    <h2 class="text-3xl font-bold text-gray-800 mb-2 tracking-tight">Laporan & Analitik Klinik</h2>
    <p class="text-sm text-gray-500 mb-8">Rangkuman eksekutif keuangan dan peringatan inventaris apotek.</p>

    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-8 text-white mb-8 relative overflow-hidden">
        <svg class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 text-indigo-400 opacity-20" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path></svg>
        <h3 class="text-lg font-semibold mb-2 opacity-90 relative z-10">Total Pendapatan (Tagihan Lunas)</h3>
        <p class="text-4xl md:text-5xl font-bold relative z-10">Rp <?php echo number_format($pendapatan ?? 0, 0, ',', '.'); ?></p>
        <p class="text-sm mt-3 opacity-75 relative z-10 font-medium">Berdasarkan data <i>v_laporan_tagihan</i></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Tabel Stok Kritis -->
        <div class="border border-red-100 rounded-xl overflow-hidden shadow-sm">
            <div class="bg-red-50/80 px-5 py-4 border-b border-red-100 flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="font-bold text-red-800 tracking-wide uppercase text-sm">Peringatan Stok Obat Kritis</h3>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="min-w-full bg-white text-left whitespace-nowrap">
                    <thead class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="py-3 px-5">Nama Obat</th>
                            <th class="py-3 px-5 text-center">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-50">
                        <?php while($row = $stok_kritis_query->fetch_assoc()): ?>
                        <tr class="hover:bg-red-50/50 transition duration-150">
                            <td class="py-3 px-5 text-gray-800 font-medium"><?php echo htmlspecialchars($row['nama_obat']); ?></td>
                            <td class="py-3 px-5 text-center font-bold text-red-600">
                                <span class="bg-red-100 text-red-800 py-1 px-2.5 rounded-full text-xs"><?php echo $row['stok']; ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($stok_kritis_query->num_rows == 0): ?>
                        <tr><td colspan="2" class="py-8 text-center text-gray-500">
                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-500 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p>Semua stok obat dalam kondisi aman.</p>
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Obat Kadaluarsa -->
        <div class="border border-orange-100 rounded-xl overflow-hidden shadow-sm">
            <div class="bg-orange-50/80 px-5 py-4 border-b border-orange-100 flex items-center">
                <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="font-bold text-orange-800 tracking-wide uppercase text-sm">Obat Kadaluarsa Tahun Ini</h3>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="min-w-full bg-white text-left whitespace-nowrap">
                    <thead class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="py-3 px-5">Nama Obat</th>
                            <th class="py-3 px-5">Tgl Kadaluarsa</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-50">
                        <?php while($row = $kadaluarsa_query->fetch_assoc()): ?>
                        <tr class="hover:bg-orange-50/50 transition duration-150">
                            <td class="py-3 px-5 text-gray-800 font-medium"><?php echo htmlspecialchars($row['nama_obat']); ?></td>
                            <td class="py-3 px-5 font-semibold text-orange-700"><?php echo date('d M Y', strtotime($row['tgl_kadaluarsa'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($kadaluarsa_query->num_rows == 0): ?>
                        <tr><td colspan="2" class="py-8 text-center text-gray-500">
                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-500 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p>Tidak ada obat yang kadaluarsa tahun ini.</p>
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>