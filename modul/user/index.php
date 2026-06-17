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
                    <span class="ml-1 md:ml-2 text-gray-700">Manajemen Pengguna</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-2">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna (User)</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola akun dan hak akses pegawai klinik.</p>
        </div>
        <a href="tambah.php" class="inline-flex items-center bg-slate-800 hover:bg-slate-900 focus:ring-4 focus:ring-slate-300 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-150 shadow-sm focus:outline-none">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah User
        </a>
    </div>

    <div class="overflow-x-auto ring-1 ring-gray-200 rounded-lg">
        <table class="min-w-full bg-white text-left whitespace-nowrap">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">ID User</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Username</th>
                    <th class="py-3 px-5 text-sm font-semibold text-gray-700 uppercase tracking-wide">Role (Hak Akses)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50 transition duration-150 ease-in-out">
                    <td class="py-4 px-5 text-sm font-bold text-gray-700"><?php echo $row['user_id']; ?></td>
                    <td class="py-4 px-5 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['username']); ?></td>
                    <td class="py-4 px-5 text-sm">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                            <?php echo htmlspecialchars($row['nama_role']); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="3" class="py-8 text-center text-gray-500">
                        <p class="mt-2 text-sm font-medium">Belum ada user terdaftar.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>