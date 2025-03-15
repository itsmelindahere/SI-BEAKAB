<?php
include 'koneksi.php';
$id = $_POST['id'];
$query = mysqli_query($conn, "SELECT * FROM kriteria WHERE id_kriteria = '$id'");
$data = mysqli_fetch_assoc($query);
echo json_encode($data);
?>
