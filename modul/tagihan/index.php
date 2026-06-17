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

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="mb-4">
        <a href="../../dashboard.php" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-semibold">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tagihan & Pembayaran</h2>
            <p class="text-sm text-gray-500">Kelola keuangan dan pembayaran pasien.</p>
        </div>
        <a href="tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150">
            + Buat Tagihan Baru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">ID Tagihan</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Pasien</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Total (Rp)</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm font-bold text-blue-600">INV-<?php echo $row['tagihan_id']; ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo date('d M Y', strtotime($row['tanggal_tagihan'])); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-bold"><?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?></td>
                    <td class="py-3 px-4 text-sm">
                        <?php if($row['status'] == 'Lunas'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Belum Lunas</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-sm text-center">
                        <a href="detail.php?id=<?php echo $row['tagihan_id']; ?>" class="text-indigo-600 hover:text-indigo-900 font-semibold border border-indigo-600 px-3 py-1 rounded">Kelola</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>