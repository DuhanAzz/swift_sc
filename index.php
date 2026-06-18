<?php 
include 'includes/koneksi.php'; 

// Ambil CMS Content
$cmsData = [
    'hero_title' => "Jump in. let's swim!",
    'hero_subtitle' => "SWIFT SC",
    'hero_desc' => "Klub renang resmi dan tersertifikasi dengan fasilitas pelatih berlisensi nasional & internasional.",
    'about_text' => "Swift Swimming Club adalah klub renang yang resmi dan telah tersertifikasi. Dengan fasilitas pelatih berlisensi nasional maupun internasional dan peralatan renang yang memadai.",
    'visi' => '',
    'misi' => '',
];

try {
    $q_cms = mysqli_query($koneksi, "SELECT judul, konten FROM cms_landing WHERE tipe='Banner' AND status='Aktif' LIMIT 1");
    if($q_cms && $row_cms = mysqli_fetch_assoc($q_cms)) {
        $cmsData['hero_title'] = $row_cms['judul'];
        $cmsData['hero_desc'] = $row_cms['konten'];
    }
} catch (\Exception $e) {}

// Load tentang_club (visi, misi, deskripsi)
try {
    $q_tentang = mysqli_query($koneksi, "SELECT * FROM tentang_club LIMIT 1");
    if($q_tentang && $row_tentang = mysqli_fetch_assoc($q_tentang)) {
        if(!empty($row_tentang['deskripsi'])) $cmsData['about_text'] = $row_tentang['deskripsi'];
        if(!empty($row_tentang['visi'])) $cmsData['visi'] = $row_tentang['visi'];
        if(!empty($row_tentang['misi'])) $cmsData['misi'] = $row_tentang['misi'];
    }
} catch (\Exception $e) {}

// Load slider images
$sliders = [];
try {
    $q_slider = mysqli_query($koneksi, "SELECT * FROM slider WHERE status='Aktif' ORDER BY urutan ASC");
    if($q_slider) { while($s = mysqli_fetch_assoc($q_slider)) { $sliders[] = $s; } }
} catch (\Exception $e) {}

