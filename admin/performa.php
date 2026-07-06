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
                echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">Rekor waktu berhasil dicatat!</div>';
            } else if($_GET['pesan'] == "sukses_edit"){
                echo '<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200">Rekor waktu berhasil diupdate!</div>';
            } else if($_GET['pesan'] == "sukses_hapus"){
                echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">Rekor waktu berhasil dihapus!</div>';
            }
        }
        ?>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Performa & Rekor Atlet</h1>
                <p class="text-sm text-gray-500">Pencatatan rekor waktu renang untuk monitoring perkembangan</p>
            </div>
            <button data-modal-target="modalTambahPerforma" data-modal-toggle="modalTambahPerforma" class="text-white bg-algolia-blue hover:bg-algolia-darkblue font-medium rounded-lg text-sm px-5 py-2.5 transition-all shadow-md">
                + Catat Waktu Baru
            </button>
        </div>

        <div class="card overflow-hidden">
            <table class="table-algolia">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Nama Atlet</th>
                        <th class="px-6 py-4">Gaya & Jarak</th>
                        <th class="px-6 py-4 text-center">Tipe Kolam</th>
                        <th class="px-6 py-4 text-center">Waktu Tempuh</th>
                        <th class="px-6 py-4">Catatan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $performaArray = [];
                    $q_perf = mysqli_query($koneksi, "SELECT p.*, m.nama as nama_atlet FROM performa p LEFT JOIN member m ON p.member_id = m.id ORDER BY p.tanggal_rekor DESC, p.id DESC");
                    if($q_perf) {
                        while($row = mysqli_fetch_assoc($q_perf)) {
                            $performaArray[] = $row;
                        }
                    }

                    if(count($performaArray) > 0) {
                        foreach($performaArray as $data) {
                    ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= date('d M Y', strtotime($data['tanggal_rekor'])); ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($data['nama_atlet']); ?></td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-blue-600"><?= htmlspecialchars($data['gaya_renang']); ?></span>
                            <span class="text-gray-500 ml-1"><?= htmlspecialchars($data['jarak']); ?>m</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-medium px-2.5 py-0.5 rounded border bg-gray-100 text-gray-800 border-[#E8E8EF]"><?= htmlspecialchars($data['tipe_kolam']); ?></span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-lg text-slate-800 tracking-wider">
                            <?= htmlspecialchars($data['waktu_formatted']); ?>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500 truncate max-w-xs"><?= htmlspecialchars($data['catatan']); ?></td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button data-modal-target="modalEdit_<?= $data['id']; ?>" data-modal-toggle="modalEdit_<?= $data['id']; ?>" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-lg transition-colors shadow-sm" title="Edit Rekor">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <a href="hapus_performa.php?id=<?= $data['id']; ?>" onclick="return confirm('Yakin ingin menghapus?');" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-colors shadow-sm flex items-center justify-center" title="Hapus Rekor">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>


                    <?php 
                        } 
                    } else {
                        echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 py-6">Belum ada data rekor yang dicatat.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

<?php
if(count($performaArray) > 0) {
    foreach($performaArray as $data) {
?>
<div id="modalEdit_<?= $data['id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="text-lg font-bold text-gray-800">Edit Waktu Performa</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto" data-modal-toggle="modalEdit_<?= $data['id']; ?>">✖</button>
            </div>
            
            <?php
            $waktu_pecah = explode(':', $data['waktu_formatted']);
            $menit_edit = (int)$waktu_pecah[0];
            
            $detik_ms_pecah = explode('.', $waktu_pecah[1]);
            $detik_edit = (int)$detik_ms_pecah[0];
            $ms_edit = (int)$detik_ms_pecah[1];
            ?>

            <form action="proses_performa.php" method="POST" class="p-6 text-left">
                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Atlet</label>
                    <select name="member_id" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                        <?php
                        $q_atlet_edit = mysqli_query($koneksi, "SELECT * FROM member WHERE 1=1");
                        if($q_atlet_edit) {
                            while($a_data = mysqli_fetch_assoc($q_atlet_edit)) {
                                $a_id = $a_data['id'];
                                $selected = ($a_id == $data['member_id']) ? 'selected' : '';
                                echo "<option value='".$a_id."' $selected>".htmlspecialchars($a_data['nama'])."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tanggal Tes</label>
                        <input type="date" name="tanggal_rekor" value="<?= $data['tanggal_rekor']; ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tipe Kolam</label>
                        <select name="tipe_kolam" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                            <option value="25m" <?= ($data['tipe_kolam'] == '25m') ? 'selected' : ''; ?>>Short Course (25m)</option>
                            <option value="50m" <?= ($data['tipe_kolam'] == '50m') ? 'selected' : ''; ?>>Long Course (50m)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Gaya Renang</label>
                        <select name="gaya_renang" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                            <option value="Bebas" <?= ($data['gaya_renang'] == 'Bebas') ? 'selected' : ''; ?>>Gaya Bebas</option>
                            <option value="Dada" <?= ($data['gaya_renang'] == 'Dada') ? 'selected' : ''; ?>>Gaya Dada</option>
                            <option value="Punggung" <?= ($data['gaya_renang'] == 'Punggung') ? 'selected' : ''; ?>>Gaya Punggung</option>
                            <option value="Kupu-kupu" <?= ($data['gaya_renang'] == 'Kupu-kupu') ? 'selected' : ''; ?>>Gaya Kupu-kupu</option>
                            <option value="Ganti" <?= ($data['gaya_renang'] == 'Ganti') ? 'selected' : ''; ?>>Gaya Ganti (IM)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Jarak (Meter)</label>
                        <input type="number" name="jarak" value="<?= $data['jarak']; ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Waktu Tempuh</label>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <input type="number" name="menit" value="<?= $menit_edit; ?>" min="0" max="59" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-center text-sm rounded-xl block w-full p-3" required>
                            <div class="text-[10px] text-gray-400 text-center mt-1">Menit</div>
                        </div>
                        <div>
                            <input type="number" name="detik" value="<?= $detik_edit; ?>" min="0" max="59" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-center text-sm rounded-xl block w-full p-3" required>
                            <div class="text-[10px] text-gray-400 text-center mt-1">Detik</div>
                        </div>
                        <div>
                            <input type="number" name="milidetik" value="<?= $ms_edit; ?>" min="0" max="99" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-center text-sm rounded-xl block w-full p-3" required>
                            <div class="text-[10px] text-gray-400 text-center mt-1">1/100 dtk</div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Catatan Pelatih</label>
                    <textarea name="catatan" rows="2" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3"><?= htmlspecialchars($data['catatan']); ?></textarea>
                </div>
                <button type="submit" name="edit" class="w-full text-white bg-algolia-blue hover:bg-algolia-darkblue font-bold rounded-xl text-sm px-5 py-3 shadow-lg">Update Rekor</button>
            </form>
        </div>
    </div>
</div>
<?php 
    }
}
?>

<div id="modalTambahPerforma" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b">
                <h3 class="text-lg font-bold text-gray-800">Catat Waktu Performa</h3>
                <button type="button" class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-sm w-8 h-8 ms-auto" data-modal-toggle="modalTambahPerforma">✖</button>
            </div>
            <form action="proses_performa.php" method="POST" class="p-6 text-left">
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Atlet</label>
                    <select name="member_id" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                        <option value="">-- Pilih Atlet --</option>
                        <?php
                        $q_atlet = mysqli_query($koneksi, "SELECT * FROM member WHERE 1=1");
                        if($q_atlet) {
                            while($a_data = mysqli_fetch_assoc($q_atlet)) {
                                $a_id = $a_data['id'];
                                echo "<option value='".$a_id."'>".htmlspecialchars($a_data['nama'])."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tanggal Tes</label>
                        <input type="date" name="tanggal_rekor" value="<?= date('Y-m-d'); ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tipe Kolam</label>
                        <select name="tipe_kolam" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                            <option value="25m">Short Course (25m)</option>
                            <option value="50m" selected>Long Course (50m)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Gaya Renang</label>
                        <select name="gaya_renang" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" required>
                            <option value="Bebas">Gaya Bebas</option>
                            <option value="Dada">Gaya Dada</option>
                            <option value="Punggung">Gaya Punggung</option>
                            <option value="Kupu-kupu">Gaya Kupu-kupu</option>
                            <option value="Ganti">Gaya Ganti (IM)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Jarak (Meter)</label>
                        <input type="number" name="jarak" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" placeholder="Contoh: 50, 100, 200" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Waktu Tempuh</label>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <input type="number" name="menit" min="0" max="59" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-center text-sm rounded-xl block w-full p-3" placeholder="Menit" required>
                            <div class="text-[10px] text-gray-400 text-center mt-1">Menit</div>
                        </div>
                        <div>
                            <input type="number" name="detik" min="0" max="59" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-center text-sm rounded-xl block w-full p-3" placeholder="Detik" required>
                            <div class="text-[10px] text-gray-400 text-center mt-1">Detik</div>
                        </div>
                        <div>
                            <input type="number" name="milidetik" min="0" max="99" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-center text-sm rounded-xl block w-full p-3" placeholder="Ms" required>
                            <div class="text-[10px] text-gray-400 text-center mt-1">1/100 dtk</div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Catatan Pelatih</label>
                    <textarea name="catatan" rows="2" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm rounded-xl block w-full p-3" placeholder="Kondisi atlet, cuaca, atau evaluasi teknik..."></textarea>
                </div>
                <button type="submit" name="tambah" class="w-full text-white bg-algolia-blue hover:bg-algolia-darkblue font-bold rounded-xl text-sm px-5 py-3 shadow-lg">Simpan Rekor</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>