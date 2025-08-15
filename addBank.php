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

$sActionFileName = "?vw=memberUpdate&mn='" . $mn . "&tabb=5&ID='" . $ID . "'";

$bankList = array();
$bankVal  = array();
$Getbank = ctGeneral("", "Z");
if ($Getbank->RowCount() <> 0) {
    while (!$Getbank->EOF) {
        array_push($bankList, $Getbank->fields(name));
        array_push($bankVal, $Getbank->fields(ID));
        $Getbank->MoveNext();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $bankID = $_POST['bankID'];
    $accTabungan = $_POST['accTabungan'];

    if ($bankID && $accTabungan) {

        $sSQL = "INSERT INTO bank (
                                bankID, 
                                accTabungan, 
                                refer) 
                                    VALUES (" .
            tosql($bankID, "Text") . ", " .
            tosql($accTabungan, "Number") . ", " .
            tosql($userID, "Number") . ")";

        $rs = &$conn->Execute($sSQL);

        if ($rs) {
            echo '<script>alert("Bank baru telah berjaya dimasukkan."); window.opener.location.reload(); window.close();</script>';
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
    <style>
        /* Gaya untuk select dengan anak panah */
        select {
            appearance: none;
            /* Hilangkan gaya default */
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 30px;
            /* Ruang untuk anak panah */
            background-color: #fff;
            background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 6"%3E%3Cpath fill="%23666666" d="M0 0l5 6 5-6z"/%3E%3C/svg%3E');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px 6px;
        }
    </style>
    <title>Tambah Bank</title>
</head>

<body>
    <div class="m-3"><b>Masukkan Bank Baru</b></div>
    <div class="m-3">
        <form method="POST" action="">
            <table class="table table-sm table-striped">
                <tr class="table-primary">
                    <td><b>Nama Bank</b></td>
                    <td><b>Nombor Akaun Bank</b></td>
                    <td>&nbsp;</td>
                </tr>
                <tbody>
                    <tr>
                        <td>
                            <div class="custom-select">
                                <select name="bankID" class="form-control" required>
                                    <option value="">-- Sila Pilih Bank --</option>
                                    <?php
                                    for ($i = 0; $i < count($bankList); $i++) {
                                        echo '<option value="' . htmlspecialchars($bankVal[$i]) . '">' . htmlspecialchars($bankList[$i]) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="accTabungan" maxlength="50" class="form-control" required>
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