<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentDatasetController extends Controller
{
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_siswa.csv"',
        ];
        
        $csvContent = "nama_siswa,kelas,nilai_uts,nilai_uas,penilaian_sikap,pramuka,pmr,kehadiran_siswa\n";
        $csvContent .= "Putri Santoso,8C,87,86,A,A,A,92%\n";
        $csvContent .= "Dewi Nurhayati,8C,83,83,B,B,B,93%\n";
        $csvContent .= "Rina Pratama,9A,77,80,B,B,A,100%";
        
        return response($csvContent, 200, $headers);
    }
    

    public function importCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        
        if (isset($data[0]) && is_array($data[0]) && 
            (in_array('nama_siswa', $data[0]) || in_array('Nama Siswa', $data[0]))) {
            array_shift($data);
        }
        
        $processed = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($data as $row) {
            try {
                if (count($row) < 8) {
                    $failed++;
                    $errors[] = "Baris tidak lengkap: " . implode(', ', $row);
                    continue;
                }
                
                $nama = trim($row[0]);
                $kelas = trim($row[1]);
                $uts = $this->parseNumber(trim($row[2]));
                $uas = $this->parseNumber(trim($row[3]));
                $penilaian_sikap = strtoupper(trim($row[4]));
                $pramuka = strtoupper(trim($row[5]));
                $pmr = strtoupper(trim($row[6]));
                $kehadiran = $this->parseNumber(trim($row[7]));
                
                if (!in_array($penilaian_sikap, ['SB', 'B', 'C', 'K'])) {
                    throw new \Exception("Nilai sikap tidak valid ($penilaian_sikap). Harus SB, B, C, atau K.");
                }
                
                if (!in_array($pramuka, ['A', 'B', 'C', 'D'])) {
                    throw new \Exception("Nilai pramuka tidak valid ($pramuka). Harus A, B, C, atau D.");
                }
                
                if (!in_array($pmr, ['A', 'B', 'C', 'D'])) {
                    throw new \Exception("Nilai PMR tidak valid ($pmr). Harus A, B, C, atau D.");
                }
                
                Student::create([
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'uts' => $uts,
                    'uas' => $uas,
                    'penilaian_sikap' => $penilaian_sikap,
                    'pramuka' => $pramuka,
                    'pmr' => $pmr,
                    'kehadiran' => $kehadiran,
                ]);
                
                $processed++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Error pada data: " . implode(', ', $row) . " - " . $e->getMessage();
            }
        }
        
        $message = "$processed data siswa berhasil diimpor. ";
        if ($failed > 0) {
            $message .= "$failed data gagal diimpor. ";
        }
        
        return redirect()->route('dataset.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }


    private function parseNumber($value)
    {
        if (strpos($value, '%') !== false) {
            $value = str_replace('%', '', $value);
        }
        
      
        $value = preg_replace('/[^0-9.]/', '', $value);
        
        if (strpos($value, '.') !== false) {
            return (float) $value;
        }
        
        return (float) $value;
    }
} 