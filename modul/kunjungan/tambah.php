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
                    <a href="index.php" class="ml-1 md:ml-2 text-gray-500 hover:text-blue-600 transition duration-150">Kunjungan</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Daftar Antrean</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pendaftaran Antrean Pasien</h2>
        <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form action="" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Pasien <span class="text-red-500">*</span></label>
            <select name="patient_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                <option value="" disabled selected>-- Cari Pasien --</option>
                <?php while($p = $pasien_query->fetch_assoc()): ?>
                    <option value="<?php echo $p['patient_id']; ?>"><?php echo htmlspecialchars($p['nama']); ?></option>
                <?php endwhile; ?>
            </select>
            <p class="text-xs text-gray-500 mt-2">Jika nama tidak ada, <a href="../pasien/tambah.php" class="text-blue-600 hover:text-blue-800 font-medium underline">tambah data pasien baru dulu</a>.</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Dokter Pemeriksa <span class="text-red-500">*</span></label>
            <select name="dokter_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                <option value="" disabled selected>-- Pilih Dokter --</option>
                <?php while($d = $dokter_query->fetch_assoc()): ?>
                    <option value="<?php echo $d['dokter_id']; ?>"><?php echo htmlspecialchars($d['nama']); ?> (Poli <?php echo htmlspecialchars($d['spesialisasi']); ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Layanan <span class="text-red-500">*</span></label>
            <select name="jenis_layanan" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                <option value="Konsultasi Umum">Konsultasi Umum</option>
                <option value="Pemeriksaan Rutin">Pemeriksaan Rutin</option>
                <option value="Tindakan Medis">Tindakan Medis Ringan</option>
                <option value="Kontrol Post-Op">Kontrol Post-Operasi</option>
            </select>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 text-white font-semibold rounded-lg transition duration-150 shadow-sm focus:outline-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Daftarkan Antrean
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>