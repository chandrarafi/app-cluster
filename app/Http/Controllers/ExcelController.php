<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Exports\TemplateExport;
use App\Imports\StudentsImport;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importForm()
    {
        return view('import-form');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // maksimum 5MB
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            
            return redirect()->back()->with('message', 'Data siswa berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'siswa.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateExport, 'template-siswa.xlsx');
    }
} 