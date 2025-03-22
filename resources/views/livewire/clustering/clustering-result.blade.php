<div>
<x-slot:title>
    {{ __('Hasil Clustering') }}
</x-slot:title>

<!-- Load Highcharts dan Chart.js -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="py-6">
    <div class="container mx-auto">
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Hasil K-Means Clustering') }}</h3>
                    
                    <div>
                        <button wire:click="resetClustering" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Kembali ke Konfigurasi') }}
                        </button>
                    </div>
                </div>

                <div class="px-6 py-5">
                    @if (!$isDataAvailable)
                        <div class="text-center p-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Data tidak tersedia') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Belum ada data clustering yang diproses.') }}</p>
                            <div class="mt-4">
                                <a href="{{ route('clustering.setup') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('Jalankan Clustering') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Dashboard Cards -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">{{ __('Informasi Clustering') }}</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-gradient-to-tr from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 shadow-sm">
                                    <div class="flex items-center">
                                        <div class="rounded-full bg-blue-200 p-3 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm text-blue-600 font-medium uppercase">{{ __('Jumlah Cluster') }}</div>
                                            <div class="text-2xl font-semibold text-gray-900">{{ count($centroids) }}</div>
                                            @if($optimalK)
                                            <div class="text-xs text-blue-600 mt-1">
                                                <span class="font-medium">K optimal dari Elbow Method: {{ $optimalK }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-tr from-green-50 to-green-100 p-4 rounded-lg border border-green-200 shadow-sm">
                                    <div class="flex items-center">
                                        <div class="rounded-full bg-green-200 p-3 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-green-700">{{ __('Jumlah Iterasi') }}</p>
                                            <p class="text-2xl font-bold text-green-900">{{ $iterations }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-tr from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200 shadow-sm">
                                    <div class="flex items-center">
                                        <div class="rounded-full bg-purple-200 p-3 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-purple-700">{{ __('Total Siswa') }}</p>
                                            <p class="text-2xl font-bold text-purple-900">{{ $this->getTotalStudents() }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-tr from-amber-50 to-amber-100 p-4 rounded-lg border border-amber-200 shadow-sm">
                                    <div class="flex items-center">
                                        <div class="rounded-full bg-amber-200 p-3 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-amber-700">{{ __('SSE') }}</p>
                                            <p class="text-2xl font-bold text-amber-900">{{ number_format($sse, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Visualisasi -->
                        <div class="mb-8">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">{{ __('Visualisasi Cluster') }}</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                                <!-- Distribusi Cluster -->
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                        <h5 class="font-medium text-gray-700">{{ __('Distribusi Siswa per Cluster') }}</h5>
                                    </div>
                                    <div class="p-4">
                                        <div wire:ignore class="h-80" id="cluster-distribution-chart"></div>
                                    </div>
                                </div>
                                
                              <!-- Hasil Metode Elbow -->
@if($elbowResults)
<div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-zinc-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ __('Hasil Metode Elbow') }}</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="mb-3 text-sm text-gray-600">
                    Metode Elbow digunakan untuk menentukan jumlah cluster (K) optimal dengan menemukan titik di mana penambahan cluster tidak lagi memberikan penurunan SSE yang signifikan.
                </p>
                <div class="flex items-center mb-4">
                    <div class="rounded-full bg-blue-100 p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-medium text-gray-800">Nilai K Optimal: <span class="text-blue-600 font-semibold">{{ $optimalK }}</span></p>
                    </div>
                </div>
                <div id="elbowChart" class="h-64 w-full"></div>
            </div>
        </div>
    </div>
</div>
@endif
                              
                            </div>
                        </div>

                        <!-- Ringkasan Cluster -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">{{ __('Ringkasan Cluster') }}</h4>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Cluster') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Jml Siswa') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('UTS') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('UAS') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Sikap') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Pramuka') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('PMR') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Kehadiran') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Karakteristik') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($clusters as $index => $cluster)
                                            @php
                                                $stats = $this->getClusterStats($index);
                                                $color = $this->getClusterColor($index);
                                                $karakteristik = $this->getClusterCharacteristics($index);
                                            @endphp
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 inline-flex text-sm font-medium rounded-full {{ $color }}">
                                                        {{ __('Cluster ') }} {{ $index + 1 }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $stats['count'] }} {{ __('siswa') }}
                                                    <p class="text-xs text-gray-500">({{ $this->formatNumber(($stats['count'] / $this->getTotalStudents()) * 100, 1) }}%)</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                    {{ $this->formatNumber($stats['avg_uts']) }}
                                                    <p class="text-xs {{ $this->getValueColor($stats['avg_uts'], 70) }}">{{ $this->getValueLabel($stats['avg_uts'], 70) }}</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                    {{ $this->formatNumber($stats['avg_uas']) }}
                                                    <p class="text-xs {{ $this->getValueColor($stats['avg_uas'], 70) }}">{{ $this->getValueLabel($stats['avg_uas'], 70) }}</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                    {{ $stats['sikap_huruf'] }} 
                                                    <p class="text-xs {{ $this->getSikapColor($stats['sikap_huruf']) }}">{{ $this->formatNumber($stats['avg_sikap'], 1) }}</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                    {{ $stats['pramuka_huruf'] }} ({{ $this->formatNumber($stats['avg_pramuka']) }})
                                                    <p class="text-xs {{ $this->getValueColor($stats['avg_pramuka'], 70) }}">{{ $this->getValueLabel($stats['avg_pramuka'], 70) }}</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                    {{ $stats['pmr_huruf'] }} ({{ $this->formatNumber($stats['avg_pmr']) }})
                                                    <p class="text-xs {{ $this->getValueColor($stats['avg_pmr'], 70) }}">{{ $this->getValueLabel($stats['avg_pmr'], 70) }}</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                    {{ $this->formatNumber($stats['avg_kehadiran']) }}
                                                    <p class="text-xs {{ $this->getValueColor($stats['avg_kehadiran'], 80) }}">{{ $this->getValueLabel($stats['avg_kehadiran'], 80) }}</p>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                                    <p>{{ $karakteristik }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Detail Anggota Cluster -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">{{ __('Detail Anggota Cluster') }}</h4>

                            <div class="space-y-4">
                                @foreach ($clusters as $index => $cluster)
                                    @php
                                        $color = $this->getClusterColor($index);
                                        $karakteristik = $this->getClusterCharacteristics($index);
                                    @endphp
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="px-4 py-3 {{ $color }} border-b flex justify-between items-center">
                                            <h5 class="font-medium">
                                                {{ __('Cluster ') }} {{ $index + 1 }} 
                                                <span class="text-sm font-normal">({{ count($cluster) }} {{ __('siswa') }})</span>
                                            </h5>
                                            <p class="text-sm italic">{{ $karakteristik }}</p>
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('No') }}
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('Nama') }}
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('Kelas') }}
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('UTS') }}
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('UAS') }}
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('Sikap') }}
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('Pramuka') }}
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('PMR') }}
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ __('Kehadiran') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @forelse ($cluster as $itemIndex => $item)
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $itemIndex + 1 }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $item['name'] }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $item['kelas'] }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                                {{ $item['features'][0] }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                                {{ $item['features'][1] }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                                {{ $this->getNilaiSikapHuruf($item['features'][2]) }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                                {{ $this->getNilaiHuruf($item['features'][3], 'pramuka') }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                                {{ $this->getNilaiHuruf($item['features'][4], 'pmr') }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                                                {{ $item['features'][5] }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                                {{ __('Tidak ada data dalam cluster ini') }}
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>





<!-- Script untuk chart -->
<script>
    // Variabel global untuk menyimpan instance charts
    window.clusterCharts = {
        distribution: null,
        characteristics: null,
        elbow: null
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded untuk halaman hasil clustering');
        setupChartRendering();
    });
    
    // Setup rendering chart
    function setupChartRendering() {
        // Cegah duplikasi setup
        if (window.chartsSetupDone) return;
        window.chartsSetupDone = true;
        
        console.log('Setting up chart rendering untuk hasil clustering');
        
        // Render charts jika data tersedia
        if (document.getElementById('cluster-distribution-chart')) {
            setTimeout(() => {
                renderAllCharts();
            }, 300);
        }
        
        // Tambahkan listener untuk event Livewire
        Livewire.hook('message.processed', (message, component) => {
            if (component && component.fingerprint && component.fingerprint.name === 'clustering.clustering-result') {
                console.log('Livewire component clustering-result diupdate');
                setTimeout(() => {
                    renderAllCharts();
                }, 300);
            }
        });
        
        // Tambahkan listener untuk navigasi
        document.addEventListener('livewire:navigated', function() {
            console.log('Livewire navigated event');
            setTimeout(() => {
                if (document.getElementById('cluster-distribution-chart')) {
                    renderAllCharts();
                }
            }, 300);
        });
    }
    
    // Render semua chart yang diperlukan
    function renderAllCharts() {
        console.log('Rendering semua chart hasil clustering');
        renderDistributionChart();
        renderCharacteristicsChart();
        
        // Memanggil renderElbowChart hanya jika ada data elbow
        var elbowChartContainer = document.getElementById('elbowChart');
        if (elbowChartContainer) {
            renderElbowChart();
        } else {
            console.log('Container elbow chart tidak ditemukan');
        }
    }
    
    // Fungsi untuk membuat chart distribusi
    function renderDistributionChart() {
        try {
            // Pastikan element ada di DOM
            const chartContainer = document.getElementById('cluster-distribution-chart');
            if (!chartContainer) {
                console.log('Container chart distribusi tidak ditemukan');
                return;
            }
            
            console.log('Rendering chart distribusi cluster');
            
            // Hapus chart sebelumnya jika ada
            if (window.clusterCharts.distribution) {
                try {
                    window.clusterCharts.distribution.destroy();
                } catch (e) {
                    console.warn('Gagal menghapus chart distribusi sebelumnya:', e);
                }
            }
            
            // Dapatkan data
            const clusterCounts = @json($this->getClusterCounts());
            const clusterColors = @json($this->getClusterPieColors());
            
            if (!clusterCounts || clusterCounts.length === 0) {
                console.log('Tidak ada data distribusi cluster');
                return;
            }
            
            // Buat chart baru
            window.clusterCharts.distribution = Highcharts.chart('cluster-distribution-chart', {
                chart: {
                    type: 'pie',
                    backgroundColor: 'transparent'
                },
                title: {
                    text: 'Distribusi Siswa per Cluster',
                    style: { fontSize: '14px' }
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br>Jumlah: <b>{point.y}</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>Cluster {point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                fontWeight: 'normal'
                            }
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Persentase',
                    colorByPoint: true,
                    data: clusterCounts.map((count, index) => ({
                        name: (index + 1).toString(),
                        y: count,
                        color: clusterColors[index % clusterColors.length]
                    }))
                }]
            });
            
            console.log('Chart distribusi berhasil dibuat');
        } catch (error) {
            console.error('Error rendering distribution chart:', error);
        }
    }
    
    // Fungsi untuk membuat chart karakteristik
    function renderCharacteristicsChart() {
        try {
            // Pastikan element ada di DOM
            const chartContainer = document.getElementById('cluster-characteristics-chart');
            if (!chartContainer) {
                console.log('Container chart karakteristik tidak ditemukan');
                return;
            }
            
            console.log('Rendering chart karakteristik cluster');
            
            // Hapus chart sebelumnya jika ada
            if (window.clusterCharts.characteristics) {
                try {
                    window.clusterCharts.characteristics.destroy();
                } catch (e) {
                    console.warn('Gagal menghapus chart karakteristik sebelumnya:', e);
                }
            }
            
            // Dapatkan data
            const clusterNames = [];
            const dataPoints = @json($this->getClusterDataForRadarChart());
            
            if (!dataPoints || dataPoints.length === 0) {
                console.log('Tidak ada data karakteristik cluster');
                return;
            }
            
            for (let i = 0; i < dataPoints.length; i++) {
                clusterNames.push('Cluster ' + (i + 1));
            }
            
            // Buat chart baru
            window.clusterCharts.characteristics = Highcharts.chart('cluster-characteristics-chart', {
                chart: {
                    polar: true,
                    backgroundColor: 'transparent'
                },
                title: {
                    text: 'Karakteristik Cluster',
                    style: { fontSize: '14px' }
                },
                pane: {
                    size: '80%'
                },
                xAxis: {
                    categories: ['UTS', 'UAS', 'Sikap', 'Pramuka', 'PMR', 'Kehadiran'],
                    tickmarkPlacement: 'on',
                    lineWidth: 0
                },
                yAxis: {
                    gridLineInterpolation: 'polygon',
                    lineWidth: 0,
                    min: 0,
                    max: 100
                },
                tooltip: {
                    shared: true,
                    pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}</b><br/>'
                },
                legend: {
                    align: 'right',
                    verticalAlign: 'middle',
                    layout: 'vertical'
                },
                series: dataPoints.map((data, index) => ({
                    name: 'Cluster ' + (index + 1),
                    data: data,
                    pointPlacement: 'on',
                    color: @json($this->getClusterHighchartsColors())[index % @json($this->getClusterHighchartsColors()).length]
                })),
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            },
                            pane: {
                                size: '70%'
                            }
                        }
                    }]
                }
            });
            
            console.log('Chart karakteristik berhasil dibuat');
        } catch (error) {
            console.error('Error rendering characteristics chart:', error);
        }
    }
    
    // Fungsi untuk membuat chart elbow
    function renderElbowChart() {
        try {
            // Pastikan element ada di DOM dan data tersedia
            const chartContainer = document.getElementById('elbowChart');
            if (!chartContainer) {
                console.log('Container chart elbow tidak ditemukan');
                return;
            }
            
            console.log('Rendering chart elbow pada halaman hasil');
            
            // Dapatkan data elbow results dari server
            const elbowResults = @json($elbowResults ?? []);
            
            // Periksa apakah data tersedia
            if (!elbowResults || elbowResults.length === 0) {
                console.log('Tidak ada data elbow results');
                return;
            }
            
            // Hapus chart sebelumnya jika ada
            if (window.clusterCharts.elbow) {
                try {
                    window.clusterCharts.elbow.destroy();
                } catch (e) {
                    console.warn('Gagal menghapus chart elbow sebelumnya:', e);
                }
            }
            
            // Persiapkan data untuk chart elbow
            const chartData = [];
            for (const result of elbowResults) {
                if (result && result.k !== undefined && result.sse !== undefined) {
                    chartData.push([result.k, parseFloat(result.sse)]);
                }
            }
            
            if (chartData.length === 0) {
                console.log('Tidak ada data valid untuk chart elbow');
                return;
            }
            
            // Buat chart baru
            setTimeout(() => {
                try {
                    window.clusterCharts.elbow = Highcharts.chart('elbowChart', {
                        chart: {
                            type: 'line'
                        },
                        title: {
                            text: 'Metode Elbow - SSE vs Jumlah Cluster',
                            style: {
                                fontSize: '14px'
                            }
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
                                enabled: true,
                                radius: 4
                            }
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
                        credits: {
                            enabled: false
                        }
                    });
                    
                    console.log('Chart elbow berhasil dibuat');
                } catch (chartError) {
                    console.error('Error creating elbow chart:', chartError);
                }
            }, 300);
        } catch (error) {
            console.error('Error dalam fungsi renderElbowChart:', error);
        }
    }
</script>
</div>
