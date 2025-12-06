<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptB2A.php
 *		   Description	:	Report Status Keseluruhan Pembiayaan
 *********************************************************************************/
include("common.php");

date_default_timezone_set("Asia/Jakarta");
$today = date("F j, Y");
if (get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Keseluruhan Pembiayaan';

$sSQL = "";
$sSQL = "SELECT a.*,b.* FROM loans a, loandocs b
		WHERE a.loanID = b.loanID
		AND (a.applyDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')
		ORDER BY a.applyDate ASC ";

$rs = &$conn->Execute($sSQL);
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
		<td colspan="8" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="8" height="40"><font color="#FFFFFF">' . $title . '<br>
			Dari ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="8"><font size=1>Cetak pada : ' . $today . '</font></td>
	</tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td colspan="8">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;No Rujukan</th>
					<th nowrap>&nbsp;Jenis Pembiayaan</th>
					<th nowrap>&nbsp;Jenis Pemprosesan</th>
					<th nowrap>&nbsp;No Anggota</th>
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;Jabatan/Cawangan</th>
					<th nowrap>&nbsp;Jumlah Pembiayaan</th>					
					<th nowrap>&nbsp;Tarikh Permohonan</th>
					<th nowrap>&nbsp;Tarikh Kelulusan</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($rs->fields(userID), "Text"));
		$totalsum = $totalsum + $rs->fields('loanAmt');
		print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td width="2%" align="right">' . $bil . ')&nbsp;</td>
			<td align="center">&nbsp;' . $rs->fields(loanNo) . '</td>
			<td>&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), "Number")) . '</a></td>
			<td>&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), "Number")) . '</a></td>
			<td align="center">&nbsp;</a>' . dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")) . '</td>
			<td>&nbsp;' . dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text")) . '</a></td>
			<td>&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Number")) . ' </a></td>
			<td align="right">' . number_format($rs->fields('loanAmt'), 2) . '&nbsp; </a></td>
			<td align="center">&nbsp;' . toDate("d/m/Y", $rs->fields(applyDate)) . '</a></td>
			<td align="center">&nbsp;' . toDate("d/m/Y", $rs->fields(ajkDate2)) . '</a></td>
		</tr>';
		$rs->MoveNext();
	}
	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td colspan="6" align="right">&nbsp;Jumlah Keseluruhan:</td>
					<td align="right">&nbsp;' . number_format($totalsum, 2) . '</td>
					<td colspan="4">&nbsp;</td>
			</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
			</tr>';
}
print '</table></td></tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr align="center"><td colspan="8"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table></body></html>';
