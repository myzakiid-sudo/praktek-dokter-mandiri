<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';

// Proses simpan data jika form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $no_telpon = $_POST['no_telpon'];

    $stmt = $koneksi->prepare("INSERT INTO Pasien (nama, tgl_lahir, jenis_kelamin, alamat, no_telpon) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $tgl_lahir, $jenis_kelamin, $alamat, $no_telpon);
    
    if ($stmt->execute()) {
        header("Location: index.php?pesan=sukses");
        exit;
    } else {
        $error = "Gagal menyimpan data pasien.";
    }
}

include '../../includes/header.php';
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Pasien Baru</h2>
        <a href="index.php" class="text-gray-500 hover:text-gray-700">&larr; Kembali</a>
    </div>

    <?php if(isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
            <input name="nama" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Lahir</label>
                <input name="tgl_lahir" type="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="L">Laki-laki (L)</option>
                    <option value="P">Perempuan (P)</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">No. Telpon</label>
            <input name="no_telpon" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap</label>
            <textarea name="alamat" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150">
                Simpan Data
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>