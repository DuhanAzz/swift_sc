<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ceo') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Ambil riwayat dokumen periodisasi
$dokumen_list = [];
$q_dokumen = mysqli_query($koneksi, "SELECT * FROM dokumen_periodisasi ORDER BY id DESC");
if ($q_dokumen) {
    while ($row = mysqli_fetch_assoc($q_dokumen)) {
        $dokumen_list[] = $row;
    }
}

// Ambil daftar kalender event
$event_list = [];
$q_event = mysqli_query($koneksi, "SELECT * FROM kalender_event ORDER BY tanggal_mulai ASC");
if ($q_event) {
    while ($row = mysqli_fetch_assoc($q_event)) {
        $event_list[] = $row;
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen bg-slate-50">
    <div class="p-4 lg:p-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Acuan Periodisasi & Kalender Event</h1>
            <p class="text-gray-500 mt-1 font-medium">Kelola dokumen panduan program latihan dan jadwal event klub.</p>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="mb-6 p-4 rounded-xl font-medium text-sm <?= strpos($_SESSION['pesan'], 'Gagal') !== false ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' ?>">
                <?= $_SESSION['pesan'] ?>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            
            <!-- SEGMEN PERIODISASI -->
            <div class="space-y-6">
                <!-- Form Upload -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-3">Upload Dokumen Periodisasi (PDF)</h3>
                    <form action="ceo_panduan_proses.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Judul Dokumen</label>
                            <input type="text" name="judul_dokumen" required class="w-full border border-gray-300 rounded-xl p-2.5 text-sm" placeholder="Contoh: Program Latihan Q3 2026">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">File PDF (Maks 5MB)</label>
                            <input type="file" name="file_pdf" accept="application/pdf" required class="w-full border border-gray-300 rounded-xl p-2 text-sm bg-gray-50">
                        </div>
                        <button type="submit" name="upload_periodisasi" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl transition-colors">
                            Upload & Jadikan Aktif
                        </button>
                    </form>
                </div>

                <!-- Riwayat Dokumen -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-x-auto">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-3">Riwayat Dokumen</h3>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="p-3 text-sm font-bold text-gray-700">Judul</th>
                                <th class="p-3 text-sm font-bold text-gray-700">Status</th>
                                <th class="p-3 text-sm font-bold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dokumen_list as $doc): ?>
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <td class="p-3 text-sm font-medium text-gray-800">
                                    <a href="../admin/uploads/periodisasi/<?= htmlspecialchars($doc['file_pdf']) ?>" target="_blank" class="text-indigo-600 hover:underline">
                                        <?= htmlspecialchars($doc['judul']) ?>
                                    </a>
                                    <div class="text-xs text-gray-500 mt-0.5"><?= date('d M Y, H:i', strtotime($doc['tanggal_upload'])) ?></div>
                                </td>
                                <td class="p-3">
                                    <?php if ($doc['status'] == 'Aktif'): ?>
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-full">Aktif</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-full">Arsip</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <form action="ceo_panduan_proses.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');">
                                        <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                                        <input type="hidden" name="file_pdf" value="<?= $doc['file_pdf'] ?>">
                                        <button type="submit" name="hapus_dokumen" class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($dokumen_list)): ?>
                            <tr><td colspan="3" class="p-4 text-center text-gray-500 text-sm">Belum ada dokumen periodisasi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SEGMEN KALENDER EVENT -->
            <div class="space-y-6">
                <!-- Form Kalender -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-3">Input Kalender Event</h3>
                    <form action="ceo_panduan_proses.php" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Event / Kompetisi</label>
                            <input type="text" name="nama_event" required class="w-full border border-gray-300 rounded-xl p-2.5 text-sm" placeholder="Contoh: Kejurda Renang Jatim 2026">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" required class="w-full border border-gray-300 rounded-xl p-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" required class="w-full border border-gray-300 rounded-xl p-2.5 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi</label>
                            <input type="text" name="lokasi" required class="w-full border border-gray-300 rounded-xl p-2.5 text-sm" placeholder="Contoh: Kolam Renang Koni Jatim">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Tambahan</label>
                            <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm" placeholder="Target perolehan medali, kualifikasi, dsb."></textarea>
                        </div>
                        <button type="submit" name="tambah_event" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition-colors">
                            Simpan Event
                        </button>
                    </form>
                </div>

                <!-- Daftar Event -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-x-auto">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-3">Daftar Event</h3>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="p-3 text-sm font-bold text-gray-700">Tanggal</th>
                                <th class="p-3 text-sm font-bold text-gray-700">Event & Lokasi</th>
                                <th class="p-3 text-sm font-bold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($event_list as $ev): ?>
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <td class="p-3 text-sm font-medium text-gray-800 whitespace-nowrap">
                                    <?= date('d/m/Y', strtotime($ev['tanggal_mulai'])) ?> 
                                    <?= ($ev['tanggal_mulai'] != $ev['tanggal_selesai']) ? '<br>s/d<br>' . date('d/m/Y', strtotime($ev['tanggal_selesai'])) : '' ?>
                                </td>
                                <td class="p-3 text-sm">
                                    <div class="font-bold text-gray-900"><?= htmlspecialchars($ev['nama_event']) ?></div>
                                    <div class="text-gray-500 text-xs mt-0.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <?= htmlspecialchars($ev['lokasi']) ?>
                                    </div>
                                    <?php if(!empty($ev['deskripsi'])): ?>
                                        <div class="mt-1 text-gray-600 text-xs italic">"<?= htmlspecialchars($ev['deskripsi']) ?>"</div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 align-top">
                                    <form action="ceo_panduan_proses.php" method="POST" onsubmit="return confirm('Hapus event ini?');">
                                        <input type="hidden" name="id_event" value="<?= $ev['id'] ?>">
                                        <button type="submit" name="hapus_event" class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($event_list)): ?>
                            <tr><td colspan="3" class="p-4 text-center text-gray-500 text-sm">Belum ada data event.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
