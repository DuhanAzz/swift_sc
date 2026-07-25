<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "ceo")) { 
    header("location:../login.php?pesan=belum_login"); 
    exit; 
}

require_once '../includes/koneksi.php';

$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : 0;
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : 0;

if (!$member_id || !$bulan || !$tahun) {
    die("Data tidak lengkap.");
}

// Ambil data member dan pembayaran
$query = "
    SELECT p.*, m.nama, m.nia, m.sekolah, c.nama_cabang
    FROM pembayaran p
    JOIN member m ON p.member_id = m.id
    JOIN cabang c ON m.cabang_id = c.id
    WHERE p.member_id = '$member_id' AND p.bulan = '$bulan' AND p.tahun = '$tahun' AND p.status = 'Lunas'
";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Kwitansi tidak ditemukan atau belum lunas.");
}

$bulan_array = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$nama_bulan = $bulan_array[$bulan];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi - <?= htmlspecialchars($data['nama']) ?> - <?= $nama_bulan ?> <?= $tahun ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 font-sans text-gray-800">

    <div class="max-w-2xl mx-auto bg-white p-8 rounded border border-gray-200 relative shadow-sm">
        
        <!-- Action bar -->
        <div class="no-print absolute top-4 right-4 flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded shadow text-sm font-bold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded shadow text-sm font-bold">
                Tutup
            </button>
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-6 mb-6">
            <div class="flex items-center gap-4">
                <img src="../assets/logo.png" alt="Logo" class="h-16 w-16 object-contain p-1">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">SWIFT SC</h1>
                    <p class="text-xs text-gray-500 font-medium">Professional Swimming Club</p>
                    <p class="text-[10px] text-gray-400 mt-1">Cabang: <?= htmlspecialchars($data['nama_cabang']) ?></p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-black text-gray-200 uppercase tracking-widest">Kwitansi</h2>
                <p class="text-xs font-bold text-gray-500 mt-1">#INV-<?= $tahun ?><?= str_pad($bulan, 2, '0', STR_PAD_LEFT) ?>-<?= str_pad($member_id, 4, '0', STR_PAD_LEFT) ?></p>
                <p class="text-xs text-gray-400">Tgl Cetak: <?= date('d M Y') ?></p>
            </div>
        </div>

        <!-- PAID Stamp -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 transform -rotate-12 opacity-10 pointer-events-none">
            <span class="text-8xl font-black text-green-600 border-8 border-green-600 p-4 rounded-xl inline-block uppercase">Lunas</span>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Terima Dari</h3>
                <p class="text-lg font-bold text-gray-800"><?= htmlspecialchars($data['nama']) ?></p>
                <p class="text-sm text-gray-600 font-mono mt-1">NIA: <?= htmlspecialchars($data['nia']) ?></p>
                <?php if(!empty($data['sekolah'])): ?>
                <p class="text-xs text-gray-500 mt-1">Sekolah: <?= htmlspecialchars($data['sekolah']) ?></p>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Untuk Pembayaran</h3>
                <p class="text-sm text-gray-700 font-medium">Iuran Latihan Bulanan (SPP)</p>
                <p class="text-lg font-bold text-blue-600 mt-1">Periode: <?= $nama_bulan ?> <?= $tahun ?></p>
                <p class="text-xs text-gray-500 mt-1">Tgl Bayar: <?= date('d M Y H:i', strtotime($data['tgl_bayar'])) ?></p>
            </div>
        </div>

        <!-- Amount Box -->
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 flex items-center justify-between mb-8">
            <div class="text-sm text-gray-500 font-bold uppercase tracking-wider">Total Lunas</div>
            <div class="text-3xl font-black text-gray-900">
                Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.') ?>
            </div>
        </div>

        <!-- Signature -->
        <div class="flex justify-end mt-12">
            <div class="text-center w-48">
                <p class="text-xs text-gray-500 mb-12">Diterima oleh,</p>
                <div class="border-b border-gray-400 w-full mb-2"></div>
                <p class="text-xs font-bold text-gray-700">Admin Swift SC</p>
            </div>
        </div>

    </div>

    <script>
        // Otomatis trigger print saat dibuka
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
