<?php
include '../includes/koneksi.php';

// PROSES TAMBAH DATA PELATIH
if(isset($_POST['tambah'])){
    $nama_pelatih = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $lisensi      = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
    $no_hp        = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $id_kolam     = mysqli_real_escape_string($koneksi, $_POST['id_kolam']);

    $q = mysqli_query($koneksi, "INSERT INTO pelatih (nama, sertifikasi, jabatan, cabang) VALUES ('$nama_pelatih', '$lisensi', '$no_hp', '$id_kolam')");
    
    if($q) {
        header("location:pelatih.php?pesan=sukses_tambah");
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}

// PROSES EDIT DATA PELATIH
if(isset($_POST['edit'])){
    $id           = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama_pelatih = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $lisensi      = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
    $no_hp        = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $id_kolam     = mysqli_real_escape_string($koneksi, $_POST['id_kolam']);

    $q = mysqli_query($koneksi, "UPDATE pelatih SET nama='$nama_pelatih', sertifikasi='$lisensi', jabatan='$no_hp', cabang='$id_kolam' WHERE id='$id'");
    
    if($q) {
        header("location:pelatih.php?pesan=sukses_edit");
    } else {
        echo "Gagal update data: " . mysqli_error($koneksi);
    }
}
?>