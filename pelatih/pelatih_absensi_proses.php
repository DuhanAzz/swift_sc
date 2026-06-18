<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan_presensi'])){
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $status_array = $_POST['status'];
    $coach_cabang_id = $_SESSION['cabang'] ?? '';
    $recorded_by = $_SESSION['user_id'] ?? 0;
    
    $success = true;
    
    // Looping data dari form
    foreach($status_array as $member_id => $status) {
        $member_id = mysqli_real_escape_string($koneksi, $member_id);
        $status = mysqli_real_escape_string($koneksi, $status);
        
        // Cek apakah sudah ada presensi untuk member ini pada tanggal ini
        $q_check = mysqli_query($koneksi, "SELECT id FROM absensi WHERE member_id='$member_id' AND tanggal='$tanggal' AND cabang_id='$coach_cabang_id'");
        
        if(mysqli_num_rows($q_check) > 0) {
            $row = mysqli_fetch_assoc($q_check);
            $id = $row['id'];
            $q_update = mysqli_query($koneksi, "UPDATE absensi SET status='$status', recorded_by='$recorded_by' WHERE id='$id'");
            if(!$q_update) $success = false;
        } else {
            $q_insert = mysqli_query($koneksi, "INSERT INTO absensi (member_id, tanggal, status, cabang_id, recorded_by) VALUES ('$member_id', '$tanggal', '$status', '$coach_cabang_id', '$recorded_by')");
            if(!$q_insert) $success = false;
        }
    }
    
    if($success) {
        header("location:pelatih_absensi.php?pesan=sukses");
    } else {
        header("location:pelatih_absensi.php?pesan=gagal"); 
    }
}
?>
