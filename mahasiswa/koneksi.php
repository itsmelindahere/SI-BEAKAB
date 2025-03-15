<?php 
$host = "localhost";
$user = "root";
$password = "";
$database = "beasiswa";

// Koneksi ke database
$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

?>
