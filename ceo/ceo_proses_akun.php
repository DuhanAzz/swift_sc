<?php
include '../includes/koneksi.php';

// === PROSES UNIFIED AKUN BARU ===
if(isset($_POST['tambah_akun_baru'])){
    $name     = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role_str = mysqli_real_escape_string($koneksi, $_POST['role']); // 'Admin' atau 'Pelatih'
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pool_id  = empty($_POST['cabang_id']) ? "NULL" : "'".mysqli_real_escape_string($koneksi, $_POST['cabang_id'])."'";

    if($role_str == 'Admin') {
        $q = mysqli_query($koneksi, "INSERT INTO users (username, email, password, role, cabang_id) VALUES ('$name', '$email', '$password', '$role_str', $pool_id)");
        if($q) { header("location:ceo_manage_akun.php?pesan=sukses_admin"); }
        else { header("location:ceo_manage_akun.php?pesan=gagal"); }
    } else if($role_str == 'Pelatih') {
        // Upload Foto
        $foto_name = '';
        if(isset($_FILES['foto_pelatih']) && $_FILES['foto_pelatih']['error'] == 0){
            $ext = pathinfo($_FILES['foto_pelatih']['name'], PATHINFO_EXTENSION);
            $foto_name = 'pelatih_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['foto_pelatih']['tmp_name'], '../uploads/' . $foto_name);
        }

        // Simpan ke users untuk login
        $q1 = mysqli_query($koneksi, "INSERT INTO users (username, email, password, role, cabang_id) VALUES ('$name', '$email', '$password', '$role_str', $pool_id)");
        $new_user_id = mysqli_insert_id($koneksi);

        // Simpan ke pelatih untuk data profil
        $lisensi = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
        $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']); // digunakan sebagai jabatan/no_hp
        
        // Cek cabang nama
        $q_c = mysqli_query($koneksi, "SELECT nama_cabang FROM cabang WHERE id=$pool_id");
        $nama_c = ($q_c && $r = mysqli_fetch_assoc($q_c)) ? $r['nama_cabang'] : 'Pusat';

        $q2 = mysqli_query($koneksi, "INSERT INTO pelatih (user_id, nama, sertifikasi, jabatan, id_kolam, cabang, foto) VALUES ('$new_user_id', '$name', '$lisensi', '$no_hp', $pool_id, '$nama_c', '$foto_name')");
        
        if($q1 && $q2) { header("location:ceo_manage_akun.php?pesan=sukses_pelatih"); }
        else { header("location:ceo_manage_akun.php?pesan=gagal"); }
    }
}

// === PROSES ADMIN & MANAJER ===

