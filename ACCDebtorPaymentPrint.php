<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCDebtorPaymentPrint.php
 *			Date 		: 27/7/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email, koperasiID FROM setup
        WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$coopName = $rss->fields('name');
$address1 = $rss->fields('address1');
$address2 = $rss->fields('address2');
$address3 = $rss->fields('address3');
$address4 = $rss->fields('address4');
$noPhone = $rss->fields('noPhone');
$email = $rss->fields('email');
$koperasiID = $rss->fields('koperasiID');

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$header =
    '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
    . '<html>'
    . '<head>'
    . '<title>' . $emaNetis . '</title>'
    . '<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">'
    . '<meta http-equiv="pragma" content="no-cache">'
    . '<meta http-equiv="expires" content="0">'
    . '<meta http-equiv="cache-control" content="no-cache">'
    . '<LINK rel="stylesheet" href="images/mail.css" >'
    . '</head>'
    . '<body>';

// Determining resit that are bulk payment only ------START
$subQuery = "
SELECT RVNo 
FROM cb_payments 
GROUP BY RVNo 
HAVING COUNT(RVNo) > 1
";

$sSQL = "";
$sWhere = " a.RVNo IN ($subQuery) OR a.invNo IS NULL";
$sWhere = " WHERE (" . $sWhere . ")";

// sql select dari table mana 
$sSQL = "SELECT a.RVNo
    FROM cb_payments a 
    LEFT JOIN generalacc b ON a.batchNo = b.ID
    ";

$sSQL     = $sSQL . $sWhere . " group by RVNo order by RVNo desc";
$GetRvBulk  = &$conn->Execute($sSQL);

$rvBulkList = array();
if ($GetRvBulk->RowCount() <> 0) {
    while (!$GetRvBulk->EOF) {
        array_push($rvBulkList, $GetRvBulk->fields('RVNo'));
        $GetRvBulk->MoveNext();
    }
}
// Determining resit that are bulk payment only ------END

// Determining resit that pay opening balance only ------START
$subQuery = "
SELECT RVNo 
FROM cb_payments 
GROUP BY RVNo 
HAVING COUNT(RVNo) = 1
";

$sSQL = "";
$sWhere = " a.RVNo IN ($subQuery) AND a.invNo = ''";
$sWhere = " WHERE (" . $sWhere . ")";

// sql select dari table mana 
$sSQL = "SELECT a.RVNo
    FROM cb_payments a 
    LEFT JOIN generalacc b ON a.batchNo = b.ID
    ";

$sSQL     = $sSQL . $sWhere . " group by RVNo order by RVNo desc";
$GetRvOpenbal  = &$conn->Execute($sSQL);

$rvOpenbalList = array();
if ($GetRvOpenbal->RowCount() <> 0) {
    while (!$GetRvOpenbal->EOF) {
        array_push($rvOpenbalList, $GetRvOpenbal->fields('RVNo'));
        $GetRvOpenbal->MoveNext();
    }
}
// Determining resit that pay opening balance only ------END

// echo '<pre>';
// print_r($rvOpenbalList);
// echo '</pre>';

if ($id) {
    $sql = "SELECT a.*,b.* FROM cb_payments a, generalacc b WHERE a.companyID = b.ID and a.RVNo = '" . $id . "'";

    $rs             = $conn->Execute($sql);
    $RVNo             = $rs->fields(RVNo);


    $tarikh_RV         = $rs->fields(tarikh_RV);
    $tarikh_RV         = substr($tarikh_RV, 8, 2) . "/" . substr($tarikh_RV, 5, 2) . "/" . substr($tarikh_RV, 0, 4);
    $tarikh_RV         = toDate("d/m/y", $rs->fields(tarikh_RV));

    $nama             = $rs->fields(name);
    $companyID        = $rs->fields('companyID');

    $disedia        = $rs->fields('disedia');
    $disedia1        = dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
    $sedia             = strtoupper(strip_tags($disedia1));

    $disemak        = $rs->fields('disemak');
    $disemak1        = dlookup("users", "name", "userID=" . tosql($disemak, "Text"));
    $semak             = strtoupper(strip_tags($disemak1));

    $keranisemak    = dlookup("generalacc", "name", "ID=" . tosql($companyID, "Number"));
    $departmentAdd    = dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));

    $address         = ucwords(strip_tags($departmentAdd));
    $description     = $rs->fields('description');
    $invNom            = $rs->fields(invNo);

    $tarikh_INV        = dlookup("cb_invoice", "tarikh_inv", "invNo=" . tosql($invNom, "Text"));
    $tarikh_INV     = toDate("d/m/y", $tarikh_INV);

    $amt             = $rs->fields(outstandingbalance);
    $kod_bank        = $rs->fields('kod_bank');
    $kod_bank1        = dlookup("generalacc", "name", "ID=" . tosql($kod_bank, "Number"));
    $accBank        = dlookup("generalacc", "f_noakaun", "ID=" . tosql($kod_bank, "Number"));

    $sqltotal       = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE docNo = '" . $id . "'";
    $rstotal        = $conn->Execute($sqltotal);
    $jumlah         = $rstotal->fields(tot);

    $sql2           = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = " . tosql($RVNo, "Text") . " ORDER BY ID";
    $rsDetail       = $conn->Execute($sql2);

    $invLhdn        = dlookup("cb_invoice", "invLhdn", "invNo=" . tosql($invNom, "Text"));
    $invComp        = dlookup("cb_invoice", "invComp", "invNo=" . tosql($invNom, "Text"));
}

