<?php

/*********************************************************************************
 *          Project		:	Sistem e-Koperasi(e-Koop) SEKATARAKYAT
 *          Filename		: 	loanJadual1.php
 *		   Description	:   View the status of loan
 *		   Parameter	:	$pk (userID)
 *          Date 		: 	13/7/2006
 *********************************************************************************/
//include("common.php");	
session_start();
date_default_timezone_set("Asia/Jakarta");
include("koperasiQry.php");
include("header.php");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($id))			$id = "";
$errPage2 = "Potongan Gaji Berjaya DiMasukkan Ke Dalam Pangkalan Data";
$updatedDate = date("Y-m-d H:i:s");
$updatedBy 	= get_session("Cookie_userName");
$updatedID = get_session("Cookie_groupID");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strActionPage = '?vw=loanJadual1&mn=904&id=' . $id . '';
$sFileName = '?vw=loanJadual1&mn=904';
$sFileRef  = '?vw=loanApproved&mn=3';
$title     = "Informasi pembiayaan";

$GetLoanDet = ctLoanNew("", $id);
$rnoVoucher = dlookup("loandocs", "rnoVoucher", "loanID ='" . $id . "'");


if ($apply) {

	$startPymtDate = dlookup("vauchers", "tarikh_baucer", "no_baucer ='" . $rnoVoucher . "'");
	$loanType = dlookup("loans", "loanType", "loanID ='" . $id . "'");
	$startDay	= toDateMK("d", $startPymtDate);
	$startMonth = toDateMK("m", $startPymtDate) + 1;
	$startYear	= toDateMK("Y", $startPymtDate);
	$lastmonthlyPay = $GetLoanDet->fields('pokokAkhir');
	$monthlyPay		= $GetLoanDet->fields('pokok');
	$intersetPay_total = $GetLoanDet->fields('untung');
	$intersetLast = $GetLoanDet->fields('untungAkhir');
	$monthlyPay_all = $monthlyPay + $intersetPay_total;
	$last_allPay = $lastmonthlyPay + $intersetLast;
	$interestPay	= number_format($GetLoanDet->fields('untung'), 2, '.', '');
	$fBasicTemp = $monthlyPay * 12;

	if ($loanType == 1616) {
		$lastYearPymt = $startYear;
		$lastMonthPymt = 11;
		$lastyearMthpymt = $lastYearPymt . $lastMonthPymt;
	} else {

		for ($i = 1; $i <= $loanPeriod; $i++) {
			//$nextMonth = mktime (0,0,0,$startMonth+$i,$startDay,  $startYear);
			$nextMonth = date('Y-m-28', mktime(0, 0, 0, $startMonth + $i, 1, $startYear));
			$nextMonthMK = date("Y-m-d", $nextMonth);
			$yy = toDateMK("Y", $nextMonthMK);
			$mm = toDateMK("m", $nextMonthMK);
			$lastyearMthpymt = $yy . $mm;
			return $lastyearMthpymt;
		}
	}


	$yymm = $startYear . $startMonth;
	$loanAmt	= $GetLoanDet->fields('loanAmt');
	$loanPeriod	= $GetLoanDet->fields('loanPeriod');
	$loanCaj	= $GetLoanDet->fields('kadar_u');
	$loanNo	= $GetLoanDet->fields('loanNo');
	$bondNO = $GetLoanDet->fields('rnoBond');
	$userID = $GetLoanDet->fields('userID');
	$fProfitMonthTemp = 0.00;
	$fProfitRateTemp = 0;

	$sSQL8 = "SELECT * 
	FROM potbulan WHERE loanID = '" . $loanNo . "'";
	$GetData8 = $conn->Execute($sSQL8);
	if ($GetData8->RowCount() > 0) {
		print '<script>alert("Potongan GAJI TELAH WUJUD  !");</script>';
	} else {

		$sSQL4	= "INSERT INTO potbulan (" .
			"yrmth," .
			"userID," .
			"loanType," .
			"loanID," .
			"bondNo," .
			"userCreated," .
			"CreateDate," .
			"updateDate," .
			"status," .
			"yearStart," .
			"lastPymt," .
			"lastyrmthPymt," .
			"monthStart," .
			"jumBlnP)" .
			" VALUES (" .
			"'" . $yymm . "', " .
			"'" . $userID . "', " .
			"'" . $loanType . "', " .
			"'" . $loanNo . "', " .
			"'" . $bondNO . "', " .
			"'" . $updatedID . "', " .
			"'" . $updatedDate . "', " .
			"'" . $updatedDate . "', " .
			"'" . 1 . "', " .
			"'" . $startYear . "', " .
			"'" . $last_allPay . "', " .
			"'" . $lastyearMthpymt . "', " .
			"'" . $startMonth . "', " .
			"'" . $monthlyPay_all . "')";

		$rsInstPtg = &$conn->Execute($sSQL4);

		$sSQL7 = "SELECT *
		FROM potbulan WHERE loanID = '" . $loanNo . "'  AND status = 1 ";
		$GetData7 = $conn->Execute($sSQL7);
		$ID = $GetData7->fields('ID');

		for ($i = 1; $i <= $loanPeriod; $i++) {

			$startPymtDate = dlookup("vauchers", "tarikh_baucer", "no_baucer ='" . $rnoVoucher . "'");

			$startMonth = toDateMK("m", $startPymtDate);
			$startYear	= toDateMK("Y", $startPymtDate);

			//$nextMonth = mktime (0,0,0,$startMonth+$i,$startDay,$startYear);
			$nextMonth = date('Y-m-28', mktime(0, 0, 0, $startMonth + $i, 1, $startYear));
			//$nextMonthMK = date("Y-m-d",$nextMonth);
			$yy = toDateMK("Y", $nextMonth);
			$mm = toDateMK("m", $nextMonth);
			$yrmth = $yy . $mm;
			if ($i == $loanPeriod) {
				$sSQLUpd	= "UPDATE potbulan SET" .
					" lastyrmthPymt= '" . $yrmth . "'" .
					", lastPymt= '" . $last_allPay . "'" .
					" Where ID= '" . $ID . "'";
				$rsUpd = &$conn->Execute($sSQLUpd);
				$monthlyPay = $GetLoanDet->fields('pokokAkhir');
				//$monthlyPay_P		= $GetLoanDet->fields('pokok');
				//$interestPay = $GetLoanDet->fields('untung');
				$interestPay = $GetLoanDet->fields('untungAkhir');
				//	$monthlyPay = $monthlyPay_P + $interestPay;
				//$last_allPay = $lastmonthlyPay + $intersetLast;

			}


			$sSQL5	= "INSERT INTO potbulanlook (" .
				"potID," .
				"userID," .
				"loanType," .
				"loanID," .
				"yrmth," .
				"pokok," .
				"untung," .
				"pokokThn," .
				"createDate," .
				"userIDcreated," .
				"updateDate," .
				"prcent)" .
				" VALUES (" .
				"'" . $ID . "', " .
				"'" . $userID . "', " .
				"'" . $loanType . "', " .
				"'" . $loanNo . "', " .
				"'" . $yrmth . "', " .
				"'" . $monthlyPay . "', " .
				"'" . $interestPay . "', " .
				"'" . $fBasicTemp . "', " .
				"'" . $updatedDate . "', " .
				"'" . $updatedID . "', " .
				"'" . $updatedDate . "', " .
				"'" . $fProfitRateTemp . "')";

			$rsInstPtg = &$conn->Execute($sSQL5);
		}
	}
	print '<script>alert("Permohonan di dalam sistem !");</script>';
}



