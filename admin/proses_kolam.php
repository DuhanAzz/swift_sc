<?php
include '../includes/koneksi.php';

// Cek apakah tombol 'tambah' dari form sudah ditekan
if (isset($_POST['tambah'])) {
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $address = mysqli_real_escape_string($koneksi, $_POST['address']);

    $q = mysqli_query($koneksi, "INSERT INTO cabang (nama_cabang, lokasi) VALUES ('$name', '$address')");
    
    if($q) {
        header("location:kolam.php?pesan=sukses_tambah");
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}

// Cek apakah tombol 'edit' dari form Modal Edit ditekan
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $address = mysqli_real_escape_string($koneksi, $_POST['address']);

    $q = mysqli_query($koneksi, "UPDATE cabang SET nama_cabang='$name', lokasi='$address' WHERE id='$id'");
    
    if($q) {
        header("location:kolam.php?pesan=sukses_edit");
    } else {
        echo "Gagal update data: " . mysqli_error($koneksi);
    }
}
?>