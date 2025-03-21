<?php

namespace App\Livewire\Clustering;

use Livewire\Component;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class ClusteringResult extends Component
{
    public $clusteringResult = null;
    public $isDataAvailable = false;
    public $clusters = [];
    public $centroids = [];
    public $iterations = 0;
    public $sse = 0;
    public $converged = false;
    public $elbowResults = null;
    public $optimalK = null;
    public $featureLabels = [
        'UTS', 
        'UAS', 
        'Sikap', 
        'Pramuka', 
        'PMR', 
        'Kehadiran'
    ];
    
    public function mount()
    {
        $result = Session::get('kmeans_result');
        
        if ($result) {
            $this->isDataAvailable = true;
            $this->clusters = $result['clusters'];
            $this->centroids = $result['centroids'];
            $this->iterations = $result['iterations'];
            $this->sse = $result['sse'];
            $this->converged = $result['converged'];
        }
        
        // Ambil hasil elbow method dari session jika tersedia
        if (Session::has('optimalK')) {
            $this->optimalK = Session::get('optimalK');
        }
        
        // Ambil data hasil elbow jika tersedia
        if (Session::has('elbow_results')) {
            $elbowData = Session::get('elbow_results');
            if (isset($elbowData['results'])) {
                $this->elbowResults = $elbowData['results'];
            }
        }
    }
    
    /**
     * Fungsi untuk memformat nilai numerik menjadi string dengan presisi tertentu
     */
    public function formatNumber($number, $precision = 2)
    {
        return number_format($number, $precision, '.', ',');
    }
    
    /**
     * Fungsi untuk mendapatkan warna cluster berdasarkan indeks
     */
    public function getClusterColor($index)
    {
        $colors = [
            'bg-blue-100 text-blue-800',
            'bg-green-100 text-green-800',
            'bg-yellow-100 text-yellow-800',
            'bg-red-100 text-red-800',
            'bg-purple-100 text-purple-800',
            'bg-pink-100 text-pink-800',
            'bg-indigo-100 text-indigo-800',
            'bg-gray-100 text-gray-800',
            'bg-teal-100 text-teal-800',
            'bg-orange-100 text-orange-800'
        ];
        
        return $colors[$index % count($colors)];
    }
    
    /**
     * Mengkonversi nilai numerik sikap kembali ke huruf
     */
    public function getNilaiSikapHuruf($nilai)
    {
        // Konversi nilai numerik menjadi huruf berdasarkan ketentuan baru
        if ($nilai >= 85) {
            return 'SB';
        } else if ($nilai >= 75) {
            return 'B';
        } else if ($nilai >= 65) {
            return 'C';
        } else {
            return 'K';
        }
    }
    
    /**
     * Mendapatkan informasi statistik tentang cluster
     */
    public function getClusterStats($clusterIndex)
    {
        $cluster = $this->clusters[$clusterIndex];
        $count = count($cluster);
        
        if ($count === 0) {
            return [
                'count' => 0,
                'avg_uts' => 0,
                'avg_uas' => 0,
                'avg_sikap' => 0,
                'sikap_huruf' => '-',
                'avg_pramuka' => 0,
                'pramuka_huruf' => '-',
                'avg_pmr' => 0,
                'pmr_huruf' => '-',
                'avg_kehadiran' => 0,
            ];
        }
        
        $totalUts = 0;
        $totalUas = 0;
        $totalSikap = 0;
        $totalPramuka = 0;
        $totalPmr = 0;
        $totalKehadiran = 0;
        
        foreach ($cluster as $item) {
            $totalUts += $item['features'][0];
            $totalUas += $item['features'][1];
            $totalSikap += $item['features'][2];
            $totalPramuka += $item['features'][3];
            $totalPmr += $item['features'][4];
            $totalKehadiran += $item['features'][5];
        }
        
        $avgSikap = $totalSikap / $count;
        $avgPramuka = $totalPramuka / $count;
        $avgPmr = $totalPmr / $count;
        
        return [
            'count' => $count,
            'avg_uts' => $totalUts / $count,
            'avg_uas' => $totalUas / $count,
            'avg_sikap' => $avgSikap,
            'sikap_huruf' => $this->getNilaiSikapHuruf($avgSikap),
            'avg_pramuka' => $avgPramuka,
            'pramuka_huruf' => $this->getNilaiHuruf($avgPramuka, 'pramuka'),
            'avg_pmr' => $avgPmr,
            'pmr_huruf' => $this->getNilaiHuruf($avgPmr, 'pmr'),
            'avg_kehadiran' => $totalKehadiran / $count,
        ];
    }
    
    /**
     * Reset hasil clustering
     */
    public function resetClustering()
    {
        Session::forget('kmeans_result');
        return redirect()->route('clustering.setup');
    }
    
    // Metode untuk mendapatkan jumlah siswa di setiap cluster
    public function getClusterCounts()
    {
        if (!$this->isDataAvailable) {
            return [];
        }
        
        $counts = [];
        foreach ($this->clusters as $cluster) {
            $counts[] = count($cluster);
        }
        
        return $counts;
    }
    
    // Metode untuk mendapatkan total siswa
    public function getTotalStudents()
    {
        if (!$this->isDataAvailable) {
            return 0;
        }
        
        $total = 0;
        foreach ($this->clusters as $cluster) {
            $total += count($cluster);
        }
        
        return $total;
    }
    
    // Metode untuk mendapatkan warna untuk pie chart
    public function getClusterPieColors()
    {
        return [
            '#4285F4', '#EA4335', '#FBBC05', '#34A853', 
            '#FF6D01', '#46BDC6', '#9C27B0', '#795548'
        ];
    }
    
    // Metode untuk mendapatkan warna untuk Highcharts
    public function getClusterHighchartsColors()
    {
        return [
            '#4285F4', '#EA4335', '#FBBC05', '#34A853', 
            '#FF6D01', '#46BDC6', '#9C27B0', '#795548'
        ];
    }
    
    // Metode untuk mendapatkan label nilai berdasarkan threshold
    public function getValueLabel($value, $threshold)
    {
        if ($value >= $threshold) {
            return __('Baik');
        } else if ($value >= $threshold - 20) {
            return __('Cukup');
        } else {
            return __('Kurang');
        }
    }
    
    // Metode untuk mendapatkan warna nilai berdasarkan threshold
    public function getValueColor($value, $threshold)
    {
        if ($value >= $threshold) {
            return 'text-green-600';
        } else if ($value >= $threshold - 20) {
            return 'text-yellow-600';
        } else {
            return 'text-red-600';
        }
    }
    
    // Metode untuk mendapatkan warna sikap
    public function getSikapColor($grade)
    {
        switch ($grade) {
            case 'SB':
                return 'text-green-600';
            case 'B':
                return 'text-blue-600';
            case 'C':
                return 'text-yellow-600';
            case 'K':
                return 'text-red-600';
            case 'A':
                return 'text-green-600';
            case 'D':
                return 'text-red-600';
            default:
                return 'text-gray-600';
        }
    }
    
    // Metode untuk mendapatkan data cluster untuk radar chart
    public function getClusterDataForRadarChart()
    {
        if (!$this->isDataAvailable) {
            return [];
        }
        
        $result = [];
        
        foreach ($this->clusters as $index => $cluster) {
            $stats = $this->getClusterStats($index);
            
            // Normalisasi nilai sikap dari skala 1-4 ke skala 0-100
            $sikapNormalized = ($stats['avg_sikap'] / 4) * 100;
            
            $result[] = [
                $stats['avg_uts'],             // UTS
                $stats['avg_uas'],             // UAS
                $sikapNormalized,              // Sikap (dinormalisasi)
                $stats['avg_pramuka'],         // Pramuka
                $stats['avg_pmr'],             // PMR
                $stats['avg_kehadiran']        // Kehadiran
            ];
        }
        
        return $result;
    }
    
    // Metode untuk mendapatkan karakteristik cluster
    public function getClusterCharacteristics($clusterIndex)
    {
        if (!$this->isDataAvailable) {
            return '';
        }
        
        $stats = $this->getClusterStats($clusterIndex);
        
        // Tentukan karakteristik berdasarkan nilai rata-rata
        $characteristics = [];
        
        // Akademik (UTS dan UAS)
        $akademikRata = ($stats['avg_uts'] + $stats['avg_uas']) / 2;
        if ($akademikRata >= 80) {
            $characteristics[] = __('Akademik Tinggi');
        } else if ($akademikRata >= 70) {
            $characteristics[] = __('Akademik Menengah');
        } else {
            $characteristics[] = __('Akademik Rendah');
        }
        
        // Sikap
        if ($stats['avg_sikap'] >= 3.1) {
            $characteristics[] = __('Sikap Sangat Baik');
        } else if ($stats['avg_sikap'] >= 2.1) {
            $characteristics[] = __('Sikap Baik');
        } else {
            $characteristics[] = __('Sikap Perlu Perhatian');
        }
        
        // Ekstrakurikuler (Pramuka dan PMR)
        $ekstraRata = ($stats['avg_pramuka'] + $stats['avg_pmr']) / 2;
        if ($ekstraRata >= 80) {
            $characteristics[] = __('Ekstrakurikuler Aktif');
        } else if ($ekstraRata >= 70) {
            $characteristics[] = __('Ekstrakurikuler Cukup');
        } else {
            $characteristics[] = __('Ekstrakurikuler Pasif');
        }
        
        // Kehadiran
        if ($stats['avg_kehadiran'] >= 90) {
            $characteristics[] = __('Kehadiran Tinggi');
        } else if ($stats['avg_kehadiran'] >= 80) {
            $characteristics[] = __('Kehadiran Cukup');
        } else {
            $characteristics[] = __('Kehadiran Rendah');
        }
        
        return implode(', ', $characteristics);
    }
    
    /**
     * Mendapatkan nilai huruf berdasarkan nilai numerik dan tipe
     */
    public function getNilaiHuruf($nilai, $tipe)
    {
        if ($tipe == 'sikap') {
            // Konversi nilai numerik sikap menjadi huruf
            if ($nilai >= 85) return 'SB';
            if ($nilai >= 75) return 'B';
            if ($nilai >= 65) return 'C';
            return 'K';
        } else {
            // Konversi nilai numerik pramuka dan pmr menjadi huruf
            if ($nilai >= 85) return 'A';
            if ($nilai >= 75) return 'B';
            if ($nilai >= 65) return 'C';
            return 'D';
        }
    }
    
    public function render()
    {
        return view('livewire.clustering.clustering-result');
    }
}
