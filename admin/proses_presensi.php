<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
include '../includes/koneksi.php';

if(isset($_POST['simpan_presensi'])){
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $statuses = $_POST['status']; // Ini array [atlet_id => status]
    $keterangan = $_POST['keterangan']; // Ini array [atlet_id => keterangan]

    foreach($statuses as $atlet_id => $status){
        $atlet_id = mysqli_real_escape_string($koneksi, $atlet_id);
        $status = mysqli_real_escape_string($koneksi, $status);
        $ket = mysqli_real_escape_string($koneksi, $keterangan[$atlet_id] ?? '');
        
        $cek = mysqli_query($koneksi, "SELECT id FROM absensi WHERE member_id='$atlet_id' AND tanggal='$tanggal'");
        if(mysqli_num_rows($cek) > 0) {
            $row = mysqli_fetch_assoc($cek);
            $id = $row['id'];
            mysqli_query($koneksi, "UPDATE absensi SET status='$status', keterangan='$ket' WHERE id='$id'");
        } else {
            $admin_pool_id = $_SESSION['pool_id'] ?? '';
            $recorded_by = $_SESSION['id'] ?? 0;
            mysqli_query($koneksi, "INSERT INTO absensi (member_id, tanggal, status, cabang_id, recorded_by, keterangan) VALUES ('$atlet_id', '$tanggal', '$status', '$admin_pool_id', '$recorded_by', '$ket')");
        }
    }
    
    header("location:presensi.php?tanggal=$tanggal&pesan=sukses_simpan");
}
?>