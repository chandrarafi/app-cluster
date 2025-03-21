<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Student([
            'nama' => $row['nama'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'alamat' => $row['alamat'],
            'nilai_matematika' => $row['nilai_matematika'],
            'nilai_ipa' => $row['nilai_ipa'],
            'nilai_bahasa_indonesia' => $row['nilai_bahasa_indonesia'],
            'nilai_bahasa_inggris' => $row['nilai_bahasa_inggris'],
            'nilai_ips' => $row['nilai_ips'],
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama' => 'required',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required',
            'nilai_matematika' => 'required|numeric',
            'nilai_ipa' => 'required|numeric',
            'nilai_bahasa_indonesia' => 'required|numeric',
            'nilai_bahasa_inggris' => 'required|numeric',
            'nilai_ips' => 'required|numeric',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom nama harus diisi',
            'jenis_kelamin.required' => 'Kolom jenis kelamin harus diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'alamat.required' => 'Kolom alamat harus diisi',
            'nilai_matematika.required' => 'Kolom nilai matematika harus diisi',
            'nilai_matematika.numeric' => 'Nilai matematika harus berupa angka',
            'nilai_ipa.required' => 'Kolom nilai IPA harus diisi',
            'nilai_ipa.numeric' => 'Nilai IPA harus berupa angka',
            'nilai_bahasa_indonesia.required' => 'Kolom nilai Bahasa Indonesia harus diisi',
            'nilai_bahasa_indonesia.numeric' => 'Nilai Bahasa Indonesia harus berupa angka',
            'nilai_bahasa_inggris.required' => 'Kolom nilai Bahasa Inggris harus diisi',
            'nilai_bahasa_inggris.numeric' => 'Nilai Bahasa Inggris harus berupa angka',
            'nilai_ips.required' => 'Kolom nilai IPS harus diisi',
            'nilai_ips.numeric' => 'Nilai IPS harus berupa angka',
        ];
    }
} 