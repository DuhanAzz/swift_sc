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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        algolia: { blue: '#5468FF', darkblue: '#3A4DC7', navy: '#21243D' },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#F5F5FA] min-h-screen flex items-center justify-center antialiased p-4">
    
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="index.php" class="inline-flex items-center gap-2 mb-4">
                <img src="assets/logo.png" alt="Swift SC" class="h-10 w-10 object-contain rounded-lg" onerror="this.onerror=null; this.outerHTML='<div class=\'w-10 h-10 rounded-lg bg-[#5468FF] flex items-center justify-center\'><svg class=\'w-5 h-5 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2.5\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg></div>';">
                <span class="font-bold text-[15px] text-[#21243D]">Swift<span class="text-[#5468FF]">SC</span></span>
            </a>
            <h1 class="text-xl font-bold text-[#21243D]">Log in to Swift SC</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem Manajemen Swimming Club</p>
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
        <div class="bg-white rounded-xl border border-[#E8E8EF] p-6 shadow-sm">
            <form action="proses_login.php" method="post" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#6B6F8D] uppercase tracking-wider mb-1.5" for="email">Email</label>
                    <input class="w-full bg-white border border-[#E8E8EF] rounded-lg py-2.5 px-3 text-sm text-[#21243D] focus:outline-none focus:border-[#5468FF] focus:ring-2 focus:ring-[#5468FF]/10 transition placeholder-gray-400" 
                           id="email" name="email" type="email" placeholder="you@example.com" required>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-[#6B6F8D] uppercase tracking-wider mb-1.5" for="password">Password</label>
                    <input class="w-full bg-white border border-[#E8E8EF] rounded-lg py-2.5 px-3 text-sm text-[#21243D] focus:outline-none focus:border-[#5468FF] focus:ring-2 focus:ring-[#5468FF]/10 transition placeholder-gray-400" 
                           id="password" name="password" type="password" placeholder="••••••••" required>
                </div>
                
                <button class="w-full bg-[#5468FF] hover:bg-[#3A4DC7] text-white font-semibold py-2.5 px-4 rounded-lg text-sm transition-colors" type="submit" name="login">
                    Log In
                </button>
            </form>
        </div>

        <div class="mt-5 text-center">
            <a href="index.php" class="text-sm text-gray-500 hover:text-[#5468FF] transition-colors">
                &larr; Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</body>
</html>
