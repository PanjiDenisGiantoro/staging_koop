<?php
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$today = date('Y-m-d');

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$userCreated = get_session("Cookie_userID");
$sActionFileName = "?vw=Edit_memberStmtPotongan&mn='" . $mn . "'&ID='" . $ID . "'";

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

    $potonganStart = $_POST['potonganStart'];
    $lastyrmthPymt = $_POST['lastyrmthPymt'];
    $loanType = $_POST['loanType'];
    $jumBlnP = $_POST['jumBlnP'];
    $ptjID = $_POST['ptjID'];

    if ($potonganStart && $lastyrmthPymt && $loanType && $jumBlnP && $ptjID) {
        $yearStart = substr($potonganStart, 0, 4);
        $monthStart = str_pad(substr($potonganStart, 4, 2), 2, "0", STR_PAD_LEFT);

        $sSQL = "INSERT INTO potbulan (
                                yrmth, 
                                userID, 
                                loanType, 
                                userCreated, 
                                CreateDate, 
                                status, 
                                ptjID, 
                                yearStart, 
                                monthStart, 
                                lastyrmthPymt, 
                                jumBlnP, 
                                statusD) 
                                    VALUES (" .
            tosql($potonganStart, "Text") . ", " .
            tosql($ID, "Text") . ", " .
            tosql($loanType, "Text") . ", " .
            tosql($userCreated, "Text") . ", " .
            tosql($today, "Text") . ", " .
            tosql(1, "Number") . ", " .
            tosql($ptjID, "Text") . ", " .
            tosql($yearStart, "Text") . ", " .
            tosql($monthStart, "Text") . ", " .
            tosql($lastyrmthPymt, "Text") . ", " .
            tosql($jumBlnP, "Text") . ", " .
            tosql(1, "Number") . ")";

        $rs = &$conn->Execute($sSQL);

        $strActivity = $_POST['Submit'] . 'Tambah Potongan Gaji Baru Pada Anggota - ' . $ID;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

        if ($rs) {
            echo '<script>alert("Potongan sudah ditambah ke dalam sistem."); window.opener.location.reload(); window.close();</script>';
            exit;
        }
    } else {
        echo '<script>alert("Please fill in all required fields.");</script>';
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
    <div class="m-3"><b>Masukkan Potongan Baru</b></div>
    <div class="m-3">
        <form method="POST" action="">
            <table class="table table-sm table-striped">
                <tr class="table-primary">
                    <td><b>Mula Potongan (Tahun/Bulan)</b></td>
                    <td><b>Akhir Potongan (Tahun/Bulan)</b></td>
                    <td><b>Jenis/Kod Potongan</b></td>
                    <td align="left"><b>Potongan Bulanan (RP)</b></td>
                    <td align="left"><b>PTJ</b></td>
                    <td>&nbsp;</td>
                </tr>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name="potonganStart" maxlength="6" class="form-control" placeholder="YYYYMM" required pattern="\d{6}">
                        </td>
                        <td>
                            <input type="text" name="lastyrmthPymt" maxlength="6" class="form-control" placeholder="YYYYMM" required>
                        </td>
                        <td>
                            <select class="form-select" name="loanType" required>
                                <option value="">- Semua -</option>
                                <?php
                                foreach ($objList as $index => $name) {
                                    $selected = ($loanType == $objVal[$index]) ? 'selected' : '';
                                    echo "<option value='{$objVal[$index]}' {$selected}>{$name}</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td align="right">
                            <input type="text" name="jumBlnP" class="form-control" required>
                        </td>
                        <td>
                            <select class="form-select" name="ptjID" required>
                                <option value="">- Semua -</option>
                                <?php
                                foreach ($ptjList as $index => $name) {
                                    $selected = ($ptjID == $ptjVal[$index]) ? 'selected' : '';
                                    echo "<option value='{$ptjVal[$index]}' {$selected}>{$name}</option>";
                                }
                                ?>
                            </select>
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