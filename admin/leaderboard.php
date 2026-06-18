<?php
session_start();
if ($_SESSION['status'] != "sudah_login") { header("location:../login.php?pesan=belum_login"); exit; }
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// Menangkap filter dari URL (jika ada), jika tidak ada set default ke Gaya Bebas, 50m, Long Course
$filter_style = isset($_GET['style']) ? $_GET['style'] : 'Bebas';
$filter_distance = isset($_GET['distance']) ? $_GET['distance'] : '50';
$filter_pool = isset($_GET['pool']) ? $_GET['pool'] : '50m';
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-algolia-navy">Papan Peringkat (Leaderboard)</h1>
            <p class="text-sm text-gray-500">Pantau waktu tercepat atlet berdasarkan gaya dan jarak renang</p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="leaderboard.php" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-1/3">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Gaya Renang</label>
                    <select name="style" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-2.5">
                        <option value="Bebas" <?= ($filter_style == 'Bebas') ? 'selected' : ''; ?>>Gaya Bebas</option>
                        <option value="Dada" <?= ($filter_style == 'Dada') ? 'selected' : ''; ?>>Gaya Dada</option>
                        <option value="Punggung" <?= ($filter_style == 'Punggung') ? 'selected' : ''; ?>>Gaya Punggung</option>
                        <option value="Kupu-kupu" <?= ($filter_style == 'Kupu-kupu') ? 'selected' : ''; ?>>Gaya Kupu-kupu</option>
                        <option value="Ganti" <?= ($filter_style == 'Ganti') ? 'selected' : ''; ?>>Gaya Ganti (IM)</option>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Jarak (m)</label>
                    <input type="number" name="distance" value="<?= htmlspecialchars($filter_distance); ?>" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-2.5" placeholder="Contoh: 50" required>
                </div>
                <div class="w-full md:w-1/3">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tipe Kolam</label>
                    <select name="pool" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-2.5">
                        <option value="25m" <?= ($filter_pool == '25m') ? 'selected' : ''; ?>>Short Course (25m)</option>
                        <option value="50m" <?= ($filter_pool == '50m') ? 'selected' : ''; ?>>Long Course (50m)</option>
                    </select>
                </div>
                <div class="w-full md:w-auto">
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 font-bold rounded-xl text-sm px-6 py-2.5 shadow-md transition-colors">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <div class="card overflow-hidden">
            <div class="p-4 bg-slate-800 border-b border-slate-700">
                <h2 class="text-white font-bold text-lg text-center tracking-wide">
                    👑 TOP RECORD: <?= strtoupper($filter_style); ?> <?= $filter_distance; ?>M (<?= strtoupper($filter_pool); ?>)
                </h2>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-center w-24">Peringkat</th>
                        <th class="px-6 py-4">Nama Atlet</th>
                        <th class="px-6 py-4 text-center">Waktu Tercepat</th>
                        <th class="px-6 py-4 text-center">Tgl Pencatatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil waktu tercepat, diurutkan berdasarkan time_ms dari yang terkecil (ASC)
                    $query = mysqli_query($koneksi, "
                        SELECT p.*, a.nama AS nama_atlet 
                        FROM performances p 
                        JOIN atlet a ON p.member_id = a.id 
                        WHERE p.swim_style = '$filter_style' 
                        AND p.distance = '$filter_distance' 
                        AND p.pool_length = '$filter_pool' 
                        ORDER BY p.time_ms ASC 
                        LIMIT 50
                    ");
                    
                    if(mysqli_num_rows($query) > 0) {
                        $rank = 1;
                        while($data = mysqli_fetch_assoc($query)) {
                            // Logika untuk memberikan warna/ikon pada peringkat 1, 2, dan 3
                            $rank_display = $rank;
                            $row_class = "bg-white";
                            $time_class = "text-slate-800";

                            if($rank == 1) {
                                $rank_display = '<span class="text-2xl" title="Juara 1">🥇</span>';
                                $row_class = "bg-yellow-50";
                                $time_class = "text-yellow-700";
                            } else if($rank == 2) {
                                $rank_display = '<span class="text-2xl" title="Juara 2">🥈</span>';
                                $row_class = "bg-gray-100";
                                $time_class = "text-gray-700";
                            } else if($rank == 3) {
                                $rank_display = '<span class="text-2xl" title="Juara 3">🥉</span>';
                                $row_class = "bg-orange-50";
                                $time_class = "text-orange-700";
                            }
                    ?>
                    <tr class="<?= $row_class; ?> border-b hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-center font-bold text-gray-900 text-lg"><?= $rank_display; ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800 text-base"><?= htmlspecialchars($data['nama_atlet']); ?></td>
                        <td class="px-6 py-4 text-center font-black text-xl <?= $time_class; ?> tracking-wider">
                            <?= htmlspecialchars($data['time_formatted']); ?>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500 font-medium">
                            <?= date('d M Y', strtotime($data['record_date'])); ?>
                        </td>
                    </tr>
                    <?php 
                            $rank++;
                        } 
                    } else {
                        echo '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 py-10">Belum ada rekor yang dicatat untuk kategori ini.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>