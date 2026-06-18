<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$admin_pool_id = $_SESSION['pool_id'] ?? '';
$admin_cabang = $_SESSION['cabang'] ?? 'Semua Cabang';

// 1. Mengambil Total Atlet untuk Cabang Ini
$total_atlet = 0;
$q_atlet = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE  cabang_id='$admin_pool_id'");
if($q_atlet) {
    $total_atlet = mysqli_fetch_assoc($q_atlet)['total'] ?? 0;
}

// 2. Mengambil Kehadiran Hari Ini untuk Cabang Ini
$hari_ini = date('Y-m-d');
$hadir_hari_ini = 0;

$q_presensi = mysqli_query($koneksi, "SELECT COUNT(p.id) as hadir FROM presensi p JOIN member m ON p.member_id = m.id WHERE p.tanggal='$hari_ini' AND p.status='Hadir' AND m.cabang_id='$admin_pool_id'");
if($q_presensi) {
    $hadir_hari_ini = mysqli_fetch_assoc($q_presensi)['hadir'] ?? 0;
}

// 3. Mengambil Rekor Performa Terbaru untuk Cabang Ini
$recent_performances = [];
$total_rekor = 0;
$q_perf_count = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM performa WHERE cabang_id='$admin_pool_id'");
if($q_perf_count) $total_rekor = mysqli_fetch_assoc($q_perf_count)['total'] ?? 0;

$q_perf = mysqli_query($koneksi, "SELECT p.*, m.nama as nama_atlet FROM performa p LEFT JOIN member m ON p.member_id = m.id WHERE p.cabang_id='$admin_pool_id' ORDER BY p.tanggal_rekor DESC LIMIT 5");
if($q_perf) {
    while($row = mysqli_fetch_assoc($q_perf)) {
        $recent_performances[] = $row;
    }
}
?>
<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    
    <!-- Top Bar (Desktop) -->
    <div class="topbar hidden lg:flex items-center justify-between h-14 px-6 sticky top-0 z-30">
        <div>
            <span class="text-sm font-medium text-algolia-navy">Dashboard</span>
            <span class="text-sm text-gray-400 mx-2">/</span>
            <span class="text-sm text-gray-400">Admin Cabang</span>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" class="search-box w-64 pl-9" placeholder="Search or ask a question" readonly>
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div class="w-8 h-8 rounded-full bg-algolia-blue flex items-center justify-center">
                <span class="text-white text-xs font-bold"><?= strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1)) ?></span>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="p-4 lg:p-8 page-content">
        
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-algolia-navy">Selamat Datang!</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan aktivitas Swift SC untuk cabang <span class="font-semibold text-algolia-blue"><?= htmlspecialchars($admin_cabang); ?></span></p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            
            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label">Total Atlet</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-algolia-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>
                <div class="stat-number"><?= $total_atlet; ?></div>
                <p class="text-xs text-gray-400 mt-1">Atlet terdaftar di cabang ini</p>
            </div>

            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label">Hadir Hari Ini</span>
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="stat-number"><?= $hadir_hari_ini; ?></div>
                <p class="text-xs text-gray-400 mt-1">Atlet hadir latihan hari ini</p>
            </div>

            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label">Total Rekor</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="stat-number"><?= isset($total_rekor) ? $total_rekor : 0; ?></div>
                <p class="text-xs text-gray-400 mt-1">Rekor performa dicatat</p>
            </div>
        </div>

        <!-- Recent Performance Table -->
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-panel-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h2 class="text-sm font-semibold text-algolia-navy">Pencatatan Waktu Terakhir</h2>
                <a href="performa.php" class="text-xs font-medium text-algolia-blue hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-algolia">
                    <thead>
                        <tr>
                            <th>Atlet</th>
                            <th>Gaya & Jarak</th>
                            <th class="text-center">Waktu Tempuh</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($recent_performances) > 0) {
                            foreach($recent_performances as $d) {
                                $nama_atlet = $d['nama_atlet'] ?? 'Unknown';
                        ?>
                        <tr>
                            <td class="font-semibold text-algolia-navy"><?= htmlspecialchars($nama_atlet); ?></td>
                            <td><span class="text-algolia-blue font-medium"><?= htmlspecialchars($d['gaya_renang'] ?? '-'); ?></span> — <?= htmlspecialchars($d['jarak'] ?? '-'); ?>m</td>
                            <td class="text-center font-mono font-bold text-algolia-navy"><?= htmlspecialchars($d['waktu_formatted'] ?? '-'); ?></td>
                            <td class="text-gray-400"><?= isset($d['tanggal_rekor']) ? date('d M Y', strtotime($d['tanggal_rekor'])) : '-'; ?></td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center py-10 text-gray-400">Belum ada data dicatat di cabang ini.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>


