<?php
include '../includes/koneksi.php';

$id = $_GET['id'];

try {
    // Coba hapus dari Firebase Auth terlebih dahulu
    try {
        $auth->deleteUser($id);
    } catch (\Exception $e) {
        // Jika user tidak ada di auth, hiraukan
    }
    
    // Hapus dari Firestore
    $database->deleteDocument('users', $id);
    
    header("location:admin.php?pesan=sukses_hapus");
} catch (\Exception $e) {
    echo "Gagal menghapus pengguna: " . $e->getMessage();
}
?>