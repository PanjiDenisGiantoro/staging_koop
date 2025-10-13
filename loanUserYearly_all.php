<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanUserYearly.php
 *		   Description	:	Penyata Pembiayaan Tahunan
 *          Date 		: 	13/7/2006
 *********************************************************************************/
include("common.php");
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
$sqlLoan = "SELECT a . * , (
				a.loanAmt * a.kadar_u /100 * a.loanPeriod /12
				) AS tot_untung, b.rnoVoucher
				FROM loans a, loandocs b
				WHERE a.loanID = b.loanID and a.UserID > 2520
				order by a.UserID ASC";

//SELECT a.*, (a.loanAmt * a.kadar_u/100 * a.loanPeriod/12) as tot_untung, b.rnoVoucher  FROM loans a, loandoc b WHERE a.loanID = b.loanID and b.rnoVoucher <> '' and a.userID = '".$pk."'";

$rsLoan = $conn->Execute($sqlLoan);
while (!$rsLoan->EOF) {
	$i = 0;

	$sql = "select loanType FROM `loans` where loanID = '" . $rsLoan->fields(loanID) . "'";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) $loanType = $Get->fields(loanType);

	$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

	//$id = $rsLoan->fields(userID);
	//$loanType = $rsLoan->fields(loanType);
	//$loanID =$rsLoan->fields(loanID);

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
	//		 AND deductID = ".$id_kod_potongan." 
	$sSQL = "";
	$sSQL = "SELECT	*  
		 FROM transaction 
		 WHERE pymtRefer = '$bond' and deductID not IN (1595,1596,1693,1593,1694) and pymtRefer <> '' and UserID > 2520
		 AND year(createdDate) = $yr ORDER BY UserID ASC";
	//AND yrmth like " . tosql("%".$yr."%","Text"); 
	$rs = &$conn->Execute($sSQL);


	//get loan maded total with interest
	$getJumlahLoan = "SELECT loanAmt + (loanAmt * kadar_u/100 * loanPeriod/12) AS jumlahPembiayaan
				  FROM loans  
				  WHERE loanID = " . $loanID;
	$rsJumlahLoan = $conn->Execute($getJumlahLoan);
	$jumlahPembiayaan = $rsJumlahLoan->fields(jumlahPembiayaan);
	//				  AND deductID = ".$id_kod_potongan." 
	/*					 
$getJumlahBayar = "SELECT SUM(pymtAmt) AS jumlahBayar 
                  FROM transaction  
				  WHERE userID = '".$id."'
				  AND LEFT(yrmth,4) < ".$yr."
				  AND pymtRefer = '".$bond."' 
				  GROUP BY pymtRefer";
$rsJumlahBayar = $conn->Execute($getJumlahBayar);				 
$jumlahBayar = $rsJumlahBayar->fields(jumlahBayar);
$bakiAwal = $jumlahPembiayaan - $jumlahBayar;

$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '".$bond."'
		AND userID = '".$id."' 
		AND year(createdDate) < ".$yr."
		GROUP BY userID";
$rsOpen = $conn->Execute($getOpen);
if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
else $bakiAwal = 0;
$bakiAkhir = 0;
*/
	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "' and deductID not IN (1595,1596,1693,1593,1694) and pymtRefer <>''
		AND year(createdDate) < " . $yr . "
		GROUP BY userID order by UserID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
	else $bakiAwal = 0;
	$bakiAkhir = 0;

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		and deductID not IN (1595,1596,1693,1593,1694)and pymtRefer <>''
		AND year(createdDate) < " . $yr . "
		GROUP BY userID order by UserID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwalUnt = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwalUnt = 0;
	$bakiAkhirUnt = 0;

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
			<table border=1  cellpadding="2" cellspacing="0" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>Bil</th>
					<th nowrap>Tarikh</th>
					<th nowrap>&nbsp;Nombor rujukan</th>
					<th nowrap>&nbsp;Item</th>
					<th nowrap>&nbsp;Untung</th>
					<th nowrap>&nbsp;Debit(RP)</th>
					<th nowrap>&nbsp;Kredit(RP)</th>
				</tr>';
	print '
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td width="10%" colspan=3 align="right">&nbsp;</td>
					<td width="60%" align="left">&nbsp;<b>Baki H/B</b></td>
					<td width="10%" align="right">&nbsp;</td>
					<td width="10%" align="right">&nbsp;' . number_format($bakiAwal, 2) . '</td>
					<td width="10%" align="right">&nbsp;&nbsp;</td>
				</tr>';
	$totaldebit = 0;
	$totalkredit = 0;
	$totalkreditID = 0;
	if ($rs->RowCount() <> 0) {
		while (!$rs->EOF) {
			$deductid_s = '';
			$deductid_s = $rs->fields(deductID);

			$debit = '';
			$kredit = '';
			//$untung = '';
			$kreditID = '';
			if ($rs->fields(addminus) == 0) {
				$debit = $rs->fields(pymtAmt);
				$totaldebit += $debit;
				$debit = number_format($debit, 2);
			} else {

				if ($deductid_s == 1642  or  $deductid_s == 1645 or $deductid_s == 1649 or $deductid_s == 1651 or $deductid_s == 1652 or $deductid_s == 1653 or $deductid_s == 1654 or $deductid_s == 1656 or $deductid_s == 1657 or $deductid_s == 1658 or $deductid_s == 1659 or $deductid_s == 1663 or $deductid_s == 1674 or $deductid_s == 1676 or $deductid_s == 1643  or $deductid_s == 1678 or $deductid_s == 1669 or $deductid_s == 1668 or $deductid_s == 1672 or $deductid_s == 1680) {

					$kreditID = $rs->fields(pymtAmt);
					$totalkreditID += $kreditID;
					$kreditID = number_format($kreditID, 2);
				} else {

					$kredit = $rs->fields(pymtAmt);
					$totalkredit += $kredit;
					$kredit = number_format($kredit, 2);
				}
			}

			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="5%" align="center">' . ++$i . '.</td>
							<td width="10%" align="center">&nbsp;' . toDate('d/m/y', $rs->fields(createdDate)) . '</td>
							<td width="10%">&nbsp;' . $rs->fields(docNo) . '</td>
							<td align="left">&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), "Number")) . '</td>
							<td width="10%" align="right">&nbsp;';
			if ($deductid_s == 1642  or  $deductid_s == 1645 or $deductid_s == 1649 or $deductid_s == 1651 or $deductid_s == 1652 or $deductid_s == 1653 or $deductid_s == 1654 or $deductid_s == 1656 or $deductid_s == 1657 or $deductid_s == 1658 or $deductid_s == 1659 or $deductid_s == 1663 or $deductid_s == 1674 or $deductid_s == 1676 or $deductid_s == 1643  or $deductid_s == 1678 or $deductid_s == 1669 or $deductid_s == 1668 or $deductid_s == 1672 or $deductid_s == 1680) {
				echo $kreditID;
				//echo $deductid_s ;							
			} else {
				echo "";
				//echo $deductid_s ; 
			}

			print '</td>
							<td width="10%" align="right">&nbsp;' . $debit . '</td>
							<td width="10%" align="right">&nbsp;' . $kredit . '</td>
						</tr>';
			$rs->MoveNext();
		}

		$sumDebit = $totaldebit + $bakiAwal;

		print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight:bold;" bgcolor="FFFFFF">
					<td width="10%" colspan=3 align="right">&nbsp;</td>
						<td width="60%" align="left">Jumlah &nbsp;&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . number_format($totalkreditID, 2) . '</td>
							<td width="10%" align="right">&nbsp;' . number_format($sumDebit, 2) . '</td>
							<td width="10%" align="right">&nbsp;' . number_format($totalkredit, 2) . '</td>
					</tr>';

		$bakiHB = $bakiAwal + $totaldebit;
		$bakiBB = $bakiHB - $totalkredit;
		print '
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td width="10%" colspan=3 align="right">&nbsp;</td>
					<td width="60%" align="left">&nbsp;<b>Baki B/B</b></td>
					<td width="10%" align="right">&nbsp;<b></b></td>
					<td width="10%" align="right">&nbsp;<b>' . number_format($bakiBB, 2) . '</b></td>
					<td width="10%" align="right">&nbsp;</td>
				</tr>';
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
		<td colspan="7" align="center"><b>- Tiada Rekod -</b></td>
		</tr>';

print '	
</table>
</body>
</html>';
