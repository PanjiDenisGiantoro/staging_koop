<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptF6.php
 *		   Description	:	Report Ringkasan Keseluruhan Anggota Mengikut Negeri
 *          Date 		: 	01/04/2004
 *********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
//--- Prepare negeri
$strSQL = '';
$i = 0;
$typeList = array();
$GetType = ctGeneral("", "H");
if ($GetType->RowCount() <> 0) {
	while (!$GetType->EOF) {
		$jumlah = 'jumlah' . $i++;
		array_push($typeList, $GetType->fields(name));
		$strSQL .= " , SUM(CASE 
					  WHEN a.stateID = " . $GetType->fields(ID) . " THEN 1
					  ELSE 0
					  END) AS " . $jumlah;
		$GetType->MoveNext();
	}
}

$title  = 'Ringkasan Keseluruhan Anggota Mengikut Negeri';
$sSQL = "";
$sSQL = "SELECT	b.name as department " . $strSQL . ", count(a.stateID) AS jumlahAnggota
		 FROM 	userdetails a, general b
		 WHERE 	a.status = '1'  
	 	 AND	b.ID = a.departmentID and a.stateID > 0
		 GROUP BY departmentID
		 ORDER BY departmentID";
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
					<th nowrap>&nbsp;</th>
					<th nowrap align="left">Nama Cabang/Zona</th>';
for ($cnt = 0; $cnt < count($typeList); $cnt++) {
	print '<th nowrap align="center" colspan="2" width="20">' . $typeList[$cnt] . '</th>';
}
print '				<th nowrap align="left">Jumlah</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')</td>
							<td>' . $rs->fields(department) . '</a></td>';
		$i = 0;
		$j = 0;
		for ($cnt = 0; $cnt < count($typeList); $cnt++) {
			$jumlah = 'jumlah' . $i++;

			if ($rs->fields($jumlah) > 0) $jumlahIni = $rs->fields($jumlah);
			else $jumlahIni = 0;
			if ($rs->fields('jumlahAnggota') > 0) $jumlahAnggota = $rs->fields('jumlahAnggota');
			else $jumlahAnggota = 0;
			if ($jumlahAnggota > 0) $percent = $jumlahIni /  $jumlahAnggota  * 100;
			else $percent = 0;
			//$percent = $rs->fields($jumlah) / $rs->fields('jumlahAnggota') * 100;					
			print '	<td align="right">&nbsp;' . $jumlahIni . '&nbsp;</a></td>';
			print '	<td align="right">';
			if ($percent == 0) print '0';
			else printf("%.1f", $percent);
			print '%</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
			$total[$cnt] += $jumlahIni;
			//$totalAll += $rs->fields($jumlah);
		}
		$totalAll += $jumlahAnggota;
		print '					<td align="center">' . $jumlahAnggota . '</td>
					</tr>';
		$rs->MoveNext();
	}

	//-----------------------------------

	$sSQL = "";
	$sSQL = "SELECT	 count(a.stateID) AS jumlahAnggota " . $strSQL . "
							 FROM 	userdetails a
							 WHERE 	a.status = '4' and a.stateID > 0 
							 GROUP BY a.status";
	$rs = &$conn->Execute($sSQL);

	print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . ++$bil . ')</td>
							<td>Bersara</a></td>';
	$i = 0;
	$j = 0;
	for ($cnt = 0; $cnt < count($typeList); $cnt++) {
		$jumlah = 'jumlah' . $i++;

		if ($rs->fields($jumlah) > 0) $jumlahIni = $rs->fields($jumlah);
		else $jumlahIni = 0;
		if ($rs->fields('jumlahAnggota') > 0) $jumlahAnggota = $rs->fields('jumlahAnggota');
		else $jumlahAnggota = 0;
		if ($jumlahAnggota > 0) $percent = $jumlahIni /  $jumlahAnggota  * 100;
		else $percent = 0;
		//$percent = $rs->fields($jumlah) / $rs->fields('jumlahAnggota') * 100;					
		print '	<td align="right">&nbsp;' . $jumlahIni . '&nbsp;</a></td>';
		print '	<td align="right">';
		if ($percent == 0) print '0';
		else printf("%.1f", $percent);
		print '%</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
		$total[$cnt] += $jumlahIni;
		//$totalAll += $rs->fields($jumlah);
	}
	$totalAll += $jumlahAnggota;
	print '	<td align="center">' . $jumlahAnggota . '</td>
					</tr>';

	//-----------------------------------
	//-----------------------------------

	$col = 3 + count($typeList) + count($typeList);
	print '
					<tr bgcolor="FFFFFF"><td colspan="' . $col . '"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah :</td>';
	$j = 0;
	for ($cnt = 0; $cnt < count($typeList); $cnt++) {
		$percentAll[$cnt] = ($total[$cnt] / $totalAll) * 100;
		print '<td align="right">&nbsp;<b>' . $total[$cnt] . '</b>&nbsp;</td>';
		print '<td align="right">&nbsp;<b>';
		if ($percentAll[$cnt] == 0) print '0';
		else printf("%.1f", $percentAll[$cnt]);
		print '%</b>&nbsp;&nbsp;&nbsp;</td>';
	}
	print '					<td align="center"><b>' . $totalAll . '</b></td>
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
