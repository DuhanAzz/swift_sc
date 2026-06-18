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
                        <span class="font-medium">Berhasil!</span> Data baru telah berhasil ditambahkan ke dalam sistem.
                      </div>';
            } else if($_GET['pesan'] == "sukses_edit"){
                echo '<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200" role="alert">
                        <span class="font-medium">Update Sukses!</span> Perubahan data telah berhasil disimpan.
                      </div>';
            } else if($_GET['pesan'] == "sukses_hapus"){
                echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
                        <span class="font-medium">Terhapus!</span> Data telah berhasil dihapus dari sistem.
                      </div>';
            }
        }
        ?>
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Atlet</h1>
                <p class="text-sm text-gray-500">Kelola data atlet Swift Swimming Club</p>
            </div>
            <button data-modal-target="modalTambahAtlet" data-modal-toggle="modalTambahAtlet" class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center transition-all shadow-lg">
                <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/></svg>
                Tambah Atlet
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-white uppercase bg-teal-800">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Atlet</th>
                        <th class="px-6 py-4">Gender</th>
                        <th class="px-6 py-4">No. HP</th>
                        <th class="px-6 py-4">Cabang Latihan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $atletArray = [];
                    $q_atlet = mysqli_query($koneksi, "SELECT m.*, c.nama_cabang as nama_kolam FROM member m LEFT JOIN cabang c ON m.cabang_id = c.id WHERE m.role='Atlet' ORDER BY m.id DESC");
                    if($q_atlet) {
                        while($row = mysqli_fetch_assoc($q_atlet)) {
                            $atletArray[] = $row;
                        }
                    }

                    // Cek apakah ada datanya
                    if(count($atletArray) > 0) {
                        foreach($atletArray as $data) {
                    ?>
                    <tr class="bg-white border-b hover:bg-teal-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $no++; ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['nama']); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['jenis_kelamin']); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['no_hp']); ?></td>
                        <td class="px-6 py-4 font-semibold text-primary-700"><?= htmlspecialchars($data['nama_kolam']); ?></td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <button data-modal-target="modalEditAtlet<?= $data['id']; ?>" data-modal-toggle="modalEditAtlet<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                            <a href="hapus_atlet.php?id=<?= $data['id']; ?>" onclick="return confirm('Hapus atlet <?= $data['nama']; ?>?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>

                    <div id="modalEditAtlet<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100">
                                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Edit Data Atlet</h3>
                                    <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-modal-toggle="modalEditAtlet<?= $data['id']; ?>">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                    </button>
                                </div>
                                <form action="proses_atlet.php" method="POST" class="p-6 text-left">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    
                                    <div class="mb-5">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                                        <input type="text" name="nama" value="<?= $data['nama']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 shadow-sm transition-all" required>
                                    </div>
                                    
                                    <div class="mb-5">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 shadow-sm transition-all" required>
                                            <option value="Laki-laki" <?= ($data['jenis_kelamin'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="Perempuan" <?= ($data['jenis_kelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-5">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP</label>
                                        <input type="text" name="no_hp" value="<?= $data['no_hp']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 shadow-sm transition-all" required>
                                    </div>
                                    
                                    <div class="mb-6">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Cabang Latihan</label>
                                        <select name="id_kolam" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 shadow-sm transition-all" required>
                                            <?php
                                            $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                                            if($q_kolam) {
                                                while($k = mysqli_fetch_assoc($q_kolam)) {
                                                    $k_id = $k['id'];
                                                    $select = ($k_id == $data['cabang_id']) ? 'selected' : '';
                                                    echo "<option value='".$k_id."' $select>".htmlspecialchars($k['nama_cabang'])."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" name="edit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-xl text-sm px-5 py-3 shadow-md hover:shadow-lg transition-all">Update Data Atlet</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php 
                        } 
                    } else {
                        // Jika tidak ada data, tampilkan pesan ini
                        echo '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 py-6">Belum ada data atlet yang terdaftar.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalTambahAtlet" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Pendaftaran Atlet Baru</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-modal-toggle="modalTambahAtlet">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="proses_atlet.php" method="POST" class="p-6 text-left">
                
                <div class="mb-5">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="nama" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-teal-600 focus:border-teal-600 block w-full p-3 shadow-sm transition-all" placeholder="Contoh: Michael Phelps" required>
                </div>
                
                <div class="mb-5">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-teal-600 focus:border-teal-600 block w-full p-3 shadow-sm transition-all" required>
                        <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                
                <div class="mb-5">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-teal-600 focus:border-teal-600 block w-full p-3 shadow-sm transition-all" placeholder="Contoh: 08123456789" required>
                </div>
                
                    <div class="mb-6">
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih Cabang Latihan</label>
                        <select name="id_kolam" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-medium rounded-xl focus:ring-teal-600 focus:border-teal-600 block w-full p-3 shadow-sm transition-all" required>
                            <option value="" disabled selected>-- Pilih Kolam Renang --</option>
                            <?php
                            $q_kolam2 = mysqli_query($koneksi, "SELECT * FROM cabang");
                            if($q_kolam2) {
                                while($k2 = mysqli_fetch_assoc($q_kolam2)) {
                                    $k_id2 = $k2['id'];
                                    echo "<option value='".$k_id2."'>".htmlspecialchars($k2['nama_cabang'])."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                
                <button type="submit" name="tambah" class="w-full text-white bg-teal-600 hover:bg-teal-700 font-bold rounded-xl text-sm px-5 py-3 shadow-md hover:shadow-lg transition-all">Simpan Data Atlet</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>