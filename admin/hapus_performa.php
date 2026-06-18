<?php
include '../includes/koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$q = mysqli_query($koneksi, "DELETE FROM performa WHERE id='$id'");

if($q) {
    header("location:performa.php?pesan=sukses_hapus");
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>