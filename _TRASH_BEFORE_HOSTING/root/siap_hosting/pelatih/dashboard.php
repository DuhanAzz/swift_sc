<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$hari_ini = date('Y-m-d');
$hadir_hari_ini = 0;
try {
    $presensiSnapshot = $database->getDocuments('presensi');
    foreach ($presensiSnapshot as $doc) {
        if (($doc['tanggal'] ?? '') === $hari_ini && ($doc['status'] ?? '') === 'Hadir') {
            $hadir_hari_ini++;
        }
    }
} catch (\Exception $e) { }

$recent_performances = [];
try {
    $performaSnapshot = $database->getDocuments('performances');
    $recent_performances = $performaSnapshot;
    usort($recent_performances, function($a, $b) { return strcmp($b['id'] ?? '', $a['id'] ?? ''); });
    $recent_performances = array_slice($recent_performances, 0, 5);
} catch (\Exception $e) { }
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Selamat Datang, Pelatih <?= htmlspecialchars($_SESSION['name']); ?>! 👋</h1>
            <p class="text-base text-gray-500 mt-1">Jangan lupa cek jadwal hari ini dan isi kehadiran atlet Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-bold uppercase tracking-wider mb-1">Hadir Latihan Hari Ini</p>
                        <h3 class="text-4xl font-black"><?= $hadir_hari_ini; ?> <span class="text-lg font-medium text-blue-200">Atlet</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">5 Pencatatan Waktu Terakhir</h2>
                <a href="performa.php" class="text-sm font-semibold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Atlet</th>
                            <th class="px-6 py-3">Gaya & Jarak</th>
                            <th class="px-6 py-3 text-center">Waktu Tempuh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($recent_performances) > 0) {
                            foreach($recent_performances as $d) {
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
                        </tr>
                        <?php } } else { echo '<tr><td colspan="3" class="px-6 py-6 text-center text-gray-400">Belum ada data dicatat.</td></tr>'; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
