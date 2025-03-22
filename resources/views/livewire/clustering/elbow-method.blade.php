<div>
<x-slot:title>
    {{ __('Elbow Method') }}
</x-slot:title>

<!-- Load Highcharts dengan CDN yang lebih lengkap -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<div class="py-6">
    <div class="container mx-auto">
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Elbow Method') }}</h3>
                </div>

                <div class="px-6 py-4">
                    <!-- Container untuk form konfigurasi -->
                    <div class="mt-5 bg-white rounded-lg shadow-md p-5">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Konfigurasi Metode Elbow</h2>
                        
                        <!-- Card Input Metode Elbow -->
                        <div class="w-full flex flex-col gap-4 p-6 rounded-lg shadow-lg bg-white">
                            <div class="flex flex-col">
                                <h2 class="text-2xl font-semibold text-gray-800">Metode Elbow</h2>
                                <p class="text-gray-600">Gunakan metode elbow untuk menemukan jumlah cluster optimal</p>
                            </div>

                            <!-- Form untuk menjalankan Metode Elbow -->
                            <form wire:submit.prevent="runElbowMethod" class="flex flex-col space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="minClusters" class="block text-sm font-medium text-gray-700">Minimal Cluster</label>
                                        <input type="number" id="minClusters" wire:model.live="minClusters" min="2" max="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('minClusters') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="maxClusters" class="block text-sm font-medium text-gray-700">Maksimal Cluster</label>
                                        <input type="number" id="maxClusters" wire:model.live="maxClusters" min="2" max="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('maxClusters') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="maxIterations" class="block text-sm font-medium text-gray-700">Maksimal Iterasi</label>
                                    <input type="number" id="maxIterations" wire:model.live="maxIterations" min="10" max="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('maxIterations') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex justify-between items-center">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:loading.attr="disabled" wire:target="runElbowMethod">
                                        <span wire:loading.remove wire:target="runElbowMethod">Proses Metode Elbow</span>
                                        <span wire:loading wire:target="runElbowMethod" class="inline-flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Memproses...
                                        </span>
                                    </button>
                                    
                                    <button type="button" wire:click="$refresh" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Muat Ulang
                                    </button>
                                </div>
                            </form>

                            @if (session()->has('error'))
                            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                                {{ session('error') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Container untuk hasil elbow method -->
                    <div id="elbow-results-container" class="{{ empty($elbowResults) ? 'hidden' : '' }} mt-6">
                        <div class="mt-4 border-t pt-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hasil Metode Elbow</h3>
                            
                            @if(!empty($elbowResults))
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Berdasarkan hasil metode elbow, jumlah cluster yang optimal adalah: <span class="font-bold text-blue-600">{{ $optimalK }}</span></p>
                            </div>
                            
                            <div id="elbow-chart-container" class="w-full" style="height: 400px;"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 text-right">
    @if(!empty($elbowResults))
    <a href="{{ route('clustering.setup', ['optimalK' => $optimalK]) }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
        Lanjut ke Setup Clustering
    </a>
    @endif
</div>

<script>
// Pendekatan baru untuk menangani rendering chart
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, mempersiapkan listeners');
    setupChartEventListeners();
});

// Setup semua event listeners yang diperlukan
function setupChartEventListeners() {
    // Cegah duplikasi listener
    if (window.chartListenersSetup) return;
    window.chartListenersSetup = true;
    
    console.log('Setting up chart listeners');
    
    // 1. Listener untuk event Livewire spesifik untuk elbow results
    Livewire.on('elbowResultsUpdated', function(data) {
        console.log('Event elbowResultsUpdated diterima:', data);
        showResults();
        renderChart(data.results || []);
    });
    
    // 2. Observer untuk perubahan properti Livewire
    Livewire.hook('message.processed', (message, component) => {
        if (component.fingerprint && component.fingerprint.name === 'clustering.elbow-method') {
            console.log('Livewire component diupdate');
            checkAndRenderChart();
        }
    });
    
    // 3. Tambahkan handler untuk tombol refresh
    document.querySelector('button[wire\\:click="$refresh"]')?.addEventListener('click', function() {
        console.log('Tombol refresh diklik');
        setTimeout(checkAndRenderChart, 300);
    });
    
    // 4. Tangani form submission dengan wireSubmit
    document.querySelector('form[wire\\:submit\\.prevent="runElbowMethod"]')?.addEventListener('submit', function(e) {
        console.log('Form elbow method disubmit');
        // Tambahkan delay untuk memastikan Livewire selesai memproses hasil
        setTimeout(() => {
            checkAndRenderChart();
        }, 1000); // Delay lebih lama untuk memastikan hasil diproses
    });
    
    // 5. Periksa hasil pada inisialisasi halaman
    setTimeout(checkAndRenderChart, 500);
}

