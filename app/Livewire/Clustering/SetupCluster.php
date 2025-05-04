<?php

namespace App\Livewire\Clustering;

use Livewire\Component;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class SetupCluster extends Component
{
    public $jumlahCluster = 3;
    public $maxIterasi = 100;
    public $tipeCentroid = 'mean'; // mean, random, first, kmeans++
    public $isProcessing = false;
    public $isError = false;
    public $errorMessage = '';
    public $isTooFewData = false;

    protected $rules = [
        'jumlahCluster' => 'required|integer|min:2|max:10',
        'maxIterasi' => 'required|integer|min:10|max:1000',
        'tipeCentroid' => 'required|in:mean,random,first,kmeans++',
    ];

    public function mount()
    {
        $jumlahData = Student::count();
        if ($jumlahData < 10) {
            $this->isTooFewData = true;
            $this->errorMessage = "Data siswa tidak mencukupi untuk clustering. Minimal diperlukan 10 data siswa.";
        }

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

            $data = $this->getClusteringData();

            if (count($data) < $this->jumlahCluster) {
                $this->isError = true;
                $this->errorMessage = "Jumlah data (" . count($data) . ") kurang dari jumlah cluster yang diminta ({$this->jumlahCluster})";
                $this->isProcessing = false;
                return;
            }

            Session::put('kmeans_params', [
                'jumlahCluster' => $this->jumlahCluster,
                'maxIterasi' => $this->maxIterasi,
                'tipeCentroid' => $this->tipeCentroid
            ]);

            $result = $this->kMeans($data, $this->jumlahCluster, $this->maxIterasi, $this->tipeCentroid);

            Session::put('kmeans_result', $result);

            return redirect()->route('clustering.kmeans');
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
     * @param string $centroidType Tipe pemilihan centroid awal (mean, random, first, kmeans++)
     * @return array Hasil clustering [clusters, centroids, iterations, sse]
     */
    private function kMeans($data, $k, $maxIterations, $centroidType = 'mean')
    {
        // Set seed tetap untuk memastikan hasil yang konsisten
        mt_srand(12345);

        $centroids = $this->initializeCentroids($data, $k, $centroidType);

        $clusters = array_fill(0, $k, []);
        $previousClusters = null;
        $iterations = 0;
        $converged = false;

        $iterationHistory = [];

        while (!$converged && $iterations < $maxIterations) {
            $clusters = array_fill(0, $k, []);

            $distanceMatrix = [];
            $clusterAssignments = [];

            foreach ($data as $index => $item) {
                $minDistance = PHP_FLOAT_MAX;
                $clusterIndex = 0;
                $distances = [];

                for ($i = 0; $i < $k; $i++) {
                    $distance = $this->euclideanDistance($item['features'], $centroids[$i]);
                    $distances[$i] = $distance;
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $clusterIndex = $i;
                    }
                }

                $clusters[$clusterIndex][] = $item;

                $distanceMatrix[$item['id']] = [
                    'name' => $item['name'],
                    'distances' => $distances
                ];
                $clusterAssignments[$item['id']] = $clusterIndex + 1;
            }

            if ($previousClusters !== null && $this->clustersEqual($clusters, $previousClusters)) {
                $converged = true;
            }

            for ($i = 0; $i < $k; $i++) {
                if (!empty($clusters[$i])) {
                    $newCentroid = $this->calculateCentroid($clusters[$i]);
                    $centroids[$i] = $newCentroid;
                }
            }

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


    private function initializeCentroids($data, $k, $type)
    {
        $featureCount = count($data[0]['features']);
        $centroids = [];

        switch ($type) {
            case 'random':
                // Gunakan array_keys untuk konsistensi
                $dataIndices = array_keys($data);
                sort($dataIndices);
                $indices = array_slice($dataIndices, 0, $k);

                foreach ($indices as $index) {
                    $centroids[] = $data[$index]['features'];
                }
                break;

            case 'first':
                for ($i = 0; $i < $k; $i++) {
                    if (isset($data[$i])) {
                        $centroids[] = $data[$i]['features'];
                    } else {
                        $centroids[] = $data[count($data) - 1]['features'];
                    }
                }
                break;

            case 'kmeans++':
                // Implementasi K-means++ untuk inisialisasi yang lebih stabil
                // Pilih centroid pertama secara acak
                $firstIndex = 0; // Selalu pilih data pertama untuk konsistensi
                $centroids[] = $data[$firstIndex]['features'];

                // Pilih centroid lainnya berdasarkan probabilitas jarak
                for ($i = 1; $i < $k; $i++) {
                    $distances = [];
                    $sumDistances = 0;

                    // Hitung jarak minimum dari setiap titik ke centroid yang sudah ada
                    foreach ($data as $index => $point) {
                        $minDistance = PHP_FLOAT_MAX;
                        foreach ($centroids as $centroid) {
                            $distance = $this->euclideanDistance($point['features'], $centroid);
                            $minDistance = min($minDistance, $distance);
                        }
                        $distances[$index] = $minDistance ** 2; // Square untuk meningkatkan probabilitas
                        $sumDistances += $distances[$index];
                    }

                    // Pilih titik baru dengan probabilitas proporsional terhadap jarak²
                    $target = mt_rand() / mt_getrandmax() * $sumDistances;
                    $cumulativeProb = 0;
                    $selectedIndex = 0;

                    // Sort by index for consistency
                    ksort($distances);

                    foreach ($distances as $index => $distance) {
                        $cumulativeProb += $distance;
                        if ($cumulativeProb >= $target) {
                            $selectedIndex = $index;
                            break;
                        }
                    }

                    $centroids[] = $data[$selectedIndex]['features'];
                }
                break;

            case 'mean':
            default:
                // Calculate overall mean
                $allFeatures = array_column($data, 'features');
                $means = array_fill(0, $featureCount, 0);

                foreach ($allFeatures as $features) {
                    for ($i = 0; $i < $featureCount; $i++) {
                        $means[$i] += $features[$i];
                    }
                }

                for ($i = 0; $i < $featureCount; $i++) {
                    $means[$i] /= count($data);
                }

                // Generate centroid deterministically based on mean values
                for ($i = 0; $i < $k; $i++) {
                    $centroid = [];
                    for ($j = 0; $j < $featureCount; $j++) {
                        // Use deterministic offset based on cluster index
                        $offset = $means[$j] * (($i * 5 - 10) / 100);
                        $centroid[$j] = $means[$j] + $offset;
                    }
                    $centroids[] = $centroid;
                }
                break;
        }

        return $centroids;
    }


    private function euclideanDistance($a, $b)
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }


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
