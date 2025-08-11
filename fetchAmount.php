<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: fetchAmount.php
*			Date 		: 12/12/2024
*			Description : Pickup default amount when selecting kod objek akaun dropdown
*********************************************************************************/
include 'common.php'; // Ensure the connection is included

// Check if the POST request contains 'deductID'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deductID'])) {
    $deductID = $_POST['deductID'];
    $sSQL = "SELECT j_Amount FROM general WHERE ID = '$deductID'";
    $result = $conn->Execute($sSQL);

    // Prevent unexpected output
    header('Content-Type: application/json');
    if ($result && !$result->EOF) {
        echo json_encode(array('j_Amount' => $result->fields('j_Amount')));
    } else {
        echo json_encode(array('j_Amount' => 0));
    }
    exit;
}

// If accessed directly, show an error or redirect
http_response_code(403); // Forbidden
echo "This script cannot be accessed directly.";
exit;
?>