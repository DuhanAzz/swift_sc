<?php
include '../includes/koneksi.php';

$id = $_GET['id'];

try {
    $database->deleteDocument('performances', $id);
    header("location:performa.php?pesan=sukses_hapus");
} catch (\Exception $e) {
    echo "Gagal menghapus: " . $e->getMessage();
}
?>