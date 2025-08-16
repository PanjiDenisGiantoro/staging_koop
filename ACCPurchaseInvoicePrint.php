<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCPurchaseInvoicePrint.php
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
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>'
.'<body>';

if($id){

    if ($note){
        $sql = "SELECT a.*, b.name as compName, b.ID
        FROM note a
        JOIN generalacc b ON a.companyID = b.ID
        WHERE a.noteNo = '" . $id . "';";
        $rs 			= $conn->Execute($sql);
        $PINo 			= $rs->fields('noteNo');

        $tarikh_PI 		    = $rs->fields('tarikh_note');
        $tarikh_PI 		    = substr($tarikh_note,8,2)."/".substr($tarikh_note,5,2)."/".substr($tarikh_note,0,4);
        $tarikh_PI 		    = toDate("d/m/y",$rs->fields('tarikh_note'));
        
        $outstandingbalance = $rs->fields('pymtAmt');
        $companyID			= $rs->fields('companyID');
        $compName 			= dlookup("generalacc", "name", "ID=" . tosql($companyID, "Number"));

        $disedia			= $rs->fields('keranisedia');
        $disedia1			= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
        $sedia 				= strtoupper(strip_tags($disedia1));
    
        $disemak			= $rs->fields('keranisemak');
        $disemak1			= dlookup("users", "name", "userID=" . tosql($disemak, "Text"));
        $semak 				= strtoupper(strip_tags($disemak1));
    
        $departmentAdd		= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));
    
        $alamat 			= ucwords(strip_tags($departmentAdd));
        $description 		= $rs->fields('catatan');

    } else {
        $sql = "SELECT a.*, b.name as compName, b.ID, c.*
        FROM cb_purchaseinv a
        JOIN generalacc b ON a.companyID = b.ID
        LEFT JOIN cb_purchase c 
            ON (a.purcNo = c.purcNo OR a.purcNo = '' OR a.purcNo IS NULL)
        WHERE a.PINo = '" . $id . "';";
        $rs 			= $conn->Execute($sql);
        $PINo 			= $rs->fields('PINo');

        $tarikh_PI 			= $rs->fields('tarikh_PI');
        $tarikh_PI 			= substr($tarikh_PI,8,2)."/".substr($tarikh_PI,5,2)."/".substr($tarikh_PI,0,4);
        $tarikh_PI 			= toDate("d/m/y",$rs->fields('tarikh_PI'));
    
        $purcNom        	= $rs->fields('purcNo');
        $tarikh_PO			= dlookup("cb_purchase", "tarikh_purc", "purcNo=" . tosql($purcNom, "Text"));
        $tarikh_PO 			= toDate("d/m/y",$tarikh_PO);
    
        $outstandingbalance = $rs->fields('outstandingbalance');
        $nama 				= $rs->fields('name');
        $companyID			= $rs->fields('companyID');
        $compName 			= $rs->fields('compName');
    
        $disedia			= $rs->fields('keranisedia');
        $disedia1			= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
        $sedia 				= strtoupper(strip_tags($disedia1));
    
        $disemak			= $rs->fields('keranisemak');
        $disemak1			= dlookup("users", "name", "userID=" . tosql($disemak, "Text"));
        $semak 				= strtoupper(strip_tags($disemak1));
    
        $departmentAdd		= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));
    
        $alamat 			= ucwords(strip_tags($departmentAdd));
        $description 		= $rs->fields('description');
        $invLhdn 		    = $rs->fields('invLhdn');
        $invComp 		    = $rs->fields('invComp');
    }

	$sqltotal   = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE docNo = '".$id."'";
	$rstotal    = $conn->Execute($sqltotal);
	$jumlah     = $rstotal->fields('tot');
	
    if ($note) {
        $sql2 = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = ".tosql($PINo, "Text")." ORDER BY ID";
        $rsDetail = $conn->Execute($sql2);
    }
    else {
        $sql2 = "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = ".tosql($PINo, "Text")." ORDER BY ID";
        $rsDetail = $conn->Execute($sql2);
    }
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
            margin-top:5%;
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

