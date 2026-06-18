<?php
include '../includes/koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$q = mysqli_query($koneksi, "DELETE FROM cabang WHERE id='$id'");

if($q) {
    header("location:kolam.php?pesan=sukses_hapus");
} else {
    echo "Gagal menghapus data: " . mysqli_error($koneksi);
}
?>