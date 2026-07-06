<?php
require_once 'includes/koneksi.php';

$pesan = "";

// 1. Ambil data cabang/kolam untuk pilihan di form
$kolamList = [];
try {
    $kolamList = $database->getDocuments('pools');
} catch (\Exception $e) {}

// 2. Logika memproses form saat tombol daftar diklik
if (isset($_POST['daftar'])) {
    $nama          = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $no_hp         = $_POST['no_hp'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $id_kolam      = $_POST['id_kolam'];
    $tgl_gabung    = date('Y-m-d');

    try {
        $database->newDocument('pending_members', [
            'pool_id' => $id_kolam,
            'nama' => $nama,
            'jenis_kelamin' => $jenis_kelamin,
            'no_hp' => $no_hp,
            'tanggal_lahir' => $tanggal_lahir,
            'tanggal_gabung' => $tgl_gabung,
            'payment_status' => 'Unpaid',
            'status_akun' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $pesan = "sukses";
    } catch (\Exception $e) {
        $pesan = "gagal";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Member Baru - Swift SC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-blue-600 p-6 text-white text-center relative">
            <a href="index.php" class="absolute left-4 top-4 text-blue-200 hover:text-white transition flex items-center gap-1 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <h2 class="text-2xl font-bold mt-2">Gabung Swift SC</h2>
            <p class="text-blue-100 text-sm mt-1">Isi formulir pendaftaran di bawah ini</p>
        </div>

        <div class="p-8">
            <?php if($pesan == "sukses"): ?>
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    <p class="font-bold">Pendaftaran Berhasil!</p>
                    <p class="text-sm">Data Anda sudah terkirim. Admin kami akan segera menghubungi Anda untuk langkah selanjutnya.</p>
                </div>
            <?php elseif($pesan == "gagal"): ?>
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <p class="text-sm">Maaf, pendaftaran gagal. Silakan coba lagi nanti.</p>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="L" required class="w-4 h-4 text-blue-600">
                            <span class="text-sm text-gray-600">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="P" required class="w-4 h-4 text-blue-600">
                            <span class="text-sm text-gray-600">Perempuan</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">No. WhatsApp</label>
                    <input type="number" name="no_hp" required placeholder="08123456789"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cabang Kolam Renang</label>
                    <select name="id_kolam" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition bg-white">
                        <option value="">-- Pilih Cabang Terdekat --</option>
                        <?php foreach($kolamList as $row): ?>
                            <option value="<?= htmlspecialchars($row['id'] ?? ''); ?>"><?= htmlspecialchars($row['name'] ?? ''); ?> - <?= htmlspecialchars($row['location'] ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="daftar"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg shadow-lg shadow-orange-200 transition-all duration-300 transform hover:-translate-y-1">
                    Daftar Sekarang
                </button>
            </form>
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-500">Sudah menjadi member? <a href="login.php" class="text-blue-600 font-semibold">Masuk ke Dashboard</a></p>
        </div>
    </div>

</body>
</html>