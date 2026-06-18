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
    
    <!-- Top Bar (Desktop) -->
    <div class="topbar hidden lg:flex items-center justify-between h-14 px-6 sticky top-0 z-30">
        <div>
            <span class="text-sm font-medium text-algolia-navy">Dashboard</span>
            <span class="text-sm text-gray-400 mx-2">/</span>
            <span class="text-sm text-gray-400">CEO</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="badge badge-blue">CEO Access</span>
            <div class="w-8 h-8 rounded-full bg-algolia-blue flex items-center justify-center">
                <span class="text-white text-xs font-bold"><?= strtoupper(substr($_SESSION['name'] ?? 'C', 0, 1)) ?></span>
            </div>
        </div>
    </div>

    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-algolia-navy">Selamat Datang!</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan operasional global seluruh cabang Swift SC</p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label">Total Atlet Global</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-algolia-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>
                <div class="stat-number"><?= $total_atlet; ?></div>
                <p class="text-xs text-gray-400 mt-1">Atlet terdaftar di seluruh cabang</p>
            </div>

            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label">Saldo Keuangan</span>
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="stat-number text-2xl">Rp <?= number_format($total_kas, 0, ',', '.'); ?></div>
                <p class="text-xs text-gray-400 mt-1">Total saldo arus kas global</p>
            </div>

            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label">Total Pelatih</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>
                <div class="stat-number"><?= $total_pelatih; ?></div>
                <p class="text-xs text-gray-400 mt-1">Pelatih aktif di seluruh cabang</p>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-algolia-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-algolia-navy mb-1">CMS Konten Web</h3>
                        <p class="text-xs text-gray-500 mb-3">Ubah teks Banner, Profil Klub, dan Jadwal di halaman publik.</p>
                        <a href="ceo_cms_web.php" class="btn-primary text-xs">Kelola Konten</a>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-algolia-navy mb-1">Laporan Global</h3>
                        <p class="text-xs text-gray-500 mb-3">Akses Laporan Arus Kas dan Leaderboard Prestasi Atlet.</p>
                        <a href="ceo_laporan_global.php" class="btn-outline text-xs">Lihat Laporan</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>

