<?php
include 'koneksi.php';

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data mahasiswa berdasarkan ID
    $query = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE id_mhs = $id");
    $data = mysqli_fetch_assoc($query);

    // Jika data tidak ditemukan, kembali ke halaman mahasiswa
    if (!$data) {
        echo "<script>alert('Data tidak ditemukan!'); window.location='mahasiswa.php';</script>";
        exit();
    }

    // Ambil data IPS jika ada (contoh: dari tabel ips_mahasiswa)
    $query_ips = mysqli_query($koneksi, "SELECT semester, ips FROM ips_mahasiswa WHERE id_mhs = $id ORDER BY semester");
    $ips_data = [];
    while ($row = mysqli_fetch_assoc($query_ips)) {
        $ips_data[$row['semester']] = $row['ips'];
    }
} else {
    echo "<script>alert('ID tidak ditemukan!'); window.location='mahasiswa.php';</script>";
    exit();
}

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $perguruan_tinggi = mysqli_real_escape_string($koneksi, $_POST['perguruan_tinggi']);
    $jenis_pt = mysqli_real_escape_string($koneksi, $_POST['jenis_pt']);

    // Hapus karakter non-numerik pada penghasilan sebelum diolah
    $penghasilan = floatval(str_replace(['Rp', '.', ','], '', $_POST['penghasilan']));

    $akreditasi = mysqli_real_escape_string($koneksi, $_POST['akreditasi']);
    $saudara = intval($_POST['saudara']);
    $sertifikat = mysqli_real_escape_string($koneksi, $_POST['sertifikat']);

    // Ambil data IPS dari form
    $ips_values = $_POST['ips']; // Array IPS dari form

    // Hitung IPK dari rata-rata IPS
    $total_ips = 0;
    $count_ips = 0;
    foreach ($ips_values as $ips) {
        $ips = floatval($ips);
        if ($ips >= 0 && $ips <= 4) { // Validasi nilai IPS
            $total_ips += $ips;
            $count_ips++;
        }
    }
    $ipk = ($count_ips > 0) ? ($total_ips / $count_ips) : 0;

    // **Konversi Bobot IPK (C1) berdasarkan tabel**
    if ($jenis_pt == "Negeri") {
        if ($ipk >= 4.0) $bobot_ipk = 5;
        elseif ($ipk >= 3.80) $bobot_ipk = 4;
        elseif ($ipk >= 3.70) $bobot_ipk = 3;
        elseif ($ipk >= 3.60) $bobot_ipk = 2;
        elseif ($ipk >= 3.25) $bobot_ipk = 1;
        else $bobot_ipk = 0; // Jika di bawah 3.25
    } elseif ($jenis_pt == "Swasta") {
        if ($ipk >= 4.0) $bobot_ipk = 5;
        elseif ($ipk >= 3.80) $bobot_ipk = 4;
        elseif ($ipk >= 3.70) $bobot_ipk = 3;
        elseif ($ipk >= 3.60) $bobot_ipk = 2;
        elseif ($ipk >= 3.50) $bobot_ipk = 1;
        else $bobot_ipk = 0; // Jika di bawah 3.50
    }

    // **Konversi Penghasilan Orang Tua (C2)**
    if ($penghasilan >= 5000000) $bobot_penghasilan = 5;
    elseif ($penghasilan >= 4000000) $bobot_penghasilan = 4;
    elseif ($penghasilan >= 3000000) $bobot_penghasilan = 3;
    elseif ($penghasilan >= 2000000) $bobot_penghasilan = 2;
    else $bobot_penghasilan = 1;

    // **Konversi Akreditasi Perguruan Tinggi (C3)**
    $bobot_akreditasi = 0;
    if ($jenis_pt == "Negeri") {
        if ($akreditasi == "Unggul") $bobot_akreditasi = 5;
        elseif ($akreditasi == "Baik Sekali") $bobot_akreditasi = 4;
        elseif ($akreditasi == "Baik") $bobot_akreditasi = 3;
    } else { // Swasta
        if ($akreditasi == "Unggul") $bobot_akreditasi = 5;
        elseif ($akreditasi == "Baik Sekali") $bobot_akreditasi = 3;
        elseif ($akreditasi == "Baik") $bobot_akreditasi = 2;
    }

    // **Konversi Jumlah Saudara (C4)**
    if ($saudara >= 5) $bobot_saudara = 5;
    elseif ($saudara == 4) $bobot_saudara = 4;
    elseif ($saudara == 3) $bobot_saudara = 3;
    elseif ($saudara == 2) $bobot_saudara = 2;
    else $bobot_saudara = 1;

    // **Konversi Sertifikat Kompetensi (C5)**
    $bobot_sertifikat = ($sertifikat == "Ada") ? 5 : 3;

    // Mulai Transaksi Database
    $koneksi->begin_transaction();

    try {
        // Update data mahasiswa
        $updateQuery = "UPDATE mahasiswa SET 
                        nama = '$nama', 
                        email = '$email', 
                        ipk = '$ipk', 
                        perguruan_tinggi = '$perguruan_tinggi', 
                        jenis_pt = '$jenis_pt', 
                        penghasilan = '$penghasilan', 
                        akreditasi = '$akreditasi', 
                        saudara = '$saudara', 
                        sertifikat = '$sertifikat' 
                        WHERE id_mhs = $id";

        if (!mysqli_query($koneksi, $updateQuery)) {
            throw new Exception("Gagal memperbarui data mahasiswa: " . mysqli_error($koneksi));
        }

        // Hapus data IPS lama
        $query_delete_ips = "DELETE FROM ips_mahasiswa WHERE id_mhs = $id";
        if (!mysqli_query($koneksi, $query_delete_ips)) {
            throw new Exception("Gagal menghapus data IPS lama: " . mysqli_error($koneksi));
        }

        // Simpan data IPS baru
        foreach ($ips_values as $semester => $ips) {
            $semester = intval($semester);
            $ips = floatval($ips);

            $query_insert_ips = "INSERT INTO ips_mahasiswa (id_mhs, semester, ips) VALUES ($id, $semester, $ips)";
            if (!mysqli_query($koneksi, $query_insert_ips)) {
                throw new Exception("Gagal menyimpan data IPS: " . mysqli_error($koneksi));
            }
        }

        // **Update Data di Tabel Alternatif**
        $updateAlternatifQuery = "UPDATE alternatif SET 
                                  alternatif = '$nama', 
                                  C1 = '$bobot_ipk', 
                                  C2 = '$bobot_penghasilan', 
                                  C3 = '$bobot_akreditasi', 
                                  C4 = '$bobot_saudara', 
                                  C5 = '$bobot_sertifikat' 
                                  WHERE alternatif = (SELECT nama FROM mahasiswa WHERE id_mhs = $id)";

        if (!mysqli_query($koneksi, $updateAlternatifQuery)) {
            throw new Exception("Gagal memperbarui data alternatif: " . mysqli_error($koneksi));
        }

        // Commit Transaksi
        $koneksi->commit();

        echo "<script>alert('Data berhasil diperbarui!'); window.location='home.php';</script>";
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $koneksi->rollback();
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Edit Mahasiswa</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="<?= $data['nama']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $data['email']; ?>" required>
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
                    <input type="text" name="perguruan_tinggi" class="form-control" value="<?= $data['perguruan_tinggi']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis PT</label>
                    <select name="jenis_pt" class="form-control" required>
                        <option value="Negeri" <?= ($data['jenis_pt'] == 'Negeri') ? 'selected' : ''; ?>>Negeri</option>
                        <option value="Swasta" <?= ($data['jenis_pt'] == 'Swasta') ? 'selected' : ''; ?>>Swasta</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Penghasilan Orang Tua (Rp)</label>
                    <input type="number" name="penghasilan" class="form-control" min="0" step="1000" value="<?= $data['penghasilan']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Akreditasi Perguruan Tinggi</label>
                    <select name="akreditasi" class="form-control" required>
                        <option value="Unggul" <?= ($data['akreditasi'] == 'Unggul') ? 'selected' : ''; ?>>Unggul</option>
                        <option value="Baik Sekali" <?= ($data['akreditasi'] == 'Baik Sekali') ? 'selected' : ''; ?>>Baik Sekali</option>
                        <option value="Baik" <?= ($data['akreditasi'] == 'Baik') ? 'selected' : ''; ?>>Baik</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Saudara</label>
                    <input type="number" name="saudara" class="form-control" value="<?= $data['saudara']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sertifikat</label>
                    <select name="sertifikat" class="form-control" required>
                        <option value="Ada" <?= ($data['sertifikat'] == 'Ada') ? 'selected' : ''; ?>>Ada</option>
                        <option value="Tidak Ada" <?= ($data['sertifikat'] == 'Tidak Ada') ? 'selected' : ''; ?>>Tidak Ada</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Berkas (PDF)</label>
                    <input type="file" name="file" id="file" class="form-control" accept="application/pdf">
                </div>
                <button type="submit" class="btn btn-warning">Update</button>
                <a href="mahasiswa.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<script>
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