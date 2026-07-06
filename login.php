<?php
session_start();
if (isset($_SESSION['status']) && $_SESSION['status'] == "sudah_login") {
    $role = $_SESSION['role'] ?? '';
    if ($role == 'ceo') header("location:ceo/ceo_dashboard.php");
    elseif ($role == 'admin') header("location:admin/admin_dashboard.php");
    elseif ($role == 'pelatih' || $role == 'coach') header("location:pelatih/pelatih_dashboard.php");
    else header("location:admin/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift SC — Login</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center antialiased p-4">
    
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8 flex flex-col items-center">
            <a href="index.php" class="inline-flex items-center gap-3 mb-4">
                <div class="bg-white p-3 rounded-full shadow-md w-24 h-24 flex items-center justify-center">
                    <img src="assets/logo.png" alt="Swift SC" class="h-16 w-16 object-contain" onerror="this.onerror=null; this.outerHTML='<div class=\'w-16 h-16 rounded-full bg-cyan-600 flex items-center justify-center\'><svg class=\'w-8 h-8 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2.5\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg></div>';">
                </div>
            </a>
            <h1 class="text-xl font-bold text-slate-900">Log in to Swift SC</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem Manajemen Swimming Club</p>
        </div>

        <!-- Alert messages -->
        <?php 
        if(isset($_GET['pesan'])){
            $alertClass = '';
            $alertMsg = '';
            if($_GET['pesan'] == "gagal"){
                $alertClass = 'bg-red-50 border-red-200 text-red-700';
                $alertMsg = 'Login gagal! Email atau Password salah.';
            } else if($_GET['pesan'] == "logout"){
                $alertClass = 'bg-green-50 border-green-200 text-green-700';
                $alertMsg = 'Anda telah berhasil logout.';
            } else if($_GET['pesan'] == "belum_login"){
                $alertClass = 'bg-amber-50 border-amber-200 text-amber-700';
                $alertMsg = 'Silakan login untuk mengakses halaman.';
            } else if($_GET['pesan'] == "gagal_data_tidak_ditemukan"){
                $alertClass = 'bg-red-50 border-red-200 text-red-700';
                $alertMsg = 'Data role Anda tidak ditemukan di sistem.';
            } else if($_GET['pesan'] == "error"){
                $alertClass = 'bg-red-50 border-red-200 text-red-700';
                $alertMsg = 'Error: ' . htmlspecialchars($_GET['msg'] ?? 'Terjadi kesalahan.');
            }
            if($alertMsg) {
                echo "<div class='px-4 py-3 rounded-lg border text-sm mb-6 $alertClass'>$alertMsg</div>";
            }
        }
        ?>

        <!-- Login Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <form action="actions/proses_login.php" method="post" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="email">Email</label>
                    <input class="w-full bg-white border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-900 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition placeholder-slate-400" 
                           id="email" name="email" type="email" placeholder="you@example.com" required>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="password">Password</label>
                    <input class="w-full bg-white border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-900 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition placeholder-slate-400" 
                           id="password" name="password" type="password" placeholder="••••••••" required>
                </div>
                
                <button class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2.5 px-4 rounded-lg text-sm transition-colors shadow-lg shadow-cyan-600/30" type="submit" name="login">
                    Log In
                </button>
            </form>
        </div>

        <div class="mt-5 text-center">
            <a href="index.php" class="text-sm text-slate-500 hover:text-cyan-600 transition-colors">
                &larr; Kembali ke Halaman Utama
            </a>
        </div>
    </div>
    <?php include_once __DIR__ . '/includes/swal_helper.php'; ?>
</body>
</html>
