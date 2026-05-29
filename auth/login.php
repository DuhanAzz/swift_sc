<?php
session_start();
// Jika sudah login, tendang kembali ke dashboard masing-masing
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'ceo') header("Location: ../dashboard/ceo/index.php");
    elseif ($_SESSION['role'] == 'admin') header("Location: ../dashboard/admin/index.php");
    elseif ($_SESSION['role'] == 'pelatih') header("Location: ../dashboard/pelatih/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - Swift SC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-50"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-orange-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-50"></div>

    <div class="max-w-md w-full bg-slate-800/80 backdrop-blur-xl border border-slate-700 rounded-3xl shadow-2xl overflow-hidden relative z-10">
        <div class="p-8 text-center border-b border-slate-700">
            <h2 class="text-3xl font-bold text-white tracking-tight">SWIFT<span class="text-orange-500">_SC</span></h2>
            <p class="text-slate-400 text-sm mt-2">Portal Manajemen Sistem Terintegrasi</p>
        </div>

        <div class="p-8">
            <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
                <div class="mb-6 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm text-center">
                    Username atau Password salah!
                </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Username</label>
                    <input type="text" name="username" required autocomplete="off"
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-slate-500" placeholder="Masukkan username">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                    <input type="password" name="password" required 
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-slate-500" placeholder="••••••••">
                </div>
                <button type="submit" name="login"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl shadow-[0_0_20px_rgba(249,115,22,0.3)] transition-all duration-300 transform hover:-translate-y-1">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>
        <div class="p-4 bg-slate-900/50 text-center">
            <a href="../index.php" class="text-sm text-slate-400 hover:text-white transition-colors">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>