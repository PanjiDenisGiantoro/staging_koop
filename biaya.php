<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	biaya.php
 *          Date 		: 	12/12/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$sFileName		= "?vw=biayaDokumen&mn=906&pk=" . $pk;
$sActionFileName = "?vw=dokPP&mn=906";
$title     		= "Kemaskini Maklumat Pembiayaan Anggota";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

//--- Prepare panel type
$panelList = array();
$panelVal  = array();
$GetPanel = ctGeneral("", "D");
if ($GetPanel->RowCount() <> 0) {
	while (!$GetPanel->EOF) {
		if ($GetPanel->fields(d_Type) == "I") {
			array_push($panelList, $GetPanel->fields(name));
			array_push($panelVal, $GetPanel->fields(ID));
		}
		$GetPanel->MoveNext();
	}
}

//--- Prepare payment type
$pymtList = array();
$pymtVal  = array();
$GetPymt = ctGeneral("", "K");
if ($GetPymt->RowCount() <> 0) {
	while (!$GetPymt->EOF) {
		array_push($pymtList, $GetPymt->fields(name));
		array_push($pymtVal, $GetPymt->fields(ID));
		$GetPymt->MoveNext();
	}
}

$a = 1;
$FormLabel[$a]   	= "Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No KTP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Alamat";
$FormElement[$a] 	= "add";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Umur";
$FormElement[$a] 	= "umur";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Nomor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Jawatan";
$FormElement[$a] 	= "job";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Tanggal Menjadi Anggota";
$FormElement[$a] 	= "text";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Cabang/Zona";
$FormElement[$a] 	= "dept";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Wajib Bulanan (RP)";
$FormElement[$a] 	= "monthFee";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pegangan Wajib (RP)";
$FormElement[$a] 	= "totalFee";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Nama Suami/Isteri";
$FormElement[$a] 	= "namapsgn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pekerjaan";
$FormElement[$a] 	= "jobpsgn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Majikan";
$FormElement[$a] 	= "majikanpsgn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Bil Tanggungan";
$FormElement[$a] 	= "biltggn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Alamat Majikan";
$FormElement[$a] 	= "add2";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Bil. Sekolah";
$FormElement[$a] 	= "bilsek";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Jumlah Dipohon (RP)";
$FormElement[$a] 	= "loanAmt";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Jangka Waktu Pembayaran";
$FormElement[$a] 	= "loanPeriod";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "Tujuan Pinjaman";
$FormElement[$a] 	= "cc";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";


$a++;
$FormLabel[$a]   	= "Catatan";
$FormElement[$a] 	= "purpose";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$uid = dlookup("loans", "userID", "loanID='" . $pk . "'");

