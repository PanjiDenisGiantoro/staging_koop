<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	borangcadangan.php
 *          Date 		: 	10/10/2003
 *********************************************************************************/
include("header.php");

include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.location="index.php";</script>';
}
$sFileName		= "memberEdit.php";
$sActionFileName = "memberEdit.php?pk=" . $pk;
$title     		= "Kemaskini Maklumat Anggota";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

//--- Prepare race type
$raceList = array();
$raceVal  = array();
$GetRace = ctGeneral("", "E");
if ($GetRace->RowCount() <> 0) {
	while (!$GetRace->EOF) {
		array_push($raceList, $GetRace->fields(name));
		array_push($raceVal, $GetRace->fields(ID));
		$GetRace->MoveNext();
	}
}
//--- Prepare religion type
$religionList = array();
$religionVal  = array();
$GetReligion = ctGeneral("", "F");
if ($GetReligion->RowCount() <> 0) {
	while (!$GetReligion->EOF) {
		array_push($religionList, $GetReligion->fields(name));
		array_push($religionVal, $GetReligion->fields(ID));
		$GetReligion->MoveNext();
	}
}
//--- Prepare state type
$stateList = array();
$stateVal  = array();
$GetState = ctGeneral("", "H");
if ($GetState->RowCount() <> 0) {
	while (!$GetState->EOF) {
		array_push($stateList, $GetState->fields(name));
		array_push($stateVal, $GetState->fields(ID));
		$GetState->MoveNext();
	}
}
$deptList = array();
$deptVal  = array();
$GetDept = ctGeneral("", "B");
if ($GetDept->RowCount() <> 0) {
	while (!$GetDept->EOF) {
		array_push($deptList, $GetDept->fields(name));
		array_push($deptVal, $GetDept->fields(ID));
		$GetDept->MoveNext();
	}
}

//--- Prepare society
$societyList = array();
$societyVal  = array();
$GetSociety = ctGeneral("", "L");
if ($GetSociety->RowCount() <> 0) {
	while (!$GetSociety->EOF) {
		array_push($societyList, $GetSociety->fields(name));
		array_push($societyVal, $GetSociety->fields(ID));
		$GetSociety->MoveNext();
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
$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "a";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "b";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$FormLabel[$a]   	= "Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "40";
$FormLength[$a]  	= "70";

$a++;
$FormLabel[$a]   	= "Nomor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "ID Pengguna";
$FormElement[$a] 	= "loginID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Tanggal Menjadi Anggota";
$FormElement[$a] 	= "approvedDate";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDate);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Email";
$FormElement[$a] 	= "email";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "No KTP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckNumeric);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Tanggal Lahir (dd/mm/yyyy)";
$FormElement[$a] 	= "dateBirth";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDate);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Jawatan";
$FormElement[$a] 	= "job";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Jabatan/<br>Cawangan";
$FormElement[$a] 	= "departmentIDd";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Alamat";
$FormElement[$a] 	= "address";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "Poskod";
$FormElement[$a] 	= "postcode";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckNumeric);
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "Bandar";
$FormElement[$a] 	= "city";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Negeri";
$FormElement[$a] 	= "stateID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Tel Rumah";
$FormElement[$a] 	= "homeNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Tel Bimbit";
$FormElement[$a] 	= "mobileNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Alamat Surat Menyurat";
$FormElement[$a] 	= "addressSurat";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";



$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Nombor Akaun Tabungan<br>(12-345-678901-2)";
$FormElement[$a] 	= "accTabungan";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Gaji";
$FormElement[$a] 	= "grossPay";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "No Pekerja";
$FormElement[$a] 	= "staftNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Jantina";
$FormElement[$a] 	= "sex";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Laki-laki', 'Perempuan');
$FormDataValue[$a]	= array('0', '1');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";



