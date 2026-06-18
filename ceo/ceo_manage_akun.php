<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            $pesanMap = [
                'sukses_admin' => 'Data Admin berhasil disimpan!',
                'hapus_admin' => 'Data Admin berhasil dihapus!',
                'sukses_pelatih' => 'Data Pelatih berhasil disimpan!',
                'hapus_pelatih' => 'Data Pelatih berhasil dihapus!',
                'gagal' => 'Terjadi kesalahan saat memproses data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-4 mb-4 text-sm text-{$color}-800 rounded-lg bg-{$color}-50 border border-{$color}-200'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="mb-6">
            <h1 class="text-xl font-bold text-algolia-navy">Manajemen Akun Global</h1>
            <p class="text-sm text-gray-500">Pusat kendali untuk menambah, mengedit, dan menghapus Admin/Manajer serta Pelatih.</p>
        </div>

        <div class="mb-8 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="admin-tab" data-tabs-target="#admin" type="button" role="tab" aria-controls="admin" aria-selected="false">Admin & Manajer Kolam</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="pelatih-tab" data-tabs-target="#pelatih" type="button" role="tab" aria-controls="pelatih" aria-selected="false">Data Pelatih</button>
                </li>
            </ul>
        </div>

        <div id="myTabContent">
            <!-- TAB ADMIN -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">Daftar Admin</h2>
                    <button data-modal-target="modalTambahAdmin" data-modal-toggle="modalTambahAdmin" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2 px-4 rounded-lg text-sm transition-all">+ Tambah Admin</button>
                </div>
                <div class="card overflow-hidden">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4">Nama</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Cabang</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $usersArray = [];
                            try {
                                $q_admin = mysqli_query($koneksi, "SELECT users.*, cabang.nama_cabang as nama_kolam FROM users LEFT JOIN cabang ON users.cabang_id = cabang.id WHERE role IN ('CEO', 'Admin')");
                                if($q_admin) {
                                    while($row = mysqli_fetch_assoc($q_admin)) {
                                        $usersArray[] = $row;
                                    }
                                }
                            } catch (\Exception $e) {}

                            if(count($usersArray) > 0) {
                                foreach($usersArray as $data) {
                                    $role_id = ($data['role'] == 'CEO') ? 1 : 2;
                                    $nama_role = ($role_id == 1) ? 'Super Admin' : 'Manajer Kolam';
                                    $akses_kolam = empty($data['cabang_id']) ? 'Semua Cabang' : htmlspecialchars($data['nama_kolam']);
                            ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['username']); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($data['email']); ?></td>
                                <td class="px-6 py-4 font-semibold text-indigo-600"><?= $nama_role; ?></td>
                                <td class="px-6 py-4"><?= $akses_kolam; ?></td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button data-modal-target="modalEditAdmin<?= $data['id']; ?>" data-modal-toggle="modalEditAdmin<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                                    <a href="ceo_proses_akun.php?hapus_admin=<?= $data['id']; ?>" onclick="return confirm('Hapus admin ini?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                                </td>
                            </tr>

                            <!-- Modal Edit Admin -->
                            <div id="modalEditAdmin<?= $data['id']; ?>" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                                        <h3 class="text-lg font-bold">Edit Admin</h3>
                                        <button data-modal-toggle="modalEditAdmin<?= $data['id']; ?>" class="text-gray-400 hover:text-gray-900">✖</button>
                                    </div>
                                    <form action="ceo_proses_akun.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                        <input type="text" name="name" value="<?= htmlspecialchars($data['username']); ?>" class="w-full mb-3 p-2 border rounded" required>
                                        <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" class="w-full mb-3 p-2 border rounded" required>
                                        <select name="role_id" class="w-full mb-3 p-2 border rounded">
                                            <option value="1" <?= $role_id==1 ? 'selected' : '' ?>>Super Admin</option>
                                            <option value="2" <?= $role_id==2 ? 'selected' : '' ?>>Manajer Kolam</option>
                                        </select>
                                        <select name="pool_id" class="w-full mb-3 p-2 border rounded">
                                            <option value="">-- Semua Cabang --</option>
                                            <?php
                                            try {
                                                $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                                                while($k = mysqli_fetch_assoc($q_kolam)) {
                                                    $sel = ($k['id'] == ($data['cabang_id'] ?? '')) ? 'selected' : '';
                                                    echo "<option value='{$k['id']}' $sel>{$k['nama_cabang']}</option>";
                                                }
                                            } catch (\Exception $e) {}
                                            ?>
                                        </select>
                                        <input type="password" name="password" placeholder="Password Baru (Kosongkan jika tidak ubah)" class="w-full mb-4 p-2 border rounded">
                                        <button type="submit" name="edit_admin" class="w-full bg-indigo-600 text-white font-bold py-2 rounded">Simpan</button>
                                    </form>
                                </div>
                            </div>
                            <?php } } else { echo "<tr><td colspan='5' class='px-6 py-4 text-center'>Tidak ada data.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB PELATIH -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="pelatih" role="tabpanel" aria-labelledby="pelatih-tab">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">Daftar Pelatih</h2>
                    <button data-modal-target="modalTambahPelatih" data-modal-toggle="modalTambahPelatih" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2 px-4 rounded-lg text-sm transition-all">+ Tambah Pelatih</button>
                </div>
                <div class="card overflow-hidden">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4">Nama Pelatih</th>
                                <th class="px-6 py-4">Lisensi</th>
                                <th class="px-6 py-4">No. HP</th>
                                <th class="px-6 py-4">Lokasi</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $coachesArray = [];
                            try {
                                $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih");
                                if($q_pelatih) {
                                    while($row = mysqli_fetch_assoc($q_pelatih)) {
                                        $coachesArray[] = $row;
                                    }
                                }
                            } catch (\Exception $e) {}

                            if(count($coachesArray) > 0) {
                                foreach($coachesArray as $data) {
                            ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['nama']); ?></td>
                                <td class="px-6 py-4"><span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded border"><?= htmlspecialchars($data['sertifikasi'] ?? '-'); ?></span></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($data['jabatan']); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($data['cabang']); ?></td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button data-modal-target="modalEditPelatih<?= $data['id']; ?>" data-modal-toggle="modalEditPelatih<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                                    <a href="ceo_proses_akun.php?hapus_pelatih=<?= $data['id']; ?>" onclick="return confirm('Hapus pelatih ini?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                                </td>
                            </tr>
                            
                            <!-- Modal Edit Pelatih -->
                            <div id="modalEditPelatih<?= $data['id']; ?>" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                                        <h3 class="text-lg font-bold">Edit Pelatih</h3>
                                        <button data-modal-toggle="modalEditPelatih<?= $data['id']; ?>" class="text-gray-400 hover:text-gray-900">✖</button>
                                    </div>
                                    <form action="ceo_proses_akun.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                        <input type="text" name="nama_pelatih" value="<?= htmlspecialchars($data['nama']); ?>" class="w-full mb-3 p-2 border rounded" required>
                                        <input type="text" name="lisensi" value="<?= htmlspecialchars($data['sertifikasi'] ?? ''); ?>" placeholder="Sertifikasi / Lisensi" class="w-full mb-3 p-2 border rounded" required>
                                        <input type="text" name="no_hp" value="<?= htmlspecialchars($data['jabatan'] ?? ''); ?>" placeholder="Jabatan" class="w-full mb-3 p-2 border rounded" required>
                                        <select name="id_kolam" class="w-full mb-4 p-2 border rounded">
                                            <?php
                                            try {
                                                $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                                                while($k = mysqli_fetch_assoc($q_kolam)) {
                                                    $sel = ($k['nama_cabang'] == $data['cabang']) ? 'selected' : '';
                                                    echo "<option value='{$k['nama_cabang']}' $sel>{$k['nama_cabang']}</option>";
                                                }
                                            } catch (\Exception $e) {}
                                            ?>
                                        </select>
                                        <button type="submit" name="edit_pelatih" class="w-full bg-indigo-600 text-white font-bold py-2 rounded">Simpan</button>
                                    </form>
                                </div>
                            </div>
                            <?php } } else { echo "<tr><td colspan='5' class='px-6 py-4 text-center'>Tidak ada data.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Tambah Admin -->
<div id="modalTambahAdmin" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-lg font-bold">Tambah Admin</h3>
            <button data-modal-toggle="modalTambahAdmin" class="text-gray-400 hover:text-gray-900">✖</button>
        </div>
        <form action="ceo_proses_akun.php" method="POST">
            <input type="text" name="name" placeholder="Nama Lengkap" class="w-full mb-3 p-2 border rounded" required>
            <input type="email" name="email" placeholder="Email" class="w-full mb-3 p-2 border rounded" required>
            <input type="password" name="password" placeholder="Password" class="w-full mb-3 p-2 border rounded" required>
            <select name="role_id" class="w-full mb-3 p-2 border rounded" required>
                <option value="1">Super Admin</option>
                <option value="2">Manajer Kolam</option>
            </select>
            <select name="pool_id" class="w-full mb-4 p-2 border rounded">
                <option value="">-- Semua Cabang --</option>
                <?php
                try {
                    $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                    while($k = mysqli_fetch_assoc($q_kolam)) {
                        echo "<option value='{$k['id']}'>{$k['nama_cabang']}</option>";
                    }
                } catch (\Exception $e) {}
                ?>
            </select>
            <button type="submit" name="tambah_admin" class="w-full bg-indigo-600 text-white font-bold py-2 rounded">Simpan</button>
        </form>
    </div>
</div>

<!-- Modal Tambah Pelatih -->
<div id="modalTambahPelatih" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-lg font-bold">Tambah Pelatih</h3>
            <button data-modal-toggle="modalTambahPelatih" class="text-gray-400 hover:text-gray-900">✖</button>
        </div>
        <form action="ceo_proses_akun.php" method="POST">
            <input type="text" name="nama_pelatih" placeholder="Nama Lengkap Pelatih" class="w-full mb-3 p-2 border rounded" required>
            <input type="email" name="email" placeholder="Email untuk Login" class="w-full mb-3 p-2 border rounded" required>
            <input type="password" name="password" placeholder="Password Login" class="w-full mb-3 p-2 border rounded" required>
            <input type="text" name="lisensi" placeholder="Sertifikasi / Lisensi" class="w-full mb-3 p-2 border rounded" required>
            <input type="text" name="no_hp" placeholder="Jabatan" class="w-full mb-3 p-2 border rounded" required>
            <select name="id_kolam" class="w-full mb-4 p-2 border rounded" required>
                <option value="">-- Pilih Lokasi --</option>
                <?php
                try {
                    $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                    while($k = mysqli_fetch_assoc($q_kolam)) {
                        echo "<option value='{$k['id']}'>{$k['nama_cabang']}</option>";
                    }
                } catch (\Exception $e) {}
                ?>
            </select>
            <button type="submit" name="tambah_pelatih" class="w-full bg-indigo-600 text-white font-bold py-2 rounded">Simpan</button>
        </form>
    </div>
</div>

<!-- Pastikan flowbite terload untuk tabs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
