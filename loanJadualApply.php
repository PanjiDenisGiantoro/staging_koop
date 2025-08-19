<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanJadual.php
 *          Date 		: 	13/7/2006
 *********************************************************************************/
session_start();
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";

include("common.php");
include("koperasiinfo.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("koperasiList.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
}

$sFileName = 'loanJadual.php';
$sFileRef  = 'loanJadual.php';
$title     = "Jadwal Pengembalian Pembiayaan";

if ($loanAmt) {

	$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
	$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
	list($monthlyPay, $lastmonthlyPay, $interestPay, $lastinterestPay) = countPays($totalLoan, $totalInterest, $loanAmt, $loanCaj, $loanPeriod);
	print '
<!doctype html>
<html>
<head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>' . $emaNetis . '</title>	
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
</head>
<body>

';

	print '
	
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body"> 
                                    <h5 class="card-title">' . strtoupper($title) . '</h5>
                <hr>

                                    
                                    
        
	<table class="table table-sm">
                        <tr class="table-light">
                                <td class="textFont" width="150">Jumlah pembiayaan</td>
                                <td class="Label">:&nbsp;' . number_format($loanAmt, 2, '.', ',') . '</td>
                        </tr>
                        <tr class="table-light">
                                <td class="textFont" width="150">Tempoh (bulan)</td>
                                <td class="Label">:&nbsp;' . number_format($loanPeriod, 2, '.', ',') . '</td>
                        </tr>
                        <tr class="table-light">
                                <td class="textFont" width="150">Caj Pinjaman (%)</td>
                                <td class="Label">:&nbsp;' . number_format($loanCaj, 2, '.', ',') . '</td>
                        </tr>
	</table>
        
        <div class="table-responsive">
	<table class="table table-sm table-striped m-0 mt-3" >
		<tr class="table-primary">
			<td nowrap><b>Tarikh</b></td>
			<td nowrap><b>Nombor Pembayaran</b></td>
			<td nowrap align="right"><span align="center"><b>Pokok (RM)</b></span></td>
			<td nowrap align="right"><b>Untung (RM)</b></td>
			<td nowrap align="right"><b>Jumlah (RM)</b></td>						
		</tr>';
	$monthlyTotal = 0;
	$interestTotal = 0;
	$overallTotal = 0;
	for ($i = 1; $i <= $loanPeriod; $i++) {
		$amtTotal = 0;
		if ($i == $loanPeriod) {
			$amtTotal 		= $lastmonthlyPay + $lastinterestPay;
			$monthlyTotal 	= $monthlyTotal + $lastmonthlyPay;
			$interestTotal 	= $interestTotal + $lastinterestPay;
			print '
			<tr>
				<td nowrap class="">Bulan ' . $i . '</td>
				<td nowrap class="">Pembayaran #' . $i . '</td>
				<td nowrap class="" align="right">' . number_format($lastmonthlyPay, 2, '.', ',') . '</td>
				<td nowrap class="" align="right">' . number_format($lastinterestPay, 2, '.', ',') . '</td>
				<td nowrap class="" align="right">' . number_format($amtTotal, 2, '.', ',') . '</td>						
			</tr>';
		} else {
			$amtTotal 		= $monthlyPay + $interestPay;
			$monthlyTotal 	= $monthlyTotal + $monthlyPay;
			$interestTotal 	= $interestTotal + $interestPay;
			print '
			<tr>
				<td nowrap class="">Bulan ' . $i . '</td>
				<td nowrap class="">Pembayaran #' . $i . '</td>
				<td nowrap class="" align="right">' . number_format($monthlyPay, 2, '.', ',') . '</td>
				<td nowrap class="" align="right">' . number_format($interestPay, 2, '.', ',') . '</td>
				<td nowrap class="" align="right">' . number_format($amtTotal, 2, '.', ',') . '</td>						
			</tr>';
		}
		$overallTotal = $overallTotal + $amtTotal;
	}
	print '	
		<tr>
			<td nowrap class="headerteal" colspan="2" align="right"><b>Jumlah (RM)&nbsp;&nbsp;&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($monthlyTotal) . '</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($interestTotal) . '</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($overallTotal) . '</b></td>						
		</tr>
	</table></div>
        </div>
        </div>
        </div>
        
        ';
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
