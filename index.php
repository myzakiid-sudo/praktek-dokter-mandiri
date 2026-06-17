<?php
session_start();
require_once 'includes/koneksi.php';

// Jika user sudah login sebelumnya, langsung lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Proses jika tombol login ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Di dunia nyata gunakan password_hash(), tapi untuk tugas ini kita pakai plain text dulu agar mudah

    // Mencegah SQL Injection dengan Prepared Statements
    $stmt = $koneksi->prepare("SELECT user_id, role_id FROM User WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login Sukses
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['role_id'] = $row['role_id'];
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        // Login Gagal
        $error = "Username atau password salah!";
    }
}

include 'includes/header.php';
?>

<div class="flex items-center justify-center h-[75vh]">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md border border-gray-200">
        <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Login Sistem Klinik</h2>
        
        <?php if($error != ''): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="username" name="username" type="text" placeholder="Contoh: admin_yusuf" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="password" name="password" type="password" placeholder="********" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-300" type="submit">
                    Masuk
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>