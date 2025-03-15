<?php
include 'koneksi.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    
    // Validasi status hanya boleh "Diterima" atau "Ditolak"
    if ($status !== "Diterima" && $status !== "Ditolak") {
        echo "Status tidak valid";
        exit;
    }
    
    // Update status di database
    $query = "UPDATE laporan_lpj SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Status berhasil diperbarui'); window.location.href='hom_mahasiswa.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status'); window.location.href='home_mahasiswa.php';</script>";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Parameter tidak lengkap'); window.location.href='home_mahasiswa.php';</script>";
}

mysqli_close($koneksi);
?>
