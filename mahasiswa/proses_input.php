<?php
session_start();
include '../koneksi.php';

// Pastikan pengguna sudah login sebagai mahasiswa
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'mahasiswa') {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php';</script>";
    exit;
}

// Ambil email mahasiswa dari session
$email_mahasiswa = $_SESSION['email'];

// Ambil nama mahasiswa dari tabel petugas
$query_petugas = $koneksi->prepare("SELECT nama FROM petugas WHERE email = ?");
$query_petugas->bind_param("s", $email_mahasiswa);
$query_petugas->execute();
$result_petugas = $query_petugas->get_result();
$query_petugas->close();

if ($result_petugas->num_rows == 0) {
    echo "<script>alert('Data petugas tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$row = $result_petugas->fetch_assoc();
$nama_mahasiswa = $row['nama']; // Nama mahasiswa yang terdaftar di petugas

// Periksa apakah mahasiswa sudah terdaftar
$query_mahasiswa = $koneksi->prepare("SELECT id_mhs FROM mahasiswa WHERE nama = ?");
$query_mahasiswa->bind_param("s", $nama_mahasiswa);
$query_mahasiswa->execute();
$query_mahasiswa->store_result();

if ($query_mahasiswa->num_rows > 0) {
    echo "<script>alert('Data sudah terdaftar! Silakan edit data yang sudah ada.'); window.location='edit_mahasiswa.php?nama=$nama_mahasiswa';</script>";
    exit;
}
$query_mahasiswa->close();

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $semester = intval($_POST['semester']);
    $ips_values = $_POST['ips']; // Array IPS dari form
    $ipk = floatval(str_replace(',', '.', $_POST['ipk'])); // IPK yang sudah dihitung otomatis
    $perguruan_tinggi = mysqli_real_escape_string($koneksi, $_POST['perguruan_tinggi']);
    $jenis_pt = mysqli_real_escape_string($koneksi, $_POST['jenis_pt']);
    $penghasilan = intval(str_replace(['Rp', '.', ','], '', $_POST['penghasilan']));
    $akreditasi = mysqli_real_escape_string($koneksi, $_POST['akreditasi']);
    $saudara = intval($_POST['saudara']);
    $sertifikat = mysqli_real_escape_string($koneksi, $_POST['sertifikat']);
    $pekerjaan_ayah = mysqli_real_escape_string($koneksi, $_POST['pekerjaan_ayah']);
    $pekerjaan_ibu = mysqli_real_escape_string($koneksi, $_POST['pekerjaan_ibu']);
    $status_input = 1;

    // Proses Upload File
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_new_name = uniqid() . "." . $file_ext;
        $file_path = $upload_dir . $file_new_name;

        // Validasi hanya PDF dengan ukuran maks 2MB
        if ($file_ext != 'pdf' || $file_size > 2 * 1024 * 1024) {
            echo "<script>alert('File harus berupa PDF dan maksimal 2MB!'); window.location='input_data.php';</script>";
            exit;
        }

        if (!move_uploaded_file($file_tmp, $file_path)) {
            echo "<script>alert('Gagal mengunggah file!'); window.location='input_data.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('File tidak ditemukan atau terjadi kesalahan!'); window.location='input_data.php';</script>";
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
        $stmt = $koneksi->prepare("INSERT INTO mahasiswa (nama, ipk, perguruan_tinggi, jenis_pt, penghasilan, akreditasi, saudara, sertifikat, pekerjaan_ayah, pekerjaan_ibu, file, status_input) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssdsissssi", $nama_mahasiswa, $ipk, $perguruan_tinggi, $jenis_pt, $penghasilan, $akreditasi, $saudara, $sertifikat, $pekerjaan_ayah, $pekerjaan_ibu, $file_new_name, $status_input);

        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data mahasiswa: " . $stmt->error);
        }
        $stmt->close();

        // Insert ke tabel alternatif (untuk perhitungan bobot)
        $stmt_alt = $koneksi->prepare("INSERT INTO alternatif (alternatif, C1, C2, C3, C4, C5) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_alt->bind_param("siiiii", $nama_mahasiswa, $bobot_ipk, $bobot_penghasilan, $bobot_akreditasi, $bobot_saudara, $bobot_sertifikat);

        if (!$stmt_alt->execute()) {
            throw new Exception("Gagal menyimpan data alternatif: " . $stmt_alt->error);
        }
        $stmt_alt->close();

        // Commit Transaksi
        $koneksi->commit();

        echo "<script>alert('Data mahasiswa berhasil ditambahkan!'); window.location='tampilan_data.php';</script>";
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $koneksi->rollback();
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location='input_data.php';</script>";
    }
}
?>