print '
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }

        .Mainlogo {
            position: absolute;
        }

        .AlamatPengirim {
            margin-left: 18%;
            border-spacing: 3px;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }

        .resit-statement {
            float: right;
            text-wrap: nowrap;
            text-align: center;
            margin-top: -130px;
            border-collapse: separate;
            border-spacing: 10px;
            width: 20%;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }
        .tr-space{
            background: #d3d3d3;
        }
        .tr-kod-rujukan{
            font-weight: bold;
            word-spacing: 5px;
        }
        .word-trans{
            text-wrap: nowrap;
            text-align: center;
            font-weight: bold;
            margin-top: 2%;
            margin-bottom: 2%;
        }
        .date-stylish{
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
            margin-top: 4%;
            float: right;
        }
        .stylish-date{
            float:right;
            margin-top: 5.5%;
        }
        .bor-penerima {
            margin-top: 5%;
            margin-bottom: 3%;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
            max-width: 50%;
            table-layout: fixed; /* Ensures a fixed layout */
        }
        .bor-penerima td {
            vertical-align: top; /* Aligns content to the top */
            word-wrap: break-word; /* Allows long words to wrap */
            text-align: justify; /* Apply justification */
        }
        .line-trans{
            border: groove 2px;
            margin-bottom: 3%;
        }
        .header-border{
            margin-top:2%;
            border: solid 2px;
        }
        .bayar-style{
            margin-top: 2%;
            margin-right: 65%;
        }
        .no-siri{
            margin-left:  40%;
            margin-top: -20px;
            margin-right: 30%;
        }
        .date-bayar-stylish{
            float: right;
            margin-top: -20px;
        }
        .stylish-catat{
            margin-top: 3%;
        }
        .td-thick-font{
            font-weight: bold:
        }
        .bottom {
            position: fixed;
            bottom: 10px;
            text-align: center;
            width: 100%;
        }
        .stylish-bor-top{
            border: 1.5px groove;
            margin-top:3%;
            margin-botttom:3%;
        }
        .stylish-kerani{
            margin-top:1%;
            margin-bottom:3%;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
            table-layout: fixed;
        }
        .stylish-kerani td {
            vertical-align: top; /* Aligns content to the top */
            word-wrap: break-word; /* Allows long words to wrap */
			text-align: justify; /* Apply justification */
        }
    </style>
</head>
';

if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));
$Gambar = "upload_images/" . $pic;

print '
<body>
<div class="form-container">
    <!---------Logo/Address/Watermark Resit-------->
    <div class="Mainlogo"><img id="elImage" src="' . $Gambar . '" style="height: 120px; width: 120px;" alt="Logo Koperasi"></div>
    <table class="AlamatPengirim">
        <tr><td>' . $coopName . '</td></tr>
        <tr><td>' . ucwords(strtolower($address1)) . '</td></tr>
        <tr><td>' . ucwords(strtolower($address2)) . '</td></tr>
        <tr><td>' . ucwords(strtolower($address3)) . '</td></tr>
        <tr><td>' . ucwords(strtolower($address4)) . '</td></tr>
        <tr><td>TEL: ' . $noPhone . ' </td></tr>
        <tr><td>EMEL: ' . $email . '</td></tr>
        </table>
    <table class="resit-statement">
        <tr class="tr-space"><td>Resit Rasmi</td></tr>
        <tr ><td><b>' . $RVNo . '</b></td></tr>
    </table>
    <table class="stylish-date">
        <tr>
            <td><b>TARIKH RESIT</b></td>
            <td>:&nbsp;</td>
            <td>' . $tarikh_RV . '</td>
        </tr>
';
if (in_array($RVNo, $rvBulkList)) {
} elseif (in_array($RVNo, $rvOpenbalList)) {
    print '
    <tr>
        <td nowrap="nowrap"><b>AMAUN PEMBUKA</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>RM&nbsp;' . number_format($amt, 2) . '</td>
    </tr>
';
} else {
    print '
    <tr>
        <td nowrap="nowrap"><b>NO INVOIS</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>' . $invNom . '</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>TARIKH INVOIS</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td nowrap="nowrap">' . $tarikh_INV . '</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>AMAUN INVOIS</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>RM&nbsp;' . number_format($amt, 2) . '</td>
    </tr>
';
}
print '
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr><td><b>KEPADA: </b></td></tr>
    <tr><td>' . ucwords(strtolower($keranisemak)) . '</td></td>';

