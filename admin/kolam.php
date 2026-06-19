<?php
// DEPRECATED: Halaman ini sudah dimigrasikan ke profil_cabang.php untuk Admin
// dan ceo_manage_kolam.php untuk CEO.
session_start();
$role = $_SESSION['role'] ?? '';
if($role == 'ceo') {
    header("location:../ceo/ceo_manage_kolam.php");
} else {
    header("location:profil_cabang.php");
}
exit;
?>