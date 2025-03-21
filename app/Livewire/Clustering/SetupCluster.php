<?php

namespace App\Livewire\Clustering;

use Livewire\Component;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class SetupCluster extends Component
{
    public $jumlahCluster = 3;
    public $maxIterasi = 100;
    public $tipeCentroid = 'mean'; // mean, random, first
    public $isProcessing = false;
    public $isError = false;
    public $errorMessage = '';
    public $isTooFewData = false;
    
    protected $rules = [
        'jumlahCluster' => 'required|integer|min:2|max:10',
        'maxIterasi' => 'required|integer|min:10|max:1000',
        'tipeCentroid' => 'required|in:mean,random,first',
    ];
    
    public function mount()
    {
        // Cek jumlah data siswa
        $jumlahData = Student::count();
        if ($jumlahData < 10) {
            $this->isTooFewData = true;
            $this->errorMessage = "Data siswa tidak mencukupi untuk clustering. Minimal diperlukan 10 data siswa.";
        }
        
        // Ambil nilai K optimal dari session jika tersedia
        if (session()->has('optimalK')) {
            $optimalK = session()->get('optimalK');
            if ($optimalK >= 2 && $optimalK <= 10) {
                $this->jumlahCluster = $optimalK;
            }
        }
    }
    
    public function runClustering()
    {
        $this->validate();
        
        try {
            $this->isProcessing = true;
            
            // Ambil data untuk clustering
            $data = $this->getClusteringData();
            
            if (count($data) < $this->jumlahCluster) {
                $this->isError = true;
                $this->errorMessage = "Jumlah data (" . count($data) . ") kurang dari jumlah cluster yang diminta ({$this->jumlahCluster})";
                $this->isProcessing = false;
                return;
            }
            
            // Simpan parameter untuk digunakan di halaman K-Means
            Session::put('kmeans_params', [
                'jumlahCluster' => $this->jumlahCluster,
                'maxIterasi' => $this->maxIterasi,
                'tipeCentroid' => $this->tipeCentroid
            ]);
            
            // Jalankan algoritma kmeans
            $result = $this->kMeans($data, $this->jumlahCluster, $this->maxIterasi, $this->tipeCentroid);
            
            // Simpan hasil ke session
            Session::put('kmeans_result', $result);
            
            // Redirect ke halaman hasil clustering
            return redirect()->route('clustering.result');
            
        } catch (\Exception $e) {
            $this->isError = true;
            $this->errorMessage = "Terjadi kesalahan: " . $e->getMessage();
        } finally {
            $this->isProcessing = false;
        }
    }
    
 
    private function getClusteringData()
    {
        $students = Student::all();
        $data = [];
        
        foreach ($students as $student) {
            // Menggunakan nilai_sikap_numerik untuk mengkonversi huruf ke angka
            $data[] = [
                'id' => $student->id,
                'features' => [
                    $student->uts,
                    $student->uas,
                    $student->getNilaiSikapNumerikAttribute(), // Konversi dari huruf ke angka
                    $student->getNilaiPramukaNumerikAttribute(), // Konversi pramuka dari huruf ke angka
                    $student->getNilaiPmrNumerikAttribute(), // Konversi pmr dari huruf ke angka
                    $student->kehadiran
                ],
                'name' => $student->nama,
                'kelas' => $student->kelas
            ];
        }
        
        return $data;
    }
    
    /**
     * Algoritma K-Means Clustering
     * 
     * @param array $data Data yang akan dicluster dalam format [id, features, name, kelas]
     * @param int $k Jumlah cluster
     * @param int $maxIterations Maksimum iterasi
     * @param string $centroidType Tipe pemilihan centroid awal (mean, random, first)
     * @return array Hasil clustering [clusters, centroids, iterations, sse]
     */
    private function kMeans($data, $k, $maxIterations, $centroidType = 'mean')
    {
        // Inisialisasi centroid berdasarkan tipe
        $centroids = $this->initializeCentroids($data, $k, $centroidType);
        
        // Inisialisasi cluster assignment
        $clusters = array_fill(0, $k, []);
        $previousClusters = null;
        $iterations = 0;
        $converged = false;
        
        // Untuk menyimpan history iterasi
        $iterationHistory = [];
        
        // Iterasi sampai konvergen atau mencapai maksimum iterasi
        while (!$converged && $iterations < $maxIterations) {
            // Reset cluster
            $clusters = array_fill(0, $k, []);
            
            // Untuk menyimpan matriks jarak dan penugasan cluster
            $distanceMatrix = [];
            $clusterAssignments = [];
            
            // Assign setiap data ke cluster terdekat
            foreach ($data as $index => $item) {
                $minDistance = PHP_FLOAT_MAX;
                $clusterIndex = 0;
                $distances = [];
                
                // Cari cluster dengan jarak terdekat
                for ($i = 0; $i < $k; $i++) {
                    $distance = $this->euclideanDistance($item['features'], $centroids[$i]);
                    $distances[$i] = $distance;
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $clusterIndex = $i;
                    }
                }
                
                // Assign data ke cluster
                $clusters[$clusterIndex][] = $item;
                
                // Simpan informasi jarak dan penugasan cluster
                $distanceMatrix[$item['id']] = [
                    'name' => $item['name'],
                    'distances' => $distances
                ];
                $clusterAssignments[$item['id']] = $clusterIndex + 1;
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
            
            // Simpan snapshot iterasi saat ini
            $sseIter = $this->calculateSSE($clusters, $centroids);
            $iterationHistory[] = [
                'iteration' => $iterations + 1,
                'clusters' => $clusters,
                'centroids' => $centroids,
                'sse' => $sseIter,
                'distanceMatrix' => $distanceMatrix,
                'clusterAssignments' => $clusterAssignments
            ];
            
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
            'sse' => $sse,
            'iterationHistory' => $iterationHistory
        ];
    }
    
    /**
     * Inisialisasi centroid awal berdasarkan tipe
     */
    private function initializeCentroids($data, $k, $type)
    {
        $featureCount = count($data[0]['features']);
        $centroids = [];
        
        switch ($type) {
            case 'random':
                // Pilih K data secara acak sebagai centroid awal
                $indices = array_rand($data, $k);
                if (!is_array($indices)) {
                    $indices = [$indices];
                }
                
                foreach ($indices as $index) {
                    $centroids[] = $data[$index]['features'];
                }
                break;
                
            case 'first':
                // Pilih K data pertama sebagai centroid awal
                for ($i = 0; $i < $k; $i++) {
                    if (isset($data[$i])) {
                        $centroids[] = $data[$i]['features'];
                    } else {
                        // Jika data kurang dari K, gunakan data terakhir
                        $centroids[] = $data[count($data) - 1]['features'];
                    }
                }
                break;
                
            case 'mean':
            default:
                // Hitung rata-rata keseluruhan data
                $allFeatures = array_column($data, 'features');
                $means = array_fill(0, $featureCount, 0);
                
                // Hitung total untuk setiap fitur
                foreach ($allFeatures as $features) {
                    for ($i = 0; $i < $featureCount; $i++) {
                        $means[$i] += $features[$i];
                    }
                }
                
                // Hitung rata-rata
                for ($i = 0; $i < $featureCount; $i++) {
                    $means[$i] /= count($data);
                }
                
                // Buat variasi centroid sekitar rata-rata dengan random offset
                for ($i = 0; $i < $k; $i++) {
                    $centroid = [];
                    for ($j = 0; $j < $featureCount; $j++) {
                        // Tambahkan offset acak ±20% dari rata-rata
                        $offset = $means[$j] * (mt_rand(-20, 20) / 100);
                        $centroid[$j] = $means[$j] + $offset;
                    }
                    $centroids[] = $centroid;
                }
                break;
        }
        
        return $centroids;
    }
    
    /**
     * Hitung jarak Euclidean antara dua titik
     */
    private function euclideanDistance($a, $b)
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }
    
    /**
     * Cek apakah dua cluster assignment sama
     */
    private function clustersEqual($a, $b)
    {
        if (count($a) !== count($b)) {
            return false;
        }
        
        for ($i = 0; $i < count($a); $i++) {
            if (count($a[$i]) !== count($b[$i])) {
                return false;
            }
            
            $aIds = array_column($a[$i], 'id');
            $bIds = array_column($b[$i], 'id');
            
            sort($aIds);
            sort($bIds);
            
            if ($aIds !== $bIds) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Hitung centroid baru berdasarkan rata-rata cluster
     */
    private function calculateCentroid($cluster)
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
    private function calculateSSE($clusters, $centroids)
    {
        $sse = 0;
        
        for ($i = 0; $i < count($clusters); $i++) {
            foreach ($clusters[$i] as $item) {
                $sse += pow($this->euclideanDistance($item['features'], $centroids[$i]), 2);
            }
        }
        
        return $sse;
    }
    
    public function render()
    {
        return view('livewire.clustering.setup-cluster');
    }
}
