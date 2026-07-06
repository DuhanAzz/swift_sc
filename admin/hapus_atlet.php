<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
include '../includes/koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$q = mysqli_query($koneksi, "DELETE FROM member WHERE id='$id' AND 1=1");
if($q) {
    header("location:atlet.php?pesan=sukses_hapus");
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>