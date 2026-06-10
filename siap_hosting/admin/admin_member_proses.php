<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/koneksi.php';

if (isset($_POST['approve'])) {
    $pending_id = $_POST['id'];

    try {
        // 1. Ambil data dari pending_members
        $pendingData = $database->getDocument('pending_members', $pending_id);
        if (!$pendingData) throw new Exception("Data tidak ditemukan");

        // 2. Buat ID NIA (Nomor Induk Atlet) sederhana
        $nia = 'SWF-' . date('Y') . '-' . rand(1000, 9999);

        // 3. Masukkan ke collection atlet
        $atletData = [
            'nia' => $nia,
            'pool_id' => $pendingData['pool_id'] ?? '',
            'nama' => $pendingData['nama'] ?? '',
            'jenis_kelamin' => $pendingData['jenis_kelamin'] ?? '',
            'no_hp' => $pendingData['no_hp'] ?? '',
            'tanggal_lahir' => $pendingData['tanggal_lahir'] ?? '',
            'tanggal_gabung' => $pendingData['tanggal_gabung'] ?? date('Y-m-d'),
            'payment_status' => 'Unpaid', // Default ke Unpaid
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $database->newDocument('atlet', $atletData);

        // 4. Hapus dari pending_members
        $database->deleteDocument('pending_members', $pending_id);

        header("location:admin_member.php?pesan=sukses_approve");
    } catch (\Exception $e) {
        header("location:admin_member.php?pesan=gagal");
    }
} else {
    header("location:admin_member.php");
}
?>
