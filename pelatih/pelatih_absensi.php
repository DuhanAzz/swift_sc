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
$q_atlet = mysqli_query($koneksi, "SELECT * FROM member WHERE 1=1 AND cabang_id='$coach_cabang_id'");
if($q_atlet) {
    while($row = mysqli_fetch_assoc($q_atlet)) {
        $atlet_list[] = $row;
    }
}

// Ambil data presensi hari ini
$presensi_hari_ini = [];
$q_presensi = mysqli_query($koneksi, "SELECT * FROM absensi WHERE tanggal='$tanggal_hari_ini' AND cabang_id='$coach_cabang_id'");
if($q_presensi) {
    while($row = mysqli_fetch_assoc($q_presensi)) {
        $presensi_hari_ini[$row['member_id']] = $row['status'];
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 rounded-lg mt-14 max-w-2xl mx-auto">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses' => 'Presensi berhasil disimpan!',
                'gagal' => 'Terjadi kesalahan saat menyimpan presensi.'
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
                <h1 class="text-xl font-bold text-gray-800">Input Presensi</h1>
                <p class="text-xs text-gray-500"><?= date('l, d F Y', strtotime($tanggal_hari_ini)) ?></p>
            </div>
        </div>

        <form action="pelatih_absensi_proses.php" method="POST">
            <input type="hidden" name="tanggal" value="<?= $tanggal_hari_ini ?>">
            
            <div class="space-y-4 mb-8">
                <?php
                if(count($atlet_list) > 0) {
                    foreach($atlet_list as $atlet) {
                        $mId = $atlet['id'];
                        $current_status = $presensi_hari_ini[$mId] ?? 'Hadir'; // Default Hadir
                ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 font-bold uppercase">
                            <?= substr($atlet['nama'], 0, 1) ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($atlet['nama']) ?></h3>
                            <p class="text-xs text-gray-400 font-mono"><?= htmlspecialchars($atlet['nisn'] ?? 'NEW') ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="status[<?= $mId ?>]" value="Hadir" class="peer sr-only" <?= $current_status == 'Hadir' ? 'checked' : '' ?>>
                            <div class="text-center text-xs font-bold py-2 rounded-lg border border-gray-200 text-gray-500 peer-checked:bg-green-500 peer-checked:text-white peer-checked:border-green-500 transition-colors">Hadir</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status[<?= $mId ?>]" value="Izin" class="peer sr-only" <?= $current_status == 'Izin' ? 'checked' : '' ?>>
                            <div class="text-center text-xs font-bold py-2 rounded-lg border border-gray-200 text-gray-500 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 transition-colors">Izin</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status[<?= $mId ?>]" value="Sakit" class="peer sr-only" <?= $current_status == 'Sakit' ? 'checked' : '' ?>>
                            <div class="text-center text-xs font-bold py-2 rounded-lg border border-gray-200 text-gray-500 peer-checked:bg-yellow-500 peer-checked:text-white peer-checked:border-yellow-500 transition-colors">Sakit</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status[<?= $mId ?>]" value="Alpa" class="peer sr-only" <?= $current_status == 'Alpa' ? 'checked' : '' ?>>
                            <div class="text-center text-xs font-bold py-2 rounded-lg border border-gray-200 text-gray-500 peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 transition-colors">Alpa</div>
                        </label>
                    </div>
                </div>
                <?php 
                    } 
                } else { 
                    echo "<div class='bg-white p-8 rounded-2xl border text-center text-gray-500 text-sm'>Belum ada atlet yang ditugaskan.</div>"; 
                } 
                ?>
            </div>

            <!-- Sticky Bottom Submit Button -->
            <?php if(count($atlet_list) > 0): ?>
            <div class="fixed bottom-0 left-0 sm:left-64 right-0 p-4 bg-white/80 backdrop-blur border-t border-gray-100 z-40 pb-safe">
                <button type="submit" name="simpan_presensi" class="w-full max-w-2xl mx-auto block bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 rounded-xl shadow-lg transition-transform active:scale-95 text-lg">
                    Simpan Presensi
                </button>
            </div>
            <?php endif; ?>
        </form>
        
        <!-- Spacer for sticky footer -->
        <div class="h-24"></div>

    </div>
</div>

<style>
/* Safe area padding for mobile notch/home indicator */
.pb-safe { padding-bottom: env(safe-area-inset-bottom, 1rem); }
</style>

<?php include '../includes/footer.php'; ?>
