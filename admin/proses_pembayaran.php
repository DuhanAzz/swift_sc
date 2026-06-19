<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan_bayar'])){
    $bulan = mysqli_real_escape_string($koneksi, $_POST['bulan']);
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $statuses = $_POST['status']; // array [atlet_id => status]
    $jumlah = $_POST['jumlah']; // array [atlet_id => jumlah]
    $keterangan = $_POST['keterangan']; // array [atlet_id => keterangan]

    foreach($statuses as $atlet_id => $status){
        $atlet_id = mysqli_real_escape_string($koneksi, $atlet_id);
        $status = mysqli_real_escape_string($koneksi, $status);
        $jml = (int)($jumlah[$atlet_id] ?? 0);
        $ket = mysqli_real_escape_string($koneksi, $keterangan[$atlet_id] ?? '');
        
        $tgl_bayar = ($status == 'Lunas') ? date('Y-m-d H:i:s') : 'NULL';
        $tgl_bayar_val = ($status == 'Lunas') ? "'$tgl_bayar'" : "NULL";
        
        $cek = mysqli_query($koneksi, "SELECT id, status as old_status, tgl_bayar as old_tgl FROM pembayaran WHERE member_id='$atlet_id' AND bulan='$bulan' AND tahun='$tahun'");
        if(mysqli_num_rows($cek) > 0) {
            $row = mysqli_fetch_assoc($cek);
            $id = $row['id'];
            // Only update tgl_bayar if status changes TO Lunas (don't reset if already Lunas)
            if($status == 'Lunas' && $row['old_status'] != 'Lunas') {
                $tgl_bayar_val = "'" . date('Y-m-d H:i:s') . "'";
            } elseif($status == 'Lunas' && !empty($row['old_tgl'])) {
                $tgl_bayar_val = "'" . $row['old_tgl'] . "'";
            } else {
                $tgl_bayar_val = "NULL";
            }
            mysqli_query($koneksi, "UPDATE pembayaran SET status='$status', jumlah_bayar='$jml', keterangan='$ket', tgl_bayar=$tgl_bayar_val WHERE id='$id'");
        } else {
            mysqli_query($koneksi, "INSERT INTO pembayaran (member_id, bulan, tahun, status, tgl_bayar, jumlah_bayar, keterangan) VALUES ('$atlet_id', '$bulan', '$tahun', '$status', $tgl_bayar_val, '$jml', '$ket')");
        }
    }
    
    header("location:pembayaran.php?bulan=$bulan&tahun=$tahun&pesan=sukses");
}
?>
