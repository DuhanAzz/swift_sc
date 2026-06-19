<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// Process form submission
if(isset($_POST['simpan_tentang'])) {
    $visi = mysqli_real_escape_string($koneksi, $_POST['visi']);
    $misi = mysqli_real_escape_string($koneksi, $_POST['misi']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $q_check = mysqli_query($koneksi, "SELECT id FROM tentang_club LIMIT 1");
    if($q_check && mysqli_num_rows($q_check) > 0) {
        $row = mysqli_fetch_assoc($q_check);
        mysqli_query($koneksi, "UPDATE tentang_club SET visi='$visi', misi='$misi', deskripsi='$deskripsi' WHERE id='{$row['id']}'");
    } else {
        mysqli_query($koneksi, "INSERT INTO tentang_club (visi, misi, deskripsi) VALUES ('$visi', '$misi', '$deskripsi')");
    }
    $pesan = "sukses";
}

// Load current data
$tentang = ['visi' => '', 'misi' => '', 'deskripsi' => ''];
$q_tentang = mysqli_query($koneksi, "SELECT * FROM tentang_club LIMIT 1");
if($q_tentang && $row = mysqli_fetch_assoc($q_tentang)) {
    $tentang = $row;
}
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-algolia-navy">Visi, Misi & Profil Klub</h1>
                <p class="text-sm text-gray-500">Konten ini tampil di halaman depan pada bagian "Tentang Kami"</p>
            </div>
            <a href="ceo_cms_web.php" class="text-sm text-algolia-blue hover:underline">← Kembali</a>
        </div>

        <?php if(isset($pesan) && $pesan == 'sukses'): ?>
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">Konten berhasil disimpan!</div>
        <?php endif; ?>

        <form action="ceo_cms_tentang.php" method="POST">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Visi</label>
                    <textarea name="visi" rows="4" class="w-full bg-gray-50 border border-[#E8E8EF] rounded-xl p-3 text-sm" placeholder="Masukkan visi klub..."><?= htmlspecialchars($tentang['visi'] ?? '') ?></textarea>
                </div>
                <div class="card p-6">
                    <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Misi</label>
                    <textarea name="misi" rows="4" class="w-full bg-gray-50 border border-[#E8E8EF] rounded-xl p-3 text-sm" placeholder="Masukkan misi klub..."><?= htmlspecialchars($tentang['misi'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="card p-6 mt-6">
                <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Deskripsi Klub</label>
                <textarea name="deskripsi" rows="5" class="w-full bg-gray-50 border border-[#E8E8EF] rounded-xl p-3 text-sm" placeholder="Masukkan deskripsi lengkap klub..."><?= htmlspecialchars($tentang['deskripsi'] ?? '') ?></textarea>
                <p class="text-xs text-gray-400 mt-2">Teks ini mengganti paragraf "Tentang Kami" di halaman depan.</p>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" name="simpan_tentang" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2.5 px-8 rounded-lg shadow-md">Simpan Perubahan</button>
            </div>
        </form>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
