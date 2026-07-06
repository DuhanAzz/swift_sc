<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan'])){
    $record_date = $_POST['record_date'];
    $member_id   = $_POST['member_id'];
    $swim_style  = $_POST['swim_style'];
    $distance    = $_POST['distance'];
    
    // Formatting time
    $m = str_pad((int)$_POST['time_m'], 2, '0', STR_PAD_LEFT);
    $s = str_pad((int)$_POST['time_s'], 2, '0', STR_PAD_LEFT);
    $ms = str_pad((int)$_POST['time_ms'], 2, '0', STR_PAD_LEFT);
    $time_formatted = "{$m}:{$s}.{$ms}";

    $time_in_ms = ((int)$_POST['time_m'] * 60000) + ((int)$_POST['time_s'] * 1000) + ((int)$_POST['time_ms'] * 10);
    $notes = $_POST['notes'];

    $coach_pool_id = $_SESSION['pool_id'] ?? '';
    $coach_name = $_SESSION['name'] ?? 'Pelatih';

    try {
        $database->newDocument('performances', [
            'member_id' => $member_id,
            'pool_id' => $coach_pool_id,
            'swim_style' => $swim_style,
            'distance' => $distance,
            'time_formatted' => $time_formatted,
            'time_in_ms' => $time_in_ms,
            'record_date' => $record_date,
            'notes' => $notes,
            'recorded_by_name' => $coach_name,
            'recorded_by_email' => $_SESSION['email'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:pelatih_performa.php?pesan=sukses");
    } catch (\Exception $e) { 
        header("location:pelatih_performa.php?pesan=gagal"); 
    }
}
?>
