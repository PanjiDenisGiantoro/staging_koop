<?php

/*******************w**************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptmbrSahBaki.php
 *		   Description	:	
 *          Date 		: 	28/09/2022
 *********************************************************************************/
session_start();
if (!isset($q))				$q = '';
if (!isset($by))			$by = '1';
if (!isset($status))		$status = '1';
if (!isset($dept))			$dept = '';

include("common.php");
include("koperasiQry.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}


$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status IN (1) ";
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*
		 FROM 	users a, userdetails b";
$sSQL = $sSQL . $sWhere;
$sSQL = $sSQL . " order by CAST( b.memberID AS SIGNED INTEGER )";
$GetData = &$conn->Execute($sSQL);

$title  = 'Senarai Baki Terkumpul Pada Tahun ';

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
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="85 height="40"><font color="#FFFFFF">' . $title . '<br>
			Dari ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '</font>
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border="0"  cellpadding="2" cellspacing="1" align="left" width="100%">';
print
	'<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;Bil.</th>
					<th nowrap align="center">Nombor Anggota</th>
					<th nowrap align="left">Nama Anggota</th>
					<th nowrap align="center">Nombor Kad Pengenalan</th>
					<th nowrap align="left">Nombor Telefon</th>
					<th nowrap align="right">Baki Terkumpul Wajib (RP)</th>
					<th nowrap align="right">Baki Terkumpul Syer (RP)</th>
				</tr>';

$total1 = 0;
$total2 = 0;


if ($GetData->RowCount() <> 0) {

	while (!$GetData->EOF) {

		$totalYuransTK = getBakiBulanY($GetData->fields(userID), $dtFrom, $dtTo);
		$totalSharesTK = getBakiBulanS($GetData->fields(userID), $dtFrom, $dtTo);


		$total1 += $totalYuransTK;
		$total2 += $totalSharesTK;

		// $jabatan = $GetData->fields('departmentID');
		// $namajabatan = dlookup("general", "name", "ID=".$jabatan);
		// $codejabatan = dlookup("general", "code", "ID=".$jabatan);

		$count++;

		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td align="right" valign="top" width="2%">' . $count . ')</td>
							<td align="center" valign="top">&nbsp;' . $GetData->fields('memberID') . '</a></td>
							<td valign="top" align="left" nowrap>' . strtoupper($GetData->fields('name')) . '</td>
							<td valign="top" align="center">' . convertNewIC($GetData->fields('newIC')) . '</td>
							<td valign="top" align="left">' . $GetData->fields('mobileNo') . '</td>
							<td align="right" valign="top">' . number_format($totalYuransTK, 2) . '</td>
							<td align="right" valign="top">' . number_format($totalSharesTK, 2) . '</td>
						</tr>';

		$GetData->MoveNext();
	} //'.number_format($total1,2).'

	print '					
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="right"><b>JUMLAH KESELURUHAN (RP)  </b>:</td>
						<td align="right">&nbsp;<b>' . number_format($total1, 2) . '</b></td>
						<td align="right">&nbsp;<b>' . number_format($total2, 2) . '</b></td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print 		'</table>
		</td>
	</tr>

	</table>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
