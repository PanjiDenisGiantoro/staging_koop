<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanJadual.php
 *          Date 		: 	13/7/2006
 *********************************************************************************/
session_start();
if (!isset($StartRec))    $StartRec = 1;
if (!isset($pg))        $pg = 50;
if (!isset($q))            $q = "";
include("common.php");
//include("setupinfo.php");	
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
include("koperasiList.php");
include("header.php");

if (get_session('Cookie_userID') == "") {
    print '<script>alert("' . $errPage . '");window.close();</script>';
}

$errPage2 = "Potongan Gaji Berjaya DiMasukkan Ke Dalam Pangkalan Data";
$updatedDate = date("Y-m-d H:i:s");
$updatedBy     = get_session("Cookie_userName");
$updatedID = get_session("Cookie_groupID");

$strActionPage = 'loanJadual78Month_Pot.php?id=' . $id . '';
$sFileName = 'loanJadual78Month_Pot.php';
// $sFileRef  = 'loanJadual78Month_Pot.php';
$title     = "Jadual Bayar Balik Pembiayaan";


$GetLoanDet = ctLoanNew("", $id);
$rnoBaucer = dlookup("loandocs", "rnoBaucer", "loanID ='" . $id . "'");
$loanType = dlookup("loans", "loanType", "loanID ='" . $id . "'");
$Caj = dlookup("general", "c_caj", "ID ='" . $loanType . "'");


