<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: voucherPaymentView.php
*			Date 		: 24/05/2024
*********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");	
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
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

if ($id) {
    $sql = "SELECT a.*,b.*, c.name 
            FROM  vauchers a, userdetails b,users c 
            WHERE b.userID = c.userID AND a.no_anggota = b.memberID 
            AND no_baucer = '" . $id . "'";
    $rs = $conn->Execute($sql);
    $no_baucer          = $rs->fields('no_baucer');
    $tarikh_baucer      = toDate("d/m/y", $rs->fields('tarikh_baucer'));
    $name               = $rs->fields('name');

    $newIC              = $rs->fields('newIC');
    $kod_caw            = $rs->fields('departmentID');
    $namaCaw            = dlookup("general", "name", "ID=" . tosql($kod_caw, "Text"));
    $departmentAdd      = dlookup("general", "b_Address", "ID=" . tosql($kod_caw, "Text"));
    $no_anggota         = $rs->fields('no_anggota');
    $userID             = $rs->fields('userID');
    $catatan            = $rs->fields('keterangan');

    $accTabungan        =  dlookup("userdetails", "accTabungan", "userID=" . tosql($no_anggota, "Number"));
    $bankID                =  dlookup("userdetails", "bankID", "userID=" . tosql($no_anggota, "Number"));
    $bankname            =  dlookup("general", "name", "ID=" . $bankID);

    $sql2 = "SELECT * FROM transaction WHERE docNo = " . tosql($no_baucer, "Text") . " ORDER BY ID";
    $rsDetail = $conn->Execute($sql2);
}

print '
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .stylish-date{
            float:right;
        }
        .bor-penerima {
            margin-top: 5%;
            margin-bottom: 3%;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            max-width: 50%;
            table-layout: fixed; /* Ensures a fixed layout */
        }
        .bor-penerima td {
            vertical-align: top; /* Aligns content to the top */
            word-wrap: break-word; /* Allows long words to wrap */
            text-align: justify; /* Apply justification */
        }
        .header-border{
            margin-top:2%;
            border: solid 2px;
        }
        .stylish-kerani{
            margin-top:1%;
            margin-bottom:3%;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
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

print '
<body>
<div class="form-container">
    <!---------Doc/Date-------->
    <table class="stylish-date">
        <tr>
            <td><b>No. Voucher Anggota</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$no_baucer.'</td>
        </tr>
    <tr>
        <td><b>Tarikh Voucher</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>' . $tarikh_baucer . '</td>
    </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr><td><b>Bayaran Kepada:</b></td></tr>
    <tr><td>' . ucwords(strtolower($name)) . '(' . $no_anggota . ')</td></tr>
    <tr><td>' . $newIC . '</td></tr>
    <tr><td>' . ucwords(strtolower($namaCaw)) . '</td></tr>';

/*--------------Alamat Department----------------- */
$addressLines = explode(',', $departmentAdd);
foreach ($addressLines as $line) {
    $formattedLine = ucwords(strtolower(trim($line)));
    print '<tr><td>' . $formattedLine . '</td></tr>';
}

/*----------Call Word total Ringgit-----------*/
if ($jumlah <> 0) {
    $clsRM->setValue($jumlah);
    $strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}
$jumlah = number_format($jumlah, 2);
print '
    </table>
    <!-------Bank Anggota ------->
    <div class="body-acc-num"><b>Bank Anggota: </b>' . $accTabungan . ' (' . $bankname . ')</div>
    <!-----div class="body-acc-num">Sebanyak RM <u><b>' . $jumlah . '</u></b> Ringgit <u><b>' . $strTotal . '</u></b></div>';

print '
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td><b>Bil</b></td>
                <td><b>Perkara</b></td>
                <td nowrap="nowrap"colspan="2" align="right"><b>Jumlah (RM)</b></td>
            </tr>
    </thead>';

if ($rsDetail->RowCount() <> 0) {
    $i = 1;
    while (!$rsDetail->EOF) {
        $deductID = $rsDetail->fields(deductID);
        $desc = dlookup("general", "name", "ID=" . $deductID);
        $totPymt = number_format($rsDetail->fields(pymtAmt), 2);
        print
            '<tr>
                <td nowrap="nowrap" align="left">(' . $i++ . ')&nbsp;&nbsp;</td>
                <td nowrap="nowrap">' . $desc . '</td>
                <td nowrap="nowrap" colspan="2" align="right">' . $totPymt . '</td>
            </tr>';
        $jumlah += $rsDetail->fields(pymtAmt);
        $rsDetail->MoveNext();
    }
    if ($jumlah <> 0) {
        $clsRM->setValue($jumlah);
        $strTotal = ucwords($clsRM->getValue());
    }
}

print '
    <tr><td colspan="4">&nbsp;</td></tr>
    <tr>
		<td nowrap="nowrap" colspan="2"></td>
		<td nowrap="nowrap" align="right"><b>JUMLAH</b></td>
		<td nowrap="nowrap" align="right">';
print number_format($jumlah, 2);
print '</td>
	</tr>
    </table>
        <table class="position-table">
	    <tr><td colspan="4">Catatan : ' . $catatan . '</td></tr>
        </table>
    </div>
    </body>
</html>

</body></html>';