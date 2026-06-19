<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/koneksi.php';

if (isset($_POST['update_profil'])) {
    $id = intval($_POST['id']);
    $admin_pool_id = intval($_SESSION['pool_id'] ?? 0);
    
    // SECURITY: Admin hanya bisa update cabang miliknya sendiri
    if ($id !== $admin_pool_id || $admin_pool_id === 0) {
        header("location:profil_cabang.php?pesan=akses_ditolak");
        exit;
    }
    
    $nama_cabang = mysqli_real_escape_string($koneksi, $_POST['nama_cabang']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    
    // STRICT: WHERE id = admin's own cabang_id only
    $q = mysqli_query($koneksi, "UPDATE cabang SET nama_cabang='$nama_cabang', lokasi='$lokasi' WHERE id='$admin_pool_id'");
    
    if ($q) {
        header("location:profil_cabang.php?pesan=sukses_edit");
    } else {
        header("location:profil_cabang.php?pesan=gagal");
    }
    exit;
}

header("location:profil_cabang.php");
exit;
?>
