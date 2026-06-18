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
                'sukses_tambah' => 'Berita berhasil dipublikasikan!',
                'sukses_edit' => 'Berita berhasil diperbarui!',
                'sukses_hapus' => 'Berita berhasil dihapus!',
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
                <h1 class="text-2xl font-bold text-gray-800">CMS Berita & Pengumuman</h1>
                <p class="text-sm text-gray-500">Tulis artikel atau berita prestasi yang akan tampil di halaman depan.</p>
            </div>
            <div class="space-x-2">
                <a href="ceo_cms_web.php" class="bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold py-2 px-4 rounded-lg text-sm transition-all">&larr; Kembali</a>
                <button data-modal-target="modalTambahBerita" data-modal-toggle="modalTambahBerita" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-md">+ Tulis Berita Baru</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <?php
            $beritaArray = [];
            try {
                $q_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY id DESC");
                if($q_berita) {
                    while($row = mysqli_fetch_assoc($q_berita)) {
                        $beritaArray[] = $row;
                    }
                }
            } catch (\Exception $e) {}

            if(count($beritaArray) > 0) {
                foreach($beritaArray as $data) {
                    $foto = (!empty($data['gambar']) && file_exists("../admin/" . $data['gambar'])) ? "../admin/" . $data['gambar'] : "https://images.unsplash.com/photo-1572334057861-6d72dbb688d2?auto=format&fit=crop&w=800&q=80";
            ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="h-48 bg-cover bg-center" style="background-image: url('<?= $foto ?>');"></div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-pink-100 text-pink-600"><?= htmlspecialchars($data['kategori']); ?></span>
                        <span class="text-xs text-gray-500"><?= date('d M Y', strtotime($data['tanggal'])); ?></span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight"><?= htmlspecialchars($data['judul']); ?></h3>
                    <p class="text-sm text-gray-500 line-clamp-3 mb-4 flex-1"><?= strip_tags($data['isi'] ?? ''); ?></p>
                    
                    <div class="flex space-x-2 mt-auto border-t pt-4">
                        <button onclick="document.getElementById('modalEditBerita<?= $data['id']; ?>').classList.remove('hidden')" class="flex-1 bg-gray-100 text-gray-700 font-bold py-2 rounded hover:bg-gray-200 transition">Edit</button>
                        <a href="ceo_cms_berita_proses.php?hapus=<?= $data['id']; ?>" onclick="return confirm('Hapus berita ini?')" class="flex-1 bg-red-100 text-red-600 text-center font-bold py-2 rounded hover:bg-red-200 transition">Hapus</a>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Berita -->
            <div id="modalEditBerita<?= $data['id']; ?>" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] flex flex-col">
                    <div class="flex justify-between items-center p-6 border-b">
                        <h3 class="text-lg font-bold">Edit Berita</h3>
                        <button onclick="document.getElementById('modalEditBerita<?= $data['id']; ?>').classList.add('hidden')" class="text-gray-400 hover:text-gray-900">✖</button>
                    </div>
                    <div class="p-6 overflow-y-auto">
                        <form action="ceo_cms_berita_proses.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $data['id']; ?>">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                                    <select name="kategori" class="w-full p-2 border rounded-lg focus:ring-pink-500" required>
                                        <option value="Prestasi" <?= $data['kategori'] == 'Prestasi' ? 'selected' : '' ?>>Prestasi</option>
                                        <option value="Pengumuman" <?= $data['kategori'] == 'Pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                                        <option value="Artikel" <?= $data['kategori'] == 'Artikel' ? 'selected' : '' ?>>Artikel</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                                    <input type="date" name="tanggal" value="<?= $data['tanggal']; ?>" class="w-full p-2 border rounded-lg focus:ring-pink-500" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Judul Berita</label>
                                <input type="text" name="judul" value="<?= htmlspecialchars($data['judul']); ?>" class="w-full p-2 border rounded-lg focus:ring-pink-500" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ganti Gambar <span class="font-normal">(Biarkan kosong jika tidak diganti)</span></label>
                                <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp" class="w-full border rounded-lg file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-pink-50 file:text-pink-700">
                            </div>
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Konten Berita</label>
                                <textarea name="konten" rows="6" class="w-full p-2 border rounded-lg focus:ring-pink-500" required><?= htmlspecialchars($data['isi'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="edit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 rounded-lg">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>

            <?php } } else { echo "<div class='col-span-3 text-center py-12 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300'>Belum ada berita. Klik tombol Tulis Berita Baru.</div>"; } ?>
        </div>

    </div>
</div>

<!-- Modal Tambah Berita -->
<div id="modalTambahBerita" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-lg font-bold">Tulis Berita Baru</h3>
            <button data-modal-toggle="modalTambahBerita" class="text-gray-400 hover:text-gray-900">✖</button>
        </div>
        <div class="p-6 overflow-y-auto">
            <form action="ceo_cms_berita_proses.php" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                        <select name="kategori" class="w-full p-2 border rounded-lg focus:ring-pink-500" required>
                            <option value="Prestasi">Prestasi</option>
                            <option value="Pengumuman">Pengumuman</option>
                            <option value="Artikel">Artikel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" class="w-full p-2 border rounded-lg focus:ring-pink-500" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Judul Berita</label>
                    <input type="text" name="judul" class="w-full p-2 border rounded-lg focus:ring-pink-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Gambar Utama</label>
                    <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp" class="w-full border rounded-lg file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-pink-50 file:text-pink-700">
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Konten Berita</label>
                    <textarea name="konten" rows="6" class="w-full p-2 border rounded-lg focus:ring-pink-500" required></textarea>
                </div>
                <button type="submit" name="tambah" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 rounded-lg">Publikasikan</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
