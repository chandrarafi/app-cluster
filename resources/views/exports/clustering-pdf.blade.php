<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ascii" />
    <title>Hasil Clustering K-Means</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        h2 {
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f8f9fa;
            border-left: 4px solid #4285F4;
        }

        .meta-info {
            margin-bottom: 20px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
        }

        .meta-info p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table th {
            background-color: #4285F4;
            color: white;
            padding: 6px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 5px 6px;
            border-bottom: 1px solid #eee;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .cluster-header {
            padding: 6px 8px;
            margin: 15px 0 5px 0;
            font-weight: bold;
            color: white;
        }

        .centroid-table th,
        .centroid-table td {
            text-align: center;
        }

        .stats-table th,
        .stats-table td {
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <h1>HASIL CLUSTERING K-MEANS</h1>

    <div class="meta-info">
        <p><strong>Tanggal Ekspor:</strong> {{ $exportDate }}</p>
        <p><strong>Jumlah Cluster:</strong> {{ count($centroids) }}</p>
        <p><strong>Total Siswa:</strong> {{ $totalStudents }}</p>
        <p><strong>SSE (Sum of Squared Errors):</strong> {{ number_format($sse, 2) }}</p>
        <p><strong>Jumlah Iterasi:</strong> {{ $iterations }}</p>
    </div>

    <h2>CENTROID CLUSTER</h2>
    <table class="centroid-table">
        <thead>
            <tr>
                <th>Cluster</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Sikap</th>
                <th>Pramuka</th>
                <th>PMR</th>
                <th>Kehadiran (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($centroids as $index => $centroid)
            <tr style="background-color: {{ $clusterColors['Cluster ' . ($index + 1)] }}30">
                <td><strong>Cluster {{ $index + 1 }}</strong></td>
                <td>{{ number_format($centroid[0], 2) }}</td>
                <td>{{ number_format($centroid[1], 2) }}</td>
                <td>{{ number_format($centroid[2], 2) }}</td>
                <td>{{ number_format($centroid[3], 2) }}</td>
                <td>{{ number_format($centroid[4], 2) }}</td>
                <td>{{ number_format($centroid[5], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($clusterStats))
    <h2>STATISTIK CLUSTER</h2>
    <table class="stats-table">
        <thead>
            <tr>
                <th>Cluster</th>
                <th>Jumlah Siswa</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Sikap</th>
                <th>Pramuka</th>
                <th>PMR</th>
                <th>Kehadiran (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clusterStats as $index => $stats)
            <tr style="background-color: {{ $clusterColors['Cluster ' . ($index + 1)] }}30">
                <td><strong>Cluster {{ $index + 1 }}</strong></td>
                <td>{{ $stats['count'] }} ({{ number_format(($stats['count'] / $totalStudents) * 100, 1) }}%)</td>
                <td>{{ number_format($stats['avg_uts'], 2) }}</td>
                <td>{{ number_format($stats['avg_uas'], 2) }}</td>
                <td>{{ number_format($stats['avg_sikap'], 2) }} ({{ $stats['sikap_huruf'] }})</td>
                <td>{{ number_format($stats['avg_pramuka'], 2) }} ({{ $stats['pramuka_huruf'] }})</td>
                <td>{{ number_format($stats['avg_pmr'], 2) }} ({{ $stats['pmr_huruf'] }})</td>
                <td>{{ number_format($stats['avg_kehadiran'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h2>ANGGOTA CLUSTER</h2>

    @foreach($clustersByNumber as $clusterName => $clusterMembers)
    <div class="cluster-header" style="background-color: {{ $clusterColors[$clusterName] }}">
        {{ $clusterName }} - {{ count($clusterMembers) }} Siswa
        @if(!empty($clusterMembers[0]['karakteristik']))
        <span style="font-size: 10px; font-weight: normal;">({{ $clusterMembers[0]['karakteristik'] }})</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Sikap</th>
                <th>Pramuka</th>
                <th>PMR</th>
                <th>Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clusterMembers as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student['nama'] }}</td>
                <td>{{ $student['kelas'] }}</td>
                <td>{{ $student['uts'] }}</td>
                <td>{{ $student['uas'] }}</td>
                <td>{{ $student['sikap'] }}</td>
                <td>{{ $student['pramuka'] }}</td>
                <td>{{ $student['pmr'] }}</td>
                <td>{{ $student['kehadiran'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh sistem pada {{ $exportDate }}
    </div>
</body>

</html>