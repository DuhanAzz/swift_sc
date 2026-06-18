<?php
session_start();
include '../includes/koneksi.php';

// PROSES TAMBAH REKOR BARU
if(isset($_POST['tambah'])){
    $member_id   = mysqli_real_escape_string($koneksi, $_POST['member_id']);
    $record_date = mysqli_real_escape_string($koneksi, $_POST['record_date']);
    $pool_length = mysqli_real_escape_string($koneksi, $_POST['pool_length']);
    $swim_style  = mysqli_real_escape_string($koneksi, $_POST['swim_style']);
    $distance    = mysqli_real_escape_string($koneksi, $_POST['distance']);
    
    // Menangkap input waktu
    $menit       = (int)$_POST['menit'];
    $detik       = (int)$_POST['detik'];
    $milidetik   = (int)$_POST['milidetik'];
    
    $time_formatted = sprintf("%02d:%02d.%02d", $menit, $detik, $milidetik);
    $time_ms = ($menit * 60000) + ($detik * 1000) + ($milidetik * 10);
    $notes       = mysqli_real_escape_string($koneksi, $_POST['notes']);

    $coach_cabang_id = $_SESSION['cabang'] ?? '';
    $recorded_by = $_SESSION['user_id'] ?? 0;

    $q = mysqli_query($koneksi, "INSERT INTO performa (member_id, cabang_id, gaya_renang, jarak, tipe_kolam, waktu_formatted, waktu_ms, tanggal_rekor, catatan, recorded_by) VALUES ('$member_id', '$coach_cabang_id', '$swim_style', '$distance', '$pool_length', '$time_formatted', '$time_ms', '$record_date', '$notes', '$recorded_by')");
    
    if($q) {
        header("location:performa.php?pesan=sukses_tambah");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}

// PROSES EDIT REKOR WAKTU
if(isset($_POST['edit'])){
    $id          = mysqli_real_escape_string($koneksi, $_POST['id']);
    $member_id   = mysqli_real_escape_string($koneksi, $_POST['member_id']);
    $record_date = mysqli_real_escape_string($koneksi, $_POST['record_date']);
    $pool_length = mysqli_real_escape_string($koneksi, $_POST['pool_length']);
    $swim_style  = mysqli_real_escape_string($koneksi, $_POST['swim_style']);
    $distance    = mysqli_real_escape_string($koneksi, $_POST['distance']);
    
    $menit       = (int)$_POST['menit'];
    $detik       = (int)$_POST['detik'];
    $milidetik   = (int)$_POST['milidetik'];
    
    $time_formatted = sprintf("%02d:%02d.%02d", $menit, $detik, $milidetik);
    $time_ms = ($menit * 60000) + ($detik * 1000) + ($milidetik * 10);
    $notes       = mysqli_real_escape_string($koneksi, $_POST['notes']);
    $recorded_by = $_SESSION['user_id'] ?? 0;

    $q = mysqli_query($koneksi, "UPDATE performa SET member_id='$member_id', gaya_renang='$swim_style', jarak='$distance', tipe_kolam='$pool_length', waktu_formatted='$time_formatted', waktu_ms='$time_ms', tanggal_rekor='$record_date', catatan='$notes', recorded_by='$recorded_by' WHERE id='$id'");
    
    if($q) {
        header("location:performa.php?pesan=sukses_edit");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>