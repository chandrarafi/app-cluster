<?php

namespace App\Services\KMeans;

use Illuminate\Support\Facades\Log;

class ElbowMethodService
{
    /**
     * Menghitung Elbow Method untuk menemukan jumlah K optimal
     * 
     * @param array $data Data untuk clustering
     * @param array $kValues Array yang berisi nilai K yang akan dicoba
     * @param int $maxIterations Maksimum iterasi untuk K-Means
     * @return array Hasil SSE untuk setiap nilai K
     */
    public function calculateElbowMethod(array $data, array $kValues, int $maxIterations = 100): array
    {
        $results = [];
        
        foreach ($kValues as $k) {
            // Jalankan algoritma K-Means untuk nilai K ini
            $kmeans = $this->kMeans($data, $k, $maxIterations);
            
            // Simpan hasil SSE
            $results[] = [
                'k' => $k,
                'sse' => $kmeans['sse']
            ];
        }
        
        return $results;
    }
    
    /**
     * Menemukan K optimal dari hasil elbow method
     * 
     * @param array $results Hasil SSE untuk setiap nilai K
     * @return int Nilai K optimal
     */
    public function findOptimalK(array $results): int
    {
        if (count($results) < 2) {
            return $results[0]['k'];
        }
        
        // Urutkan hasil berdasarkan nilai K
        usort($results, function($a, $b) {
            return $a['k'] <=> $b['k'];
        });
        
        // Menghitung total variance (SSE untuk K minimum)
        $totalVariance = $results[0]['sse'];
        
        // Hitung persentase variance explained untuk setiap K
        $variances = [];
        foreach ($results as $result) {
            $explainedVariance = 1 - ($result['sse'] / $totalVariance);
            $variances[] = [
                'k' => $result['k'],
                'explained_variance' => $explainedVariance
            ];
        }
        
        // Hitung perbedaan persentase variance explained antara K berurutan
        $differences = [];
        for ($i = 1; $i < count($variances); $i++) {
            $differences[] = [
                'k' => $variances[$i]['k'],
                'diff' => $variances[$i]['explained_variance'] - $variances[$i-1]['explained_variance']
            ];
        }
        
        // Mencari "elbow point" - titik di mana penambahan persentase mulai melambat signifikan
        // Gunakan threshold 0.1 (10%) - jika kenaikan kurang dari 10%, dianggap sebagai elbow point
        $threshold = 0.1;
        $optimalK = $results[0]['k']; // Default ke K terkecil
        
        foreach ($differences as $diff) {
            // Jika perbedaan masih cukup signifikan, update optimal K
            if ($diff['diff'] >= $threshold) {
                $optimalK = $diff['k'];
            } else {
                // Begitu menemukan perbedaan yang tidak signifikan, itu adalah titik elbow
                break;
            }
        }
        
        Log::info('Optimal K found: ' . $optimalK);
        return $optimalK;
    }
    
    /**
     * Algoritma K-Means Clustering
     * 
     * @param array $data Data yang akan dicluster
     * @param int $k Jumlah cluster
     * @param int $maxIterations Maksimum iterasi
     * @return array Hasil clustering [clusters, centroids, iterations, sse]
     */
    private function kMeans(array $data, int $k, int $maxIterations): array
    {
        // Pastikan data tidak kosong
        if (empty($data)) {
            return [
                'clusters' => [],
                'centroids' => [],
                'iterations' => 0,
                'sse' => 0
            ];
        }
        
        // Ekstrak fitur dari data
        $features = [];
        foreach ($data as $item) {
            $features[] = $this->extractFeatures($item);
        }
        
        // Inisialisasi centroid
        $centroids = $this->initializeCentroids($features, $k);
        
        // Inisialisasi cluster assignment
        $clusters = array_fill(0, $k, []);
        $previousClusters = null;
        $iterations = 0;
        $converged = false;
        
        // Iterasi sampai konvergen atau mencapai maksimum iterasi
        while (!$converged && $iterations < $maxIterations) {
            // Reset cluster
            $clusters = array_fill(0, $k, []);
            
            // Assign setiap data ke cluster terdekat
            foreach ($data as $index => $item) {
                $itemFeatures = $this->extractFeatures($item);
                $minDistance = PHP_FLOAT_MAX;
                $clusterIndex = 0;
                
                // Cari cluster dengan jarak terdekat
                for ($i = 0; $i < $k; $i++) {
                    $distance = $this->euclideanDistance($itemFeatures, $centroids[$i]);
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $clusterIndex = $i;
                    }
                }
                
                // Assign data ke cluster
                $clusters[$clusterIndex][] = [
                    'index' => $index,
                    'data' => $item,
                    'features' => $itemFeatures
                ];
            }
            
            // Cek konvergensi - jika cluster assignment tidak berubah
            if ($previousClusters !== null && $this->clustersEqual($clusters, $previousClusters)) {
                $converged = true;
            }
            
            // Update centroid berdasarkan rata-rata cluster
            for ($i = 0; $i < $k; $i++) {
                if (!empty($clusters[$i])) {
                    $newCentroid = $this->calculateCentroid($clusters[$i]);
                    $centroids[$i] = $newCentroid;
                }
            }
            
            $previousClusters = $clusters;
            $iterations++;
        }
        
