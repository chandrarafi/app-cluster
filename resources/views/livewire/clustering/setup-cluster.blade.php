<x-slot:title>
    {{ __('Tentukan Cluster') }}
</x-slot:title>

<div class="py-6">
    <div class="container mx-auto">
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Konfigurasi Clustering') }}</h3>
                </div>

                <div class="px-6 py-5">
                    @if ($isTooFewData)
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                        <p>{{ $errorMessage }}</p>
                        <p class="mt-2">
                            <a href="{{ route('student.dataset') }}" class="text-red-700 underline">Tambahkan data siswa</a> terlebih dahulu.
                        </p>
                    </div>
                    @endif

                    @if ($isError)
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                        {{ $errorMessage }}
                    </div>
                    @endif

                    <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Penjelasan:</strong> K-Means adalah algoritma clustering yang membagi data menjadi K kelompok berdasarkan kemiripan. Algoritma akan menempatkan titik-titik data ke dalam kelompok yang memiliki jarak terdekat dengan pusat cluster (centroid).
                                </p>
                                <p class="text-sm text-blue-700 mt-1">
                                    <strong>Proses:</strong> (1) Tentukan jumlah cluster, (2) Pilih centroid awal, (3) Tetapkan setiap titik data ke cluster terdekat, (4) Hitung ulang centroid, (5) Ulangi langkah 3-4 hingga konvergen atau mencapai batas iterasi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form wire:submit="runClustering" class="max-w-lg">
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-700 mb-2">{{ __('Parameter Clustering') }}</h4>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="jumlahCluster" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Jumlah Cluster (K)') }}</label>
                                    <div class="flex items-center">
                                        <input type="range" wire:model.live="jumlahCluster" id="jumlahCluster" min="2" max="10" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" />
                                        <span class="ml-3 w-8 text-center text-gray-700">{{ $jumlahCluster }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Jumlah kelompok yang akan dibentuk (2-10)</p>
                                    @error('jumlahCluster') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="maxIterasi" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Maksimum Iterasi') }}</label>
                                    <div class="flex items-center">
                                        <input type="range" wire:model.live="maxIterasi" id="maxIterasi" min="10" max="1000" step="10" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" />
                                        <span class="ml-3 w-12 text-center text-gray-700">{{ $maxIterasi }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Batas maksimum pengulangan algoritma (10-1000)</p>
                                    @error('maxIterasi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="tipeCentroid" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Metode Inisialisasi Centroid') }}</label>
                                    <select wire:model="tipeCentroid" id="tipeCentroid" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        <option value="mean">Rata-rata data (dengan variasi acak)</option>
                                        <option value="random">Pemilihan acak dari data</option>
                                        <option value="first">Data-data pertama</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Cara memilih titik pusat awal</p>
                                    @error('tipeCentroid') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200 ease-in-out" wire:loading.attr="disabled" @if($isTooFewData) disabled @endif>
                                <span wire:loading.remove wire:target="runClustering">{{ __('Mulai Clustering') }}</span>
                                <span wire:loading wire:target="runClustering">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('Memproses...') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-6 bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Informasi Tambahan') }}</h3>
                </div>
                
                <div class="px-6 py-5">
                    <div class="prose max-w-none">
                        <h4>Metode K-Means Clustering</h4>
                        <p>K-Means adalah algoritma yang membagi data menjadi K kelompok non-overlap di mana setiap titik data hanya dapat menjadi anggota satu kelompok. Algoritma ini mencoba membuat kelompok-kelompok data di mana variasi di dalam kelompok sekecil mungkin, tetapi variasi antar kelompok sebesar mungkin.</p>
                        
                        <h4>Cara Kerja</h4>
                        <ol>
                            <li>Tentukan jumlah cluster (K)</li>
                            <li>Inisialisasi K centroid (pusat cluster) secara acak dari data</li>
                            <li>Hitung jarak setiap data ke semua centroid</li>
                            <li>Tetapkan setiap data ke cluster dengan centroid terdekat</li>
                            <li>Hitung ulang posisi centroid berdasarkan rata-rata data dalam cluster</li>
                            <li>Ulangi langkah 3-5 hingga centroid tidak berubah atau mencapai jumlah iterasi maksimum</li>
                        </ol>
                        
                        <h4>Parameter yang Digunakan</h4>
                        <ul>
                            <li><strong>Jumlah Cluster (K)</strong>: Jumlah kelompok yang ingin dibentuk. Biasanya ditentukan berdasarkan analisis domain atau menggunakan metode Elbow.</li>
                            <li><strong>Maksimum Iterasi</strong>: Batas pengulangan algoritma untuk mencegah loop tak terbatas jika tidak konvergen.</li>
                            <li><strong>Metode Inisialisasi Centroid</strong>: Cara pemilihan titik pusat awal, yang dapat mempengaruhi hasil akhir clustering.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
