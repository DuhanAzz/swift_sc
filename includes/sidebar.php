<?php
// Pastikan session sudah aktif di file yang memanggil sidebar ini
$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['name'] ?? 'Pengguna';

// Detect current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobile Top Bar -->
<div class="fixed top-0 left-0 right-0 h-16 glass-card-solid z-40 flex items-center justify-between px-4 lg:hidden">
    <button id="sidebarToggle" class="p-2 rounded-xl text-slate-400 hover:text-electric-400 hover:bg-electric-500/10 transition-all" aria-label="Toggle menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    <div class="font-sora font-bold text-lg tracking-tight">
        SWIFT<span class="text-electric-500">_SC</span>
    </div>
    <div class="w-8 h-8 rounded-full bg-electric-500/20 border border-electric-500/30 flex items-center justify-center">
        <span class="text-electric-400 text-xs font-bold"><?= strtoupper(substr($nama, 0, 1)) ?></span>
    </div>
</div>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebarOverlay" class="sidebar-overlay fixed inset-0 z-40 lg:hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-[270px] z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col"
       style="background: linear-gradient(180deg, #0C1322 0%, #0F172A 50%, #0C1322 100%); border-right: 1px solid rgba(255,255,255,0.06);">
    
    <!-- Logo Section -->
    <div class="p-6 pb-4">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-electric-500 to-xenon-500 flex items-center justify-center shadow-glow-blue">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <h1 class="font-sora font-bold text-xl tracking-tight text-white">SWIFT<span class="text-electric-400">_SC</span></h1>
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-medium">Swimming Club</p>
            </div>
        </div>
    </div>
    
    <!-- User Info -->
    <div class="mx-4 mb-4 p-4 rounded-xl glass-card">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-electric-500/20 to-xenon-500/20 border border-electric-500/20 flex items-center justify-center flex-shrink-0">
                <span class="text-electric-400 font-bold text-sm"><?= strtoupper(substr($nama, 0, 2)) ?></span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-200 truncate"><?= htmlspecialchars($nama) ?></p>
                <span class="badge-electric text-[10px] px-2 py-0.5 rounded-full uppercase font-bold tracking-wider inline-block mt-1">
                    <?= htmlspecialchars($role) ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 px-3 space-y-1 overflow-y-auto pb-4">
        
        <?php if ($role == 'ceo'): ?>
            <p class="text-[10px] text-slate-500 uppercase tracking-[0.15em] font-semibold mb-2 mt-2 px-3">Menu CEO</p>
            
            <a href="../ceo/ceo_dashboard.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'ceo_dashboard.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard Utama
            </a>
            <a href="../ceo/ceo_cms_web.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'ceo_cms_web.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                CMS Halaman Web
            </a>
            <a href="../ceo/ceo_manage_akun.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'ceo_manage_akun.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Manajemen Akun
            </a>
            <a href="../ceo/ceo_manage_kolam.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'ceo_manage_kolam.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Manajemen Kolam
            </a>
            <a href="../ceo/ceo_laporan_global.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'ceo_laporan_global.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Laporan Global
            </a>

        <?php elseif ($role == 'admin'): ?>
            <p class="text-[10px] text-slate-500 uppercase tracking-[0.15em] font-semibold mb-2 mt-2 px-3">Dashboard</p>
            
            <a href="../admin/admin_dashboard.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'admin_dashboard.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard Cabang
            </a>
            
            <p class="text-[10px] text-slate-500 uppercase tracking-[0.15em] font-semibold mb-2 mt-5 px-3">Manajemen Data</p>
            
            <a href="../admin/atlet.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'atlet.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Data Atlet
            </a>
            <a href="../admin/pelatih.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'pelatih.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Data Pelatih
            </a>
            <a href="../admin/jadwal.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'jadwal.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Jadwal Latihan
            </a>
            <a href="../admin/kolam.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'kolam.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Data Kolam
            </a>
            
            <p class="text-[10px] text-slate-500 uppercase tracking-[0.15em] font-semibold mb-2 mt-5 px-3">Aktivitas</p>
            
            <a href="../admin/performa.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'performa.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Performa Atlet
            </a>
            <a href="../admin/presensi.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'presensi.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Presensi
            </a>
            <a href="../admin/pembayaran.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'pembayaran.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Pembayaran SPP
            </a>
            <a href="../admin/arus_kas.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'arus_kas.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Arus Kas
            </a>

        <?php elseif ($role == 'pelatih' || $role == 'coach'): ?>
            <p class="text-[10px] text-slate-500 uppercase tracking-[0.15em] font-semibold mb-2 mt-2 px-3">Menu Pelatih</p>
            
            <a href="../pelatih/pelatih_dashboard.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'pelatih_dashboard.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard Pelatih
            </a>
            <a href="../pelatih/pelatih_absensi.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'pelatih_absensi.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Input Presensi
            </a>
            <a href="../pelatih/pelatih_performa.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'pelatih_performa.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Catat Performa
            </a>
            <a href="../pelatih/pelatih_program.php" class="sidebar-link flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-slate-400 <?= ($current_page == 'pelatih_program.php') ? 'active' : '' ?>">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Jurnal Latihan
            </a>
        <?php endif; ?>
    </nav>
    
    <!-- Logout Button -->
    <div class="p-4 border-t border-white/5">
        <a href="../logout.php" class="flex items-center gap-3 py-2.5 px-3 rounded-xl text-sm font-medium text-red-400/70 hover:text-red-300 hover:bg-red-500/10 transition-all">
            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Logout
        </a>
    </div>
</aside>