<?php 
include 'includes/koneksi.php'; 

// Ambil CMS Content
$cmsData = [
    'hero_title' => "Jump in. let's swim!",
    'hero_subtitle' => "SWIFT SC",
    'hero_desc' => "Klub renang resmi dan tersertifikasi dengan fasilitas pelatih berlisensi nasional & internasional.",
    'about_text' => "Swift Swimming Club adalah klub renang yang resmi dan telah tersertifikasi. Dengan fasilitas pelatih berlisensi nasional maupun internasional dan peralatan renang yang memadai."
];

// Load CMS Landing (Banner text)
try {
    $q_cms = mysqli_query($koneksi, "SELECT judul, konten FROM cms_landing WHERE tipe='Banner' AND status='Aktif' LIMIT 1");
    if($q_cms && $row_cms = mysqli_fetch_assoc($q_cms)) {
        $cmsData['hero_title'] = $row_cms['judul'];
        $cmsData['hero_desc'] = $row_cms['konten'];
    }
} catch (\Exception $e) {}

// Load tentang_club (deskripsi)
try {
    $q_tentang = mysqli_query($koneksi, "SELECT * FROM tentang_club LIMIT 1");
    if($q_tentang && $row_tentang = mysqli_fetch_assoc($q_tentang)) {
        if(!empty($row_tentang['deskripsi'])) $cmsData['about_text'] = $row_tentang['deskripsi'];
    }
} catch (\Exception $e) {}

// Load slider untuk dynamic hero slider (menggabungkan gambar & teks)
$banners = [];
try {
    $q_banners = mysqli_query($koneksi, "SELECT * FROM slider WHERE status='Aktif' ORDER BY urutan ASC");
    if($q_banners) { while($b = mysqli_fetch_assoc($q_banners)) { $banners[] = $b; } }
} catch (\Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift SC - Jump in. let's swim!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .glass-panel {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .bg-ocean {
            background-color: #0b162c;
            background-image: 
                radial-gradient(ellipse at top right, rgba(13, 148, 136, 0.25) 0%, transparent 60%),
                radial-gradient(ellipse at bottom left, rgba(249, 115, 22, 0.15) 0%, transparent 60%);
            background-size: cover;
            background-repeat: no-repeat;
            /* background-attachment: fixed; dihapus untuk cegah lag */
        }
        .bg-ocean-glow {
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(13, 148, 136, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(249, 115, 22, 0.1) 0%, transparent 50%);
            animation: soft-pulse 8s infinite alternate ease-in-out;
        }
        @keyframes soft-pulse {
            from { opacity: 0.7; transform: scale(1); }
            to { opacity: 1; transform: scale(1.05); }
        }
        .badge-cyan {
            background-color: #06b6d4;
            color: #ffffff;
            box-shadow: 0 4px 14px 0 rgba(6, 182, 212, 0.39);
        }
    </style>
