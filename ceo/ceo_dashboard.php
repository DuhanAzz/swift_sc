<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// 1. Total Active Members (Semua Atlet)
$total_atlet = 0;
$q1 = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member");
if($q1 && $r = mysqli_fetch_assoc($q1)) $total_atlet = $r['total'];

// 2. Total Coaches
$total_pelatih = 0;
$q2 = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM pelatih");
if($q2 && $r = mysqli_fetch_assoc($q2)) $total_pelatih = $r['total'];

// 3. Total Cash (Saldo Global)
$total_kas = 0;
$q3 = mysqli_query($koneksi, "SELECT SUM(CASE WHEN type='Pemasukan' THEN amount ELSE -amount END) as total_saldo FROM cash_flows");
if($q3 && $r = mysqli_fetch_assoc($q3)) $total_kas = floatval($r['total_saldo']);

?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-8 flex justify-between items-center border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-algolia-navy tracking-tight">Super Admin Dashboard</h1>
                <p class="text-base text-gray-500 mt-1">Ringkasan operasional global seluruh cabang Swift SC.</p>
            </div>
            <div class="text-right">
                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded border border-indigo-400">CEO Access</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-6 shadow-lg text-white relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-indigo-100 text-sm font-bold uppercase tracking-wider mb-1">Total Atlet Global</p>
                    <h3 class="text-4xl font-black"><?= $total_atlet; ?> <span class="text-lg font-medium text-indigo-200">Orang</span></h3>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 shadow-lg text-white relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-emerald-100 text-sm font-bold uppercase tracking-wider mb-1">Total Saldo Keuangan</p>
                    <h3 class="text-3xl font-black truncate">Rp <?= number_format($total_kas, 0, ',', '.'); ?></h3>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-6 shadow-lg text-white relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-amber-100 text-sm font-bold uppercase tracking-wider mb-1">Total Pelatih</p>
                    <h3 class="text-4xl font-black"><?= $total_pelatih; ?> <span class="text-lg font-medium text-amber-200">Orang</span></h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center text-center">
                <div class="p-4 bg-indigo-50 rounded-full mb-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">CMS Konten Web</h3>
                <p class="text-sm text-gray-500 mb-4">Ubah teks Banner Utama, Profil Klub, dan Jadwal di halaman publik tanpa menyentuh kode.</p>
                <a href="ceo_cms_web.php" class="bg-indigo-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-indigo-700 transition">Kelola Konten</a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center text-center">
                <div class="p-4 bg-emerald-50 rounded-full mb-4">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Laporan Global</h3>
                <p class="text-sm text-gray-500 mb-4">Akses Laporan Arus Kas lintas cabang dan Leaderboard Prestasi Atlet global.</p>
                <a href="ceo_laporan_global.php" class="bg-emerald-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-emerald-700 transition">Lihat Laporan</a>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
