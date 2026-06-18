<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || ($_SESSION['role'] != 'coach' && $_SESSION['role'] != 'pelatih')) { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$coach_cabang_id = $_SESSION['cabang_id'] ?? '';
$coach_id = $_SESSION['id'] ?? 0;
$coach_name = $_SESSION['name'] ?? 'Pelatih';

// 1. Total Atlet di Cabang Pelatih
$total_atlet = 0;
$q_atlet = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE role='Atlet' AND cabang_id='$coach_cabang_id'");
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
$q_perf = mysqli_query($koneksi, "SELECT p.*, m.nama as nama_atlet FROM performa p LEFT JOIN member m ON p.member_id = m.id WHERE p.cabang_id='$coach_cabang_id' AND p.pelatih_id='$coach_id' ORDER BY p.tanggal_rekor DESC, p.id DESC LIMIT 3");
if($q_perf) {
    while($row = mysqli_fetch_assoc($q_perf)) {
        $recent_performances[] = $row;
    }
}
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14 max-w-2xl mx-auto"> <!-- Optimized for mobile width -->
        
        <div class="mb-6 bg-slate-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 bg-orange-500 w-32 h-32 rounded-full opacity-20 blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 bg-teal-500 w-32 h-32 rounded-full opacity-20 blur-2xl"></div>
            
            <p class="text-sm text-slate-300 font-medium mb-1">Selamat Datang, Pelatih</p>
            <h1 class="text-3xl font-black tracking-tight mb-4"><?= htmlspecialchars($coach_name); ?> 👋</h1>
            
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="bg-white/10 backdrop-blur border border-white/20 p-4 rounded-2xl">
                    <p class="text-xs text-slate-300 uppercase font-bold mb-1">Atlet Cabang</p>
                    <p class="text-3xl font-black"><?= $total_atlet ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 p-4 rounded-2xl">
                    <p class="text-xs text-slate-300 uppercase font-bold mb-1">Hadir Hari Ini</p>
                    <p class="text-3xl font-black text-orange-400"><?= $hadir_hari_ini ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Actions (Mobile Friendly Grid) -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <a href="pelatih_absensi.php" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="font-bold text-gray-800 text-sm">Input Presensi</span>
            </a>
            <a href="pelatih_performa.php" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="font-bold text-gray-800 text-sm">Catat Performa</span>
            </a>
            <a href="pelatih_program.php" class="col-span-2 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-gray-800 block">Jurnal & Program Latihan</span>
                    <span class="text-xs text-gray-500">Catat menu latihan hari ini</span>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Catatan Waktu Terakhir Anda</h2>
                <a href="pelatih_performa.php" class="text-xs font-bold text-blue-600">Semua &rarr;</a>
            </div>
            
            <div class="divide-y divide-gray-100">
                <?php
                if(count($recent_performances) > 0) {
                    foreach($recent_performances as $d) {
                        $nama_atlet = $d['nama_atlet'] ?? 'Unknown';
                ?>
                <div class="p-4 flex justify-between items-center hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($nama_atlet); ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($d['gaya_renang'] ?? '-'); ?> - <?= htmlspecialchars($d['jarak'] ?? '-'); ?>m</p>
                    </div>
                    <div class="text-right">
                        <span class="bg-slate-800 text-white font-mono text-sm font-bold px-3 py-1 rounded-lg inline-block mb-1 shadow-sm"><?= htmlspecialchars($d['time_formatted'] ?? '-'); ?></span>
                        <p class="text-[10px] text-gray-400"><?= isset($d['tanggal_rekor']) ? date('d M Y', strtotime($d['tanggal_rekor'])) : '-'; ?></p>
                    </div>
                </div>
                <?php } } else { echo '<div class="p-8 text-center text-sm text-gray-500 italic">Belum ada rekor yang Anda catat.</div>'; } ?>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
