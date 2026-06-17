<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../../dashboard.php");
    exit;
}

require_once '../../includes/koneksi.php';

$role_query = $koneksi->query("SELECT role_id, nama_role FROM Role");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Disimpan plain-text untuk kemudahan testing tugas ini
    $role_id = $_POST['role_id'];

    $stmt = $koneksi->prepare("INSERT INTO User (username, password, role_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $password, $role_id);
    
    if ($stmt->execute()) {
        header("Location: index.php?pesan=sukses");
        exit;
    } else {
        $error = "Gagal menambah user. Username mungkin sudah ada.";
    }
}

include '../../includes/header.php';
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
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="index.php" class="ml-1 md:ml-2 text-gray-500 hover:text-blue-600 transition duration-150">User</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-700">Tambah User</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="bg-white shadow-md rounded-xl p-6 md:p-8 border border-gray-100 mt-4 max-w-lg mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah User Baru</h2>
        <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Batal
        </a>
    </div>

    <?php if(isset($error)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <p><?php echo $error; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
            <input name="username" type="text" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
            <input name="password" type="password" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Role <span class="text-red-500">*</span></label>
            <select name="role_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 focus:bg-white transition duration-150 ease-in-out shadow-sm" required>
                <option value="" disabled selected>-- Pilih Role --</option>
                <?php while($r = $role_query->fetch_assoc()): ?>
                    <option value="<?php echo $r['role_id']; ?>"><?php echo htmlspecialchars($r['nama_role']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="pt-4 border-t border-gray-100">
            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-2.5 bg-slate-800 hover:bg-slate-900 focus:ring-4 focus:ring-slate-300 text-white font-semibold rounded-lg transition duration-150 shadow-sm focus:outline-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Simpan User
            </button>
        </div>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>