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

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Catat Rekam Medis Baru</h2>
        <a href="index.php" class="text-gray-500 hover:text-gray-700">&larr; Kembali</a>
    </div>

    <?php if(isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Antrean Pasien</label>
            <select name="kunjungan_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">-- Pilih Pasien Menunggu --</option>
                <?php while($k = $kunjungan_query->fetch_assoc()): ?>
                    <option value="<?php echo $k['kunjungan_id']; ?>">
                        #<?php echo $k['kunjungan_id']; ?> - <?php echo htmlspecialchars($k['nama_pasien']); ?> (Antre sejak <?php echo date('H:i', strtotime($k['waktu_datang'])); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Keluhan / Anamnesa</label>
            <textarea name="anamnesa" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Pasien mengeluh pusing dan mual..." required></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Hasil Pemeriksaan Fisik</label>
            <textarea name="pemeriksaan_fisik" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tensi 120/80, Suhu 38C..."></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2 text-red-600">Diagnosa / Catatan Klinis</label>
            <textarea name="catatan_klinis" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 border-red-200" placeholder="Suspect demam berdarah..." required></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150">
                Simpan Rekam Medis
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>