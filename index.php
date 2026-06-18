<?php 
include 'includes/koneksi.php'; 

// Ambil CMS Content
$cmsData = [
    'hero_title' => "Jump in. let's swim!",
    'hero_subtitle' => "SWIFT SC",
    'hero_desc' => "Klub renang resmi dan tersertifikasi dengan fasilitas pelatih berlisensi nasional & internasional.",
    'about_text' => "Swift Swimming Club adalah klub renang yang resmi dan telah tersertifikasi. Dengan fasilitas pelatih berlisensi nasional maupun internasional dan peralatan renang yang memadai."
];

try {
    $q_cms = mysqli_query($koneksi, "SELECT judul, konten FROM cms_landing WHERE tipe='Banner' AND status='Aktif' LIMIT 1");
    if($q_cms && $row_cms = mysqli_fetch_assoc($q_cms)) {
        $cmsData['hero_title'] = $row_cms['judul'];
        $cmsData['hero_desc'] = $row_cms['konten'];
    }
} catch (\Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift SC — Jump in. let's swim!</title>
    <meta name="description" content="Swift Swimming Club - Klub renang resmi dan tersertifikasi dengan pelatih berlisensi nasional & internasional.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        .hero-gradient {
            background: linear-gradient(135deg, #21243D 0%, #2D3167 50%, #21243D 100%);
        }
        .section-fade { animation: sectionFade 0.5s ease-out; }
        @keyframes sectionFade {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-[#F5F5FA] text-[#21243D] antialiased">
    
    <!-- Navbar -->
    <nav class="bg-white border-b border-[#E8E8EF] sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 h-14">
            <a href="index.php" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#5468FF] flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="font-bold text-[15px] text-[#21243D]">SWIFT<span class="text-[#5468FF]">_SC</span></span>
            </a>

            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="#tentang" class="text-[#6B6F8D] hover:text-[#21243D] transition-colors">Profil</a>
                <a href="#program" class="text-[#6B6F8D] hover:text-[#21243D] transition-colors">Program</a>
                <a href="#jadwal" class="text-[#6B6F8D] hover:text-[#21243D] transition-colors">Jadwal</a>
                <a href="#pelatih" class="text-[#6B6F8D] hover:text-[#21243D] transition-colors">Pelatih</a>
                <a href="login.php" class="bg-[#5468FF] hover:bg-[#3A4DC7] text-white px-4 py-2 rounded-lg font-semibold text-xs transition-colors ml-2">Login Portal</a>
            </div>

            <!-- Mobile menu button -->
            <button id="mobileMenuBtn" class="md:hidden p-2 text-[#6B6F8D] hover:text-[#21243D]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-[#E8E8EF] bg-white px-4 pb-4 pt-2 space-y-2">
            <a href="#tentang" class="block py-2 text-sm text-[#6B6F8D] font-medium">Profil</a>
            <a href="#program" class="block py-2 text-sm text-[#6B6F8D] font-medium">Program</a>
            <a href="#jadwal" class="block py-2 text-sm text-[#6B6F8D] font-medium">Jadwal & Biaya</a>
            <a href="#pelatih" class="block py-2 text-sm text-[#6B6F8D] font-medium">Pelatih</a>
            <a href="login.php" class="block py-2 text-sm text-[#5468FF] font-semibold">Login Portal →</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-gradient relative overflow-hidden">
        <?php if (file_exists('admin/uploads/hero_bg.jpg')): ?>
            <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('admin/uploads/hero_bg.jpg?v=<?= time() ?>');"></div>
        <?php endif; ?>
        
        <!-- Subtle decorative circles (no blur = no lag) -->
        <div class="absolute top-10 right-10 w-64 h-64 rounded-full bg-[#5468FF]/10"></div>
        <div class="absolute bottom-10 left-10 w-48 h-48 rounded-full bg-[#5468FF]/5"></div>

        <div class="relative max-w-4xl mx-auto text-center px-4 py-20 lg:py-28">
            <p class="text-[#9CA0B8] text-sm font-semibold uppercase tracking-wider mb-4"><?= htmlspecialchars($cmsData['hero_subtitle']) ?></p>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6"><?= htmlspecialchars($cmsData['hero_title']) ?></h1>
            <p class="text-lg text-[#B8BBCF] max-w-2xl mx-auto mb-10"><?= htmlspecialchars($cmsData['hero_desc']) ?></p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="pendaftaran.php" class="bg-[#5468FF] hover:bg-[#3A4DC7] text-white font-semibold px-8 py-3 rounded-lg text-sm transition-colors">Daftar Sekarang</a>
                <a href="#tentang" class="bg-white/10 hover:bg-white/20 text-white font-medium px-8 py-3 rounded-lg text-sm transition-colors border border-white/20">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-16 space-y-20">
        
        <!-- PROFIL & PROGRAM -->
        <section id="tentang" class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start section-fade">
            <div>
                <p class="text-xs font-semibold text-[#5468FF] uppercase tracking-wider mb-2">Tentang Kami</p>
                <h2 class="text-2xl font-bold text-[#21243D] mb-4">Swift Swimming Club</h2>
                <p class="text-[#6B6F8D] leading-relaxed mb-6"><?= nl2br(htmlspecialchars($cmsData['about_text'])) ?></p>
                
                <p class="text-xs font-semibold text-[#6B6F8D] uppercase tracking-wider mb-3">Menerima Siswa-Siswi Baru:</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-[#E8E8EF]">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-[#5468FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div><span class="text-sm font-semibold text-[#21243D]">Grup</span> <span class="text-xs text-[#9CA0B8] ml-1">(Min. usia 4 tahun)</span></div>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-[#E8E8EF]">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div><span class="text-sm font-semibold text-[#21243D]">Privat</span> <span class="text-xs text-[#9CA0B8] ml-1">(Tidak ada Min. usia)</span></div>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-[#E8E8EF]">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div><span class="text-sm font-semibold text-[#21243D]">Persiapan TNI/Polri</span></div>
                    </div>
                </div>
            </div>
            
            <div id="program" class="bg-white rounded-xl border border-[#E8E8EF] p-6">
                <div class="flex items-center gap-2 mb-5">
                    <span class="inline-flex items-center gap-1 bg-[#EEF0FF] text-[#5468FF] text-[11px] font-semibold px-2.5 py-1 rounded">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        Tersertifikasi
                    </span>
                </div>
                <h3 class="text-lg font-bold text-[#21243D] mb-4">Badan Sertifikasi Resmi</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-[#F5F5FA] p-3 rounded-lg text-center"><span class="text-xs font-semibold text-[#5468FF]">Swim Clinic</span></div>
                    <div class="bg-[#F5F5FA] p-3 rounded-lg text-center"><span class="text-xs font-semibold text-[#5468FF]">ASCA Lv. 1</span></div>
                    <div class="bg-[#F5F5FA] p-3 rounded-lg text-center"><span class="text-xs font-semibold text-[#5468FF]">ASCA Lv. 2</span></div>
                    <div class="bg-[#F5F5FA] p-3 rounded-lg text-center"><span class="text-xs font-semibold text-[#5468FF]">SK Club Swift</span></div>
                    <div class="bg-[#EEF0FF] p-3 rounded-lg text-center col-span-2 border border-[#5468FF]/10"><span class="text-xs font-semibold text-[#5468FF]">Lisensi Kategori C Nasional</span></div>
                </div>
            </div>
        </section>

        <!-- JADWAL DAN BIAYA -->
        <section id="jadwal" class="section-fade">
            <div class="text-center mb-8">
                <p class="text-xs font-semibold text-[#5468FF] uppercase tracking-wider mb-2">Jadwal & Biaya</p>
                <h2 class="text-2xl font-bold text-[#21243D]">Lokasi dan Jadwal Latihan</h2>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-xl border border-[#E8E8EF] overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[11px] text-[#6B6F8D] uppercase tracking-wider bg-[#FAFAFE]">
                                <th class="px-5 py-3 font-semibold">Lokasi Kolam Renang</th>
                                <th class="px-5 py-3 font-semibold">Jadwal Latihan</th>
                            </tr>
                        </thead>
                        <tbody>
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
                            <tr class="border-t border-[#F0F0F5] hover:bg-[#FAFAFE] transition-colors">
                                <td class="px-5 py-3 font-medium text-[#21243D]"><?= htmlspecialchars($s['lokasi']) ?></td>
                                <td class="px-5 py-3 text-[#4A4F6A]"><?= htmlspecialchars($s['hari']) ?> <span class="text-[#9CA0B8] text-xs">(<?= date('H:i', strtotime($s['jam_mulai'])) ?> - <?= date('H:i', strtotime($s['jam_selesai'])) ?> WIB)</span></td>
                            </tr>
                            <?php } } else { ?>
                            <tr><td colspan="2" class="px-5 py-8 text-center text-[#9CA0B8]">Jadwal latihan belum tersedia.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- BIAYA -->
                <div class="bg-white rounded-xl border border-[#E8E8EF] p-6">
                    <h3 class="text-base font-bold text-[#21243D] mb-4">Biaya</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-[#F5F5FA] p-4 rounded-lg">
                            <p class="text-[11px] font-semibold text-[#6B6F8D] uppercase mb-2">Paket Reguler</p>
                            <div class="flex justify-between items-center border-b border-[#E8E8EF] pb-2 mb-2">
                                <span class="text-sm text-[#4A4F6A]">Pendaftaran</span>
                                <span class="text-sm font-bold text-[#5468FF]">Rp 100.000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-[#4A4F6A]">SPP (8 Sesi)</span>
                                <span class="text-sm font-bold text-[#5468FF]">Rp 225.000</span>
                            </div>
                        </div>
                        
                        <div class="bg-[#EEF0FF] p-4 rounded-lg border border-[#5468FF]/10">
                            <p class="text-[11px] font-semibold text-[#5468FF] uppercase mb-2">* Khusus Umbang Tirta</p>
                            <div class="flex justify-between items-center border-b border-[#5468FF]/10 pb-2 mb-2">
                                <span class="text-xs text-[#4A4F6A]">Pendaftaran</span>
                                <span class="text-xs font-bold text-[#5468FF]">Rp 100.000</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-[#5468FF]/10 pb-2 mb-2">
                                <span class="text-xs text-[#4A4F6A]">4 Sesi/bulan</span>
                                <span class="text-xs font-bold text-[#5468FF]">Rp 150.000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-[#4A4F6A]">8 Sesi/bulan</span>
                                <span class="text-xs font-bold text-[#5468FF]">Rp 200.000</span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="pendaftaran.php" class="block w-full text-center bg-[#5468FF] hover:bg-[#3A4DC7] text-white font-semibold py-3 rounded-lg text-sm mt-5 transition-colors">Daftar Sekarang</a>
                </div>
            </div>
        </section>

        <!-- TIM KEPELATIHAN -->
        <section id="pelatih" class="section-fade">
            <div class="text-center mb-8">
                <p class="text-xs font-semibold text-[#5468FF] uppercase tracking-wider mb-2">Tim Kami</p>
                <h2 class="text-2xl font-bold text-[#21243D]">Tim Kepelatihan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $pelatihList = [];
                try {
                    $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih WHERE is_highlighted=1 LIMIT 3");
                    if(!$q_pelatih || mysqli_num_rows($q_pelatih) == 0) {
                        $q_pelatih = mysqli_query($koneksi, "SELECT * FROM pelatih LIMIT 3");
                    }
                    while($row = mysqli_fetch_assoc($q_pelatih)) {
                        $pelatihList[] = $row;
                    }
                } catch (\Exception $e) {}
                
                if (count($pelatihList) > 0) :
                    foreach($pelatihList as $p) :
                        $foto_pelatih = !empty($p['foto']) && file_exists("admin/" . $p['foto']) ? "admin/" . $p['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($p['nama']) . "&background=5468FF&color=fff&size=256";
                ?>
                <div class="bg-white rounded-xl border border-[#E8E8EF] overflow-hidden hover:shadow-md transition-shadow">
                    <div class="h-48 bg-[#F5F5FA] bg-cover bg-center" style="background-image: url('<?= $foto_pelatih ?>');"></div>
                    <div class="p-4 text-center">
                        <h4 class="text-sm font-bold text-[#21243D]"><?= htmlspecialchars($p['nama']); ?></h4>
                        <p class="text-xs text-[#5468FF] font-semibold"><?= htmlspecialchars($p['jabatan']); ?></p>
                        <p class="text-[11px] text-[#9CA0B8] mt-1"><?= htmlspecialchars($p['sertifikasi']); ?></p>
                    </div>
                </div>
                <?php 
                    endforeach; 
                else : 
                ?>
                    <p class="text-[#9CA0B8] col-span-3 text-center py-8 text-sm">Belum ada data pelatih di sistem.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- BERITA -->
        <section id="berita" class="section-fade">
            <div class="text-center mb-8">
                <p class="text-xs font-semibold text-[#5468FF] uppercase tracking-wider mb-2">Informasi</p>
                <h2 class="text-2xl font-bold text-[#21243D]">Berita Terkini</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                <div class="bg-white rounded-xl border border-[#E8E8EF] overflow-hidden hover:shadow-md transition-shadow group">
                    <div class="h-44 bg-cover bg-center bg-[#F5F5FA]" style="background-image: url('<?= $foto_berita ?>');"></div>
                    <div class="p-4">
                        <span class="inline-block bg-[#EEF0FF] text-[#5468FF] text-[10px] font-semibold px-2 py-0.5 rounded mb-2"><?= htmlspecialchars($b['cabang'] ?? 'Umum') ?></span>
                        <h4 class="text-sm font-bold text-[#21243D] mb-1 leading-snug"><?= htmlspecialchars($b['judul']) ?></h4>
                        <p class="text-xs text-[#9CA0B8] line-clamp-2"><?= strip_tags($b['isi'] ?? '') ?></p>
                    </div>
                </div>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <p class="text-[#9CA0B8] col-span-3 text-center py-8 text-sm">Belum ada berita yang dipublikasikan.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-[#21243D] text-white mt-16">
        <div class="max-w-6xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-3 gap-10">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-[#5468FF] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="font-bold text-sm">SWIFT<span class="text-[#5468FF]">_SC</span></span>
                </div>
                <p class="text-xs text-[#9CA0B8] leading-relaxed">Mencetak atlet renang berprestasi dengan fasilitas dan metode kepelatihan terbaik.</p>
            </div>
            
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-[#6B6F8D] mb-4">Media Sosial</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="https://instagram.com/swiftswimmingclub" target="_blank" class="text-[#9CA0B8] hover:text-white transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.88z"/></svg>
                        swiftswimmingclub
                    </a></li>
                    <li><a href="https://tiktok.com/@swiftswimmingclub" target="_blank" class="text-[#9CA0B8] hover:text-white transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg>
                        swiftswimmingclub
                    </a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-[#6B6F8D] mb-4">WhatsApp Pendaftaran</h4>
                <a href="https://wa.me/6289668366724" target="_blank" class="inline-flex items-center gap-2 text-green-400 hover:text-green-300 font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    +62 896-6836-6724
                </a>
                <p class="text-[11px] text-[#6B6F8D] mt-2">Klik untuk chat langsung via WhatsApp</p>
            </div>
        </div>
        <div class="border-t border-white/5 py-4 text-center">
            <p class="text-[11px] text-[#6B6F8D]">&copy; <?= date('Y'); ?> Swift Swimming Club. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        });
        // Close mobile menu on link click
        document.querySelectorAll('#mobileMenu a').forEach(function(link) {
            link.addEventListener('click', function() {
                document.getElementById('mobileMenu').classList.add('hidden');
            });
        });
    </script>
</body>
</html>