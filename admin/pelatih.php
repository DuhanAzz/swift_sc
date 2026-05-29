<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
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
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Pelatih</h1>
                <p class="text-sm text-gray-500">Kelola data instruktur dan pelatih Swift Swimming Club</p>
            </div>
            <button data-modal-target="modalTambahPelatih" data-modal-toggle="modalTambahPelatih" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center transition-all shadow-lg">
                <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/></svg>
                Tambah Pelatih
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-white uppercase bg-indigo-800">
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
                    try {
                        $documents = $database->getDocuments('coaches');
                        foreach ($documents as $data) {
                            $data['nama_kolam'] = '-';
                            if (!empty($data['id_kolam'])) {
                                try {
                                    $poolDoc = $database->getDocument('pools', $data['id_kolam']);
                                    if ($poolDoc) {
                                        $data['nama_kolam'] = $poolDoc['name'];
                                    }
                                } catch (\Exception $e) {}
                            }
                            $coachesArray[] = $data;
                        }
                    } catch (\Exception $e) {}

                    usort($coachesArray, function($a, $b) { return strcmp($b['id'], $a['id']); });

                    if(count($coachesArray) > 0) {
                        foreach($coachesArray as $data) {
                    ?>
                    <tr class="bg-white border-b hover:bg-indigo-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $no++; ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['nama_pelatih']); ?></td>
                        <td class="px-6 py-4"><span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded border border-indigo-400"><?= htmlspecialchars($data['lisensi']); ?></span></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['no_hp']); ?></td>
                        <td class="px-6 py-4 font-semibold text-indigo-700"><?= htmlspecialchars($data['nama_kolam']); ?></td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <button data-modal-target="modalEditPelatih<?= $data['id']; ?>" data-modal-toggle="modalEditPelatih<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                            <a href="hapus_pelatih.php?id=<?= $data['id']; ?>" onclick="return confirm('Hapus pelatih <?= $data['nama_pelatih']; ?>?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>

                    <div id="modalEditPelatih<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100">
                                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Edit Data Pelatih</h3>
                                    <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="modalEditPelatih<?= $data['id']; ?>">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                    </button>
                                </div>
                                <form action="proses_pelatih.php" method="POST" class="p-6 text-left">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <div class="mb-5">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pelatih</label>
                                        <input type="text" name="nama_pelatih" value="<?= $data['nama_pelatih']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3 shadow-sm" required>
                                    </div>
                                    <div class="mb-5">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Lisensi</label>
                                        <select name="lisensi" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3 shadow-sm" required>
                                            <option value="Lisensi D" <?= ($data['lisensi'] == 'Lisensi D') ? 'selected' : '' ?>>Lisensi D (Pemula)</option>
                                            <option value="Lisensi C" <?= ($data['lisensi'] == 'Lisensi C') ? 'selected' : '' ?>>Lisensi C (Menengah)</option>
                                            <option value="Lisensi B" <?= ($data['lisensi'] == 'Lisensi B') ? 'selected' : '' ?>>Lisensi B (Lanjutan)</option>
                                            <option value="Lisensi Nasional" <?= ($data['lisensi'] == 'Lisensi Nasional') ? 'selected' : '' ?>>Lisensi Nasional</option>
                                        </select>
                                    </div>
                                    <div class="mb-5">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP</label>
                                        <input type="text" name="no_hp" value="<?= $data['no_hp']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3 shadow-sm" required>
                                    </div>
                                    <div class="mb-6">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi Melatih</label>
                                        <select name="id_kolam" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3 shadow-sm" required>
                                            <?php
                                            try {
                                                $q_kolam = $database->getDocuments('pools');
                                                foreach($q_kolam as $k) {
                                                    $k_id = $k['id'];
                                                    $select = ($k_id == $data['id_kolam']) ? 'selected' : '';
                                                    echo "<option value='".$k_id."' $select>".htmlspecialchars($k['name'])."</option>";
                                                }
                                            } catch (\Exception $e) {}
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="edit" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 font-bold rounded-xl text-sm px-5 py-3 shadow-md transition-all">Update Data Pelatih</button>
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
        <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Pendaftaran Pelatih Baru</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="modalTambahPelatih">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="proses_pelatih.php" method="POST" class="p-6 text-left">
                <div class="mb-5">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="nama_pelatih" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-indigo-600 focus:border-indigo-600 block w-full p-3 shadow-sm" placeholder="Contoh: Coach Richard" required>
                </div>
                <div class="mb-5">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tingkat Lisensi</label>
                    <select name="lisensi" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-indigo-600 focus:border-indigo-600 block w-full p-3 shadow-sm" required>
                        <option value="" disabled selected>-- Pilih Lisensi --</option>
                        <option value="Lisensi D">Lisensi D (Pemula)</option>
                        <option value="Lisensi C">Lisensi C (Menengah)</option>
                        <option value="Lisensi B">Lisensi B (Lanjutan)</option>
                        <option value="Lisensi Nasional">Lisensi Nasional</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-indigo-600 focus:border-indigo-600 block w-full p-3 shadow-sm" placeholder="Contoh: 0812..." required>
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tugaskan di Kolam</label>
                    <select name="id_kolam" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-indigo-600 focus:border-indigo-600 block w-full p-3 shadow-sm" required>
                        <option value="" disabled selected>-- Pilih Lokasi --</option>
                        <?php
                        try {
                            $query_kolam = $database->getDocuments('pools');
                            foreach($query_kolam as $kolam) {
                                $kolam_id = $kolam['id'];
                                echo "<option value='".$kolam_id."'>".htmlspecialchars($kolam['name'])."</option>";
                            }
                        } catch (\Exception $e) {}
                        ?>
                    </select>
                </div>
                <button type="submit" name="tambah" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 font-bold rounded-xl text-sm px-5 py-3 shadow-md transition-all">Simpan Data Pelatih</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>