// Fungsi untuk memeriksa apakah hasil tersedia dan merender chart jika ada
function checkAndRenderChart() {
    console.log('Memeriksa hasil elbow');
    if (@this && @this.elbowResults && @this.elbowResults.length > 0) {
        console.log('Hasil elbow ditemukan dari komponen Livewire');
        showResults();
        renderChart(@this.elbowResults);
    } else {
        console.log('Tidak ada hasil elbow yang tersedia di komponen');
    }
}

// Fungsi untuk menampilkan container hasil
function showResults() {
    const resultContainer = document.getElementById('elbow-results-container');
    if (resultContainer && resultContainer.classList.contains('hidden')) {
        resultContainer.classList.remove('hidden');
        console.log('Container hasil ditampilkan');
    }
}

// Fungsi yang disederhanakan untuk membuat chart
function renderChart(results) {
    console.log('Memulai proses render chart dengan data:', results);
    
    if (!results || results.length === 0) {
        console.error('Tidak ada data untuk dirender');
        return;
    }
    
    const chartContainer = document.getElementById('elbow-chart-container');
    if (!chartContainer) {
        console.error('Container chart tidak ditemukan di DOM');
        return;
    }
    
    // Hapus chart yang ada sebelum membuat yang baru
    if (window.elbowChart) {
        try {
            window.elbowChart.destroy();
            console.log('Chart sebelumnya dihapus');
        } catch (e) {
            console.warn('Gagal menghapus chart sebelumnya:', e);
        }
    }
    
    // Siapkan data untuk chart
    const chartData = [];
    try {
        if (Array.isArray(results)) {
            for (const item of results) {
                if (item && typeof item.k !== 'undefined' && typeof item.sse !== 'undefined') {
                    chartData.push([item.k, parseFloat(item.sse)]);
                }
            }
        } else if (typeof results === 'object') {
            for (const k in results) {
                if (results.hasOwnProperty(k)) {
                    chartData.push([parseInt(k), parseFloat(results[k])]);
                }
            }
        }
    } catch (e) {
        console.error('Error saat memproses data chart:', e);
    }
    
    if (chartData.length === 0) {
        console.error('Tidak ada data valid untuk chart setelah diproses');
        return;
    }
    
    console.log('Data chart siap:', chartData);
    
    // Buat chart dengan data yang sudah diproses
    setTimeout(() => {
        try {
            window.elbowChart = Highcharts.chart('elbow-chart-container', {
                chart: { type: 'line' },
                title: { text: 'Metode Elbow - SSE vs Jumlah Cluster' },
                xAxis: {
                    title: { text: 'Jumlah Cluster (K)' },
                    allowDecimals: false
                },
                yAxis: {
                    title: { text: 'Sum of Squared Errors (SSE)' }
                },
                series: [{
                    name: 'SSE',
                    data: chartData,
                    color: '#3b82f6',
                    marker: { enabled: true }
                }],
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return Highcharts.numberFormat(this.y, 2);
                            }
                        }
                    }
                },
                tooltip: {
                    formatter: function() {
                        return '<b>K = ' + this.x + '</b><br>SSE: ' + Highcharts.numberFormat(this.y, 2);
                    }
                },
                credits: { enabled: false }
            });
            console.log('Chart berhasil dibuat');
        } catch (error) {
            console.error('Error saat membuat chart:', error);
            
            // Tambahkan fallback jika chart gagal dibuat
            console.log('Mencoba fallback untuk pembuatan chart...');
            const fallbackDelay = 800;
            setTimeout(() => {
                try {
                    // Pastikan container masih tersedia
                    if (document.getElementById('elbow-chart-container')) {
                        window.elbowChart = Highcharts.chart('elbow-chart-container', {
                            chart: { type: 'line' },
                            title: { text: 'Metode Elbow - SSE vs Jumlah Cluster' },
                            series: [{ name: 'SSE', data: chartData }]
                        });
                        console.log('Chart fallback berhasil dibuat');
                    } else {
                        console.error('Container chart tidak ditemukan saat fallback');
                    }
                } catch (e) {
                    console.error('Fallback chart juga gagal:', e);
                }
            }, fallbackDelay);
        }
    }, 300);
}
</script>
