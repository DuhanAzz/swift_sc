<?php
session_start();
include '../includes/koneksi.php';

// PROSES TAMBAH REKOR BARU
if(isset($_POST['tambah'])){
    $member_id   = $_POST['member_id'];
    $record_date = $_POST['record_date'];
    $pool_length = $_POST['pool_length'];
    $swim_style  = $_POST['swim_style'];
    $distance    = $_POST['distance'];
    
    // Menangkap input waktu
    $menit       = (int)$_POST['menit'];
    $detik       = (int)$_POST['detik'];
    $milidetik   = (int)$_POST['milidetik'];
    
    $time_formatted = sprintf("%02d:%02d.%02d", $menit, $detik, $milidetik);
    $time_ms = ($menit * 60000) + ($detik * 1000) + ($milidetik * 10);
    $notes       = $_POST['notes'];

    try {
        $database->newDocument('performances', [
            'member_id' => $member_id,
            'record_date' => $record_date,
            'pool_length' => $pool_length,
            'swim_style' => $swim_style,
            'distance' => $distance,
            'time_formatted' => $time_formatted,
            'time_ms' => $time_ms,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:performa.php?pesan=sukses_tambah");
    } catch (\Exception $e) {
        echo "Gagal: " . $e->getMessage();
    }
}

// PROSES EDIT REKOR WAKTU
if(isset($_POST['edit'])){
    $id          = $_POST['id'];
    $member_id   = $_POST['member_id'];
    $record_date = $_POST['record_date'];
    $pool_length = $_POST['pool_length'];
    $swim_style  = $_POST['swim_style'];
    $distance    = $_POST['distance'];
    
    $menit       = (int)$_POST['menit'];
    $detik       = (int)$_POST['detik'];
    $milidetik   = (int)$_POST['milidetik'];
    
    $time_formatted = sprintf("%02d:%02d.%02d", $menit, $detik, $milidetik);
    $time_ms = ($menit * 60000) + ($detik * 1000) + ($milidetik * 10);
    $notes       = $_POST['notes'];

    try {
        $database->setDocument('performances', $id, [
            'member_id' => $member_id,
            'record_date' => $record_date,
            'pool_length' => $pool_length,
            'swim_style' => $swim_style,
            'distance' => $distance,
            'time_formatted' => $time_formatted,
            'time_ms' => $time_ms,
            'notes' => $notes
        ]);
        header("location:performa.php?pesan=sukses_edit");
    } catch (\Exception $e) {
        echo "Gagal: " . $e->getMessage();
    }
}
?>