<?php
session_start();
require_once '../../config/database.php';

// Proteksi: Hanya 'admin' yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$pesan = "";

// --- LOGIKA TAMBAH BERITA ---
if (isset($_POST['tambah_berita'])) {
    $judul    = bersihkan_input($_POST['judul']);
    $kategori = bersihkan_input($_POST['kategori']);
    $isi      = bersihkan_input($_POST['isi']);
    $tanggal  = date('Y-m-d');
    
    // Default gambar jika tidak upload
    $nama_gambar = 'default_news.jpg'; 

    // Cek apakah ada file gambar yang diunggah
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        $nama_file = $_FILES['gambar']['name'];
        $x = explode('.', $nama_file);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['gambar']['size'];
        $file_tmp = $_FILES['gambar']['tmp_name'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 2044070) { // Maksimal 2MB
                $nama_gambar = 'news_' . time() . '.' . $ekstensi; // Rename agar tidak bentrok
                move_uploaded_file($file_tmp, '../../uploads/' . $nama_gambar);
            } else {
                $pesan = "<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200'>Ukuran file terlalu besar (Maks 2MB).</div>";
            }
        } else {
            $pesan = "<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200'>Ekstensi file tidak diperbolehkan (Hanya JPG/PNG).</div>";
        }
    }

    if ($pesan == "") {
        $query_insert = "INSERT INTO berita (judul, kategori, isi, tanggal, gambar, cabang) 
                         VALUES ('$judul', '$kategori', '$isi', '$tanggal', '$nama_gambar', 'Pusat')";
        if (mysqli_query($koneksi, $query_insert)) {
            $pesan = "<div class='p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200'>Berita berhasil dipublikasikan!</div>";
        }
    }
}

// --- LOGIKA HAPUS BERITA ---
if (isset($_GET['hapus'])) {
    $id_hapus = bersihkan_input($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM berita WHERE id = '$id_hapus'");
    header("Location: kelola_berita.php");
    exit;
}

// Ambil semua data berita
$query_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Swift SC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 flex">

    <?php include '../../includes/sidebar.php'; ?>

    <div class="ml-64 p-8 w-full">
        
        <div class="mb-8 border-b border-slate-200 pb-4">
            <h1 class="text-3xl font-bold text-slate-900">Publikasi Berita</h1>
            <p class="text-slate-500 mt-1">Kelola artikel dan pengumuman yang tampil di halaman depan website.</p>
        </div>

        <?= $pesan; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h2 class="font-bold text-lg mb-4 text-slate-800 border-b pb-2">Tulis Berita Baru</h2>
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Berita</label>
                            <input type="text" name="judul" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                            <select name="kategori" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Pengumuman">Pengumuman</option>
                                <option value="Prestasi">Prestasi</option>
                                <option value="Event">Event / Perlombaan</option>
                                <option value="Tips">Tips Latihan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar/Foto (Opsional)</label>
                            <input type="file" name="gambar" accept="image/*" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm">
                            <p class="text-xs text-gray-400 mt-1">Maksimal 2MB (JPG/PNG)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Berita</label>
                            <textarea name="isi" required rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                        </div>
                        <button type="submit" name="tambah_berita" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition">
                            Publikasikan Berita
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs tracking-wider">
                                <th class="p-4 font-bold">Judul & Kategori</th>
                                <th class="p-4 font-bold">Tanggal</th>
                                <th class="p-4 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(mysqli_num_rows($query_berita) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_berita)): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="p-4">
                                            <p class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($row['judul']) ?></p>
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100 mt-1 inline-block">
                                                <?= htmlspecialchars($row['kategori']) ?>
                                            </span>
                                        </td>
                                        <td class="p-4 text-sm text-slate-500">
                                            <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                        </td>
                                        <td class="p-4 text-center">
                                            <a href="?hapus=<?= $row['id'] ?>" 
                                               onclick="return confirm('Yakin ingin menghapus berita ini?');"
                                               class="inline-block bg-red-100 text-red-600 hover:bg-red-500 hover:text-white text-xs font-bold px-3 py-1.5 rounded transition">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="p-8 text-center text-slate-500">Belum ada berita yang dipublikasikan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</body>
</html>