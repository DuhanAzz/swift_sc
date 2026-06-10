<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$pesan = "";

// Ambil data CMS saat ini
$cmsData = [
    'hero_title' => "Jump in. let's swim!",
    'hero_subtitle' => "SWIFT SC",
    'hero_desc' => "Klub renang resmi dan tersertifikasi dengan fasilitas pelatih berlisensi nasional & internasional.",
    'about_text' => "Swift Swimming Club adalah klub renang yang resmi dan telah tersertifikasi. Dengan fasilitas pelatih berlisensi nasional maupun internasional dan peralatan renang yang memadai."
];

try {
    $existingCms = $database->getDocument('cms_content', 'global');
    if ($existingCms) {
        $cmsData = array_merge($cmsData, $existingCms);
    }
} catch (\Exception $e) { }

if (isset($_POST['simpan_cms'])) {
    $newData = [
        'hero_title' => $_POST['hero_title'],
        'hero_subtitle' => $_POST['hero_subtitle'],
        'hero_desc' => $_POST['hero_desc'],
        'about_text' => $_POST['about_text'],
        'updated_at' => date('Y-m-d H:i:s')
    ];

    try {
        $database->setDocument('cms_content', 'global', $newData);
        $pesan = "<div class='p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200'>Konten web berhasil diperbarui!</div>";
        $cmsData = array_merge($cmsData, $newData);
    } catch (\Exception $e) {
        $pesan = "<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200'>Gagal menyimpan konten: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
        <div class="mb-6 flex justify-between items-center border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Teks Banner & Profil Klub</h1>
                <p class="text-sm text-gray-500">Edit teks yang tampil pada bagian atas halaman depan publik (index.php).</p>
            </div>
            <a href="ceo_cms_web.php" class="bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold py-2 px-4 rounded-lg text-sm transition-all">&larr; Kembali ke CMS Hub</a>
        </div>

        <?= $pesan; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <form action="" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Bagian Hero Banner</h3>
                    <div class="mb-4">
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Teks Kecil (Atas)</label>
                        <input type="text" name="hero_title" value="<?= htmlspecialchars($cmsData['hero_title']); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3 font-cursive text-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Judul Utama (Besar)</label>
                        <input type="text" name="hero_subtitle" value="<?= htmlspecialchars($cmsData['hero_subtitle']); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3 font-black uppercase" required>
                    </div>
                    <div class="mb-6">
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Deskripsi Pendek</label>
                        <textarea name="hero_desc" rows="2" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required><?= htmlspecialchars($cmsData['hero_desc']); ?></textarea>
                    </div>

                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2 mt-8">Bagian Profil / Tentang Klub</h3>
                    <div class="mb-6">
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Teks Paragraf "Tentang Swift SC"</label>
                        <textarea name="about_text" rows="4" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required><?= htmlspecialchars($cmsData['about_text']); ?></textarea>
                    </div>

                    <button type="submit" name="simpan_cms" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 font-bold rounded-xl text-sm px-5 py-3 shadow-md">Simpan Perubahan Konten</button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-lg border border-slate-700 p-6 text-white">
                    <h3 class="font-bold text-lg mb-2">Panduan CMS</h3>
                    <p class="text-sm text-slate-300 mb-4">Setiap perubahan yang Anda simpan di sini akan langsung mengubah teks di halaman depan (Landing Page). Kosongkan cache browser jika perubahan belum terlihat.</p>
                    <a href="../index.php" target="_blank" class="block text-center w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-md">Lihat Halaman Publik</a>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-slate-800 mb-2">Ubah Background Gambar?</h3>
                    <p class="text-sm text-gray-500 mb-4">Untuk mengganti gambar Hero Banner, Anda dapat mengakses fitur ini melalui menu CMS Halaman Web di halaman Admin.</p>
                    <a href="../admin/manage_hero.php" class="text-indigo-600 font-semibold hover:underline text-sm">Buka Upload Gambar Banner &rarr;</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
