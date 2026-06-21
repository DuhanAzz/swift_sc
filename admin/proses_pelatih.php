<?php
session_start();
include '../includes/koneksi.php';

// Check admin authorization
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'ceo'])) { header("location:../login.php"); exit; }
$pool_id = !empty($_SESSION['pool_id']) ? $_SESSION['pool_id'] : "NULL";

// PROSES TAMBAH DATA PELATIH
if(isset($_POST['tambah'])){
    $nama_pelatih   = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $email          = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password       = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $lisensi        = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
    $kelas_mengajar = mysqli_real_escape_string($koneksi, $_POST['kelas_mengajar']);
    $no_hp          = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $jabatan        = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    
    // Default foto
    $foto_name = 'default_coach.jpg';
    if(isset($_FILES['foto_pelatih']) && $_FILES['foto_pelatih']['error'] == 0){
        $ext = pathinfo($_FILES['foto_pelatih']['name'], PATHINFO_EXTENSION);
        $foto_name = 'pelatih_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['foto_pelatih']['tmp_name'], 'uploads/' . $foto_name);
    }

    $foto_hover_name = NULL;
    if(isset($_FILES['foto_hover_pelatih']) && $_FILES['foto_hover_pelatih']['error'] == 0){
        $ext_h = pathinfo($_FILES['foto_hover_pelatih']['name'], PATHINFO_EXTENSION);
        $foto_hover_name = 'pelatih_hover_1_' . time() . '.' . $ext_h;
        move_uploaded_file($_FILES['foto_hover_pelatih']['tmp_name'], 'uploads/' . $foto_hover_name);
    }

    $foto_hover_2_name = NULL;
    if(isset($_FILES['foto_hover_2']) && $_FILES['foto_hover_2']['error'] == 0){
        $ext_h2 = pathinfo($_FILES['foto_hover_2']['name'], PATHINFO_EXTENSION);
        $foto_hover_2_name = 'pelatih_hover_2_' . time() . '.' . $ext_h2;
        move_uploaded_file($_FILES['foto_hover_2']['tmp_name'], 'uploads/' . $foto_hover_2_name);
    }

    $q1 = mysqli_query($koneksi, "INSERT INTO users (username, email, password, role, cabang_id) VALUES ('$nama_pelatih', '$email', '$password', 'Pelatih', $pool_id)");
    $new_user_id = mysqli_insert_id($koneksi);

    $q_c = mysqli_query($koneksi, "SELECT nama_cabang FROM cabang WHERE id=$pool_id");
    $nama_c = ($q_c && $r = mysqli_fetch_assoc($q_c)) ? $r['nama_cabang'] : 'Pusat';

    $f_hover_val = $foto_hover_name ? "'$foto_hover_name'" : "NULL";
    $f_hover_2_val = $foto_hover_2_name ? "'$foto_hover_2_name'" : "NULL";
    $q2 = mysqli_query($koneksi, "INSERT INTO pelatih (user_id, nama, lisensi, kelas_mengajar, no_hp, jabatan, id_kolam, cabang, foto, foto_hover_1, foto_hover_2) VALUES ('$new_user_id', '$nama_pelatih', '$lisensi', '$kelas_mengajar', '$no_hp', '$jabatan', $pool_id, '$nama_c', '$foto_name', $f_hover_val, $f_hover_2_val)");
    
    if($q1 && $q2) {
        header("location:pelatih.php?pesan=sukses_tambah");
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}

// PROSES EDIT DATA PELATIH
if(isset($_POST['edit'])){
    $id             = mysqli_real_escape_string($koneksi, $_POST['id']);
    $user_id        = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $nama_pelatih   = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $email          = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password_baru  = $_POST['password_baru'];
    $lisensi        = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
    $kelas_mengajar = mysqli_real_escape_string($koneksi, $_POST['kelas_mengajar']);
    $no_hp          = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $jabatan        = mysqli_real_escape_string($koneksi, $_POST['jabatan']);

    $q_c = mysqli_query($koneksi, "SELECT nama_cabang FROM cabang WHERE id=$pool_id");
    $nama_c = ($q_c && $r = mysqli_fetch_assoc($q_c)) ? $r['nama_cabang'] : 'Pusat';

    // Get old name for fallback
    $old_nama = '';
    if(!$user_id) {
        $q_old = mysqli_query($koneksi, "SELECT nama FROM pelatih WHERE id='$id'");
        if($q_old && $row = mysqli_fetch_assoc($q_old)) {
            $old_nama = mysqli_real_escape_string($koneksi, $row['nama']);
        }
    }

    $foto_query = "";
    if(isset($_FILES['foto_pelatih']) && $_FILES['foto_pelatih']['error'] == 0){
        $ext = pathinfo($_FILES['foto_pelatih']['name'], PATHINFO_EXTENSION);
        $foto_name = 'pelatih_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['foto_pelatih']['tmp_name'], 'uploads/' . $foto_name);
        $foto_query .= ", foto='$foto_name'";
    }

    if(isset($_FILES['foto_hover_pelatih']) && $_FILES['foto_hover_pelatih']['error'] == 0){
        $ext_h = pathinfo($_FILES['foto_hover_pelatih']['name'], PATHINFO_EXTENSION);
        $foto_hover_name = 'pelatih_hover_1_' . time() . '.' . $ext_h;
        move_uploaded_file($_FILES['foto_hover_pelatih']['tmp_name'], 'uploads/' . $foto_hover_name);
        $foto_query .= ", foto_hover_1='$foto_hover_name'";
    }

    if(isset($_FILES['foto_hover_2']) && $_FILES['foto_hover_2']['error'] == 0){
        $ext_h2 = pathinfo($_FILES['foto_hover_2']['name'], PATHINFO_EXTENSION);
        $foto_hover_2_name = 'pelatih_hover_2_' . time() . '.' . $ext_h2;
        move_uploaded_file($_FILES['foto_hover_2']['tmp_name'], 'uploads/' . $foto_hover_2_name);
        $foto_query .= ", foto_hover_2='$foto_hover_2_name'";
    }

    // 1. Update Pelatih
    $q1 = mysqli_query($koneksi, "UPDATE pelatih SET nama='$nama_pelatih', lisensi='$lisensi', kelas_mengajar='$kelas_mengajar', no_hp='$no_hp', jabatan='$jabatan', id_kolam=$pool_id, cabang='$nama_c' $foto_query WHERE id='$id'");
    
    // 2. Update Users
    if($user_id) {
        if(!empty($password_baru)) {
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $q2 = mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email', password='$hash' WHERE id='$user_id'");
        } else {
            $q2 = mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email' WHERE id='$user_id'");
        }
    } else if($old_nama) {
        if(!empty($password_baru)) {
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email', password='$hash' WHERE username='$old_nama' AND role='Pelatih'");
        } else {
            mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email' WHERE username='$old_nama' AND role='Pelatih'");
        }
        
        // If no user was updated (meaning this legacy coach never had an account), create one!
        if(mysqli_affected_rows($koneksi) == 0 && !empty($email) && !empty($password_baru)) {
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $q_insert = mysqli_query($koneksi, "INSERT INTO users (username, email, password, role, cabang_id) VALUES ('$nama_pelatih', '$email', '$hash', 'Pelatih', $pool_id)");
            if($q_insert) {
                $new_user_id = mysqli_insert_id($koneksi);
                mysqli_query($koneksi, "UPDATE pelatih SET user_id='$new_user_id' WHERE id='$id'");
            }
        }
    }

    if($q1) {
        header("location:pelatih.php?pesan=sukses_edit");
    } else {
        echo "Gagal update data: " . mysqli_error($koneksi);
    }
}
?>