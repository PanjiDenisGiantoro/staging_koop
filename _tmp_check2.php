<?php
include("C:/xampp/htdocs/staging_koop/common.php");
$rs = $conn->Execute("SELECT * FROM setup WHERE setupID = 1");
$fields = $rs->fields;
foreach($fields as $key => $val) {
    if(!is_numeric($key)) echo "$key = $val\n";
}
echo "\n--- general simpanan ---\n";
$rs2 = $conn->Execute("SELECT ID, code, name, category FROM general WHERE category = 'Y' LIMIT 10");
while(!$rs2->EOF) {
    echo "ID=" . $rs2->fields["ID"] . " | code=" . $rs2->fields["code"] . " | name=" . $rs2->fields["name"] . "\n";
    $rs2->MoveNext();
}
echo "\n--- sample userdetails newIC ---\n";
$rs3 = $conn->Execute("SELECT userID, newIC FROM userdetails LIMIT 3");
while(!$rs3->EOF) {
    echo "userID=" . $rs3->fields["userID"] . " | newIC=" . $rs3->fields["newIC"] . "\n";
    $rs3->MoveNext();
}
?>
