<?php
session_start();
include '../koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'mahasiswa') {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php';</script>";
    exit;
}

// Ambil email dari session
$email_petugas = mysqli_real_escape_string($koneksi, $_SESSION['email']);

// Ambil data petugas berdasarkan email
$query_petugas = mysqli_query($koneksi, "SELECT nama FROM petugas WHERE email = '$email_petugas'");
if (mysqli_num_rows($query_petugas) > 0) {
    $petugas = mysqli_fetch_assoc($query_petugas);
    $nama_mahasiswa = $petugas['nama']; // Ambil nama dari tabel petugas
   

    // Cek apakah mahasiswa sudah mengisi data berdasarkan nama
    $query_mahasiswa = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nama = '$nama_mahasiswa'");
    $mahasiswa_terdaftar = mysqli_num_rows($query_mahasiswa) > 0;

    // Jika mahasiswa sudah ada, ambil datanya
    $mahasiswa = mysqli_fetch_assoc($query_mahasiswa);
} else {
    echo "<script>alert('Data petugas tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Input Data Mahasiswa</h4>
        </div>
        <div class="card-body">
            <?php if ($mahasiswa_terdaftar): ?>
                <div class="alert alert-warning">
                    Anda sudah mengisi data sebelumnya. Silakan <a href="edit_mahasiswa.php?id=<?= $mahasiswa['id_mhs'] ?>">edit data</a> jika perlu.
                </div>
            <?php else: ?>
                <form method="POST" action="proses_input.php" onsubmit="return validateForm()" enctype="multipart/form-data">
                    <input type="hidden" name="status_input" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                    <!-- Tambahan kolom Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= $email_mahasiswa; ?>" required>
                    </div>


                    <!-- Bagian IPK yang diubah -->
                    <div class="mb-3">
                        <label class="form-label">Semester</label>
                        <select name="semester" id="semester" class="form-control" required onchange="updateIPSFields()">
                            <option value="" hidden>Pilih Semester</option>
                            <?php for ($i = 2; $i <= 8; $i++): ?>
                                <option value="<?= $i ?>">Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div id="ips-container"></div>

                    <div class="mb-3">
                        <label class="form-label">IPK (Otomatis dihitung)</label>
                        <input type="text" name="ipk" id="ipk" class="form-control" readonly>
                    </div>
                    <!-- Akhir bagian IPK yang diubah -->

                    <div class="mb-3">
                        <label class="form-label">Perguruan Tinggi</label>
                        <input type="text" name="perguruan_tinggi" id="perguruan_tinggi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis PT</label>
                        <select name="jenis_pt" id="jenis_pt" class="form-control" required>
                            <option value="" hidden>Pilih Jenis PT</option>
                            <option value="Negeri">Negeri</option>
                            <option value="Swasta">Swasta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penghasilan Orang Tua</label>
                        <input type="text" id="penghasilan" name="penghasilan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Akreditasi Perguruan Tinggi</label>
                        <select name="akreditasi" id="akreditasi" class="form-control" required>
                            <option value="" hidden>Pilih Akreditasi</option>
                            <option value="Unggul">Unggul</option>
                            <option value="Baik Sekali">Baik Sekali</option>
                            <option value="Baik">Baik</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Saudara</label>
                        <input type="number" name="saudara" id="saudara" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sertifikat Kompetensi</label>
                        <select name="sertifikat" id="sertifikat" class="form-control" required>
                            <option value="" hidden>Pilih Opsi</option>
                            <option value="Ada">Ada</option>
                            <option value="Tidak Ada">Tidak Ada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pekerjaan Ayah</label>
                        <select name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-control" required>
                            <option value="" hidden>Pilih Pekerjaan</option>
                            <option value="PNS">PNS</option>
                            <option value="Karyawan BUMN">Karyawan BUMN</option>
                            <option value="Karyawan Swasta">Karyawan Swasta</option>
                            <option value="Mengurus Rumah Tangga">Mengurus Rumah Tangga</option>
                            <option value="Tidak Bekerja">Tidak Bekerja</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pekerjaan Ibu</label>
                        <select name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-control" required>
                            <option value="" hidden>Pilih Pekerjaan</option>
                            <option value="PNS">PNS</option>
                            <option value="Karyawan BUMN">Karyawan BUMN</option>
                            <option value="Karyawan Swasta">Karyawan Swasta</option>
                            <option value="Mengurus Rumah Tangga">Mengurus Rumah Tangga</option>
                            <option value="Tidak Bekerja">Tidak Bekerja</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Berkas (PDF)</label>
                        <input type="file" name="file" id="file" class="form-control" accept="application/pdf" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="home_mahasiswa.php" class="btn btn-secondary">Batal</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Format input penghasilan ke Rupiah
document.getElementById('penghasilan').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value ? 'Rp' + parseInt(value).toLocaleString('id-ID') : '';
});

// Validasi Form Sebelum Submit
function validateForm() {
    let fields = ["nama", "email", "ipk", "perguruan_tinggi", "jenis_pt", "penghasilan", "akreditasi", "saudara", "sertifikat", "pekerjaan_ayah", "pekerjaan_ibu"];
    for (let field of fields) {
        let input = document.getElementById(field);
        if (!input.value.trim()) {
            alert("Harap isi semua kolom sebelum mengirim formulir!");
            input.focus();
            return false;
        }
    }
    // Pastikan input IPK menggunakan titik, bukan koma
    document.getElementById('ipk').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(',', '.');
    });
    // Pastikan IPK dalam rentang yang valid
    let ipk = parseFloat(document.getElementById('ipk').value);
    if (ipk < 0 || ipk > 4) {
        alert("IPK harus dalam rentang 0 - 4");
        document.getElementById('ipk').focus();
        return false;
    }
    // Validasi ukuran file ≤ 2MB
    let fileInput = document.getElementById('file');
    if (fileInput.files[0].size > 2 * 1024 * 1024) {
        alert("Ukuran file harus maksimal 2MB");
        fileInput.value = "";
        return false;
    }

    return true;
}

// Fungsi untuk mengupdate field IPS berdasarkan semester yang dipilih
function updateIPSFields() {
    let semester = document.getElementById('semester').value;
    let container = document.getElementById('ips-container');
    container.innerHTML = '';

    if (semester) {
        let totalIps = 0;
        for (let i = 1; i < semester; i++) {
            container.innerHTML += `
                <div class="mb-3">
                    <label class="form-label">IPS Semester ${i}</label>
                    <input type="number" step="0.01" name="ips[]" class="form-control ips-input" min="0" max="4" required>
                </div>
            `;
        }

        // Tambahkan event listener untuk menghitung rata-rata IPS
        setTimeout(() => {
            document.querySelectorAll('.ips-input').forEach(input => {
                input.addEventListener('input', calculateIPK);
            });
        }, 100);
    }
}

// Fungsi untuk menghitung IPK berdasarkan IPS yang dimasukkan
function calculateIPK() {
    let ipsInputs = document.querySelectorAll('.ips-input');
    let total = 0;
    let count = 0;

    ipsInputs.forEach(input => {
        let value = parseFloat(input.value);
        if (!isNaN(value)) {
            total += value;
            count++;
        }
    });

    let ipk = count > 0 ? (total / count).toFixed(2) : '';
    document.getElementById('ipk').value = ipk;
}
</script>

</body>
</html>