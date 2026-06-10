<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

include '../includes/koneksi.php';

// Set header to force download as excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Pelatih_SwiftSC.xls");
header("Pragma: no-cache");
header("Expires: 0");

$user_role = $_SESSION['role'];
$user_cabang = $_SESSION['cabang'] ?? 'Pusat';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Export Laporan Pelatih</title>
</head>
<body>
    <h2>Laporan Data Pelatih - Swift SC</h2>
    <?php if ($user_role !== 'admin'): ?>
        <p>Cabang: <?= $user_cabang; ?></p>
    <?php else: ?>
        <p>Semua Cabang (Pusat & Cabang)</p>
    <?php endif; ?>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pelatih</th>
                <th>Cabang</th>
                <th>Jabatan</th>
                <th>Sertifikasi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q_str = "SELECT * FROM pelatih";
            if ($user_role !== 'admin') {
                $q_str .= " WHERE cabang = '$user_cabang'";
            }
            $q_str .= " ORDER BY id DESC";
            $query = mysqli_query($koneksi, $q_str);
            $no = 1;
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) :
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['nama']; ?></td>
                <td><?= $row['cabang'] ?? 'Pusat'; ?></td>
                <td><?= $row['jabatan']; ?></td>
                <td><?= $row['sertifikasi']; ?></td>
            </tr>
            <?php 
                endwhile;
            } else {
                echo '<tr><td colspan="5" style="text-align:center;">Tidak ada data pelatih</td></tr>';
            }
            ?>
        </tbody>
    </table>
</body>
</html>
