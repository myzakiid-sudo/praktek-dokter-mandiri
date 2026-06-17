<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

require_once '../../includes/koneksi.php';
$resep_id = $_GET['id'];

// Ambil daftar obat untuk pilihan
$obat_query = $koneksi->query("SELECT obat_id, nama_obat, stok FROM Obat WHERE stok > 0");

// Proses memasukkan detail resep
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $obat_id = $_POST['obat_id'];
    $jumlah = $_POST['jumlah'];
    $dosis = $_POST['dosis'];
    $instruksi = $_POST['instruksi_khusus']; // Sudah diubah ke instruksi_khusus

    try {
        // Panggil Stored Procedure. Ini akan memicu Trigger potong stok!
        $stmt = $koneksi->prepare("CALL sp_tambah_detail_resep(?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $resep_id, $obat_id, $jumlah, $dosis, $instruksi);
        $stmt->execute();
        $sukses = "Obat berhasil ditambahkan! Stok otomatis terpotong.";
    } catch (mysqli_sql_exception $e) {
        // MENANGKAP ERROR DARI TRIGGER (Jika stok tidak cukup)
        $error = $e->getMessage();
    }
}

// Ambil data list obat yang sudah masuk ke resep ini
$detail_query = $koneksi->query("
    SELECT dr.detail_id, o.nama_obat, dr.jumlah, dr.dosis, dr.instruksi_khusus 
    FROM Detail_Resep dr
    JOIN Obat o ON dr.obat_id = o.obat_id
    WHERE dr.resep_id = $resep_id
");

include '../../includes/header.php';
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="mb-4">
        <a href="index.php" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-semibold">
            &larr; Kembali ke Daftar Resep
        </a>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Detail Resep Obat #RSP-<?php echo $resep_id; ?></h2>

    <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-100">
        <h3 class="font-bold text-blue-800 mb-3">Tambahkan Obat ke Resep</h3>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-3 text-sm font-bold">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($sukses)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3 text-sm font-bold">
                ✅ <?php echo $sukses; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="flex flex-wrap gap-3 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-gray-700 text-xs font-bold mb-1">Pilih Obat</label>
                <select name="obat_id" class="shadow border rounded w-full py-2 px-3 text-sm" required>
                    <option value="">-- Pilih Obat --</option>
                    <?php while($o = $obat_query->fetch_assoc()): ?>
                        <option value="<?php echo $o['obat_id']; ?>"><?php echo $o['nama_obat']; ?> (Stok: <?php echo $o['stok']; ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="w-full md:w-1/6">
                <label class="block text-gray-700 text-xs font-bold mb-1">Jumlah</label>
                <input name="jumlah" type="number" min="1" class="shadow border rounded w-full py-2 px-3 text-sm" required>
            </div>
            <div class="w-full md:w-1/5">
                <label class="block text-gray-700 text-xs font-bold mb-1">Dosis</label>
                <input name="dosis" type="text" placeholder="Contoh: 500mg" class="shadow border rounded w-full py-2 px-3 text-sm" required>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-gray-700 text-xs font-bold mb-1">Instruksi Khusus</label>
                <input name="instruksi_khusus" type="text" placeholder="3x Sehari Sesudah Makan" class="shadow border rounded w-full py-2 px-3 text-sm" required>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">Tambah</button>
            </div>
        </form>
    </div>

    <table class="min-w-full bg-white border border-gray-200 mt-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Obat</th>
                <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Jumlah</th>
                <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Dosis</th>
                <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Instruksi Khusus</th>
            </tr>
        </thead>
        <tbody>
            <?php while($detail = $detail_query->fetch_assoc()): ?>
            <tr>
                <td class="py-2 px-4 text-sm border-b"><?php echo htmlspecialchars($detail['nama_obat']); ?></td>
                <td class="py-2 px-4 text-sm border-b font-bold text-red-600"><?php echo htmlspecialchars($detail['jumlah']); ?> pcs</td>
                <td class="py-2 px-4 text-sm border-b"><?php echo htmlspecialchars($detail['dosis']); ?></td>
                <td class="py-2 px-4 text-sm border-b"><?php echo htmlspecialchars($detail['instruksi_khusus']); ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if($detail_query->num_rows == 0): ?>
            <tr><td colspan="4" class="py-4 text-center text-gray-500 text-sm">Belum ada obat dalam resep ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>