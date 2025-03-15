<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$query = "SELECT * FROM profil_mhs WHERE email='$email'";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa</title>
    <style>
        /* Styling untuk kontainer utama */
        .content {
            width: 50%;
            margin: 40px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            font-family: 'Nunito', sans-serif;
        }

        /* Header profil */
        .profile-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        /* Styling untuk avatar */
        .avatar {
            text-align: center;
            margin-bottom: 20px;
        }

        .avatar img {
            width: 150px; /* Sesuaikan lebar agar mendekati 4x6 */
            height: 200px; /* Sesuaikan tinggi agar mendekati 4x6 */
            object-fit: cover; /* Pastikan gambar tetap proporsional */
            border-radius: 0; /* Pastikan gambar tetap persegi */
            border: 3px solid #3498db; /* Tambahkan border */
        }


        .avatar input {
            display: none;
        }

        /* Styling label input */
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
            color: #34495e;
        }

        /* Styling untuk semua input dan textarea */
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }

        /* Efek input saat focus */
        input:focus, textarea:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
        }

        /* Styling untuk tombol */
        button {
            width: 100%;
            padding: 12px;
            border: none;
            background: #3498db;
            color: white;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
        }

        /* Efek hover pada tombol */
        button:hover {
            background: #2980b9;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {
            .content {
                width: 90%;
            }
        }

        /* Styling untuk form-group */
        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="profile-header">
            <h1>Profil Mahasiswa</h1>
        </div>

        <form action="update_profil.php" method="post" enctype="multipart/form-data">
            <div class="avatar">
                <label for="avatar">
                    <img src="<?= $row['foto_profil'] ?? 'default_avatar.png' ?>" alt="Foto Profil" id="avatar-preview">
                </label>
                <input type="file" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(event)">
                <!-- Input tersembunyi untuk menyimpan path foto profil lama -->
                <input type="hidden" name="foto_profil_lama" value="<?= $row['foto_profil'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" value="<?= $row['nama'] ?? '' ?>" placeholder="Masukkan Nama Lengkap" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= $row['email'] ?? '' ?>" placeholder="Masukkan Email" required>
            </div>

            <div class="form-group">
                <label for="tempat_lahir">Tempat Lahir:</label>
                <input type="text" id="tempat_lahir" name="tempat_lahir" value="<?= $row['tempat_lahir'] ?? '' ?>" placeholder="Masukkan Tempat Lahir" required>
            </div>

            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= $row['tanggal_lahir'] ?? '' ?>" placeholder="Masukkan Tanggal Lahir" required>
            </div>

            <div class="form-group">
                <label for="nim">NIM:</label>
                <input type="text" id="nim" name="nim" value="<?= $row['nim'] ?? '' ?>" placeholder="Masukkan NIM" required>
            </div>

            <div class="form-group">
                <label for="perguruan_tinggi">Perguruan Tinggi:</label>
                <input type="text" id="perguruan_tinggi" name="perguruan_tinggi" value="<?= $row['perguruan_tinggi'] ?? '' ?>" placeholder="Masukkan Perguruan Tinggi" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" placeholder="Masukkan Alamat" required><?= $row['alamat'] ?? '' ?></textarea>
            </div>

            <div class="form-group">
                <label for="nomor_wa">Nomor WA:</label>
                <input type="text" id="nomor_wa" name="nomor_wa" value="<?= $row['nomor_wa'] ?? '' ?>" placeholder="Masukkan Nomor WA" required>
            </div>

            <button type="submit">Update</button>
        </form>
              <!-- Tombol Back di luar form -->
              <button class="back-button" onclick="window.location.href='home_mahasiswa.php'">Back</button>
    </div>

    <script>
        // Fungsi untuk preview avatar
        function previewAvatar(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('avatar-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // Fungsi untuk mengubah input menjadi huruf kapital
        function toUpperCaseInput(event) {
            if (event.target.id !== 'email') { // Kecualikan kolom email
                event.target.value = event.target.value.toUpperCase();
            }
        }

        // Menambahkan event listener ke semua input dan textarea
        document.querySelectorAll('input, textarea').forEach(function(element) {
            element.addEventListener('input', toUpperCaseInput);
        });
    </script>
</body>
</html>