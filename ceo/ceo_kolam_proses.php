<?php
include '../includes/koneksi.php';

if(isset($_POST['tambah'])){
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $location = mysqli_real_escape_string($koneksi, $_POST['location']);

    $q = mysqli_query($koneksi, "INSERT INTO cabang (nama_cabang, lokasi) VALUES ('$name', '$location')");
    if($q) {
        header("location:ceo_manage_kolam.php?pesan=sukses_tambah");
    } else {
        header("location:ceo_manage_kolam.php?pesan=gagal");
    }
}

if(isset($_POST['edit'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $location = mysqli_real_escape_string($koneksi, $_POST['location']);

    $q = mysqli_query($koneksi, "UPDATE cabang SET nama_cabang='$name', lokasi='$location' WHERE id='$id'");
    if($q) {
        header("location:ceo_manage_kolam.php?pesan=sukses_edit");
    } else {
        header("location:ceo_manage_kolam.php?pesan=gagal");
    }
}

if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $q = mysqli_query($koneksi, "DELETE FROM cabang WHERE id='$id'");
    if($q) {
        header("location:ceo_manage_kolam.php?pesan=sukses_hapus");
    } else {
        header("location:ceo_manage_kolam.php?pesan=gagal");
    }
}
?>
