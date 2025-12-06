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
date_default_timezone_set("Asia/Jakarta");
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
//Titles

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Pembiayaan Yang Belum Diklik Pada Bulan ' . $mth . ' Tahun ' . $yr;
//SQL Statement
$sSQL = "";
$sSQL = "SELECT a.*,b.* FROM loans a, loandocs b WHERE a.loanID=b.loanID AND a.status IN (3) AND b.rnoBaucer IS NULL";
$sSQL = $sSQL . " ORDER BY CAST(a.userID AS SIGNED INTEGER) ASC";
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
	<td nowrap><div align="center">Bil</div></td>
	<td nowrap align="center">Nomor Anggota </td>
	<td align="left" nowrap>Nama</td>
	<td align="left">Nama Pembiayaan</td>
	<td align="center">Nomor Rujukan</td>
	<td align="center">Nombor Bond</td>
	<td align="right">Jumlah Pembiayaan (RP)</td>
	<td align="center">Tanggal Pembiayaan Diluluskan</td>
</tr>';
if ($GetMember->RowCount() <> 0) {
	$bil = 1;
	while (!$GetMember->EOF) {

		$name 			= dlookup("users", "name", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$loanName 		= dlookup("general", "name", "ID=" . tosql($GetMember->fields(loanType), "Text"));
		$startPymtDate 	= toDate("d/m/Y", $GetMember->fields(startPymtDate));

		print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td width="31" align="center">' . $bil . '</td>
	<td width="96" align="center">' . $GetMember->fields(userID) . '</td>
	<td align="right"><div align="left">' . $name . '</div></td>
	<td align="right"><div align="left">' . $loanName . '</div></td>
	<td align="right"><div align="center">' . $GetMember->fields(loanNo) . '</div></td>
	<td align="right"><div align="center">' . $GetMember->fields(rnoBond) . '</div></td>
	<td align="right"><div align="right">' . $GetMember->fields(loanAmt) . '</div></td>
	<td align="right"><div align="center">' . $startPymtDate . '</div></td>

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
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
