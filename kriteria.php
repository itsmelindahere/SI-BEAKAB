<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kriteria</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>
<body>

<div class="container mt-4">
  
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <strong>Data Kriteria</strong>
            <a href="tambah_kriteria.php" class="btn btn-warning btn-sm">Tambah Kriteria</a>
        </div>
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kriteria</th>
                        <th>Kepentingan</th>
                        <th>Cost / Benefit</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT * FROM kriteria");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        $id_kriteria = $row['id_kriteria'];
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['kriteria']}</td>
                                <td>{$row['kepentingan']}</td>
                                <td>{$row['cost_benefit']}</td>
                                <td>
                                    <a href='edit_kriteria.php?id={$id_kriteria}' class='btn btn-success btn-sm'>
                                    ✏️ Edit
                                    </a>
                                    <a href='hapus_kriteria.php?id={$id_kriteria}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>
                                    🗑️
                                    </a>
                                </td>
                                </tr>
                              </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- jQuery dan DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            lengthChange: false // Menghilangkan dropdown "Show X entries"
        });
    });
</script>

</body>
</html>
