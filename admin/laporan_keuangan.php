<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Laporan Pendapatan Iuran</h1>
                <p class="text-sm text-gray-500">Ringkasan total uang iuran yang diterima per bulan</p>
            </div>
            
            <form action="laporan_keuangan.php" method="GET">
                <select name="tahun" onchange="this.form.submit()" class="bg-white border border-gray-300 text-sm rounded-lg p-2.5 font-bold text-blue-700">
                    <?php for($i=date('Y'); $i>=date('Y')-2; $i--) echo "<option value='$i' ".($filter_tahun==$i?'selected':'').">Tahun $i</option>"; ?>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-800 text-white uppercase text-xs">
                        <tr>
                            <th class="p-4 text-center">Bulan</th>
                            <th class="p-4 text-center">Atlet Lunas</th>
                            <th class="p-4 text-right">Total Pendapatan</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        $nama_bulan = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];

                        $grand_total = 0;

                        for($m=1; $m<=12; $m++) {
                            // Query hitung total per bulan
                            $query = mysqli_query($koneksi, "
                                SELECT 
                                    SUM(jumlah_bayar) as total_masuk, 
                                    COUNT(CASE WHEN status='Lunas' THEN 1 END) as jml_lunas 
                                FROM pembayaran 
                                WHERE bulan='$m' AND tahun='$filter_tahun'
                            ");
                            $data = mysqli_fetch_assoc($query);
                            $total = $data['total_masuk'] ?? 0;
                            $lunas = $data['jml_lunas'] ?? 0;
                            $grand_total += $total;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-bold text-gray-700"><?= $nama_bulan[$m]; ?></td>
                            <td class="p-4 text-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                                    <?= $lunas; ?> Atlet
                                </span>
                            </td>
                            <td class="p-4 text-right font-black text-gray-900 text-base">
                                Rp <?= number_format($total, 0, ',', '.'); ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="pembayaran.php?bulan=<?= sprintf("%02d", $m); ?>&tahun=<?= $filter_tahun; ?>" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-tighter"> Detail &rarr;</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                        <tr>
                            <td colspan="2" class="p-5 text-right font-bold text-gray-500 uppercase">Total Pendapatan Tahun <?= $filter_tahun; ?>:</td>
                            <td class="p-5 text-right font-black text-2xl text-green-700">
                                Rp <?= number_format($grand_total, 0, ',', '.'); ?>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="flex justify-end">
                <button onclick="window.print()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg border border-gray-300 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </button>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>