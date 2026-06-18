<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || ($_SESSION['role'] != 'coach' && $_SESSION['role'] != 'pelatih')) { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$coach_cabang_id = $_SESSION['cabang'] ?? '';
$coach_id = $_SESSION['user_id'] ?? 0;
$coach_name = $_SESSION['name'] ?? 'Pelatih';

// 1. Total Atlet di Cabang Pelatih
$total_atlet = 0;
$q_atlet = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE 1=1 AND cabang_id='$coach_cabang_id'");
if($q_atlet) {
    $row = mysqli_fetch_assoc($q_atlet);
    $total_atlet = $row['total'];
}

// 2. Kehadiran Hari Ini
$hari_ini = date('Y-m-d');
$hadir_hari_ini = 0;
$q_hadir = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM absensi WHERE tanggal='$hari_ini' AND status='Hadir' AND cabang_id='$coach_cabang_id'");
if($q_hadir) {
    $row = mysqli_fetch_assoc($q_hadir);
    $hadir_hari_ini = $row['total'];
}

// 3. Pencatatan Performa Terbaru (Oleh Pelatih Ini)
$recent_performances = [];
$q_perf = mysqli_query($koneksi, "SELECT p.*, m.nama as nama_atlet FROM performa p LEFT JOIN member m ON p.member_id = m.id WHERE p.cabang_id='$coach_cabang_id' AND p.recorded_by='$coach_id' ORDER BY p.tanggal_rekor DESC, p.id DESC LIMIT 3");
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
            <span class="text-sm text-gray-400">Pelatih</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="badge badge-green">Pelatih</span>
            <div class="w-8 h-8 rounded-full bg-algolia-blue flex items-center justify-center">
                <span class="text-white text-xs font-bold"><?= strtoupper(substr($coach_name, 0, 1)) ?></span>
            </div>
        </div>
    </div>

    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-algolia-navy">Halo, <?= htmlspecialchars($coach_name); ?>!</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan aktivitas latihan cabang Anda</p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label">Atlet Cabang</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-algolia-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>
                <div class="stat-number"><?= $total_atlet ?></div>
                <p class="text-xs text-gray-400 mt-1">Atlet terdaftar</p>
            </div>

            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label">Hadir Hari Ini</span>
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="stat-number"><?= $hadir_hari_ini ?></div>
                <p class="text-xs text-gray-400 mt-1">Atlet hadir latihan</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <a href="pelatih_absensi.php" class="card p-5 flex items-center gap-3 hover:border-green-300 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 group-hover:bg-green-100 transition-colors">
                    <svg class="w-4.5 h-4.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="text-sm font-semibold text-algolia-navy block">Input Presensi</span>
                    <span class="text-[11px] text-gray-400">Catat kehadiran atlet</span>
                </div>
            </a>
            <a href="pelatih_performa.php" class="card p-5 flex items-center gap-3 hover:border-blue-300 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition-colors">
                    <svg class="w-4.5 h-4.5 text-algolia-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div>
                    <span class="text-sm font-semibold text-algolia-navy block">Catat Performa</span>
                    <span class="text-[11px] text-gray-400">Input waktu renang</span>
                </div>
            </a>
            <a href="pelatih_program.php" class="card p-5 flex items-center gap-3 hover:border-purple-300 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0 group-hover:bg-purple-100 transition-colors">
                    <svg class="w-4.5 h-4.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <span class="text-sm font-semibold text-algolia-navy block">Jurnal Latihan</span>
                    <span class="text-[11px] text-gray-400">Program & catatan</span>
                </div>
            </a>
        </div>

        <!-- Recent Performance -->
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-panel-border flex justify-between items-center">
                <h2 class="text-sm font-semibold text-algolia-navy">Catatan Waktu Terakhir Anda</h2>
                <a href="pelatih_performa.php" class="text-xs font-medium text-algolia-blue hover:underline">Semua &rarr;</a>
            </div>
            
            <div class="divide-y divide-gray-50">
                <?php
                if(count($recent_performances) > 0) {
                    foreach($recent_performances as $d) {
                        $nama_atlet = $d['nama_atlet'] ?? 'Unknown';
                ?>
                <div class="px-5 py-3.5 flex justify-between items-center hover:bg-gray-50/50 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-algolia-navy"><?= htmlspecialchars($nama_atlet); ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($d['gaya_renang'] ?? '-'); ?> — <?= htmlspecialchars($d['jarak'] ?? '-'); ?>m</p>
                    </div>
                    <div class="text-right">
                        <span class="font-mono text-sm font-bold text-algolia-navy"><?= htmlspecialchars($d['time_formatted'] ?? '-'); ?></span>
                        <p class="text-[10px] text-gray-400"><?= isset($d['tanggal_rekor']) ? date('d M Y', strtotime($d['tanggal_rekor'])) : '-'; ?></p>
                    </div>
                </div>
                <?php } } else { echo '<div class="px-5 py-10 text-center text-sm text-gray-400">Belum ada rekor yang Anda catat.</div>'; } ?>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>

