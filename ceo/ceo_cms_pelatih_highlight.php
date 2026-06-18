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
                'sukses_highlight' => 'Status highlight pelatih berhasil diperbarui!',
                'gagal' => 'Terjadi kesalahan saat memproses data.'
            ];
            $p = $_GET['pesan'];
            if (array_key_exists($p, $pesanMap)) {
                $color = strpos($p, 'gagal') !== false ? 'red' : 'green';
                echo "<div class='p-4 mb-4 text-sm text-{$color}-800 rounded-lg bg-{$color}-50 border border-{$color}-200'>{$pesanMap[$p]}</div>";
            }
        }
        ?>

        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Highlight Profil Pelatih</h1>
                <p class="text-sm text-gray-500">Pilih pelatih unggulan yang akan ditampilkan pada halaman depan (Landing Page).</p>
            </div>
            <a href="ceo_cms_web.php" class="bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold py-2 px-4 rounded-lg text-sm transition-all">&larr; Kembali</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                    $foto_pelatih = (!empty($data['foto']) && file_exists("../admin/" . $data['foto'])) ? "../admin/" . $data['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($data['nama']) . "&background=0f172a&color=fff&size=256";
                    $is_highlighted = isset($data['is_highlighted']) && $data['is_highlighted'] == 1;
            ?>
            <div class="bg-white rounded-2xl shadow-sm border <?= $is_highlighted ? 'border-teal-400 ring-2 ring-teal-100' : 'border-gray-100' ?> overflow-hidden relative">
                
                <?php if ($is_highlighted): ?>
                <div class="absolute top-2 right-2 bg-teal-500 text-white text-xs font-bold px-2 py-1 rounded shadow-md z-10 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    Ditampilkan
                </div>
                <?php endif; ?>

                <div class="h-48 bg-slate-800 bg-cover bg-center" style="background-image: url('<?= $foto_pelatih ?>');"></div>
                <div class="p-5 text-center">
                    <h4 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($data['nama']); ?></h4>
                    <p class="text-orange-500 font-semibold text-sm mb-2"><?= htmlspecialchars($data['jabatan'] ?? 'Pelatih'); ?></p>
                    <p class="text-xs text-slate-500 line-clamp-2 mb-4"><?= htmlspecialchars($data['sertifikasi'] ?? '-'); ?></p>

                    <form action="ceo_cms_pelatih_proses.php" method="POST">
                        <input type="hidden" name="id" value="<?= $data['id']; ?>">
                        <input type="hidden" name="current_status" value="<?= $is_highlighted ? '1' : '0'; ?>">
                        
                        <?php if ($is_highlighted): ?>
                            <button type="submit" name="toggle_highlight" class="w-full bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold py-2 rounded-lg text-sm transition-colors border border-slate-300">Sembunyikan dari Publik</button>
                        <?php else: ?>
                            <button type="submit" name="toggle_highlight" class="w-full bg-teal-500 text-white hover:bg-teal-600 font-bold py-2 rounded-lg text-sm transition-colors shadow-md">Tampilkan ke Publik</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <?php } } else { echo "<div class='col-span-3 text-center py-12 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300'>Belum ada data pelatih. Tambahkan pelatih di menu Manajemen Akun.</div>"; } ?>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
