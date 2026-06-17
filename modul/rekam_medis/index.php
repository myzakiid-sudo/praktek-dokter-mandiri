<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';
include '../../includes/header.php';

// Memanggil View Rekam Medis yang sudah kita buat di Tahap 2
$query = "SELECT * FROM v_rekam_medis_pasien ORDER BY waktu_datang DESC";
$result = $koneksi->query($query);
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Rekam Medis Pasien</h2>
            <p class="text-sm text-gray-500">Daftar riwayat pemeriksaan klinis pasien.</p>
        </div>
        <a href="tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150">
            + Catat Pemeriksaan
        </a>
    </div>

    <?php if(isset($_GET['pesan'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Berhasil menyimpan rekam medis baru!
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Waktu Periksa</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Pasien</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Dokter</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Keluhan (Anamnesa)</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Diagnosa Klinis</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo date('d M Y, H:i', strtotime($row['waktu_datang'])); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-bold"><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['anamnesa']); ?></td>
                    <td class="py-3 px-4 text-sm text-red-600 font-medium"><?php echo htmlspecialchars($row['catatan_klinis']); ?></td>
                </tr>
                <?php endwhile; ?>
                
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">Belum ada riwayat rekam medis.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>