<?php
/*
 * AJAX endpoint — returns JSON list of active usaha for a given memberID
 */
include("common.php");
include("koperasiQry.php");

header('Content-Type: application/json');

$memberID = isset($_GET['memberID']) ? trim($_GET['memberID']) : '';

if ($memberID === '') {
    // No member selected: return all active usaha
    $rs = $conn->Execute("SELECT usahaID, nama_usaha FROM usaha WHERE status=1 ORDER BY nama_usaha");
} else {
    $rs = $conn->Execute(
        "SELECT usahaID, nama_usaha FROM usaha WHERE status=1 AND memberID=" . tosql($memberID, "Text") . " ORDER BY nama_usaha"
    );
}

$result = array();
while ($rs && !$rs->EOF) {
    $result[] = array(
        'usahaID'    => $rs->fields('usahaID'),
        'nama_usaha' => $rs->fields('nama_usaha'),
    );
    $rs->MoveNext();
}

echo json_encode($result);
