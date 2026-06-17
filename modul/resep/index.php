<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';
include '../../includes/header.php';

// Mengambil daftar resep beserta nama pasien dan dokter
$query = "
    SELECT r.resep_id, r.tanggal_resep, r.status_resep, p.nama AS nama_pasien, d.nama AS nama_dokter
    FROM Resep r
    JOIN Rekam_Medis rm ON r.record_id = rm.record_id
    JOIN Kunjungan k ON rm.kunjungan_id = k.kunjungan_id
    JOIN Pasien p ON k.patient_id = p.patient_id
    JOIN Dokter d ON r.dokter_id = d.dokter_id
    ORDER BY r.tanggal_resep DESC
";
$result = $koneksi->query($query);
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="mb-4">
        <a href="../../dashboard.php" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-semibold">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Resep Obat</h2>
            <p class="text-sm text-gray-500">Daftar resep yang dikeluarkan oleh dokter.</p>
        </div>
        <a href="tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150">
            + Buat Resep Baru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">ID Resep</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Pasien</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Dokter</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm font-bold text-blue-600">RSP-<?php echo $row['resep_id']; ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo date('d M Y', strtotime($row['tanggal_resep'])); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                    <td class="py-3 px-4 text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            <?php echo $row['status_resep']; ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm text-center">
                        <a href="detail.php?id=<?php echo $row['resep_id']; ?>" class="text-indigo-600 hover:text-indigo-900 font-semibold border border-indigo-600 px-3 py-1 rounded">Lihat / Isi Obat</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>