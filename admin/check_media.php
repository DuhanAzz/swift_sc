<?php
include '../includes/koneksi.php';

$used_images = ['hero_bg.jpg']; // Hardcoded

$q = mysqli_query($koneksi, "SELECT foto FROM pelatih WHERE foto != ''");
while($r = mysqli_fetch_assoc($q)) {
    $used_images[] = basename($r['foto']);
}
$q = mysqli_query($koneksi, "SELECT foto FROM cabang WHERE foto != ''");
while($r = mysqli_fetch_assoc($q)) {
    $used_images[] = basename($r['foto']);
}
$q = mysqli_query($koneksi, "SELECT gambar FROM berita WHERE gambar != ''");
while($r = mysqli_fetch_assoc($q)) {
    $used_images[] = basename($r['gambar']);
}

$files = glob("uploads/*.*");
$unused = [];
foreach($files as $f) {
    if (!in_array(basename($f), $used_images)) {
        $unused[] = $f;
    }
}
echo json_encode($unused, JSON_PRETTY_PRINT);
