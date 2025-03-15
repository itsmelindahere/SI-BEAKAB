<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data dari database
    $query = mysqli_query($koneksi, "SELECT * FROM kriteria WHERE id_kriteria = '$id'");
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        echo "Data tidak ditemukan!";
        exit;
    }
} else {
    echo "ID tidak ditemukan!";
    exit;
}

// Proses update jika tombol simpan ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kriteria = $_POST['kriteria'];
    $kepentingan = $_POST['kepentingan'];
    $cost_benefit = $_POST['cost_benefit'];

    $updateQuery = "UPDATE kriteria SET kriteria='$kriteria', kepentingan='$kepentingan', cost_benefit='$cost_benefit' WHERE id_kriteria='$id'";

    if (mysqli_query($koneksi, $updateQuery)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='kriteria.php';</script>";
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
    <title>Edit Kriteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-dark">
            <strong>Edit Kriteria</strong>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="kriteria" class="form-label">Kriteria</label>
                    <input type="text" class="form-control" id="kriteria" name="kriteria" value="<?= $data['kriteria']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="kepentingan" class="form-label">Kepentingan</label>
                    <input type="number" class="form-control" id="kepentingan" name="kepentingan" value="<?= $data['kepentingan']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="cost_benefit" class="form-label">Cost / Benefit</label>
                    <select class="form-control" id="cost_benefit" name="cost_benefit" required>
                        <option value="cost" <?= ($data['cost_benefit'] == 'cost') ? 'selected' : ''; ?>>Cost</option>
                        <option value="benefit" <?= ($data['cost_benefit'] == 'benefit') ? 'selected' : ''; ?>>Benefit</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="kriteria.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
