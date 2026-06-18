<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-algolia-navy tracking-tight">CMS Command Hub</h1>
            <p class="text-base text-gray-500 mt-1">Pusat kendali konten untuk halaman publik (Landing Page).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <a href="ceo_cms_general.php" class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 shadow-lg text-white hover:scale-105 transition-transform duration-300 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-20 group-hover:opacity-40 transition-opacity">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold mb-2">Teks Banner & Profil</h3>
                    <p class="text-indigo-100 text-sm">Ubah teks Hero Banner dan paragraf "Tentang Klub".</p>
                </div>
            </a>

            <a href="ceo_cms_jadwal.php" class="bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl p-6 shadow-lg text-white hover:scale-105 transition-transform duration-300 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-20 group-hover:opacity-40 transition-opacity">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold mb-2">Jadwal Publik</h3>
                    <p class="text-orange-100 text-sm">Atur lokasi dan jadwal latihan yang tampil di tabel publik.</p>
                </div>
            </a>

            <a href="ceo_cms_pelatih_highlight.php" class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-2xl p-6 shadow-lg text-white hover:scale-105 transition-transform duration-300 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-20 group-hover:opacity-40 transition-opacity">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold mb-2">Highlight Pelatih</h3>
                    <p class="text-teal-100 text-sm">Pilih pelatih yang akan ditampilkan di halaman depan.</p>
                </div>
            </a>

            <a href="ceo_cms_berita.php" class="bg-gradient-to-br from-pink-500 to-pink-700 rounded-2xl p-6 shadow-lg text-white hover:scale-105 transition-transform duration-300 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-20 group-hover:opacity-40 transition-opacity">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold mb-2">Manajemen Berita</h3>
                    <p class="text-pink-100 text-sm">Tulis, edit, atau hapus artikel dan pengumuman terbaru.</p>
                </div>
            </a>

        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
