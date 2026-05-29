<?php
include '../includes/koneksi.php';

$id = $_GET['id']; // Firestore Document ID

try {
    $database->deleteDocument('atlet', $id);
    header("location:atlet.php?pesan=sukses_hapus");
} catch (\Exception $e) {
    echo "Gagal menghapus: " . $e->getMessage();
}
?>