<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA10.php
 *		   Description	:	Laporan Senarai Anggota Yang Tidak Mempunyai Penama
 *          Date 		: 	12/12/2003
 *********************************************************************************/
session_start();
if (!isset($dept))		$dept = "ALL";

include("common.php");
include("koperasiinfo.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}


$title  = 'SENARAI ANGGOTA TIADA MAKLUMAT PENAMA';

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
$sSQL = 	"SELECT	a.name, b.memberID, b.w_name1 "
	. "FROM users a, userdetails b "
	. "WHERE a.userID = b.userID "
	. "AND b.status = '1'";

$sSQL .= " ORDER BY CAST( b.memberID AS SIGNED INTEGER )";
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

<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . ' PADA ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">';

print '
							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<td nowrap>&nbsp;</td>
								<td nowrap align="center">Nombor Anggota</td>
								<td nowrap align="left">Nama Anggota</td>
								<td nowrap align="left">Nama Penama</td>
							</tr>';
$bil = 0;

if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {

		if ($rs->fields('w_name1') == '') {
			$bil++;
			print '
							<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
								<td align="right">' . $bil . ')</td>
								<td align="center">' . $rs->fields('memberID') . '</a></td>
								<td>' . strtoupper($rs->fields('name')) . '</td>
								<td>' . strtoupper($rs->fields('w_name1')) . '</td>
							</tr>';
		}
		$rs->MoveNext();
	}
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" height="30" valign="bottom">Jumlah Anggota : <b>' . $bil . '</b></td>
					</tr>					
					';
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
