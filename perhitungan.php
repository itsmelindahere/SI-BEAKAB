<?php
include 'koneksi.php';
session_start();

// Ambil data dari database
function jml_kriteria()
{
    global $koneksi;
    $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kriteria");
    $data = mysqli_fetch_assoc($query);
    return $data['total'];
}

function jml_alternatif()
{
    global $koneksi;
    $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM alternatif");
    $data = mysqli_fetch_assoc($query);
    return $data['total'];
}

function get_kepentingan()
{
    global $koneksi;
    $query = mysqli_query($koneksi, "SELECT kepentingan FROM kriteria");
    $kepentingan = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $kepentingan[] = $row['kepentingan'];
    }
    return $kepentingan;
}

function get_costbenefit()
{
    global $koneksi;
    $query = mysqli_query($koneksi, "SELECT cost_benefit FROM kriteria");
    $costbenefit = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $costbenefit[] = $row['cost_benefit'];
    }
    return $costbenefit;
}

function get_alt_name()
{
    global $koneksi;
    $query = mysqli_query($koneksi, "SELECT alternatif FROM alternatif");
    $alt_name = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $alt_name[] = $row['alternatif'];
    }
    return $alt_name;
}

function get_alternatif()
{
    global $koneksi;
    $query = mysqli_query($koneksi, "SELECT * FROM alternatif");
    $alt = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $alt[] = [$row['C1'], $row['C2'], $row['C3'], $row['C4'], $row['C5']];
    }
    return $alt;
}

// Hitung Matrix Alternatif - Kriteria
$alt = get_alternatif();
$alt_name = get_alt_name();
$kep = get_kepentingan();
$cb = get_costbenefit();
$k = jml_kriteria();
$a = jml_alternatif();
$tkep = array_sum($kep);

// Hitung Bobot Kepentingan
$bkep = array_map(fn($val) => $val / $tkep, $kep);

// Hitung Pangkat
$pangkat = array_map(function ($val, $cb_val) {
    return ($cb_val == "cost") ? -1 * $val : $val;
}, $bkep, $cb);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perhitungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <b>Perhitungan</b>
        </div>
        <div class="card-body">
            <center>
                <h5><b>Matrix Alternatif - Kriteria</b></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Alternatif / Kriteria</th>
                            <th>C1</th><th>C2</th><th>C3</th><th>C4</th><th>C5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alt as $i => $row): ?>
                            <tr>
                                <td><b>A<?= $i + 1 ?></b></td>
                                <?php foreach ($row as $value): ?>
                                    <td><?= $value ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h5><b>Perhitungan Bobot Kepentingan</b></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>C1</th><th>C2</th><th>C3</th><th>C4</th><th>C5</th><th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Kepentingan</b></td>
                            <?php foreach ($kep as $value): ?>
                                <td><?= $value ?></td>
                            <?php endforeach; ?>
                            <td><?= $tkep ?></td>
                        </tr>
                        <tr>
                            <td><b>Bobot Kepentingan</b></td>
                            <?php foreach ($bkep as $value): ?>
                                <td><?= round($value, 6) ?></td>
                            <?php endforeach; ?>
                            <td><?= array_sum($bkep) ?></td>
                        </tr>
                    </tbody>
                </table>

                <h5><b>Perhitungan Pangkat</b></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>C1</th><th>C2</th><th>C3</th><th>C4</th><th>C5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Cost/Benefit</b></td>
                            <?php foreach ($cb as $value): ?>
                                <td><?= ucwords($value) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><b>Pangkat</b></td>
                            <?php foreach ($pangkat as $value): ?>
                                <td><?= round($value, 6) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>

                <h5><b>Perhitungan Nilai S</b></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Alternatif</th><th>S</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $ss = [];
                        foreach ($alt as $i => $row) {
                            $ss[$i] = array_product(array_map(fn($val, $pang) => pow($val, $pang), $row, $pangkat));
                        ?>
                            <tr>
                                <td><b>A<?= $i + 1 ?></b></td>
                                <td><?= round($ss[$i], 6) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <h5><b>Hasil Akhir</b></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Alternatif</th><th>V</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = array_sum($ss);
                        $v = array_map(fn($val) => round($val / $total, 6), $ss);
                        foreach ($alt_name as $i => $name) { ?>
                            <tr>
                                <td><b><?= $name ?></b></td>
                                <td><?= $v[$i] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </center>
        </div>
    </div>
</div>

</body>
</html>
