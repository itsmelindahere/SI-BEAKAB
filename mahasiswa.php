<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .table-container {
            border-radius: 8px;
            overflow-x: auto;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        th, td {
            white-space: nowrap;
        }
        .btn-group .btn {
            margin-right: 5px;
        }
        .search-container {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="m-0">📋 Data Mahasiswa</h4>
            <a href="tambah_mahasiswa.php" class="btn btn-warning btn-sm">+ Tambah Mahasiswa</a>
        </div>
        <div class="card-body">
          
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-bordered">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Email</th>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM mahasiswa");
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)) {
                            $id_mhs = $row['id_mhs'];
                            $penghasilan_rp = "Rp " . number_format($row['penghasilan'], 0, ',', '.'); // Format penghasilan ke rupiah
                            $file_url = "uploads/" . $row['file']; // Pastikan path benar

                            echo "<tr class='text-center'>
                                    <td>{$no}</td>
                                    <td class='text-start'>" . htmlspecialchars($row['nama']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>{$row['ipk']}</td>
                                    <td>" . htmlspecialchars($row['perguruan_tinggi']) . "</td>
                                    <td>" . htmlspecialchars($row['jenis_pt']) . "</td>
                                    <td>{$penghasilan_rp}</td>
                                    <td>" . htmlspecialchars($row['akreditasi']) . "</td>
                                    <td>{$row['saudara']}</td>
                                    <td>" . htmlspecialchars($row['sertifikat']) . "</td>
                                    <td>" . htmlspecialchars($row['pekerjaan_ayah']) . "</td>
                                    <td>" . htmlspecialchars($row['pekerjaan_ibu']) . "</td>
                                    <td><a href='{$file_url}' class='btn btn-sm btn-success' target='_blank'>📂 Lihat</a></td>
                                    <td>
                                        <div class='btn-group'>
                                            <a href='edit_mahasiswa.php?id={$id_mhs}' class='btn btn-success btn-sm'>✏️Edit</a>
                                            <a href='hapus_mahasiswa.php?id={$id_mhs}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>🗑️</a>
                                        </div>
                                    </td>
                                  </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery dan DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable dengan opsi pencarian dan paginasi
        var table = $('#dataTable').DataTable({
            lengthChange: false,  // Sembunyikan opsi "Show entries"
            pageLength: 10,       // Atur per halaman 10 data
            language: {
                search: "🔍Cari Nama Mahasiswa:", // Label pencarian di DataTables
                paginate: {
                    first: "«",
                    last: "»",
                    next: "›",
                    previous: "‹"
                }
            }
        });

        // Custom Pencarian
        $('#searchBox').on('keyup', function() {
            table.search(this.value).draw(); // Memfilter berdasarkan input
        });
    });
</script>

</body>
</html>
