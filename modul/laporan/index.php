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

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="mb-6">
        <a href="../../dashboard.php" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-semibold">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <h2 class="text-3xl font-bold text-gray-800 mb-2">Laporan & Analitik Klinik</h2>
    <p class="text-sm text-gray-500 mb-8">Rangkuman eksekutif keuangan dan peringatan inventaris apotek.</p>

    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 text-white mb-8">
        <h3 class="text-lg font-semibold mb-2 opacity-80">Total Pendapatan (Tagihan Lunas)</h3>
        <p class="text-4xl font-bold">Rp <?php echo number_format($pendapatan ?? 0, 0, ',', '.'); ?></p>
        <p class="text-sm mt-2 opacity-75">Berdasarkan data <i>v_laporan_tagihan</i></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="border border-red-200 rounded-lg overflow-hidden">
            <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                <h3 class="font-bold text-red-800">⚠️ Peringatan Stok Obat Kritis</h3>
            </div>
            <div class="p-0">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="py-2 px-4 text-left">Nama Obat</th>
                            <th class="py-2 px-4 text-center">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php while($row = $stok_kritis_query->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 text-gray-800 font-medium"><?php echo htmlspecialchars($row['nama_obat']); ?></td>
                            <td class="py-2 px-4 text-center font-bold text-red-600"><?php echo $row['stok']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($stok_kritis_query->num_rows == 0): ?>
                        <tr><td colspan="2" class="py-4 text-center text-gray-500">Semua stok obat dalam kondisi aman.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border border-orange-200 rounded-lg overflow-hidden">
            <div class="bg-orange-50 px-4 py-3 border-b border-orange-200">
                <h3 class="font-bold text-orange-800">⏳ Obat Kadaluarsa Tahun Ini</h3>
            </div>
            <div class="p-0">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="py-2 px-4 text-left">Nama Obat</th>
                            <th class="py-2 px-4 text-left">Tgl Kadaluarsa</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php while($row = $kadaluarsa_query->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 text-gray-800 font-medium"><?php echo htmlspecialchars($row['nama_obat']); ?></td>
                            <td class="py-2 px-4 text-orange-700"><?php echo date('d M Y', strtotime($row['tgl_kadaluarsa'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($kadaluarsa_query->num_rows == 0): ?>
                        <tr><td colspan="2" class="py-4 text-center text-gray-500">Tidak ada obat yang kadaluarsa tahun ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>