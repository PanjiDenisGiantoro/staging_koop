<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA5.php
 *		   Description	:	Report Senarai Keseluruhan Anggota
 *		   Parameter	:   $dateFrom , $dateTo
 *          Date 		: 	07/04/2004
 *********************************************************************************/
session_start();
if (!isset($dept))		$dept = "ALL";

include("common.php");
include("koperasiinfo.php");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Maklumat Simpanan Anggota';

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status = 1 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

$sSQL = "";
$sSQL = "SELECT	a.name, a.loginID, a.email, 
				b.memberID, b.memberDate, b.newIC, b.oldIC, c.name as department,
				b.totalFee, b.totalShare
		 FROM 	users a, userdetails b
		 INNER JOIN general c
		 ON		c.ID = b.departmentID 
		 WHERE  a.userID = b.userID 
		 AND 	b.status = '1'  ";
if ($dept <> "ALL")
	$sSQL .= " AND b.departmentID  = " . tosql($dept, "Number");
$sSQL .= " ORDER BY department, approvedDate DESC ";
$rs = &$conn->Execute($sSQL);
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
<p class="textFont">Pilihan Jabatan
		<select name="dept" class="textFont" onchange="document.MyForm.submit();">
			<option value="ALL">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '	</select>
</p>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#0c479d" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . ' Pada ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">';
$tempDept = '';
$amtFee = 0;
$amtShare = 0;
$totalFee = 0;
$totalShare = 0;
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		if ($tempDept <> $rs->fields(department)) {
			if ($tempDept <> "") {
				print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
									<td colspan="6" height="30" align="right">Jumlah Simpanan :&nbsp;</td>
									<td align="right"><b>' . sprintf("%01.2f", $amtFee) . '</b>&nbsp;</td>
									<td align="right"><b>' . $amtShare . '</b>&nbsp;</td>
								</tr>';
			}
			print '
							<tr><td colspan="8"  style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" height="30" valign="bottom">
							Jabatan : ' . $rs->fields(department) . '</td></tr>
							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<td nowrap rowspan="2" >&nbsp;</td>
								<td nowrap rowspan="2" width="100">&nbsp;Nomor Anggota</td>
								<td nowrap rowspan="2" align="left">&nbsp;Nama</td>
								<td nowrap rowspan="2" width="80">&nbsp;Nombor KP Baru</td>
								<td nowrap rowspan="2" width="80">&nbsp;Nombor KP Lama</td>
								<td nowrap rowspan="2" width="150">&nbsp;Email</td>
								<td nowrap colspan="2" align="center">&nbsp;Simpanan</td>
							</tr>
							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<th nowrap align="center">&nbsp;Yuran</th>
								<th nowrap align="center">&nbsp;Syer</th>
							</tr>';
			$bil = 0;
			$amtFee = 0;
			$amtShare = 0;
		}
		$bil++;
		$amtFee += $rs->fields(totalFee);
		$totalFee += $amtFee;
		$amtShare += $rs->fields(totalShare);
		$totalShare += $amtShare;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(memberID) . '</a></td>
							<td>&nbsp;' . $rs->fields(name) . '</a></td>
							<td>&nbsp;' . $rs->fields(newIC) . '</a></td>
							<td>&nbsp;' . $rs->fields(oldIC) . '</a></td>
							<td>&nbsp;' . $rs->fields(email) . '</a></td>
							<td align="right">&nbsp;' . $rs->fields(totalFee) . '&nbsp;</td>
							<td align="right">&nbsp;' . $rs->fields(totalShare) . '&nbsp;</td>
						</tr>';
		$tempDept = $rs->fields(department);
		$rs->MoveNext();
	}
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" height="30" align="right">Jumlah Simpanan :&nbsp;</td>
						<td align="right"><b>' . sprintf("%01.2f", $amtFee) . '</b>&nbsp;</td>
						<td align="right"><b>' . $amtShare . '</b>&nbsp;</td>
					</tr>					
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" height="30" align="right">Jumlah Keseluruhan Simpanan :&nbsp;</td>
						<td align="right"><b>' . sprintf("%01.2f", $totalFee) . '</b>&nbsp;</td>
						<td align="right"><b>' . $totalShare . '</b>&nbsp;</td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</form>
</body>
</html>';
