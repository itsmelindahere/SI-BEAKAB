<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mulai transaksi agar data tetap aman jika terjadi error
    mysqli_begin_transaction($koneksi);

    try {
        // Ambil nama mahasiswa yang akan dihapus untuk menghapus di tabel alternatif
        $query_get_nama = "SELECT nama FROM mahasiswa WHERE id_mhs='$id'";
        $result = mysqli_query($koneksi, $query_get_nama);
        $row = mysqli_fetch_assoc($result);
        $nama_mahasiswa = $row['nama'];

        if ($nama_mahasiswa) {
            // Hapus data di tabel alternatif berdasarkan nama mahasiswa
            $query_delete_alternatif = "DELETE FROM alternatif WHERE alternatif='$nama_mahasiswa'";
            mysqli_query($koneksi, $query_delete_alternatif);
        }

        // Hapus data di tabel mahasiswa
        $query_delete_mahasiswa = "DELETE FROM mahasiswa WHERE id_mhs='$id'";
        if (mysqli_query($koneksi, $query_delete_mahasiswa)) {
            // Jika semua proses berhasil, commit transaksi
            mysqli_commit($koneksi);
            echo "<script>alert('Data berhasil dihapus!'); window.location='home.php';</script>";
        } else {
            throw new Exception("Gagal menghapus data mahasiswa.");
        }
    } catch (Exception $e) {
        // Jika terjadi kesalahan, rollback transaksi
        mysqli_rollback($koneksi);
        echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "'); window.location='home.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak ditemukan!'); window.location='home.php';</script>";
}
?>
