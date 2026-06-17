<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';
include '../../includes/header.php';

// Cukup panggil View yang dibuat di Tahap 2
$query = "SELECT * FROM v_kunjungan_hari_ini ORDER BY waktu_datang DESC";
$result = $koneksi->query($query);
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Antrean Kunjungan</h2>
            <p class="text-sm text-gray-500">Menampilkan daftar pasien yang mendaftar berobat hari ini.</p>
        </div>
        <a href="tambah.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-150">
            + Daftar Kunjungan
        </a>
    </div>

    <?php if(isset($_GET['pesan'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['pesan']); ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">No. Kunjungan</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Pasien</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Dokter Tujuan</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Waktu Datang</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm font-bold text-blue-600">#<?php echo $row['kunjungan_id']; ?></td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo date('H:i', strtotime($row['waktu_datang'])); ?> WIB</td>
                    <td class="py-3 px-4 text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">Belum ada pasien yang mendaftar hari ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>