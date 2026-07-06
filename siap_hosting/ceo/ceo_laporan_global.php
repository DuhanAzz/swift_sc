<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// --- Ambil Laporan Cash Flow Lintas Cabang ---
$cash_flows = [];
$total_masuk = 0;
$total_keluar = 0;

try {
    $docs = $database->getDocuments('cash_flows');
    foreach ($docs as $d) {
        // Ambil nama kolam jika ada
        $nama_kolam = 'Pusat';
        if (!empty($d['pool_id'])) {
            try {
                $pool = $database->getDocument('pools', $d['pool_id']);
                if ($pool) $nama_kolam = $pool['name'];
            } catch (\Exception $e) {}
        }
        $d['nama_kolam'] = $nama_kolam;
        $cash_flows[] = $d;

        $nominal = floatval($d['amount'] ?? 0);
        if (($d['type'] ?? '') == 'Pemasukan') {
            $total_masuk += $nominal;
        } else if (($d['type'] ?? '') == 'Pengeluaran') {
            $total_keluar += $nominal;
        }
    }
} catch (\Exception $e) {}

// Urutkan Cash Flow Terbaru
usort($cash_flows, function($a, $b) {
    return strcmp($b['date'] ?? '', $a['date'] ?? '');
});

// --- Ambil Leaderboard Atlet Global ---
$performances = [];
try {
    $perfDocs = $database->getDocuments('performances');
    foreach ($perfDocs as $p) {
        $nama_atlet = 'Unknown';
        if (!empty($p['member_id'])) {
            try {
                $atlet = $database->getDocument('atlet', $p['member_id']);
                if ($atlet) $nama_atlet = $atlet['nama'];
            } catch (\Exception $e) {}
        }
        $p['nama_atlet'] = $nama_atlet;
        $performances[] = $p;
    }
} catch (\Exception $e) {}

// Urutkan berdasarkan waktu tercepat (ASC)
usort($performances, function($a, $b) {
    $waktu_a = $a['time_formatted'] ?? '99:99.99';
    $waktu_b = $b['time_formatted'] ?? '99:99.99';
    return strcmp($waktu_a, $waktu_b);
});
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Laporan & Analitik Global</h1>
            <p class="text-sm text-gray-500">Melihat pergerakan arus kas dari seluruh cabang dan performa atlet secara global.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Pemasukan (Global)</p>
                    <h3 class="text-2xl font-black text-green-600">Rp <?= number_format($total_masuk, 0, ',', '.'); ?></h3>
                </div>
                <div class="bg-green-100 p-3 rounded-full text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Pengeluaran (Global)</p>
                    <h3 class="text-2xl font-black text-red-600">Rp <?= number_format($total_keluar, 0, ',', '.'); ?></h3>
                </div>
                <div class="bg-red-100 p-3 rounded-full text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </div>
        </div>

        <div class="mb-8 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="cashflow-tab" data-tabs-target="#cashflow" type="button" role="tab" aria-controls="cashflow" aria-selected="false">Arus Kas Global</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="leaderboard-tab" data-tabs-target="#leaderboard" type="button" role="tab" aria-controls="leaderboard" aria-selected="false">Global Leaderboard</button>
                </li>
            </ul>
        </div>

        <div id="myTabContent">
            <!-- TAB CASHFLOW -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="cashflow" role="tabpanel" aria-labelledby="cashflow-tab">
                <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-white uppercase bg-slate-800">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Cabang / Kolam</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($cash_flows) > 0) { foreach($cash_flows as $c) { 
                                $color = ($c['type'] == 'Pemasukan') ? 'text-green-600' : 'text-red-600';
                            ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4"><?= date('d M Y', strtotime($c['date'])); ?></td>
                                <td class="px-6 py-4 font-bold"><?= htmlspecialchars($c['nama_kolam']); ?></td>
                                <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-100 border rounded text-xs"><?= htmlspecialchars($c['type']); ?></span></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($c['description']); ?></td>
                                <td class="px-6 py-4 font-bold <?= $color ?>">Rp <?= number_format($c['amount'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='5' class='text-center py-4'>Belum ada data.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB LEADERBOARD -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="leaderboard" role="tabpanel" aria-labelledby="leaderboard-tab">
                <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-white uppercase bg-slate-800">
                            <tr>
                                <th class="px-6 py-4">Rank</th>
                                <th class="px-6 py-4">Atlet</th>
                                <th class="px-6 py-4">Gaya</th>
                                <th class="px-6 py-4">Jarak</th>
                                <th class="px-6 py-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (count($performances) > 0) { 
                                $rank = 1;
                                foreach($performances as $p) { 
                            ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 font-black text-slate-800">#<?= $rank++; ?></td>
                                <td class="px-6 py-4 font-bold text-indigo-700"><?= htmlspecialchars($p['nama_atlet']); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($p['swim_style'] ?? '-'); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($p['distance'] ?? '-'); ?>m</td>
                                <td class="px-6 py-4 font-black text-slate-900"><?= htmlspecialchars($p['time_formatted'] ?? '-'); ?></td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='5' class='text-center py-4'>Belum ada data prestasi.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
