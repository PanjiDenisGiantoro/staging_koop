<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanYearlyAwalReHL.php
 *          Date 		: 	12/09/2006
 *********************************************************************************/
include("common.php");
$today = date("F j, Y, g:i a");
$yy	= date("Y");
$mm	= date("m");
$yymm = sprintf("%04d%02d", $yy, $mm);

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
$sqlLoan = "SELECT a.*, (
				a.loanAmt * a.kadar_u /100 * a.loanPeriod /12
				) AS tot_untung, b.rnoBaucer
				FROM loans a, loandocs b
				WHERE a.loanID = b.loanID
				AND b.rnoBaucer <> '' 
				AND a.loanID = '" . $pk . "' AND a.status = '7' order by a.loanNo ASC";

//SELECT a.*, (a.loanAmt * a.kadar_u/100 * a.loanPeriod/12) as tot_untung, b.rnoBaucer  FROM loans a, loandoc b WHERE a.loanID = b.loanID and b.rnoBaucer <> '' and a.userID = '".$pk."'";

$rsLoan = $conn->Execute($sqlLoan);
while (!$rsLoan->EOF) {
	$i = 0;

	$sql = "select loanType FROM `loans` where status ='7' AND loanID = '" . $pk . "'";
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
		 WHERE userID = '$id' 
		 AND pymtRefer = '$bond'
		 
		 ORDER BY createdDate";
	//AND yrmth like " . tosql("%".$yr."%","Text"); 
	$rs = &$conn->Execute($sSQL);


	//get loan maded total with interest
	$getJumlahLoan = "SELECT loanAmt + (loanAmt * kadar_u/100 * loanPeriod/12) AS jumlahPembiayaan, month(startPymtDate)as Month
				  FROM loans  
				  WHERE status ='7' AND loanID = " . $loanID;
	$rsJumlahLoan = $conn->Execute($getJumlahLoan);
	$jumlahPembiayaan = $rsJumlahLoan->fields(jumlahPembiayaan);
	$monthStart2 = $rsJumlahLoan->fields(Month) + 1;
	$monthStart = sprintf("%02d", $monthStart2);

	//$monthStart = 
	if ($mm > $monthStart) {
		$yrmthNowT = $yy . $monthStart;
	} else {
		$yr = $yy - 1;
		$yrmthNowT = $yr . $monthStart;
	}


	$getOpenLoanP = "SELECT sum(pokokThn) as Sumpokok, max(yrmth) as thnbln, min(pokok) as pokok, min(untung)as untung,loanID FROM potbulanlook where yrmth <= '" . $yrmthNowT . "' and loanID = '" . $loanNo . "'  group by loanID";
	$rsOpenP = $conn->Execute($getOpenLoanP);
	$yrmthPot = $rsOpenP->fields(thnbln);
	$pokok = $rsOpenP->fields(pokok);
	$untung = $rsOpenP->fields(untung);
	$sumpokok = $rsOpenP->fields(Sumpokok);
	$yyr = sprintf("%04d", $yrmthNowT);


	if ($mm == $monthStart) {
		//$totalPokok = $pokok * 12;
		$sumAll2 = ($sumpokok + ($pokok * 12) + $untung);
		$sumAll = ($sumpokok + ($pokok * 12));
	}
	if ($yy == $yyr) {
		$total = $mm - $monthStart2;
		$totalSum2 = $pokok * $total;
		$sumAll2 = number_format(($rsOpenP->fields(Sumpokok) + $totalSum2 + $untung), 2);
		$sumAll = number_format(($rsOpenP->fields(Sumpokok) + $totalSum2), 2);
	} else {
		$total = 12 - $monthStart;
		$totalSum = $total * $pokok;
		$totalSum2 = $pokok * $monthStart2;
		$sumAll2 = number_format(($rsOpenP->fields(Sumpokok) + $totalSum2 + $totalSum + $untung), 2);
		$sumAll = number_format(($rsOpenP->fields(Sumpokok) + $totalSum2 + $totalSum), 2);
	}

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND userID = '" . $id . "' 
		AND deductID NOT IN (1642,1645,1649,1646,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745)
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	//if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranK
	$bakiAwal = 0;
	$bakiAkhir = 0;

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "' AND
		deductID IN (1642,1645,1646,1649,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745)
		AND userID = '" . $id . "' 
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	$bakiAwalUnt = 0;
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
					<th nowrap>&nbsp;Debit(RM)</th>
					<th nowrap>&nbsp;Kredit(RM)</th>
				</tr>';
	print '
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td width="10%" colspan=3 align="right">&nbsp;</td>
					<td width="60%" align="left"></b></td>
					<td width="10%" align="right"></td>
					<td width="10%" align="right"></td>
				</tr>';

	$totaldebit = 0;
	$totalkredit = 0;
	$totalkreditID = 0;

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

		if ($deductid_s == 1642  or  $deductid_s == 1645 or $deductid_s == 1649 or $deductid_s == 1651 or $deductid_s == 1652 or $deductid_s == 1653 or $deductid_s == 1654 or $deductid_s == 1656 or $deductid_s == 1657 or $deductid_s == 1658 or $deductid_s == 1659 or $deductid_s == 1663 or $deductid_s == 1674 or $deductid_s == 1676 or $deductid_s == 1643  or $deductid_s == 1678 or $deductid_s == 1669 or $deductid_s == 1668 or $deductid_s == 1672 or $deductid_s == 1680 or $deductid_s == 1771 or $deductid_s == 1767  or $deductid_s == 1745) {

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
							<td width="5%" align="center">1.</td>
							<td width="10%" align="center">&nbsp;' . toDate('d/m/y', $rs->fields(createdDate)) . '</td>
							<td width="10%">' . $rs->fields(docNo) . '</td>
							<td align="left">' . dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), "Number")) . '</td>
';

	print '
							<td width="10%" align="right">&nbsp;' . $debit . '</td>
							<td width="10%" align="right">&nbsp;' . $kredit . '</td>
						</tr> 
						
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="5%" align="center">2.</td>
							<td width="10%" align="center"></td>
							<td align="left">&nbsp;</td>
							<td width="10%" align="left">Bayaran Pembaiyaan Sehingga Tahun ' . $yy . ' Bulan ' . $mm . '';


	print '</td>
							<td width="10%" align="right">&nbsp;</td>
							<td width="10%" align="right">' . number_format($sumpokok, 2) . '</td>
						</tr>
						
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="5%" align="center">3.</td>
							<td width="10%" align="center"></td>
							<td align="left">&nbsp; </td>
							<td width="10%" align="left">Caj Sebulan Untung ';


	print '</td>
							<td width="10%" align="right">' . number_format($untung, 2) . '</td>
							<td width="10%" align="right"></td>
						</tr>
						';

	$sumAll_kredit = $untung + $sumpokok;


	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight:bold;" bgcolor="FFFFFF">
					<td width="10%" colspan=3 align="right">&nbsp;</td>
						<td width="60%" align="left">Jumlah &nbsp;&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . $debit . '</td>
							<td width="10%" align="right">&nbsp;' . number_format($sumAll_kredit, 2) . '</td>
					</tr>';
	$debit2 = $rs->fields(pymtAmt);
	$bakiBB = $debit2 - $sumAll_kredit;
	//$bakiBB = RoundCurrency($rs->fields(pymtAmt) - ($sumpokok + ($pokok * 12)+$untung));
	print '
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td width="10%" colspan=3 align="right">&nbsp;</td>
					<td width="60%" align="left">&nbsp;<b>Baki B/B</b></td>
					<td width="10%" align="right">&nbsp;<b>' . number_format($bakiBB, 2) . '</b></td>
					<td width="10%" align="right">&nbsp;</td>
				</tr>';
	//		} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Urusniaga -</b></td>
					</tr>';
	//	}

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
function RoundCurrency($fValue_)
{
	if ((ceil($fValue_) - $fValue_) >= 0.5) {
		$fTemp_ = floor($fValue_) + 0.5;
	} else {
		$fTemp_ = ceil($fValue_);
	}


	return $fTemp_;
}
