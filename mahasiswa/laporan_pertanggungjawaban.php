<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['email'])) {
    die("Anda harus login terlebih dahulu.");
}

$email = $_SESSION['email'];
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-upload"></i> Upload Laporan Pertanggungjawaban (LPJ)</h4>
                </div>
                <div class="card-body">
                    <form action="upload_lpj.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label"><strong>Surat Pernyataan (PDF)</strong></label>
                            <input type="file" name="surat_pernyataan" class="form-control" accept=".pdf" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Laporan Penggunaan Dana (PDF)</strong></label>
                            <input type="file" name="laporan_dana" class="form-control" accept=".pdf" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Bukti Pembayaran UKT (PDF/Gambar)</strong></label>
                            <input type="file" name="bukti_ukt" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>

                        <!-- Tambahan Fakta Integritas -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Fakta Integritas (PDF)</strong></label>
                            <input type="file" name="fakta_integritas" class="form-control" accept=".pdf" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Kirim LPJ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="home_mahasiswa.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan animasi CSS -->
<style>
    .card {
        border-radius: 15px;
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .btn-success {
        background: linear-gradient(to right, #28a745, #218838);
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 16px;
    }

    .btn-success:hover {
        background: linear-gradient(to right, #218838, #1e7e34);
    }
</style>
