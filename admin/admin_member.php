<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$admin_pool_id = $_SESSION['pool_id'] ?? '';
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses_approve' => 'Pendaftaran berhasil disetujui! Anggota baru telah ditambahkan ke Atlet.',
                'gagal' => 'Terjadi kesalahan saat memproses data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-4 mb-4 text-sm text-{$color}-800 rounded-lg bg-{$color}-50 border border-{$color}-200'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Member Cabang</h1>
            <p class="text-sm text-gray-500">Kelola pendaftaran baru dan data atlet aktif untuk cabang Anda.</p>
        </div>

        <div class="mb-8 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-blue-600 border-blue-600" id="pending-tab" data-tabs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pendaftar Baru</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="active-tab" data-tabs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false">Data Member Aktif</button>
                </li>
            </ul>
        </div>

        <div id="myTabContent">
            <!-- TAB 1: Pendaftar Baru (Pending Members) -->
            <div class="p-4 rounded-lg bg-gray-50" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-white uppercase bg-slate-800">
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
                                $query_pending = "SELECT * FROM calon_member WHERE status_approval = 'Pending' AND cabang_id = '$admin_pool_id' ORDER BY created_at DESC";
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
                                    <form action="admin_member_proses.php" method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?= $d['id']; ?>">
                                        <button type="submit" name="approve" onclick="return confirm('Setujui pendaftar ini menjadi atlet resmi?')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow text-xs uppercase tracking-wider">Approve</button>
                                    </form>
                                </td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='6' class='px-6 py-8 text-center text-slate-500 italic'>Belum ada pendaftar baru untuk cabang ini.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: Data Member Aktif (Atlet) -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="active" role="tabpanel" aria-labelledby="active-tab">
                <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-white uppercase bg-slate-800">
                            <tr>
                                <th class="px-6 py-4">NIA / ID</th>
                                <th class="px-6 py-4">Nama Atlet</th>
                                <th class="px-6 py-4">Jenis Kelamin</th>
                                <th class="px-6 py-4">Tgl Bergabung</th>
                                <th class="px-6 py-4">Status Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $activeMembers = [];
                            try {
                                $query_active = "SELECT * FROM member WHERE cabang_id = '$admin_pool_id' ORDER BY nama ASC";
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
                                <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($d['nama'] ?? ''); ?></td>
                                <td class="px-6 py-4"><?= ($d['jenis_kelamin'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                <td class="px-6 py-4"><?= isset($d['tanggal_gabung']) ? date('d M Y', strtotime($d['tanggal_gabung'])) : '-'; ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $badgeColor ?>"><?= $payment_status ?></span>
                                </td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='5' class='px-6 py-8 text-center text-slate-500 italic'>Belum ada member aktif di cabang ini.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
