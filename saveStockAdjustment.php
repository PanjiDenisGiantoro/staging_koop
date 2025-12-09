<?php
// Enable error reporting
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
date_default_timezone_set("Asia/Jakarta");

// Log to a file for debugging
$log_file = 'adjustment_debug.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . ": Script started\n");

// Output as JSON
header('Content-Type: application/json');

// Include common functions and DB connection
try {
    include("common.php");
    include("koperasiQry.php");
    file_put_contents($log_file, date('Y-m-d H:i:s') . ": common.php included\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . ": Error including common.php: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(array(
        'success' => false,
        'error' => 'Failed to include required files: ' . $e->getMessage()
    ));
    exit;
}

// Extract and validate form data
$adjNo = isset($_POST['adjNo']) ? $_POST['adjNo'] : '';
$title = isset($_POST['tajuk']) ? $_POST['tajuk'] : 'Pelarasan Stok';
$tarikh_adj = isset($_POST['tarikh_adj']) ? $_POST['tarikh_adj'] : date('Y-m-d');
$tarikh_adj = date('Y-m-d H:i:s');

// Get stock items
$stockIDss = isset($_POST['stokID']) ? $_POST['stokID'] : array();
$quantities = isset($_POST['kuantiti']) ? $_POST['kuantiti'] : array();
$unitCosts = isset($_POST['kosUnit']) ? $_POST['kosUnit'] : array();
$pymtAmts = isset($_POST['jumlah']) ? $_POST['jumlah'] : array();
$remarks = isset($_POST['catatan']) ? $_POST['catatan'] : array();

// Log received data
file_put_contents($log_file, date('Y-m-d H:i:s') . ": Data received - AdjNo: $adjNo, Title: $title\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . ": tarikh_adj: $tarikh_adj\n", FILE_APPEND);

file_put_contents($log_file, date('Y-m-d H:i:s') . ": Items count: " . count($stockIDss) . "\n", FILE_APPEND);

// Set up user info
$createdBy = isset($_POST['createdBy']) ? $_POST['createdBy'] : 'system';
file_put_contents($log_file, date('Y-m-d H:i:s') . ": created by - Name: $createdBy\n", FILE_APPEND);

if (empty($createdBy)) {
    $createdBy = 'system'; // Fallback username if session value is empty
}
$updatedBy = $createdBy;
$createdDate = date("Y-m-d H:i:s");
$updatedDate = $createdDate;

file_put_contents($log_file, date('Y-m-d H:i:s') . ": User: $createdBy\n", FILE_APPEND);

// Convert date format if needed
// if (function_exists('saveDateDb')) {
//     $tarikh_adj = saveDateDb($tarikh_adj);
// }
file_put_contents($log_file, date('Y-m-d H:i:s') . ": tarikh_adj after conversion: $tarikh_adj\n", FILE_APPEND);

// Calculate total amount
$totalPymt = 0;
foreach ($pymtAmts as $amt) {
    $totalPymt += floatval($amt);
}

// Generate a new adjNo if not provided
// if (empty($adjNo)) {
//     $getMax = "SELECT MAX(CAST(RIGHT(adjNo,6) AS SIGNED)) AS no2 FROM adjustment";
//     $rsMax = $conn->Execute($getMax);
    
//     if (!$rsMax) {
//         file_put_contents($log_file, date('Y-m-d H:i:s') . ": Error getting max adjNo: " . $conn->ErrorMsg() . "\n", FILE_APPEND);
//         echo json_encode(array(
//             'success' => false,
//             'error' => 'Database error: ' . $conn->ErrorMsg()
//         ));
//         exit;
//     }
    
//     $max = ($rsMax && isset($rsMax->fields['no2'])) ? intval($rsMax->fields['no2']) + 1 : 1;
//     $adjNo = 'ADJ' . sprintf("%06s", $max);
// }

file_put_contents($log_file, date('Y-m-d H:i:s') . ": Using AdjNo: $adjNo\n", FILE_APPEND);

// Insert header record - using direct insertion to avoid parameter issues
$headerSQL = "INSERT INTO adjustment 
    (adjNo, tarikh_adj, title, addminus, stockID, pymtAmt, quantity, unitCost, `status`, createdDate, createdBy, updatedDate, updatedBy)
    VALUES ('$adjNo', '$tarikh_adj', '$title', 0, '', $totalPymt, 0, 0, 0, '$createdDate', '$createdBy', '$updatedDate', '$createdBy')";

file_put_contents($log_file, date('Y-m-d H:i:s') . ": Executing: $headerSQL\n", FILE_APPEND);

$result1 = $conn->Execute($headerSQL);

if (!$result1) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . ": Header insert failed: " . $conn->ErrorMsg() . "\n", FILE_APPEND);
    echo json_encode(array(
        'success' => false,
        'error' => 'Failed to save header: ' . $conn->ErrorMsg()
    ));
    exit;
}

