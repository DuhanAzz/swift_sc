<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelatih') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil dokumen periodisasi Aktif
$dokumen_aktif = null;
$q_dok_aktif = mysqli_query($koneksi, "SELECT * FROM dokumen_periodisasi WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
if ($q_dok_aktif && mysqli_num_rows($q_dok_aktif) > 0) {
    $dokumen_aktif = mysqli_fetch_assoc($q_dok_aktif);
}

// Ambil kalender event yang akan datang (>= hari ini)
$event_mendatang = [];
$hari_ini = date('Y-m-d');
$q_event = mysqli_query($koneksi, "SELECT * FROM kalender_event WHERE tanggal_mulai >= '$hari_ini' ORDER BY tanggal_mulai ASC");
if ($q_event) {
    while ($row = mysqli_fetch_assoc($q_event)) {
        $event_mendatang[] = $row;
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen bg-slate-50">
    <div class="p-4 lg:p-8 max-w-7xl mx-auto">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Pusat Panduan Pelatih</h1>
            <p class="text-gray-500 mt-1 font-medium">Acuan periodisasi program latihan dan kalender event mendatang.</p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <!-- VIEW PDF PERIODISASI (Kiri, lebih lebar) -->
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 border-b pb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Program Latihan (Periodisasi)</h2>
                            <?php if($dokumen_aktif): ?>
                                <p class="text-sm text-gray-500 mt-1">Dokumen Aktif: <span class="font-bold text-indigo-600"><?= htmlspecialchars($dokumen_aktif['judul']) ?></span></p>
                            <?php endif; ?>
                        </div>
                        <?php if($dokumen_aktif): ?>
                            <a href="../admin/uploads/periodisasi/<?= htmlspecialchars($dokumen_aktif['file_pdf']) ?>" download class="hidden sm:inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-4 py-2 rounded-xl text-sm font-bold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download PDF
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if ($dokumen_aktif): ?>
                        <div class="w-full bg-slate-100 rounded-xl overflow-hidden border border-gray-200" style="min-height: 600px;">
                            <embed src="../admin/uploads/periodisasi/<?= htmlspecialchars($dokumen_aktif['file_pdf']) ?>#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="600px" class="w-full h-[600px] md:h-[800px]" />
                        </div>
                        <div class="mt-4 sm:hidden">
                            <a href="../admin/uploads/periodisasi/<?= htmlspecialchars($dokumen_aktif['file_pdf']) ?>" download class="w-full inline-flex justify-center items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-3 rounded-xl text-sm font-bold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download PDF
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-64 bg-slate-50 rounded-xl border border-dashed border-gray-300">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-gray-500 font-medium">Belum ada dokumen periodisasi aktif dari pusat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TIMELINE EVENT (Kanan, lebih sempit) -->
            <div class="xl:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Kalender Event Mendatang
                    </h2>

                    <div class="relative border-l-2 border-slate-200 ml-3 space-y-8 pb-4">
                        <?php foreach ($event_mendatang as $index => $ev): 
                            $date_mulai = new DateTime($ev['tanggal_mulai']);
                            $date_selesai = new DateTime($ev['tanggal_selesai']);
                            $is_today = ($date_mulai->format('Y-m-d') == date('Y-m-d'));
                        ?>
                        <div class="relative pl-6">
                            <!-- Dot -->
                            <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-white <?= $is_today ? 'bg-emerald-500 animate-pulse' : 'bg-indigo-500' ?>"></div>
                            
                            <!-- Card Content -->
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all">
                                <div class="text-xs font-bold <?= $is_today ? 'text-emerald-600' : 'text-indigo-600' ?> mb-1">
                                    <?= $date_mulai->format('d M Y') ?>
                                    <?= ($ev['tanggal_mulai'] != $ev['tanggal_selesai']) ? ' - ' . $date_selesai->format('d M Y') : '' ?>
                                </div>
                                <h3 class="font-bold text-gray-900 leading-tight mb-1"><?= htmlspecialchars($ev['nama_event']) ?></h3>
                                <div class="flex items-start gap-1.5 text-gray-500 text-xs mt-2">
                                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="leading-tight"><?= htmlspecialchars($ev['lokasi']) ?></span>
                                </div>
                                <?php if(!empty($ev['deskripsi'])): ?>
                                <div class="mt-3 text-sm text-gray-600 bg-white p-2.5 rounded-lg border border-slate-100 shadow-sm italic">
                                    "<?= htmlspecialchars($ev['deskripsi']) ?>"
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($event_mendatang)): ?>
                        <div class="pl-6">
                            <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-white bg-gray-300"></div>
                            <p class="text-sm text-gray-500 font-medium italic">Tidak ada event dalam waktu dekat.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
