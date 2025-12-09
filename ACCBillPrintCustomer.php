<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCBillPrintCustomer.php
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

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

$header =
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
.'<html>'
.'<head>'
.'<title>'.$emaNetis.'</title>'
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE8U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>';

if($id){
	$sql = "SELECT * FROM billacc WHERE no_bill = '".$id."'";
	$rs = $conn->Execute($sql);

	$no_bill 		= $rs->fields(no_bill);
	$tarikh_bill 	= toDate("d/m/y",$rs->fields(tarikh_bill));
	$tarikh 		= toDate("d/m/y",$rs->fields(tarikh));

    if (dlookup("billacc", "PINo", "no_bill=".tosql(($no_bill),"Text")) <> "") {
    $PINo           = $rs->fields(PINo);
    $tarikhPI       = toDate("d/m/y",dlookup("cb_purchaseinv", "tarikh_PI", "PINo=".tosql(($PINo),"Text")));
    $totalPI        = number_format(dlookup("cb_purchaseinv", "outstandingbalance", "PINo=".tosql(($PINo))) - dlookup("cb_purchaseinv", "balance", "PINo=".tosql(($PINo))));
    }

	$bayar_kod 		= $rs->fields(bayar_kod);
	$bayar_nama		= $rs->fields(name);
	$no_anggota 	= $rs->fields(memberID);

	$carabayar 		= $rs->fields(cara_byr);
	$cara_byr 		= dlookup("general", "name", "ID=".$carabayar);
	
	$Cheque			= $rs->fields(Cheque);
	$akaun_bank 	= $rs->fields(akaun_bank);
	$kod_project 	= $rs->fields(kod_project);
	$kod_jabatan	= $rs->fields(kod_jabatan);	
	$keterangan		= $rs->fields(keterangan);
	$diterima_drpd	= $rs->fields(diterima_drpd);
	$namac 			= dlookup("generalacc", "name", "ID=".$diterima_drpd);
	$departmentAdd	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($diterima_drpd, "Number"));
	$alamat 		= strtoupper(strip_tags($departmentAdd));

	$catatan 		= $rs->fields(catatan);

	$master 		= $rs->fields(masteraccount);
	$masterA 		= dlookup("generalacc", "name", "ID=".$master);

	$kod_bank 		= $rs->fields(kod_bank);
	$kod_bankA 		= dlookup("generalacc", "name", "ID=".$kod_bank);

	$sqltotal = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE addminus IN (0) AND docNo = '".$id."'";
	$rstotal = $conn->Execute($sqltotal);
	$jumlah = $rstotal->fields(tot);
	
	$sql2 = "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = ".tosql($no_bill, "Text")." ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);

    $kerani		    = $rs->fields(kerani);
	$kerani		    = dlookup("users", "name", "userID=" . tosql($kerani, "Text"));
	$kerani		    = strtoupper(strip_tags($kerani));
    $disedia		= $rs->fields(disedia);
	$disedia		= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
	$disedia		= strtoupper(strip_tags($disedia));
	$disahkan 		= $rs->fields(disahkan);
	$disahkan		= dlookup("users", "name", "userID=" . tosql($disahkan, "Text"));
	$disahkan		= strtoupper(strip_tags($disahkan));
}

print'
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            padding-bottom: 10px; /* Adds space above the .bottom text */
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
        .word-trans{
            text-wrap: nowrap;
            text-align: center;
            font-weight: bold;
            margin-top: 2%;
            margin-bottom: 2%;
        }
        .date-stylish{
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
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
            font-family: Arial, Helvetica, sans-serif;
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
            margin-top:3%;
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
            position: relative;
            bottom: 10px;
            text-align: center;
            width: 100%;
        }
        .bottom hr {
            padding: 3px 0;
            margin: 0; /* Remove margin from hr */
            border-width: 1px; /* Set a minimal border width */
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

if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));     
$Gambar= "upload_images/".$pic;

