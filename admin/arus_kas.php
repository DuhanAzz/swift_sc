<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// Cek ID User yang sedang login
$user_id_login = $_SESSION['user_id'] ?? 0;
$admin_pool_id = $_SESSION['pool_id'] ?? '';
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "sukses_tambah"){
                echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">Transaksi berhasil dicatat!</div>';
            } else if($_GET['pesan'] == "sukses_edit"){
                echo '<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200">Transaksi berhasil diupdate!</div>';
            } else if($_GET['pesan'] == "sukses_hapus"){
                echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">Transaksi berhasil dihapus!</div>';
            }
        }
        ?>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Arus Kas & Keuangan</h1>
                <p class="text-sm text-gray-500">Pencatatan Pemasukan dan Pengeluaran Kolam</p>
            </div>
            <button data-modal-target="modalTambahKas" data-modal-toggle="modalTambahKas" class="text-white bg-slate-800 hover:bg-slate-900 font-medium rounded-lg text-sm px-5 py-2.5 transition-all shadow-md">
                + Catat Transaksi
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-white uppercase bg-slate-800">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-right">Nominal (Rp)</th>
                        <th class="px-6 py-4">Lokasi & Admin</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $kasArray = [];
                    $q_str = "SELECT c.*, b.nama_cabang as nama_kolam, u.username as nama_admin 
                              FROM cash_flows c 
                              LEFT JOIN cabang b ON c.pool_id = b.id 
                              LEFT JOIN users u ON c.user_id = u.id ";
                    if(!empty($admin_pool_id)) {
                        $q_str .= " WHERE c.pool_id = '$admin_pool_id'";
                    }
                    $q_str .= " ORDER BY c.transaction_date DESC, c.id DESC";
                    
                    $q_kas = mysqli_query($koneksi, $q_str);
                    if($q_kas) {
                        while($row = mysqli_fetch_assoc($q_kas)) {
                            $kasArray[] = $row;
                        }
                    }

                    if(count($kasArray) > 0) {
                        foreach($kasArray as $data) {
                            $type_bg = ($data['type'] == 'Pemasukan') ? 'bg-green-100 text-green-800 border-green-400' : 'bg-red-100 text-red-800 border-red-400';
                            $text_color = ($data['type'] == 'Pemasukan') ? 'text-green-600' : 'text-red-600';
                    ?>
                    <tr class="bg-white border-b hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= date('d M Y', strtotime($data['transaction_date'])); ?></td>
                        <td class="px-6 py-4"><span class="text-xs font-medium px-2.5 py-0.5 rounded border <?= $type_bg; ?>"><?= htmlspecialchars($data['type']); ?></span></td>
                        <td class="px-6 py-4 font-semibold text-gray-700"><?= htmlspecialchars($data['category']); ?></td>
                        <td class="px-6 py-4 truncate max-w-xs"><?= htmlspecialchars($data['description']); ?></td>
                        <td class="px-6 py-4 font-bold text-right <?= $text_color; ?>"><?= number_format($data['amount'], 0, ',', '.'); ?></td>
                        <td class="px-6 py-4 text-xs">
                            <div class="font-bold text-slate-700"><?= htmlspecialchars($data['nama_kolam'] ?? '-'); ?></div>
                            <div class="text-gray-400">Oleh: <?= htmlspecialchars($data['nama_admin'] ?? '-'); ?></div>
                        </td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <button data-modal-target="modalEditKas<?= $data['id']; ?>" data-modal-toggle="modalEditKas<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                            <a href="hapus_arus_kas.php?id=<?= $data['id']; ?>" onclick="return confirm('Yakin hapus transaksi ini?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>

                    <div id="modalEditKas<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100">
                                <div class="flex items-center justify-between p-5 border-b">
                                    <h3 class="text-lg font-bold text-gray-800">Edit Transaksi</h3>
                                    <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto" data-modal-toggle="modalEditKas<?= $data['id']; ?>">✖</button>
                                </div>
                                <form action="proses_arus_kas.php" method="POST" class="p-6 text-left">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tanggal</label>
                                            <input type="date" name="transaction_date" value="<?= $data['transaction_date']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tipe</label>
                                            <select name="type" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                                                <option value="Pemasukan" <?= ($data['type'] == 'Pemasukan') ? 'selected' : '' ?>>Pemasukan</option>
                                                <option value="Pengeluaran" <?= ($data['type'] == 'Pengeluaran') ? 'selected' : '' ?>>Pengeluaran</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Kategori</label>
                                        <input type="text" name="category" value="<?= $data['category']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required placeholder="Cth: SPP Bulanan, Gaji">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Nominal (Rp)</label>
                                        <input type="number" name="amount" value="<?= $data['amount']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                                    </div>
                                    <div class="mb-6">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Keterangan</label>
                                        <textarea name="description" rows="3" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3"><?= htmlspecialchars($data['description']); ?></textarea>
                                    </div>
                                    <button type="submit" name="edit" class="w-full text-white bg-slate-800 hover:bg-slate-900 font-bold rounded-xl text-sm px-5 py-3">Update Transaksi</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php 
                        } 
                    } else {
                        echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 py-6">Belum ada data transaksi keuangan.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalTambahKas" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="text-lg font-bold text-gray-800">Catat Transaksi Baru</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto" data-modal-toggle="modalTambahKas">✖</button>
            </div>
            <form action="proses_arus_kas.php" method="POST" class="p-6 text-left">
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Cabang Kolam</label>
                    <select name="pool_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                        <?php
                        $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                        if($q_kolam) {
                            while($k = mysqli_fetch_assoc($q_kolam)) {
                                $k_id = $k['id'];
                                $sel = ($k_id == $admin_pool_id) ? 'selected' : '';
                                echo "<option value='".$k_id."' $sel>".htmlspecialchars($k['nama_cabang'])."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tanggal</label>
                        <input type="date" name="transaction_date" value="<?= date('Y-m-d'); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tipe</label>
                        <select name="type" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                            <option value="Pemasukan">Pemasukan</option>
                            <option value="Pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Kategori</label>
                    <input type="text" name="category" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" placeholder="Contoh: SPP Bulanan, Operasional" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Nominal (Rp)</label>
                    <input type="number" name="amount" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" placeholder="Contoh: 150000" required>
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Keterangan Tambahan</label>
                    <textarea name="description" rows="2" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" placeholder="Detail transaksi..."></textarea>
                </div>
                <button type="submit" name="tambah" class="w-full text-white bg-slate-800 hover:bg-slate-900 font-bold rounded-xl text-sm px-5 py-3">Simpan Transaksi</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>