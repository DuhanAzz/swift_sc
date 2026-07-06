<?php
$files = ['atlet.php', 'pelatih.php', 'jadwal.php', 'pembayaran.php', 'performa.php', 'presensi.php', 'rekap_presensi.php', 'leaderboard.php', 'laporan_keuangan.php', 'admin.php', 'proses_presensi_bulanan.php'];
$patch_pages = 'session_start();
if (!isset($_SESSION[\'status\']) || $_SESSION[\'status\'] != "sudah_login" || $_SESSION[\'role\'] != "admin") { 
    header("location:../login.php?pesan=belum_login"); 
    exit; 
}';

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    if (strpos($content, "\$_SESSION['role'] != \"admin\"") === false && strpos($content, "\$_SESSION['role'] != 'admin'") === false) {
        // Find the first session_start() and the following if statement
        $content = preg_replace('/session_start\(\);\s*if\s*\(\$?[^}]+\}/is', $patch_pages, $content, 1);
        file_put_contents($file, $content);
        echo "Patched: $file\n";
    }
}
