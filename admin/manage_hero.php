<?php
session_start();
// Proteksi: Hanya user yang login (dan sebaiknya admin) yang boleh mengakses CMS Web
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

include '../includes/koneksi.php';

$uploadDataDir = __DIR__ . "/uploads";
if (!is_dir($uploadDataDir)) {
    mkdir($uploadDataDir, 0777, true);
}

$hero_path = "uploads/hero_bg.jpg";
$pesan = "";

// Proses Upload
if (isset($_POST['upload_hero'])) {
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Save explicitly as hero_bg.jpg to automatically overwrite
            if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $uploadDataDir . '/hero_bg.jpg')) {
                $pesan = "<div class='p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200'>Berhasil! Gambar Banner Utama telah diperbarui (Silakan clear cache browser jika gambar tidak langsung berubah).</div>";
            } else {
                $pesan = "<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200'>Gagal memindahkan file yang diunggah.</div>";
            }
        } else {
            $pesan = "<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200'>Format file tidak valid. Gunakan format JPG, PNG, atau WEBP.</div>";
        }
    } else {
         $pesan = "<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200'>Harap pilih gambar terlebih dahulu.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Banner Utama - Swift SC Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

<?php include '../includes/sidebar.php'; ?>

    <div class="p-4 sm:ml-64 pt-20">
        <div class="container mx-auto max-w-4xl">
            
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black text-slate-800 uppercase">Banner Utama</h1>
                    <p class="text-slate-500 mt-1">Ganti gambar latar belakang "Jump in. let's swim!" di halaman utama.</p>
                </div>
                <a href="../index.php" target="_blank" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2 px-4 rounded-lg text-sm transition-all shadow-md">Lihat Halaman Utama</a>
            </div>

            <?= $pesan; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Preview -->
                <div class="card overflow-hidden">
                    <div class="p-4 border-b">
                        <h3 class="font-bold text-slate-800">Preview Aktif</h3>
                    </div>
                    <div class="p-4">
                        <?php if (file_exists($uploadDataDir . '/hero_bg.jpg')): ?>
                            <div class="w-full aspect-video rounded-xl bg-cover bg-center shadow-inner relative group border border-slate-200" style="background-image: url('<?= $hero_path ?>?t=<?= time() ?>');">
                                <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                                    <span class="text-white font-bold tracking-wider uppercase text-sm">Banner Terpasang</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="w-full aspect-video rounded-xl bg-slate-100 flex flex-col items-center justify-center border-2 border-dashed border-slate-300">
                                <svg class="w-12 h-12 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-slate-500 text-sm font-medium">Belum Ada Banner Khusus (Menggunakan Warna Standar)</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Upload -->
                <div class="card overflow-hidden">
                    <div class="p-4 border-b">
                        <h3 class="font-bold text-slate-800">Unggah Banner Baru</h3>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" class="p-6">
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File Gambar</label>
                            <input type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp" required class="w-full border border-[#E8E8EF] rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all duration-200">
                            <p class="mt-2 text-xs text-slate-500">Rekomendasi ukuran: 1920x1080px. Format: JPG atau PNG.</p>
                        </div>
                        <button type="submit" name="upload_hero" class="w-full bg-slate-800 text-white font-bold py-3 rounded-xl hover:bg-slate-900 hover:shadow-lg transition-all duration-200 uppercase tracking-widest text-sm">Ganti Banner</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