$strMember = "SELECT a. * , b.memberID, b.newIC, b.dateBirth, b.job, b.grossPay, b.address, b.city, b.postcode, b.stateID, b.departmentID, b.approvedDate as startdate, b.monthFee, b.totalFee, (b.grossPay+ b.totalFee) as jumLayak ,c . * , c.gaji + c.elaun + c.gajipsgn + c.elaunpsgn + c.lain_i + c.lain_ii AS jumpdpt, c.saraan + c.pljn + c.p_prmhn + c.p_kereta + c.p_lain + c.epfdll AS jumpblj, d . * FROM users a, userdetails b, userloandetails c, loans d WHERE a.userID = '" . $uid . "' AND a.userID = b.userID AND b.userID = c.userID AND c.userID = d.userID AND d.loanID = '" . $pk . "'";
//print $strMember;
$GetLoan = &$conn->Execute($strMember);

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
	if ($startPymtDate <> "") {
		$startPymtDate = substr($startPymtDate, 6, 4) . '-' . substr($startPymtDate, 3, 2) . '-' . substr($startPymtDate, 0, 2);
	}
	if ($applyDate <> "") {
		$applyDate = substr($applyDate, 6, 4) . '-' . substr($applyDate, 3, 2) . '-' . substr($applyDate, 0, 2);
	}
	if ($approvedDate <> "") {
		$approvedDate = substr($approvedDate, 6, 4) . '-' . substr($approvedDate, 3, 2) . '-' . substr($approvedDate, 0, 2);
	}
	if ($rejectedDate <> "") {
		$rejectedDate = substr($rejectedDate, 6, 4) . '-' . substr($rejectedDate, 3, 2) . '-' . substr($rejectedDate, 0, 2);
	}
	if ($updatedDate <> "") {
		$updatedDate = substr($updatedDate, 6, 4) . '-' . substr($updatedDate, 3, 2) . '-' . substr($updatedDate, 0, 2);
	}
	if (count($strErrMsg) == "0") {
		$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
		$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
		if ($loanAmt <> "") $monthlyPay		= number_format($loanAmt / $loanPeriod, 2, '.', '');
		$lastmonthlyPay	= number_format($loanAmt - ($monthlyPay * ($loanPeriod - 1)), 2, '.', '');
		if ($totalInterest <> "" and $loanPeriod <> "") $interestPay	= number_format($totalInterest / $loanPeriod, 2, '.', '');
		$lastinterestPay = number_format($totalInterest - ($interestPay * ($loanPeriod - 1)), 2, '.', '');
		$monthlyPymt	= $monthlyPay + $interestPay;

		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "loanID=" . tosql($pk, "Number");
		$sSQL	= "UPDATE loans SET " .
			"startPymtDate=" . tosql($startPymtDate, "Text") .
			",loanAmt=" . tosql($loanAmt, "Number") .
			",loanPeriod=" . tosql($loanPeriod, "Number") .
			",monthlyPymt=" . tosql($monthlyPymt, "Number") .
			",outstandingAmt=" . tosql($totalLoan, "Number") .
			",insuranceID=" . tosql($insuranceID, "Number") .
			",insurancePymt=" . tosql($insurancePymt, "Number") .
			",paymentID=" . tosql($paymentID, "Number") .
			",guarantorID=" . tosql($sellUserID, "Text") .
			",purpose=" . tosql($purpose, "Text") .
			",updatedDate=" . tosql($updatedDate, "Text") .
			",updatedBy=" . tosql($updatedBy, "Text");
		//add for new field	
		/*   ",remark=" . tosql($remark, "Text") .
				       ",loanCaj=" . tosql($loanCaj, "Text") .
				       ",proPinjam=" . tosql($proPinjam, "Text") .
				       ",overlap=" . tosql($overlap, "Text") .
					   ",catatan=" . tosql($catatan, "Text") .
					   ",applyDate=" . tosql($applyDate, "Text") .
					   ",approvedDate=" . tosql($approvedDate, "Text") .
					   ",rejectedDate=" . tosql($rejectedDate, "Text") .
					   ",updatedDate=" . tosql($updatedDate, "Text").
					   ",earlyMonth=" . tosql($earlyMonth, "Text"); */
		$sSQL .= " where " . $sWhere;
		//print $sSQL;
		//$rs = &$conn->Execute($sSQL);
		print '<script>
					//alert ("Status permohonan pinjaman telah dikemaskinikan ke dalam sistem.");
					//window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->


print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $GetLoan->fields(userID) . '">
<input type="hidden" name="loanType" value="' . $GetLoan->fields(loanType) . '">
<input type="hidden" name="loanID" value="' . $GetLoan->fields(loanID) . '">
<table border=0 cellpadding=3 cellspacing=0 width=95% align="center" class="lineBG">
<h5 class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
<span>' . strtoupper($title) . ' :  ' . $GetLoan->fields(loanID) . '</span>
<div class="btn btn-primary" style="display: inline-block;">
<i class="mdi mdi-pencil" style="font-size: 15px; cursor: pointer;" onClick="window.location.href=\'?vw=biayaEdit&mn=906&pk=' . $pk . '&userID=' . $uid . '\';"></i>
</div></h5>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;
	if ($i == 1) print '<tr><td colspan=4><div class="card-header">A. BUTIR-BUTIR PERPOHONAN</div></td></tr>';
	if ($i == 17) print '<tr><td colspan=4><div class="card-header">B. BUTIR-BUTIR PEMBIAYAAN</div></td></tr>';
	if ($i == 21) print '<tr><td colspan=4><div class="card-header">C. PENYATA PENDAPATAN/PERBELANJAAN</div></td></tr>';
	if ($i == 39) print '<tr><td colspan=4><div class="card-header">&nbsp;* UNTUK KEGUNAAN PEJABAT</div></td></tr>';
	if ($i == 39) print '<tr><td colspan=4><div class="card-header">D. MAKLUMAT KELAYAKAN PEMOHON</div></td></tr>';
	if ($i == 55) print '<tr><td colspan=4><div class="card-header">E. HAD TANGGUNGAN PENJAMIN</div></td></tr>';
	if ($i == 55) print '<tr><td colspan=4><div class="card-header">i) BUTIR-BUTIR PENJAMIN 1</div></td></tr>';
	if ($i == 73) print '<tr><td colspan=4><div class="card-header">ii) BUTIR-BUTIR PENJAMIN 2</div></td></tr>';
	if ($i == 91) print '<tr><td colspan=4><div class="card-header">iii) BUTIR-BUTIR PENJAMIN 3</div></td></tr>';
	if ($i == 109) print '<tr><td colspan=4><div class="card-header">F. MAKLUMAT KELAYAKAN PEMOHON:</div></td></tr>';
	//if ($i == 109) print '<tr><td class=Header colspan=4>Audit Informasi :</td></tr>';

	if ($cnt == 1) print '<tr valign=top>';
	print '<td class=Data align=right>' . $FormLabel[$i];
	//if (!($i == 1 or $i == 2 or $i == 8 or $i ==30 or $i == 32)) 
	print ':';
	print ' </td>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetLoan->fields($FormElement[$i]));
	if ($strFormValue == '') $strFormValue = $$FormElement[$i];
	if ($i == 1337) {
		$strFormValue = dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '&nbsp;-&nbsp; ' . dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Text"));
	}
	if ($i == 1338) {
		$strFormValue = dlookup("userdetails", "newIC", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '&nbsp;/&nbsp; ' . dlookup("userdetails", "oldIC", "userID=" . tosql($GetLoan->fields(userID), "Text"));
	}

	if ($i == 7) {
		$strFormValue = todate("d/m/y", $GetLoan->fields(startdate));
	}

	if ($i == 10) {	//$totalShares
		$strFormValue = number_format(getFees($GetLoan->fields(userID), date("Y")), 2);
	}

	if ($i == 19) {
		//$strFormValue = dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")).'&nbsp;/&nbsp; '.						dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		$strFormValue = dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
	}
	// if ($i == 18) {
	//$caj = tosql($GetLoan->fields(loanCaj), "Number");
	//if($caj==0){
	//$strFormValue = dlookup("general", "C_Caj", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
	//}
	// }

	if ($i == 42) {
		$sqlGet = "SELECT sum(loanAmt) as totalLoan FROM `loans` where userID = '" . $GetLoan->fields(userID) . "' and isApproved = 1";
		$GettotLoan =  &$conn->Execute($sqlGet);
		$strFormValue = $GettotLoan->fields(totalLoan);
		$totalT = $GettotLoan->fields(totalLoan);
	}

	if ($i == 44) {
		$bList = array();
		$sSQL = "SELECT DISTINCT A.loanID FROM loans A, userdetails B WHERE ( A.userID = B.userID AND ( A.penjaminID1 = '" . $GetLoan->fields(userID) . "' OR A.penjaminID2 = '" . $GetLoan->fields(userID) . "' OR A.penjaminID2 = '" . $GetLoan->fields(userID) . "')) ORDER BY A.applyDate";
		$rs = &$conn->Execute($sSQL);
		$j = $i;
		if ($rs->RowCount() <> 0) {
			while (!$rs->EOF) {

				$loID = $rs->fields(loanID);
				$userID = $GetLoan->fields(userID);
				$memberID = dlookup("userdetails", "memberID", "userID='" . $userID . "'");
				if ($pid = dlookup("loans", "penjaminID1", "loanID='" . $loID . "'")) {
					if ($pid == $memberIDy) $field = 1;
					$bList[$j] = $loID;
				}
				if ($pid = dlookup("loans", "penjaminID2", "loanID='" . $loID . "'")) {
					if ($pid == $memberIDy) $field = 2;
					$bList[$j] = $loID;
				}
				if ($pid = dlookup("loans", "penjaminID3", "loanID='" . $loID . "'")) {
					if ($pid == $memberIDy)	$field = 3;
					$bList[$j] = $loID;
				}

				$j += 2;;
				$rs->MoveNext();
			}
		}
		$amt1 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt1) $strFormValue = "(" . $bList[$i] . ") RP " . $amt1;
	}

	if ($i == 46) {
		$amt2 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt2) $strFormValue = "(" . $bList[$i] . ") RP " . $amt2;
	}

	if ($i == 48) {
		$amt3 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt3) $strFormValue = "(" . $bList[$i] . ") RP " . $amt3;
	}

	if ($i == 50) {
		$amt4 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt4) $strFormValue = "(" . $bList[$i] . ") RP " . $amt4;
	}

	if ($i == 52) {
		$amt5 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt5) $strFormValue = "(" . $bList[$i] . ") RP " . $amt5;
	}

	if ($i == 53) {
		$wages = tosql($GetLoan->fields(grossPay), "Number");
		$strFormValue = 0.5 * $wages . "/" . 0.75 * $wages;
	}

	if ($i == 54) {
		$strFormValue = $totalT + $amt1 + $amt2 + $amt3 + $amt4 + $amt5;
	}

	//PENJAMIN 1
	if ($i == 57) {
		$userID = 0;
		if ($pid1 = $GetLoan->fields(penjaminID1)) {
			$userID = $pid1; //dlookup("loans", "userID", "loanID='".$loID."'");
			$memberID = dlookup("userdetails", "memberID", "userID='" . $userID . "'");
			$gaji = dlookup("userloandetails", "gaji", "userID='" . $userID . "'");
			$pid1Fee = dlookup("userdetails", "totalFee", "userID='" . $userID . "'");
			$totPid1 =  $gaji + $pid1Fee;

			$sqlGet = "SELECT sum(loanAmt) as totalLoan FROM `loans` where userID = '" . $userID . "' and isApproved = 1";
			$GettotLoan =  &$conn->Execute($sqlGet);
			$jumbiaya = $GettotLoan->fields(totalLoan);
			$totalT = $GettotLoan->fields(totalLoan);

			$strFormValue = $gaji;
		}
	}

	if ($i == 58) {
		if ($userID) $strFormValue = $totalT;
	}

	if ($i == 59) {
		if ($userID) $strFormValue = $pid1Fee;
	}

	if ($i == 61) {
		if ($userID) $strFormValue = $totPid1;
	}

	if ($i == 60) {
		if ($userID) {
			$bList = array();
			$sSQL = "SELECT DISTINCT A.loanID FROM loans A, userdetails B WHERE ( A.userID = B.userID AND ( A.penjaminID1 = '" . $userID . "' OR A.penjaminID2 = '" . $userID . "' OR A.penjaminID2 = '" . $userID . "')) ORDER BY A.applyDate";
			$rs = &$conn->Execute($sSQL);
			$j = $i;
			if ($rs->RowCount() <> 0) {
				while (!$rs->EOF) {

					$loID = $rs->fields(loanID);
					$memberID = dlookup("userdetails", "memberID", "userID='" . $userID . "'");
					if ($pid = dlookup("loans", "penjaminID1", "loanID='" . $loID . "'")) {
						if ($pid == $userID) $field = 1;
						$bList[$j] = $loID;
					}
					if ($pid = dlookup("loans", "penjaminID2", "loanID='" . $loID . "'")) {
						if ($pid == $userID) $field = 2;
						$bList[$j] = $loID;
					}
					if ($pid = dlookup("loans", "penjaminID3", "loanID='" . $loID . "'")) {
						if ($pid == $userID)	$field = 3;
						$bList[$j] = $loID;
					}

					$j += 2;;
					$rs->MoveNext();
				}
			}
			$amt1 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
			$strFormValue = "(" . $bList[$i] . ") RP " . $amt1;
		}
	}

	if ($i == 62) {
		$amt2 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt2) $strFormValue = "(" . $bList[$i] . ") RP " . $amt2;
	}

	if ($i == 64) {
		$amt3 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt3) $strFormValue = "(" . $bList[$i] . ") RP " . $amt3;
	}

	if ($i == 66) {
		$amt4 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt4) $strFormValue = "(" . $bList[$i] . ") RP " . $amt4;
	}

	if ($i == 68) {
		$amt5 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt5) $strFormValue = "(" . $bList[$i] . ") RP " . $amt5;
	}

	if ($i == 70) {
		if ($userID) $strFormValue = $totalT + $amt1 + $amt2 + $amt3 + $amt4 + $amt5;
	}

	//PENJAMIN 2
	if ($i == 75) {
		$userID = 0;
		if ($pid1 = $GetLoan->fields(penjaminID2)) {
			$userID = $pid1; //dlookup("loans", "userID", "loanID='".$loID."'");
			$memberID = dlookup("userdetails", "memberID", "userID='" . $userID . "'");
			$gaji = dlookup("userloandetails", "gaji", "userID='" . $userID . "'");
			$pid1Fee = dlookup("userdetails", "totalFee", "userID='" . $userID . "'");
			$totPid1 =  $gaji + $pid1Fee;

			$sqlGet = "SELECT sum(loanAmt) as totalLoan FROM `loans` where userID = '" . $userID . "' and isApproved = 1";
			$GettotLoan =  &$conn->Execute($sqlGet);
			$jumbiaya = $GettotLoan->fields(totalLoan);
			$totalT = $GettotLoan->fields(totalLoan);

			$strFormValue = $gaji;
		}
	}

	if ($i == 76) {
		if ($userID) $strFormValue = $totalT;
	}

	if ($i == 77) {
		if ($userID) $strFormValue = $pid1Fee;
	}

	if ($i == 79) {
		if ($userID) $strFormValue = $totPid1;
	}

	if ($i == 78) {
		if ($userID) {
			$bList = array();
			$sSQL = "SELECT DISTINCT A.loanID FROM loans A, userdetails B WHERE ( A.userID = B.userID AND ( A.penjaminID1 = '" . $userID . "' OR A.penjaminID2 = '" . $userID . "' OR A.penjaminID2 = '" . $userID . "')) ORDER BY A.applyDate";
			$rs = &$conn->Execute($sSQL);
			$j = $i;
			if ($rs->RowCount() <> 0) {
				while (!$rs->EOF) {

					$loID = $rs->fields(loanID);
					$memberID = dlookup("userdetails", "memberID", "userID='" . $userID . "'");
					if ($pid = dlookup("loans", "penjaminID1", "loanID='" . $loID . "'")) {
						if ($pid == $userID) $field = 1;
						$bList[$j] = $loID;
					}
					if ($pid = dlookup("loans", "penjaminID2", "loanID='" . $loID . "'")) {
						if ($pid == $userID) $field = 2;
						$bList[$j] = $loID;
					}
					if ($pid = dlookup("loans", "penjaminID3", "loanID='" . $loID . "'")) {
						if ($pid == $userID)	$field = 3;
						$bList[$j] = $loID;
					}

					$j += 2;;
					$rs->MoveNext();
				}
			}
			$amt1 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
			$strFormValue = "(" . $bList[$i] . ") RP " . $amt1;
		}
	}

	if ($i == 80) {
		$amt2 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt2) $strFormValue = "(" . $bList[$i] . ") RP " . $amt2;
	}

	if ($i == 82) {
		$amt3 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt3) $strFormValue = "(" . $bList[$i] . ") RP " . $amt3;
	}

	if ($i == 84) {
		$amt4 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt4) $strFormValue = "(" . $bList[$i] . ") RP " . $amt4;
	}

	if ($i == 86) {
		$amt5 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt5) $strFormValue = "(" . $bList[$i] . ") RP " . $amt5;
	}

	if ($i == 88) {
		if ($userID) $strFormValue = $totalT + $amt1 + $amt2 + $amt3 + $amt4 + $amt5;
	}

	//PENJAMIN3
	if ($i == 93) {
		$userID = 0;
		if ($pid1 = $GetLoan->fields(penjaminID3)) {
			$userID = $pid1; //dlookup("loans", "userID", "loanID='".$loID."'");
			$memberID = dlookup("userdetails", "memberID", "userID='" . $userID . "'");
			$gaji = dlookup("userloandetails", "gaji", "userID='" . $userID . "'");
			$pid1Fee = dlookup("userdetails", "totalFee", "userID='" . $userID . "'");
			$totPid1 =  $gaji + $pid1Fee;

			$sqlGet = "SELECT sum(loanAmt) as totalLoan FROM `loans` where userID = '" . $userID . "' and isApproved = 1";
			$GettotLoan =  &$conn->Execute($sqlGet);
			$jumbiaya = $GettotLoan->fields(totalLoan);
			$totalT = $GettotLoan->fields(totalLoan);

			$strFormValue = $gaji;
		}
	}

	if ($i == 94) {
		if ($userID) $strFormValue = $totalT;
	}

	if ($i == 95) {
		if ($userID) $strFormValue = $pid1Fee;
	}

	if ($i == 97) {
		if ($userID) $strFormValue = $totPid1;
	}

	if ($i == 96) {
		if ($userID) {
			$bList = array();
			$sSQL = "SELECT DISTINCT A.loanID FROM loans A, userdetails B WHERE ( A.userID = B.userID AND ( A.penjaminID1 = '" . $userID . "' OR A.penjaminID2 = '" . $userID . "' OR A.penjaminID2 = '" . $userID . "')) ORDER BY A.applyDate";
			$rs = &$conn->Execute($sSQL);
			$j = $i;
			if ($rs->RowCount() <> 0) {
				while (!$rs->EOF) {

					$loID = $rs->fields(loanID);
					$memberID = dlookup("userdetails", "memberID", "userID='" . $userID . "'");
					if ($pid = dlookup("loans", "penjaminID1", "loanID='" . $loID . "'")) {
						if ($pid == $userID) $field = 1;
						$bList[$j] = $loID;
					}
					if ($pid = dlookup("loans", "penjaminID2", "loanID='" . $loID . "'")) {
						if ($pid == $userID) $field = 2;
						$bList[$j] = $loID;
					}
					if ($pid = dlookup("loans", "penjaminID3", "loanID='" . $loID . "'")) {
						if ($pid == $userID)	$field = 3;
						$bList[$j] = $loID;
					}

					$j += 2;;
					$rs->MoveNext();
				}
			}
			$amt1 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
			$strFormValue = "(" . $bList[$i] . ") RP " . $amt1;
		}
	}

	if ($i == 98) {
		$amt2 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt2) $strFormValue = "(" . $bList[$i] . ") RP " . $amt2;
	}

	if ($i == 100) {
		$amt3 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt3) $strFormValue = "(" . $bList[$i] . ") RP " . $amt3;
	}

	if ($i == 102) {
		$amt4 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt4) $strFormValue = "(" . $bList[$i] . ") RP " . $amt4;
	}

	if ($i == 104) {
		$amt5 = dlookup("loans", "loanAmt", "loanID='" . $bList[$i] . "'");
		if ($amt5) $strFormValue = "(" . $bList[$i] . ") RP " . $amt5;
	}

	if ($i == 106) {
		if ($userID) $strFormValue = $totalT + $amt1 + $amt2 + $amt3 + $amt4 + $amt5;
	}


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


	if ($i == 2) {
		print str_repeat("&nbsp;", 20);
	}

	if ($i == 3) {
		$stradd = str_replace("<pre>", "", $GetLoan->fields(address));
		$stradd = str_replace("</pre>", "", $stradd);

		$add = $stradd . ', ' . tohtml($GetLoan->fields(city)) . ', <br />  ' . tohtml($GetLoan->fields(postcode)) . ', ' . dlookup("general", "name", "ID=" . $GetLoan->fields(stateID));

		print '<b>' . $add . '</b>';
	}

	if ($i == 4) {
		$yric = substr($GetLoan->fields(newIC), 0, 2);
		$yfisrt = substr($GetLoan->fields(newIC), 0, 1);
		$nowyr = (int)date(y);
		if ($yic <> '0') $age = (100 - $yric) + $nowyr;
		else $age = $nowyr;
		print '<b>' . $age . '</b>';
	}

	if ($i == 8) {
		$dept = dlookup("general", "name", "ID=" . $GetLoan->fields(departmentID));
		print '<b>' . $dept . '</b>';
	}

	if ($i == 15) {
		$stradd = str_replace("<pre>", "", $GetLoan->fields(addresspsgn));
		$stradd = str_replace("</pre>", "", $stradd);

		if ($stradd) $add = $stradd;
		else $add = '';
		// .', '.tohtml($GetLoan->fields(citypsgn)).', <br />  '.tohtml($GetLoan->fields(postcodepsgn)).', '.dlookup("general", "name", "ID=" . $GetLoan->fields(stateIDpsgn)); 
		print '<b>' . $add . '</b>';
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td>';
	if ($cnt == 0) print '</tr>';
}
//$pic = dlookup("userloandetails", "gaji_img", "userID=" . tosql($uid, "Text"));
//$Gambar= "upload_gaji/".$pic;

