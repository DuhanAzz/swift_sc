<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelatih') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$pelatih_id = $_SESSION['user_id'];
$cabang_id = $_SESSION['cabang_id'];

// Ambil data event dari kalender_event
$event_list = [];
$q_event = mysqli_query($koneksi, "SELECT id, nama_event, tanggal_mulai, lokasi FROM kalender_event ORDER BY tanggal_mulai DESC");
if ($q_event) {
    while ($row = mysqli_fetch_assoc($q_event)) {
        $event_list[] = $row;
    }
}

// Ambil data atlet berdasarkan cabang pelatih
$atlet_list = [];
$q_atlet = mysqli_query($koneksi, "SELECT id, nama, kelompok_umur FROM member WHERE role='atlet' AND cabang_id='$cabang_id' ORDER BY nama ASC");
if ($q_atlet) {
    while ($row = mysqli_fetch_assoc($q_atlet)) {
        $atlet_list[] = $row;
    }
}

$gaya_renang = ['Bebas', 'Dada', 'Punggung', 'Kupu-kupu', 'Ganti'];
$jarak_lomba = [50, 100, 200, 400, 800, 1500];
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen bg-slate-50">
    <div class="p-4 lg:p-8 max-w-4xl mx-auto">
        
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Input Race Pace</h1>
                <p class="text-gray-500 mt-1 font-medium">Rekam performa waktu atlet beserta detail split time per 50m.</p>
            </div>
            <a href="performa.php" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded-xl shadow-sm text-sm transition-colors">
                Riwayat Performa
            </a>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="mb-6 p-4 rounded-xl font-medium text-sm <?= strpos($_SESSION['pesan'], 'Gagal') !== false ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' ?> flex items-center justify-between">
                <div><?= $_SESSION['pesan'] ?></div>
                <button onclick="this.parentElement.remove()" class="text-opacity-50 hover:text-opacity-100">&times;</button>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="pelatih_input_lomba_proses.php" method="POST" id="formRacePace" class="p-6 sm:p-8">
                
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Data Utama Lomba
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Event -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Event Kompetisi</label>
                        <select name="event_id" required class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
                            <option value="">-- Pilih Event --</option>
                            <?php foreach($event_list as $ev): ?>
                                <option value="<?= $ev['id'] ?>"><?= htmlspecialchars($ev['nama_event']) ?> (<?= date('d M Y', strtotime($ev['tanggal_mulai'])) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Atlet -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Atlet</label>
                        <select name="atlet_id" required class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
                            <option value="">-- Pilih Atlet --</option>
                            <?php foreach($atlet_list as $at): ?>
                                <option value="<?= $at['id'] ?>"><?= htmlspecialchars($at['nama']) ?> (KU <?= htmlspecialchars($at['kelompok_umur']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Gaya Renang -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Gaya Renang</label>
                        <select name="gaya_renang" required class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
                            <option value="">-- Pilih Gaya --</option>
                            <?php foreach($gaya_renang as $g): ?>
                                <option value="<?= $g ?>"><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Lintasan -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Lintasan</label>
                        <select name="lintasan" required class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
                            <option value="">-- Pilih Lintasan --</option>
                            <?php for($i=0; $i<=9; $i++): ?>
                                <option value="Lintasan <?= $i ?>">Lintasan <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <!-- Jarak -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jarak Total (Meter)</label>
                        <select name="jarak" id="jarakSelect" required class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer bg-slate-50 font-bold">
                            <option value="">-- Pilih Jarak Lomba --</option>
                            <?php foreach($jarak_lomba as $j): ?>
                                <option value="<?= $j ?>"><?= $j ?>m</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="splitContainerWrapper" class="hidden">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 border-b pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Input Split Time
                    </h3>
                    
                    <div class="bg-slate-50 p-4 sm:p-6 rounded-xl border border-slate-200 mb-8">
                        <div class="flex flex-col gap-4" id="splitInputsBox">
                            <!-- JS akan merender input split di sini -->
                        </div>
                    </div>
                    
                    <!-- Tanggal Rekam (Otomatis hari ini jika tidak diubah) -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Lomba / Rekam</label>
                        <input type="date" name="tanggal_rekor" value="<?= date('Y-m-d') ?>" required class="w-full md:w-1/2 border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Lomba (Opsional)</label>
                        <textarea name="catatan" rows="3" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Misal: Kurang maksimal di putaran terakhir, start bagus."></textarea>
                    </div>

                    <button type="submit" name="simpan_performa" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 transition-all text-lg">
                        Simpan Data Race Pace
                    </button>
                </div>
                
                <div id="placeholderSplit" class="py-12 text-center border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                    <p class="text-gray-500 font-medium">Pilih "Jarak Total" terlebih dahulu untuk memasukkan Split Time.</p>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jarakSelect = document.getElementById('jarakSelect');
        const splitContainerWrapper = document.getElementById('splitContainerWrapper');
        const splitInputsBox = document.getElementById('splitInputsBox');
        const placeholderSplit = document.getElementById('placeholderSplit');

        jarakSelect.addEventListener('change', function() {
            const jarak = parseInt(this.value);
            splitInputsBox.innerHTML = ''; // Kosongkan dulu
            
            if (isNaN(jarak) || jarak <= 0) {
                splitContainerWrapper.classList.add('hidden');
                placeholderSplit.classList.remove('hidden');
                return;
            }

            splitContainerWrapper.classList.remove('hidden');
            placeholderSplit.classList.add('hidden');

            const jumlahSplit = Math.floor(jarak / 50);
            
            for (let i = 1; i <= jumlahSplit; i++) {
                const meter = i * 50;
                
                // HTML Input Split
                const div = document.createElement('div');
                div.className = 'flex items-center gap-4 bg-white p-3 sm:p-4 rounded-lg border border-gray-200 shadow-sm';
                
                div.innerHTML = `
                    <div class="w-24 sm:w-32 shrink-0 font-bold text-slate-700 text-sm sm:text-base">
                        Lap ${meter}m
                        <input type="hidden" name="split_jarak[]" value="${meter}">
                    </div>
                    <div class="flex-1 relative">
                        <input type="text" name="split_waktu[]" required placeholder="00:00.00" class="w-full font-mono text-center sm:text-left text-lg tracking-wider font-bold border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all uppercase time-input" autocomplete="off" maxlength="8">
                    </div>
                `;
                splitInputsBox.appendChild(div);
            }
            
            initTimeFormatting();
        });

        // Format MM:SS.ms Otomatis saat mengetik
        function initTimeFormatting() {
            const inputs = document.querySelectorAll('.time-input');
            inputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let val = this.value.replace(/[^0-9]/g, ''); // hanya angka
                    
                    if (val.length > 6) val = val.substring(0, 6);
                    
                    let formatted = '';
                    if (val.length > 0) {
                        formatted += val.substring(0, 2);
                    }
                    if (val.length > 2) {
                        formatted += ':' + val.substring(2, 4);
                    }
                    if (val.length > 4) {
                        formatted += '.' + val.substring(4, 6);
                    }
                    
                    this.value = formatted;
                });
            });
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
