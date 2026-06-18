<?php
session_start();
// Proteksi: Hanya user yang login yang boleh mengakses
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}
$user_role = $_SESSION['role'];
$user_cabang = $_SESSION['cabang'] ?? 'Pusat';

include '../includes/koneksi.php';

// Logika Tambah Berita
if (isset($_POST['tambah'])) {
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $cabang = ($user_role === 'admin' && isset($_POST['cabang'])) ? mysqli_real_escape_string($koneksi, $_POST['cabang']) : $user_cabang;
    
    $gambar = 'default_news.jpg';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $gambar_name = uniqid('news_') . '.' . $ext;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar_name)) {
                $gambar = 'uploads/' . $gambar_name;
            }
        }
    }
    
    $query = "INSERT INTO berita (kategori, judul, isi, tanggal, cabang, gambar) VALUES ('$kategori', '$judul', '$isi', '$tanggal', '$cabang', '$gambar')";
    mysqli_query($koneksi, $query);
    header("Location: manage_berita.php");
    exit;
}

// Logika Hapus Berita
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM berita WHERE id='$id'");
    header("Location: manage_berita.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Swift SC Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

    <?php include '../includes/sidebar.php'; ?>

    <div class="p-4 sm:ml-64 pt-20">
        <div class="container mx-auto">
            
            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-800 uppercase">Manajemen Berita (CMS)</h1>
                <p class="text-slate-500 mt-1">Kelola artikel, pengumuman, dan berita prestasi klub.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-2xl card-hover sticky top-24">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Buat Berita Baru</h3>
                        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <?php if ($user_role === 'admin'): ?>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Cabang / Pool</label>
                                <select name="cabang" required class="w-full border border-[#E8E8EF] rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="Pusat">Pusat</option>
                                    <option value="Cabang Utara">Cabang Utara</option>
                                    <option value="Cabang Selatan">Cabang Selatan</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                                <select name="kategori" required class="w-full border border-[#E8E8EF] rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition-all duration-200">
                                    <option value="Prestasi">Prestasi</option>
                                    <option value="Pengumuman">Pengumuman</option>
                                    <option value="Artikel">Artikel Umum</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Berita</label>
                                <input type="text" name="judul" required class="w-full border border-[#E8E8EF] rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Brosur / Gambar (Opsional)</label>
                                <input type="file" name="gambar" accept=".jpg,.jpeg,.png" class="w-full border border-[#E8E8EF] rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Publish</label>
                                <input type="date" name="tanggal" required class="w-full border border-[#E8E8EF] rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition-all duration-200" value="<?= date('Y-m-d'); ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Berita</label>
                                <textarea name="isi" rows="5" required class="w-full border border-[#E8E8EF] rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition-all duration-200"></textarea>
                            </div>
                            <button type="submit" name="tambah" class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700 hover:shadow-lg transition-all duration-200">Publish Berita</button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="card overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-800 text-white text-sm uppercase">
                                <tr>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4">Kategori</th>
                                    <th class="p-4">Cabang</th>
                                    <th class="p-4">Judul Berita</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php
                                $q_str = "SELECT * FROM berita";
                                if ($user_role !== 'admin') {
                                    $q_str .= " WHERE cabang = '$user_cabang'";
                                }
                                $q_str .= " ORDER BY tanggal DESC, id DESC";
                                $query = mysqli_query($koneksi, $q_str);
                                while ($row = mysqli_fetch_assoc($query)) :
                                ?>
                                <tr class="hover:bg-slate-50 transition-all duration-200">
                                    <td class="p-4 text-sm font-semibold text-slate-500"><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                                    <td class="p-4">
                                        <?php 
                                        // Mewarnai badge kategori agar lebih cantik
                                        $color = $row['kategori'] == 'Prestasi' ? 'text-orange-600 bg-orange-100' : ($row['kategori'] == 'Pengumuman' ? 'text-blue-600 bg-blue-100' : 'text-gray-600 bg-gray-100');
                                        ?>
                                        <span class="text-xs font-bold px-2 py-1 rounded <?= $color; ?>"><?= $row['kategori']; ?></span>
                                    </td>
                                    <td class="p-4 text-sm text-orange-600 font-bold"><?= $row['cabang'] ?? 'Pusat'; ?></td>
                                    <td class="p-4 font-bold text-slate-800">
                                        <div class="flex items-center gap-3">
                                            <?php if (!empty($row['gambar']) && file_exists($row['gambar'])): ?>
                                                <div class="w-10 h-10 rounded-md bg-cover bg-center shadow-sm" style="background-image: url('<?= $row['gambar'] ?>');"></div>
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-md bg-slate-200 flex items-center justify-center text-slate-400 text-xs shadow-sm">No Img</div>
                                            <?php endif; ?>
                                            <span><?= $row['judul']; ?></span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="manage_berita.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus berita ini?')" class="bg-red-100 text-red-600 px-3 py-1 rounded-md text-xs font-bold hover:bg-red-200 hover:shadow transition-all duration-200">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>