<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


// Make sure to use the correct path to the autoload file
require_once __DIR__ . '/vendor/autoload.php';

use C45\C45;

// Correct the path by adding a slash
$filename = __DIR__ . '/Testing(100).csv';

// Check if the training file exists
if (!file_exists($filename)) {
    die("Training file does not exist.");
}

$c45 = new C45([
    'targetAttribute' => 'attack_cat', // make sure 'attack_cat' is the correct target attribute in your dataset
    'trainingFile' => $filename,
    'splitCriterion' => C45::SPLIT_GAIN,
]);

// Building the tree
$tree = $c45->buildTree();

// Convert the tree to a string and print it
$treeString = $tree->toString();
echo '<pre>';
print_r($treeString);
echo '</pre>';

// Prepare testing data, make sure the field names and values match the model expectations
$testingData = [
  'service' => 'ftp',
  'spkts' => 'low', // Ensure these values ('false', 'high', 'true') are expected categorical values in your model
  'sbytes' => 'high',
  'sttl' => 'low',
  'smean' => 'low',
];

// Classify the new data point
$result = $tree->classify($testingData);
echo "Classification Result: " . $result; // Improved output for clarity

?>
