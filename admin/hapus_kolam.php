<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
include '../includes/koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$q_member = mysqli_query($koneksi, "UPDATE member SET cabang_id = NULL WHERE cabang_id='$id'");
$q = mysqli_query($koneksi, "DELETE FROM cabang WHERE id='$id'");

if($q) {
    header("location:kolam.php?pesan=sukses_hapus");
} else {
    echo "Gagal menghapus data: " . mysqli_error($koneksi);
}
?>