<?php
include '../includes/koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$q = mysqli_query($koneksi, "DELETE FROM pelatih WHERE id='$id'");

if($q) {
    header("location:pelatih.php?pesan=sukses_hapus");
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>