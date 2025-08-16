<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCResitPrintCustomer.php
*			Date 		: 27/7/2006
*********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

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
.'</head>'
.'<body>';

if($id){
	$sql = "SELECT * FROM  resitacc WHERE no_resit = '". $id."'";
	$rs = $conn->Execute($sql);

	$no_resit 		= $rs->fields(no_resit);
	$tarikh_resit 	= toDate("d/m/y",$rs->fields(tarikh_resit));
	$tarikh 		= toDate("d/m/y",$rs->fields(tarikh));
	$bayar_kod 		= $rs->fields(bayar_kod);
	$bayar_nama 	= $rs->fields(name);
	$no_anggota 	= $rs->fields(memberID);
	$cara_bayar 	= $rs->fields(cara_bayar);
	$Ncara_bayar 	= dlookup("general","name","ID=".tosql($cara_bayar,"Text"));
	$Cheque			= $rs->fields(Cheque);
	$akaun_bank 	= $rs->fields(akaun_bank);
	$kod_project 	= $rs->fields(kod_project);
	$kod_jabatan	= $rs->fields(kod_jabatan);
	$keterangan		= $rs->fields(keterangan);
	$diterima_drpd	= $rs->fields(diterima_drpd);
	$catatan 		= $rs->fields(catatan);

	$kerani			= $rs->fields(kerani);

	$master 		= $rs->fields(masteraccount);
	$masterA 		= dlookup("generalacc", "name", "ID=".$master);

	$kod_bank 		= $rs->fields(kod_bank);
	$kod_bankA 		= dlookup("generalacc", "name", "ID=".$kod_bank);

	$sqltotal = "SELECT sum(pymtAmt) as tot FROM transactionacc WHERE addminus IN (1) AND docNo = '".$id."'";
	$rstotal = $conn->Execute($sqltotal);
	$jumlah = $rstotal->fields(tot);
	
	$sql2 = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = ".tosql($no_resit, "Text")." ORDER BY ID";
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
        <tr ><td><b>'.$no_resit.'</b></td></tr>
    </table>
    <table class="stylish-date">
        <tr>
            <td><b>TARIKH RESIT</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_resit.'</td>
        </tr>
        <tr>
            <td><b>TARIKH TERIMAAN</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh.'</td>
        </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr>
        <td nowrap="nowrap">DITERIMA DARIPADA: </td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.ucwords(strtolower($diterima_drpd)).'</td>
    </tr>
    ';
    if($keterangan <> "") {
        print'
        <tr>
            <td nowrap="nowrap">KETERANGAN</td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.ucwords(strtolower($keterangan)).'</td>
        </tr>
        ';
    }
    if($kod_bankA <> "") {
        print'
        <tr>
            <td nowrap="nowrap">NAMA BANK</td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.ucwords(strtolower($kod_bankA)).'</td>
        </tr>     
        ';
    }
    if($Cheque <> "") {
        print'
        <tr>
            <td nowrap="nowrap">CHEQUE NO</td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.ucwords(strtolower($Cheque)).'</td>
        </tr>
        ';
    }
    if($Ncara_bayar <> "") {
        print'
        <tr>
            <td nowrap="nowrap">CARA BAYARAN</td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.ucwords(strtolower($Ncara_bayar)).'</td>
        </tr>     
        ';
    }
    print'
    </table>';

    print'
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td nowrap="nowrap"><b>BIL</b></td>
                <td><b>KETERANGAN</b></td>
				<td nowrap="nowrap" align="right"><b>AMAUN (RM)</b></td>
            </tr>
    </thead>';

    if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
			
		$accNom 	= $rsDetail->fields(deductID);
		$accN 		= $rsDetail->fields(deductID);
		$codeproject= $rsDetail->fields(kod_project);
		$codejabatan= $rsDetail->fields(kod_jabatan);
		$desc_akaun = $rsDetail->fields(desc_akaun);
		$taxing 	= $rsDetail->fields(taxNo);

		$accNombor 	= dlookup("generalacc", "code", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);
		$kodprojek 	= dlookup("generalacc", "name", "ID=".$codeproject);
		$kodjabatan = dlookup("generalacc", "name", "ID=".$codejabatan);
		$cukai  	= dlookup("generalacc", "name", "ID=".$taxing);
		$totPymt = number_format($rsDetail->fields(pymtAmt),2);

		print
			'<tr>
				<td nowrap="nowrap">'.$i.'</td>
                <td style="text-align: justify;">'.$desc_akaun.'</td>
				<td nowrap="nowrap" align="right">'.$totPymt.'</td>
			</tr>';
			$jumlah1 += $rsDetail->fields(pymtAmt);
			$i++;
			$rsDetail->MoveNext();
			}
			if($jumlah1<>0){
			$clsRM->setValue($jumlah1);
			$strTotal1 = ucwords($clsRM->getValue());
			}
		}

        print '
        <tr><td colspan="6">&nbsp;</td></tr>
        <tr>				
            <td nowrap="nowrap" align="right" colspan="2"><b>JUMLAH</b></td>
            <td nowrap="nowrap" align="right"><b>RM '.number_format($jumlah1,2).'</b></td>
        </tr>
        </table>
        <tr><td colspan="5">&nbsp;</td></tr>
        
            <!-----------Jumlah Dalam Perkataan------------->
            <table class="stylish-kerani">
                <tr>
                    <td nowrap="nowrap"><b>JUMLAH DALAM PERKATAAN</b></td>
                    <td>&nbsp;:&nbsp;</b></td>
                    <td>'.ucwords(strtolower($strTotal1)).' Ringgit Malaysia Sahaja.</td>
                </tr>
        
            <!-----------Kerani/Akaun/Nama Bank------------->
                <tr>
                    <td><b>DISEDIAKAN OLEH</b></td>
                    <td>&nbsp;:&nbsp;</b></td>
                    <td>'.ucwords(strtolower($kerani)).'</td>
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
?>