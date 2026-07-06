<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
include '../includes/koneksi.php';

// Proses Tambah Jadwal
if(isset($_POST['tambah_jadwal'])){
    $hari = mysqli_real_escape_string($koneksi, $_POST['hari']);
    $jam_mulai = mysqli_real_escape_string($koneksi, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($koneksi, $_POST['jam_selesai']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $program = mysqli_real_escape_string($koneksi, $_POST['program']);

    $q = mysqli_query($koneksi, "INSERT INTO jadwal (hari, jam_mulai, jam_selesai, lokasi, program) VALUES ('$hari', '$jam_mulai', '$jam_selesai', '$lokasi', '$program')");
    if($q) {
        header("location:jadwal.php?pesan=sukses");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}

// Proses Hapus Jadwal
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $q = mysqli_query($koneksi, "DELETE FROM jadwal WHERE id='$id'");
    if($q) {
        header("location:jadwal.php?pesan=hapus");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>