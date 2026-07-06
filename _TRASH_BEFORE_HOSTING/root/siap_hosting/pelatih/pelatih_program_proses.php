<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan'])){
    $tanggal     = $_POST['tanggal'];
    $target_grup = $_POST['target_grup'];
    $judul       = $_POST['judul'];
    $deskripsi   = $_POST['deskripsi'];
    
    $coach_pool_id = $_SESSION['pool_id'] ?? '';
    $coach_name    = $_SESSION['name'] ?? 'Pelatih';
    $coach_email   = $_SESSION['email'] ?? '';

    try {
        $database->newDocument('training_programs', [
            'tanggal' => $tanggal,
            'target_grup' => $target_grup,
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'pool_id' => $coach_pool_id,
            'coach_name' => $coach_name,
            'coach_email' => $coach_email,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:pelatih_program.php?pesan=sukses");
    } catch (\Exception $e) { 
        header("location:pelatih_program.php?pesan=gagal"); 
    }
}

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    try {
        $database->deleteDocument('training_programs', $id);
        header("location:pelatih_program.php?pesan=hapus");
    } catch (\Exception $e) { 
        header("location:pelatih_program.php?pesan=gagal"); 
    }
}
?>
