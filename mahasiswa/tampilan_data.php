<?php
session_start();
include '../koneksi.php';

// Pastikan petugas sudah login sebelum mengakses halaman
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php';</script>";
    exit;
}

// Ambil email petugas dari session
$email_petugas = mysqli_real_escape_string($koneksi, $_SESSION['email']);

// Cari nama petugas berdasarkan email yang login
$query_petugas = mysqli_query($koneksi, "SELECT nama FROM petugas WHERE email = '$email_petugas'");
$data_petugas = mysqli_fetch_assoc($query_petugas);

// Pastikan petugas ditemukan
if (!$data_petugas) {
    echo "<script>alert('Data petugas tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$nama_petugas = $data_petugas['nama'];

// Cari data mahasiswa berdasarkan nama petugas
$query_mahasiswa = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nama = '$nama_petugas'");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .table-container {
            border-radius: 8px;
            overflow-x: auto; /* Agar tabel bisa digeser jika lebar */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        th, td {
            white-space: nowrap; /* Mencegah teks turun ke bawah */
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>📋 Data Mahasiswa</h4>
        </div>
        <div class="card-body">
            <div class="table-container bg-white p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="text-center">
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>IPK</th>
                                <th>Perguruan Tinggi</th>
                                <th>Jenis PT</th>
                                <th>Penghasilan</th>
                                <th>Akreditasi</th>
                                <th>Saudara</th>
                                <th>Sertifikat</th>
                                <th>Pekerjaan Ayah</th>
                                <th>Pekerjaan Ibu</th>
                                <th>Berkas</th>
                                <th>Status Input</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query_mahasiswa)) {
                                // Cek status input mahasiswa
                                $status_input = ($row['status_input'] == 1) ? "<span class='badge bg-success'>Sudah Mengisi</span>" : "<span class='badge bg-danger'>Belum Mengisi</span>";

                                echo "<tr class='text-center'>
                                        <td>{$no}</td>
                                        <td>" . htmlspecialchars($row['nama']) . "</td>
                                        <td>" . number_format($row['ipk'], 2) . "</td>
                                        <td>" . htmlspecialchars($row['perguruan_tinggi']) . "</td>
                                        <td>" . htmlspecialchars($row['jenis_pt']) . "</td>
                                        <td>Rp " . number_format($row['penghasilan'], 0, ',', '.') . "</td>
                                        <td>" . htmlspecialchars($row['akreditasi']) . "</td>
                                        <td>{$row['saudara']}</td>
                                        <td>" . htmlspecialchars($row['sertifikat']) . "</td>
                                        <td>" . htmlspecialchars($row['pekerjaan_ayah']) . "</td>
                                        <td>" . htmlspecialchars($row['pekerjaan_ibu']) . "</td>
                                        <td><a href='../uploads/" . htmlspecialchars($row['file']) . "' class='btn btn-sm btn-success' target='_blank'>📂 Lihat</a></td>
                                        <td>{$status_input}</td>
                                        <td>
                                            <a href='edit_mahasiswa.php?id={$row['id_mhs']}' class='btn btn-warning btn-sm'>✏️Edit</a> ";

                        

                                echo "</td></tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div> <!-- table-responsive -->
            </div> <!-- table-container -->
        </div> <!-- card-body -->
    </div> <!-- card -->
</div> <!-- container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
