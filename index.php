<?php
include 'includes/koneksi.php'; 

// Fetch CMS Data
$cmsData = [
    'hero_title' => "Berlatih Layaknya Sang Juara",
    'hero_desc' => "Klub renang resmi dan tersertifikasi dengan fasilitas pelatih berlisensi nasional & internasional."
];
try {
    $q_cms = mysqli_query($koneksi, "SELECT judul, konten FROM cms_landing WHERE tipe='Banner' AND status='Aktif' LIMIT 1");
    if($q_cms && $row_cms = mysqli_fetch_assoc($q_cms)) {
        if(!empty($row_cms['judul'])) $cmsData['hero_title'] = $row_cms['judul'];
        if(!empty($row_cms['konten'])) $cmsData['hero_desc'] = $row_cms['konten'];
    }
} catch (\Exception $e) {}

// Fetch Slider
$sliders = [];
try {
    $q_sliders = mysqli_query($koneksi, "SELECT * FROM slider WHERE status='Aktif' ORDER BY urutan ASC");
    while($row = mysqli_fetch_assoc($q_sliders)) {
        $sliders[] = $row;
    }
} catch (\Exception $e) {}
if(empty($sliders)) {
    $sliders[] = ['gambar' => 'default.jpg', 'caption' => '']; // Fallback
}

// Fetch Tentang Club
$tentangData = ['deskripsi' => 'Swift Swimming Club adalah klub renang resmi.', 'visi' => 'Menjadi klub renang terbaik.', 'misi' => '1. Berlatih keras\n2. Juara'];
try {
    $q_tentang = mysqli_query($koneksi, "SELECT * FROM tentang_club LIMIT 1");
    if($q_tentang && $row = mysqli_fetch_assoc($q_tentang)) {
        if(!empty($row['deskripsi'])) $tentangData['deskripsi'] = $row['deskripsi'];
        if(!empty($row['visi'])) $tentangData['visi'] = $row['visi'];
        if(!empty($row['misi'])) $tentangData['misi'] = $row['misi'];
        if(!empty($row['feature_1'])) $tentangData['feature_1'] = $row['feature_1'];
        if(!empty($row['feature_2'])) $tentangData['feature_2'] = $row['feature_2'];
        if(!empty($row['feature_3'])) $tentangData['feature_3'] = $row['feature_3'];
    }
} catch (\Exception $e) {}

// Fetch Berita
$beritaData = [];
try {
    $q_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY id DESC LIMIT 12");
    while($row = mysqli_fetch_assoc($q_berita)) {
        $beritaData[] = $row;
    }
} catch (\Exception $e) {}

// Fetch Paket Biaya
$paketData = [];
try {
    $q_paket = mysqli_query($koneksi, "SELECT * FROM paket_biaya ORDER BY urutan ASC LIMIT 3");
    while($row = mysqli_fetch_assoc($q_paket)) {
        $paketData[] = $row;
    }
} catch (\Exception $e) {}

// Fetch Jadwal
$jadwalData = [];
try {
    $q_jadwal = mysqli_query($koneksi, "SELECT j.*, c.nama_cabang, c.lokasi as alamat_cabang FROM jadwal j LEFT JOIN cabang c ON j.cabang_id = c.id ORDER BY j.cabang_id, FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')");
    while($row = mysqli_fetch_assoc($q_jadwal)) {
        $jadwalData[$row['nama_cabang'] ?? 'Umum'][] = $row;
    }
} catch (\Exception $e) {}

