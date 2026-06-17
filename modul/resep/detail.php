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
                    <a href="index.php" class="ml-1 md:ml-2 text-gray-500 hover:text-blue-600 transition duration-150">Resep</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Detail Resep</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-2">
    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
        <h2 class="text-2xl font-bold text-gray-800">Detail Resep Obat #RSP-<?php echo $resep_id; ?></h2>
        <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150 focus:outline-none">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-blue-50/50 p-5 rounded-xl mb-6 border border-blue-100/50">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="font-bold text-blue-800 text-sm tracking-wide uppercase">Tambahkan Obat ke Resep</h3>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4 text-sm font-medium flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($sukses)): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4 text-sm font-medium flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <?php echo $sukses; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="flex flex-wrap gap-4 items-end">
            <div class="w-full md:flex-1">
                <label class="block text-gray-700 text-xs font-semibold mb-2">Pilih Obat</label>
                <select name="obat_id" class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-150" required>
                    <option value="" disabled selected>-- Pilih Obat --</option>
                    <?php while($o = $obat_query->fetch_assoc()): ?>
                        <option value="<?php echo $o['obat_id']; ?>"><?php echo $o['nama_obat']; ?> (Stok: <?php echo $o['stok']; ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="w-full md:w-24">
                <label class="block text-gray-700 text-xs font-semibold mb-2">Jumlah</label>
                <input name="jumlah" type="number" min="1" class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-150" required>
            </div>
            <div class="w-full md:w-1/5">
                <label class="block text-gray-700 text-xs font-semibold mb-2">Dosis</label>
                <input name="dosis" type="text" placeholder="Contoh: 500mg" class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-150" required>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-gray-700 text-xs font-semibold mb-2">Instruksi Khusus</label>
                <input name="instruksi_khusus" type="text" placeholder="3x sehari sesudah makan" class="w-full px-3 py-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-150" required>
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white font-semibold rounded-lg text-sm transition duration-150 shadow-sm focus:outline-none">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto ring-1 ring-gray-200 rounded-lg">
        <table class="min-w-full bg-white text-left whitespace-nowrap">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-5 text-xs font-semibold text-gray-700 uppercase tracking-wide">Nama Obat</th>
                    <th class="py-3 px-5 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Jumlah</th>
                    <th class="py-3 px-5 text-xs font-semibold text-gray-700 uppercase tracking-wide">Dosis</th>
                    <th class="py-3 px-5 text-xs font-semibold text-gray-700 uppercase tracking-wide">Instruksi Khusus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while($detail = $detail_query->fetch_assoc()): ?>
                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out">
                    <td class="py-4 px-5 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($detail['nama_obat']); ?></td>
                    <td class="py-4 px-5 text-sm text-center font-bold text-red-600">
                        <span class="bg-red-50 text-red-700 py-1 px-2.5 rounded-md border border-red-100"><?php echo htmlspecialchars($detail['jumlah']); ?> pcs</span>
                    </td>
                    <td class="py-4 px-5 text-sm text-gray-700"><?php echo htmlspecialchars($detail['dosis']); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-700"><?php echo htmlspecialchars($detail['instruksi_khusus']); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if($detail_query->num_rows == 0): ?>
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500">
                        <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <p class="text-sm font-medium">Belum ada obat dalam resep ini.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>