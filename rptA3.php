<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA3.php
 *		   Description	:	Report Status Permohonan Anggota
 *		   Parameter	:   $dateFrom , $dateTo
 *          Date 		: 	12/12/2003
 *********************************************************************************/
session_start();
include("common.php");

$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$code 	= "C";
$title  = 'Permohonan Ditolak';

$sSQL = "";
$sSQL = "SELECT	a.name, a.loginID, b.newIC, b.oldIC, a.applyDate, b.rejectedDate, c.name as department  
		 FROM 	users a, userdetails b
		 INNER JOIN general c
		 ON		c.ID = b.departmentID 
		 WHERE  a.userID = b.userID 
		 AND 	b.status = '2'  
		 AND	rejectedDate >= " . tosql($dtFrom, "Text") . "
		 AND	rejectedDate <= " . tosql($dtTo, "Text") . "
		 ORDER BY department, rejectedDate DESC ";
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
		<td colspan="5" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="85 height="40"><font color="#FFFFFF">' . $title . '<br>
			Dari ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="7"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td colspan="7">&nbsp;</td></tr>
	<tr>
		<td colspan="7">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap>&nbsp;</td>
					<td nowrap><b>Nama</b></td>
					<td nowrap><b>Kad Pengenalan</b></td>
					<td nowrap><b>Cawangan/Zon</b></td>
					<td nowrap align="center"><b>Tarikh Permohonan</b></td>
					<td nowrap align="center"><b>Tarikh Ditolak</b></td>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')</td>
							<td>' . $rs->fields(name) . '</a></td>
							<td>' . $rs->fields(newIC) . ' / ' . $rs->fields(oldIC) . '</a></td>
							<td>' . $rs->fields(department) . ' </a></td>
							<td align="center">' . toDate("d/m/Y", $rs->fields(applyDate)) . '</a></td>
							<td align="center">' . toDate("d/m/Y", $rs->fields(rejectedDate)) . '</a></td>
						</tr>';
		$rs->MoveNext();
	}
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	
</table>
</body>
</html>
<tr><td colspan="7">&nbsp;</td></tr>
<center><tr><td colspan="7"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
