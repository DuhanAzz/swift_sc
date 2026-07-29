<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelatih') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';

// Fitur tanpa sidebar dan header utama agar rapi saat diprint
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Evaluasi Atlet - Swift SC</title>
    <link rel="icon" type="image/png" href="../assets/favicon.png?v=<?= time() ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            body { background-color: white !important; }
            .print-exact { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8 print:py-0 print:bg-white">

    <?php
    $member_id = isset($_GET['atlet']) ? (int)$_GET['atlet'] : 0;
    
    // Ambil daftar atlet untuk dropdown jika belum memilih
    $cabang_id = $_SESSION['cabang_id'];
    $atlet_list = [];
    $q_atlet = mysqli_query($koneksi, "SELECT id, nama, kelompok_umur FROM member WHERE role='atlet' AND cabang_id='$cabang_id' ORDER BY nama ASC");
    if ($q_atlet) {
        while ($row = mysqli_fetch_assoc($q_atlet)) {
            $atlet_list[] = $row;
        }
    }
    
    if (!$member_id):
    ?>
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-200 print:hidden">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Cetak Rapor Evaluasi Atlet</h2>
        <form action="rapor_atlet.php" method="GET" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Atlet</label>
                <select name="atlet" required class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    <option value="">-- Pilih Atlet --</option>
                    <?php foreach($atlet_list as $at): ?>
                        <option value="<?= $at['id'] ?>"><?= htmlspecialchars($at['nama']) ?> (KU <?= htmlspecialchars($at['kelompok_umur']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-colors">
                Tampilkan Rapor
            </button>
            <a href="pelatih_dashboard.php" class="block text-center mt-4 text-sm text-gray-500 hover:text-indigo-600 font-medium">Kembali ke Dashboard</a>
        </form>
    </div>
    
    <?php else:
        
        // Ambil Data Profil Atlet & Cabang
        $q_profil = mysqli_query($koneksi, "
            SELECT m.*, c.nama_cabang 
            FROM member m 
            LEFT JOIN cabang c ON m.cabang_id = c.id 
            WHERE m.id = $member_id AND m.role = 'atlet'
        ");
        $profil = mysqli_fetch_assoc($q_profil);
        
        if(!$profil) {
            echo "<div class='text-center mt-12 text-red-500 font-bold'>Atlet tidak ditemukan.</div>";
            exit;
        }
        
        // Ambil Evaluasi Terakhir
        $q_eval = mysqli_query($koneksi, "
            SELECT e.*, p.nama as nama_pelatih
            FROM evaluasi_kualitatif e
            LEFT JOIN member p ON e.pelatih_id = p.id
            WHERE e.member_id = $member_id
            ORDER BY e.tanggal_evaluasi DESC LIMIT 1
        ");
        $eval = mysqli_fetch_assoc($q_eval);
        
        // Hitung Kehadiran (3 Bulan Terakhir)
        $tiga_bulan_lalu = date('Y-m-d', strtotime('-3 months'));
        $q_abs = mysqli_query($koneksi, "
            SELECT 
                COUNT(*) as total_pertemuan,
                SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir
            FROM absensi 
            WHERE member_id = $member_id AND tanggal_jadwal >= '$tiga_bulan_lalu'
        ");
        $abs = mysqli_fetch_assoc($q_abs);
        $total_pertemuan = $abs['total_pertemuan'] ?? 0;
        $total_hadir = $abs['total_hadir'] ?? 0;
        
        $persentase = ($total_pertemuan > 0) ? round(($total_hadir / $total_pertemuan) * 100) : 0;
        
        // Helper function for Stars
        function renderStars($nilai) {
            $html = '<div class="flex items-center gap-1">';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $nilai) {
                    $html .= '<svg class="w-5 h-5 text-yellow-400 print-exact" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                } else {
                    $html .= '<svg class="w-5 h-5 text-gray-200 print-exact" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                }
            }
            $html .= '<span class="ml-2 font-bold text-gray-700 text-sm">(' . $nilai . '/5)</span></div>';
            return $html;
        }
    ?>

    <!-- Navigasi (Sembunyi saat Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center px-4 print:hidden">
        <a href="rapor_atlet.php" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Rapor
        </button>
    </div>

    <!-- Lembar Rapor (Kertas A4) -->
    <div class="max-w-4xl mx-auto bg-white border border-gray-200 shadow-md p-10 sm:p-14 print:shadow-none print:border-none print:p-0">
        
        <!-- Header Rapor -->
        <div class="flex items-center justify-between border-b-4 border-indigo-600 pb-6 mb-8 print-exact">
            <div class="flex items-center gap-4">
                <img src="../assets/logo.png?v=<?= time() ?>" alt="Swift SC" class="h-16 w-auto object-contain">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">RAPOR EVALUASI ATLET</h1>
                    <p class="text-indigo-600 font-bold tracking-wide">SWIFT SWIMMING CLUB INDONESIA</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-gray-500">Tanggal Cetak</p>
                <p class="text-gray-900 font-medium"><?= date('d F Y') ?></p>
            </div>
        </div>

        <!-- Profil Atlet -->
        <div class="grid grid-cols-2 gap-8 mb-10 bg-slate-50 p-6 rounded-xl border border-slate-200 print-exact">
            <div>
                <table class="text-sm">
                    <tr><td class="py-1 text-gray-500 w-32">Nama Lengkap</td><td class="font-bold text-gray-900 text-lg uppercase">: <?= htmlspecialchars($profil['nama']) ?></td></tr>
                    <tr><td class="py-1 text-gray-500">Kelompok Umur</td><td class="font-medium text-gray-900">: KU <?= htmlspecialchars($profil['kelompok_umur']) ?></td></tr>
                    <tr><td class="py-1 text-gray-500">Tanggal Lahir</td><td class="font-medium text-gray-900">: <?= date('d M Y', strtotime($profil['tanggal_lahir'])) ?></td></tr>
                </table>
            </div>
            <div>
                <table class="text-sm">
                    <tr><td class="py-1 text-gray-500 w-32">Cabang Latihan</td><td class="font-bold text-gray-900 uppercase">: <?= htmlspecialchars($profil['nama_cabang']) ?></td></tr>
                    <tr><td class="py-1 text-gray-500">Periode Evaluasi</td><td class="font-medium text-gray-900">: <?= $eval ? date('M Y', strtotime($eval['tanggal_evaluasi'])) : '-' ?></td></tr>
                    <tr><td class="py-1 text-gray-500">Nama Pelatih</td><td class="font-medium text-gray-900">: <?= $eval ? htmlspecialchars($eval['nama_pelatih']) : '-' ?></td></tr>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Kuantitatif (Kehadiran) -->
            <div class="md:col-span-1">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2 uppercase text-sm tracking-wider">Metrik Kuantitatif</h3>
                
                <div class="bg-white border border-gray-200 rounded-xl p-5 text-center shadow-sm">
                    <h4 class="text-gray-500 text-sm font-bold mb-3">Tingkat Kehadiran Latihan<br>(3 Bulan Terakhir)</h4>
                    
                    <!-- Circular Progress Visual -->
                    <div class="relative w-32 h-32 mx-auto mb-4 flex items-center justify-center rounded-full bg-slate-100 border-4 <?= $persentase >= 80 ? 'border-emerald-500' : ($persentase >= 60 ? 'border-yellow-400' : 'border-red-500') ?> print-exact">
                        <div class="text-3xl font-black text-gray-900"><?= $persentase ?>%</div>
                    </div>
                    
                    <div class="text-sm text-gray-600">
                        <span class="font-bold text-gray-900"><?= $total_hadir ?></span> hadir dari <span class="font-bold text-gray-900"><?= $total_pertemuan ?></span> jadwal
                    </div>
                    
                    <div class="mt-4 text-xs font-medium px-3 py-2 rounded-lg <?= $persentase >= 80 ? 'bg-emerald-50 text-emerald-700' : ($persentase >= 60 ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') ?> print-exact">
                        <?= $persentase >= 80 ? 'Sangat Disiplin (Sangat Baik)' : ($persentase >= 60 ? 'Kurang Disiplin (Perlu Ditingkatkan)' : 'Sering Absen (Evaluasi Kehadiran!)') ?>
                    </div>
                </div>
            </div>
            
            <!-- Kolom Kanan: Kualitatif -->
            <div class="md:col-span-2">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2 uppercase text-sm tracking-wider">Metrik Kualitatif (Skala 1-5)</h3>
                
                <?php if($eval): ?>
                    <div class="space-y-5">
                        
                        <div class="flex items-center justify-between border-b border-dashed border-gray-200 pb-3">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Postur & Streamline</h4>
                                <p class="text-xs text-gray-500">Posisi tubuh saat berenang & efisiensi gerak.</p>
                            </div>
                            <?= renderStars($eval['postur_streamline']) ?>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-dashed border-gray-200 pb-3">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Teknik Pembalikan (Turn)</h4>
                                <p class="text-xs text-gray-500">Kecepatan & akurasi tumble turn.</p>
                            </div>
                            <?= renderStars($eval['teknik_turn']) ?>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-dashed border-gray-200 pb-3">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Teknik Lompatan (Start)</h4>
                                <p class="text-xs text-gray-500">Daya ledak, reaksi, dan masuknya badan ke air.</p>
                            </div>
                            <?= renderStars($eval['teknik_start']) ?>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-dashed border-gray-200 pb-3">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Sikap & Kedisiplinan</h4>
                                <p class="text-xs text-gray-500">Fokus latihan & mental saat bertanding.</p>
                            </div>
                            <?= renderStars($eval['disiplin']) ?>
                        </div>
                        
                        <div class="mt-6">
                            <h4 class="font-bold text-gray-900 mb-2 uppercase text-xs tracking-wider text-indigo-600">Catatan Pelatih</h4>
                            <div class="bg-indigo-50 p-5 rounded-xl border border-indigo-100 print-exact">
                                <p class="text-gray-700 italic text-sm leading-relaxed">
                                    "<?= nl2br(htmlspecialchars($eval['catatan_pelatih'])) ?>"
                                </p>
                            </div>
                        </div>
                        
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-gray-500 italic">Pelatih belum menginput data evaluasi kualitatif untuk atlet ini.</p>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
        
        <!-- Footer / TTD -->
        <div class="mt-16 pt-8 flex justify-between items-end px-8">
            <div class="text-center text-sm text-gray-500">
                <p class="mb-16">Mengetahui, Orang Tua/Wali</p>
                <p class="border-t border-gray-400 pt-2 font-bold w-48 mx-auto">( ........................................ )</p>
            </div>
            <div class="text-center text-sm text-gray-500">
                <p class="mb-16">Pelatih Kepala Cabang</p>
                <p class="border-t border-gray-400 pt-2 font-bold text-gray-900 w-48 mx-auto"><?= $eval ? htmlspecialchars($eval['nama_pelatih']) : '........................................' ?></p>
            </div>
        </div>

    </div>
    
    <?php endif; ?>
</body>
</html>