$pic = dlookup("userloandetails", "gaji_img", "userID=" . tosql($uid, "Text"));
$Gambar = "upload_gaji/" . $pic;
$picjwtn = dlookup("userloandetails", "jwtn_img", "userID=" . tosql($uid, "Text"));
$Gambarjwtn = "upload_jwtn/" . $pic;
$picic = dlookup("userloandetails", "ic_img", "userID=" . tosql($uid, "Text"));
$Gambaric = "upload_ic/" . $pic;
$picccris = dlookup("userloandetails", "ccris_img", "userID=" . tosql($uid, "Text"));
$Gambarccris = "upload_CCRIS/" . $pic;
if (!isset($picother)) $picother = dlookup("userloandetails", "lain_img", "userID=" . tosql($uid, "Text"));
$Gambarother = "upload_lain/" . $pic;

//
//pendapatan / perbelanjaan
print '<tr>
		<td colspan=4><div class="card-header mt-3 d-flex justify-content-between align-items-center">C. PENYATA PENDAPATAN/PERBELANJAAN&nbsp;
			<div>
				<a href="?vw=biayaEdit&mn=906&pk=' . $pk . '&userID=' . $uid . '" style="font-size: 24px;" class="fas fa-pencil-alt" title="Ubah"></a>
			</div>
			</div>
		</div></td></tr>
		';

