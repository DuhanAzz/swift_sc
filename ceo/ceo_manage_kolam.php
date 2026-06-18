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

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses_tambah' => 'Data Kolam berhasil ditambahkan!',
                'sukses_edit' => 'Data Kolam berhasil diperbarui!',
                'sukses_hapus' => 'Data Kolam berhasil dihapus!',
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
                <h1 class="text-xl font-bold text-algolia-navy">Manajemen Cabang Kolam (Pools)</h1>
                <p class="text-sm text-gray-500">Kelola daftar cabang kolam renang Swift SC.</p>
            </div>
            <button data-modal-target="modalTambahKolam" data-modal-toggle="modalTambahKolam" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-md">+ Tambah Kolam</button>
        </div>

        <div class="card overflow-hidden">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4">Nama Kolam</th>
                        <th class="px-6 py-4">Lokasi / Alamat Lengkap</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $poolsArray = [];
                    try {
                        $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                        while($row = mysqli_fetch_assoc($q_kolam)) {
                            $poolsArray[] = $row;
                        }
                    } catch (\Exception $e) {}

                    if(count($poolsArray) > 0) {
                        foreach($poolsArray as $data) {
                    ?>
                    <tr class="bg-white border-b hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['nama_cabang']); ?></td>
                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($data['lokasi']); ?></td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <button data-modal-target="modalEditKolam<?= $data['id']; ?>" data-modal-toggle="modalEditKolam<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                            <a href="ceo_kolam_proses.php?hapus=<?= $data['id']; ?>" onclick="return confirm('Yakin ingin menghapus kolam ini?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit Kolam -->
                    <div id="modalEditKolam<?= $data['id']; ?>" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h3 class="text-lg font-bold">Edit Data Kolam</h3>
                                <button data-modal-toggle="modalEditKolam<?= $data['id']; ?>" class="text-gray-400 hover:text-gray-900">✖</button>
                            </div>
                            <form action="ceo_kolam_proses.php" method="POST">
                                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kolam</label>
                                    <input type="text" name="name" value="<?= htmlspecialchars($data['nama_cabang']); ?>" class="w-full p-2 border rounded-lg focus:ring-indigo-500" required>
                                </div>
                                <div class="mb-6">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lokasi / Alamat</label>
                                    <textarea name="location" rows="3" class="w-full p-2 border rounded-lg focus:ring-indigo-500" required><?= htmlspecialchars($data['lokasi']); ?></textarea>
                                </div>
                                <button type="submit" name="edit" class="w-full bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2 rounded-lg">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                    <?php } } else { echo "<tr><td colspan='3' class='px-6 py-8 text-center text-slate-500 italic'>Belum ada data kolam cabang. Silakan tambah data baru.</td></tr>"; } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal Tambah Kolam -->
<div id="modalTambahKolam" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-lg font-bold">Tambah Cabang Kolam Baru</h3>
            <button data-modal-toggle="modalTambahKolam" class="text-gray-400 hover:text-gray-900">✖</button>
        </div>
        <form action="ceo_kolam_proses.php" method="POST">
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kolam</label>
                <input type="text" name="name" placeholder="Contoh: Ledhok Pereng" class="w-full p-2 border rounded-lg focus:ring-indigo-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lokasi / Alamat Lengkap</label>
                <textarea name="location" rows="3" placeholder="Contoh: Jl. Magelang Km 10..." class="w-full p-2 border rounded-lg focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" name="tambah" class="w-full bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2 rounded-lg">Simpan Kolam</button>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
