<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
include '../includes/koneksi.php';

// PROSES TAMBAH PENGGUNA BARU
if(isset($_POST['tambah'])){
    $username = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password = $_POST['password'];
    $cabang_id= empty($_POST['cabang_id']) ? "NULL" : "'".mysqli_real_escape_string($koneksi, $_POST['cabang_id'])."'";

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $q = mysqli_query($koneksi, "INSERT INTO users (username, email, password, role, cabang_id) VALUES ('$username', '$email', '$hashed_password', '$role', $cabang_id)");
    if($q) {
        header("location:admin.php?pesan=sukses_tambah");
    } else {
        echo "Gagal membuat pengguna: " . mysqli_error($koneksi);
    }
}

// PROSES EDIT DATA PENGGUNA
if(isset($_POST['edit'])){
    $id       = mysqli_real_escape_string($koneksi, $_POST['id']);
    $username = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password = $_POST['password'];
    $cabang_id= empty($_POST['cabang_id']) ? "NULL" : "'".mysqli_real_escape_string($koneksi, $_POST['cabang_id'])."'";

    if(!empty($password)){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $q = mysqli_query($koneksi, "UPDATE users SET username='$username', email='$email', role='$role', cabang_id=$cabang_id, password='$hashed_password' WHERE id='$id'");
    } else {
        $q = mysqli_query($koneksi, "UPDATE users SET username='$username', email='$email', role='$role', cabang_id=$cabang_id WHERE id='$id'");
    }

    if($q) {
        header("location:admin.php?pesan=sukses_edit");
    } else {
        echo "Gagal update pengguna: " . mysqli_error($koneksi);
    }
}
?>