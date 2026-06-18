<?php
include '../includes/koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$q = mysqli_query($koneksi, "DELETE FROM cash_flows WHERE id='$id'");

if($q) {
    header("location:arus_kas.php?pesan=sukses_hapus");
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>