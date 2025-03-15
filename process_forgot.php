<?php
require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';
include 'koneksi.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Ambil email dari form
$email = $_POST['email'];

// Cek apakah email terdaftar
$cek = $koneksi->prepare("SELECT * FROM petugas WHERE email = ?");
$cek->bind_param("s", $email);
$cek->execute();
$result = $cek->get_result();
$user = $result->fetch_assoc();

// Tutup hasil sebelum lanjut ke query berikutnya
$cek->free_result();
$cek->close();


if ($user) {
    // Generate token reset password
    $token = bin2hex(random_bytes(16));

    $update = $koneksi->prepare("UPDATE petugas SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
    $update->bind_param("ss", $token, $email);
    $update->execute();

    // Link reset password
    $resetLink = "http://localhost/Beasiswa-wp/reset_password.php?token=$token";

    // Kirim email pakai PHPMailer


    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'melindaputri1006@gmail.com';
        $mail->Password = 'xcwc jbrb hzgd dexe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Pengirim & Penerima
        $mail->setFrom('melindaputri1006@gmail.com', 'Beasiswa Pemkab Asahan');
        $mail->addAddress($email);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password E-Surat';
        $mail->Body = "Klik link berikut untuk reset password: <a href='$resetLink'>$resetLink</a>";

        $mail->send();
        echo "Email reset password telah dikirim.";
    } catch (Exception $e) {
        echo "Gagal mengirim email: {$mail->ErrorInfo}";
    }
} else {
    echo "Email tidak terdaftar!";
}