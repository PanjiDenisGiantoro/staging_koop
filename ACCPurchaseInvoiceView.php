<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCPurchaseInvoiceView.php
*			Date 		: 27/7/2006
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

    if($note){
        $sql = "SELECT a.*, b.name as compName, b.ID, a.batchNo as batch
        FROM note a
        JOIN generalacc b ON a.companyID = b.ID
        WHERE a.noteNo = '" . $id . "';";
            
        $rs 				= $conn->Execute($sql);
        $PINo 				= $rs->fields(noteNo);
        $tarikh_PI 			= $rs->fields(tarikh_note);
        $tarikh_PI 			= substr($tarikh_PI,8,2)."/".substr($tarikh_PI,5,2)."/".substr($tarikh_PI,0,4);
        $tarikh_PI 			= toDate("d/m/y",$rs->fields(tarikh_note));
        $outstandingbalance = $rs->fields(pymtAmt);
        $companyID			= $rs->fields(companyID);
        $compName 			= dlookup("generalacc", "name", "ID=" . tosql($companyID, "Number"));
        $namaBatch 	        = dlookup("generalacc", "name", "ID=" . tosql($rs->fields(batch), "Text"));

        $disedia			= $rs->fields('keranisedia');
        $disedia1			= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
        $sedia 				= strtoupper(strip_tags($disedia1));

        $disemak			= $rs->fields('keranisemak');
        $disemak1			= dlookup("users", "name", "userID=" . tosql($disemak, "Text"));
        $semak 				= strtoupper(strip_tags($disemak1));

        $departmentAdd		= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));

        $alamat 			= strtoupper(strip_tags($departmentAdd));
        $catatan 		    = $rs->fields('catatan');

        $carabayar 	        = $rs->fields('cara_byr');
        $carabayar 	        = dlookup("general", "name", "ID=".$carabayar);
    } else {
        $sql = "SELECT a.*, b.name as compName, b.ID, c.*, a.batchNo as batch
        FROM cb_purchaseinv a
        JOIN generalacc b ON a.companyID = b.ID
        LEFT JOIN cb_purchase c 
            ON (a.purcNo = c.purcNo OR a.purcNo = '' OR a.purcNo IS NULL)
        WHERE a.PINo = '" . $id . "';";
            
        $rs 				= $conn->Execute($sql);
        $PINo 				= $rs->fields(PINo);
        $tarikh_PI 			= $rs->fields(tarikh_PI);
        $tarikh_PI 			= substr($tarikh_PI,8,2)."/".substr($tarikh_PI,5,2)."/".substr($tarikh_PI,0,4);
        $tarikh_PI 			= toDate("d/m/y",$rs->fields(tarikh_PI));
        if (dlookup("cb_purchaseinv", "purcNo", "PINo=" . tosql($PINo, "Text")) <> "") {
        $purcNom        	= $rs->fields(purcNo);
        $tarikh_purc 		= $rs->fields(tarikh_purc);
        $tarikh_purc 		= toDate("d/m/y",$rs->fields(tarikh_purc));
        }
        $outstandingbalance = $rs->fields(outstandingbalance);
        $nama 				= $rs->fields(name);
        $companyID			= $rs->fields(companyID);
        $compName 			= dlookup("generalacc", "name", "ID=" . tosql($companyID, "Number"));
        $namaBatch 	        = dlookup("generalacc", "name", "ID=" . tosql($rs->fields(batch), "Text"));

        $disedia			= $rs->fields('keranisedia');
        $disedia1			= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
        $sedia 				= strtoupper(strip_tags($disedia1));

        $disemak			= $rs->fields('keranisemak');
        $disemak1			= dlookup("users", "name", "userID=" . tosql($disemak, "Text"));
        $semak 				= strtoupper(strip_tags($disemak1));

        $departmentAdd		= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));

        $alamat 			= strtoupper(strip_tags($departmentAdd));
        $catatan 		    = $rs->fields('catatan');

        $carabayar 	        = $rs->fields('cara_byr');
        $carabayar 	        = dlookup("general", "name", "ID=".$carabayar);
        $invLhdn 		    = $rs->fields(invLhdn);
        $invComp 		    = $rs->fields(invComp);
    }

	$sqltotal = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE docNo = '".$id."'";
	$rstotal = $conn->Execute($sqltotal);
	$jumlah = $rstotal->fields(tot);
	
    if ($note) {
        $sql2 = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = ".tosql($PINo, "Text")." ORDER BY ID";
        $rsDetail = $conn->Execute($sql2);
    }
    else {
        $sql2 = "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = ".tosql($PINo, "Text")." ORDER BY ID";
        $rsDetail = $conn->Execute($sql2);
    }
}

