<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA25style.php
 *		   Description	:	Report Trial Balance
 *		   Parameter	:   $dateFrom , $dateTo
 *          Date 		: 	6/2024
 *   	   Description  : 	Trial Balance in new page style
 *********************************************************************************/
session_start();
include("common.php");
include("AccountQry.php");

date_default_timezone_set("Asia/Jakarta");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'IMBANGAN DUGA (TRIAL BALANCE)';

$sSQL = "";
//$sSQL = "SELECT *,ID as transID FROM transactionacc WHERE docID NOT IN (0) AND (tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."')  ORDER BY docNo ASC,tarikh_doc";

$sSQL = "SELECT a.*,b.* FROM transactionacc a, generalacc b WHERE a.deductID=b.ID AND a.docID NOT IN (0) AND (a.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') GROUP BY a.deductID ORDER BY b.code ";
//GROUP BY a.deductID 
$rs = &$conn->Execute($sSQL);
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="en">
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<head>
<title>' . $emaNetis . '</title>
</head>
<body>';
print '

<table width="100%" class="table table-sm table-striped">

    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
        <td align="right">' . strtoupper($emaNetis) . '</td>
    </tr>
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: center;">
        <th colspan="9" height="40">' . $title . '<br>
            DARI ' . toDate("d/m/Y", $dtFrom) . ' HINGGA ' . toDate("d/m/Y", $dtTo) . '
        </th>
    </tr>
	    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
        <td colspan="8" align="left"><font size=1>CETAK PADA : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
    </tr>
	</table>
    <tr><td colspan="9">&nbsp;</td></tr>
    <tr>
        <td colspan="9">
            <table class="table table-striped" width="100%">
                <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold;">
                    <th nowrap style="text-align: center;">BIL</th>
                    <th nowrap style="text-align: center;">KOD AKAUN</th>
                    <th nowrap style="text-align: left;">NAMA AKAUN</th>
                    <th nowrap style="text-align: right;">DEBIT (RP)</th>
                    <th nowrap style="text-align: right;">KREDIT (RP)</th>    
                </tr>';
$totaldebit 	= 0;
$totalkredit 	= 0;

if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;

		//$ID 			= $rs->fields(deductID);
		$jumlah 		= 0;
		$tarikh_baucer 	= toDate("d/m/y", $rs->fields(tarikh_doc));
		$glname 		= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(deductID), "Text"));
		$glnameCode 	= dlookup("generalacc", "code", "ID=" . tosql($rs->fields(deductID), "Text"));

		$getAmaunTBD	= getAmaunTBD($rs->fields(deductID), $dtFrom, $dtTo);
		$debit1 		= $getAmaunTBD->fields(amaun);

		$getAmaunTBK	= getAmaunTBK($rs->fields(deductID), $dtFrom, $dtTo);
		$kredit1 		= $getAmaunTBK->fields(amaun);

		print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td width="2%" align="center">' . $bil . ')&nbsp;</td>
		<td align="center">' . $glnameCode . '</a></td>
		<td align="left">' . $glname . ' </a></td>';


		if ($debit1 == 0) {
			print '	<td class="Data" align ="right">0.00</td>';
		} else {
			print '	<td class="Data" align ="right">' . $debit1 . '</td>';
			$totaldebit += $debit1;
		}

		if ($kredit1 == 0) {
			print '	<td class="Data" align ="right">0.00</td>';
		} else {
			print '	<td class="Data" align ="right">' . $kredit1 . '</td>';
			$totalkredit += $kredit1;
		}

		print '</tr>';

		$rs->MoveNext();
	}

	print '	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="3" align="right"><b>&nbsp;JUMLAH KESELURUHAN (RP)</b></td>
		<td align="right">' . number_format($totaldebit, 2) . '</td>
		<td align="right">' . number_format($totalkredit, 2) . '</td>
	</tr>';

	$baki = ($totaldebit - $totalkredit);

	print '	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="3" align="right"><b>&nbsp;SALDO (RP)</b></td>
		<td colspan="2" align="right">' . number_format($baki, 2) . '</td>
	</tr>';
} else {
	print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="5" align="center"><b>- TIADA REKOD DICETAK-</b></td>
	</tr>';
}
print '</table></td></tr>
</table></body></html>
<tr><td colspan="5">&nbsp;</td></tr>
<center><tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
