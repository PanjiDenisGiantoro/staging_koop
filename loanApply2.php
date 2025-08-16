<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanApply.php
 *          Date 		: 	1/6/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
if (get_session("Cookie_groupID") == 0) {
	$userID		= get_session('Cookie_userID');
	$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

$sFileName		= "?vw=loanApply2&mn=$mn";
$sActionFileName = "?vw=loan&mn=$mn";
if (get_session("Cookie_groupID") == 0) {
	//$sqlC = "select * from loans where userID =".$userID." and isApproved <> 1";
	//$rsC = &$conn->Execute($sqlC);
	//if($rsC->RowCount() == 0) 
	$sActionFileName = "?vw=loanInProcess&mn=$mn";
}
$title     		= "Permohonan Pembiayaan";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();
if ($Semak <> "") {
	$a = 1;
	$FormLabel[$a]   	= "* No. Anggota";
	$FormElement[$a] 	= "memberID";
	if (get_session("Cookie_groupID") == 0) {
		$FormType[$a]	  	= "hiddentext";
	} else {
		$FormType[$a]	  	= "hiddentext";
	}
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "5";
	$FormLength[$a]  	= "12";
} else {
	$a = 1;
	$FormLabel[$a]   	= "* No. Anggota";
	$FormElement[$a] 	= "memberID";
	if (get_session("Cookie_groupID") == 0) {
		$FormType[$a]	  	= "hiddentext";
	} else {
		$FormType[$a]	  	= "textx";
	}
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "5";
	$FormLength[$a]  	= "12";
}

$a = $a + 1;
$FormLabel[$a]   	= "* Kad Pengenalan";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "12";
$FormLength[$a]  	= "12";

/*
$a = $a + 1;
$FormLabel[$a]   	= "No KP Lama";
$FormElement[$a] 	= "oldIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";
*/

$a = $a + 1;
$FormLabel[$a]   	= "* Kod Pembiayaan";
$FormElement[$a] 	= "loanCode";
if (get_session("Cookie_groupID") == 0) {
	$FormType[$a]	  	= "displayonly";
} else {
	$FormType[$a]	  	= "displayonly";
}
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "8";
$FormLength[$a]  	= "20";

$a = $a + 1;
$FormLabel[$a]   	= "* Kadar Keuntungan(%)";
$FormElement[$a] 	= "loanCajtexr";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "4";

if ((isset($_POST['Semak']) && $_POST['Semak'] !== "") || (isset($_POST['Kira']) && $_POST['Kira'] !== "")) {
	$a = $a + 1;
	$FormLabel[$a]   	= "* Tempoh Bayaran (Tahun)";
	$FormElement[$a] 	= "loanPeriod";
	$FormType[$a]	  	= "&nbsp;";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "13";

	$a = $a + 1;
	$FormLabel[$a]   	= "* Jumlah Pembiayaan (RM)";
	$FormElement[$a] 	= "loanAmt";
	$FormType[$a]	  	= "hiddentext";
	$FormData[$a]   	= "";

	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "13";

	$a = $a + 1;
	$FormLabel[$a]   	= "* Ada Pembiayaan Rumah?";
	$FormElement[$a] 	= "houseLoan";
	$FormType[$a]	  	= "&nbsp;";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "13";

	$a = $a + 1;
	$FormLabel[$a]    = "* Hendak Overlap?";
	$FormElement[$a] 	= "test";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a = $a + 1;
	$FormLabel[$a]    = "Jumlah Maksima Permohonan (RM)";
	$FormElement[$a] 	= "test";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
} else {
	$a = $a + 1;
	$FormLabel[$a]   	= "* Tempoh Bayaran (Tahun)";
	$FormElement[$a] 	= "loanPeriod";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('1 TAHUN', '2 TAHUN', '3 TAHUN', '4 TAHUN', '5 TAHUN', '6 TAHUN', '7 TAHUN', '8 TAHUN', '9 TAHUN', '10 TAHUN');
	$FormDataValue[$a]	= array('12', '24', '36', '48', '60', '72', '84', '96', '108', '120');
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "5";
	$FormLength[$a]  	= "3";

	$a = $a + 1;
	$FormLabel[$a]   	= "* Jumlah Pembiayaan (RM)";
	$FormElement[$a] 	= "loanAmt";
	$FormType[$a]	  	= "hiddentext";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "13";

	$a = $a + 1;
	$FormLabel[$a]   	= "* Ada Pembiayaan Rumah?";
	$FormElement[$a] 	= "houseLoan";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('TIADA', 'ADA');
	$FormDataValue[$a]	= array('0', '1');
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a = $a + 1;
	$FormLabel[$a]    = "* Hendak Overlap?";
	$FormElement[$a] 	= "test";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
}

if ($Semak == "" && $Kira == "") {
	$a = $a + 1;
	$FormLabel[$a]    = "Maklumat Perkiraan Pembiayaan";
	$FormElement[$a]  = "Semak";
	$FormType[$a]     = "submit";
	$FormData[$a]     = "";
	$FormDataValue[$a] = "";
	$FormCheck[$a]    = array();
	$FormSize[$a]     = "20";
	$FormLength[$a]   = "13";
}

if ($Semak <> "") {
	$a++;
	$FormLabel[$a]   	= "* Tujuan Pembiayaan";
	$FormElement[$a] 	= "purpose";
	$FormType[$a]	  	= "textarea";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "40";
	$FormLength[$a]  	= "7";

	$a++;
	$FormLabel[$a]   	= "&nbsp;";
	$FormElement[$a] 	= "Kira";
	$FormType[$a]	  	= "submit";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "13";
}
$a = $a + 1;
$FormLabel[$a]   	= "* Ansuran Bulanan (RM)";
$FormElement[$a] 	= "monthlyPymt";
$FormType[$a]	  	= "displayonly";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "13";
$FormLength[$a]  	= "13";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

if ($Semak <> "") {
	//--- BEGIN : Check loan ID ---
	if ($loanCode <> "" && $loanPeriod <> "" && $loanAmt <> "") {
		if (dlookup("general", "category", "code=" . tosql($loanCode, "Text")) == "C") {
			$loanType = dlookup("general", "ID", "code=" . tosql($loanCode, "Text"));
			$loanName = dlookup("general", "name", "ID=" . tosql($loanType, "Number"));
			$loanCaj  = dlookup("general", "c_Caj", "ID=" . tosql($loanType, "Number"));
			$loanPeriodMax  = dlookup("general", "c_Period", "ID=" . tosql($loanType, "Number"));
			$loanAmtMax  = dlookup("general", "c_Maksimum", "ID=" . tosql($loanType, "Number"));
			$codegroup	= dlookup("general", "parentID", "ID=" . tosql($loanType, "Number"));
			$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
			$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
			list($monthlyPay, $lastmonthlyPay, $interestPay, $lastinterestPay) = countPays($totalLoan, $totalInterest, $loanAmt, $loanCaj, $loanPeriod);

			$monthlyPymt	= $monthlyPay + $interestPay;
		} else {
			$Semak = "";
			print '<script>alert("Kod pembiayaan : ' . $loanCode . ' tidak wujud...!");</script>';
		}
	} else {
		$Semak = "";
		//print '<script>alert("Sila masukkan semua butiran pembiayaan");</script>';
		alert("Sila masukkan semua butiran pembiayaan");
	}
	//--- END   : Check loan ID ---	
	//--- BEGIN : Check member ID ---
	if ($Semak <> "") {
		if ($memberID <> "") {
			if (dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text")) <> "") {
				$userID = dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text"));
				$userName 	= dlookup("users", "name", "userID=" . tosql($userID, "Text"));
				$newIC 	= dlookup("userdetails", "newIC", "userID=" . tosql($userID, "Text"));
				//$oldIC 	= dlookup("userdetails", "oldIC", "userID=" . tosql($userID, "Text"));		
				$unitOnHand = dlookup("userdetails", "totalShare", "userID=" . tosql($userID, "Text"));
			} else {
				$Semak = "";
				print '<script>alert("No Anggota : ' . $memberID . ' tidak wujud/sah...!");</script>';
			}
		} else {
			$Semak = "";
			print '<script>alert("Sila masukkan no anggota");</script>';
		}
	}
	//--- END   : Check member ID ---
}


if ($Kira <> "") {
	if ($loanCode <> "" && $loanPeriod <> "" && $maxAmount <> "") {
		if (dlookup("general", "category", "code=" . tosql($loanCode, "Text")) == "C") {
			$loanType = dlookup("general", "ID", "code=" . tosql($loanCode, "Text"));
			$loanName = dlookup("general", "name", "ID=" . tosql($loanType, "Number"));
			$loanCaj  = dlookup("general", "c_Caj", "ID=" . tosql($loanType, "Number"));
			$loanPeriodMax  = dlookup("general", "c_Period", "ID=" . tosql($loanType, "Number"));
			$loanAmtMax  = dlookup("general", "c_Maksimum", "ID=" . tosql($loanType, "Number"));
			$codegroup	= dlookup("general", "parentID", "ID=" . tosql($loanType, "Number"));
			$totalInterest 	= $maxAmount * ($loanCaj / 100) * ($loanPeriod / 12);
			$totalLoan 		= $maxAmount + $totalInterest;
			list($monthlyPay, $lastmonthlyPay, $interestPay, $lastinterestPay) = countPays($totalLoan, $totalInterest, $maxAmount, $loanCaj, $loanPeriod);
			$monthlyPymt	= $monthlyPay + $interestPay;
		}
	}
}

if ($SubmitForm <> "") {
	$pass = 1;
	$sSQL = "SELECT	* FROM loans 
				 WHERE status <> 3 AND status <> 4 AND status <> 5 AND status <> 9 AND statusL = 0 AND userID = '" . $userID . "' 
				 ORDER BY applyDate ASC";


	$GetLoan = &$conn->Execute($sSQL);
	if ($GetLoan->RowCount() <> 0) {
		print '<script>
					alert ("Terdapat permohonan pembiayaan belum siap diproses untuk anggota ini!");
					//window.location.href = "loanView.php";
					window.location.href = "?vw=loan&mn=' . $mn . '";
				</script>';
		$pass = 0;
	}
}

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
<input type="hidden" name="kadar_u" value="' . $loanCaj . '">
<input type="hidden" name="loanType" value="' . $loanType . '">
<input type="hidden" name="loanType" value="' . $loanType . '">
<input type="hidden" name="pokok" value="' . $monthlyPay . '">
<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">
<input type="hidden" name="untung" value="' . $interestPay . '">
<input type="hidden" name="pokokAkhir" value="' . $lastmonthlyPay . '">
<input type="hidden" name="untungAkhir" value="' . $lastinterestPay . '">
<input type="hidden" name="totalLoan" value="' . $totalLoan . '">
<input type="hidden" name="unitOnHand" value="' . $unitOnHand . '">
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="Data">
<div style="display: flex;"><h5 class="card-title" style="flex-grow: 1;"><i class="mdi mdi-application"></i>&nbsp;' . strtoupper($title) . '</h5>
<input type="button" align="right" value="Set Semula" class="btn btn-primary btn-sm" onClick="window.location.href=\'?vw=loanApply2&mn=' . $mn . '\';"/>
</div><br/>';

if ($Semak <> "") {
	//gaji pokok	
	$pic = dlookup("userloandetails", "gaji_img", "userID=" . tosql($userID, "Text"));
	$picjwtn = dlookup("userloandetails", "jwtn_img", "userID=" . tosql($userID, "Text"));
	$picic = dlookup("userloandetails", "ic_img", "userID=" . tosql($userID, "Text"));
	$picccris = dlookup("userloandetails", "ccris_img", "userID=" . tosql($userID, "Text"));
	$YuranBlnan = dlookup("userdetails", "monthFee", "userID=" . tosql($userID, "Text"));

	$sqlGet = "select SUM(amt) as amt from userstates where userID = '" . $userID . "' and payType = 'A'";
	$GettotA =  &$conn->Execute($sqlGet);
	$totalA = $GettotA->fields(amt);
	$netPay = $totalA; // total jumlah pendapatan (gaji kasar)
	if (!$totalA) {
		$layakSDesc = 'Pastikan maklumat pendapatan dan perbelanjaan anggota dilengkapkan !<br><br>';
		$layakS = "N";
		$Nisbahdsr = 100;
	} else {

		//potongan gaji	
		$sqlGetB = "select sum(amt) as amt from userstates where userID = '" . $userID . "' and payType = 'B'";
		$GettotB =  &$conn->Execute($sqlGetB);
		$totalB = $GettotB->fields(amt) + $YuranBlnan; // total jumlah perbelanjaan

		$sqlGetBKWSP = "select sum(amt) as amt from userstates where userID = '" . $userID . "' and payID IN ('1563','1564') and payType = 'B'";
		$GettotBKWSP =  &$conn->Execute($sqlGetBKWSP);
		$totalBKWSPSOC = $GettotBKWSP->fields(amt);
		//get total debit

		$sqlGetkoop = "select sum(amt) as amt from userstates where userID = '" . $userID . "' and payID IN ('1569') and payType = 'B'";
		$Gettotkoop =  &$conn->Execute($sqlGetkoop);
		$totalkoop = $Gettotkoop->fields(amt);

		$payIDCCRIS = '1839';
		$checkStatesCCRIS = "SELECT * FROM userstates WHERE userID = '" . $userID . "' AND payID ='" . $payIDCCRIS . "'"; //sewa
		$rscheckStatesCCRIS = $conn->Execute($checkStatesCCRIS);
		$CCRIS = $rscheckStatesCCRIS->fields(amt);

		$JumPotBaru = $monthlyPymt + $totalB; // T5     //jumlah perbelanjaan + bayaran bulanan
		$JumPotxSi = $JumPotBaru - ($totalBKWSPSOC + $CCRIS);	// AK
		$JumBsihxSi = $netPay - ($totalBKWSPSOC + $CCRIS); //AM     //jumlah pendapatan (gaji kasar)
		$Nisbahdsr = (number_format(($JumPotxSi / $JumBsihxSi) * 100)); // AK/AM*100

		if ($totalA <> 0 && $totalB <> 0) {
			if ($houseLoan == 1) {
				$NetPay75 = $totalA  * (75 / 100);
				$LayakPay75 = $NetPay75 - ($totalB);
				$overlap = $LayakPay75 + $totalUntungS;
				$maxAmount = ($overlap * $loanPeriod) / (1 + ($loanPeriod / 12) * ($loanCaj / 100));
			} else {
				$NetPay50 = $totalA * (59 / 100);
				$LayakPay50 = $NetPay50 - ($totalB);
				$overlap = $LayakPay50 + $totalUntungS;
				$maxAmount = ($overlap * $loanPeriod) / (1 + ($loanPeriod / 12) * ($loanCaj / 100));
			}
		} else {
			$layakSDesc = 'SILA KEMASKINI MAKLUMAT PEMBIAYAAN ATAU GAJI TERKINI<br><br>';
			// $layakS = "N";
		}
	}
	if (($loanAmt > $loanAmtMax) or ($loanPeriod > $loanPeriodMax)) { //amik lepas KIRA
		$layakSDesc = 'Pastikan JUMLAH PERMOHONAN (RM)  / TEMPOH BAYARAN(BULAN) tidak melebihi JUMLAH PEMBIAYAAN (RM) / TEMPOH PEMBIAYAAN (BULAN) telah yang ditetapkan.<br><br>';
		$layakS = "N";
	}

	if ($houseLoan == 1) { // 75%

		if ($loanAmt >= 100000) {
			$layakCCRIS = 'SILA KEMUKAKAN LAPORAN CCRIS DARIPADA BANK NEGARA UNTUK MEMUDAHKAH PROSES PERMOHONAN';
		}


		if ($monthlyPymt > $LayakPay75) {
			$layakSDesc = 'Tidak Layak Memohon. Nisbah DSR melebihi 75%. / (SILA KEMASKINI MAKLUMAT PEMBIAYAAN ATAU GAJI TERKINI) Jumlah Maximum Bayaran Balik Bulanan yang dibenarkan RM ' . number_format($LayakPay75, 2) . ' dan DSR ' . number_format($Nisbahdsr, 2) . '%<br><br>';
			$layakS = "N";
			print '<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">';
		} else {

			$layakSDesc = 'Layak Memohon (Dalam Proses) dan DSR ' . number_format($Nisbahdsr, 2) . '% ' . $layakCCRIS . '';
			$layakS = "Y";
			print '<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">';
		}
	} else {

		if ($monthlyPymt > $LayakPay50) {
			$layakSDesc = 'Tidak Layak Memohon Nisbah. DSR melebihi 50%. /  (SILA KEMASKINI MAKLUMAT PEMBIAYAAN ATAU GAJI TERKINI)Jumlah Maximum Bayaran Balik Bulanan yang dibenarkan RM ' . $LayakPay50 . ' dan DSR ' . number_format($Nisbahdsr, 2) . ' %<br><br>';
			$layakS = "N";
			print '<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">';
		} else {
			$layakSDesc = 'Layak Memohon (Dalam Proses)' . $layakCCRIS . ' dan DSR ' . number_format($Nisbahdsr, 2) . '%';
			$layakS = "Y";
			print '<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">';
		}
	}
	// ------------------------------- end qualificaton


}

if ($Kira <> "") {
	if ($maxAmount > $loanAmt) {
		$layakSDesc = 'Jumlah Permohonan melebihi had maksimum yang ditetapkan. Jumlah maksimum yang layak dipohon adalah tidak melebihi RM ' . number_format($loanAmt, 2) . '<br><br>';
		$layakS = "N";
	} else {
		$layakSDesc = 'Layak Memohon (Dalam Proses) dan DSR ' . $Nisbahdsr . '%';
		$layakS = "Y";
	}
}

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<div class="card-header">MAKLUMAT ANGGOTA</div>';
	if ($i == 3) {
		print '<tr><td colspan=2><div class="card-header">PRA KELAYAKAN PERMOHONAN PEMBIAYAAN</div></td></tr>';
	}
	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . '</td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = $$FormElement[$i];
	FormEntry(
		$FormLabel[$i],
		$FormElement[$i],
		$FormType[$i],
		$strFormValue,
		$FormData[$i],
		$FormDataValue[$i],
		$FormSize[$i],
		$FormLength[$i]
	);

	if ($i == 1) {
		if ($Semak == "") {
			if (get_session("Cookie_groupID") == 1 or get_session("Cookie_groupID") == 2) {
				print '
				<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="document.MyForm.monthlyPymt.value=\'\';window.open(\'selMember.php\',\'sel\',\'top=10,left=10,width=1300,height=700,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
			}
			print '&nbsp;<label><input type="text" name="userName" class="form-control" value="' . $userName . '" onfocus="this.blur()" size="50"></label>';
		} else {
			print '&nbsp;<label><input type="text" name="userName" class="form-control" value="' . $userName . '" onfocus="this.blur()" size="50"></label>';
		}
	}

	if ($i == 3) {
		if ($Semak == "" && $Kira == "") {
			print '
			<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="document.MyForm.monthlyPymt.value=\'\'; var userid = document.MyForm.userID.value; window.open(\'selLoan.php?userID=\'+userid,\'sel\',\'top=10,left=10,width=1300,height=700,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
		}
		print '&nbsp;<input type="text" name="loanName" class="form-controlx" value="' . $loanName . '" onfocus="this.blur()" size="80">';
	}

	if ($i == 4) {
		print '<label><input type="text" name="loanCaj" class="form-controlx" value="' . $loanCaj . '" onfocus="this.blur()" size="6"></label>';
	}

	if ($i == 5) {
		// Mapping months to years
		$months_to_years = array(
			'12' => '1 TAHUN',
			'24' => '2 TAHUN',
			'36' => '3 TAHUN',
			'48' => '4 TAHUN',
			'60' => '5 TAHUN',
			'72' => '6 TAHUN',
			'84' => '7 TAHUN',
			'96' => '8 TAHUN',
			'108' => '9 TAHUN',
			'120' => '10 TAHUN'
		);

		$selected_month = $loanPeriod;

		// Check if the 'Semak' or 'Kira' button is clicked
		if ((isset($_POST['Semak']) && $_POST['Semak'] !== "") || (isset($_POST['Kira']) && $_POST['Kira'] !== "")) {
			// Check if the selected months exist in the predefined months_to_years array
			if (isset($months_to_years[$selected_month])) {
				// If a match is found, display the loan period in years, but store and submit the months value.
				print '&nbsp;<input type="text" name="selected_month" class="form-controlx" value="' . $months_to_years[$selected_month] . '" onfocus="this.blur()" size="50">';
				print '<input type="hidden" name="loanPeriod" class="form-controlx" value="' . $loanPeriod . '">';
			}
		}
	}

	if ($i == 7) {
		if ($Semak <> "" || isset($_POST['Kira']) && $_POST['Kira'] !== "") {
			if ($houseLoan == 0) {
				print '<label><input type="hiddentext" name="houseLoan" class="form-controlx" value="Tiada" onfocus="this.blur()" size="6"></label>';
			} else {
				print '<label><input type="hiddentext" name="houseLoan" class="form-controlx" value="Ada" onfocus="this.blur()" size="6"></label>';
			}
		}
	}

	if ($i == 8) {
		// Ensure these values are initialized or set only once and remain unchanged
		$totalBakiBBFormatted = $totalBakiBB;
		$totalUntungSFormatted = $totalUntungS;

		if ($Semak != "") { // When $Semak is not empty, show the selected value in a readonly text field
			$overlap = $_POST['overlap'];
			print '
				<input type="text" name="overlap" class="form-controlx" id="overlapField" value="' . ($overlap == "1" ? 'Ya' : 'Tidak') . '" readonly/><br/>
				
				Jumlah Baki : <input type="text" name="totalBakiBB" class="form-controlx" id="totalBakiBBField" value="' . $totalBakiBBFormatted . '" readonly/><br/>
				Jumlah Bayaran Bulanan : <input type="text" name="totalUntungS" class="form-controlx" id="totalUntungSField" value="' . $totalUntungSFormatted . '" readonly/><br/>
			';
		} elseif ($Kira != "") { // When $Kira is not empty but $Semak is empty, show the selected value in a readonly text field
			$overlap = $_POST['overlap']; // assuming overlap is set before this
			print '
				<input type="text" name="overlap" class="form-controlx" id="overlapField" value="' . $overlap . '" readonly/><br/>
				
				Jumlah Baki : <input type="text" name="totalBakiBB" class="form-controlx" id="totalBakiBBField" value="' . $totalBakiBBFormatted . '" readonly/><br/>
				Jumlah Bayaran Bulanan : <input type="text" name="totalUntungS" class="form-controlx" id="totalUntungSField" value="' . $totalUntungSFormatted . '" readonly/><br/>
			';
		} else { // When both $Semak and $Kira are empty, show the default dropdown
			print '
				<select name="overlap" id="overlap" onchange="showPopup()" class="form-selectx" data-userid="' . $userID . '">
					<option value="overlap" selected="selected">- pilih * Hendak Overlap? -</option>
					<option value="1">Ya</option>
					<option value="0">Tidak</option>
				</select><br/>
				
				Jumlah Baki : <input type="text" name="totalBakiBB" class="form-controlx" id="totalBakiBBField" value="' . $totalBakiBBFormatted . '" readonly/><br/>
				Jumlah Bayaran Bulanan : <input type="text" name="totalUntungS" class="form-controlx" id="totalUntungSField" value="' . $totalUntungSFormatted . '" readonly/><br/>
			';
		}
	}



	if ($i == 9) {
		if ((isset($_POST['Semak']) && $_POST['Semak'] !== "") || (isset($_POST['Kira']) && $_POST['Kira'] !== "")) {
	
			// Tentukan sama ada readonly perlu ditambah
			$readonly = (isset($_POST['Kira']) && $_POST['Kira'] !== "") ? 'readonly' : '';
	
			if ($maxAmount > $loanAmt) {
				print '<label><input type="text" name="maxAmount" class="form-controlx text-danger" id="maxAmount" value="' . $maxAmount . '" style="font-weight: bold;" ' . $readonly . '></label>';
				print '<br/><i class="mdi mdi-information-outline text-danger"> Jumlah Maksima Permohonan tidak boleh melebihi jumlah pembiayaan yang dibenarkan iaitu sebanyak RM ' . number_format($loanAmt, 2) . '. <u>Sila tukar jumlah tersebut.</u></i>';
			} else {
				print '<label><input type="text" name="maxAmount" class="form-controlx text-primary" id="maxAmount" value="' . $maxAmount . '" ' . $readonly . '></label>';
			}
		}
	}
	
	if ($i == 10) {
		if ($Kira <> "" && $Semak == "") {
			print '
			<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped form-control-plaintext" style="font-size: 10pt;">
			<tr><td class="Data">Jadual Bayar Balik Pembiayaan</td><td class="Label"> &nbsp;';

			if ($codegroup <> 1638) {
				print '<input type="button" value="Jadual" class="btn btn-info btn-sm" onclick="openJadual(\'loanJadualApply.php?loanAmt=' . $maxAmount . '&loanPeriod=' . $loanPeriod . '&loanCaj=' . $loanCaj . '\');">';
			} else {
				print '<input type="button" value="Jadual" class="btn btn-info btn-sm" onclick="openJadual(\'loanJadual78_Apply.php?type=vehicle&page=view&id=' . $userID . '&loanAmt=' . $maxAmount . '&loanPeriod=' . $loanPeriod . '&loanCaj=' . $loanCaj . '\');">';
			}

			print '</td></tr>
			<tr><td class="Data">Jumlah Pembiayaan ( Termasuk Untung )</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($totalLoan, 2, '.', ',') . '</b></td></tr>
			<tr><td class="Data">Bayaran Pokok</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($monthlyPay, 2, '.', ',') . '</b></td></tr>
			<tr><td class="Data">Bayaran Pokok Terakhir</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($lastmonthlyPay, 2, '.', ',') . '</b></td></tr>
			<tr><td class="Data">Untung Bulanan</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($interestPay, 2, '.', ',') . '</b></td></tr>
			<tr><td class="Data">Untung Bulanan Terakhir</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($lastinterestPay, 2, '.', ',') . '</b></td></tr>
			</table></div>';
		}
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td></tr>';
}

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($pass) {

	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
		for ($j = 0; $j < count($FormCheck[$i]); $j++) {
			FormValidation(
				$FormLabel[$i],
				$FormElement[$i],
				$$FormElement[$i],
				$FormCheck[$i][$j],
				$i
			);
		}
	}
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
		$applyDate = date("Y-m-d H:i:s");
		$year = date("Y");
		$month  = date('m');
		$pre_loan = date("Y-m");
		$sSQL = "SELECT max( right( loanNo, 3 ) ) as no FROM `loans` WHERE month( applyDate ) = " . $month . " AND year( applyDate ) =" . $year;
		$rs = &$conn->Execute($sSQL);
		$no = $rs->fields('no');
		if ($no) {
			$no = (int)$no;
			$no++;
		} else {
			$no = 1;
		}
		$no = sprintf("%03s",  $no);
		$loanNo = $pre_loan . '-' . $no;
		$sSQL	= "INSERT INTO loans (" .
			"loanNo," .
			"loanType," .
			"kadar_u," .
			"loanAmt," .
			"loanPeriod," .
			"pokok," .
			"untung," .
			"pokokAkhir," .
			"untungAkhir," .
			"userID," .
			"monthlyPymt," .
			"houseLoan," .
			"purpose," .
			"outstandingAmt," .
			"Nisbahdsr," .
			"GajiKasar," .
			"applyDate)" .
			" VALUES (" .
			tosql($loanNo, "Text") . "," .
			tosql($loanType, "Number") . "," .
			tosql($kadar_u, "Text") . "," .
			tosql($maxAmount, "Number") . "," .
			tosql($loanPeriod, "Number") . "," .
			tosql($pokok, "Number") . "," .
			tosql($untung, "Number") . "," .
			tosql($pokokAkhir, "Number") . "," .
			tosql($untungAkhir, "Number") . "," .
			tosql($userID, "Text") . "," .
			tosql($monthlyPymt, "Number") . "," .
			tosql($houseLoan, "Number") . "," .
			tosql($purpose, "Text") . "," .
			tosql($totalLoan, "Text") . "," .
			tosql($Nisbahdsr, "Text") . "," .
			tosql($totalA, "Number") . "," .
			tosql($applyDate, "Text") . ")";
		//print $sSQL;
		$rs = &$conn->Execute($sSQL);

		$sSQL = "SELECT max( loanID ) as maxID FROM `loans`";
		$rs = &$conn->Execute($sSQL);
		$maxID = $rs->fields('maxID');

		$sSQL	= "INSERT INTO loandocs (" .
			"loanID," .
			"userID)" .
			" VALUES (" .
			tosql($maxID, "Text") . "," .
			tosql($userID, "Text") . ")";
		//print $sSQL;
		$rs = &$conn->Execute($sSQL);
		//####################################
		//place to check and prompt user if guarantor is required
		$loanGuarantor  = dlookup("general", "c_gurrantor", "ID=" . tosql($loanType, "Number"));
		if ($loanGuarantor == 1) {
			print '<script>';
			if (get_session("Cookie_groupID") == 0) {
				print '	alert ("Permohonan pembiayaan telah didaftarkan ke dalam sistem. Pembiayaan yang dipohon memerlukan penjamin. Sila isikan borang penjamin.");';
			}
			print 	'window.location.href = "' . $sActionFileName . '";</script>';
		} else {
			print '<script>
					alert ("Permohonan pembiayaan telah didaftarkan ke dalam sistem."' . $mesg . ');
					window.location.href = "' . $sActionFileName . '";
				</script>';
		}
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

if ($Kira <> "") {
	print '
		<!--tr><td class="DataB" align="right" valign="top">ANGKASA&nbsp;</td><td class="Label" valign="top"><font class="redText">' . $layakADesc . '</font></td></tr-->
		<tr><td class="DataB" align="right" valign="top">Semakan Koperasi&nbsp;</td><td class="Label" valign="top"><font class="redText">' . $layakSDesc . '</font></td></tr>';
}
if ($Kira <> "") {
	if ($layakS == 'Y') {
		print '
		<tr><td class="DataB" align="right" valign="top">Proses Permohonan&nbsp;</td>
			<td><input type="Submit" name="SubmitForm" class="btn btn-primary" value="Mohon Pembiayaan">&nbsp;
			</td>
		</tr>';
	}
}
print '
</table>
</form>';

include("footer.php");

print '
<script type="text/javascript">
    function showPopup() {
        var overlapSelect = document.getElementById("overlap");
        var selectedValue = overlapSelect.value;

        // Pastikan untuk mendapatkan userID dari atribut data-userid yang berada dalam elemen select
        var userID = overlapSelect.getAttribute("data-userid");

        if (selectedValue == "1") {  // Jika "Ya" dipilih
            window.open(\'overlap.php?pk=\' + userID, \'sel\', \'top=10,left=10,width=1300,height=700,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');
        }
    }

	 function openJadual(url) {
        window.open(url, "pop", "width=800,height=600,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
    }
</script>';
