<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';
include '../../includes/header.php';

// Menampilkan data obat
$query = "SELECT * FROM Obat ORDER BY nama_obat ASC";
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
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Stok Apotek</h2>
            <p class="text-sm text-gray-500">Pantau ketersediaan dan tambah stok obat.</p>
        </div>
        <a href="tambah.php" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition duration-150">
            + Restock (Tambah Stok)
        </a>
    </div>

    <?php if(isset($_GET['pesan'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Berhasil memperbarui stok obat!
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Obat</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Jenis</th>
                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Sisa Stok</th>
                    <th class="py-3 px-4 border-b text-right text-xs font-semibold text-gray-600 uppercase">Harga Satuan</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Kedaluwarsa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm text-gray-900 font-bold"><?php echo htmlspecialchars($row['nama_obat']); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['jenis_obat']); ?></td>
                    <td class="py-3 px-4 text-sm text-center">
                        <?php if($row['stok'] <= 20): ?>
                            <span class="px-3 py-1 font-bold text-red-700 bg-red-100 rounded-full"><?php echo $row['stok']; ?> pcs</span>
                        <?php else: ?>
                            <span class="px-3 py-1 font-bold text-green-700 bg-green-100 rounded-full"><?php echo $row['stok']; ?> pcs</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-sm text-right text-gray-700">Rp <?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo date('d M Y', strtotime($row['tgl_kadaluarsa'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>