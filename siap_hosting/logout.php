<?php 
// 1. Mulai session untuk bisa mendeteksi session yang sedang aktif
session_start();

// 2. Hapus semua data session (seperti email, nama, dan role)
session_unset();

// 3. Hancurkan session tersebut sepenuhnya dari server
session_destroy();

// 4. Arahkan pengguna kembali ke halaman depan atau halaman login 
// (Di sini kita arahkan ke halaman utama/publik saja agar lebih elegan)
header("location:index.php");
exit;
?>