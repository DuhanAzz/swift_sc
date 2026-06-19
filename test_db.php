<?php
session_start();
include 'includes/koneksi.php';
$pool_id = $_SESSION['pool_id'] ?? "NULL";
var_dump($pool_id);
?>
