<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';

// Ambil data Pasien untuk Dropdown
$pasien_query = $koneksi->query("SELECT patient_id, nama FROM Pasien ORDER BY nama ASC");
// Ambil data Dokter untuk Dropdown
$dokter_query = $koneksi->query("SELECT dokter_id, nama, spesialisasi FROM Dokter ORDER BY nama ASC");

// Proses Eksekusi Stored Procedure
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $dokter_id = $_POST['dokter_id'];
    $jenis_layanan = $_POST['jenis_layanan'];

    // Menyiapkan pemanggilan Stored Procedure (parameter IN, IN, IN, OUT)
    $stmt = $koneksi->prepare("CALL sp_daftar_kunjungan(?, ?, ?, @status_pesan)");
    $stmt->bind_param("iis", $patient_id, $dokter_id, $jenis_layanan);
    $stmt->execute();
    
    // Mengambil pesan balasan dari parameter OUT
    $result_pesan = $koneksi->query("SELECT @status_pesan AS pesan");
    $row_pesan = $result_pesan->fetch_assoc();
    $pesan_db = $row_pesan['pesan'];

    // Redirect kembali dengan membawa pesan dari Database
    header("Location: index.php?pesan=" . urlencode($pesan_db));
    exit;
}

include '../../includes/header.php';
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pendaftaran Antrean Pasien</h2>
        <a href="index.php" class="text-gray-500 hover:text-gray-700">&larr; Kembali</a>
    </div>

    <form action="" method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Pasien</label>
            <select name="patient_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">-- Cari Pasien --</option>
                <?php while($p = $pasien_query->fetch_assoc()): ?>
                    <option value="<?php echo $p['patient_id']; ?>"><?php echo htmlspecialchars($p['nama']); ?></option>
                <?php endwhile; ?>
            </select>
            <p class="text-xs text-gray-500 mt-1">Jika nama tidak ada, <a href="../pasien/tambah.php" class="text-blue-500 underline">tambah data pasien baru dulu</a>.</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Dokter Pemeriksa</label>
            <select name="dokter_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">-- Pilih Dokter --</option>
                <?php while($d = $dokter_query->fetch_assoc()): ?>
                    <option value="<?php echo $d['dokter_id']; ?>"><?php echo htmlspecialchars($d['nama']); ?> (Poli <?php echo htmlspecialchars($d['spesialisasi']); ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Layanan</label>
            <select name="jenis_layanan" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="Konsultasi Umum">Konsultasi Umum</option>
                <option value="Pemeriksaan Rutin">Pemeriksaan Rutin</option>
                <option value="Tindakan Medis">Tindakan Medis Ringan</option>
                <option value="Kontrol Post-Op">Kontrol Post-Operasi</option>
            </select>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150">
                Daftarkan Antrean
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>