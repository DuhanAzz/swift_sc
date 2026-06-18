<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses_tambah' => 'Jadwal berhasil ditambahkan!',
                'sukses_edit' => 'Jadwal berhasil diperbarui!',
                'sukses_hapus' => 'Jadwal berhasil dihapus!',
                'gagal' => 'Terjadi kesalahan saat memproses data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-4 mb-4 text-sm text-{$color}-800 rounded-lg bg-{$color}-50 border border-{$color}-200'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">CMS Jadwal Publik</h1>
                <p class="text-sm text-gray-500">Kelola daftar lokasi dan jadwal latihan yang ditampilkan di Landing Page.</p>
            </div>
            <div class="space-x-2">
                <a href="ceo_cms_web.php" class="bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold py-2 px-4 rounded-lg text-sm transition-all">&larr; Kembali</a>
                <button data-modal-target="modalTambahJadwal" data-modal-toggle="modalTambahJadwal" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-md">+ Tambah Jadwal</button>
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-white uppercase bg-slate-800">
                    <tr>
                        <th class="px-6 py-4">Lokasi Kolam Renang</th>
                        <th class="px-6 py-4">Hari Latihan</th>
                        <th class="px-6 py-4">Jam Latihan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $schedulesArray = [];
                    try {
                        $q_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal ORDER BY id ASC");
                        if($q_jadwal) {
                            while($row = mysqli_fetch_assoc($q_jadwal)) {
                                $schedulesArray[] = $row;
                            }
                        }
                    } catch (\Exception $e) {}

                    if(count($schedulesArray) > 0) {
                        foreach($schedulesArray as $data) {
                    ?>
                    <tr class="bg-white border-b hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['lokasi']); ?></td>
                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($data['hari']); ?></td>
                        <td class="px-6 py-4 font-mono text-indigo-600"><?= date('H:i', strtotime($data['jam_mulai'])); ?> - <?= date('H:i', strtotime($data['jam_selesai'])); ?> WIB</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <button data-modal-target="modalEditJadwal<?= $data['id']; ?>" data-modal-toggle="modalEditJadwal<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                            <a href="ceo_cms_jadwal_proses.php?hapus=<?= $data['id']; ?>" onclick="return confirm('Hapus jadwal ini?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit Jadwal -->
                    <div id="modalEditJadwal<?= $data['id']; ?>" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h3 class="text-lg font-bold">Edit Jadwal</h3>
                                <button data-modal-toggle="modalEditJadwal<?= $data['id']; ?>" class="text-gray-400 hover:text-gray-900">✖</button>
                            </div>
                            <form action="ceo_cms_jadwal_proses.php" method="POST">
                                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lokasi Kolam</label>
                                    <input type="text" name="lokasi" value="<?= htmlspecialchars($data['lokasi']); ?>" class="w-full p-2 border rounded-lg focus:ring-orange-500" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hari Latihan</label>
                                    <select name="hari" class="w-full p-2 border rounded-lg focus:ring-orange-500" required>
                                        <?php
                                        $hari_list = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
                                        foreach($hari_list as $h) {
                                            $sel = ($data['hari'] == $h) ? 'selected' : '';
                                            echo "<option value='$h' $sel>$h</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Mulai</label>
                                        <input type="time" name="jam_mulai" value="<?= date('H:i', strtotime($data['jam_mulai'])); ?>" class="w-full p-2 border rounded-lg focus:ring-orange-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Selesai</label>
                                        <input type="time" name="jam_selesai" value="<?= date('H:i', strtotime($data['jam_selesai'])); ?>" class="w-full p-2 border rounded-lg focus:ring-orange-500" required>
                                    </div>
                                </div>
                                <button type="submit" name="edit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-lg">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                    <?php } } else { echo "<tr><td colspan='4' class='px-6 py-8 text-center text-slate-500 italic'>Belum ada jadwal yang ditambahkan. Silakan tambah jadwal baru.</td></tr>"; } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div id="modalTambahJadwal" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-lg font-bold">Tambah Jadwal Baru</h3>
            <button data-modal-toggle="modalTambahJadwal" class="text-gray-400 hover:text-gray-900">✖</button>
        </div>
        <form action="ceo_cms_jadwal_proses.php" method="POST">
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lokasi Kolam</label>
                <input type="text" name="lokasi" placeholder="Contoh: Ledhok Pereng" class="w-full p-2 border rounded-lg focus:ring-orange-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hari Latihan</label>
                <select name="hari" class="w-full p-2 border rounded-lg focus:ring-orange-500" required>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
            </div>
            <div class="mb-6 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="w-full p-2 border rounded-lg focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="w-full p-2 border rounded-lg focus:ring-orange-500" required>
                </div>
            </div>
            <button type="submit" name="tambah" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-lg">Simpan Jadwal</button>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
