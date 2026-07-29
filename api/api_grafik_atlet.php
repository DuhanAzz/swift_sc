<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelatih') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include '../includes/koneksi.php';

$action = $_GET['action'] ?? '';

// ACTION 1: Mendapatkan tren progresi (Chart 1)
if ($action == 'get_trend') {
    $atlet_id = (int)($_GET['atlet_id'] ?? 0);
    $gaya = bersihkan_input($_GET['gaya'] ?? '');
    
    if (!$atlet_id || !$gaya) {
        echo json_encode(['error' => 'Parameter tidak lengkap']);
        exit;
    }
    
    // Ambil performa lomba
    $query = "SELECT p.id, p.waktu_ms, p.waktu_formatted, p.tanggal_rekor, p.jarak, p.lintasan,
                     COALESCE(e.nama_event, 'Latihan/Time Trial') as nama_event
              FROM performa p
              LEFT JOIN kalender_event e ON p.event_id = e.id
              WHERE p.member_id = ? AND p.gaya_renang = ?
              ORDER BY p.tanggal_rekor ASC, p.id ASC";
              
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "is", $atlet_id, $gaya);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $labels = [];
    $data_ms = [];
    $raw_data = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $label = date('d/M/y', strtotime($row['tanggal_rekor'])) . ' - ' . $row['nama_event'];
        $labels[] = $label;
        $data_ms[] = (int)$row['waktu_ms'];
        $raw_data[] = $row;
    }
    
    echo json_encode([
        'labels' => $labels,
        'data' => $data_ms,
        'raw_data' => $raw_data
    ]);
    exit;
}

// ACTION 2: Mendapatkan detail split time (Chart 2)
if ($action == 'get_splits') {
    $performa_id = (int)($_GET['performa_id'] ?? 0);
    
    if (!$performa_id) {
        echo json_encode(['error' => 'ID performa tidak valid']);
        exit;
    }
    
    $query = "SELECT jarak_split, waktu_lap_ms, waktu_lap_format 
              FROM performa_splits 
              WHERE performa_id = ? 
              ORDER BY jarak_split ASC";
              
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $performa_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $labels = [];
    $data_ms = [];
    $data_format = [];
    
    // Untuk menghitung lap time murni (jika waktu_lap_ms adalah akumulasi)
    // Tapi berdasarkan spesifikasi user: "Sumbu Y: Waktu murni PER SPLIT (bukan waktu akumulasi). Ambil dari waktu_lap_ms."
    // Asumsi input pelatih sudah berupa waktu murni lap, atau akumulasi? 
    // Berdasarkan flow pelatih_input_lomba_proses.php, kita menyimpan apa yang diinput pelatih.
    
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['jarak_split'] . 'm';
        $data_ms[] = (int)$row['waktu_lap_ms'];
        $data_format[] = $row['waktu_lap_format'];
    }
    
    echo json_encode([
        'labels' => $labels,
        'data' => $data_ms,
        'format' => $data_format
    ]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
