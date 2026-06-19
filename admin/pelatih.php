<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "sukses_tambah"){
                echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
                        <span class="font-medium">Berhasil!</span> Data pelatih baru telah ditambahkan.
                      </div>';
            } else if($_GET['pesan'] == "sukses_edit"){
                echo '<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200" role="alert">
                        <span class="font-medium">Update Sukses!</span> Perubahan data pelatih tersimpan.
                      </div>';
            } else if($_GET['pesan'] == "sukses_hapus"){
                echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
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
            <button data-modal-target="modalTambahPelatih" data-modal-toggle="modalTambahPelatih" class="text-white bg-algolia-blue hover:bg-algolia-darkblue focus:ring-4 focus:ring-blue-200 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center transition-all shadow-lg">
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
                    $q_pelatih = mysqli_query($koneksi, "SELECT p.*, u.email, u.username, c.nama_cabang as nama_kolam FROM pelatih p LEFT JOIN cabang c ON p.id_kolam = c.id LEFT JOIN users u ON p.user_id = u.id WHERE p.id_kolam = '$pool_id' ORDER BY p.id DESC");
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
                        <td class="px-6 py-4"><span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded border border-indigo-400"><?= htmlspecialchars($data['sertifikasi']); ?></span></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['jabatan']); ?></td>
                        <td class="px-6 py-4 font-semibold text-indigo-700"><?= htmlspecialchars($data['nama_kolam'] ?? $data['cabang']); ?></td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <button data-modal-target="modalEditPelatih<?= $data['id']; ?>" data-modal-toggle="modalEditPelatih<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                            <a href="hapus_pelatih.php?id=<?= $data['id']; ?>" onclick="return confirm('Hapus pelatih <?= $data['nama']; ?>?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>

                    <div id="modalEditPelatih<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-xl shadow-lg border border-panel-border">
                                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                                    <h3 class="text-base font-semibold text-algolia-navy">Edit Data Pelatih</h3>
                                    <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="modalEditPelatih<?= $data['id']; ?>">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                    </button>
                                </div>
                                <form action="proses_pelatih.php" method="POST" class="p-6 text-left" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <input type="hidden" name="user_id" value="<?= $data['user_id']; ?>">
                                    
                                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama & Akun Login</label>
                                    <input type="text" name="nama_pelatih" value="<?= htmlspecialchars($data['nama']); ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm mb-3" required>
                                    <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? ''); ?>" placeholder="Email Login" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm mb-3" required>
                                    <input type="password" name="password_baru" placeholder="Password Baru (Kosongkan jika tidak ubah)" class="bg-yellow-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm mb-5">
                                    
                                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Profil Pelatih</label>
                                    <div class="mb-5">
                                        <select name="lisensi" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm" required>
                                            <option value="Lisensi D" <?= ($data['sertifikasi'] == 'Lisensi D') ? 'selected' : '' ?>>Lisensi D (Pemula)</option>
                                            <option value="Lisensi C" <?= ($data['sertifikasi'] == 'Lisensi C') ? 'selected' : '' ?>>Lisensi C (Menengah)</option>
                                            <option value="Lisensi B" <?= ($data['sertifikasi'] == 'Lisensi B') ? 'selected' : '' ?>>Lisensi B (Lanjutan)</option>
                                            <option value="Lisensi Nasional" <?= ($data['sertifikasi'] == 'Lisensi Nasional') ? 'selected' : '' ?>>Lisensi Nasional</option>
                                        </select>
                                    </div>
                                    <div class="mb-5">
                                        <input type="text" name="no_hp" value="<?= htmlspecialchars($data['jabatan']); ?>" placeholder="No. HP / Jabatan" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm" required>
                                    </div>
                                    <div class="mb-6">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi Melatih</label>
                                        <select name="id_kolam" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm" required>
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
                                    <button type="submit" name="edit" class="w-full text-white bg-algolia-blue hover:bg-algolia-darkblue font-bold rounded-xl text-sm px-5 py-3 shadow-md transition-all">Update Data Pelatih</button>
                                </form>
                            </div>
                        </div>
                    </div>
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

<div id="modalTambahPelatih" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-xl shadow-lg border border-panel-border">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-base font-semibold text-algolia-navy">Tambah Data Pelatih</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="modalTambahPelatih">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="proses_pelatih.php" method="POST" class="p-6 text-left" enctype="multipart/form-data">
                <input type="hidden" name="id_kolam" value="<?= $pool_id; ?>">
                
                <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama & Akun Login</label>
                <input type="text" name="nama_pelatih" placeholder="Nama Lengkap" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm mb-3" required>
                <input type="email" name="email" placeholder="Email untuk Login" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm mb-3" required>
                <input type="password" name="password" placeholder="Password Login" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm mb-5" required>
                
                <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Profil Pelatih</label>
                <div class="mb-5">
                    <select name="lisensi" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm" required>
                        <option value="">-- Pilih Lisensi --</option>
                        <option value="Lisensi D">Lisensi D (Pemula)</option>
                        <option value="Lisensi C">Lisensi C (Menengah)</option>
                        <option value="Lisensi B">Lisensi B (Lanjutan)</option>
                        <option value="Lisensi Nasional">Lisensi Nasional</option>
                    </select>
                </div>
                <div class="mb-5">
                    <input type="text" name="no_hp" placeholder="No. HP / Jabatan" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm" required>
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-xs font-bold text-gray-500">Foto Pelatih</label>
                    <input type="file" name="foto_pelatih" accept="image/*" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue block w-full p-2 shadow-sm">
                </div>
                
                <button type="submit" name="tambah" class="w-full text-white bg-algolia-blue hover:bg-algolia-darkblue font-bold rounded-xl text-sm px-5 py-3 shadow-md transition-all">+ Tambah Pelatih</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>