<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/koneksi.php';

// Folder uploads
$upload_dir = '../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

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
    $jam_operasional = mysqli_real_escape_string($koneksi, $_POST['jam_operasional']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $foto_lama = $_POST['foto_lama'];
    
    $foto_update = $foto_lama; // Fallback default
    
    // Cek apakah ada file foto baru yang diupload
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_baru = 'cabang_' . time() . '.' . $ext;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_baru)) {
            $foto_update = $foto_baru;
            // Hapus foto lama jika ada
            if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
                unlink($upload_dir . $foto_lama);
            }
        }
    }
    
    // STRICT: WHERE id = admin's own cabang_id only
    $q = mysqli_query($koneksi, "UPDATE cabang SET nama_cabang='$nama_cabang', lokasi='$lokasi', jam_operasional='$jam_operasional', deskripsi='$deskripsi', foto='$foto_update' WHERE id='$admin_pool_id'");
    
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
