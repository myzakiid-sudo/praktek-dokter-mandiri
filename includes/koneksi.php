<?php
$host = "localhost";
$user = "root";       
$pass = "";           
$db   = "db_praktek_dokter";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli($host, $user, $pass, $db);
    $koneksi->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>