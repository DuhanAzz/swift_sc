<?php
include '../includes/koneksi.php';

// PROSES TAMBAH PENGGUNA BARU
if(isset($_POST['tambah'])){
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $role_id  = $_POST['role_id'];
    $password = $_POST['password'];
    $pool_id  = empty($_POST['pool_id']) ? null : $_POST['pool_id'];

    $role_str = ($role_id == 1) ? 'ceo' : 'admin';

    try {
        // Buat user di Firebase Auth
        $userProperties = [
            'email' => $email,
            'emailVerified' => false,
            'password' => $password,
            'displayName' => $name,
        ];
        $createdUser = $auth->createUser($userProperties);
        $uid = $createdUser->uid;

        // Simpan data tambahan di Firestore
        $database->setDocument('users', $uid, [
            'name' => $name,
            'email' => $email,
            'role' => $role_str,
            'role_id' => $role_id,
            'pool_id' => $pool_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        header("location:admin.php?pesan=sukses_tambah");
    } catch (\Exception $e) {
        echo "Gagal membuat pengguna: " . $e->getMessage();
    }
}

// PROSES EDIT DATA PENGGUNA
if(isset($_POST['edit'])){
    $id       = $_POST['id']; // Firestore Document ID / Auth UID
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $role_id  = $_POST['role_id'];
    $password = $_POST['password'];
    $pool_id  = empty($_POST['pool_id']) ? null : $_POST['pool_id'];

    $role_str = ($role_id == 1) ? 'ceo' : 'admin';

    try {
        // Update user di Firebase Auth
        $userProperties = [
            'email' => $email,
            'displayName' => $name,
        ];
        if(!empty($password)){
            $userProperties['password'] = $password;
        }
        $auth->updateUser($id, $userProperties);

        // Update document di Firestore
        $database->setDocument('users', $id, [
            'name' => $name,
            'email' => $email,
            'role' => $role_str,
            'role_id' => $role_id,
            'pool_id' => $pool_id
        ]);
        
        header("location:admin.php?pesan=sukses_edit");
    } catch (\Exception $e) {
        echo "Gagal update pengguna: " . $e->getMessage();
    }
}
?>