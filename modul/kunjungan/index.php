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
                    <span class="ml-1 md:ml-2 text-gray-700">Kunjungan</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-2">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Antrean Kunjungan</h2>
            <p class="text-sm text-gray-500 mt-1">Menampilkan daftar pasien yang mendaftar berobat hari ini.</p>
        </div>
        <a href="tambah.php" class="inline-flex items-center bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-150 shadow-sm focus:outline-none">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Daftar Kunjungan
        </a>
    </div>

    <?php if(isset($_GET['pesan'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <p><?php echo htmlspecialchars($_GET['pesan']); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto ring-1 ring-gray-200 rounded-lg">
        <table class="min-w-full bg-white text-left whitespace-nowrap">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">No. Kunjungan</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Nama Pasien</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Dokter Tujuan</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Waktu Datang</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out group">
                    <td class="py-4 px-5 text-sm font-bold text-blue-600">#<?php echo $row['kunjungan_id']; ?></td>
                    <td class="py-4 px-5 text-sm font-medium text-gray-900 group-hover:text-blue-900"><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-600 group-hover:text-blue-900"><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                    <td class="py-4 px-5 text-sm text-gray-600 group-hover:text-blue-900"><?php echo date('H:i', strtotime($row['waktu_datang'])); ?> WIB</td>
                    <td class="py-4 px-5 text-sm">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="mt-2 text-sm font-medium">Belum ada pasien yang mendaftar hari ini.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>