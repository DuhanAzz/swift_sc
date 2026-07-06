<?php
session_start();
if (isset($_SESSION['status']) && $_SESSION['status'] == "sudah_login") {
    header("location:admin/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift SC - Login Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-panel {
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .bg-ocean {
            background-color: #0b162c;
            background-image: 
                radial-gradient(ellipse at top right, rgba(13, 148, 136, 0.25) 0%, transparent 60%),
                radial-gradient(ellipse at bottom left, rgba(249, 115, 22, 0.15) 0%, transparent 60%);
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-ocean min-h-screen font-sans antialiased text-slate-100 flex items-center justify-center relative overflow-hidden">
    
    <!-- Background Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-teal-900/30 rounded-full mix-blend-screen filter blur-[100px] animate-pulse"></div>
        <div class="absolute -bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-orange-900/20 rounded-full mix-blend-screen filter blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <div class="w-full max-w-md p-8 m-4 rounded-3xl glass-panel shadow-2xl border border-slate-700/50 relative">
        <div class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-white border-2 border-teal-500/20 p-2.5 rounded-full shadow-[0_10px_30px_rgba(13,148,136,0.3)] z-50">
            <!-- Fallback logo logic -->
            <img src="assets/logo.png" alt="Swift SC" class="w-20 h-20 object-contain" onerror="this.onerror=null; this.outerHTML='<div class=\'w-20 h-20 flex items-center justify-center bg-gradient-to-br from-teal-500 to-orange-500 rounded-full text-white font-black text-xl italic\'>SC</div>';">
        </div>
        
        <div class="text-center mt-12 mb-8">
            <h2 class="text-3xl font-black tracking-tight text-white uppercase">Login Portal</h2>
            <p class="text-sm text-slate-400 mt-2">Sistem Manajemen <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-orange-400 font-bold">Swift SC</span></p>
        </div>

        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "gagal"){
                echo "<div class='bg-red-500/20 border border-red-500/50 text-red-100 px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium shadow-inner flex items-center justify-center gap-2'>
                        <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'></path></svg>
                        Login gagal! Email atau Password salah.
                      </div>";
            } else if($_GET['pesan'] == "logout"){
                echo "<div class='bg-green-500/20 border border-green-500/50 text-green-100 px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium shadow-inner flex items-center justify-center gap-2'>
                        <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'></path></svg>
                        Anda telah berhasil logout.
                      </div>";
            } else if($_GET['pesan'] == "belum_login"){
                echo "<div class='bg-orange-500/20 border border-orange-500/50 text-orange-100 px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium shadow-inner flex items-center justify-center gap-2'>
                        <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z' clip-rule='evenodd'></path></svg>
                        Silakan login untuk mengakses halaman admin.
                      </div>";
            } else if($_GET['pesan'] == "gagal_data_tidak_ditemukan"){
                echo "<div class='bg-red-500/20 border border-red-500/50 text-red-100 px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium shadow-inner flex items-center justify-center gap-2'>
                        <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'></path></svg>
                        Login berhasil, tetapi data role Anda tidak ditemukan di sistem.
                      </div>";
            } else if($_GET['pesan'] == "error"){
                $msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Terjadi kesalahan internal.';
                echo "<div class='bg-red-500/20 border border-red-500/50 text-red-100 px-4 py-3 rounded-lg mb-6 text-sm text-center font-medium shadow-inner flex items-center justify-center gap-2'>
                        <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z' clip-rule='evenodd'></path></svg>
                        Error: " . $msg . "
                      </div>";
            }
        }
        ?>

        <form action="proses_login.php" method="post" class="space-y-5">
            <div>
                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase tracking-wide" for="email">
                    Alamat Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input class="w-full bg-slate-900/50 text-white border border-slate-600 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition-colors placeholder-slate-500" 
                           id="email" name="email" type="email" placeholder="contoh@email.com" required>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase tracking-wide" for="password">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input class="w-full bg-slate-900/50 text-white border border-slate-600 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition-colors placeholder-slate-500" 
                           id="password" name="password" type="password" placeholder="••••••••" required>
                </div>
            </div>
            
            <div>
                <button class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-orange-500/50 transition-all transform hover:-translate-y-0.5 border border-orange-400/50" type="submit" name="login">
                    Masuk ke Sistem
                </button>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-slate-700/50 pt-4">
            <a href="index.php" class="text-sm font-medium text-slate-400 hover:text-orange-400 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</body>
</html>
