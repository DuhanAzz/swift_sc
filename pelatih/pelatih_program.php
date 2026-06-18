<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || ($_SESSION['role'] != 'coach' && $_SESSION['role'] != 'pelatih')) { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$coach_cabang_id = $_SESSION['cabang'] ?? '';
$coach_id = $_SESSION['user_id'] ?? 0;
$coach_name = $_SESSION['name'] ?? 'Pelatih';
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 rounded-lg mt-14 max-w-4xl mx-auto">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses' => 'Jurnal/Program latihan berhasil disimpan!',
                'hapus' => 'Jurnal latihan berhasil dihapus!',
                'gagal' => 'Terjadi kesalahan saat memproses data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-4 mb-4 text-sm text-{$color}-800 rounded-lg bg-{$color}-50 border border-{$color}-200'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Jurnal & Program Latihan</h1>
                <p class="text-sm text-gray-500">Catat dan pantau menu latihan harian.</p>
            </div>
            <button data-modal-target="modalTambahProgram" data-modal-toggle="modalTambahProgram" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-md">+ Buat Jurnal Baru</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $programs = [];
            $q_prog = mysqli_query($koneksi, "SELECT * FROM program_latihan WHERE pelatih_id='$coach_id' AND cabang_id='$coach_cabang_id' ORDER BY tanggal DESC");
            if($q_prog) {
                while($row = mysqli_fetch_assoc($q_prog)) {
                    $programs[] = $row;
                }
            }

            if(count($programs) > 0) {
                foreach($programs as $p) {
                    $targetClass = 'bg-gray-100 text-gray-800';
                    if(($p['target_grup'] ?? '') == 'Prestasi') $targetClass = 'bg-orange-100 text-orange-800';
                    if(($p['target_grup'] ?? '') == 'Pemula') $targetClass = 'bg-green-100 text-green-800';
            ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative group hover:shadow-lg transition-all">
                <a href="pelatih_program_proses.php?hapus=<?= $p['id'] ?>" onclick="return confirm('Hapus jurnal ini?')" class="absolute top-4 right-4 text-gray-300 hover:text-red-500 transition-colors hidden group-hover:block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </a>
                
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs font-bold px-2 py-1 rounded <?= $targetClass ?>"><?= htmlspecialchars($p['target_grup'] ?? 'Umum') ?></span>
                    <span class="text-xs text-gray-400"><?= isset($p['tanggal']) ? date('d M Y', strtotime($p['tanggal'])) : '-' ?></span>
                </div>
                
                <h3 class="font-bold text-lg text-gray-800 mb-2 leading-tight"><?= htmlspecialchars($p['judul'] ?? 'Program Latihan') ?></h3>
                
                <div class="text-sm text-gray-600 mb-4 line-clamp-4 prose prose-sm">
                    <?= nl2br(htmlspecialchars($p['deskripsi'] ?? '')) ?>
                </div>
                
                <p class="text-[10px] text-gray-400 mt-auto pt-4 border-t border-gray-50 uppercase tracking-wider">Oleh: <?= htmlspecialchars($coach_name) ?></p>
            </div>
            <?php } } else { ?>
                <div class="col-span-full bg-white p-12 rounded-2xl border border-gray-100 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <p>Belum ada jurnal latihan yang Anda buat.</p>
                </div>
            <?php } ?>
        </div>

    </div>
</div>

<!-- Modal Tambah Program -->
<div id="modalTambahProgram" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <h3 class="text-lg font-bold text-gray-800">Tulis Jurnal Baru</h3>
            <button data-modal-toggle="modalTambahProgram" class="text-gray-400 hover:text-gray-900 bg-gray-100 p-2 rounded-full">✖</button>
        </div>
        <form action="pelatih_program_proses.php" method="POST" class="space-y-4">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Latihan</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:ring-purple-500 focus:border-purple-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Target Grup</label>
                    <select name="target_grup" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="Semua (Gabungan)">Semua (Gabungan)</option>
                        <option value="Pemula">Pemula</option>
                        <option value="Prestasi">Prestasi</option>
                        <option value="Dewasa / TNI-Polri">Dewasa / TNI-Polri</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Judul Fokus Latihan</label>
                <input type="text" name="judul" placeholder="Contoh: Fokus Daya Tahan (Endurance) 800m" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:ring-purple-500 focus:border-purple-500" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Deskripsi / Menu Latihan</label>
                <textarea name="deskripsi" rows="6" placeholder="Contoh:&#10;1. Pemanasan 400m bebas&#10;2. Drill teknik 4x50m&#10;3. Inti 8x100m interval 2:00&#10;4. Pendinginan 200m santai" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm" required></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" name="simpan" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-xl shadow-lg transition-transform active:scale-95 text-lg">
                    Simpan Jurnal
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
