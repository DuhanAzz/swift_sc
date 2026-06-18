<?php
include '../includes/koneksi.php';

if(isset($_POST['tambah'])){
    $lokasi      = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $hari        = mysqli_real_escape_string($koneksi, $_POST['hari']);
    $jam_mulai   = mysqli_real_escape_string($koneksi, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($koneksi, $_POST['jam_selesai']);

    $q = mysqli_query($koneksi, "INSERT INTO jadwal (lokasi, hari, jam_mulai, jam_selesai) VALUES ('$lokasi', '$hari', '$jam_mulai', '$jam_selesai')");
    if($q) { header("location:ceo_cms_jadwal.php?pesan=sukses_tambah"); }
    else { header("location:ceo_cms_jadwal.php?pesan=gagal"); }
}

if(isset($_POST['edit'])){
    $id          = mysqli_real_escape_string($koneksi, $_POST['id']);
    $lokasi      = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $hari        = mysqli_real_escape_string($koneksi, $_POST['hari']);
    $jam_mulai   = mysqli_real_escape_string($koneksi, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($koneksi, $_POST['jam_selesai']);

    $q = mysqli_query($koneksi, "UPDATE jadwal SET lokasi='$lokasi', hari='$hari', jam_mulai='$jam_mulai', jam_selesai='$jam_selesai' WHERE id='$id'");
    if($q) { header("location:ceo_cms_jadwal.php?pesan=sukses_edit"); }
    else { header("location:ceo_cms_jadwal.php?pesan=gagal"); }
}

if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $q = mysqli_query($koneksi, "DELETE FROM jadwal WHERE id='$id'");
    if($q) { header("location:ceo_cms_jadwal.php?pesan=sukses_hapus"); }
    else { header("location:ceo_cms_jadwal.php?pesan=gagal"); }
}
?>
