<?php
include '../includes/koneksi.php';

if(isset($_POST['tambah'])){
    $nama_cabang = mysqli_real_escape_string($koneksi, $_POST['nama_cabang']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);

    $q = mysqli_query($koneksi, "INSERT INTO cabang (nama_cabang, lokasi) VALUES ('$nama_cabang', '$lokasi')");
    if($q) {
        header("location:ceo_manage_kolam.php?pesan=sukses_tambah");
    } else {
        header("location:ceo_manage_kolam.php?pesan=gagal");
    }
}

if(isset($_POST['edit'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama_cabang = mysqli_real_escape_string($koneksi, $_POST['nama_cabang']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);

    $q = mysqli_query($koneksi, "UPDATE cabang SET nama_cabang='$nama_cabang', lokasi='$lokasi' WHERE id='$id'");
    if($q) {
        header("location:ceo_manage_kolam.php?pesan=sukses_edit");
    } else {
        header("location:ceo_manage_kolam.php?pesan=gagal");
    }
}

if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // (Opsional) jika mau hapus foto lama saat cabang dihapus CEO
    // Meskipun upload fotonya dari Admin
    $upload_dir = '../uploads/';
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