if(isset($_POST['tambah_admin'])){
    $name     = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role_id  = $_POST['role_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pool_id  = empty($_POST['cabang_id']) ? "NULL" : "'".mysqli_real_escape_string($koneksi, $_POST['cabang_id'])."'";
    $role_str = ($role_id == 1) ? 'CEO' : 'Admin';

    $q = mysqli_query($koneksi, "INSERT INTO users (username, email, password, role, cabang_id) VALUES ('$name', '$email', '$password', '$role_str', $pool_id)");
    if($q) { header("location:ceo_manage_akun.php?pesan=sukses_admin"); }
    else { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

if(isset($_POST['edit_admin'])){
    $id       = mysqli_real_escape_string($koneksi, $_POST['id']);
    $name     = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role_id  = $_POST['role_id'];
    $pool_id  = empty($_POST['cabang_id']) ? "NULL" : "'".mysqli_real_escape_string($koneksi, $_POST['cabang_id'])."'";
    $role_str = ($role_id == 1) ? 'CEO' : 'Admin';
    $password = $_POST['password'];

    if(!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $q = mysqli_query($koneksi, "UPDATE users SET username='$name', email='$email', role='$role_str', cabang_id=$pool_id, password='$hash' WHERE id='$id'");
    } else {
        $q = mysqli_query($koneksi, "UPDATE users SET username='$name', email='$email', role='$role_str', cabang_id=$pool_id WHERE id='$id'");
    }

    if($q) { header("location:ceo_manage_akun.php?pesan=sukses_admin"); }
    else { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

if(isset($_GET['hapus_admin'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus_admin']);
    $q = mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");
    if($q) { header("location:ceo_manage_akun.php?pesan=hapus_admin"); }
    else { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

// === PROSES PELATIH ===

if(isset($_POST['tambah_pelatih'])){
    $nama_pelatih = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $email        = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $lisensi      = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
    $no_hp        = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $cabang_str   = empty($_POST['id_kolam']) ? 'Pusat' : mysqli_real_escape_string($koneksi, $_POST['id_kolam']);

    $q_cab = mysqli_query($koneksi, "SELECT id FROM cabang WHERE nama_cabang='$cabang_str' LIMIT 1");
    $cabang_id = "NULL";
    if($q_cab && $row_cab = mysqli_fetch_assoc($q_cab)) {
        $cabang_id = "'" . $row_cab['id'] . "'";
    }

    $q1 = mysqli_query($koneksi, "INSERT INTO users (username, email, password, role, cabang_id) VALUES ('$nama_pelatih', '$email', '$password', 'Pelatih', $cabang_id)");
    $q2 = mysqli_query($koneksi, "INSERT INTO pelatih (nama, jabatan, sertifikasi, cabang) VALUES ('$nama_pelatih', '$no_hp', '$lisensi', '$cabang_str')");

    if($q1 && $q2) { header("location:ceo_manage_akun.php?pesan=sukses_pelatih"); }
    else { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

if(isset($_POST['edit_pelatih'])){
    $id           = mysqli_real_escape_string($koneksi, $_POST['id']);
    $user_id      = isset($_POST['user_id']) ? mysqli_real_escape_string($koneksi, $_POST['user_id']) : '';
    $nama_pelatih = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $email        = isset($_POST['email']) ? mysqli_real_escape_string($koneksi, $_POST['email']) : '';
    $password_baru= isset($_POST['password_baru']) ? $_POST['password_baru'] : '';
    $lisensi      = mysqli_real_escape_string($koneksi, $_POST['lisensi']);
    $no_hp        = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    
    $raw_id_kolam = $_POST['id_kolam'] ?? '';
    if(is_numeric($raw_id_kolam)) {
        $id_kolam = $raw_id_kolam;
        $q_c = mysqli_query($koneksi, "SELECT nama_cabang FROM cabang WHERE id='$id_kolam'");
        $nama_c = ($q_c && $r = mysqli_fetch_assoc($q_c)) ? $r['nama_cabang'] : 'Pusat';
    } else {
        // Fallback if legacy form sent branch name instead of ID
        $nama_c = mysqli_real_escape_string($koneksi, $raw_id_kolam);
        $q_c = mysqli_query($koneksi, "SELECT id FROM cabang WHERE nama_cabang='$nama_c' LIMIT 1");
        $id_kolam = ($q_c && $r = mysqli_fetch_assoc($q_c)) ? $r['id'] : 'NULL';
    }

    $id_kolam_val = empty($id_kolam) || $id_kolam == 'NULL' ? "NULL" : "'".mysqli_real_escape_string($koneksi, $id_kolam)."'";

    // Get old name for fallback
    $old_nama = '';
    if(!$user_id) {
        $q_old = mysqli_query($koneksi, "SELECT nama FROM pelatih WHERE id='$id'");
        if($q_old && $row = mysqli_fetch_assoc($q_old)) {
            $old_nama = mysqli_real_escape_string($koneksi, $row['nama']);
        }
    }

    // 1. Update Pelatih
    $q1 = mysqli_query($koneksi, "UPDATE pelatih SET nama='$nama_pelatih', sertifikasi='$lisensi', jabatan='$no_hp', id_kolam=$id_kolam_val, cabang='$nama_c' WHERE id='$id'");
    
    // 2. Update Users
    if($user_id) {
        if(!empty($password_baru)) {
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $q2 = mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email', password='$hash' WHERE id='$user_id'");
        } else {
            $q2 = mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email' WHERE id='$user_id'");
        }
    } else if($old_nama) {
        // Fallback for older data without user_id
        if(!empty($password_baru)) {
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email', password='$hash' WHERE username='$old_nama' AND role='Pelatih'");
        } else {
            mysqli_query($koneksi, "UPDATE users SET username='$nama_pelatih', email='$email' WHERE username='$old_nama' AND role='Pelatih'");
        }
    }

    if($q1) { header("location:ceo_manage_akun.php?pesan=sukses_pelatih"); }
    else { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

if(isset($_GET['hapus_pelatih'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus_pelatih']);
    
    $q_get = mysqli_query($koneksi, "SELECT user_id, nama FROM pelatih WHERE id='$id'");
    if($q_get && $row = mysqli_fetch_assoc($q_get)) {
        if($row['user_id']) {
            mysqli_query($koneksi, "DELETE FROM users WHERE id='{$row['user_id']}'");
        } else {
            mysqli_query($koneksi, "DELETE FROM users WHERE username='{$row['nama']}' AND role='Pelatih'");
        }
    }

    $q = mysqli_query($koneksi, "DELETE FROM pelatih WHERE id='$id'");
    if($q) { header("location:ceo_manage_akun.php?pesan=hapus_pelatih"); }
    else { header("location:ceo_manage_akun.php?pesan=gagal"); }
}
?>
