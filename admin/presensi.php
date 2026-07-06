<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    header("location:../login.php?pesan=belum_login"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$admin_pool_id = $_SESSION['pool_id'] ?? '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$nama_bulan = [
    '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
    '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
    '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
];

// Jumlah hari di bulan ini
$jml_hari = cal_days_in_month(CAL_GREGORIAN, intval($filter_bulan), intval($filter_tahun));

// Ambil semua member
$members = [];
$q_str = "SELECT id, nama FROM member WHERE 1=1";
if(!empty($admin_pool_id)) $q_str .= " AND cabang_id='$admin_pool_id'";
$q_str .= " ORDER BY nama ASC";
$q = mysqli_query($koneksi, $q_str);
if($q) { while($r = mysqli_fetch_assoc($q)) { $members[] = $r; } }

// Ambil semua absensi bulan ini sekaligus
$absensi_map = []; // [member_id][tanggal] = status
$q_abs = mysqli_query($koneksi, "SELECT member_id, DAY(tanggal) as tgl, status FROM absensi WHERE MONTH(tanggal)='$filter_bulan' AND YEAR(tanggal)='$filter_tahun'" . (!empty($admin_pool_id) ? " AND cabang_id='$admin_pool_id'" : ""));
if($q_abs) {
    while($r = mysqli_fetch_assoc($q_abs)) {
        $absensi_map[$r['member_id']][$r['tgl']] = $r['status'];
    }
}

