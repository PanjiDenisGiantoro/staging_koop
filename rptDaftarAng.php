<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptDaftarAng.php
 *		   Description	:	Report Senarai Daftar Anggota
 *          Date 		: 	12/12/2003
 *********************************************************************************/
session_start();
if (!isset($dept))		$dept = "ALL";

include("common.php");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$title  = 'Senarai Daftar Anggota';

$sSQL = "";
$sSQL = "SELECT a.name, CAST( b.memberID AS SIGNED INTEGER ) as memberID, b.approvedDate, b.newIC, b.job, 		b.address, b.postcode, b.city, c.name AS negeri, b.isApprovedTDate
		FROM users a, userdetails b
		INNER JOIN general c ON c.ID = b.stateID
		WHERE a.userID = b.userID
		AND (
		b.status = '1'
		OR b.status = '3')";
$sSQL .= " ORDER BY memberID";
$rs = &$conn->Execute($sSQL);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/default.css" >		
</head>
<body>';

print '<table border="0" cellpadding="5" cellspacing="0" width="100%">
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
			<table border="0" cellpadding="2" cellspacing="1" align=left width="100%">
							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<th nowrap>&nbsp;</th>
								<th nowrap width="100">&nbsp;Nombor Anggota</th>
								<th nowrap align="left">&nbsp;Nama</th>
								<th nowrap width="80">&nbsp;Nombor KP</th>
								<th nowrap width="80">&nbsp;Pekerjaan</th>
								<th nowrap width="200">&nbsp;Alamat</th>
								<th nowrap width="80">&nbsp;Poskod</th>
								<th nowrap width="80">&nbsp;Bandar</th>
								<th nowrap width="80">&nbsp;Negeri</th>
								<th nowrap align="center" width="150">&nbsp;Tarikh Keanggotaan</th>
							<th nowrap align="center" width="150">&nbsp;Tarikh Berhenti</th>
							</tr>';
$tempDept = '';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		if ($tempDept <> $rs->fields(department)) {
			if ($tempDept <> "") {
				print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
									<td colspan="7" height="30" valign="bottom">Jumlah Anggota : <b>' . $bil . '</b></td>
								</tr>';
			}
			print '
							<tr><td colspan="7"  style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" height="30" valign="bottom">
							Jabatan : ' . $rs->fields(department) . '</td></tr>
							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<th nowrap>&nbsp;</th>
								<th nowrap width="100">&nbsp;Nombor Anggota</th>
								<th nowrap align="left">&nbsp;Nama</th>
								<th nowrap width="80">&nbsp;Nombor KP</th>
								<th nowrap width="80">&nbsp;Pekerjaan</th>
								<th nowrap width="200">&nbsp;Alamat</th>
								<th nowrap width="80">&nbsp;Poskod</th>
								<th nowrap width="80">&nbsp;Bandar</th>
								<th nowrap width="80">&nbsp;Negeri</th>
								<th nowrap align="center" width="150">&nbsp;Tarikh Keanggotaan</th>
							<th nowrap align="center" width="150">&nbsp;Tarikh Berhenti</th>
							</tr>';
			$bil = 0;
		}

		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . (int)$rs->fields(memberID) . '</td>
							<td>&nbsp;' . $rs->fields(name) . '</td>
							<td>&nbsp;' . $rs->fields(newIC) . '</td>
							<td>&nbsp;' . $rs->fields(job) . '</td>
							<td>&nbsp;' . $rs->fields(address) . '</td>
							<td>&nbsp;' . $rs->fields(postcode) . '</td>
							<td>&nbsp;' . $rs->fields(city) . '</td>
							<td>&nbsp;' . $rs->fields(negeri) . '</td>
							<td align="center">&nbsp;' . toDate("d/m/Y", $rs->fields(approvedDate)) . '</td>
							<td align="center">&nbsp;' . toDate("d/m/Y", $rs->fields(isApprovedTDate)) . '</td>
						</tr>';
		$tempDept = $rs->fields(department);
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
