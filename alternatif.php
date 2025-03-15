<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Alternatif</title>
    
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
            <strong>Data Alternatif</strong>
            <a href="tambah_alternatif.php" class="btn btn-warning btn-sm">Tambah Alternatif</a>
        </div>
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Alternatif</th>
                        <th>C1</th>
                        <th>C2</th>
                        <th>C3</th>
                        <th>C4</th>
                        <th>C5</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($koneksi, "SELECT * FROM alternatif");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        $id_alternatif = $row['id_alternatif'];
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['alternatif']}</td>
                                <td>{$row['C1']}</td>
                                <td>{$row['C2']}</td>
                                <td>{$row['C3']}</td>
                                <td>{$row['C4']}</td>
                                <td>{$row['C5']}</td>
                                <td>
                                    <a href='edit_alternatif.php?id={$id_alternatif}' class='btn btn-success btn-sm'>
                                    ✏️ Edit
                                    </a>
                                    <a href='hapus_alternatif.php?id={$id_alternatif}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>
                                    🗑️
                                    </a>
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
