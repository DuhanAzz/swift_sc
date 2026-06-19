<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'ceo') { 
    exit; 
}
include '../includes/koneksi.php';

$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');
$filter_cabang = isset($_GET['cabang_id']) ? $_GET['cabang_id'] : '';

$nama_cabang_report = "Seluruh Cabang";
if(!empty($filter_cabang)) {
    $q_cab = mysqli_query($koneksi, "SELECT nama_cabang FROM cabang WHERE id='$filter_cabang'");
    if($q_cab && $r_cab = mysqli_fetch_assoc($q_cab)) {
        $nama_cabang_report = $r_cab['nama_cabang'];
    }
}

// Set Headers for Excel Download
$filename = "Laporan_Global_Arus_Kas_" . date('Ymd_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

$arus_kas = [];
$q_str = "SELECT a.*, c.nama_cabang, u.username as nama_admin 
          FROM arus_kas a 
          LEFT JOIN cabang c ON a.cabang_id = c.id 
          LEFT JOIN users u ON a.user_id = u.id 
          WHERE a.tanggal >= '$tgl_mulai' AND a.tanggal <= '$tgl_akhir'";

if(!empty($filter_cabang)) {
    $q_str .= " AND a.cabang_id='$filter_cabang'";
}
$q_str .= " ORDER BY a.tanggal ASC, a.id ASC";

$q_tabel = mysqli_query($koneksi, $q_str);
if($q_tabel) {
    while($row = mysqli_fetch_assoc($q_tabel)) {
        $arus_kas[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Global Arus Kas</title>
</head>
<body>
    <h2>Laporan Global Arus Kas (Buku Besar) - Swift SC</h2>
    <p>Cabang: <?= htmlspecialchars($nama_cabang_report) ?></p>
    <p>Periode: <?= date('d M Y', strtotime($tgl_mulai)) ?> s/d <?= date('d M Y', strtotime($tgl_akhir)) ?></p>
    
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>No</th>
                <th>Tanggal</th>
                <th>Cabang</th>
                <th>Jenis Transaksi</th>
                <th>Keterangan</th>
                <th>Pencatat</th>
                <th>Pemasukan (Rp)</th>
                <th>Pengeluaran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $tot_masuk = 0;
            $tot_keluar = 0;
            if(count($arus_kas) > 0) {
                foreach($arus_kas as $t): 
                    $is_in = ($t['jenis'] == 'Pemasukan');
                    $masuk = $is_in ? $t['nominal'] : 0;
                    $keluar = !$is_in ? $t['nominal'] : 0;
                    $tot_masuk += $masuk;
                    $tot_keluar += $keluar;
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y', strtotime($t['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($t['nama_cabang'] ?? 'Pusat') ?></td>
                    <td><?= htmlspecialchars($t['jenis']) ?></td>
                    <td><?= htmlspecialchars($t['keterangan']) ?></td>
                    <td><?= htmlspecialchars($t['nama_admin'] ?? 'Sistem') ?></td>
                    <td style="text-align:right"><?= $masuk > 0 ? $masuk : '-' ?></td>
                    <td style="text-align:right"><?= $keluar > 0 ? $keluar : '-' ?></td>
                </tr>
                <?php endforeach; 
            } else { ?>
                <tr>
                    <td colspan="8" align="center">Tidak ada transaksi.</td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #e6e6e6;">
                <td colspan="6" align="right">TOTAL</td>
                <td align="right"><?= $tot_masuk ?></td>
                <td align="right"><?= $tot_keluar ?></td>
            </tr>
            <tr style="font-weight: bold; background-color: #d9edf7;">
                <td colspan="6" align="right">SALDO AKHIR</td>
                <td colspan="2" align="center"><?= $tot_masuk - $tot_keluar ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
