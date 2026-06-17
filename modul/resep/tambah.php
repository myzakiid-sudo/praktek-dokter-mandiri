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

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4 max-w-2xl mx-auto">
    <div class="mb-6">
        <button onclick="history.back()" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 font-semibold focus:outline-none">
            &larr; Kembali
        </button>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Buat Resep Baru</h2>

    <?php if(isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Rekam Medis (Pasien)</label>
            <select name="record_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                <option value="">-- Pilih Pasien yang Selesai Diperiksa --</option>
                <?php while($rm = $rekam_medis_query->fetch_assoc()): ?>
                    <option value="<?php echo $rm['record_id']; ?>">
                        <?php echo htmlspecialchars($rm['nama_pasien']); ?> (Diagnosa: <?php echo htmlspecialchars($rm['catatan_klinis']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Dokter Pemberi Resep</label>
            <select name="dokter_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                <option value="">-- Pilih Dokter --</option>
                <?php while($d = $dokter_query->fetch_assoc()): ?>
                    <option value="<?php echo $d['dokter_id']; ?>"><?php echo htmlspecialchars($d['nama']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Tambahan (Opsional)</label>
            <textarea name="catatan_dokter" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder="Alergi obat tertentu..."></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Simpan Resep
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>