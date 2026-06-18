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
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
$admin_pool_id = $_SESSION['pool_id'] ?? '';
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses' => 'Data pembayaran berhasil disimpan!',
                'gagal' => 'Terjadi kesalahan saat menyimpan data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-4 mb-4 text-sm text-{$color}-800 rounded-lg bg-{$color}-50 border border-{$color}-200'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Manajemen Iuran / SPP</h1>
                <p class="text-sm text-gray-500">Pantau absensi dan pembayaran bulanan atlet. Kirim tagihan via WhatsApp.</p>
            </div>
            
            <form action="pembayaran.php" method="GET" class="flex items-center gap-2">
                <select name="bulan" class="bg-white border border-[#E8E8EF] text-sm rounded-lg p-2.5">
                    <?php foreach($nama_bulan as $m => $nama) : ?>
                        <option value="<?= $m; ?>" <?= ($filter_bulan == $m) ? 'selected' : ''; ?>><?= $nama; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tahun" class="bg-white border border-[#E8E8EF] text-sm rounded-lg p-2.5">
                    <?php for($i=date('Y'); $i>=date('Y')-1; $i--) echo "<option value='$i' ".($filter_tahun==$i?'selected':'').">$i</option>"; ?>
                </select>
                <button type="submit" class="bg-slate-800 text-white px-4 py-2.5 rounded-lg text-sm font-bold">Cek</button>
            </form>
        </div>

        <div class="bg-white p-2 shadow-sm border border-[#E8E8EF] rounded-xl overflow-hidden">
            <form action="proses_pembayaran.php" method="POST">
                <input type="hidden" name="bulan" value="<?= $filter_bulan; ?>">
                <input type="hidden" name="tahun" value="<?= $filter_tahun; ?>">
                
                <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse border border-[#E8E8EF]">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="border border-[#E8E8EF] p-3 text-center w-12">No</th>
                            <th class="border border-[#E8E8EF] p-3">Nama Atlet</th>
                            <th class="border border-[#E8E8EF] p-3 text-center w-28">Absensi</th>
                            <th class="border border-[#E8E8EF] p-3 text-center w-40">Status Bayar</th>
                            <th class="border border-[#E8E8EF] p-3 text-center w-36">Nominal (Rp)</th>
                            <th class="border border-[#E8E8EF] p-3 w-40">Keterangan</th>
                            <th class="border border-[#E8E8EF] p-3 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_atlet = [];
                        $q_str = "SELECT m.*, c.nama_cabang FROM member m LEFT JOIN cabang c ON m.cabang_id = c.id WHERE 1=1";
                        if(!empty($admin_pool_id)) {
                            $q_str .= " AND m.cabang_id='$admin_pool_id'";
                        }
                        $q_str .= " ORDER BY m.nama ASC";
                        $q = mysqli_query($koneksi, $q_str);
                        if($q) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $q_atlet[] = $row;
                            }
                        }

                        $no = 1;
                        foreach($q_atlet as $a){
                            $atlet_id = $a['id'];
                            
                            // Count attendance this month
                            $jumlah_absensi = 0;
                            $q_abs = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE member_id='$atlet_id' AND status='Hadir' AND MONTH(tanggal)='$filter_bulan' AND YEAR(tanggal)='$filter_tahun'");
                            if ($q_abs) {
                                $abs_row = mysqli_fetch_assoc($q_abs);
                                $jumlah_absensi = intval($abs_row['total']);
                            }
                            
                            $status = 'Belum Bayar';
                            $jumlah = '';
                            $ket = '';
                            
                            $q_bayar = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE member_id='$atlet_id' AND bulan='$filter_bulan' AND tahun='$filter_tahun'");
                            if($q_bayar && mysqli_num_rows($q_bayar) > 0) {
                                $pData = mysqli_fetch_assoc($q_bayar);
                                $status = $pData['status'] ?? 'Belum Bayar';
                                $jumlah = $pData['jumlah_bayar'] ?? '';
                                $ket = $pData['keterangan'] ?? '';
                            }
                            
                            // Badge colors for attendance
                            $abs_badge = 'bg-gray-100 text-gray-600';
                            $abs_icon = '';
                            if ($jumlah_absensi >= 8 && $status == 'Lunas') {
                                $abs_badge = 'bg-green-100 text-green-700';
                                $abs_icon = '✅';
                            } elseif ($jumlah_absensi >= 8 && $status != 'Lunas') {
                                $abs_badge = 'bg-amber-100 text-amber-700';
                                $abs_icon = '⚠️';
                            }
                            
                            // Generate invoice data for WA
                            $wa_phone = formatPhoneWA($a['no_hp'] ?? '');
                            $inv_text = generateInvoiceSPP(
                                $a['nama'], 
                                $a['nia'] ?? 'SWF-' . $atlet_id, 
                                $a['nama_cabang'] ?? 'Swift SC', 
                                $filter_bulan, 
                                $filter_tahun, 
                                $jumlah_absensi,
                                $a['no_hp'] ?? ''
                            );
                        ?>
                        <tr class="hover:bg-blue-50 <?= ($jumlah_absensi >= 8 && $status != 'Lunas') ? 'bg-amber-50/50' : '' ?>">
                            <td class="border border-[#E8E8EF] p-2 text-center text-gray-400"><?= $no++; ?></td>
                            <td class="border border-[#E8E8EF] p-2 font-bold text-gray-700 uppercase text-xs"><?= $a['nama']; ?></td>
                            <td class="border border-[#E8E8EF] p-2 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold <?= $abs_badge ?>">
                                    <?= $abs_icon ?> <?= $jumlah_absensi ?>x hadir
                                </span>
                            </td>
                            <td class="border border-[#E8E8EF] p-0">
                                <select name="status[<?= $atlet_id; ?>]" class="w-full border-0 bg-transparent p-2 text-xs font-bold <?= $status == 'Lunas' ? 'text-green-600' : 'text-red-500' ?>">
                                    <option value="Belum Bayar" <?= $status == 'Belum Bayar' ? 'selected' : '' ?>>❌ Belum Bayar</option>
                                    <option value="Lunas" <?= $status == 'Lunas' ? 'selected' : '' ?>>✅ Lunas</option>
                                </select>
                            </td>
                            <td class="border border-[#E8E8EF] p-0">
                                <input type="number" name="jumlah[<?= $atlet_id; ?>]" value="<?= $jumlah; ?>" placeholder="0" class="w-full border-0 bg-transparent p-2 text-sm text-right outline-none">
                            </td>
                            <td class="border border-[#E8E8EF] p-0">
                                <input type="text" name="keterangan[<?= $atlet_id; ?>]" value="<?= $ket; ?>" placeholder="Catatan..." class="w-full border-0 bg-transparent p-2 text-xs outline-none">
                            </td>
                            <td class="border border-[#E8E8EF] p-2 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" onclick="copyInvoiceSPP(<?= $atlet_id ?>)" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Copy Invoice">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                    <a href="https://wa.me/<?= $wa_phone ?>?text=<?= urlencode($inv_text) ?>" target="_blank" class="p-1.5 hover:bg-green-50 rounded-lg transition-colors" title="Kirim Tagihan via WA">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                    </a>
                                </div>
                                <!-- Hidden invoice text for copy -->
                                <textarea id="inv_<?= $atlet_id ?>" class="hidden"><?= htmlspecialchars($inv_text) ?></textarea>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </div>
                
                <div class="p-4 bg-gray-50 border-t border-[#E8E8EF] flex flex-col sm:flex-row justify-between items-center gap-3">
                    <p class="text-xs text-gray-500">
                        <span class="inline-block w-3 h-3 bg-amber-100 rounded mr-1"></span> Kuning = sudah 8x hadir, belum bayar
                        <span class="inline-block w-3 h-3 bg-green-100 rounded ml-3 mr-1"></span> Hijau = sudah bayar
                    </p>
                    <button type="submit" name="simpan_bayar" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition-all">
                        Simpan Perubahan Pembayaran
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function copyInvoiceSPP(id) {
    const textarea = document.getElementById('inv_' + id);
    if (textarea) {
        navigator.clipboard.writeText(textarea.value).then(() => {
            // Brief visual feedback
            const btn = event.currentTarget;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            setTimeout(() => { btn.innerHTML = originalHTML; }, 1500);
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>