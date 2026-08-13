<?php
session_start();
if (!isset($_SESSION['status']) || !in_array($_SESSION['role'], ['admin', 'ceo'])) exit("Unauthorized");
include '../includes/koneksi.php';

if(isset($_POST['insert_data'])) {
    // 1. Kosongkan tabel (opsional, tapi disarankan agar tidak duplikat jika di-refresh)
    mysqli_query($koneksi, "TRUNCATE TABLE jadwal");
    mysqli_query($koneksi, "TRUNCATE TABLE pelatih");

    // 2. Insert Jadwal
    $jadwal = [
        ['hari' => 'Rabu', 'mulai' => '15:30:00', 'selesai' => '16:30:00', 'lokasi' => 'Kolam Renang Ledhok Pereng'],
        ['hari' => 'Jumat', 'mulai' => '15:30:00', 'selesai' => '16:30:00', 'lokasi' => 'Kolam Renang Ledhok Pereng'],
        ['hari' => 'Selasa', 'mulai' => '15:30:00', 'selesai' => '17:00:00', 'lokasi' => 'Kolam Renang Sumbertirto Berbah'],
        ['hari' => 'Kamis', 'mulai' => '15:30:00', 'selesai' => '17:00:00', 'lokasi' => 'Kolam Renang Sumbertirto Berbah'],
        ['hari' => 'Sabtu', 'mulai' => '15:30:00', 'selesai' => '17:00:00', 'lokasi' => 'Kolam Renang Sumbertirto Berbah'],
        ['hari' => 'Kamis', 'mulai' => '15:30:00', 'selesai' => '17:00:00', 'lokasi' => 'Kolam Renang Tirto Jowo Bantul'],
        ['hari' => 'Sabtu', 'mulai' => '15:30:00', 'selesai' => '17:00:00', 'lokasi' => 'Kolam Renang Tirto Jowo Bantul'],
        ['hari' => 'Kamis', 'mulai' => '15:30:00', 'selesai' => '17:00:00', 'lokasi' => 'Kolam Renang Umbang Tirta'],
        ['hari' => 'Sabtu', 'mulai' => '15:30:00', 'selesai' => '17:00:00', 'lokasi' => 'Kolam Renang Umbang Tirta'],
    ];

    foreach($jadwal as $j) {
        $hari = $j['hari'];
        $mulai = $j['mulai'];
        $selesai = $j['selesai'];
        $lokasi = $j['lokasi'];
        $program = 'Kelas Pemula';
        
        $q = "INSERT INTO jadwal (hari, jam_mulai, jam_selesai, lokasi, program) VALUES ('$hari', '$mulai', '$selesai', '$lokasi', '$program')";
        mysqli_query($koneksi, $q);
    }

    // 3. Insert Pelatih
    $pelatih = [
        ['nama' => 'Muhammad Arifin', 'kelas' => 'Menengah A, Menengah C', 'lokasi' => 'Ledhok Pereng'],
        ['nama' => 'Sabila Nurul Anastasya', 'kelas' => 'Menengah B', 'lokasi' => 'Ledhok Pereng'],
        ['nama' => 'Tomi', 'kelas' => 'Menengah', 'lokasi' => 'Sumbertirto'],
        ['nama' => 'Larasati Azizah', 'kelas' => 'Pemula', 'lokasi' => 'Sumbertirto'],
        ['nama' => 'Andri', 'kelas' => 'Pemula', 'lokasi' => 'Sumbertirto'],
        ['nama' => 'Nasikh Furqoni Adha', 'kelas' => '', 'lokasi' => 'Bantul'],
        ['nama' => 'Darojatun Joko S', 'kelas' => '', 'lokasi' => 'Bantul'],
        ['nama' => 'Maryanto', 'kelas' => '', 'lokasi' => 'Bantul'],
        ['nama' => 'Hafifa Salma', 'kelas' => 'Level Up', 'lokasi' => 'Umbang Tirta'],
    ];

    foreach($pelatih as $p) {
        $nama = $p['nama'];
        $kelas = $p['kelas'];
        $lokasi = $p['lokasi'];
        
        $q2 = "INSERT INTO pelatih (nama, jabatan, kelas_mengajar, cabang) VALUES ('$nama', 'Pelatih', '$kelas', '$lokasi')";
        mysqli_query($koneksi, $q2);
    }

    echo "<script>alert('Berhasil memasukkan data Jadwal Latihan & Pelatih!'); window.location='dashboard.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert Data Tambahan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg text-center max-w-sm">
        <h1 class="font-bold text-xl mb-4 text-gray-800">Setup Data Jadwal & Pelatih</h1>
        <p class="text-sm text-gray-500 mb-6">Klik tombol di bawah ini untuk memasukkan 9 data pelatih dan jadwal latihan kelas pemula secara otomatis ke database.</p>
        <form method="POST">
            <button type="submit" name="insert_data" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all">
                Mulai Masukkan Data
            </button>
        </form>
    </div>
</body>
</html>
