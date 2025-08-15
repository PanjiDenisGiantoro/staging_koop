<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanEdit.php
 *          Date 		: 	08/10/2003
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$sFileName		= "loanEdit.php";
$sActionFileName = "loan.php";
$title     		= "Kemaskini Permohonan Pinjaman";

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
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "No KP Baru / Lama";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Tarikh Mula Bayar Mengikut Jadual";
$FormElement[$a] 	= "startPymtDate";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDate);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Insuran";
$FormElement[$a] 	= "insuranceID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $panelList;
$FormDataValue[$a]	= $panelVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Bayaran Insuran";
$FormElement[$a] 	= "insurancePymt";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Kod Pinjaman";
$FormElement[$a] 	= "loanCode";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Caj Pinjaman (%)";
$FormElement[$a] 	= "loanCaj";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "2";

$a++;
$FormLabel[$a]   	= "Proses Pinjaman";
$FormElement[$a] 	= "proPinjam";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Overlapping";
$FormElement[$a] 	= "overlap";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Tempoh Bayaran";
$FormElement[$a] 	= "loanPeriod";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "Jumlah Pinjaman";
$FormElement[$a] 	= "loanAmt";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Bayar Balik (Bulan)";
$FormElement[$a] 	= "monthlyPymt";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Bil bulan potongan awal";
$FormElement[$a] 	= "earlyMonth";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "2";

$a++;
$FormLabel[$a]   	= "Ada Pinjaman Rumah?";
$FormElement[$a] 	= "houseLoan";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= array('Tak Ada', 'Ada');
$FormDataValue[$a]	= array('0', '1');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Tujuan Pinjaman";
$FormElement[$a] 	= "purpose";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "100";

$a++;
$FormLabel[$a]   	= "Bentuk Pembayaran";
$FormElement[$a] 	= "paymentID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $pymtList;
$FormDataValue[$a]	= $pymtVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]	  	= "Catatan";
$FormElement[$a] 	= "catatan";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "100";

$a++;
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "sellMemberID";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]  	= "Tarikh Memohon";
$FormElement[$a] 	= "applyDate";
$FormType[$a]  		= "date";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Status";
$FormElement[$a] 	= "status";
$FormType[$a]  		= "hidden";
$FormData[$a]    	= $statusList;
$FormDataValue[$a]	= $statusVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Diluluskan";
$FormElement[$a] 	= "approvedDate";
$FormType[$a]  		= "date";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Ditolak";
$FormElement[$a] 	= "rejectedDate";
$FormType[$a]  		= "date";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Kemaskini";
$FormElement[$a] 	= "updatedDate";
$FormType[$a]  		= "date";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]	  	= "Kemaskini Oleh";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]	  	= "Catatan";
$FormElement[$a] 	= "remark";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "100";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

$GetLoan = ctLoan("", $pk);

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
			",updatedBy=" . tosql($updatedBy, "Text") .
			//add for new field	
			",remark=" . tosql($remark, "Text") .
			",loanCaj=" . tosql($loanCaj, "Text") .
			",proPinjam=" . tosql($proPinjam, "Text") .
			",overlap=" . tosql($overlap, "Text") .
			",catatan=" . tosql($catatan, "Text") .
			",applyDate=" . tosql($applyDate, "Text") .
			",approvedDate=" . tosql($approvedDate, "Text") .
			",rejectedDate=" . tosql($rejectedDate, "Text") .
			",updatedDate=" . tosql($updatedDate, "Text") .
			",earlyMonth=" . tosql($earlyMonth, "Text");
		$sSQL .= " where " . $sWhere;
		//print $sSQL;
		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Status permohonan pinjaman telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
<input type="hidden" name="loanType" value="' . $loanType . '">
<table border=0 cellpadding=3 cellspacing=0 width=95% align="center" class="lineBG">
	<tr>
		<td colspan="2" class="Data"><b class="maroonText">' . strtoupper($title) . ' : ' . sprintf("%010d", $GetLoan->fields(loanID)) . '</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<tr><td class=Header colspan=2>Butir-butir Permohonan :</td></tr>';
	if ($i == 6) print '<tr><td class=Header colspan=2>Butir-butir Pinjaman :</td></tr>';
	if ($i == 18) print '<tr><td class=Header colspan=2>Butir-butir Penjamin :</td></tr>';
	if ($i == 19) print '<tr><td class=Header colspan=2>Audit Informasi :</td></tr>';

	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . ' :</td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetLoan->fields($FormElement[$i]));
	if ($strFormValue == '') $strFormValue = $$FormElement[$i];
	if ($i == 1) {
		$strFormValue = dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '&nbsp;-&nbsp; ' .
			dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Text"));
	}
	if ($i == 2) {
		$strFormValue = dlookup("userdetails", "newIC", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '&nbsp;/&nbsp; ' .
			dlookup("userdetails", "oldIC", "userID=" . tosql($GetLoan->fields(userID), "Text"));
	}
	if ($i == 6) {
		$strFormValue = dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")) . '&nbsp;/&nbsp; ' .
			dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
	}
	if ($i == 7) {
		$caj = tosql($GetLoan->fields(loanCaj), "Number");
		if ($caj == 0) {
			$strFormValue = dlookup("general", "C_Caj", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		}
	}
	/*
	if ($i == 8) {
		if ($strFormValue == 0)
			$strFormValue = dlookup("general", "C_Period", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
	}
	*/
	if ($i == 18) {
		print '<input type="hidden" name="sellUserID" value="' . $GetLoan->fields(guarantorID) . '">';
		$strFormValue = dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(guarantorID), "Text"));
		$sellUserName = dlookup("users", "name", "userID=" . tosql($GetLoan->fields(guarantorID), "Text"));
	}
	if ($i == 24) {
		$strFormValue 	=  dlookup("users", "name", "loginID='" . $strFormValue . "'");
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
	if ($i == 3) {
		if ($GetLoan->fields(startPymtDate) <> "") {
			print '&nbsp;<input type=button value="Lihat Jadual" class="but" onClick=window.open("loanJadual.php?id=' . $GetLoan->fields(loanID) . '","pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
		}
	}


	if ($i == 18) {
		print '
		<input type="button" class="label" value="..." onclick="window.open(\'selToMember.php?refer=d\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		<input type="text" name="sellUserName" class="Data" value="' . $sellUserName . '" onfocus="this.blur()" size="50">';
	}
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td></tr>';
}

print '<tr><td colspan=2 align=center class=Data>
			<p class="Label"><u>Perhatian</u> : Pengiraan semula bayaran bulanan akan diambilkira sekiranya ada perubahan dalam jumlah pinjaman atau tempoh bayaran.</p>
			<input type="hidden" name="pk" value="' . $pk . '">
			<input type=Submit name=SubmitForm class="but" value=Kemaskini>
			</td>
		</tr>
</table>
</form>';
//$updatedBy 	=  dlookup("users", "name", "loginID='". get_session("Cookie_userName")."'");	

include("footer.php");
