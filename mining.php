<?php 
include 'koneksi.php'; ?>
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

    <script src="https://d3js.org/d3.v6.min.js"></script>
    <style>
    .node {
        cursor: pointer;
    }

    .node circle {
        fill: #999;
    }

    .node text {
        font: 12px sans-serif;
    }

    .link {
        fill: none;
        stroke: #555;
        stroke-width: 1.5px;
    }
    </style>

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
                <form method="post" enctype="multipart/form-data">
                    <!-- <div class="mb-3">
                        <label for="file" class="form-label">Upload Excel File</label>
                        <input type="file" class="form-control" id="file" name="file">
                    </div> -->
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
                    // $uploadedFile = $_FILES['file']['tmp_name'];
                    $new_data = array(
                        'service' => $_POST['service'],
                        'spkts' => $_POST['spkts'],
                        'sbytes' => $_POST['sbytes'],
                        'sttl' => $_POST['sttl'],
                        'smean' => $_POST['smean'],
                    );

                    // Load data from the 'data_training' table and convert it into an array
                    $data_training = [];
                    $result = $conn->query("SELECT * FROM data_training");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $data_training[] = $row;
                        }
                    }
                    // var_dump($data_training);

                    

                    $c45 = new Algorithm\C45();
                    $input = new Algorithm\C45\DataInput;
                    $input->setData($data_training);
                    $input->setAttributes(array('service', 'spkts', 'sbytes', 'sttl', 'smean', 'attack_cat'));
                    $c45->c45 = $input; // Set input data
                    $c45->setTargetAttribute('attack_cat');
                    $initialize = $c45->initialize();
                    // $c45->loadFile($uploadedFile)->setTargetAttribute('attack_cat')->initialize();
                    echo "<pre>";
                    $datal = $initialize->buildTree()->toString();
                    // print_r ($initialize->buildTree()->toString()); // print as string
                    print_r($datal);
                    // echo json_encode($initialize->buildTree()->toString());
                    $datall = json_encode($datal);
                    // echo json_encode($datal);
                    echo "</pre>";
                    $result = $initialize->initialize()->buildTree()->classify($new_data);
                    echo "Hasil Klasifikasi: " . $result;
                }
                ?>
                <!-- <div id="tree"></div> -->
                <!-- <button id="download">Download as Image</button> -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
                <script>
                // Sample data (replace with your JSON data)
                // const data = /* Your JSON data here */ ;
                // const data = <?php echo ($datall); ?>;
                const dataTeks = <?php echo ($datall); ?>;

                // Parse the text data into a tree structure
                function parseTextToTree(text) {
                    const lines = text.split('\n');
                    const root = {
                        name: 'root',
                        children: []
                    };
                    const stack = [root];

                    lines.forEach(line => {
                        const level = line.lastIndexOf('|') + 1;
                        const trimmedLine = line.replace(/^\|+/, '').trim();
                        const [name, ...rest] = trimmedLine.split(':');
                        const label = rest.join(':').trim();
                        const node = {
                            name: name.trim(),
                            label: label || null,
                            children: []
                        };

                        while (stack.length > level + 1) {
                            stack.pop();
                        }

                        stack[stack.length - 1].children.push(node);
                        stack.push(node);
                    });

                    return root.children[0];
                }

                const data = parseTextToTree(dataTeks);
                console.log(data); // Check the parsed data structure


                const width = 960;
                const height = 500;

                const svg = d3.select("#tree").append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .append("g")
                    .attr("transform", "translate(40,0)");

                const tree = d3.tree().size([height, width - 160]);

                const root = d3.hierarchy(data);

                tree(root);

                const link = svg.selectAll(".link")
                    .data(root.descendants().slice(1))
                    .enter().append("path")
                    .attr("class", "link")
                    .attr("d", d => `
                M${d.y},${d.x}
                C${(d.y + d.parent.y) / 2},${d.x}
                 ${(d.y + d.parent.y) / 2},${d.parent.x}
                 ${d.parent.y},${d.parent.x}
            `);

                const node = svg.selectAll(".node")
                    .data(root.descendants())
                    .enter().append("g")
                    .attr("class", d => "node" + (d.children ? " node--internal" : " node--leaf"))
                    .attr("transform", d => `translate(${d.y},${d.x})`);

                node.append("circle")
                    .attr("r", 2.5);

                node.append("text")
                    .attr("dy", 3)
                    .attr("x", d => d.children ? -8 : 8)
                    .style("text-anchor", d => d.children ? "end" : "start")
                    .text(d => d.data.name);

                // Download as Image
                document.getElementById('download').onclick = function() {
                    html2canvas(document.getElementById('tree')).then(canvas => {
                        const link = document.createElement('a');
                        link.href = canvas.toDataURL();
                        link.download = 'tree.png';
                        link.click();
                    });
                }
                </script>

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