<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';

// Mengambil data obat dan lokasi (gudang/apotek) untuk dropdown
$obat_query = $koneksi->query("SELECT obat_id, nama_obat, stok FROM Obat ORDER BY nama_obat ASC");
$lokasi_query = $koneksi->query("SELECT lokasi_id, nama_lokasi FROM Lokasi ORDER BY nama_lokasi ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $obat_id = $_POST['obat_id'];
    $lokasi_id = $_POST['lokasi_id'];
    $jumlah = $_POST['jumlah'];
    $jenis = 'Masuk'; // Karena ini form penambahan stok
    $keterangan = $_POST['keterangan'];

    try {
        // Panggil Stored Procedure untuk update stok sekaligus mencatat riwayat
        $stmt = $koneksi->prepare("CALL sp_update_stok_obat(?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $obat_id, $lokasi_id, $jumlah, $jenis, $keterangan);
        $stmt->execute();
        
        header("Location: index.php?pesan=sukses");
        exit;
    } catch (Exception $e) {
        $error = "Gagal memperbarui stok: " . $e->getMessage();
    }
}

include '../../includes/header.php';
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
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="index.php" class="ml-1 md:ml-2 text-gray-500 hover:text-blue-600 transition duration-150">Stok</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Restock Obat</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Penerimaan Stok Obat Baru (Restock)</h2>
        <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Batal
        </a>
    </div>

    <?php if(isset($error)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <p><?php echo $error; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Obat <span class="text-red-500">*</span></label>
            <select name="obat_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                <option value="" disabled selected>-- Pilih Obat --</option>
                <?php while($o = $obat_query->fetch_assoc()): ?>
                    <option value="<?php echo $o['obat_id']; ?>">
                        <?php echo htmlspecialchars($o['nama_obat']); ?> (Sisa saat ini: <?php echo $o['stok']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Masuk (Pcs) <span class="text-red-500">*</span></label>
                <input name="jumlah" type="number" min="1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" placeholder="Misal: 50" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Penerimaan <span class="text-red-500">*</span></label>
                <select name="lokasi_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                    <option value="" disabled selected>-- Pilih Lokasi --</option>
                    <?php while($l = $lokasi_query->fetch_assoc()): ?>
                        <option value="<?php echo $l['lokasi_id']; ?>"><?php echo htmlspecialchars($l['nama_lokasi']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan / Nomor Faktur <span class="text-red-500">*</span></label>
            <textarea name="keterangan" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" placeholder="Penerimaan dari Supplier PT XYZ (Faktur: FK-123)..." required></textarea>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 text-white font-semibold rounded-lg transition duration-150 shadow-sm focus:outline-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Simpan Stok Masuk
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>