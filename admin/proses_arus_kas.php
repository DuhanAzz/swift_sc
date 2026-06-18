<?php
session_start();
include '../includes/koneksi.php';

// PROSES TAMBAH KAS BARU
if(isset($_POST['tambah'])){
    $pool_id          = mysqli_real_escape_string($koneksi, $_POST['pool_id']);
    // Mengambil ID user dari session admin yang sedang login
    $user_id          = $_SESSION['user_id'] ?? 0; 
    
    $transaction_date = mysqli_real_escape_string($koneksi, $_POST['transaction_date']);
    $type             = mysqli_real_escape_string($koneksi, $_POST['type']);
    $category         = mysqli_real_escape_string($koneksi, $_POST['category']);
    $amount           = (int)$_POST['amount'];
    $description      = mysqli_real_escape_string($koneksi, $_POST['description']);

    $q = mysqli_query($koneksi, "INSERT INTO cash_flows (pool_id, user_id, transaction_date, type, category, amount, description) VALUES ('$pool_id', '$user_id', '$transaction_date', '$type', '$category', '$amount', '$description')");
    
    if($q) {
        header("location:arus_kas.php?pesan=sukses_tambah");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}

// PROSES EDIT DATA KAS
if(isset($_POST['edit'])){
    $id               = mysqli_real_escape_string($koneksi, $_POST['id']);
    $transaction_date = mysqli_real_escape_string($koneksi, $_POST['transaction_date']);
    $type             = mysqli_real_escape_string($koneksi, $_POST['type']);
    $category         = mysqli_real_escape_string($koneksi, $_POST['category']);
    $amount           = (int)$_POST['amount'];
    $description      = mysqli_real_escape_string($koneksi, $_POST['description']);

    $q = mysqli_query($koneksi, "UPDATE cash_flows SET transaction_date='$transaction_date', type='$type', category='$category', amount='$amount', description='$description' WHERE id='$id'");
    
    if($q) {
        header("location:arus_kas.php?pesan=sukses_edit");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>