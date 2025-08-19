<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanApply.php
 *          Date 		: 	1/6/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

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

$sFileName		= "?vw=loanApply&mn=$mn";
$sActionFileName = "?vw=loan&mn=$mn";
if (get_session("Cookie_groupID") == 0) {
	//$sqlC = "select * from loans where userID =".$userID." and isApproved <> 1";
	//$rsC = &$conn->Execute($sqlC);
	//if($rsC->RowCount() == 0) 
	$sActionFileName = "?vw=loanInProcess&mn=$mn";
}
$title     		= "Pengajuan Pembiayaan";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = 1;
$FormLabel[$a]   	= "* Nomor Anggota";
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
$FormLabel[$a]   	= "* Kartu Identitas";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "12";
$FormLength[$a]  	= "12";

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
if ($Kira <> "") {
	$FormType[$a]	  	= "select";
} else {
	$FormType[$a]	  	= "select";
}
$FormData[$a]   	= array('1 BULAN', '2 BULAN', '3 BULAN', '4 BULAN', '5 BULAN', '6 BULAN', '7 BULAN', '8 BULAN', '9 BULAN', '10 BULAN', '11 BULAN', '12 BULAN / 1 TAHUN', '24 BULAN / 2 TAHUN', '36 BULAN / 3 TAHUN', '48 BULAN / 4 TAHUN', '60 BULAN / 5 TAHUN', '72 BULAN / 6 TAHUN', '84 BULAN / 7 TAHUN', '96 BULAN / 8 TAHUN', '108 BULAN / 9 TAHUN', '120 BULAN / 10 TAHUN');
$FormDataValue[$a]	= array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '24', '36', '48', '60', '72', '84', '96', '108', '120');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "3";

