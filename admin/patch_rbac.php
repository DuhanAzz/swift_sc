<?php
$files = glob("*.php");
$patch_pages = 'session_start();
if (!isset($_SESSION[\'status\']) || $_SESSION[\'status\'] != "sudah_login" || $_SESSION[\'role\'] != "admin") { 
    header("location:../login.php?pesan=belum_login"); 
    exit; 
}';
$patch_procs = 'session_start();
if (!isset($_SESSION[\'status\']) || $_SESSION[\'status\'] != "sudah_login" || $_SESSION[\'role\'] != "admin") { 
    exit("Unauthorized access");
}';

foreach ($files as $file) {
    if ($file == 'patch_rbac.php') continue;
    $content = file_get_contents($file);
    
    // Check if already fully patched
    if (strpos($content, "\$_SESSION['role'] != \"admin\"") !== false || strpos($content, "\$_SESSION['role'] != 'admin'") !== false) {
        echo "Already patched: $file\n";
        continue;
    }
    
    // Fix pages that have weak checks
    if (preg_match('/session_start\(\);\s*if\s*\(!isset\(\$_SESSION\[\'status\'\]\)\s*\|\|\s*\$_SESSION\[\'status\'\]\s*!=\s*"sudah_login"\)\s*\{[^}]+\}/i', $content, $matches)) {
        $content = str_replace($matches[0], $patch_pages, $content);
        file_put_contents($file, $content);
        echo "Patched page: $file\n";
        continue;
    }
    
    // Fix processors that only have session_start() but no checks
    if (preg_match('/session_start\(\);\s*(include|require)[^\n]+koneksi\.php\';/i', $content, $matches)) {
        // if it lacks role check
        if (strpos($content, "\$_SESSION['role']") === false) {
            $replacement = $patch_procs . "\n" . $matches[1] . " '../includes/koneksi.php';";
            $content = str_replace($matches[0], $replacement, $content);
            file_put_contents($file, $content);
            echo "Patched processor (with session_start): $file\n";
            continue;
        }
    }
    
    // Fix pure processors (hapus_*.php, proses_*.php) that don't even have session_start()
    if (strpos($content, "<?php\ninclude '../includes/koneksi.php';") !== false) {
        $replacement = "<?php\n" . $patch_procs . "\ninclude '../includes/koneksi.php';";
        $content = str_replace("<?php\ninclude '../includes/koneksi.php';", $replacement, $content);
        file_put_contents($file, $content);
        echo "Patched pure processor: $file\n";
        continue;
    }
    
    echo "Requires manual check: $file\n";
}
echo "Done.\n";
