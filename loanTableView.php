<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanView.php
 *          Date 		: 	23/12/2003
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
include("koperasiList.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$sFileName = 'loanTable.php';
$sFileRef  = 'loanTableView.php';
$title     = "Jadual Bayar Balik Pinjaman";

$sSQL = "SELECT	* FROM loans 
		 WHERE	userID = '" . $pk . "' 
		 AND	status = '1'
		 ORDER BY applyDate DESC";
$GetLoan = &$conn->Execute($sSQL);
$GetLoan->Move($StartRec - 1);

$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileRef . ' method="post">
<input type="hidden" name="id">
<input type="hidden" name="pk" value="' . $pk . '">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td class="textFont" width="150">Nombor Anggota</td>
					<td class="Label">:&nbsp;' . dlookup("userdetails", "memberID", "userID=" . tosql($pk, "Text")) . '</td>
				</tr>
				<tr>
					<td class="textFont">Nama Anggota</td>
					<td class="Label">:&nbsp;' . dlookup("users", "name", "userID=" . tosql($pk, "Text")) . '</td>
				</tr>
			</table>
		</td>
	</tr>';
if ($GetLoan->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont">&nbsp;</td>
						<td align="right" class="textFont">
							Paparan <SELECT name="pg" class="Data" onchange="doListAll();">';
	if ($pg == 5)	print '<option value="5" selected>5</option>';
	else print '<option value="5">5</option>';
	if ($pg == 10)	print '<option value="10" selected>10</option>';
	else print '<option value="10">10</option>';
	if ($pg == 20)	print '<option value="20" selected>20</option>';
	else print '<option value="20">20</option>';
	if ($pg == 30)	print '<option value="30" selected>30</option>';
	else print '<option value="30">30</option>';
	if ($pg == 40)	print '<option value="40" selected>40</option>';
	else print '<option value="40">40</option>';
	if ($pg == 50)	print '<option value="50" selected>50</option>';
	else print '<option value="50">50</option>';
	if ($pg == 100)	print '<option value="100" selected>100</option>';
	else print '<option value="100">100</option>';
	print '				</select>setiap mukasurat.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap>&nbsp;No Rujukan ID</td>
						<td nowrap>&nbsp;Pinjaman</td>
						<td nowrap align="center">&nbsp;Tarikh Kelulusan</td>
						<td nowrap align="center">&nbsp;Tarikh Mula Bayar</td>
						<td nowrap align="center">&nbsp;Jumlah</td>						
						<td nowrap align="center">&nbsp;Tempoh</td>
						<td nowrap align="center">&nbsp;Jadual Bayar Balik</td>
						<td nowrap align="center">&nbsp;Baki Tertunggak</td>
					</tr>';
	$totalOutstanding = '0.00';
	while (!$GetLoan->EOF && $cnt <= $pg) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields(userID), "Text"));
		$status = $GetLoan->fields(status);
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		print ' <tr>
						<td class="Data" align="right" height="20">' . $bil . '&nbsp;</td>
						<td class="Data">&nbsp;' . sprintf("%010d", $GetLoan->fields(loanID)) . '</td>
						<td class="Data">&nbsp;' . dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")) . '</td>
						<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetLoan->fields(approvedDate)) . '</td>
						<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetLoan->fields(startPymtDate)) . '</td>
						<td class="Data" align="right">' . $GetLoan->fields('loanAmt') . '&nbsp;</td>
						<td class="Data" align="center">&nbsp;' . $GetLoan->fields('loanPeriod') . '</td>
						<td class="Data" align="center">&nbsp;';
		if ($GetLoan->fields(startPymtDate) <> "") {
			print '<input type=button value="Lihat Jadual" class="but" onClick="ITRActionButtonClick(\'' . $GetLoan->fields(loanID) . '\')";>';
		}
		print '		</td>
						<td class="Data" align="right">' . $GetLoan->fields('outstandingAmt') . '&nbsp;</td>
					</tr>';
		$cnt++;
		$bil++;
		$totalOutstanding += $GetLoan->fields(outstandingAmt);
		$GetLoan->MoveNext();
	}
	print ' 	<tr>
						<td class="Data" align="right" colspan="8" height="20">Jumlah&nbsp;&nbsp;&nbsp;</td>
						<td class="DataB" align="right">' . number_format($totalOutstanding, 2, '.', '') . '&nbsp;</td>
					</tr>
				 </table>
			</td>
		</tr>		
		<tr>
			<td>';
	if ($TotalRec > $pg) {
		print '
					<table border="0" cellspacing="5" cellpadding="0"  class="actionBG" width="100%">';
		if ($TotalRec % $pg == 0) {
			$numPage = $TotalPage;
		} else {
			$numPage = $TotalPage + 1;
		}
		print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Rekod : <b>' . $GetLoan->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk Pinjaman  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
</form>';

if ($id <> "") {
	$GetLoanDet = ctLoan("", $id);
	//	$loanAmt	= dlookup("general", "c_Maksimum", "ID=" . tosql($GetLoanDet->fields(loanType), "Number"));
	$loanAmt 	= $GetLoanDet->fields('loanAmt');
	//	$loanPeriod = dlookup("general", "c_Period", "ID=" . tosql($GetLoanDet->fields(loanType), "Number"));
	$loanPeriod 	= $GetLoanDet->fields('loanPeriod');
	$loanCaj	= dlookup("general", "c_Caj", "ID=" . tosql($GetLoanDet->fields(loanType), "Number"));

	$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
	$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
	$monthlyPay		= number_format($loanAmt / $loanPeriod, 2, '.', '');
	$lastmonthlyPay	= number_format($loanAmt - ($monthlyPay * ($loanPeriod - 1)), 2, '.', '');
	$interestPay	= number_format($totalInterest / $loanPeriod, 2, '.', '');
	$lastinterestPay = number_format($totalInterest - ($interestPay * ($loanPeriod - 1)), 2, '.', '');
	print '
	<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center"><tr><td>
	<table width="100%">
		<tr>
			<td class="textFont" width="150">Nombor Rujukan ID</td>
			<td class="Label">:&nbsp;' . sprintf("%010d", $id) . '</td>
		</tr>
		<tr>
			<td class="textFont" width="150">Caj Pinjaman (%)</td>
			<td class="Label">:&nbsp;' . $loanCaj . '</td>
		</tr>
		<tr>
			<td class="textFont" width="150">Baki Tertunggak</td>
			<td class="Label">:&nbsp;' . $GetLoanDet->fields('outstandingAmt') . '</td>
		</tr>
	</table>
	<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
		<tr class="header">
			<td nowrap>&nbsp;Tarikh</td>
			<td nowrap>&nbsp;No Pembayaran</td>
			<td nowrap align="center" width="100">&nbsp;Bayaran</td>
			<td nowrap align="center" width="100">&nbsp;Caj Perkhidmatan</td>
			<td nowrap align="center" width="150">&nbsp;Jumlah</td>						
		</tr>';
	$monthlyTotal = 0;
	$interestTotal = 0;
	$overallTotal = 0;
	$startDay	= toDate("d", $GetLoanDet->fields(startPymtDate));
	$startMonth = toDate("m", $GetLoanDet->fields(startPymtDate)) - 1;
	$startYear	= toDate("Y", $GetLoanDet->fields(startPymtDate));
	for ($i = 1; $i <= $loanPeriod; $i++) {
		$amtTotal = 0;
		$nextMonth = mktime(0, 0, 0, $startMonth + $i, $startDay,  $startYear);
		if ($i == $loanPeriod) {
			$amtTotal 		= $lastmonthlyPay + $lastinterestPay;
			$monthlyTotal 	= $monthlyTotal + $lastmonthlyPay;
			$interestTotal 	= $interestTotal + $lastinterestPay;
			print '
			<tr>
				<td nowrap class="data">&nbsp;' . date("d/m/Y", $nextMonth) . '</td>
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
				<td nowrap class="data" align="right">' . $monthlyPay . '&nbsp;</td>
				<td nowrap class="data" align="right">' . $interestPay . '&nbsp;</td>
				<td nowrap class="data" align="right">' . sprintf("%01.2f", $amtTotal) . '&nbsp;</td>						
			</tr>';
		}
		$overallTotal = $overallTotal + $amtTotal;
	}
	print '	
		<tr>
			<td nowrap class="Header" colspan="2" align="right"><b>Jumlah&nbsp;:&nbsp;&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . sprintf("%01.2f", $monthlyTotal) . '&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . sprintf("%01.2f", $interestTotal) . '&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . sprintf("%01.2f", $overallTotal) . '&nbsp;</b></td>						
		</tr>
	</table>
	</td></tr></table>';
}

include("footer.php");

print '
<script language="JavaScript">
	function ITRActionButtonClick(v) {
        e = document.MyForm;
        e.id.value = v;
        e.submit();
    }	   
		
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
