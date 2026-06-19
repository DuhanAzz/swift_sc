<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/koneksi.php';

// STRICT: Admin hanya bisa lihat cabang miliknya sendiri
$admin_pool_id = $_SESSION['pool_id'] ?? '';
if(empty($admin_pool_id)) {
    echo '<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen"><div class="p-8"><p class="text-red-500 font-bold">Akses ditolak: Akun Anda belum terhubung ke cabang manapun.</p></div></div>';
    include '../includes/footer.php';
    exit;
}

// Ambil data cabang milik admin ini saja
$cabang_data = null;
$q = mysqli_query($koneksi, "SELECT * FROM cabang WHERE id='$admin_pool_id' LIMIT 1");
if($q && mysqli_num_rows($q) > 0) {
    $cabang_data = mysqli_fetch_assoc($q);
}

// Hitung statistik cabang
$total_atlet = 0;
$total_pelatih = 0;
$q_atlet = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM member WHERE cabang_id='$admin_pool_id'");
if($q_atlet) $total_atlet = mysqli_fetch_assoc($q_atlet)['total'] ?? 0;

$q_pelatih = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM users WHERE cabang_id='$admin_pool_id' AND role='Pelatih'");
if($q_pelatih) $total_pelatih = mysqli_fetch_assoc($q_pelatih)['total'] ?? 0;
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen">
    <div class="p-4 lg:p-8 page-content">
        
        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "sukses_edit"){
                echo '<div class="p-3 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 font-medium">✅ Profil cabang berhasil diperbarui!</div>';
            }
        }
        ?>

        <div class="mb-6">
            <h1 class="text-xl font-bold text-algolia-navy">Profil Cabang</h1>
            <p class="text-sm text-gray-500">Detail informasi cabang kolam renang Anda</p>
        </div>

        <?php if($cabang_data): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Profile Card -->
            <div class="lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-8">
                        <div class="flex items-center gap-4">
                            <?php if(!empty($cabang_data['foto'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($cabang_data['foto']) ?>" class="w-16 h-16 rounded-xl object-cover border-2 border-white/20">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($cabang_data['nama_cabang']) ?></h2>
                                <p class="text-sm text-white/70">ID Cabang: #<?= $cabang_data['id'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="proses_profil_cabang.php" method="POST" enctype="multipart/form-data" class="p-6">
                        <input type="hidden" name="id" value="<?= $cabang_data['id'] ?>">
                        <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($cabang_data['foto'] ?? '') ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama Cabang</label>
                                <input type="text" name="nama_cabang" value="<?= htmlspecialchars($cabang_data['nama_cabang']) ?>" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Terdaftar Sejak</label>
                                <input type="text" value="<?= date('d M Y', strtotime($cabang_data['created_at'] ?? 'now')) ?>" class="bg-gray-100 border border-[#E8E8EF] text-gray-500 text-sm rounded-xl block w-full p-3 cursor-not-allowed" disabled>
                            </div>
                        </div>
                        
                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jam Operasional</label>
                                <input type="text" name="jam_operasional" value="<?= htmlspecialchars($cabang_data['jam_operasional'] ?? '') ?>" placeholder="Misal: Senin - Jumat (08:00 - 17:00)" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Upload Foto Cabang</label>
                                <input type="file" name="foto" accept="image/*" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-2.5 shadow-sm transition-all">
                                <p class="text-[10px] text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah foto saat ini.</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Alamat / Lokasi</label>
                            <textarea name="lokasi" rows="2" class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm transition-all" required><?= htmlspecialchars($cabang_data['lokasi']) ?></textarea>
                        </div>
                        
                        <div class="mt-5">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi Cabang</label>
                            <textarea name="deskripsi" rows="3" placeholder="Jelaskan detail fasilitas dan profil singkat kolam renang..." class="bg-gray-50 border border-[#E8E8EF] text-gray-900 text-sm font-medium rounded-xl focus:ring-algolia-blue focus:border-algolia-blue block w-full p-3 shadow-sm transition-all" required><?= htmlspecialchars($cabang_data['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <div class="mt-5 flex justify-end">
                            <button type="submit" name="update_profil" class="bg-algolia-blue hover:bg-algolia-darkblue text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition-all text-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats Sidebar -->
            <div class="space-y-4">
                <div class="card p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-algolia-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-algolia-navy"><?= $total_atlet ?></p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Total Atlet</p>
                        </div>
                    </div>
                </div>
                
                <div class="card p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-algolia-navy"><?= $total_pelatih ?></p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Pelatih</p>
                        </div>
                    </div>
                </div>

                <div class="card p-4 bg-amber-50 border-amber-200">
                    <div class="flex items-start gap-2">
                        <span class="text-amber-500 mt-0.5">🔒</span>
                        <div>
                            <p class="text-xs font-bold text-amber-800">Akses Terbatas</p>
                            <p class="text-[10px] text-amber-600 mt-0.5">Anda hanya dapat mengedit profil cabang ini. Untuk menambah/menghapus cabang, hubungi CEO.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card p-10 text-center">
            <p class="text-gray-400">Data cabang tidak ditemukan.</p>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
