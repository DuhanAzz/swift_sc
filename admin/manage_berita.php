<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "ceo")) { 
    header("location:../login.php?pesan=belum_login"); 
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
    $cabang = $user_cabang;
    
    $gambar = 'default_news.jpg';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        if (validasi_gambar($_FILES['gambar'])) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $gambar_name = uniqid('news_') . '.' . $ext;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar_name)) {
                    $gambar = 'uploads/' . $gambar_name;
                }
            }
        } else {
            exit("File tidak valid atau terlalu besar (Maks 2MB).");
        }
    }
    
    $query = "INSERT INTO berita (kategori, judul, isi, tanggal, cabang, gambar) VALUES ('$kategori', '$judul', '$isi', '$tanggal', '$cabang', '$gambar')";
    mysqli_query($koneksi, $query);
    header("Location: manage_berita.php?pesan=sukses_tambah");
    exit;
}

// Logika Edit Berita
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    
    $query_update = "UPDATE berita SET kategori='$kategori', judul='$judul', isi='$isi', tanggal='$tanggal' WHERE id='$id' AND cabang='$user_cabang'";
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        if (validasi_gambar($_FILES['gambar'])) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $gambar_name = uniqid('news_') . '.' . $ext;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar_name)) {
                    $gambar = 'uploads/' . $gambar_name;
                    $query_update = "UPDATE berita SET kategori='$kategori', judul='$judul', isi='$isi', tanggal='$tanggal', gambar='$gambar' WHERE id='$id' AND cabang='$user_cabang'";
                }
            }
        } else {
            exit("File tidak valid atau terlalu besar (Maks 2MB).");
        }
    }
    
    mysqli_query($koneksi, $query_update);
    header("Location: manage_berita.php?pesan=sukses_edit");
    exit;
}

// Logika Hapus Berita
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM berita WHERE id='$id' AND cabang='$user_cabang'");
    header("Location: manage_berita.php?pesan=sukses_hapus");
    exit;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Manajemen Berita</h1>
                <p class="text-sm text-gray-500">Kelola artikel, pengumuman, dan berita prestasi klub.</p>
            </div>
            <button data-modal-target="modalTambahBerita" data-modal-toggle="modalTambahBerita" class="text-white bg-algolia-blue hover:bg-algolia-darkblue focus:ring-4 focus:ring-blue-200 font-bold rounded-xl text-sm px-6 py-3 flex items-center transition-all shadow-lg shadow-blue-500/30">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Berita
            </button>
        </div>

        <div class="card overflow-hidden">
            <table class="table-algolia">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Cabang</th>
                        <th class="px-6 py-4">Judul Berita</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $beritaArray = [];
                    $q_str = "SELECT * FROM berita WHERE cabang = '$user_cabang' ORDER BY tanggal DESC, id DESC";
                    $query = mysqli_query($koneksi, $q_str);
                    if ($query) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $beritaArray[] = $row;
                        }
                    }

                    if (count($beritaArray) > 0) {
                        foreach ($beritaArray as $row) {
                    ?>
                    <tr class="hover:bg-slate-50 transition-all duration-200 border-b border-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-500 text-xs whitespace-nowrap"><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td class="px-6 py-4">
                            <?php 
                            $color = $row['kategori'] == 'Prestasi' ? 'text-orange-600 bg-orange-100' : ($row['kategori'] == 'Pengumuman' ? 'text-blue-600 bg-blue-100' : 'text-gray-600 bg-gray-100');
                            ?>
                            <span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-md <?= $color; ?>"><?= htmlspecialchars($row['kategori']); ?></span>
                        </td>
                        <td class="px-6 py-4 font-bold text-algolia-blue text-sm"><?= htmlspecialchars($row['cabang'] ?? 'Pusat'); ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($row['gambar']) && file_exists('uploads/' . basename($row['gambar']))): ?>
                                    <div class="w-10 h-10 rounded-lg bg-cover bg-center shadow-sm" style="background-image: url('uploads/<?= basename($row['gambar']) ?>');"></div>
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-xs shadow-sm">Img</div>
                                <?php endif; ?>
                                <span class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($row['judul']); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button data-modal-target="modalEditBerita_<?= $row['id']; ?>" data-modal-toggle="modalEditBerita_<?= $row['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-lg transition-colors shadow-sm" title="Edit Berita">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <a href="manage_berita.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus berita ini?')" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-colors shadow-sm flex items-center justify-center" title="Hapus Berita">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada berita atau pengumuman.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Berita -->
<div id="modalTambahBerita" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl mx-auto max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Buat Berita Baru</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-modal-toggle="modalTambahBerita">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="manage_berita.php" method="POST" enctype="multipart/form-data" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</label>
                        <select name="kategori" required class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm">
                            <option value="Prestasi">Prestasi</option>
                            <option value="Pengumuman">Pengumuman</option>
                            <option value="Artikel">Artikel Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Publish</label>
                        <input type="date" name="tanggal" required value="<?= date('Y-m-d'); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Berita</label>
                    <input type="text" name="judul" required class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm" placeholder="Contoh: Juara 1 Lomba Renang...">
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Brosur / Gambar (Opsional)</label>
                    <input type="file" name="gambar" accept=".jpg,.jpeg,.png" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl block w-full p-2.5 shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Isi Berita</label>
                    <textarea name="isi" rows="5" required class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm"></textarea>
                </div>
                <div class="flex justify-end border-t border-gray-100 pt-5 mt-2">
                    <button type="submit" name="tambah" class="text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-xl text-sm px-8 py-3.5 shadow-md hover:shadow-lg transition-all">Publish Berita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Render Edit Modals Outside Table -->
<?php
if (count($beritaArray) > 0) {
    foreach ($beritaArray as $row) {
?>
<div id="modalEditBerita_<?= $row['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl mx-auto max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Edit Berita</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-modal-toggle="modalEditBerita_<?= $row['id']; ?>">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="manage_berita.php" method="POST" enctype="multipart/form-data" class="p-6">
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</label>
                        <select name="kategori" required class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm">
                            <option value="Prestasi" <?= $row['kategori'] == 'Prestasi' ? 'selected' : ''; ?>>Prestasi</option>
                            <option value="Pengumuman" <?= $row['kategori'] == 'Pengumuman' ? 'selected' : ''; ?>>Pengumuman</option>
                            <option value="Artikel" <?= $row['kategori'] == 'Artikel' ? 'selected' : ''; ?>>Artikel Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Publish</label>
                        <input type="date" name="tanggal" required value="<?= $row['tanggal']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Berita</label>
                    <input type="text" name="judul" required value="<?= htmlspecialchars($row['judul']); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm">
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Update Gambar (Opsional)</label>
                    <input type="file" name="gambar" accept=".jpg,.jpeg,.png" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl block w-full p-2.5 shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Isi Berita</label>
                    <textarea name="isi" rows="5" required class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm"><?= htmlspecialchars($row['isi']); ?></textarea>
                </div>
                <div class="flex justify-end border-t border-gray-100 pt-5 mt-2">
                    <button type="submit" name="edit" class="text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-xl text-sm px-8 py-3.5 shadow-md hover:shadow-lg transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
    }
}
?>

<?php include '../includes/footer.php'; ?>