// Load cms_banners
$banners = [];
try {
    $q_banners = mysqli_query($koneksi, "SELECT * FROM cms_banners WHERE status='Aktif' ORDER BY urutan ASC");
    if($q_banners) { while($b = mysqli_fetch_assoc($q_banners)) { $banners[] = $b; } }
} catch (\Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift SC — <?= htmlspecialchars($cmsData['hero_title']) ?></title>
    <meta name="description" content="Swift Swimming Club - Klub renang resmi dan tersertifikasi dengan pelatih berlisensi nasional & internasional.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Sora:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        xenon: {
                            50: '#f0f1ff', 100: '#e0e3ff', 200: '#c7cbff', 300: '#a3a8ff',
                            400: '#5468FF', 500: '#3A4DC7', 600: '#003DFF', 700: '#002eb3',
                            800: '#1a1c3a', 900: '#0d0f2b', 950: '#07081a'
                        },
                    },
                    fontFamily: {
                        sora: ['Sora', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .blue-gradient { background: linear-gradient(135deg, #003DFF 0%, #5468FF 100%); }
        .hero-bg { 
            background: #0d0f2b;
            background-image: 
                radial-gradient(ellipse at 30% 50%, rgba(0,61,255,0.15) 0%, transparent 60%),
                radial-gradient(ellipse at 70% 30%, rgba(84,104,255,0.1) 0%, transparent 50%);
        }
        .card-hover-gradient:hover .gradient-line {
            width: 100%;
        }
        .gradient-line {
            transition: width 0.4s ease;
        }
        .stat-glow { 
            text-shadow: 0 0 40px rgba(84,104,255,0.3);
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.6s ease-out forwards; }
        .fade-up-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .fade-up-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .fade-up-delay-3 { animation-delay: 0.3s; opacity: 0; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased font-inter">
    
    <!-- ===== NAVBAR (Algolia Dark Style) ===== -->
    <nav class="bg-xenon-900 sticky top-0 z-50 border-b border-white/5">
        <div class="max-w-[1440px] mx-auto flex justify-between items-center px-4 lg:px-10 h-16">
            <a href="index.php" class="flex items-center gap-3 group">
                <img src="assets/logo.png" alt="Swift SC Logo" class="h-9 w-9 object-contain rounded-lg" onerror="this.onerror=null; this.outerHTML='<div class=\'h-9 w-9 rounded-lg bg-xenon-400 flex items-center justify-center\'><svg class=\'w-5 h-5 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2.5\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg></div>';">
                <span class="font-sora font-bold text-white text-base tracking-tight">Swift<span class="text-xenon-400">SC</span></span>
            </a>

            <div class="hidden md:flex items-center gap-1 text-sm">
                <a href="#tentang" class="text-gray-400 hover:text-white transition-colors px-3 py-2 rounded-lg hover:bg-white/5 font-medium">Profil</a>
                <a href="#program" class="text-gray-400 hover:text-white transition-colors px-3 py-2 rounded-lg hover:bg-white/5 font-medium">Program</a>
                <a href="#jadwal" class="text-gray-400 hover:text-white transition-colors px-3 py-2 rounded-lg hover:bg-white/5 font-medium">Jadwal</a>
                <a href="#pelatih" class="text-gray-400 hover:text-white transition-colors px-3 py-2 rounded-lg hover:bg-white/5 font-medium">Pelatih</a>
                <a href="#berita" class="text-gray-400 hover:text-white transition-colors px-3 py-2 rounded-lg hover:bg-white/5 font-medium">Berita</a>
                <div class="w-px h-5 bg-white/10 mx-2"></div>
                <a href="login.php" class="text-gray-300 hover:text-white transition-colors px-3 py-2 rounded-lg hover:bg-white/5 font-medium">Login</a>
                <a href="pendaftaran.php" class="blue-gradient text-white px-5 py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-opacity ml-1">Daftar Sekarang</a>
            </div>

            <button id="mobileMenuBtn" class="md:hidden p-2 text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
        <div id="mobileMenu" class="hidden md:hidden bg-xenon-900 border-t border-white/5 px-4 pb-4 pt-2 space-y-1">
            <a href="#tentang" class="block py-2.5 text-sm text-gray-400 hover:text-white font-medium">Profil</a>
            <a href="#program" class="block py-2.5 text-sm text-gray-400 hover:text-white font-medium">Program</a>
            <a href="#jadwal" class="block py-2.5 text-sm text-gray-400 hover:text-white font-medium">Jadwal & Biaya</a>
            <a href="#pelatih" class="block py-2.5 text-sm text-gray-400 hover:text-white font-medium">Pelatih</a>
            <a href="#berita" class="block py-2.5 text-sm text-gray-400 hover:text-white font-medium">Berita</a>
            <div class="border-t border-white/5 pt-3 mt-2 flex flex-col gap-2">
                <a href="login.php" class="text-sm text-gray-300 font-medium py-2">Login Portal</a>
                <a href="pendaftaran.php" class="blue-gradient text-white px-5 py-2.5 rounded-lg font-semibold text-sm text-center">Daftar Sekarang</a>
            </div>
        </div>
    </nav>

    <!-- ===== HERO SECTION (Algolia Dark Hero) ===== -->
    <header class="hero-bg relative overflow-hidden">
        <?php if (file_exists('admin/uploads/hero_bg.jpg')): ?>
            <div class="absolute inset-0 bg-cover bg-center opacity-15" style="background-image: url('admin/uploads/hero_bg.jpg?v=<?= time() ?>');"></div>
        <?php endif; ?>
        
        <div class="relative max-w-[1440px] mx-auto flex flex-col lg:flex-row items-center">
            <!-- Left: Text Content -->
            <div class="w-full lg:w-1/2 px-6 lg:pl-20 py-16 lg:py-28 text-center lg:text-left">
                <h1 class="font-sora font-bold text-white mb-6 text-[40px] sm:text-[56px] lg:text-[70px] leading-[105%] tracking-[-3px] fade-up">
                    <?= htmlspecialchars($cmsData['hero_title']) ?>
                </h1>
                <p class="font-sora text-base sm:text-lg lg:text-[19px] font-normal leading-relaxed text-gray-300 mb-0 max-w-[430px] mx-auto lg:mx-0 fade-up fade-up-delay-1">
                    <?= htmlspecialchars($cmsData['hero_desc']) ?>
                </p>
                
                <div class="flex justify-center lg:justify-start gap-3 mt-9 fade-up fade-up-delay-2">
                    <a href="pendaftaran.php" class="blue-gradient text-white px-6 lg:px-8 py-3.5 rounded-lg font-semibold text-sm hover:opacity-90 transition-opacity inline-flex items-center gap-2">
                        Daftar Sekarang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="#tentang" class="border border-white/20 text-white px-6 lg:px-8 py-3.5 rounded-lg font-medium text-sm hover:bg-white/5 transition-colors">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            
            <!-- Right: Stats / Key Numbers -->
            <div class="w-full lg:w-1/2 px-6 lg:px-16 pb-16 lg:py-28">
                <div class="grid grid-cols-2 gap-6 max-w-md mx-auto lg:mx-0 lg:ml-auto">
                    <div class="text-center lg:text-left">
                        <p class="font-sora text-4xl lg:text-5xl font-bold text-white stat-glow">4+</p>
                        <p class="text-gray-400 text-sm mt-1 font-medium">Cabang Latihan</p>
                    </div>
                    <div class="text-center lg:text-left">
                        <p class="font-sora text-4xl lg:text-5xl font-bold text-white stat-glow">50+</p>
                        <p class="text-gray-400 text-sm mt-1 font-medium">Atlet Aktif</p>
                    </div>
                    <div class="text-center lg:text-left">
                        <p class="font-sora text-4xl lg:text-5xl font-bold text-xenon-400 stat-glow">C</p>
                        <p class="text-gray-400 text-sm mt-1 font-medium">Lisensi Nasional</p>
                    </div>
                    <div class="text-center lg:text-left">
                        <p class="font-sora text-4xl lg:text-5xl font-bold text-white stat-glow">ASCA</p>
                        <p class="text-gray-400 text-sm mt-1 font-medium">Lv. 1 & 2 Certified</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== TENTANG SECTION ===== -->
    <section id="tentang" class="bg-white py-20 lg:py-28">
        <div class="max-w-6xl mx-auto px-4 lg:px-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <span class="inline-block text-xenon-400 text-xs font-semibold uppercase tracking-widest mb-4 font-sora">Tentang Kami</span>
                    <h2 class="font-sora font-bold text-gray-900 text-3xl lg:text-[2.625rem] lg:leading-[3.4rem] tracking-[-0.01em] mb-6"><?= htmlspecialchars($cmsData['hero_subtitle']) ?></h2>
                    <p class="text-gray-600 leading-relaxed text-base lg:text-lg"><?= nl2br(htmlspecialchars($cmsData['about_text'])) ?></p>
                    
                    <?php if(!empty($cmsData['visi']) || !empty($cmsData['misi'])): ?>
                    <div class="mt-6 space-y-4">
                        <?php if(!empty($cmsData['visi'])): ?>
                        <div class="bg-xenon-50/50 rounded-lg p-4 border border-xenon-100">
                            <p class="text-[10px] font-semibold text-xenon-400 uppercase tracking-widest mb-1 font-sora">Visi</p>
                            <p class="text-sm text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($cmsData['visi'])) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($cmsData['misi'])): ?>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-1 font-sora">Misi</p>
                            <p class="text-sm text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($cmsData['misi'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                
                <div class="space-y-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4 font-sora">Menerima Siswa-Siswi Baru</p>
                    
                    <div class="group flex items-center gap-4 p-5 bg-gray-50 rounded-xl border border-gray-100 hover:border-xenon-200 hover:bg-xenon-50/30 transition-all cursor-default relative overflow-hidden card-hover-gradient">
                        <div class="w-10 h-10 rounded-lg blue-gradient flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-sora font-semibold text-gray-900">Grup</p>
                            <p class="text-sm text-gray-500">Min. usia 4 tahun</p>
                        </div>
                        <div class="gradient-line absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-600 to-purple-600"></div>
                    </div>

                    <div class="group flex items-center gap-4 p-5 bg-gray-50 rounded-xl border border-gray-100 hover:border-xenon-200 hover:bg-xenon-50/30 transition-all cursor-default relative overflow-hidden card-hover-gradient">
                        <div class="w-10 h-10 rounded-lg blue-gradient flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="font-sora font-semibold text-gray-900">Privat</p>
                            <p class="text-sm text-gray-500">Tidak ada batas usia</p>
                        </div>
                        <div class="gradient-line absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-600 to-purple-600"></div>
                    </div>

                    <div class="group flex items-center gap-4 p-5 bg-gray-50 rounded-xl border border-gray-100 hover:border-xenon-200 hover:bg-xenon-50/30 transition-all cursor-default relative overflow-hidden card-hover-gradient">
                        <div class="w-10 h-10 rounded-lg blue-gradient flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <p class="font-sora font-semibold text-gray-900">Persiapan TNI/Polri</p>
                            <p class="text-sm text-gray-500">Program khusus seleksi</p>
                        </div>
                        <div class="gradient-line absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-600 to-purple-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SERTIFIKASI SECTION (Dark) ===== -->
    <section id="program" class="bg-xenon-900 py-20 lg:py-24">
        <div class="max-w-6xl mx-auto px-4 lg:px-10 text-center">
            <span class="inline-block text-xenon-400 text-xs font-semibold uppercase tracking-widest mb-4 font-sora">Tersertifikasi</span>
            <h2 class="font-sora font-bold text-white text-3xl lg:text-[2.625rem] lg:leading-[3.4rem] tracking-[-0.01em] mb-12">Badan Sertifikasi Resmi</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 max-w-4xl mx-auto">
                <?php 
                $certs = ['Swim Clinic', 'ASCA Level 1', 'ASCA Level 2', 'SK Club Swift Sleman', 'Lisensi Kat. C Nasional'];
                foreach($certs as $i => $cert): 
                    $isLast = ($i === count($certs) - 1);
                ?>
                <div class="<?= $isLast ? 'col-span-2 md:col-span-1' : '' ?> bg-white/5 border border-white/10 rounded-xl p-5 hover:bg-white/10 hover:border-xenon-400/30 transition-all">
                    <p class="font-sora text-sm font-semibold text-white"><?= $cert ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ===== JADWAL & BIAYA ===== -->
    <section id="jadwal" class="bg-white py-20 lg:py-28">
        <div class="max-w-6xl mx-auto px-4 lg:px-10">
            <div class="text-center mb-12">
                <span class="inline-block text-xenon-400 text-xs font-semibold uppercase tracking-widest mb-4 font-sora">Jadwal & Biaya</span>
                <h2 class="font-sora font-bold text-gray-900 text-3xl lg:text-[2.625rem] lg:leading-[3.4rem] tracking-[-0.01em]">Lokasi dan Jadwal Latihan</h2>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Table -->
                <div class="lg:col-span-2 rounded-xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] text-gray-500 uppercase tracking-wider font-sora">
                                <th class="px-6 py-4 font-semibold">Lokasi Kolam</th>
                                <th class="px-6 py-4 font-semibold">Jadwal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $schedules = [];
                            try {
                                $q_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal ORDER BY id ASC");
                                while($row = mysqli_fetch_assoc($q_jadwal)) { $schedules[] = $row; }
                            } catch (\Exception $e) {}
                            if(count($schedules) > 0) {
                                foreach($schedules as $s) {
                            ?>
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($s['lokasi']) ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($s['hari']) ?> <span class="text-gray-400 text-xs">(<?= date('H:i', strtotime($s['jam_mulai'])) ?>–<?= date('H:i', strtotime($s['jam_selesai'])) ?> WIB)</span></td>
                            </tr>
                            <?php } } else { ?>
                            <tr><td colspan="2" class="px-6 py-10 text-center text-gray-400">Jadwal belum tersedia.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Biaya Card -->
                <div class="rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="font-sora font-bold text-gray-900 text-lg mb-5">Biaya</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-3 font-sora">Paket Reguler</p>
                        <div class="flex justify-between border-b border-gray-200 pb-2.5 mb-2.5">
                            <span class="text-sm text-gray-600">Pendaftaran</span>
                            <span class="text-sm font-bold text-xenon-400">Rp 100.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">SPP (8 Sesi)</span>
                            <span class="text-sm font-bold text-xenon-400">Rp 225.000</span>
                        </div>
                    </div>
                    
                    <div class="bg-xenon-50 rounded-lg p-4 border border-xenon-100 mb-6">
                        <p class="text-[11px] font-semibold text-xenon-400 uppercase tracking-wider mb-3 font-sora">Khusus Umbang Tirta</p>
                        <div class="flex justify-between border-b border-xenon-100 pb-2 mb-2">
                            <span class="text-xs text-gray-600">Pendaftaran</span>
                            <span class="text-xs font-bold text-xenon-400">Rp 100.000</span>
                        </div>
                        <div class="flex justify-between border-b border-xenon-100 pb-2 mb-2">
                            <span class="text-xs text-gray-600">4 Sesi/bulan</span>
                            <span class="text-xs font-bold text-xenon-400">Rp 150.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">8 Sesi/bulan</span>
                            <span class="text-xs font-bold text-xenon-400">Rp 200.000</span>
                        </div>
                    </div>
                    
                    <a href="pendaftaran.php" class="blue-gradient text-white px-6 py-3 rounded-lg font-semibold text-sm text-center hover:opacity-90 transition-opacity mt-auto">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== PELATIH SECTION (Dark) ===== -->
    <section id="pelatih" class="bg-xenon-900 py-20 lg:py-24">
        <div class="max-w-6xl mx-auto px-4 lg:px-10">
            <div class="text-center mb-12">
                <span class="inline-block text-xenon-400 text-xs font-semibold uppercase tracking-widest mb-4 font-sora">Tim Kami</span>
                <h2 class="font-sora font-bold text-white text-3xl lg:text-[2.625rem] lg:leading-[3.4rem] tracking-[-0.01em]">Tim Kepelatihan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $pelatihList = [];
                try {
                    $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih WHERE is_highlighted=1 LIMIT 3");
                    if(!$q_pelatih || mysqli_num_rows($q_pelatih) == 0) {
                        $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih LIMIT 3");
                    }
                    while($row = mysqli_fetch_assoc($q_pelatih)) { $pelatihList[] = $row; }
                } catch (\Exception $e) {}
                
                if (count($pelatihList) > 0) :
                    foreach($pelatihList as $p) :
                        $foto = !empty($p['foto']) && file_exists("admin/" . $p['foto']) ? "admin/" . $p['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($p['nama']) . "&background=003DFF&color=fff&size=256";
                ?>
                <div class="group bg-white/5 border border-white/10 rounded-xl overflow-hidden hover:border-xenon-400/40 hover:bg-white/10 transition-all relative card-hover-gradient">
                    <div class="h-52 bg-xenon-800 bg-cover bg-center" style="background-image: url('<?= $foto ?>');"></div>
                    <div class="p-5">
                        <h4 class="font-sora font-semibold text-white text-base"><?= htmlspecialchars($p['nama']); ?></h4>
                        <p class="text-xenon-400 text-sm font-medium"><?= htmlspecialchars($p['jabatan']); ?></p>
                        <p class="text-gray-500 text-xs mt-1"><?= htmlspecialchars($p['sertifikasi']); ?></p>
                    </div>
                    <div class="gradient-line absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-600 to-purple-600"></div>
                </div>
                <?php endforeach; else : ?>
                    <p class="text-gray-500 col-span-3 text-center py-10">Belum ada data pelatih di sistem.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== BERITA (Light) ===== -->
    <section id="berita" class="bg-gray-50 py-20 lg:py-28">
        <div class="max-w-6xl mx-auto px-4 lg:px-10">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <span class="inline-block text-xenon-400 text-xs font-semibold uppercase tracking-widest mb-4 font-sora">Informasi</span>
                    <h2 class="font-sora font-bold text-gray-900 text-3xl lg:text-[2.625rem] lg:leading-[3.4rem] tracking-[-0.01em]">Berita Terkini</h2>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $beritaList = [];
                try {
                    $q_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY id DESC LIMIT 3");
                    while($row = mysqli_fetch_assoc($q_berita)) { $beritaList[] = $row; }
                } catch (\Exception $e) {}
                
                if (count($beritaList) > 0) :
                    foreach($beritaList as $b) :
                        $foto_berita = (!empty($b['gambar']) && file_exists("admin/" . $b['gambar'])) ? "admin/" . $b['gambar'] : "https://images.unsplash.com/photo-1572334057861-6d72dbb688d2?auto=format&fit=crop&w=800&q=80";
                ?>
                <div class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-gray-300 transition-all relative card-hover-gradient">
                    <div class="h-48 bg-gray-100 bg-cover bg-center" style="background-image: url('<?= $foto_berita ?>');"></div>
                    <div class="p-5">
                        <span class="inline-block bg-xenon-50 text-xenon-400 text-[10px] font-semibold px-2.5 py-1 rounded font-sora uppercase tracking-wider mb-2"><?= htmlspecialchars($b['cabang'] ?? 'Umum') ?></span>
                        <h4 class="font-sora font-semibold text-gray-900 text-sm leading-snug mb-2"><?= htmlspecialchars($b['judul']) ?></h4>
                        <p class="text-gray-500 text-xs line-clamp-2 leading-relaxed"><?= strip_tags($b['isi'] ?? '') ?></p>
                    </div>
                    <div class="gradient-line absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-600 to-purple-600"></div>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-gray-400 col-span-3 text-center py-10">Belum ada berita.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== CTA Section ===== -->
    <section class="hero-bg py-20 lg:py-24">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="font-sora font-bold text-white text-3xl lg:text-5xl tracking-[-2px] mb-6">Siap untuk berenang?</h2>
            <p class="text-gray-400 text-base lg:text-lg mb-8 max-w-xl mx-auto">Bergabunglah dengan Swift Swimming Club dan raih prestasi terbaikmu bersama pelatih bersertifikat nasional & internasional.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="pendaftaran.php" class="blue-gradient text-white px-8 py-3.5 rounded-lg font-semibold text-sm hover:opacity-90 transition-opacity">Daftar Sekarang</a>
                <a href="https://wa.me/6289668366724" target="_blank" class="border border-white/20 text-white px-8 py-3.5 rounded-lg font-medium text-sm hover:bg-white/5 transition-colors inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER (Dark) ===== -->
    <footer class="bg-xenon-950 border-t border-white/5">
        <div class="max-w-[1440px] mx-auto px-4 lg:px-10 py-12 grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <img src="assets/logo.png" alt="Swift SC" class="h-8 w-8 object-contain rounded" onerror="this.style.display='none'">
                    <span class="font-sora font-bold text-white text-sm">Swift<span class="text-xenon-400">SC</span></span>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">Mencetak atlet renang berprestasi dengan fasilitas dan metode kepelatihan terbaik.</p>
            </div>
            
            <div>
                <h4 class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 mb-4 font-sora">Menu</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#tentang" class="text-gray-400 hover:text-white transition-colors">Profil</a></li>
                    <li><a href="#program" class="text-gray-400 hover:text-white transition-colors">Program</a></li>
                    <li><a href="#jadwal" class="text-gray-400 hover:text-white transition-colors">Jadwal</a></li>
                    <li><a href="#pelatih" class="text-gray-400 hover:text-white transition-colors">Pelatih</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 mb-4 font-sora">Sosial Media</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="https://instagram.com/swiftswimmingclub" target="_blank" class="text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.88z"/></svg>
                        Instagram
                    </a></li>
                    <li><a href="https://tiktok.com/@swiftswimmingclub" target="_blank" class="text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg>
                        TikTok
                    </a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 mb-4 font-sora">Pendaftaran</h4>
                <a href="https://wa.me/6289668366724" target="_blank" class="inline-flex items-center gap-2 text-green-400 hover:text-green-300 font-semibold text-sm transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    +62 896-6836-6724
                </a>
                <p class="text-[11px] text-gray-600 mt-2">Klik untuk chat langsung</p>
            </div>
        </div>
        <div class="border-t border-white/5 py-5">
            <p class="text-center text-[11px] text-gray-600">&copy; <?= date('Y'); ?> Swift Swimming Club. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => mobileMenu.classList.add('hidden'));
        });
    </script>
</body>
</html>