$a = $a + 1;
$FormLabel[$a]   	= "* Jumlah Pembiayaan (RM)";
$FormElement[$a] 	= "loanAmt";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a = $a + 1;
$FormLabel[$a]   	= "* Ada Pembiayaan Rumah?<br/> (Kiraan DSR)";
$FormElement[$a] 	= "houseLoan";
$FormType[$a]	  	= "select";
$FormData[$a]   	= array('TIADA', 'ADA');
$FormDataValue[$a]	= array('0', '1');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

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
$FormLabel[$a]   	= "* Ansuran Bulanan (RM)";
$FormElement[$a] 	= "monthlyPymt";
$FormType[$a]	  	= "displayonly";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "13";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "* Tujuan Pembiayaan";
$FormElement[$a] 	= "purpose";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "40";
$FormLength[$a]  	= "7";

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
			$codegroup	= dlookup("general", "parentID", "ID=" . tosql($loanType, "Number"));
			$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
			$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
			list($monthlyPay, $lastmonthlyPay, $interestPay, $lastinterestPay) = countPays($totalLoan, $totalInterest, $loanAmt, $loanCaj, $loanPeriod);

			$monthlyPymt	= $monthlyPay + $interestPay;
		} else {
			$Kira = "";
			print '<script>alert("Kode pembiayaan : ' . $loanCode . ' tidak Ada...!");</script>';
		}
	} else {
		$Kira = "";
		//print '<script>alert("Silakan masukan semua detail pembiayaan");</script>';
		alert("Silakan masukan semua detail pembiayaan");
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

if ($SubmitForm <> "") {
	$pass = 1;
	$sSQL = "SELECT	* FROM loans 
				 WHERE status <> 3 AND status <> 4 AND status <> 5 AND status <> 7 AND status <> 9 AND userID = '" . $userID . "'
				 ORDER BY applyDate ASC";


	$GetLoan = &$conn->Execute($sSQL);
	if ($GetLoan->RowCount() <> 0) {
		print '<script>
					alert ("Terdapat permohonan pembiayaan yang belum selesai diproses untuk anggota ini!");
					//window.location.href = "loanView.php";
					window.location.href = "?vw=loan&mn=' . $mn . '";
				</script>';
		$pass = 0;
	}
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
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
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
			tosql($loanAmt, "Number") . "," .
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

		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			" VALUES ('Permohonan Pembiayaan - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '2')";
		$rs = &$conn->Execute($sqlAct);
		//####################################
		//place to check and prompt user if guarantor is required
		$loanGuarantor  = dlookup("general", "c_gurrantor", "ID=" . tosql($loanType, "Number"));
		if ($loanGuarantor == 1) {
			print '<script>';
			if (get_session("Cookie_groupID") == 0) {
				print '	alert ("Permohonan pembiayaan telah didaftarkan ke dalam sistem. Pembiayaan yang diajukan memerlukan penjamin. Silakan isi formulir penjamin.");';
				print 'window.location.href = "?vw=biayaMember&mn=5";</script>';
			}
			print '	alert ("Permohonan pembiayaan telah didaftarkan ke dalam sistem. Pembiayaan yang diajukan memerlukan penjamin.");';
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
<input type="button" align="right" value="Set Semula" class="btn btn-primary btn-sm" onClick="window.location.href=\'?vw=loanApply&mn=' . $mn . '\';"/>
</div><br/>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<div class="card-header">INFORMASI ANGGOTA</div>';
	if ($i == 3) {
		print '<tr><td colspan=2><div class="card-header">PRA KELAYAKAN PENGAJUAN PEMBIAYAAN</div></td></tr>';
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
		if (get_session("Cookie_groupID") == 1 or get_session("Cookie_groupID") == 2) {
			print '
			<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="document.MyForm.monthlyPymt.value=\'\';window.open(\'selMember.php\',\'sel\',\'top=10,left=10,width=1300,height=700,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
		}
		print '&nbsp;<label><input type="text" name="userName" class="form-control" value="' . $userName . '" onfocus="this.blur()" size="50"></label>';
	}
	if ($i == 3) {
		print '
		<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="document.MyForm.monthlyPymt.value=\'\'; var userid = document.MyForm.userID.value; window.open(\'selLoan.php?userID=\'+userid,\'sel\',\'top=10,left=10,width=1300,height=700,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		<input type="text" name="loanName" class="form-control" value="' . $loanName . '" onfocus="this.blur()" size="50">';
	}

	if ($i == 4) {
		print '<label><input type="text" name="loanCaj" class="form-control" value="' . $loanCaj . '" onfocus="this.blur()" size="6"></label>';
	}

	if ($i == 8) {
		print '
		
		<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped form-control-plaintext" style="font-size: 10pt;">
		<tr><td class="Data">Jadual Bayar Balik Pembiayaan</td><td class="Label"> &nbsp;';

		if ($codegroup <> 1638) {
			print '<input type=button value="Jadual" class="btn btn-info btn-sm" onClick=window.open("loanJadualApply.php?loanAmt=' . $loanAmt . '&loanPeriod=' . $loanPeriod . '&loanCaj=' . $loanCaj . '","pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
		} else {
			print '<input type=button value="Jadual" class="btn btn-info btn-sm" onClick=window.open("loanJadual78_Apply.php?type=vehicle&page=view&id=' . $userID . '&loanAmt=' . $loanAmt . '&loanPeriod=' . $loanPeriod . '&loanCaj=' . $loanCaj . '","pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
		}

		print '</td></tr>
		<tr><td class="Data">Jumlah Pembiayaan ( Termasuk Untung )</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($totalLoan, 2, '.', ',') . '</b></td></tr>
		<tr><td class="Data">Bayaran Pokok</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($monthlyPay, 2, '.', ',') . '</b></td></tr>
		<tr><td class="Data">Bayaran Pokok Terakhir</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($lastmonthlyPay, 2, '.', ',') . '</b></td></tr>
		<tr><td class="Data">Untung Bulanan</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($interestPay, 2, '.', ',') . '</b></td></tr>
		<tr><td class="Data">Untung Bulanan Terakhir</td><td class="Label"> &nbsp;<b>RM&nbsp;' . number_format($lastinterestPay, 2, '.', ',') . '</b></td></tr>
		</table></div>';
	}


	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td></tr>';
}

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
		$layakSDesc = 'Pastikan informasi pendapatan dan pengeluaran anggota dilengkapi !<br><br>';
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
		$layakSDesc = 'Pastikan JUMLAH PERMOHONAN (RM) / JANGKA WAKTU PEMBAYARAN (BULAN) tidak melebihi JUMLAH PEMBIAYAAN (RM) / JANGKA WAKTU PEMBIAYAAN (BULAN) yang telah ditetapkan.<br><br>';
		$layakS = "N";
	}

	if ($houseLoan == 1) { // 75%

		if ($loanAmt >= 100000) {
			$layakCCRIS = 'SILAKAN AJUKAN LAPORAN CCRIS DARI BANK INDONESIA UNTUK MEMUDAHKAN PROSES PERMOHONAN';
		}


		if ($monthlyPymt > $LayakPay75) {
			$layakSDesc = 'Tidak Layak Mengajukan Permohonan. Rasio DSR melebihi 75%. / (SILAKAN PERBARUI INFORMASI PEMBIAYAAN ATAU GAJI TERBARU) Jumlah Maksimum Pembayaran Bulanan yang diizinkan RM' . number_format($LayakPay75, 2) . ' dan DSR ' . number_format($Nisbahdsr, 2) . '%<br><br>';
			$layakS = "N";
			print '<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">';
		} //else{ 		


		//if(!$pic){
		//$layakSDesc = 'Pastikan Penyata Gaji, IC dan Penetapan Jawatan DiMuatNaik Dahulu !<br><br>'; 
		//$layakS="N";
		//}
		else {

			$layakSDesc = 'Layak Mengajukan Permohonan (Dalam Proses) dan DSR' . number_format($Nisbahdsr, 2) . '% ' . $layakCCRIS . '';
			$layakS = "Y";
			print '<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">';
		}

		//}

	} else {

		if ($monthlyPymt > $LayakPay50) {
			$layakSDesc = 'Tidak Layak Mengajukan Permohonan. Rasio DSR melebihi 50%. / (SILAKAN PERBARUI INFORMASI PEMBIAYAAN ATAU GAJI TERBARU) Jumlah Maksimum Pembayaran Bulanan yang diizinkan RM' . $LayakPay50 . ' dan DSR ' . number_format($Nisbahdsr, 2) . ' %<br><br>';
			$layakS = "N";
			print '<input type="hidden" name="Nisbahdsr" value="' . $Nisbahdsr . '">';
		} else {
			$layakSDesc = 'Layak Mengajukan (Dalam Proses)' . $layakCCRIS . ' dan DSR ' . number_format($Nisbahdsr, 2) . '%';
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
*/
	// ------------------------------- end qualificaton

	print '
		<!--tr><td class="DataB" align="right" valign="top">ANGKASA&nbsp;</td><td class="Label" valign="top"><font class="redText">' . $layakADesc . '</font></td></tr-->
		<tr><td class="DataB" align="right" valign="top">Semakan Koperasi&nbsp;</td><td class="Label" valign="top"><font class="redText">' . $layakSDesc . '</font></td></tr>';
	if ($layakS == 'Y') {
		print '
		<tr><td class="DataB" align="right" valign="top">Proses Pengajuan&nbsp;</td>
			<td><input type="Submit" name="SubmitForm" class="btn btn-primary" value="Mohon Pembiayaan">&nbsp;
			</td>
		</tr>';
	}
}

print '
</table>
</form>';

include("footer.php");

function countLoan($loanCode, $loanPeriod, $loanAmt, $loanCaj)
{
	//get loan code, period and amount
	//calculate montly pays, last monthly pay
	// , monthly interest and last monthly interest

	//from loan code
	$loanType = dlookup("general", "ID", "code=" . tosql($loanCode, "Text"));
	//get loan name, caj and max period and amount
	$loanName = dlookup("general", "name", "ID=" . tosql($loanType, "Number"));
	$loanCaj  = dlookup("general", "c_Caj", "ID=" . tosql($loanType, "Number"));
	$loanPeriodMax  = dlookup("general", "c_Period", "ID=" . tosql($loanType, "Number"));
	$loanAmtMax  = dlookup("general", "c_Maksimum", "ID=" . tosql($loanType, "Number"));

	//from loan amount, caj and period get total interest and total loan
	$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
	$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
	if ($loanAmt <> "") {
		$monthlyPay		= number_format($loanAmt / $loanPeriod, 2, '.', '');
		$chkCent = substr($monthlyPay, -2, 2);
		$monthlyPay1 = $chkCent;
	}
	$lastmonthlyPay	= number_format($loanAmt - ($monthlyPay * ($loanPeriod - 1)), 2, '.', '');

	if ($loanCaj <> 0) { //zero interest for 0 caj
		if ($totalInterest and $loanPeriod <> "") {
			$interestPay	= number_format($totalInterest / $loanPeriod, 2, '.', '');
			$chkCent = substr($interestPay, -2, 2);
			$interestPay = (int)$interestPay;
			if ($chkCent > 0 && $chkCent < 50) {
				$interestPay = number_format($interestPay + 0.5, 2, '.', '');
			} elseif ($chkCent >= 50) {
				$interestPay = number_format($interestPay + 1, 2, '.', '');
			}
		}
	} else {
		$interestPay = 0.0;
	}
	$lastinterestPay = number_format($totalInterest - ($interestPay * ($loanPeriod - 1)), 2, '.', '');
	if ($lastinterestPay < 0) $lastinterestPay = 0.0;

	$monthlyPymt	= $monthlyPay + $interestPay;
}
