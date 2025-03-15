<?php
include 'koneksi.php';

// Ambil data kepentingan dan cost/benefit dari kriteria
$query_kriteria = mysqli_query($koneksi, "SELECT kepentingan, cost_benefit FROM kriteria");
$kepentingan = [];
$cost_benefit = [];

while ($row = mysqli_fetch_assoc($query_kriteria)) {
    $kepentingan[] = $row['kepentingan'];
    $cost_benefit[] = $row['cost_benefit'];
}

// Hitung total kepentingan
$total_kepentingan = array_sum($kepentingan);

// Hitung bobot kepentingan
$bobot_kepentingan = array_map(fn($val) => $val / $total_kepentingan, $kepentingan);

// Hitung pangkat berdasarkan cost/benefit
$pangkat = array_map(function ($bobot, $cb) {
    return ($cb === "cost") ? -1 * $bobot : $bobot;
}, $bobot_kepentingan, $cost_benefit);

// Ambil data alternatif dan nilai C1 - C5
$query = mysqli_query($koneksi, "SELECT a.alternatif, a.C1, a.C2, a.C3, a.C4, a.C5, 
                                        m.pekerjaan_ayah, m.pekerjaan_ibu 
                                 FROM alternatif a 
                                 JOIN mahasiswa m ON a.alternatif = m.nama");

$data = [];
$total_s = 0;

// Hitung nilai S dengan pangkat yang benar
while ($row = mysqli_fetch_assoc($query)) {
    $C = [$row['C1'], $row['C2'], $row['C3'], $row['C4'], $row['C5']];
    $nilai_s = array_product(array_map(fn($val, $pang) => pow($val, $pang), $C, $pangkat));
    
    $data[] = [
        'nama' => $row['alternatif'],
        'nilai_s' => $nilai_s,
        'pekerjaan_ayah' => $row['pekerjaan_ayah'],
        'pekerjaan_ibu' => $row['pekerjaan_ibu']
    ];
    
    $total_s += $nilai_s;
}

// Hitung nilai V
foreach ($data as $key => $row) {
    $data[$key]['nilai_v'] = round($row['nilai_s'] / $total_s, 6);
}

// Urutkan berdasarkan nilai V (descending)
usort($data, function ($a, $b) {
    return $b['nilai_v'] <=> $a['nilai_v'];
});

// Pisahkan yang memiliki PNS ke peringkat bawah
$non_pns = [];
$pns = [];

foreach ($data as $row) {
    if ($row['pekerjaan_ayah'] === 'PNS' || $row['pekerjaan_ibu'] === 'PNS') {
        $pns[] = $row;
    } else {
        $non_pns[] = $row;
    }
}

// Gabungkan kembali dengan PNS di bawah
$ranked_data = array_merge($non_pns, $pns);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .peringkat-bawah {
            background-color: #ffcccc !important; /* Warna merah muda untuk seluruh baris */
        }
        .pns {
            background-color: #ff9999 !important; /* Warna merah untuk kolom pekerjaan */
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <b>Ranking Mahasiswa</b>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nama</th>
                        <th>Nilai Akhir (V)</th>
                        <th>Pekerjaan Ayah</th>
                        <th>Pekerjaan Ibu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ranked_data as $index => $row): ?>
                        <tr class="<?= ($row['pekerjaan_ayah'] === 'PNS' || $row['pekerjaan_ibu'] === 'PNS') ? 'peringkat-bawah' : '' ?>">
                            <td><?= $index + 1 ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['nilai_v'] ?></td>
                            <td class="<?= $row['pekerjaan_ayah'] === 'PNS' ? 'pns' : '' ?>"><?= $row['pekerjaan_ayah'] ?></td>
                            <td class="<?= $row['pekerjaan_ibu'] === 'PNS' ? 'pns' : '' ?>"><?= $row['pekerjaan_ibu'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
