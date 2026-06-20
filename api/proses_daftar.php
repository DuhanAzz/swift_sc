<?php
error_reporting(0);
header('Content-Type: application/json');

require_once '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Baca data JSON jika dikirim menggunakan fetch dengan json body
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    // Menangani data apakah dikirim via JSON atau Form Data standar
    $nama          = isset($input['nama']) ? $input['nama'] : (isset($_POST['nama']) ? $_POST['nama'] : '');
    $jenis_kelamin = isset($input['jenis_kelamin']) ? $input['jenis_kelamin'] : (isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '');
    $no_hp         = isset($input['no_hp']) ? $input['no_hp'] : (isset($_POST['no_hp']) ? $_POST['no_hp'] : '');
    $sekolah       = isset($input['sekolah']) ? $input['sekolah'] : (isset($_POST['sekolah']) ? $_POST['sekolah'] : '');
    $tanggal_lahir = isset($input['tanggal_lahir']) ? $input['tanggal_lahir'] : (isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '');
    $id_kolam      = isset($input['id_kolam']) ? $input['id_kolam'] : (isset($_POST['id_kolam']) ? $_POST['id_kolam'] : '');
    $tingkatan_kelas = isset($input['tingkatan_kelas']) ? $input['tingkatan_kelas'] : (isset($_POST['tingkatan_kelas']) ? $_POST['tingkatan_kelas'] : '');

    if (empty($nama) || empty($jenis_kelamin) || empty($tanggal_lahir) || empty($id_kolam) || empty($tingkatan_kelas)) {
        echo json_encode(["status" => "gagal", "message" => "Mohon lengkapi semua data wajib."]);
        exit;
    }

    if (function_exists('bersihkan_input')) {
        $nama          = bersihkan_input($nama);
        $jenis_kelamin = bersihkan_input($jenis_kelamin);
        $no_hp         = bersihkan_input($no_hp);
        $sekolah       = bersihkan_input($sekolah);
        $tanggal_lahir = bersihkan_input($tanggal_lahir);
        $id_kolam      = bersihkan_input($id_kolam);
        $tingkatan_kelas = bersihkan_input($tingkatan_kelas);
    }
    
    $tgl_gabung = date('Y-m-d');

    try {
        // Menggunakan Prepared Statement untuk keamanan
        $stmt = $koneksi->prepare("INSERT INTO calon_member (nama, jenis_kelamin, no_hp, sekolah, tanggal_lahir, cabang_id, tingkatan_kelas, tanggal_daftar, payment_status, status_approval) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Unpaid', 'Pending')");
        
        if ($stmt) {
            $stmt->bind_param("sssssiss", $nama, $jenis_kelamin, $no_hp, $sekolah, $tanggal_lahir, $id_kolam, $tingkatan_kelas, $tgl_gabung);
            if ($stmt->execute()) {
                echo json_encode(["status" => "sukses", "message" => "Pendaftaran Berhasil! Data Anda sudah terkirim. Admin kami akan segera menghubungi Anda."]);
            } else {
                echo json_encode(["status" => "gagal", "message" => "Gagal menyimpan data."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["status" => "gagal", "message" => "Kesalahan query database."]);
        }
    } catch (\Exception $e) {
        echo json_encode(["status" => "gagal", "message" => "Terjadi kesalahan server."]);
    }
} else {
    echo json_encode(["status" => "gagal", "message" => "Metode tidak valid."]);
}
?>
