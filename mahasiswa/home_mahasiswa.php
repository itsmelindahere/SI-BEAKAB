<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SPK-WP</title>

    <!-- Custom fonts -->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SI-BEAKAB</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link menu-link" href="#" data-page="dashboard_mahasiswa">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="#" data-page="profil_mahasiswa">
                    <i class="fas fa-user"></i>
                    <span>Data Diri</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link" href="#" data-page="persyaratan">
                    <i class="fas fa-file-alt"></i>
                    <span>Persyaratan</span>
                </a>
            </li>

              <li class="nav-item">
                <a class="nav-link menu-link" href="#" data-page="input_data">
                    <i class="fas fa-upload"></i>
                    <span>Upload Data</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="#" data-page="tampilan_data">
                <i class="fas fa-file-alt"></i>
                    <span>Lihat Data</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="true" aria-controls="collapseLaporan">
                    <i class="fas fa-file-alt"></i>
                    <span>Data Laporan</span>
                </a>
                <div id="collapseLaporan" class="collapse" aria-labelledby="headingLaporan" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a href="generate_pdf.php" target="_blank" class="btn btn-primary btn-sm d-block mb-2">
                            <i class="fas fa-file-pdf"></i> Cetak Integritas
                        </a>
                        <a class="collapse-item menu-link" href="#" data-page="laporan_pertanggungjawaban">LPJ</a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a class="nav-link" href="logout_mahasiswa.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </li>

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div id="content-area">
                        <?php include 'dashboard_mahasiswa.php'; ?>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; MAP</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah ini untuk keluar.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- AJAX untuk memuat konten -->
    <script>
    $(document).ready(function() {
        $('.menu-link').on('click', function(e) {
            e.preventDefault();
            var page = $(this).data('page');

            console.log("Memuat halaman: " + page + '.php');
            $('#content-area').html('<p class="text-center">Loading...</p>');

            $('#content-area').load(page + '.php', function(response, status, xhr) {
                if (status == "error") {
                    console.log("Error: " + xhr.status + " - " + xhr.statusText);
                }
            });

            $('.menu-link').removeClass('active');
            $(this).addClass('active');
        });
    });
    </script>

</body>

</html>
