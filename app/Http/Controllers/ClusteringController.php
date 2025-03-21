<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Clustering;
use Illuminate\Http\Request;

class ClusteringController extends Controller
{
    private function processData($data)
    {
        // Proses data untuk dikirim ke front-end
        $processedData = [];
        
        foreach ($data as $index => $item) {
            $processedData[] = [
                'id' => $item->id,
                'nama' => $item->nama,
                'kelas' => $item->kelas,
                'uts' => $item->uts,
                'uas' => $item->uas,
                'penilaian_sikap' => $item->penilaian_sikap, // Tetap sebagai huruf
                'pramuka' => $item->pramuka, // Tetap sebagai huruf
                'pmr' => $item->pmr, // Tetap sebagai huruf
                'kehadiran' => $item->kehadiran . '%', // Format sebagai persentase
                'cluster' => $item->cluster ?? 0
            ];
        }
        
        return $processedData;
    }
} 