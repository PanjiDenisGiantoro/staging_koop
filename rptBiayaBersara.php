<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");
$month = (int)substr($yrmth, 4, 2);
$yr = (int)substr($yrmth, 0, 4);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Bayaran Bulanan Ahli Bersara Sehingga ' . displayBulan($month) . ' ' . $yr;
$title	= strtoupper($title);

$sSQL = "";
$sSQL = "SELECT a.name, b.memberID, b.monthFee, b.totalFee, c.approvedDate
		FROM users a, userdetails b, `userterminate` c
		WHERE a.userID = b.userID
		AND b.userID = c.userID
		AND c.status =4
		AND month( c.approvedDate ) < " . ++$month . "
		AND year( c.approvedDate ) < " . ++$yr;
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
		<th colspan="85 height="40"><font color="#FFFFFF">' . $title . '</font>
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
					<th nowrap>&nbsp;BIL</th>
					<th nowrap>&nbsp;NO ANGGOTA</th>
					<th nowrap>&nbsp;NAMA</th>
					<th nowrap>&nbsp;TARIKH MULA BERSARA</th>
					<th nowrap>&nbsp;JUMLAH YURAN MULA BERSARA(RP)</th>
					<th nowrap>&nbsp;JUMLAH YURAN TERKINI</th>
					<th nowrap>&nbsp;YURAN BULANAN</th>
					<th nowrap>&nbsp;2005</th>
					<th nowrap>&nbsp;2006</th>
					<th nowrap>&nbsp;2007</th>
					<th nowrap>&nbsp;CATITAN</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		//$time = explode(" ", $rs->fields(sendDate));
		//$rs->fields(referID);
		$jabatan = dlookup("general", "name", "ID=" . tosql($rs->fields(departmentID), "Number"));
		$jenis = dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), ""));
		$bil++;
		$dateBersara = toDate("d/m/y", $rs->fields(approvedDate));
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td>&nbsp;' . $bil . ' </td>
							<td>&nbsp;' . $rs->fields(memberID) . '</td>
							<td>&nbsp;' . $rs->fields(name) . '</td>
							<td>&nbsp;' . $dateBersara . '</td>
							<td align="right">&nbsp;' . number_format($rs->fields(totalFee), 2) . '</td>
							<td align="right">&nbsp;' . number_format($rs->fields(totalFee), 2) . '</td>
							<td align="right">&nbsp;' . number_format($rs->fields(monthFee), 2) . '</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>';
		$rs->MoveNext();
	}
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="11" align="center"><b>- Tiada Rekod Dicetak-</b></td>
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