// Fetch Pelatih
$pelatihData = [];
try {
    $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih ORDER BY is_highlighted DESC, id ASC LIMIT 8");
    while($row = mysqli_fetch_assoc($q_pelatih)) {
        $pelatihData[] = $row;
    }
} catch (\Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift SC - <?= htmlspecialchars($cmsData['hero_title']) ?></title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        indigo: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81', 950: '#1e1b4b' },
                        cyan: { 400: '#22d3ee', 500: '#06b6d4' }
                    }
                }
            }
        }
    </script>
    <style>
        .mobile-menu { display: none; }
        .mobile-menu.active { display: block; }
        .hero-slide { transition: opacity 1s ease-in-out; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-[#111928]  text-white  antialiased selection:bg-[#005A70]/30 selection:text-indigo-900 :text-[#003847] relative overflow-x-hidden">

    <!-- HEADER -->
    <header class="sticky top-0 z-50 bg-[#111928]/80 /80 backdrop-blur-xl border-b border-[#1F2937]  shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <a href="index.php" class="flex items-center">
                    <div class="w-16 h-16 md:w-20 md:h-20 flex items-center justify-center">
                        <img src="assets/logo.png" alt="Swift SC Logo" class="w-full h-full object-contain" onerror="this.onerror=null; this.outerHTML='<span class=\'font-bold text-xl tracking-tight italic text-black\'>SWIFT</span>';">
                    </div>
                </a>
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="#beranda" class="text-sm font-semibold text-gray-200  hover:text-[#008AAB] :text-[#00A7D1] transition-colors">Beranda</a>
                    <a href="#berita" class="text-sm font-semibold text-gray-200  hover:text-[#008AAB] :text-[#00A7D1] transition-colors">Berita</a>
                    <a href="#tentang" class="text-sm font-semibold text-gray-200  hover:text-[#008AAB] :text-[#00A7D1] transition-colors">Tentang</a>
                    <a href="#lokasi" class="text-sm font-semibold text-gray-200  hover:text-[#008AAB] :text-[#00A7D1] transition-colors">Lokasi & Jadwal</a>
                    <a href="#biaya" class="text-sm font-semibold text-gray-200  hover:text-[#008AAB] :text-[#00A7D1] transition-colors">Biaya</a>
                    <a href="#pelatih" class="text-sm font-semibold text-gray-200  hover:text-[#008AAB] :text-[#00A7D1] transition-colors">Pelatih</a>
                </nav>
                <div class="flex items-center gap-2">
                    <a href="pendaftaran.php" class="hidden sm:flex h-10 px-6 bg-white hover:bg-gray-100 text-[#0B1120] text-sm font-bold rounded-xl transition-colors items-center shadow-sm">Daftar Sekarang</a>
                    <a href="login.php" class="hidden sm:flex h-10 px-6 bg-[#1F2937] hover:bg-[#374151] text-white text-sm font-bold rounded-xl transition-colors items-center shadow-sm">Login Admin</a>
                    <button onclick="document.getElementById('mobileMenu').classList.toggle('active');" class="lg:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5 rounded-xl hover:bg-[#1F2937] transition-colors">
                        <span class="w-5 h-0.5 bg-white rounded-full"></span>
                        <span class="w-5 h-0.5 bg-white rounded-full"></span>
                        <span class="w-5 h-0.5 bg-white rounded-full"></span>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobileMenu" class="mobile-menu lg:hidden bg-[#111928]  border-t border-[#1F2937]  shadow-2xl absolute w-full left-0 z-50">
            <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
                <a href="#beranda" class="block px-4 py-3 text-gray-200  hover:bg-[#1F2937] :bg-neutral-800 rounded-xl font-bold transition-colors" onclick="document.getElementById('mobileMenu').classList.remove('active');">Beranda</a>
                <a href="#berita" class="block px-4 py-3 text-gray-200  hover:bg-[#1F2937] :bg-neutral-800 rounded-xl font-bold transition-colors" onclick="document.getElementById('mobileMenu').classList.remove('active');">Berita</a>
                <a href="#tentang" class="block px-4 py-3 text-gray-200  hover:bg-[#1F2937] :bg-neutral-800 rounded-xl font-bold transition-colors" onclick="document.getElementById('mobileMenu').classList.remove('active');">Tentang</a>
                <a href="#lokasi" class="block px-4 py-3 text-gray-200  hover:bg-[#1F2937] :bg-neutral-800 rounded-xl font-bold transition-colors" onclick="document.getElementById('mobileMenu').classList.remove('active');">Lokasi & Jadwal</a>
                <a href="#biaya" class="block px-4 py-3 text-gray-200  hover:bg-[#1F2937] :bg-neutral-800 rounded-xl font-bold transition-colors" onclick="document.getElementById('mobileMenu').classList.remove('active');">Biaya</a>
                <a href="#pelatih" class="block px-4 py-3 text-gray-200  hover:bg-[#1F2937] :bg-neutral-800 rounded-xl font-bold transition-colors" onclick="document.getElementById('mobileMenu').classList.remove('active');">Pelatih</a>
                <div class="pt-4 mt-4 border-t border-[#1F2937] flex flex-col gap-2">
                    <a href="pendaftaran.php" class="flex items-center justify-center w-full h-12 bg-white hover:bg-gray-100 text-[#0B1120] font-bold rounded-xl transition-colors shadow-sm">Daftar Sekarang</a>
                    <a href="login.php" class="flex items-center justify-center w-full h-12 bg-[#1F2937] hover:bg-[#374151] text-white font-bold rounded-xl transition-colors shadow-sm">Login Admin</a>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO -->
    <section id="beranda" class="py-16 sm:py-24 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Left Content -->
                <div class="z-10 relative">
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white  leading-[1.15] mb-6 tracking-tight">
                        <?= nl2br(htmlspecialchars($cmsData['hero_title'])) ?>
                    </h1>
                    <p class="text-lg text-gray-300  mb-8 leading-relaxed font-medium">
                        <?= nl2br(htmlspecialchars($cmsData['hero_desc'])) ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="pendaftaran.php" class="inline-flex items-center gap-2 px-8 py-4 bg-[#008AAB] hover:bg-[#005A70] transition-all transform hover:-translate-y-1 rounded-xl text-white font-bold shadow-xl shadow-[#008AAB]/30">
                            Daftar Sekarang
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="login.php" class="inline-flex items-center gap-2 px-8 py-4 bg-[#111928]  border-2 border-[#374151]  hover:border-[#008AAB] :border-[#005A70] hover:text-[#008AAB] :text-[#00A7D1] transition-all rounded-xl text-white  font-bold shadow-sm">
                            Login Admin
                        </a>
                    </div>
                </div>

                <!-- Right Dynamic Slider -->
                <div class="relative h-[400px] lg:h-[550px] rounded-[2rem] overflow-hidden shadow-2xl group border border-[#374151]/50 /50">
                    <div id="heroSlider" class="w-full h-full relative">
                        <?php foreach($sliders as $idx => $sld): 
                            $imgSrc = !empty($sld['gambar']) && file_exists('admin/uploads/'.$sld['gambar']) ? 'admin/uploads/'.$sld['gambar'] : 'https://images.unsplash.com/photo-1572334057861-6d72dbb688d2?auto=format&fit=crop&w=1200&q=80';
                        ?>
                        <div class="hero-slide absolute inset-0 w-full h-full <?= $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' ?>">
                            <img src="<?= $imgSrc ?>" alt="Slide" class="w-full h-full object-cover">
                            
                            <!-- Semi-transparent gradient overlay at the bottom -->
                            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                            
                            <?php if(!empty($sld['caption'])): ?>
                            <div class="absolute bottom-6 left-6 right-6 z-20">
                                <div class="bg-black/40 backdrop-blur-md border border-white/10 rounded-xl p-4 inline-block shadow-2xl">
                                    <p class="text-white font-semibold text-sm sm:text-base drop-shadow-md">
                                        <?= htmlspecialchars($sld['caption']) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BERITA TERBARU (3 Columns, Full Width Img, Load More) -->
    <section id="berita" class="py-16 sm:py-24 bg-[#0B1120]  border-y border-[#374151] ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center md:text-left flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white  tracking-tight">Berita & Informasi</h2>
                    <p class="text-gray-300  mt-3 font-medium text-lg">Update prestasi, kegiatan, dan pengumuman dari Swift SC.</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="beritaGrid">
                <?php if(count($beritaData) > 0): foreach($beritaData as $i => $b): 
                    $bImg = !empty($b['gambar']) && file_exists('admin/'.$b['gambar']) ? 'admin/'.$b['gambar'] : 'https://images.unsplash.com/photo-1572334057861-6d72dbb688d2?w=800&q=80';
                    $hiddenClass = $i > 2 ? 'hidden berita-hidden' : '';
                ?>
                <div class="bg-[#111928]  border border-[#374151]  rounded-[2rem] overflow-hidden hover:border-[#005A70]/50 :border-[#005A70]/50 transition-all duration-300 shadow-sm hover:shadow-2xl group flex flex-col <?= $hiddenClass ?>">
                    <div class="h-56 overflow-hidden relative">
                        <img src="<?= $bImg ?>" alt="<?= htmlspecialchars($b['judul']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                        <div class="absolute top-4 left-4 bg-[#008AAB]/90 backdrop-blur text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider shadow-lg"><?= htmlspecialchars($b['kategori']) ?></div>
                    </div>
                    <div class="p-6 md:p-8 flex flex-col flex-1 relative bg-[#111928] ">
                        <p class="text-xs text-[#008AAB]  mb-3 font-bold uppercase tracking-widest"><?= date('d M Y', strtotime($b['tanggal'])) ?></p>
                        <h3 class="text-white  font-bold text-xl mb-3 leading-snug line-clamp-2 group-hover:text-[#008AAB] :text-[#00A7D1] transition-colors"><?= htmlspecialchars($b['judul']) ?></h3>
                        <p class="text-gray-300  text-sm line-clamp-3 mb-6 leading-relaxed"><?= strip_tags($b['isi']) ?></p>
                        <div class="mt-auto">
                            <span class="inline-flex items-center gap-2 text-[#008AAB]  text-sm font-bold group-hover:gap-3 transition-all duration-300">Baca selengkapnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-gray-400 col-span-3 text-center py-10 font-medium">Belum ada berita.</p>
                <?php endif; ?>
            </div>

            <?php if(count($beritaData) > 3): ?>
            <div class="mt-12 text-center" id="loadMoreContainer">
                <button onclick="loadMoreBerita()" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1F2937] hover:bg-[#374151]  :bg-neutral-700 text-white  font-bold rounded-xl transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    Muat Lebih Banyak
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- TENTANG, VISI & MISI -->
    <section id="tentang" class="py-20 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                
                <!-- Left Column - Header -->
                <div class="lg:col-span-5 lg:sticky lg:top-32">
                    <span class="text-[#008AAB]  text-sm font-bold tracking-widest uppercase bg-[#002b36] /30 px-3 py-1 rounded-full">Tentang Klub</span>
                    <h2 class="mt-6 text-4xl lg:text-5xl font-black text-white  leading-[1.1]">
                        Mencetak Atlet <span class="text-[#008AAB] ">Berprestasi</span>.
                    </h2>
                    <p class="mt-6 text-lg text-gray-300  leading-relaxed text-justify font-medium">
                        <?= nl2br(htmlspecialchars($tentangData['deskripsi'])) ?>
                    </p>
                    <div class="mt-10 p-6 bg-[#0B1120]  border border-[#374151]  rounded-2xl shadow-sm">
                        <ul class="space-y-4 font-semibold text-gray-200 ">
                            <li class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-green-100 /40 text-green-600  rounded-full flex items-center justify-center shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                <?= htmlspecialchars($tentangData['feature_1'] ?? 'Berlisensi Nasional (ASCA & PRSI)') ?>
                            </li>
                            <li class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-green-100 /40 text-green-600  rounded-full flex items-center justify-center shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                <?= htmlspecialchars($tentangData['feature_2'] ?? 'Pelatih Profesional & Tersertifikasi') ?>
                            </li>
                            <li class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-green-100 /40 text-green-600  rounded-full flex items-center justify-center shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                <?= htmlspecialchars($tentangData['feature_3'] ?? 'Pendekatan Program Terstruktur') ?>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right Column - Cards Grid for Visi Misi -->
                <div class="lg:col-span-7 grid sm:grid-cols-2 gap-8">
                    <!-- Visi Card -->
                    <div class="p-8 bg-[#008AAB]  border border-[#005A70] rounded-[2rem] shadow-2xl shadow-[#008AAB]/20 text-white hover:-translate-y-2 transition-transform duration-300 relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 text-white/10">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5h-13L12 6.5z"/></svg>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-[#111928]/20 backdrop-blur text-white flex items-center justify-center mb-6 relative z-10">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <h4 class="text-3xl font-black mb-4 relative z-10 tracking-tight">Visi Klub</h4>
                        <p class="text-[#003847] leading-relaxed text-lg font-medium relative z-10">
                            <?= nl2br(htmlspecialchars($tentangData['visi'])) ?>
                        </p>
                    </div>

                    <!-- Misi Card -->
                    <div class="p-8 bg-[#111928]  border border-[#374151]  rounded-[2rem] shadow-xl hover:-translate-y-2 transition-transform duration-300 sm:translate-y-12 relative overflow-hidden">
                        <div class="w-14 h-14 rounded-2xl bg-[#002b36] /30 text-[#008AAB]  flex items-center justify-center mb-6 relative z-10">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h4 class="text-3xl font-black text-white  mb-4 tracking-tight relative z-10">Misi Kami</h4>
                        <div class="text-gray-300  leading-relaxed text-base font-medium space-y-2 relative z-10">
                            <?= nl2br(htmlspecialchars($tentangData['misi'])) ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- JADWAL DAN LOKASI -->
    <section id="lokasi" class="py-20 sm:py-24 bg-[#0B1120]  border-y border-[#374151] ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[#008AAB]  text-sm font-bold tracking-widest uppercase mb-3 block">Lokasi & Jadwal Latihan</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white  tracking-tight">Pilih Tempat Terdekat</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if(count($jadwalData) > 0): foreach($jadwalData as $namaCabang => $jadwals): ?>
                <div class="bg-[#111928]  border border-[#374151]  rounded-[2rem] p-8 shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center gap-5 mb-8 pb-6 border-b border-[#1F2937] ">
                        <div class="w-14 h-14 bg-[#002b36] /30 rounded-2xl flex items-center justify-center text-[#008AAB]  shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-xl text-white  leading-tight"><?= htmlspecialchars($namaCabang) ?></h3>
                            <p class="text-sm text-gray-400 font-medium mt-1 line-clamp-1"><?= htmlspecialchars($jadwals[0]['alamat_cabang'] ?? 'Kolam Renang') ?></p>
                        </div>
                    </div>
                    <ul class="space-y-4">
                        <?php foreach($jadwals as $j): ?>
                        <li class="flex justify-between items-center text-sm group">
                            <span class="font-bold text-gray-200  bg-[#0B1120]  border border-[#374151]  px-4 py-2 rounded-lg group-hover:bg-[#002b36] :bg-indigo-900/30 group-hover:text-[#008AAB] :text-[#00A7D1] group-hover:border-indigo-200 :border-indigo-800 transition-colors"><?= htmlspecialchars($j['hari']) ?></span>
                            <span class="text-gray-300  font-mono text-sm font-semibold"><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-center col-span-3 text-gray-400 font-medium">Belum ada jadwal yang dimasukkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- BIAYA (Pricing Minimal Centered) -->
    <section id="biaya" class="py-20 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[#008AAB]  text-sm font-bold tracking-widest uppercase mb-4 block">Paket Biaya</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white  mb-6 tracking-tight">Investasi Masa Depan Juara</h2>
                <p class="text-lg text-gray-300  font-medium">Pilih program kelas yang sesuai dengan kebutuhan dan target.</p>
            </div>
            
            <div class="grid lg:grid-cols-3 gap-8 lg:gap-0 relative z-10 max-w-5xl mx-auto">
                <?php foreach($paketData as $idx => $paket): ?>
                    <?php if($paket['is_populer']): ?>
                    <!-- Populer Plan -->
                    <div class="relative z-20">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-5 py-1.5 bg-[#008AAB] text-white text-[11px] font-black rounded-full uppercase tracking-widest shadow-lg z-30">
                            Terpopuler
                        </div>
                        <div class="bg-[#008AAB]  rounded-[2rem] p-8 text-white shadow-2xl shadow-[#008AAB]/40 transform lg:scale-105 h-full flex flex-col relative overflow-hidden">
                            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-[#111928]/10 rounded-full blur-2xl"></div>
                            <div class="inline-flex items-center gap-3 mb-6 relative z-10">
                                <div class="w-10 h-10 rounded-xl bg-[#111928]/20 flex items-center justify-center backdrop-blur-sm"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div>
                                <span class="text-lg font-bold"><?= htmlspecialchars($paket['nama_paket']) ?></span>
                            </div>
                            <div class="mb-6 border-b border-[#005A70] pb-6 relative z-10">
                                <span class="text-4xl font-black tracking-tight"><?= htmlspecialchars($paket['harga']) ?></span>
                                <?php if(!empty($paket['satuan_waktu'])): ?>
                                <span class="text-[#003847] font-semibold"><?= htmlspecialchars($paket['satuan_waktu']) ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-[#003847] mb-8 font-medium relative z-10"><?= htmlspecialchars($paket['deskripsi']) ?></p>
                            <ul class="space-y-4 mb-8 flex-1 relative z-10">
                                <?php if(!empty($paket['fitur_1'])): ?><li class="flex gap-3 font-semibold"><svg class="w-5 h-5 shrink-0 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> <?= htmlspecialchars($paket['fitur_1']) ?></li><?php endif; ?>
                                <?php if(!empty($paket['fitur_2'])): ?><li class="flex gap-3 font-semibold"><svg class="w-5 h-5 shrink-0 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> <?= htmlspecialchars($paket['fitur_2']) ?></li><?php endif; ?>
                                <?php if(!empty($paket['fitur_3'])): ?><li class="flex gap-3 font-semibold"><svg class="w-5 h-5 shrink-0 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> <?= htmlspecialchars($paket['fitur_3']) ?></li><?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Normal Plan -->
                    <?php 
                        $isLeft = ($idx == 0); 
                        $borderClasses = $isLeft ? "lg:border-y lg:border-l border-[#374151] lg:rounded-l-[2rem] bg-[#111928] rounded-[2rem] lg:rounded-r-none border lg:border-r-0" : "lg:border-y lg:border-r border-[#374151] lg:rounded-r-[2rem] bg-[#111928] rounded-[2rem] lg:rounded-l-none border lg:border-l-0";
                    ?>
                    <div class="<?= $borderClasses ?> p-8 shadow-sm hover:shadow-xl transition-shadow z-10 flex flex-col">
                        <div class="inline-flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-[#002b36] /30 flex items-center justify-center text-[#008AAB] "><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                            <span class="text-lg font-bold text-white "><?= htmlspecialchars($paket['nama_paket']) ?></span>
                        </div>
                        <div class="mb-6 border-b border-[#1F2937]  pb-6">
                            <span class="text-4xl font-black text-white  tracking-tight"><?= htmlspecialchars($paket['harga']) ?></span>
                            <?php if(!empty($paket['satuan_waktu'])): ?>
                            <span class="text-gray-400  font-semibold"><?= htmlspecialchars($paket['satuan_waktu']) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-300  mb-8 font-medium"><?= htmlspecialchars($paket['deskripsi']) ?></p>
                        <ul class="space-y-4 mb-8 flex-1">
                            <?php if(!empty($paket['fitur_1'])): ?><li class="flex gap-3 text-gray-200  font-semibold"><svg class="w-5 h-5 shrink-0 text-[#005A70]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> <?= htmlspecialchars($paket['fitur_1']) ?></li><?php endif; ?>
                            <?php if(!empty($paket['fitur_2'])): ?><li class="flex gap-3 text-gray-200  font-semibold"><svg class="w-5 h-5 shrink-0 text-[#005A70]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> <?= htmlspecialchars($paket['fitur_2']) ?></li><?php endif; ?>
                            <?php if(!empty($paket['fitur_3'])): ?><li class="flex gap-3 text-gray-200  font-semibold"><svg class="w-5 h-5 shrink-0 text-[#005A70]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> <?= htmlspecialchars($paket['fitur_3']) ?></li><?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="mt-12 text-center">
                <a href="pendaftaran.php" class="inline-flex items-center gap-2 px-10 py-4 bg-[#008AAB] hover:bg-[#005A70] text-white text-lg font-bold rounded-xl transition-all shadow-xl shadow-[#008AAB]/30 hover:-translate-y-1">
                    Daftar Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- PELATIH (Polaroid Hover Effect) -->
    <section id="pelatih" class="py-20 sm:py-24 bg-[#0B1120]  border-t border-[#374151]  overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[#008AAB]  text-sm font-bold tracking-widest uppercase mb-3 block">Coach & Instruktur</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white  tracking-tight">Tim Ahli di Balik Layar</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-10">
                <?php if(count($pelatihData) > 0): foreach($pelatihData as $idx => $p): 
                    $fMain = (!empty($p['foto']) && file_exists('admin/uploads/'.$p['foto'])) ? 'admin/uploads/'.$p['foto'] : "https://ui-avatars.com/api/?name=".urlencode($p['nama'])."&background=e0e7ff&color=4f46e5&size=512";
                    // Fallback berantai agar tidak blank
                    $fHover1 = (!empty($p['foto_hover_1']) && file_exists('admin/uploads/'.$p['foto_hover_1'])) ? 'admin/uploads/'.$p['foto_hover_1'] : $fMain;
                    $fHover2 = (!empty($p['foto_hover_2']) && file_exists('admin/uploads/'.$p['foto_hover_2'])) ? 'admin/uploads/'.$p['foto_hover_2'] : $fHover1;
                    // Sedikit rotasi acak untuk efek polaroid organik
                    $rotates = ['-rotate-2', 'rotate-2', '-rotate-3', 'rotate-1'];
                    $rot = $rotates[$idx % 4];
                ?>
                <div class="bg-white p-3 pb-8 md:pb-12 shadow-xl hover:shadow-2xl <?= $rot ?> hover:rotate-0 hover:-translate-y-2 transition-all duration-500 rounded-sm border border-gray-200 group relative z-10 hover:z-30">
                    <div class="aspect-[3/4] overflow-hidden relative bg-gray-100 shadow-inner rounded-sm">
                        <!-- Main Image (Top Layer, fades out first) -->
                        <img src="<?= $fMain ?>" alt="<?= htmlspecialchars($p['nama']) ?>" class="w-full h-full object-cover relative z-30 transition-opacity duration-[1000ms] group-hover:opacity-0 grayscale group-hover:grayscale-0">
                        <!-- Hover Image 1 (Middle Layer, always visible, revealed when Main fades) -->
                        <img src="<?= $fHover1 ?>" alt="<?= htmlspecialchars($p['nama']) ?> Action 1" class="w-full h-full object-cover absolute inset-0 z-20">
                        <!-- Hover Image 2 (Top Layer, fades in later) -->
                        <img src="<?= $fHover2 ?>" alt="<?= htmlspecialchars($p['nama']) ?> Action 2" class="w-full h-full object-cover absolute inset-0 z-40 opacity-0 group-hover:opacity-100 transition-opacity duration-[1000ms] delay-[1000ms]">
                    </div>
                    <div class="pt-5 text-center px-2">
                        <h4 class="font-black text-gray-900 text-lg tracking-tight line-clamp-1"><?= htmlspecialchars($p['nama']) ?></h4>
                        <p class="text-[#005A70] text-[10px] sm:text-xs font-bold mt-1.5 uppercase tracking-widest line-clamp-1"><?= htmlspecialchars($p['jabatan']) ?></p>
                        <p class="text-gray-500 text-[11px] mt-2 line-clamp-1 italic font-medium"><?= htmlspecialchars($p['lisensi'] ?? '') ?></p>
                    </div>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-center col-span-4 text-gray-400 font-medium">Belum ada pelatih yang terdaftar.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FOOTER ORISINAL -->
    <footer id="kontak" class="border-t border-slate-800/80 mt-12 bg-[#081223] relative z-10 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-12">
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
                    <li class="mt-4"><p class="text-xs text-gray-400 italic">Silakan klik nomor admin untuk chat langsung dan melakukan pendaftaran.</p></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-8 border-t border-slate-800 text-gray-300 flex justify-between items-center">
            <p class="text-xs">&copy; <?= date('Y'); ?> Swift Swimming Club. All Rights Reserved.</p>
            <a href="https://www.instagram.com/mduhanazz/" class="text-xs hover:text-white transition-colors">Made with ❤️</a>
        </div>
    </footer>

    <script>
        // Hero Slider Logic
        document.addEventListener('DOMContentLoaded', () => {
            const slides = document.querySelectorAll('.hero-slide');
            if(slides.length > 1) {
                let current = 0;
                setInterval(() => {
                    slides[current].classList.remove('opacity-100', 'z-10');
                    slides[current].classList.add('opacity-0', 'z-0');
                    current = (current + 1) % slides.length;
                    slides[current].classList.remove('opacity-0', 'z-0');
                    slides[current].classList.add('opacity-100', 'z-10');
                }, 5000);
            }
        });

        // Load More Berita
        function loadMoreBerita() {
            const hiddenItems = document.querySelectorAll('.berita-hidden');
            hiddenItems.forEach(item => {
                item.classList.remove('hidden');
                item.classList.remove('berita-hidden');
            });
            document.getElementById('loadMoreContainer').style.display = 'none';
        }
    </script>
    <?php include_once __DIR__ . '/includes/swal_helper.php'; ?>
</body>
</html>
