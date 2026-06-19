<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
include '../includes/invoice_template.php';

$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$nama_bulan = [
    '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
    '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
    '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
];
$admin_pool_id = $_SESSION['pool_id'] ?? '';

// Ambil semua member
$q_atlet = [];
$q_str = "SELECT m.*, c.nama_cabang FROM member m LEFT JOIN cabang c ON m.cabang_id = c.id WHERE 1=1";
if(!empty($admin_pool_id)) $q_str .= " AND m.cabang_id='$admin_pool_id'";
$q_str .= " ORDER BY m.nama ASC";
$q = mysqli_query($koneksi, $q_str);
if($q) { while($row = mysqli_fetch_assoc($q)) { $q_atlet[] = $row; } }
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-6 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses' => '✅ Data pembayaran berhasil disimpan!',
                'gagal' => '❌ Terjadi kesalahan saat menyimpan data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-3 mb-4 text-sm text-{$color}-800 rounded-lg bg-{$color}-50 border border-{$color}-200 font-medium'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-5 gap-3">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Penagihan & Iuran Bulanan</h1>
                <p class="text-sm text-gray-500">Detail kehadiran, riwayat pembayaran, dan tagihan via WhatsApp</p>
            </div>
            
            <form action="pembayaran.php" method="GET" class="flex items-center gap-2">
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

        <form action="proses_pembayaran.php" method="POST">
            <input type="hidden" name="bulan" value="<?= $filter_bulan; ?>">
            <input type="hidden" name="tahun" value="<?= $filter_tahun; ?>">

            <div class="grid grid-cols-1 gap-4">
                <?php 
                $no = 1;
                foreach($q_atlet as $a):
                    $atlet_id = $a['id'];
                    
                    // === KEHADIRAN BULAN INI ===
                    $absensi_dates = [];
                    $jumlah_hadir = 0;
                    $q_abs = mysqli_query($koneksi, "SELECT tanggal, status FROM absensi WHERE member_id='$atlet_id' AND MONTH(tanggal)='$filter_bulan' AND YEAR(tanggal)='$filter_tahun' ORDER BY tanggal ASC");
                    if($q_abs) {
                        while($ab = mysqli_fetch_assoc($q_abs)) {
                            $absensi_dates[] = $ab;
                            if($ab['status'] == 'Hadir') $jumlah_hadir++;
                        }
                    }
                    
                    // === PEMBAYARAN BULAN INI ===
                    $status = 'Belum Bayar';
                    $jumlah = '';
                    $ket = '';
                    $tgl_bayar = null;
                    $q_bayar = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE member_id='$atlet_id' AND bulan='$filter_bulan' AND tahun='$filter_tahun'");
                    if($q_bayar && mysqli_num_rows($q_bayar) > 0) {
                        $pData = mysqli_fetch_assoc($q_bayar);
                        $status = $pData['status'] ?? 'Belum Bayar';
                        $jumlah = $pData['jumlah_bayar'] ?? '';
                        $ket = $pData['keterangan'] ?? '';
                        $tgl_bayar = $pData['tgl_bayar'] ?? null;
                    }
                    
                    // === RIWAYAT BAYAR TERAKHIR ===
                    $last_pay = null;
                    $q_last = mysqli_query($koneksi, "SELECT bulan, tahun, tgl_bayar, jumlah_bayar FROM pembayaran WHERE member_id='$atlet_id' AND status='Lunas' ORDER BY tahun DESC, bulan DESC, tgl_bayar DESC LIMIT 1");
                    if($q_last && $r_last = mysqli_fetch_assoc($q_last)) {
                        $last_pay = $r_last;
                    }
                    
                    // Status logic
                    $perlu_bayar = ($jumlah_hadir >= 8 && $status != 'Lunas');
                    $sudah_lunas = ($status == 'Lunas');
                    
                    // Card border color
                    $card_border = 'border-[#E8E8EF]';
                    if($sudah_lunas) $card_border = 'border-emerald-200';
                    elseif($perlu_bayar) $card_border = 'border-amber-300';
                    
                    // WA Invoice
                    $wa_phone = formatPhoneWA($a['no_hp'] ?? '');
                    $inv_text = generateInvoiceSPP(
                        $a['nama'], $a['nia'] ?? 'SWF-'.$atlet_id, 
                        $a['nama_cabang'] ?? 'Swift SC', 
                        $filter_bulan, $filter_tahun, $jumlah_hadir, $a['no_hp'] ?? ''
                    );
                ?>
                
                <div class="bg-white rounded-xl border-2 <?= $card_border ?> overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex flex-col lg:flex-row">
                        <!-- LEFT: Info Atlet -->
                        <div class="lg:w-[240px] flex-shrink-0 p-4 border-b lg:border-b-0 lg:border-r border-[#E8E8EF] bg-gray-50/50">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    <?= strtoupper(substr($a['nama'], 0, 1)) ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-sm text-gray-800 truncate"><?= htmlspecialchars($a['nama']) ?></p>
                                    <p class="text-[10px] text-gray-500 font-mono"><?= htmlspecialchars($a['nia'] ?? '-') ?></p>
                                </div>
                            </div>
                            
                            <div class="space-y-1.5 text-[11px]">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">No HP</span>
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($a['no_hp'] ?? '-') ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Cabang</span>
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($a['nama_cabang'] ?? '-') ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Terakhir Bayar</span>
                                    <?php if($last_pay && $last_pay['tgl_bayar']): ?>
                                        <span class="font-medium text-emerald-600"><?= date('d M Y', strtotime($last_pay['tgl_bayar'])) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Belum pernah</span>
                                    <?php endif; ?>
                                </div>
                                <?php if($last_pay): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Periode</span>
                                    <span class="font-medium text-gray-600"><?= $nama_bulan[str_pad($last_pay['bulan'],2,'0',STR_PAD_LEFT)] ?? '' ?> <?= $last_pay['tahun'] ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- CENTER: Attendance Grid -->
                        <div class="flex-1 p-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kehadiran <?= $nama_bulan[$filter_bulan] ?> <?= $filter_tahun ?></p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold <?php
                                    if($jumlah_hadir >= 8 && $sudah_lunas) echo 'bg-emerald-100 text-emerald-700';
                                    elseif($jumlah_hadir >= 8) echo 'bg-amber-100 text-amber-700';
                                    else echo 'bg-gray-100 text-gray-600';
                                ?>"><?= $jumlah_hadir ?>x / 8 pertemuan</span>
                            </div>
                            
                            <!-- Attendance pills -->
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                <?php 
                                if(count($absensi_dates) > 0):
                                    foreach($absensi_dates as $ab):
                                        $st = $ab['status'];
                                        $pill_class = 'bg-gray-100 text-gray-500';
                                        $pill_icon = '';
                                        if($st == 'Hadir') { $pill_class = 'bg-emerald-100 text-emerald-700'; $pill_icon = '●'; }
                                        elseif($st == 'Sakit') { $pill_class = 'bg-amber-100 text-amber-700'; $pill_icon = 'S'; }
                                        elseif($st == 'Izin') { $pill_class = 'bg-sky-100 text-sky-700'; $pill_icon = 'I'; }
                                        elseif($st == 'Alpa') { $pill_class = 'bg-red-100 text-red-700'; $pill_icon = 'A'; }
                                ?>
                                    <span class="inline-flex items-center gap-0.5 px-2 py-1 rounded-md text-[10px] font-semibold <?= $pill_class ?>" title="<?= $st . ' - ' . date('d M', strtotime($ab['tanggal'])) ?>">
                                        <span class="text-[8px]"><?= $pill_icon ?></span>
                                        <?= date('d', strtotime($ab['tanggal'])) ?>
                                    </span>
                                <?php 
                                    endforeach;
                                else: 
                                ?>
                                    <span class="text-xs text-gray-400 italic">Belum ada data kehadiran bulan ini</span>
                                <?php endif; ?>
                            </div>

                            <!-- Progress bar -->
                            <?php $pct = min(($jumlah_hadir / 8) * 100, 100); ?>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-3">
                                <div class="h-1.5 rounded-full transition-all <?= $pct >= 100 ? 'bg-emerald-500' : 'bg-slate-400' ?>" style="width: <?= $pct ?>%"></div>
                            </div>
                            
                            <?php if($perlu_bayar): ?>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-2.5 flex items-start gap-2">
                                <span class="text-amber-500 text-sm mt-0.5">⚠️</span>
                                <div>
                                    <p class="text-xs font-bold text-amber-800">Sudah 8x hadir — Waktunya bayar!</p>
                                    <p class="text-[10px] text-amber-600">Kirim tagihan via WhatsApp atau konfirmasi pembayaran.</p>
                                </div>
                            </div>
                            <?php elseif($sudah_lunas): ?>
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2.5 flex items-start gap-2">
                                <span class="text-emerald-500 text-sm mt-0.5">✅</span>
                                <div>
                                    <p class="text-xs font-bold text-emerald-800">Lunas — Dibayar <?= $tgl_bayar ? date('d M Y, H:i', strtotime($tgl_bayar)) : '' ?></p>
                                    <?php if(!empty($ket)): ?>
                                    <p class="text-[10px] text-emerald-600"><?= htmlspecialchars($ket) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- RIGHT: Payment Actions -->
                        <div class="lg:w-[200px] flex-shrink-0 p-4 border-t lg:border-t-0 lg:border-l border-[#E8E8EF] bg-gray-50/30 flex flex-col gap-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Status Bayar</label>
                                <select name="status[<?= $atlet_id ?>]" class="w-full border border-[#E8E8EF] bg-white rounded-lg p-2 text-xs font-bold <?= $sudah_lunas ? 'text-emerald-600' : 'text-red-500' ?>">
                                    <option value="Belum Bayar" <?= $status == 'Belum Bayar' ? 'selected' : '' ?>>❌ Belum Bayar</option>
                                    <option value="Lunas" <?= $status == 'Lunas' ? 'selected' : '' ?>>✅ Lunas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                                <input type="number" name="jumlah[<?= $atlet_id ?>]" value="<?= $jumlah ?>" placeholder="0" class="w-full border border-[#E8E8EF] bg-white rounded-lg p-2 text-xs text-right">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Catatan</label>
                                <input type="text" name="keterangan[<?= $atlet_id ?>]" value="<?= htmlspecialchars($ket) ?>" placeholder="Opsional..." class="w-full border border-[#E8E8EF] bg-white rounded-lg p-2 text-xs">
                            </div>
                            
                            <div class="flex gap-1.5 mt-auto pt-2">
                                <button type="button" onclick="copyInvoiceSPP(<?= $atlet_id ?>)" class="flex-1 flex items-center justify-center gap-1 bg-white border border-[#E8E8EF] text-gray-600 py-2 rounded-lg text-[10px] font-bold hover:bg-gray-50 transition-colors" title="Copy Invoice">
                                    📋 Copy
                                </button>
                                <a href="https://wa.me/<?= $wa_phone ?>?text=<?= urlencode($inv_text) ?>" target="_blank" class="flex-1 flex items-center justify-center gap-1 bg-green-500 text-white py-2 rounded-lg text-[10px] font-bold hover:bg-green-600 transition-colors" title="Kirim via WA">
                                    💬 WhatsApp
                                </a>
                            </div>
                            
                            <textarea id="inv_<?= $atlet_id ?>" class="hidden"><?= htmlspecialchars($inv_text) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <?php endforeach; ?>
                
                <?php if(count($q_atlet) == 0): ?>
                <div class="bg-white rounded-xl border border-[#E8E8EF] p-10 text-center">
                    <p class="text-gray-400 text-sm">Belum ada data atlet untuk cabang ini.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if(count($q_atlet) > 0): ?>
            <div class="mt-4 flex justify-end">
                <button type="submit" name="simpan_bayar" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-3 px-10 rounded-lg shadow-md transition-all text-sm">
                    Simpan Semua Pembayaran
                </button>
            </div>
            <?php endif; ?>
        </form>

    </div>
</div>

<script>
function copyInvoiceSPP(id) {
    const textarea = document.getElementById('inv_' + id);
    if (textarea) {
        navigator.clipboard.writeText(textarea.value).then(() => {
            const btn = event.currentTarget;
            const orig = btn.innerHTML;
            btn.innerHTML = '✅ OK!';
            btn.classList.add('bg-green-50', 'text-green-600');
            setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('bg-green-50', 'text-green-600'); }, 1500);
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>