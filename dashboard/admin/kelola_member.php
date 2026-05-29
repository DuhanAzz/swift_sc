<?php
session_start();
require_once '../../config/database.php';

// Proteksi: Hanya 'admin' yang boleh masuk halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$pesan = "";

// --- LOGIKA TERIMA / TOLAK MEMBER ---
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_member = bersihkan_input($_GET['id']);
    
    if ($_GET['aksi'] == 'terima') {
        // Ubah status menjadi Aktif
        $update = mysqli_query($koneksi, "UPDATE members SET status_akun = 'Aktif' WHERE id = '$id_member'");
        if ($update) $pesan = "<div class='p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200'>Member berhasil disetujui dan diaktifkan!</div>";
    } 
    elseif ($_GET['aksi'] == 'tolak') {
        // Hapus data pendaftar
        $hapus = mysqli_query($koneksi, "DELETE FROM members WHERE id = '$id_member'");
        if ($hapus) $pesan = "<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200'>Pendaftaran member berhasil ditolak/dihapus.</div>";
    }
}

// Ambil data member yang masih 'Pending'
$query_pending = mysqli_query($koneksi, "SELECT m.*, p.name as nama_kolam FROM members m LEFT JOIN pools p ON m.pool_id = p.id WHERE m.status_akun = 'Pending' ORDER BY m.tanggal_gabung DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pendaftar - Swift SC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 flex">

    <?php include '../../includes/sidebar.php'; ?>

    <div class="ml-64 p-8 w-full">
        
        <div class="mb-8 border-b border-slate-200 pb-4 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Persetujuan Member Baru</h1>
                <p class="text-slate-500 mt-1">Tinjau dan setujui calon atlet yang mendaftar via website.</p>
            </div>
            <a href="index.php" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">&larr; Kembali ke Dashboard</a>
        </div>

        <?= $pesan; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs tracking-wider">
                            <th class="p-4 font-bold">Nama Lengkap</th>
                            <th class="p-4 font-bold">Kontak / Tgl Lahir</th>
                            <th class="p-4 font-bold">Cabang Pilihan</th>
                            <th class="p-4 font-bold">Tgl Daftar</th>
                            <th class="p-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(mysqli_num_rows($query_pending) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query_pending)): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4">
                                        <p class="font-bold text-slate-800"><?= htmlspecialchars($row['nama']) ?></p>
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-slate-100 text-slate-600">
                                            <?= $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($row['no_hp']) ?></p>
                                        <p class="text-xs text-slate-500"><?= htmlspecialchars($row['tanggal_lahir']) ?></p>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 text-sm font-semibold rounded-lg border border-blue-100">
                                            <?= htmlspecialchars($row['nama_kolam'] ?? 'Belum Pilih') ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-slate-500">
                                        <?= date('d M Y', strtotime($row['tanggal_gabung'])) ?>
                                    </td>
                                    <td class="p-4 text-center space-x-2">
                                        <a href="?aksi=terima&id=<?= $row['id'] ?>" 
                                           onclick="return confirm('Anda yakin ingin MENYETUJUI <?= $row['nama'] ?> sebagai member aktif?');"
                                           class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
                                            Terima
                                        </a>
                                        <a href="?aksi=tolak&id=<?= $row['id'] ?>" 
                                           onclick="return confirm('Anda yakin ingin MENOLAK dan MENGHAPUS data <?= $row['nama'] ?>?');"
                                           class="inline-block bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
                                            Tolak
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">
                                    Tidak ada pendaftar baru saat ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>