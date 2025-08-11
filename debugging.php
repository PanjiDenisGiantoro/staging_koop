<?php
include("common.php");
echo "This is a page to debug anything";
echo '<br><br>';

// Show columns of the 'generalacc' table
$sSQLtest = "SHOW COLUMNS FROM generalacc";
$rsTest = $conn->Execute($sSQLtest);

// Check if the query was successful
if ($rsTest) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<thead><tr><th colspan='100%'>Fields in generalacc</th></tr><tr>";

    // Create an array to store column names
    $columnNames = array();
    
    // Print column headers
    while (!$rsTest->EOF) {
        $columnNames[] = $rsTest->fields['Field']; // Store each field name
        echo "<th>" . $rsTest->fields['Field'] . "</th>";
        $rsTest->MoveNext();
    }
    echo "</tr></thead>";
    $rsTest->Close();
    
    // Show data from the 'generalacc' table
    $sSQLData = "SELECT * FROM generalacc";
    $rsData = $conn->Execute($sSQLData);

    // Check if the query was successful
    if ($rsData) {
        echo "<tbody>";
        
        // Print data rows
        while (!$rsData->EOF) {
            echo "<tr>";
            foreach ($columnNames as $column) {
                // Output the data in each field in the same order as the columns
                echo "<td>" . htmlspecialchars($rsData->fields[$column]) . "</td>";
            }
            echo "</tr>";
            $rsData->MoveNext();
        }
        echo "</tbody></table>";
        $rsData->Close();
    } else {
        echo "Error retrieving data from generalacc<br>";
    }
} else {
    echo "Error retrieving fields from generalacc<br>";
}

print'<br><br><br><br><br><br><br>';
?>
