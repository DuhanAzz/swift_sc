<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$pesan = '';

// Process slider upload
if(isset($_POST['tambah_slider'])) {
    if(isset($_FILES['gambar_slider']) && $_FILES['gambar_slider']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar_slider']['name'], PATHINFO_EXTENSION);
        $filename = 'slider_' . time() . '.' . $ext;
        $target = '../admin/uploads/' . $filename;
        if(move_uploaded_file($_FILES['gambar_slider']['tmp_name'], $target)) {
            $judul = mysqli_real_escape_string($koneksi, $_POST['judul'] ?? '');
            $subjudul = mysqli_real_escape_string($koneksi, $_POST['subjudul'] ?? '');
            $link = mysqli_real_escape_string($koneksi, $_POST['link'] ?? '#');
            $urutan = intval($_POST['urutan'] ?? 0);
            mysqli_query($koneksi, "INSERT INTO slider (gambar, judul, subjudul, link, urutan, status) VALUES ('$filename', '$judul', '$subjudul', '$link', '$urutan', 'Aktif')");
            $pesan = 'sukses';
        }
    }
}

// Delete slider
if(isset($_GET['hapus_slider'])) {
    $id = intval($_GET['hapus_slider']);
    $q = mysqli_query($koneksi, "SELECT gambar FROM slider WHERE id='$id'");
    if($q && $r = mysqli_fetch_assoc($q)) {
        @unlink('../admin/uploads/' . $r['gambar']);
    }
    mysqli_query($koneksi, "DELETE FROM slider WHERE id='$id'");
    $pesan = 'hapus';
}

// Toggle status
if(isset($_GET['toggle_slider'])) {
    $id = intval($_GET['toggle_slider']);
    mysqli_query($koneksi, "UPDATE slider SET status = IF(status='Aktif','Nonaktif','Aktif') WHERE id='$id'");
}

// Load data
$sliders = [];
$q_slider = mysqli_query($koneksi, "SELECT * FROM slider ORDER BY urutan ASC");
if($q_slider) { while($s = mysqli_fetch_assoc($q_slider)) { $sliders[] = $s; } }
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Slider & Banner</h1>
                <p class="text-sm text-gray-500">Kelola gambar slider dan banner promo di halaman depan</p>
            </div>
            <a href="ceo_cms_web.php" class="text-sm text-algolia-blue hover:underline">← Kembali</a>
        </div>

        <?php if($pesan == 'sukses'): ?>
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">Berhasil disimpan!</div>
        <?php elseif($pesan == 'hapus'): ?>
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">Data berhasil dihapus.</div>
        <?php endif; ?>

        <!-- SLIDER SECTION -->
        <div class="card p-6 mb-6">
            <h2 class="text-base font-bold text-slate-900 mb-4">📸 Slider Hero (Landing Page)</h2>
            <form action="ceo_cms_slider.php" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-3 items-end mb-6 border-b pb-6 border-slate-200">
                <div>
                    <label class="block mb-1 text-xs font-bold text-slate-500">Gambar</label>
                    <input type="file" name="gambar_slider" accept="image/*" class="text-sm border border-slate-200 rounded-lg p-2 bg-slate-50" required>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-slate-500">Judul</label>
                    <input type="text" name="judul" class="border border-slate-200 rounded-lg p-2 text-sm bg-slate-50 w-48" placeholder="Judul Teks Utama">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-slate-500">Subjudul / Deskripsi</label>
                    <input type="text" name="subjudul" class="border border-slate-200 rounded-lg p-2 text-sm bg-slate-50 w-64" placeholder="Deskripsi Singkat">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-slate-500">Link Tombol (Opsional)</label>
                    <input type="text" name="link" value="pendaftaran.php" class="w-32 border border-slate-200 rounded-lg p-2 text-sm bg-slate-50">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-slate-500">Urutan</label>
                    <input type="number" name="urutan" value="0" class="w-16 border border-slate-200 rounded-lg p-2 text-sm bg-slate-50">
                </div>
                <button type="submit" name="tambah_slider" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition-colors shadow-md">+ Tambah</button>
            </form>

            <?php if(count($sliders) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach($sliders as $sl): ?>
                <div class="rounded-xl overflow-hidden border border-slate-200 shadow-sm relative group bg-white">
                    <img src="../admin/uploads/<?= htmlspecialchars($sl['gambar']) ?>" class="w-full h-40 object-cover" onerror="this.src='https://placehold.co/400x200?text=No+Image'">
                    <div class="p-4">
                        <p class="font-bold text-sm text-slate-900 mb-1 line-clamp-1"><?= htmlspecialchars($sl['judul'] ?? 'Tanpa Judul') ?></p>
                        <p class="text-xs text-slate-500 mb-3 line-clamp-2"><?= htmlspecialchars($sl['subjudul'] ?? 'Tanpa deskripsi') ?></p>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-100 text-xs">
                            <span class="font-bold px-2 py-1 rounded bg-slate-100 <?= $sl['status'] == 'Aktif' ? 'text-green-600' : 'text-red-500' ?>"><?= $sl['status'] ?></span>
                            <div class="flex gap-3">
                                <a href="?toggle_slider=<?= $sl['id'] ?>" class="text-cyan-600 font-semibold hover:underline">Toggle Status</a>
                                <a href="?hapus_slider=<?= $sl['id'] ?>" onclick="return confirm('Hapus slider?')" class="text-red-500 font-semibold hover:underline">Hapus</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-sm text-slate-400 italic">Belum ada slider. Tambahkan gambar dan teks untuk menampilkan slider di halaman depan.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
