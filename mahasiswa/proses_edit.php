<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mhs = intval($_POST['id_mhs']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $ipk = floatval($_POST['ipk']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $perguruan_tinggi = mysqli_real_escape_string($koneksi, $_POST['perguruan_tinggi']);
    $jenis_pt = mysqli_real_escape_string($koneksi, $_POST['jenis_pt']);
    $penghasilan = floatval(str_replace(['Rp', '.', ','], '', $_POST['penghasilan']));
    $akreditasi = mysqli_real_escape_string($koneksi, $_POST['akreditasi']);
    $saudara = intval($_POST['saudara']);
    $sertifikat = mysqli_real_escape_string($koneksi, $_POST['sertifikat']);
    $pekerjaan_ayah = mysqli_real_escape_string($koneksi, $_POST['pekerjaan_ayah']);
    $pekerjaan_ibu = mysqli_real_escape_string($koneksi, $_POST['pekerjaan_ibu']);
    $status_input = "Selesai"; // Bisa diubah sesuai aturan Anda

    // Ambil data IPS dari form
    $ips_values = $_POST['ips']; // Array IPS dari form

    // Mulai Transaksi Database
    $koneksi->begin_transaction();

    try {
        // Update data mahasiswa
        $query = "UPDATE mahasiswa SET 
                    nama = '$nama', 
                    email = '$email', 
                    ipk = '$ipk', 
                    perguruan_tinggi = '$perguruan_tinggi', 
                    jenis_pt = '$jenis_pt', 
                    penghasilan = '$penghasilan', 
                    akreditasi = '$akreditasi', 
                    saudara = '$saudara', 
                    sertifikat = '$sertifikat', 
                    pekerjaan_ayah = '$pekerjaan_ayah', 
                    pekerjaan_ibu = '$pekerjaan_ibu',
                    status_input = '$status_input' 
                  WHERE id_mhs = $id_mhs";

        if (!mysqli_query($koneksi, $query)) {
            throw new Exception("Gagal memperbarui data mahasiswa: " . mysqli_error($koneksi));
        }

        // Hapus data IPS lama
        $query_delete_ips = "DELETE FROM ips_mahasiswa WHERE id_mhs = $id_mhs";
        if (!mysqli_query($koneksi, $query_delete_ips)) {
            throw new Exception("Gagal menghapus data IPS lama: " . mysqli_error($koneksi));
        }

        // Simpan data IPS baru
        foreach ($ips_values as $semester => $ips) {
            $semester = intval($semester);
            $ips = floatval($ips);

            $query_insert_ips = "INSERT INTO ips_mahasiswa (id_mhs, semester, ips) VALUES ($id_mhs, $semester, $ips)";
            if (!mysqli_query($koneksi, $query_insert_ips)) {
                throw new Exception("Gagal menyimpan data IPS: " . mysqli_error($koneksi));
            }
        }

        // Commit Transaksi
        $koneksi->commit();

        echo "<script>alert('Data berhasil diperbarui!'); window.location='home_mahasiswa.php';</script>";
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $koneksi->rollback();
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>