$a++;
$FormLabel[$a]   	= "Bangsa";
$FormElement[$a] 	= "raceID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $raceList;
$FormDataValue[$a]	= $raceVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Status Pernikahan";
$FormElement[$a] 	= "maritalID";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Belum menikah', 'Menikah', 'Janda/Duda');
$FormDataValue[$a]	= array('0', '1', '2');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Agama";
$FormElement[$a] 	= "religionID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $religionList;
$FormDataValue[$a]	= $religionVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";



$a++;
$FormLabel[$a]   	= "Daftar Hitam Dividen";
$FormElement[$a] 	= "BlackListDIV";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Ya', 'Tidak');
$FormDataValue[$a]	= array('1', '0');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";


$a++;
$FormLabel[$a]   	= "Status Daftar Hitam";
$FormElement[$a] 	= "BlackListID";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Ya', 'Tidak');
$FormDataValue[$a]	= array('1', '0');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Status Utang Macet";
$FormElement[$a] 	= "statusHL";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Ya', 'Tidak');
$FormDataValue[$a]	= array('1', '0');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Status MSS";
$FormElement[$a] 	= "statusMSS";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Ya', 'Tidak');
$FormDataValue[$a]	= array('1', '0');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";




$a++;
$FormLabel[$a]   	= "Jumlah Bayaran";
$FormElement[$a] 	= "totPay";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Wajib Bulanan";
$FormElement[$a] 	= "monthFee";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Nama";
$FormElement[$a] 	= "saksi1";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No KP";
$FormElement[$a] 	= "saksiIC1";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";

$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "14";

$a++;
$FormLabel[$a]   	= "Nama";
$FormElement[$a] 	= "saksi2";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No KP";
$FormElement[$a] 	= "saksiIC2";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "14";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
//$conn->debug=1;
//$GetMember = ctMemberDetail($pk);
$strMember = "SELECT a . * , b . * FROM users a, userdetails b WHERE a.userID = '" . $pk . "' AND a.userID = b.userID";
$GetMember = &$conn->Execute($strMember);
//$statusHLID = 2;
//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
$SQLID = "select * FROM userdetails where userID = '" . $pk . "'";
$GetLoansIDs =  &$conn->Execute($SQLID);
$statusHLID = $GetLoansIDs->fields('statusHL');

