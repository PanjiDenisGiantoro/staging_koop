<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptF2.php
 *		   Description	:	Report Ringkasan Keseluruhan Anggota Mengikut Jantina
 *          Date 		: 	29/03/2004
 *********************************************************************************/
session_start();
include("common.php");

$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Keseluruhan Anggota Mengikut Jantina';
$sSQL = "";
$sSQL = "SELECT	b.name as department, 
	SUM(CASE WHEN a.sex = '0' THEN 1 ELSE 0 END) AS jumlahP,
	SUM(CASE WHEN a.sex = '1' THEN 1 ELSE 0 END) AS jumlahL
	
FROM 	userdetails a, general b
WHERE 	a.status = '1'  
AND	b.ID = a.departmentID 
GROUP BY department
ORDER BY department";

//$conn->debug = true;
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
					<td nowrap align="left">&nbsp;Nama Jabatan</td>
					<td nowrap align="center" width="120" colspan="3">&nbsp;Lelaki</td>
					<td nowrap align="center" width="120" colspan="3">&nbsp;Perempuan</td>
					<td nowrap align="center" width="50" >&nbsp;Jumlah</td>
				</tr>';
$totalL = 0;
$totalP = 0;
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		$jumlahAnggota = 0;
		$percentL = 0;
		$percentP = 0;
		$jumlahAnggota = $rs->fields(jumlahL) + $rs->fields(jumlahP);
		$percentL = ($rs->fields(jumlahL) / $jumlahAnggota) * 100;
		$percentP = ($rs->fields(jumlahP) / $jumlahAnggota) * 100;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(department) . '</a></td>
							<td align="right" width="70">&nbsp;' . $rs->fields(jumlahL) . ' </a>&nbsp;</td>
							<td align="right">&nbsp;';
		if ($percentL == 0) print '0';
		else printf("%.1f", $percentL);
		print '% </a>&nbsp;&nbsp;&nbsp;</td>
							<td width="10">&nbsp;</td>
							<td align="right" width="70">&nbsp;' . $rs->fields(jumlahP) . ' </a>&nbsp;</td>
							<td align="right">&nbsp;';
		if ($percentP == 0) print '0';
		else printf("%.1f", $percentP);
		print '% </a>&nbsp;&nbsp;&nbsp;</td>
							<td width="10">&nbsp;</td>
							<td align="right">&nbsp;' . $jumlahAnggota . '&nbsp;&nbsp;&nbsp;</td>
						</tr>';
		$totalL += $rs->fields(jumlahL);
		$totalP += $rs->fields(jumlahP);
		$rs->MoveNext();
	}
	//----------------------------------------------------------------------------
	$sSQL = "";
	$sSQL = "SELECT 
						SUM(CASE WHEN sex = '0' THEN 1 ELSE 0 END) AS jumlahP,
						SUM(CASE WHEN sex = '1' THEN 1 ELSE 0 END) AS jumlahL
						FROM 	userdetails
						WHERE 	status = '4'
						GROUP BY status";

	//$conn->debug = true;
	$rs = &$conn->Execute($sSQL);

	$jumlahAnggota = 0;
	$percentL = 0;
	$percentP = 0;
	$jumlahAnggota = $rs->fields(jumlahL) + $rs->fields(jumlahP);
	$percentL = ($rs->fields(jumlahL) / $jumlahAnggota) * 100;
	$percentP = ($rs->fields(jumlahP) / $jumlahAnggota) * 100;
	print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;Bersara</a></td>
							<td align="right" width="70">&nbsp;' . $rs->fields(jumlahL) . ' </a>&nbsp;</td>
							<td align="right">&nbsp;';
	if ($percentL == 0) print '0';
	else printf("%.1f", $percentL);
	print '% </a>&nbsp;&nbsp;&nbsp;</td>
							<td width="10">&nbsp;</td>
							<td align="right" width="70">&nbsp;' . $rs->fields(jumlahP) . ' </a>&nbsp;</td>
							<td align="right">&nbsp;';
	if ($percentP == 0) print '0';
	else printf("%.1f", $percentP);
	print '% </a>&nbsp;&nbsp;&nbsp;</td>
							<td width="10">&nbsp;</td>
							<td align="right">&nbsp;' . $jumlahAnggota . '&nbsp;&nbsp;&nbsp;</td>
						</tr>';
	$totalL += $rs->fields(jumlahL);
	$totalP += $rs->fields(jumlahP);

	//----------------------------------------------------------------------------
	/*						$sSQL = "";
						$sSQL = "SELECT	
							SUM(CASE WHEN sex = '0' THEN 1 ELSE 0 END) AS jumlahP,
							SUM(CASE WHEN sex = '1' THEN 1 ELSE 0 END) AS jumlahL
						FROM 	userdetails
						WHERE 	status in (1,4) and departmentID <= 0";

						//$conn->debug = true;
						$rs = &$conn->Execute($sSQL);
						
						$jumlahAnggota = 0;
						$percentL = 0;
						$percentP = 0;
						$jumlahAnggota = $rs->fields(jumlahL) + $rs->fields(jumlahP);
						$percentL = ($rs->fields(jumlahL) / $jumlahAnggota) * 100;
						$percentP = ($rs->fields(jumlahP) / $jumlahAnggota) * 100;
						print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">'.$bil.')&nbsp;</td>
							<td>&nbsp;Tiada Data</a></td>
							<td align="right" width="70">&nbsp;'.$rs->fields(jumlahL).' </a>&nbsp;</td>
							<td align="right">&nbsp;'; if ($percentL==0) print '0'; else printf ("%.1f",$percentL); print '% </a>&nbsp;&nbsp;&nbsp;</td>
							<td width="10">&nbsp;</td>
							<td align="right" width="70">&nbsp;'.$rs->fields(jumlahP).' </a>&nbsp;</td>
							<td align="right">&nbsp;'; if ($percentP==0) print '0'; else printf ("%.1f",$percentP); print '% </a>&nbsp;&nbsp;&nbsp;</td>
							<td width="10">&nbsp;</td>
							<td align="right">&nbsp;'.$jumlahAnggota.'&nbsp;&nbsp;&nbsp;</td>
						</tr>';
						$totalL += $rs->fields(jumlahL);
						$totalP += $rs->fields(jumlahP);
*/
	//----------------------------------------------------------------------------

	$totalAll = $totalL + $totalP;
	$percentTotalL = ($totalL / $totalAll) * 100;
	$percentTotalP = ($totalP / $totalAll) * 100;
	print '
					<tr bgcolor="FFFFFF"><td colspan="9"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah :</td>
						<td align="right">&nbsp;<b>' . $totalL . '</b>&nbsp;</td>
						<td align="right">&nbsp;<b>';
	if ($percentTotalL == 0) print '0';
	else printf("%.1f", $percentTotalL);
	print '%</b>&nbsp;&nbsp;&nbsp;</td>
						<td width="10">&nbsp;</td>

						<td align="right">&nbsp;<b>' . $totalP . '</b>&nbsp;</td>
						<td align="right">&nbsp;<b>';
	if ($percentTotalP == 0) print '0';
	else printf("%.1f", $percentTotalP);
	print '%</b>&nbsp;&nbsp;&nbsp;</td>
						<td width="10">&nbsp;</td>
						<td align="right">&nbsp;<b>' . $totalAll . '</b>&nbsp;&nbsp;&nbsp;</td>
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
