<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pendukung Keputusan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        .image-grid img {
            width: 100%;
            height: auto; /* Agar gambar tetap proporsional */
            max-height: 100vh; /* Pastikan gambar tidak lebih tinggi dari layar */
            object-fit: cover; /* Mengisi area tanpa distorsi */
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .image-grid {
            display: flex;
            flex-direction: column;
            align-items: center; /* Pusatkan gambar di tengah */
        }

        .image-grid .col-12 {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-primary bg-white topbar mb-4 static-top shadow">
        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <!-- Tulisan Selamat Datang -->
        <h4><marquee>Selamat Datang Di Sistem Pendukung Keputusan Seleksi Beasiswa Kab. Asahan</marquee></h4>
    </nav>
    <!-- End of Topbar -->

    <div class="container-fluid">
        <center>
            <h2 style="color:#2980b9; font-family:'arial black'">
                Menerapkan Sistem Pendukung Keputusan Menggunakan WPM Untuk Seleksi Penerima Beasiswa Pemerintahan Kabupaten Asahan
            </h2>
        </center>

        <!-- Grid Gambar -->
        <div class="container-fluid">
            <div class="row image-grid">
                <div class="col-12">
                    <img src="4.png" alt="4.png" class="img-fluid">
                </div>
                <div class="col-12">
                    <img src="5.png" alt="5.png" class="img-fluid">
                </div>
                <div class="col-12">
                    <img src="6.png" alt="6.png" class="img-fluid">
                </div>
                <div class="col-12">
                    <img src="7.png" alt="7.png" class="img-fluid">
                </div>
                <div class="col-12">
                    <img src="8.png" alt="8.png" class="img-fluid">
                </div>
                <div class="col-12">
                    <img src="9.png" alt="9.png" class="img-fluid">
                </div>
                <div class="col-12">
                    <img src="10.png" alt="10.png" class="img-fluid">
                </div>
                <div class="col-12">
                    <img src="11.png" alt="11.png" class="img-fluid">
                </div>
                <div class="col-12">
                    <img src="12.png" alt="12.png" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
