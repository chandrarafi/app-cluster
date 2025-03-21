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
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('3. Elbow Method') }}</h3>
                </div>

                <div class="px-6 py-4">
                    <!-- Container untuk chart selalu ada di DOM, tetapi disembunyikan jika tidak ada hasil -->
                    <div class="mt-5 @if(empty($elbowResults)) hidden @endif" id="result-container">
                        <h2 class="text-lg font-semibold text-gray-800">Hasil Metode Elbow</h2>
                        <div class="mt-3 bg-white rounded-lg shadow-md p-4">
                            <div class="text-center mb-4">
                                <p>Nilai K Optimal: <span class="font-bold text-blue-600">{{ $optimalK ?? '-' }}</span></p>
                            </div>
                            <div id="elbowChart" style="height: 400px;"></div>
                        </div>
                    </div>

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
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="runElbowMethod">Proses Metode Elbow</span>
                                        <span wire:loading wire:target="runElbowMethod">
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

                            <div id="result-container" class="{{ empty($elbowResults) ? 'hidden' : '' }}">
                                <div class="mt-4 border-t pt-4">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Hasil Metode Elbow</h3>
                                    
                                    @if(!empty($elbowResults))
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">Berdasarkan hasil metode elbow, jumlah cluster yang optimal adalah: <span class="font-bold text-blue-600">{{ $optimalK }}</span></p>
                                    </div>
                                    @endif
                                    
                                    <div id="elbowChart" class="w-full h-80"></div>
                                </div>
                            </div>
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
// Script untuk membuat chart Elbow Method
document.addEventListener('livewire:initialized', function () {
    console.log('Livewire initialized');
    
    // Tambahkan listener untuk event ketika hasil Elbow Method diperbarui
    @this.on('elbowResultsUpdated', function(event) {
        console.log('Event elbowResultsUpdated diterima:', event);
        
        // Tampilkan container hasil
        const resultContainer = document.getElementById('result-container');
        if (resultContainer) {
            resultContainer.classList.remove('hidden');
            console.log('Container hasil ditampilkan');
            
            // Buat chart
            setTimeout(createChart, 200);
        }
    });
    
    // Fungsi untuk membuat chart
    function createChart() {
        console.log('Membuat chart...');
        
        const chartElement = document.getElementById('elbowChart');
        if (!chartElement) {
            console.error('Elemen chart tidak ditemukan');
            return;
        }
        
        const results = @this.elbowResults;
        if (!results || results.length === 0) {
            console.error('Tidak ada data hasil untuk ditampilkan');
            return;
        }
        
        console.log('Data untuk chart:', results);
        
        try {
            // Siapkan data untuk chart
            const chartData = [];
            
            // Format data berdasarkan struktur hasil
            if (Array.isArray(results)) {
                // Untuk format array objek
                for (const item of results) {
                    if (item && item.k !== undefined && item.sse !== undefined) {
                        chartData.push([item.k, parseFloat(item.sse)]);
                    }
                }
            } else {
                // Untuk format objek
                for (const k in results) {
                    if (results.hasOwnProperty(k)) {
                        chartData.push([parseInt(k), parseFloat(results[k])]);
                    }
                }
            }
            
            console.log('Data chart yang disiapkan:', chartData);
            
            // Reset container chart
            chartElement.innerHTML = '';
            
            // Buat chart menggunakan Highcharts
            Highcharts.chart('elbowChart', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: 'Metode Elbow - SSE vs Jumlah Cluster'
                },
                xAxis: {
                    title: {
                        text: 'Jumlah Cluster (K)'
                    },
                    allowDecimals: false
                },
                yAxis: {
                    title: {
                        text: 'Sum of Squared Errors (SSE)'
                    }
                },
                series: [{
                    name: 'SSE',
                    data: chartData,
                    color: '#3b82f6',
                    marker: {
                        enabled: true
                    }
                }],
                tooltip: {
                    formatter: function() {
                        return '<b>K = ' + this.x + '</b><br>SSE: ' + Highcharts.numberFormat(this.y, 2);
                    }
                },
                credits: {
                    enabled: false
                }
            });
            
            console.log('Chart berhasil dibuat');
        } catch (error) {
            console.error('Error saat membuat chart:', error);
        }
    }
    
    // Jika ada hasil saat halaman dimuat, buat chart
    if (@this.elbowResults) {
        console.log('Hasil Elbow ditemukan saat inisialisasi, membuat chart...');
        setTimeout(createChart, 500);
    }
});
</script>
