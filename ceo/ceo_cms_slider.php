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
            $urutan = intval($_POST['urutan'] ?? 0);
            mysqli_query($koneksi, "INSERT INTO slider (gambar, urutan, status) VALUES ('$filename', '$urutan', 'Aktif')");
            $pesan = 'sukses';
        }
    }
}

// Process banner upload
if(isset($_POST['tambah_banner'])) {
    if(isset($_FILES['gambar_banner']) && $_FILES['gambar_banner']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar_banner']['name'], PATHINFO_EXTENSION);
        $filename = 'banner_' . time() . '.' . $ext;
        $target = '../admin/uploads/' . $filename;
        if(move_uploaded_file($_FILES['gambar_banner']['tmp_name'], $target)) {
            $judul = mysqli_real_escape_string($koneksi, $_POST['judul'] ?? '');
            $subjudul = mysqli_real_escape_string($koneksi, $_POST['subjudul'] ?? '');
            $link = mysqli_real_escape_string($koneksi, $_POST['link'] ?? '#');
            $urutan = intval($_POST['urutan'] ?? 0);
            mysqli_query($koneksi, "INSERT INTO cms_banners (gambar, judul, subjudul, link, urutan, status) VALUES ('$filename', '$judul', '$subjudul', '$link', '$urutan', 'Aktif')");
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

// Delete banner
if(isset($_GET['hapus_banner'])) {
    $id = intval($_GET['hapus_banner']);
    $q = mysqli_query($koneksi, "SELECT gambar FROM cms_banners WHERE id='$id'");
    if($q && $r = mysqli_fetch_assoc($q)) {
        @unlink('../admin/uploads/' . $r['gambar']);
    }
    mysqli_query($koneksi, "DELETE FROM cms_banners WHERE id='$id'");
    $pesan = 'hapus';
}

// Toggle status
if(isset($_GET['toggle_slider'])) {
    $id = intval($_GET['toggle_slider']);
    mysqli_query($koneksi, "UPDATE slider SET status = IF(status='Aktif','Nonaktif','Aktif') WHERE id='$id'");
}
if(isset($_GET['toggle_banner'])) {
    $id = intval($_GET['toggle_banner']);
    mysqli_query($koneksi, "UPDATE cms_banners SET status = IF(status='Aktif','Draft','Aktif') WHERE id='$id'");
}

// Load data
$sliders = [];
$q_slider = mysqli_query($koneksi, "SELECT * FROM slider ORDER BY urutan ASC");
if($q_slider) { while($s = mysqli_fetch_assoc($q_slider)) { $sliders[] = $s; } }

$banners = [];
$q_banners = mysqli_query($koneksi, "SELECT * FROM cms_banners ORDER BY urutan ASC");
if($q_banners) { while($b = mysqli_fetch_assoc($q_banners)) { $banners[] = $b; } }
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
            <h2 class="text-base font-bold text-algolia-navy mb-4">📸 Slider Gambar</h2>
            <form action="ceo_cms_slider.php" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-3 items-end mb-4 border-b pb-4 border-[#E8E8EF]">
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-500">Gambar</label>
                    <input type="file" name="gambar_slider" accept="image/*" class="text-sm border border-[#E8E8EF] rounded-lg p-2 bg-gray-50" required>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-500">Urutan</label>
                    <input type="number" name="urutan" value="0" class="w-20 border border-[#E8E8EF] rounded-lg p-2 text-sm bg-gray-50">
                </div>
                <button type="submit" name="tambah_slider" class="bg-algolia-blue text-white font-bold py-2 px-6 rounded-lg text-sm">+ Tambah</button>
            </form>

            <?php if(count($sliders) > 0): ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php foreach($sliders as $sl): ?>
                <div class="rounded-xl overflow-hidden border border-[#E8E8EF] relative group">
                    <img src="../admin/uploads/<?= htmlspecialchars($sl['gambar']) ?>" class="w-full h-32 object-cover" onerror="this.src='https://placehold.co/400x200?text=No+Image'">
                    <div class="p-2 flex justify-between items-center bg-gray-50 text-xs">
                        <span class="font-medium <?= $sl['status'] == 'Aktif' ? 'text-green-600' : 'text-red-500' ?>"><?= $sl['status'] ?></span>
                        <div class="flex gap-1">
                            <a href="?toggle_slider=<?= $sl['id'] ?>" class="text-blue-500 hover:underline">Toggle</a>
                            <a href="?hapus_slider=<?= $sl['id'] ?>" onclick="return confirm('Hapus slider?')" class="text-red-500 hover:underline">Hapus</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-400 italic">Belum ada slider. Tambahkan gambar untuk menampilkan slider di halaman depan.</p>
            <?php endif; ?>
        </div>

        <!-- BANNER SECTION -->
        <div class="card p-6">
            <h2 class="text-base font-bold text-algolia-navy mb-4">🖼️ Banner Promo</h2>
            <form action="ceo_cms_slider.php" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-3 items-end mb-4 border-b pb-4 border-[#E8E8EF]">
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-500">Gambar</label>
                    <input type="file" name="gambar_banner" accept="image/*" class="text-sm border border-[#E8E8EF] rounded-lg p-2 bg-gray-50" required>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-500">Judul</label>
                    <input type="text" name="judul" class="border border-[#E8E8EF] rounded-lg p-2 text-sm bg-gray-50" placeholder="Judul banner">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-500">Subjudul</label>
                    <input type="text" name="subjudul" class="border border-[#E8E8EF] rounded-lg p-2 text-sm bg-gray-50" placeholder="Subjudul">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-500">Link</label>
                    <input type="text" name="link" value="#" class="w-32 border border-[#E8E8EF] rounded-lg p-2 text-sm bg-gray-50">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-500">Urutan</label>
                    <input type="number" name="urutan" value="0" class="w-20 border border-[#E8E8EF] rounded-lg p-2 text-sm bg-gray-50">
                </div>
                <button type="submit" name="tambah_banner" class="bg-algolia-blue text-white font-bold py-2 px-6 rounded-lg text-sm">+ Tambah</button>
            </form>

            <?php if(count($banners) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <?php foreach($banners as $bn): ?>
                <div class="rounded-xl overflow-hidden border border-[#E8E8EF]">
                    <div class="h-32 bg-cover bg-center" style="background-image: url('../admin/uploads/<?= htmlspecialchars($bn['gambar']) ?>');"></div>
                    <div class="p-3 bg-gray-50">
                        <p class="font-bold text-sm text-gray-800"><?= htmlspecialchars($bn['judul'] ?? 'Banner') ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($bn['subjudul'] ?? '') ?></p>
                        <div class="flex justify-between items-center mt-2 text-xs">
                            <span class="font-medium <?= $bn['status'] == 'Aktif' ? 'text-green-600' : 'text-red-500' ?>"><?= $bn['status'] ?></span>
                            <div class="flex gap-2">
                                <a href="?toggle_banner=<?= $bn['id'] ?>" class="text-blue-500 hover:underline">Toggle</a>
                                <a href="?hapus_banner=<?= $bn['id'] ?>" onclick="return confirm('Hapus banner?')" class="text-red-500 hover:underline">Hapus</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-400 italic">Belum ada banner. Tambahkan banner promo untuk ditampilkan di halaman depan.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
