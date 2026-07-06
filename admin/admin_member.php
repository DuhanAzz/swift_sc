<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
include '../includes/invoice_template.php';

$admin_pool_id = $_SESSION['pool_id'] ?? '';

// Fetch Pelatih for this branch
$pelatihList = [];
$q_pel = mysqli_query($koneksi, "SELECT id, nama FROM pelatih WHERE id_kolam = '$admin_pool_id'");
if ($q_pel) {
    while($rp = mysqli_fetch_assoc($q_pel)) {
        $pelatihList[] = $rp;
    }
}

// Check if we need to show invoice modal (after approve)
$show_invoice = false;
$invoice_data = [];
if (isset($_GET['invoice_id'])) {
    $inv_id = intval($_GET['invoice_id']);
    $q_inv = mysqli_query($koneksi, "SELECT m.*, c.nama_cabang FROM member m LEFT JOIN cabang c ON m.cabang_id = c.id WHERE m.id = '$inv_id'");
    if ($q_inv && mysqli_num_rows($q_inv) > 0) {
        $invoice_data = mysqli_fetch_assoc($q_inv);
        $show_invoice = true;
    }
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses_approve' => 'Pendaftaran berhasil disetujui! Anggota baru telah ditambahkan ke Atlet.',
                'gagal' => 'Terjadi kesalahan saat memproses data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-4 mb-4 text-sm text-{$color}-800 rounded-xl bg-{$color}-50 border border-{$color}-200'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="mb-6 border-b pb-4">
            <h1 class="text-xl font-bold text-algolia-navy">Manajemen Member Cabang</h1>
            <p class="text-sm text-gray-500">Kelola pendaftaran baru dan data atlet aktif untuk cabang Anda.</p>
        </div>

        <?php
        $q_pending = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM calon_member WHERE status_approval = 'Pending' AND cabang_id = '$admin_pool_id'");
        $count_pending = mysqli_fetch_assoc($q_pending)['total'] ?? 0;

        $q_aktif = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE status_aktif = 'Aktif' AND cabang_id = '$admin_pool_id'");
        $count_aktif = mysqli_fetch_assoc($q_aktif)['total'] ?? 0;

        $q_unpaid = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE payment_status = 'Unpaid' AND cabang_id = '$admin_pool_id'");
        $count_unpaid = mysqli_fetch_assoc($q_unpaid)['total'] ?? 0;
        ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Menunggu Persetujuan</p>
                    <h3 class="text-2xl font-black text-gray-800"><?= $count_pending ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Atlet Aktif</p>
                    <h3 class="text-2xl font-black text-gray-800"><?= $count_aktif ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Belum Lunas</p>
                    <h3 class="text-2xl font-black text-gray-800"><?= $count_unpaid ?></h3>
                </div>
            </div>
        </div>

        <div class="mb-8 border-b border-[#E8E8EF]">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-blue-600 border-blue-600" id="pending-tab" data-tabs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pendaftar Baru</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-[#E8E8EF]" id="active-tab" data-tabs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false">Data Member Aktif</button>
                </li>
            </ul>
        </div>

        <div id="myTabContent">
            <!-- TAB 1: Pendaftar Baru (Pending Members) -->
            <div class="p-4 rounded-xl bg-gray-50" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="card overflow-hidden">
                    <table class="table-algolia">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4">Tanggal Daftar</th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">Jenis Kelamin</th>
                                <th class="px-6 py-4">Usia</th>
                                <th class="px-6 py-4">No WhatsApp</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pendingMembers = [];
                            try {
                                $query_pending = "SELECT * FROM calon_member WHERE status_approval = 'Pending'";
                                if(!empty($admin_pool_id)) $query_pending .= " AND cabang_id = '$admin_pool_id'";
                                $query_pending .= " ORDER BY created_at DESC";
                                $res_pending = mysqli_query($koneksi, $query_pending);
                                if ($res_pending) {
                                    while ($row = mysqli_fetch_assoc($res_pending)) {
                                        $pendingMembers[] = $row;
                                    }
                                }
                            } catch (\Exception $e) {}

                            if(count($pendingMembers) > 0) {
                                foreach($pendingMembers as $d) {
                                    // Hitung umur
                                    $umur = '-';
                                    if(!empty($d['tanggal_lahir'])) {
                                        $lahir = new DateTime($d['tanggal_lahir']);
                                        $sekarang = new DateTime('today');
                                        $umur = $lahir->diff($sekarang)->y . ' Thn';
                                    }
                            ?>
                            <tr class="bg-white border-b hover:bg-orange-50 transition-colors">
                                <td class="px-6 py-4"><?= date('d M Y', strtotime($d['created_at'] ?? '')); ?></td>
                                <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($d['nama'] ?? ''); ?></td>
                                <td class="px-6 py-4"><?= ($d['jenis_kelamin'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                <td class="px-6 py-4 font-semibold"><?= $umur; ?></td>
                                <td class="px-6 py-4">
                                    <a href="https://wa.me/<?= preg_replace('/^0/', '62', $d['no_hp'] ?? ''); ?>" target="_blank" class="text-green-600 font-bold hover:underline">
                                        <?= htmlspecialchars($d['no_hp'] ?? ''); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openApproveModal(<?= $d['id']; ?>)" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-lg shadow-sm text-[10px] uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                        Proses Pendaftar
                                    </button>
                                </td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='6' class='px-6 py-8 text-center text-slate-500 italic'>Belum ada pendaftar baru untuk cabang ini.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: Data Member Aktif (Atlet) -->
            <div class="hidden p-4 rounded-xl bg-gray-50" id="active" role="tabpanel" aria-labelledby="active-tab">
                <?php $filter_aktif = $_GET['filter_aktif'] ?? 'Aktif'; ?>
                <div class="mb-4 flex justify-end">
                    <form action="" method="GET" class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Filter Status:</label>
                        <select name="filter_aktif" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="Semua" <?= $filter_aktif == 'Semua' ? 'selected' : '' ?>>Semua</option>
                            <option value="Aktif" <?= $filter_aktif == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?= $filter_aktif == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </form>
                </div>
                <div class="card overflow-hidden">
                    <table class="table-algolia">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4">NIA / ID</th>
                                <th class="px-6 py-4">Nama Atlet & Sekolah</th>
                                <th class="px-6 py-4">Kelas & Pelatih</th>
                                <th class="px-6 py-4">Jenis Kelamin</th>
                                <th class="px-6 py-4">Tgl Bergabung</th>
                                <th class="px-6 py-4">Administrasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $activeMembers = [];
                            try {
                                $query_active = "SELECT m.*, p.nama as nama_pelatih FROM member m LEFT JOIN pelatih p ON m.pelatih_id = p.id WHERE 1=1";
                                if(!empty($admin_pool_id)) $query_active .= " AND m.cabang_id = '$admin_pool_id'";
                                if($filter_aktif != 'Semua') {
                                    $query_active .= " AND m.status_aktif = '$filter_aktif'";
                                }
                                $query_active .= " ORDER BY m.nama ASC";
                                $res_active = mysqli_query($koneksi, $query_active);
                                if ($res_active) {
                                    while ($row = mysqli_fetch_assoc($res_active)) {
                                        $activeMembers[] = $row;
                                    }
                                }
                            } catch (\Exception $e) {}

                            if(count($activeMembers) > 0) {
                                foreach($activeMembers as $d) {
                                    $nia = $d['nia'] ?? ('SWF-' . substr($d['id'], 0, 5));
                                    $payment_status = $d['payment_status'] ?? 'Unpaid';
                                    $badgeColor = ($payment_status == 'Paid') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 font-mono font-bold text-slate-700"><?= strtoupper($nia); ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800"><?= htmlspecialchars($d['nama'] ?? ''); ?></div>
                                    <div class="text-[10px] text-gray-500 mt-1"><?= htmlspecialchars($d['sekolah'] ?? '-'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 mb-1">
                                        <?= htmlspecialchars($d['tingkatan_kelas'] ?? 'Pemula'); ?>
                                    </span>
                                    <?php if(!empty($d['nama_pelatih'])): ?>
                                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                                        <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <?= htmlspecialchars($d['nama_pelatih']); ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="text-[10px] text-gray-400 italic mt-1">- Tanpa Pelatih -</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4"><?= ($d['jenis_kelamin'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                <td class="px-6 py-4"><?= isset($d['tanggal_gabung']) ? date('d M Y', strtotime($d['tanggal_gabung'])) : '-'; ?></td>
                                <td class="px-6 py-4">
                                    <?php if($payment_status == 'Unpaid'): ?>
                                        <a href="admin_member_proses.php?action=mark_paid&id=<?= $d['id'] ?>" onclick="return confirm('Tandai biaya pendaftaran sebesar Rp 100.000 telah lunas? Ini akan otomatis tercatat di arus kas.')" class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-800 hover:bg-green-600 hover:text-white transition-colors cursor-pointer inline-flex items-center gap-1 shadow-sm w-max" title="Tandai Lunas">
                                            <span>Unpaid</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </a>
                                    <?php else: ?>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-800 inline-flex items-center gap-1 w-max">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Paid
                                            </span>
                                            <a href="cetak_invoice.php?id=<?= $d['id'] ?>" target="_blank" class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors shadow-sm" title="Cetak Invoice Resmi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='6' class='px-6 py-8 text-center text-slate-500 italic'>Belum ada member aktif di cabang ini.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

</div>

<!-- ===== INVOICE MODAL ===== -->
<?php if ($show_invoice && !empty($invoice_data)): 
    $inv_nama = htmlspecialchars($invoice_data['nama']);
    $inv_nia = htmlspecialchars($invoice_data['nia']);
    $inv_cabang = htmlspecialchars($invoice_data['nama_cabang'] ?? 'Swift SC');
    $inv_tanggal = $invoice_data['tanggal_gabung'];
    $inv_no_hp = $invoice_data['no_hp'];
    $inv_phone_wa = formatPhoneWA($inv_no_hp);
    $invoice_text = generateInvoicePendaftaran($inv_nama, $inv_nia, $inv_cabang, $inv_tanggal, $inv_no_hp);
?>
<div id="invoiceModal" class="fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl shadow-slate-900/20 w-full max-w-xl mx-auto max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-[#E8E8EF] flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-algolia-navy">Invoice Pendaftaran</h3>
                <p class="text-xs text-gray-500">Member berhasil disetujui — kirim invoice pembayaran</p>
            </div>
            <button onclick="closeInvoiceModal()" class="p-1.5 hover:bg-gray-100 rounded-xl transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Invoice Preview -->
        <div class="px-6 py-4 overflow-y-auto flex-1">
            <div class="bg-gray-50 border border-[#E8E8EF] rounded-xl p-4">
                <pre id="invoiceText" class="text-xs text-gray-700 whitespace-pre-wrap font-mono leading-relaxed"><?= $invoice_text ?></pre>
            </div>
            
            <!-- Member Info Summary -->
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                    <p class="text-[10px] text-blue-600 font-semibold uppercase">Member</p>
                    <p class="text-sm font-bold text-gray-800"><?= $inv_nama ?></p>
                    <p class="text-xs text-gray-500"><?= $inv_nia ?></p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 border border-green-100">
                    <p class="text-[10px] text-green-600 font-semibold uppercase">WhatsApp</p>
                    <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($inv_no_hp) ?></p>
                    <p class="text-xs text-gray-500"><?= $inv_cabang ?></p>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="px-6 py-4 border-t border-[#E8E8EF] bg-gray-50 flex flex-col sm:flex-row gap-2">
            <button onclick="copyInvoice()" id="copyBtn" class="flex-1 flex items-center justify-center gap-2 bg-white border border-[#E8E8EF] text-gray-700 font-bold py-3 px-4 rounded-xl text-sm hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <span id="copyBtnText">Copy Invoice</span>
            </button>
            <a href="https://wa.me/<?= $inv_phone_wa ?>?text=<?= urlencode($invoice_text) ?>" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl text-sm transition-colors shadow-lg shadow-green-500/30">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                Kirim via WhatsApp
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== APPROVE MODAL ===== -->
<div id="approveModal" class="fixed inset-0 bg-black/50 z-[999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden flex flex-col">
        <form action="admin_member_proses.php" method="POST">
            <input type="hidden" name="id" id="approve_member_id">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold">Proses Pendaftar Baru</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Pilih Tingkatan Kelas</label>
                    <select name="tingkatan_kelas" class="w-full border border-gray-300 rounded p-2 focus:ring-blue-500 focus:border-blue-500 bg-white" required>
                        <option value="Pemula">Pemula</option>
                        <option value="Lanjutan">Lanjutan</option>
                        <option value="Prestasi">Prestasi</option>
                        <option value="Privat">Privat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tetapkan Pelatih</label>
                    <select name="pelatih_id" class="w-full border border-gray-300 rounded p-2 focus:ring-blue-500 focus:border-blue-500 bg-white" required>
                        <option value="">-- Pilih Pelatih --</option>
                        <?php foreach($pelatihList as $pel): ?>
                            <option value="<?= $pel['id'] ?>"><?= htmlspecialchars($pel['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Biaya Pendaftaran</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="biaya_pendaftaran" value="100000" class="w-full border border-gray-300 rounded pl-9 p-2 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Iuran Bulan 1</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="biaya_bulanan" value="350000" class="w-full border border-gray-300 rounded pl-9 p-2 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded font-bold text-xs uppercase">Batal</button>
                <button type="submit" name="approve" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold text-xs uppercase shadow-sm">Setujui Pendaftar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openApproveModal(id) {
    document.getElementById('approve_member_id').value = id;
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModal').classList.add('flex');
}
function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approveModal').classList.remove('flex');
}

function closeInvoiceModal() {
    document.getElementById('invoiceModal').style.display = 'none';
    // Clean URL
    window.history.replaceState({}, document.title, 'admin_member.php?pesan=sukses_approve');
}
function copyInvoice() {
    const text = document.getElementById('invoiceText').textContent;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyBtnText');
        btn.textContent = '✓ Tersalin!';
        setTimeout(() => { btn.textContent = 'Copy Invoice'; }, 2000);
    });
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
