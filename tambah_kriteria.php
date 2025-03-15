<?php
include 'koneksi.php';

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kriteria = $_POST['kriteria'];
    $kepentingan = $_POST['kepentingan'];
    $cost_benefit = $_POST['cost_benefit'];

    $query = "INSERT INTO kriteria (kriteria, kepentingan, cost_benefit) VALUES ('$kriteria', '$kepentingan', '$cost_benefit')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kriteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <strong>Tambah Kriteria</strong>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="kriteria" class="form-label">Kriteria</label>
                    <input type="text" class="form-control" id="kriteria" name="kriteria" required>
                </div>
                <div class="mb-3">
                    <label for="kepentingan" class="form-label">Kepentingan</label>
                    <input type="number" class="form-control" id="kepentingan" name="kepentingan" required>
                </div>
                <div class="mb-3">
                    <label for="cost_benefit" class="form-label">Cost / Benefit</label>
                    <select class="form-control" id="cost_benefit" name="cost_benefit" required>
                        <option value="cost">Cost</option>
                        <option value="benefit">Benefit</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="home.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