if ($id <> "") {
	$GetLoanDet = ctLoan("", $id);

	$rnoVoucher = dlookup("loandocs", "rnoVoucher", "loanID ='" . $id . "'");
	$loanAmt	= $GetLoanDet->fields('loanAmt');
	$loanPeriod	= $GetLoanDet->fields('loanPeriod');
	$loanCaj	= $GetLoanDet->fields('kadar_u');
	$loanNo	= $GetLoanDet->fields('loanNo');
	$bondNO = $GetLoanDet->fields('rnoBond');
	$startPymtDate = dlookup("vauchers", "tarikh_baucer", "no_bond ='" . $bondNO . "'");
	$userID = $GetLoanDet->fields('userID');
	$loanType = $GetLoanDet->fields('userID');

	$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
	$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
	$monthlyPay		= number_format($GetLoanDet->fields('pokok'), 2, '.', '');
	$lastmonthlyPay	= number_format($GetLoanDet->fields('pokokAkhir'), 2, '.', '');
	$interestPay	= number_format($GetLoanDet->fields('untung'), 2, '.', '');
	$lastinterestPay = number_format($GetLoanDet->fields('untungAkhir'), 2, '.', '');
	$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoanDet->fields(userID), "Text"));


	// <tr><input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="apply" value="Potongan Gaji" class="btn btn-secondary" onclick="PageRefresh();"/>&nbsp;<input class="btn btn-secondary" type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="apply_pat" value="Potongan Akaun Tabungan" onclick="PageRefresh();"/> </td>
	print '
