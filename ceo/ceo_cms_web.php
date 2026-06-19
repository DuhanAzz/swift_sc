<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// Proses Update Visi Misi
if(isset($_POST['update_visi_misi'])) {
    $visi = mysqli_real_escape_string($koneksi, $_POST['visi']);
    $misi = mysqli_real_escape_string($koneksi, $_POST['misi']);
    $tentang = mysqli_real_escape_string($koneksi, $_POST['tentang']);
    
    $cek = mysqli_query($koneksi, "SELECT id FROM tentang_club LIMIT 1");
    if(mysqli_num_rows($cek) == 0) {
        mysqli_query($koneksi, "INSERT INTO tentang_club (visi, misi, deskripsi) VALUES ('$visi', '$misi', '$tentang')");
    } else {
        mysqli_query($koneksi, "UPDATE tentang_club SET visi='$visi', misi='$misi', deskripsi='$tentang'");
    }
    
    header("location:ceo_cms_web.php?pesan=sukses");
    exit;
}

// Data Loaders
$q_slider = mysqli_query($koneksi, "SELECT * FROM slider ORDER BY urutan ASC");
$q_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY id DESC");

$cms_data = ['visi' => '', 'misi' => '', 'tentang_kami' => ''];
$q_cms = mysqli_query($koneksi, "SELECT * FROM tentang_club LIMIT 1");
if($q_cms && $row = mysqli_fetch_assoc($q_cms)) {
    $cms_data['visi'] = $row['visi'];
    $cms_data['misi'] = $row['misi'];
    $cms_data['tentang_kami'] = $row['deskripsi'];
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan']) && $_GET['pesan'] == 'sukses'){
            echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">✅ Konten berhasil diperbarui! Gambar lama tetap dipertahankan jika tidak ada gambar baru yang diunggah.</div>';
        }
        ?>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-algolia-navy tracking-tight">Manajemen Konten Website (CMS)</h1>
            <p class="text-base text-gray-500 mt-1">Kelola tampilan halaman publik Swift SC dengan sistem tabulasi.</p>
        </div>

        <div class="mb-8 border-b border-[#E8E8EF]">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="cmsTab" data-tabs-toggle="#cmsTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="hero-tab" data-tabs-target="#hero" type="button" role="tab" aria-controls="hero" aria-selected="false">Hero Banner</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-[#E8E8EF]" id="berita-tab" data-tabs-target="#berita" type="button" role="tab" aria-controls="berita" aria-selected="false">Berita & Pengumuman</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-[#E8E8EF]" id="visi-tab" data-tabs-target="#visi" type="button" role="tab" aria-controls="visi" aria-selected="false">Visi Misi & Profil</button>
                </li>
            </ul>
        </div>

        <div id="cmsTabContent">
            
            <!-- TAB: HERO BANNER -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">Pengaturan Hero Banner</h2>
                    <a href="ceo_cms_slider.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-sm">Kelola Gambar Banner Lanjutan &rarr;</a>
                </div>
                <div class="bg-blue-50 p-4 rounded border border-blue-200 text-sm text-blue-800 mb-4">
                    <strong>Info Logika Fallback Gambar:</strong> Saat memperbarui banner di modul lanjutan, jika form upload gambar dibiarkan kosong, sistem secara otomatis mendeteksi <code>$_FILES['gambar']['error'] !== 0</code> dan akan mempertahankan file gambar lama di database tanpa me-replace-nya dengan NULL.
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php if($q_slider) { while($sl = mysqli_fetch_assoc($q_slider)) { ?>
                    <div class="rounded-xl overflow-hidden border border-[#E8E8EF] bg-white shadow-sm">
                        <img src="../admin/uploads/<?= htmlspecialchars($sl['gambar']) ?>" class="w-full h-32 object-cover" onerror="this.src='https://placehold.co/400x200?text=No+Image'">
                        <div class="p-3 text-center">
                            <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded">Urutan: <?= $sl['urutan'] ?></span>
                        </div>
                    </div>
                    <?php } } ?>
                </div>
            </div>

            <!-- TAB: BERITA / PENGUMUMAN -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="berita" role="tabpanel" aria-labelledby="berita-tab">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">Daftar Berita & Pengumuman</h2>
                    <a href="ceo_cms_berita.php" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-sm">+ Tulis / Edit Berita &rarr;</a>
                </div>
                <div class="bg-blue-50 p-4 rounded border border-blue-200 text-sm text-blue-800 mb-4">
                    <strong>Info Logika Fallback Gambar:</strong> Saat meng-edit berita melalui form, query SQL menggunakan logika dinamis <code>$q_gambar = ""; if(!empty(upload)) { $q_gambar = ", gambar='...'"; }</code> sehingga gambar lama 100% aman jika input file kosong.
                </div>

                <div class="card overflow-hidden">
                    <table class="table-algolia w-full text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 border-b">Tanggal</th>
                                <th class="px-6 py-3 border-b">Kategori</th>
                                <th class="px-6 py-3 border-b">Judul</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($q_berita) { while($br = mysqli_fetch_assoc($q_berita)) { ?>
                            <tr class="border-b hover:bg-slate-50 bg-white">
                                <td class="px-6 py-3 text-sm text-gray-500"><?= $br['tanggal'] ?></td>
                                <td class="px-6 py-3 text-sm"><span class="bg-gray-100 font-bold px-2 py-1 rounded"><?= $br['kategori'] ?></span></td>
                                <td class="px-6 py-3 text-sm font-bold text-algolia-navy"><?= $br['judul'] ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB: VISI MISI -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="visi" role="tabpanel" aria-labelledby="visi-tab">
                <h2 class="text-lg font-bold mb-4">Edit Profil Klub (Visi & Misi)</h2>
                <div class="card p-6 bg-white">
                    <form action="ceo_cms_web.php" method="POST">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tentang Klub (Deskripsi Singkat)</label>
                            <textarea name="tentang" rows="4" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-algolia-blue"><?= htmlspecialchars($cms_data['tentang_kami'] ?? '') ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Visi</label>
                                <textarea name="visi" rows="5" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-algolia-blue"><?= htmlspecialchars($cms_data['visi'] ?? '') ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Misi</label>
                                <textarea name="misi" rows="5" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-algolia-blue"><?= htmlspecialchars($cms_data['misi'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <button type="submit" name="update_visi_misi" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg text-sm w-full md:w-auto shadow-md">Simpan Perubahan Profil</button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
