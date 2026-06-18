<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// 1. Mengambil Total Atlet
$total_atlet = 0;
$q_atlet = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE 1=1");
if($q_atlet) {
    $total_atlet = mysqli_fetch_assoc($q_atlet)['total'] ?? 0;
}

// 2. Mengambil Total Kehadiran Hari Ini
$hari_ini = date('Y-m-d');
$hadir_hari_ini = 0;
$q_presensi = mysqli_query($koneksi, "SELECT COUNT(id) as hadir FROM presensi WHERE tanggal='$hari_ini' AND status='Hadir'");
if($q_presensi) {
    $hadir_hari_ini = mysqli_fetch_assoc($q_presensi)['hadir'] ?? 0;
}

// 3. Mengambil Total Rekor Performa
$total_rekor = 0;
$recent_performances = [];

$q_perf_count = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM performa");
if($q_perf_count) {
    $total_rekor = mysqli_fetch_assoc($q_perf_count)['total'] ?? 0;
}

$q_perf = mysqli_query($koneksi, "SELECT p.*, m.nama as nama_atlet FROM performa p LEFT JOIN member m ON p.member_id = m.id ORDER BY p.tanggal_rekor DESC, p.id DESC LIMIT 5");
if($q_perf) {
    while($row = mysqli_fetch_assoc($q_perf)) {
        $recent_performances[] = $row;
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-algolia-navy tracking-tight">Selamat Datang, <?= htmlspecialchars($_SESSION['name']); ?>! 👋</h1>
            <p class="text-base text-gray-500 mt-1">Ini adalah ringkasan aktivitas klub renang Swift SC hari ini.</p>
            <p class="text-sm font-semibold text-blue-600 mt-2">Role: <?= htmlspecialchars(ucfirst($_SESSION['role'])); ?> | Cabang: <?= htmlspecialchars($_SESSION['cabang'] ?: 'Semua'); ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-bold uppercase tracking-wider mb-1">Total Atlet Aktif</p>
                        <h3 class="text-4xl font-black"><?= $total_atlet; ?> <span class="text-lg font-medium text-blue-200">Orang</span></h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-6 shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-bold uppercase tracking-wider mb-1">Hadir Latihan Hari Ini</p>
                        <h3 class="text-4xl font-black"><?= $hadir_hari_ini; ?> <span class="text-lg font-medium text-green-200">Atlet</span></h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-6 shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-bold uppercase tracking-wider mb-1">Total Rekor Dicatat</p>
                        <h3 class="text-4xl font-black"><?= $total_rekor; ?> <span class="text-lg font-medium text-amber-200">Data</span></h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">5 Pencatatan Waktu Terakhir</h2>
                <a href="performa.php" class="text-sm font-semibold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-algolia">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Atlet</th>
                            <th class="px-6 py-3">Gaya & Jarak</th>
                            <th class="px-6 py-3 text-center">Waktu Tempuh</th>
                            <th class="px-6 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($recent_performances) > 0) {
                            foreach($recent_performances as $d) {
                                $nama_atlet = $d['nama_atlet'] ?? 'Unknown';
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($nama_atlet); ?></td>
                            <td class="px-6 py-4 text-blue-600 font-medium"><?= htmlspecialchars($d['gaya_renang'] ?? '-'); ?> - <?= htmlspecialchars($d['jarak'] ?? '-'); ?>m</td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700"><?= htmlspecialchars($d['waktu_formatted'] ?? '-'); ?></td>
                            <td class="px-6 py-4 text-gray-400"><?= isset($d['tanggal_rekor']) ? date('d M Y', strtotime($d['tanggal_rekor'])) : '-'; ?></td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">Belum ada data dicatat.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>