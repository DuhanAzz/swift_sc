<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan_bayar'])){
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $statuses = $_POST['status']; // array [atlet_id => status]
    $jumlah = $_POST['jumlah']; // array [atlet_id => jumlah]
    $keterangan = $_POST['keterangan']; // array [atlet_id => keterangan]

    foreach($statuses as $atlet_id => $status){
        $jml = (int)($jumlah[$atlet_id] ?? 0);
        $ket = $keterangan[$atlet_id] ?? '';
        
        // Composite ID for uniqueness per atlet per bulan per tahun
        $docId = $atlet_id . '_' . $bulan . '_' . $tahun;
        
        try {
            $database->setDocument('pembayaran', $docId, [
                'atlet_id' => $atlet_id,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status' => $status,
                'jumlah_bayar' => $jml,
                'keterangan' => $ket,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}
    }
    
    header("location:pembayaran.php?bulan=$bulan&tahun=$tahun&pesan=sukses_simpan");
}
?>
