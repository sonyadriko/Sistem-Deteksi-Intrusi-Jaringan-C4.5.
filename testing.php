<?php 
include 'koneksi.php';
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

    <title>Tree</title>
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

                <h6 class="mb-0 text-uppercase">Testing</h6>
                <hr />
                <form method="post" action="">
                    <label for="test_size">Select Test Size:</label>
                    <select id="test_size" name="test_size" class="form-select mt-2">
                        <?php for ($i = 1; $i <= 9; $i++) : ?>
                        <option value="<?php echo $i / 10; ?>">
                            <?php echo "Data Testing " . $i / 10 . " - Data Training " . (1 - $i / 10); ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="btn btn-primary mt-2 mb-2">Submit</button>
                </form>
                <!-- <img src="../backend/tree_graph.png"> -->


                <?php
// URL of the Flask API
$url = 'http://localhost:5000/train_full';

// Data to be sent to the Flask API
$data = array('data_path' => 'data100.xlsx', 'model_path' => 'model.pkl');

// Prepare the POST options
$options = array(
    'http' => array(
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    )
);

// Create the context for the POST request
$context  = stream_context_create($options);

// Attempt to receive a response from the Flask API
// $result = file_get_contents($url, false, $context);

// // Check the result and decode the JSON response
// if ($result === FALSE) {
//     // If the server is unreachable or returns a bad response, output an error message
//     echo "Failed to connect to the Flask server or received a bad response.";
// } else {
//     // Decode the JSON response
//     $response = json_decode($result);

//     // Check if there was a JSON decoding error
//     if (json_last_error() !== JSON_ERROR_NONE) {
//         // Output the JSON error message
//         echo "JSON decoding error: " . json_last_error_msg();
//     } elseif (isset($response->error)) {
//         // If the Flask API returned an error, display it
//         echo "Error from Flask: " . $response->error;
//     } elseif (isset($response->image_url)) {
//         // If the response includes an image URL, display the image
//         echo "<img src='" . htmlspecialchars($response->image_url) . "' alt='Model Diagram'>";
//     } else {
//         // If the response format is unexpected, output the raw response for debugging
//         echo "Unexpected response format: " . htmlspecialchars($result);
//     }
// }
?>
                <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $test_size = $_POST['test_size'];
    $endpoint = "http://localhost:5000/testc45?test_size=$test_size";

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    $data = json_decode($response, true);

    // Validasi respons dari API
    if (is_array($data) && isset($data['confusion_details'], $data['classification_report'])) {
        // Tampilkan informasi
        echo "Accuracy: " . $data['accuracy'] . "<br>";
        echo "Confusion Matrix: <br>";
        foreach ($data['confusion_matrix'] as $row) {
            echo implode(' ', $row) . "<br>";
        }

        // Tampilkan TN, FP, FN, TP jika tersedia
        foreach ($data['confusion_details'] as $class => $details) {
            echo "<h3>$class:</h3>";
            echo "TP: " . $details['TP'] . "<br>";
            echo "TN: " . $details['TN'] . "<br>";
            echo "FP: " . $details['FP'] . "<br>";
            echo "FN: " . $details['FN'] . "<br>";
        }

        // echo "Classification Report: <pre>" . $data['classification_report'] . "</pre><br>";
        echo "Precision: " . $data['precision'] . "<br>";
        echo "F1 Score: " . $data['f1_score'] . "<br>";
        echo "Recall: " . $data['recall'] . "<br>";

    } else {
        echo 'Data yang diterima dari API tidak lengkap atau tidak valid.';
    }
}
?>


                <?php
// $url = "http://localhost:5000/testc45";  // Adjust the URL based on your Flask API location

// $ch = curl_init($url); 
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, false);
// $response = curl_exec($ch);

// if ($response === false) {
//     echo 'Error during communication: ' . curl_error($ch);
//     curl_close($ch);
//     exit;
// }

// curl_close($ch);
// // $result = json_decode($response, true);  // Decode the JSON response into an associative array// Menggunakan curl atau GuzzleHTTP untuk mendapatkan $data
// $data = json_decode($response, true);
// // Validasi respons dari API
// if (is_array($data) && isset($data['confusion_details'], $data['classification_report'])) {
//     // Tampilkan informasi
//     echo "Accuracy: " . $data['accuracy'] . "<br>";
//     echo "Confusion Matrix: <br>";
//     foreach ($data['confusion_matrix'] as $row) {
//         echo implode(' ', $row) . "<br>";
//     }

//     // Tampilkan TN, FP, FN, TP jika tersedia
//     foreach ($data['confusion_details'] as $class => $details) {
//         echo "<h3>$class:</h3>";
//         echo "TP: " . $details['TP'] . "<br>";
//         echo "TN: " . $details['TN'] . "<br>";
//         echo "FP: " . $details['FP'] . "<br>";
//         echo "FN: " . $details['FN'] . "<br>";
//     }

//     echo "Classification Report: <pre>" . $data['classification_report'] . "</pre><br>";
//     echo "Precision: " . $data['precision'] . "<br>";
//     echo "F1 Score: " . $data['f1_score'] . "<br>";
//     echo "Recall: " . $data['recall'] . "<br>";

// } else {
//     echo 'Data yang diterima dari API tidak lengkap atau tidak valid.';
// }
                ?>




                <?php
// $url = "http://localhost:5000/model_info";
// $response = file_get_contents($url);
// $model_info = json_decode($response, true);

// echo "<pre>";
// print_r($model_info);
// echo "</pre>";
?>








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