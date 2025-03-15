<?php
include 'koneksi.php';
session_start();

function get_alternatif() {
    global $koneksi;
    $query = mysqli_query($koneksi, "SELECT DISTINCT a.*, m.pekerjaan_ayah, m.pekerjaan_ibu, m.perguruan_tinggi 
                                     FROM alternatif a 
                                     JOIN mahasiswa m ON a.alternatif = m.nama");
    $alt = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $alt[] = [
            'nama' => $row['alternatif'],
            'C1' => $row['C1'],
            'C2' => $row['C2'],
            'C3' => $row['C3'],
            'C4' => $row['C4'],
            'C5' => $row['C5'],
            'pekerjaan_ayah' => $row['pekerjaan_ayah'],
            'pekerjaan_ibu' => $row['pekerjaan_ibu'],
            'perguruan_tinggi' => $row['perguruan_tinggi']
        ];
    }
    return $alt;
}

function get_kriteria() {
    global $koneksi;
    $query = mysqli_query($koneksi, "SELECT kepentingan, cost_benefit FROM kriteria");
    $kriteria = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $kriteria[] = $row;
    }
    return $kriteria;
}

// Data alternatif & kriteria
$alternatif = get_alternatif();
$kriteria = get_kriteria();

// Hitung bobot kepentingan
$kepentingan = array_column($kriteria, 'kepentingan');
$total_kepentingan = array_sum($kepentingan);
$bobot = array_map(fn($val) => $val / $total_kepentingan, $kepentingan);

// Hitung pangkat (cost/benefit)
$cost_benefit = array_column($kriteria, 'cost_benefit');
$pangkat = array_map(fn($b, $cb) => ($cb == 'cost' ? -1 * $b : $b), $bobot, $cost_benefit);

// Hitung nilai S untuk setiap alternatif
$nilai_s = [];
foreach ($alternatif as $alt) {
    $s = 1;
    for ($i = 0; $i < count($pangkat); $i++) {
        $s *= pow($alt["C" . ($i + 1)], $pangkat[$i]);
    }
    $nilai_s[] = [
        'nama' => $alt['nama'],
        'nilai' => $s,
        'pekerjaan_ayah' => $alt['pekerjaan_ayah'],
        'pekerjaan_ibu' => $alt['pekerjaan_ibu'],
        'perguruan_tinggi' => $alt['perguruan_tinggi']
    ];
}

// Hitung nilai V (normalisasi)
$total_s = array_sum(array_column($nilai_s, 'nilai'));
foreach ($nilai_s as &$alt) {
    $alt['nilai'] = round($alt['nilai'] / $total_s, 6);
}

// Urutkan berdasarkan nilai V tertinggi
usort($nilai_s, fn($a, $b) => $b['nilai'] <=> $a['nilai']);

// Hapus alternatif dengan nilai 0 dan yang memiliki orang tua PNS
$final_result = array_filter($nilai_s, fn($alt) => $alt['nilai'] > 0 && $alt['pekerjaan_ayah'] !== 'PNS' && $alt['pekerjaan_ibu'] !== 'PNS');
$final_result = array_values($final_result);

// Hapus duplikasi berdasarkan nama
$final_result = array_unique($final_result, SORT_REGULAR);