// Loan Insert Potbulan / PotBulanLook DB
if ($apply) {

    $startPymtDate = dlookup("vauchers", "tarikh_baucer", "no_baucer ='" . $rnoBaucer . "'");
    $loanType = dlookup("loans", "loanType", "loanID ='" . $id . "'");
    $startDay    = toDateMK("d", $startPymtDate);
    $startMonth = toDateMK("m", $startPymtDate);
    $startYear    = toDateMK("Y", $startPymtDate);

    // $nextMonthPot = mktime (0,0,0,$startMonth+1,$startDay,$startYear);
    $nextMonthPot = date('Y-m-28', mktime(0, 0, 0, $startMonth + 1, 1, $startYear));
    // $nextMonthMK = date("Y-m-d",$nextMonthPot);
    // $startDayNew = toDateMK("d",$nextMonthMK);
    $startMonthNew = toDateMK("m", $nextMonthPot);
    $startYearNew = toDateMK("Y", $nextMonthPot);
    $yymm = $startYearNew . $startMonthNew;

    $lastmonthlyPay = $GetLoanDet->fields('pokokAkhir');
    $lastmonthlyIntPay = $GetLoanDet->fields('untungAkhir');
    $totalAkhirByr  = $lastmonthlyPay + $lastmonthlyIntPay;
    $monthlyPayPokok        = $GetLoanDet->fields('pokok');
    $monthlyPay        = $GetLoanDet->fields('monthlyPymt');
    $interestPay    = number_format($GetLoanDet->fields('untung'), 2, '.', '');
    $fBasicTemp = $monthlyPayPokok * 12;

    $loanAmt    = $GetLoanDet->fields('loanAmt');
    $loanPeriod    = $GetLoanDet->fields('loanPeriod');
    $Caj = dlookup("general", "c_caj", "ID ='" . $loanType . "'");
    //$loanCaj	= $GetLoanDet->fields('kadar_u');
    $loanNo    = $GetLoanDet->fields('loanNo');
    $bondNO = $GetLoanDet->fields('rnoBond');
    $userID = $GetLoanDet->fields('userID');
    $fProfitMonthTemp = 0.00;
    $fProfitRateTemp = 0.00;


    $sSQL8 = "SELECT * FROM potbulan WHERE loanID = '" . $loanNo . "'";
    $GetData8 = $conn->Execute($sSQL8);


    if ($GetData8->RowCount() > 0) {

        print '<script>alert("Potongan gaji TELAH WUJUD !");</script>';
    } else {

        $sSQL4    = "INSERT INTO potbulan (" .
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
            "'" . $startYearNew . "', " .
            "'" . $startMonthNew . "', " .
            "'" . $monthlyPay . "')";

        $rsInstPtg = &$conn->Execute($sSQL4);

        $sSQL7 = "SELECT * FROM potbulan WHERE loanID = '" . $loanNo . "'  AND status = 1 ";
        $GetData7 = $conn->Execute($sSQL7);
        $IDPot = $GetData7->fields('ID');
        $nLoanMonthTotal    = $loanPeriod * (($loanPeriod + 1) / 2);

        for ($i = 1; $i <= $loanPeriod; $i++) {

            $totalInterest     = number_format($loanAmt * ($Caj / 100) * ($loanPeriod / 12), 2, '.', '');
            $totalLoan         = number_format($loanAmt + $totalInterest, 2, '.', '');
            $monthlyPayPokok        = number_format($GetLoanDet->fields('pokok'), 2, '.', '');
            $monthlyPay        = number_format($GetLoanDet->fields('monthlyPymt'), 2, '.', '');
            $lastmonthlyPay    = number_format($GetLoanDet->fields('pokokAkhir'), 2, '.', '');
            $interestPay    = number_format($GetLoanDet->fields('untung'), 2, '.', '');
            $lastinterestPay = number_format($GetLoanDet->fields('untungAkhir'), 2, '.', '');
            $totalByranAkhir = number_format($GetLoanDet->fields('untungAkhir') + $GetLoanDet->fields('pokokAkhir'), 2, '.', '');
            $jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoanDet->fields(userID), "Text"));
            $Monthly         = number_format($GetLoanDet->fields('monthlyPymt'), 2, '.', '');
            $startPymtDate = dlookup("vauchers", "tarikh_baucer", "no_baucer ='" . $rnoBaucer . "'");
            $startDay    = toDateMK("d", $startPymtDate);
            $startMonth = toDateMK("m", $startPymtDate);
            $startYear    = toDateMK("Y", $startPymtDate);
            $nextMonth = date('Y-m-28', mktime(0, 0, 0, $startMonth + $i, 1, $startYear));
            $DateNew = toDate("28/m/Y", $nextMonth);
            $yy = toDateMK("Y", $nextMonth);
            $mm = toDateMK("m", $nextMonth);
            $yymmNew1 = $yy . $mm;


            if ($i == 1) {
                $MonthlyIntPay78    = $totalInterest * $loanPeriod / $nLoanMonthTotal;
                $n = $loanPeriod;
                $MonthlyPrinciple78    = $Monthly - $MonthlyIntPay78;
                $amtTotal             = $MonthlyPrinciple78 + $MonthlyIntPay78;
                $monthlyTotal         = $monthlyTotal + $amtTotal;
                $interestTotal         = $interestTotal + $MonthlyIntPay78;
                $monthlyPricipleTotal = $monthlyPricipleTotal + $MonthlyPrinciple78;
                $BalanceLoan         = $totalLoan - $amtTotal;
                $BalancePriciple     = $loanAmt - $MonthlyPrinciple78;
                $balancePokok       =  thousand($BalancePriciple, 2, '.', ',');
            } elseif ($i < $loanPeriod) {

                $n = $loanPeriod - ($i - 1);

                $MonthlyIntPay78    = $totalInterest * ($loanPeriod - ($i - 1)) / $nLoanMonthTotal;
                $MonthlyPrinciple78    = $Monthly - $MonthlyIntPay78;
                $amtTotal             = $MonthlyPrinciple78 + $MonthlyIntPay78;
                $monthlyTotal         = $monthlyTotal + $amtTotal;
                $interestTotal         = $interestTotal + $MonthlyIntPay78;
                $monthlyPricipleTotal = $monthlyPricipleTotal + $MonthlyPrinciple78;
                $BalanceLoan = $totalLoan - $monthlyTotal;
                $BalancePriciple     = $BalancePriciple - $MonthlyPrinciple78;
                $balancePokok       =  thousand($BalancePriciple, 2, '.', ',');
            } else {
                //$BalanceLoan 		= $BalanceLoan;

                $MonthlyIntPay78    = $totalInterest * ($loanPeriod - ($i - 1)) / $nLoanMonthTotal;
                $MonthlyPrinciple78    = $BalanceLoan - $MonthlyIntPay78;
                $amtTotal             = $MonthlyPrinciple78 + $MonthlyIntPay78;
                $monthlyTotal         = $monthlyTotal + $amtTotal;
                $interestTotal         = $interestTotal + $MonthlyIntPay78;
                $monthlyPricipleTotal = $monthlyPricipleTotal + $MonthlyPrinciple78;
                $BalancePriciple     = - ($BalancePriciple - $MonthlyPrinciple78);
                $balancePokok       =  thousand($BalancePriciple, 2, '.', ',');
                $BalanceLoan = $totalLoan - $monthlyTotal; //$


                $sSQL10 = "";
                // $sWhere = "";		
                $sWhere = "ID=" . tosql($IDPot, "Number");
                $sWhere = " WHERE (" . $sWhere . ")";
                $sSQL10    = "UPDATE potbulan SET " .
                    " lastPymt=" . tosql($amtTotal, "Number") .
                    ",lastyrmthPymt=" . tosql($yymmNew1, "Number");

                $sSQL10 = $sSQL10 . $sWhere;
                $rs = &$conn->Execute($sSQL10);
            }

            $sSQL5    = "INSERT INTO potbulanlook (" .
                "potID," .
                "userID," .
                "loanType," .
                "loanID," .
                "yrmth," .
                "pokok," .
                "untung," .
                "PokokThn," .
                "createDate," .
                "userIDcreated," .
                "updateDate," .
                "prcent)" .
                " VALUES (" .
                "'" . $IDPot . "', " .
                "'" . $userID . "', " .
                "'" . $loanType . "', " .
                "'" . $loanNo . "', " .
                "'" . $yymmNew1 . "', " .
                "'" . $MonthlyPrinciple78 . "', " .
                "'" . $MonthlyIntPay78 . "', " .
                "'" . $BalancePriciple . "', " .
                "'" . $updatedDate . "', " .
                "'" . $updatedID . "', " .
                "'" . $updatedDate . "', " .
                "'" . $fProfitRateTemp . "')";

            $rsInstPtg = &$conn->Execute($sSQL5);
        }
        print '<script>alert("Rekod Potongan gaji telah DIKEMASKINI !");</script>';
    }
}


