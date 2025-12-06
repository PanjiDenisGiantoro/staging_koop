<?php
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	resitPaymentPrint.php
 *          Date 		: 	24/05/2024
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

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}


$header =
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
.'<html>'
.'<head>'
.'<title>'.$emaNetis.'</title>'
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>'
.'<body>';

$footer = '
<script>window.print();</script>
</body></html>';

if($ID){
	$sql = "SELECT a.*,b.newIC,b.memberID,b.address, b.city, b.postcode, b.stateID, b.departmentID, c.name FROM  resit a, userdetails b,users c WHERE b.userID = c.userID and a.bayar_nama = b.memberID and no_resit = ".tosql($ID, "Text");
	$rs = $conn->Execute($sql);

	$no_resit 		= $rs->fields('no_resit');
	$tarikh_resit 	= toDate("d/m/y",$rs->fields('tarikh_resit'));
	$bayar_kod 		= $rs->fields('bayar_kod');
	$bayar_nama 	= $rs->fields('name');
    $noIC           = $rs->fields('newIC');
	$no_anggota 	= $rs->fields('memberID');
	$deptID			=  $rs->fields('departmentID');
	$departmentAdd	= dlookup("general", "b_Address", "ID=" . tosql($deptID, "Number"));
	$alamat 		= strtoupper(strip_tags($departmentAdd));
    $departmentname	= dlookup("general", "name", "ID=" . tosql($deptID, "Number"));
	//-----------------
	$cara_bayar 	= $rs->fields('cara_bayar');
	$kod_siri 		= $rs->fields('kod_siri');
	$tarikh 		= toDate("d/m/y",$rs->fields('tarikh'));
	$akaun_bank 	= $rs->fields('akaun_bank');
	$kerani 		= $rs->fields('kerani');
	$catatan 		= $rs->fields('catatan');

	$accTabungan	=  dlookup("userdetails", "accTabungan", "userID=" . tosql($no_anggota, "Number"));
	$bankID			=  dlookup("userdetails", "bankID", "userID=" . tosql($no_anggota, "Number"));
	$bankname		=  dlookup("general", "name", "ID=" . $bankID);

	$sqltotal 	= "SELECT SUM(pymtAmt) AS tot FROM transaction WHERE docNo = '".$ID."'";
	$rstotal 	= $conn->Execute($sqltotal);
	$jumlah 	= $rstotal->fields('tot');
	
	$sql2 = "SELECT * FROM transaction WHERE docNo = ".tosql($ID, "Text")." ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);

}


print'
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
            margin-left: 18%;
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
            margin-top: 3%;
            border-collapse: separate;
            border-spacing: 3px;
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

print'
<body>
<div class="form-container">
    <!---------Logo/Address/Watermark Resit-------->
    <div class="Mainlogo"><img id="elImage" src="'.$Gambar.'" style="height: 120px; width: 120px;" alt="Logo Koperasi"></div>
    <table class="AlamatPengirim">
        <tr><td>'.$coopName.'</td></tr>
        <tr><td>'.ucwords(strtolower($address1)).'</td></tr>
        <tr><td>'.ucwords(strtolower($address2)).'</td></tr>
        <tr><td>'.ucwords(strtolower($address3)).'</td></tr>
        <tr><td>'.ucwords(strtolower($address4)).'</td></tr>
        <tr><td>TEL: '.$noPhone.' </td></tr>
        <tr><td>EMEL: '.$email.'</td></tr>
        </table>
    <table class="resit-statement">
        <tr class="tr-space"><td>Resit Rasmi</td></tr>
        <tr class="tr-kod-rujukan"><td>'.$no_resit.'</td></tr>
    </table>
    <!---------Tanggal---------->
    <table class="date-stylish">
    <tr>
        <td>Tanggal Resit : </td>
        <td>'.$tarikh_resit.'</td>
    </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr><td>Diterima Daripada:</td></tr>

    <tr><td>'.$bayar_nama.'('.$no_anggota.')</td></tr>
    <tr><td>'.$noIC.'</td></tr>
    <tr><td>'.ucwords(strtolower($departmentname)).'</td></tr>';
    
    /*--------------Alamat Department----------------- */
    $addressLines = explode(',', $alamat);
        foreach ($addressLines as $line) {
    $formattedLine = ucwords(strtolower(trim($line)));
        print '<tr><td>' . $formattedLine . '</td></tr>';
    }

            /*----------Call Word total Ringgit-----------*/
            if($jumlah<>0){
                $clsRP->setValue($jumlah);
                $strTotal = ucwords($clsRP->getValue()).' Sahaja.';
            }
            $jumlah = number_format($jumlah,2);            
    print'
    </table>
    <!-------Bank Anggota ------->
    <div class="body-acc-num">Bank Anggota: '.$accTabungan.' ('.$bankname.')</div>
    <div class="body-acc-num">Sebanyak RP <u><b>'.$jumlah.'</u></b> Ringgit <u><b>'.$strTotal.'</u></b></div>';

    print'
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td><b>Bil</b></td>
                <td><b>Perkara</b></td>
                <td colspan="2" align="right"><b>Jumlah (RP)</b></td>
            </tr>
    </thead>';

    $jumlah = 0;
    if ($rsDetail->RowCount() <> 0){
    $i=0;
	    while (!$rsDetail->EOF) {
	    $code = dlookup("general", "code", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
	    $name = dlookup("general", "name", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
			print '
			<tr>
				<td nowrap="nowrap" align="left">('.++$i.')</td>
				<td nowrap="nowrap" align="left">'.$name.'</td>
				<td nowrap="nowrap" align="right" colspan="2">'; print  number_format($rsDetail->fields('pymtAmt'),2); print'</td>
			</tr>';
	$jumlah += $rsDetail->fields('pymtAmt');
	$rsDetail->MoveNext();
	}
}

print'
    <tr><td colspan="4">&nbsp;</td></tr>

    <tr>     
		<td nowrap="nowrap" colspan="2"></td>
        <td nowrap="nowrap"align="right">CUKAI</td>
		<td valign="top" align="right">0.00</td>
	</tr>
    <tr>
		<td nowrap="nowrap" colspan="2"></td>
		<td nowrap="nowrap" align="right"><b>JUMLAH</b></td>
		<td nowrap="nowrap" align="right">'; print number_format($jumlah,2); print '</td>
	</tr>
    </table>
    <!-----------Cara Bayar/No. Siri/Tanggal Pembayaran------------->
    <div class="bayar-style">Cara Bayaran : <u>'.$cara_bayar.'</u></div>
    <div class="no-siri">Kod & No. Siri : <u>'.$kod_siri.'</u></div>
    <div class="date-bayar-stylish">Tanggal Pembayaran : <u>'.$tarikh.'</u></div>
    <div class="stylish-catat">Catatan :'.$catatan.'</div>
    <center>
    <div class="bottom">
        <hr size="1">
        <b>INI ADALAH CETAKAN KOMPUTER DAN TIDAK PERLU DITANDATANGAN</b>
    </div>
    </center>
    </div>';

print $footer;
?>