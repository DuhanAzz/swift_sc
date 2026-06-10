<?php
include '../includes/koneksi.php';

// PROSES TAMBAH DATA PELATIH
if(isset($_POST['tambah'])){
    $nama_pelatih = $_POST['nama_pelatih'];
    $lisensi      = $_POST['lisensi'];
    $no_hp        = $_POST['no_hp'];
    $id_kolam     = $_POST['id_kolam'];

    try {
        $database->newDocument('coaches', [
            'nama_pelatih' => $nama_pelatih,
            'lisensi' => $lisensi,
            'no_hp' => $no_hp,
            'id_kolam' => $id_kolam,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:pelatih.php?pesan=sukses_tambah");
    } catch (\Exception $e) {
        echo "Gagal menambahkan data: " . $e->getMessage();
    }
}

// PROSES EDIT DATA PELATIH
if(isset($_POST['edit'])){
    $id           = $_POST['id'];
    $nama_pelatih = $_POST['nama_pelatih'];
    $lisensi      = $_POST['lisensi'];
    $no_hp        = $_POST['no_hp'];
    $id_kolam     = $_POST['id_kolam'];

    try {
        $database->setDocument('coaches', $id, [
            'nama_pelatih' => $nama_pelatih,
            'lisensi' => $lisensi,
            'no_hp' => $no_hp,
            'id_kolam' => $id_kolam
        ]);
        header("location:pelatih.php?pesan=sukses_edit");
    } catch (\Exception $e) {
        echo "Gagal update data: " . $e->getMessage();
    }
}
?>