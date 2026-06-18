<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan'])){
    $record_date = mysqli_real_escape_string($koneksi, $_POST['record_date']);
    $member_id   = mysqli_real_escape_string($koneksi, $_POST['member_id']);
    $swim_style  = mysqli_real_escape_string($koneksi, $_POST['swim_style']);
    $distance    = mysqli_real_escape_string($koneksi, $_POST['distance']);
    
    // Formatting time
    $m = str_pad((int)$_POST['time_m'], 2, '0', STR_PAD_LEFT);
    $s = str_pad((int)$_POST['time_s'], 2, '0', STR_PAD_LEFT);
    $ms = str_pad((int)$_POST['time_ms'], 2, '0', STR_PAD_LEFT);
    $time_formatted = "{$m}:{$s}.{$ms}";

    $time_in_ms = ((int)$_POST['time_m'] * 60000) + ((int)$_POST['time_s'] * 1000) + ((int)$_POST['time_ms'] * 10);
    $notes = mysqli_real_escape_string($koneksi, $_POST['notes']);

    $recorded_by = $_SESSION['user_id'] ?? 0;
    $cabang_id = $_SESSION['cabang'] ?? 0;

    $q_insert = mysqli_query($koneksi, "INSERT INTO performa (member_id, cabang_id, gaya_renang, jarak, waktu_formatted, waktu_ms, tanggal_rekor, catatan, recorded_by) VALUES ('$member_id', '$cabang_id', '$swim_style', '$distance', '$time_formatted', '$time_in_ms', '$record_date', '$notes', '$recorded_by')");
    if($q_insert) {
        header("location:pelatih_performa.php?pesan=sukses");
    } else {
        header("location:pelatih_performa.php?pesan=gagal"); 
    }
}
?>
