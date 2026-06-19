<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');
$filter_cabang = isset($_GET['cabang_id']) ? $_GET['cabang_id'] : '';

// Ambil list cabang untuk dropdown
$list_cabang = [];
$qc = mysqli_query($koneksi, "SELECT id, nama_cabang FROM cabang ORDER BY nama_cabang ASC");
if($qc) {
    while($rc = mysqli_fetch_assoc($qc)) $list_cabang[] = $rc;
}

// --- Ambil Laporan Cash Flow Lintas Cabang (Arus Kas) ---
$arus_kas = [];
$total_masuk = 0;
$total_keluar = 0;

$q_str = "SELECT arus_kas.*, cabang.nama_cabang, users.username as nama_admin 
          FROM arus_kas 
          LEFT JOIN cabang ON arus_kas.cabang_id = cabang.id 
          LEFT JOIN users ON arus_kas.user_id = users.id
          WHERE arus_kas.tanggal >= '$tgl_mulai' AND arus_kas.tanggal <= '$tgl_akhir'";

if(!empty($filter_cabang)) {
    $q_str .= " AND arus_kas.cabang_id = '$filter_cabang'";
}
$q_str .= " ORDER BY arus_kas.tanggal DESC, arus_kas.id DESC";

$q_cf = mysqli_query($koneksi, $q_str);
if($q_cf) {
    while($row = mysqli_fetch_assoc($q_cf)) {
        $row['nama_kolam'] = $row['nama_cabang'] ?? 'Pusat';
        $row['date'] = $row['tanggal'];
        
        $nominal = floatval($row['nominal'] ?? 0);
        if ($row['jenis'] == 'Pemasukan') {
            $total_masuk += $nominal;
        } else if ($row['jenis'] == 'Pengeluaran') {
            $total_keluar += $nominal;
        }
        $arus_kas[] = $row;
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
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Laporan & Analitik Global</h1>
                <p class="text-sm text-gray-500">Melihat pergerakan arus kas dari seluruh cabang dan performa atlet secara global.</p>
            </div>
            
            <a href="ceo_export_laporan.php?tgl_mulai=<?= $tgl_mulai ?>&tgl_akhir=<?= $tgl_akhir ?>&cabang_id=<?= $filter_cabang ?>" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-sm">
                <span>📊</span> Export Master (Excel)
            </a>
        </div>

        <!-- Filter Bar Multi-Dimensi -->
        <div class="card p-4 mb-6 flex flex-wrap items-end gap-4">
            <form action="ceo_laporan_global.php" method="GET" class="flex flex-wrap items-center gap-3 w-full">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                    <input type="date" name="tgl_mulai" value="<?= $tgl_mulai ?>" class="bg-gray-50 border border-[#E8E8EF] text-sm rounded-lg p-2 focus:ring-algolia-blue">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="bg-gray-50 border border-[#E8E8EF] text-sm rounded-lg p-2 focus:ring-algolia-blue">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pilih Cabang</label>
                    <select name="cabang_id" class="bg-gray-50 border border-[#E8E8EF] text-sm rounded-lg p-2 focus:ring-algolia-blue min-w-[200px]">
                        <option value="">-- Seluruh Cabang --</option>
                        <?php foreach($list_cabang as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $filter_cabang == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nama_cabang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="pb-0.5 mt-auto">
                    <button type="submit" class="bg-algolia-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-algolia-darkblue transition-colors">Terapkan Filter</button>
                </div>
            </form>
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
                                <th class="px-6 py-4">Cabang</th>
                                <th class="px-6 py-4">Jenis</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4">Pencatat</th>
                                <th class="px-6 py-4 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($arus_kas) > 0) { foreach($arus_kas as $c) { 
                                $is_in = ($c['jenis'] == 'Pemasukan');
                                $color = $is_in ? 'text-green-600' : 'text-red-600';
                            ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm"><?= date('d M Y', strtotime($c['date'])); ?></td>
                                <td class="px-6 py-4 text-sm font-bold"><?= htmlspecialchars($c['nama_kolam']); ?></td>
                                <td class="px-6 py-4 text-sm"><span class="px-2 py-1 <?= $is_in ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?> border rounded text-xs font-bold"><?= htmlspecialchars($c['jenis']); ?></span></td>
                                <td class="px-6 py-4 text-sm"><?= htmlspecialchars($c['keterangan']); ?></td>
                                <td class="px-6 py-4 text-xs text-gray-500"><?= htmlspecialchars($c['nama_admin'] ?? 'Sistem'); ?></td>
                                <td class="px-6 py-4 text-sm font-bold <?= $color ?> text-right"><?= $is_in ? '+' : '-' ?> Rp <?= number_format($c['nominal'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='6' class='text-center py-4 text-gray-500 italic'>Tidak ada transaksi pada filter ini.</td></tr>"; } ?>
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