if ($note) {
    $subject        = "Nota Debit"; 
    $subjectTarikh  = "TARIKH"; 
} else {
    $subject        = "Purchase Invoice";
    $subjectTarikh  = "TARIKH PI"; 
} 

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
        <tr class="tr-space"><td>'.$subject.'</td></tr>
        <tr ><td><b>'.$PINo.'</b></td></tr>
        <tr><td><b>'.$invLhdn.'</b></td></tr>
        <tr><td><b>'.$invComp.'</b></td></tr>
    </table>
    <table class="stylish-date">
        <tr>
            <td><b>'.$subjectTarikh.'</b></td>
            <td>:&nbsp;</td>
            <td>'.$tarikh_PI.'</td>
        </tr>';
        if (dlookup("cb_purchaseinv", "purcNo", "PINo=" . tosql($PINo, "Text")) <> "") {
        print'
		<tr>
			<td><b>NO PO</b></td>
			<td>:&nbsp;</td>
			<td>'.$purcNom.'</td>
		</tr>
        <tr>
			<td align="right"><b>TARIKH PO</b></td>
			<td>:&nbsp;</td>
			<td>'.$tarikh_PO.'</td>
		</tr>
		<tr>
			<td><b>AMAUN PO</b></td>
			<td>:&nbsp;</td>
			<td>RM'.number_format($outstandingbalance,2).'</td>
		</tr>
        ';
        }
    print'
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr><td><b>DITERIMA DARIPADA: </b></td></tr>
    <tr><td>'.ucwords(strtolower($compName)).'</td></td>';
    
    $addressLines = explode(',', $alamat);
        foreach ($addressLines as $line) {
            print '<tr><td>'.trim($line).'</td></tr>';
        }

    print'
    <tr><td colspan="3">&nbsp;</td></tr>
    </table>

    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td nowrap="nowrap"><b>BIL</b></td>
                <td nowrap="nowrap"><b>KETERANGAN</b></td>
                <td nowrap="nowrap" align="center"><b>KUANTITI</b></td>
                <td nowrap="nowrap" align="right"><b>HARGA SEUNIT (RM)</b></td>
                <td nowrap="nowrap" align="right"><b>AMAUN (RM)</b></td>
            </tr>
    </thead>';

    if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
			
		$accNom 	= $rsDetail->fields('deductID');
		$accN 		= $rsDetail->fields('deductID');
		$desc_akaun = $rsDetail->fields('desc_akaun');
		$cara_b 	= $rsDetail->fields('cara_bayar');

		$keterangan_resit 	= $rsDetail->fields('keterangan');


		$accNombor 	= dlookup("generalacc", "code", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);
		$carabayar 	= dlookup("general", "name", "ID=".$cara_b);

		$totPymt = number_format($rsDetail->fields('pymtAmt'),2);
		print
			'<tr>
				<td>'.$i.'</td>
                <td style="text-align: justify;">'.$desc_akaun.'</td>
				<td align="center">'.$rsDetail->fields('quantity').'</td>
				<td align="right">'.$rsDetail->fields('price').'</td>				
				<td align="right">'.$totPymt.'</td>
			</tr>';
			$jumlah1 += $rsDetail->fields('pymtAmt');
			$baki = $outstandingbalance-$jumlah1;
			$i++;
			$rsDetail->MoveNext();
			}
			if($jumlah1<>0){
			$clsRM->setValue($baki);
			$clsRM->setValue($jumlah1);
			$strTotal = strtoupper($clsRM->getValue());
			}
		}

print '
<tr><td colspan="5">&nbsp;</td></tr>
<tr>				
    <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH</b></td>
    <td nowrap="nowrap" align="right"><b>RM '.number_format($jumlah1,2).'</b></td>
</tr>';
if (dlookup("cb_purchaseinv", "purcNo", "PINo=" . tosql($PINo, "Text")) <> "") {
print'
<tr>				
    <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH BAKI PO</b></td>
    <td nowrap="nowrap" align="right"><b>RM '.number_format($baki,2).'</b></td>
</tr>';
}
print'
</table>
<tr><td colspan="5">&nbsp;</td></tr>

    <!-----------Jumlah Dalam Perkataan------------->
    <table class="stylish-kerani">
        <tr>
            <td nowrap="nowrap"><b>JUMLAH DALAM PERKATAAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($strTotal)).' Ringgit Malaysia Sahaja.</td>
        </tr>

    <!-----------Kerani/Akaun/Nama Bank------------->
        <tr>
            <td><b>DISEDIAKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($sedia)).'</td>
        </tr>
        <tr>
            <td><b>DISEMAK OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($semak)).'</td>
        </tr>
    </table>

    <center>
        <div class="bottom"><hr size="1px">
            <b>INI ADALAH CETAKAN KOMPUTER DAN TIDAK PERLU DITANDATANGAN</b>
        </div>
    </center>
    </div>
    </body>

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