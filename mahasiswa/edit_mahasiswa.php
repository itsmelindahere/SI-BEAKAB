<?php
include '../koneksi.php';

// Pastikan ID mahasiswa ada di URL
if (!isset($_GET['id'])) {
    echo "<script>alert('ID tidak ditemukan!'); window.location='tampilan_data.php';</script>";
    exit;
}

$id = intval($_GET['id']); // Pastikan ID berupa angka
$query = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE id_mhs = $id");
$data = mysqli_fetch_assoc($query);
$email_mahasiswa = $data['email'];

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='tampilan_data.php';</script>";
    exit;
}

// Ambil data IPS jika ada (contoh: dari tabel ips_mahasiswa)
$query_ips = mysqli_query($koneksi, "SELECT semester, ips FROM ips_mahasiswa WHERE id_mhs = $id ORDER BY semester");
$ips_data = [];
while ($row = mysqli_fetch_assoc($query_ips)) {
    $ips_data[$row['semester']] = $row['ips'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Edit Data Mahasiswa</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="proses_edit.php" onsubmit="return validateForm()" enctype="multipart/form-data">
                <input type="hidden" name="id_mhs" value="<?= $id; ?>">

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email_mahasiswa); ?>" required>
                </div>


                <!-- Bagian IPK yang diubah -->
                <div class="mb-3">
                    <label class="form-label">Semester</label>
                    <select name="semester" id="semester" class="form-control" required onchange="updateIPSFields()">
                        <option value="" hidden>Pilih Semester</option>
                        <?php for ($i = 2; $i <= 8; $i++): ?>
                            <option value="<?= $i ?>" <?= isset($ips_data[$i]) ? 'selected' : ''; ?>>Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div id="ips-container">
                    <?php
                    if (!empty($ips_data)) {
                        foreach ($ips_data as $semester => $ips) {
                            echo '
                                <div class="mb-3">
                                    <label class="form-label">IPS Semester ' . $semester . '</label>
                                    <input type="number" step="0.01" name="ips[]" class="form-control ips-input" min="0" max="4" value="' . $ips . '" required>
                                </div>
                            ';
                        }
                    }
                    ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">IPK (Otomatis dihitung)</label>
                    <input type="text" name="ipk" id="ipk" class="form-control" value="<?= $data['ipk']; ?>" readonly>
                </div>
                <!-- Akhir bagian IPK yang diubah -->

                <div class="mb-3">
                    <label class="form-label">Perguruan Tinggi</label>
                    <input type="text" name="perguruan_tinggi" id="perguruan_tinggi" class="form-control" value="<?= htmlspecialchars($data['perguruan_tinggi']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis PT</label>
                    <select name="jenis_pt" id="jenis_pt" class="form-control" required>
                        <option value="Negeri" <?= $data['jenis_pt'] == "Negeri" ? 'selected' : ''; ?>>Negeri</option>
                        <option value="Swasta" <?= $data['jenis_pt'] == "Swasta" ? 'selected' : ''; ?>>Swasta</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Penghasilan Orang Tua</label>
                    <input type="text" name="penghasilan" id="penghasilan" class="form-control" value="Rp<?= number_format($data['penghasilan'], 0, ',', '.'); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Akreditasi Perguruan Tinggi</label>
                    <select name="akreditasi" id="akreditasi" class="form-control" required>
                        <option value="Unggul" <?= $data['akreditasi'] == "Unggul" ? 'selected' : ''; ?>>Unggul</option>
                        <option value="Baik Sekali" <?= $data['akreditasi'] == "Baik Sekali" ? 'selected' : ''; ?>>Baik Sekali</option>
                        <option value="Baik" <?= $data['akreditasi'] == "Baik" ? 'selected' : ''; ?>>Baik</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Saudara</label>
                    <input type="number" name="saudara" id="saudara" class="form-control" value="<?= $data['saudara']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sertifikat Kompetensi</label>
                    <select name="sertifikat" id="sertifikat" class="form-control" required>
                        <option value="Ada" <?= $data['sertifikat'] == "Ada" ? 'selected' : ''; ?>>Ada</option>
                        <option value="Tidak Ada" <?= $data['sertifikat'] == "Tidak Ada" ? 'selected' : ''; ?>>Tidak Ada</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pekerjaan Ayah</label>
                    <select name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-control" required>
                        <option value="PNS" <?= $data['pekerjaan_ayah'] == "PNS" ? 'selected' : ''; ?>>PNS</option>
                        <option value="Karyawan BUMN" <?= $data['pekerjaan_ayah'] == "Karyawan BUMN" ? 'selected' : ''; ?>>Karyawan BUMN</option>
                        <option value="Karyawan Swasta" <?= $data['pekerjaan_ayah'] == "Karyawan Swasta" ? 'selected' : ''; ?>>Karyawan Swasta</option>
                        <option value="Mengurus Rumah Tangga" <?= $data['pekerjaan_ayah'] == "Mengurus Rumah Tangga" ? 'selected' : ''; ?>>Mengurus Rumah Tangga</option>
                        <option value="Tidak Bekerja" <?= $data['pekerjaan_ayah'] == "Tidak Bekerja" ? 'selected' : ''; ?>>Tidak Bekerja</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pekerjaan Ibu</label>
                    <select name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-control" required>
                        <option value="PNS" <?= $data['pekerjaan_ibu'] == "PNS" ? 'selected' : ''; ?>>PNS</option>
                        <option value="Karyawan BUMN" <?= $data['pekerjaan_ibu'] == "Karyawan BUMN" ? 'selected' : ''; ?>>Karyawan BUMN</option>
                        <option value="Karyawan Swasta" <?= $data['pekerjaan_ibu'] == "Karyawan Swasta" ? 'selected' : ''; ?>>Karyawan Swasta</option>
                        <option value="Mengurus Rumah Tangga" <?= $data['pekerjaan_ibu'] == "Mengurus Rumah Tangga" ? 'selected' : ''; ?>>Mengurus Rumah Tangga</option>
                        <option value="Tidak Bekerja" <?= $data['pekerjaan_ibu'] == "Tidak Bekerja" ? 'selected' : ''; ?>>Tidak Bekerja</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Berkas (PDF)</label>
                    <input type="file" name="file" id="file" class="form-control" accept="application/pdf">
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="home_mahasiswa.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
// Format input penghasilan ke Rupiah
document.getElementById('penghasilan').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value ? 'Rp' + parseInt(value).toLocaleString('id-ID') : '';
});

// Fungsi untuk mengupdate field IPS berdasarkan semester yang dipilih
function updateIPSFields() {
    let semester = document.getElementById('semester').value;
    let container = document.getElementById('ips-container');
    container.innerHTML = '';

    if (semester) {
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

// Panggil fungsi updateIPSFields saat halaman dimuat
document.addEventListener('DOMContentLoaded', function () {
    updateIPSFields();
});
</script>

</body>
</html>