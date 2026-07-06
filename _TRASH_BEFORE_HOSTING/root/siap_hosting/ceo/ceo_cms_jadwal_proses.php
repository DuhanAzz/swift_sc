<?php
include '../includes/koneksi.php';

if(isset($_POST['tambah'])){
    $lokasi = $_POST['lokasi'];
    $hari = $_POST['hari'];
    $jam = $_POST['jam'];

    try {
        $database->newDocument('public_schedules', [
            'lokasi' => $lokasi,
            'hari' => $hari,
            'jam' => $jam,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:ceo_cms_jadwal.php?pesan=sukses_tambah");
    } catch (\Exception $e) { header("location:ceo_cms_jadwal.php?pesan=gagal"); }
}

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $lokasi = $_POST['lokasi'];
    $hari = $_POST['hari'];
    $jam = $_POST['jam'];

    try {
        $database->setDocument('public_schedules', $id, [
            'lokasi' => $lokasi,
            'hari' => $hari,
            'jam' => $jam
        ]);
        header("location:ceo_cms_jadwal.php?pesan=sukses_edit");
    } catch (\Exception $e) { header("location:ceo_cms_jadwal.php?pesan=gagal"); }
}

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    try {
        $database->deleteDocument('public_schedules', $id);
        header("location:ceo_cms_jadwal.php?pesan=sukses_hapus");
    } catch (\Exception $e) { header("location:ceo_cms_jadwal.php?pesan=gagal"); }
}
?>
