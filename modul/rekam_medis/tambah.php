<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';

// Ambil antrean kunjungan yang belum diperiksa
$kunjungan_query = $koneksi->query("
    SELECT k.kunjungan_id, p.nama AS nama_pasien, k.waktu_datang 
    FROM Kunjungan k
    JOIN Pasien p ON k.patient_id = p.patient_id
    WHERE k.kunjungan_id NOT IN (SELECT kunjungan_id FROM Rekam_Medis)
    ORDER BY k.waktu_datang ASC
");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kunjungan_id = $_POST['kunjungan_id'];
    $anamnesa = $_POST['anamnesa'];
    $pemeriksaan_fisik = $_POST['pemeriksaan_fisik'];
    $catatan_klinis = $_POST['catatan_klinis'];
    
    // Kita gunakan Transaction agar jika error, data tidak setengah masuk
    $koneksi->begin_transaction();
    try {
        // 1. Insert ke tabel Rekam_Medis
        $stmt = $koneksi->prepare("INSERT INTO Rekam_Medis (kunjungan_id, tanggal_catatan, anamnesa, pemeriksaan_fisik, catatan_klinis) VALUES (?, NOW(), ?, ?, ?)");
        $stmt->bind_param("isss", $kunjungan_id, $anamnesa, $pemeriksaan_fisik, $catatan_klinis);
        $stmt->execute();
        
        // 2. Update status Kunjungan menjadi Selesai
        $stmt2 = $koneksi->prepare("UPDATE Kunjungan SET status = 'Selesai', waktu_selesai = NOW() WHERE kunjungan_id = ?");
        $stmt2->bind_param("i", $kunjungan_id);
        $stmt2->execute();
        
        $koneksi->commit();
        header("Location: index.php?pesan=sukses");
        exit;
    } catch (Exception $e) {
        $koneksi->rollback();
        $error = "Gagal menyimpan data: " . $e->getMessage();
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
                    <a href="index.php" class="ml-1 md:ml-2 text-gray-500 hover:text-blue-600 transition duration-150">Rekam Medis</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Catat Baru</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Catat Rekam Medis Baru</h2>
        <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Antrean Pasien <span class="text-red-500">*</span></label>
            <select name="kunjungan_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                <option value="" disabled selected>-- Pilih Pasien Menunggu --</option>
                <?php while($k = $kunjungan_query->fetch_assoc()): ?>
                    <option value="<?php echo $k['kunjungan_id']; ?>">
                        #<?php echo $k['kunjungan_id']; ?> - <?php echo htmlspecialchars($k['nama_pasien']); ?> (Antre sejak <?php echo date('H:i', strtotime($k['waktu_datang'])); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Keluhan / Anamnesa <span class="text-red-500">*</span></label>
            <textarea name="anamnesa" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" placeholder="Pasien mengeluh pusing dan mual..." required></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hasil Pemeriksaan Fisik</label>
            <textarea name="pemeriksaan_fisik" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" placeholder="Tensi 120/80, Suhu 38C..."></textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-red-600 mb-2">Diagnosa / Catatan Klinis <span class="text-red-500">*</span></label>
            <textarea name="catatan_klinis" rows="3" class="w-full px-4 py-2.5 bg-red-50 border border-red-200 text-red-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" placeholder="Suspect demam berdarah..." required></textarea>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white font-semibold rounded-lg transition duration-150 shadow-sm focus:outline-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Simpan Rekam Medis
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>