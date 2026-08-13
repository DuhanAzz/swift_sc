<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || $_SESSION['role'] != "admin") { 
    exit("Unauthorized access");
}
include '../includes/koneksi.php';

// Get pool_id from session (this ensures they import to their own branch)
$admin_pool_id = $_SESSION['pool_id'] ?? 1;

if(isset($_POST['import'])) {
    if($_FILES['file_csv']['error'] == 0) {
        $file = fopen($_FILES['file_csv']['tmp_name'], 'r');
        $format = $_POST['format_csv'];
        
        $success = 0;
        $fail = 0;
        
        if ($format == 'google_form') {
            // Format 1: Salinan Formulir Pendaftaran (Google Forms)
            for ($i = 0; $i < 6; $i++) fgetcsv($file); // Skip headers
            
            while (($row = fgetcsv($file)) !== FALSE) {
                if (empty(trim($row[0]))) continue;
                
                $nama_siswa = bersihkan_input($row[4]);
                $no_hp = bersihkan_input($row[2]);
                $sekolah = bersihkan_input($row[8] ?? '');
                
                $tanggal_lahir = $row[6] ?? '';
                $tl_parts = explode('/', $tanggal_lahir);
                if(count($tl_parts) == 3) {
                    $tanggal_lahir_db = $tl_parts[2] . '-' . $tl_parts[1] . '-' . $tl_parts[0];
                } else {
                    $tanggal_lahir_db = date('Y-m-d');
                }

                $tanggal_mendaftar = $row[11] ?? '';
                $tm_parts = explode('/', $tanggal_mendaftar);
                if(count($tm_parts) == 3) {
                    $tanggal_gabung_db = $tm_parts[2] . '-' . $tm_parts[1] . '-' . $tm_parts[0];
                } else {
                    $tanggal_gabung_db = date('Y-m-d');
                }

                $jenis_kelamin = 'L'; // Default
                $tingkatan_kelas = 'Pemula';
                
                insert_atlet($koneksi, $nama_siswa, $jenis_kelamin, $no_hp, $tanggal_lahir_db, $admin_pool_id, $tanggal_gabung_db, $tingkatan_kelas, $sekolah, $success, $fail);
            }
        } 
        else if ($format == 'excel_split') {
            // Format 2: Data Atlet Excel (Pemula & Menengah split)
            $current_kelas = 'Pemula';
            while (($row = fgetcsv($file)) !== FALSE) {
                $full_row = implode(",", $row);
                if (empty(trim($row[0])) && empty(trim($row[1]))) {
                    if (strpos(strtoupper($full_row), 'MENENGAH') !== false) {
                        $current_kelas = 'Menengah';
                    } elseif (strpos(strtoupper($full_row), 'PEMULA') !== false) {
                        $current_kelas = 'Pemula';
                    }
                    continue;
                }

                if (strtolower(trim($row[0])) == 'no.' || empty(trim($row[1]))) {
                    continue;
                }

                $nama_siswa = bersihkan_input($row[1]);
                $tanggal_lahir_raw = $row[4] ?? '';
                $sekolah = bersihkan_input($row[6] ?? '');
                
                if (!empty($tanggal_lahir_raw)) {
                    $tl_parts = explode('/', $tanggal_lahir_raw);
                    if(count($tl_parts) == 3) {
                        $d = str_pad($tl_parts[0], 2, '0', STR_PAD_LEFT);
                        $m = str_pad($tl_parts[1], 2, '0', STR_PAD_LEFT);
                        $y = str_pad($tl_parts[2], 4, '20', STR_PAD_LEFT);
                        $tanggal_lahir_db = "$y-$m-$d";
                    } else {
                        $tanggal_lahir_db = date('Y-m-d');
                    }
                } else {
                    $tanggal_lahir_db = date('Y-m-d'); 
                }

                $no_hp = '-'; 
                $jenis_kelamin = 'L'; 
                $tanggal_gabung_db = date('Y-m-d');
                
                insert_atlet($koneksi, $nama_siswa, $jenis_kelamin, $no_hp, $tanggal_lahir_db, $admin_pool_id, $tanggal_gabung_db, $current_kelas, $sekolah, $success, $fail);
            }
        }
        
        fclose($file);
        echo "<script>alert('Import selesai! Berhasil: $success data, Gagal: $fail data.'); window.location='atlet.php';</script>";
    } else {
        echo "<script>alert('Gagal upload file');</script>";
    }
}

function insert_atlet($koneksi, $nama, $jk, $hp, $tgl_lahir, $cabang_id, $tgl_gabung, $kelas, $sekolah, &$success, &$fail) {
    $tahun = date('Y', strtotime($tgl_gabung));
    $q_last = mysqli_query($koneksi, "SELECT id FROM member ORDER BY id DESC LIMIT 1");
    $next_id = 1;
    if($q_last && $r = mysqli_fetch_assoc($q_last)) {
        $next_id = $r['id'] + 1;
    }
    $nia = 'SWF-' . $tahun . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

    $query = "INSERT INTO member (nia, nama, jenis_kelamin, no_hp, tanggal_lahir, cabang_id, tanggal_gabung, tingkatan_kelas, sekolah, biaya_pendaftaran, biaya_bulanan) 
              VALUES ('$nia', '$nama', '$jk', '$hp', '$tgl_lahir', '$cabang_id', '$tgl_gabung', '$kelas', '$sekolah', 100000, 350000)";
    
    if (mysqli_query($koneksi, $query)) {
        $success++;
    } else {
        $fail++;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Import CSV Atlet</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-xl w-full bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Import Data Atlet (CSV)</h2>
            <p class="text-gray-500 text-sm mt-2">Unggah file CSV Anda untuk menambahkan data atlet secara massal ke cabang <b><?php echo $admin_pool_id == 1 ? "Anda" : "Anda (ID: $admin_pool_id)"; ?></b>.</p>
        </div>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Format File CSV:</label>
                <select name="format_csv" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl p-3 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                    <option value="google_form">Format 1: Hasil Google Forms (Contoh: Pendaftaran Ledhok Pereng)</option>
                    <option value="excel_split">Format 2: Data Excel Kelas Terpisah (Contoh: Umbang Tirta Pemula & Menengah)</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih File CSV:</label>
                <input type="file" name="file_csv" accept=".csv" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl p-3 focus:ring-blue-500 focus:border-blue-500 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="pt-4 flex gap-4">
                <a href="atlet.php" class="flex-1 text-center bg-white border border-gray-300 text-gray-700 font-bold py-3 px-4 rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" name="import" class="flex-1 bg-blue-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30">
                    Import Sekarang
                </button>
            </div>
        </form>
    </div>
</body>
</html>
