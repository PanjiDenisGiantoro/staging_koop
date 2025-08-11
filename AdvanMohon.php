<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	AdvanMohon.php
 *          Date 		: 	08/05/2024
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

include("forms.php");

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
if (get_session("Cookie_groupID") == 0) {
	$userID		= get_session('Cookie_userID');
	$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

$sFileName		= "?vw=AdvanMohon&mn=$mn";
$sActionFileName = "?vw=AdvanInProses&mn=$mn";
// if(get_session("Cookie_groupID") == 0){
// //$sqlC = "select * from loans where userID =".$userID." and isApproved <> 1";
// //$rsC = &$conn->Execute($sqlC);
// //if($rsC->RowCount() == 0)
// 	$sActionFileName= "?vw=AdvanMember&mn=$mn";
// }
$title  = "Permohonan Advance Payment";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

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
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "8";
$FormLength[$a]  	= "20";

$a = $a + 1;
$FormLabel[$a]   	= "* Caj Perkhidmatan (%)";
$FormElement[$a] 	= "loanCajtexr";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "4";

if ($Kira <> "") {

	$a = $a + 1;
	$FormLabel[$a]   	= "* Jumlah Pembiayaan (RM)";
	$FormElement[$a] 	= "loanAmt";
	$FormType[$a]	  	= "hiddentext";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= "";
	$FormSize[$a]    	= "10";
	$FormLength[$a]  	= "13";

	$a = $a + 1;
	$FormLabel[$a]   	= "Jumlah Pokok + Caj Perkhidmatan (RM)";
	$FormElement[$a] 	= "";
	$FormType[$a]	  	= "";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= "";
	$FormSize[$a]    	= "";
	$FormLength[$a]  	= "";
	
	$a = $a + 1;
	$FormLabel[$a]   	= "* Tempoh Bayaran (Bulan)";
	$FormElement[$a] 	= "loanPeriod";
	$FormType[$a]	  	= "&nbsp;";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= "";
	$FormSize[$a]    	= "";
	$FormLength[$a]  	= "";

} else {

	$a = $a + 1;
	$FormLabel[$a]   	= "* Jumlah Pembiayaan (RM)";
	$FormElement[$a] 	= "loanAmt";
	$FormType[$a]	  	= "textx";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "13";

	$a = $a + 1;
	$FormLabel[$a]   	= "Maklumat Perkiraan Pembiayaan";
	$FormElement[$a] 	= "Kira";
	$FormType[$a]	  	= "submit";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "13";
	
}

// $a = $a + 1;
// $FormLabel[$a]   	= "* Ansuran Bulanan (RM)";
// $FormElement[$a] 	= "monthlyPymt";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
// $FormSize[$a]    	= "13";
// $FormLength[$a]  	= "13";

// $a++;
// $FormLabel[$a]   	= "* Tujuan Pembiayaan";
// $FormElement[$a] 	= "purpose";
// $FormType[$a]	  	= "textarea";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array(CheckBlank);
// $FormSize[$a]    	= "40";
// $FormLength[$a]  	= "7";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

/*if ($Kira <> "") {
	//--- BEGIN : Check loan ID ---
	if ($loanCode <> "" && $loanPeriod <> "" && $loanAmt <> "") {
		if (dlookup("general", "category", "code=" . tosql($loanCode, "Text")) == "O") {
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
			$Kira = "";
			print '<script>alert("Kod pembiayaan : ' . $loanCode . ' tidak wujud...!");</script>';
		}
	} else {
		$Kira = "";
		//print '<script>alert("Sila masukkan semua butiran pembiayaan");</script>';
		alert("Sila masukkan semua butiran pembiayaan");
	}
	//--- END   : Check loan ID ---	
	//--- BEGIN : Check member ID ---
	if ($Kira <> "") {
		if ($memberID <> "") {
			if (dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text")) <> "") {
				$userID = dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text"));
				$userName 	= dlookup("users", "name", "userID=" . tosql($userID, "Text"));
				$newIC 	= dlookup("userdetails", "newIC", "userID=" . tosql($userID, "Text"));
				//$oldIC 	= dlookup("userdetails", "oldIC", "userID=" . tosql($userID, "Text"));
				$unitOnHand = dlookup("userdetails", "totalShare", "userID=" . tosql($userID, "Text"));
			} else {
				$Kira = "";
				print '<script>alert("No Anggota : ' . $memberID . ' tidak wujud/sah...!");</script>';
			}
		} else {
			$Kira = "";
			print '<script>alert("Sila masukkan no anggota");</script>';
		}
	}
	//--- END   : Check member ID ---
}*/
/*if ($SubmitForm <> "") {
	$pass = 1;
	$sSQL = "SELECT	* FROM advance_pay
				 WHERE status <> 3 AND status <> 4 AND status <> 5 AND status <> 9 AND userID = '" . $userID . "'
				 ORDER BY applyDate ASC";


	$GetLoan = &$conn->Execute($sSQL);
	if ($GetLoan->RowCount() <> 0) {
		print '<script>
					alert ("Terdapat permohonan pembiayaan belum siap diproses untuk anggota ini!");
					//window.location.href = "AdvanView.php";
					window.location.href = "?vw=AdvanPay&mn=' . $mn . '";
				</script>';
		$pass = 0;
	}
}*/
//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->

$profit = $loanAmt * ($loanCaj / 100);

$monthlyPymt1 = ($profit / 3) + ($loanAmt / 3);
$monthlyPymt2 = ($profit / 6) + ($loanAmt / 6);
$monthlyPymt3 = ($profit / 12) + ($loanAmt / 12);

$monthlyPymt = 0;
$pokokAkhir = 0;
$untungAkhir = 0;

$SubmitForm = isset($_POST['SubmitForm']) ? $_POST['SubmitForm'] : '';
$selectedRadio = isset($_POST['radio']) ? $_POST['radio'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $SubmitForm != '') {
    if ($selectedRadio == "3") {
        $monthlyPymt = $monthlyPymt1;
		$loanPeriod = 3;
    } else if ($selectedRadio == "6") {
        $monthlyPymt = $monthlyPymt2;
		$loanPeriod = 6;
    } else if ($selectedRadio == "12") {
        $monthlyPymt = $monthlyPymt3;
		$loanPeriod = 12;
    }
    $pokok = $loanAmt/$loanPeriod;
	$untung = $profit/$loanPeriod;
	$pokokAkhir = $loanAmt/$loanPeriod;
	$untungAkhir = $profit/$loanPeriod;

	// --- Begin : Call function FormValidation ---
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
			// "houseLoan," .
			// "purpose," .
			// "outstandingAmt," .
			// "Nisbahdsr," .
			// "GajiKasar," .
			"sahMohon," .
			"statusL," .
			"applyDate)" .
			" VALUES (" .
			tosql($loanNo, "Text") . "," .
			tosql($loanType, "Number") . "," .
			tosql($kadar_u, "Text") . "," .
			tosql($loanAmt, "Number") . "," .
			tosql($loanPeriod, "Number") . "," .
			tosql($pokok, "Number") . "," .
			tosql($untung, "Number") . "," .
			tosql($pokokAkhir, "Number") . "," .
			tosql($untungAkhir, "Number") . "," .
			tosql($userID, "Text") . "," .
			tosql($monthlyPymt, "Number") . "," .
			// tosql($houseLoan, "Number") . "," .
			// tosql($purpose, "Text") . "," .
			// tosql($totalLoan, "Text") . "," .
			// tosql($Nisbahdsr, "Text") . "," .
			// tosql($totalA, "Number") . "," .
			tosql(1, "Number") . "," .
			tosql(1, "Number") . "," .
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
		// $loanGuarantor  = dlookup("general", "c_gurrantor", "ID=" . tosql($loanType, "Number"));
		// if ($loanGuarantor == 1) {
		// 	print '<script>';
		// 	if (get_session("Cookie_groupID") == 0) {
		// 		print '	alert ("Permohonan pembiayaan telah didaftarkan ke dalam sistem. Pembiayaan yang dipohon memerlukan penjamin. Sila isikan borang penjamin.");';
		// 	}
		// 	print 	'window.location.href = "' . $sActionFileName . '";</script>';
		// } else {
			print '<script>
					alert ("Permohonan pembiayaan telah didaftarkan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
		// }
	}
}

//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
<input type="hidden" name="kadar_u" value="' . $loanCaj . '">
<input type="hidden" name="loanType" value="' . $loanType . '">
<input type="hidden" name="pokok" value="' . $pokok . '">
<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">
<input type="hidden" name="untung" value="' . $interestPay . '">
<input type="hidden" name="pokokAkhir" value="' . $pokokAkhir . '">
<input type="hidden" name="untungAkhir" value="' . $untungAkhir . '">
<input type="hidden" name="totalLoan" value="' . $totalLoan . '">
<input type="hidden" name="unitOnHand" value="' . $unitOnHand . '">
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="Data">
<div style="display: flex;"><h5 class="card-title" style="flex-grow: 1;"><i class="mdi mdi-application"></i>&nbsp;' . strtoupper($title) . '</h5>
<input type="button" align="right" value="Set Semula" class="btn btn-primary btn-sm" onClick="window.location.href=\'?vw=AdvanMohon&mn='.$mn.'\';"/></div>';

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
		print '&nbsp;<label><input type="text" name="userName" class="form-control" value="' . $userName . '" onfocus="this.blur()" size="50"></label>';
	}
	if ($i == 3) {
		if ($Kira == "") {
			print '
			<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="document.MyForm.loanCode.value=\'\'; var userid = document.MyForm.userID.value; window.open(\'AdvanPilihPayment.php?userID=\'+userid,\'sel\',\'top=10,left=10,width=1300,height=700,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
			<input type="text" name="loanName" class="form-control" value="' . $loanName . '" onfocus="this.blur()" size="50">';
		} else {
			print '&nbsp;<input type="text" name="loanName" class="form-control" value="'.$loanName.'" onfocus="this.blur()" size="80">';
		}
	}

	
	if ($i == 6) {
		if ($Kira <> "") {
		$pokUnt = $loanAmt + ($loanAmt * ($loanCaj / 100));
		print '<input type="text" class="form-controlx" value="'.number_format($pokUnt, 2).'" onfocus="this.blur()" size="7"/>';
		}
	}

	if ($i == 7) {
		if ($Kira <> "") {
			print '		
			<form method="POST" action="">
    			<input type="hidden" name="SubmitForm" value="1">		
					<div class="row">
						<!-- Card 1 -->
						<div class="col-md-4">
							<div class="card bg-soft-primary" style="position: relative; padding-top: 20px;">
								<div class="radio-btn" style="position: absolute; top:10px; left:10px;">
									<input type="radio" name="radio" value="3">
								</div>
								<div class="card-body">
									<h5 class="card-title">RM' . number_format($monthlyPymt1, 2) . '</h5>
									<p class="card-text text-danger">x 3 Bulan</p>
								</div>
							</div>
						</div>
						<!-- Card 2 -->
						<div class="col-md-4">
							<div class="card bg-soft-primary" style="position: relative; padding-top: 20px;">
								<div class="radio-btn" style="position: absolute; top:10px; left:10px;">
									<input type="radio" name="radio" value="6">
								</div>
								<div class="card-body">
									<h5 class="card-title">RM' . number_format($monthlyPymt2, 2) . '</h5>
									<p class="card-text text-danger">x 6 Bulan</p>
								</div>
							</div>
						</div>
						<!-- Card 3 -->
						<!--div class="col-md-4">
							<div class="card bg-soft-primary" style="position: relative; padding-top: 20px;">
								<div class="radio-btn" style="position: absolute; top:10px; left:10px;">
									<input type="radio" name="radio" value="12">
								</div>
								<div class="card-body">
									<h5 class="card-title">RM' . number_format($monthlyPymt3, 2) . '</h5>
									<p class="card-text text-danger">x 12 Bulan</p>
								</div>
							</div>
						</div-->
					</div>
					</form>';

					// print '
					// <tr><td class="DataB" align="right" valign="top">Proses Permohonan&nbsp;</td>
					// 	<td><input type="Submit" name="SubmitForm" class="btn btn-primary" value="Mohon Pembiayaan">&nbsp;
					// 	</td>
					// </tr>';
		}
	}

	if ($i == 4) {
		print '<input type="text" name="loanCaj" class="form-controlx" value="' . $loanCaj . '" onfocus="this.blur()" size="6">';
	}

	// if ($i == 8) {
	// 	print '

	// 	<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped form-control-plaintext" style="font-size: 10pt;">
	// 	<tr><td class="Data">Jadual Bayar Balik Pembiayaan</td><td class="Label"> &nbsp;';

	// 	if ($codegroup <> 1638) {
	// 		print '<input type=button value="Jadual" class="btn btn-info btn-sm" onClick=window.open("AdvanApplyJadual.php?loanAmt=' . $loanAmt . '&loanPeriod=' . $loanPeriod . '&loanCaj=' . $loanCaj . '","pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
	// 	} else {
	// 		print '<input type=button value="Jadual" class="btn btn-info btn-sm" onClick=window.open("AdvanJadual78_Apply.php?type=vehicle&page=view&id=' . $userID . '&loanAmt=' . $loanAmt . '&loanPeriod=' . $loanPeriod . '&loanCaj=' . $loanCaj . '","pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
	// 	}

	// 	print '</td></tr>
	// 	<tr><td class="Data">Jumlah Pembiayaan ( Termasuk Untung )</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($totalLoan, 2, '.', ',') . '</b></td></tr>
	// 	<tr><td class="Data">Bayaran Pokok</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($monthlyPay, 2, '.', ',') . '</b></td></tr>
	// 	<tr><td class="Data">Bayaran Pokok Terakhir</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($lastmonthlyPay, 2, '.', ',') . '</b></td></tr>
	// 	<tr><td class="Data">Untung Bulanan</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($interestPay, 2, '.', ',') . '</b></td></tr>
	// 	<tr><td class="Data">Untung Bulanan Terakhir</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($lastinterestPay, 2, '.', ',') . '</b></td></tr>
	// 	</table></div>';
	// }

	//--- End   : Call function FormEntry ---------------------------------------------------------
	print '</td></tr>';
}

