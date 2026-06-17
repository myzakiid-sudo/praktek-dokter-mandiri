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

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="index.php" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 font-semibold">
            &larr; Batal
        </a>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Penerimaan Stok Obat Baru (Restock)</h2>

    <?php if(isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Obat</label>
            <select name="obat_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                <option value="">-- Pilih Obat --</option>
                <?php while($o = $obat_query->fetch_assoc()): ?>
                    <option value="<?php echo $o['obat_id']; ?>">
                        <?php echo htmlspecialchars($o['nama_obat']); ?> (Sisa saat ini: <?php echo $o['stok']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Masuk (Pcs)</label>
                <input name="jumlah" type="number" min="1" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="Misal: 50" required>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Penerimaan</label>
                <select name="lokasi_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                    <?php while($l = $lokasi_query->fetch_assoc()): ?>
                        <option value="<?php echo $l['lokasi_id']; ?>"><?php echo htmlspecialchars($l['nama_lokasi']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan / Nomor Faktur</label>
            <textarea name="keterangan" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder="Penerimaan dari Supplier PT XYZ (Faktur: FK-123)..." required></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded">
                Simpan Stok
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>