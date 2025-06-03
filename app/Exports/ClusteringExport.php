<?php

namespace App\Exports;

use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ClusteringExport
{
    protected $clusteringData;
    protected $centroids;
    protected $clusterStats;
    protected $totalStudents;
    protected $sse;
    protected $iterations;

    public function __construct($clusteringData, $centroids, $clusterStats = [], $metadata = [])
    {
        $this->clusteringData = $clusteringData;
        $this->centroids = $centroids;
        $this->clusterStats = $clusterStats;
        $this->totalStudents = $metadata['totalStudents'] ?? count($clusteringData);
        $this->sse = $metadata['sse'] ?? 0;
        $this->iterations = $metadata['iterations'] ?? 0;
    }

    public function download($filename)
    {
        return new StreamedResponse(function () {
            $options = new Options();

            $writer = new Writer($options);
            $writer->openToFile('php://output');

            $headerStyle = (new Style())
                ->setFontBold()
                ->setFontSize(12)
                ->setBackgroundColor('4285F4')
                ->setFontColor('FFFFFF');

            $titleStyle = (new Style())
                ->setFontBold()
                ->setFontSize(14)
                ->setBackgroundColor('E8EAED');

            // Create cluster styles
            $clusterColors = [
                0 => 'E06666', // Merah
                1 => '93C47D', // Hijau
                2 => 'FFD966', // Kuning
                3 => '76A5AF', // Biru
            ];

            // --- TITLE ROW ---
            $titleRow = Row::fromValues(['HASIL CLUSTERING K-MEANS - ' . date('d-m-Y')], $titleStyle);
            $writer->addRow($titleRow);

            // Empty row
            $writer->addRow(Row::fromValues(['']));

            // Headers
            $headers = [
                'Nama Siswa',
                'Kelas',
                'UTS',
                'UAS',
                'Sikap',
                'Pramuka',
                'PMR',
                'Kehadiran (%)',
                'Cluster',
                'Karakteristik Cluster'
            ];

            $writer->addRow(Row::fromValues($headers, $headerStyle));

            // --- STUDENT DATA ---
            // Group by cluster
            $groupedData = [];
            foreach ($this->clusteringData as $rowData) {
                $clusterName = $rowData['cluster'];
                if (!isset($groupedData[$clusterName])) {
                    $groupedData[$clusterName] = [];
                }
                $groupedData[$clusterName][] = $rowData;
            }

            // Add rows by cluster
            foreach ($groupedData as $clusterName => $clusterRows) {
                $clusterIndex = (int)substr($clusterName, -1) - 1;
                $color = isset($clusterColors[$clusterIndex]) ? $clusterColors[$clusterIndex] : 'CCCCCC';

                // Cluster header row
                $clusterHeaderStyle = (new Style())
                    ->setFontBold()
                    ->setBackgroundColor($color)
                    ->setFontColor('FFFFFF');

                $writer->addRow(Row::fromValues([
                    $clusterName,
                    count($clusterRows) . ' siswa',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $clusterRows[0]['karakteristik'] ?? ''
                ], $clusterHeaderStyle));

                // Add data rows with alternating background
                foreach ($clusterRows as $index => $rowData) {
                    $rowStyle = (new Style())
                        ->setBackgroundColor($color . ($index % 2 == 0 ? '20' : '10'));

                    $dataRow = Row::fromValues([
                        $rowData['nama'],
                        $rowData['kelas'],
                        $rowData['uts'],
                        $rowData['uas'],
                        $rowData['sikap'],
                        $rowData['pramuka'],
                        $rowData['pmr'],
                        $rowData['kehadiran'],
                        $rowData['cluster'],
                        $rowData['karakteristik']
                    ], $rowStyle);

                    $writer->addRow($dataRow);
                }

                // Add empty row after each cluster
                $writer->addRow(Row::fromValues(['']));
            }

            // --- EMPTY ROW ---
            $writer->addRow(Row::fromValues(['']));

            // --- CLUSTER INFO ---
            $writer->addRow(Row::fromValues(['INFORMASI CLUSTER'], $headerStyle));

            $writer->addRow(Row::fromValues([
                'Total Siswa:',
                $this->totalStudents,
                'Jumlah Cluster:',
                count($this->centroids),
                'SSE:',
                number_format($this->sse, 2),
                'Iterasi:',
                $this->iterations
            ]));

            // --- EMPTY ROW ---
            $writer->addRow(Row::fromValues(['']));

            // --- CENTROID SECTION ---
            $writer->addRow(Row::fromValues(['CENTROID CLUSTER'], $headerStyle));

            // --- CENTROID HEADERS ---
            $centroidHeaders = ['Cluster', 'UTS', 'UAS', 'Sikap', 'Pramuka', 'PMR', 'Kehadiran (%)'];
            $writer->addRow(Row::fromValues($centroidHeaders, $headerStyle));

            // --- CENTROID DATA ---
            foreach ($this->centroids as $index => $centroid) {
                $centroidStyle = (new Style())
                    ->setBackgroundColor($clusterColors[$index] . '50');

                $centroidRow = array_merge(
                    ['Cluster ' . ($index + 1)],
                    array_map(function ($value) {
                        return number_format($value, 2);
                    }, $centroid)
                );

                $writer->addRow(Row::fromValues($centroidRow, $centroidStyle));
            }

            $writer->close();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function downloadPDF($filename)
    {
        try {
            $clustersByNumber = [];
            $safeClusterStats = [];

            foreach ($this->clusterStats as $index => $stats) {
                $safeClusterStats[$index] = $stats;
                if (isset($stats['karakteristik'])) {
                    $safeClusterStats[$index]['karakteristik'] = $this->toASCII($stats['karakteristik']);
                }
            }

            foreach ($this->clusteringData as $row) {
                $clusterName = $row['cluster'];
                if (!isset($clustersByNumber[$clusterName])) {
                    $clustersByNumber[$clusterName] = [];
                }

                $safeRow = [];
                foreach ($row as $key => $value) {
                    if (is_string($value)) {
                        $safeRow[$key] = $this->toASCII($value);
                    } else {
                        $safeRow[$key] = $value;
                    }
                }

                $clustersByNumber[$clusterName][] = $safeRow;
            }

            $clusterColors = [
                'Cluster 1' => '#E06666',
                'Cluster 2' => '#93C47D',
                'Cluster 3' => '#FFD966',
                'Cluster 4' => '#76A5AF'
            ];

            $data = [
                'clusteringData' => $this->clusteringData,
                'centroids' => $this->centroids,
                'clusterStats' => $safeClusterStats,
                'totalStudents' => $this->totalStudents,
                'sse' => $this->sse,
                'iterations' => $this->iterations,
                'clustersByNumber' => $clustersByNumber,
                'clusterColors' => $clusterColors,
                'exportDate' => date('d-m-Y H:i:s')
            ];

            $pdf = Pdf::loadView('exports.clustering-pdf', $data);
            $pdf->setPaper('a4', 'landscape');

            $domPdf = $pdf->getDomPDF();
            $options = $domPdf->getOptions();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', false);
            $options->set('defaultMediaType', 'screen');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultFont', 'sans-serif');

            $domPdf->setOptions($options);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename);
        } catch (\Exception $e) {
            report($e);
            throw $e;
        }
    }


    private function toASCII($text)
    {
        if (!is_string($text)) return $text;

        $ascii = preg_replace('/[^\x00-\x7F]/', '', $text);

        $ascii = preg_replace('/[\x00-\x1F\x7F]/', '', $ascii);

        $ascii = preg_replace('/[^a-zA-Z0-9 \.,;:_\-\(\)]/', '', $ascii);

        return trim($ascii);
    }
}