</head>
<body class="bg-ocean text-slate-100 antialiased relative overflow-x-hidden min-h-screen">
    
    <!-- Background Decorative Elements (Optimized) -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none bg-ocean-glow"></div>

    <nav class="glass-panel sticky top-0 z-50 border-b border-slate-700/50 shadow-sm">
        <div class="container mx-auto flex justify-between items-center px-4 py-3">
            
            <!-- Logo (Left Side) -->
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="bg-white p-2 w-12 h-12 flex items-center justify-center rounded-full shadow-lg shadow-cyan-900/20">
                    <img src="assets/logo.png" alt="Swift SC Logo" class="w-full h-full object-contain transition-transform group-hover:scale-105" onerror="this.onerror=null; this.outerHTML='<span class=\'text-xs font-black italic tracking-tighter text-slate-900\'>SWIFT<span class=\'text-cyan-500\'>_SC</span></span>';">
                </div>
                <span class="font-black italic tracking-tighter text-xl hidden sm:block">SWIFT<span class="text-cyan-400">_SC</span></span>
            </a>

            <!-- Navigation Links (Right Side) -->
            <div class="hidden md:flex gap-6 font-semibold text-sm items-center ml-auto">
                <a href="#tentang" class="text-slate-300 hover:text-cyan-400 transition-colors">Profil</a>
                <a href="#program" class="text-slate-300 hover:text-cyan-400 transition-colors">Program & Sertifikasi</a>
                <a href="#jadwal" class="text-slate-300 hover:text-cyan-400 transition-colors">Jadwal & Biaya</a>
                <a href="#pelatih" class="text-slate-300 hover:text-cyan-400 transition-colors">Pelatih</a>
                <a href="login.php" class="bg-cyan-500/20 border border-cyan-500/50 text-cyan-400 px-5 py-2 rounded-full font-bold hover:bg-cyan-500 hover:text-white transition-all shadow-lg hover:shadow-cyan-500/25 ml-4">Login Portal</a>
            </div>

            <!-- Hamburger Button for Mobile -->
            <button class="md:hidden text-slate-300 hover:text-white ml-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            
        </div>
    </nav>

    <!-- Hero Section (True Split-Screen & Mobile Optimized) -->
    <header class="relative w-full min-h-[100dvh] flex flex-col md:flex-row" id="heroHeader">
        
        <!-- Sisi Kiri: Rata Kiri, Full Blur, Teks -->
        <div class="relative w-full md:w-1/2 flex-[1.2] md:flex-none md:min-h-screen flex flex-col justify-center overflow-hidden">
            <!-- Background Kiri (Statis) -->
            <?php $first_bg = count($banners) > 0 ? "admin/uploads/".$banners[0]['gambar'] : "https://images.unsplash.com/photo-1572334057861-6d72dbb688d2?auto=format&fit=crop&w=1920&q=80"; ?>
            <div id="heroLeftBg" class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000" style="background-image: url('<?= htmlspecialchars($first_bg) ?>');"></div>
            
            <!-- Overlay Blur & Teks Rata Kiri (Permanen) -->
            <div class="absolute inset-0 bg-slate-900/70 md:bg-slate-900/60 backdrop-blur-xl z-10 flex flex-col justify-center px-6 sm:px-12 md:px-16 lg:px-24">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 to-transparent"></div>
                <div class="absolute top-20 -left-10 w-64 h-64 bg-cyan-500/20 rounded-full filter blur-[60px] -z-10"></div>
                
                <h2 class="text-xl sm:text-3xl md:text-4xl mb-2 sm:mb-4 text-cyan-300 font-extrabold drop-shadow-md tracking-wider">
                    Jump in. let's swim!
                </h2>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black mb-4 sm:mb-6 uppercase tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-white via-slate-100 to-cyan-400 drop-shadow-2xl leading-[1.1]">
                    SWIFT SC
                </h1>
                <p class="text-sm sm:text-base md:text-xl text-slate-200 font-medium mb-8 sm:mb-10 leading-relaxed max-w-lg drop-shadow">
                    Klub renang resmi dan tersertifikasi dengan fasilitas pelatih berlisensi nasional & internasional.
                </p>
                <div>
                    <a href="pendaftaran.php" class="bg-cyan-600 hover:bg-cyan-500 text-white px-6 py-3 sm:px-8 sm:py-4 rounded-full font-bold text-sm sm:text-base md:text-lg transition-all inline-flex items-center gap-3 transform hover:-translate-y-1 hover:scale-105 border border-cyan-400 shadow-[0_0_20px_rgba(6,182,212,0.4)]">
                        DAFTAR SEKARANG
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Gambar Dinamis Bergeser -->
        <div class="relative w-full md:w-1/2 flex-1 md:flex-none md:min-h-screen bg-slate-900 overflow-hidden">
            <!-- Shadow gradient di perbatasan untuk pemisah yang halus -->
            <div class="absolute inset-y-0 left-0 w-6 bg-gradient-to-r from-slate-900/50 to-transparent z-10 hidden md:block"></div>
            <div class="absolute inset-x-0 top-0 h-6 bg-gradient-to-b from-slate-900/50 to-transparent z-10 block md:hidden"></div>
            
            <div id="heroRightBg" class="absolute inset-0 bg-cover bg-center transition-all duration-[5000ms] ease-linear transform scale-110" style="background-image: url('<?= htmlspecialchars($first_bg) ?>');"></div>
        </div>
    </header>

    <main class="container mx-auto py-16 px-4 space-y-24 max-w-6xl relative z-10">
        
        <!-- PROFIL & PROGRAM -->
        <section id="tentang" class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h3 class="text-3xl font-black uppercase text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400 mb-6 drop-shadow">Tentang Swift SC</h3>
                <p class="text-slate-300 mb-6 leading-relaxed text-justify text-lg font-light">
                    <?= nl2br(htmlspecialchars($cmsData['about_text'])) ?>
                </p>
                
                <h4 class="text-xl font-bold text-white mb-4 mt-8 flex items-center gap-2">
                    <span class="w-2 h-6 bg-cyan-500 rounded-full"></span> Menerima Siswa-Siswi Baru:
                </h4>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 bg-slate-800/40 p-3 rounded-xl border border-slate-700/50">
                        <div class="bg-cyan-500/20 text-cyan-400 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                        <span class="font-bold text-white text-lg">Grup <span class="text-slate-400 font-normal text-sm ml-2">(Min. usia 4 tahun)</span></span>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-800/40 p-3 rounded-xl border border-slate-700/50">
                        <div class="bg-cyan-500/20 text-cyan-400 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>
                        <span class="font-bold text-white text-lg">Privat <span class="text-slate-400 font-normal text-sm ml-2">(Tidak ada Min. usia)</span></span>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-800/40 p-3 rounded-xl border border-slate-700/50">
                        <div class="bg-cyan-500/20 text-cyan-400 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
                        <span class="font-bold text-white text-lg">Persiapan TNI/Polri</span>
                    </div>
                </div>
            </div>
            
            <div id="program" class="glass-panel p-8 rounded-3xl border border-slate-600 shadow-2xl relative">
                <div class="absolute -top-4 -right-4 bg-cyan-500 text-white font-black px-4 py-1 rounded-full shadow-lg transform rotate-3">TERSERTIFIKASI</div>
                <h4 class="text-2xl font-bold mb-6 text-white text-center">Badan Sertifikasi Resmi</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                    <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700">
                        <div class="h-16 flex items-center justify-center mb-2 px-2 bg-slate-900 rounded-lg">
                            <span class="font-bold text-teal-400 text-xs">Swim Clinic</span>
                        </div>
                    </div>
                    <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700">
                        <div class="h-16 flex items-center justify-center mb-2 px-2 bg-slate-900 rounded-lg">
                            <span class="font-bold text-teal-400 text-xs">ASCA Lv. 1</span>
                        </div>
                    </div>
                    <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700">
                        <div class="h-16 flex items-center justify-center mb-2 px-2 bg-slate-900 rounded-lg">
                            <span class="font-bold text-teal-400 text-xs">ASCA Lv. 2</span>
                        </div>
                    </div>
                    <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700">
                        <div class="h-16 flex items-center justify-center mb-2 px-2 bg-slate-900 rounded-lg">
                            <span class="font-bold text-teal-400 text-xs text-balance">SK Club Swift Sleman</span>
                        </div>
                    </div>
                    <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700 md:col-span-2">
                        <div class="h-16 flex items-center justify-center mb-2 px-2 bg-slate-900 rounded-lg">
                            <span class="font-bold text-cyan-400 text-sm">Lisensi Kategori C Nasional</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- JADWAL DAN BIAYA -->
        <section id="jadwal">
            <h3 class="text-4xl font-black text-center mb-12 uppercase text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Jadwal dan Lokasi Latihan</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 overflow-x-auto rounded-2xl border border-slate-700/50 shadow-xl">
                    <table class="w-full text-left">
                        <thead class="bg-slate-800 text-cyan-400 text-sm uppercase tracking-wider border-b border-slate-700">
                            <tr>
                                <th class="p-5 font-bold">Lokasi Kolam Renang</th>
                                <th class="p-5 font-bold">Jadwal Latihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50 bg-slate-900/60 backdrop-blur-md text-slate-200">
                            <?php
                            $schedules = [];
                            try {
                                $q_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal ORDER BY id ASC");
                                while($row = mysqli_fetch_assoc($q_jadwal)) {
                                    $schedules[] = $row;
                                }
                            } catch (\Exception $e) {}
                            if(count($schedules) > 0) {
                                foreach($schedules as $s) {
                            ?>
                            <tr class="hover:bg-slate-800 transition-colors">
                                <td class="p-5 font-semibold"><?= htmlspecialchars($s['lokasi']) ?></td>
                                <td class="p-5"><?= htmlspecialchars($s['hari']) ?> <span class="block text-slate-400 text-sm">(<?= date('H:i', strtotime($s['jam_mulai'])) ?> - <?= date('H:i', strtotime($s['jam_selesai'])) ?> WIB)</span></td>
                            </tr>
                            <?php } } else { ?>
                            <tr class="hover:bg-slate-800 transition-colors">
                                <td colspan="2" class="p-5 text-center text-slate-400 italic">Jadwal latihan belum tersedia.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- BIAYA -->
                <div class="glass-panel p-8 rounded-2xl flex flex-col justify-between border-t-4 border-cyan-500 shadow-[0_10px_30px_rgba(6,182,212,0.15)] relative">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-3xl font-black mb-6 text-white uppercase tracking-wider">Biaya</h4>
                        
                        <div class="space-y-6">
                            <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-700">
                                <h5 class="text-slate-400 text-sm font-bold uppercase mb-1">Paket Reguler (Semua Kolam)</h5>
                                <div class="flex justify-between items-end border-b border-slate-700 pb-2 mb-2">
                                    <span class="text-white font-medium">Pendaftaran</span>
                                    <span class="font-bold text-cyan-400">Rp. 100.000</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <span class="text-white font-medium">SPP (8 Sesi Latihan)</span>
                                    <span class="font-bold text-cyan-400">Rp. 225.000</span>
                                </div>
                            </div>
                            
                            <div class="bg-cyan-900/20 p-4 rounded-xl border border-cyan-500/30 ring-1 ring-cyan-500/10">
                                <h5 class="text-cyan-400 text-sm font-bold uppercase mb-1">* Khusus Umbang Tirta</h5>
                                <div class="flex justify-between items-end border-b border-cyan-800/30 pb-2 mb-2 text-xs">
                                    <span class="text-slate-300">Pendaftaran</span>
                                    <span class="font-bold text-cyan-300">Rp. 100.000</span>
                                </div>
                                <div class="flex justify-between items-end border-b border-cyan-800/30 pb-2 mb-2 text-xs">
                                    <span class="text-slate-300">Paket 4 Sesi/bulan</span>
                                    <span class="font-bold text-cyan-300">Rp. 150.000</span>
                                </div>
                                <div class="flex justify-between items-end text-xs">
                                    <span class="text-slate-300">Paket 8 Sesi/bulan</span>
                                    <span class="font-bold text-cyan-300">Rp. 200.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 text-center">
                        <a href="pendaftaran.php" class="w-full text-center block badge-cyan hover:bg-cyan-600 px-6 py-4 rounded-xl font-black text-xl transition-all shadow-lg hover:shadow-cyan-500/50 mb-4">DAFTAR SEKARANG</a>
                        <?php
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                        $domainName = $_SERVER['HTTP_HOST'];
                        $dir = dirname($_SERVER['PHP_SELF']);
                        $pendaftaranUrl = $protocol . $domainName . $dir . '/pendaftaran.php';
                        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&color=0f172a&data=" . urlencode($pendaftaranUrl);
                        ?>
                        <div class="flex flex-col items-center justify-center bg-white p-3 rounded-xl border-4 border-cyan-100 shadow-inner w-max mx-auto">
                            <img src="<?= $qrUrl ?>" alt="QR Code Pendaftaran" class="w-24 h-24 rounded">
                            <p class="text-[10px] text-slate-800 font-bold mt-1 uppercase">Scan untuk Daftar</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TOP PERFORMER PELATIH (Dynamic Check) -->
        <section id="pelatih">
            <h3 class="text-4xl font-black text-center mb-16 uppercase text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-blue-400 drop-shadow-sm">Tim Kepelatihan</h3>
            <div class="flex flex-wrap justify-center gap-8 md:gap-12 px-4">
                <?php
                $pelatihList = [];
                try {
                    $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih WHERE is_highlighted=1 LIMIT 3");
                    // jika kurang dari 3 atau tidak ada, fallback ambil aja sembarang
                    if(!$q_pelatih || mysqli_num_rows($q_pelatih) == 0) {
                        $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih LIMIT 3");
                    }
                    while($row = mysqli_fetch_assoc($q_pelatih)) {
                        $pelatihList[] = $row;
                    }
                } catch (\Exception $e) {}
                
                if (count($pelatihList) > 0) :
                    // Variasi rotasi untuk efek organik
                    $rotations = ['-rotate-3', 'rotate-2', '-rotate-1'];
                    foreach($pelatihList as $index => $p) :
                        $rotClass = $rotations[$index % count($rotations)];
                        $foto_pelatih = !empty($p['foto']) && file_exists("admin/uploads/" . $p['foto']) ? "admin/uploads/" . $p['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($p['nama']) . "&background=0f172a&color=fff&size=512";
                ?>
                <!-- Bingkai Polaroid -->
                <div class="bg-slate-50 p-3 pb-16 md:p-4 md:pb-20 shadow-2xl rounded-sm transform transition-all duration-500 hover:-translate-y-4 hover:scale-105 hover:rotate-0 hover:shadow-[0_20px_50px_rgba(6,182,212,0.3)] relative group w-64 <?= $rotClass ?> z-10 hover:z-30">
                    <div class="aspect-[3/4] bg-slate-200 overflow-hidden shadow-inner relative">
                        <img src="<?= $foto_pelatih ?>" class="w-full h-full object-cover filter brightness-95 contrast-125 grayscale-[30%] group-hover:grayscale-0 group-hover:brightness-100 transition-all duration-700" alt="<?= htmlspecialchars($p['nama']) ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    
                    <!-- Area Teks Bawah Polaroid -->
                    <div class="absolute bottom-0 left-0 w-full px-4 pb-4 pt-2 text-center flex flex-col items-center justify-end h-16 md:h-20">
                        <h4 class="text-xl font-black text-slate-800 tracking-tight leading-tight line-clamp-1"><?= htmlspecialchars($p['nama']); ?></h4>
                        <p class="text-xs font-bold text-cyan-700 uppercase tracking-widest mt-0.5 line-clamp-1"><?= htmlspecialchars($p['jabatan']); ?></p>
                        <p class="text-[10px] text-slate-500 mt-1 line-clamp-1 italic"><?= htmlspecialchars($p['sertifikasi']); ?></p>
                    </div>
                    
                    <!-- Pin Hiasan Opsional -->
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-4 h-4 rounded-full bg-slate-300/80 shadow-sm opacity-50 z-20"></div>
                </div>
                <?php 
                    endforeach; 
                else : 
                ?>
                    <p class="text-slate-500 italic text-center w-full">Belum ada data pelatih di sistem.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- BERITA / LATEST NEWS -->
        <section id="berita" class="pt-8 bg-slate-900/30 rounded-3xl p-8 border border-slate-700/30 shadow-2xl">
            <h3 class="text-3xl font-black text-center mb-10 uppercase text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Berita Terkini</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $beritaList = [];
                try {
                    $q_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY id DESC LIMIT 3");
                    while($row = mysqli_fetch_assoc($q_berita)) {
                        $beritaList[] = $row;
                    }
                } catch (\Exception $e) {}
                
                if (count($beritaList) > 0) :
                    foreach($beritaList as $b) :
                        $foto_berita = (!empty($b['gambar']) && file_exists("admin/" . $b['gambar'])) ? "admin/" . $b['gambar'] : "https://images.unsplash.com/photo-1572334057861-6d72dbb688d2?auto=format&fit=crop&w=800&q=80";
                ?>
                <div class="glass-panel overflow-hidden rounded-2xl shadow-lg border border-slate-700/50 hover:-translate-y-2 transition-all duration-300 group">
                    <div class="h-48 bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('<?= $foto_berita ?>');"></div>
                    <div class="p-6 relative bg-slate-900/40">
                        <span class="absolute -top-3 left-6 badge-cyan px-3 py-1 rounded-full text-xs font-bold shadow-[0_4px_10px_rgba(6,182,212,0.5)]"><?= $b['cabang'] ?? 'Umum' ?></span>
                        <h4 class="text-xl font-bold text-white mb-2 leading-tight"> <?= htmlspecialchars($b['judul']) ?> </h4>
                        <p class="text-slate-400 text-sm line-clamp-3 mb-4"><?= strip_tags($b['isi'] ?? '') ?></p>
                        <p class="text-teal-500 font-bold text-xs uppercase tracking-wider">Oleh: Swift Admin</p>
                    </div>
                </div>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <p class="text-slate-500 italic col-span-3 text-center py-8">Belum ada berita yang dipublikasikan.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <footer id="kontak" class="border-t border-slate-800/80 mt-12 bg-[#081223] relative z-10 pt-16 pb-8">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-12">
            <div>
                <img src="assets/logo.png" alt="Swift SC Logo" class="h-16 mb-6" onerror="this.onerror=null; this.outerHTML='<span class=\'text-3xl font-black italic tracking-tighter text-white block mb-6\'>SWIFT<span class=\'text-cyan-500\'>_SC</span></span>';">
                <p class="text-sm text-slate-400">Mencetak atlet renang berprestasi dengan fasilitas dan metode kepelatihan terbaik di kelasnya. Jump in. let's swim!</p>
            </div>
            
            <div>
                <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm border-b border-slate-800 pb-2">Kontak Media Sosial</h4>
                <ul class="space-y-4 text-sm text-slate-400">
                    <li class="flex items-center gap-3">
                        <span class="bg-gradient-to-br from-purple-500 to-pink-500 text-white p-2 rounded-lg"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.88z"/></svg></span>
                        <a href="https://instagram.com/swiftswimmingclub" target="_blank" class="hover:text-cyan-400 font-semibold">swiftswimmingclub</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="bg-black text-white p-2 rounded-lg"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg></span>
                        <a href="https://tiktok.com/@swiftswimmingclub" target="_blank" class="hover:text-cyan-400 font-semibold">swiftswimmingclub</a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm border-b border-slate-800 pb-2">WhatsApp Pendaftaran</h4>
                <ul class="space-y-4 text-sm text-slate-400">
                    <li class="flex items-center gap-3">
                        <span class="bg-green-500 text-white p-2 rounded-lg"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg></span>
                        <a href="https://wa.me/6289668366724" target="_blank" class="hover:text-green-400 font-semibold text-lg">+62 896-6836-6724</a>
                    </li>
                    <li class="mt-4"><p class="text-xs text-slate-500 italic">Silakan klik nomor admin untuk chat langsung dan melakukan pendaftaran.</p></li>
                </ul>
            </div>
        </div>
        <div class="container mx-auto px-4 text-center text-xs mt-12 pt-8 border-t border-slate-800 text-slate-600">
            <p>&copy; <?= date('Y'); ?> Swift Swimming Club. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Dynamic Split-Screen Slider Logic
        const heroBanners = <?= json_encode($banners) ?>;
        const heroLeftBg = document.getElementById('heroLeftBg');
        const heroRightBg = document.getElementById('heroRightBg');

        if(heroBanners && heroBanners.length > 1) {
            let leftIndex = 0;
            let rightIndex = 1;

            // Initial Setup
            heroLeftBg.style.backgroundImage = `url('admin/uploads/${heroBanners[leftIndex].gambar}')`;
            heroRightBg.style.backgroundImage = `url('admin/uploads/${heroBanners[rightIndex].gambar}')`;
            
            // Start right pan
            setTimeout(() => {
                heroRightBg.style.transition = 'transform 5s linear';
                heroRightBg.style.transform = 'scale(1.1) translateX(-5%)';
            }, 50);

            // Change slide every 5 seconds
            setInterval(() => {
                // Right becomes Left
                leftIndex = rightIndex;
                rightIndex = (rightIndex + 1) % heroBanners.length;
                
                // Instantly update Left to match what Right was
                heroLeftBg.style.backgroundImage = `url('admin/uploads/${heroBanners[leftIndex].gambar}')`;
                
                // Instantly reset Right position and update its image
                heroRightBg.style.transition = 'none';
                heroRightBg.style.transform = 'scale(1.1) translateX(5%)';
                heroRightBg.style.backgroundImage = `url('admin/uploads/${heroBanners[rightIndex].gambar}')`;
                
                // Start Right panning again
                setTimeout(() => {
                    heroRightBg.style.transition = 'transform 5s linear';
                    heroRightBg.style.transform = 'scale(1.1) translateX(-5%)';
                }, 50);

            }, 5000); // 5s interval
        } else if (heroRightBg) {
            // Infinite pan for single image
            setTimeout(() => {
                heroRightBg.style.transition = 'transform 15s alternate infinite linear';
                heroRightBg.style.transform = 'scale(1.15) translateX(-5%)';
            }, 100);
        }
    </script>
</body>
</html>