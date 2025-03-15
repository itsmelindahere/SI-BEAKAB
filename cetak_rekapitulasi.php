<?php
include 'koneksi.php';

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query = "SELECT p.nama, p.nim FROM laporan_lpj l
          JOIN profil_mhs p ON l.email = p.email";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Error pada query: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekapitulasi LPJ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        @media print {
            .btn-cetak {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Rekapitulasi Mahasiswa yang Telah Menyerahkan LPJ</h2>
        
        <!-- Tombol Cetak -->
        <div class="text-end">
            <button class="btn btn-primary btn-cetak" onclick="cetakDanKembali();">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>

        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIM</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['nim']) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        function cetakDanKembali() {
            window.print(); // Cetak dokumen
            
            setTimeout(() => {
                window.location.href = "home.php"; // Kembali ke home setelah cetak selesai
            }, 1000);
        }
    </script>
</body>
</html>
