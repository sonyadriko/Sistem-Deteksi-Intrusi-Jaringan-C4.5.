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
        <?php include 'sidebar.php'; ?>
        <!--end sidebar -->

        <!--start top header-->
        <?php include 'header.php'; ?>
        <!--end top header-->

        <!-- start page content wrapper-->
        <div class="page-content-wrapper">
            <!-- start page content-->
            <div class="page-content">
            <form method="post"  enctype="multipart/form-data">
            <div class="mb-3">
                        <label for="file" class="form-label">Upload Excel File</label>
                        <input type="file" class="form-control" id="file" name="file">
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label">Service</label>
                        <select class="form-select" id="service" name="service">
                            <option value="http">HTTP</option>
                            <option value="dns">DNS</option>
                            <option value="ftp">FTP</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="spkts" class="form-label">Spkts</label>
                        <input type="text" class="form-control" id="spkts" name="spkts">
                    </div>
                    <div class="mb-3">
                        <label for="sbytes" class="form-label">Sbytes</label>
                        <input type="text" class="form-control" id="sbytes" name="sbytes">
                    </div>
                    <div class="mb-3">
                        <label for="sttl" class="form-label">Sttl</label>
                        <input type="text" class="form-control" id="sttl" name="sttl">
                    </div>
                    <div class="mb-3">
                        <label for="smean" class="form-label">Smean</label>
                        <input type="text" class="form-control" id="smean" name="smean">
                    </div>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </form>

                <?php
                ini_set('display_errors', 1);
                error_reporting(E_ALL);

                require_once __DIR__ . '/vendor/autoload.php';

                use C45\C45;

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $uploadedFile = $_FILES['file']['tmp_name'];
                    $new_data = array(
                        'service' => $_POST['service'],
                        'spkts' => $_POST['spkts'],
                        'sbytes' => $_POST['sbytes'],
                        'sttl' => $_POST['sttl'],
                        'smean' => $_POST['smean'],
                    );

                    $c45 = new Algorithm\C45();
                    $c45->loadFile($uploadedFile)->setTargetAttribute('attack_cat')->initialize();
                    echo "<pre>";
                    print_r ($c45->buildTree()->toString()); // print as string
                    echo "</pre>";
                    $result = $c45->initialize()->buildTree()->classify($new_data);
                    echo "Hasil Klasifikasi: " . $result;
                }
                ?>


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
        <?php include 'footer.php'; ?>
    </div>
    <!--end wrapper-->

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
