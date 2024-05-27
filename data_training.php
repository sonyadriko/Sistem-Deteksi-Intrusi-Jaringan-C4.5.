<?php 
include 'koneksi.php';

require 'vendor/autoload.php'; // Make sure this path is correct

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['import'])) {
        // Process the uploaded Excel file for import
        if (isset($_FILES['excelFile'])) {
            $fileError = $_FILES['excelFile']['error'];
            if ($fileError == UPLOAD_ERR_OK) {
                $excelFile = $_FILES['excelFile']['tmp_name'];

                try {
                    // Load the Excel file
                    $spreadsheet = IOFactory::load($excelFile);
                    $worksheet = $spreadsheet->getActiveSheet();

                    // Prepare the statement for inserting data into the 'handphone' table
                    $stmtInsert = $conn->prepare('INSERT INTO data_training (`service`, `spkts`, `sbytes`, `sttl`, `smean`, `attack_cat`) VALUES (?, ?, ?, ?, ?, ?)');

                    // Initialize variable for counting successful inserts
                    $successCount = 0;

                    // Iterate through rows and insert data into the 'handphone' table
                    foreach ($worksheet->getRowIterator() as $row) {
                        $rowData = [];
                        foreach ($row->getCellIterator() as $cell) {
                            $rowData[] = $cell->getValue();
                        }

                        // Assuming the Excel columns are in the order specified
                        if (count($rowData) == 6) {
                            // Extracting values
                            $service = $rowData[0];
                            $spkts = $rowData[1];
                            $sbytes = $rowData[2];
                            $sttl = $rowData[3];
                            $smean = $rowData[4];
                            $attack_cat = $rowData[5];

                            // Determine spkts category based on the value
                            $spkts = $spkts <= 14 ? 'low' : 'high';

                            // Determine sbytes category based on the value
                            $sbytes = (int) $sbytes <= 132 ? 'low' : 'high';

                            // Determine sttl category based on the value
                            $sttlValue = (int) $sttl;
                            if ($sttlValue <= 31) {
                                $sttl = 'low';
                            } elseif ($sttlValue <= 62) {
                                $sttl = 'med';
                            } else {
                                $sttl = 'high';
                            }

                            // Determine smean category based on the value
                            $smean = (int) $smean <= 84 ? 'low' : 'high';

                            // Insert data into the 'handphone' table
                            $stmtInsert->bind_param('ssssss', $service, $spkts, $sbytes, $sttl, $smean, $attack_cat);
                            if ($stmtInsert->execute()) {
                                $successCount++; // Increment the count of successful inserts
                            }
                        }
                    }

                    // Check if any data was successfully inserted
                    if ($successCount > 0) {
                        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                        echo "<script>
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Import successful!',
                                    text: 'Total $successCount rows inserted.',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'data.php';
                                    }
                                });
                              </script>";
                    } else {
                        echo '<script>alert("No data inserted.");</script>';
                    }

                    // exit();
                } catch (Exception $e) {
                    echo '<script>alert("Error processing the file: ' . $e->getMessage() . '");</script>';
                }
            } else {
                echo '<script>alert("Error uploading the file.");</script>';
            }
        } else {
            echo '<script>alert("No file uploaded.");</script>';
        }
    }
}

?>
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

                <h6 class="mb-0 text-uppercase">Data Training</h6>
                <hr />
                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                            enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="excelFile" class="form-label">Choose Excel File</label>
                                <input type="file" class="form-control" id="excelFile" name="excelFile"
                                    accept=".xls, .xlsx" required>
                                <div id="fileError" class="invalid-feedback"></div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="import">Upload</button>
                        </form>
                    </div>
                </div>
                <hr />
                <a href="tambah_data_training.php" class="btn btn-primary btn-user">Tambah Data Training</a>
                <hr />
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>service</th>
                                        <th>spkts</th>
                                        <th>sbytes</th>
                                        <th>sttl</th>
                                        <th>smean</th>
                                        <th>attack cat</th>
                                        <th>aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $get_data = mysqli_query($conn, "select * from data_training");
                                            while($display = mysqli_fetch_array($get_data)) {
                                                $id = $display['id_data_training'];
                                                $service = $display['service'];
                                                $spkts = $display['spkts'];
                                                $sbytes = $display['sbytes'];
                                                $sttl = $display['sttl'];
                                                $smean = $display['smean'];
                                                $attack_cat = $display['attack_cat'];
                                    ?>
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $service; ?></td>
                                        <td><?php echo $spkts; ?></td>
                                        <td><?php echo $sbytes; ?></td>
                                        <td><?php echo $sttl; ?></td>
                                        <td><?php echo $smean; ?></td>
                                        <td><?php echo $attack_cat; ?></td>
                                        <td>
                                            <a href='edit_data.php?GetID=<?php echo $id; ?>'
                                                style="text-decoration: none; list-style: none;"><input type='submit'
                                                    value='Ubah' id='editbtn' class="btn btn-primary btn-user"></a>
                                            <a href='delete_data.php?Del=<?php echo $id; ?>'
                                                style="text-decoration: none; list-style: none;"><input type='submit'
                                                    value='Hapus' id='delbtn' class="btn btn-primary btn-user"></a>
                                        </td>
                                    </tr>

                                    <?php $no++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="clear_all_data.php" method="post">
                                    <button type="submit" class="btn btn-danger">Delete All Data</button>
                                </form>
                            </div>
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
        <?php include 'footer.php' ?>

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