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
            
            // 2. Buat ID NIA (Nomor Induk Atlet) — sequential
            $q_last_nia = mysqli_query($koneksi, "SELECT id FROM member ORDER BY id DESC LIMIT 1");
            $next_num = 1;
            if($q_last_nia && $r_nia = mysqli_fetch_assoc($q_last_nia)) {
                $next_num = $r_nia['id'] + 1;
            }
            $nia = 'SWF-' . date('Y') . '-' . str_pad($next_num, 4, '0', STR_PAD_LEFT);

            $calon_member_id = $pendingData['id'];
            $cabang_id = $pendingData['cabang_id'];
            $nama = $pendingData['nama'];
            $jenis_kelamin = $pendingData['jenis_kelamin'];
            $no_hp = $pendingData['no_hp'];
            $tanggal_lahir = $pendingData['tanggal_lahir'];
            $tanggal_gabung = date('Y-m-d');
            
            // Tangkap data tambahan (Kelas & Pelatih)
            $tingkatan_kelas = isset($_POST['tingkatan_kelas']) ? mysqli_real_escape_string($koneksi, $_POST['tingkatan_kelas']) : 'Pemula';
            $pelatih_id = !empty($_POST['pelatih_id']) ? "'" . mysqli_real_escape_string($koneksi, $_POST['pelatih_id']) . "'" : "NULL";
            
            // 3. Masukkan ke tabel member
            $query_insert = "INSERT INTO member (calon_member_id, nia, nama, jenis_kelamin, no_hp, tanggal_lahir, cabang_id, tanggal_gabung, payment_status, status_aktif, tingkatan_kelas, pelatih_id) 
                             VALUES ('$calon_member_id', '$nia', '$nama', '$jenis_kelamin', '$no_hp', '$tanggal_lahir', '$cabang_id', '$tanggal_gabung', 'Unpaid', 'Aktif', '$tingkatan_kelas', $pelatih_id)";
            
            if (mysqli_query($koneksi, $query_insert)) {
                // 4. Update status_approval di calon_member
                $query_update = "UPDATE calon_member SET status_approval = 'Approved' WHERE id = '$pending_id'";
                mysqli_query($koneksi, $query_update);
                
                // Get the newly inserted member ID for invoice
                $new_member_id = mysqli_insert_id($koneksi);
                // Fallback if insert_id returns 0
                if($new_member_id == 0) {
                    $q_find = mysqli_query($koneksi, "SELECT id FROM member WHERE calon_member_id='$calon_member_id' LIMIT 1");
                    if($q_find && $r_find = mysqli_fetch_assoc($q_find)) {
                        $new_member_id = $r_find['id'];
                    }
                }
                
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
