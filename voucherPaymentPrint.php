<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: voucherPaymentPrint.php
*			Date 		: 24/05/2024
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

        .Mainlogo {
            position: absolute;
        }

        .AlamatPengirim {
            margin-left: 17%;
            border-spacing: 3px;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
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
            font-family: Arial, Helvetica, sans-serif;
        }
        .tr-space{
            background: #d3d3d3;
        }
        .tr-kod-rujukan{
            font-weight: bold;
            word-spacing: 5px;
        }
        .date-stylish{
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            margin-top: 4%;
            float: right;
        }
        .bor-penerima {
            border-spacing: 3px;
            margin-top: 3%;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .header-border{
            border: groove 3px;
        }
        .body-acc-num {
            margin-top: 2%;
            border-collapse: separate;
            border-spacing: 3px;
            margin-bottom: 3%;
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
        .position-table{
            width: 100%;
        }
        .bottom {
            position: fixed;
            bottom: 10px;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
';

if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));     
$Gambar= "upload_images/".$pic;

print '
<body>
<div class="form-container">
    <!---------Logo/Address/Watermark Resit-------->
    <div class="Mainlogo"><img id="elImage" src="'.$Gambar.'" style="height: 120px; width: 120px;" alt="Logo Koperasi"></div>
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
        <tr class="tr-space"><td>Voucher Bayaran Anggota</td></tr>
        <tr class="tr-kod-rujukan"><td>' . $no_baucer . '</td></tr>
    </table>
    <!---------Tanggal---------->
    <table class="date-stylish">
    <tr>
        <td>Tanggal : </td>
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
    $clsRP->setValue($jumlah);
    $strTotal = ucwords($clsRP->getValue()) . ' Sahaja.';
}
$jumlah = number_format($jumlah, 2);
print '
    </table>
    <!-------Bank Anggota ------->
    <div class="body-acc-num">Bank Anggota: ' . $accTabungan . ' (' . $bankname . ')</div>
    <!-----div class="body-acc-num">Sebanyak RP <u><b>' . $jumlah . '</u></b> Ringgit <u><b>' . $strTotal . '</u></b></div>';

print '
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td><b>Bil</b></td>
                <td><b>Perkara</b></td>
                <td nowrap="nowrap"colspan="2" align="right"><b>Jumlah (RP)</b></td>
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
        $clsRP->setValue($jumlah);
        $strTotal = ucwords($clsRP->getValue());
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
    <!-----------Cara Bayar/No. Siri/Tanggal Bayaran------------->
    <table class="position-table">
	    <tr><td colspan="4">Catatan : ' . $catatan . '</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
	    <tr>
            <td colspan="3"></td>
            <td align="right">b.p [NAMA KOPERASI]</td>
        </tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <td nowrap="nowrap">&nbsp;</td>
	    <tr>
			<td nowrap="nowrap"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Disemak</td></tr></table></td>
			<td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
			<td nowrap="nowrap" colspan="4"align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Diluluskan</td></tr></table></td>
		</tr>
        <tr><td colspan="4"><hr size="1px"/></td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
            <td nowrap="nowrap">Tanggal : _______________________</td>
        </tr>
        <tr><td colspan="4"><hr size="1px" /></td></tr>
        <tr><td nowrap="nowrap" colspan="3">&nbsp;</td></tr>
    </table>
    
    <!---------Watermark on the bottomless page---------->
    </div>
    </body>
</html>

<script>window.print();</script>
</body></html>';