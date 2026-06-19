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

        <div class="mb-8 border-b border-[#E8E8EF]">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="admin-tab" data-tabs-target="#admin" type="button" role="tab" aria-controls="admin" aria-selected="false">Admin & Manajer Kolam</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-[#E8E8EF]" id="pelatih-tab" data-tabs-target="#pelatih" type="button" role="tab" aria-controls="pelatih" aria-selected="false">Data Pelatih</button>
                </li>
            </ul>
        </div>

        <div id="myTabContent">
            <!-- TAB ADMIN -->
            <div class="hidden p-4 rounded-lg bg-gray-50" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">Daftar Admin</h2>
                    <button data-modal-target="modalTambahAkun" data-modal-toggle="modalTambahAkun" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2 px-4 rounded-lg text-sm transition-all">+ Tambah Akun Baru</button>
                </div>
                <div class="card overflow-hidden">
                    <table class="table-algolia">
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
                                        <select name="cabang_id" class="w-full mb-3 p-2 border rounded">
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
                    <button data-modal-target="modalTambahAkun" data-modal-toggle="modalTambahAkun" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2 px-4 rounded-lg text-sm transition-all">+ Tambah Akun Baru</button>
                </div>
                <div class="card overflow-hidden">
                    <table class="table-algolia">
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
                                $q_pelatih = mysqli_query($koneksi, "SELECT pelatih.*, users.email, users.username, cabang.nama_cabang as nama_kolam FROM pelatih LEFT JOIN users ON pelatih.user_id = users.id LEFT JOIN cabang ON pelatih.id_kolam = cabang.id");
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
                                <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['nama_pelatih'] ?? $data['nama'] ?? '-'); ?></td>
                                <td class="px-6 py-4"><span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded border"><?= htmlspecialchars($data['lisensi'] ?? $data['sertifikasi'] ?? '-'); ?></span></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($data['no_hp'] ?? $data['jabatan'] ?? '-'); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($data['nama_kolam'] ?? $data['cabang'] ?? '-'); ?></td>
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
                                        <input type="hidden" name="user_id" value="<?= $data['user_id']; ?>">
                                        
                                        <label class="block text-xs font-bold text-gray-500 mb-1 mt-2">Nama & Akun Login</label>
                                        <input type="text" name="nama_pelatih" value="<?= htmlspecialchars($data['nama_pelatih'] ?? $data['nama'] ?? ''); ?>" class="w-full mb-2 p-2 border rounded text-sm" required>
                                        <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? ''); ?>" class="w-full mb-2 p-2 border rounded text-sm" placeholder="Email Login" required>
                                        <input type="password" name="password_baru" placeholder="Password Baru (Kosongkan jika tidak ubah)" class="w-full mb-4 p-2 border rounded text-sm bg-yellow-50">
                                        
                                        <label class="block text-xs font-bold text-gray-500 mb-1">Profil Pelatih</label>
                                        <input type="text" name="lisensi" value="<?= htmlspecialchars($data['lisensi'] ?? $data['sertifikasi'] ?? ''); ?>" placeholder="Sertifikasi / Lisensi" class="w-full mb-2 p-2 border rounded text-sm" required>
                                        <input type="text" name="no_hp" value="<?= htmlspecialchars($data['no_hp'] ?? $data['jabatan'] ?? ''); ?>" placeholder="Jabatan / No HP" class="w-full mb-3 p-2 border rounded text-sm" required>
                                        <select name="id_kolam" class="w-full mb-4 p-2 border rounded text-sm">
                                            <?php
                                            try {
                                                $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                                                while($k = mysqli_fetch_assoc($q_kolam)) {
                                                    $sel = ($k['id'] == $data['id_kolam']) ? 'selected' : '';
                                                    echo "<option value='{$k['id']}' $sel>{$k['nama_cabang']}</option>";
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

<!-- Modal Tambah Akun Baru -->
<div id="modalTambahAkun" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-lg font-bold">Tambah Akun Baru</h3>
            <button data-modal-toggle="modalTambahAkun" class="text-gray-400 hover:text-gray-900">✖</button>
        </div>
        <form action="ceo_proses_akun.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nama Lengkap" class="w-full mb-3 p-2 border rounded" required>
            <input type="email" name="email" placeholder="Email" class="w-full mb-3 p-2 border rounded" required>
            <input type="password" name="password" placeholder="Password" class="w-full mb-3 p-2 border rounded" required>
            
            <select name="role" class="w-full mb-3 p-2 border rounded" required id="roleSelect" onchange="togglePelatihFields()">
                <option value="">-- Pilih Role --</option>
                <option value="Admin">Admin</option>
                <option value="Pelatih">Pelatih</option>
            </select>
            
            <select name="cabang_id" class="w-full mb-3 p-2 border rounded" required>
                <option value="">-- Wajib Pilih Cabang --</option>
                <?php
                try {
                    $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                    while($k = mysqli_fetch_assoc($q_kolam)) {
                        echo "<option value='{$k['id']}'>{$k['nama_cabang']}</option>";
                    }
                } catch (\Exception $e) {}
                ?>
            </select>

            <div id="pelatihFields" class="hidden">
                <input type="text" name="lisensi" placeholder="Sertifikasi / Lisensi (Hanya Pelatih)" class="w-full mb-3 p-2 border rounded">
                <input type="text" name="no_hp" placeholder="Jabatan/No HP (Hanya Pelatih)" class="w-full mb-3 p-2 border rounded">
                <label class="block text-xs font-bold text-gray-500 mb-1">Foto Pelatih:</label>
                <input type="file" name="foto_pelatih" accept="image/*" class="w-full mb-4 p-2 border rounded text-sm">
            </div>

            <button type="submit" name="tambah_akun_baru" class="w-full bg-indigo-600 text-white font-bold py-2 rounded">Simpan</button>
        </form>
    </div>
</div>

<script>
function togglePelatihFields() {
    const role = document.getElementById('roleSelect').value;
    const fields = document.getElementById('pelatihFields');
    if(role === 'Pelatih') {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}
</script>

<!-- Pastikan flowbite terload untuk tabs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<?php include '../includes/footer.php'; ?>
