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

$q_cf = mysqli_query($koneksi, "SELECT cash_flows.*, cabang.nama_cabang FROM cash_flows LEFT JOIN cabang ON cash_flows.pool_id = cabang.id ORDER BY transaction_date DESC");
if($q_cf) {
    while($row = mysqli_fetch_assoc($q_cf)) {
        $row['nama_kolam'] = $row['nama_cabang'] ?? 'Pusat';
        $row['date'] = $row['transaction_date'];
        
        $nominal = floatval($row['amount'] ?? 0);
        if ($row['type'] == 'Pemasukan') {
            $total_masuk += $nominal;
        } else if ($row['type'] == 'Pengeluaran') {
            $total_keluar += $nominal;
        }
        $cash_flows[] = $row;
    }
}

// --- Ambil Leaderboard Atlet Global ---
$performances = [];
$q_perf = mysqli_query($koneksi, "SELECT performa.*, member.nama as nama_atlet FROM performa LEFT JOIN member ON performa.member_id = member.id ORDER BY waktu_ms ASC LIMIT 100");
if($q_perf) {
    while($row = mysqli_fetch_assoc($q_perf)) {
        $performances[] = $row;
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-algolia-navy">Laporan & Analitik Global</h1>
            <p class="text-sm text-gray-500">Melihat pergerakan arus kas dari seluruh cabang dan performa atlet secara global.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="card p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Pemasukan (Global)</p>
                    <h3 class="text-2xl font-black text-green-600">Rp <?= number_format($total_masuk, 0, ',', '.'); ?></h3>
                </div>
                <div class="bg-green-100 p-3 rounded-full text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
            </div>
            <div class="card p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Pengeluaran (Global)</p>
                    <h3 class="text-2xl font-black text-red-600">Rp <?= number_format($total_keluar, 0, ',', '.'); ?></h3>
                </div>
                <div class="bg-red-100 p-3 rounded-full text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </div>
        </div>

        <div class="mb-8 border-b border-[#E8E8EF]">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="cashflow-tab" data-tabs-target="#cashflow" type="button" role="tab" aria-controls="cashflow" aria-selected="false">Arus Kas Global</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-[#E8E8EF]" id="leaderboard-tab" data-tabs-target="#leaderboard" type="button" role="tab" aria-controls="leaderboard" aria-selected="false">Global Leaderboard</button>
                </li>
            </ul>
        </div>

        <div id="myTabContent">
            <!-- TAB CASHFLOW -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="cashflow" role="tabpanel" aria-labelledby="cashflow-tab">
                <div class="card overflow-hidden">
                    <table class="table-algolia">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
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
                                <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-100 border rounded text-xs"><?= htmlspecialchars($c['category'] ?? $c['type']); ?></span></td>
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
                <div class="card overflow-hidden">
                    <table class="table-algolia">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
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
                                <td class="px-6 py-4"><?= htmlspecialchars($p['gaya_renang'] ?? '-'); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($p['jarak'] ?? '-'); ?>m</td>
                                <td class="px-6 py-4 font-black text-slate-900"><?= htmlspecialchars($p['waktu_formatted'] ?? '-'); ?></td>
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
