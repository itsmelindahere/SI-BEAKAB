<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email'];
    $old_password = md5($_POST['old_password']);
    $new_password = md5($_POST['new_password']);

    $query = "SELECT * FROM mahasiswa WHERE email='$email' AND password='$old_password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $update_query = "UPDATE mahasiswa SET password='$new_password' WHERE email='$email'";
        if ($conn->query($update_query) === TRUE) {
            echo "Password berhasil diubah.";
        } else {
            echo "Gagal mengubah password.";
        }
    } else {
        echo "Password lama salah.";
    }
}
?>
