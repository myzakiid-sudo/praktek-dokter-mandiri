<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';

// Cari kunjungan yang statusnya 'Selesai' tapi belum ada di tabel Tagihan
$kunjungan_query = $koneksi->query("
    SELECT k.kunjungan_id, p.nama AS nama_pasien 
    FROM Kunjungan k
    JOIN Pasien p ON k.patient_id = p.patient_id
    WHERE k.status = 'Selesai' 
    AND k.kunjungan_id NOT IN (SELECT kunjungan_id FROM Tagihan)
");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kunjungan_id = $_POST['kunjungan_id'];
    
    // Insert ke tabel Tagihan (total otomatis 0 dulu, nanti diisi lewat trigger detail)
    $stmt = $koneksi->prepare("INSERT INTO Tagihan (kunjungan_id, tanggal_tagihan, total_tagihan, status) VALUES (?, NOW(), 0, 'Belum Lunas')");
    $stmt->bind_param("i", $kunjungan_id);
    
    if ($stmt->execute()) {
        $tagihan_id = $stmt->insert_id; // Ambil ID yang baru saja terbuat
        header("Location: detail.php?id=" . $tagihan_id);
        exit;
    }
}

include '../../includes/header.php';
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4 max-w-xl mx-auto">
    <div class="mb-6">
        <button onclick="history.back()" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 font-semibold focus:outline-none">
            &larr; Batal
        </button>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Buat Tagihan Baru</h2>

    <form action="" method="POST">
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Pasien (Kunjungan Selesai)</label>
            <select name="kunjungan_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                <option value="">-- Pilih Kunjungan --</option>
                <?php while($k = $kunjungan_query->fetch_assoc()): ?>
                    <option value="<?php echo $k['kunjungan_id']; ?>">
                        <?php echo htmlspecialchars($k['nama_pasien']); ?> (Kunjungan #<?php echo $k['kunjungan_id']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">Buka Tagihan</button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>