// end potbulan look


if ($id <> "") {

    $GetLoanDet = ctLoan("", $id);
    $rnoBaucer = dlookup("loandocs", "rnoBaucer", "loanID ='" . $id . "'");
    $caj = dlookup("general", "c_caj", "ID ='" . $loanType . "'");
    $loanAmt    = $GetLoanDet->fields('loanAmt');
    $loanPeriod    = $GetLoanDet->fields('loanPeriod');
    $loanCaj    = $GetLoanDet->fields('kadar_u');
    $loanNo    = $GetLoanDet->fields('loanNo');
    $bondNO = $GetLoanDet->fields('rnoBond');
    $userID = $GetLoanDet->fields('userID');
    $loanType = $GetLoanDet->fields('userID');

    $totalInterest     = number_format($loanAmt * ($Caj / 100) * ($loanPeriod / 12), 2, '.', '');
    $totalLoan         = number_format($loanAmt + $totalInterest, 2, '.', '');
    $monthlyPayPokok        = number_format($GetLoanDet->fields('pokok'), 2, '.', '');
    $monthlyPay        = number_format($GetLoanDet->fields('monthlyPymt'), 2, '.', '');
    $lastmonthlyPay    = number_format($GetLoanDet->fields('pokokAkhir'), 2, '.', '');
    $interestPay    = number_format($GetLoanDet->fields('untung'), 2, '.', '');
    $lastinterestPay = number_format($GetLoanDet->fields('untungAkhir'), 2, '.', '');
    $totalByranAkhir = number_format($GetLoanDet->fields('untungAkhir') + $GetLoanDet->fields('pokokAkhir'), 2, '.', '');
    $jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoanDet->fields(userID), "Text"));

    //$totalInterest 	= number_format($loanAmt * ($loanCaj/100) * ($loanPeriod/12), 2, '.', '');
    //$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
    $Monthly         = number_format($GetLoanDet->fields('monthlyPymt'), 2, '.', '');
    list($monthlyPayPokok, $lastmonthlyPay, $interestPay, $lastinterestPay) = countPays($totalLoan, $totalInterest, $loanAmt, $loanCaj, $loanPeriod);
    $startPymtDate = dlookup("vauchers", "tarikh_baucer", "no_bond ='" . $bondNO . "'");

    print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">

