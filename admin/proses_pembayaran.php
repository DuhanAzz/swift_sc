<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
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
        $jml = (float)($jumlah[$atlet_id] ?? 0);
        $ket_input = mysqli_real_escape_string($koneksi, $keterangan[$atlet_id] ?? '');
        $metode = mysqli_real_escape_string($koneksi, $_POST['metode'][$atlet_id] ?? 'Tunai');
        
        $paket = (int)($_POST['limit_hadir'] ?? 0);
        
        if($status == 'Lunas' && $paket <= 0) {
            continue; // Skip member if Lunas but no package selected
        }
        
        // Parse keterangan (which is type="month", format YYYY-MM) for ledger month
        $pay_bulan = $bulan;
        $pay_tahun = $tahun;
        $ket_text = "";
        
        if(preg_match('/^(\d{4})-(\d{2})$/', $ket_input, $matches)) {
            $pay_tahun = $matches[1];
            $pay_bulan = $matches[2];
            $ket_text = "[$metode] Pemb. Paket $paket Sesi ($ket_input)";
        } else {
            $ket_text = "[$metode] $ket_input";
        }
        
        $tgl_bayar = ($status == 'Lunas') ? date('Y-m-d H:i:s') : 'NULL';
        $tgl_bayar_val = ($status == 'Lunas') ? "'$tgl_bayar'" : "NULL";
        
        // Cek apakah ada tagihan 'Belum Bayar' yang bisa kita update
        $cek = mysqli_query($koneksi, "SELECT id FROM pembayaran WHERE member_id='$atlet_id' AND bulan='$pay_bulan' AND tahun='$pay_tahun' AND status='Belum Bayar' ORDER BY id DESC LIMIT 1");
        
        if($status == 'Lunas') {
            $jml_sesi = $paket;
            $tgl_awal = "NULL"; // Pre-paid, we don't have exact cover dates
            $tgl_akhir = "NULL";
            $detail_tanggal_val = "NULL";
            
            if(mysqli_num_rows($cek) > 0) {
                // Update tagihan kosong
                $row = mysqli_fetch_assoc($cek);
                $id = $row['id'];
                mysqli_query($koneksi, "UPDATE pembayaran SET status='$status', jumlah_bayar='$jml', keterangan='$ket_text', tgl_bayar=$tgl_bayar_val, jumlah_sesi_terbayar=$jml_sesi, cover_tgl_awal=$tgl_awal, cover_tgl_akhir=$tgl_akhir, detail_tanggal=$detail_tanggal_val WHERE id='$id'");
            } else {
                // Insert transaksi Lunas baru
                mysqli_query($koneksi, "INSERT INTO pembayaran (member_id, bulan, tahun, status, tgl_bayar, jumlah_bayar, keterangan, jumlah_sesi_terbayar, cover_tgl_awal, cover_tgl_akhir, detail_tanggal) VALUES ('$atlet_id', '$pay_bulan', '$pay_tahun', '$status', $tgl_bayar_val, '$jml', '$ket_text', $jml_sesi, $tgl_awal, $tgl_akhir, $detail_tanggal_val)");
            }
            
            // Otomatisasi Pemasukan SPP ke Arus Kas
            $admin_id = intval($_SESSION['user_id'] ?? 0);
            $cabang_id = intval($_SESSION['pool_id'] ?? 0);
            $nominal_spp = $jml; // Menggunakan nominal yang dihitung (paket * template_nominal)
            
            if($nominal_spp > 0 && $cabang_id > 0) {
                // Ambil nama atlet
                $nama_atlet = "Atlet";
                $q_nama = mysqli_query($koneksi, "SELECT nama FROM member WHERE id='$atlet_id'");
                if($q_nama && $r_nama = mysqli_fetch_assoc($q_nama)) $nama_atlet = $r_nama['nama'];
                
                $ket_kas = "[$metode] Pemb. Paket Sesi ($paket Pertemuan) a/n " . mysqli_real_escape_string($koneksi, $nama_atlet);
                $tgl_sekarang = date('Y-m-d');
                
                mysqli_query($koneksi, "INSERT INTO arus_kas (cabang_id, jenis, category, nominal, keterangan, tanggal, user_id) 
                                        VALUES ('$cabang_id', 'Pemasukan', 'SPP', '$nominal_spp', '$ket_kas', '$tgl_sekarang', '$admin_id')");
            }
        } else {
            // Jika Status 'Belum Bayar'
            if(mysqli_num_rows($cek) > 0) {
                $row = mysqli_fetch_assoc($cek);
                $id = $row['id'];
                mysqli_query($koneksi, "UPDATE pembayaran SET status='$status', jumlah_bayar='$jml', keterangan='$ket_text', tgl_bayar=$tgl_bayar_val WHERE id='$id'");
            } else {
                if($jml > 0 || !empty($ket_text)) {
                    mysqli_query($koneksi, "INSERT INTO pembayaran (member_id, bulan, tahun, status, tgl_bayar, jumlah_bayar, keterangan) VALUES ('$atlet_id', '$pay_bulan', '$pay_tahun', '$status', $tgl_bayar_val, '$jml', '$ket_text')");
                }
            }
        }
    }
    
    header("location:pembayaran.php?tanggal_mulai=$tgl_mulai&tanggal_akhir=$tgl_akhir&pesan=sukses");
}
?>
