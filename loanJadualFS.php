<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanYearly.php
 *          Date 		: 	12/09/2006
 *********************************************************************************/
session_start();
include("common.php");
$today = date("F j, Y");

if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
	exit;
}
$header =
	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
	. '<html>'
	. '<head>'
	. '<title>' . $emaNetis . '</title>'
	. '<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">'
	. '<meta http-equiv="pragma" content="no-cache">'
	. '<meta http-equiv="expires" content="0">'
	. '<meta http-equiv="cache-control" content="no-cache">'
	. '<LINK rel="stylesheet" href="images/mail.css" >'
	. '</head>'
	. '</html>';

print '
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .Mainlogo {
            position: absolute;
        }

        .AlamatPengirim {
            margin-left: 19%;
            border-spacing: 3px;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .resit-statement {
            float: right;
            text-wrap: nowrap;
            text-align: center;
            margin-top: -130px;
            border-collapse: separate;
            border-spacing: 10px;
            width: 20%;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .tr-space{
            background: #d3d3d3;
        }
        .tr-kod-rujukan{
            font-weight: bold;
            word-spacing: 5px;
        }
        .date-stylish{
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            margin-top: 4%;
            float: right;
        }
        .bor-penerima {
            margin-top: 3%;
            border-collapse: separate;
            border-spacing: 3px;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .header-border{
            border: groove 3px;
        }
        .body-acc-num {
            margin-top: 2%;
            border-collapse: separate;
            border-spacing: 3px;
            margin-bottom: 3%;
        }
        .bayar-style{
            margin-top: 2%;
            margin-right: 65%;
        }
        .no-siri{
            margin-left:  40%;
            margin-top: -20px;
            margin-right: 30%;
        }
        .date-bayar-stylish{
            float: right;
            margin-top: -20px;
        }
        .stylish-catat{
            margin-top: 3%;
        }
        .td-thick-font{
            font-weight: bold:
        }
        .bottom {
            position: fixed;
            bottom: 10px;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
';

print '
<body>
<div class="form-container">

<table border="0" cellpadding="5" cellspacing="0" width="100%">';
$sqlLoan = "SELECT a . * , (
				a.loanAmt * a.kadar_u /100 * a.loanPeriod /12
				) AS tot_untung, b.rnoVoucher
				FROM loans a, loandocs b
				WHERE a.loanID = b.loanID
				AND b.rnoVoucher <> '' 
				AND a.loanID = '" . $pk . "'";

$sql = "select loanType FROM `loans` where loanID = '" . $pk . "'";
$Get =  &$conn->Execute($sql);
if ($Get->RowCount() > 0) $loanType = $Get->fields(loanType);

$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
$Get =  &$conn->Execute($sql);
if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

$rsLoan = $conn->Execute($sqlLoan);
while (!$rsLoan->EOF) {
	$i = 0;
	//$id = $rsLoan->fields(userID);
	//$loanType = $rsLoan->fields(loanType);
	//$loanID =$rsLoan->fields(loanID);
	$id = $rsLoan->fields(userID);
	$loanType = $rsLoan->fields(loanType);
	$loanNo = $rsLoan->fields(loanNo);    //add loan No ref style
	$loanID = $rsLoan->fields(loanID);
	$loanType = $rsLoan->fields(loanType);
	$loanAmt = $rsLoan->fields(loanAmt);
	$loanPeriod = $rsLoan->fields(loanPeriod);
	$monthlyPymt = $rsLoan->fields(monthlyPymt);
	$kadar_u = $rsLoan->fields(kadar_u);
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
		 AND year(createdDate) = $yr ORDER BY createdDate DESC";
	//AND yrmth like " . tosql("%".$yr."%","Text"); 
	$rs = &$conn->Execute($sSQL);

	$getJumlahLoan = "SELECT loanAmt + (loanAmt * kadar_u/100 * loanPeriod/12) AS jumlahPembiayaan
				  FROM loans  
				  WHERE loanID = " . $loanID;
	$rsJumlahLoan = $conn->Execute($getJumlahLoan);
	$jumlahPembiayaan = $rsJumlahLoan->fields(jumlahPembiayaan);

	$nLoanYear		= $loanPeriod / 12;
	$profit = ($kadar_u * 0.01) * $loanAmt * $nLoanYear;

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID NOT IN (1642,1645,1649,1646,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745)
		AND userID = '" . $id . "' 
		AND year(createdDate) < " . $yr . "
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
	else $bakiAwal = 0;
	$bakiAkhir = 0;

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "' AND
		deductID IN (1642,1645,1646,1649,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745)
		AND userID = '" . $id . "' 
		AND year(createdDate) < " . $yr . "
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwalUnt = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwalUnt = 0;
	$bakiAkhirUnt = 0;

	$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($id, "Text"));
	$monthStart1 = dlookup("potbulan", "monthStart", "bondNo=" . tosql($bond, "Text"));
	$monthStart = str_pad($monthStart1, 2, '0', STR_PAD_LEFT);
	$yearStart = dlookup("potbulan", "yearStart", "bondNo=" . tosql($bond, "Text"));

	$lastyrmthPymt = $rs->fields['yrmth'];
	if (strlen($lastyrmthPymt) == 6) {
		$yearEnd = substr($lastyrmthPymt, 0, 4);
		$monthEnd = substr($lastyrmthPymt, 4, 2);
	}


	$getBayar = "SELECT SUM(pymtAmt) as dahBayar FROM transaction WHERE pymtRefer = '" . $bond . "' AND addminus = 1";
	$rsBayar = $conn->Execute($getBayar);

	print '
<tr><td colspan="2">
    <!---------Logo/Address/Watermark Resit-------->
    <div class="Mainlogo"><img src="assets/images/logo-ori.png" style="height: 120px; width: 120px;" alt="Logo Koperasi"></div>
    <table class="AlamatPengirim">
        <tr><td>ALM Core Solutions Sdn Bhd</td></tr>
        <tr><td>3-1, Jalan Dagang SB 4/1,</td></tr>
        <tr><td>Taman Sungai Besi Indah,</td></tr>
        <tr><td>43300, Seri Kembangan,</td></tr>
        <tr><td>Selangor.</td></tr>
        <tr><td>TEL: +6011 - 74648313</td></tr>
        <tr><td>EMEL: helpdesk@ikoop.com.my</td></tr>
        </table>
  </td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td>No Anggota</td>
		<td>:&nbsp;<b>' . dlookup("userdetails", "memberID", "userID=" . tosql($id, "Text")) . '</b></td>
	</tr>
	<tr>
	<tr>
		<td>Nama</td>
		<td>:&nbsp;<b>' . dlookup("users", "name", "userID=" . tosql($id, "Text")) . '</b></td>
	</tr>
	<tr>
		<td>No Kartu Identitas</td>
		<td>:&nbsp;<b>' . dlookup("userdetails", "newIC", "userID=" . tosql($id, "Text")) . '</b></td>
	</tr>
	<tr>
		<td>Cawangan/Kawasan/Zon</td>
		<td>:&nbsp;<b>' . dlookup("general", "name", "ID=" . tosql($jabatan, "Text")) . '</b></td>
	</tr>
	<tr><td colspan="2"><hr class=1 px;></td></tr>
	<tr>
		<td>Jenis Pembiayaan / No Rujukan</td>
		<td>:&nbsp;<b>' . dlookup("general", "name", "ID=" . tosql($loanType, "Text")) . ' / ' . $loanNo . '</b></td>
	</tr>
	<tr>
		<td>Bayaran Bulanan <b>BB</b></td>
		<td>:&nbsp;<b>RM ' . number_format($monthlyPymt, 2) . '</b></td>
	</tr>
	<tr>
		<td>No Bond</td>
		<td>:&nbsp;<b>' . $bond . '</b></td>
	</tr>
	<tr>
		<td>Jumlah Pembiayaan</td>
		<td>:&nbsp;<b>RM ' . number_format($loanAmt, 2) . '</b></td>
	</tr>
	<tr>
		<td>Jumlah Keuntungan</td>
		<td>:&nbsp;<b>RM ' . number_format($profit, 2) . '</b></td>
	</tr>
	<tr>
		<td>Rate</td>
		<td>:&nbsp;<b>' . $kadar_u . '% p.a</b></td>
	</tr>
    <tr>
		<td>Mula Potongan</td>
		<td>:&nbsp;<b>' . $monthStart . ' / ' . $yearStart . '</b></td>
	</tr>
	<tr><td><br/><br/></td></tr>
	<tr>
		<td colspan="2">Akhir Bayaran Terkini <b>' . $monthEnd . ' / ' . $yearEnd . '</b></td>
	</tr>	
	<tr>
		<td colspan="2">Jumlah Keseluruhan Bayaran : <b>RM ' . number_format($rsBayar->fields(dahBayar), 2) . '</b></td>
	</tr>
    <!--tr>
		<td colspan="2">' . dlookup("general", "name", "ID=" . tosql($loanType, "Text")) . ' / ' . $loanNo . '</td>
	</tr-->
    <tr><td><br/></td></tr>
	<tr>
		<td colspan="2">
		<table class="table table-striped" border=1  cellpadding="2" cellspacing="0" style="font-size: 10pt;">
		<tr>
			<td nowrap align="center"><i>No.</i><br/>Bil.</td>
			<td nowrap><i>Descriptions</i><br/>Keterangan</td>
			<td nowrap align="right"><i>Debit (RP)</i><br/>Masuk</td>
			<td nowrap align="right"><i>Kredit (RP)</i><br/>Keluar</td>
			<td nowrap align="right"><i>Balance (RP)</i><br/>Baki</td>
		</tr>';

	function calculateRule78Interest($loanAmt, $kadar_u, $loanPeriod, $totalPayments)
	{
		// Calculate total interest using flat rate method
		$totalInterest = ($loanAmt * ($kadar_u / 100) * ($loanPeriod / 12));

		// Calculate the sum of the digits for the loan tenure
		$sumOfDigits = ($loanPeriod * ($loanPeriod + 1)) / 2;

		// Calculate the monthly payment
		$totalPayment = $loanAmt + $totalInterest;
		$monthlyPayment = $totalPayment / $loanPeriod;

		// Calculate interest allocation for each month using Rule of 78
		$interestAllocation = array();  // menggunakan array() untuk mendefinisikan array
		for ($i = 1; $i <= $loanPeriod; $i++) {
			$interestAllocation[$i] = ($totalInterest * ($loanPeriod - $i + 1)) / $sumOfDigits;
		}

		// Calculate total interest paid after the given number of payments
		$monthsPaid = intval($totalPayments / $monthlyPayment);
		$totalInterestPaid = 0;
		$totalPrincipalPaid = 0;

		for ($month = 1; $month <= $monthsPaid; $month++) {
			$interestForMonth = $interestAllocation[$month];
			$principalForMonth = $monthlyPayment - $interestForMonth;
			$totalInterestPaid += $interestForMonth;
			$totalPrincipalPaid += $principalForMonth;
		}

		// Calculate remaining balance
		$remainingBalance = $loanAmt - $totalPrincipalPaid;

		return array(
			'totalInterest' => $totalInterest,
			'remainingBalance' => $remainingBalance,
			'totalInterestPaid' => $totalInterestPaid
		);
	}

	// Fungsi untuk mengira caj lain-lain berdasarkan peratusan
	function calculateAdditionalCharges($bakiSemasa, $otherChargesPercentage)
	{
		// Kira caj lain-lain berdasarkan peratusan
		return ($bakiSemasa * $otherChargesPercentage) / 100;
	}

	$totaldebit = 0;
	$totalkredit = 0;
	$totalkreditID = 0;
	$bakiSemasa = $bakiAwal; // Inisialisasi baki semasa dengan baki awal

	if ($rs->RowCount() <> 0) {
		$bil = 1; // Initialize Bil numbering
		$totaldebit = 0; // Total debit for row 1
		$totalkredit = 0; // Total credit for row 2
		$bakiSemasa = $bakiAwal; // Initialize balance with the starting balance

		while (!$rs->EOF) {
			$deductid_s = $rs->fields['deductID'];
			$pymtAmt = $rs->fields['pymtAmt'];

			if ($rs->fields['addminus'] == 0) {
				// Handle debit (addminus == 0)
				$debit = $pymtAmt;
				$totaldebit += $debit; // Accumulate total debit
			} else {
				// Handle credit (addminus != 0)
				// if ($deductid_s == 1642 || $deductid_s == 1645 || $deductid_s == 1649 || 
				// 	$deductid_s == 1651 || $deductid_s == 1652 || $deductid_s == 1653 || 
				// 	$deductid_s == 1654 || $deductid_s == 1656 || $deductid_s == 1657 || 
				// 	$deductid_s == 1658 || $deductid_s == 1659 || $deductid_s == 1663 || 
				// 	$deductid_s == 1674 || $deductid_s == 1676 || $deductid_s == 1643 || 
				// 	$deductid_s == 1678 || $deductid_s == 1669 || $deductid_s == 1668 || 
				// 	$deductid_s == 1672 || $deductid_s == 1680 || $deductid_s == 1771 || 
				// 	$deductid_s == 1767 || $deductid_s == 1745) {
				// 	// Special condition for specific credit IDs
				// 	$kreditID = $pymtAmt;
				// 	$totalkreditID += $kreditID;
				// 	$bakiSemasa -= $kreditID; // Subtract from balance
				// } else {
				// Regular credit transaction
				$kredit = $pymtAmt;
				$totalkredit += $kredit; // Accumulate total credit
			}
			// }

			// Move to the next record
			$rs->MoveNext();
		}

		// Row 1: Total Pembiayaan + Caj
		$bakiSemasa = $totaldebit + $profit; // The first row shows the debit amount without subtraction
		print '
			<tr>
				<td align="center">1.</td>
				<td align="left">Total Pembiayaan + Caj</td>
				<td align="right">' . number_format($totaldebit + $profit, 2) . '</td>
				<td align="right">&nbsp;</td>
				<td align="right">' . number_format($bakiSemasa, 2) . '</td>
			</tr>';

		// Row 2: Bayaran (Pokok) + (Profit)
		$bakiSemasa -= $totalkredit; // Now we subtract the credit amount to get the updated balance
		print '
			<tr>
				<td align="center">2.</td>
				<td align="left">Bayaran (Pokok) + (Profit)</td>
				<td align="right">&nbsp;</td>
				<td align="right">' . number_format($totalkredit, 2) . '</td>
				<td align="right">' . number_format($bakiSemasa, 2) . '</td>
			</tr>';

		$totalPayments = $rsBayar->fields(dahBayar);

		// Call the function to calculate interest
		$interestData = calculateRule78Interest($loanAmt, $kadar_u, $loanPeriod, $totalPayments);

		// Hitung Rebat (Keuntungan Belum Terakru)
		$rebat = $interestData['totalInterest'] - $interestData['totalInterestPaid'];

		// Kemas kini baki semasa dengan rebat
		$bakiSemasa -= $rebat; // Kurangkan baki dengan rebat

		// Row 3: Rebet (Keuntungan Belum Terakru)
		print '
			<tr>
				<td align="center">3.</td>
				<td align="left">Rebet (Keuntungan Belum Terakru)</td>
				<td align="right">&nbsp;</td>
				<td align="right">' . number_format($rebat, 2) . '</td>
				<td align="right">' . number_format($bakiSemasa, 2) . '</td>
			</tr>';

		// Summary row
		print '
			<tr>
				<td align="center">(A)</td>
				<td align="right"><b>JUMLAH KESELURUHAN BAYARAN PEMBIAYAAN (RP)</b></td>
				<td align="right"><b>' . number_format($totaldebit + $profit, 2) . '</b></td>
				<td align="right"><b>' . number_format($totalkredit + $rebat, 2) . '</b></td>
				<td align="right"><b>' . number_format($bakiSemasa, 2) . '</b></td>
			</tr>';
	} else {
		print '
			<tr>
				<td colspan="5" align="center"><b>- Tiada Rekod Urusniaga -</b></td>
			</tr>';
	}
	print '		</table>';

	// Baris untuk LAIN LAIN CAJ (RP) PEMBIAYAAN
	$getCat = "SELECT * FROM `general` WHERE category = 'J' AND j_EarlyDeduct = 1 ORDER BY code";
	$rsCat = $conn->Execute($getCat);

	print '  <tr>
            <td colspan="2">Lain - Lain Caj Pembiayaan <i>(Full Settlement)</i></td>
        </tr>
        <tr><td><br/></td></tr>
        <table class="table table-striped" style="font-size: 10pt;">
            <tr>
                <td align="center"><i>No.</i><br/>Bil.</td>
                <td><i>Descriptions</i><br/>Keterangan</td>
                <td align="right"><i>Amount (RP)</i><br/>Amaun</td>
            </tr>';

	$i = 0;
	$total_caj = 0;

	if ($rsCat) {
		while (!$rsCat->EOF) {
			$name = ucwords(strtolower($rsCat->fields['name']));
			$otherChargesPercentage = $rsCat->fields['j_Percentage'];

			$interestData = calculateRule78Interest($loanAmt, $kadar_u, $loanPeriod, $totalPayments);
			$otherCharges = calculateAdditionalCharges($bakiSemasa, $otherChargesPercentage);

			if (stripos($name, 'caj pembiayaan proses') !== false) {
				$j_Amount = $rsCat->fields['j_Amount'];
			} else if (stripos($name, 'caj keuntungan') !== false) {
				$j_Amount = $interestData['totalInterestPaid'];
			} else if (stripos($name, 'caj') !== false) {
				$j_Amount = $otherCharges;
			}

			print '
				<tr>
					<td align="center">' . ++$i . '.</td>
					<td align="left">' . $name . '</td>
					<td align="right">' . number_format($j_Amount, 2) . '</td>
				</tr>';
			$total_caj += $j_Amount;
			$rsCat->MoveNext();
		}
	} else {
		print '
			<tr>
				<td colspan="3" align="center"><b>- Tiada Rekod Caj -</b></td>
			</tr>';
	}

	// Baris untuk TOTAL LAIN LAIN CAJ (RP)
	print '
		<tr>
			<td align="center">(B)</td>
			<td align="right"><b>JUMLAH KESELURUHAN LAIN LAIN CAJ (RP)</b></td>
			<td align="right"><b>' . number_format($total_caj, 2) . '</b></td>
		</tr>';

	print '</table>
            <tr><td><br/></td></tr>
            <tr>
                <td colspan="2">Total bayaran yang perlu di buat bayaran <i>full settlement</i> sebanyak (A) + (B) = <b>RM ' . number_format($bakiSemasa + $total_caj, 2) . '</b></td>
            </tr>
		</td>
	</tr>';
	$rsLoan->MoveNext();
}

if ($rsLoan->RecordCount() < 1)
	print '	<tr>
		<td colspan="7" align="center"><b>- Tiada Urusniaga -</b></td>
		</tr>';

print '	
</table>
</body>
</html>

<script>window.print();</script>';
