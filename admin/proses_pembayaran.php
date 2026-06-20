<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan_bayar'])){
    $tgl_mulai = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai']);
    $tgl_akhir = mysqli_real_escape_string($koneksi, $_POST['tanggal_akhir']);
    
    // We use current month/year for the ledger record in 'pembayaran'
    $bulan = date('m');
    $tahun = date('Y');
    
    $statuses = $_POST['status']; // array [atlet_id => status]
    $jumlah = $_POST['jumlah']; // array [atlet_id => jumlah]
    $keterangan = $_POST['keterangan']; // array [atlet_id => keterangan]

    foreach($statuses as $atlet_id => $status){
        $atlet_id = mysqli_real_escape_string($koneksi, $atlet_id);
        $status = mysqli_real_escape_string($koneksi, $status);
        $jml = (int)($jumlah[$atlet_id] ?? 0);
        $ket = mysqli_real_escape_string($koneksi, $keterangan[$atlet_id] ?? '');
        
        $selected_ids = $_POST['selected_absensi'][$atlet_id] ?? [];
        if($status == 'Lunas' && empty($selected_ids)) {
            continue; // Skip member if Lunas but no dates selected
        }
        
        $tgl_bayar = ($status == 'Lunas') ? date('Y-m-d H:i:s') : 'NULL';
        $tgl_bayar_val = ($status == 'Lunas') ? "'$tgl_bayar'" : "NULL";
        
        // Cek apakah ada tagihan 'Belum Bayar' yang bisa kita update
        $cek = mysqli_query($koneksi, "SELECT id FROM pembayaran WHERE member_id='$atlet_id' AND bulan='$bulan' AND tahun='$tahun' AND status='Belum Bayar' ORDER BY id DESC LIMIT 1");
        
        $id_str = "";
        
        if($status == 'Lunas') {
            $id_list = [];
            foreach($selected_ids as $sid) { $id_list[] = (int)$sid; }
            $id_str = implode(',', $id_list);
            
            $jml_sesi = "NULL";
            $tgl_awal = "NULL";
            $tgl_akhir = "NULL";
            $detail_tanggal_val = "NULL";
            
            $q_abs = mysqli_query($koneksi, "SELECT COUNT(id) as jml, MIN(tanggal) as awal, MAX(tanggal) as akhir, GROUP_CONCAT(tanggal ORDER BY tanggal ASC) as all_dates FROM absensi WHERE id IN ($id_str)");
            if($q_abs && $r_abs = mysqli_fetch_assoc($q_abs)){
                if($r_abs['jml'] > 0){
                    $jml_sesi = $r_abs['jml'];
                    $tgl_awal = "'" . $r_abs['awal'] . "'";
                    $tgl_akhir = "'" . $r_abs['akhir'] . "'";
                    
                    $dates_array = explode(',', $r_abs['all_dates']);
                    $formatted_dates = [];
                    $eng_m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $ind_m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    foreach($dates_array as $d) {
                        $formatted_dates[] = str_replace($eng_m, $ind_m, date('d M', strtotime($d)));
                    }
                    $detail_tanggal_val = "'" . mysqli_real_escape_string($koneksi, implode(', ', $formatted_dates)) . "'";
                }
            }
            
            if(mysqli_num_rows($cek) > 0) {
                // Update tagihan kosong
                $row = mysqli_fetch_assoc($cek);
                $id = $row['id'];
                mysqli_query($koneksi, "UPDATE pembayaran SET status='$status', jumlah_bayar='$jml', keterangan='$ket', tgl_bayar=$tgl_bayar_val, jumlah_sesi_terbayar=$jml_sesi, cover_tgl_awal=$tgl_awal, cover_tgl_akhir=$tgl_akhir, detail_tanggal=$detail_tanggal_val WHERE id='$id'");
            } else {
                // Insert transaksi Lunas baru
                mysqli_query($koneksi, "INSERT INTO pembayaran (member_id, bulan, tahun, status, tgl_bayar, jumlah_bayar, keterangan, jumlah_sesi_terbayar, cover_tgl_awal, cover_tgl_akhir, detail_tanggal) VALUES ('$atlet_id', '$bulan', '$tahun', '$status', $tgl_bayar_val, '$jml', '$ket', $jml_sesi, $tgl_awal, $tgl_akhir, $detail_tanggal_val)");
            }
            
            // Mark attendances as Paid
            if(!empty($id_str)) {
                mysqli_query($koneksi, "UPDATE absensi SET status_bayar='Paid' WHERE id IN ($id_str)");
            }
            
            // Otomatisasi Pemasukan SPP ke Arus Kas
            $admin_id = intval($_SESSION['user_id'] ?? 0);
            $cabang_id = intval($_SESSION['pool_id'] ?? 0);
            $nominal_spp = $jml; // Menggunakan nominal yang diisi admin
            
            if($nominal_spp > 0 && $cabang_id > 0) {
                // Ambil nama atlet
                $nama_atlet = "Atlet";
                $q_nama = mysqli_query($koneksi, "SELECT nama FROM member WHERE id='$atlet_id'");
                if($q_nama && $r_nama = mysqli_fetch_assoc($q_nama)) $nama_atlet = $r_nama['nama'];
                
                $ket_kas = "Pembayaran SPP a/n " . mysqli_real_escape_string($koneksi, $nama_atlet);
                $tgl_sekarang = date('Y-m-d');
                
                mysqli_query($koneksi, "INSERT INTO arus_kas (cabang_id, jenis, category, nominal, keterangan, tanggal, user_id) 
                                        VALUES ('$cabang_id', 'Pemasukan', 'SPP', '$nominal_spp', '$ket_kas', '$tgl_sekarang', '$admin_id')");
            }
        } else {
            // Jika Status 'Belum Bayar'
            if(mysqli_num_rows($cek) > 0) {
                $row = mysqli_fetch_assoc($cek);
                $id = $row['id'];
                mysqli_query($koneksi, "UPDATE pembayaran SET status='$status', jumlah_bayar='$jml', keterangan='$ket', tgl_bayar=$tgl_bayar_val WHERE id='$id'");
            } else {
                if($jml > 0 || !empty($ket)) {
                    mysqli_query($koneksi, "INSERT INTO pembayaran (member_id, bulan, tahun, status, tgl_bayar, jumlah_bayar, keterangan) VALUES ('$atlet_id', '$bulan', '$tahun', '$status', $tgl_bayar_val, '$jml', '$ket')");
                }
            }
        }
    }
    
    header("location:pembayaran.php?tanggal_mulai=$tgl_mulai&tanggal_akhir=$tgl_akhir&pesan=sukses");
}
?>
