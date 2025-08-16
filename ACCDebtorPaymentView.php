<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCDebtorPaymentView.php
*			Date 		: 27/7/2006
*********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

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

// Determining resit that are bulk payment only ------START
$subQuery = "
    SELECT RVNo 
    FROM cb_payments 
    GROUP BY RVNo 
    HAVING COUNT(RVNo) > 1
";

$sSQL = "";
$sWhere = " a.RVNo IN ($subQuery) OR a.invNo IS NULL";
$sWhere = " WHERE (".$sWhere.")";

// sql select dari table mana 
$sSQL = "SELECT a.RVNo
		FROM cb_payments a 
		LEFT JOIN generalacc b ON a.batchNo = b.ID
		";

$sSQL 	= $sSQL.$sWhere." group by RVNo order by RVNo desc";
$GetRvBulk  = &$conn->Execute($sSQL);

$rvBulkList = Array();
if ($GetRvBulk->RowCount() <> 0){
	while (!$GetRvBulk->EOF) {
		array_push ($rvBulkList, $GetRvBulk->fields('RVNo'));
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
$sWhere = " WHERE (".$sWhere.")";

// sql select dari table mana 
$sSQL = "SELECT a.RVNo
		FROM cb_payments a 
		LEFT JOIN generalacc b ON a.batchNo = b.ID
		";

$sSQL 	= $sSQL.$sWhere." group by RVNo order by RVNo desc";
$GetRvOpenbal  = &$conn->Execute($sSQL);

$rvOpenbalList = Array();
if ($GetRvOpenbal->RowCount() <> 0){
	while (!$GetRvOpenbal->EOF) {
		array_push ($rvOpenbalList, $GetRvOpenbal->fields('RVNo'));
		$GetRvOpenbal->MoveNext();
	}
}
// Determining resit that pay opening balance only ------END

// echo '<pre>';
// print_r($rvOpenbalList);
// echo '</pre>';

if($id){
	$sql = "SELECT a.*,b.* FROM cb_payments a, generalacc b WHERE a.companyID = b.ID and a.RVNo = '".$id."'";
          
	$rs 			= $conn->Execute($sql);
	$RVNo 			= $rs->fields('RVNo');
	$tarikh_RV 		= toDate("d/m/y",$rs->fields('tarikh_RV'));
	
	$nama 			= $rs->fields(name);
	$companyID		= $rs->fields('companyID');


	$disedia		= $rs->fields('disedia');
	$disedia1		= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
	$sedia 			= strtoupper(strip_tags($disedia1));

	$disemak		= $rs->fields('disemak');
	$disemak1		= dlookup("users", "name", "userID=" . tosql($disemak, "Text"));
	$semak 			= strtoupper(strip_tags($disemak1));

	$keranisemak	= dlookup("generalacc", "name", "ID=" . tosql($companyID, "Number"));
	$departmentAdd	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));

	$alamat 		= strtoupper(strip_tags($departmentAdd));
	$catatan 	    = $rs->fields('catatan');

	$invNom			= $rs->fields('invNo');
	$invNombor 		= strtoupper(strip_tags($invNom));

    $tarikh_INV		= dlookup("cb_invoice", "tarikh_inv", "invNo=" . tosql($invNom, "Text"));
	$tarikh_INV 	= toDate("d/m/y",$tarikh_INV);

	$outstandingbal	= dlookup("cb_invoice", "outstandingbalance", "invNo=" . tosql($invNom, "Text"));
	$amt 			= $rs->fields(outstandingbalance);


	$sqltotal = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE docNo = '".$id."'";
	$rstotal = $conn->Execute($sqltotal);
	$jumlah = $rstotal->fields(tot);
	
	$sql2 = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = ".tosql($RVNo, "Text")." ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);

    if ($rs->fields(batchNo)) {
        $namaBatch 	= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(batchNo), "Text"));
    } else {
        $namaBatch 	= "-";
    }

    if ($rs->fields(kod_project))
        $namaProjek 	= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(kod_project), "Text"));
    else
        $namaProjek     = "-";

    if ($rs->fields(kod_jabatan))
        $namaJabatan 	= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(kod_jabatan), "Text"));
    else
        $namaJabatan    = "-";

    if ($rs->fields(kod_bank))
        $namaBank 	= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(kod_bank), "Text"));
    else
        $namaBank    = "-";

    $invLhdn		= dlookup("cb_invoice", "invLhdn", "invNo=" . tosql($invNom, "Text"));
    $invComp		= dlookup("cb_invoice", "invComp", "invNo=" . tosql($invNom, "Text"));
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
            margin-top:6%;
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