if ($valAddDpt <= 0) $valAddDpt = 0;
if ($addDpt) $valAddDpt++;
if ($minDpt) $valAddDpt--;
print '<tr>
		<td colspan=4><div class="card-header bg-soft-primary">i. PENYATA PENDAPATAN</div>
		<input type="hidden" name="valAddDpt" value="' . $valAddDpt . '" class="but">
		<!--input type="submit" name="addDpt" value="Tambah" class="but"><input type="submit" name="minDpt" value="Kurang" class="but"--></td></tr>';

$SQLdpt = "";
$SQLdpt	= "SELECT * FROM `userstates` WHERE userID = '" . $uid . "' AND payType = 'A'";
$rsdpt = &$conn->Execute($SQLdpt);

if ($rsdpt->RowCount() <> 0) {
	$i = 1;
	$tot = 0;
	while (!$rsdpt->EOF) {
		$id = $rsdpt->fields('payID');
		$stateID = $rsdpt->fields('ID');
		//$code = dlookup("general", "code", "ID=" . tosql($id, "Number"));
		$name = dlookup("general", "name", "ID=" . tosql($id, "Number"));
		$value = $rsdpt->fields('amt');

		print 	'<tr valign="top">
				<td class="Data" align="right" width="20%">
					<input name="idD' . $i . '" type="hidden" value="' . $id . '">
					<input name="stateIDd' . $i . '" type="hidden" value="' . $stateID . '"> 
					<input name="nameD' . $i . '" type="hidden" class="Data" value="' . $name . '" size="20" align="right" onfocus="this.blur()">' . $name . ': </td>
				<td class="Data">
					<input name="valueD' . $i . '" size="10" maxlength="15" type="hidden" value="' . $value . '"> <b>' . $value . '</b>&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>';
		$totD = $i;
		$i++;
		$tot += $value;
		$rsdpt->MoveNext();
	}
	print 	'<tr valign="top">
				<td class="DataB" align="right" width="20%">
				<b>Jumlah : </b></td>
				<td class="DataB">
				<b>' . number_format($tot, 2) . '</b>&nbsp;
				</td>
				<td class="DataB" align="right">&nbsp; </td>
				<td class="DataB">&nbsp;</td>
				</tr>';
}

