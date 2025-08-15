<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanJadual.php
 *          Date 		: 	13/7/2006
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("koperasiList.php");
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
$GetLoanDet = ctLoan("", $id);
$rnoBaucer = dlookup("loandocs", "rnoBaucer", "loanID ='" . $id . "'");

$strActionPage = 'loanJadual.php?id=' . $id . '';
$sFileName = 'loanJadual.php';
$sFileRef  = 'loanJadual.php';
$title     = "Maklumat pembiayaan";
if ($apply) {
	//$GetLoanDet = ctLoan("",$id);
	$startPymtDate = dlookup("vauchers", "tarikh_baucer", "no_baucer ='" . $rnoBaucer . "'");
	$loanType = dlookup("loans", "loanType", "loanID ='" . $id . "'");
	$startDay	= toDateMK("d", $startPymtDate);
	$startMonth = toDateMK("m", $startPymtDate);
	$startYear	= toDateMK("Y", $startPymtDate);
	$lastmonthlyPay = $GetLoanDet->fields('pokokAkhir');
	$monthlyPay		= $GetLoanDet->fields('pokok');

	if ($loanType == 1616) {
		$lastYearPymt = $startYear;
		$lastMonthPymt = 12;
		$lastyearMthpymt = $lastYearPymt . $lastMonthPymt;
	}
	//$monthLoanStartPy = ($rs->fields('Month')+1);
	//$yearLoanStartPy = $rs->fields('year');
	$yymm = $startYear . $startMonth;

	$loanAmt	= $GetLoanDet->fields('loanAmt');
	$loanPeriod	= $GetLoanDet->fields('loanPeriod');
	$loanCaj	= $GetLoanDet->fields('kadar_u');
	$loanNo	= $GetLoanDet->fields('loanNo');
	$bondNO = $GetLoanDet->fields('rnoBond');
	$userID = $GetLoanDet->fields('userID');
	//$loanType = $GetLoanDet->fields ('');


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
		"'" . $lastmonthlyPay . "', " .
		"'" . $lastyearMthpymt . "', " .
		"'" . $monthlyPay . "')";

	$rsInstPtg = &$conn->Execute($sSQL4);

	print '<script>alert("Permohonan di dalam sistem !");</script>';
}

if ($id <> "") {
	$GetLoanDet = ctLoan("", $id);

	$rnoBaucer = dlookup("loandocs", "rnoBaucer", "loanID ='" . $id . "'");
	$loanAmt	= $GetLoanDet->fields('loanAmt');
	$loanPeriod	= $GetLoanDet->fields('loanPeriod');
	$loanCaj	= $GetLoanDet->fields('kadar_u');
	$loanNo	= $GetLoanDet->fields('loanNo');
	$bondNO = $GetLoanDet->fields('rnoBond');
	$userID = $GetLoanDet->fields('userID');
	$loanType = $GetLoanDet->fields('userID');

	$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
	$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
	$monthlyPay		= number_format($GetLoanDet->fields('pokok'), 2, '.', '');
	$lastmonthlyPay	= number_format($GetLoanDet->fields('pokokAkhir'), 2, '.', '');
	$interestPay	= number_format($GetLoanDet->fields('untung'), 2, '.', '');
	$lastinterestPay = number_format($GetLoanDet->fields('untungAkhir'), 2, '.', '');



	$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoanDet->fields(userID), "Text"));



	print '
