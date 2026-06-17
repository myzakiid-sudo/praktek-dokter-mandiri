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

<div class="mb-4">
    <nav class="flex text-sm text-gray-500 font-medium" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="../../dashboard.php" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Resep Obat</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-2">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Resep Obat</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar resep yang dikeluarkan oleh dokter.</p>
        </div>
        <a href="tambah.php" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-150 shadow-sm focus:outline-none">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Buat Resep Baru
        </a>
    </div>

    <div class="overflow-x-auto ring-1 ring-gray-200 rounded-lg">
        <table class="min-w-full bg-white text-left whitespace-nowrap">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">ID Resep</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Tanggal</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Nama Pasien</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Dokter</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out group">
                    <td class="py-4 px-5 text-sm font-bold text-blue-600">RSP-<?php echo $row['resep_id']; ?></td>
                    <td class="py-4 px-5 text-sm text-gray-600 group-hover:text-blue-900"><?php echo date('d M Y', strtotime($row['tanggal_resep'])); ?></td>
                    <td class="py-4 px-5 text-sm font-medium text-gray-900 group-hover:text-blue-900"><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-600 group-hover:text-blue-900"><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                    <td class="py-4 px-5 text-sm">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                            <?php echo $row['status_resep']; ?>
                        </span>
                    </td>
                    <td class="py-4 px-5 text-sm text-center">
                        <a href="detail.php?id=<?php echo $row['resep_id']; ?>" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-md hover:bg-indigo-600 hover:text-white transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Lihat / Isi Obat
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        <p class="mt-2 text-sm font-medium">Belum ada resep yang dikeluarkan.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>