//print_r($_POST);
if ($valAddBlj <= 0) $valAddBlj = 0;
if ($addBlj) $valAddBlj++;
if ($minBlj) $valAddBlj--;
print '	<tr><td colspan=4><div class="card-header bg-soft-primary">ii. PENYATA PERBELANJAAN</div>
		<input type="hidden" name="valAddBlj" value="' . $valAddBlj . '" class="but">
		<!--input type="submit" name="addBlj" value="Tambah" class="but"><input type="submit" name="minBlj" value="Kurang" class="but"--></td></tr>';

$SQLblj = "";
$SQLblj	= "SELECT * FROM `userstates` WHERE userID = '" . $uid . "' AND payType = 'B'";
$rsblj = &$conn->Execute($SQLblj);

if ($rsblj->RowCount() <> 0) {
	$i = 1;
	$tot = 0;
	while (!$rsblj->EOF) {
		$id = $rsblj->fields('payID');
		$stateID = $rsblj->fields('ID');
		//$code = dlookup("general", "code", "ID=" . tosql($id, "Number"));
		$name = dlookup("general", "name", "ID=" . tosql($id, "Number"));
		$value = $rsblj->fields('amt');

		print 	'<tr valign="top">
				<td class="Data" align="right" width="20%">
					<input name="idJ' . $i . '" type="hidden" value="' . $id . '">
					<input name="stateIDj' . $i . '" type="hidden" value="' . $stateID . '"> 
					<input name="nameJ' . $i . '" type="hidden" class="Data" value="' . $name . '" size="20" align="right" onfocus="this.blur()">' . $name . ': </td>
				<td class="Data">
					<input name="valueJ' . $i . '" size="10" maxlength="15" type="hidden" value="' . $value . '"><b> ' . $value . '</b>&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>';
		$totJ = $i;
		$i++;
		$tot += $value;
		$rsblj->MoveNext();
	}
	print 	'<tr valign="top">
				<td class="DataB" align="right" width="20%">
				<b>Jumlah : </b></td>
				<td class="DataB"><b>
				' . number_format($tot, 2) . '</b>&nbsp;
				</td>
				<td class="DataB" align="right">&nbsp; </td>
				<td class="DataB">&nbsp;</td>
				</tr>';
}

