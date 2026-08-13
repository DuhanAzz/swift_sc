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
                        <span class="font-medium">Berhasil!</span> Data baru telah berhasil ditambahkan ke dalam sistem.
                      </div>';
            } else if($_GET['pesan'] == "sukses_edit"){
                echo '<div class="p-4 mb-4 text-sm text-blue-800 rounded-xl bg-blue-50 border border-blue-200" role="alert">
                        <span class="font-medium">Update Sukses!</span> Perubahan data telah berhasil disimpan.
                      </div>';
            } else if($_GET['pesan'] == "sukses_hapus"){
                echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200" role="alert">
                        <span class="font-medium">Terhapus!</span> Data telah berhasil dihapus dari sistem.
                      </div>';
            }
        }
        
        $admin_pool_id = $_SESSION['pool_id'] ?? '';
        $pelatihList = [];
        $q_pel = mysqli_query($koneksi, "SELECT id, nama FROM pelatih WHERE id_kolam = '$admin_pool_id'");
        if ($q_pel) {
            while($rp = mysqli_fetch_assoc($q_pel)) {
                $pelatihList[] = $rp;
            }
        }
        ?>
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Manajemen Atlet</h1>
                <p class="text-sm text-gray-500">Kelola data atlet Swift Swimming Club</p>
            </div>
            <button data-modal-target="modalTambahAtlet" data-modal-toggle="modalTambahAtlet" class="text-white bg-algolia-blue hover:bg-algolia-darkblue focus:ring-4 focus:ring-blue-200 font-bold rounded-xl text-sm px-6 py-3 flex items-center transition-all shadow-lg shadow-blue-500/30">
                <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/></svg>
                Tambah Atlet
            </button>
        </div>

        <?php
        $q_tot = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE cabang_id = '$admin_pool_id'");
        $count_tot = mysqli_fetch_assoc($q_tot)['total'] ?? 0;

        $q_putra = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE jenis_kelamin = 'L' AND cabang_id = '$admin_pool_id'");
        $count_putra = mysqli_fetch_assoc($q_putra)['total'] ?? 0;

        $q_putri = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE jenis_kelamin = 'P' AND cabang_id = '$admin_pool_id'");
        $count_putri = mysqli_fetch_assoc($q_putri)['total'] ?? 0;
        ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Atlet</p>
                    <h3 class="text-2xl font-black text-gray-800"><?= $count_tot ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Atlet Putra</p>
                    <h3 class="text-2xl font-black text-gray-800"><?= $count_putra ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Atlet Putri</p>
                    <h3 class="text-2xl font-black text-gray-800"><?= $count_putri ?></h3>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm" placeholder="Cari nama atlet...">
            </div>
            <div class="w-full md:w-64">
                <select id="filterKelas" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                    <option value="">Semua Kelas</option>
                    <option value="Pemula">Pemula</option>
                    <option value="Lanjutan">Lanjutan</option>
                    <option value="Prestasi">Prestasi</option>
                    <option value="Privat">Privat</option>
                </select>
            </div>
            <div class="w-full md:w-64">
                <select id="filterStatus" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="mangkir">Suspend</option>
                </select>
            </div>
        </div>

        <div class="card overflow-hidden">
            <table class="table-algolia">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Atlet</th>
                        <th class="px-6 py-4">Kelas & Pelatih</th>
                        <th class="px-6 py-4">Gender</th>
                        <th class="px-6 py-4">No. HP</th>
                        <th class="px-6 py-4">Cabang Latihan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $atletArray = [];
                    $q_atlet = mysqli_query($koneksi, "
                        SELECT m.*, c.nama_cabang as nama_kolam, p.nama as nama_pelatih,
                        (SELECT MAX(tanggal) FROM absensi WHERE member_id = m.id AND status = 'Hadir') as last_hadir,
                        (SELECT COUNT(id) FROM absensi WHERE member_id = m.id AND status_bayar = 'Unpaid' AND tanggal < DATE_SUB(CURDATE(), INTERVAL 60 DAY)) as unpaid_old_count
                        FROM member m 
                        LEFT JOIN cabang c ON m.cabang_id = c.id 
                        LEFT JOIN pelatih p ON m.pelatih_id = p.id 
                        WHERE 1=1 AND m.cabang_id = '$admin_pool_id' 
                        ORDER BY m.nama ASC
                    ");
                    if($q_atlet) {
                        while($row = mysqli_fetch_assoc($q_atlet)) {
                            $atletArray[] = $row;
                        }
                    }

                    // Cek apakah ada datanya
                    if(count($atletArray) > 0) {
                        foreach($atletArray as $data) {
                            $is_mangkir = false;
                            
                            if ($data['unpaid_old_count'] > 0) {
                                $is_mangkir = true;
                            } else {
                                if ($data['last_hadir']) {
                                    $days_since_last = (time() - strtotime($data['last_hadir'])) / (60*60*24);
                                    if ($days_since_last > 60) $is_mangkir = true;
                                } else {
                                    $days_since_gabung = (time() - strtotime($data['tanggal_gabung'])) / (60*60*24);
                                    if ($days_since_gabung > 60) $is_mangkir = true;
                                }
                            }
                            
                            $status_class = $is_mangkir ? 'mangkir' : 'aktif';
                    ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors atlet-row" data-status="<?= $status_class ?>">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $no++; ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800 atlet-nama"><?= htmlspecialchars($data['nama']); ?></td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 mb-1 atlet-kelas">
                                <?= htmlspecialchars($data['tingkatan_kelas'] ?? 'Pemula'); ?>
                            </span>
                            <?php if(!empty($data['nama_pelatih'])): ?>
                            <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                                <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <?= htmlspecialchars($data['nama_pelatih']); ?>
                            </div>
                            <?php else: ?>
                            <div class="text-[10px] text-gray-400 italic mt-1">- Tanpa Pelatih -</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['jenis_kelamin']); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($data['no_hp']); ?></td>
                        <td class="px-6 py-4 font-semibold text-algolia-blue"><?= htmlspecialchars($data['nama_kolam']); ?></td>
                        <td class="px-6 py-4">
                            <?php if($is_mangkir): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-red-100 text-red-800">
                                    Suspend
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <?php if($is_mangkir): ?>
                                <a href="proses_atlet.php?action=reaktivasi&id=<?= $data['id']; ?>" onclick="return confirm('Atlet ini telah tidak aktif atau menunggak > 2 bulan. Re-aktivasi akan mengaktifkan kembali statusnya dan mewajibkan pembayaran ulang Biaya Pendaftaran + Iuran Bulanan!');" class="p-2 bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white rounded-lg transition-colors shadow-sm" title="Re-Aktivasi Atlet">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </a>
                                <?php endif; ?>
                                <button data-modal-target="modalEdit_<?= $data['id']; ?>" data-modal-toggle="modalEdit_<?= $data['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-lg transition-colors shadow-sm" title="Edit Atlet">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <a href="hapus_atlet.php?id=<?= $data['id']; ?>" onclick="return confirm('Yakin ingin menghapus?');" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-colors shadow-sm flex items-center justify-center" title="Hapus Atlet">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>


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

<?php
if(count($atletArray) > 0) {
    foreach($atletArray as $data) {
?>
<div id="modalEdit_<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl mx-auto max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl shadow-slate-900/20 border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Edit Data Atlet</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-modal-toggle="modalEdit_<?= $data['id']; ?>">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="proses_atlet.php" method="POST" class="p-6">
                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                            <option value="L" <?= ($data['jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= ($data['jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" value="<?= htmlspecialchars($data['no_hp']); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="<?= $data['tanggal_lahir'] ?? ''; ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Cabang Latihan</label>
                        <select name="id_kolam" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
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
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tingkatan Kelas</label>
                        <select name="tingkatan_kelas" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                            <option value="Pemula" <?= (($data['tingkatan_kelas'] ?? '') == 'Pemula') ? 'selected' : '' ?>>Pemula</option>
                            <option value="Lanjutan" <?= (($data['tingkatan_kelas'] ?? '') == 'Lanjutan') ? 'selected' : '' ?>>Lanjutan</option>
                            <option value="Prestasi" <?= (($data['tingkatan_kelas'] ?? '') == 'Prestasi') ? 'selected' : '' ?>>Prestasi</option>
                            <option value="Privat" <?= (($data['tingkatan_kelas'] ?? '') == 'Privat') ? 'selected' : '' ?>>Privat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Asal Sekolah</label>
                        <input type="text" name="sekolah" value="<?= htmlspecialchars($data['sekolah'] ?? ''); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" placeholder="Contoh: SMA Negeri 1">
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tugaskan ke Pelatih</label>
                        <select name="pelatih_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                            <option value="">-- Tanpa Pelatih --</option>
                            <?php foreach($pelatihList as $pel): ?>
                                <option value="<?= $pel['id'] ?>" <?= (($data['pelatih_id'] ?? '') == $pel['id']) ? 'selected' : '' ?>><?= htmlspecialchars($pel['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Biaya Pendaftaran</label>
                        <input type="number" name="biaya_pendaftaran" value="<?= htmlspecialchars($data['biaya_pendaftaran'] ?? 100000); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Iuran Bulanan</label>
                        <input type="number" name="biaya_bulanan" value="<?= htmlspecialchars($data['biaya_bulanan'] ?? 350000); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                    </div>
                </div>
                
                <div class="flex justify-end border-t border-gray-100 pt-5 mt-2">
                    <button type="submit" name="edit" class="text-white bg-teal-600 hover:bg-teal-700 font-bold rounded-xl text-sm px-8 py-3.5 shadow-md hover:shadow-lg transition-all focus:ring-4 focus:ring-teal-200">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
    }
}
?>

<div id="modalTambahAtlet" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl mx-auto max-h-full">
        <div class="relative bg-white rounded-2xl shadow-2xl shadow-slate-900/20 border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Pendaftaran Atlet Baru</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-modal-toggle="modalTambahAtlet">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="proses_atlet.php" method="POST" class="p-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="nama" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" placeholder="Contoh: Michael Phelps" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" placeholder="Contoh: 08123456789" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Cabang Latihan</label>
                        <select name="id_kolam" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" required>
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
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tingkatan Kelas</label>
                        <select name="tingkatan_kelas" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                            <option value="Pemula" selected>Pemula</option>
                            <option value="Lanjutan">Lanjutan</option>
                            <option value="Prestasi">Prestasi</option>
                            <option value="Privat">Privat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Asal Sekolah</label>
                        <input type="text" name="sekolah" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all" placeholder="Contoh: SMA Negeri 1">
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tugaskan ke Pelatih</label>
                        <select name="pelatih_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                            <option value="">-- Tanpa Pelatih --</option>
                            <?php foreach($pelatihList as $pel): ?>
                                <option value="<?= $pel['id'] ?>"><?= htmlspecialchars($pel['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Biaya Pendaftaran</label>
                        <input type="number" name="biaya_pendaftaran" value="100000" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Iuran Bulanan</label>
                        <input type="number" name="biaya_bulanan" value="350000" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm font-semibold rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-4 shadow-sm transition-all">
                    </div>
                </div>
                
                <div class="flex justify-end border-t border-gray-100 pt-5 mt-2">
                    <button type="submit" name="tambah" class="text-white bg-teal-600 hover:bg-teal-700 font-bold rounded-xl text-sm px-8 py-3.5 shadow-md hover:shadow-lg transition-all focus:ring-4 focus:ring-teal-200">Simpan Data Atlet</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterKelas = document.getElementById('filterKelas');
    const filterStatus = document.getElementById('filterStatus');
    const tableRows = document.querySelectorAll('tbody tr.atlet-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const kelasValue = filterKelas.value.toLowerCase();
        const statusValue = filterStatus ? filterStatus.value.toLowerCase() : '';

        tableRows.forEach(row => {
            const nama = row.querySelector('.atlet-nama').textContent.toLowerCase();
            const kelas = row.querySelector('.atlet-kelas').textContent.toLowerCase();
            const rowStatus = row.getAttribute('data-status');
            
            const matchSearch = nama.includes(searchTerm);
            const matchKelas = kelasValue === '' || kelas.includes(kelasValue);
            const matchStatus = statusValue === '' || rowStatus === statusValue;

            if (matchSearch && matchKelas && matchStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (filterKelas) filterKelas.addEventListener('change', filterTable);
    if (filterStatus) filterStatus.addEventListener('change', filterTable);
});
</script>

<?php include '../includes/footer.php'; ?>