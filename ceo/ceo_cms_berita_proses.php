<?php
include '../includes/koneksi.php';

$uploadDir = __DIR__ . '/../admin/uploads/';
if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

if(isset($_POST['tambah'])){
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $tanggal  = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $konten   = mysqli_real_escape_string($koneksi, $_POST['konten']);
    $cabang   = 'Umum';

    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        if(validasi_gambar($_FILES['gambar'])) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('news_') . '.' . $ext;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename)) {
                $gambar = 'uploads/' . $filename;
            }
        } else {
            header("location:ceo_cms_berita.php?pesan=gagal&msg=FormatGambarTidakValid"); exit;
        }
    }

    $q = mysqli_query($koneksi, "INSERT INTO berita (kategori, judul, tanggal, isi, cabang, gambar) VALUES ('$kategori', '$judul', '$tanggal', '$konten', '$cabang', '$gambar')");
    if($q) { header("location:ceo_cms_berita.php?pesan=sukses_tambah"); }
    else { header("location:ceo_cms_berita.php?pesan=gagal"); }
}

if(isset($_POST['edit'])){
    $id       = mysqli_real_escape_string($koneksi, $_POST['id']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $tanggal  = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $konten   = mysqli_real_escape_string($koneksi, $_POST['konten']);

    $q_gambar = "";
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        if(validasi_gambar($_FILES['gambar'])) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('news_') . '.' . $ext;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename)) {
                $gambar = 'uploads/' . $filename;
                $q_gambar = ", gambar='$gambar'";
            }
        } else {
            header("location:ceo_cms_berita.php?pesan=gagal&msg=FormatGambarTidakValid"); exit;
        }
    }

    $q = mysqli_query($koneksi, "UPDATE berita SET kategori='$kategori', judul='$judul', tanggal='$tanggal', isi='$konten' $q_gambar WHERE id='$id'");
    if($q) { header("location:ceo_cms_berita.php?pesan=sukses_edit"); }
    else { header("location:ceo_cms_berita.php?pesan=gagal"); }
}

if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $q = mysqli_query($koneksi, "DELETE FROM berita WHERE id='$id'");
    if($q) { header("location:ceo_cms_berita.php?pesan=sukses_hapus"); }
    else { header("location:ceo_cms_berita.php?pesan=gagal"); }
}
?>
