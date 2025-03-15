<?php
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php'; // Pastikan koneksi ke database benar

// Debugging: Cek apakah $koneksi terdefinisi
if (!isset($koneksi)) {
    die("Koneksi database tidak terdefinisi. Pastikan file koneksi.php benar.");
    // Debugging: Cek apakah koneksi berhasil
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'mahasiswa'; // Role default

    // Cek apakah semua input terisi
    if (empty($nama) || empty($email) || empty($password)) {
        echo "<script>alert('Semua kolom wajib diisi!'); window.location='register.php';</script>";
        exit();
    }

    // Cek apakah email sudah digunakan
    $cek_email = "SELECT id FROM petugas WHERE email = ?";
    $stmt = $koneksi->prepare($cek_email);
    if (!$stmt) {
        die("Error prepare statement: " . $koneksi->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan!'); window.location='register.php';</script>";
        exit();
    }
    $stmt->close();


    // Hash password dengan bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Simpan data ke database
    $query = "INSERT INTO petugas (nama, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    if (!$stmt) {
        die("Error prepare statement: " . $koneksi->error);
    }
    $stmt->bind_param("ssss", $nama, $email,  $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Registrasi gagal! Error: " . $stmt->error . "'); window.location='register.php';</script>";
    }

    $stmt->close();
    $koneksi->close();
} else {
    header("Location: register.php");
    exit();
}
?>