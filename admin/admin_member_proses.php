<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/koneksi.php';

if (isset($_POST['approve'])) {
    $pending_id = bersihkan_input($_POST['id']);

    try {
        // 1. Ambil data dari calon_member
        $query_calon = "SELECT * FROM calon_member WHERE id = '$pending_id'";
        $res_calon = mysqli_query($koneksi, $query_calon);
        
        if ($res_calon && mysqli_num_rows($res_calon) > 0) {
            $pendingData = mysqli_fetch_assoc($res_calon);
            
            // 2. Buat ID NIA (Nomor Induk Atlet) sederhana
            $nia = 'SWF-' . date('Y') . '-' . rand(1000, 9999);

            $calon_member_id = $pendingData['id'];
            $cabang_id = $pendingData['cabang_id'];
            $nama = $pendingData['nama'];
            $jenis_kelamin = $pendingData['jenis_kelamin'];
            $no_hp = $pendingData['no_hp'];
            $tanggal_lahir = $pendingData['tanggal_lahir'];
            $tanggal_gabung = date('Y-m-d');
            
            // 3. Masukkan ke tabel member
            $query_insert = "INSERT INTO member (calon_member_id, nia, nama, jenis_kelamin, no_hp, tanggal_lahir, cabang_id, tanggal_gabung, payment_status, status_aktif) 
                             VALUES ('$calon_member_id', '$nia', '$nama', '$jenis_kelamin', '$no_hp', '$tanggal_lahir', '$cabang_id', '$tanggal_gabung', 'Unpaid', 'Aktif')";
            
            if (mysqli_query($koneksi, $query_insert)) {
                // 4. Update status_approval di calon_member
                $query_update = "UPDATE calon_member SET status_approval = 'Approved' WHERE id = '$pending_id'";
                mysqli_query($koneksi, $query_update);
                
                // Get the newly inserted member ID for invoice
                $new_member_id = mysqli_insert_id($koneksi);
                
                header("location:admin_member.php?pesan=sukses_approve&invoice_id=" . $new_member_id);
                exit;
            } else {
                throw new Exception("Gagal insert ke tabel member");
            }
        } else {
            throw new Exception("Data tidak ditemukan");
        }
    } catch (\Exception $e) {
        header("location:admin_member.php?pesan=gagal");
        exit;
    }
} else {
    header("location:admin_member.php");
    exit;
}
?>
