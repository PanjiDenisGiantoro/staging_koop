<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA4.php
 *		   Description	:	Report Senarai Keseluruhan Anggota
 *          Date 		: 	12/12/2003
 *********************************************************************************/
session_start();
if (!isset($dept))		$dept = "ALL";

include("common.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai bagi anggota yang sudah bersara dari perkhidmatan';

//--- Prepare department list

$sSQL = "";
$sSQL = "SELECT	a.name, a.loginID, a.email, 
				b.memberID, b.approvedDate, b.newIC, b.oldIC 
		 FROM 	users a, userdetails b
		 WHERE  a.userID = b.userID
		 AND 	b.status = '4'  ";
if ($dept <> "ALL")
	$sSQL .= " AND b.departmentID  = " . tosql($dept, "Number");
$sSQL .= " ORDER BY CAST( b.memberID AS SIGNED INTEGER ), b.approvedDate DESC ";
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
<!--p class="textFont">Pilihan Jabatan
		<select name="dept" class="textFont" onchange="document.MyForm.submit();">
			<option value="ALL">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '	</select>
</p-->
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border="0" cellpadding="2" cellspacing="1" align=left width="100%">';
$tempDept = '';
$bil = 0;
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		if ($bil == 0) {
			if ($tempDept <> "") {
				print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
									<td colspan="7" height="30" valign="bottom">Jumlah Anggota : <b>' . $bil . '</b></td>
								</tr>';
			} //'.$rs->fields(department).'							<tr><td colspan="7"  style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" height="30" valign="bottom">	Bersara </td></tr>
			print '

							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<td nowrap>&nbsp;</th>
								<td nowrap width="100" align="center">Nomor Anggota</td>
								<td nowrap align="left">Nama</td>
								<td nowrap width="80" align="center">Kartu Identitas</td>
								<td nowrap width="150">Emel</td>
								<td nowrap align="center" width="150">Tanggal Keanggotaan</td>
							</tr>';
			$bil = 0;
		}
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')</td>
							<td align="center">' . (int)$rs->fields(memberID) . '</a></td>
							<td>' . $rs->fields(name) . '</a></td>
							<td align="center">' . $rs->fields(newIC) . '</a></td>
							<td>' . $rs->fields(email) . '</a></td>
							<td align="center">' . toDate("d/m/Y", $rs->fields(approvedDate)) . '</a></td>
						</tr>';
		//$tempDept = $rs->fields(department);
		$rs->MoveNext();
	}
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" height="30" valign="bottom">Jumlah Anggota : <b>' . $bil . '</b></td>
					</tr>					
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" height="30" valign="bottom">Jumlah Keseluruhan Anggota : <b>' . $rs->RowCount() . '</b></td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	
</table>
</form>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
