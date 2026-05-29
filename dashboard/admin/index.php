<?php
session_start();
require_once '../../config/database.php';

// Proteksi: Hanya 'admin' yang boleh masuk halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Mengambil total pendaftar baru (Status: Pending)
$q_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM members WHERE status_akun = 'Pending'");
$pending = mysqli_fetch_assoc($q_pending)['total'];

// Mengambil total member aktif
$q_aktif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM members WHERE status_akun = 'Aktif'");
$aktif = mysqli_fetch_assoc($q_aktif)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Swift SC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include '../../includes/sidebar.php'; ?>

    <div class="ml-64 p-8">
        
        <div class="mb-8 border-b border-slate-200 pb-4">
            <h1 class="text-3xl font-bold text-slate-900">Ikhtisar Cabang</h1>
            <p class="text-slate-500 mt-1">Pantau aktivitas pendaftaran dan member cabang Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-orange-500">
                <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-2">Pendaftar Baru</h3>
                <div class="flex items-end gap-3">
                    <p class="text-4xl font-black text-slate-800"><?= $pending ?></p>
                    <p class="text-sm text-orange-500 font-semibold mb-1">Menunggu Persetujuan</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-blue-500">
                <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-2">Total Member Aktif</h3>
                <div class="flex items-end gap-3">
                    <p class="text-4xl font-black text-slate-800"><?= $aktif ?></p>
                    <p class="text-sm text-blue-500 font-semibold mb-1">Siswa</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Tugas Anda Hari Ini</h2>
            
            <?php if($pending > 0): ?>
                <div class="bg-orange-50 border border-orange-200 p-6 rounded-xl flex items-center justify-between">
                    <div>
                        <h4 class="text-orange-800 font-bold text-lg">Ada <?= $pending ?> Calon Member Baru!</h4>
                        <p class="text-orange-700 text-sm mt-1">Mereka mendaftar melalui halaman website. Segera tinjau data mereka dan setujui agar mereka bisa mulai berlatih.</p>
                    </div>
                    <a href="kelola_member.php" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-bold shadow-lg shadow-orange-500/30 transition transform hover:-translate-y-1 whitespace-nowrap ml-6">
                        Tinjau Sekarang &rarr;
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-slate-50 border border-slate-200 p-6 rounded-xl text-center">
                    <p class="text-slate-500">Tidak ada pendaftar baru yang menunggu persetujuan. Kerja bagus!</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>