<?php
session_start();
require_once '../config/database.php';

if (isset($_POST['login'])) {
    // Variabel dari form tetap 'username', tapi nanti kita cocokkan dengan kolom 'email' di database
    $username = bersihkan_input($_POST['username']);
    $password = bersihkan_input($_POST['password']);

    // REVISI: Cari user di database berdasarkan kolom 'email'
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$username'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Verifikasi Password (pencocokan langsung)
        if ($password == $data['password']) {
            
            // Simpan data penting ke dalam Sesi (Session)
            $_SESSION['user_id'] = $data['id'];
            // REVISI: Mengambil data dari kolom 'name' sesuai struktur database Anda
            $_SESSION['nama_user'] = $data['name']; 
            $_SESSION['role'] = $data['role'];
            $_SESSION['pool_id'] = $data['pool_id']; // Penting untuk filter cabang Admin/Pelatih
            
            if(isset($data['tingkatan_pelatih'])) {
                $_SESSION['tingkatan_pelatih'] = $data['tingkatan_pelatih'];
            }

            // ROUTING (Arahkan ke kamar masing-masing)
            if ($data['role'] == 'ceo') {
                header("Location: ../dashboard/ceo/index.php");
            } elseif ($data['role'] == 'admin') {
                header("Location: ../dashboard/admin/index.php");
            } elseif ($data['role'] == 'pelatih') {
                header("Location: ../dashboard/pelatih/index.php");
            }
            exit;
        } else {
            // Password Salah
            header("Location: login.php?pesan=gagal");
            exit;
        }
    } else {
        // Username/Email tidak ditemukan
        header("Location: login.php?pesan=gagal");
        exit;
    }
} else {
    header("Location: login.php");
}
?>