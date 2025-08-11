<?php

/*********************************************************************************
 *   Project		:	iKOOP.com.my
 *   Filename	: 	loanUserYearlyHLThnNew.php
 *	Description	:	Penyata Pembiayaan Tahunan Hutang Lapuk
 *   Date 		: 	25/12/2017
 *********************************************************************************/
session_start();
include("common.php");
$today = date("d-m-Y");

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
$sqlLoan = "SELECT a.* , (a.loanAmt * a.kadar_u /100 * a.loanPeriod /12) AS tot_untung, b.rnoBaucer FROM loans a, loandocs b
				WHERE a.loanID = b.loanID AND a.userID = '" . $pk . "' AND a.status = '7' order by a.loanNo ASC";

$rsLoan = $conn->Execute($sqlLoan);
while (!$rsLoan->EOF) {
	$i = 0;

	$sql = "select loanType FROM `loans` where status ='7' AND loanID = '" . $rsLoan->fields(loanID) . "'";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) $loanType = $Get->fields(loanType);

	$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

	$id = $rsLoan->fields(userID);
	$loanType = $rsLoan->fields(loanType);
	$loanNo = $rsLoan->fields(loanNo);    //add loan No ref style
	$loanID = $rsLoan->fields(loanID);
	//get deduct code
	$id_kod_potongan = dlookup("general", "c_Deduct", "ID=" . $loanType);
	$nama_Pembiayaan = dlookup("general", "name", "ID=" . tosql($rsLoan->fields(loanType), "Number"));
	$title  = 'Penyata ' . $nama_Pembiayaan . ' (' . $loanNo . ') Pada Tahun ' . $yr;
	$title = strtoupper($title);
	$bond = dlookup("loandocs", "rnoBond", "loanID=" . $rsLoan->fields(loanID));
	$sSQL = "";
	$sSQL = "SELECT	* FROM transactionhl WHERE userID = '$id' AND pymtRefer = '$bond' ORDER BY createdDate";
	$rs = &$conn->Execute($sSQL);

	//get loan maded total with interest
	$getJumlahLoan = "SELECT loanAmt + (loanAmt * kadar_u/100 * loanPeriod/12) AS jumlahPembiayaan FROM loans WHERE status ='7' AND loanID = " . $loanID;
	$rsJumlahLoan = $conn->Execute($getJumlahLoan);
	$jumlahPembiayaan = $rsJumlahLoan->fields(jumlahPembiayaan);


	$getTOpen = "SELECT 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND userID = '" . $id . "' 
		GROUP BY userID";
	$rsTOpen = $conn->Execute($getTOpen);
	$payment = $rsTOpen->fields(yuranKt);




	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transactionhl
		WHERE
		pymtRefer = '" . $bond . "'
		AND userID = '" . $id . "' 
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
	else $bakiAwal = 0;
	$bakiAkhir = 0;

	$getOpenKreditPokok = "SELECT *
		FROM transactionhl
		WHERE
		pymtRefer = '" . $bond . "'
		AND userID = '" . $id . "' AND
		deductID IN (1642,1645,1649,1646,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745,1781)
		";
	$rsOpenKPokok = $conn->Execute($getOpenKreditPokok);
	if ($rsOpenKPokok->RowCount() == 1) $bakiAwalKreditPokok = $rsOpenKPokok->fields(yuranKt);
	else $bakiAwalKreditPokok = 0;
	$bakiAkhir = 0;

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transactionhl
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID NOT IN (1642,1645,1649,1646,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745,1781)
		AND userID = '" . $id . "' 
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $yuranAwlKt = $rsOpen->fields(yuranKt);
	else $bakiAwalUnt = 0;
	$bakiAkhirUnt = 0;

	$bakiAwalUntgCmpr = $yuranAwlKt;

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
		<td width="20%">&nbsp;Alamat</td>
		<td>:&nbsp;' . dlookup("userdetails", "address", "userID=" . tosql($id, "Text")) . '</td>
	</tr>
	<tr>
		<td colspan="2">
		<table border=1  cellpadding="2" cellspacing="0" align=left width="100%">
		<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
		<th nowrap>Bil</th>
		<th width="15%">Tarikh</th>
		<th width="10%" align="left">&nbsp;Nombor rujukan</th>
		<th nowrap align="left">&nbsp;Item</th>
		<th width="10%">&nbsp;Debit</th>
		<th width="10%">&nbsp;Kredit</th>
		<th width="15%">&nbsp;Baki(RM)</th>
		</tr>';

	$totalakhir = $jumlahPembiayaan - $payment;


	print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td width="10%" colspan=2 align="right">&nbsp;</td>
		<td width="10%" align="right">&nbsp;</td>
		<td width="60%" align="left">&nbsp;<b>Jumlah Pembiayaan(Pokok + Untung)</b></td>
		<td width="10%" align="right">&nbsp;' . number_format($jumlahPembiayaan, 2) . '</td>
		<td width="10%" align="right">&nbsp;</td>
		<td width="15%" align="right">&nbsp;</td>
		</tr>';
	print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td width="10%" colspan=2 align="right">&nbsp;</td>
		<td width="10%" align="right">&nbsp;</td>
		<td width="60%" align="left">&nbsp;<b>Bayaran Pembiayaan (Sebelum Berhenti)</b></td>
		<td width="10%" align="right">&nbsp;</td>
		<td width="10%" align="right">&nbsp;' . number_format($payment, 2) . '</td>
		<td width="15%" align="right">&nbsp;' . number_format($totalakhir, 2) . '</td>
		</tr>';
	$totaldebit = 0;
	$totalkredit = 0;
	$totalkreditID = 0;
	while (!$rs->EOF) {
		$deductid_s = '';
		$deductid_s = $rs->fields(deductID);

		$debit = '';
		$kredit = '';
		$kreditID = '';
		if ($rs->fields(addminus) == 0) {
			$debit_t = $rs->fields(pymtAmt);
			$totaldebit += $debit_t;
			$debit = number_format($debit_t, 2);
		}
		$kreditID = $rs->fields(pymtAmt);
		$totalkreditID += $kreditID;
		$baki = $kreditID - $debit_t;
		$bakipembayaran = $totalakhir - $baki;
		$bakiterakhir = $bakipembayaran - $bakiloop;
		print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td width="5%" align="center">' . ++$i . '.</td>
		<td width="10%" align="center">&nbsp;' . toDate('d/m/y', $rs->fields(createdDate)) . '</td>
		<td width="10%">&nbsp;' . $rs->fields(docNo) . '</td>
		<td align="left">&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), "Number")) . '</td>
		<td width="10%" align="right">&nbsp;</td>;	
		<td width="10%" align="right">&nbsp;' . number_format($baki, 2) . '</td>
		<td width="10%" align="right">&nbsp;' . number_format($bakiterakhir, 2) . '</td>
		</tr>';
		$rs->MoveNext();
		$bakiloop += $baki;
	}




	print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="8" align="center"><b>- Tiada Rekod Urusniaga -</b></td>
		</tr>';
	print '</table> </td></tr>';
	$rsLoan->MoveNext();
}
if ($rsLoan->RecordCount() < 1)
	print '	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="7" align="center"><b>- Tiada Rekod -</b></td>
		</tr>';
print '	
</table>
</body>
</html>';
