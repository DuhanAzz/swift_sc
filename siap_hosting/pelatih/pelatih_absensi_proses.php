<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['simpan_presensi'])){
    $tanggal = $_POST['tanggal'];
    $status_array = $_POST['status'];
    $coach_pool_id = $_SESSION['pool_id'] ?? '';
    
    try {
        // Ambil data presensi yang sudah ada hari ini untuk pool ini
        $existing = [];
        $pDocs = $database->getDocuments('presensi');
        foreach ($pDocs as $p) {
            if (($p['tanggal'] ?? '') == $tanggal && ($p['pool_id'] ?? '') == $coach_pool_id) {
                $existing[$p['member_id']] = $p['id'];
            }
        }

        // Looping data dari form
        foreach($status_array as $member_id => $status) {
            $data = [
                'member_id' => $member_id,
                'tanggal' => $tanggal,
                'status' => $status,
                'pool_id' => $coach_pool_id,
                'recorded_by' => $_SESSION['email']
            ];

            if (isset($existing[$member_id])) {
                // Update
                $database->setDocument('presensi', $existing[$member_id], $data);
            } else {
                // Create new
                $database->newDocument('presensi', $data);
            }
        }
        header("location:pelatih_absensi.php?pesan=sukses");
    } catch (\Exception $e) { 
        header("location:pelatih_absensi.php?pesan=gagal"); 
    }
}
?>
