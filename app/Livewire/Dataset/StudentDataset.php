<?php

namespace App\Livewire\Dataset;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;

class StudentDataset extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $file = null;

    protected $listeners = ['fileReset' => 'resetFile'];

    public function resetFile()
    {
        $this->reset('file');
    }

    public function cleanupOldUploads()
    {
        if (Storage::exists('temp')) {
            foreach (Storage::files('temp') as $file) {
                if (Storage::lastModified($file) < now()->subHours(24)->getTimestamp()) {
                    Storage::delete($file);
                }
            }
        }
    }

    public function exportToExcel()
    {
        $students = Student::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="siswa.csv"',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Nama',
                'Kelas',
                'UTS',
                'UAS',
                'Penilaian Sikap',
                'Pramuka',
                'PMR',
                'Kehadiran',
                'Dibuat Pada',
                'Diperbarui Pada'
            ]);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->id,
                    $student->nama,
                    $student->kelas,
                    $student->uts,
                    $student->uas,
                    $student->penilaian_sikap,
                    $student->pramuka,
                    $student->pmr,
                    $student->kehadiran,
                    $student->created_at,
                    $student->updated_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $templatePath = public_path('templates/dataset_template.xlsx');

        if (file_exists($templatePath)) {
            return response()->download($templatePath, 'template-siswa.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } else {

            if (class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
                return $this->downloadExcelTemplate();
            } else {
                return $this->downloadCSVTemplate();
            }
        }
    }

    private function downloadExcelTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'nama',
            'kelas',
            'uts',
            'uas',
            'penilaian_sikap',
            'pramuka',
            'pmr',
            'kehadiran'
        ];

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E2EFDA');

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        for ($i = 0; $i < count($headers); $i++) {
            $sheet->setCellValue(chr(65 + $i) . '1', $headers[$i]);
        }

        $kelas = ['7A', '7B', '7C', '8A', '8B', '8C', '9A', '9B', '9C'];

        $firstNames = ['Budi', 'Andi', 'Siti', 'Dewi', 'Ahmad', 'Putri', 'Dimas', 'Rina', 'Joko', 'Maya'];
        $lastNames = ['Santoso', 'Wijaya', 'Nurhayati', 'Kusuma', 'Hidayat', 'Sari', 'Pratama', 'Purnama', 'Susanto', 'Indah'];

        for ($i = 0; $i < 50; $i++) {
            $row = $i + 2; // Start from row 2

            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $nama = $firstName . ' ' . $lastName;

            $kelasIndex = array_rand($kelas);
            $uts = rand(60, 100);
            $uas = rand(60, 100);

            $sikapWeights = [
                'A' => 30,
                'B' => 40,
                'C' => 20,
                'D' => 8,
                'E' => 2
            ];
            $sikapValues = [];
            foreach ($sikapWeights as $value => $weight) {
                for ($j = 0; $j < $weight; $j++) {
                    $sikapValues[] = $value;
                }
            }
            $penilaian_sikap = $sikapValues[array_rand($sikapValues)];

            $pramuka = rand(70, 100);
            $pmr = rand(70, 100);
            $kehadiran = rand(80, 100);

            $sheet->setCellValue('A' . $row, $nama);
            $sheet->setCellValue('B' . $row, $kelas[$kelasIndex]);
            $sheet->setCellValue('C' . $row, $uts);
            $sheet->setCellValue('D' . $row, $uas);
            $sheet->setCellValue('E' . $row, $penilaian_sikap);
            $sheet->setCellValue('F' . $row, $pramuka);
            $sheet->setCellValue('G' . $row, $pmr);
            $sheet->setCellValue('H' . $row, $kehadiran);
        }

        $validation = $sheet->getCell('E2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"A,B,C,D,E"');
        $validation->setPromptTitle('Penilaian Sikap');
        $validation->setPrompt('Pilih nilai A, B, C, D, atau E');
        $validation->setErrorTitle('Nilai Tidak Valid');
        $validation->setError('Pilih nilai dari daftar yang tersedia.');

        $sheet->setDataValidation('E2:E1000', $validation);

        $filePath = storage_path('app/public/template-siswa.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath, 'template-siswa.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ])->deleteFileAfterSend(true);
    }

    private function downloadCSVTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-siswa.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Pastikan encoding UTF-8 dengan BOM untuk Excel
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'nama',
                'kelas',
                'uts',
                'uas',
                'penilaian_sikap',
                'pramuka',
                'pmr',
                'kehadiran'
            ]);

            $kelas = ['7A', '7B', '7C', '8A', '8B', '8C', '9A', '9B', '9C'];

            $firstNames = ['Budi', 'Andi', 'Siti', 'Dewi', 'Ahmad', 'Putri', 'Dimas', 'Rina', 'Joko', 'Maya'];
            $lastNames = ['Santoso', 'Wijaya', 'Nurhayati', 'Kusuma', 'Hidayat', 'Sari', 'Pratama', 'Purnama', 'Susanto', 'Indah'];

            for ($i = 0; $i < 50; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $nama = $firstName . ' ' . $lastName;

                $kelasIndex = array_rand($kelas);
                $uts = rand(60, 100);
                $uas = rand(60, 100);

                $sikapWeights = [
                    'A' => 30,
                    'B' => 40,
                    'C' => 20,
                    'D' => 8,
                    'E' => 2
                ];
                $sikapValues = [];
                foreach ($sikapWeights as $value => $weight) {
                    for ($j = 0; $j < $weight; $j++) {
                        $sikapValues[] = $value;
                    }
                }
                $penilaian_sikap = $sikapValues[array_rand($sikapValues)];

                $pramuka = rand(70, 100);
                $pmr = rand(70, 100);
                $kehadiran = rand(80, 100);

                // Write data row
                fputcsv($file, [
                    $nama,
                    $kelas[$kelasIndex],
                    $uts,
                    $uas,
                    $penilaian_sikap,
                    $pramuka,
                    $pmr,
                    $kehadiran
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importExcel()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:5120', // CSV dan Excel, maksimum 5MB
        ]);

        try {
            $filename = $this->file->getClientOriginalName();
            $path = $this->file->storeAs('public/imports', $filename);

            $storagePath = storage_path('app' . DIRECTORY_SEPARATOR . $path);

            if (!file_exists($storagePath)) {
                $storagePath = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app/' . $path));

                if (!file_exists($storagePath)) {
                    $storagePath = Storage::path($path);

                    if (!file_exists($storagePath)) {
                        throw new \Exception('File tidak ditemukan setelah upload. Coba lagi.');
                    }
                }
            }

            $extension = pathinfo($storagePath, PATHINFO_EXTENSION);

            if ($extension == 'csv') {
                $imported = $this->importCSV($storagePath);
            } else {
                $imported = $this->importExcelFile($storagePath);
            }

            Storage::delete($path);

            session()->flash('message', "Data siswa berhasil diimport ($imported data).");
            $this->reset('file');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function importCSV($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File tidak ditemukan: ' . $filePath);
        }

        $file = null;
        $modes = ['r', 'rb', 'rt'];

        foreach ($modes as $mode) {
            $file = @fopen($filePath, $mode);
            if ($file !== false) {
                break;
            }
        }

        if (!$file) {
            throw new \Exception('Gagal membuka file. Pastikan file tidak rusak dan memiliki format CSV yang benar.');
        }

        $headers = fgetcsv($file);

        if (!$headers) {
            fclose($file);
            throw new \Exception('Format file tidak valid atau file kosong.');
        }

        if (count($headers) < 8) {
            fclose($file);
            throw new \Exception('Format file tidak valid. Jumlah kolom kurang dari yang diharapkan.');
        }

        $imported = 0;
        $errors = [];
        $rowCount = 1;

        while (($row = fgetcsv($file)) !== false) {
            $rowCount++;

            if (empty(array_filter($row))) {
                continue;
            }

            if (count($row) < 8) {
                $errors[] = "Baris $rowCount: Jumlah kolom kurang dari yang diharapkan.";
                continue;
            }

            try {
                Student::create([
                    'nama' => trim($row[0]) ?: 'Tanpa Nama',
                    'kelas' => trim($row[1]) ?: 'Unknown',
                    'uts' => is_numeric($row[2]) ? floatval($row[2]) : 0,
                    'uas' => is_numeric($row[3]) ? floatval($row[3]) : 0,
                    'penilaian_sikap' => trim($row[4]) ?: 'C',
                    'pramuka' => $this->convertToLetterGrade(trim($row[5])),
                    'pmr' => $this->convertToLetterGrade(trim($row[6])),
                    'kehadiran' => is_numeric($row[7]) ? floatval($row[7]) : 0
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris $rowCount: " . $e->getMessage();
            }
        }

        fclose($file);

        if ($imported == 0) {
            if (!empty($errors)) {
                throw new \Exception('Gagal mengimpor data. Masalah: ' . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? ' dan ' . (count($errors) - 3) . ' kesalahan lainnya.' : ''));
            }
            throw new \Exception('Tidak ada data yang diimpor. Harap periksa format file CSV Anda.');
        }

        if (!empty($errors)) {
            session()->flash('warning', 'Berhasil mengimpor ' . $imported . ' data, tetapi terdapat ' . count($errors) . ' kesalahan.');
        }

        return $imported;
    }

    private function importExcelFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File tidak ditemukan: ' . $filePath);
        }

        try {
            // Cek class yang dibutuhkan tersedia
            if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
                throw new \Exception('Pustaka PhpSpreadsheet tidak tersedia. Silakan gunakan format CSV sebagai alternatif.');
            }

            // Coba membaca file Excel
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = $worksheet->toArray();

            $headers = array_shift($rows);

            $expectedHeaders = ['nama', 'kelas', 'uts', 'uas', 'penilaian_sikap', 'pramuka', 'pmr', 'kehadiran(%)'];
            $headerCount = count(array_intersect($headers, $expectedHeaders));

            if ($headerCount < count($expectedHeaders)) {
                throw new \Exception('Format header tidak sesuai. Gunakan template yang disediakan.');
            }

            $imported = 0;
            $errors = [];
            $rowCount = 1;

            foreach ($rows as $row) {
                $rowCount++;

                if (empty(array_filter($row))) {
                    continue;
                }

                if (count($row) < 8) {
                    $errors[] = "Baris $rowCount: Jumlah kolom kurang dari yang diharapkan.";
                    continue;
                }

                try {
                    Student::create([
                        'nama' => trim($row[0]) ?: 'Tanpa Nama',
                        'kelas' => trim($row[1]) ?: 'Unknown',
                        'uts' => is_numeric($row[2]) ? floatval($row[2]) : 0,
                        'uas' => is_numeric($row[3]) ? floatval($row[3]) : 0,
                        'penilaian_sikap' => trim($row[4]) ?: 'C',
                        'pramuka' => $this->convertToLetterGrade(trim($row[5])),
                        'pmr' => $this->convertToLetterGrade(trim($row[6])),
                        'kehadiran' => is_numeric($row[7]) ? floatval($row[7]) : 0
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris $rowCount: " . $e->getMessage();
                }
            }

            if ($imported == 0) {
                if (!empty($errors)) {
                    throw new \Exception('Gagal mengimpor data. Masalah: ' . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? ' dan ' . (count($errors) - 3) . ' kesalahan lainnya.' : ''));
                }
                throw new \Exception('Tidak ada data yang diimpor. Harap periksa format file Excel Anda.');
            }

            if (!empty($errors)) {
                session()->flash('warning', 'Berhasil mengimpor ' . $imported . ' data, tetapi terdapat ' . count($errors) . ' kesalahan.');
            }

            return $imported;
        } catch (\Exception $e) {
            throw new \Exception('Error membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Konversi nilai numerik menjadi nilai huruf
     * A = 85-100, B = 75-84, C = 65-74, D = 0-64
     */
    private function convertToLetterGrade($value)
    {
        if (in_array(strtoupper(trim($value)), ['A', 'B', 'C', 'D'])) {
            return strtoupper(trim($value));
        }

        $numericValue = is_numeric($value) ? floatval($value) : 0;

        if ($numericValue >= 85) return 'A';
        if ($numericValue >= 75) return 'B';
        if ($numericValue >= 65) return 'C';
        return 'D';
    }

    public function truncateStudents()
    {
        try {
            // Hapus semua data siswa
            Student::truncate();

            if (session()->has('clustering_results')) {
                session()->forget('clustering_results');
            }

            if (session()->has('elbow_results')) {
                session()->forget('elbow_results');
            }

            session()->flash('message', 'Semua data siswa berhasil dihapus.');

            $this->dispatch('refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->cleanupOldUploads();

        $students = Student::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('kelas', 'like', '%' . $this->search . '%');
            })
            ->orderBy('kelas')
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.dataset.student-dataset', [
            'students' => $students
        ]);
    }
}
