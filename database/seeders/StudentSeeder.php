<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'nama' => 'Budi Santoso',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'nilai_matematika' => 85.5,
                'nilai_ipa' => 78.0,
                'nilai_bahasa_indonesia' => 90.0,
                'nilai_bahasa_inggris' => 82.5,
                'nilai_ips' => 88.0,
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Pahlawan No. 45, Bandung',
                'nilai_matematika' => 92.0,
                'nilai_ipa' => 88.5,
                'nilai_bahasa_indonesia' => 85.0,
                'nilai_bahasa_inggris' => 90.0,
                'nilai_ips' => 78.5,
            ],
            [
                'nama' => 'Ahmad Rizki',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Sudirman No. 78, Surabaya',
                'nilai_matematika' => 75.0,
                'nilai_ipa' => 82.0,
                'nilai_bahasa_indonesia' => 78.5,
                'nilai_bahasa_inggris' => 76.0,
                'nilai_ips' => 80.0,
            ],
            [
                'nama' => 'Dewi Lestari',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Gatot Subroto No. 12, Semarang',
                'nilai_matematika' => 88.0,
                'nilai_ipa' => 90.5,
                'nilai_bahasa_indonesia' => 92.0,
                'nilai_bahasa_inggris' => 85.5,
                'nilai_ips' => 79.0,
            ],
            [
                'nama' => 'Rudi Hermawan',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Ahmad Yani No. 56, Yogyakarta',
                'nilai_matematika' => 95.0,
                'nilai_ipa' => 92.5,
                'nilai_bahasa_indonesia' => 85.0,
                'nilai_bahasa_inggris' => 88.0,
                'nilai_ips' => 90.5,
            ],
            [
                'nama' => 'Rina Wijaya',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Diponegoro No. 34, Malang',
                'nilai_matematika' => 78.5,
                'nilai_ipa' => 80.0,
                'nilai_bahasa_indonesia' => 88.5,
                'nilai_bahasa_inggris' => 92.0,
                'nilai_ips' => 85.0,
            ],
            [
                'nama' => 'Doni Kusuma',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Imam Bonjol No. 67, Medan',
                'nilai_matematika' => 82.0,
                'nilai_ipa' => 85.5,
                'nilai_bahasa_indonesia' => 79.0,
                'nilai_bahasa_inggris' => 80.5,
                'nilai_ips' => 83.0,
            ],
            [
                'nama' => 'Maya Sari',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Veteran No. 23, Makassar',
                'nilai_matematika' => 90.5,
                'nilai_ipa' => 87.0,
                'nilai_bahasa_indonesia' => 92.5,
                'nilai_bahasa_inggris' => 89.0,
                'nilai_ips' => 85.5,
            ],
            [
                'nama' => 'Andi Pratama',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Thamrin No. 89, Palembang',
                'nilai_matematika' => 76.0,
                'nilai_ipa' => 78.5,
                'nilai_bahasa_indonesia' => 80.0,
                'nilai_bahasa_inggris' => 75.5,
                'nilai_ips' => 77.0,
            ],
            [
                'nama' => 'Lina Anggraini',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Pemuda No. 56, Denpasar',
                'nilai_matematika' => 85.0,
                'nilai_ipa' => 88.0,
                'nilai_bahasa_indonesia' => 90.5,
                'nilai_bahasa_inggris' => 87.5,
                'nilai_ips' => 82.0,
            ],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}
