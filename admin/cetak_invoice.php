<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login") {
    header("location:../login.php?pesan=belum_login");
    exit;
}
include '../includes/koneksi.php';

$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';

if (empty($id)) {
    die("Data member tidak ditemukan.");
}

$query = "SELECT m.*, c.nama_cabang, c.lokasi as alamat_cabang, p.nama as nama_pelatih 
          FROM member m 
          LEFT JOIN cabang c ON m.cabang_id = c.id 
          LEFT JOIN pelatih p ON m.pelatih_id = p.id 
          WHERE m.id = '$id'";
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Data member tidak ditemukan.");
}

$data = mysqli_fetch_assoc($result);

// Generate Metadata
$invoice_number = "INV-" . str_pad($data['id'], 4, '0', STR_PAD_LEFT) . "-" . date('Y', strtotime($data['tanggal_gabung'] ?? date('Y-m-d')));
$invoice_date = date('d M Y', strtotime($data['tanggal_gabung'] ?? date('Y-m-d')));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Receipt - <?= htmlspecialchars($data['nama']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc; /* Slate-50 background for contrast against card */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 16px;
        }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>

    <!-- Floating Print Button (optional for user) -->
    <div class="fixed bottom-6 right-6 no-print z-50">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-5 rounded-full shadow-lg flex items-center gap-2 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print E-Receipt
        </button>
    </div>

    <!-- E-Receipt Card Container -->
    <div class="w-[430px] mx-auto bg-white p-6 rounded-2xl shadow-lg border border-gray-100 my-4 text-gray-800 relative overflow-hidden">
        
        <!-- Decoration Element -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-0"></div>

        <!-- Header -->
        <div class="flex justify-between items-start border-b border-dashed border-gray-200 pb-5 mb-5 relative z-10">
            <!-- Logo area -->
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm border border-gray-100 overflow-hidden">
                    <img src="../assets/logo.png" alt="Logo Swift SC" class="w-10 h-10 object-contain" onerror="this.onerror=null; this.outerHTML='<div class=\'w-full h-full bg-blue-600 flex items-center justify-center text-white font-bold text-xl\'>SC</div>';">
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900 tracking-tight leading-none">SWIFT SC</h1>
                    <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest"><?= htmlspecialchars($data['nama_cabang'] ?? 'Pusat') ?></p>
                </div>
            </div>
            
            <!-- Title & Meta -->
            <div class="text-right">
                <h2 class="text-sm font-black text-gray-800 tracking-wider uppercase mb-1">E-RECEIPT</h2>
                <p class="text-[10px] text-gray-500 font-mono"><?= $invoice_number ?></p>
                <p class="text-[10px] text-gray-500 font-medium"><?= $invoice_date ?></p>
            </div>
        </div>

        <!-- Details Section -->
        <div class="bg-gray-50/80 rounded-xl p-4 mb-6 border border-gray-100 relative z-10">
            <div class="grid grid-cols-2 gap-y-4 gap-x-3 text-xs">
                <div>
                    <p class="text-gray-400 font-bold uppercase tracking-wider mb-1 text-[9px]">Nama Atlet</p>
                    <p class="font-bold text-gray-900"><?= htmlspecialchars($data['nama']) ?></p>
                </div>
                <div>
                    <p class="text-gray-400 font-bold uppercase tracking-wider mb-1 text-[9px]">Asal Sekolah</p>
                    <p class="font-bold text-gray-900"><?= htmlspecialchars($data['sekolah'] ?? '-') ?></p>
                </div>
                <div>
                    <p class="text-gray-400 font-bold uppercase tracking-wider mb-1 text-[9px]">Kelas</p>
                    <p class="font-bold text-gray-900"><?= htmlspecialchars($data['tingkatan_kelas'] ?? 'Pemula') ?></p>
                </div>
                <div>
                    <p class="text-gray-400 font-bold uppercase tracking-wider mb-1 text-[9px]">Pelatih</p>
                    <p class="font-bold text-gray-900"><?= htmlspecialchars($data['nama_pelatih'] ?? 'Belum Ditentukan') ?></p>
                </div>
            </div>
        </div>

        <!-- Billing Items -->
        <div class="relative z-10">
            <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Rincian Pembayaran</h3>
            
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-3">
                    <div>
                        <p class="font-bold text-gray-800">Biaya Pendaftaran</p>
                    </div>
                    <p class="font-medium text-gray-700">Rp 100.000</p>
                </div>
                <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-3">
                    <div>
                        <p class="font-bold text-gray-800">Iuran Bulan Pertama</p>
                    </div>
                    <p class="font-medium text-gray-700">Rp 350.000</p>
                </div>
            </div>

            <!-- Total Row with Badge -->
            <div class="flex justify-between items-center bg-gray-50/50 p-4 rounded-xl border-2 border-gray-100 shadow-sm mt-2">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Total Dibayar</p>
                    <?php if(($data['payment_status'] ?? '') == 'Paid'): ?>
                        <span class="inline-block border-2 border-green-500 text-green-600 bg-green-50 text-[10px] font-black px-3 py-1 rounded-md uppercase tracking-widest shadow-sm">LUNAS</span>
                    <?php else: ?>
                        <span class="inline-block border-2 border-red-500 text-red-600 bg-red-50 text-[10px] font-black px-3 py-1 rounded-md uppercase tracking-widest shadow-sm">UNPAID</span>
                    <?php endif; ?>
                </div>
                <p class="text-2xl font-black text-blue-600 tracking-tight">Rp 450.000</p>
            </div>
        </div>
        
    </div>

</body>
</html>
