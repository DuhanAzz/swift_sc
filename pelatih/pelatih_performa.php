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
$tanggal_hari_ini = date('Y-m-d');

// Ambil semua atlet di cabang ini
$atlet_list = [];
$q_atlet = mysqli_query($koneksi, "SELECT * FROM member WHERE 1=1 AND cabang_id='$coach_cabang_id' ORDER BY nama ASC");
if($q_atlet) {
    while($row = mysqli_fetch_assoc($q_atlet)) {
        $atlet_list[] = $row;
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses' => 'Catatan performa berhasil disimpan!',
                'gagal' => 'Terjadi kesalahan saat menyimpan data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-4 mb-4 text-sm text-{$color}-800 rounded-lg bg-{$color}-50 border border-{$color}-200'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="mb-6 flex items-center gap-3">
            <a href="pelatih_dashboard.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 p-2 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Catat Performa Atlet</h1>
                <p class="text-xs text-gray-500">Rekam waktu tempuh renang</p>
            </div>
        </div>

        <div class="card p-6">
            <form action="pelatih_performa_proses.php" method="POST" class="space-y-4">
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal</label>
                    <input type="date" name="record_date" value="<?= $tanggal_hari_ini ?>" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Atlet</label>
                    <select name="member_id" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3" required>
                        <option value="">-- Sentuh untuk memilih atlet --</option>
                        <?php foreach($atlet_list as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?> (<?= htmlspecialchars($a['nisn'] ?? 'NEW') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Gaya Renang</label>
                        <select name="swim_style" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3" required>
                            <option value="Bebas (Freestyle)">Bebas</option>
                            <option value="Dada (Breaststroke)">Dada</option>
                            <option value="Punggung (Backstroke)">Punggung</option>
                            <option value="Kupu-kupu (Butterfly)">Kupu-kupu</option>
                            <option value="Ganti (IM)">Ganti (IM)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Jarak (m)</label>
                        <select name="distance" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3" required>
                            <option value="25">25m</option>
                            <option value="50">50m</option>
                            <option value="100">100m</option>
                            <option value="200">200m</option>
                            <option value="400">400m</option>
                            <option value="800">800m</option>
                            <option value="1500">1500m</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Waktu Tempuh</label>
                    <div class="flex gap-2">
                        <input type="number" name="time_m" placeholder="Menit" min="0" max="59" class="w-1/3 bg-slate-50 border border-slate-200 text-slate-900 text-center rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 font-mono text-lg" required>
                        <span class="flex items-center text-xl font-bold">:</span>
                        <input type="number" name="time_s" placeholder="Detik" min="0" max="59" class="w-1/3 bg-slate-50 border border-slate-200 text-slate-900 text-center rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 font-mono text-lg" required>
                        <span class="flex items-center text-xl font-bold">.</span>
                        <input type="number" name="time_ms" placeholder="MS" min="0" max="99" class="w-1/3 bg-slate-50 border border-slate-200 text-slate-900 text-center rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 font-mono text-lg" required>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Format: Menit : Detik . Milidetik</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Catatan Pelatih (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Teknik pernapasan perlu diperbaiki, tolakan start bagus." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3"></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" name="simpan" class="w-full bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-4 rounded-xl shadow-lg transition-transform active:scale-95 text-lg">
                        Simpan Rekor
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
