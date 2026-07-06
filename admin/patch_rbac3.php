<?php
$files = ['manage_berita.php', 'manage_hero.php', 'manage_pelatih.php', 'export_laporan.php', 'invoice_cetak.php'];
$patch = 'session_start();
if (!isset($_SESSION[\'role\']) || ($_SESSION[\'role\'] != "admin" && $_SESSION[\'role\'] != "ceo")) { 
    header("location:../login.php?pesan=belum_login"); 
    exit; 
}';

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    $content = preg_replace('/session_start\(\);\s*if\s*\(!isset\(\$_SESSION\[\'role\'\]\)\)\s*\{[^}]+\}/i', $patch, $content, 1);
    file_put_contents($file, $content);
    echo "Patched: $file\n";
}
