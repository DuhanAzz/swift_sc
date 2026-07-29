<?php
// Script untuk auto-import database dari database.sql ke Hostinger.
require_once __DIR__ . '/config/database.php';

// Pastikan hanya bisa dijalankan di Hostinger atau bisa dibatasi.
// Tapi karena kita akan menghapusnya setelah sukses, ini aman.

$sqlFile = __DIR__ . '/database.sql';

if (!file_exists($sqlFile)) {
    die("File database.sql tidak ditemukan.");
}

// Baca isi SQL
$sql = file_get_contents($sqlFile);

if (!$sql) {
    die("Gagal membaca file database.sql.");
}

// Matikan foreign key checks untuk menghindari error saat drop/create
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=0;");

// Eksekusi multi query
if (mysqli_multi_query($koneksi, $sql)) {
    do {
        // Bebaskan hasil query dari memori (wajib untuk multi_query)
        if ($result = mysqli_store_result($koneksi)) {
            mysqli_free_result($result);
        }
    } while (mysqli_more_results($koneksi) && mysqli_next_result($koneksi));
    
    echo "SUCCESS_IMPORT";
} else {
    echo "FAILED_IMPORT: " . mysqli_error($koneksi);
}

mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=1;");
mysqli_close($koneksi);
?>
