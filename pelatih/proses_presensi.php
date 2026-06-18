<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan_presensi'])){
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $statuses = $_POST['status']; // Ini array [atlet_id => status]
    $keterangan = $_POST['keterangan']; // Ini array [atlet_id => keterangan]
    $coach_cabang_id = $_SESSION['cabang_id'] ?? '';
    $recorded_by = $_SESSION['id'] ?? 0;

    foreach($statuses as $atlet_id => $status){
        $ket = $keterangan[$atlet_id] ?? '';
        
        $atlet_id = mysqli_real_escape_string($koneksi, $atlet_id);
        $status = mysqli_real_escape_string($koneksi, $status);
        $ket = mysqli_real_escape_string($koneksi, $ket);
        
        // Cek existing
        $q_check = mysqli_query($koneksi, "SELECT id FROM absensi WHERE member_id='$atlet_id' AND tanggal='$tanggal' AND cabang_id='$coach_cabang_id'");
        
        if(mysqli_num_rows($q_check) > 0) {
            $row = mysqli_fetch_assoc($q_check);
            $id = $row['id'];
            mysqli_query($koneksi, "UPDATE absensi SET status='$status', keterangan='$ket', recorded_by='$recorded_by' WHERE id='$id'");
        } else {
            mysqli_query($koneksi, "INSERT INTO absensi (member_id, tanggal, status, cabang_id, recorded_by, keterangan) VALUES ('$atlet_id', '$tanggal', '$status', '$coach_cabang_id', '$recorded_by', '$ket')");
        }
    }
    
    header("location:presensi.php?tanggal=$tanggal&pesan=sukses_simpan");
}
?>