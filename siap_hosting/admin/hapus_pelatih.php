<?php
include '../includes/koneksi.php';

$id = $_GET['id'];

try {
    $database->deleteDocument('coaches', $id);
    header("location:pelatih.php?pesan=sukses_hapus");
} catch (\Exception $e) {
    echo "Gagal menghapus: " . $e->getMessage();
}
?>