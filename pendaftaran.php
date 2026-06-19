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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        algolia: { blue: '#5468FF', darkblue: '#3A4DC7', navy: '#21243D' },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#F5F5FA] min-h-screen flex items-center justify-center antialiased p-4">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-6">
            <a href="index.php" class="inline-flex items-center gap-2 mb-4">
                <img src="assets/logo.png" alt="Swift SC" class="h-9 w-9 object-contain rounded-lg" onerror="this.onerror=null; this.outerHTML='<div class=\'w-9 h-9 rounded-lg bg-[#5468FF] flex items-center justify-center\'><svg class=\'w-4 h-4 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2.5\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg></div>';">
                <span class="font-bold text-[15px] text-[#21243D]">Swift<span class="text-[#5468FF]">SC</span></span>
            </a>
            <h1 class="text-xl font-bold text-[#21243D]">Formulir Pendaftaran</h1>
            <p class="text-sm text-[#6B6F8D] mt-1">Gabung Swift Swimming Club sekarang</p>
        </div>

        <!-- Alert messages (Dynamic) -->
        <div id="alert-container" class="hidden mb-4 px-4 py-3 rounded-lg border text-sm">
            <p id="alert-title" class="font-semibold"></p>
            <p id="alert-message" class="text-xs mt-0.5"></p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-[#E8E8EF] p-6 shadow-sm">
            <form id="form-pendaftaran" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#6B6F8D] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Contoh: Budi Santoso"
                        class="w-full bg-white border border-[#E8E8EF] rounded-lg py-2.5 px-3 text-sm text-[#21243D] focus:outline-none focus:border-[#5468FF] focus:ring-2 focus:ring-[#5468FF]/10 transition placeholder-[#9CA0B8]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6F8D] uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="L" required class="w-4 h-4 text-[#5468FF] border-[#E8E8EF] focus:ring-[#5468FF]">
                            <span class="text-sm text-[#4A4F6A]">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="P" required class="w-4 h-4 text-[#5468FF] border-[#E8E8EF] focus:ring-[#5468FF]">
                            <span class="text-sm text-[#4A4F6A]">Perempuan</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6F8D] uppercase tracking-wider mb-1.5">No. WhatsApp</label>
                    <input type="number" name="no_hp" required placeholder="08123456789"
                        class="w-full bg-white border border-[#E8E8EF] rounded-lg py-2.5 px-3 text-sm text-[#21243D] focus:outline-none focus:border-[#5468FF] focus:ring-2 focus:ring-[#5468FF]/10 transition placeholder-[#9CA0B8]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6F8D] uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required
                        class="w-full bg-white border border-[#E8E8EF] rounded-lg py-2.5 px-3 text-sm text-[#21243D] focus:outline-none focus:border-[#5468FF] focus:ring-2 focus:ring-[#5468FF]/10 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#6B6F8D] uppercase tracking-wider mb-1.5">Cabang Kolam Renang</label>
                    <select name="id_kolam" required
                        class="w-full bg-white border border-[#E8E8EF] rounded-lg py-2.5 px-3 text-sm text-[#21243D] focus:outline-none focus:border-[#5468FF] focus:ring-2 focus:ring-[#5468FF]/10 transition appearance-auto">
                        <option value="">— Pilih Cabang Terdekat —</option>
                        <?php foreach($kolamList as $row): ?>
                            <option value="<?= htmlspecialchars($row['id'] ?? ''); ?>"><?= htmlspecialchars($row['nama_cabang'] ?? ''); ?> - <?= htmlspecialchars($row['lokasi'] ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" id="btn-submit"
                    class="w-full bg-[#5468FF] hover:bg-[#3A4DC7] text-white font-semibold py-2.5 px-4 rounded-lg text-sm transition-colors mt-2 flex justify-center items-center gap-2">
                    <span>Daftar Sekarang</span>
                </button>
            </form>
        </div>

        <div class="mt-5 text-center">
            <p class="text-xs text-[#9CA0B8]">Sudah menjadi member? <a href="login.php" class="text-[#5468FF] font-semibold hover:underline">Masuk ke Dashboard</a></p>
        </div>

        <div class="mt-3 text-center">
            <a href="index.php" class="text-sm text-[#6B6F8D] hover:text-[#5468FF] transition-colors">&larr; Kembali ke Halaman Utama</a>
        </div>
    </div>

    <script>
        document.getElementById('form-pendaftaran').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const btnSubmit = document.getElementById('btn-submit');
            const alertContainer = document.getElementById('alert-container');
            const alertTitle = document.getElementById('alert-title');
            const alertMessage = document.getElementById('alert-message');
            
            // Loading state
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-75', 'cursor-not-allowed');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = `<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Memproses...</span>`;
            
            alertContainer.classList.add('hidden');
            
            const formData = new FormData(form);
            
            fetch('api/proses_daftar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alertContainer.classList.remove('hidden', 'bg-green-50', 'border-green-200', 'text-green-700', 'bg-red-50', 'border-red-200', 'text-red-700');
                
                if (data.status === 'sukses') {
                    alertContainer.classList.add('bg-green-50', 'border-green-200', 'text-green-700');
                    alertTitle.textContent = 'Pendaftaran Berhasil!';
                    alertMessage.textContent = data.message;
                    form.reset();
                } else {
                    alertContainer.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                    alertTitle.textContent = 'Terjadi Kesalahan';
                    alertMessage.textContent = data.message;
                }
            })
            .catch(error => {
                alertContainer.classList.remove('hidden');
                alertContainer.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                alertTitle.textContent = 'Terjadi Kesalahan';
                alertMessage.textContent = 'Koneksi ke server gagal. Silakan coba lagi.';
            })
            .finally(() => {
                // Reset button state
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-75', 'cursor-not-allowed');
                btnSubmit.innerHTML = originalText;
            });
        });
    </script>
</body>
</html>