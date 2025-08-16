<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptF.php
 *		   Description	:	Report Status Pembatalan Pinjaman
 *		   Parameter	:   $dateFrom , $dateTo
 *          Date 		: 	12/12/2003
 *********************************************************************************/
include("common.php");

$today = date("F j, Y");
if (get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Pembiayaan Dibatalkan';
//AND	rejectedDate >= ".tosql($dtFrom , "Text")."
//AND	rejectedDate <= ".tosql($dtTo , "Text")."

$sSQL = "";
$sSQL = "SELECT	* FROM 	loans 
		 WHERE  status = '5'  
		AND (
		cancelDate
		BETWEEN '" . $dtFrom . "'
		AND '" . $dtTo . "')
		ORDER BY cancelDate DESC ";
$rs = &$conn->Execute($sSQL);

//<th colspan="8" height="40"><font color="#FFFFFF">'.$title.'<br>
//	Dari '.toDate("d/m/Y",$dtFrom).' Hingga '.toDate("d/m/Y",$dtTo).'</font>
//</th>
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
		<td colspan="9" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="8" height="40"><font color="#FFFFFF">' . $title . '<br>
			Dari ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="9"><font size=1>Cetak pada : ' . $today . '</font></td>
	</tr>
	<tr><td colspan="9">&nbsp;</td></tr>
	<tr>
		<td colspan="9">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;No Rujukan ID</th>
					<th nowrap>&nbsp;Jenis Pinjaman</th>
					<th nowrap>&nbsp;No Anggota</th>
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;Jabatan/Cawangan</th>
					<th nowrap>&nbsp;Jumlah Pinjaman</th>					
					<th nowrap>&nbsp;Tarikh Memohon</th>
					<th nowrap>&nbsp;Tarikh Dibatalkan</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($rs->fields(userID), "Text"));
		$sumtotal = $sumtotal + $rs->fields('loanAmt');
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(loanNo) . '</td>
							<td>&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), "Number")) . '</a></td>
							<td>&nbsp;</a>' . dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")) . '</td>
							<td>&nbsp;' . dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text")) . '</a></td>
							<td>&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Number")) . ' </a></td>
							<td align="right">' . number_format($rs->fields('loanAmt'), 2) . '&nbsp; </a></td>
							<td align="center">&nbsp;' . toDate("d/m/Y", $rs->fields(applyDate)) . '</a></td>
							<td align="center">&nbsp;' . toDate("d/m/Y", $rs->fields(cancelDate)) . '</a></td>
						</tr>';
		$rs->MoveNext();
	}
	print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="6">&nbsp;Jumlah Keseluruhan: </td>
							<td align="right">' . number_format($sumtotal, 2) . '&nbsp; </a></td>
							<td align="center" colspan="2">&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="9" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	</table>
</body>
</html>
<tr><td colspan="9">&nbsp;</td></tr>
<center><tr><td colspan="9"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
