<?php
session_start();
// Proteksi halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';
include '../../includes/header.php';

// Mengambil data pasien terbaru
$query = "SELECT * FROM Pasien ORDER BY patient_id DESC";
$result = $koneksi->query($query);
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Data Pasien</h2>
        <a href="tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150">
            + Tambah Pasien
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Pasien</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Lahir</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">L/P</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Telpon</th>
                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                $no = 1;
                while($row = $result->fetch_assoc()): 
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo $no++; ?></td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo date('d-m-Y', strtotime($row['tgl_lahir'])); ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo $row['jenis_kelamin']; ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['no_telpon']); ?></td>
                    <td class="py-3 px-4 text-sm text-center">
                        <a href="edit.php?id=<?php echo $row['patient_id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <a href="hapus.php?id=<?php echo $row['patient_id']; ?>" onclick="return confirm('Yakin ingin menghapus pasien ini?');" class="text-red-600 hover:text-red-900">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data pasien.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>