<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	feesYearlyAll.php
 *          Date 		: 	16/12/2003
 *********************************************************************************/
session_start();
include("common.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
	exit;
}

$title  = 'Penyata Pokok Keseluruhan Anggota';
$title = strtoupper($title);

$sSQL = "SELECT * FROM transaction 
		 WHERE  userID = " . tosql($id, "Text") . "
		 and deductid in (1596,1780,1607) 
		 ORDER BY createdDate";
$rs = &$conn->Execute($sSQL);

$getYuranOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		deductID in (1596,1780,1607) 
		AND userID = '" . $id . "' 
		GROUP BY userID";
$rsYuranOpen = $conn->Execute($getYuranOpen);
$bakiAwal = 0;
$bakiAkhir = 0;

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/mail.css" >
</head>
<body>';
$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($id, "Text"));
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="2" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="2" height="40"><font color="#FFFFFF">' . $title . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="2"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Nombor&nbsp;Anggota</td>
		<td>:&nbsp;' . dlookup("userdetails", "memberID", "userID=" . tosql($id, "Text")) . '</td>
	</tr>
	<tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Nama&nbsp;Anggota</td>
		<td>:&nbsp;' . dlookup("users", "name", "userID=" . tosql($id, "Text")) . '</td>
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Kad&nbsp;Pengenalan</td>
		<td>:&nbsp;' . dlookup("userdetails", "newIC", "userID=" . tosql($id, "Text")) . '</td>
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Cawangan/Zon</td>
		<td>:&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Text")) . '</td>
	</tr>
	<tr>
		<td colspan="2">
			<table border=1  cellpadding="2" cellspacing="0" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap><b>Bil</b></td>
					<td nowrap><b>Tarikh</b></td>
					<td nowrap><b>Nombor Rujukan</b></td>
					<td nowrap><b>Item</b></td>
					<td nowrap align="right"><b>Debit(RP)</b></td>
					<td nowrap align="right"><b>Kredit(RP)</b></td>
				</tr>';
print '
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td width="20%" colspan=3 align="right">&nbsp;</td>
					<td width="60%" align="left">&nbsp;<b>Baki H/B</b></td>
					<td width="10%" align="right">&nbsp;</td>
					<td width="10%" align="right">&nbsp;' . number_format($bakiAwal, 2) . '&nbsp;</td>
				</tr>';
$totaldebit = 0;
$totalkredit = 0;

$i = 0;
if ($rs->RowCount() <> 0) {

	while (!$rs->EOF) {
		$debit = '';
		$kredit = '';
		if ($rs->fields(addminus) == 0) {
			$debit = $rs->fields(pymtAmt);
			$totaldebit += $debit;
			$debit = number_format($debit, 2);
		} else {
			$kredit = $rs->fields(pymtAmt);
			$totalkredit += $kredit;
			$kredit = number_format($kredit, 2);
		}
		$deductid = $rs->fields(deductID);
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="5%" align="center">' . ++$i . '.</td>
							<td width="10%" align="center">&nbsp;' . toDate('d/m/y', $rs->fields(createdDate)) . '&nbsp;</td>
							<td width="10%">&nbsp;' . $rs->fields(docNo) . '</td>
							<td width="60%" align="left">&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), "Number")) . '&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . $debit . '&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . $kredit . '&nbsp;</td>
						</tr>';
		$rs->MoveNext();
	}
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight:bold;" bgcolor="FFFFFF">
					<td width="20%" colspan=3 align="right">&nbsp;</td>
						<td width="60%" align="left">Jumlah &nbsp;&nbsp;&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . number_format($totaldebit, 2) . '&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . number_format($totalkredit, 2) . '&nbsp;</td>
					</tr>';
	$bakiBB = $bakiAwal + ($totalkredit - $totaldebit);
	print '
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td width="20%" colspan=3 align="right">&nbsp;</td>
					<td width="60%" align="left">&nbsp;<b>Baki B/B</b></td>
					<td width="10%" align="right">&nbsp;</td>
					<td width="10%" align="right">&nbsp;<b>' . number_format($bakiBB, 2) . '</b>&nbsp;</td>
				</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod yuran </b></td>
					</tr>';
}

print '		</table> 
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>';

print '	
</table>
</body>
</html>';
