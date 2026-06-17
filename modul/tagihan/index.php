<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';
include '../../includes/header.php';

$query = "SELECT * FROM v_laporan_tagihan ORDER BY tanggal_tagihan DESC";
$result = $koneksi->query($query);
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
                    <span class="ml-1 md:ml-2 text-gray-700">Tagihan & Pembayaran</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-2">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tagihan & Pembayaran</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola keuangan dan pembayaran pasien.</p>
        </div>
        <a href="tambah.php" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-150 shadow-sm focus:outline-none">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Buat Tagihan Baru
        </a>
    </div>

    <div class="overflow-x-auto ring-1 ring-gray-200 rounded-lg">
        <table class="min-w-full bg-white text-left whitespace-nowrap">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">ID Tagihan</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Tanggal</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Nama Pasien</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Total (Rp)</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out group">
                    <td class="py-4 px-5 text-sm font-bold text-blue-600">INV-<?php echo $row['tagihan_id']; ?></td>
                    <td class="py-4 px-5 text-sm text-gray-600 group-hover:text-blue-900"><?php echo date('d M Y', strtotime($row['tanggal_tagihan'])); ?></td>
                    <td class="py-4 px-5 text-sm font-medium text-gray-900 group-hover:text-blue-900"><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-900 font-bold group-hover:text-blue-900"><?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?></td>
                    <td class="py-4 px-5 text-sm">
                        <?php if($row['status'] == 'Lunas'): ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Lunas</span>
                        <?php else: ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">Belum Lunas</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-5 text-sm text-center">
                        <a href="detail.php?id=<?php echo $row['tagihan_id']; ?>" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-md hover:bg-indigo-600 hover:text-white transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Kelola
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">
                        <p class="mt-2 text-sm font-medium">Belum ada tagihan.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>