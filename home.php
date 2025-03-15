<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Jika bukan admin, kembali ke login
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

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="background: primary;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SPK-BEA</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php" data-page="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="penjelasan.php" data-page="penjelasan">
                    <i class="fas fa-edit"></i>
                    <span>Penjelasan</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Data
            </div>

            <!-- Nav Item - Data -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseData" aria-expanded="true" aria-controls="collapseData" id="dataMenu">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Data</span>
                </a>
                <div id="collapseData" class="collapse" aria-labelledby="headingData" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="mahasiswa.php" data-page="mahasiswa">
                            <i class="fas fa-users"></i>
                            <span>Data Mahasiswa</span>
                        </a>
                        <a class="collapse-item" href="kriteria.php" data-page="kriteria">
                            <i class="fas fa-edit"></i>
                            <span>Data Kriteria</span>
                        </a>
                        <a class="collapse-item" href="alternatif.php" data-page="alternatif">
                            <i class="fas fa-search"></i>
                            <span>Data Alternatif</span>
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Hasil Perhitungan -->
            <li class="nav-item">
                <a class="nav-link" href="perhitungan.php" data-page="perhitungan">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Hasil Perhitungan</span>
                </a>
                
            </li>

            <!-- Nav Item - Data Perankingan -->
            <li class="nav-item">
                <a class="nav-link" href="perankingan.php" data-page="perankingan">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Data Perankingan</span>
                </a>
            </li>

            <!-- Nav Item - Cetak Laporan -->
            <li class="nav-item">
                <a class="nav-link" href="analisa.php" data-page="analisa">
                    <i class="fas fa-fw fa-print"></i>
                    <span>Cetak Laporan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseData" aria-expanded="true" aria-controls="collapseData" id="dataMenu">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Laporan Data LPJ</span>
                </a>
                <div id="collapseData" class="collapse" aria-labelledby="headingData" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="rekapitulasi_lpj.php" data-page="rekapitulasi_lpj">
                            <i class="fas fa-users"></i>
                            <span>Rekapitulasi LPJ</span>
                            </a>
                    </div>
                </div>
                </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Nav Item - Keluar -->
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </li>

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Begin Page Content -->
                <div class="container-fluid" id="content">
                    <?php include 'dashboard.php'; ?>
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

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
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

    <!-- AJAX untuk memuat konten -->
    <script>
        $(document).ready(function() {
            // Pulihkan status collapse dari localStorage
            var collapseData = localStorage.getItem('collapseData');
            if (collapseData === 'show') {
                $('#collapseData').addClass('show');
                $('#dataMenu').removeClass('collapsed');
            }

            // Simpan status collapse ke localStorage saat di-klik
            $('#collapseData').on('show.bs.collapse', function() {
                localStorage.setItem('collapseData', 'show');
            });

            $('#collapseData').on('hide.bs.collapse', function() {
                localStorage.setItem('collapseData', 'hide');
            });

            // Handle klik pada menu
            $('.nav-link, .collapse-item').on('click', function(e) {
                var page = $(this).data('page');
                var href = $(this).attr('href');

                // Cek jika tombol yang diklik adalah Logout
                if (href.includes("logout.php")) {
                    return true; // Izinkan logout berjalan normal
                }

                if (page) {
                    e.preventDefault();
                    console.log("Memuat halaman: " + page + '.php');
                    $('#content').load(page + '.php', function(response, status, xhr) {
                        if (status == "error") {
                            console.log("Error: " + xhr.status + " - " + xhr.statusText);
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>