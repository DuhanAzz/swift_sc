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
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">

        <div class="mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Tim Pelatih & Performa</h1>
            <p class="text-sm text-gray-500">Lihat pelatih yang ditugaskan di cabang Anda dan pantau performa atlet yang mereka catat.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Daftar Pelatih -->
            <div class="lg:col-span-1 space-y-4">
                <h2 class="text-lg font-bold text-gray-800 mb-2">Pelatih Cabang</h2>
                <?php
                $coaches = [];
                $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih WHERE cabang = '$admin_pool_id'");
                if($q_pelatih) {
                    while($row = mysqli_fetch_assoc($q_pelatih)) {
                        $coaches[] = $row;
                    }
                }

                if(count($coaches) > 0) {
                    foreach($coaches as $c) {
                        $foto_pelatih = (!empty($c['foto']) && file_exists("../admin/" . $c['foto'])) ? "../admin/" . $c['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($c['nama_pelatih']) . "&background=0f172a&color=fff";
                ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <img src="<?= $foto_pelatih ?>" alt="Pelatih" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <h3 class="font-bold text-gray-800"><?= htmlspecialchars($c['nama']) ?></h3>
                        <p class="text-xs text-blue-600 font-semibold"><?= htmlspecialchars($c['jabatan'] ?? 'Pelatih') ?></p>
                        <p class="text-xs text-gray-400 mt-1"><i class="fa fa-phone"></i> <?= htmlspecialchars($c['sertifikasi'] ?? '') ?></p>
                    </div>
                </div>
                <?php } } else { echo "<div class='text-sm text-gray-500 italic p-4 bg-gray-50 rounded-xl border'>Belum ada pelatih yang ditugaskan di cabang ini.</div>"; } ?>
            </div>

            <!-- Kolom Kanan: Log Performa -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                        <h2 class="text-lg font-bold text-gray-800">Catatan Performa Terbaru</h2>
                    </div>
                    <div class="overflow-x-auto p-4">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-400 uppercase bg-gray-50 rounded-lg">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Atlet</th>
                                    <th class="px-4 py-3">Gaya & Jarak</th>
                                    <th class="px-4 py-3 text-center">Waktu</th>
                                    <th class="px-4 py-3 text-right">Pencatat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $performances = [];
                                $q_perf = mysqli_query($koneksi, "SELECT p.*, m.nama as nama_atlet FROM performa p LEFT JOIN member m ON p.member_id = m.id WHERE p.cabang_id = '$admin_pool_id' ORDER BY p.tanggal DESC");
                                if($q_perf) {
                                    while($row = mysqli_fetch_assoc($q_perf)) {
                                        $performances[] = $row;
                                    }
                                }

                                if(count($performances) > 0) {
                                    foreach($performances as $p) {
                                        // Cari nama atlet
                                        $nama_atlet = $p['nama_atlet'] ?? 'Unknown';
                                ?>
                                <tr class="border-b hover:bg-orange-50 transition-colors">
                                    <td class="px-4 py-3 text-gray-400"><?= isset($p['tanggal']) ? date('d M Y', strtotime($p['tanggal'])) : '-'; ?></td>
                                    <td class="px-4 py-3 font-bold text-gray-800"><?= htmlspecialchars($nama_atlet); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded"><?= htmlspecialchars($p['gaya_renang'] ?? '-'); ?></span>
                                        <span class="text-xs text-gray-500 ml-1"><?= htmlspecialchars($p['jarak'] ?? '-'); ?>m</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-700 bg-slate-50"><?= htmlspecialchars($p['waktu'] ?? '-'); ?></td>
                                    <td class="px-4 py-3 text-right text-xs text-gray-500"><?= htmlspecialchars($p['pelatih_id'] ?? 'Pelatih'); ?></td>
                                </tr>
                                <?php } } else { echo "<tr><td colspan='5' class='px-4 py-8 text-center text-gray-500 italic'>Belum ada data performa yang dicatat oleh pelatih di cabang ini.</td></tr>"; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
