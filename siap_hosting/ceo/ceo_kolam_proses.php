<?php
include '../includes/koneksi.php';

if(isset($_POST['tambah'])){
    $name = $_POST['name'];
    $location = $_POST['location'];

    try {
        $database->newDocument('pools', [
            'name' => $name,
            'location' => $location,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:ceo_manage_kolam.php?pesan=sukses_tambah");
    } catch (\Exception $e) { header("location:ceo_manage_kolam.php?pesan=gagal"); }
}

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $location = $_POST['location'];

    try {
        $database->setDocument('pools', $id, [
            'name' => $name,
            'location' => $location
        ]);
        header("location:ceo_manage_kolam.php?pesan=sukses_edit");
    } catch (\Exception $e) { header("location:ceo_manage_kolam.php?pesan=gagal"); }
}

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    try {
        $database->deleteDocument('pools', $id);
        header("location:ceo_manage_kolam.php?pesan=sukses_hapus");
    } catch (\Exception $e) { header("location:ceo_manage_kolam.php?pesan=gagal"); }
}
?>
