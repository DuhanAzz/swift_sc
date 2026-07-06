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

// Logika Tambah Pelatih
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $sertifikasi = mysqli_real_escape_string($koneksi, $_POST['sertifikasi']);
    $cabang = ($user_role === 'admin' && isset($_POST['cabang'])) ? mysqli_real_escape_string($koneksi, $_POST['cabang']) : $user_cabang;

    $foto = 'default_coach.jpg';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $foto_name = uniqid('coach_') . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $foto_name)) {
                $foto = 'uploads/' . $foto_name;
            }
        }
    }
    
    $query = "INSERT INTO pelatih (nama, jabatan, sertifikasi, cabang, foto) VALUES ('$nama', '$jabatan', '$sertifikasi', '$cabang', '$foto')";
    mysqli_query($koneksi, $query);
    header("Location: manage_pelatih.php");
    exit;
}

// Logika Edit Pelatih
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $sertifikasi = mysqli_real_escape_string($koneksi, $_POST['sertifikasi']);
    $cabang = ($user_role === 'admin' && isset($_POST['cabang'])) ? mysqli_real_escape_string($koneksi, $_POST['cabang']) : $user_cabang;

    $query_update = "UPDATE pelatih SET nama='$nama', jabatan='$jabatan', sertifikasi='$sertifikasi', cabang='$cabang' WHERE id='$id'";
    mysqli_query($koneksi, $query_update);

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $foto_name = uniqid('coach_') . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $foto_name)) {
                mysqli_query($koneksi, "UPDATE pelatih SET foto='uploads/$foto_name' WHERE id='$id'");
            }
        }
    }
    header("Location: manage_pelatih.php");
    exit;
}