<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>';

    print '
    <form name="MyForm" action=' . $strActionPage . ' method="post">
    <input type="hidden" name="action">
	<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center"><tr><td>
	<table width="100%" >
        <tr><input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="apply" value="Potongan Gaji" onclick="PageRefresh();"/>&nbsp;<input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="apply_pat" value="Akaun Tabungan" onclick="PageRefresh();"/>
            </td>
            <tr>
			<td class="textFont" width="180">Nombor Anggota</td>
			<td class="Label">:&nbsp;' . $userID . '</td>
        </tr>      
        
            <tr>
			    <td class="textFont" width="180">Pembiayaan (RM)</td>
			    <td class="Label">:&nbsp;' . number_format($loanAmt, 2, '.', ',') . '</td>
            </tr>

            <tr>
			<td class="textFont" width="180">Keuntungan (RM)</td>
			<td class="Label">:&nbsp;' . number_format($totalInterest, 2, '.', ',') . '</td>
		</tr>
        <tr>
			<td class="textFont" width="180">Jumlah Pembiayaan (RM)</td>
			<td class="Label">:&nbsp;' . number_format($totalLoan, 2, '.', ',') . '</td>
		</tr>

		<tr>
			<td class="textFont" width="180">Tempoh (bulan)</td>
			<td class="Label">:&nbsp;' . $loanPeriod . '</td>
        </tr>
        

        
		<tr>
			<td class="textFont" width="180">Keuntungan Pembiayaan Tahunan (%)</td>
			<td class="Label">:&nbsp;' . number_format($Caj, 2, '.', ',') . '</td>
		</tr>

		
		<tr>
			<td class="textFont" width="180">Bayaran Bulanan (RM)</td>
			<td class="Label">:&nbsp;' . number_format($monthlyPay, 2, '.', ',') . '</td>
        </tr>
        <tr>
            <td class="textFont" width="150">No Bond</td>
            <td class="Label">:&nbsp;' . $bondNO . '</td>
        </tr>
        <tr>
            <td class="textFont" width="150">No Vaucher</td>
            <td class="Label">:&nbsp;' . $rnoBaucer . '</td>
        </tr>
        <tr>
        <td class="textFont" width="150">tarikh Vaucher</td>
        <td class="Label">:&nbsp;' . toDate("d/m/Y", $startPymtDate) . '</td>
    </tr>

	</table>';

    $startDay    = toDateMK("d", $startPymtDate);
    $startMonth = toDateMK("m", $startPymtDate);
    $startYear    = toDateMK("Y", $startPymtDate);



    if ($startPymtDate <> '') {

        print '
	<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
		<tr class="header">
			<td nowrap><b>&nbsp;Tarikh</td>
			<td nowrap><b>&nbsp;No Pembayaran</td>
			<td nowrap align="right"><b>Pokok</td>
			<td nowrap align="right"><b>Untung</td>
			<td nowrap align="right"><b>Bulanan</td>
			<td nowrap align="right"><b>Baki Pokok(RM)</td>
			<td nowrap align="right"><b>Baki Pembiayaan(RM)</td>					
		</tr>
		';

        $nLoanMonthTotal    = $loanPeriod * (($loanPeriod + 1) / 2);

        for ($i = 1; $i <= $loanPeriod; $i++) {

            $nextMonth = date('Y-m-28', mktime(0, 0, 0, $startMonth + $i, 1, $startYear));
            $DateNew = toDate("28/m/Y", $nextMonth);
            $yy = toDateMK("Y", $nextMonth);
            $mm = toDateMK("m", $nextMonth);
            $yymmNew1 = $yy . $mm;


            if ($i == 1) {
                $MonthlyIntPay78    = $totalInterest * $loanPeriod / $nLoanMonthTotal;
                $n = $loanPeriod;
                $MonthlyPrinciple78    = $Monthly - $MonthlyIntPay78;
                $amtTotal             = $MonthlyPrinciple78 + $MonthlyIntPay78;
                $monthlyTotal         = $monthlyTotal + $amtTotal;
                $interestTotal         = $interestTotal + $MonthlyIntPay78;
                $monthlyPricipleTotal = $monthlyPricipleTotal + $MonthlyPrinciple78;
                $BalanceLoan         = $totalLoan - $amtTotal;
                $BalancePriciple     = $loanAmt - $MonthlyPrinciple78;
            } elseif ($i < $loanPeriod) {

                $n = $loanPeriod - ($i - 1);

                $MonthlyIntPay78    = $totalInterest * ($loanPeriod - ($i - 1)) / $nLoanMonthTotal;
                $MonthlyPrinciple78    = $Monthly - $MonthlyIntPay78;
                $amtTotal             = $MonthlyPrinciple78 + $MonthlyIntPay78;
                $monthlyTotal         = $monthlyTotal + $amtTotal;
                $interestTotal         = $interestTotal + $MonthlyIntPay78;
                $monthlyPricipleTotal = $monthlyPricipleTotal + $MonthlyPrinciple78;
                $BalanceLoan = $totalLoan - $monthlyTotal;
                $BalancePriciple     = $BalancePriciple - $MonthlyPrinciple78;
            } else {
                //$BalanceLoan 		= $BalanceLoan;

                $MonthlyIntPay78    = $totalInterest * ($loanPeriod - ($i - 1)) / $nLoanMonthTotal;
                $MonthlyPrinciple78    = $BalanceLoan - $MonthlyIntPay78;
                $amtTotal             = $MonthlyPrinciple78 + $MonthlyIntPay78;
                $monthlyTotal         = $monthlyTotal + $amtTotal;
                $interestTotal         = $interestTotal + $MonthlyIntPay78;
                $monthlyPricipleTotal = $monthlyPricipleTotal + $MonthlyPrinciple78;
                $BalancePriciple     = - ($BalancePriciple - $MonthlyPrinciple78);
                $BalanceLoan = $totalLoan - $monthlyTotal; //$

            }

            print '
			<tr>
				<td nowrap class="data">' . $DateNew . '</td>
				<td nowrap class="data">&nbsp;Pembayaran Bulan(' . $i . ')</td>
				<td nowrap class="data" align="right">' . number_format($MonthlyPrinciple78, 2, '.', ',') . '&nbsp;</td>
				<td nowrap class="data" align="right">' . number_format($MonthlyIntPay78, 2, '.', ',') . '&nbsp;</td>
				<td nowrap class="data" align="right">' . number_format($amtTotal, 2, '.', ',') . '&nbsp;</td>
				<td nowrap class="data" align="right">' . number_format($BalancePriciple, 2, '.', ',') . '&nbsp;</td>
				<td nowrap class="data" align="right">' . number_format($BalanceLoan, 2, '.', ',') . '&nbsp;</td>							
			</tr>';

            $overallTotal = $overallTotal + $amtTotal;
        } // end loop for
        print '	
		<tr>
			<td nowrap class="Header" colspan="2" align="right"><b>Jumlah&nbsp;:&nbsp;&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($monthlyPricipleTotal) . '&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($interestTotal) . '&nbsp;</b></td>
			<td nowrap class="data" align="right"><b>' . thousand($overallTotal) . '&nbsp;</b></td>	
            <td nowrap class="data" align="right"></td> 
            <td nowrap class="data" align="right"></td> 					
		</tr>
    </table></form>';
    }

    print '
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
