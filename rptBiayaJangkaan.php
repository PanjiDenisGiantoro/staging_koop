<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");
$month = (int)substr($yrmth, 4, 2);
$yr = (int)substr($yrmth, 0, 4);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Kesimpulan Jangkaan Kutipan Penghutang Dalam Bulan ' . displayBulan($month) . ' ' . $yr;
$title	= strtoupper($title);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="5" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="85 height="40"><font color="#FFFFFF">' . $title . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="5"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;JENIS PEMBIAYAAN</th>
					<th nowrap>&nbsp;PEMBIAYAAN PERIBADI</th>
					<th nowrap>&nbsp;PEMBIAYAAN BARANGAN</th>
					<th nowrap>&nbsp;PEMBIAYAAN ATAS YURAN</th>
					<th nowrap>&nbsp;PEMBIAYAAN INSURAN</th>
					<th nowrap>&nbsp;PEMBIAYAAN NAIK SEKOLAH</th>
					<th nowrap>&nbsp;PEMBIAYAAN UMRAH</th>
					<th nowrap>&nbsp;PEMBIAYAAN DEPOSIT HARTANAH</th>
					<th nowrap>&nbsp;JUMLAH</th>
				</tr>';

$sSQL = "";
$sSQL = "SELECT SUM(pokok) AS jumpkk, SUM(untung) AS jumutg
		FROM loans a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) =  " . $month . " and  rnoBond <> ''
		AND c.parentID =1632";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	$pkkTunai = $rs->fields(jumpkk);
	$utgTunai = $rs->fields(jumutg);
	$totTunai = $pkkTunai + $utgTunai;
} else {
	$pkkTunai = 0;
	$utgTunai = 0;
	$totTunai = 0;
}

$sSQL = "";
$sSQL = "SELECT sum( pokok ) AS jumpkk, sum( untung ) AS jumutg
		FROM `loans` a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) =  " . $month . " and  rnoBond <> ''
		AND c.parentID =1633";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	$pkkBrg = $rs->fields(jumpkk);
	$utgBrg = $rs->fields(jumutg);
	$totBrg = $pkkBrg + $utgBrg;
} else {
	$pkkBrg = 0;
	$utgBrg = 0;
	$totBrg = 0;
}

$sSQL = "";
$sSQL = "SELECT sum( pokok ) AS jumpkk, sum( untung ) AS jumutg
		FROM `loans` a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) =  " . $month . " and  rnoBond <> ''
		AND c.parentID =1634";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	$pkkShm = $rs->fields(jumpkk);
	$utgShm = $rs->fields(jumutg);
	$totShm = $pkkShm + $utgShm;
} else {
	$pkkShm = 0;
	$utgShm = 0;
	$totShm = 0;
}

$sSQL = "";
$sSQL = "SELECT sum( pokok ) AS jumpkk, sum( untung ) AS jumutg
		FROM `loans` a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) =  " . $month . " and  rnoBond <> ''
		AND c.parentID =1635";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	$pkkIns = $rs->fields(jumpkk);
	$utgIns = $rs->fields(jumutg);
	$totIns = $pkkIns + $utgIns;
} else {
	$pkkIns = 0;
	$utgIns = 0;
	$totIns = 0;
}

$sSQL = "";
$sSQL = "SELECT sum( pokok ) AS jumpkk, sum( untung ) AS jumutg
		FROM `loans` a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) =  " . $month . " and  rnoBond <> ''
		AND c.parentID =1636";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	$pkkUrh = $rs->fields(jumpkk);
	$utgUrh = $rs->fields(jumutg);
	$totUrh = $pkkUrh + $utgUrh;
} else {
	$pkkUrh = 0;
	$utgUrh = 0;
	$totUrh = 0;
}

$sSQL = "";
$sSQL = "SELECT sum( pokok ) AS jumpkk, sum( untung ) AS jumutg
		FROM `loans` a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) =  " . $month . " and  rnoBond <> ''
		AND c.parentID =1637";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	$pkkSek = $rs->fields(jumpkk);
	$utgSek = $rs->fields(jumutg);
	$totSek = $pkkSek + $utgSek;
} else {
	$pkkSek = 0;
	$utgSek = 0;
	$totSek = 0;
}

$sSQL = "";
$sSQL = "SELECT sum( pokok ) AS jumpkk, sum( untung ) AS jumutg
		FROM `loans` a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) =  " . $month . " and  rnoBond <> ''
		AND c.parentID =1638";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	$pkkKdn = $rs->fields(jumpkk);
	$utgKdn = $rs->fields(jumutg);
	$totKdn = $pkkKdn + $utgKdn;
} else {
	$pkkKdn = 0;
	$utgKdn = 0;
	$totKdn = 0;
}

$sSQL = "";
$sSQL = "SELECT sum( pokok ) AS jumpkk, sum( untung ) AS jumutg
		FROM loans a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) = " . $month . " and rnoBond <> ''
		AND c.parentID =1638";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	$pkkhar = $rs->fields(jumpkk);
	$utghar = $rs->fields(jumutg);
	$tothar = $pkkhar + $utghar;
} else {
	$pkkhar = 0;
	$utghar = 0;
	$tothar = 0;
}
$totpkk = $pkkTunai + $pkkBrg + $pkkShm + $pkkIns + $pkkSek + $pkkUrh + $pkkhar;
print '
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td>&nbsp;JUMLAH POKOK</th>
					<td align="right">&nbsp;' . $pkkTunai . '</th>
					<td align="right">&nbsp;' . $pkkBrg . '</th>
					<td align="right">&nbsp;' . $pkkShm . '</th>
					<td align="right">&nbsp;' . $pkkIns . '</th>
					<td align="right">&nbsp;' . $pkkSek . '</th>
					<td align="right">&nbsp;' . $pkkUrh . '</th>
					<td align="right">&nbsp;' . $pkkhar . '</th>
					<td align="right">&nbsp;' . number_format($totpkk, 2) . '</th>
				</tr>';
$totutg = $utgTunai + $utgBrg + $utgShm + $utgIns + $utgSek + $utgUrh + $utghar;
print '
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td>&nbsp;JUMLAH UNTUNG</th>
					<td align="right">&nbsp;' . $utgTunai . '</th>
					<td align="right">&nbsp;' . $utgBrg . '</th>
					<td align="right">&nbsp;' . $utgShm . '</th>
					<td align="right">&nbsp;' . $utgIns . '</th>
					<td align="right">&nbsp;' . $utgSek . '</th>
					<td align="right">&nbsp;' . $utgUrh . '</th>
					<td align="right">&nbsp;' . $utghar . '</th>
					<td align="right">&nbsp;' . number_format($totutg, 2) . '</th>
				</tr>';
$totAll = $totpkk + $totutg;
print '
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;JUMLAH KESELURUHAN BULANAN</th>
					<th align="right">&nbsp;' . number_format($totTunai, 2) . '</th>
					<th align="right">&nbsp;' . number_format($totBrg, 2) . '</th>
					<th align="right">&nbsp;' . number_format($totShm, 2) . '</th>
					<th align="right">&nbsp;' . number_format($totIns, 2) . '</th>
					<th align="right">&nbsp;' . number_format($totSek, 2) . '</th>
					<th align="right">&nbsp;' . number_format($totUrh, 2) . '</th>
					<th align="right">&nbsp;' . number_format($tothar, 2) . '</th>
					<th align="right">&nbsp;' . number_format($totAll, 2) . '</th>
				</tr>';
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr align="center"><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
