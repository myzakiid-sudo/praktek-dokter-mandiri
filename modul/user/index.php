<?php
session_start();
// Proteksi: Harus login DAN harus Admin (Role 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo "<script>alert('Akses Ditolak! Anda bukan Administrator.'); window.location.href='../../dashboard.php';</script>";
    exit;
}

require_once '../../includes/koneksi.php';
include '../../includes/header.php';

$query = "SELECT u.user_id, u.username, r.nama_role FROM User u JOIN Role r ON u.role_id = r.role_id ORDER BY u.user_id ASC";
$result = $koneksi->query($query);
?>

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4">
    <div class="mb-4">
        <a href="../../dashboard.php" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-semibold">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna (User)</h2>
            <p class="text-sm text-gray-500">Kelola akun dan hak akses pegawai klinik.</p>
        </div>
        <a href="tambah.php" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded transition duration-150">
            + Tambah User
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">ID User</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Username</th>
                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Role (Hak Akses)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm font-bold text-gray-700"><?php echo $row['user_id']; ?></td>
                    <td class="py-3 px-4 text-sm text-gray-900"><?php echo htmlspecialchars($row['username']); ?></td>
                    <td class="py-3 px-4 text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            <?php echo htmlspecialchars($row['nama_role']); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>