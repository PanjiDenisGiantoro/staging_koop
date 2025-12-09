<?php
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$today = date('Y-m-d');

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") != 1 && get_session("Cookie_groupID") != 2 && get_session("Cookie_koperasiID") != $koperasiID) {
    echo '<script>alert("Unauthorized access.");parent.location.href = "index.php";</script>';
    exit;
}

$sActionFileName = "?vw=memberUpdate&mn='" . $mn . "&tabb=3&ID='" . $ID . "'";

//--- Prepare kod objek/akaun type
$objList = array();
$objVal  = array();
$GetObj = ctGeneral("", "J");
if ($GetObj->RowCount() <> 0) {
    while (!$GetObj->EOF) {
        array_push($objList, $GetObj->fields(name));
        array_push($objVal, $GetObj->fields(ID));
        $GetObj->MoveNext();
    }
}

//--- Prepare ptj type
$ptjList = array();
$ptjVal  = array();
$GetPtj = ctGeneral("", "U");
if ($GetPtj->RowCount() <> 0) {
    while (!$GetPtj->EOF) {
        array_push($ptjList, $GetPtj->fields(name));
        array_push($ptjVal, $GetPtj->fields(ID));
        $GetPtj->MoveNext();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $newIC = $_POST['newIC'];
    $mobileNo = $_POST['mobileNo'];
    $address = $_POST['address'];
    $percent = isset($_POST['percent']) && $_POST['percent'] !== '' ? $_POST['percent'] : '0.00'; // Set default to 0.00 if empty

    if ($name && $newIC && $mobileNo && $address && $percent) {

        $sSQL = "INSERT INTO nominee (
                                name, 
                                newIC, 
                                mobileNo, 
                                address, 
                                percent, 
                                refer) 
                                    VALUES (" .
            tosql($name, "Text") . ", " .
            tosql($newIC, "Number") . ", " .
            tosql($mobileNo, "Text") . ", " .
            tosql($address, "Text") . ", " .
            tosql($percent, "Number") . ", " .
            tosql($userID, "Number") . ")";

        $rs = &$conn->Execute($sSQL);

        if ($rs) {
            echo '<script>alert("Penama baru telah berjaya dimasukkan."); window.opener.location.reload(); window.close();</script>';
            exit;
        }
    } else {
        echo '<script>alert("Sila masukkan semua maklumat dengan lengkap.");</script>';
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <title>Tambah Potongan</title>
</head>

<body>
    <div class="m-3"><b>Masukkan Penama Baru</b></div>
    <div class="m-3">
        <form method="POST" action="">
            <table class="table table-sm table-striped">
                <tr class="table-primary">
                    <td><b>Nama Penama</b></td>
                    <td><b>No Kartu Identitas</b></td>
                    <td><b>No Telefon</b></td>
                    <td align="left"><b>Alamat Rumah</b></td>
                    <td><b>Peratus (%)</b></td>
                    <td>&nbsp;</td>
                </tr>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name="name" maxlength="50" class="form-control" required>
                        </td>
                        <td>
                            <input type="text" name="newIC" maxlength="12" class="form-control" placeholder="99XXXXXXXXXX" required>
                        </td>
                        <td>
                            <input type="text" name="mobileNo" maxlength="12" class="form-control" placeholder="6013XXXXXXXX" required>
                        </td>
                        <td>
                            <textarea name="address" class="form-control" required></textarea>
                        </td>
                        <td>
                            <input type="text" name="percent" maxlength="12" class="form-control" placeholder="0.00">
                        </td>
                        <td>
                            <input type="submit" class="btn btn-primary" value="Simpan">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
</body>

</html>