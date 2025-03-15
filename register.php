<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Register</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-6 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h5 text-gray-900 mb-4">
                      <marquee>Registrasi Akun Sistem Pendukung Keputusan Seleksi Penerima Beasiswa Kab Asahan</marquee>
                    </h1>
                  </div>
                  <form class="user" method="post" action="simpan.php"> 
                    <div class="form-group">
                      <input type="text" name="nama" class="form-control form-control-user" placeholder="Masukkan Nama sesuai KTP" required>
                    </div>
                    <div class="form-group">
                      <input type="email" name="email" class="form-control form-control-user" placeholder="Email" required>
                    </div>
                   
                    <div class="form-group">
                      <input type="password" name="password" class="form-control form-control-user" placeholder="Password" required>
                    </div>
    
                    <input type="submit" class="btn btn-primary btn-user btn-block" value="Daftar!">
                    <hr>
                    <div class="text-center">
                      <p class="mb-1">Sudah punya akun?</p>
                      <a href="index.php" class="text-primary">Silahkan Login</a>
                    </div>
                  </form>

                  <?php
                  if (isset($_GET['error'])) {
                      echo "<p class='text-danger text-center mt-2'>{$_GET['error']}</p>";
                  }
                  if (isset($_GET['success'])) {
                      echo "<p class='text-success text-center mt-2'>{$_GET['success']}</p>";
                  }
                  ?>
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
</body>
</html>