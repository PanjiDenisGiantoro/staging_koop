<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA2.php
 *		   Description	:	Report Status Kelulusan Anggota
 *		   Parameter	:   $dateFrom , $dateTo
 *          Date 		: 	12/12/2003
 *********************************************************************************/
session_start();
include("common.php");
include("setupinfo.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$code 	= "B";
$title  = 'Kelulusan Anggota';

$sSQL = "";
$sSQL = "SELECT	a.name, a.loginID, b.memberID, b.newIC, b.oldIC,b.staftNo ,b.monthFee, a.applyDate, b.approvedDate, c.name as department  
		 FROM 	users a, userdetails b
		 INNER JOIN general c
		 ON		c.ID = b.departmentID 
		 WHERE  a.userID = b.userID 
		 AND 	b.status = '1'  
		 AND	approvedDate >= " . tosql($dtFrom, "Text") . "
		 AND	approvedDate <= " . tosql($dtTo, "Text") . "
		 ORDER BY CAST( b.memberID AS SIGNED INTEGER ), department, approvedDate DESC ";
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
		<td colspan="7" align="right">' . strtoupper($emaNetis) . '</td>
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
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" >
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap>&nbsp;</td>
					<td nowrap align="center"><b>Nombor Anggota</b></td>
					<td nowrap><b>Nama</b></td>
					<td nowrap align="center"><b>Kad Pengenalan</b></td>
					<td nowrap align="center"><b>No Pekerja</b></td>
					<td nowrap align="right"><b>Yuran Bulanan (RP)</b></td>
					<td nowrap><b>Cawangan/Zon</b></td>
					<td nowrap align="center"><b>Tarikh Memohon</b></td>
					<td nowrap align="center"><b>Tarikh Diluluskan</b></td>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="center">' . $bil . ')</td>
							<td align="center">' . $rs->fields(memberID) . '</a></td>
							<td>' . $rs->fields(name) . '</a></td>
							<td align="center">' . $rs->fields(newIC) . '</a></td>
							<td align="center">' . $rs->fields(staftNo) . '</a></td>
							<td align="right">' . $rs->fields(monthFee) . ' </a></td>
							<td>' . $rs->fields(department) . ' </a></td>
							<td align="center">' . toDate("d/m/Y", $rs->fields(applyDate)) . '</a></td>
							<td align="center">' . toDate("d/m/Y", $rs->fields(approvedDate)) . '</a></td>
						</tr>';
		$rs->MoveNext();
	}
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
<tr><td colspan="7">&nbsp;</td></tr>
<center><tr><td colspan="7"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
