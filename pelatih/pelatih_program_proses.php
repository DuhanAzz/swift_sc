<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan'])){
    $tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $target_grup = mysqli_real_escape_string($koneksi, $_POST['target_grup']);
    $judul       = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $tipe_program = 'Harian';
    
    $coach_cabang_id = $_SESSION['cabang'] ?? '';
    $pelatih_id = $_SESSION['user_id'] ?? 0;

    $q_insert = mysqli_query($koneksi, "INSERT INTO program_latihan (pelatih_id, tanggal, target_grup, judul, tipe_program, deskripsi, cabang_id) VALUES ('$pelatih_id', '$tanggal', '$target_grup', '$judul', '$tipe_program', '$deskripsi', '$coach_cabang_id')");
    if($q_insert) {
        header("location:pelatih_program.php?pesan=sukses");
    } else {
        header("location:pelatih_program.php?pesan=gagal"); 
    }
}

if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $q_delete = mysqli_query($koneksi, "DELETE FROM program_latihan WHERE id='$id'");
    if($q_delete) {
        header("location:pelatih_program.php?pesan=hapus");
    } else {
        header("location:pelatih_program.php?pesan=gagal"); 
    }
}
?>