if ($Kira <> "") {
	print ' <tr><td colspan=2>             
				<div class="row "><center>                                                                                   
					<textarea class="form-control" cols="" rows="7" wrap="hard" name="syarat" readonly>Dengan ini saya bersetuju bahawa segala maklumat yang diberikan adalah benar. Saya juga mengesahkan bahawa saya telah membuat permohonan advance payment sebanyak RM '.number_format($loanAmt, 2).'.</textarea>
				</center></div>
				<div class="row m-3"><center>                                                                                   
					<input type="checkbox" class="form-check-input" name="pk[]" id="pk[]" onchange="toggleSubmitButton();">&nbsp;Saya bersetuju dengan TERMA & SYARAT</a>
				</center></div>
				<div class="row m-2 mb-4"><center>																			
					<div class="col-md-3"><input type="Submit" class="btn btn-primary w-md waves-effect waves-light" id="submit" name="SubmitForm"  value="Mohon Pembiayaan" size="50" onClick="ITRActionButtonClickStatus(\'submit\');" disabled>																			
					</div>
				</center></div>                                    							
	</td></tr>';
}
/*
if ($Kira <> "") {
	//gaji pokok
	$pic = dlookup("userloandetails", "gaji_img", "userID=" . tosql($userID, "Text"));
	$picjwtn = dlookup("userloandetails", "jwtn_img", "userID=" . tosql($userID, "Text"));
	$picic = dlookup("userloandetails", "ic_img", "userID=" . tosql($userID, "Text"));
	$picccris = dlookup("userloandetails", "ccris_img", "userID=" . tosql($userID, "Text"));
	$YuranBlnan = dlookup("userdetails", "monthFee", "userID=" . tosql($userID, "Text"));

	$sqlGet = "select SUM(amt) as amt from userstates where userID = '" . $userID . "' and payType = 'A'";
	$GettotA =  &$conn->Execute($sqlGet);
	$totalA = $GettotA->fields(amt);
	$netPay = $totalA;

	if (!$totalA) {
		$layakSDesc = 'Pastikan maklumat pendapatan dan perbelanjaan anggota dilengkapkan !<br><br>';
		$layakS = "N";
		$Nisbahdsr = 100;
	} else {

		//potongan gaji
		$sqlGetB = "select sum(amt) as amt from userstates where userID = '" . $userID . "' and payType = 'B'";
		$GettotB =  &$conn->Execute($sqlGetB);
		$totalB = $GettotB->fields(amt) + $YuranBlnan;

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

		$JumPotBaru = $monthlyPymt + $totalB; // T5
		$JumPotxSi = $JumPotBaru - ($totalBKWSPSOC + $CCRIS);	// AK
		$JumBsihxSi = $netPay - ($totalBKWSPSOC + $CCRIS); //AM
		$Nisbahdsr = (number_format(($JumPotxSi / $JumBsihxSi) * 100)); // AK/AM*100

		if ($houseLoan == 1) {
			$NetPay75 = $totalA  * 75 / 100;
			$LayakPay75 = $NetPay75 - ($totalB);
		} else {

			$NetPay50 = $totalA * 50 / 100;
			$LayakPay50 = $NetPay50 - ($totalB);
		}
		$totalFee = getTotFees($userID, date("Y"));
		//	$total = $totalFee +  $totalA15;

		//print 't '.$total.'l '.$loanAmt;
		//echo "pendapatan $totalA : pendapatan lima belas $totalA15 : jumlah yuran $totalFee : jumlah syer $totalShare";
		//echo "<br> jumlah kelayakan $total : jumlah pinjam $loanAmt";

	}
	if (($loanAmt > $loanAmtMax) or ($loanPeriod > $loanPeriodMax)) {
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
		} //else{


		//if(!$pic){
		//$layakSDesc = 'Pastikan Penyata Gaji, IC dan Penetapan Jawatan DiMuatNaik Dahulu !<br><br>';
		//$layakS="N";
		//}
		else {

			$layakSDesc = 'Layak Memohon (Dalam Proses) dan DSR ' . number_format($Nisbahdsr, 2) . '% ' . $layakCCRIS . '';
			$layakS = "Y";
			print '<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">';
		}

		//}

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
	//if ($layakK_amt > 0) 	$layakS = "Y";
	/*
	if ($layakS == 'N') {
		$layakKDesc = 'Tidak Layak Memohon';

		if ($houseLoan == 1) {
			$layakKDesc .= '<br>- Jumlah pendapatan pemohon X 75% - potongan bulanan mesti melebihi dari 0 atau sama dengan bayaran bulanan -<br><br>';
		} else {
			$layakKDesc .= '<br>- Jumlah pendapatan pemohon X 50% - potongan bulanan mesti melebihi dari 0 atau sama dengan bayaran bulanan -<br><br>';
		}
	} else {
		$layakKDesc = 'Layak Memohon';
	}

	// ------------------------------- end qualificaton

	print '
		<!--tr><td class="DataB" align="right" valign="top">ANGKASA&nbsp;</td><td class="Label" valign="top"><font class="redText">' . $layakADesc . '</font></td></tr-->
		<tr><td class="DataB" align="right" valign="top">Semakan Koperasi&nbsp;</td><td class="Label" valign="top"><font class="redText">' . $layakSDesc . '</font></td></tr>';
	if ($layakS == 'Y') {
		
	}
}*/

print '
</table>
</form>';

include("footer.php");
print '
<script language="JavaScript">
	var allChecked=false;

	 function toggleSubmitButton() {
        var submitButton = document.getElementById("submit");
        var checkbox = document.getElementById("pk[]");
        submitButton.disabled = !checkbox.checked;
    }

	function ITRActionButtonClickStatus(v) {
		var strStatus="";
		e = document.MyForm;
		if(e==null) {
		alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
		count=0;
		j=0;
		for(c=0; c<e.elements.length; c++) {
			if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
			pk = e.elements[c].value;
			//strStatus = strStatus + ":" + pk;
			count++;
			}
		}
		
		if(count==0) {
			//alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
		} else {
			//if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
			//e.submit();
			//window.location.href ="memberApply.php?pk=" + strStatus;
			window.location.href ="?vw=AdvanInProses&mn=12";
			//}
		}
		}
	}

</script>';
?>