$addressLines = explode(',', $address);
foreach ($addressLines as $line) {
    print '<tr><td>' . trim($line) . '</td></tr>';
}

print '
    <tr><td colspan="3">&nbsp;</td></tr>
    </table>

    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td nowrap="nowrap"><b>BIL</b></td>
    ';
if (in_array($RVNo, $rvBulkList)) {
    print '
                <td nowrap="nowrap"><b>NO INVOIS</b></td>
    ';
}
print '
                <td nowrap="nowrap"><b>CARA BAYARAN</b></td>
                <td nowrap="nowrap"><b>KETERANGAN</b></td>
                <td nowrap="nowrap" align="right"><b>AMAUN (RP)</b></td>
            </tr>
    </thead>';

if ($rsDetail->RowCount() <> 0) {
    $i = 1;
    while (!$rsDetail->EOF) {

        $accNom     = $rsDetail->fields(deductID);
        $accN         = $rsDetail->fields(deductID);
        $desc_akaun = $rsDetail->fields(desc_akaun);
        $cara_b     = $rsDetail->fields(cara_bayar);

        if (in_array($RVNo, $rvBulkList)) {
            $invNom = $rsDetail->fields(pymtReferC);
        }

        $keterangan_resit = $rsDetail->fields(keterangan);

        $accNombor     = dlookup("generalacc", "name", "ID=" . $accN);
        $accdet     = dlookup("generalacc", "name", "ID=" . $accNom);
        $carabayar     = dlookup("general", "name", "ID=" . $cara_b);

        $totPymt = number_format($rsDetail->fields(pymtAmt), 2);
        print
            '<tr>
				<td>' . $i . '</td>
        ';
        if (in_array($RVNo, $rvBulkList)) {
            print '
				<td nowrap="nowrap">' . $invNom . '</td>
        ';
        }
        print '
				<td nowrap="nowrap">' . $carabayar . '</td>
                <td style="text-align: justify;">' . $desc_akaun . '</td>
				<td nowrap="nowrap" align="right">' . $totPymt . '</td>
			</tr>';
        $jumlah1 += $rsDetail->fields(pymtAmt);
        $baki = $amt - $jumlah1;
        $i++;
        $rsDetail->MoveNext();
    }
    if ($jumlah1 <> 0) {
        $clsRM->setValue($baki);
        $clsRM->setValue($jumlah1);
        $strTotal = ucwords($clsRM->getValue());
    }
}

print '
<tr><td colspan="6">&nbsp;</td></tr>
';

$colspan = in_array($RVNo, $rvBulkList) ? 4 : 3;
print '
<tr>				
    <td nowrap="nowrap" align="right" colspan="' . $colspan . '"><b>JUMLAH</b></td>
    <td nowrap="nowrap" align="right"><b>RM ' . number_format($jumlah1, 2) . '</b></td>
</tr>
';

if (in_array($RVNo, $rvBulkList)) {
} else {
    print '
<tr>
<td nowrap="nowrap" align="right" colspan="3"><b>JUMLAH BAKI</b></td>
<td nowrap="nowrap" align="right"><b>RM ' . number_format($baki, 2) . '</b></td>
</tr>
';
}
print '
</table>
<tr><td colspan="5">&nbsp;</td></tr>

    <!-----------Jumlah Dalam Perkataan------------->
    <table class="stylish-kerani">
        <tr>
            <td nowrap="nowrap"><b>JUMLAH DALAM PERKATAAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($strTotal)) . ' Ringgit Malaysia Sahaja.</td>
        </tr>

    <!-----------Kerani/Akaun/Nama Bank------------->
        <tr>
            <td><b>DISEDIAKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($sedia)) . '</td>
        </tr>
        <tr>
            <td><b>DICEK OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>' . ucwords(strtolower($semak)) . '</td>
        </tr>
    </table>

    <center>
        <div class="bottom"><hr size="1px">
            <b>INI ADALAH CETAKAN KOMPUTER DAN TIDAK PERLU DITANDATANGAN</b>
        </div>
    </center>
    </div>
    </body>
</html>

<script>window.print();</script>

<!--- Add style for footer on print --->
<style>
  @media print {
    .bottom {
      position: fixed;
      bottom: 0;
      width: 100%;
      font-size: 12px; /* Adjust as needed */
    }
    body {
      margin-bottom: 50px; /* Make sure there is enough space for the footer */
    }
  }
</style>

</body></html>';