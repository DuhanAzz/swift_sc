<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

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
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Manajemen Iuran / SPP</h1>
                <p class="text-sm text-gray-500">Pantau dan catat pembayaran bulanan atlet</p>
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
                
                <table class="w-full text-sm text-left border-collapse border border-[#E8E8EF]">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="border border-[#E8E8EF] p-3 text-center w-12">No</th>
                            <th class="border border-[#E8E8EF] p-3">Nama Atlet</th>
                            <th class="border border-[#E8E8EF] p-3 text-center w-48">Status Bayar</th>
                            <th class="border border-[#E8E8EF] p-3 text-center w-48">Nominal (Rp)</th>
                            <th class="border border-[#E8E8EF] p-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_atlet = [];
                        $q_str = "SELECT * FROM member WHERE 1=1";
                        if(!empty($admin_pool_id)) {
                            $q_str .= " AND cabang_id='$admin_pool_id'";
                        }
                        $q_str .= " ORDER BY nama ASC";
                        $q = mysqli_query($koneksi, $q_str);
                        if($q) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $q_atlet[] = $row;
                            }
                        }

                        $no = 1;
                        foreach($q_atlet as $a){
                            $atlet_id = $a['id'];
                            
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
                        ?>
                        <tr class="hover:bg-blue-50">
                            <td class="border border-[#E8E8EF] p-2 text-center text-gray-400"><?= $no++; ?></td>
                            <td class="border border-[#E8E8EF] p-2 font-bold text-gray-700 uppercase text-xs"><?= $a['nama']; ?></td>
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
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <div class="p-4 bg-gray-50 border-t border-[#E8E8EF] flex justify-end">
                    <button type="submit" name="simpan_bayar" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition-all">
                        Simpan Perubahan Pembayaran
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>