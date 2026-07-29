<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelatih') {
    header("Location: ../login.php");
    exit;
}
include '../includes/koneksi.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$cabang_id = $_SESSION['cabang_id'];

// Ambil data atlet
$atlet_list = [];
$q_atlet = mysqli_query($koneksi, "SELECT id, nama, tanggal_lahir FROM member WHERE cabang_id='$cabang_id' ORDER BY nama ASC");
if ($q_atlet) {
    while ($row = mysqli_fetch_assoc($q_atlet)) {
        $row['umur'] = date('Y') - date('Y', strtotime($row['tanggal_lahir']));
        $atlet_list[] = $row;
    }
}

$gaya_renang = ['Bebas', 'Dada', 'Punggung', 'Kupu-kupu', 'Ganti'];
?>

<div class="lg:ml-[220px] pt-16 lg:pt-0 min-h-screen bg-slate-50">
    <div class="p-4 lg:p-8 max-w-7xl mx-auto">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Visualisasi Analitik Atlet</h1>
            <p class="text-gray-500 mt-1 font-medium">Analisis tren progresi waktu lomba dan detail split pace (per 50m).</p>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Atlet</label>
                    <select id="filterAtlet" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
                        <option value="">-- Pilih Atlet --</option>
                        <?php foreach($atlet_list as $at): ?>
                            <option value="<?= $at['id'] ?>"><?= htmlspecialchars($at['nama']) ?> (Umur: <?= htmlspecialchars($at['umur']) ?> thn)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Gaya Renang</label>
                    <select id="filterGaya" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
                        <option value="">-- Pilih Gaya --</option>
                        <?php foreach($gaya_renang as $g): ?>
                            <option value="<?= $g ?>"><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button id="btnTampilkan" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Tampilkan Analitik
                    </button>
                </div>
            </div>
        </div>

        <div id="alertInfo" class="hidden mb-6 p-4 rounded-xl bg-blue-50 text-blue-700 font-medium text-sm">
            Mencari data...
        </div>

        <!-- Chart Section -->
        <div id="chartContainer" class="hidden space-y-8">
            
            <!-- GRAFIK 1: Tren Progresi -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Tren Progresi Lintas Event</h3>
                <p class="text-sm text-gray-500 mb-6">Penurunan grafik menunjukkan waktu yang lebih cepat (lebih baik).</p>
                
                <div class="w-full h-[350px]">
                    <canvas id="trendChart"></canvas>
                </div>
                
                <!-- Tabel Riwayat Lomba untuk dipicu klik -->
                <div class="mt-8 border-t pt-6">
                    <h4 class="text-md font-bold text-gray-900 mb-4">Riwayat Lomba (Klik baris untuk melihat analisis Split)</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="tableRiwayat">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="p-3 text-sm font-bold text-gray-700">Tanggal & Event</th>
                                    <th class="p-3 text-sm font-bold text-gray-700">Jarak</th>
                                    <th class="p-3 text-sm font-bold text-gray-700">Waktu Total</th>
                                    <th class="p-3 text-sm font-bold text-gray-700">Lintasan</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRiwayat">
                                <!-- JS Injected -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- GRAFIK 2: Analisis Split Time -->
            <div id="splitChartWrapper" class="hidden bg-white rounded-2xl shadow-sm border border-indigo-100 p-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Race Pace / Split Time Analyzer</h3>
                <p id="splitChartSubtitle" class="text-sm text-gray-500 mb-6">Konsistensi pace per 50m pada event terpilih.</p>
                
                <div class="w-full h-[350px]">
                    <canvas id="splitChart"></canvas>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Import Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let trendChartInstance = null;
    let splitChartInstance = null;
    
    // Helper function to format MS back to MM:SS.ms
    function formatMsToTime(ms) {
        if (!ms) return "00:00.00";
        let date = new Date(ms);
        let minutes = Math.floor(ms / 60000);
        let seconds = Math.floor((ms % 60000) / 1000);
        let centiseconds = Math.floor((ms % 1000) / 10);
        
        return (minutes < 10 ? '0' : '') + minutes + ':' + 
               (seconds < 10 ? '0' : '') + seconds + '.' + 
               (centiseconds < 10 ? '0' : '') + centiseconds;
    }

    document.getElementById('btnTampilkan').addEventListener('click', function() {
        const atletId = document.getElementById('filterAtlet').value;
        const gaya = document.getElementById('filterGaya').value;
        const alertInfo = document.getElementById('alertInfo');
        const chartContainer = document.getElementById('chartContainer');
        
        if (!atletId || !gaya) {
            alert('Harap pilih Atlet dan Gaya Renang.');
            return;
        }
        
        alertInfo.classList.remove('hidden');
        alertInfo.textContent = 'Mencari data...';
        alertInfo.className = 'mb-6 p-4 rounded-xl bg-blue-50 text-blue-700 font-medium text-sm';
        
        chartContainer.classList.add('hidden');
        document.getElementById('splitChartWrapper').classList.add('hidden');
        
        fetch(`../api/api_grafik_atlet.php?action=get_trend&atlet_id=${atletId}&gaya=${gaya}`)
            .then(response => response.json())
            .then(res => {
                if(res.error) {
                    alertInfo.textContent = res.error;
                    alertInfo.className = 'mb-6 p-4 rounded-xl bg-red-50 text-red-700 font-medium text-sm';
                    return;
                }
                
                if(res.data.length === 0) {
                    alertInfo.textContent = 'Tidak ada riwayat lomba untuk kriteria ini.';
                    alertInfo.className = 'mb-6 p-4 rounded-xl bg-yellow-50 text-yellow-700 font-medium text-sm';
                    return;
                }
                
                alertInfo.classList.add('hidden');
                chartContainer.classList.remove('hidden');
                
                renderTrendChart(res.labels, res.data);
                renderRiwayatTable(res.raw_data);
            })
            .catch(err => {
                console.error(err);
                alertInfo.textContent = 'Terjadi kesalahan sistem.';
            });
    });
    
    function renderTrendChart(labels, dataMs) {
        const ctx = document.getElementById('trendChart').getContext('2d');
        
        if (trendChartInstance) {
            trendChartInstance.destroy();
        }
        
        trendChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Waktu Total',
                    data: dataMs,
                    borderColor: '#0d9488', // Emerald 600
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#0f766e',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.3 // Smooth curve
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Waktu: ' + formatMsToTime(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        reverse: false, // Normalnya grafik makin turun makin cepat, tapi karena ini MS, angka kecil = cepat. Kita biarkan reverse=false, tapi kalau mau angka kecil di atas bisa diset true.
                        ticks: {
                            callback: function(value) {
                                return formatMsToTime(value);
                            }
                        }
                    }
                }
            }
        });
    }
    
    function renderRiwayatTable(rawData) {
        const tbody = document.getElementById('tbodyRiwayat');
        tbody.innerHTML = '';
        
        rawData.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-100 last:border-0 hover:bg-indigo-50 transition-colors cursor-pointer';
            tr.onclick = function() {
                loadSplitChart(row.id, row.nama_event + ' (' + row.jarak + 'm)');
                // Highlight row
                Array.from(tbody.children).forEach(el => el.classList.remove('bg-indigo-50'));
                tr.classList.add('bg-indigo-50');
            };
            
            tr.innerHTML = `
                <td class="p-3 text-sm font-medium text-gray-800">
                    <div class="font-bold">${row.nama_event}</div>
                    <div class="text-gray-500 text-xs">${row.tanggal_rekor}</div>
                </td>
                <td class="p-3 text-sm text-gray-700 font-bold">${row.jarak}m</td>
                <td class="p-3 text-sm text-emerald-600 font-mono font-bold">${row.waktu_formatted}</td>
                <td class="p-3 text-sm text-gray-500">${row.lintasan || '-'}</td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    function loadSplitChart(performaId, subtitleText) {
        document.getElementById('splitChartWrapper').classList.remove('hidden');
        document.getElementById('splitChartSubtitle').textContent = 'Analisis event: ' + subtitleText;
        
        // Scroll smoothly to chart
        document.getElementById('splitChartWrapper').scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        fetch(`../api/api_grafik_atlet.php?action=get_splits&performa_id=${performaId}`)
            .then(response => response.json())
            .then(res => {
                if(res.error || res.data.length === 0) {
                    alert('Data split tidak ditemukan untuk event ini.');
                    return;
                }
                renderSplitChart(res.labels, res.data);
            });
    }
    
    function renderSplitChart(labels, dataMs) {
        const ctx = document.getElementById('splitChart').getContext('2d');
        
        if (splitChartInstance) {
            splitChartInstance.destroy();
        }
        
        splitChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pace Waktu (per 50m)',
                    data: dataMs,
                    borderColor: '#4f46e5', // Indigo 600
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#4338ca',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4 // Extra smooth curve
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Lap Pace: ' + formatMsToTime(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return formatMsToTime(value);
                            }
                        }
                    }
                }
            }
        });
    }
</script>

<?php include '../includes/footer.php'; ?>
