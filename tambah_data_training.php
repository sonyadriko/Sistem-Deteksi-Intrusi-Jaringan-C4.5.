<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- loader-->
    <link href="assets/css/pace.min.css" rel="stylesheet" />
    <script src="assets/js/pace.min.js"></script>

    <!--plugins-->
    <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />

    <!-- CSS Files -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!--Theme Styles-->
    <link href="assets/css/dark-theme.css" rel="stylesheet" />
    <link href="assets/css/semi-dark.css" rel="stylesheet" />
    <link href="assets/css/header-colors.css" rel="stylesheet" />

    <title>Data Training</title>
</head>

<body>


    <!--start wrapper-->
    <div class="wrapper">

        <!--start sidebar -->
        <?php include 'sidebar.php' ?>
        <!--end sidebar -->

        <!--start top header-->
        <?php include 'header.php' ?>
        <!--end top header-->


        <!-- start page content wrapper-->
        <div class="page-content-wrapper">
            <!-- start page content-->
            <div class="page-content">
                <div class="card">
                    <div class="card-body">
                        <div class="border p-3 rounded">
                            <h6 class="mb-0 text-uppercase">Tambah Data Training</h6>
                            <hr>
                            <form action="tambah_data_training.php" method="post" class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">service</label>
                                    <select class="form-select" name="service">
                                        <option value="http">HTTP</option>
                                        <option value="dns">DNS</option>
                                        <option value="ftp">FTP</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">spkts</label>
                                    <input type="number" name="spkts" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">sbytes</label>
                                    <input type="number" name="sbytes" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">sttl</label>
                                    <input type="number" name="sttl" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">smean</label>
                                    <input type="number" name="smean" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">attack cat</label>
                                    <select class="form-select" name="attack_cat">
                                        <option value="exploits">Exploits</option>
                                        <option value="generic">Generic</option>
                                        <option value="normal">Normal</option>
                                        <option value="fuzzers">Fuzzers</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!--end row-->


                <!--end row-->



            </div>
            <!-- end page content-->
        </div>
        <!--end page content wrapper-->


        <!--start footer-->

        <!--end footer-->


        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top">
            <ion-icon name="arrow-up-outline"></ion-icon>
        </a>
        <!--End Back To Top Button-->

        <!--start switcher-->

        <!--end switcher-->


        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->

    </div>
    <!--end wrapper-->

    <?php include 'footer.php' ?>
    <!-- JS Files-->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <!--plugins-->
    <script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <script src="assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
    <script src="assets/plugins/easyPieChart/jquery.easypiechart.js"></script>
    <script src="assets/plugins/chartjs/chart.min.js"></script>
    <script src="assets/js/index.js"></script>
    <!-- Main JS-->
    <script src="assets/js/main.js"></script>


</body>

</html>

<?php 
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $service = $_POST['service'];
    $spkts = $_POST['spkts'];
    $sbytes = $_POST['sbytes'];
    $sttl = $_POST['sttl'];
    $smean = $_POST['smean'];
    $attack_cat = $_POST['attack_cat'];

    $insertData = "INSERT INTO data_training (`id_data_training`, `service`, `spkts`, `sbytes`, `sttl`, `smean`, `attack_cat`) 
                   VALUES (NULL, '$service', '$spkts', '$sbytes', '$sttl', '$smean', '$attack_cat')";

    $insertResult = mysqli_query($conn, $insertData);

    if ($insertResult) {
        echo "<script>alert('Berhasil menambah data training.')</script>";
        echo "<script>window.location.href = 'data_training.php';</script>";
        // Alternatively, you can use: echo "<script>location.reload();</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>