// Ambil tanggal hari ini dalam bahasa Indonesia
$bulan = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];
$tanggal_hari_ini = date('d ') . $bulan[date('F')] . date(' Y'); // Format: 14 Maret 2025
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Laporan Data</title>

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        @media print {
            .btn-cetak, .sidebar, .navbar, .footer {
                display: none;
            }
            body {
                padding: 20px;
            }
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 20px;
            padding: 20px 0;
            border-bottom: 2px solid #000;
        }
        .header img {
            margin-right: 20px;
            width: 120px;
            height: auto;
        }
        .header div {
            text-align: left;
        }
        .container-fluid {
            padding: 0 20px;
        }
        .table {
            color: #000;
            border-color: #000;
        }
        .table th,
        .table td {
            border-color: #000;
        }
        .table thead th {
            border-bottom: 2px solid #000;
        }
        .table-bordered {
            border: 2px solid #000;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000;
        }
        .header h4,
        .header h5,
        .header h6 {
            color: #000;
        }
        .pengumuman {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
            text-decoration: underline;
            color: #000; /* Tambahkan ini */
        }
        .penjelasan {
            text-align: center;
            margin: 10px 0 20px 0;
            font-size: 16px;
            color: #000; /* Tambahkan ini */
        }
        .catatan {
            margin-top: 20px;
            font-size: 14px;
            width: 100%;
            color: #000; /* Tambahkan ini */
        }
        .catatan p {
            margin: 5px 0;
        }
        .kisaran {
            text-align: right;
            margin-top: 20px;
            color: #000; /* Tambahkan ini */
        }
        .format-tanda-tangan {
            margin-top: 30px;
            text-align: right;
            color: #000; /* Tambahkan ini */
        }
        .format-tanda-tangan p {
            margin: 5px 0;
            line-height: 1.5;
        }
        .format-tanda-tangan .nama-jabatan {
            font-weight: bold;
        }
        .format-tanda-tangan .nip {
            margin-top: 10px;
        }
        .tanda-tangan {
            margin-top: 100px; /* Jarak untuk tanda tangan */
            text-align: right;
            color: #000; /* Tambahkan ini */
        }
        .tanda-tangan p {
            margin: 5px 0;
            line-height: 1.5;
        }
        .tanda-tangan .nama-jabatan {
            font-weight: bold;
        }
        .tanda-tangan .nip {
            margin-top: 10px;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="header">
                        <img src="logo-a.png" alt="Logo Kabupaten Asahan">
                        <div>
                            <h4 class="m-0 font-weight-bold" align="center">PEMERINTAH KABUPATEN ASAHAN</h4>
                            <h4 class="m-0 font-weight-bold" align="center">SEKRETARIAT DAERAH</h4>
                            <h6 class="m-0 font-weight-bold" align="center">Jalan Jenderal Sudirman No.5 Telepon 41928</h6>
                            <h5 class="m-0 font-weight-bold" align="center">KISARAN - 21216</h5>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="pengumuman">PENGUMUMAN</div>

                    <div class="penjelasan">
                        Sehubung dengan program beasiswa bagi mahasiswa berprestasi yang berasal dari keluarga tidak mampu yang diadakan oleh pemerintah kabupaten Asahan. Berikut merupakan keputusan penerima beasiswa tahun 2024.
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ranking</th>
                                <th>Nama</th>
                                <th>Perguruan Tinggi</th>
                                <th>Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($final_result as $i => $alt): ?>
                                <tr>
                                    <td><b><?= $i + 1 ?></b></td>
                                    <td><?= $alt['nama'] ?></td>
                                    <td><?= $alt['perguruan_tinggi'] ?></td>
                                    <td><?= $alt['nilai'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="catatan">
                        <p>Bagi nama-nama tersebut di atas agar:</p>
                        <p>Membawa Bukti Pendaftaran Asli dan Fotocopy Rekening Bank Sumut yang masih aktif atas nama mahasiswa sendiri.</p>
                    </div>

                    <div class="format-tanda-tangan">
                        <p>Kisaran, <?= $tanggal_hari_ini ?></p>
                        <p class="nama-jabatan">An. BUPATI ASAHAN</p>
                        <p class="nama-jabatan">SEKRETARIS DAERAH</p>
                    </div>

                    <div class="tanda-tangan">
                        <p class="nama-jabatan">Drs. H. JOHN HARDI NASUTION, M.Si</p>
                        <p class="nama-jabatan">PEMBINA UTAMA MADYA</p>
                        <p class="nip">NIP 19670502 199002 1 002</p>
                    </div>

                    <div class="text-end mt-3">
                        <button class="btn btn-success btn-cetak" onclick="window.print()">Cetak Laporan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>