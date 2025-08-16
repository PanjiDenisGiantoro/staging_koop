<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptF1.php
 *		   Description	:	Report Ringkasan Keseluruhan Anggota
 *          Date 		: 	05/04/2004
 *********************************************************************************/
session_start();
include("common.php");

$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Keseluruhan Anggota Mengikut Jabatan';

$sSQL = "";
$sSQL = "SELECT	b.name as department, count(a.userID) as jumlah 
		 FROM 	userdetails a, general b
		 WHERE 	a.status = '1'  
	 	 AND	b.ID = a.departmentID 
		 GROUP BY department
		 ORDER BY department";
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
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . ' Pada ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap>&nbsp;</td>
					<td nowrap align="left">Nama Jabatan</td>
					<td nowrap align="center" width="200">Jumlah Anggota</td>
				</tr>';
$total = 0;
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')</td>
							<td>' . $rs->fields(department) . '</a></td>
							<td align="center">' . $rs->fields(jumlah) . '</a></td>
						</tr>';
		$total += $rs->fields(jumlah);
		$rs->MoveNext();
	}

	//-------------------------------------------------------------
	$sSQL = "";
	$sSQL = "SELECT	b.name as department, count(a.userID) as jumlah 
							 FROM 	userdetails a, general b
							 WHERE 	a.status = '4'  
							 AND	b.ID = a.departmentID 
							 GROUP BY status";
	$rs = &$conn->Execute($sSQL);

	print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . ++$bil . ')</td>
							<td>Bersara</a></td>
							<td align="center">' . $rs->fields(jumlah) . '</a></td>
						</tr>';
	$total += $rs->fields(jumlah);
	//------------------------------------------------------------
	/*					$sSQL = "";
					$sSQL = "SELECT count(userID) as jumlah 
							 FROM 	userdetails 
							 WHERE 	status in ('1','4') and departmentID <= 0";

					$rs = &$conn->Execute($sSQL);

						print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">'.++$bil.')&nbsp;</td>
							<td>&nbsp;Tiada Data</a></td>
							<td align="right">&nbsp;'.$rs->fields(jumlah).'</a>&nbsp;&nbsp;&nbsp;</td>
						</tr>';
					$total += $rs->fields(jumlah);
*/					//------------------------------------------------------------

	print '
					<tr bgcolor="FFFFFF"><td colspan="3"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan Anggota :</td>
						<td align="center"><b>' . $total . '</b></td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="3" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	
</table>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
