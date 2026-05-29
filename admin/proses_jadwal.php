<?php
session_start();
include '../includes/koneksi.php';

// Proses Tambah Jadwal
if(isset($_POST['tambah_jadwal'])){
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $lokasi = $_POST['lokasi'];
    $program = $_POST['program'];

    try {
        $database->newDocument('jadwal', [
            'hari' => $hari,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'lokasi' => $lokasi,
            'program' => $program
        ]);
        header("location:jadwal.php?pesan=sukses");
    } catch (\Exception $e) {
        echo "Gagal: " . $e->getMessage();
    }
}

// Proses Hapus Jadwal
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    try {
        $database->deleteDocument('jadwal', $id);
        header("location:jadwal.php?pesan=hapus");
    } catch (\Exception $e) {
        echo "Gagal: " . $e->getMessage();
    }
}
?>