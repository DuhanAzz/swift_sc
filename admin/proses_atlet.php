<?php
include '../includes/koneksi.php';

// PROSES TAMBAH
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    // Normalize jenis_kelamin to match enum('L','P')
    if($jenis_kelamin == 'Laki-laki' || strtolower($jenis_kelamin) == 'laki-laki') $jenis_kelamin = 'L';
    if($jenis_kelamin == 'Perempuan' || strtolower($jenis_kelamin) == 'perempuan') $jenis_kelamin = 'P';
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $id_kolam = mysqli_real_escape_string($koneksi, $_POST['id_kolam']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir'] ?? date('Y-m-d'));
    $tanggal_gabung = date('Y-m-d');
    
    $tingkatan_kelas = isset($_POST['tingkatan_kelas']) ? mysqli_real_escape_string($koneksi, $_POST['tingkatan_kelas']) : 'Pemula';
    $pelatih_id = !empty($_POST['pelatih_id']) ? "'" . mysqli_real_escape_string($koneksi, $_POST['pelatih_id']) . "'" : "NULL";
    $sekolah = isset($_POST['sekolah']) ? mysqli_real_escape_string($koneksi, $_POST['sekolah']) : '';

    // Auto-generate NIA: SWF-YYYY-XXXX
    $tahun = date('Y');
    $q_last = mysqli_query($koneksi, "SELECT id FROM member ORDER BY id DESC LIMIT 1");
    $next_id = 1;
    if($q_last && $row = mysqli_fetch_assoc($q_last)) {
        $next_id = $row['id'] + 1;
    }
    $nia = 'SWF-' . $tahun . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

    $q = mysqli_query($koneksi, "INSERT INTO member (nia, nama, jenis_kelamin, no_hp, tanggal_lahir, cabang_id, tanggal_gabung, tingkatan_kelas, pelatih_id, sekolah) VALUES ('$nia', '$nama', '$jenis_kelamin', '$no_hp', '$tanggal_lahir', '$id_kolam', '$tanggal_gabung', '$tingkatan_kelas', $pelatih_id, '$sekolah')");
    if($q) {
        header("location:atlet.php?pesan=sukses_tambah");
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}

// PROSES EDIT
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    if($jenis_kelamin == 'Laki-laki' || strtolower($jenis_kelamin) == 'laki-laki') $jenis_kelamin = 'L';
    if($jenis_kelamin == 'Perempuan' || strtolower($jenis_kelamin) == 'perempuan') $jenis_kelamin = 'P';
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $id_kolam = mysqli_real_escape_string($koneksi, $_POST['id_kolam']);
    
    $tingkatan_kelas = isset($_POST['tingkatan_kelas']) ? mysqli_real_escape_string($koneksi, $_POST['tingkatan_kelas']) : 'Pemula';
    $pelatih_id = !empty($_POST['pelatih_id']) ? "'" . mysqli_real_escape_string($koneksi, $_POST['pelatih_id']) . "'" : "NULL";
    $sekolah = isset($_POST['sekolah']) ? mysqli_real_escape_string($koneksi, $_POST['sekolah']) : '';

    $q = mysqli_query($koneksi, "UPDATE member SET nama='$nama', jenis_kelamin='$jenis_kelamin', no_hp='$no_hp', cabang_id='$id_kolam', tingkatan_kelas='$tingkatan_kelas', pelatih_id=$pelatih_id, sekolah='$sekolah' WHERE id='$id' ");
    if($q) {
        header("location:atlet.php?pesan=sukses_edit");
    } else {
        echo "Gagal update data: " . mysqli_error($koneksi);
    }
}
?>