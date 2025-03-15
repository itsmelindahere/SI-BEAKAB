<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Login Sistem</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
  <div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
      <div class="col-xl-6 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center">
                    <marquee behavior="scroll" direction="left">
                      <h1 class="h5 text-gray-900 mb-4">Sistem Pendukung Keputusan Seleksi Penerima Beasiswa Kab Asahan</h1>
                    </marquee>
                  </div>

                  <!-- Form Login -->
                  <form class="user" method="post" action="cek_login.php">
                    <div class="form-group">
                      <input type="email" name="email" class="form-control form-control-user" placeholder="Masukkan Email Anda..." required>
                    </div>
                    
                    <div class="form-group">
                      <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control form-control-user" placeholder="Password" required>
                        <div class="input-group-append">
                          <button type="button" class="btn btn-light border" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="role">Masuk Sebagai:</label>
                      <select name="role" id="role" class="form-control" required>
                        <option value="" disabled selected>Pilih Peran</option>
                        <option value="admin">Admin</option>
                        <option value="mahasiswa">Mahasiswa</option>
                      </select>
                    </div>
                    
                    <input type="submit" class="btn btn-primary btn-user btn-block" value="Login!">
                    <hr>

                    <div class="text-center">
                      <p class="mb-1">Belum punya akun?</p>
                      <a href="register.php" class="text-primary">Daftar sekarang</a>
                    </div>
                    <div class="text-center">
                      <a href="lupa_password.php" class="text-danger">Lupa password?</a>
                    </div>
                  </form>

                  <!-- Pesan error jika login gagal -->
                  <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger text-center mt-3">
                      <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Script untuk Toggle Password -->
  <script>
    function togglePassword() {
      var passwordField = document.getElementById("password");
      var eyeIcon = document.getElementById("eyeIcon");
      
      if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
      } else {
        passwordField.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
      }
    }
  </script>

</body>
</html>
