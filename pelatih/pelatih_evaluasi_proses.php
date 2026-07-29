<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelatih') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';

if (isset($_POST['simpan_evaluasi'])) {
    
    $pelatih_id = (int)$_SESSION['user_id'];
    $member_id = (int)$_POST['member_id'];
    $tanggal_evaluasi = bersihkan_input($_POST['tanggal_evaluasi']);
    
    // Validasi range 1-5
    $postur_streamline = (int)$_POST['postur_streamline'];
    $teknik_turn = (int)$_POST['teknik_turn'];
    $teknik_start = (int)$_POST['teknik_start'];
    $disiplin = (int)$_POST['disiplin'];
    $catatan = bersihkan_input($_POST['catatan_pelatih']);
    
    // Batasi nilai agar tidak ada yang iseng mengubah HTML
    $postur_streamline = max(1, min(5, $postur_streamline));
    $teknik_turn = max(1, min(5, $teknik_turn));
    $teknik_start = max(1, min(5, $teknik_start));
    $disiplin = max(1, min(5, $disiplin));
    
    // Cek apakah sudah ada evaluasi di tanggal yang sama untuk atlet ini
    $q_cek = mysqli_query($koneksi, "SELECT id FROM evaluasi_kualitatif WHERE member_id = $member_id AND tanggal_evaluasi = '$tanggal_evaluasi'");
    
    if (mysqli_num_rows($q_cek) > 0) {
        $row = mysqli_fetch_assoc($q_cek);
        $eval_id = $row['id'];
        
        // Update
        $stmt = mysqli_prepare($koneksi, "UPDATE evaluasi_kualitatif SET postur_streamline=?, teknik_turn=?, teknik_start=?, disiplin=?, catatan_pelatih=?, pelatih_id=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "iiiisii", $postur_streamline, $teknik_turn, $teknik_start, $disiplin, $catatan, $pelatih_id, $eval_id);
    } else {
        // Insert
        $stmt = mysqli_prepare($koneksi, "INSERT INTO evaluasi_kualitatif (member_id, pelatih_id, tanggal_evaluasi, postur_streamline, teknik_turn, teknik_start, disiplin, catatan_pelatih) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iisiiiis", $member_id, $pelatih_id, $tanggal_evaluasi, $postur_streamline, $teknik_turn, $teknik_start, $disiplin, $catatan);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['pesan'] = "Berhasil: Evaluasi kualitatif atlet telah tersimpan.";
    } else {
        $_SESSION['pesan'] = "Gagal: Terjadi kesalahan saat menyimpan data.";
    }
    
    mysqli_stmt_close($stmt);
    header("Location: pelatih_input_evaluasi.php");
    exit;
}

header("Location: pelatih_input_evaluasi.php");
exit;
?>
