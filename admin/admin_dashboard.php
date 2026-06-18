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

<div class="lg:ml-[270px] pt-20 lg:pt-6 p-4 lg:p-8 page-content min-h-screen">
    
        <div class="mb-8 animate-fade-in">
            <h1 class="text-2xl lg:text-3xl font-sora font-bold text-white tracking-tight">Dashboard Admin Cabang</h1>
            <p class="text-sm text-slate-400 mt-1">Ringkasan aktivitas Swift SC untuk cabang: <span class="font-semibold text-electric-400"><?= htmlspecialchars($admin_cabang); ?></span></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-8">
            <!-- Stat Card 1: Total Atlet -->
            <div class="stat-card glass-card-solid rounded-2xl p-5 lg:p-6 animate-slide-up">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mb-2">Total Atlet Cabang</p>
                        <h3 class="text-3xl lg:text-4xl font-sora font-bold text-white"><?= $total_atlet; ?> <span class="text-base font-normal text-slate-500">Orang</span></h3>
                    </div>
                    <div class="p-3 rounded-xl bg-electric-500/10 border border-electric-500/20">
                        <svg class="w-7 h-7 text-electric-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Stat Card 2: Hadir Hari Ini -->
            <div class="stat-card glass-card-solid rounded-2xl p-5 lg:p-6 animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mb-2">Hadir Latihan Hari Ini</p>
                        <h3 class="text-3xl lg:text-4xl font-sora font-bold text-white"><?= $hadir_hari_ini; ?> <span class="text-base font-normal text-slate-500">Atlet</span></h3>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Stat Card 3: Total Rekor -->
            <div class="stat-card glass-card-solid rounded-2xl p-5 lg:p-6 animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mb-2">Total Rekor Dicatat</p>
                        <h3 class="text-3xl lg:text-4xl font-sora font-bold text-white"><?= isset($total_rekor) ? $total_rekor : 0; ?> <span class="text-base font-normal text-slate-500">Data</span></h3>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20">
                        <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Performance Table -->
        <div class="glass-card-solid rounded-2xl overflow-hidden animate-slide-up" style="animation-delay: 0.3s">
            <div class="p-5 border-b border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h2 class="text-base font-sora font-semibold text-white">5 Pencatatan Waktu Terakhir</h2>
                <a href="performa.php" class="text-xs font-semibold text-electric-400 hover:text-electric-300 transition-colors">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left table-dark">
                    <thead>
                        <tr class="text-[11px] text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-3 font-semibold">Atlet</th>
                            <th class="px-6 py-3 font-semibold">Gaya & Jarak</th>
                            <th class="px-6 py-3 font-semibold text-center">Waktu Tempuh</th>
                            <th class="px-6 py-3 font-semibold">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($recent_performances) > 0) {
                            foreach($recent_performances as $d) {
                                $nama_atlet = $d['nama_atlet'] ?? 'Unknown';
                        ?>
                        <tr>
                            <td class="px-6 py-4 font-semibold text-slate-200"><?= htmlspecialchars($nama_atlet); ?></td>
                            <td class="px-6 py-4 text-electric-400 font-medium"><?= htmlspecialchars($d['gaya_renang'] ?? '-'); ?> — <?= htmlspecialchars($d['jarak'] ?? '-'); ?>m</td>
                            <td class="px-6 py-4 text-center font-bold font-mono text-white"><?= htmlspecialchars($d['waktu_formatted'] ?? '-'); ?></td>
                            <td class="px-6 py-4 text-slate-500"><?= isset($d['tanggal_rekor']) ? date('d M Y', strtotime($d['tanggal_rekor'])) : '-'; ?></td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada data dicatat di cabang ini.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

</div>

<?php include '../includes/footer.php'; ?>

