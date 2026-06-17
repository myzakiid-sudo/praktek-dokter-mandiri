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
                    <span class="ml-1 md:ml-2 text-gray-700">Manajemen Stok Apotek</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-2">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Stok Apotek</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau ketersediaan dan tambah stok obat.</p>
        </div>
        <a href="tambah.php" class="inline-flex items-center bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-150 shadow-sm focus:outline-none">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Restock (Tambah Stok)
        </a>
    </div>

    <?php if(isset($_GET['pesan'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <p>Berhasil memperbarui stok obat!</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto ring-1 ring-gray-200 rounded-lg">
        <table class="min-w-full bg-white text-left whitespace-nowrap">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Nama Obat</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Jenis</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide text-center">Sisa Stok</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide text-right">Harga Satuan</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Kedaluwarsa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out group">
                    <td class="py-4 px-5 text-sm font-bold text-gray-900 group-hover:text-blue-900"><?php echo htmlspecialchars($row['nama_obat']); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-600 group-hover:text-blue-900"><?php echo htmlspecialchars($row['jenis_obat']); ?></td>
                    <td class="py-4 px-5 text-sm text-center">
                        <?php if($row['stok'] <= 20): ?>
                            <span class="px-3 py-1 font-bold text-red-700 bg-red-100 rounded-full border border-red-200 text-xs"><?php echo $row['stok']; ?> pcs</span>
                        <?php else: ?>
                            <span class="px-3 py-1 font-bold text-green-700 bg-green-100 rounded-full border border-green-200 text-xs"><?php echo $row['stok']; ?> pcs</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-5 text-sm text-right text-gray-700 font-medium group-hover:text-blue-900">Rp <?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-600 group-hover:text-blue-900"><?php echo date('d M Y', strtotime($row['tgl_kadaluarsa'])); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500">
                        <p class="mt-2 text-sm font-medium">Belum ada data obat.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>