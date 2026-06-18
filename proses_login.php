<?php
// Mulai sesi
session_start();

// Panggil file koneksi database
require_once 'includes/koneksi.php';

if (isset($_POST['login'])) {
    $email = bersihkan_input($_POST['email']);
    $password = $_POST['password'];

    try {
        // Query database
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($koneksi, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $userData = mysqli_fetch_assoc($result);

            // Verifikasi password
            if (password_verify($password, $userData['password'])) {
                // Set Session
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['email'] = $userData['email'];
                $_SESSION['name'] = isset($userData['username']) ? $userData['username'] : 'User';
                $_SESSION['role'] = strtolower($userData['role']);
                $_SESSION['cabang'] = isset($userData['cabang_id']) ? $userData['cabang_id'] : '';
                $_SESSION['pool_id'] = isset($userData['cabang_id']) ? $userData['cabang_id'] : '';
                $_SESSION['status'] = "sudah_login";

                // Role-based Routing
                $role = $_SESSION['role'];
                if ($role === 'ceo') {
                    header("location: ceo/ceo_dashboard.php");
                } else if ($role === 'coach' || $role === 'pelatih') {
                    header("location: pelatih/pelatih_dashboard.php");
                } else {
                    // Default fallback to Admin
                    header("location: admin/admin_dashboard.php");
                }
                exit;
            } else {
                // Password salah
                header("location: login.php?pesan=gagal");
                exit;
            }
        } else {
            // User tidak ditemukan
            header("location: login.php?pesan=gagal");
            exit;
        }

    } catch (\Exception $e) {
        // Error lainnya
        header("location: login.php?pesan=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("location: login.php");
    exit;
}
?>