if ($SubmitForm <> "") {

	$sqlLoan = "select * FROM loans where userID = '" . $pk . "' AND status = 3 ";
	$GetLoans =  &$conn->Execute($sqlLoan);
	$kira = $GetLoans->RowCount();

	if ($dept == '') {
		array_push($strErrMsg, "departmentIDd");
		print '- <font class=redText>Sila pilih jabatan.</font><br />';
	}


	if ($accTabungan) {
		if (!ereg("([0-9]{2})-([0-9]{3})-([0-9]{6})-([0-9]{1})", $accTabungan, $regs)) {
			array_push($strErrMsg, "accTabungan");
			print '- <font class=redText>Nombor akaun tabungan tersebut tidak mengikut format.</font><br />';
		}
	}
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
	$memberDate = substr($memberDate, 6, 4) . '-' . substr($memberDate, 3, 2) . '-' . substr($memberDate, 0, 2);
	$approvedDate = substr($approvedDate, 6, 4) . '-' . substr($approvedDate, 3, 2) . '-' . substr($approvedDate, 0, 2);
	$dateBirth = substr($dateBirth, 6, 4) . '-' . substr($dateBirth, 3, 2) . '-' . substr($dateBirth, 0, 2);
	$dateStarted   = substr($dateStarted, 6, 4) . '-' . substr($dateStarted, 3, 2) . '-' . substr($dateStarted, 0, 2);
	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE users SET " .
			"name=" . tosql($name, "Text") .
			",email=" . tosql($email, "Text") .
			",updatedDate=" . tosql($updatedDate, "Text") .
			",updatedBy=" . tosql($updatedBy, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);

		if ($address <> "") $address = '<pre>' . $address . '</pre>';
		if ($w_address1 <> "") $w_address1 = '<pre>' . $w_address1 . '</pre>';
		if ($w_address2 <> "") $w_address2 = '<pre>' . $w_address2 . '</pre>';
		if ($w_address3 <> "") $w_address3 = '<pre>' . $w_address3 . '</pre>';
		if ($w_address4 <> "") $w_address4 = '<pre>' . $w_address4 . '</pre>';
		if ($w_address5 <> "") $w_address5 = '<pre>' . $w_address5 . '</pre>';
		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE userdetails SET " .
			" approvedDate=" . tosql($approvedDate, "Text") .
			", staftNo=" . tosql($staftNo, "Text") .
			", picture=" . tosql($picture, "Text") .
			", newIC=" . tosql($newIC, "Text") .
			", dateBirth=" . tosql($dateBirth, "Text") .
			", sex=" . tosql($sex, "Number") .
			", raceID=" . tosql($raceID, "Number") .
			", religionID=" . tosql($religionID, "Number") .
			", maritalID=" . tosql($maritalID, "Number") .
			", BlackListID=" . tosql($BlackListID, "Number") .
			", statusHL=" . tosql($statusHL, "Number") .
			", statusMSS=" . tosql($statusMSS, "Number") .
			", BlackListDIV=" . tosql($BlackListDIV, "Number") .
			", job=" . tosql($job, "Text") .
			", accTabungan=" . tosql($accTabungan, "Text") .
			", grossPay=" . tosql($grossPay, "Number") .
			", address=" . tosql($address, "Text") .
			", city=" . tosql($city, "Text") .
			", postcode=" . tosql($postcode, "Text") .
			", stateID=" . tosql($stateID, "Number") .
			", homeNo=" . tosql($homeNo, "Text") .
			", mobileNo=" . tosql($mobileNo, "Text") .
			", addressSurat=" . tosql($addressSurat, "Text") .
			", citySurat=" . tosql($citySurat, "Text") .
			", postcodeSurat=" . tosql($postcodeSurat, "Text") .
			", stateIDSurat=" . tosql($stateIDSurat, "Number") .
			", departmentID=" . tosql($dept, "Number") .
			", totPay=" . tosql($totPay, "Number") .
			", monthFee=" . tosql($monthFee, "Number") .
			", w_name1=" . tosql($w_name1, "Text") .
			", w_ic1=" . tosql($w_ic1, "Text") .
			", w_relation1=" . tosql($w_relation1, "Text") .
			", w_address1=" . tosql($w_address1, "Text") .
			", saksi1=" . tosql($saksi1, "Text") .
			", saksiIC1=" . tosql($saksiIC1, "Text") .
			", saksi2=" . tosql($saksi2, "Text") .
			", saksiIC2=" . tosql($saksiIC2, "Text") .
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			" VALUES ('Mengemaskini Maklumat Peribadi Anggota - $pk', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '1')";
		$rs = &$conn->Execute($sqlAct);

		$SQLID = "select * FROM userdetails where userID = '" . $pk . "'";
		$GetLoansIDs =  &$conn->Execute($SQLID);
		$statusHLID = $GetLoansIDs->fields('statusHL');

		if ($statusHLID == '1') {
			for ($i = 0; $i < $kira; $i++) {

				$sqlLoan = "select * FROM loans where userID = '" . $pk . "' AND status = 3 ";
				$GetLoans =  &$conn->Execute($sqlLoan);
				$GetLoansID = $GetLoans->fields('loanID');

				$sSQL =	'';
				$sWhere	= '	loanID	 = ' . $GetLoansID;
				$sSQL	= '	UPDATE loans ';
				$sSQL	.= ' SET ' .
					' status	=' . tosql(7, "Text") .
					' ,selesaiBy	=' . tosql($updatedBy, "Text") .
					' ,selesaiDate='	. tosql($updatedDate, "Text");
				$sSQL .= ' WHERE ' . $sWhere;
				$rsHL	= &$conn->Execute($sSQL);
			}
		} // end loop update
		print '<script>
					alert ("Informasi anggota telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="picture" value="' . $pic . '">
<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
	<tr>
		<td colspan="4" class="Data"><b class="maroonText">' . strtoupper($title) . '</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;
	if ($i == 1) print '<tr><td class=Header colspan=4>INFORPASI PENDAFTARAN ID :</td></tr>';
	if ($i == 9) print '<tr><td class=Header colspan=4>A. DETAIL PRIBADI :</td></tr>';
	if ($i == 33) print '<tr><td class=Header colspan=4>B. BAYARAN MASUK/YURAN :</td></tr>';
	$addr = str_replace("<pre>", "", $GetMember->fields('w_address1'));
	$addr1 = str_replace("</pre>", "", $addr);

	if ($i == 35) {
		print '<tr><td class=Header colspan=4>C. PENAMA:</td></tr>';
		print '<tr class="Data">
					<td colspan="4">
						<table width="100%">
							<tr class="DataB">
								<td>&nbsp;</td>	
								<td>Nama</td>
								<td>No KP</td>
								<td>Hubungan</td>
								<td>Alamat</td>
							</tr>
						<tr class="Data">
								<td valign="top">&nbsp;</td>	
								<td valign="top"><input type="text" name="w_name1" value="' . tohtml($GetMember->fields('w_name1')) . '" size=30 maxlength=50></td>
								<td valign="top"><input type="text" name="w_ic1" value="' . tohtml($GetMember->fields('w_ic1')) . '" size=15 maxlength=14></td>
								<td valign="top"><input type="text" name="w_relation1" value="' . tohtml($GetMember->fields('w_relation1')) . '" size=15 maxlength=15></td>
								<td valign="top"><textarea cols=30 rows=3 wrap="hard" name="w_address1">' . $addr1 . '</textarea></td>
							</tr>';
		print	'</table></td></tr>
		       <tr><td class=Header colspan=4>D. SAKSI :</td></tr>';
	}

	if ($cnt == 1) print '<tr valign=top>';
	print '<td class=Data align=right>' . $FormLabel[$i];
	if (!($i == 1 or $i == 2 or $i == 8  or $i == 20 or $i == 21 or $i == 22 or $i == 31)) print ':';
	print ' </td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetMember->fields($FormElement[$i]));
	if ($FormType[$i] == 'textarea') {
		$strFormValue = str_replace("<pre>", "", $GetMember->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>", "", $strFormValue);
	}

	if ($i == 19) {
		if (!$dept) {
			$strFormValue = dlookup("general", "b_Address", "ID=" . tosql($GetMember->fields('departmentID'), "Number"));
		} else {
			$strFormValue = dlookup("general", "b_Address", "ID=" . $dept);
		}
		$strFormValue = str_replace("<pre>", "", $strFormValue);
		$strFormValue = str_replace("</pre>", "", $strFormValue);
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

	if ($i == 1) {
		if (!isset($pic)) $pic = dlookup("userdetails", "picture", "userID=" . tosql($pk, "Text"));
		$Gambar = "upload_images/" . $pic;
		print '<img id="elImage" src="' . $Gambar . '" width="100" height="90">&nbsp;<input type="button" name="GetPicture" value="Tambah Gambar" width="30" height="10" onclick= "Javascript:(window.location.href=\'?vw=uploadwin&pk=' . $pk . '\')">';
	}

	if ($i == 12) {
		if (!isset($dept)) $dept = $GetMember->fields('departmentID');
		print '<select name="dept" class="data">
				<option value="">- Semua -';
		for ($j = 0; $j < count($deptList); $j++) {
			print '	<option value="' . $deptVal[$j] . '" ';
			if ($dept == $deptVal[$j]) print ' selected';
			print '>' . $deptList[$j];
		}
		print '</select>&nbsp;';
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td>';
	if ($cnt == 0) print '</tr>';
}

if ((get_session("Cookie_groupID") == 2) or (get_session("Cookie_userName") == "SUFRI") or (get_session("Cookie_userName") == "Adleen31")) {
	print '<tr><td colspan=4 align=center class=Data>
			<input type="hidden" name="pk" value="' . $pk . '">
			<input type=Submit name=SubmitForm class="but" value=Kemaskini>
			</td></tr>';
}
print '</table></form>';
include("footer.php");
