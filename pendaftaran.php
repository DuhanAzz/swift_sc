<?php
require_once 'includes/koneksi.php';

$pesan = "";

// 1. Ambil data cabang/kolam untuk pilihan di form
$kolamList = [];
try {
    $q = mysqli_query($koneksi, "SELECT * FROM cabang ORDER BY nama_cabang ASC");
    if($q) {
        while($row = mysqli_fetch_assoc($q)) {
            $kolamList[] = $row;
        }
    }
} catch (\Exception $e) {}

// 2. Logika memproses form saat tombol daftar diklik telah dipindahkan ke api/proses_daftar.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran — Swift SC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center antialiased p-4">

    <div class="w-full max-w-2xl">
        <!-- Logo -->
        <div class="text-center mb-6 flex flex-col items-center">
            <a href="index.php" class="inline-flex items-center gap-3 mb-4">
                <div class="bg-white p-3 rounded-full shadow-md w-24 h-24 flex items-center justify-center">
                    <img src="assets/logo.png" alt="Swift SC" class="h-16 w-16 object-contain" onerror="this.onerror=null; this.outerHTML='<div class=\'w-16 h-16 rounded-full bg-cyan-600 flex items-center justify-center\'><svg class=\'w-8 h-8 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2.5\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg></div>';">
                </div>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Formulir Pendaftaran</h1>
            <p class="text-sm text-slate-500 mt-1">Gabung Swift Swimming Club sekarang</p>
        </div>

        <!-- Alert messages (Dynamic) -->
        <div id="alert-container" class="hidden mb-4 px-4 py-3 rounded-lg border text-sm">
            <p id="alert-title" class="font-semibold"></p>
            <p id="alert-message" class="text-xs mt-0.5"></p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 md:p-8 shadow-sm mb-6">
            <form id="form-pendaftaran" class="space-y-6">
                
                <!-- Data Pribadi Section -->
                <div>
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Informasi Pribadi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama" required placeholder="Contoh: Budi Santoso"
                                class="w-full bg-white border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-900 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition placeholder-slate-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" required
                                class="w-full bg-white border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-900 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                            <div class="flex gap-4 pt-1">
                                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 flex-1 hover:bg-slate-100 transition-colors">
                                    <input type="radio" name="jenis_kelamin" value="L" required class="w-4 h-4 text-cyan-600 border-slate-300 focus:ring-cyan-500">
                                    <span class="text-sm text-slate-700 font-medium">Laki-laki</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 flex-1 hover:bg-slate-100 transition-colors">
                                    <input type="radio" name="jenis_kelamin" value="P" required class="w-4 h-4 text-cyan-600 border-slate-300 focus:ring-cyan-500">
                                    <span class="text-sm text-slate-700 font-medium">Perempuan</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">No. WhatsApp</label>
                            <input type="number" name="no_hp" required placeholder="08123456789"
                                class="w-full bg-white border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-900 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <!-- Cabang & Domisili Section -->
                <div>
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2 mt-2">Domisili & Lokasi Latihan</h2>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cabang Kolam Renang Tujuan</label>
                            <select name="id_kolam" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-3 px-3 text-sm text-slate-900 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition appearance-auto font-medium">
                                <option value="">— Pilih Cabang Terdekat —</option>
                                <?php foreach($kolamList as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_cabang']) ?> (<?= htmlspecialchars($k['lokasi']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Asal Sekolah</label>
                            <input type="text" name="sekolah" required placeholder="Tuliskan nama sekolah lengkap..."
                                class="w-full bg-white border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-900 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition placeholder-slate-400">
                        </div>


                    </div>
                </div>

                <!-- Bagian Checkbox Persetujuan -->
                <div class="mt-4 pt-4 border-t border-slate-200">
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <div class="flex-shrink-0 mt-0.5">
                            <input type="checkbox" required
                                class="w-4 h-4 text-cyan-600 border-slate-300 rounded focus:ring-cyan-500 cursor-pointer">
                        </div>
                        <div class="text-xs text-slate-500 leading-relaxed group-hover:text-slate-700 transition-colors">
                            Saya menyatakan bahwa seluruh data yang diisi adalah benar, dan saya bersedia mematuhi seluruh <a href="#" class="text-cyan-600 font-semibold hover:underline">Syarat & Ketentuan</a> serta peraturan tata tertib yang berlaku di Swift Swimming Club.
                        </div>
                    </label>
                </div>

                <div class="pt-4">
                    <button id="btn-submit" type="submit"
                        class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3.5 px-4 rounded-xl text-base transition-all shadow-lg shadow-cyan-600/30 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                        <span>Kirim Formulir Pendaftaran</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center pb-8 space-y-3">
            <p class="text-sm text-slate-500">Owner/ Coach/ Admin <a href="login.php" class="text-cyan-600 font-bold hover:underline">Masuk ke Dashboard</a></p>
            <p class="text-sm text-slate-500"><a href="index.php" class="hover:text-cyan-600 transition-colors">&larr; Kembali ke Beranda Utama</a></p>
        </div>
    </div>

    <script>
        document.getElementById('form-pendaftaran').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const btnSubmit = document.getElementById('btn-submit');
            
            // Loading state
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-75', 'cursor-not-allowed');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = `<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Memproses...</span>`;
            
            const formData = new FormData(form);
            
            fetch('api/proses_daftar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pendaftaran Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        form.reset();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: data.message,
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Terhubung',
                    text: 'Koneksi ke server gagal. Silakan coba lagi.',
                    confirmButtonColor: '#2563eb'
                });
            })
            .finally(() => {
                // Reset button state
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-75', 'cursor-not-allowed');
                btnSubmit.innerHTML = originalText;
            });
        });
    </script>
    <?php include_once __DIR__ . '/includes/swal_helper.php'; ?>
</body>
</html>