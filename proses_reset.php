<?php
include 'koneksi.php';

// Ambil data dari form
$token = $_POST['token'] ?? null;
$password = $_POST['password'] ?? null;

// Validasi input
if (empty($token) || empty($password)) {
    die('Token atau password tidak boleh kosong!');
}

// Hash password baru
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Periksa apakah token valid dan belum kadaluarsa
$query = $koneksi->prepare("SELECT * FROM petugas WHERE reset_token = ? AND reset_token_expiry > NOW()");
$query->bind_param('s', $token);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die('Token tidak valid atau sudah kadaluarsa!');
}

// Update password dan reset token
$update = $koneksi->prepare("UPDATE petugas SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
$update->bind_param('ss', $hashed_password, $token);

if ($update->execute()) {
    echo "Password berhasil direset! Silakan login dengan password baru.<br>index.php<br>";
} else {
    echo "Terjadi kesalahan saat mereset password. Coba lagi nanti.";
}

$update->close();
$query->close();
$koneksi->close();