<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$admin_pool_id = intval($_SESSION['pool_id'] ?? 0);
if(empty($admin_pool_id)) {
    echo '<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen"><div class="p-8"><p class="text-red-500 font-bold">Akses ditolak: Anda belum terhubung ke cabang manapun.</p></div></div>';
    include '../includes/footer.php';
    exit;
}

$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');

// Proses Tambah Pengeluaran Manual
if(isset($_POST['simpan_pengeluaran'])){
    $nominal = (float) $_POST['nominal'];
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $user_id = intval($_SESSION['user_id'] ?? 0);
    
    if($nominal > 0 && !empty($keterangan) && !empty($tanggal)){
        $q_insert = mysqli_query($koneksi, "INSERT INTO arus_kas (cabang_id, jenis, category, nominal, keterangan, tanggal, user_id) 
                                            VALUES ('$admin_pool_id', 'Pengeluaran', 'Umum', '$nominal', '$keterangan', '$tanggal', '$user_id')");
        if($q_insert) {
            header("location:arus_kas.php?tgl_mulai=$tgl_mulai&tgl_akhir=$tgl_akhir&pesan=sukses");
            exit;
        } else {
            header("location:arus_kas.php?tgl_mulai=$tgl_mulai&tgl_akhir=$tgl_akhir&pesan=gagal");
            exit;
        }
    }
}

// Proses Tambah Pemasukan Manual
if(isset($_POST['simpan_pemasukan'])){
    $nominal = (float) $_POST['nominal'];
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $user_id = intval($_SESSION['user_id'] ?? 0);
    
    if($nominal > 0 && !empty($keterangan) && !empty($tanggal)){
        $q_insert = mysqli_query($koneksi, "INSERT INTO arus_kas (cabang_id, jenis, category, nominal, keterangan, tanggal, user_id) 
                                            VALUES ('$admin_pool_id', 'Pemasukan', 'Umum', '$nominal', '$keterangan', '$tanggal', '$user_id')");
        if($q_insert) {
            header("location:arus_kas.php?tgl_mulai=$tgl_mulai&tgl_akhir=$tgl_akhir&pesan=sukses_masuk");
            exit;
        } else {
            header("location:arus_kas.php?tgl_mulai=$tgl_mulai&tgl_akhir=$tgl_akhir&pesan=gagal");
            exit;
        }
    }
}

// Hapus Transaksi (Hanya jika dibutuhkan, tapi untuk ledger baiknya ada pembatasan. Kita sediakan endpoint hapusnya)
if(isset($_GET['hapus'])){
    $id_hapus = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM arus_kas WHERE id='$id_hapus' AND cabang_id='$admin_pool_id'");
    header("location:arus_kas.php?tgl_mulai=$tgl_mulai&tgl_akhir=$tgl_akhir&pesan=hapus_sukses");
    exit;
}

// Kalkulasi Statistik berdasar Filter Tanggal
$total_pemasukan = 0;
$total_pengeluaran = 0;

$q_stat = mysqli_query($koneksi, "SELECT jenis, SUM(nominal) as total FROM arus_kas 
                                  WHERE cabang_id='$admin_pool_id' AND tanggal >= '$tgl_mulai' AND tanggal <= '$tgl_akhir' 
                                  GROUP BY jenis");
if($q_stat){
    while($r = mysqli_fetch_assoc($q_stat)){
        if($r['jenis'] == 'Pemasukan') $total_pemasukan = $r['total'];
        if($r['jenis'] == 'Pengeluaran') $total_pengeluaran = $r['total'];
    }
}
$saldo_akhir = $total_pemasukan - $total_pengeluaran;

// Mengambil Data Tabel
$transaksi = [];
$q_tabel = mysqli_query($koneksi, "SELECT a.*, u.username as nama_admin 
                                   FROM arus_kas a 
                                   LEFT JOIN users u ON a.user_id = u.id 
                                   WHERE a.cabang_id='$admin_pool_id' AND a.tanggal >= '$tgl_mulai' AND a.tanggal <= '$tgl_akhir' 
                                   ORDER BY a.tanggal DESC, a.id DESC");
if($q_tabel) {
    while($row = mysqli_fetch_assoc($q_tabel)) {
        $transaksi[] = $row;
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesan = $_GET['pesan'];
            if($pesan == "sukses") echo '<div class="p-3 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 font-medium">✅ Transaksi pengeluaran berhasil dicatat!</div>';
            if($pesan == "sukses_masuk") echo '<div class="p-3 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 font-medium">✅ Transaksi pemasukan berhasil dicatat!</div>';
            if($pesan == "hapus_sukses") echo '<div class="p-3 mb-4 text-sm text-amber-800 rounded-lg bg-amber-50 border border-amber-200 font-medium">🗑️ Transaksi berhasil dihapus.</div>';
            if($pesan == "gagal") echo '<div class="p-3 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 font-medium">❌ Terjadi kesalahan saat menyimpan transaksi.</div>';
        }
        ?>

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Arus Kas (Buku Besar)</h1>
                <p class="text-sm text-gray-500">Laporan pemasukan dan pengeluaran cabang</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="export_arus_kas.php?tgl_mulai=<?= $tgl_mulai ?>&tgl_akhir=<?= $tgl_akhir ?>" target="_blank" class="bg-white border border-[#E8E8EF] text-gray-700 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-gray-50 transition-colors">
                    <span>📊</span> Export Excel
                </a>
                <button data-modal-target="modalPemasukan" data-modal-toggle="modalPemasukan" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">
                    + Input Pemasukan
                </button>
                <button data-modal-target="modalPengeluaran" data-modal-toggle="modalPengeluaran" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">
                    - Input Pengeluaran
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card p-4 mb-6 flex flex-wrap items-end gap-4">
            <form action="arus_kas.php" method="GET" class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                    <input type="date" name="tgl_mulai" value="<?= $tgl_mulai ?>" class="bg-gray-50 border border-[#E8E8EF] text-sm rounded-lg p-2 focus:ring-algolia-blue">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="bg-gray-50 border border-[#E8E8EF] text-sm rounded-lg p-2 focus:ring-algolia-blue">
                </div>
                <div class="pb-0.5">
                    <button type="submit" class="bg-algolia-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-algolia-darkblue transition-colors">Tampilkan</button>
                </div>
            </form>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="card p-5 border-b-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Total Pemasukan</p>
                        <h3 class="text-2xl font-black text-green-600">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-500">📈</div>
                </div>
            </div>
            <div class="card p-5 border-b-4 border-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Total Pengeluaran</p>
                        <h3 class="text-2xl font-black text-red-500">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">📉</div>
                </div>
            </div>
            <div class="card p-5 border-b-4 <?= $saldo_akhir >= 0 ? 'border-blue-500' : 'border-red-600' ?>">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Saldo Akhir</p>
                        <h3 class="text-2xl font-black <?= $saldo_akhir >= 0 ? 'text-blue-600' : 'text-red-600' ?>">Rp <?= number_format($saldo_akhir, 0, ',', '.') ?></h3>
                    </div>
                    <div class="w-10 h-10 rounded-full <?= $saldo_akhir >= 0 ? 'bg-blue-50 text-blue-500' : 'bg-red-50 text-red-600' ?> flex items-center justify-center">💰</div>
                </div>
            </div>
        </div>

        <!-- Transaction Table -->
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-[#E8E8EF]">
                <h2 class="text-sm font-bold text-algolia-navy">Riwayat Transaksi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="table-algolia w-full text-left border-collapse">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                        <tr>
                            <th class="px-5 py-3 border-b border-[#E8E8EF]">Tanggal</th>
                            <th class="px-5 py-3 border-b border-[#E8E8EF]">Jenis</th>
                            <th class="px-5 py-3 border-b border-[#E8E8EF]">Keterangan</th>
                            <th class="px-5 py-3 border-b border-[#E8E8EF]">Pencatat</th>
                            <th class="px-5 py-3 border-b border-[#E8E8EF] text-right">Nominal</th>
                            <th class="px-5 py-3 border-b border-[#E8E8EF] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($transaksi) > 0): ?>
                            <?php foreach($transaksi as $t): 
                                $is_in = ($t['jenis'] == 'Pemasukan');
                                $color_cls = $is_in ? 'text-green-600' : 'text-red-500';
                                $bg_cls = $is_in ? 'bg-green-50' : 'bg-red-50';
                            ?>
                            <tr class="hover:bg-gray-50/50 border-b border-gray-100 last:border-0 transition-colors">
                                <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap"><?= date('d M Y', strtotime($t['tanggal'])) ?></td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-bold <?= $color_cls . ' ' . $bg_cls ?>">
                                        <?= $is_in ? '▲ Pemasukan' : '▼ Pengeluaran' ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-sm font-medium text-gray-800"><?= htmlspecialchars($t['keterangan']) ?></td>
                                <td class="px-5 py-3 text-xs text-gray-500"><?= htmlspecialchars($t['nama_admin'] ?? 'Sistem') ?></td>
                                <td class="px-5 py-3 text-sm font-bold <?= $color_cls ?> text-right whitespace-nowrap">
                                    <?= $is_in ? '+' : '-' ?> Rp <?= number_format($t['nominal'], 0, ',', '.') ?>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <a href="arus_kas.php?hapus=<?= $t['id'] ?>&tgl_mulai=<?= $tgl_mulai ?>&tgl_akhir=<?= $tgl_akhir ?>" onclick="return confirm('Yakin ingin menghapus transaksi ini?')" class="text-red-500 hover:bg-red-50 px-2 py-1 rounded text-xs font-bold transition-colors">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-400 italic text-sm">Tidak ada transaksi pada rentang tanggal ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Input Pengeluaran -->
<div id="modalPengeluaran" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-xl shadow-lg border border-panel-border">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-base font-bold text-algolia-navy">Input Pengeluaran Manual</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="modalPengeluaran">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="arus_kas.php" method="POST" class="p-5">
                <input type="hidden" name="tgl_mulai" value="<?= $tgl_mulai ?>">
                <input type="hidden" name="tgl_akhir" value="<?= $tgl_akhir ?>">
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-lg focus:ring-algolia-blue block w-full p-2.5" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nominal (Rp)</label>
                    <input type="number" name="nominal" placeholder="Contoh: 150000" min="1" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-lg focus:ring-algolia-blue block w-full p-2.5" required>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Keterangan / Tujuan Pengeluaran</label>
                    <textarea name="keterangan" rows="3" placeholder="Contoh: Beli alat kebersihan kolam..." class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-lg focus:ring-algolia-blue block w-full p-2.5" required></textarea>
                </div>
                
                <button type="submit" name="simpan_pengeluaran" class="w-full text-white bg-red-500 hover:bg-red-600 font-bold rounded-lg text-sm px-5 py-3 transition-colors shadow-sm">
                    Simpan Pengeluaran
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Input Pemasukan -->
<div id="modalPemasukan" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-xl shadow-lg border border-panel-border">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-base font-bold text-algolia-navy">Input Pemasukan Manual</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="modalPemasukan">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="arus_kas.php" method="POST" class="p-5">
                <input type="hidden" name="tgl_mulai" value="<?= $tgl_mulai ?>">
                <input type="hidden" name="tgl_akhir" value="<?= $tgl_akhir ?>">
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-lg focus:ring-algolia-blue block w-full p-2.5" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nominal (Rp)</label>
                    <input type="number" name="nominal" placeholder="Contoh: 150000" min="1" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-lg focus:ring-algolia-blue block w-full p-2.5" required>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Keterangan / Sumber Pemasukan</label>
                    <textarea name="keterangan" rows="3" placeholder="Contoh: Pembayaran SPP bulan Maret via Transfer..." class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-lg focus:ring-algolia-blue block w-full p-2.5" required></textarea>
                </div>
                
                <button type="submit" name="simpan_pemasukan" class="w-full text-white bg-emerald-500 hover:bg-emerald-600 font-bold rounded-lg text-sm px-5 py-3 transition-colors shadow-sm">
                    Simpan Pemasukan
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>