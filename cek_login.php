<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi database sudah benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']); // Ambil role dari form login

    // Ambil data user dari database berdasarkan email dan role
    $query = "SELECT id, email, password, role FROM petugas WHERE email = ? AND role = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $role = $row['role'];

        // Cek apakah password sudah di-hash atau tidak
        if ($role == 'admin') {
            // Admin: Password tidak di-hash → bandingkan langsung
            if (password_verify($password, $hashed_password)) {
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                header("Location: home.php"); // Ganti dengan halaman setelah login
                exit();
            } else {
                // Password salah
                echo "<script>alert('Password salah!'); window.location='index.php';</script>";
                exit();
            }
        } else {
            // Mahasiswa: Password di-hash → gunakan password_verify()
            if (password_verify($password, $hashed_password)) {
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                header("Location: mahasiswa/home_mahasiswa.php"); // Ganti dengan halaman setelah login
                exit();
            } else {
                // Password salah
                echo "<script>alert('Password salah!'); window.location='index.php';</script>";
                exit();
            }
        }
    } else {
        // Email belum terdaftar
        echo "<script>alert('Email belum terdaftar. Silahkan daftar terlebih dahulu.'); window.location='index.php';</script>";
        exit();
    }
}
?>