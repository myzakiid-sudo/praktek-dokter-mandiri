<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';

// Ambil data rekam medis yang belum dibuatkan resep
$rekam_medis_query = $koneksi->query("
    SELECT rm.record_id, p.nama AS nama_pasien, rm.catatan_klinis 
    FROM Rekam_Medis rm
    JOIN Kunjungan k ON rm.kunjungan_id = k.kunjungan_id
    JOIN Pasien p ON k.patient_id = p.patient_id
    WHERE rm.record_id NOT IN (SELECT record_id FROM Resep)
");

$dokter_query = $koneksi->query("SELECT dokter_id, nama FROM Dokter");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $record_id = $_POST['record_id'];
    $dokter_id = $_POST['dokter_id'];
    $catatan = $_POST['catatan_dokter'];
    
    // Panggil Stored Procedure Pembuatan Resep (Tahap 3)
    $stmt = $koneksi->prepare("CALL sp_buat_resep(?, ?, ?)");
    $stmt->bind_param("iis", $record_id, $dokter_id, $catatan);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal membuat resep obat.";
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
                    <a href="index.php" class="ml-1 md:ml-2 text-gray-500 hover:text-blue-600 transition duration-150">Resep</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Buat Resep Baru</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Buat Resep Baru</h2>
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Rekam Medis (Pasien) <span class="text-red-500">*</span></label>
            <select name="record_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                <option value="" disabled selected>-- Pilih Pasien yang Selesai Diperiksa --</option>
                <?php while($rm = $rekam_medis_query->fetch_assoc()): ?>
                    <option value="<?php echo $rm['record_id']; ?>">
                        <?php echo htmlspecialchars($rm['nama_pasien']); ?> (Diagnosa: <?php echo htmlspecialchars($rm['catatan_klinis']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dokter Pemberi Resep <span class="text-red-500">*</span></label>
            <select name="dokter_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                <option value="" disabled selected>-- Pilih Dokter --</option>
                <?php while($d = $dokter_query->fetch_assoc()): ?>
                    <option value="<?php echo $d['dokter_id']; ?>"><?php echo htmlspecialchars($d['nama']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
            <textarea name="catatan_dokter" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" placeholder="Alergi obat tertentu..."></textarea>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white font-semibold rounded-lg transition duration-150 shadow-sm focus:outline-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Simpan Resep
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>