// Logika Hapus Pelatih
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pelatih WHERE id='$id'");
    header("Location: manage_pelatih.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelatih - Swift SC Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tambahkan library flowbite untuk modal yang lebih mulus jika diperlukan, atau css manual -->
    <style>
        .hidden { display: none; }
    </style>
</head>
<body class="bg-slate-50">

<?php include '../includes/sidebar.php'; ?>

    <div class="p-4 sm:ml-64 pt-20">
        <div class="container mx-auto">
            
            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-800 uppercase">Manajemen Pelatih (CMS)</h1>
                <p class="text-slate-500 mt-1">Atur data pelatih yang akan ditampilkan di halaman publik.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Tambah Pelatih Baru</h3>
                        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <?php if ($user_role === 'admin'): ?>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Cabang / Pool</label>
                                <select name="cabang" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="Pusat">Pusat</option>
                                    <option value="Cabang Utara">Cabang Utara</option>
                                    <option value="Cabang Selatan">Cabang Selatan</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama" required class="w-full border border-gray-300 rounded-lg p-2 flex-1 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan / Posisi</label>
                                <input type="text" name="jabatan" required placeholder="Contoh: Head Coach" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Pelatih</label>
                                <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" class="w-full border border-gray-300 rounded-lg p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Sertifikasi & Deskripsi</label>
                                <textarea name="sertifikasi" rows="4" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            <button type="submit" name="tambah" class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition-all duration-200">Simpan Pelatih</button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-800 text-white text-sm uppercase">
                                <tr>
                                    <th class="p-4 whitespace-nowrap">Foto</th>
                                    <th class="p-4 whitespace-nowrap">Nama Pelatih</th>
                                    <th class="p-4 whitespace-nowrap">Cabang</th>
                                    <th class="p-4 whitespace-nowrap">Jabatan</th>
                                    <th class="p-4 min-w-[200px]">Sertifikasi</th>
                                    <th class="p-4 text-center whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php
                                $q_str = "SELECT * FROM pelatih";
                                if ($user_role !== 'admin') {
                                    $q_str .= " WHERE cabang = '$user_cabang'";
                                }
                                $q_str .= " ORDER BY id DESC";
                                $query = mysqli_query($koneksi, $q_str);
                                while ($row = mysqli_fetch_assoc($query)) :
                                    $foto_path = !empty($row['foto']) && file_exists($row['foto']) ? $row['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($row['nama']) . '&background=random';
                                ?>
                                <tr class="hover:bg-slate-50 transition-all duration-200 align-top">
                                    <td class="p-4">
                                        <div class="w-12 h-12 rounded-full bg-cover bg-center shadow-sm" style="background-image: url('<?= $foto_path ?>');"></div>
                                    </td>
                                    <td class="p-4 font-bold text-slate-800"><?= $row['nama']; ?></td>
                                    <td class="p-4 text-sm text-orange-600 font-bold"><?= $row['cabang'] ?? 'Pusat'; ?></td>
                                    <td class="p-4 text-sm text-blue-600 font-semibold"><?= $row['jabatan']; ?></td>
                                    <td class="p-4 text-xs text-slate-500 whitespace-pre-wrap"><?= $row['sertifikasi']; ?></td>
                                    <td class="p-4 text-center space-y-2 whitespace-nowrap">
                                        <button onclick="document.getElementById('modalEdit<?= $row['id'] ?>').classList.remove('hidden')" class="bg-blue-100 text-blue-600 px-3 py-1 rounded-md text-xs font-bold hover:bg-blue-200 hover:shadow transition-all duration-200 block w-full">Edit</button>
                                        <a href="manage_pelatih.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus pelatih ini?')" class="bg-red-100 text-red-600 px-3 py-1 rounded-md text-xs font-bold hover:bg-red-200 hover:shadow transition-all duration-200 block w-full text-center">Hapus</a>
                                    </td>
                                </tr>

                                <!-- Edit Modal (Pure Tailwind/JS wrapper) -->
                                <div id="modalEdit<?= $row['id'] ?>" class="hidden fixed inset-0 z-50 overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm flex justify-center items-center px-4">
                                    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl">
                                        <div class="flex items-center justify-between p-4 border-b rounded-t">
                                            <h3 class="text-lg font-bold text-gray-900">Edit Data Pelatih</h3>
                                            <button onclick="document.getElementById('modalEdit<?= $row['id'] ?>').classList.add('hidden')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 focus:outline-none flex justify-center items-center">
                                                ✖
                                            </button>
                                        </div>
                                        <div class="p-5 text-left">
                                            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                
                                                <?php if ($user_role === 'admin'): ?>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cabang / Pool</label>
                                                    <select name="cabang" required class="w-full border border-gray-300 rounded-lg p-2">
                                                        <option value="Pusat" <?= $row['cabang'] == 'Pusat' ? 'selected' : '' ?>>Pusat</option>
                                                        <option value="Cabang Utara" <?= $row['cabang'] == 'Cabang Utara' ? 'selected' : '' ?>>Cabang Utara</option>
                                                        <option value="Cabang Selatan" <?= $row['cabang'] == 'Cabang Selatan' ? 'selected' : '' ?>>Cabang Selatan</option>
                                                    </select>
                                                </div>
                                                <?php else: ?>
                                                <input type="hidden" name="cabang" value="<?= $row['cabang']; ?>">
                                                <?php endif; ?>
                                                
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                                                    <input type="text" name="nama" value="<?= $row['nama']; ?>" required class="w-full border border-gray-300 rounded-lg p-2">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan / Posisi</label>
                                                    <input type="text" name="jabatan" value="<?= $row['jabatan']; ?>" required class="w-full border border-gray-300 rounded-lg p-2">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ganti Foto <span class="text-xs font-normal text-slate-400">(Biarkan kosong jika tidak diganti)</span></label>
                                                    <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" class="w-full border border-gray-300 rounded-lg p-2 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sertifikasi</label>
                                                    <textarea name="sertifikasi" rows="4" required class="w-full border border-gray-300 rounded-lg p-2"><?= htmlspecialchars($row['sertifikasi']); ?></textarea>
                                                </div>
                                                
                                                <div class="pt-3 flex justify-end gap-3">
                                                    <button type="button" onclick="document.getElementById('modalEdit<?= $row['id'] ?>').classList.add('hidden')" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                                                    <button type="submit" name="edit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 shadow transition-colors">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>