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
                    <a href="index.php" class="ml-1 md:ml-2 text-gray-500 hover:text-blue-600 transition duration-150">Tagihan</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Detail Tagihan</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-2">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b border-gray-100 pb-4 gap-4">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-gray-800">Invoice #INV-<?php echo $tagihan_id; ?></h2>
            <?php if($header['status'] == 'Lunas'): ?>
                <span class="px-3 py-1 font-bold rounded-full bg-green-100 text-green-800 border border-green-200 text-sm">LUNAS</span>
            <?php else: ?>
                <span class="px-3 py-1 font-bold rounded-full bg-red-100 text-red-800 border border-red-200 text-sm">BELUM LUNAS</span>
            <?php endif; ?>
        </div>
        <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150 focus:outline-none">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="mb-6 bg-slate-50 p-4 rounded-lg border border-slate-100 inline-block">
        <p class="text-gray-600 text-sm uppercase tracking-wide font-semibold mb-1">Informasi Pasien</p>
        <p class="text-gray-900 font-bold text-lg"><?php echo htmlspecialchars($header['nama']); ?></p>
    </div>

    <?php if(isset($error)): ?><div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-6 text-sm font-medium shadow-sm flex items-center"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if(isset($sukses)): ?><div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6 text-sm font-medium shadow-sm flex items-center"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><?php echo $sukses; ?></div><?php endif; ?>

    <?php if($header['status'] !== 'Lunas'): ?>
    <div class="bg-gray-50/50 p-5 rounded-xl mb-8 border border-gray-200 shadow-sm">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <h3 class="font-bold text-gray-700 text-sm tracking-wide uppercase">Tambahkan Rincian Biaya</h3>
        </div>
        <form action="" method="POST" class="flex flex-wrap gap-4 items-end">
            <div class="w-full md:flex-1">
                <label class="block text-gray-700 text-xs font-semibold mb-2">Nama Layanan / Obat</label>
                <input name="jenis_item" type="text" placeholder="Contoh: Jasa Konsultasi Dokter" class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150" required>
            </div>
            <div class="w-full md:w-48">
                <label class="block text-gray-700 text-xs font-semibold mb-2">Harga Satuan (Rp)</label>
                <input name="harga_satuan" type="number" min="0" class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150" required>
            </div>
            <div class="w-full md:w-24">
                <label class="block text-gray-700 text-xs font-semibold mb-2">Jumlah (Qty)</label>
                <input name="jumlah" type="number" min="1" value="1" class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150" required>
            </div>
            <div class="w-full md:w-auto">
                <button name="tambah_item" type="submit" class="w-full md:w-auto inline-flex justify-center items-center bg-gray-800 hover:bg-gray-900 focus:ring-4 focus:ring-gray-300 text-white font-semibold py-2 px-5 rounded-lg text-sm transition duration-150 focus:outline-none">
                    Tambahkan
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="overflow-x-auto ring-1 ring-gray-200 rounded-lg mb-8">
        <table class="min-w-full bg-white text-left whitespace-nowrap">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-5 text-xs font-semibold text-gray-700 uppercase tracking-wide">Item Biaya</th>
                    <th class="py-3 px-5 text-xs font-semibold text-gray-700 uppercase tracking-wide text-right">Harga Satuan</th>
                    <th class="py-3 px-5 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Qty</th>
                    <th class="py-3 px-5 text-xs font-semibold text-gray-700 uppercase tracking-wide text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while($detail = $detail_query->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="py-4 px-5 text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($detail['jenis_item']); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-600 text-right">Rp <?php echo number_format($detail['harga_satuan'], 0, ',', '.'); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-900 text-center font-semibold bg-gray-50/50"><?php echo $detail['jumlah']; ?></td>
                    <td class="py-4 px-5 text-sm text-gray-900 text-right font-bold">Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="bg-blue-50/50 border-t-2 border-blue-200">
                    <td colspan="3" class="py-4 px-5 text-right font-bold text-gray-800 uppercase tracking-wide">Total Tagihan Keseluruhan:</td>
                    <td class="py-4 px-5 text-right font-bold text-blue-700 text-xl">Rp <?php echo number_format($header['total_tagihan'], 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if($header['status'] !== 'Lunas' && $header['total_tagihan'] > 0): ?>
    <div class="bg-green-50 p-5 rounded-xl border border-green-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-green-800 tracking-wide uppercase text-sm mb-1">Proses Pembayaran</h3>
            <p class="text-xs text-green-700">Pastikan menerima dana sebelum klik lunas.</p>
        </div>
        <form action="" method="POST" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="metode_pembayaran" class="bg-white shadow-sm border border-gray-300 rounded-lg py-2.5 px-4 text-sm text-gray-700 focus:ring-green-500 focus:border-green-500" required>
                <option value="Tunai">Tunai</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="QRIS">QRIS</option>
                <option value="Asuransi">Asuransi</option>
            </select>
            <button name="bayar_tagihan" type="submit" class="bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition duration-150 shadow-sm focus:outline-none w-full md:w-auto" onclick="return confirm('Proses pembayaran? Setelah lunas data tidak bisa diubah lagi.');">
                ✅ Konfirmasi Lunas
            </button>
        </form>
    </div>
    <?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>