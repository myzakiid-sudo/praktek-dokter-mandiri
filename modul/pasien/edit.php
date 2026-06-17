<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Proses Update Data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $no_telpon = $_POST['no_telpon'];

    $stmt = $koneksi->prepare("UPDATE Pasien SET nama=?, tgl_lahir=?, jenis_kelamin=?, alamat=?, no_telpon=? WHERE patient_id=?");
    $stmt->bind_param("sssssi", $nama, $tgl_lahir, $jenis_kelamin, $alamat, $no_telpon, $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?pesan=edit_sukses");
        exit;
    } else {
        $error = "Gagal mengupdate data pasien.";
    }
}

// Ambil data pasien saat ini untuk diisikan ke form
$stmt = $koneksi->prepare("SELECT * FROM Pasien WHERE patient_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Jika data tidak ada/dihapus, hentikan proses
if ($result->num_rows == 0) {
    die("Data pasien tidak ditemukan.");
}
$row = $result->fetch_assoc();

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
                    <a href="index.php" class="ml-1 md:ml-2 text-gray-500 hover:text-blue-600 transition duration-150">Pasien</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Edit Pasien</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Data Pasien</h2>
        <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Batal
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
            <input name="nama" type="text" value="<?php echo htmlspecialchars($row['nama']); ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input name="tgl_lahir" type="date" value="<?php echo $row['tgl_lahir']; ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                    <option value="L" <?php echo ($row['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki (L)</option>
                    <option value="P" <?php echo ($row['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan (P)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">No. Telpon</label>
            <input name="no_telpon" type="text" value="<?php echo htmlspecialchars($row['no_telpon']); ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
            <textarea name="alamat" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition duration-150 ease-in-out shadow-sm"><?php echo htmlspecialchars($row['alamat']); ?></textarea>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white font-semibold rounded-lg transition duration-150 shadow-sm focus:outline-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Update Data
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>