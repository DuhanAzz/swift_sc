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
            $sekolah = mysqli_real_escape_string($koneksi, $pendingData['sekolah'] ?? '');
            
            // Tangkap data tambahan (Kelas & Pelatih)
            $tingkatan_kelas = isset($_POST['tingkatan_kelas']) ? mysqli_real_escape_string($koneksi, $_POST['tingkatan_kelas']) : 'Pemula';
            $pelatih_id = !empty($_POST['pelatih_id']) ? "'" . mysqli_real_escape_string($koneksi, $_POST['pelatih_id']) . "'" : "NULL";
            
            // 3. Masukkan ke tabel member
            $query_insert = "INSERT INTO member (calon_member_id, nia, nama, jenis_kelamin, no_hp, sekolah, tanggal_lahir, cabang_id, tanggal_gabung, payment_status, status_aktif, tingkatan_kelas, pelatih_id) 
                             VALUES ('$calon_member_id', '$nia', '$nama', '$jenis_kelamin', '$no_hp', '$sekolah', '$tanggal_lahir', '$cabang_id', '$tanggal_gabung', 'Unpaid', 'Aktif', '$tingkatan_kelas', $pelatih_id)";
            
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
} else if (isset($_GET['action']) && $_GET['action'] == 'mark_paid' && isset($_GET['id'])) {
    $member_id = bersihkan_input($_GET['id']);
    
    try {
        // Cek status saat ini
        $q_cek = mysqli_query($koneksi, "SELECT * FROM member WHERE id = '$member_id'");
        if ($q_cek && mysqli_num_rows($q_cek) > 0) {
            $member_data = mysqli_fetch_assoc($q_cek);
            
            if ($member_data['payment_status'] == 'Unpaid') {
                // Update tabel member
                $q_upd = mysqli_query($koneksi, "UPDATE member SET payment_status = 'Paid' WHERE id = '$member_id'");
                
                if ($q_upd) {
                    // Masukkan ke Arus Kas (Pendaftaran 100.000)
                    $admin_id = intval($_SESSION['user_id'] ?? 0);
                    $cabang_id = intval($member_data['cabang_id'] ?? 0);
                    $nominal_pendaftaran = 100000;
                    $nama_atlet = mysqli_real_escape_string($koneksi, $member_data['nama']);
                    $ket_kas = "Biaya Pendaftaran a/n " . $nama_atlet;
                    $tgl_sekarang = date('Y-m-d');
                    
                    mysqli_query($koneksi, "INSERT INTO arus_kas (cabang_id, jenis, category, nominal, keterangan, tanggal, user_id) 
                                            VALUES ('$cabang_id', 'Pemasukan', 'Pendaftaran', '$nominal_pendaftaran', '$ket_kas', '$tgl_sekarang', '$admin_id')");
                    
                    header("location:admin_member.php?pesan=sukses_bayar");
                    exit;
                }
            }
        }
        header("location:admin_member.php");
        exit;
    } catch (\Exception $e) {
        header("location:admin_member.php?pesan=gagal_bayar");
        exit;
    }
} elseif (isset($_POST['action'])) {
    if ($_POST['action'] === 'edit_sekolah' && isset($_POST['member_id']) && isset($_POST['sekolah'])) {
        $member_id = intval($_POST['member_id']);
        $sekolah = mysqli_real_escape_string($koneksi, $_POST['sekolah']);
        mysqli_query($koneksi, "UPDATE member SET sekolah = '$sekolah' WHERE id = '$member_id'");
        header("location:admin_member.php?pesan=sukses_edit");
        exit;
    } elseif ($_POST['action'] === 'mutasi_pelatih' && isset($_POST['member_id']) && isset($_POST['pelatih_id'])) {
        $member_id = intval($_POST['member_id']);
        $pelatih_id = intval($_POST['pelatih_id']);
        mysqli_query($koneksi, "UPDATE member SET pelatih_id = '$pelatih_id' WHERE id = '$member_id'");
        header("location:admin_member.php?pesan=sukses_mutasi");
        exit;
    } else {
        header("location:admin_member.php");
        exit;
    }
} else {
    header("location:admin_member.php");
    exit;
}
?>
