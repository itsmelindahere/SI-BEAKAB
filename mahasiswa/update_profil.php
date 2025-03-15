<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi ke database

// Inisialisasi variabel untuk pesan kesalahan
$error_message = '';
$success_message = '';

// Periksa apakah form telah dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $perguruan_tinggi = mysqli_real_escape_string($koneksi, $_POST['perguruan_tinggi']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $nomor_wa = mysqli_real_escape_string($koneksi, $_POST['nomor_wa']);

    // Handle file upload (foto profil)
    $foto_profil = $_POST['foto_profil_lama']; // Default ke foto profil lama
    if ($_FILES['avatar']['error'] == 0) {
        $target_dir = "uploads/"; // Direktori untuk menyimpan file
        $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Periksa apakah file adalah gambar
        $check = getimagesize($_FILES["avatar"]["tmp_name"]);
        if ($check === false) {
            $error_message = "File yang diunggah bukan gambar.";
        }

        // Periksa ukuran file (misalnya, maksimal 5MB)
        if ($_FILES["avatar"]["size"] > 5000000) {
            $error_message = "Maaf, file terlalu besar. Maksimal ukuran file adalah 5MB.";
        }

        // Periksa format file
        if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            $error_message = "Maaf, hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        }

        // Jika tidak ada error, coba pindahkan file
        if (empty($error_message)) {
            if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                $error_message = "Maaf, terjadi kesalahan saat mengunggah file.";
            } else {
                $foto_profil = $target_file; // Update path foto profil
            }
        }
    }

    // Jika tidak ada error, lakukan update atau insert data
    if (empty($error_message)) {
        // Cek apakah data sudah ada di database
        $cek_query = "SELECT * FROM profil_mhs WHERE email = '$email'";
        $cek_result = mysqli_query($koneksi, $cek_query);

        if (mysqli_num_rows($cek_result) > 0) {
            // Data sudah ada, lakukan UPDATE
            $query_profil_mhs = "UPDATE profil_mhs SET 
                                 nama = '$nama', 
                                 tempat_lahir = '$tempat_lahir', 
                                 tanggal_lahir = '$tanggal_lahir', 
                                 nim = '$nim', 
                                 perguruan_tinggi = '$perguruan_tinggi', 
                                 alamat = '$alamat', 
                                 nomor_wa = '$nomor_wa', 
                                 foto_profil = '$foto_profil' 
                                 WHERE email = '$email'";
        } else {
            // Data belum ada, lakukan INSERT
            $query_profil_mhs = "INSERT INTO profil_mhs (email, nama, tempat_lahir, tanggal_lahir, nim, perguruan_tinggi, alamat, nomor_wa, foto_profil) 
                                 VALUES ('$email', '$nama', '$tempat_lahir', '$tanggal_lahir', '$nim', '$perguruan_tinggi', '$alamat', '$nomor_wa', '$foto_profil')";
        }

        if (mysqli_query($koneksi, $query_profil_mhs)) {
            // Update tabel petugas (nama dan email)
            $query_petugas = "UPDATE petugas SET 
                              nama = '$nama', 
                              email = '$email' 
                              WHERE email = '$email'";

            if (mysqli_query($koneksi, $query_petugas)) {
                // Jika berhasil, set pesan sukses
                $success_message = "Data berhasil diperbarui!";
            } else {
                $error_message = "Terjadi kesalahan saat memperbarui tabel petugas: " . mysqli_error($koneksi);
            }
        } else {
            $error_message = "Terjadi kesalahan saat memperbarui tabel profil_mhs: " . mysqli_error($koneksi);
        }
    }
}

// Jika ada error, tampilkan notifikasi error dan redirect kembali ke halaman profil
if (!empty($error_message)) {
    echo "<script>alert('$error_message'); window.location.href = 'profil_mahasiswa.php';</script>";
    exit;
}

// Jika update berhasil, tampilkan notifikasi sukses dan redirect kembali
if (!empty($success_message)) {
    echo "<script>alert('$success_message'); window.location.href = 'home_mahasiswa.php';</script>";
    exit;
}
?>
