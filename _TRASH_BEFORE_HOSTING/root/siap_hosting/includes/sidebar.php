<?php
// Pastikan session sudah aktif di file yang memanggil sidebar ini
$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama_user'] ?? 'Pengguna';
?>

<div class="w-64 bg-slate-900 text-white min-h-screen p-4 flex flex-col fixed left-0 top-0">
    <div class="text-2xl font-black text-center mb-8 border-b border-slate-700 pb-4 tracking-tighter mt-4">
        SWIFT<span class="text-orange-500">_SC</span>
    </div>
    
    <div class="mb-8 text-center bg-slate-800/50 p-4 rounded-xl border border-slate-700">
        <p class="text-xs text-slate-400 mb-1">Selamat datang,</p>
        <p class="font-bold text-lg truncate"><?= htmlspecialchars($nama) ?></p>
        <span class="bg-orange-500/20 text-orange-400 border border-orange-500/50 text-xs px-3 py-1 rounded-full uppercase mt-2 inline-block font-bold">
            <?= $role ?>
        </span>
    </div>

    <nav class="flex-1 space-y-2 text-sm font-medium">
        <?php if ($role == 'ceo'): ?>
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2 mt-4 px-4">Menu CEO</p>
            <a href="../ceo/ceo_dashboard.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Dashboard Utama</a>
            <a href="../ceo/ceo_cms_web.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">CMS Halaman Web</a>
            <a href="../ceo/ceo_manage_akun.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Manajemen Akun</a>
            <a href="../ceo/ceo_manage_kolam.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Manajemen Kolam</a>
            <a href="../ceo/ceo_laporan_global.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Laporan Global</a>

        <?php elseif ($role == 'admin'): ?>
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2 mt-4 px-4">Menu Admin Cabang</p>
            <a href="../admin/admin_dashboard.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Dashboard Cabang</a>
            <a href="../admin/admin_member.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Manajemen Member</a>
            <a href="../admin/admin_pelatih.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Tim Pelatih & Performa</a>

        <?php elseif ($role == 'pelatih' || $role == 'coach'): ?>
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2 mt-4 px-4">Menu Pelatih</p>
            <a href="../pelatih/pelatih_dashboard.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Dashboard Pelatih</a>
            <a href="../pelatih/pelatih_absensi.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Input Presensi</a>
            <a href="../pelatih/pelatih_performa.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Catat Performa</a>
            <a href="../pelatih/pelatih_program.php" class="block py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-800 hover:text-orange-400">Jurnal Latihan</a>
        <?php endif; ?>
    </nav>

    <div class="mt-auto pt-4 border-t border-slate-700">
        <a href="../logout.php" class="block py-3 px-4 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition duration-200 text-center font-bold">Logout</a>
    </div>
</div>