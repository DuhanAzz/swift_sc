<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    header("location:../login.php?pesan=belum_login"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
include '../includes/invoice_template.php';

$limit_hadir = isset($_GET['limit_hadir']) ? (int)$_GET['limit_hadir'] : 8;
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? mysqli_real_escape_string($koneksi, trim($_GET['tanggal_mulai'], "'\" ")) : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? mysqli_real_escape_string($koneksi, trim($_GET['tanggal_akhir'], "'\" ")) : date('Y-m-t');
$cari_nama = isset($_GET['cari_nama']) ? mysqli_real_escape_string($koneksi, $_GET['cari_nama']) : '';
$filter_kelas = isset($_GET['filter_kelas']) ? mysqli_real_escape_string($koneksi, $_GET['filter_kelas']) : '';
$template_nominal = isset($_GET['template_nominal']) ? $_GET['template_nominal'] : '';

$admin_pool_id = $_SESSION['pool_id'] ?? '';

// Ambil opsi kelas
$kelas_options = [];
$q_kelas = mysqli_query($koneksi, "SELECT DISTINCT tingkatan_kelas FROM member WHERE tingkatan_kelas IS NOT NULL AND tingkatan_kelas != '' ORDER BY tingkatan_kelas");
if($q_kelas) { while($r_k = mysqli_fetch_assoc($q_kelas)) { $kelas_options[] = $r_k['tingkatan_kelas']; } }

// Ambil semua member
$q_atlet = [];
$q_str = "SELECT m.*, c.nama_cabang FROM member m LEFT JOIN cabang c ON m.cabang_id = c.id WHERE 1=1";
if(!empty($admin_pool_id)) $q_str .= " AND m.cabang_id='$admin_pool_id'";
if(!empty($cari_nama)) $q_str .= " AND m.nama LIKE '%$cari_nama%'";
if(!empty($filter_kelas)) $q_str .= " AND m.tingkatan_kelas = '$filter_kelas'";
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
            
            <form action="pembayaran.php" method="GET" class="flex flex-col gap-3 w-full md:w-auto">
                <!-- Baris 1: Cari Nama, Kelas, Nominal Template -->
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <input type="text" name="cari_nama" value="<?= htmlspecialchars($cari_nama) ?>" placeholder="Cari Nama Atlet..." class="w-full sm:w-auto bg-white border border-[#E8E8EF] text-sm rounded-lg p-2">
                    <select name="filter_kelas" class="w-full sm:w-auto bg-white border border-[#E8E8EF] text-sm rounded-lg p-2">
                        <option value="">Semua Kelas</option>
                        <?php foreach($kelas_options as $k): ?>
                            <option value="<?= htmlspecialchars($k) ?>" <?= $filter_kelas == $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" id="template_nominal" name="template_nominal" value="<?= htmlspecialchars($template_nominal) ?>" placeholder="Nominal/Sesi (Rp)" class="w-full sm:w-auto bg-white border border-[#E8E8EF] text-sm rounded-lg p-2" title="Otomatis mengalikan nominal dengan sesi yang dicentang">
                </div>
                
                <!-- Baris 2: Limit, Tanggal, Tombol -->
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <div class="flex items-center bg-white border border-[#E8E8EF] rounded-lg px-2 w-full sm:w-auto">
                        <span class="text-xs text-gray-500 font-bold px-1 whitespace-nowrap">Limit Sesi:</span>
                        <select name="limit_hadir" class="w-full sm:w-auto border-none text-sm p-2 focus:ring-0 font-bold text-gray-700 bg-transparent cursor-pointer text-center">
                            <option value="4" <?= $limit_hadir == 4 ? 'selected' : '' ?>>4</option>
                            <option value="8" <?= $limit_hadir == 8 ? 'selected' : '' ?>>8</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input type="date" name="tanggal_mulai" value="<?= $tanggal_mulai ?>" class="flex-1 bg-white border border-[#E8E8EF] text-sm rounded-lg p-2">
                        <span class="text-gray-400">-</span>
                        <input type="date" name="tanggal_akhir" value="<?= $tanggal_akhir ?>" class="flex-1 bg-white border border-[#E8E8EF] text-sm rounded-lg p-2">
                    </div>
                    <button type="submit" class="w-full sm:w-auto bg-slate-800 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-slate-700 transition-colors">Tampilkan</button>
                </div>
            </form>
        </div>

        <form action="proses_pembayaran.php" method="POST">
            <input type="hidden" name="tanggal_mulai" value="<?= $tanggal_mulai; ?>">
            <input type="hidden" name="tanggal_akhir" value="<?= $tanggal_akhir; ?>">
            <input type="hidden" name="limit_hadir" value="<?= $limit_hadir; ?>">

            <div class="grid grid-cols-1 gap-4">
                <?php 
                $no = 1;
                foreach($q_atlet as $a):
                    $atlet_id = $a['id'];
                    
                    // === HITUNG SISA KUOTA (PRE-PAID LOGIC) ===
                    $q_kuota = mysqli_query($koneksi, "SELECT SUM(jumlah_sesi_terbayar) as total_kuota FROM pembayaran WHERE member_id='$atlet_id' AND status='Lunas'");
                    $r_kuota = mysqli_fetch_assoc($q_kuota);
                    $total_kuota = $r_kuota['total_kuota'] ?? 0;

                    $q_hadir_all = mysqli_query($koneksi, "SELECT COUNT(id) as total_hadir FROM absensi WHERE member_id='$atlet_id' AND status='Hadir'");
                    $r_hadir_all = mysqli_fetch_assoc($q_hadir_all);
                    $total_hadir = $r_hadir_all['total_hadir'] ?? 0;

                    $sisa_kuota = $total_kuota - $total_hadir;

                    // === KEHADIRAN PERIODE INI (UNTUK TAMPILAN) ===
                    $absensi_dates = [];
                    $jumlah_hadir_periode = 0;
                    $q_abs = mysqli_query($koneksi, "SELECT id, tanggal, status FROM absensi WHERE member_id='$atlet_id' AND tanggal >= '$tanggal_mulai' AND tanggal <= '$tanggal_akhir' ORDER BY tanggal ASC");
                    if($q_abs) {
                        while($ab = mysqli_fetch_assoc($q_abs)) {
                            $absensi_dates[] = $ab;
                            if($ab['status'] == 'Hadir') $jumlah_hadir_periode++;
                        }
                    }
                    
                    // Default form state
                    $status = 'Belum Bayar';
                    $jumlah = '';
                    $ket = '';
                    $tgl_bayar = null;
                    
                    // === RIWAYAT BAYAR TERAKHIR ===
                    $last_pay = null;
                    $q_last = mysqli_query($koneksi, "SELECT bulan, tahun, tgl_bayar, jumlah_bayar, jumlah_sesi_terbayar, cover_tgl_awal, cover_tgl_akhir, detail_tanggal FROM pembayaran WHERE member_id='$atlet_id' AND status='Lunas' ORDER BY tgl_bayar DESC LIMIT 1");
                    if($q_last && $r_last = mysqli_fetch_assoc($q_last)) {
                        $last_pay = $r_last;
                    }
                    
                    // Status logic
                    $perlu_bayar = ($sisa_kuota <= 0);
                    $sudah_lunas = ($sisa_kuota > 0);
                    
                    // Card border color
                    $card_border = 'border-[#E8E8EF]';
                    if($sudah_lunas) $card_border = 'border-emerald-200';
                    elseif($perlu_bayar) $card_border = 'border-amber-400 border-2 shadow-amber-100 shadow-lg';
                    
                    // WA Invoice
                    $wa_phone = formatPhoneWA($a['no_hp'] ?? '');
                    
                    $total_sesi = 8; // Default offer
                    
                    $inv_nama = $a['nama'];
                    $inv_nia = $a['nia'] ?? '-';
                    $inv_cabang = $a['nama_cabang'] ?? '-';
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
                                <?php if($last_pay): 
                                    $nama_bulan_arr = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                                ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Periode</span>
                                    <span class="font-medium text-gray-600"><?= $nama_bulan_arr[str_pad($last_pay['bulan'],2,'0',STR_PAD_LEFT)] ?? '' ?> <?= $last_pay['tahun'] ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mt-2 pt-2 border-t border-dashed border-gray-200">
                                    <span class="text-gray-500 font-bold block mb-1.5">Cakupan Sesi</span>
                                    <?php if($last_pay && !empty($last_pay['jumlah_sesi_terbayar'])): 
                                        $eng_m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                        $ind_m = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                        $t_awal = str_replace($eng_m, $ind_m, date('d M Y', strtotime($last_pay['cover_tgl_awal'])));
                                        $t_akhir = str_replace($eng_m, $ind_m, date('d M Y', strtotime($last_pay['cover_tgl_akhir'])));
                                    ?>
                                        <div class="text-left">
                                            <span class="inline-block bg-teal-100 text-teal-800 rounded-lg text-xs px-2 py-1 font-bold mb-1.5"><?= $last_pay['jumlah_sesi_terbayar'] ?>x Pertemuan</span>
                                            <span class="block text-xs text-slate-500"><?= $t_awal ?> - <?= $t_akhir ?></span>
                                            <?php if(!empty($last_pay['detail_tanggal'])): ?>
                                                <span class="block text-[9px] text-gray-400 mt-1 italic break-words"><?= htmlspecialchars($last_pay['detail_tanggal']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 italic">Belum dicatat (-)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- CENTER: Attendance Grid & Quota -->
                        <div class="flex-1 p-4">
                            <!-- Progress Kuota -->
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Sisa Kuota Latihan</p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold <?php
                                    if($sisa_kuota <= 0) echo 'bg-red-100 text-red-700';
                                    elseif($sisa_kuota <= 2) echo 'bg-amber-100 text-amber-700';
                                    else echo 'bg-emerald-100 text-emerald-700';
                                ?>"><?= $sisa_kuota ?> Sesi Tersisa</span>
                            </div>
                            
                            <!-- Progress bar -->
                            <?php 
                            $pct = min(($total_hadir % $limit_hadir) / $limit_hadir * 100, 100); 
                            if($sisa_kuota <= 0) $pct = 100;
                            ?>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4">
                                <div class="h-1.5 rounded-full transition-all <?= $sisa_kuota <= 0 ? 'bg-red-500' : ($sisa_kuota <= 2 ? 'bg-amber-400' : 'bg-emerald-500') ?>" style="width: <?= $pct ?>%"></div>
                            </div>
                            
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Riwayat Kehadiran (Periode Filter)</p>
                                <span class="text-[10px] text-gray-500"><?= $jumlah_hadir_periode ?>x Hadir</span>
                            </div>

                            <!-- Attendance pills -->
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                <?php 
                                if(count($absensi_dates) > 0):
                                    foreach($absensi_dates as $ab):
                                        $st = $ab['status'];
                                        $tgl_ab = date('j/n', strtotime($ab['tanggal']));
                                        $tgl_full = date('d M Y', strtotime($ab['tanggal']));
                                        
                                        if($st == 'Hadir') {
                                            ?>
                                            <span class="inline-flex items-center gap-0.5 px-2 py-1 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200" title="Hadir - <?= $tgl_full ?>">
                                                <span class="text-[8px]">🏊‍♂️</span>
                                                <?= $tgl_ab ?>
                                            </span>
                                            <?php
                                        } else {
                                            $pill_class = 'bg-gray-100 text-gray-500';
                                            $pill_icon = '';
                                            if($st == 'Sakit') { $pill_class = 'bg-amber-100 text-amber-700'; $pill_icon = 'S'; }
                                            elseif($st == 'Izin') { $pill_class = 'bg-sky-100 text-sky-700'; $pill_icon = 'I'; }
                                            elseif($st == 'Alpa') { $pill_class = 'bg-red-100 text-red-700'; $pill_icon = 'A'; }
                                            ?>
                                            <span class="inline-flex items-center gap-0.5 px-2 py-1 rounded-md text-[10px] font-semibold <?= $pill_class ?>" title="<?= $st . ' - ' . $tgl_full ?>">
                                                <span class="text-[8px]"><?= $pill_icon ?></span>
                                                <?= $tgl_ab ?>
                                            </span>
                                            <?php
                                        }
                                    endforeach;
                                else: 
                                ?>
                                    <span class="text-xs text-gray-400 italic">Belum ada data kehadiran pada periode ini</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($perlu_bayar): ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-2.5 flex items-start gap-2">
                                <span class="text-red-500 text-sm mt-0.5">⚠️</span>
                                <div>
                                    <p class="text-xs font-bold text-red-800">Kuota Habis — Waktunya beli paket sesi!</p>
                                    <p class="text-[10px] text-red-600">Atlet ini tidak memiliki kuota latihan. Harap tagih pembayaran baru.</p>
                                </div>
                            </div>
                            <?php elseif($sisa_kuota <= 2): ?>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-2.5 flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2">
                                    <span class="text-amber-500 text-sm mt-0.5">⚠️</span>
                                    <div>
                                        <p class="text-xs font-bold text-amber-800">Kuota Menipis (Sisa <?= $sisa_kuota ?>)</p>
                                        <p class="text-[10px] text-amber-600">Ingatkan atlet untuk segera membeli paket sesi baru.</p>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2.5 flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2">
                                    <span class="text-emerald-500 text-sm mt-0.5">✅</span>
                                    <div>
                                        <p class="text-xs font-bold text-emerald-800">Kuota Aman</p>
                                        <p class="text-[10px] text-emerald-600">Tersedia <?= $sisa_kuota ?> sesi aktif.</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- RIGHT: Payment Actions -->
                        <div class="lg:w-[200px] flex-shrink-0 p-4 border-t lg:border-t-0 lg:border-l border-[#E8E8EF] bg-gray-50/30 flex flex-col gap-2.5">
                            
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Metode Pembayaran</label>
                                <select name="metode[<?= $atlet_id ?>]" class="w-full border border-[#E8E8EF] bg-white rounded-lg p-2 text-xs font-bold text-gray-700">
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer">Transfer</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Status Bayar</label>
                                <select name="status[<?= $atlet_id ?>]" onchange="updateNominal(<?= $atlet_id ?>)" class="w-full border border-[#E8E8EF] bg-white rounded-lg p-2 text-xs font-bold text-gray-700">
                                    <option value="Belum Bayar">❌ Belum Bayar</option>
                                    <option value="Lunas">✅ Lunas</option>
                                </select>
                            </div>
                            
                            <!-- Nominal Input is hidden, handled via JS calculation (paket * template_nominal) -->
                            <input type="hidden" name="jumlah[<?= $atlet_id ?>]" id="input_jumlah_<?= $atlet_id ?>" value="">
                            <div class="bg-white border border-gray-100 rounded p-1.5 mb-1 flex justify-between items-center text-xs">
                                <span class="text-gray-500">Total:</span>
                                <span class="font-bold text-gray-800" id="display_nominal_<?= $atlet_id ?>">Rp 0</span>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Bulan Cover (Opsional)</label>
                                <input type="month" name="keterangan[<?= $atlet_id ?>]" value="<?= date('Y-m') ?>" class="w-full border border-[#E8E8EF] bg-white rounded-lg p-2 text-[11px]">
                            </div>
                            
                            <div class="flex gap-1.5 mt-auto pt-2">
                                <?php
                                    $js_nama = htmlspecialchars($inv_nama, ENT_QUOTES);
                                    $js_nia = htmlspecialchars($inv_nia, ENT_QUOTES);
                                    $js_cabang = htmlspecialchars($inv_cabang, ENT_QUOTES);
                                ?>
                                <button type="button" onclick="copyDynamicWA(event, <?= $atlet_id ?>, '<?= $js_nama ?>', '<?= $js_nia ?>', '<?= $js_cabang ?>')" class="flex-1 flex items-center justify-center gap-1 bg-white border border-[#E8E8EF] text-gray-600 py-2 rounded-lg text-[10px] font-bold hover:bg-gray-50 transition-colors" title="Copy Invoice">
                                    📋 Copy
                                </button>
                                <a href="#" onclick="openDynamicWA(event, <?= $atlet_id ?>, '<?= $wa_phone ?>', '<?= $js_nama ?>', '<?= $js_nia ?>', '<?= $js_cabang ?>')" class="flex-1 flex items-center justify-center gap-1 bg-green-500 text-white py-2 rounded-lg text-[10px] font-bold hover:bg-green-600 transition-colors" title="Kirim via WA">
                                    💬 WhatsApp
                                </a>
                            </div>
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
function updateNominal(id) {
    const templateInput = document.getElementById('template_nominal');
    const templateVal = templateInput ? parseInt(templateInput.value) : 0;
    
    const selectPaket = document.querySelector(`select[name="limit_hadir"]`);
    const paketVal = selectPaket ? parseInt(selectPaket.value) : 0;
    
    // Check if status is Lunas
    const selectStatus = document.querySelector(`select[name="status[${id}]"]`);
    const isLunas = selectStatus && selectStatus.value === 'Lunas';
    
    let total = 0;
    if (templateVal > 0 && paketVal > 0 && isLunas) {
        total = templateVal * paketVal;
    }
    
    document.getElementById(`input_jumlah_${id}`).value = total > 0 ? total : '';
    document.getElementById(`display_nominal_${id}`).innerText = total > 0 ? 'Rp ' + Number(total).toLocaleString('id-ID') : 'Rp 0';
}

function buildInvoiceText(id, nama, nia, cabang) {
    const selectPaket = document.querySelector(`select[name="limit_hadir"]`);
    const paketVal = selectPaket ? parseInt(selectPaket.value) : 8;
    
    let total_sesi = paketVal > 0 ? paketVal : 8; // default if not selected
    
    const nominalInput = document.getElementById(`input_jumlah_${id}`).value;
    let nominalStr = nominalInput ? Number(nominalInput).toLocaleString('id-ID') : '[Ketik Nominal di Atas]';
    
    let inv_text = "════════════════\n";
    inv_text += "TAGIHAN SWIFT SC\n";
    inv_text += "════════════════\n";
    inv_text += `Kepada : *${nama}*\n`;
    inv_text += `NIA    : ${nia}\n`;
    if(cabang && cabang !== '-') inv_text += `Cabang : ${cabang}\n`;
    inv_text += "\n─── Rincian ───\n";
    inv_text += `Pembelian Paket Latihan\n`;
    inv_text += `Kuota   : ${total_sesi} Pertemuan\n`;
    inv_text += "───────────────\n";
    inv_text += `TOTAL   : *Rp ${nominalStr}*\n\n`;
    inv_text += "Mohon segera melakukan pembayaran.\n";
    inv_text += "via:\n";
    inv_text += "- Transfer Bank: BCA - 123456789 a/n Swift Swimming Club\n";
    inv_text += "- Tunai saat latihan\n\n";
    inv_text += "Terima kasih,\n";
    inv_text += "*Admin Swift SC*\n";
    inv_text += "════════════════";
    return inv_text;
}

function formatDateIndo(dateStr) {
    const ind_m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const d = new Date(dateStr);
    const day = String(d.getDate()).padStart(2, '0');
    const month = ind_m[d.getMonth()];
    const year = d.getFullYear();
    return `${day} ${month} ${year}`;
}

function openDynamicWA(e, id, phone, nama, nia, cabang) {
    e.preventDefault();
    const text = buildInvoiceText(id, nama, nia, cabang);
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
}

function copyDynamicWA(e, id, nama, nia, cabang) {
    e.preventDefault();
    const text = buildInvoiceText(id, nama, nia, cabang);
    navigator.clipboard.writeText(text).then(() => {
        const btn = e.currentTarget;
        const orig = btn.innerHTML;
        btn.innerHTML = '✅ OK!';
        setTimeout(() => { btn.innerHTML = orig; }, 2000);
    });
}
</script>

<?php include '../includes/footer.php'; ?>