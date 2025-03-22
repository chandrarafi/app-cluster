<x-layouts.app :title="__('Dashboard')">
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard Analisis Clustering</h1>
            <p class="mt-1 text-sm text-gray-600">Selamat datang di Aplikasi Clustering untuk Analisis Akademik dan Non-Akademik Siswa</p>
        </div>

        <!-- Kartu Informasi -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Siswa -->
            <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 rounded-md bg-blue-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292m-5.833 8.25A8 8 0 1120.5 13a2 2 0 10-4 0c0 2.21-1.79 4-4 4a4 4 0 01-4-4"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Siswa</dt>
                            <dd>
                                <div class="text-xl font-medium text-gray-900">{{ \App\Models\Student::count() }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <a href="{{ route('student.dataset') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">Lihat Dataset Siswa &rarr;</a>
                </div>
            </div>

            <!-- Status Clustering -->
            <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Proses Clustering</h3>
                    </div>
                    
                    @php
                        $clusteringResult = session('clustering_result');
                        $clusteringData = session('clustering_data');
                        $clusters = session('clusters');
                        $isProcessed = $clusteringResult || $clusteringData || $clusters;
                        
                        // Menentukan jumlah data yang tersedia
                        $dataCount = \App\Models\Student::count();
                        $hasEnoughData = $dataCount >= 5;
                    @endphp
                    
                    <div class="space-y-3">
                        <!-- Status Proses -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Status:</span>
                            @if($isProcessed)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Sudah Diproses
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Belum Diproses
                                </span>
                            @endif
                        </div>
                        
                        <!-- Kesiapan Data -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Data Siswa:</span>
                            @if($hasEnoughData)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $dataCount }} (Siap Diproses)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $dataCount }} (Kurang Data)
                                </span>
                            @endif
                        </div>
                        
                        <!-- Aksi yang Direkomendasikan -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Aksi:</span>
                            @if(!$isProcessed && $hasEnoughData)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Siap untuk Clustering
                                </span>
                            @elseif(!$isProcessed && !$hasEnoughData)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Perlu Tambah Data
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Lihat Hasil
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 flex justify-between">
                    @if(!$isProcessed && $hasEnoughData)
                        <a href="{{ route('clustering.setup') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            Mulai Clustering &rarr;
                        </a>
                    @elseif(!$isProcessed && !$hasEnoughData)
                        <a href="{{ route('student.dataset') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            Tambah Data &rarr;
                        </a>
                    @else
                        <a href="{{ route('clustering.result') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            Lihat Hasil &rarr;
                        </a>
                    @endif
                </div>
            </div>

            <!-- Jumlah Cluster -->
            <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Analisis Cluster</h3>
                    </div>
                    
                    @php
                        // Periksa berbagai sumber data untuk K optimal
                        $optimalK = session('optimal_k') ?? 0;
                        
                        if ($optimalK == 0) {
                            $setupParams = session('setup_params');
                            if ($setupParams && isset($setupParams['jumlahCluster'])) {
                                $optimalK = $setupParams['jumlahCluster'];
                            } elseif ($clusteringResult && isset($clusteringResult['clusters'])) {
                                $optimalK = count($clusteringResult['clusters']);
                            } elseif ($clusters) {
                                $optimalK = count($clusters);
                            }
                        }
                        
                        if ($optimalK == 0) {
                            $elbowResults = session('elbow_results');
                            if ($elbowResults && isset($elbowResults['optimal_k'])) {
                                $optimalK = $elbowResults['optimal_k'];
                            }
                        }
                        
                        $elbowProcessed = session('elbow_results') || session('optimal_k');
                    @endphp
                    
                    <div class="space-y-3">
                        <!-- K Optimal -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Jumlah Cluster:</span>
                            @if($optimalK > 0)
                                <div class="flex items-center">
                                    @for($i = 0; $i < $optimalK; $i++)
                                        <div class="w-4 h-4 rounded-full bg-indigo-{{ 500 - ($i * 100) }} mr-1"></div>
                                    @endfor
                                    <span class="ml-2 font-medium">{{ $optimalK }} cluster</span>
                                </div>
                            @else
                                <span class="text-sm text-yellow-600">Belum ditentukan</span>
                            @endif
                        </div>
                        
                        <!-- Status Analisis Elbow -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Analisis Elbow:</span>
                            @if($elbowProcessed)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Belum Diproses
                                </span>
                            @endif
                        </div>
                        
                        <!-- Rekomendasi -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Rekomendasi:</span>
                            @if(!$elbowProcessed)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Jalankan Elbow Method
                                </span>
                            @elseif($optimalK > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Gunakan K = {{ $optimalK }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Tentukan K Optimal
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <a href="{{ route('clustering.elbow') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        {{ $elbowProcessed ? 'Lihat Hasil Elbow Method' : 'Jalankan Elbow Method' }} &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Panduan Cepat dan Informasi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Panduan Cepat -->
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Panduan Cepat Clustering</h3>
                </div>
                <div class="px-6 py-5">
                    <ol class="list-decimal list-inside space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <span class="font-medium text-blue-600 mr-2">Langkah 1:</span>
                            <span>Tentukan jumlah cluster optimal menggunakan <a href="{{ route('clustering.elbow') }}" class="text-blue-600 hover:underline">Elbow Method</a></span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-medium text-blue-600 mr-2">Langkah 2:</span>
                            <span>Konfigurasikan parameter clustering di menu <a href="{{ route('clustering.setup') }}" class="text-blue-600 hover:underline">Setup Clustering</a></span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-medium text-blue-600 mr-2">Langkah 3:</span>
                            <span>Lihat proses iterasi clustering di menu <a href="{{ route('clustering.kmeans') }}" class="text-blue-600 hover:underline">Proses K-Means</a></span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-medium text-blue-600 mr-2">Langkah 4:</span>
                            <span>Analisis hasil pengelompokan di menu <a href="{{ route('clustering.result') }}" class="text-blue-600 hover:underline">Hasil Clustering</a></span>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Informasi Algoritma -->
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tentang K-Means Clustering</h3>
                </div>
                <div class="px-6 py-5">
                    <div class="space-y-4 text-sm text-gray-600">
                        <p>
                            <span class="font-medium">K-Means</span> adalah algoritma clustering yang mengelompokkan data berdasarkan kesamaan karakteristik.
                        </p>
                        <p>
                            Algoritma ini bekerja dengan menghitung jarak setiap data ke pusat cluster (centroid) dan menempatkan data ke cluster terdekat.
                        </p>
                        <p>
                            Proses clustering membantu mengidentifikasi pola dalam data akademik dan non-akademik siswa untuk mendukung pengambilan keputusan.
                        </p>
                        <p class="pt-2">
                            <a href="{{ route('clustering.setup') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Mulai Clustering
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
