<?php
include '../includes/koneksi.php';

// Folder uploads
$upload_dir = '../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if(isset($_POST['tambah'])){
    $nama_cabang = mysqli_real_escape_string($koneksi, $_POST['nama_cabang']);
    $jam_operasional = mysqli_real_escape_string($koneksi, $_POST['jam_operasional']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Upload Foto
    $foto_name = '';
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = 'cabang_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name);
    }

    $q = mysqli_query($koneksi, "INSERT INTO cabang (nama_cabang, lokasi, deskripsi, jam_operasional, foto) VALUES ('$nama_cabang', '$lokasi', '$deskripsi', '$jam_operasional', '$foto_name')");
    if($q) {
        header("location:ceo_manage_kolam.php?pesan=sukses_tambah");
    } else {
        header("location:ceo_manage_kolam.php?pesan=gagal");
    }
}

if(isset($_POST['edit'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama_cabang = mysqli_real_escape_string($koneksi, $_POST['nama_cabang']);
    $jam_operasional = mysqli_real_escape_string($koneksi, $_POST['jam_operasional']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $foto_lama = $_POST['foto_lama'];
    
    $foto_update = $foto_lama; // Fallback
    
    // Cek apakah ada file foto baru yang diupload
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_baru = 'cabang_' . time() . '.' . $ext;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_baru)) {
            $foto_update = $foto_baru;
            // Hapus foto lama jika ada dan bukan kosong
            if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
                unlink($upload_dir . $foto_lama);
            }
        }
    }

    $q = mysqli_query($koneksi, "UPDATE cabang SET nama_cabang='$nama_cabang', lokasi='$lokasi', deskripsi='$deskripsi', jam_operasional='$jam_operasional', foto='$foto_update' WHERE id='$id'");
    
    if($q) {
        header("location:ceo_manage_kolam.php?pesan=sukses_edit");
    } else {
        header("location:ceo_manage_kolam.php?pesan=gagal");
    }
}

if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Ambil info foto sebelum menghapus
    $q_info = mysqli_query($koneksi, "SELECT foto FROM cabang WHERE id='$id'");
    if ($q_info && $row = mysqli_fetch_assoc($q_info)) {
        $foto_lama = $row['foto'];
        if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
            unlink($upload_dir . $foto_lama);
        }
    }
    
    $q = mysqli_query($koneksi, "DELETE FROM cabang WHERE id='$id'");
    if($q) {
        header("location:ceo_manage_kolam.php?pesan=sukses_hapus");
    } else {
        header("location:ceo_manage_kolam.php?pesan=gagal");
    }
}
?>
