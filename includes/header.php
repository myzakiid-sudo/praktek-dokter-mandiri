<?php
// Pastikan session sudah dimulai jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Hitung path relatif untuk logout dari subfolder mana pun
$script_depth = substr_count($_SERVER['SCRIPT_NAME'], '/') - 1;
$logout_path = str_repeat('../', max(0, $script_depth - 1)) . 'logout.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klinik Mandiri</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800 flex flex-col">
    <nav class="bg-blue-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="font-bold text-xl tracking-tight">Klinik Mandiri</span>
                </div>
                
                <div>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-blue-200 hidden md:inline-block">
                                Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                            <a href="<?php echo $logout_path; ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium transition duration-150">
                                Logout
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex-grow w-full">
