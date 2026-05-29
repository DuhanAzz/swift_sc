<?php
session_start();
include '../includes/koneksi.php';

// PROSES TAMBAH KAS BARU
if(isset($_POST['tambah'])){
    $pool_id          = $_POST['pool_id'];
    // Mengambil ID user dari session admin yang sedang login
    $user_id          = $_SESSION['uid'] ?? 'admin'; 
    
    $transaction_date = $_POST['transaction_date'];
    $type             = $_POST['type'];
    $category         = $_POST['category'];
    $amount           = $_POST['amount'];
    $description      = $_POST['description'];

    try {
        $database->newDocument('cash_flows', [
            'pool_id' => $pool_id,
            'user_id' => $user_id,
            'transaction_date' => $transaction_date,
            'type' => $type,
            'category' => $category,
            'amount' => (int)$amount,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:arus_kas.php?pesan=sukses_tambah");
    } catch (\Exception $e) {
        echo "Gagal: " . $e->getMessage();
    }
}

// PROSES EDIT DATA KAS
if(isset($_POST['edit'])){
    $id               = $_POST['id'];
    $transaction_date = $_POST['transaction_date'];
    $type             = $_POST['type'];
    $category         = $_POST['category'];
    $amount           = $_POST['amount'];
    $description      = $_POST['description'];

    try {
        $database->setDocument('cash_flows', $id, [
            'transaction_date' => $transaction_date,
            'type' => $type,
            'category' => $category,
            'amount' => (int)$amount,
            'description' => $description
        ]);
        header("location:arus_kas.php?pesan=sukses_edit");
    } catch (\Exception $e) {
        echo "Gagal: " . $e->getMessage();
    }
}
?>