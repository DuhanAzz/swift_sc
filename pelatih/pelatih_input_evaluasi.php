<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelatih') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$cabang_id = $_SESSION['cabang_id'];
$pelatih_id = $_SESSION['user_id'];

// Ambil data atlet berdasarkan cabang pelatih
$atlet_list = [];
$q_atlet = mysqli_query($koneksi, "SELECT id, nama, kelompok_umur FROM member WHERE role='atlet' AND cabang_id='$cabang_id' ORDER BY nama ASC");
if ($q_atlet) {
    while ($row = mysqli_fetch_assoc($q_atlet)) {
        $atlet_list[] = $row;
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen bg-slate-50">
    <div class="p-4 lg:p-8 max-w-4xl mx-auto">
        
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Input Evaluasi Atlet</h1>
                <p class="text-gray-500 mt-1 font-medium">Beri penilaian kualitatif (Skala 1-5) pada teknik dan kedisiplinan atlet.</p>
            </div>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="mb-6 p-4 rounded-xl font-medium text-sm <?= strpos($_SESSION['pesan'], 'Gagal') !== false ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' ?> flex items-center justify-between">
                <div><?= $_SESSION['pesan'] ?></div>
                <button onclick="this.parentElement.remove()" class="text-opacity-50 hover:text-opacity-100">&times;</button>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="pelatih_evaluasi_proses.php" method="POST" class="p-6 sm:p-8">
                
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Data Atlet
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Atlet -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Atlet</label>
                        <select name="member_id" required class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer bg-slate-50 font-bold">
                            <option value="">-- Pilih Atlet --</option>
                            <?php foreach($atlet_list as $at): ?>
                                <option value="<?= $at['id'] ?>"><?= htmlspecialchars($at['nama']) ?> (KU <?= htmlspecialchars($at['kelompok_umur']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Evaluasi</label>
                        <input type="date" name="tanggal_evaluasi" value="<?= date('Y-m-d') ?>" required class="w-full md:w-1/2 border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    Metrik Penilaian Kualitatif (Skala 1 - 5)
                </h3>
                
                <div class="space-y-8 bg-slate-50 p-6 rounded-xl border border-slate-200 mb-8">
                    
                    <!-- Postur Streamline -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-800">Postur & Streamline</label>
                            <span id="val_streamline" class="font-bold text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full text-xs">Nilai: 3</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Posisi tubuh saat berenang, seberapa rata dan minim hambatan air.</p>
                        <input type="range" name="postur_streamline" min="1" max="5" value="3" step="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('val_streamline').textContent = 'Nilai: ' + this.value">
                        <div class="flex justify-between text-xs text-gray-400 mt-2 font-medium">
                            <span>1 (Buruk)</span><span>2</span><span>3 (Cukup)</span><span>4</span><span>5 (Sempurna)</span>
                        </div>
                    </div>
                    
                    <hr class="border-gray-200">
                    
                    <!-- Teknik Turn -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-800">Teknik Turn (Pembalikan)</label>
                            <span id="val_turn" class="font-bold text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full text-xs">Nilai: 3</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Kecepatan, akurasi tumble turn, dan daya tolak dinding.</p>
                        <input type="range" name="teknik_turn" min="1" max="5" value="3" step="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('val_turn').textContent = 'Nilai: ' + this.value">
                        <div class="flex justify-between text-xs text-gray-400 mt-2 font-medium">
                            <span>1 (Buruk)</span><span>2</span><span>3 (Cukup)</span><span>4</span><span>5 (Sempurna)</span>
                        </div>
                    </div>
                    
                    <hr class="border-gray-200">
                    
                    <!-- Teknik Start -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-800">Teknik Start (Lompatan)</label>
                            <span id="val_start" class="font-bold text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full text-xs">Nilai: 3</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Reaksi saat peluit, daya ledak lompatan, dan sudut masuk ke air.</p>
                        <input type="range" name="teknik_start" min="1" max="5" value="3" step="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('val_start').textContent = 'Nilai: ' + this.value">
                        <div class="flex justify-between text-xs text-gray-400 mt-2 font-medium">
                            <span>1 (Buruk)</span><span>2</span><span>3 (Cukup)</span><span>4</span><span>5 (Sempurna)</span>
                        </div>
                    </div>
                    
                    <hr class="border-gray-200">
                    
                    <!-- Disiplin -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-800">Sikap & Kedisiplinan</label>
                            <span id="val_disiplin" class="font-bold text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full text-xs">Nilai: 3</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Fokus saat latihan, sikap terhadap pelatih dan rekan tim.</p>
                        <input type="range" name="disiplin" min="1" max="5" value="3" step="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600" oninput="document.getElementById('val_disiplin').textContent = 'Nilai: ' + this.value">
                        <div class="flex justify-between text-xs text-gray-400 mt-2 font-medium">
                            <span>1 (Buruk)</span><span>2</span><span>3 (Cukup)</span><span>4</span><span>5 (Sempurna)</span>
                        </div>
                    </div>

                </div>
                
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Pelatih / Saran Pengembangan</label>
                    <textarea name="catatan_pelatih" rows="4" required class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Misal: Perlu lebih banyak drill kaki gaya bebas, sudah bagus di ketahanan..."></textarea>
                </div>

                <button type="submit" name="simpan_evaluasi" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 transition-all text-lg">
                    Simpan Evaluasi
                </button>

            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
