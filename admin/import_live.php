<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
include '../includes/koneksi.php';

$admin_pool_id = $_SESSION['pool_id'] ?? 1;

if(isset($_POST['import'])) {
    if($_FILES['file_csv']['error'] == 0) {
        $file = fopen($_FILES['file_csv']['tmp_name'], 'r');
        
        // Skip header
        for ($i = 0; $i < 6; $i++) {
            fgetcsv($file);
        }

        $success = 0;
        $fail = 0;
        
        while (($row = fgetcsv($file)) !== FALSE) {
            if (empty(trim($row[0]))) continue;
            
            $nama_siswa = bersihkan_input($row[4]);
            $no_hp = bersihkan_input($row[2]);
            $sekolah = bersihkan_input($row[8]);
            
            $tanggal_lahir = $row[6];
            $tl_parts = explode('/', $tanggal_lahir);
            if(count($tl_parts) == 3) {
                $tanggal_lahir_db = $tl_parts[2] . '-' . $tl_parts[1] . '-' . $tl_parts[0];
            } else {
                $tanggal_lahir_db = date('Y-m-d');
            }

            $tanggal_mendaftar = $row[11];
            $tm_parts = explode('/', $tanggal_mendaftar);
            if(count($tm_parts) == 3) {
                $tanggal_gabung_db = $tm_parts[2] . '-' . $tm_parts[1] . '-' . $tm_parts[0];
            } else {
                $tanggal_gabung_db = date('Y-m-d');
            }

            $jenis_kelamin = 'L'; // Default
            $tingkatan_kelas = 'Pemula';
            $biaya_pendaftaran = 100000;
            $biaya_bulanan = 350000;

            // Generate NIA
            $tahun = date('Y', strtotime($tanggal_gabung_db));
            $q_last = mysqli_query($koneksi, "SELECT id FROM member ORDER BY id DESC LIMIT 1");
            $next_id = 1;
            if($q_last && $r = mysqli_fetch_assoc($q_last)) {
                $next_id = $r['id'] + 1;
            }
            $nia = 'SWF-' . $tahun . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

            $query = "INSERT INTO member (nia, nama, jenis_kelamin, no_hp, tanggal_lahir, cabang_id, tanggal_gabung, tingkatan_kelas, sekolah, biaya_pendaftaran, biaya_bulanan) 
                      VALUES ('$nia', '$nama_siswa', '$jenis_kelamin', '$no_hp', '$tanggal_lahir_db', '$admin_pool_id', '$tanggal_gabung_db', '$tingkatan_kelas', '$sekolah', $biaya_pendaftaran, $biaya_bulanan)";
            
            if (mysqli_query($koneksi, $query)) {
                $success++;
            } else {
                $fail++;
            }
        }
        fclose($file);
        echo "<script>alert('Import selesai. Sukses: $success, Gagal: $fail'); window.location='atlet.php';</script>";
    } else {
        echo "<script>alert('Gagal upload file');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Import CSV Atlet</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Import Data Atlet dari CSV</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih File CSV:</label>
                <input type="file" name="file_csv" accept=".csv" required class="w-full">
            </div>
            <button type="submit" name="import" class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Import Data</button>
            <a href="atlet.php" class="inline-block mt-4 text-blue-500">Kembali ke Atlet</a>
        </form>
    </div>
</body>
</html>
