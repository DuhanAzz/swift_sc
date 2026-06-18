<?php
include '../includes/koneksi.php';

// PROSES TAMBAH
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $id_kolam = mysqli_real_escape_string($koneksi, $_POST['id_kolam']);

    $q = mysqli_query($koneksi, "INSERT INTO member (nama, jenis_kelamin, no_hp, cabang_id, role) VALUES ('$nama', '$jenis_kelamin', '$no_hp', '$id_kolam', 'Atlet')");
    if($q) {
        header("location:atlet.php?pesan=sukses_tambah");
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}

// PROSES EDIT
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $id_kolam = mysqli_real_escape_string($koneksi, $_POST['id_kolam']);

    $q = mysqli_query($koneksi, "UPDATE member SET nama='$nama', jenis_kelamin='$jenis_kelamin', no_hp='$no_hp', cabang_id='$id_kolam' WHERE id='$id' AND role='Atlet'");
    if($q) {
        header("location:atlet.php?pesan=sukses_edit");
    } else {
        echo "Gagal update data: " . mysqli_error($koneksi);
    }
}
?>