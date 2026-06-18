<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// Menangkap filter bulan dan tahun, default ke bulan & tahun sekarang
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-algolia-navy">Rekap Presensi Bulanan</h1>
            <p class="text-sm text-gray-500">Laporan statistik kehadiran atlet per periode</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form action="rekap_presensi.php" method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-full md:w-48">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Pilih Bulan</label>
                    <select name="bulan" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <?php foreach($nama_bulan as $m => $nama) : ?>
                            <option value="<?= $m; ?>" <?= ($filter_bulan == $m) ? 'selected' : ''; ?>><?= $nama; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-32">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tahun</label>
                    <select name="tahun" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <?php 
                        $thn_skrg = date('Y');
                        for($i = $thn_skrg; $i >= $thn_skrg-2; $i--) {
                            echo "<option value='$i' ".($filter_tahun == $i ? 'selected' : '').">$i</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-bold rounded-lg text-sm px-5 py-2.5">
                    Tampilkan Rekap
                </button>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-xl border border-gray-200 bg-white">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h2 class="font-bold text-gray-700 uppercase text-center">
                    LAPORAN KEHADIRAN: <?= strtoupper($nama_bulan[$filter_bulan]); ?> <?= $filter_tahun; ?>
                </h2>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-4 text-center border-r">No</th>
                        <th class="px-6 py-4 border-r">Nama Atlet</th>
                        <th class="px-4 py-4 text-center text-green-700 bg-green-50 border-r">Hadir</th>
                        <th class="px-4 py-4 text-center text-blue-700 bg-blue-50 border-r">Izin</th>
                        <th class="px-4 py-4 text-center text-yellow-700 bg-yellow-50 border-r">Sakit</th>
                        <th class="px-4 py-4 text-center text-red-700 bg-red-50 border-r">Alpa</th>
                        <th class="px-4 py-4 text-center bg-gray-200 text-gray-800">Efektivitas (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query sakti untuk menghitung semua status dalam satu kali jalan
                    $query = mysqli_query($koneksi, "
                        SELECT 
                            a.nama,
                            COUNT(CASE WHEN p.status = 'Hadir' THEN 1 END) as jml_hadir,
                            COUNT(CASE WHEN p.status = 'Izin' THEN 1 END) as jml_izin,
                            COUNT(CASE WHEN p.status = 'Sakit' THEN 1 END) as jml_sakit,
                            COUNT(CASE WHEN p.status = 'Alpa' THEN 1 END) as jml_alpa,
                            COUNT(p.id) as total_sesi
                        FROM atlet a
                        LEFT JOIN presensi p ON a.id = p.atlet_id 
                            AND MONTH(p.tanggal) = '$filter_bulan' 
                            AND YEAR(p.tanggal) = '$filter_tahun'
                        GROUP BY a.id
                        ORDER BY a.nama ASC
                    ");

                    $no = 1;
                    while($d = mysqli_fetch_assoc($query)){
                        // Hitung persentase kehadiran
                        $persentase = ($d['total_sesi'] > 0) ? ($d['jml_hadir'] / $d['total_sesi']) * 100 : 0;
                        
                        // Warna progress bar berdasarkan persentase
                        $color = "bg-red-500";
                        if($persentase >= 80) $color = "bg-green-500";
                        elseif($persentase >= 50) $color = "bg-yellow-500";
                    ?>
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-4 text-center border-r font-medium"><?= $no++; ?></td>
                        <td class="px-6 py-4 border-r font-bold text-gray-800 uppercase text-xs"><?= htmlspecialchars($d['nama']); ?></td>
                        <td class="px-4 py-4 text-center border-r font-bold text-green-700"><?= $d['jml_hadir']; ?></td>
                        <td class="px-4 py-4 text-center border-r font-bold text-blue-700"><?= $d['jml_izin']; ?></td>
                        <td class="px-4 py-4 text-center border-r font-bold text-yellow-700"><?= $d['jml_sakit']; ?></td>
                        <td class="px-4 py-4 text-center border-r font-bold text-red-700"><?= $d['jml_alpa']; ?></td>
                        <td class="px-4 py-4 text-center bg-gray-50">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-full bg-gray-200 rounded-full h-2 max-w-[60px]">
                                    <div class="<?= $color; ?> h-2 rounded-full" style="width: <?= $persentase; ?>%"></div>
                                </div>
                                <span class="font-black text-gray-800"><?= round($persentase); ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
            <p class="text-sm text-blue-800">
                <strong>Info:</strong> Persentase dihitung dari total kehadiran dibagi total sesi latihan yang tercatat untuk atlet tersebut pada bulan yang dipilih.
            </p>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>