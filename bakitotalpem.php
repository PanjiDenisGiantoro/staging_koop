<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanYearly.php
 *          Date 		: 	12/09/2006
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
	exit;
}
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/mail.css" >
</head>
<body><table border="0" cellpadding="5" cellspacing="0" width="100%">';
$sqlLoan = "SELECT a.*, b.*	FROM users a, userdetails b WHERE a.userID=b.userID AND a.userID = '" . $pk . "'";
$rsLoan = $conn->Execute($sqlLoan);
while (!$rsLoan->EOF) {
	$i = 0;
	//$id = $rsLoan->fields(userID);
	//$loanType = $rsLoan->fields(loanType);
	//$loanID =$rsLoan->fields(loanID);
	$id = $rsLoan->fields(userID);
	//get deduct code

	$nama_Pembiayaan = dlookup("general", "name", "ID=" . tosql($rsLoan->fields(loanType), "Number"));
	$title  = 'Penyata Baki Pembiayaan';
	$title = strtoupper($title);


	$sSQL = "SELECT	a.*,b.* FROM loans a, loandocs b WHERE a.loanID=b.loanID AND a.userID = '" . $id . "' ORDER BY a.applyDate ASC";
	$rs = &$conn->Execute($sSQL);

	$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($id, "Text"));

	print '
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
		<td>:&nbsp;' . dlookup("userdetails", "memberID", "userID=" . tosql($id, "Text")) . '</td>
	</tr>
	<tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Nama&nbsp;Anggota</td>
		<td>:&nbsp;' . dlookup("users", "name", "userID=" . tosql($id, "Text")) . '</td>
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;No&nbsp;KP</td>
		<td>:&nbsp;' . dlookup("userdetails", "newIC", "userID=" . tosql($id, "Text")) . '</td>
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Jabatan</td>
		<td>:&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Text")) . '</td>
	</tr>
	<tr>
		<td colspan="2">
			<table border="1" cellpadding="2" cellspacing="0" align="left" width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap align="center">Bil</th>
					<th nowrap align="center">Nama Pembiayaan</th>
					<th nowrap align="center">No Bond</th>
					<th nowrap align="center">Baki Dibayar</th>
					<th nowrap align="center">Baki Sepatutnya</th>
					<th nowrap align="center">Baki (Dibayar - Sepatutnya)</th>
				</tr>';

	if ($rs->RowCount() <> 0) {
		while (!$rs->EOF) {
			//$bakiAwalSBP = getBakiALL($GetMember->fields(userID),$yrmth2,$bond);
			$bond = $rs->fields(rnoBond);
			$bayaran = getBakiALL($rs->fields(userID), $bond);

			//$jadualpayment = 
			$bakiOverall = ($bayaran - $jadualpayment);

			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							
							<td align="center">' . ++$i . '.</td>
							
							<td align="left">&nbsp;' . $rs->fields(loanNo) . ' - ' . dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), "Number")) . ' </td>
							
							<td align="center">' . $bond . '</td>

							<td align="center">' . number_format($bayaran, 2) . '</td>
							
							<td align="center">' . $jadualpayment . '</td>

							<td align="center">' . $bakiOverall . '</td>
						</tr>';
			$rs->MoveNext();
		}
	} else {
		print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Urusniaga -</b></td>
					</tr>';
	}

	print '		</table> 
		</td>
	</tr>';
	$rsLoan->MoveNext();
}

if ($rsLoan->RecordCount() < 1)
	print '	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="7" align="center"><b>- Tiada Urusniaga -</b></td>
		</tr>';

print '	
</table>
</body>
</html>';
