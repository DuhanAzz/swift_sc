<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != 'admin') { 
    exit; 
}
include '../includes/koneksi.php';

$admin_pool_id = intval($_SESSION['pool_id'] ?? 0);
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');

// Set Headers for Excel Download
$filename = "Laporan_Arus_Kas_Cabang_" . date('Ymd_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

$transaksi = [];
$q_tabel = mysqli_query($koneksi, "SELECT a.*, u.name as nama_admin 
                                   FROM arus_kas a 
                                   LEFT JOIN users u ON a.user_id = u.id 
                                   WHERE a.cabang_id='$admin_pool_id' AND a.tanggal >= '$tgl_mulai' AND a.tanggal <= '$tgl_akhir' 
                                   ORDER BY a.tanggal ASC, a.id ASC");
if($q_tabel) {
    while($row = mysqli_fetch_assoc($q_tabel)) {
        $transaksi[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Arus Kas</title>
</head>
<body>
    <h2>Laporan Arus Kas (Buku Besar) Cabang</h2>
    <p>Periode: <?= date('d M Y', strtotime($tgl_mulai)) ?> s/d <?= date('d M Y', strtotime($tgl_akhir)) ?></p>
    
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>No</th>
                <th>Tanggal</th>
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
            if(count($transaksi) > 0) {
                foreach($transaksi as $t): 
                    $is_in = ($t['jenis'] == 'Pemasukan');
                    $masuk = $is_in ? $t['nominal'] : 0;
                    $keluar = !$is_in ? $t['nominal'] : 0;
                    $tot_masuk += $masuk;
                    $tot_keluar += $keluar;
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y', strtotime($t['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($t['jenis']) ?></td>
                    <td><?= htmlspecialchars($t['keterangan']) ?></td>
                    <td><?= htmlspecialchars($t['nama_admin'] ?? 'Sistem') ?></td>
                    <td style="text-align:right"><?= $masuk > 0 ? $masuk : '-' ?></td>
                    <td style="text-align:right"><?= $keluar > 0 ? $keluar : '-' ?></td>
                </tr>
                <?php endforeach; 
            } else { ?>
                <tr>
                    <td colspan="7" align="center">Tidak ada transaksi.</td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #e6e6e6;">
                <td colspan="5" align="right">TOTAL</td>
                <td align="right"><?= $tot_masuk ?></td>
                <td align="right"><?= $tot_keluar ?></td>
            </tr>
            <tr style="font-weight: bold; background-color: #d9edf7;">
                <td colspan="5" align="right">SALDO AKHIR</td>
                <td colspan="2" align="center"><?= $tot_masuk - $tot_keluar ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
