<?php
include '../includes/koneksi.php';

$uploadDir = __DIR__ . '/../admin/uploads/';
if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

if(isset($_POST['tambah'])){
    $kategori = $_POST['kategori'];
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];
    $konten = $_POST['konten'];
    $cabang = 'Umum';

    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $filename = uniqid('news_') . '.' . $ext;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename)) {
            $gambar = 'uploads/' . $filename;
        }
    }

    try {
        $database->newDocument('berita', [
            'kategori' => $kategori,
            'judul' => $judul,
            'tanggal' => $tanggal,
            'konten' => $konten,
            'cabang' => $cabang,
            'gambar' => $gambar,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        header("location:ceo_cms_berita.php?pesan=sukses_tambah");
    } catch (\Exception $e) { header("location:ceo_cms_berita.php?pesan=gagal"); }
}

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $kategori = $_POST['kategori'];
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];
    $konten = $_POST['konten'];

    $dataUpdate = [
        'kategori' => $kategori,
        'judul' => $judul,
        'tanggal' => $tanggal,
        'konten' => $konten
    ];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $filename = uniqid('news_') . '.' . $ext;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename)) {
            $dataUpdate['gambar'] = 'uploads/' . $filename;
        }
    }

    try {
        $database->setDocument('berita', $id, $dataUpdate);
        header("location:ceo_cms_berita.php?pesan=sukses_edit");
    } catch (\Exception $e) { header("location:ceo_cms_berita.php?pesan=gagal"); }
}

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    try {
        $database->deleteDocument('berita', $id);
        header("location:ceo_cms_berita.php?pesan=sukses_hapus");
    } catch (\Exception $e) { header("location:ceo_cms_berita.php?pesan=gagal"); }
}
?>
