<?php
require_once 'includes/koneksi.php';
include 'includes/header.php';
?>

<div class="flex items-center justify-center h-[70vh]">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md border border-gray-200">
        <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Login Sistem</h2>
        
        <form action="dashboard.php" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="username" name="username" type="text" placeholder="Masukkan username" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" id="password" name="password" type="password" placeholder="******************" required>
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