        // Hitung Sum of Squared Errors (SSE)
        $sse = $this->calculateSSE($clusters, $centroids);
        
        return [
            'clusters' => $clusters,
            'centroids' => $centroids,
            'iterations' => $iterations,
            'converged' => $converged,
            'sse' => $sse
        ];
    }
    
    /**
     * Ekstrak fitur dari data item
     */
    private function extractFeatures(array $item): array
    {
        $features = [];
        
        // Ambil semua nilai numerik dari item data
        foreach ($item as $key => $value) {
            // Kecualikan 'id' dan kunci non-numerik lainnya
            if ($key !== 'id' && is_numeric($value)) {
                $features[] = (float)$value;
            }
        }
        
        return $features;
    }
    
    /**
     * Inisialisasi centroid awal
     */
    private function initializeCentroids(array $features, int $k): array
    {
        $dataCount = count($features);
        $featureCount = count($features[0]);
        $centroids = [];
        
        // Jika jumlah data lebih sedikit dari k, gunakan data yang ada
        if ($dataCount <= $k) {
            return array_slice($features, 0, $dataCount);
        }
        
        // Gunakan K-means++ untuk inisialisasi centroid
        // Pilih centroid pertama secara acak
        $firstIndex = rand(0, $dataCount - 1);
        $centroids[] = $features[$firstIndex];
        
        // Pilih centroid berikutnya berdasarkan probabilitas jarak
        for ($i = 1; $i < $k; $i++) {
            $distances = [];
            $sum = 0;
            
            // Hitung jarak terpendek dari setiap titik ke centroid yang sudah ada
            foreach ($features as $point) {
                $minDistance = PHP_FLOAT_MAX;
                foreach ($centroids as $centroid) {
                    $distance = $this->euclideanDistance($point, $centroid);
                    $minDistance = min($minDistance, $distance);
                }
                $distances[] = $minDistance ** 2; // Kuadrat jarak
                $sum += $minDistance ** 2;
            }
            
            // Normalisasi jarak menjadi probabilitas
            for ($j = 0; $j < count($distances); $j++) {
                $distances[$j] /= $sum;
            }
            
            // Pilih titik berikutnya berdasarkan probabilitas terbesar
            $r = mt_rand() / mt_getrandmax();
            $cumulativeProb = 0;
            $nextIndex = 0;
            
            for ($j = 0; $j < count($distances); $j++) {
                $cumulativeProb += $distances[$j];
                if ($r <= $cumulativeProb) {
                    $nextIndex = $j;
                    break;
                }
            }
            
            $centroids[] = $features[$nextIndex];
        }
        
        return $centroids;
    }
    
    /**
     * Hitung jarak Euclidean antara dua titik
     */
    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0;
        for ($i = 0; $i < min(count($a), count($b)); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }
    
    /**
     * Cek apakah dua cluster assignment sama
     */
    private function clustersEqual(array $a, array $b): bool
    {
        if (count($a) !== count($b)) {
            return false;
        }
        
        for ($i = 0; $i < count($a); $i++) {
            if (count($a[$i]) !== count($b[$i])) {
                return false;
            }
            
            $aIndices = array_column($a[$i], 'index');
            $bIndices = array_column($b[$i], 'index');
            
            sort($aIndices);
            sort($bIndices);
            
            if ($aIndices !== $bIndices) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Hitung centroid baru berdasarkan rata-rata cluster
     */
    private function calculateCentroid(array $cluster): array
    {
        if (empty($cluster)) {
            return [];
        }
        
        $featureCount = count($cluster[0]['features']);
        $centroid = array_fill(0, $featureCount, 0);
        
        foreach ($cluster as $item) {
            for ($i = 0; $i < $featureCount; $i++) {
                $centroid[$i] += $item['features'][$i];
            }
        }
        
        for ($i = 0; $i < $featureCount; $i++) {
            $centroid[$i] /= count($cluster);
        }
        
        return $centroid;
    }
    
    /**
     * Hitung Sum of Squared Errors (SSE)
     */
    private function calculateSSE(array $clusters, array $centroids): float
    {
        $sse = 0;
        
        for ($i = 0; $i < count($clusters); $i++) {
            foreach ($clusters[$i] as $item) {
                $sse += pow($this->euclideanDistance($item['features'], $centroids[$i]), 2);
            }
        }
        
        return $sse;
    }
} 