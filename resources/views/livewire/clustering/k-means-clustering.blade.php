<x-slot:title>
    {{ __('K-Means Clustering') }}
</x-slot:title>

<div class="py-6">
    <div class="container mx-auto">
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('K-Means Clustering') }}</h3>
                </div>

                <div class="px-6 py-4">
                    @if(!$hasData)
                        <p class="mb-4 text-gray-700">{{ __('Belum ada proses clustering yang dijalankan.') }}</p>

                        <div class="mt-6">
                            <a href="{{ route('clustering.setup') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Setup Cluster') }}
                            </a>
                        </div>
                    @else
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-800 mb-2">{{ __('Parameter Clustering') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                                    <p class="text-sm font-medium text-gray-500">{{ __('Jumlah Cluster (K)') }}</p>
                                    <p class="text-xl font-semibold mt-1 text-blue-600">{{ $jumlahCluster }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                                    <p class="text-sm font-medium text-gray-500">{{ __('Maksimum Iterasi') }}</p>
                                    <p class="text-xl font-semibold mt-1 text-blue-600">{{ $maxIterasi }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                                    <p class="text-sm font-medium text-gray-500">{{ __('Tipe Centroid Awal') }}</p>
                                    <p class="text-xl font-semibold mt-1 text-blue-600">{{ ucfirst($tipeCentroid) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-800 mb-2">{{ __('Hasil Algoritma') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                                    <p class="text-sm font-medium text-gray-500">{{ __('Jumlah Iterasi') }}</p>
                                    <p class="text-xl font-semibold mt-1 text-blue-600">{{ $totalIterasi }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                                    <p class="text-sm font-medium text-gray-500">{{ __('Status Konvergensi') }}</p>
                                    <p class="text-xl font-semibold mt-1">
                                        @if($converged)
                                            <span class="text-green-500">{{ __('Konvergen') }}</span>
                                        @else
                                            <span class="text-yellow-500">{{ __('Henti - Iterasi Maksimum') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                                    <p class="text-sm font-medium text-gray-500">{{ __('Nilai SSE Akhir') }}</p>
                                    <p class="text-xl font-semibold mt-1 text-blue-600">{{ number_format($finalSSE, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-800 mb-3">{{ __('Proses Iterasi K-Means') }}</h4>
                            
                            <div class="space-y-6">
                                @foreach($iterationHistory as $iteration)
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 rounded-t-lg">
                                            <h5 class="text-base font-medium text-gray-800">
                                                {{ __('Iterasi') }} #{{ $iteration['iteration'] }} - 
                                                <span class="text-blue-600">SSE: {{ number_format($iteration['sse'], 2) }}</span>
                                            </h5>
                                        </div>
                                        
                                        <div class="p-4">
                                            <!-- Tahap 1: Centroid -->
                                            <div class="mb-4">
                                                <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                    <span class="inline-flex items-center justify-center w-5 h-5 mr-2 text-xs font-bold text-white bg-blue-600 rounded-full">1</span>
                                                    {{ __('Centroid pada Iterasi Ini') }}
                                                </h6>
                                                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cluster') }}</th>
                                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('UTS') }}</th>
                                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('UAS') }}</th>
                                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Sikap') }}</th>
                                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Pramuka') }}</th>
                                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('PMR') }}</th>
                                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kehadiran') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @foreach($iteration['centroids'] as $idx => $centroid)
                                                                <tr>
                                                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                        Cluster {{ $idx + 1 }}
                                                                    </td>
                                                                    @foreach($centroid as $value)
                                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                                            {{ number_format($value, 2) }}
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <!-- Tahap 2: Jarak Euclidean (Penjelasan) -->
                                            <div class="mb-4">
                                                <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                    <span class="inline-flex items-center justify-center w-5 h-5 mr-2 text-xs font-bold text-white bg-blue-600 rounded-full">2</span>
                                                    {{ __('Perhitungan Jarak Euclidean') }}
                                                </h6>
                                                
                                                <div class="p-3 bg-blue-50 rounded-lg mb-3">
                                                    <p class="text-sm text-gray-600">
                                                        {{ __('Setiap data dihitung jaraknya ke masing-masing centroid menggunakan rumus jarak Euclidean:') }}
                                                        <span class="font-mono text-xs bg-white px-1 py-0.5 rounded">d(x,y) = √Σ(xᵢ-yᵢ)²</span>
                                                    </p>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ __('Setiap data akan dimasukkan ke cluster dengan jarak centroid terdekat.') }}
                                                    </p>
                                                </div>
                                                
                                                <div>
                                                    <h6 class="text-sm font-medium text-gray-700 mb-2">
                                                        {{ __('Perulangan') }} {{ $iteration['iteration'] }} - {{ __('Hitung Euclidean Distance') }}
                                                    </h6>
                                                </div>
                                                
                                                @if(isset($iteration['distanceMatrix']) && !empty($iteration['distanceMatrix']))
                                                <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                                                    @foreach($iteration['centroids'] as $idx => $centroid)
                                                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                            {{ __('Cluster') }} {{ $idx + 1 }}
                                                                        </th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($iteration['distanceMatrix'] as $id => $item)
                                                                    <tr class="hover:bg-gray-50">
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 border-t border-gray-200">
                                                                            {{ $item['name'] }}
                                                                        </td>
                                                                        @foreach($item['distances'] as $distance)
                                                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 border-t border-gray-200">
                                                                                {{ number_format($distance, 14) }}
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    <p class="text-sm text-yellow-600">
                                                        {{ __('Data jarak Euclidean tidak tersedia untuk iterasi ini. Mungkin proses clustering perlu dijalankan ulang.') }}
                                                    </p>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Tahap 3: Hasil Cluster -->
                                            <div>
                                                <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                    <span class="inline-flex items-center justify-center w-5 h-5 mr-2 text-xs font-bold text-white bg-blue-600 rounded-full">3</span>
                                                    {{ __('Hasil Pengelompokan Data') }}
                                                </h6>
                                                
                                                <div>
                                                    <h6 class="text-sm font-medium text-gray-700 mb-2">
                                                        {{ __('Perulangan') }} {{ $iteration['iteration'] }} - {{ __('Hasil Cluster') }}
                                                    </h6>
                                                </div>
                                                
                                                @if(isset($iteration['distanceMatrix']) && !empty($iteration['distanceMatrix']) && isset($iteration['clusterAssignments']))
                                                <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <tbody>
                                                                @foreach($iteration['distanceMatrix'] as $id => $item)
                                                                    <tr class="hover:bg-gray-50">
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 border-t border-gray-200">
                                                                            {{ $item['name'] }}
                                                                        </td>
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-t border-gray-200">
                                                                            {{ $iteration['clusterAssignments'][$id] }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    <p class="text-sm text-yellow-600">
                                                        {{ __('Data hasil pengelompokan tidak tersedia untuk iterasi ini. Mungkin proses clustering perlu dijalankan ulang.') }}
                                                    </p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('clustering.result') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Lihat Hasil Clustering') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