file_put_contents($log_file, date('Y-m-d H:i:s') . ": Header saved successfully\n", FILE_APPEND);

// Insert detail records
$detailsInserted = 0;

for ($i = 0; $i < count($stockIDss); $i++) {
    $stockID = isset($stockIDss[$i]) ? trim($stockIDss[$i]) : '';
    $quantity = isset($quantities[$i]) ? floatval($quantities[$i]) : 0;
    $unitCost = isset($unitCosts[$i]) ? floatval($unitCosts[$i]) : 0;
    $pymtAmt = isset($pymtAmts[$i]) ? floatval($pymtAmts[$i]) : 0;
    $remark = isset($remarks[$i]) ? trim($remarks[$i]) : '';
    
    file_put_contents($log_file, date('Y-m-d H:i:s') . ": Processing item $i: stockID=$stockID, qty=$quantity, cost=$unitCost\n", FILE_APPEND);
    
    if (empty($stockID)) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . ": Skipping item $i due to empty stock code\n", FILE_APPEND);
        continue;
    }
    
    // Direct SQL insertion to avoid parameter binding issues
    $detailSQL = "INSERT INTO adjustment 
        (adjNo, tarikh_adj, addminus, stockID, remark, pymtAmt, quantity, unitCost, `status`, createdDate, createdBy, updatedDate, updatedBy)
        VALUES ('$adjNo', '$tarikh_adj', 1, '$stockID', '$remark', $pymtAmt, $quantity, $unitCost, 0, '$createdDate', '$createdBy', '$updatedDate', '$createdBy')";
    
    file_put_contents($log_file, date('Y-m-d H:i:s') . ": Detail SQL $i: $detailSQL\n", FILE_APPEND);
    
    $result2 = $conn->Execute($detailSQL);
    
    if (!$result2) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . ": Detail insert failed: " . $conn->ErrorMsg() . "\n", FILE_APPEND);
        echo json_encode(array(
            'success' => false,
            'error' => 'Failed to save item #' . ($i + 1) . ': ' . $conn->ErrorMsg()
        ));
        exit;
    }
    
    file_put_contents($log_file, date('Y-m-d H:i:s') . ": Detail record $i inserted successfully\n", FILE_APPEND);
    $detailsInserted++;
}

file_put_contents($log_file, date('Y-m-d H:i:s') . ": Successfully inserted $detailsInserted detail records\n", FILE_APPEND);

// Return success response
$response = array(
    'success' => true,
    'adjNo' => $adjNo,
    'itemCount' => $detailsInserted
);

file_put_contents($log_file, date('Y-m-d H:i:s') . ": Returning success response: " . json_encode($response) . "\n", FILE_APPEND);
echo json_encode($response);

file_put_contents($log_file, date('Y-m-d H:i:s') . ": Script completed successfully\n", FILE_APPEND);
exit;
?>