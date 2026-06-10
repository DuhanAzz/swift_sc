<?php
include '../includes/koneksi.php';

// Tangkap ID (Document ID) yang dikirim lewat URL
$id = $_GET['id'];

try {
    // Hapus dokumen di Firestore
    $database->deleteDocument('pools', $id);
    header("location:kolam.php?pesan=sukses_hapus");
} catch (\Exception $e) {
    echo "Gagal menghapus data: " . $e->getMessage();
}
?>