if($jumlah<>0){
	$clsRP->setValue($jumlah);
	$strTotal = ucwords($clsRP->getValue()).' Sahaja.';
}
$jumlah = number_format($jumlah,2);

print'
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
            margin-top:5%;
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

if($note) $subject = "NO. NOTA DEBIT"; else $subject = "NO. PI";

print'
<body>
<div class="form-container">
    <!---------Doc/Date-------->
    <table class="stylish-date">
        <tr>
			<td><b>'.$subject.'</b></td>
            <td>&nbsp;:&nbsp;</td>
			<td>'.$PINo.'</td>
		</tr>
        <tr>
            <td><b>TARIKH</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_PI.'</td>
        </tr>
        <tr><td colspan="8"><br></td></tr>
        ';
        if ($invLhdn) {
        print'
        <tr>
            <td><b>NO INVOIS LHDN</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$invLhdn.'</td>
        </tr>
        ';
        }
        if ($invComp) {
        print'
        <tr>
            <td><b>NO INVOIS SYARIKAT</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$invComp.'</td>
        </tr>
        ';
        }
    print'
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr>
        <td nowrap="nowrap"><b>DITERIMA DARIPADA</b></td>
        <td>&nbsp;:&nbsp;</td>
		<td>'.ucwords(strtolower($compName)).'</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>ALAMAT</b></td>
		<td>&nbsp;:&nbsp;</td>
		<td>'.ucwords(strtolower($alamat)).'</td>
    </tr>
	<tr>
		<td nowrap="nowrap"><b>NAMA BATCH</b></td>
		<td>&nbsp;:&nbsp;</td>
		<td>'.$namaBatch.'</td>
	</tr>
    <tr>
        <td nowrap="nowrap"><b>CARA BAYARAN</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.$carabayar.'</td>
    </tr>
    ';
    if (dlookup("cb_purchaseinv", "purcNo", "PINo=" . tosql($PINo, "Text")) <> "") {
        print'
        <tr>
            <td><b>NO PO</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$purcNom.'</td>
        </tr>
        <tr>
            <td><b>TARIKH PO</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_purc.'</td>
        </tr>
        <tr>
            <td><b>AMAUN PO</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>RP'.number_format($outstandingbalance,2).'</td>
        </tr>';
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
                <td nowrap="nowrap" align="right"><b>HARGA SEUNIT (RP)</b></td>
                <td nowrap="nowrap" align="right"><b>AMAUN (RP)</b></td>
            </tr>
    </thead>';

	if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
			
		$accNom 	= $rsDetail->fields(deductID);
		$accN 		= $rsDetail->fields(deductID);
		$desc_akaun = $rsDetail->fields(desc_akaun);
		$cara_b 	= $rsDetail->fields(cara_bayar);

		$keterangan_resit 	= $rsDetail->fields(keterangan);


		$accNombor 	= dlookup("generalacc", "code", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);

		$totPymt = number_format($rsDetail->fields(pymtAmt),2);
		print
			'<tr>
				<td>'.$i.'</td>
                <td style="text-align: justify;">'.$desc_akaun.'</td>
				<td align="center">'.$rsDetail->fields(quantity).'</td>
				<td align="right">'.$rsDetail->fields(price).'</td>				
				<td align="right">'.$totPymt.'</td>
			</tr>';
			$jumlah1 += $rsDetail->fields(pymtAmt);
			$baki = $outstandingbalance-$jumlah1;
            $i++;
			$rsDetail->MoveNext();
			}
			if($jumlah1<>0){
			$clsRP->setValue($baki);
			$clsRP->setValue($jumlah1);
			$strTotal = strtoupper($clsRP->getValue());
			}
		}

print '
<tr><td colspan="5">&nbsp;</td></tr>
<tr>				
    <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH</b></td>
    <td nowrap="nowrap" align="right"><b>RP '.number_format($jumlah1,2).'</b></td>
</tr>';

if (dlookup("cb_purchaseinv", "purcNo", "PINo=" . tosql($PINo, "Text")) <> "") {
    print'
        <tr>				
            <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH BAKI PO</b></td>
            <td nowrap="nowrap" align="right"><b>RP '.number_format($baki,2).'</b></td>
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

    <!-----------Catatan/Description------------->
        <tr>
            <td nowrap="nowrap"><b>CATATAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($catatan)).'</td>
        </tr>
    <!-----------Kerani/Akaun/Nama Bank------------->
        <tr>
            <td><b>DISEDIAKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($sedia)).'</td>
        </tr>
        <tr>
            <td><b>DICEK OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($semak)).'</td>
        </tr>
    </table>

    </div>
    </body>

</body></html>';
?>