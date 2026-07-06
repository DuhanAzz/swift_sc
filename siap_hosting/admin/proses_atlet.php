<?php
include '../includes/koneksi.php';

// PROSES TAMBAH
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $no_hp = $_POST['no_hp'];
    $id_kolam = $_POST['id_kolam'];

    try {
        $database->newDocument('atlet', [
            'nama' => $nama,
            'jenis_kelamin' => $jenis_kelamin,
            'no_hp' => $no_hp,
            'id_kolam' => $id_kolam,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:atlet.php?pesan=sukses_tambah");
    } catch (\Exception $e) {
        echo "Gagal menambahkan data: " . $e->getMessage();
    }
}

// PROSES EDIT
if (isset($_POST['edit'])) {
    $id = $_POST['id']; // Firestore Document ID
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $no_hp = $_POST['no_hp'];
    $id_kolam = $_POST['id_kolam'];

    try {
        $database->setDocument('atlet', $id, [
            'nama' => $nama,
            'jenis_kelamin' => $jenis_kelamin,
            'no_hp' => $no_hp,
            'id_kolam' => $id_kolam
        ]);
        header("location:atlet.php?pesan=sukses_edit");
    } catch (\Exception $e) {
        echo "Gagal update data: " . $e->getMessage();
    }
}
?>