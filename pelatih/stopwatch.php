<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "sudah_login" || ($_SESSION['role'] != 'coach' && $_SESSION['role'] != 'pelatih')) { 
    header("location:../login.php"); 
    exit; 
}
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="lg:ml-[220px] pt-14 lg:pt-0 min-h-screen bg-slate-50 flex flex-col">
    
    <!-- Top Bar (Desktop) -->
    <div class="topbar hidden lg:flex items-center justify-between h-14 px-6 sticky top-0 z-30 bg-white border-b border-slate-200">
        <div>
            <span class="text-sm font-medium text-slate-900">Dashboard</span>
            <span class="text-sm text-slate-400 mx-2">/</span>
            <span class="text-sm font-medium text-slate-900">Pelatih</span>
            <span class="text-sm text-slate-400 mx-2">/</span>
            <span class="text-sm text-slate-500">Stopwatch Digital</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="badge bg-cyan-100 text-cyan-800 px-2 py-1 rounded text-xs font-semibold">Tools Lapangan</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-4 lg:p-8">
        
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Stopwatch Lapangan</h1>
                <p class="text-sm text-slate-500 mt-1">Catat waktu split atlet dengan akurat saat latihan</p>
            </div>

            <!-- Stopwatch Display Card -->
            <div class="bg-slate-900 rounded-2xl shadow-xl overflow-hidden mb-6 border border-slate-800">
                <div class="p-8 sm:p-12 text-center relative">
                    <!-- Digital Display -->
                    <div class="font-mono text-5xl sm:text-7xl md:text-8xl font-black text-white tracking-wider tabular-nums" id="display">
                        00:00:00<span class="text-3xl sm:text-5xl md:text-6xl text-cyan-400">.00</span>
                    </div>
                </div>
                
                <!-- Controls -->
                <div class="bg-slate-800 p-4 sm:p-6 border-t border-slate-700">
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 max-w-lg mx-auto">
                        <button id="btn-lap" class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-4 rounded-xl text-sm sm:text-base transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            LAP
                        </button>
                        <button id="btn-start" class="bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-4 rounded-xl text-sm sm:text-base transition-colors shadow-lg shadow-cyan-900/50">
                            START
                        </button>
                        <button id="btn-reset" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 font-bold py-4 rounded-xl text-sm sm:text-base transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            RESET
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lap Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800">Riwayat Waktu Lap</h3>
                    <span class="text-xs font-semibold text-slate-500 bg-white px-2 py-1 rounded border border-slate-200" id="lap-count">0 Lap</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-white">
                                <th class="py-3 px-5 font-bold text-slate-500 w-16">No</th>
                                <th class="py-3 px-5 font-bold text-slate-500">Waktu Split</th>
                                <th class="py-3 px-5 font-bold text-slate-500">Total Waktu</th>
                                <th class="py-3 px-5 font-bold text-slate-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="laps-container">
                            <!-- Empty State -->
                            <tr id="empty-state">
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Belum ada catatan lap. Tekan START untuk memulai.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Stopwatch Logic
    let startTime;
    let elapsedTime = 0;
    let timerInterval;
    let isRunning = false;
    let laps = [];
    let lastLapTime = 0;

    const display = document.getElementById('display');
    const btnStart = document.getElementById('btn-start');
    const btnLap = document.getElementById('btn-lap');
    const btnReset = document.getElementById('btn-reset');
    const lapsContainer = document.getElementById('laps-container');
    const emptyState = document.getElementById('empty-state');
    const lapCount = document.getElementById('lap-count');

    function formatTime(time) {
        let date = new Date(time);
        let m = date.getUTCMinutes().toString().padStart(2, '0');
        let s = date.getUTCSeconds().toString().padStart(2, '0');
        let ms = Math.floor(date.getUTCMilliseconds() / 10).toString().padStart(2, '0');
        
        // Return HTML string with styled milliseconds
        return `${m}:${s}<span class="text-3xl sm:text-5xl md:text-6xl text-cyan-400">.${ms}</span>`;
    }

    function formatTimePlain(time) {
        let date = new Date(time);
        let m = date.getUTCMinutes().toString().padStart(2, '0');
        let s = date.getUTCSeconds().toString().padStart(2, '0');
        let ms = Math.floor(date.getUTCMilliseconds() / 10).toString().padStart(2, '0');
        return `${m}:${s}.${ms}`;
    }

    function updateDisplay() {
        display.innerHTML = formatTime(elapsedTime);
    }

    function startTimer() {
        startTime = Date.now() - elapsedTime;
        timerInterval = setInterval(function printTime() {
            elapsedTime = Date.now() - startTime;
            updateDisplay();
        }, 10);
        
        btnStart.textContent = 'STOP';
        btnStart.classList.remove('bg-cyan-600', 'hover:bg-cyan-500', 'shadow-cyan-900/50');
        btnStart.classList.add('bg-red-500', 'hover:bg-red-400', 'shadow-red-900/50');
        
        btnLap.disabled = false;
        btnReset.disabled = true;
        isRunning = true;
    }

    function stopTimer() {
        clearInterval(timerInterval);
        
        btnStart.textContent = 'START';
        btnStart.classList.remove('bg-red-500', 'hover:bg-red-400', 'shadow-red-900/50');
        btnStart.classList.add('bg-cyan-600', 'hover:bg-cyan-500', 'shadow-cyan-900/50');
        
        btnLap.disabled = true;
        btnReset.disabled = false;
        isRunning = false;
    }

    function resetTimer() {
        clearInterval(timerInterval);
        elapsedTime = 0;
        lastLapTime = 0;
        laps = [];
        updateDisplay();
        
        btnLap.disabled = true;
        btnReset.disabled = true;
        
        renderLaps();
    }

    function recordLap() {
        const currentLapTime = elapsedTime - lastLapTime;
        laps.unshift({
            total: elapsedTime,
            split: currentLapTime
        });
        lastLapTime = elapsedTime;
        renderLaps();
    }

    function renderLaps() {
        lapCount.textContent = `${laps.length} Lap`;
        
        if (laps.length === 0) {
            lapsContainer.innerHTML = '';
            lapsContainer.appendChild(emptyState);
            return;
        }

        let html = '';
        laps.forEach((lap, index) => {
            const lapNumber = laps.length - index;
            html += `
                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-5 font-bold text-slate-400">${lapNumber}</td>
                    <td class="py-3 px-5 font-mono font-semibold text-cyan-600">${formatTimePlain(lap.split)}</td>
                    <td class="py-3 px-5 font-mono text-slate-600">${formatTimePlain(lap.total)}</td>
                    <td class="py-3 px-5 text-right">
                        <button class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded font-medium transition-colors" onclick="alert('Fitur simpan lap ke database segera hadir!')">
                            Simpan ke Atlet
                        </button>
                    </td>
                </tr>
            `;
        });
        
        lapsContainer.innerHTML = html;
    }

    // Event Listeners
    btnStart.addEventListener('click', () => {
        if (isRunning) stopTimer();
        else startTimer();
    });

    btnLap.addEventListener('click', recordLap);
    btnReset.addEventListener('click', resetTimer);

</script>

<?php include '../includes/footer.php'; ?>
