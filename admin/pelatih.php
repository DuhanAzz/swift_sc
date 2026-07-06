<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    header("location:../login.php?pesan=belum_login"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "sukses_tambah"){
                echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200" role="alert">
                        <span class="font-medium">Berhasil!</span> Data pelatih baru telah ditambahkan.
                      </div>';
            } else if($_GET['pesan'] == "sukses_edit"){
                echo '<div class="p-4 mb-4 text-sm text-blue-800 rounded-xl bg-blue-50 border border-blue-200" role="alert">
                        <span class="font-medium">Update Sukses!</span> Perubahan data pelatih tersimpan.
                      </div>';
            } else if($_GET['pesan'] == "sukses_hapus"){
                echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200" role="alert">
                        <span class="font-medium">Terhapus!</span> Data pelatih telah dihapus.
                      </div>';
            }
        }
        ?>
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Manajemen Pelatih</h1>
                <p class="text-sm text-gray-500">Kelola data instruktur dan pelatih Swift Swimming Club</p>
            </div>
            <button data-modal-target="modalTambahPelatih" data-modal-toggle="modalTambahPelatih" class="text-white bg-algolia-blue hover:bg-algolia-darkblue focus:ring-4 focus:ring-blue-200 font-bold rounded-xl text-sm px-6 py-3 flex items-center transition-all shadow-lg shadow-blue-500/30">
                <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/></svg>
                Tambah Pelatih
            </button>
        </div>

        <div class="card overflow-hidden">
            <table class="table-algolia">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Pelatih</th>
                        <th class="px-6 py-4 text-center">Total Anak Didik</th>
                        <th class="px-6 py-4">Lisensi</th>
                        <th class="px-6 py-4">No. HP</th>
                        <th class="px-6 py-4">Lokasi Melatih</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $coachesArray = [];
                    $pool_id = $_SESSION['pool_id'] ?? "NULL";
                    $q_pelatih = mysqli_query($koneksi, "SELECT p.*, u.email, u.username, c.nama_cabang as nama_kolam, (SELECT COUNT(id) FROM member WHERE pelatih_id = p.id) as total_anak_didik FROM pelatih p LEFT JOIN cabang c ON p.id_kolam = c.id LEFT JOIN users u ON p.user_id = u.id WHERE p.id_kolam = '$pool_id' ORDER BY p.id DESC");
                    if($q_pelatih) {
                        while($row = mysqli_fetch_assoc($q_pelatih)) {
                            $coachesArray[] = $row;
                        }
                    }

                    if(count($coachesArray) > 0) {
                        foreach($coachesArray as $data) {
                    ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $no++; ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['nama']); ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-teal-100 text-teal-800 text-xs font-bold px-2.5 py-1 rounded-full border border-teal-200">
                                <?= $data['total_anak_didik']; ?> Atlet
                            </span>
                        </td>
                        <td class="px-6 py-4"><span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded border border-indigo-400"><?= htmlspecialchars($data['sertifikasi']); ?></span></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['jabatan']); ?></td>
                        <td class="px-6 py-4 font-semibold text-indigo-700"><?= htmlspecialchars($data['nama_kolam'] ?? $data['cabang']); ?></td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button data-modal-target="modalEdit_<?= $data['id']; ?>" data-modal-toggle="modalEdit_<?= $data['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-lg transition-colors shadow-sm" title="Edit Pelatih">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <a href="hapus_pelatih.php?id=<?= $data['id']; ?>" onclick="return confirm('Yakin ingin menghapus?');" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-colors shadow-sm flex items-center justify-center" title="Hapus Pelatih">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>


                    <?php 
                        } 
                    } else {
                        echo '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 py-6">Belum ada data pelatih.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
if(count($coachesArray) > 0) {
    foreach($coachesArray as $data) {
?>
<div id="modalEdit_<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl mx-auto max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl shadow-slate-900/20 border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Edit Data Pelatih</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="modalEdit_<?= $data['id']; ?>">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="proses_pelatih.php" method="POST" class="p-6" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                <input type="hidden" name="user_id" value="<?= $data['user_id']; ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="nama_pelatih" value="<?= htmlspecialchars($data['nama']); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Email Login</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? ''); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Password Baru</label>
                        <input type="password" name="password_baru" placeholder="(Kosongkan jika tidak ubah)" class="bg-yellow-50 border border-yellow-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Lisensi</label>
                        <input type="text" name="lisensi" value="<?= htmlspecialchars($data['lisensi'] ?? ''); ?>" placeholder="Lisensi Pelatih" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas Mengajar</label>
                        <select name="kelas_mengajar" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                            <option value="Pemula" <?= (($data['kelas_mengajar'] ?? '') == 'Pemula') ? 'selected' : '' ?>>Pemula</option>
                            <option value="Reguler" <?= (($data['kelas_mengajar'] ?? '') == 'Reguler') ? 'selected' : '' ?>>Reguler</option>
                            <option value="Prestasi" <?= (($data['kelas_mengajar'] ?? '') == 'Prestasi') ? 'selected' : '' ?>>Prestasi</option>
                            <option value="Privat" <?= (($data['kelas_mengajar'] ?? '') == 'Privat') ? 'selected' : '' ?>>Privat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP</label>
                        <input type="tel" name="no_hp" value="<?= htmlspecialchars($data['no_hp'] ?? ''); ?>" placeholder="08xxxxxxxxxx" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Jabatan</label>
                        <input type="text" name="jabatan" value="<?= htmlspecialchars($data['jabatan']); ?>" placeholder="Contoh: Head Coach" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi Melatih</label>
                        <select name="id_kolam" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                            <?php
                            $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                            if($q_kolam) {
                                while($k = mysqli_fetch_assoc($q_kolam)) {
                                    $k_id = $k['id'];
                                    $select = ($k_id == $data['cabang']) ? 'selected' : '';
                                    echo "<option value='".$k_id."' $select>".htmlspecialchars($k['nama_cabang'])."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Foto Pelatih Baru (Opsional)</label>
                        <input type="file" name="foto_pelatih" accept="image/*" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm transition-all">
                        <p class="text-xs text-gray-500 mt-2 font-medium">Kosongkan jika tidak ingin mengubah foto utama.</p>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block mb-2 text-sm font-bold text-gray-800">Foto Hover 1 (Action) <span class="text-xs text-gray-500 font-medium">(Opsional)</span></label>
                        <input type="file" name="foto_hover_pelatih" accept="image/*" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm transition-all">
                        <p class="text-xs text-gray-500 mt-2 font-medium">Kosongkan jika tidak ingin mengubah foto hover 1.</p>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block mb-2 text-sm font-bold text-gray-800">Foto Hover 2 (Action) <span class="text-xs text-gray-500 font-medium">(Opsional)</span></label>
                        <input type="file" name="foto_hover_2" accept="image/*" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm transition-all">
                        <p class="text-xs text-gray-500 mt-2 font-medium">Kosongkan jika tidak ingin mengubah foto hover 2.</p>
                    </div>
                </div>
                
                <div class="flex justify-end border-t border-gray-100 pt-5 mt-2">
                    <button type="submit" name="edit" class="text-white bg-teal-600 hover:bg-teal-700 font-bold rounded-xl text-sm px-8 py-3.5 shadow-md hover:shadow-lg transition-all focus:ring-4 focus:ring-teal-200">Update Data Pelatih</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
    }
}
?>

<div id="modalTambahPelatih" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl mx-auto max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl shadow-slate-900/20 border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Tambah Data Pelatih</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="modalTambahPelatih">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="proses_pelatih.php" method="POST" class="p-6" enctype="multipart/form-data">
                <input type="hidden" name="id_kolam" value="<?= $pool_id; ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="nama_pelatih" placeholder="Nama Lengkap" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Email Login</label>
                        <input type="email" name="email" placeholder="Email untuk Login" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Password Login</label>
                        <input type="password" name="password" placeholder="Password Login" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Lisensi</label>
                        <input type="text" name="lisensi" placeholder="Lisensi Pelatih" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas Mengajar</label>
                        <select name="kelas_mengajar" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                            <option value="">-- Pilih Kelas --</option>
                            <option value="Pemula">Pemula</option>
                            <option value="Reguler">Reguler</option>
                            <option value="Prestasi">Prestasi</option>
                            <option value="Privat">Privat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP</label>
                        <input type="tel" name="no_hp" placeholder="08xxxxxxxxxx" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Jabatan</label>
                        <input type="text" name="jabatan" placeholder="Contoh: Head Coach" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi Melatih</label>
                        <select name="id_kolam" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                            <?php
                            $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                            if($q_kolam) {
                                while($k = mysqli_fetch_assoc($q_kolam)) {
                                    $k_id = $k['id'];
                                    $select = ($k_id == $pool_id) ? 'selected' : '';
                                    echo "<option value='".$k_id."' $select>".htmlspecialchars($k['nama_cabang'])."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Foto Pelatih (Opsional)</label>
                        <input type="file" name="foto_pelatih" accept="image/*" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm transition-all">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block mb-2 text-sm font-bold text-gray-800">Foto Hover 1 (Action) <span class="text-red-500">*</span></label>
                        <input type="file" name="foto_hover_pelatih" accept="image/*" required class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm transition-all">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block mb-2 text-sm font-bold text-gray-800">Foto Hover 2 (Action) <span class="text-red-500">*</span></label>
                        <input type="file" name="foto_hover_2" accept="image/*" required class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-3 shadow-sm transition-all">
                    </div>
                </div>
                
                <div class="flex justify-end border-t border-gray-100 pt-5 mt-2">
                    <button type="submit" name="tambah" class="text-white bg-teal-600 hover:bg-teal-700 font-bold rounded-xl text-sm px-8 py-3.5 shadow-md hover:shadow-lg transition-all focus:ring-4 focus:ring-teal-200">+ Tambah Pelatih</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>