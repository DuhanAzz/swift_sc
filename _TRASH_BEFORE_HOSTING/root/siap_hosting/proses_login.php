<?php
// Mulai sesi
session_start();

// Panggil file koneksi Firebase
require_once 'includes/koneksi.php';

use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // 1. Authenticate with Firebase Auth
        $signInResult = $auth->signInWithEmailAndPassword($email, $password);
        
        // 2. Fetch User Data from Firestore using UID
        $uid = $signInResult->firebaseUserId();
        $userData = $database->getDocument('users', $uid);

        if ($userData) {
            // 3. Set Session
            $_SESSION['email'] = $userData['email'];
            $_SESSION['name'] = isset($userData['name']) ? $userData['name'] : 'User';
            $_SESSION['role'] = $userData['role']; 
            $_SESSION['cabang'] = isset($userData['cabang']) ? $userData['cabang'] : ''; 
            $_SESSION['pool_id'] = isset($userData['pool_id']) ? $userData['pool_id'] : ''; 
            $_SESSION['status'] = "sudah_login";

            // 4. Role-based Routing
            $role = strtolower($userData['role']);
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
            // Auth success tapi data tidak ada di Firestore 'users' collection
            header("location: login.php?pesan=gagal_data_tidak_ditemukan");
            exit;
        }

    } catch (InvalidPassword | UserNotFound $e) {
        // Jika login gagal (password salah atau user tidak ada)
        header("location: login.php?pesan=gagal");
        exit;
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