print '<tr>
		<td colspan=4><div class="card-header mt-3">PAPARAN DOKUMEN YANG TELAH DIMUAT NAIK</div></td></tr>';

print '<tr><td colspan=4>
		<table class="table table-sm table-striped">
		<tr class="table-primary">
			<td><b>Perkara</b></td>
			<td><b>Nama Fail</b></td>
		</tr>

		<tr>
			<td class="align-middle">* Slip Gaji</td>
			<td class="align-middle">';

if ($pic) {
	print '
					<a href onClick=window.open(\'upload_gaji/' . $pic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Slip Gaji';
}
print '</td>
		</tr>

		<tr>
			<td class="align-middle">* Kartu Identitas</td>
			<td class="align-middle">';

if ($picic) {
	print '
					<a href onClick=window.open(\'upload_ic/' . $picic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan IC';
}
print '</td>
		</tr>

		<tr>
			<td class="align-middle">Jawatan Tetap</td>
			<td class="align-middle">';

if ($picjwtn) {
	print '
					<a href onClick=window.open(\'upload_jwtn/' . $picjwtn . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Pengesahan Jawatan';
}
print '</td>
		</tr>

		<tr>
			<td class="align-middle">CCRIS</td>
			<td class="align-middle">';

if ($picccris) {
	print '
					<a href type=button onClick=window.open(\'upload_CCRIS/' . $picccris . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan CCRIS';
}
print '</td>
		</tr>

		<tr>
			<td class="align-middle">Lain-lain</td>
			<td class="align-middle">';

if ($picother) {
	print '
					<a href type=button onClick=window.open(\'upload_lain/' . $picother . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Lain-lain';
}
print '</td>
		</tr>

		</table></td></tr>';

print '<tr><td colspan=4 align=center class=Data>
			<!--input type=Reset name=ResetForm class="but" value=Clear Form>
			<input type=Submit name=SubmitForm class="but" value=Kemaskini>
		<input type="button" class="label" value="Dokumen Tawaran" onclick="window.open(\'dokPP.php?pk=' . $pk . '&obj=5\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">	
		<input type=Submit name=SubmitForm class="but" value="Semakan Komiti"-->
		<button class="btn btn-secondary waves-effect waves-light" onClick="window.print();">Cetak</button>
		<input type=button name=ResetForm class="btn btn-secondary" value=<< onClick="window.location.href =\'' . $sFileName . '\'"></td>
		</tr>

</table>
</form>';
print '
<script language="JavaScript">
	var allChecked=false;
	
	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }
		
	function ITRActionButtonClickStatus(v) {
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
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
	          //alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          //if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          //window.location.href ="memberApply.php?pk=" + strStatus;
	          window.location.href ="' . $sActionFileName . '";
			  //}
	        }
	      }
	    }

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}



</script>';
print '
<script>
	function selectPengurusan(rpt) {
		window.open(rpt+".php" ,"pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");
	}	  
</script>';
include("footer.php");
