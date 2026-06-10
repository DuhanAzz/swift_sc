<?php
// config/database.php
$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "Swift_SC"; // Sesuai dengan nama database lokal Anda

try {
    // Mematikan warning bawaan agar kita bisa menangkapnya sebagai exception
    mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
    
    $koneksi = mysqli_connect($host, $user, $pass, $db);
} catch (mysqli_sql_exception $e) {
    die("<div style='font-family: sans-serif; background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 8px; max-width: 600px; margin: 40px auto; text-align: center; border: 1px solid #f87171;'>
            <h2 style='margin-top:0;'>⚠️ Gagal Terhubung ke Database</h2>
            <p>Pastikan layanan <b>MySQL di XAMPP</b> Anda sudah di-start dan nama database (<b>Swift_SC</b>) sudah sesuai di phpMyAdmin.</p>
            <p style='font-size: 14px; color: #7f1d1d;'><b>Detail Error:</b> " . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
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