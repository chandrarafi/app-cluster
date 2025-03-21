<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'kelas',
        'uts',
        'uas',
        'penilaian_sikap',
        'pramuka',
        'pmr',
        'kehadiran',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uts' => 'float',
        'uas' => 'float',
        'kehadiran' => 'float',
    ];
    
    /**
     * Mengkonversi nilai sikap yang berupa huruf menjadi angka untuk diproses oleh algoritma k-means
     * Ketentuan: SB = 85-100, B = 75-84, C = 65-74, K = 0-64
     */
    public function getNilaiSikapNumerikAttribute()
    {
        return match(strtoupper($this->penilaian_sikap)) {
            'SB' => 90, // nilai tengah range 85-100
            'B' => 80,  // nilai tengah range 75-84
            'C' => 70,  // nilai tengah range 65-74
            'K' => 55,  // nilai tengah range 0-64
            default => 0,
        };
    }
    
    /**
     * Mengkonversi nilai pramuka yang berupa huruf menjadi angka untuk diproses oleh algoritma k-means
     * Ketentuan: A = 85-100, B = 75-84, C = 65-74, D = 0-64
     */
    public function getNilaiPramukaNumerikAttribute()
    {
        return match(strtoupper($this->pramuka)) {
            'A' => 90, // nilai tengah range 85-100
            'B' => 80, // nilai tengah range 75-84
            'C' => 70, // nilai tengah range 65-74
            'D' => 55, // nilai tengah range 0-64
            default => 0,
        };
    }
    
    /**
     * Mengkonversi nilai PMR yang berupa huruf menjadi angka untuk diproses oleh algoritma k-means
     * Ketentuan: A = 85-100, B = 75-84, C = 65-74, D = 0-64
     */
    public function getNilaiPmrNumerikAttribute()
    {
        return match(strtoupper($this->pmr)) {
            'A' => 90, // nilai tengah range 85-100
            'B' => 80, // nilai tengah range 75-84
            'C' => 70, // nilai tengah range 65-74
            'D' => 55, // nilai tengah range 0-64
            default => 0,
        };
    }
} 