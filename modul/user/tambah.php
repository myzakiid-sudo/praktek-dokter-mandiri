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

<div class="bg-white shadow rounded-lg p-6 border border-gray-200 mt-4 max-w-lg mx-auto">
    <div class="mb-6"><a href="index.php" class="text-sm text-gray-500 font-semibold">&larr; Batal</a></div>
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah User Baru</h2>

    <?php if(isset($error)): ?><div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div><?php endif; ?>

    <form action="" method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
            <input name="username" type="text" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input name="password" type="password" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Role</label>
            <select name="role_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                <?php while($r = $role_query->fetch_assoc()): ?>
                    <option value="<?php echo $r['role_id']; ?>"><?php echo htmlspecialchars($r['nama_role']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded w-full">Simpan User</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>