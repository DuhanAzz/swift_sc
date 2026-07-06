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

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
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
                <h1 class="text-xl font-bold text-algolia-navy">Manajemen Admin & Pengguna</h1>
                <p class="text-sm text-gray-500">Kelola hak akses pengguna sistem Swift Swimming Club</p>
            </div>
            <button data-modal-target="modalTambahAdmin" data-modal-toggle="modalTambahAdmin" class="text-white bg-slate-800 hover:bg-slate-900 font-medium rounded-lg text-sm px-5 py-2.5 transition-all shadow-md">
                + Tambah Pengguna Baru
            </button>
        </div>

        <div class="card overflow-hidden">
            <table class="table-algolia">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
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
                    $q_users = mysqli_query($koneksi, "SELECT u.*, c.nama_cabang as nama_kolam FROM users u LEFT JOIN cabang c ON u.cabang_id = c.id WHERE u.role IN ('CEO', 'Admin') ORDER BY u.role, u.username ASC");
                    if($q_users) {
                        while($row = mysqli_fetch_assoc($q_users)) {
                            $usersArray[] = $row;
                        }
                    }

                    if(count($usersArray) > 0) {
                        foreach($usersArray as $data) {
                            $role = $data['role'];
                            $nama_role = ($role == 'CEO') ? 'Super Admin' : 'Manajer Kolam';
                            $role_color = ($role == 'CEO') ? 'bg-purple-100 text-purple-800 border-purple-400' : 'bg-green-100 text-green-800 border-green-400';
                            
                            $akses_kolam = empty($data['cabang_id']) ? '<span class="text-gray-400 italic">Semua Cabang</span>' : htmlspecialchars($data['nama_kolam'] ?? '');
                    ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $no++; ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['username']); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['email']); ?></td>
                        <td class="px-6 py-4"><span class="text-xs font-medium px-2.5 py-0.5 rounded border <?= $role_color; ?>"><?= $nama_role; ?></span></td>
                        <td class="px-6 py-4 font-medium"><?= $akses_kolam; ?></td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button data-modal-target="modalEdit_<?= $data['id']; ?>" data-modal-toggle="modalEdit_<?= $data['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-lg transition-colors shadow-sm" title="Edit Admin">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <a href="hapus_admin.php?id=<?= $data['id']; ?>" onclick="return confirm('Yakin ingin menghapus?');" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-colors shadow-sm flex items-center justify-center" title="Hapus Admin">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>


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

<?php
if(count($usersArray) > 0) {
    foreach($usersArray as $data) {
        $role = $data['role'];
?>
<div id="modalEdit_<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="text-lg font-bold text-gray-800">Edit Pengguna</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto" data-modal-toggle="modalEdit_<?= $data['id']; ?>">✖</button>
            </div>
            <form action="proses_admin.php" method="POST" class="p-6 text-left">
                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($data['username']); ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Role</label>
                        <select name="role" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                            <option value="CEO" <?= ($role == 'CEO') ? 'selected' : '' ?>>Super Admin (CEO)</option>
                            <option value="Admin" <?= ($role == 'Admin') ? 'selected' : '' ?>>Manajer Kolam (Admin)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Akses Kolam</label>
                        <select name="cabang_id" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3">
                            <option value="">-- Semua Cabang --</option>
                            <?php
                            $q_kolam = mysqli_query($koneksi, "SELECT * FROM cabang");
                            if($q_kolam) {
                                while($k = mysqli_fetch_assoc($q_kolam)) {
                                    $k_id = $k['id'];
                                    $select = ($k_id == ($data['cabang_id'] ?? '')) ? 'selected' : '';
                                    echo "<option value='".$k_id."' $select>".htmlspecialchars($k['nama_cabang'])."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <p class="text-xs text-red-500 mb-4">*Kosongkan password jika tidak ingin diubah.</p>
                <div class="mb-6">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Password Baru</label>
                    <input type="password" name="password" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" placeholder="Masukkan password baru">
                </div>
                <button type="submit" name="edit" class="w-full text-white bg-slate-800 hover:bg-slate-900 font-bold rounded-xl text-sm px-5 py-3">Update Pengguna</button>
            </form>
        </div>
    </div>
</div>
<?php 
    }
}
?>

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
                    <input type="text" name="name" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Email</label>
                    <input type="email" name="email" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Password</label>
                    <input type="password" name="password" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Role</label>
                        <select name="role" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                            <option value="CEO">Super Admin (CEO)</option>
                            <option value="Admin">Manajer Kolam (Admin)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Penempatan Kolam</label>
                        <select name="cabang_id" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3">
                            <option value="">-- Kosongkan Jika Admin --</option>
                            <?php
                            $q_kolam2 = mysqli_query($koneksi, "SELECT * FROM cabang");
                            if($q_kolam2) {
                                while($k2 = mysqli_fetch_assoc($q_kolam2)) {
                                    echo "<option value='".$k2['id']."'>".htmlspecialchars($k2['nama_cabang'])."</option>";
                                }
                            }
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