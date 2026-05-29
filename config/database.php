<?php
// config/database.php
$host = "localhost";
$user = "root"; // Default XAMPP
$pass = "";     // Default XAMPP (kosong)
$db   = "Swift_SC"; // Nama database sesuai yang kita buat

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Set zona waktu agar data absensi/pendaftaran akurat
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk mengamankan inputan dari SQL Injection (Bonus Keamanan)
function bersihkan_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}
?>