print'
<body>
<div class="form-container">
    <!---------Doc/Date-------->
    <table class="stylish-date">
        <tr>
            <td><b>NO RESIT</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$RVNo.'</td>
        </tr>

        <tr>
            <td><b>TARIKH RESIT</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_RV.'</td>
        </tr>

        <tr><td colspan="8"><br></td></tr>
';
if (in_array($RVNo, $rvBulkList)) { // if this resit is a bulk payment resit
} elseif (in_array($RVNo, $rvOpenbalList)) { // if this resit is to pay company opening balance
print'
    <tr>
        <td nowrap="nowrap"><b>AMAUN PEMBUKA</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>RM&nbsp;'.number_format($amt,2).'</td>
    </tr>
';
} else { // if this resit is normal resit
print'
        <tr>
            <td nowrap="nowrap"><b>NO INVOIS</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td nowrap="nowrap">'.$invNom.'</td>
        </tr>

        <tr>
            <td nowrap="nowrap"><b>TARIKH INVOIS</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td nowrap="nowrap">'.$tarikh_INV.'</td>
        </tr>

        <tr>
            <td nowrap="nowrap"><b>AMAUN INVOIS</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td nowrap="nowrap">RM&nbsp;'.number_format($amt,2).'</td>
        </tr>

        <tr><td colspan="8"><br></td></tr>
';
}
if ($invLhdn){
    print'
    <tr>
        <td><b>NO INVOIS LHDN</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.$invLhdn.'</td>
    </tr> 
    ';
}

if ($invComp){
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
        <td><b>DITERIMA DARIPADA</b></td>
		<td>&nbsp;:&nbsp;</td>
        <td>'.ucwords(strtolower($keranisemak)).'</td>
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
        <td nowrap="nowrap"><b>NAMA BANK</b></td>
		<td>&nbsp;:&nbsp;</td>
		<td>'.ucwords(strtolower($namaBank)).'</td>
    </tr>
	<tr>
        <td nowrap="nowrap"><b>NAMA PROJEK</b></td>
		<td>&nbsp;:&nbsp;</td>
		<td>'.ucwords(strtolower($namaProjek)).'</td>
    </tr>
	<tr>
        <td nowrap="nowrap"><b>NAMA JABATAN</b></td>
		<td>&nbsp;:&nbsp;</td>
		<td>'.ucwords(strtolower($namaJabatan)).'</td>
    </tr>
    ';

    print'
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
    print'
                <td nowrap="nowrap"><b>NO INVOIS</b></td>
    ';
    }
    print'
                <td nowrap="nowrap"><b>CARA BAYARAN</b></td>
                <td nowrap="nowrap"><b>KETERANGAN</b></td>
                <td nowrap="nowrap" align="right"><b>AMAUN (RM)</b></td>
            </tr>
    </thead>';

    if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
			
		$accNom 	= $rsDetail->fields(deductID);
		$accN 	    = $rsDetail->fields(deductID);
		$desc_akaun = $rsDetail->fields(desc_akaun);
		$cara_b 	= $rsDetail->fields(cara_bayar);

        if (in_array($RVNo, $rvBulkList)) {
            $invNom = $rsDetail->fields(pymtReferC);
        }

		$keterangan_resit = $rsDetail->fields(keterangan);

		$accNombor 	= dlookup("generalacc", "name", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);
		$carabayar 	= dlookup("general", "name", "ID=".$cara_b);

		$totPymt = number_format($rsDetail->fields(pymtAmt),2);
		print
			'<tr>
				<td>'.$i.'</td>
        ';
        if (in_array($RVNo, $rvBulkList)) {
        print'
				<td nowrap="nowrap">'.$invNom.'</td>
        ';
        }
        print'
				<td nowrap="nowrap">'.$carabayar.'</td>
                <td style="text-align: justify;">'.$desc_akaun.'</td>
				<td nowrap="nowrap" align="right">'.$totPymt.'</td>
			</tr>';
			$jumlah1 += $rsDetail->fields(pymtAmt);
			$baki = $amt-$jumlah1;
            $i++;
			$rsDetail->MoveNext();
			}
			if($jumlah1<>0){
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
print'
<tr>
<td nowrap="nowrap" align="right" colspan="3"><b>JUMLAH BAKI</b></td>
<td nowrap="nowrap" align="right"><b>RM '.number_format($baki,2).'</b></td>
</tr>
';
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
            <td><b>DISEMAK OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($semak)).'</td>
        </tr>
    </table>
    </div>
    </body>
</html>
</body></html>';
?>