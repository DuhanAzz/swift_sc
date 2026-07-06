<?php
// config/database.php
$host = "sql307.infinityfree.com";
$user = "if0_42047561"; 
$pass = "hK1S1bFoVBoE";     
$db   = "if0_42047561_swift_sc"; 

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