print'
<body>
<div class="form-container">
    <!---------Logo/Address/Watermark Resit-------->
    <div class="Mainlogo"><img id="elImage" src="'.$Gambar.'" style="height: 120px; width: 120px;" alt="Logo Koperasi"></div>
    <div class="lines">
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
        <tr class="tr-space"><td>Bil Rasmi</td></tr>
        <tr ><td><b>'.$no_bill.'</b></td></tr>
    </table>
    <table class="stylish-date">
        <tr>
            <td><b>TARIKH BIL</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_bill.'</td>
        </tr>
        ';
        if (dlookup("billacc", "PINo", "no_bill=".tosql(($no_bill),"Text")) <> "") {
        print'
        <tr>
            <td><b>NO PI</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$PINo.'</td>
        </tr>
        <tr>
            <td><b>TARIKH PI</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikhPI.'</td>
        </tr>
        <tr>
            <td><b>AMAUN PI</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>RP '.$totalPI.'</td>
        </tr>
        ';
        }
    print'
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr>
    <td nowrap="nowrap"><b>DITERIMA DARIPADA: </b></td>
    <td>&nbsp;:&nbsp;</td>
    <td>'.ucwords(strtolower($namac)).'</td></td></tr>
    <tr>
        <td nowrap="nowrap"><b>ALAMAT</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.ucwords(strtolower($alamat)).'</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>BANK</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.$kod_bankA.'</td>
    </tr>
    <tr>
        <td><b>KETERANGAN</b></td>
		<td>&nbsp;:&nbsp;</td>
        <td>'.$keterangan.'</td>
    </tr>
    <tr>
        <td><b>CARA BAYARAN</b></td>
		<td>&nbsp;:&nbsp;</td>
        <td>'.$cara_byr.'</td>
    </tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    </table>

    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
				<td nowrap="nowrap"><b>BIL</b></td>
                <td nowrap="nowrap"><b>JABATAN</b></td>
                <td nowrap="nowrap"><b>PROJEK</b></td>
                <td nowrap="nowrap"><b>KETERANGAN</b></td>
                <td nowrap="nowrap" align="right"><b>AMAUN (RP)</b></td>
            </tr>
    </thead>';

    if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
			
		$accNom 	    = $rsDetail->fields(deductID);
		$accN 		    = $rsDetail->fields(deductID);
		$codeproject 	= $rsDetail->fields(kod_project);
		$codejabatan 	= $rsDetail->fields(kod_jabatan);
		$desc_akaun     = $rsDetail->fields(desc_akaun);
		$accNombor 	= dlookup("generalacc", "code", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);
		$kodprojek 	= dlookup("generalacc", "name", "ID=".$codeproject);
		$kodjabatan = dlookup("generalacc", "name", "ID=".$codejabatan);
		$cukai  	= dlookup("generalacc", "name", "ID=".$taxing);

		$totPymt = number_format($rsDetail->fields(pymtAmt),2);
		print
			'<tr>
				<td>'.$i.'</td>
				<td>'.$kodjabatan.'</td>
				<td>'.$kodprojek.'</td>
                <td style="text-align: justify;">'.$desc_akaun.'</td>
				<td nowrap="nowrap" align="right">'.$totPymt.'</td>
			</tr>';
			$jumlah1 += $rsDetail->fields(pymtAmt);
            $i++;
			$rsDetail->MoveNext();
			}
			if($jumlah1<>0){
			$clsRP->setValue($jumlah1);
			$strTotal1 = strtoupper($clsRP->getValue());
			}
		}
        if (dlookup("billacc", "PINo", "no_bill=".tosql(($no_bill),"Text")) <> "") {
            $baki = str_replace(",", "", $totalPI) - str_replace(",", "", $jumlah1);
        }

print '
<tr><td colspan="5">&nbsp;</td></tr>
<tr>				
    <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH BAYARAN</b></td>
    <td nowrap="nowrap" align="right"><b>RP '.number_format($jumlah1,2).'</b></td>
</tr>';
if (dlookup("billacc", "PINo", "no_bill=".tosql(($no_bill),"Text")) <> "") {
    print'
        <tr>
            <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH BAKI PI</b></td>
            <td nowrap="nowrap" align="right"><b>RP '.number_format($baki,2).'</b></td>
        </tr>
    ';
}
print'
</table>

    <!-----------Kerani/Akaun/Nama Bank------------->
        <table class="stylish-kerani">
        <tr>
            <td><b>DIMASUKKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($kerani)).'</td>
        </tr>
        <tr>
            <td><b>DISAHKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($disahkan)).'</td>
        </tr>
    </table>
</div>

    <div class="bottom">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td nowrap="nowrap">&nbsp;</td>
                <td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_________________________<br /><span style="margin-top: 10px; display: inline-block;">Pengerusi /<br />Timbalan Pengerusi</td></tr></table></td>
                <td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_________________________<br /><span style="margin-top: 10px; display: inline-block;">Setiausaha /<br />Timbalan Setiausaha</td></tr></table></td>
                <td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_________________________<br /><span style="margin-top: 10px; display: inline-block;">Bendahari /<br />Timbalan Bendahari</td></tr></table></td>
            </tr>
        </table>
    </div>
        </div>

<script>window.print();</script>

<style type="text/css">
@media print
{
        body, html{
            height:100%;
            padding:0;
        }

        .form-container {
            position:relative;
            min-height:100%;
        }

        .lines{
            padding-bottom:200px;
        }

        .bottom {
            font-size: 13px;
            position: absolute; 
            bottom: 0;
            left: 4px; 
            right: 4px;
        }
}
</style>

</body></html>';
?>