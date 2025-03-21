<?php

namespace App\Livewire\Clustering;

use App\Models\Student;
use App\Services\KMeans\ElbowMethodService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ElbowMethod extends Component
{
    public $minClusters = 2;
    public $maxClusters = 8;
    public $maxIterations = 100;
    public $elbowResults = [];
    public $optimalK = null;
    public $isProcessing = false;
    
    // Validasi input
    public function rules()
    {
        return [
            'minClusters' => 'required|integer|min:2|max:10',
            'maxClusters' => 'required|integer|min:2|max:10|gte:minClusters',
            'maxIterations' => 'required|integer|min:10|max:1000',
        ];
    }
    
    public function runElbowMethod()
    {
        Log::info('Memulai proses Elbow Method');
        
        // Set status pemrosesan
        $this->isProcessing = true;
        
        // Validasi input
        $this->validate();
        
        // Ambil data siswa dari database
        $students = Student::all();
        
        if ($students->isEmpty()) {
            Log::error('Tidak ada data siswa untuk di-cluster');
            session()->flash('error', 'Tidak ada data siswa untuk proses clustering.');
            return;
        }
        
        Log::info('Jumlah data siswa: ' . $students->count());
        
        try {
            // Persiapkan data untuk clustering (semua atribut)
            $data = [];
            foreach ($students as $student) {
                // Konversi penilaian sikap, pramuka, dan pmr ke nilai numerik
                $sikapValue = $this->convertSikapToNumeric($student->penilaian_sikap);
                $pramukaValue = $this->convertScoreToNumeric($student->pramuka);
                $pmrValue = $this->convertScoreToNumeric($student->pmr);
                
                // Gunakan semua atribut untuk clustering
                $data[] = [
                    'id' => $student->id,
                    'uts' => (float) $student->uts,
                    'uas' => (float) $student->uas,
                    'sikap' => $sikapValue,
                    'pramuka' => $pramukaValue,
                    'pmr' => $pmrValue,
                    'kehadiran' => (float) $student->kehadiran,
                ];
            }
            
            // Cek apakah data tersedia
            if (empty($data)) {
                Log::error('Data untuk clustering kosong');
                session()->flash('error', 'Data untuk clustering tidak tersedia.');
                return;
            }
            
            // Log data pertama untuk debugging
            Log::info('Contoh data untuk clustering:', $data[0] ?? []);
            
            // Jalankan Elbow Method
            $elbowMethod = new ElbowMethodService();
            $results = $elbowMethod->calculateElbowMethod(
                $data, 
                range($this->minClusters, $this->maxClusters), 
                $this->maxIterations
            );
            
            Log::info('Hasil Elbow Method berhasil diproses');
            
            // Simpan hasil ke properties
            $this->elbowResults = $results;
            
            // Tentukan nilai K optimal dari hasil elbow method
            $this->optimalK = $elbowMethod->findOptimalK($results);
            Log::info('Nilai K optimal: ' . $this->optimalK);
            
            // Simpan ke session untuk digunakan di halaman lain
            session()->put('optimalK', $this->optimalK);
            
            // Dispatch event untuk update UI
            $this->dispatch('elbowResultsUpdated', $results);
            
        } catch (\Exception $e) {
            Log::error('Error saat menjalankan Elbow Method: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            // Set status pemrosesan selesai
            $this->isProcessing = false;
        }
    }
    
    // Konversi nilai sikap ke nilai numerik
    private function convertSikapToNumeric($sikap)
    {
        return match ($sikap) {
            'SB' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'K' => 1.0,
            default => 0.0,
        };
    }
    
    // Konversi nilai huruf ke nilai numerik
    private function convertScoreToNumeric($score)
    {
        return match ($score) {
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            default => 0.0,
        };
    }
    
    public function render()
    {
        return view('livewire.clustering.elbow-method');
    }
}
