<?php
include 'koneksi.php';

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
    $pekerjaan_ayah = mysqli_real_escape_string($koneksi, $_POST['pekerjaan_ayah']);
    $pekerjaan_ibu = mysqli_real_escape_string($koneksi, $_POST['pekerjaan_ibu']);

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

    // Perbaikan Proses Upload File
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Pastikan direktori uploads ada
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_new_name = uniqid() . "." . $file_ext;
        $file_path = $upload_dir . $file_new_name;

        // Validasi hanya menerima PDF dengan ukuran maks 2MB
        if ($file_ext != 'pdf') {
            echo "<script>alert('File harus berupa PDF!'); window.location='home.php';</script>";
            exit;
        }

        if ($file_size > 2 * 1024 * 1024) {
            echo "<script>alert('Ukuran file maksimal 2MB!'); window.location='home.php';</script>";
            exit;
        }

        if (!move_uploaded_file($file_tmp, $file_path)) {
            echo "<script>alert('Gagal mengunggah file!'); window.location='home.php';</script>";
            exit;
        }

        // Perubahan di sini: Nama file yang disimpan di database diperbarui menjadi nama baru
        $file_name = $file_new_name;
    } else {
        echo "<script>alert('File tidak ditemukan atau terjadi kesalahan saat mengunggah!'); window.location='home.php';</script>";
        exit;
    }

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
        // Insert ke tabel mahasiswa
        $query = "INSERT INTO mahasiswa (nama, email ipk, perguruan_tinggi, jenis_pt, penghasilan, akreditasi, saudara, sertifikat, pekerjaan_ayah, pekerjaan_ibu, file) 
                  VALUES ('$nama', '$email',  '$ipk', '$perguruan_tinggi', '$jenis_pt', '$penghasilan', '$akreditasi', '$saudara', '$sertifikat', '$pekerjaan_ayah', '$pekerjaan_ibu', '$file_name')";

        if (!mysqli_query($koneksi, $query)) {
            throw new Exception("Gagal menyimpan data mahasiswa: " . mysqli_error($koneksi));
        }

        // Ambil ID mahasiswa yang baru saja ditambahkan
        $mahasiswa_id = mysqli_insert_id($koneksi);

        // Simpan data IPS ke tabel ips_mahasiswa
        foreach ($ips_values as $semester => $ips) {
            $semester = intval($semester);
            $ips = floatval($ips);

            $query_insert_ips = "INSERT INTO ips_mahasiswa (id_mhs, semester, ips) VALUES ($mahasiswa_id, $semester, $ips)";
            if (!mysqli_query($koneksi, $query_insert_ips)) {
                throw new Exception("Gagal menyimpan data IPS: " . mysqli_error($koneksi));
            }
        }

        // Insert ke tabel alternatif sesuai bobot
        $query_alternatif = "INSERT INTO alternatif (alternatif, C1, C2, C3, C4, C5) 
                             VALUES ('$nama', '$bobot_ipk', '$bobot_penghasilan', '$bobot_akreditasi', '$bobot_saudara', '$bobot_sertifikat')";
        if (!mysqli_query($koneksi, $query_alternatif)) {
            throw new Exception("Gagal menyimpan data alternatif: " . mysqli_error($koneksi));
        }

        // Commit Transaksi
        $koneksi->commit();

        echo "<script>alert('Mahasiswa berhasil ditambahkan dan dikonversi ke alternatif!'); window.location='home.php';</script>";
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $koneksi->rollback();
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location='tambah_mahasiswa.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Tambah Mahasiswa</h4>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
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
                    <input type="text" name="perguruan_tinggi" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis PT</label>
                    <select name="jenis_pt" class="form-control" required>
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
                    <select name="akreditasi" class="form-control" required>
                        <option value="Unggul">Unggul</option>
                        <option value="Baik Sekali">Baik Sekali</option>
                        <option value="Baik">Baik</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Saudara</label>
                    <input type="number" name="saudara" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sertifikat Kompetensi</label>
                    <select name="sertifikat" class="form-control" required>
                        <option value="Ada">Ada</option>
                        <option value="Tidak Ada">Tidak Ada</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pekerjaan Ayah</label>
                    <select name="pekerjaan_ayah" class="form-control" required>
                        <option value="PNS">PNS</option>
                        <option value="Karyawan BUMN">Karyawan BUMN</option>
                        <option value="Karyawan Swasta">Karyawan Swasta</option>
                        <option value="Wiraswasta">Wiraswasta</option>
                        <option value="Mengurus Rumah Tangga">Mengurus Rumah Tangga</option>
                        <option value="Tidak Bekerja">Tidak Bekerja</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pekerjaan Ibu</label>
                    <select name="pekerjaan_ibu" class="form-control" required>
                        <option value="PNS">PNS</option>
                        <option value="Karyawan BUMN">Karyawan BUMN</option>
                        <option value="Wiraswasta">Wiraswasta</option>
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
                <a href="home.php" class="btn btn-secondary">Batal</a>
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
    document.getElementById('email').addEventListener('input', function (e) {
        let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(e.target.value)) {
            e.target.setCustomValidity("Format email tidak valid!");
        } else {
            e.target.setCustomValidity("");
        }
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