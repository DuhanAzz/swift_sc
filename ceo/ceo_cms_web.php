<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/koneksi.php';

// PROSES LOGO
if(isset($_POST['simpan_logo'])) {
    if(isset($_FILES['logo_web']) && $_FILES['logo_web']['error'] == 0){
        $ext = strtolower(pathinfo($_FILES['logo_web']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
            move_uploaded_file($_FILES['logo_web']['tmp_name'], '../assets/logo.png');
            header("Location: ceo_cms_web.php?pesan=sukses&tab=hero"); exit;
        }
    }
}

// PROSES TENTANG KLUB
if(isset($_POST['simpan_tentang'])) {
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $visi = mysqli_real_escape_string($koneksi, $_POST['visi']);
    $misi = mysqli_real_escape_string($koneksi, $_POST['misi']);
    $feature_1 = mysqli_real_escape_string($koneksi, $_POST['feature_1']);
    $feature_2 = mysqli_real_escape_string($koneksi, $_POST['feature_2']);
    $feature_3 = mysqli_real_escape_string($koneksi, $_POST['feature_3']);
    
    $q_cek = mysqli_query($koneksi, "SELECT * FROM tentang_club LIMIT 1");
    if(mysqli_num_rows($q_cek) > 0) {
        mysqli_query($koneksi, "UPDATE tentang_club SET deskripsi='$deskripsi', visi='$visi', misi='$misi', feature_1='$feature_1', feature_2='$feature_2', feature_3='$feature_3'");
    } else {
        mysqli_query($koneksi, "INSERT INTO tentang_club (deskripsi, visi, misi, feature_1, feature_2, feature_3) VALUES ('$deskripsi', '$visi', '$misi', '$feature_1', '$feature_2', '$feature_3')");
    }
    header("Location: ceo_cms_web.php?pesan=sukses&tab=tentang"); exit;
}

// PROSES PAKET BIAYA
if(isset($_POST['simpan_paket'])) {
    $id_paket = mysqli_real_escape_string($koneksi, $_POST['id_paket']);
    $nama_paket = mysqli_real_escape_string($koneksi, $_POST['nama_paket']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $satuan_waktu = mysqli_real_escape_string($koneksi, $_POST['satuan_waktu']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $fitur_1 = mysqli_real_escape_string($koneksi, $_POST['fitur_1']);
    $fitur_2 = mysqli_real_escape_string($koneksi, $_POST['fitur_2']);
    $fitur_3 = mysqli_real_escape_string($koneksi, $_POST['fitur_3']);
    $is_populer = isset($_POST['is_populer']) ? 1 : 0;
    
    mysqli_query($koneksi, "UPDATE paket_biaya SET nama_paket='$nama_paket', harga='$harga', satuan_waktu='$satuan_waktu', deskripsi='$deskripsi', fitur_1='$fitur_1', fitur_2='$fitur_2', fitur_3='$fitur_3', is_populer='$is_populer' WHERE id='$id_paket'");
    
    header("Location: ceo_cms_web.php?pesan=sukses&tab=paket"); exit;
}

// PROSES CMS HERO
if(isset($_POST['simpan_hero'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $konten = mysqli_real_escape_string($koneksi, $_POST['konten']);
    $q_cek = mysqli_query($koneksi, "SELECT * FROM cms_landing WHERE tipe='Banner'");
    if(mysqli_num_rows($q_cek) > 0) {
        mysqli_query($koneksi, "UPDATE cms_landing SET judul='$judul', konten='$konten', status='Aktif' WHERE tipe='Banner'");
    } else {
        mysqli_query($koneksi, "INSERT INTO cms_landing (tipe, judul, konten, status) VALUES ('Banner', '$judul', '$konten', 'Aktif')");
    }
    header("Location: ceo_cms_web.php?pesan=sukses&tab=hero"); exit;
}

// PROSES SLIDER
if(isset($_POST['tambah_slider'])) {
    $caption = mysqli_real_escape_string($koneksi, $_POST['caption']);
    $urutan = (int)$_POST['urutan'];
    $gambar = '';
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0){
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'slider_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../admin/uploads/' . $gambar);
    }
    mysqli_query($koneksi, "INSERT INTO slider (gambar, caption, urutan, status) VALUES ('$gambar', '$caption', '$urutan', 'Aktif')");
    header("Location: ceo_cms_web.php?pesan=sukses&tab=slider"); exit;
}
if(isset($_GET['hapus_slider'])) {
    $id = $_GET['hapus_slider'];
    $gambar = $_GET['g'];
    if($gambar && file_exists('../admin/uploads/'.$gambar)) { unlink('../admin/uploads/'.$gambar); }
    mysqli_query($koneksi, "DELETE FROM slider WHERE id='$id'");
    header("Location: ceo_cms_web.php?pesan=sukses&tab=slider"); exit;
}

// LOAD DATA
$cmsData = ['judul' => '', 'konten' => ''];
$q_cms = mysqli_query($koneksi, "SELECT judul, konten FROM cms_landing WHERE tipe='Banner' LIMIT 1");
if($q_cms && $row = mysqli_fetch_assoc($q_cms)) { $cmsData = $row; }

$tentangData = ['deskripsi' => '', 'visi' => '', 'misi' => '', 'feature_1' => '', 'feature_2' => '', 'feature_3' => ''];
$q_tentang = mysqli_query($koneksi, "SELECT * FROM tentang_club LIMIT 1");
if($q_tentang && $row = mysqli_fetch_assoc($q_tentang)) { $tentangData = $row; }

$paketData = [];
$q_paket = mysqli_query($koneksi, "SELECT * FROM paket_biaya ORDER BY urutan ASC");
while($row = mysqli_fetch_assoc($q_paket)) { $paketData[] = $row; }

$sliders = [];
$res_slider = mysqli_query($koneksi, "SELECT * FROM slider ORDER BY urutan ASC");
while($row = mysqli_fetch_assoc($res_slider)) { $sliders[] = $row; }

$active_tab = $_GET['tab'] ?? 'hero';

include '../includes/header.php';
include '../includes/sidebar.php';
?>
<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen bg-gray-50">
    <div class="p-4 lg:p-8">
        
        <div class="mb-8 flex flex-col md:flex-row justify-between md:items-end gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Pusat Kendali CMS</h1>
                <p class="text-gray-500 mt-1 font-medium">Kelola keseluruhan tampilan publik Landing Page dari satu dasbor terpusat.</p>
            </div>
            <a href="../index.php" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-indigo-600/30 text-sm transition-all inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg> Lihat Website
            </a>
        </div>

        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'sukses'): ?>
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-medium text-sm flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                Perubahan berhasil disimpan dan sudah tayang di Landing Page!
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <div class="flex gap-2 overflow-x-auto pb-4 mb-6 border-b border-gray-200">
            <a href="?tab=hero" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all whitespace-nowrap <?= $active_tab == 'hero' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' ?>">Teks Banner Utama</a>
            <a href="?tab=slider" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all whitespace-nowrap <?= $active_tab == 'slider' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' ?>">Galeri Slider</a>
            <a href="?tab=tentang" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all whitespace-nowrap <?= $active_tab == 'tentang' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' ?>">Visi Misi & Profil</a>
            <a href="?tab=paket" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all whitespace-nowrap <?= $active_tab == 'paket' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' ?>">Paket Biaya</a>
        </div>

        <!-- Tab Content: HERO -->
        <?php if($active_tab == 'hero'): ?>
        
        <!-- PENGATURAN LOGO -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Pengaturan Logo Web</h3>
            <div class="flex flex-col md:flex-row items-start gap-8">
                <div class="w-40 h-40 border border-gray-200 p-2 flex items-center justify-center flex-shrink-0">
                    <img src="../assets/logo.png?v=<?= time() ?>" alt="Logo Saat Ini" class="max-w-full max-h-full object-contain">
                </div>
                <div class="flex-1 w-full">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Upload Logo Baru (PNG/JPG/WEBP)</label>
                            <input type="file" name="logo_web" accept="image/png, image/jpeg, image/jpg, image/webp" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm" required>
                            <p class="text-xs text-gray-500 mt-2">Logo akan langsung menggantikan logo di seluruh website (sidebar, login, invoice, dll). Disarankan format PNG dengan background transparan.</p>
                        </div>
                        <button type="submit" name="simpan_logo" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transition-colors text-sm">Upload & Ganti Logo</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Pengaturan Teks Hero</h3>
            <form action="" method="POST" class="max-w-3xl">
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Utama (Hero Title)</label>
                    <input type="text" name="judul" value="<?= htmlspecialchars($cmsData['judul']) ?>" class="w-full border border-gray-300 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                    <p class="text-xs text-gray-500 mt-2">Gunakan kalimat pendek dan meyakinkan. Contoh: Berlatih Layaknya Sang Juara.</p>
                </div>
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Sub-judul (Hero Description)</label>
                    <textarea name="konten" rows="3" class="w-full border border-gray-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required><?= htmlspecialchars($cmsData['konten']) ?></textarea>
                </div>
                <button type="submit" name="simpan_hero" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-colors">Simpan Teks Banner</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Tab Content: SLIDER -->
        <?php if($active_tab == 'slider'): ?>
        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Galeri Slider</h3>
                <p class="text-gray-500 text-sm mt-1">Kelola gambar yang berputar pada sisi kanan Hero Landing.</p>
            </div>
            <button onclick="document.getElementById('modalSlider').classList.remove('hidden')" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Slider
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach($sliders as $s): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden group">
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        <img src="../admin/uploads/<?= $s['gambar'] ?>" alt="Slider" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <a href="?hapus_slider=<?= $s['id'] ?>&g=<?= $s['gambar'] ?>" onclick="return confirm('Hapus gambar ini?')" class="bg-red-500 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-600">Hapus</a>
                        </div>
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur text-gray-900 text-xs font-black px-2 py-1 rounded-md shadow">#<?= $s['urutan'] ?></div>
                    </div>
                    <div class="p-4">
                        <p class="text-sm font-semibold text-gray-800 line-clamp-2"><?= htmlspecialchars($s['caption'] ?? 'Tidak ada caption') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Modal Tambah Slider -->
        <div id="modalSlider" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center">
            <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl">
                <h3 class="font-bold text-2xl mb-6 text-gray-900">Upload Gambar Slider</h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-5">
                        <label class="block text-sm font-bold mb-2">Pilih Gambar</label>
                        <input type="file" name="gambar" accept="image/*" required class="w-full border border-gray-300 rounded-xl p-2.5 text-sm">
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-bold mb-2">Caption <span class="text-gray-400 font-normal">(Muncul di pojok gambar)</span></label>
                        <input type="text" name="caption" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-8">
                        <label class="block text-sm font-bold mb-2">Urutan Tampil</label>
                        <input type="number" name="urutan" value="<?= count($sliders)+1 ?>" required class="w-full border border-gray-300 rounded-xl p-3 text-sm">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalSlider').classList.add('hidden')" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-xl font-bold transition-colors">Batal</button>
                        <button type="submit" name="tambah_slider" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-colors">Simpan & Upload</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tab Content: TENTANG -->
        <?php if($active_tab == 'tentang'): ?>
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Profil, Visi & Misi</h3>
            <form action="" method="POST">
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Klub</label>
                    <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required><?= htmlspecialchars($tentangData['deskripsi']) ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Visi</label>
                        <textarea name="visi" rows="5" class="w-full border border-gray-300 rounded-xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required><?= htmlspecialchars($tentangData['visi']) ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Misi</label>
                        <textarea name="misi" rows="5" class="w-full border border-gray-300 rounded-xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required><?= htmlspecialchars($tentangData['misi']) ?></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-8 border-t border-gray-100 pt-6">
                    <div class="col-span-full mb-2">
                        <label class="block text-sm font-bold text-gray-700">3 Poin Keunggulan Utama (Features)</label>
                        <p class="text-xs text-gray-500">Muncul di samping teks Tentang Klub di halaman utama.</p>
                    </div>
                    <div>
                        <input type="text" name="feature_1" value="<?= htmlspecialchars($tentangData['feature_1'] ?? '') ?>" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Poin 1" required>
                    </div>
                    <div>
                        <input type="text" name="feature_2" value="<?= htmlspecialchars($tentangData['feature_2'] ?? '') ?>" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Poin 2" required>
                    </div>
                    <div>
                        <input type="text" name="feature_3" value="<?= htmlspecialchars($tentangData['feature_3'] ?? '') ?>" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Poin 3" required>
                    </div>
                </div>

                <button type="submit" name="simpan_tentang" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg transition-colors">Simpan Profil Klub</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Tab Content: PAKET BIAYA -->
        <?php if($active_tab == 'paket'): ?>
        <div class="mb-12">
            <h3 class="text-2xl font-black text-slate-800 mb-2">Manajemen Paket Biaya</h3>
            <p class="text-slate-500 mb-8 font-medium">Ubah nama paket, harga, dan fitur-fitur yang didapatkan.</p>
            
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <?php foreach($paketData as $paket): ?>
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <form action="ceo_cms_web.php" method="POST">
                        <input type="hidden" name="id_paket" value="<?= $paket['id'] ?>">
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Nama Paket</label>
                            <input type="text" name="nama_paket" value="<?= htmlspecialchars($paket['nama_paket']) ?>" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Harga</label>
                                <input type="text" name="harga" value="<?= htmlspecialchars($paket['harga']) ?>" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Satuan Waktu</label>
                                <input type="text" name="satuan_waktu" value="<?= htmlspecialchars($paket['satuan_waktu']) ?>" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500" placeholder="/bln">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Deskripsi</label>
                            <textarea name="deskripsi" rows="2" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500" required><?= htmlspecialchars($paket['deskripsi']) ?></textarea>
                        </div>
                        <div class="mb-4 space-y-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase">3 Fitur Utama</label>
                            <input type="text" name="fitur_1" value="<?= htmlspecialchars($paket['fitur_1']) ?>" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm" placeholder="Fitur 1">
                            <input type="text" name="fitur_2" value="<?= htmlspecialchars($paket['fitur_2']) ?>" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm" placeholder="Fitur 2">
                            <input type="text" name="fitur_3" value="<?= htmlspecialchars($paket['fitur_3']) ?>" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm" placeholder="Fitur 3">
                        </div>
                        <div class="mb-6 flex items-center">
                            <input type="checkbox" name="is_populer" value="1" <?= $paket['is_populer'] ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                            <label class="ml-2 text-sm font-bold text-gray-700">Tandai sebagai Terpopuler</label>
                        </div>
                        <button type="submit" name="simpan_paket" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-colors">Simpan Paket</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php include '../includes/footer.php'; ?>
