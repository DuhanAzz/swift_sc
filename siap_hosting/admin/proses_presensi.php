<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan_presensi'])){
    $tanggal = $_POST['tanggal'];
    $statuses = $_POST['status']; // Ini array [atlet_id => status]
    $keterangan = $_POST['keterangan']; // Ini array [atlet_id => keterangan]

    foreach($statuses as $atlet_id => $status){
        $ket = $keterangan[$atlet_id] ?? '';
        
        // Gunakan composite ID untuk menghindari duplikasi absensi atlet yang sama di hari yang sama
        $docId = $atlet_id . '_' . $tanggal;
        try {
            $database->setDocument('presensi', $docId, [
                'atlet_id' => $atlet_id,
                'tanggal' => $tanggal,
                'status' => $status,
                'keterangan' => $ket,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}
    }
    
    header("location:presensi.php?tanggal=$tanggal&pesan=sukses_simpan");
}
?>