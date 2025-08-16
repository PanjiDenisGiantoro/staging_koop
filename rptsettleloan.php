<?php

/*********************************************************************************
 *   Project		:	iKOOP.com.my
 *   Filename	: 	rptsettleloan.php
 *	Description	:	Loan Selesai Pada Bulan Dan Tahun
 *   Date 		: 	15/4/2017
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");
$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);

$IdJnsPinjm =  $_REQUEST['id'];
$yr1 = $yr + 1;
$mth1 = $mth + 1;

$sSQL = "SELECT ID, name, c_Deduct FROM general Where ID= '" . $IdJnsPinjm . "'";
$rs2 = &$conn->Execute($sSQL);
$JnsPinjaman = $rs2->fields(name);
$DeductID = $rs2->fields(c_Deduct);
//Title

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Pembiayaan Yang Telah Selesai Pada Bulan ' . $mth . ' Tahun ' . $yr;
//SQL Statement
$sSQL = "";
$sWhere = " a.userID = b.userID AND b.lastyrmthPymt = '" . $yrmth2 . "'";
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT a.userID,a.name, a.*,b.* FROM users a, potbulan b";
$sSQL = $sSQL . $sWhere . " ORDER BY CAST( a.userID AS SIGNED INTEGER ) ASC";
$GetMember = &$conn->Execute($sSQL);

print '
<html>
<head><title>' . $emaNetis . '</title></head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
<td align="right">' . strtoupper($emaNetis) . '</td>
</tr>
<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
<th height="40"><font color="#FFFFFF">' . $title . '
</th></tr><tr>
<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
</tr><tr><td>
	<table width="100%" border=0 align="center"  cellpadding="2" cellspacing="1">
	<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
	<td nowrap align="center"><b>Bil</b></td>
	<td nowrap align="center"><b>Nombor Anggota</b></td>
	<td align="center" nowrap><b>Nama Anggota</b></td>
	<td align="center"><b>Nombor Bond</b></td>
	<td align="center" nowrap align="center"><b>Tarikh Terakhir Loan</b></td>
</tr>';
if ($GetMember->RowCount() <> 0) {
	$bil = 1;
	while (!$GetMember->EOF) {
		$bond = $GetMember->fields(bondNo);
		print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
	<td width="31" align="center">' . $bil . '</td>
	<td width="96" align="center">' . $GetMember->fields(userID) . '</td>
	<td align="left">' . $GetMember->fields(name) . '</td>
	<td align="center">' . $bond . '</td>
	<td align="right">' . $GetMember->fields(lastyrmthPymt) . '</td>

</tr>';
		$GetMember->MoveNext();
		$bil = $bil + 1;
	}
	$GetMember->Close();
} else {
	print '
<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
<td colspan="4" align="center"><b>- Tiada Rekod Dicetak-</b></td>
</tr>';
}
print '</table></td></tr>
</table></body></html>
<tr><td>&nbsp;</td></tr>
<center>
<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
