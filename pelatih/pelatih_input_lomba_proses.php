<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelatih') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';

if (isset($_POST['simpan_performa'])) {
    
    $pelatih_id = $_SESSION['user_id'];
    $cabang_id = $_SESSION['cabang'] ?? 0;
    
    $event_id = (int) $_POST['event_id'];
    $atlet_id = (int) $_POST['atlet_id'];
    $gaya_renang = bersihkan_input($_POST['gaya_renang']);
    $lintasan = bersihkan_input($_POST['lintasan']);
    $jarak = bersihkan_input($_POST['jarak']);
    $tanggal_rekor = bersihkan_input($_POST['tanggal_rekor']);
    $catatan = bersihkan_input($_POST['catatan']);
    
    $split_jarak = $_POST['split_jarak'] ?? [];
    $split_waktu = $_POST['split_waktu'] ?? [];
    
    if (empty($split_jarak) || empty($split_waktu) || count($split_jarak) != count($split_waktu)) {
        $_SESSION['pesan'] = "Gagal: Data split tidak valid atau kosong.";
        header("Location: pelatih_input_lomba.php");
        exit;
    }
    
    // Fungsi konversi waktu "MM:SS.ms" ke millisecond integer
    // ms disimulasikan sebagai perseratus detik (centiseconds) yang biasa ditulis di renang (contoh .50 = 500ms)
    function timeToMs($timeStr) {
        $parts = explode(':', $timeStr);
        if(count($parts) != 2) return 0;
        
        $m = (int)$parts[0];
        
        $sec_parts = explode('.', $parts[1]);
        $s = (int)$sec_parts[0];
        
        // ms di renang biasanya 2 digit (centiseconds). e.g., 50 -> 500ms
        $ms = 0;
        if(isset($sec_parts[1])) {
            $ms_str = str_pad($sec_parts[1], 3, '0', STR_PAD_RIGHT); // 50 -> 500
            $ms = (int)$ms_str;
        }
        
        return ($m * 60000) + ($s * 1000) + $ms;
    }
    
    // Waktu total diambil dari split terakhir
    $waktu_total_format = end($split_waktu);
    $waktu_total_ms = timeToMs($waktu_total_format);
    
    if ($waktu_total_ms <= 0) {
        $_SESSION['pesan'] = "Gagal: Format waktu total tidak valid.";
        header("Location: pelatih_input_lomba.php");
        exit;
    }

    // 1. Insert Master ke tabel `performa`
    $tipe_kolam = 'Lomba'; // Hardcode tipe_kolam jika ini dari event lomba
    
    // Gunakan Prepared Statement
    $stmt = mysqli_prepare($koneksi, "INSERT INTO performa (member_id, cabang_id, gaya_renang, jarak, tipe_kolam, waktu_formatted, waktu_ms, tanggal_rekor, catatan, recorded_by, event_id, lintasan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    mysqli_stmt_bind_param($stmt, "iissssisssis", $atlet_id, $cabang_id, $gaya_renang, $jarak, $tipe_kolam, $waktu_total_format, $waktu_total_ms, $tanggal_rekor, $catatan, $pelatih_id, $event_id, $lintasan);
    
    if (mysqli_stmt_execute($stmt)) {
        $performa_id = mysqli_insert_id($koneksi);
        mysqli_stmt_close($stmt);
        
        // 2. Looping Insert ke tabel `performa_splits`
        $stmt_split = mysqli_prepare($koneksi, "INSERT INTO performa_splits (performa_id, jarak_split, waktu_lap_ms, waktu_lap_format) VALUES (?, ?, ?, ?)");
        
        for ($i = 0; $i < count($split_jarak); $i++) {
            $j = (int) $split_jarak[$i];
            $w_format = bersihkan_input($split_waktu[$i]);
            $w_ms = timeToMs($w_format);
            
            mysqli_stmt_bind_param($stmt_split, "iiis", $performa_id, $j, $w_ms, $w_format);
            mysqli_stmt_execute($stmt_split);
        }
        
        mysqli_stmt_close($stmt_split);
        
        $_SESSION['pesan'] = "Berhasil: Data Race Pace & Split Time tersimpan!";
    } else {
        $_SESSION['pesan'] = "Gagal menyimpan data utama performa.";
    }
    
    header("Location: pelatih_input_lomba.php");
    exit;
}

// Jika akses langsung ke file ini
header("Location: pelatih_input_lomba.php");
exit;
?>
