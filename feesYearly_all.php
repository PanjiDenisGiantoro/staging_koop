<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	feesYearly_all.php
 *          Date 		: 	16/12/2003
 *********************************************************************************/
session_start();
include("common.php");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
	exit;
}

$title  = 'Laporan Wajib Anggota Pada Tahun ' . $yr;
$title = strtoupper($title);
$sSQL2 = "select * from transaction 
		 WHERE deductid in (1595,1607) 
		 and yrmth like " . tosql($yr . "%", "Text") . " and UserID > 4265
		 group by USERID ORDER BY UserID";

$rs2 = &$conn->Execute($sSQL2);

while (!$rs2->EOF) {

	$sSQL = "select * from transaction 
		 WHERE  userID = '" . $rs2->fields(userID) . "'
		 and deductid in (1595,1607) 
		 and yrmth like " . tosql($yr . "%", "Text") . "
		 ORDER BY UserID";

	$rs1 = &$conn->Execute($sSQL);
	$jabatan = dlookup("userdetails", "departmentID", "userID='" . $rs2->fields(userID) . "'");

	$getWajibOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE UserID ='" . $rs2->fields(userID) . "' and
		deductID in (1595,1607)
		AND year(createdDate) < " . $yr . "
		GROUP BY userID order by UserID";
	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) $bakiAwal = $rsWajibOpen->fields(yuranKt) - $rsWajibOpen->fields(yuranDb);
	else $bakiAwal = 0;
	$bakiAkhir = 0;

	print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/mail.css" >
</head>
<body>';

	print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="2" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#336699" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="2" height="40"><font color="#FFFFFF">' . $title . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="2"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;No&nbsp;Anggota</td>
		<td>:&nbsp;' . dlookup("userdetails", "memberID", "userID='" . $rs2->fields(userID) . "'") . '</td>
	</tr>
	<tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Nama&nbsp;Anggota</td>
		<td>:&nbsp;' . dlookup("users", "name", "userID='" . $rs2->fields(userID) . "'") . '</td>
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;No&nbsp;KP</td>
		<td>:&nbsp;' . dlookup("userdetails", "newIC", "userID='" . $rs2->fields(userID) . "'") . '</td>
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Jabatan</td>
		<td>:&nbsp;' . dlookup("general", "name", "userID='" . $rs2->fields(userID) . "'") . '</td>
	</tr>
	<tr>
		<td colspan="2">
			<table border=1  cellpadding="2" cellspacing="0" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>Bil</th>
					<th nowrap>Tanggal</th>
					<th nowrap>&nbsp;Nombor rujukan</th>
					<th nowrap>&nbsp;Item</th>
					<th nowrap>&nbsp;Debit(RP)</th>
					<th nowrap>&nbsp;Kredit(RP)</th>
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
	if ($rs1->RowCount() <> 0) {

		while (!$rs1->EOF) {
			$debit = '';
			$kredit = '';
			if ($rs1->fields(addminus) == 0) {
				$debit = $rs1->fields(pymtAmt);
				$totaldebit += $debit;
				$debit = number_format($debit, 2);
			} else {
				$kredit = $rs1->fields(pymtAmt);
				$totalkredit += $kredit;
				$kredit = number_format($kredit, 2);
			}
			$deductid = $rs1->fields(deductID);
			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="5%" align="center">' . ++$i . '.</td>
							<td width="10%" align="center">&nbsp;' . toDate('d/m/y', $rs1->fields(createdDate)) . '&nbsp;</td>
							<td width="10%">&nbsp;' . $rs1->fields(docNo) . '</td>
							<td width="60%" align="left">&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs1->fields(deductID), "Number")) . '&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . $debit . '&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . $kredit . '&nbsp;</td>
						</tr>';
			$rs1->MoveNext();
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
	$rs2->MoveNext();
}
print '</table></body></html>';
