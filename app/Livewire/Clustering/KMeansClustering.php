<?php

namespace App\Livewire\Clustering;

use Livewire\Component;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class KMeansClustering extends Component
{
    public $iterationHistory = null;
    public $hasData = false;
    public $jumlahCluster = 0;
    public $maxIterasi = 0;
    public $tipeCentroid = '';
    public $totalIterasi = 0;
    public $converged = false;
    public $finalSSE = 0;
    public $isDataValid = true;
    
    public function mount()
    {
        // Ambil data hasil clustering dari session
        $kmeansResult = Session::get('kmeans_result');
        
        if ($kmeansResult && isset($kmeansResult['iterationHistory'])) {
            $this->hasData = true;
            $this->iterationHistory = $kmeansResult['iterationHistory'];
            $this->totalIterasi = $kmeansResult['iterations'];
            $this->converged = $kmeansResult['converged'];
            $this->finalSSE = $kmeansResult['sse'];
            
            // Validasi data iterasi
            $this->validateIterationData();
            
            // Ambil parameter yang digunakan
            $setupParams = Session::get('kmeans_params');
            if ($setupParams) {
                $this->jumlahCluster = $setupParams['jumlahCluster'] ?? 0;
                $this->maxIterasi = $setupParams['maxIterasi'] ?? 0;
                $this->tipeCentroid = $setupParams['tipeCentroid'] ?? '';
            }
        }
    }
    
    protected function validateIterationData()
    {
        if (!is_array($this->iterationHistory) || empty($this->iterationHistory)) {
            $this->isDataValid = false;
            return;
        }
        
        foreach ($this->iterationHistory as $key => $iteration) {
            // Validasi struktur data yang dibutuhkan
            if (!isset($iteration['iteration']) || 
                !isset($iteration['centroids']) || 
                !isset($iteration['sse']) ||
                !isset($iteration['distanceMatrix']) ||
                !isset($iteration['clusterAssignments'])) {
                
                // Jika salah satu kunci tidak ada, jalankan proses recovery
                $this->recoverMissingData($key);
            }
        }
    }
    
    protected function recoverMissingData($iterIndex)
    {
        if (!isset($this->iterationHistory[$iterIndex]['distanceMatrix'])) {
            $this->iterationHistory[$iterIndex]['distanceMatrix'] = [];
            
            // Jika ada data clusters, gunakan untuk membuat distanceMatrix palsu
            if (isset($this->iterationHistory[$iterIndex]['clusters'])) {
                foreach ($this->iterationHistory[$iterIndex]['clusters'] as $clusterIdx => $cluster) {
                    foreach ($cluster as $item) {
                        $distances = [];
                        for ($i = 0; $i < $this->jumlahCluster; $i++) {
                            $distances[$i] = ($i == $clusterIdx) ? 0 : 99.99;
                        }
                        
                        $this->iterationHistory[$iterIndex]['distanceMatrix'][$item['id']] = [
                            'name' => $item['name'],
                            'distances' => $distances
                        ];
                        
                        // Juga buat clusterAssignments jika tidak ada
                        if (!isset($this->iterationHistory[$iterIndex]['clusterAssignments'])) {
                            $this->iterationHistory[$iterIndex]['clusterAssignments'] = [];
                        }
                        
                        $this->iterationHistory[$iterIndex]['clusterAssignments'][$item['id']] = $clusterIdx + 1;
                    }
                }
            }
        }
    }
    
    public function formatCentroidValue($value)
    {
        return number_format($value, 2);
    }
    
    public function render()
    {
        return view('livewire.clustering.k-means-clustering');
    }

    /**
     * Mengambil data untuk clustering dari model
     * 
     * @return array
     */
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
}
