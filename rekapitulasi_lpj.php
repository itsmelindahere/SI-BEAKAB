<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi LPJ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center mb-4">Rekapitulasi LPJ</h2>

        <!-- Tombol Cetak -->
        <div class="mb-3 text-end">
            <a href="cetak_rekapitulasi.php" target="_blank" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Surat Pernyataan</th>
                        <th>Laporan Dana</th>
                        <th>Bukti UKT</th>
                        <th>Fakta Integritas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    include 'koneksi.php';

                    if (!$koneksi) {
                        die("Koneksi gagal: " . mysqli_connect_error());
                    }

                    $query = "SELECT l.id, p.nama, p.nim, l.surat_pernyataan, l.laporan_dana, l.bukti_ukt, l.fakta_integritas, l.status 
                              FROM laporan_lpj l
                              JOIN profil_mhs p ON l.email = p.email";
                    
                    $result = mysqli_query($koneksi, $query);

                    if (!$result) {
                        die("Error pada query: " . mysqli_error($koneksi));
                    }

                    $base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SPK_WP_TEST/uploads/lpj/";

                    while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['nim']) ?></td>
                        
                        <?php 
                        $files = ['surat_pernyataan', 'laporan_dana', 'bukti_ukt', 'fakta_integritas'];
                        foreach ($files as $file) { ?>
                            <td>
                                <?php if (!empty($row[$file])): ?>
                                    <a href="<?= $base_url . htmlspecialchars($row[$file]) ?>" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-file-alt"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    <span class="text-danger">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                        <?php } ?>

                        <td>
                            <?php if ($row['status'] == 'Diterima'): ?>
                                <span class="badge bg-success">Diterima</span>
                            <?php elseif ($row['status'] == 'Ditolak'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="update_status_lpj.php?id=<?= $row['id']; ?>&status=Diterima" class="btn btn-success btn-sm">
                                <i class="fas fa-check-circle"></i> Terima
                            </a>
                            <a href="update_status_lpj.php?id=<?= $row['id']; ?>&status=Ditolak" class="btn btn-danger btn-sm">
                                <i class="fas fa-times-circle"></i> Tolak
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div> 
</body>
</html>
