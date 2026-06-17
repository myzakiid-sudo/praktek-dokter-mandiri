<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/koneksi.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;

try {
    $stmt = $koneksi->prepare("DELETE FROM Pasien WHERE patient_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Redirect dengan status sukses
    header("Location: index.php?pesan=hapus_sukses");
} catch (mysqli_sql_exception $e) {
    // Karena di Tahap 1 kita memakai ON DELETE RESTRICT
    // Jika pasien sudah pernah berobat (ada di tabel kunjungan), dia tidak boleh dihapus
    echo "<script>
        alert('Gagal menghapus! Pasien ini tidak bisa dihapus karena sudah memiliki riwayat kunjungan.');
        window.location.href='index.php';
    </script>";
}
exit;
?>