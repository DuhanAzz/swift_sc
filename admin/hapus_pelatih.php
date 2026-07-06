<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
include '../includes/koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$q_member = mysqli_query($koneksi, "UPDATE member SET pelatih_id = NULL WHERE pelatih_id='$id'");
$q = mysqli_query($koneksi, "DELETE FROM pelatih WHERE id='$id'");

if($q) {
    header("location:pelatih.php?pesan=sukses_hapus");
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>