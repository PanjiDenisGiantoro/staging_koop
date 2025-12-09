<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *********************************************************************************/
session_start();
include("common.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Emel Dihantar';

$sSQL = "";
$sSQL = "SELECT distinct a.sendDate, b.name, c.memberID, c.newIC, d.name as department, e.title
		FROM `letterLog` a inner join users b on a.userID = b.userID inner join userdetails c on a.userID = c.userID inner join general d on c.departmentID = d.ID inner join letters e on e.ID = a.letterID
		WHERE a.TYPE = 'EMAIL'
		AND a.sendDate >= '" . $dtFrom . ' 00:00:00' . "'
		AND a.sendDate <= '" . $dtTo . ' 23:59:59' . "'
		ORDER BY a.ID";
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
		<td colspan="5"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;Nomor Anggota</th>
					<th nowrap>&nbsp;Nombor KP Baru</th>
					<th nowrap>&nbsp;Cawangan/Jabatan</th>
					<th nowrap>&nbsp;Jenis surat</th>
					<th nowrap>&nbsp;Tanggal Hantar</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		//$time = explode(" ", $rs->fields(sendDate));
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(name) . '</a></td>
							<td align="center">&nbsp;' . $rs->fields(memberID) . '</a></td>
							<td align="center">&nbsp;' . $rs->fields(newIC) . '</a></td>
							<td>&nbsp;' . $rs->fields(department) . ' </a></td>
							<td>&nbsp;' . $rs->fields(title) . ' </a></td>
							<td align="center">&nbsp;' . toDate("d/m/Y", $rs->fields(sendDate)) . '  ' . $time[1] . '</a></td>
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
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr align="center"><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
