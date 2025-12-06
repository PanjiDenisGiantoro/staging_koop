<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptD2.php
 *		   Description	:	Report Senarai Urusniaga Tahunan
 *		   Parameter	:   $yr , $id (ALL)
 *          Date 		: 	06/04/2004
 *********************************************************************************/
session_start();
if (!isset($code))		$code = "ALL";
if (!isset($status))	$status = "1";

include("common.php");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Urusniaga Tahunan';

$sSQL = "";
//--- Prepare deduct list
$deductList = array();
$deductVal  = array();
$sSQL = "	SELECT B.ID, B.code , B.name 
			FROM transaction A, general B
			WHERE A.deductID= B.ID
			AND   A.yrmth LIKE " . tosql($yr . "%", "Text") . "
			GROUP BY A.deductID";
$GetDeduct = &$conn->Execute($sSQL);
if ($GetDeduct->RowCount() <> 0) {
	while (!$GetDeduct->EOF) {
		array_push($deductList, $GetDeduct->fields(code) . ' - ' . $GetDeduct->fields(name));
		array_push($deductVal, $GetDeduct->fields(ID));
		$GetDeduct->MoveNext();
	}
}

$sSQL = "";
$sSQL = "SELECT	* FROM transaction
		 WHERE yrmth LIKE " . tosql($yr . "%", "Text") . "
		 AND 	status = " . $status;
if ($code <> "ALL")
	$sSQL .= " AND deductID = " . tosql($code, "Number");
$sSQL .= " ORDER BY yrmth ";
$rs = &$conn->Execute($sSQL);
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/default.css" >		
</head>
<body>';
print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<p class="textFont">Pilihan Kod Potongan
		<select name="code" class="textFont" onchange="document.MyForm.submit();">
			<option value="ALL">- Semua -';
for ($i = 0; $i < count($deductList); $i++) {
	print '	<option value="' . $deductVal[$i] . '" ';
	if ($code == $deductVal[$i]) print ' selected';
	print '>' . $deductList[$i];
}
print '	</select>
		Status
		<select name="status" class="textFont" onchange="document.MyForm.submit();">';
for ($i = 0; $i < count($statusList); $i++) {
	if ($statusVal[$i] < 3) {
		print '	<option value="' . $statusVal[$i] . '" ';
		if ($status == $statusVal[$i]) print ' selected';
		print '>' . $statusList[$i];
	}
}
print '
</p>
<input type="hidden" name="yr" value="' . $yr . '">
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="10" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#336699" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="10" height="40"><font color="#FFFFFF">' . $title . ' Bagi Tahun ' . $yr . '
		</th>
	</tr>
	<tr>
		<td colspan="10"><font size=1>Cetak Pada : ' . $today . '</font></td>
	</tr>
	<tr><td colspan="10">&nbsp;</td></tr>
	<tr>
		<td colspan="10">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap>Bulan/Tahun</th>
					<th nowrap>&nbsp;No Rujukan</th>
					<th nowrap>&nbsp;Nomor Anggota</th>
					<th nowrap>&nbsp;Kod</th>
					<th nowrap>&nbsp;Jenis Bayaran</th>
					<th nowrap>&nbsp;Bayaran</th>
					<th nowrap>&nbsp;Caj</th>
					<th nowrap>&nbsp;Debit</th>
					<th nowrap>&nbsp;Kredit</th>					
				</tr>';
$amtTotal = 0;
$DRTotal = 0;
$CRTotal = 0;
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		$pymtTotal = $rs->fields(pymtAmt) + $rs->fields(cajAmt);
		$DRAmt = '';
		$CRAmt = '';
		if ($rs->fields(addminus) == 0) {
			$DRTotal += $pymtTotal;
			$DRAmt = number_format($pymtTotal, 2, '.', ',');
		} else {
			$CRTotal += $pymtTotal;
			$CRAmt = number_format($pymtTotal, 2, '.', ',');
		}
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td width="2%" align="center">&nbsp;' . substr($rs->fields(yrmth), 4, 2) . '/' . substr($rs->fields(yrmth), 0, 4) . '&nbsp;</td>
							<td>&nbsp;' . sprintf("%010d", $rs->fields(ID)) . '-' . $rs->fields(docNo) . '</td>
							<td>&nbsp;' . dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")) . '</td>
							<td>&nbsp;' . dlookup("general", "code", "ID=" . tosql($rs->fields(deductID), "Text")) . '</td>
							<td>&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields(pymtID), "Text")) . '</td>
							<td align="right">' . number_format($rs->fields(pymtAmt), 2, '.', ',') . '&nbsp;</td>
							<td align="right">' . $rs->fields(cajAmt) . '&nbsp;</td>
							<td align="right">' . $DRAmt . '&nbsp;</td>
							<td align="right">' . $CRAmt . '&nbsp;</td>
						</tr>';
		$rs->MoveNext();
	}
	$amtTotal = $DRTotal - $CRTotal;
	if ($amtTotal < 0) {
		$amtTotal = str_replace("-", "", $amtTotal);
		$amtTotal = number_format($amtTotal, 2, '.', ',');
	} else {
		$amtTotal = number_format($amtTotal, 2, '.', ',');
	}
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight: bold;" bgcolor="FFFFFF">
						<td colspan="8" align="right">Jumlah :&nbsp;&nbsp;&nbsp;</td>
						<td align="right">' . number_format($DRTotal, 2, '.', ',') . '&nbsp;</td>
						<td align="right">' . number_format($CRTotal, 2, '.', ',') . '&nbsp;</td>
					</tr>
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight: bold;" bgcolor="FFFFFF">
						<td colspan="8" align="right">Amaun Beza&nbsp;&nbsp;&nbsp;</td>
						<td align="right" colspan="2">' . $amtTotal . '&nbsp;</td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="10" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="10">&nbsp;</td></tr>
	<tr><td colspan="10"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</form>
</body>
</html>';