<form name="MyForm" action=' . $strActionPage . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<h5 class="card-title">' . strtoupper($title) . '</h5>
	
	<tr>
		<td>
			<table width="100%">

				<tr>
					<td class="textFont" width="150">Nomor Anggota</td>
					<td class="Label">:&nbsp;<b>' . $GetLoanDet->fields(userID) . '</b></td>
				</tr>
				<tr>
					<td class="textFont">Nama Anggota</td>
					<td class="Label">:&nbsp;<b>' . dlookup("users", "name", "userID=" . tosql($GetLoanDet->fields(userID), "Text")) . '</b></td>
				</tr>
				<tr>
					<td class="textFont">Kartu Identitas</td>
					<td class="Label">:&nbsp;<b>' . dlookup("userdetails", "newIC", "userID=" . tosql($GetLoanDet->fields(userID), "Text")) . '</b></td>
				</tr>
				<tr>
					<td class="textFont">Cawangan/Kawasan/<br/>Zon</td>
					<td class="Label">:&nbsp;<b>' . dlookup("general", "name", "ID=" . tosql($jabatan, "Text")) . '</b></td>
				</tr>
			</table>
		</td>
	</tr>';

	print '
	<tr><td>
	<table width="100%">
		<tr>
			<td class="textFont" width="150">Nomor Rujukan</td>
			<td class="Label">:&nbsp;<b>' . $loanNo . '</b></td>
		</tr>
		<tr>
			<td class="textFont" width="150">Jumlah pinjaman</td>
			<td class="Label">:&nbsp;<b>' . number_format($loanAmt, 2) . '</b></td>
		</tr>
		
		<tr>
			<td class="textFont" width="150">Caj Pinjaman (%)</td>
			<td class="Label">:&nbsp;<b>' . $loanCaj . '</b></td>
		</tr>
		<!--tr>
			<td class="textFont" width="150">Baki Pokok</td>
			<td class="Label">:&nbsp;<b>' . $GetLoanDet->fields('outstandingAmt') . '</b></td>
		</tr>
		<tr>
			<td class="textFont" width="150">Baki Untung</td>
			<td class="Label">:&nbsp;<b>' . $GetLoanDet->fields('outstandingAmt') . '</b></td>
		</tr-->
		<tr>
			<td class="textFont" width="150">Nombor Bond</td>
			<td class="Label">:&nbsp;<b>' . $bondNO . '</b></td>
		</tr-->
				<tr>
			<td class="textFont" width="150">Nomor Voucher</td>
			<td class="Label">:&nbsp;<b>' . $rnoVoucher . '</b></td>
		</tr-->
		<tr>
		<td class="textFont" width="150">Tanggal Voucher</td>
		<td class="Label">:&nbsp;<b>' . toDate("d/m/Y", $startPymtDate) . '</b></td>
	</tr-->
	</table>';

	$monthlyTotal = 0;
	$interestTotal = 0;
	$overallTotal = 0;



	$startDay	= toDateMK("d", $startPymtDate);
	$startMonth = toDateMK("m", $startPymtDate);
	$startYear	= toDateMK("Y", $startPymtDate);



	if ($startPymtDate <> '') { // check date vaucher exist


		print '<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
		<tr class="header">
			<td nowrap>&nbsp;Tanggal</td>
			<td nowrap>&nbsp;No Pembayaran</td>
			<td nowrap align="center" width="100">&nbsp;Pokok</td>
			<td nowrap align="center" width="100">&nbsp;Untung</td>
			<td nowrap align="center" width="150">&nbsp;Jumlah</td>						
		</tr>';

		for ($i = 1; $i <= $loanPeriod; $i++) {
			$amtTotal = 0;
			//$nextMonth = mktime (0,0,0,$startMonth+$i,$startDay,$startYear);
			$nextMonth = date('Y-m-28', mktime(0, 0, 0, $startMonth + $i, 1, $startYear));
			$DateNew = toDate("d/m/Y", $nextMonth);
			$yy = toDateMK("Y", $nextMonth);
			$mm = toDateMK("m", $nextMonth);

			if ($i == $loanPeriod) {

				$amtTotal 		= $lastmonthlyPay + $lastinterestPay;
				$monthlyTotal 	= $monthlyTotal + $lastmonthlyPay;
				$interestTotal 	= $interestTotal + $lastinterestPay;
				print '
			<tr>
				<td nowrap class="data">&nbsp;' . $DateNew . '</td>
				<td nowrap class="data">&nbsp;Pembayaran #' . $i . '</td>
				<td nowrap class="data" align="right">' . $lastmonthlyPay . '&nbsp;</td>
				<td nowrap class="data" align="right">' . $lastinterestPay . '&nbsp;</td>
				<td nowrap class="data" align="right">' . sprintf("%01.2f", $amtTotal) . '&nbsp;</td>						
			</tr>';
			} else {
				$amtTotal 		= $monthlyPay + $interestPay;
				$monthlyTotal 	= $monthlyTotal + $monthlyPay;
				$interestTotal 	= $interestTotal + $interestPay;
				print '
			<tr>
				<td nowrap class="data">&nbsp;' . $DateNew . '</td>
				<td nowrap class="data">&nbsp;Pembayaran #' . $i . '</td>
				<td nowrap class="data" align="right">' . number_format($monthlyPay, 2, '.', ',') . '&nbsp;</td>
				<td nowrap class="data" align="right">' . $interestPay . '&nbsp;</td>
				<td nowrap class="data" align="right">' . sprintf("%01.2f", $amtTotal) . '&nbsp;</td>						
			</tr>';
			}
			$overallTotal = $overallTotal + $amtTotal;
		} //end for
		print '	
		<tr>
			<td nowrap class="Header" colspan="2" align="right"><b>Jumlah&nbsp;:&nbsp;&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($monthlyTotal) . '&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($interestTotal) . '&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($overallTotal) . '&nbsp;</b></td>						
		</tr>';

		print '</table></form>';
	} // end if date

	print '	</td></tr></table>';
}
print '<script>window.print();</script>';

//include("footer.php");	

print '
<script language="JavaScript">
	function ITRActionButtonClick(v) {
        e = document.MyForm;
        e.id.value = v;
        e.submit();
    }	   
	function PageRefresh() {
	frm = document.MyForm;
	document.location = ' . $strActionPage . ';
	}
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $strActionPage . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
