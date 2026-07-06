<?php
include '../includes/koneksi.php';

// === PROSES ADMIN & MANAJER ===

if(isset($_POST['tambah_admin'])){
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $role_id  = $_POST['role_id'];
    $password = $_POST['password'];
    $pool_id  = empty($_POST['pool_id']) ? null : $_POST['pool_id'];
    $role_str = ($role_id == 1) ? 'ceo' : 'admin';

    try {
        $userProperties = [
            'email' => $email,
            'emailVerified' => false,
            'password' => $password,
            'displayName' => $name,
        ];
        $createdUser = $auth->createUser($userProperties);
        $uid = $createdUser->uid;

        $database->setDocument('users', $uid, [
            'name' => $name,
            'email' => $email,
            'role' => $role_str,
            'role_id' => $role_id,
            'pool_id' => $pool_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:ceo_manage_akun.php?pesan=sukses_admin");
    } catch (\Exception $e) { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

if(isset($_POST['edit_admin'])){
    $id       = $_POST['id'];
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $role_id  = $_POST['role_id'];
    $password = $_POST['password'];
    $pool_id  = empty($_POST['pool_id']) ? null : $_POST['pool_id'];
    $role_str = ($role_id == 1) ? 'ceo' : 'admin';

    try {
        $userProperties = ['email' => $email, 'displayName' => $name];
        if(!empty($password)) { $userProperties['password'] = $password; }
        $auth->updateUser($id, $userProperties);

        $database->setDocument('users', $id, [
            'name' => $name,
            'email' => $email,
            'role' => $role_str,
            'role_id' => $role_id,
            'pool_id' => $pool_id
        ]);
        header("location:ceo_manage_akun.php?pesan=sukses_admin");
    } catch (\Exception $e) { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

if(isset($_GET['hapus_admin'])){
    $id = $_GET['hapus_admin'];
    try {
        $auth->deleteUser($id);
        $database->deleteDocument('users', $id);
        header("location:ceo_manage_akun.php?pesan=hapus_admin");
    } catch (\Exception $e) { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

// === PROSES PELATIH ===

if(isset($_POST['tambah_pelatih'])){
    $nama_pelatih = $_POST['nama_pelatih'];
    $email        = $_POST['email'];
    $password     = $_POST['password'];
    $lisensi      = $_POST['lisensi'];
    $no_hp        = $_POST['no_hp'];
    $id_kolam     = $_POST['id_kolam'];

    try {
        $userProperties = [
            'email' => $email,
            'emailVerified' => false,
            'password' => $password,
            'displayName' => $nama_pelatih,
        ];
        $createdUser = $auth->createUser($userProperties);
        $uid = $createdUser->uid;

        $database->setDocument('users', $uid, [
            'name' => $nama_pelatih,
            'email' => $email,
            'role' => 'coach',
            'role_id' => 3,
            'pool_id' => $id_kolam,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $database->newDocument('coaches', [
            'auth_uid' => $uid,
            'nama_pelatih' => $nama_pelatih,
            'lisensi' => $lisensi,
            'no_hp' => $no_hp,
            'id_kolam' => $id_kolam,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:ceo_manage_akun.php?pesan=sukses_pelatih");
    } catch (\Exception $e) { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

if(isset($_POST['edit_pelatih'])){
    $id           = $_POST['id'];
    $nama_pelatih = $_POST['nama_pelatih'];
    $lisensi      = $_POST['lisensi'];
    $no_hp        = $_POST['no_hp'];
    $id_kolam     = $_POST['id_kolam'];

    try {
        $database->setDocument('coaches', $id, [
            'nama_pelatih' => $nama_pelatih,
            'lisensi' => $lisensi,
            'no_hp' => $no_hp,
            'id_kolam' => $id_kolam
        ]);
        header("location:ceo_manage_akun.php?pesan=sukses_pelatih");
    } catch (\Exception $e) { header("location:ceo_manage_akun.php?pesan=gagal"); }
}

if(isset($_GET['hapus_pelatih'])){
    $id = $_GET['hapus_pelatih'];
    try {
        $database->deleteDocument('coaches', $id);
        header("location:ceo_manage_akun.php?pesan=hapus_pelatih");
    } catch (\Exception $e) { header("location:ceo_manage_akun.php?pesan=gagal"); }
}
?>