$status_codes = ['H'=>'Hadir','I'=>'Izin','S'=>'Sakit','A'=>'Alpa'];
$status_colors = [
    'H' => 'bg-emerald-500 text-white',
    'I' => 'bg-sky-500 text-white', 
    'S' => 'bg-amber-500 text-white',
    'A' => 'bg-red-500 text-white',
    '' => 'bg-gray-100 text-gray-400'
];
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-6 page-content">
        
        <?php 
        if(isset($_GET['pesan']) && $_GET['pesan'] == "sukses_simpan"){
            echo '<div class="p-3 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 font-medium">✅ Absensi bulan '.$nama_bulan[$filter_bulan].' '.$filter_tahun.' berhasil disimpan!</div>';
        }
        ?>

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-5 gap-3">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Buku Absensi Bulanan</h1>
                <p class="text-sm text-gray-500">Klik kotak untuk mengubah status kehadiran atlet</p>
            </div>
            
            <form action="presensi.php" method="GET" class="flex items-center gap-2">
                <select name="bulan" class="bg-white border border-[#E8E8EF] text-sm rounded-lg p-2">
                    <?php foreach($nama_bulan as $m => $nama) : ?>
                        <option value="<?= $m; ?>" <?= ($filter_bulan == $m) ? 'selected' : ''; ?>><?= $nama; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tahun" class="bg-white border border-[#E8E8EF] text-sm rounded-lg p-2">
                    <?php for($i=date('Y'); $i>=date('Y')-1; $i--) echo "<option value='$i' ".($filter_tahun==$i?'selected':'').">$i</option>"; ?>
                </select>
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold">Tampilkan</button>
            </form>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap items-center gap-3 mb-4 text-xs">
            <span class="font-semibold text-gray-500 uppercase">Keterangan:</span>
            <span class="inline-flex items-center gap-1"><span class="w-6 h-6 rounded bg-emerald-500 text-white text-[10px] font-bold flex items-center justify-center">H</span> Hadir</span>
            <span class="inline-flex items-center gap-1"><span class="w-6 h-6 rounded bg-sky-500 text-white text-[10px] font-bold flex items-center justify-center">I</span> Izin</span>
            <span class="inline-flex items-center gap-1"><span class="w-6 h-6 rounded bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center">S</span> Sakit</span>
            <span class="inline-flex items-center gap-1"><span class="w-6 h-6 rounded bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">A</span> Alpa</span>
            <span class="inline-flex items-center gap-1"><span class="w-6 h-6 rounded bg-gray-100 text-gray-400 text-[10px] font-bold flex items-center justify-center">—</span> Kosong</span>
        </div>

        <form action="proses_presensi_bulanan.php" method="POST">
            <input type="hidden" name="bulan" value="<?= $filter_bulan ?>">
            <input type="hidden" name="tahun" value="<?= $filter_tahun ?>">
            
            <div class="bg-white rounded-xl shadow-sm border border-[#E8E8EF] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="text-xs border-collapse w-full" style="min-width: <?= 160 + ($jml_hari * 36) ?>px;">
                        <thead>
                            <tr class="bg-slate-800 text-white">
                                <th class="sticky left-0 z-10 bg-slate-800 px-3 py-2.5 text-left font-bold text-[11px] uppercase tracking-wide min-w-[140px] border-r border-slate-700">Nama Atlet</th>
                                <?php for($d=1; $d<=$jml_hari; $d++): 
                                    $tgl_full = sprintf('%s-%s-%02d', $filter_tahun, $filter_bulan, $d);
                                    $day_name = date('D', strtotime($tgl_full));
                                    $is_sun = ($day_name == 'Sun');
                                ?>
                                <th class="px-0.5 py-2 text-center font-medium w-8 min-w-[32px] <?= $is_sun ? 'bg-slate-900' : '' ?>">
                                    <div class="text-[9px] opacity-60 mb-0.5"><?= substr($day_name, 0, 2) ?></div>
                                    <div class="text-[11px]"><?= $d ?></div>
                                </th>
                                <?php endfor; ?>
                                <th class="px-2 py-2 text-center font-bold text-[10px] bg-slate-900 min-w-[32px]">∑</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($members) == 0): ?>
                            <tr><td colspan="<?= $jml_hari + 2 ?>" class="text-center py-10 text-gray-400">Belum ada data atlet.</td></tr>
                            <?php endif; ?>
                            
                            <?php foreach($members as $idx => $m): 
                                $mid = $m['id'];
                                $total_hadir = 0;
                            ?>
                            <tr class="border-b border-[#E8E8EF] hover:bg-blue-50/30 <?= $idx % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' ?>">
                                <td class="sticky left-0 z-10 <?= $idx % 2 == 0 ? 'bg-white' : 'bg-gray-50' ?> px-3 py-1.5 font-semibold text-gray-800 uppercase text-[10px] tracking-wide border-r border-[#E8E8EF] whitespace-nowrap">
                                    <?= htmlspecialchars($m['nama']) ?>
                                </td>
                                <?php for($d=1; $d<=$jml_hari; $d++): 
                                    $status_val = $absensi_map[$mid][$d] ?? '';
                                    $status_code = '';
                                    if($status_val == 'Hadir') { $status_code = 'H'; $total_hadir++; }
                                    elseif($status_val == 'Izin') $status_code = 'I';
                                    elseif($status_val == 'Sakit') $status_code = 'S';
                                    elseif($status_val == 'Alpa') $status_code = 'A';
                                    
                                    $cell_class = $status_colors[$status_code] ?? $status_colors[''];
                                    $tgl_full = sprintf('%s-%s-%02d', $filter_tahun, $filter_bulan, $d);
                                    $is_future = (strtotime($tgl_full) > strtotime('today'));
                                    $is_sun = (date('w', strtotime($tgl_full)) == 0);
                                ?>
                                <td class="px-0.5 py-1 text-center <?= $is_sun ? 'bg-gray-100/50' : '' ?>">
                                    <?php if(!$is_future): ?>
                                    <input type="hidden" name="abs[<?= $mid ?>][<?= $d ?>]" id="val_<?= $mid ?>_<?= $d ?>" value="<?= $status_code ?>">
                                    <div 
                                        onclick="toggleStatus(<?= $mid ?>, <?= $d ?>)"
                                        id="cell_<?= $mid ?>_<?= $d ?>"
                                        class="w-7 h-7 rounded cursor-pointer flex items-center justify-center text-[10px] font-bold mx-auto transition-all hover:scale-110 hover:shadow <?= $cell_class ?>"
                                        title="<?= $status_codes[$status_code] ?? 'Klik untuk isi' ?>"
                                    ><?= $status_code ?: '—' ?></div>
                                    <?php else: ?>
                                    <div class="w-7 h-7 rounded bg-gray-50 flex items-center justify-center text-[10px] text-gray-300 mx-auto">—</div>
                                    <?php endif; ?>
                                </td>
                                <?php endfor; ?>
                                <td class="px-2 py-1 text-center font-black text-[11px] text-emerald-600 bg-emerald-50/50" id="sum_<?= $mid ?>"><?= $total_hadir ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Bottom bar -->
                <div class="p-3 bg-gray-50 border-t border-[#E8E8EF] flex flex-col sm:flex-row justify-between items-center gap-3">
                    <p class="text-[10px] text-gray-500 uppercase tracking-wide">
                        <?= count($members) ?> atlet · <?= $nama_bulan[$filter_bulan] ?> <?= $filter_tahun ?> · <?= $jml_hari ?> hari
                    </p>
                    <button type="submit" name="simpan_absensi_bulanan" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition-all text-sm">
                        Simpan Absensi
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
const cycle = ['', 'H', 'I', 'S', 'A'];
const colors = {
    'H': 'bg-emerald-500 text-white',
    'I': 'bg-sky-500 text-white',
    'S': 'bg-amber-500 text-white',
    'A': 'bg-red-500 text-white',
    '': 'bg-gray-100 text-gray-400'
};
const labels = { 'H':'H', 'I':'I', 'S':'S', 'A':'A', '':'—' };
const titles = { 'H':'Hadir', 'I':'Izin', 'S':'Sakit', 'A':'Alpa', '':'Klik untuk isi' };

function toggleStatus(mid, day) {
    const input = document.getElementById('val_' + mid + '_' + day);
    const cell = document.getElementById('cell_' + mid + '_' + day);
    if (!input || !cell) return;
    
    let current = input.value;
    let idx = cycle.indexOf(current);
    let next = cycle[(idx + 1) % cycle.length];
    
    input.value = next;
    cell.textContent = labels[next];
    cell.title = titles[next];
    
    // Remove old colors, add new
    Object.values(colors).forEach(c => c.split(' ').forEach(cls => cell.classList.remove(cls)));
    colors[next].split(' ').forEach(cls => cell.classList.add(cls));
    
    // Update sum
    updateSum(mid);
}

function updateSum(mid) {
    let count = 0;
    document.querySelectorAll('input[name^="abs[' + mid + ']"]').forEach(inp => {
        if (inp.value === 'H') count++;
    });
    const sumEl = document.getElementById('sum_' + mid);
    if (sumEl) sumEl.textContent = count;
}
</script>

<?php include '../includes/footer.php'; ?>