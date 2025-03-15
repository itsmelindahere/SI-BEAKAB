<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alternatif = $_POST['alternatif'];
    $C1 = $_POST['C1'];
    $C2 = $_POST['C2'];
    $C3 = $_POST['C3'];
    $C4 = $_POST['C4'];
    $C5 = $_POST['C5'];

    $query = "INSERT INTO alternatif (alternatif, C1, C2, C3, C4, C5) 
              VALUES ('$alternatif', '$C1', '$C2', '$C3', '$C4', '$C5')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='alternatif.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data!'); window.location='alternatif.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Alternatif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <strong>Tambah Alternatif</strong>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Alternatif</label>
                    <input type="text" name="alternatif" class="form-control" required>
                </div>
                <?php for ($i = 1; $i <= 5; $i++) { ?>
                    <div class="mb-3">
                        <label class="form-label">C<?= $i ?></label>
                        <input type="text" name="C<?= $i ?>" class="form-control" required>
                    </div>
                <?php } ?>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="home.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
