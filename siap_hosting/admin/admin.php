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
                echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">Data admin berhasil ditambahkan!</div>';
            } else if($_GET['pesan'] == "sukses_edit"){
                echo '<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200">Data admin berhasil diupdate!</div>';
            } else if($_GET['pesan'] == "sukses_hapus"){
                echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">Data admin berhasil dihapus!</div>';
            }
        }
        ?>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Admin & Pengguna</h1>
                <p class="text-sm text-gray-500">Kelola hak akses pengguna sistem Swift Swimming Club</p>
            </div>
            <button data-modal-target="modalTambahAdmin" data-modal-toggle="modalTambahAdmin" class="text-white bg-slate-800 hover:bg-slate-900 font-medium rounded-lg text-sm px-5 py-2.5 transition-all shadow-md">
                + Tambah Pengguna Baru
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-xl bg-white border border-gray-100">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-white uppercase bg-slate-800">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Akses Kolam</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $usersArray = [];
                    try {
                        $documents = $database->getDocuments('users');
                        foreach ($documents as $data) {
                            // Hanya tampilkan yang role_id 1 atau 2 (Admin) atau role ceo/admin
                            if ((isset($data['role_id']) && in_array($data['role_id'], [1, 2])) || (isset($data['role']) && in_array($data['role'], ['ceo', 'admin']))) {
                                $data['nama_kolam'] = '-';
                                if (!empty($data['pool_id'])) {
                                    try {
                                        $poolDoc = $database->getDocument('pools', $data['pool_id']);
                                        if ($poolDoc) {
                                            $data['nama_kolam'] = $poolDoc['name'];
                                        }
                                    } catch (\Exception $e) {}
                                }
                                $usersArray[] = $data;
                            }
                        }
                    } catch (\Exception $e) {}

                    if(count($usersArray) > 0) {
                        foreach($usersArray as $data) {
                            $role_id = $data['role_id'] ?? ($data['role'] == 'ceo' ? 1 : 2);
                            $nama_role = ($role_id == 1) ? 'Super Admin' : 'Manajer Kolam';
                            $role_color = ($role_id == 1) ? 'bg-purple-100 text-purple-800 border-purple-400' : 'bg-green-100 text-green-800 border-green-400';
                            
                            $akses_kolam = empty($data['pool_id']) ? '<span class="text-gray-400 italic">Semua Cabang</span>' : htmlspecialchars($data['nama_kolam']);
                    ?>
                    <tr class="bg-white border-b hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $no++; ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['name']); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['email']); ?></td>
                        <td class="px-6 py-4"><span class="text-xs font-medium px-2.5 py-0.5 rounded border <?= $role_color; ?>"><?= $nama_role; ?></span></td>
                        <td class="px-6 py-4 font-medium"><?= $akses_kolam; ?></td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <button data-modal-target="modalEditAdmin<?= $data['id']; ?>" data-modal-toggle="modalEditAdmin<?= $data['id']; ?>" class="font-medium text-blue-600 hover:underline">Edit</button>
                            <a href="hapus_admin.php?id=<?= $data['id']; ?>" onclick="return confirm('Yakin hapus pengguna ini?')" class="font-medium text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>

                    <div id="modalEditAdmin<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100">
                                <div class="flex items-center justify-between p-5 border-b">
                                    <h3 class="text-lg font-bold text-gray-800">Edit Pengguna</h3>
                                    <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto" data-modal-toggle="modalEditAdmin<?= $data['id']; ?>">✖</button>
                                </div>
                                <form action="proses_admin.php" method="POST" class="p-6 text-left">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <div class="mb-4">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                                        <input type="text" name="name" value="<?= $data['name']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Email</label>
                                        <input type="email" name="email" value="<?= $data['email']; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Role</label>
                                            <select name="role_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                                                <option value="1" <?= ($role_id == 1) ? 'selected' : '' ?>>Super Admin</option>
                                                <option value="2" <?= ($role_id == 2) ? 'selected' : '' ?>>Manajer Kolam</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Akses Kolam</label>
                                            <select name="pool_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3">
                                                <option value="">-- Semua Cabang --</option>
                                                <?php
                                                try {
                                                    $q_kolam = $database->getDocuments('pools');
                                                    foreach($q_kolam as $k) {
                                                        $k_id = $k['id'];
                                                        $select = ($k_id == ($data['pool_id'] ?? '')) ? 'selected' : '';
                                                        echo "<option value='".$k_id."' $select>".htmlspecialchars($k['name'])."</option>";
                                                    }
                                                } catch (\Exception $e) {}
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <p class="text-xs text-red-500 mb-4">*Kosongkan password jika tidak ingin diubah.</p>
                                    <div class="mb-6">
                                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Password Baru</label>
                                        <input type="password" name="password" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" placeholder="Masukkan password baru">
                                    </div>
                                    <button type="submit" name="edit" class="w-full text-white bg-slate-800 hover:bg-slate-900 font-bold rounded-xl text-sm px-5 py-3">Update Pengguna</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php 
                        } 
                    } else {
                        echo '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 py-6">Belum ada data admin.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalTambahAdmin" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="text-lg font-bold text-gray-800">Tambah Pengguna Baru</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto" data-modal-toggle="modalTambahAdmin">✖</button>
            </div>
            <form action="proses_admin.php" method="POST" class="p-6 text-left">
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                    <input type="text" name="name" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Email</label>
                    <input type="email" name="email" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Password</label>
                    <input type="password" name="password" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Role</label>
                        <select name="role_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3" required>
                            <option value="1">Super Admin</option>
                            <option value="2">Manajer Kolam</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Penempatan Kolam</label>
                        <select name="pool_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-3">
                            <option value="">-- Kosongkan Jika Admin --</option>
                            <?php
                            try {
                                $q_kolam2 = $database->getDocuments('pools');
                                foreach($q_kolam2 as $k2) {
                                    $k_id2 = $k2['id'];
                                    echo "<option value='".$k_id2."'>".htmlspecialchars($k2['name'])."</option>";
                                }
                            } catch (\Exception $e) {}
                            ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="tambah" class="w-full text-white bg-slate-800 hover:bg-slate-900 font-bold rounded-xl text-sm px-5 py-3">Simpan Pengguna</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>