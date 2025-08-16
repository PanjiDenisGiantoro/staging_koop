<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA19.php
 *		   Description	:	Report Senarai Anggota Ada Maklumat Bank
 *          Date 		: 	12/12/2021
 *********************************************************************************/
session_start();
if (!isset($dept))		$dept = "ALL";

include("common.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'SENARAI ANGGOTA MEMPUNYAI MAKLUMAT AKAUN BANK';

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
$sSQL = "SELECT	a.name,CAST(b.memberID AS SIGNED INTEGER) as memberID, b.approvedDate,b.newIC,b.accTabungan,b.bankID
		 FROM 	users a, userdetails b
		 WHERE  a.userID = b.userID
		 AND 	b.status = '1'
		 AND    BIT_LENGTH(b.accTabungan) > 0";

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
								<th nowrap>&nbsp;</th>
								<th nowrap width="100" align="center">&nbsp;Nombor Anggota</th>
								<th nowrap align="left">&nbsp;Nama Anggota</th>
								<th nowrap width="150" align="center">&nbsp;Kad Pengenalan</th>
								<th nowrap width="200" align="left">&nbsp;Akaun Bank</th>
								<th nowrap width="200" align="left">&nbsp;Nama Bank</th>
								<th nowrap align="center" width="150">&nbsp;Tarikh Keanggotaan</th>
							</tr>';
$bil = 0;

if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {

		$bank 		= $rs->fields(bankID);
		$namabank 	= dlookup("general", "name", "ID=" . $bank);

		if ($rs->fields(accTabungan) == '') $rs->MoveNext();


		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td align="center">&nbsp;' . $rs->fields(memberID) . '</a></td>
							<td >&nbsp;' . strtoupper($rs->fields(name)) . '</a></td>
							<td align="center">&nbsp;' . $rs->fields(newIC) . '</a></td>
							<td align="left">&nbsp;' . $rs->fields(accTabungan) . '</a></td>
							<td align="left">&nbsp;' . $namabank . '</a></td>
							<td align="center">&nbsp;' . toDate("d/m/Y", $rs->fields(approvedDate)) . '</a></td>
						</tr>';
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
	<tr><td>&nbsp;</td></tr>
	<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</form>
</body>
</html>';