<form name="MyForm" action=' . $strActionPage . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
	
	<tr>
		<td>
			<table width="100%">
			<tr></td>
				<tr>
					<td class="textFont" width="150">Nombor Anggota</td>
					<td class="Label">:&nbsp;' . $GetLoanDet->fields(userID) . '</td>
				</tr>
				<tr>
					<td class="textFont">Nama Anggota</td>
					<td class="Label">:&nbsp;' . dlookup("users", "name", "userID=" . tosql($GetLoanDet->fields(userID), "Text")) . '</td>
				</tr>
				<tr>
					<td class="textFont">No KP</td>
					<td class="Label">:&nbsp;' . dlookup("userdetails", "newIC", "userID=" . tosql($GetLoanDet->fields(userID), "Text")) . '</td>
				</tr>
				<tr>
					<td class="textFont">Jabatan</td>
					<td class="Label">:&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Text")) . '</td>
				</tr>
			</table>
		</td>
	</tr>';

	print '
	<tr><td>
	<table width="100%">
		<tr>
			<td class="textFont" width="150">Nombor Rujukan</td>
			<td class="Label">:&nbsp;' . $loanNo . ':&nbsp;' . $id . '</td>
		</tr>
		<tr>
			<td class="textFont" width="150">Jumlah pinjaman</td>
			<td class="Label">:&nbsp;' . number_format($loanAmt, 2) . '</td>
		</tr>
		
		<tr>
			<td class="textFont" width="150">Caj Pinjaman (%)</td>
			<td class="Label">:&nbsp;' . $loanCaj . '</td>
		</tr>
		<!--tr>
			<td class="textFont" width="150">Baki Pokok</td>
			<td class="Label">:&nbsp;' . $GetLoanDet->fields('outstandingAmt') . '</td>
		</tr>
		<tr>
			<td class="textFont" width="150">Baki Untung</td>
			<td class="Label">:&nbsp;' . $GetLoanDet->fields('outstandingAmt') . '</td>
		</tr-->
		<tr>
			<td class="textFont" width="150">No Bond</td>
			<td class="Label">:&nbsp;' . $bondNO . '</td>
		</tr-->
	</table>';

	$monthlyTotal = 0;
	$interestTotal = 0;
	$overallTotal = 0;
	//$startDay	= substr($GetLoanDet->fields(startPymtDate),6,2);//toDate("d",$GetLoanDet->fields(startPymtDate));
	//$startMonth = substr($GetLoanDet->fields(startPymtDate),4,2) - 1; //toDate("m",$GetLoanDet->fields(startPymtDate))-1;
	//$startYear	= substr($GetLoanDet->fields(startPymtDate),0,4);//toDate("Y",$GetLoanDet->fields(startPymtDate));

	//	$rnoBaucer = dlookup("loandocs", "rnoBaucer", "loanID ='" .$id."'");
	$startPymtDate = dlookup("vauchers", "tarikh_baucer", "no_baucer ='" . $rnoBaucer . "'");
	//$startPymtDate = dlookup("loandocs", "rcreatedDate", "loanID=" . $id );
	$startDay	= toDateMK("d", $startPymtDate);
	$startMonth = toDateMK("m", $startPymtDate) - 1;
	$startYear	= toDateMK("Y", $startPymtDate);



	if ($startPymtDate <> '') { // check date vaucher exist


		print '<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
		<tr class="header">
			<td nowrap>&nbsp;Tarikh</td>
			<td nowrap>&nbsp;No Pembayaran</td>
			<td nowrap align="center" width="100">&nbsp;Pokok</td>
			<td nowrap align="center" width="100">&nbsp;Untung</td>
			<td nowrap align="center" width="150">&nbsp;Jumlah</td>						
		</tr>';

		for ($i = 1; $i <= $loanPeriod; $i++) {
			$amtTotal = 0;
			$nextMonth = mktime(0, 0, 0, $startMonth + $i, $startDay,  $startYear);
			if ($i == $loanPeriod) {
				$amtTotal 		= $lastmonthlyPay + $lastinterestPay;
				$monthlyTotal 	= $monthlyTotal + $lastmonthlyPay;
				$interestTotal 	= $interestTotal + $lastinterestPay;
				print '
			<tr>
				<td nowrap class="data">&nbsp;' . toDate("d/m/Y", $nextMonth) . '</td>
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
				<td nowrap class="data">&nbsp;' . date("d/m/Y", $nextMonth) . '</td>
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

	print '	</td></tr>';
}

include("footer.php");

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
