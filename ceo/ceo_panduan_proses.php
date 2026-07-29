<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ceo') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';

// 1. PROSES UPLOAD PERIODISASI
if (isset($_POST['upload_periodisasi'])) {
    $judul = bersihkan_input($_POST['judul_dokumen']);
    $uploader_id = $_SESSION['user_id'];
    
    if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
        $file = $_FILES['file_pdf'];
        
        // Validasi Ekstensi & Ukuran
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $max_size = 5 * 1024 * 1024; // 5 MB
        
        if ($ext !== 'pdf') {
            $_SESSION['pesan'] = "Gagal: Format file harus PDF.";
            header("Location: ceo_panduan_event.php");
            exit;
        }
        
        if ($file['size'] > $max_size) {
            $_SESSION['pesan'] = "Gagal: Ukuran file maksimal 5MB.";
            header("Location: ceo_panduan_event.php");
            exit;
        }
        
        // Generate nama file unik
        $nama_file_baru = 'periodisasi_' . time() . '_' . rand(1000, 9999) . '.pdf';
        $path_upload = '../admin/uploads/periodisasi/' . $nama_file_baru;
        
        if (move_uploaded_file($file['tmp_name'], $path_upload)) {
            // Update dokumen lama menjadi arsip
            mysqli_query($koneksi, "UPDATE dokumen_periodisasi SET status = 'Arsip' WHERE status = 'Aktif'");
            
            // Insert dokumen baru
            $stmt = mysqli_prepare($koneksi, "INSERT INTO dokumen_periodisasi (judul, file_pdf, status, uploader_id) VALUES (?, ?, 'Aktif', ?)");
            mysqli_stmt_bind_param($stmt, "ssi", $judul, $nama_file_baru, $uploader_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan'] = "Berhasil: Dokumen periodisasi baru telah aktif.";
            } else {
                $_SESSION['pesan'] = "Gagal menyimpan data ke database.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['pesan'] = "Gagal: Terjadi kesalahan saat memindahkan file.";
        }
    } else {
        $_SESSION['pesan'] = "Gagal: File PDF tidak ditemukan atau rusak.";
    }
    header("Location: ceo_panduan_event.php");
    exit;
}

// 2. PROSES HAPUS DOKUMEN
if (isset($_POST['hapus_dokumen'])) {
    $id = (int) $_POST['id'];
    $file_pdf = $_POST['file_pdf'];
    
    $path = '../admin/uploads/periodisasi/' . $file_pdf;
    if (file_exists($path) && !empty($file_pdf)) {
        unlink($path);
    }
    
    $stmt = mysqli_prepare($koneksi, "DELETE FROM dokumen_periodisasi WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    $_SESSION['pesan'] = "Berhasil: Dokumen dihapus.";
    header("Location: ceo_panduan_event.php");
    exit;
}

// 3. PROSES TAMBAH EVENT
if (isset($_POST['tambah_event'])) {
    $nama_event = bersihkan_input($_POST['nama_event']);
    $tanggal_mulai = bersihkan_input($_POST['tanggal_mulai']);
    $tanggal_selesai = bersihkan_input($_POST['tanggal_selesai']);
    $lokasi = bersihkan_input($_POST['lokasi']);
    $deskripsi = bersihkan_input($_POST['deskripsi']);
    
    if ($tanggal_selesai < $tanggal_mulai) {
        $_SESSION['pesan'] = "Gagal: Tanggal selesai tidak boleh lebih awal dari tanggal mulai.";
        header("Location: ceo_panduan_event.php");
        exit;
    }
    
    $stmt = mysqli_prepare($koneksi, "INSERT INTO kalender_event (nama_event, tanggal_mulai, tanggal_selesai, lokasi, deskripsi) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $nama_event, $tanggal_mulai, $tanggal_selesai, $lokasi, $deskripsi);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['pesan'] = "Berhasil: Event kalender ditambahkan.";
    } else {
        $_SESSION['pesan'] = "Gagal menambah event.";
    }
    mysqli_stmt_close($stmt);
    header("Location: ceo_panduan_event.php");
    exit;
}

// 4. PROSES HAPUS EVENT
if (isset($_POST['hapus_event'])) {
    $id = (int) $_POST['id_event'];
    
    $stmt = mysqli_prepare($koneksi, "DELETE FROM kalender_event WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    $_SESSION['pesan'] = "Berhasil: Event kalender dihapus.";
    header("Location: ceo_panduan_event.php");
    exit;
}

// Jika akses langsung ke file ini
header("Location: ceo_panduan_event.php");
exit;
?>
