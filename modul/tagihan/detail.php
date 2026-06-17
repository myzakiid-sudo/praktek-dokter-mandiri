<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

require_once '../../includes/koneksi.php';
$tagihan_id = $_GET['id'];

// Proses Tambah Item Tagihan (Triggers di MySQL akan mengupdate total otomatis)
if (isset($_POST['tambah_item'])) {
    $jenis = $_POST['jenis_item'];
    $harga = str_replace('.', '', $_POST['harga_satuan']); // Hapus titik format ribuan
    $jumlah = $_POST['jumlah'];

    try {
        // Menggunakan function sf_hitung_subtotal langsung di dalam query
        $stmt = $koneksi->prepare("
            INSERT INTO Detail_Tagihan (tagihan_id, jenis_item, harga_satuan, jumlah, subtotal) 
            VALUES (?, ?, ?, ?, sf_hitung_subtotal(?, ?))
        ");
        $stmt->bind_param("isdidd", $tagihan_id, $jenis, $harga, $jumlah, $harga, $jumlah);
        $stmt->execute();
        $sukses = "Item berhasil ditambahkan. Total tagihan otomatis diupdate oleh Trigger!";
    } catch (Exception $e) {
        $error = "Gagal menambah item: " . $e->getMessage();
    }
}

// Proses Pembayaran Lunas menggunakan Procedure
if (isset($_POST['bayar_tagihan'])) {
    $metode = $_POST['metode_pembayaran'];
    try {
        // Mengakali parameter INOUT di PHP
        $koneksi->query("SET @tid = $tagihan_id");
        $stmt = $koneksi->prepare("CALL sp_bayar_tagihan(@tid, ?)");
        $stmt->bind_param("s", $metode);
        $stmt->execute();
        $sukses = "Tagihan berhasil dilunasi!";
    } catch (Exception $e) {
        $error = "Gagal membayar: " . $e->getMessage();
    }
}

// Ambil Header Tagihan
$header = $koneksi->query("SELECT t.*, p.nama FROM Tagihan t JOIN Kunjungan k ON t.kunjungan_id = k.kunjungan_id JOIN Pasien p ON k.patient_id = p.patient_id WHERE t.tagihan_id = $tagihan_id")->fetch_assoc();

// Ambil Detail Tagihan
$detail_query = $koneksi->query("SELECT * FROM Detail_Tagihan WHERE tagihan_id = $tagihan_id");

include '../../includes/header.php';
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="mb-4">
        <a href="index.php" class="inline-flex items-center text-sm text-blue-600 font-semibold">&larr; Kembali</a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Invoice #INV-<?php echo $tagihan_id; ?></h2>
        <?php if($header['status'] == 'Lunas'): ?>
            <span class="px-4 py-2 text-lg font-bold rounded-md bg-green-100 text-green-800">LUNAS</span>
        <?php else: ?>
            <span class="px-4 py-2 text-lg font-bold rounded-md bg-red-100 text-red-800">BELUM LUNAS</span>
        <?php endif; ?>
    </div>

    <p class="text-gray-600 mb-6"><strong>Pasien:</strong> <?php echo htmlspecialchars($header['nama']); ?></p>

    <?php if(isset($error)): ?><div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-3">⚠️ <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if(isset($sukses)): ?><div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-3">✅ <?php echo $sukses; ?></div><?php endif; ?>

    <?php if($header['status'] !== 'Lunas'): ?>
    <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
        <h3 class="font-bold text-gray-800 mb-3">Tambahkan Rincian Biaya</h3>
        <form action="" method="POST" class="flex flex-wrap gap-3 items-end">
            <div class="w-full md:w-2/5">
                <label class="block text-gray-700 text-xs font-bold mb-1">Nama Layanan / Obat</label>
                <input name="jenis_item" type="text" placeholder="Contoh: Jasa Konsultasi Dokter" class="shadow border rounded w-full py-2 px-3 text-sm" required>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-gray-700 text-xs font-bold mb-1">Harga Satuan (Rp)</label>
                <input name="harga_satuan" type="number" min="0" class="shadow border rounded w-full py-2 px-3 text-sm" required>
            </div>
            <div class="w-full md:w-1/6">
                <label class="block text-gray-700 text-xs font-bold mb-1">Jumlah</label>
                <input name="jumlah" type="number" min="1" value="1" class="shadow border rounded w-full py-2 px-3 text-sm" required>
            </div>
            <div>
                <button name="tambah_item" type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">Tambahkan</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <table class="min-w-full bg-white border border-gray-200 mb-6">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Item</th>
                <th class="py-2 px-4 border-b text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                <th class="py-2 px-4 border-b text-right text-xs font-semibold text-gray-600 uppercase">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($detail = $detail_query->fetch_assoc()): ?>
            <tr>
                <td class="py-2 px-4 text-sm border-b"><?php echo htmlspecialchars($detail['jenis_item']); ?></td>
                <td class="py-2 px-4 text-sm border-b text-right">Rp <?php echo number_format($detail['harga_satuan'], 0, ',', '.'); ?></td>
                <td class="py-2 px-4 text-sm border-b text-center"><?php echo $detail['jumlah']; ?></td>
                <td class="py-2 px-4 text-sm border-b text-right font-medium">Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="bg-blue-50">
                <td colspan="3" class="py-3 px-4 text-right font-bold text-gray-800">TOTAL TAGIHAN KESELURUHAN:</td>
                <td class="py-3 px-4 text-right font-bold text-blue-700 text-lg">Rp <?php echo number_format($header['total_tagihan'], 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <?php if($header['status'] !== 'Lunas' && $header['total_tagihan'] > 0): ?>
    <div class="border-t border-gray-200 pt-4 flex justify-end">
        <form action="" method="POST" class="flex items-center gap-3">
            <select name="metode_pembayaran" class="shadow border rounded py-2 px-3 text-sm text-gray-700" required>
                <option value="Tunai">Tunai</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="QRIS">QRIS</option>
                <option value="Asuransi">Asuransi</option>
            </select>
            <button name="bayar_tagihan" type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded text-sm transition duration-150" onclick="return confirm('Proses pembayaran? Setelah lunas data tidak bisa diubah lagi.');">
                Proses Pembayaran Lunas
            </button>
        </form>
    </div>
    <?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>