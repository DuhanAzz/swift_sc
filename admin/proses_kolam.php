<?php
// Panggil koneksi database (sekarang memanggil Firestore SDK)
include '../includes/koneksi.php';

// Cek apakah tombol 'tambah' dari form sudah ditekan
if (isset($_POST['tambah'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];

    try {
        // Create new document in 'pools' collection
        $database->newDocument('pools', [
            'name' => $name,
            'address' => $address
        ]);
        header("location:kolam.php?pesan=sukses_tambah");
    } catch (\Exception $e) {
        echo "Gagal menambahkan data: " . $e->getMessage();
    }
}

// Cek apakah tombol 'edit' dari form Modal Edit ditekan
if (isset($_POST['edit'])) {
    $id = $_POST['id']; // Ini sekarang berupa Document ID dari Firestore
    $name = $_POST['name'];
    $address = $_POST['address'];

    try {
        // Update document
        $database->setDocument('pools', $id, [
            'name' => $name,
            'address' => $address
        ]);
        
        header("location:kolam.php?pesan=sukses_edit");
    } catch (\Exception $e) {
        echo "Gagal update data: " . $e->getMessage();
    }
}
?>