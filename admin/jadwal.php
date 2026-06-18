<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-algolia-navy">Jadwal Latihan</h1>
            <p class="text-sm text-gray-500">Atur agenda dan program latihan mingguan</p>
        </div>

        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "sukses") echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">Berhasil menambahkan jadwal baru!</div>';
            if($_GET['pesan'] == "hapus") echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">Jadwal berhasil dihapus!</div>';
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-1">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="font-bold text-gray-700 mb-4 uppercase text-sm border-b pb-2">Tambah Jadwal Baru</h2>
                    <form action="proses_jadwal.php" method="POST">
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 mb-2">Hari</label>
                            <select name="hari" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5" required>
                                <option value="Senin">Senin</option><option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option><option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option><option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 mb-2">Lokasi / Kolam</label>
                            <input type="text" name="lokasi" placeholder="Misal: Kolam Renang Tirtomoyo" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5" required>
                        </div>
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-500 mb-2">Fokus Program (Opsional)</label>
                            <textarea name="program" rows="3" placeholder="Misal: Sprint 50m Gaya Bebas" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"></textarea>
                        </div>
                        <button type="submit" name="tambah_jadwal" class="w-full text-white bg-blue-700 hover:bg-blue-800 font-bold rounded-lg text-sm px-5 py-2.5">
                            Simpan Jadwal
                        </button>
                    </form>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4">Hari & Waktu</th>
                                <th class="px-6 py-4">Lokasi</th>
                                <th class="px-6 py-4">Program</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php
                            $jadwalArray = [];
                            $q = mysqli_query($koneksi, "SELECT * FROM jadwal");
                            if($q) {
                                while($row = mysqli_fetch_assoc($q)) {
                                    $jadwalArray[] = $row;
                                }
                            }

                            $hari_order = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];

                            usort($jadwalArray, function($a, $b) use ($hari_order) {
                                $orderA = $hari_order[$a['hari']] ?? 8;
                                $orderB = $hari_order[$b['hari']] ?? 8;
                                if ($orderA == $orderB) {
                                    return strcmp($a['jam_mulai'] ?? '', $b['jam_mulai'] ?? '');
                                }
                                return $orderA - $orderB;
                            });

                            if(count($jadwalArray) > 0){
                                foreach($jadwalArray as $d){
                            ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-800 block text-base"><?= $d['hari']; ?></span>
                                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded mt-1 inline-block">
                                        <?= date('H:i', strtotime($d['jam_mulai'])); ?> - <?= date('H:i', strtotime($d['jam_selesai'])); ?> WIB
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-700"><?= htmlspecialchars($d['lokasi']); ?></td>
                                <td class="px-6 py-4 text-gray-500 italic"><?= nl2br(htmlspecialchars($d['program'])); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="proses_jadwal.php?hapus=<?= $d['id']; ?>" onclick="return confirm('Yakin ingin menghapus jadwal ini?')" class="text-red-500 hover:text-red-700 font-bold bg-red-50 px-3 py-1.5 rounded-lg text-xs">Hapus</a>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo '<tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada jadwal latihan yang dibuat.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>