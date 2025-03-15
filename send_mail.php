<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Pakai Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'melindaputri1006@gmail.com'; // Ganti dengan email kamu
    $mail->Password = 'xcwc jbrb hzgd dexe'; // Ganti dengan password aplikasi Gmail kamu
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Pengirim & Penerima
    $mail->setFrom('melindaputri1006@gmail.com', 'Melinda');
    $mail->addAddress('arigandap@gmail.com', 'Penerima'); // Ganti dengan email tujuan

    // Konten email
    $mail->isHTML(true);
    $mail->Subject = 'Coba Kirim Email dengan PHPMailer';
    $mail->Body    = '<h3>Hello!</h3><p>Ini email pertama kamu pakai PHPMailer 🎉</p>';

    // Kirim email
    $mail->send();
    echo 'Email berhasil dikirim!';
} catch (Exception $e) {
    echo "Gagal mengirim email. Error: {$mail->ErrorInfo}";
}
?>