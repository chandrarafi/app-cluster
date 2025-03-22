<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Clustering;
use Illuminate\Http\Request;

class ClusteringController extends Controller
{
    private function processData($data)
    {
      
        $processedData = [];
        
        foreach ($data as $index => $item) {
            $processedData[] = [
                'id' => $item->id,
                'nama' => $item->nama,
                'kelas' => $item->kelas,
                'uts' => $item->uts,
                'uas' => $item->uas,
                'penilaian_sikap' => $item->penilaian_sikap,
                'pramuka' => $item->pramuka,
                'pmr' => $item->pmr,
                'kehadiran' => $item->kehadiran . '%',
                'cluster' => $item->cluster ?? 0
            ];
        }
        
        return $processedData;
    }
} 