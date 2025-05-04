<?php

namespace App\Livewire\Clustering;

use App\Models\Student;
use App\Services\KMeans\ElbowMethodService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ElbowMethod extends Component
{
    public $minClusters = 2;
    public $maxClusters = 8;
    public $maxIterations = 100;
    public $elbowResults = [];
    public $optimalK = null;
    public $isProcessing = false;

    public function mount()
    {
        if (Session::has('elbow_results')) {
            $sessionData = Session::get('elbow_results');
            if (isset($sessionData['results']) && !empty($sessionData['results'])) {
                $this->elbowResults = $sessionData['results'];
                Log::info('Memuat hasil elbow dari session: ' . count($this->elbowResults) . ' item');
            }

            if (isset($sessionData['optimalK'])) {
                $this->optimalK = $sessionData['optimalK'];
                Log::info('Memuat K optimal dari session: ' . $this->optimalK);
            }
        }
    }

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

        $this->isProcessing = true;

        $this->validate();

        $this->elbowResults = [];
        $this->optimalK = null;

        $students = Student::all();

        if ($students->isEmpty()) {
            Log::error('Tidak ada data siswa untuk di-cluster');
            session()->flash('error', 'Tidak ada data siswa untuk proses clustering.');
            $this->isProcessing = false;
            return;
        }

        Log::info('Jumlah data siswa: ' . $students->count());

        try {
            $data = [];
            foreach ($students as $student) {
                $sikapValue = $this->convertSikapToNumeric($student->penilaian_sikap);
                $pramukaValue = $this->convertScoreToNumeric($student->pramuka);
                $pmrValue = $this->convertScoreToNumeric($student->pmr);

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

            if (empty($data)) {
                Log::error('Data untuk clustering kosong');
                session()->flash('error', 'Data untuk clustering tidak tersedia.');
                $this->isProcessing = false;
                return;
            }

            Log::info('Contoh data untuk clustering:', $data[0] ?? []);

            $elbowMethod = new ElbowMethodService();
            $results = $elbowMethod->calculateElbowMethod(
                $data,
                range($this->minClusters, $this->maxClusters),
                $this->maxIterations
            );

            Log::info('Hasil Elbow Method berhasil diproses');

            $this->elbowResults = $results;

            $this->optimalK = $elbowMethod->findOptimalK($results);
            Log::info('Nilai K optimal: ' . $this->optimalK);

            Session::put('elbow_results', [
                'results' => $results,
                'optimalK' => $this->optimalK
            ]);

            Session::put('optimalK', $this->optimalK);

            Log::info('Data elbow disimpan ke session');

            $this->dispatch('elbowResultsUpdated', ['results' => $results, 'optimalK' => $this->optimalK]);
            Log::info('Event elbowResultsUpdated dipancarkan dengan ' . count($results) . ' hasil');
        } catch (\Exception $e) {
            Log::error('Error saat menjalankan Elbow Method: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

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
