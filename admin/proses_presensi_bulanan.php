<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    header("location:../login.php?pesan=belum_login"); 
    exit; 
}
include '../includes/koneksi.php';

if (isset($_POST['simpan_absensi_bulanan'])) {
    $bulan = str_pad(intval($_POST['bulan']), 2, '0', STR_PAD_LEFT);
    $tahun = intval($_POST['tahun']);
    $admin_pool_id = $_SESSION['pool_id'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;
    
    $abs_data = $_POST['abs'] ?? [];
    // abs[member_id][day] = H|I|S|A|''
    
    $status_map = [
        'H' => 'Hadir',
        'I' => 'Izin',
        'S' => 'Sakit',
        'A' => 'Alpa'
    ];
    
    foreach ($abs_data as $member_id => $days) {
        $member_id = intval($member_id);
        
        foreach ($days as $day => $code) {
            $day = intval($day);
            $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $day);
            
            // Skip empty/kosong
            if (empty($code) || !isset($status_map[$code])) {
                // Delete existing record if status cleared
                mysqli_query($koneksi, "DELETE FROM absensi WHERE member_id='$member_id' AND tanggal='$tanggal'" . (!empty($admin_pool_id) ? " AND cabang_id='$admin_pool_id'" : ""));
                continue;
            }
            
            $status = $status_map[$code];
            
            // Check if record exists
            $q_check = mysqli_query($koneksi, "SELECT id FROM absensi WHERE member_id='$member_id' AND tanggal='$tanggal'" . (!empty($admin_pool_id) ? " AND cabang_id='$admin_pool_id'" : "") . " LIMIT 1");
            
            if ($q_check && mysqli_num_rows($q_check) > 0) {
                // Update existing
                $row = mysqli_fetch_assoc($q_check);
                mysqli_query($koneksi, "UPDATE absensi SET status='$status', recorded_by='$user_id' WHERE id='{$row['id']}'");
            } else {
                // Insert new
                $cabang_val = !empty($admin_pool_id) ? "'$admin_pool_id'" : "NULL";
                mysqli_query($koneksi, "INSERT INTO absensi (member_id, tanggal, status, cabang_id, recorded_by) VALUES ('$member_id', '$tanggal', '$status', $cabang_val, '$user_id')");
            }
        }
    }
    
    header("location:presensi.php?pesan=sukses_simpan&bulan=$bulan&tahun=$tahun");
    exit;
}

header("location:presensi.php");
exit;
?>
