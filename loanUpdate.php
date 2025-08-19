<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanApply.php
 *          Date update	: 	19/7/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("koperasiList.php");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}
if (get_session("Cookie_groupID") == 0) {
	$userID		= get_session('Cookie_userID');
	$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	//$oldIC		= dlookup("userdetails", "oldIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

$sFileName		= "loanUpdate.php";
if (get_session("Cookie_groupID") == 0) {
	$sActionFileName = "biayaMember.php";
} else {
	$sActionFileName = "loan.php";
}
$title     		= "Perbarui Pembiayaan";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = 1;
$FormLabel[$a]   	= "* Nomor Anggota";
$FormElement[$a] 	= "memberID";
if (get_session("Cookie_groupID") == 0) {
	$FormType[$a]	  	= "hiddentext";
} else {
	$FormType[$a]	  	= "text";
}
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "12";

$a = $a + 1;
$FormLabel[$a]   	= "* No KTP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

/*
$a = $a + 1;
$FormLabel[$a]   	= "No KTP Lama";
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
	$FormType[$a]	  	= "hiddentext";
} else {
	$FormType[$a]	  	= "text";
}
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "20";

$a = $a + 1;
$FormLabel[$a]   	= "* Kartuar Keuntungan(%)";
$FormElement[$a] 	= "loanCajtexr";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "4";

$a = $a + 1;
$FormLabel[$a]   	= "* Jangka Waktu Pembayaran";
$FormElement[$a] 	= "loanPeriod";
//$FormType[$a]	  	= "hiddentext";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "3";

$a = $a + 1;
$FormLabel[$a]   	= "* Jumlah Pembiayaan";
$FormElement[$a] 	= "loanAmt";
//$FormType[$a]	  	= "hiddentext";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a = $a + 1;
$FormLabel[$a]   	= "Informasi Perkiraan Pembiayaan";
$FormElement[$a] 	= "Kira";
$FormType[$a]	  	= "submit";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a = $a + 1;
$FormLabel[$a]   	= "* Bayar Balik (Bulan)";
$FormElement[$a] 	= "monthlyPymt";
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
$FormData[$a]   	= array('Tak Ada', 'Ada');
$FormDataValue[$a]	= array('0', '1');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Tujuan Pembiayaan";
$FormElement[$a] 	= "purpose";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "100";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

if ($Kira <> "") {
	//--- BEGIN : Check loan ID ---
	if ($loanCode <> "" && $loanPeriod <> "" && $loanAmt <> "") {
		if (dlookup("general", "category", "code=" . tosql($loanCode, "Text")) == "C") {
			$loanType = dlookup("general", "ID", "code=" . tosql($loanCode, "Text"));
			$loanName = dlookup("general", "name", "ID=" . tosql($loanType, "Number"));
			$loanCaj  = dlookup("general", "c_Caj", "ID=" . tosql($loanType, "Number"));
			$loanPeriodMax  = dlookup("general", "c_Period", "ID=" . tosql($loanType, "Number"));
			$loanAmtMax  = dlookup("general", "c_Maksimum", "ID=" . tosql($loanType, "Number"));

			$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
			$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
			if ($loanAmt <> "") {
				$monthlyPay		= number_format($loanAmt / $loanPeriod, 2, '.', '');
				$chkCent = substr($monthlyPay, -2, 2);
				$monthlyPay = (int)$monthlyPay;
				if ($chkCent < 50) {
					$monthlyPay = number_format($monthlyPay + 0.5, 2, '.', '');
				} elseif ($chkCent >= 50) {
					$monthlyPay = number_format($monthlyPay + 1, 2, '.', '');
				}
			}
			$lastmonthlyPay	= number_format($loanAmt - ($monthlyPay * ($loanPeriod - 1)), 2, '.', '');

			if ($totalInterest <> "" and $loanPeriod <> "") {
				$interestPay	= number_format($totalInterest / $loanPeriod, 2, '.', '');
				$chkCent = substr($interestPay, -2, 2);
				$interestPay = (int)$interestPay;
				if ($chkCent < 50) {
					$interestPay = number_format($interestPay + 0.5, 2, '.', '');
				} elseif ($chkCent >= 50) {
					$interestPay = number_format($interestPay + 1, 2, '.', '');
				}
			}
			$lastinterestPay = number_format($totalInterest - ($interestPay * ($loanPeriod - 1)), 2, '.', '');
			$monthlyPymt	= $monthlyPay + $interestPay;
		} else {
			$Kira = "";
			print '<script>alert("Kode pembiayaan : ' . $loanCode . ' tidak Ada...!");</script>';
		}
	} else {
		$Kira = "";
		print '<script>alert("Silakan masukan semua detail pembiayaan");</script>';
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
				print '<script>alert("Nomor Anggota : ' . $memberID . ' tidak Ada/Sah...!");</script>';
			}
		} else {
			$Kira = "";
			print '<script>alert("Silakan masukkan no anggota");</script>';
		}
	}
	//--- END   : Check member ID ---
}

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
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
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";
		$sWhere = "loanID=" . tosql($loanID, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";

		$sSQL	= "UPDATE loans SET " .
			"loanType =" . tosql($loanType, "Text") .
			", kadar_u =" . tosql($kadar_u, "Text") .
			", loanAmt =" . tosql($loanAmt, "Text") .
			", loanPeriod =" . tosql($loanPeriod, "Text") .
			", pokok =" . tosql($pokok, "Text") .
			", untung =" . tosql($untung, "Text") .
			", pokokAkhir =" . tosql($pokokAkhir, "Text") .
			", untungAkhir =" . tosql($untungAkhir, "Text") .
			", userID =" . tosql($userID, "Text") .
			", monthlyPymt =" . tosql($monthlyPymt, "Text") .
			", houseLoan =" . tosql($houseLoan, "Text") .
			", purpose =" . tosql($purpose, "Text") .
			", outstandingAmt =" . tosql($totalLoan, "Text") .
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");
		$sSQL = $sSQL . $sWhere;
		//print $sSQL;
		$rs = &$conn->Execute($sSQL);


		print '<script>
					alert ("Pembiayaan telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="loanID" value="' . $loanID . '">
<input type="hidden" name="userID" value="' . $userID . '">
<input type="hidden" name="kadar_u" value="' . $loanCaj . '">
<input type="hidden" name="loanType" value="' . $loanType . '">
<input type="hidden" name="pokok" value="' . $monthlyPay . '">
<input type="hidden" name="untung" value="' . $interestPay . '">
<input type="hidden" name="pokokAkhir" value="' . $lastmonthlyPay . '">
<input type="hidden" name="untungAkhir" value="' . $lastinterestPay . '">
<input type="hidden" name="totalLoan" value="' . $totalLoan . '">
<input type="hidden" name="unitOnHand" value="' . $unitOnHand . '">
<table border=0 cellpadding=3 cellspacing=0 width=95% align="center" class="Data">
	<tr>
		<td colspan="2" class="Data"><b class="maroonText">' . strtoupper($title) . '</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<tr><td class=Header colspan=2>Maklumat Anggota :</td></tr>';
	if ($i == 3) {
		print '<tr><td class=Header colspan=2>Pra Kelayakan Permohonan Pembiayaan :</td></tr>';
		print '<tr><td class=Label colspan=2><u>1% dari jumlah pembiayaan dikenakan sebagai yuran</u></td></tr>';
	}
	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . ' :</td>';
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
		if (get_session("Cookie_groupID") == 1 or get_session("Cookie_groupID") == 2) {
			print '
			<input type="button" class="label" value="..." onclick="document.MyForm.monthlyPymt.value=\'\';window.open(\'selLoanList.php\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
		}
		print '	<input type="text" name="userName" class="Data" value="' . $userName . '" onfocus="this.blur()" size="50">';
	}
	if ($i == 3) {
		print '
		<input type="button" class="label" value="..." onclick="document.MyForm.monthlyPymt.value=\'\'; var userid = document.MyForm.userID.value; window.open(\'selLoan.php?userID=\'+userid,\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		<input type="text" name="loanName" class="Data" value="' . $loanName . '" onfocus="this.blur()" size="50">';
	}

	if ($i == 4) {
		print '<input type="text" name="loanCaj" class="Data" value="' . $loanCaj . '" onfocus="this.blur()" size="6">';
	}

	if ($i == 7) {
		print '
		<table border="0" cellpadding="3" cellspacing="0"> 
		<tr><td class="Data">Jumlah Pembiayaan ( termasuk faedah )</td><td class="Label">:&nbsp;' . $totalLoan . '</td></tr>
		<tr><td class="Data">Bayaran Bulanan</td><td class="Label">:&nbsp;' . $monthlyPay . '</td></tr>
		<tr><td class="Data">Bayaran Bulanan Terakhir</td><td class="Label">:&nbsp;' . $lastmonthlyPay . '</td></tr>
		<tr><td class="Data">Faedah Bulanan</td><td class="Label">:&nbsp;' . $interestPay . '</td></tr>
		<tr><td class="Data">Faedah Bulanan Terakhir</td><td class="Label">:&nbsp;' . $lastinterestPay . '</td></tr>
		</table>';
	}
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td></tr>';
}

if ($Kira <> "") {
	$grossPay 	= dlookup("userdetails", "grossPay", "userID=" . tosql($userID, "Text"));
	//$netPay		= dlookup("userdetails", "netPay", "userID=" . tosql($userID, "Text"));
	$netPay		= $grossPay;

	// --- qualification ---
	$layakA = "N";
	if ($monthlyPymt <> "" and $netPay <> '0.00') $layakA_percent = (($netPay - $monthlyPymt) / $netPay) * 100;
	if ($layakA_percent > 40) 	$layakA = "Y";
	if ($layakA == 'N')
		$layakADesc = 'Tidak Layak Memohon<br>- Jumlah ((pendapatan sebulan - potongan bulanan )/pendapatan pemohon) X 100 adalah kurang daripada 40% -<br><br>';
	else
		$layakADesc = 'Layak Memohon';
	// -------------------------------

	// --- qualification ---
	$layakK = "N";
	if ($houseLoan == 1) {
		$layakK_amt = ($netPay * (75 / 100)) - $monthlyPymt;
	} else {
		$layakK_amt = ($netPay * (50 / 100)) - $monthlyPymt;
	}
	if ($layakK_amt > 0) 	$layakK = "Y";
	if ($layakK == 'N') {
		$layakKDesc = 'Tidak Layak Memohon';
		if ($houseLoan == 1) {
			$layakKDesc .= '<br>- Jumlah pendapatan pemohon X 75% - potongan bulanan mesti melebihi dari 0 atau sama dengan bayaran bulanan -<br><br>';
		} else {
			$layakKDesc .= '<br>- Jumlah pendapatan pemohon X 50% - potongan bulanan mesti melebihi dari 0 atau sama dengan bayaran bulanan -<br><br>';
		}
	} else {
		$layakKDesc = 'Layak Memohon';
	}
	// -------------------------------
	print '
		<tr><td colspan=2 align=center class=Data>
			<input type=Reset name=ResetForm class="but" value=Clear Form>
			<input type=Submit name=SubmitForm class="but" value=Simpan>
			</td>
		</tr>';
}

print '
</table>
</form>';

include("footer.php");
