<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM kriteria WHERE id_kriteria = '$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='home.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location='home.php';</script>";
    }
}
?>
