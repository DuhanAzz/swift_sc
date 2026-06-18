<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

$tanggal_absensi = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        
        <?php 
        if(isset($_GET['pesan']) && $_GET['pesan'] == "sukses_simpan"){
            echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 font-medium">Absensi tanggal '.date('d M Y', strtotime($tanggal_absensi)).' berhasil disimpan!</div>';
        }
        ?>

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Data Entry Presensi</h1>
                <p class="text-sm text-gray-500">Mode Spreadsheet - Isi data layaknya Microsoft Excel</p>
            </div>
            <form action="presensi.php" method="GET" class="flex items-center gap-2">
                <input type="date" name="tanggal" value="<?= $tanggal_absensi; ?>" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2 shadow-sm">
                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-md text-sm px-4 py-2 transition-all">
                    Load Data
                </button>
            </form>
        </div>

        <div class="bg-white p-2 shadow-sm border border-gray-200">
            <form action="proses_presensi.php" method="POST">
                <input type="hidden" name="tanggal" value="<?= $tanggal_absensi; ?>">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-800 border-collapse border border-gray-400">
                        <thead>
                            <tr class="bg-gray-200 border-b border-gray-400">
                                <th class="border border-gray-400 px-3 py-2 text-center w-12 font-bold text-gray-700">NO</th>
                                <th class="border border-gray-400 px-3 py-2 font-bold text-gray-700">NAMA ATLET</th>
                                <th class="border border-gray-400 px-3 py-2 text-center w-40 font-bold text-gray-700">STATUS</th>
                                <th class="border border-gray-400 px-3 py-2 font-bold text-gray-700">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $coach_cabang_id = $_SESSION['cabang_id'] ?? '';
                            
                            $q_atlet = [];
                            $res_atlet = mysqli_query($koneksi, "SELECT * FROM member WHERE 1=1 AND cabang_id='$coach_cabang_id' ORDER BY nama ASC");
                            if($res_atlet) {
                                while($row = mysqli_fetch_assoc($res_atlet)) {
                                    $q_atlet[] = $row;
                                }
                            }

                            // Ambil data presensi yang sudah ada hari ini
                            $presensi_hari_ini = [];
                            $res_presensi = mysqli_query($koneksi, "SELECT * FROM absensi WHERE tanggal='$tanggal_absensi' AND cabang_id='$coach_cabang_id'");
                            if($res_presensi) {
                                while($row = mysqli_fetch_assoc($res_presensi)) {
                                    $presensi_hari_ini[$row['member_id']] = [
                                        'status' => $row['status'],
                                        'keterangan' => $row['keterangan']
                                    ];
                                }
                            }

                            $no = 1;
                            foreach($q_atlet as $a){
                                $atlet_id = $a['id'];
                                
                                $status_skrg = 'Hadir';
                                $ket_skrg = '';
                                if(isset($presensi_hari_ini[$atlet_id])) {
                                    $status_skrg = $presensi_hari_ini[$atlet_id]['status'] ?? 'Hadir';
                                    $ket_skrg = $presensi_hari_ini[$atlet_id]['keterangan'] ?? '';
                                }
                            ?>
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="border border-gray-300 px-3 py-1 text-center bg-gray-50 text-gray-500 font-medium"><?= $no++; ?></td>
                                
                                <td class="border border-gray-300 px-3 py-1 font-semibold text-gray-800 bg-gray-50 uppercase text-xs"><?= htmlspecialchars($a['nama']); ?></td>
                                
                                <td class="border border-gray-300 p-0">
                                    <select name="status[<?= $atlet_id; ?>]" class="w-full h-full border-0 bg-transparent focus:ring-2 focus:ring-blue-500 px-2 py-1.5 text-sm font-medium cursor-pointer outline-none <?php 
                                        if($status_skrg == 'Hadir') echo 'text-green-700';
                                        elseif($status_skrg == 'Izin') echo 'text-blue-700';
                                        elseif($status_skrg == 'Sakit') echo 'text-yellow-700';
                                        else echo 'text-red-700';
                                    ?>">
                                        <option value="Hadir" <?= ($status_skrg == 'Hadir') ? 'selected' : ''; ?> class="text-green-700">Hadir</option>
                                        <option value="Izin" <?= ($status_skrg == 'Izin') ? 'selected' : ''; ?> class="text-blue-700">Izin</option>
                                        <option value="Sakit" <?= ($status_skrg == 'Sakit') ? 'selected' : ''; ?> class="text-yellow-700">Sakit</option>
                                        <option value="Alpa" <?= ($status_skrg == 'Alpa') ? 'selected' : ''; ?> class="text-red-700">Alpa</option>
                                    </select>
                                </td>
                                
                                <td class="border border-gray-300 p-0">
                                    <input type="text" name="keterangan[<?= $atlet_id; ?>]" value="<?= htmlspecialchars($ket_skrg); ?>" class="w-full h-full border-0 bg-transparent focus:ring-2 focus:ring-blue-500 px-3 py-1.5 text-sm outline-none placeholder-gray-300" placeholder="-">
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-between items-center bg-gray-100 p-3 border border-gray-300 rounded-md">
                    <p class="text-xs text-gray-600 font-medium">Tips: Gunakan tombol <kbd class="px-1 py-0.5 bg-white border border-gray-300 rounded text-gray-800">Tab</kbd> pada keyboard untuk berpindah sel dengan cepat.</p>
                    <button type="submit" name="simpan_presensi" class="text-white bg-blue-700 hover:bg-blue-800 font-bold rounded text-sm px-8 py-2.5 shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Data Kehadiran
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>