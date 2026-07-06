<?php
include '../includes/koneksi.php';

$id = $_GET['id'];

try {
    $database->deleteDocument('cash_flows', $id);
    header("location:arus_kas.php?pesan=sukses_hapus");
} catch (\Exception $e) {
    echo "Gagal menghapus: " . $e->getMessage();
}
?>