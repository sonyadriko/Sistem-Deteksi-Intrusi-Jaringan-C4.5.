<?php 
include 'koneksi.php';

// Ambil data dari tabel MySQL
$sql = "SELECT * FROM data_training";
$result = $conn->query($sql);

// Persiapkan data untuk diproses oleh algoritma C4.5
$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Hitung jumlah rekaman total
$total_records = count($data);

// Hitung jumlah rekaman dalam setiap kelas
$class_counts = array_count_values(array_column($data, 'attack_cat'));

// Hitung jumlah fitur
$num_features = count($data[0]) - 1; // Kurangi satu karena kolom terakhir adalah label kelas

// Hitung entropi dataset
$entropy = 0;
foreach ($class_counts as $class_count) {
    $probability = $class_count / $total_records;
    $entropy -= $probability * log($probability, 2);
}

$exploits_count = isset($class_counts['Exploits']) ? $class_counts['Exploits'] : 0;
$generic_count = isset($class_counts['Generic']) ? $class_counts['Generic'] : 0;
$fuzzers_count = isset($class_counts['Fuzzers']) ? $class_counts['Fuzzers'] : 0;
$normal_count = isset($class_counts['Normal']) ? $class_counts['Normal'] : 0;

// Calculate the probability of each attack category
$exploits_probability = $exploits_count / $total_records;
$generic_probability = $generic_count / $total_records;
$fuzzers_probability = $fuzzers_count / $total_records;
$normal_probability = $normal_count / $total_records;

// Calculate the entropy contribution for each attack category
$exploits_entropy = ($exploits_count > 0) ? (-$exploits_probability * log($exploits_probability, 2)) : 0;
$generic_entropy = ($generic_count > 0) ? (-$generic_probability * log($generic_probability, 2)) : 0;
$fuzzers_entropy = ($fuzzers_count > 0) ? (-$fuzzers_probability * log($fuzzers_probability, 2)) : 0;
$normal_entropy = ($normal_count > 0) ? (-$normal_probability * log($normal_probability, 2)) : 0;

// Total entropy calculation
$total_entropy = $exploits_entropy + $generic_entropy + $fuzzers_entropy + $normal_entropy;

// Hitung statistik untuk setiap fitur
$statistics = [
    "service" => [],
    "spkts" => [],
    "sbytes" => [],
    "sttl" => [],
    "smean" => [],
    "" => []
];


foreach ($statistics as $feature => &$values) {
    $total_gain_attribute = 0;
    $unique_values = array_unique(array_column($data, $feature));
    if ($feature == "spkts") {
        // Kelompokkan spkts menjadi <=10 dan >10
        $unique_values = ["<=10", ">10"];
    } elseif ($feature == "sbytes") {
        // Kelompokkan sbytes menjadi <=766 dan >766
        $unique_values = ["<=766", ">766"];
    } elseif ($feature == "sttl") {
        // Kelompokkan sttl menjadi 31, 62, dan 254
        $unique_values = [31, 62, 254];
    } elseif ($feature == "smean") {
        // Kelompokkan smean menjadi <=78 dan >78
        $unique_values = ["<=78", ">78"];
    }else {
        $unique_values = array_unique(array_column($data, $feature));
    }

    foreach ($unique_values as $value) {
        $subset_entropy = 0;
        $value_count = 0;
        $class_counts_subset = [];
        $split_info_subset = 0;

        foreach ($data as $row) {
            $match_condition = false;
            if ($feature == "spkts") {
                $match_condition = ($value == "<=10" && $row[$feature] <= 10) || ($value == ">10" && $row[$feature] > 10);
            } elseif ($feature == "sbytes") {
                $match_condition = ($value == "<=766" && $row[$feature] <= 766) || ($value == ">766" && $row[$feature] > 766);
            } elseif ($feature == "sttl") {
                $match_condition = $row[$feature] == $value;
            } elseif ($feature == "smean") {
                $match_condition = ($value == "<=78" && $row[$feature] <= 78) || ($value == ">78" && $row[$feature] > 78);
            } else {
                $match_condition = $row[$feature] == $value;
            }

            if ($match_condition) {
                $value_count++;
                if (!isset($class_counts_subset[$row['attack_cat']])) {
                    $class_counts_subset[$row['attack_cat']] = 0;
                }
                $class_counts_subset[$row['attack_cat']]++;
            }
        }

        $gain = 0;
        foreach ($class_counts_subset as $class_count) {
            $probability = $class_count / $value_count;
            $subset_entropy -= $probability * log($probability, 2);

            $subset_probability = $value_count/$total_records;

            $gain_subset = $subset_probability * $subset_entropy;
            $gain += $gain_subset;

        }

        $split_info_subset -= $subset_probability * log($subset_probability,2);


        $values[] = [
            "Value" => $value,
            "Jml_Record" => $value_count,
            "Fuzzers" => isset($class_counts_subset["Fuzzers"]) ? $class_counts_subset["Fuzzers"] : 0,
            "Exploits" => isset($class_counts_subset["Exploits"]) ? $class_counts_subset["Exploits"] : 0,
            "Generic" => isset($class_counts_subset["Generic"]) ? $class_counts_subset["Generic"] : 0,
            "Normal" => isset($class_counts_subset["Normal"]) ? $class_counts_subset["Normal"] : 0,
            "Entropy" => $subset_entropy,
            "Gain" => $gain_subset,
            "Split_Info" => $split_info_subset,

        ];
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
        <h3>Total Entropy : <?php echo $total_entropy ?></h3>
                <?php
                // Tampilkan statistik yang dihitung
                foreach ($statistics as $feature => $values) {
                    echo "<h6 class='mb-0 text-uppercase'>$feature</h6>";
                    echo "<table border='1'>";
                    echo "<tr><th>Value</th><th>Jml Record</th><th>Fuzzers</th><th>Exploits</th><th>Generic</th><th>Normal</th><th>Entropy</th><th>Gain</th><th>Split Ratio</th>
                    <th>Gain Ratio</th></tr>";
                    foreach ($values as $value) {
                        echo "<tr>";
                        echo "<td>{$value['Value']}</td>";
                        echo "<td>{$value['Jml_Record']}</td>";
                        echo "<td>{$value['Fuzzers']}</td>";
                        echo "<td>{$value['Exploits']}</td>";
                        echo "<td>{$value['Generic']}</td>";
                        echo "<td>{$value['Normal']}</td>";
                        echo "<td>{$value['Entropy']}</td>";
                        echo "<td>{$value['Gain']}</td>";
                        echo "<td>{$value['Split_Info']}</td>";
                        echo "</tr>";
                    }
                    $total_gain_attribute = 0;
                    $total_split_info = 0;

                    // Calculate total gain for this attribute
                    foreach ($values as $value) {
                        $total_gain_attribute += $value['Gain'];
                        $total_split_info += $value['Split_Info'];
                    }
                    $total_gain = $total_entropy - $total_gain_attribute;

                    $gain_ratio_total = $total_gain / $total_split_info;

                    echo "</table>";
                    echo "<td>Gain : $total_gain</td>";
                    echo "<td>Split Ratios : {$total_split_info}</td>";
                    echo "<td>Gain Ratios : {$gain_ratio_total}</td>";
                    echo "<br>";
                    echo "<br>";
                }
                ?>

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

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

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
