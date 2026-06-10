<?php
include '../includes/koneksi.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Atlet_SwiftSC.xls");
?>
<center>
    <h2>DATA ATLET SWIFT SWIMMING CLUB</h2>
</center>
<table border="1">
    <tr>
        <th>No</th>
        <th>Nama Lengkap</th>
        <th>Tempat, Tgl Lahir</th>
        <th>Jenis Kelamin</th>
        <th>Alamat</th>
    </tr>
    <?php
    $no = 1;
    $data = mysqli_query($koneksi, "SELECT * FROM atlet ORDER BY nama ASC");
    while($d = mysqli_fetch_array($data)){
    ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= $d['nama']; ?></td>
        <td><?= $d['tempat_lahir']; ?>, <?= $d['tanggal_lahir']; ?></td>
        <td><?= $d['jenis_kelamin']; ?></td>
        <td><?= $d['alamat']; ?></td>
    </tr>
    <?php } ?>
</table>