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

// Load cms_banners untuk dynamic hero slider
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
    <title>Swift SC - <?= htmlspecialchars($cmsData['hero_title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-cursive { font-family: 'Caveat', cursive; }
        
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
            /* background-attachment: fixed; dihapus karena menyebabkan lag saat scrolling */
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
        .badge-orange {
            background-color: #f97316;
            color: #ffffff;
            box-shadow: 0 4px 14px 0 rgba(249, 115, 22, 0.39);
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
                <div class="bg-white p-1.5 rounded-full shadow-lg shadow-teal-900/20">
                    <img src="assets/logo.png" alt="Swift SC Logo" class="h-10 w-10 object-contain transition-transform group-hover:scale-105" onerror="this.onerror=null; this.outerHTML='<span class=\'text-2xl font-black italic tracking-tighter text-slate-900 px-2\'>SWIFT<span class=\'text-orange-500\'>_SC</span></span>';">
                </div>
            </a>

            <!-- Navigation Links (Right Side) -->
            <div class="hidden md:flex gap-6 font-semibold text-sm items-center ml-auto">
                <a href="#tentang" class="text-slate-300 hover:text-orange-400 transition-colors">Profil</a>
                <a href="#program" class="text-slate-300 hover:text-orange-400 transition-colors">Program & Sertifikasi</a>
                <a href="#jadwal" class="text-slate-300 hover:text-orange-400 transition-colors">Jadwal & Biaya</a>
                <a href="#pelatih" class="text-slate-300 hover:text-orange-400 transition-colors">Pelatih</a>
                <a href="login.php" class="bg-orange-500/20 border border-orange-500/50 text-orange-400 px-5 py-2 rounded-full font-bold hover:bg-orange-500 hover:text-white transition-all shadow-lg hover:shadow-orange-500/25 ml-4">Login Portal</a>
            </div>

            <!-- Hamburger Button for Mobile -->
            <button class="md:hidden text-slate-300 hover:text-white ml-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            
        </div>
    </nav>

    <!-- Dynamic Hero Slider -->
    <header class="relative py-24 px-4 text-center overflow-hidden" id="heroHeader">
        <?php if (file_exists('admin/uploads/hero_bg.jpg') && count($banners) == 0): ?>
            <div id="heroBgImg" class="absolute inset-0 bg-cover bg-center bg-no-repeat -z-20 transition-all duration-1000 ease-in-out" style="background-image: url('admin/uploads/hero_bg.jpg?v=<?= time() ?>');"></div>
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px] -z-10 mix-blend-multiply"></div>
        <?php else: ?>
            <?php $first_bg = count($banners) > 0 ? "admin/uploads/".$banners[0]['gambar'] : "https://images.unsplash.com/photo-1572334057861-6d72dbb688d2?auto=format&fit=crop&w=1920&q=80"; ?>
            <div id="heroBgImg" class="absolute inset-0 bg-cover bg-center bg-no-repeat -z-20 transition-all duration-1000 ease-in-out" style="background-image: url('<?= htmlspecialchars($first_bg) ?>');"></div>
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px] -z-10 mix-blend-multiply"></div>
        <?php endif; ?>
        
        <h2 id="heroTitle" class="font-cursive text-5xl md:text-7xl mb-2 text-white drop-shadow-lg tracking-wide transform -rotate-2 transition-opacity duration-1000 ease-in-out">
            <?= htmlspecialchars(count($banners) > 0 ? $banners[0]['judul'] : $cmsData['hero_title']) ?>
        </h2>
        <h1 class="text-6xl md:text-8xl font-black mb-8 uppercase tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-teal-400 via-white to-orange-400 drop-shadow-2xl">
            <?= htmlspecialchars($cmsData['hero_subtitle']) ?>
        </h1>
        <p id="heroSubtitle" class="text-lg md:text-xl text-slate-300 max-w-3xl mx-auto font-medium mb-10 drop-shadow transition-opacity duration-1000 ease-in-out">
            <?= htmlspecialchars(count($banners) > 0 ? $banners[0]['subjudul'] : $cmsData['hero_desc']) ?>
        </p>
        <a href="pendaftaran.php" class="badge-orange hover:bg-orange-600 px-10 py-4 rounded-full font-black text-xl transition-all inline-block transform hover:-translate-y-1 hover:scale-105 border border-orange-400">DAFTAR SEKARANG</a>
    </header>

    <main class="container mx-auto py-16 px-4 space-y-24 max-w-6xl relative z-10">
        
        <!-- PROFIL & PROGRAM -->
        <section id="tentang" class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h3 class="text-3xl font-black uppercase text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-400 mb-6 drop-shadow">Tentang Swift SC</h3>
                <p class="text-slate-300 mb-6 leading-relaxed text-justify text-lg font-light">
                    <?= nl2br(htmlspecialchars($cmsData['about_text'])) ?>
                </p>
                
                <h4 class="text-xl font-bold text-white mb-4 mt-8 flex items-center gap-2">
                    <span class="w-2 h-6 bg-orange-500 rounded-full"></span> Menerima Siswa-Siswi Baru:
                </h4>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 bg-slate-800/40 p-3 rounded-xl border border-slate-700/50">
                        <div class="bg-orange-500/20 text-orange-400 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                        <span class="font-bold text-white text-lg">Grup <span class="text-slate-400 font-normal text-sm ml-2">(Min. usia 4 tahun)</span></span>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-800/40 p-3 rounded-xl border border-slate-700/50">
                        <div class="bg-orange-500/20 text-orange-400 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>
                        <span class="font-bold text-white text-lg">Privat <span class="text-slate-400 font-normal text-sm ml-2">(Tidak ada Min. usia)</span></span>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-800/40 p-3 rounded-xl border border-slate-700/50">
                        <div class="bg-orange-500/20 text-orange-400 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
                        <span class="font-bold text-white text-lg">Persiapan TNI/Polri</span>
                    </div>
                </div>
            </div>
            
            <div id="program" class="glass-panel p-8 rounded-3xl border border-slate-600 shadow-2xl relative">
                <div class="absolute -top-4 -right-4 bg-orange-500 text-white font-black px-4 py-1 rounded-full shadow-lg transform rotate-3">TERSERTIFIKASI</div>
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
                            <span class="font-bold text-orange-400 text-sm">Lisensi Kategori C Nasional</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- JADWAL DAN BIAYA -->
        <section id="jadwal">
            <h3 class="text-4xl font-black text-center mb-12 uppercase text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-400">Jadwal dan Lokasi Latihan</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 overflow-x-auto rounded-2xl border border-slate-700/50 shadow-xl">
                    <table class="w-full text-left">
                        <thead class="bg-slate-800 text-orange-400 text-sm uppercase tracking-wider border-b border-slate-700">
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
                <div class="glass-panel p-8 rounded-2xl flex flex-col justify-between border-t-4 border-orange-500 shadow-[0_10px_30px_rgba(249,115,22,0.15)] relative">
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
                                    <span class="font-bold text-orange-400">Rp. 100.000</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <span class="text-white font-medium">SPP (8 Sesi Latihan)</span>
                                    <span class="font-bold text-orange-400">Rp. 225.000</span>
                                </div>
                            </div>
                            
                            <div class="bg-orange-900/20 p-4 rounded-xl border border-orange-500/30 ring-1 ring-orange-500/10">
                                <h5 class="text-orange-400 text-sm font-bold uppercase mb-1">* Khusus Umbang Tirta</h5>
                                <div class="flex justify-between items-end border-b border-orange-800/30 pb-2 mb-2 text-xs">
                                    <span class="text-slate-300">Pendaftaran</span>
                                    <span class="font-bold text-orange-300">Rp. 100.000</span>
                                </div>
                                <div class="flex justify-between items-end border-b border-orange-800/30 pb-2 mb-2 text-xs">
                                    <span class="text-slate-300">Paket 4 Sesi/bulan</span>
                                    <span class="font-bold text-orange-300">Rp. 150.000</span>
                                </div>
                                <div class="flex justify-between items-end text-xs">
                                    <span class="text-slate-300">Paket 8 Sesi/bulan</span>
                                    <span class="font-bold text-orange-300">Rp. 200.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 text-center">
                        <a href="pendaftaran.php" class="w-full text-center block badge-orange hover:bg-orange-600 px-6 py-4 rounded-xl font-black text-xl transition-all shadow-lg hover:shadow-orange-500/50 mb-4">DAFTAR SEKARANG</a>
                        <?php
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                        $domainName = $_SERVER['HTTP_HOST'];
                        $dir = dirname($_SERVER['PHP_SELF']);
                        $pendaftaranUrl = $protocol . $domainName . $dir . '/pendaftaran.php';
                        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&color=0f172a&data=" . urlencode($pendaftaranUrl);
                        ?>
                        <div class="flex flex-col items-center justify-center bg-white p-3 rounded-xl border-4 border-orange-100 shadow-inner w-max mx-auto">
                            <img src="<?= $qrUrl ?>" alt="QR Code Pendaftaran" class="w-24 h-24 rounded">
                            <p class="text-[10px] text-slate-800 font-bold mt-1 uppercase">Scan untuk Daftar</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TOP PERFORMER PELATIH (Dynamic Check) -->
        <section id="pelatih">
            <h3 class="text-3xl font-black text-center mb-10 uppercase text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-blue-400">Tim Kepelatihan</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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
                    foreach($pelatihList as $p) :
                        $foto_pelatih = !empty($p['foto']) && file_exists("admin/" . $p['foto']) ? "admin/" . $p['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($p['nama']) . "&background=0f172a&color=fff&size=256";
                ?>
                <div class="glass-panel rounded-2xl overflow-hidden shadow-lg text-center pb-6 transition-all duration-300 hover:shadow-[0_0_20px_rgba(13,148,136,0.3)] hover:-translate-y-2 border border-slate-700/50 group">
                    <div class="h-48 bg-slate-800 bg-cover bg-center" style="background-image: url('<?= $foto_pelatih ?>');"></div>
                    <div class="relative z-20">
                        <h4 class="text-xl font-bold mt-4 text-white"><?= $p['nama']; ?></h4>
                        <p class="text-orange-400 font-semibold text-sm"><?= $p['jabatan']; ?></p>
                        <p class="text-xs text-slate-400 mt-2 px-4"><?= $p['sertifikasi']; ?></p>
                    </div>
                </div>
                <?php 
                    endforeach; 
                else : 
                ?>
                    <p class="text-slate-500 italic col-span-3 text-center">Belum ada data pelatih di sistem.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- BERITA / LATEST NEWS -->
        <section id="berita" class="pt-8 bg-slate-900/30 rounded-3xl p-8 border border-slate-700/30 shadow-2xl">
            <h3 class="text-3xl font-black text-center mb-10 uppercase text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-400">Berita Terkini</h3>
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
                        <span class="absolute -top-3 left-6 badge-orange px-3 py-1 rounded-full text-xs font-bold shadow-[0_4px_10px_rgba(249,115,22,0.5)]"><?= $b['cabang'] ?? 'Umum' ?></span>
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
                <img src="assets/logo.png" alt="Swift SC Logo" class="h-16 mb-6" onerror="this.onerror=null; this.outerHTML='<span class=\'text-3xl font-black italic tracking-tighter text-white block mb-6\'>SWIFT<span class=\'text-orange-500\'>_SC</span></span>';">
                <p class="text-sm text-slate-400">Mencetak atlet renang berprestasi dengan fasilitas dan metode kepelatihan terbaik di kelasnya. Jump in. let's swim!</p>
            </div>
            
            <div>
                <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm border-b border-slate-800 pb-2">Kontak Media Sosial</h4>
                <ul class="space-y-4 text-sm text-slate-400">
                    <li class="flex items-center gap-3">
                        <span class="bg-gradient-to-br from-purple-500 to-pink-500 text-white p-2 rounded-lg"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.88z"/></svg></span>
                        <a href="https://instagram.com/swiftswimmingclub" target="_blank" class="hover:text-orange-400 font-semibold">swiftswimmingclub</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="bg-black text-white p-2 rounded-lg"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg></span>
                        <a href="https://tiktok.com/@swiftswimmingclub" target="_blank" class="hover:text-orange-400 font-semibold">swiftswimmingclub</a>
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
        // Dynamic Hero Slider Logic
        const heroBanners = <?= json_encode($banners) ?>;
        if(heroBanners && heroBanners.length > 1) {
            let currentHeroIndex = 0;
            const heroBg = document.getElementById('heroBgImg');
            const heroTitle = document.getElementById('heroTitle');
            const heroSub = document.getElementById('heroSubtitle');

            setInterval(() => {
                // Fade out
                heroBg.style.opacity = '0';
                heroTitle.style.opacity = '0';
                heroSub.style.opacity = '0';

                setTimeout(() => {
                    currentHeroIndex = (currentHeroIndex + 1) % heroBanners.length;
                    const banner = heroBanners[currentHeroIndex];
                    
                    heroBg.style.backgroundImage = `url('admin/uploads/${banner.gambar}')`;
                    heroTitle.textContent = banner.judul;
                    heroSub.textContent = banner.subjudul;
                    
                    // Fade in
                    heroBg.style.opacity = '0.3'; // Keeping it dark/blended as per original design
                    heroTitle.style.opacity = '1';
                    heroSub.style.opacity = '1';
                }, 1000); // 1s fade-out duration
            }, 6000); // Change slide every 6s
        }
    </script>
</body>
</html>