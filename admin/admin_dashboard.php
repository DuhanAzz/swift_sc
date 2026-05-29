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
try {
    $atletSnapshot = $database->getDocuments('atlet');
    foreach ($atletSnapshot as $doc) {
        if (($doc['pool_id'] ?? '') == $admin_pool_id) {
            $total_atlet++;
        }
    }
} catch (\Exception $e) { }

// 2. Mengambil Kehadiran Hari Ini untuk Cabang Ini
$hari_ini = date('Y-m-d');
$hadir_hari_ini = 0;
try {
    $presensiSnapshot = $database->getDocuments('presensi');
    foreach ($presensiSnapshot as $doc) {
        if (($doc['tanggal'] ?? '') === $hari_ini && ($doc['status'] ?? '') === 'Hadir' && ($doc['pool_id'] ?? '') == $admin_pool_id) {
            $hadir_hari_ini++;
        }
    }
} catch (\Exception $e) { }

// 3. Mengambil Rekor Performa Terbaru untuk Cabang Ini
$recent_performances = [];
try {
    $performaSnapshot = $database->getDocuments('performances');
    foreach ($performaSnapshot as $doc) {
        if (($doc['pool_id'] ?? '') == $admin_pool_id) {
            $recent_performances[] = $doc;
        }
    }
    
    // Urutkan DESC berdasarkan tanggal
    usort($recent_performances, function($a, $b) {
        return strcmp($b['record_date'] ?? '', $a['record_date'] ?? ''); 
    });
    
    $total_rekor = count($recent_performances);
    $recent_performances = array_slice($recent_performances, 0, 5);
} catch (\Exception $e) { }
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Dashboard Admin Cabang</h1>
            <p class="text-base text-gray-500 mt-1">Ringkasan aktivitas Swift SC untuk cabang: <span class="font-bold text-blue-600"><?= htmlspecialchars($admin_cabang); ?></span>.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-bold uppercase tracking-wider mb-1">Total Atlet Cabang</p>
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
                        <h3 class="text-4xl font-black"><?= isset($total_rekor) ? $total_rekor : 0; ?> <span class="text-lg font-medium text-amber-200">Data</span></h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">5 Pencatatan Waktu Terakhir (Cabang Ini)</h2>
                <a href="admin_pelatih.php" class="text-sm font-semibold text-blue-600 hover:underline">Lihat Semua Performa &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
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
                                // Cari nama atlet
                                $nama_atlet = 'Unknown';
                                if(isset($d['member_id'])) {
                                    try {
                                        $atletDoc = $database->getDocument('atlet', $d['member_id']);
                                        if($atletDoc) $nama_atlet = $atletDoc['nama'] ?? 'Unknown';
                                    } catch (\Exception $e) {}
                                }
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($nama_atlet); ?></td>
                            <td class="px-6 py-4 text-blue-600 font-medium"><?= htmlspecialchars($d['swim_style'] ?? '-'); ?> - <?= htmlspecialchars($d['distance'] ?? '-'); ?>m</td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700"><?= htmlspecialchars($d['time_formatted'] ?? '-'); ?></td>
                            <td class="px-6 py-4 text-gray-400"><?= isset($d['record_date']) ? date('d M Y', strtotime($d['record_date'])) : '-'; ?></td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">Belum ada data dicatat di cabang ini.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
