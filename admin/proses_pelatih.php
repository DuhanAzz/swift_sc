<?php
session_start();
include '../includes/koneksi.php';

$pool_id = $_SESSION['pool_id'] ?? "NULL";

// PROSES TAMBAH DATA PELATIH
if(isset($_POST['tambah'])){
    $nama_pelatih = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $email        = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $lisensi      = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
    $no_hp        = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    
    // Default foto
    $foto_name = 'default_coach.jpg';
    if(isset($_FILES['foto_pelatih']) && $_FILES['foto_pelatih']['error'] == 0){
        $ext = pathinfo($_FILES['foto_pelatih']['name'], PATHINFO_EXTENSION);
        $foto_name = 'pelatih_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['foto_pelatih']['tmp_name'], '../uploads/' . $foto_name);
    }

    $q1 = mysqli_query($koneksi, "INSERT INTO users (username, email, password, role, cabang_id) VALUES ('$nama_pelatih', '$email', '$password', 'Pelatih', $pool_id)");
    $new_user_id = mysqli_insert_id($koneksi);

    $q_c = mysqli_query($koneksi, "SELECT nama_cabang FROM cabang WHERE id=$pool_id");
    $nama_c = ($q_c && $r = mysqli_fetch_assoc($q_c)) ? $r['nama_cabang'] : 'Pusat';

    $q2 = mysqli_query($koneksi, "INSERT INTO pelatih (user_id, nama, sertifikasi, jabatan, id_kolam, cabang, foto) VALUES ('$new_user_id', '$nama_pelatih', '$lisensi', '$no_hp', $pool_id, '$nama_c', '$foto_name')");
    
    if($q1 && $q2) {
        header("location:pelatih.php?pesan=sukses_tambah");
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}

// PROSES EDIT DATA PELATIH
if(isset($_POST['edit'])){
    $id           = mysqli_real_escape_string($koneksi, $_POST['id']);
    $user_id      = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $nama_pelatih = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $email        = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password_baru= $_POST['password_baru'];
    $lisensi      = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
    $no_hp        = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    $q_c = mysqli_query($koneksi, "SELECT nama_cabang FROM cabang WHERE id=$pool_id");
    $nama_c = ($q_c && $r = mysqli_fetch_assoc($q_c)) ? $r['nama_cabang'] : 'Pusat';

    // 1. Update Pelatih
    $q1 = mysqli_query($koneksi, "UPDATE pelatih SET nama='$nama_pelatih', sertifikasi='$lisensi', jabatan='$no_hp', id_kolam=$pool_id, cabang='$nama_c' WHERE id='$id'");
    
    // 2. Update Users
    if($user_id) {
        if(!empty($password_baru)) {
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $q2 = mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email', password='$hash' WHERE id='$user_id'");
        } else {
            $q2 = mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email' WHERE id='$user_id'");
        }
    } else {
        if(!empty($password_baru)) {
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email', password='$hash' WHERE username=(SELECT nama FROM pelatih WHERE id='$id') AND role='Pelatih'");
        } else {
            mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email' WHERE username=(SELECT nama FROM pelatih WHERE id='$id') AND role='Pelatih'");
        }
    }

    if($q1) {
        header("location:pelatih.php?pesan=sukses_edit");
    } else {
        echo "Gagal update data: " . mysqli_error($koneksi);
    }
}
?>