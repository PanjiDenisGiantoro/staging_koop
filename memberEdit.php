<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberEdit.php
 *          Date 		: 	10/10/2003
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

if (@$tabb == '') {
	$tabb = 1;
}
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.location="index.php";</script>';
}

$sFileName		= "?vw=memberEdit&mn=905&pk=" . $pk . "";
$sActionFileName = "?vw=memberEdit&mn=905&pk=" . $pk . "&tabb=3";
$sActionFileName1 = "?vw=memberEdit&mn=905&pk=" . $pk . "&tabb=5";
$sFileRef  = "?vw=Edit_memberStmtPotonganPokok&mn=$mn";

$title     		= "Perbaharui Informasi Anggota";

$ID               = $_REQUEST['ID'];
$code             = $_REQUEST['code'];
$edit             = $_REQUEST['edit'];

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
if ($code == 1) {
	// Deleting nominee record
	$sSQLdel = "DELETE FROM nominee WHERE ID = " . intval($_REQUEST['IDtype']);
	$rsdel = $conn->Execute($sSQLdel);

	print '<script>alert("Penama telah berjaya dihapuskan!");</script>';
	print '<script>window.location.href = "' . $sActionFileName . '";</script>';
}

if ($code == 2) {
	$ID = $_REQUEST['pk'];
	// Retrieving nominee record
	$sSQL = "SELECT * FROM nominee WHERE userID = " . tosql($pk, "Text");
	$rs = $conn->Execute($sSQL);
}

if ($edit) {
	// Retrieving form data
	$IDtype = $_POST['IDtype'];
	$name = $_POST['name'];
	$newIC = $_POST['newIC'];
	$mobileNo = $_POST['mobileNo'];
	$address = $_POST['address'];
	$percent = $_POST['percent'];

	// Update nominee details (manual single quotes around each variable)
	$sSQLUpd = "UPDATE nominee SET 
                    name = '$name', 
                    newIC = '$newIC', 
                    mobileNo = '$mobileNo', 
                    address = '$address',
					percent = '$percent' 
                WHERE ID = '$IDtype'";

	$rsUpd = $conn->Execute($sSQLUpd);

	echo '<script>alert("Perbaruan Penerima Berhasil!");</script>';
	echo '<script>window.location.href = "' . $sActionFileName . '";</script>';
}

if ($code == 3) {
	// Deleting bank record
	$sSQLdel = "DELETE FROM bank WHERE ID = " . intval($_REQUEST['IDtype']);
	$rsdel = $conn->Execute($sSQLdel);

	print '<script>alert("Bank telah berjaya dihapuskan!");</script>';
	print '<script>window.location.href = "' . $sActionFileName1 . '";</script>';
}

if ($code == 4) {
	$ID = $_REQUEST['pk'];
	// Retrieving bank record
	$sSQL = "SELECT * FROM bank WHERE userID = " . tosql($pk, "Text");
	$rs = $conn->Execute($sSQL);
}

if ($edit1) {
	// Retrieving form data
	$IDtype = $_POST['IDtype'];
	$bankID = $_POST['bankID'];
	$accTabungan = $_POST['accTabungan'];

	// Update bank details (manual single quotes around each variable)
	$sSQLUpd = "UPDATE bank SET 
                    bankID = '$bankID', 
                    accTabungan = '$accTabungan' 
                WHERE ID = '$IDtype'";

	$rsUpd = $conn->Execute($sSQLUpd);

	echo '<script>alert("Perbaruan Bank Berhasil!");</script>';
	echo '<script>window.location.href = "' . $sActionFileName1 . '";</script>';
}

// if ($_POST['action'] == "set_priority") {
// 	if (isset($_POST['pk'])) {
// 		$selectedID = intval($_POST['pk']);

// 		// Set priority = 0 untuk semua bank terlebih dahulu
// 		$resetSQL = "UPDATE bank SET priority = 0 WHERE priority = 1";
// 		$conn->Execute($resetSQL);

// 		// Set priority = 1 untuk bank yang dipilih sahaja
// 		$updateSQL = "UPDATE bank SET priority = 1 WHERE ID = " . tosql($selectedID, "Number") . " AND priority = 0";
// 		$conn->Execute($updateSQL);

// 		$strActivity = $_POST['Submit'] . 'Perbarui Bank Utama Anggota - ' . get_session('Cookie_userID');
// 		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

// 		// Paparkan mesej alert dan refresh halaman
// 		echo '<script>            
//             location.reload(); // Refresh page
//         </script>';
// 	} else {
// 		echo '<script>alert("Sila pilih satu bank.");</script>';
// 	}
// }


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

//--- Prepare job type
$jobTypeList = array();
$jobTypeVal  = array();
$GetjobType = ctGeneral("", "L");
if ($GetjobType->RowCount() <> 0) {
	while (!$GetjobType->EOF) {
		array_push($jobTypeList, $GetjobType->fields(name));
		array_push($jobTypeVal, $GetjobType->fields(ID));
		$GetjobType->MoveNext();
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

//--- Prepare ptj type
$ptjList = array();
$ptjVal  = array();
$GetPtj = ctGeneral("", "U");
if ($GetPtj->RowCount() <> 0) {
	while (!$GetPtj->EOF) {
		array_push($ptjList, $GetPtj->fields(name));
		array_push($ptjVal, $GetPtj->fields(ID));
		$GetPtj->MoveNext();
	}
}

//--- Prepare department list
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

$bankList = array();
$bankVal  = array();
$Getbank = ctGeneral("", "Z");
if ($Getbank->RowCount() <> 0) {
	while (!$Getbank->EOF) {
		array_push($bankList, $Getbank->fields(name));
		array_push($bankVal, $Getbank->fields(ID));
		$Getbank->MoveNext();
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

if (@$tabb == 1) {
	$a = 1;
	$FormLabel[$a]   	= "&nbsp;";
	$FormElement[$a] 	= "test";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
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
	$FormLabel[$a]   	= "Nama Lengkap";
	$FormElement[$a] 	= "name";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "40";
	$FormLength[$a]  	= "70";

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
	$FormLabel[$a]   	= "Email";
	$FormElement[$a] 	= "email";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "30";
	$FormLength[$a]  	= "50";

	$a++;
	$FormLabel[$a]   	= "ID Pengguna";
	$FormElement[$a] 	= "loginID";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "15";
	$FormLength[$a]  	= "10";

	$a++;
	$FormLabel[$a]   	= "Tanggal Menjadi Anggota";
	$FormElement[$a] 	= "approvedDate";
	$FormType[$a]	  	= "date";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "10";

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
	$FormLabel[$a]   	= "Kartu Identitas<br/><b>*Tidak Ada (-)</b>";
	$FormElement[$a] 	= "newIC";
	$FormType[$a]	  	= "textx";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "12";

	$a++;
	$FormLabel[$a]   	= "Tanggal Lahir";
	$FormElement[$a] 	= "dateBirth";
	$FormType[$a]	  	= "date";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "10";

	$a++;
	$FormLabel[$a]   	= "Jenis Pekerjaan";
	$FormElement[$a] 	= "jobType";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= $jobTypeList;
	$FormDataValue[$a]	= $jobTypeVal;
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Jabatan Pekerjaan";
	$FormElement[$a] 	= "job";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "30";
	$FormLength[$a]  	= "50";

	$a++;
	$FormLabel[$a]   	= "Cabang / Zona";
	$FormElement[$a] 	= "departmentIDd";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Alamat Tempat Tinggal";
	$FormElement[$a] 	= "address";
	$FormType[$a]	  	= "textarea";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "30";
	$FormLength[$a]  	= "3";

	$a++;
	$FormLabel[$a]   	= "Alamat Cabang";
	$FormElement[$a] 	= "addressSurat";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "30";
	$FormLength[$a]  	= "3";

	$a++;
	$FormLabel[$a]   	= "Kode Pos";
	$FormElement[$a] 	= "postcode";
	$FormType[$a]	  	= "textx";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "10";
	$FormLength[$a]  	= "5";

	$a++;
	$FormLabel[$a]   	= "* Nomor Telepon<br><b>Cth: 6011XXXXXXXX</b>";
	$FormElement[$a] 	= "mobileNo";
	$FormType[$a]	  	= "textx";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "20";
	$FormLength[$a]  	= "15";

	$a++;
	$FormLabel[$a]   	= "Kota Tempat Tinggal";
	$FormElement[$a] 	= "city";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "30";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Nomor Karyawan";
	$FormElement[$a] 	= "staftNo";
	$FormType[$a]	  	= "textx";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "10";
	$FormLength[$a]  	= "10";

	$a++;
	$FormLabel[$a]   	= "Provinsi Tempat Tinggal";
	$FormElement[$a] 	= "stateID";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= $stateList;
	$FormDataValue[$a]	= $stateVal;
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Suku Bangsa";
	$FormElement[$a] 	= "raceID";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= $raceList;
	$FormDataValue[$a]	= $raceVal;
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Agama";
	$FormElement[$a] 	= "religionID";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= $religionList;
	$FormDataValue[$a]	= $religionVal;
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Status Pernikahan";
	$FormElement[$a] 	= "maritalID";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('Belum menikah', 'Menikah', 'Janda/Duda');
	$FormDataValue[$a]	= array('0', '1', '2');
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Jenis Kelamin";
	$FormElement[$a] 	= "sex";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('Laki-Laki', 'Perempuan');
	$FormDataValue[$a]	= array('0', '1');
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Status Piutang Bermasalah";
	$FormElement[$a] 	= "statusHL";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('Ya', 'Tidak');
	$FormDataValue[$a]	= array('1', '0');
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Status Daftar Hitam";
	$FormElement[$a] 	= "BlackListID";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('Ya', 'Tidak');
	$FormDataValue[$a]	= array('1', '0');
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Daftar Hitam Dividen";
	$FormElement[$a] 	= "BlackListDIV";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('Ya', 'Tidak');
	$FormDataValue[$a]	= array('1', '0');
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]   	= "Jabatan dalam Koperasi";
	$FormElement[$a] 	= "jawkopID";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('Biasa', 'Perwakilan');
	$FormDataValue[$a]	= array('0', '1');
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
}

// if (@$tabb == 2) {
// 	$a = 1;
// 	$FormLabel[$a]   	= "Bayaran Pendaftaran (RM)";
// 	$FormElement[$a] 	= "totPay";
// 	$FormType[$a]	  	= "textx";
// 	$FormData[$a]   	= "";
// 	$FormDataValue[$a]	= "";
// 	$FormCheck[$a]   	= array();
// 	$FormSize[$a]    	= "10";
// 	$FormLength[$a]  	= "10";

// 	$a++;
// 	$FormLabel[$a]   	= "* Yuran Bulanan (RM)";
// 	$FormElement[$a] 	= "monthFee";
// 	$FormType[$a]	  	= "textx";
// 	$FormData[$a]   	= "";
// 	$FormDataValue[$a]	= "";
// 	$FormCheck[$a]   	= array();
// 	$FormSize[$a]    	= "10";
// 	$FormLength[$a]  	= "10";

// 	$a++;
// 	$FormLabel[$a]   	= "* Deposit Khas (RM)";
// 	$FormElement[$a] 	= "monthDepo";
// 	$FormType[$a]	  	= "textx";
// 	$FormData[$a]   	= "";
// 	$FormDataValue[$a]	= "";
// 	$FormCheck[$a]   	= array();
// 	$FormSize[$a]    	= "10";
// 	$FormLength[$a]  	= "10";

// 	$a++;
// 	$FormLabel[$a]   	= "* Syer Bulanan (RM)";
// 	$FormElement[$a] 	= "unitShare";
// 	$FormType[$a]	  	= "textx";
// 	$FormData[$a]   	= "";
// 	$FormDataValue[$a]	= "";
// 	$FormCheck[$a]   	= array();
// 	$FormSize[$a]    	= "10";
// 	$FormLength[$a]  	= "10";
// }

if (@$tabb == 4) {
	$a = 1;
	$FormLabel[$a]   	= "Nomor Anggota";
	$FormElement[$a] 	= "saksi1";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "10";
	$FormLength[$a]  	= "10";
}

// if (@$tabb == 5) {
// 	$a++;
// 	$FormLabel[$a]   	= "Nombor Akaun Bank<br>(XXXXXXXXXXXXXXXX)";
// 	$FormElement[$a] 	= "accTabungan";
// 	$FormType[$a]	  	= "text";
// 	$FormData[$a]   	= "";
// 	$FormDataValue[$a]	= "";
// 	$FormCheck[$a]   	= array();
// 	$FormSize[$a]    	= "30";
// 	$FormLength[$a]  	= "25";

// 	$a++;
// 	$FormLabel[$a]   	= "Nama Bank";
// 	$FormElement[$a] 	= "bankID";
// 	$FormType[$a]	  	= "select";
// 	$FormData[$a]   	= $bankList;
// 	$FormDataValue[$a]	= $bankVal;
// 	$FormCheck[$a]   	= array();
// 	$FormSize[$a]    	= "1";
// 	$FormLength[$a]  	= "1";
// }

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$strMember = "SELECT a.*,b.* FROM users a, userdetails b WHERE a.userID = '" . $pk . "' AND a.userID = b.userID";
$GetMember = &$conn->Execute($strMember);
//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->


$SQLID = "SELECT * FROM userdetails WHERE userID = '" . $pk . "'";
$GetLoansIDs =  &$conn->Execute($SQLID);
$statusHLID = $GetLoansIDs->fields('statusHL');

if ($SubmitForm <> "") {

	$sqlLoan = "SELECT * FROM loans WHERE userID = '" . $pk . "' AND status = 3 ";
	$GetLoans =  &$conn->Execute($sqlLoan);
	$kira = $GetLoans->RowCount();
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
	if (@$tabb == 1) {
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

		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE userdetails SET " .
			" approvedDate=" . tosql($approvedDate, "Text") .
			", staftNo=" . tosql($staftNo, "Text") .
			", newIC=" . tosql($newIC, "Text") .
			", dateBirth=" . tosql($dateBirth, "Text") .
			", sex=" . tosql($sex, "Number") .
			", raceID=" . tosql($raceID, "Number") .
			", religionID=" . tosql($religionID, "Number") .
			", maritalID=" . tosql($maritalID, "Number") .
			", BlackListID=" . tosql($BlackListID, "Number") .
			", statusHL=" . tosql($statusHL, "Number") .
			", job=" . tosql($job, "Text") .
			// ", grossPay=" . tosql($grossPay, "Number") .
			", jawkopID=" . tosql($jawkopID, "Number").
			", address=" . tosql($address, "Text") .
			", city=" . tosql($city, "Text") .
			", postcode=" . tosql($postcode, "Text") .
			", stateID=" . tosql($stateID, "Number") .
			", mobileNo=" . tosql($mobileNo, "Text") .
			", addressSurat=" . tosql($addressSurat, "Text") .
			", citySurat=" . tosql($citySurat, "Text") .
			", postcodeSurat=" . tosql($postcodeSurat, "Text") .
			", stateIDSurat=" . tosql($stateIDSurat, "Number") .
			", departmentID=" . tosql($dept, "Number") .
			", saksi1=" . tosql($saksi1, "Text") .
			// ", ptjID=" . tosql($ptjID, "Number") .
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");

		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);

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

		$activity = "Memperbarui Informasi Anggota";
		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			" VALUES ('Mengemaskini Maklumat Peribadi Anggota - $pk', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '1')";
		$rs = &$conn->Execute($sqlAct);

		alert("Informasi anggota telah diperbarui dalam sistem.");
		gopage("?vw=memberEdit&mn=905&pk=" . $pk . "&tabb=1", 1000);
	} else if (@$tabb == 2) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");

		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE userdetails SET " .
			"totPay=" . tosql($totPay, "Number") .
			", monthFee=" . tosql($monthFee, "Number") .
			", monthDepo=" . tosql($monthDepo, "Number") .
			", unitShare=" . tosql($unitShare, "Number") .
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");

		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);

		$activity = "Mengemaskini Maklumat Anggota";
		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			" VALUES ('Mengemaskini Maklumat Peribadi Anggota - $pk', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '1')";
		$rs = &$conn->Execute($sqlAct);

		alert("Maklumat anggota telah dikemaskinikan ke dalam sistem.");
		gopage("?vw=memberEdit&mn=905&pk=" . $pk . "&tabb=2", 1000);
	} else if (@$tabb == 4) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE userdetails SET " .
			"saksi1=" . tosql($saksi1, "Text") .
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");

		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);

		$activity = "Mengemaskini Maklumat Anggota";
		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			" VALUES ('Mengemaskini Maklumat Peribadi Anggota -$pk', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '1')";
		$rs = &$conn->Execute($sqlAct);

		alert("Maklumat anggota telah dikemaskinikan ke dalam sistem.");
		gopage("?vw=memberEdit&mn=905&pk=" . $pk . "&tabb=4", 1000);
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
if (!isset($pic)) $pic = dlookup("userloandetails", "gaji_img", "userID=" . tosql($pk, "Text"));
if (!isset($picjwtn)) $picjwtn = dlookup("userloandetails", "jwtn_img", "userID=" . tosql($pk, "Text"));
if (!isset($picic)) $picic = dlookup("userloandetails", "ic_img", "userID=" . tosql($pk, "Text"));
if (!isset($picccris)) $picccris = dlookup("userloandetails", "ccris_img", "userID=" . tosql($pk, "Text"));
if (!isset($picother)) $picother = dlookup("userloandetails", "lain_img", "userID=" . tosql($pk, "Text"));

print '
<form name="MyForm" action="" method=post>
<h5 class="card-title">' . strtoupper($title) . '</h5>
<div class="mb-3 row">';
//(* Menunjukkan anggota dibenarkan mengubah maklumat.)
//--- Begin : Looping to display label -------------------------------------------------------------

?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<a href="<?php print $sFileName; ?>&tabb=1" class="nav-link <?php if (@$tabb == 1) {
																		print "active";
																	} ?>" id="home-tab" aria-controls="home" aria-selected="true">PROFIL</a>
	</li>
	<li class="nav-item" role="presentation">
		<a href="<?php print $sFileName; ?>&tabb=6" class="nav-link <?php if (@$tabb == 6) {
																		print "active";
																	} ?>" id="profile-tab" aria-controls="profile" aria-selected="false">DOKUMEN</a>
	</li>
	<li class="nav-item" role="presentation">
		<a href="<?php print $sFileName; ?>&tabb=3" class="nav-link <?php if (@$tabb == 3) {
																		print "active";
																	} ?>" id="profile-tab" aria-controls="profile" aria-selected="false">PENAMA</a>
	</li>
	<li class="nav-item" role="presentation">
		<a href="<?php print $sFileName; ?>&tabb=5" class="nav-link <?php if (@$tabb == 5) {
																		print "active";
																	} ?>" id="profile-tab" aria-controls="profile" aria-selected="false">INFORMASI BANK</a>
	</li>
	<li class="nav-item" role="presentation">
		<a href="<?php print $sFileName; ?>&tabb=2" class="nav-link <?php if (@$tabb == 2) {
																		print "active";
																	} ?>" id="profile-tab" aria-controls="profile" aria-selected="false">POTONGAN GAJI</a>
	</li>
	<li class="nav-item" role="presentation">
		<a href="<?php print $sFileName; ?>&tabb=4" class="nav-link <?php if (@$tabb == 4) {
																		print "active";
																	} ?>" id="profile-tab" aria-controls="profile" aria-selected="false">PENGUSUL</a>
	</li>


</ul>
<?php
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;
	if ($i == 1) print '<div>&nbsp;</div>';
	if ($i == 9) {
		print '<div class="card-header mb-3">DETAIL PERIBADI';

		if ($pic) {
			print '&nbsp;<input type=button value="Paparan Slip Gaji" class="btn btn-sm btn-outline-danger" onClick=window.open(\'upload_gaji/' . $pic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
		';
		}

		if ($picic) {
			print '&nbsp;<input type=button value="Paparan IC" class="btn btn-sm btn-outline-danger" onClick=window.open(\'upload_ic/' . $picic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
		';
		}

		if ($picjwtn) {
			print '&nbsp;<input type=button value="Paparan Bank Statement" class="btn btn-sm btn-outline-danger" onClick=window.open(\'upload_ccris/' . $picjwtn . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
		';
		}

		if ($picccris) {
			print '&nbsp;<input type=button value="Paparan Bank Statement" class="btn btn-sm btn-outline-danger" onClick=window.open(\'upload_ccris/' . $picccris . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
		';
		}

		if ($picother) {
			print '&nbsp;<input type=button value="Paparan Bank Statement" class="btn btn-sm btn-outline-danger" onClick=window.open(\'upload_ccris/' . $picother . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
		';
		}
		print '</div>';
	}
	if ($i == 29) print '<div class="card-header mb-3">BAYARAN</div>';

	$addr = str_replace("<pre>", "", $GetMember->fields('w_address1'));
	$addr1 = str_replace("</pre>", "", $addr);

	if ($i == 33) {
		print '<div class="card-header mb-3">PENAMA (18 TAHUN KE ATAS)</div>';

		print '<div class="row m-1 mt-3">
                                                    <div class="col-md-3">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom032">*Nama Penama</label>
                                                            <input type="text" class="form-control" name="w_name1" value="' . tohtml($GetMember->fields('w_name1')) . '" size=30 maxlength=50 id="validationCustom032">                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom03">Kartu Identitas</label>
                                                            <input type="text" class="form-control" name="w_ic1" value="' . tohtml($GetMember->fields('w_ic1')) . '" size=15 maxlength=14 id="validationCustom03" placeholder="Tiada (-)">                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom04">Nombor Telefon</label>
                                                            <input type="text" class="form-control" name="w_contact1" value="' . tohtml($GetMember->fields('w_contact1')) . '" size=15 maxlength=15 id="validationCustom04" placeholder="(6XXXXXXXXXX)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom05">Hubungan Penama</label>      
                                                            <input type="text" class="form-control" name="w_relation1" value="' . tohtml($GetMember->fields('w_relation1')) . '" size=15 maxlength=15 id="validationCustom05">         
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom06">Alamat Tempat Tinggal</label>
                                                            <textarea class="form-control" cols=30 rows=3 wrap="hard" name="w_address1" id="validationCustom06">' . $addr1 . '</textarea>
                                                        </div>
                                                    </div>
                                                </div>';


		print	'			</table>
					</td>
			   </tr>
		       <div class="card-header mt-3">PENCADANG (NOMBOR ANGGOTA YANG TELAH BERDAFTAR BERSAMA KOPERASI)</div>';
	}


	if ($i == 35) print '<div class="card-header mt-3">MAKLUMAT BANK</div>';

	if ($cnt == 1) print '<div class="m-1 row">';
	print '<label class="col-md-2 col-form-label">' . $FormLabel[$i];

	print ' </label>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<div class="col-md-4 bg-danger">';
	else
		print '<div class="col-md-4">';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetMember->fields($FormElement[$i]));
	if ($FormType[$i] == 'textarea') {
		$strFormValue = str_replace("<pre>", "", $GetMember->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>", "", $strFormValue);
	}

	if ($i == 14) {
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

	if (@$tabb == 1) {
		if ($i == 1) {
			if (!isset($picuser)) $picuser = $GetMember->fields('picture');
			echo '<div style="display: flex; align-items: flex-end; position: relative;">';
			if ($picuser) {
				print '<img src="upload_images/' . $picuser . '" alt="User Picture" height="150">';
			} else {
				echo '<img src="images/user.png" alt="User Picture" height="150">';
			}
		}
	}

	if ($i == 13) {
		if (!isset($dept)) $dept = $GetMember->fields('departmentID');
		print '<select name="dept"  class="form-selectx">
				<option value="">- Semua -';
		for ($j = 0; $j < count($deptList); $j++) {
			print '	<option value="' . $deptVal[$j] . '" ';
			if ($dept == $deptVal[$j]) print ' selected';
			print '>' . $deptList[$j];
		}
		print '</select>&nbsp;';
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '</div>';
	if ($cnt == 0) print '</div>';
}

// ----------------------------------------- Bahagian potongan gaji -----------------------------------------
if (@$tabb == 2) {

	$sSQL = "select * from potbulan 	 
				 WHERE  userID = " . tosql($pk, "Text") . "
				 AND status IN (1)";

	$rs = &$conn->Execute($sSQL);

	$sSQL2 = "SELECT	DISTINCT a.*, b.*
				  FROM 	users a, userdetails b
				  WHERE a.UserID =" . tosql($pk, "Text") . "
				  AND a.UserID = b.UserID";

	$rs1 = &$conn->Execute($sSQL2);

	print '
			<div class="table-responsive" style="overflow-x: auto;">
			<table class="table table-striped table-sm mt-3" style="table-layout: auto; width: 100%;">
				<tr class="table-primary">
					<td nowrap align="center" style="white-space: nowrap; text-align: center;"><b>Bil</b></td>
					<td nowrap align="center" style="white-space: nowrap; text-align: center;"><b>Mula Potongan<br/>(Tahun/Bulan)</b></td>
					<td nowrap align="center" style="white-space: nowrap; text-align: center;"><b>Akhir Potongan<br/>(Tahun/Bulan)</b></td>
					<td nowrap align="left" style="white-space: nowrap;"><b>Jenis/Kod Potongan</b></td>
					<td nowrap align="right" style="text-align: right;"><b>Potongan<br/>Bulanan (RM)</b></td>
					<td nowrap align="center" style="white-space: nowrap; text-align: center;"><b>Bond /<br/>Rujukan</b></td>
					<td nowrap align="center" style="white-space: nowrap; text-align: center;"><b>PTJ</b></td>
					<td nowrap align="center" style="white-space: nowrap; text-align: center;"><b>Status</b></td>	  
				</tr>';

	$jumlah = array(
		'aktif' => array(),
		'tamat' => array(),
		'standby' => array()
	);

	if ($rs->RowCount() <> 0) {
		$count = 1;
		while (!$rs->EOF) {
			$sSQL3 = "select * from general
					WHERE  ID = " . $rs->fields(loanType) . "
					ORDER BY ID";
			$rs3 = &$conn->Execute($sSQL3);

			$monthFee 		= $rs1->fields(monthFee);
			$syerbulan 		= $rs1->fields(unitShare);

			$yearStart = $rs->fields['yearStart'];
			$monthStart = $rs->fields['monthStart'];
			$monthStart1 = str_pad($monthStart, 2, '0', STR_PAD_LEFT);
			$yrmthStart = $yearStart . $monthStart1;

			$lastyrmthPymt = $rs->fields(lastyrmthPymt);

			$category = dlookup("general", "category", "ID=" . tosql($rs->fields(loanType), "Number"));

			//kategori pembiayaan
			if ($category == "C") {
				$c_Deduct = dlookup("general", "c_Deduct", "ID=" . tosql($rs->fields(loanType), "Number"));
				$priority = dlookup("general", "priority", "ID=" . tosql($c_Deduct, "Number"));
			} else {
				$priority = dlookup("general", "priority", "ID=" . tosql($rs->fields(loanType), "Number"));
			}

			// Tambah sebulan
			$nextMonthTimestamp = strtotime("+1 month", strtotime($yrmthNow . "01")); // Tambah 1 bulan
			$yrmthNext = date("Ym", $nextMonthTimestamp); // Format kembali ke format Y-m

			//cek kalau dia pembiayaan, dia akan cek pulak yrmth dengan lastyrmthpymt tu 
			if ($category == "C") {
				if ($rs->fields(yrmth) == $yrmthNext) {
					$status = '<div class="text-warning"><b>Standby</b></div>';
					$jumlah['standby'][] = $rs->fields['jumBlnP'];
				} else {
					if ($lastyrmthPymt >= $yymm) {
						$status = '<div class="text-primary"><b>Aktif</b></div>';
						$jumlah['aktif'][] = $rs->fields['jumBlnP'];
					} else {
						$status = '<div class="text-danger"><b>Tamat</b></div>';
						$jumlah['tamat'][] = $rs->fields['jumBlnP'];
					}
				}
			} else if ($lastyrmthPymt >= $yymm) {
				$status = '<div class="text-primary"><b>Aktif</b></div>';
				$jumlah['aktif'][] = $rs->fields['jumBlnP'];
			} else {
				$status = '<div class="text-danger"><b>Tamat</b></div>';
				$jumlah['tamat'][] = $rs->fields['jumBlnP'];
			}

			print '
			<tr>
			  <td class="Data" align="center">' . $count . '</td>
			  <td class="Data" align="center">' . $yrmthStart . '</td>
			  <td class="Data" align="center">' . $rs->fields(lastyrmthPymt) . '</td>';
			if ($category == "C") {
				print '<td class="Data"><a href="' . $sFileRef . '&ID=' . tohtml($rs->fields(ID)) . '">' . $rs3->fields(name) . ' - ' . $rs3->fields(code) . '</a></td>';
			} else {
				print '<td class="Data">' . $rs3->fields(name) . ' - ' . $rs3->fields(code) . '</td>';
			}
			print '
			  <td class="Data" align="right" >' . number_format($rs->fields(jumBlnP), 2) . '</td>
			  <td class="Data" nowrap align="center">' . $rs->fields(bondNo) . '</td>
			  <td class="Data" align="center">' . dlookup("general", "name", "ID=" . tosql($rs->fields(ptjID), "Text")) . '</td>	
			  <td class="Data" nowrap align="center">' . $status . '</td>
			</tr>';
			$count++;
			$rs->MoveNext();
		}
	} else {
		print '
							<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt;" bgcolor="FFFFFF">
								<td colspan="8" align="center"><b>- Tidak Ada Data -</b></td>
							</tr>';
	}

	print '
</table>
<div><hr class="1px"></div>
		  <div>Jumlah Potongan Aktif : <b>RM ' . number_format(array_sum($jumlah['aktif']), 2) . '</b></div>
		  <div>Jumlah Potongan Tamat : <b>RM ' . number_format(array_sum($jumlah['tamat']), 2) . '</b></div>
		  <div><hr class="1px"></div>
		  <div><b>Jumlah Potongan : RM ' . number_format(array_sum($jumlah['aktif']) + array_sum($jumlah['tamat']), 2) . '</b></div>
		  </div>';
}

// ----------------------------------------- Bahagian penama -----------------------------------------
if (@$tabb == 3) {
	$sSQL = "SELECT * FROM nominee WHERE refer = " . tosql($pk, "Text") . " ORDER BY ID";
	$rs = $conn->Execute($sSQL);

	print '
        <div style="padding-left: 15px; padding-right: 15px;" class="table-responsive"><br/>
        <form id="Edittrans" name="Edittrans" method="post" action="' . $sActionFileName . '">
        <input type="hidden" name="IDtype" value="' . htmlspecialchars($_REQUEST['IDtype']) . '">
        <input type="hidden" name="ID" value="' . htmlspecialchars($ID) . '">
        <input type="hidden" name="edit" value="1">
		<div><input type="button" class="btn btn-primary" value="Tambah Penama" onclick="window.open(\'addNominee.php?userID=' . $pk . '\', \'newwindow\', \'top=\' + ((window.innerHeight / 2) - (500 / 2)) + \',left=\' + ((window.innerWidth / 2) - (950 / 2)) + \',width=950,height=200,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');"></div> 
		<div class="mt-3">
			<i class="text-info mdi mdi-information-outline"></i>&nbsp;
			<span class="text-info small">Penama mestilah 18 tahun keatas.</span>
		</div>
		<div>
			<i class="text-info mdi mdi-information-outline"></i>&nbsp;
			<span class="text-info small">Penama adalah pemegang amanah.</span>
		</div>
		<div>
			<i class="text-info mdi mdi-information-outline"></i>&nbsp;
			<span class="text-info small">Peratusan penama hanya untuk <i>non-muslim</i> sahaja, sekiranya orang islam tidak diambil kira.</span>
		</div>
		<div>
            <table class="table table-sm table-striped mt-1">
                <tr class="table-primary">
                    <td align="center"><b>Bil</b></td>
                    <td align="left"><b>Nama Penama</b></td>
					<td align="center"><b>Kartu Identitas</b></td>
                    <td align="center"><b>Nombor Telefon</b></td>
                    <td align="left" width="35%"><b>Alamat</b></td>
					<td align="center"><b>Peratus (%)</b></td>
                    <td align="center" colspan="3"><b>&nbsp;</b></td>
                </tr>';

	if ($rs && $rs->RecordCount() > 0) {
		$count = 1;
		while (!$rs->EOF) {
			print '
                    <tr>
                        <td class="Data" align="center">' . $count . '</td>
                        <td class="Data" align="left">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input class="form-control-sm" name="name" value="' . htmlspecialchars($rs->fields['name']) . '">';
			} else {
				print htmlspecialchars($rs->fields['name']);
			}

			print '</td>
	<td class="Data" align="center">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input maxlength="12" class="form-control-sm" name="newIC" value="' . htmlspecialchars($rs->fields['newIC']) . '">';
			} else {
				print htmlspecialchars($rs->fields['newIC']);
			}

			print '</td>
			<td class="Data" align="center">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input maxlength="12" class="form-control-sm" name="mobileNo" value="' . htmlspecialchars($rs->fields['mobileNo']) . '">';
			} else {
				print htmlspecialchars($rs->fields['mobileNo']);
			}
			print '</td>
		<td class="Data" align="left">';
			if ($IDtype == $rs->fields(ID)) {
				print '<textarea cols="50" rows="4" class="form-control-sm" name="address">' . htmlspecialchars($rs->fields['address']) . '</textarea>';
			} else {
				print htmlspecialchars($rs->fields['address']);
			}
			print '</td>
			<td class="Data" align="center">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input maxlength="12" class="form-control-sm" name="percent" value="' . htmlspecialchars($rs->fields['percent']) . '">';
			} else {
				print htmlspecialchars($rs->fields['percent']);
			}
			print '</td>
			<td class="Data" align="center">
				<a href="' . $sFileName . '&tabb=3&IDtype=' . $rs->fields['ID'] . '&code=2" title="kemaskini">
					<i class="mdi mdi-lead-pencil text-primary" style="font-size: 1.4rem;"></i>
				</a>
			</td>
			<td class="Data" align="center">
				<a href="' . $sFileName . '&tabb=3&IDtype=' . $rs->fields['ID'] . '&code=1" title="Hapus" onClick="if(!confirm(\'Adakah ada pasti untuk hapus file ini?\')) {return false;}">
					<i class="fas fa-trash-alt text-danger" style="font-size: 1.1rem; position: relative; top: 8px;"></i>
				</a>
			</td>
			<td class="Data" align="center">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input type="submit" class="btn btn-sm btn-info" onClick="if(!confirm(\'Apakah Anda yakin ingin memperbarui file ini?\')) {return false;}" name="edit" id="edit" value="edit">';
			}
			print '</td>
		</tr>';
			$count++;
			$rs->MoveNext();
		}
	} else {
		print '
	<tr>
		<td colspan="7" align="center"><b>- Tiada Rekod -</b></td>
	</tr>';
	}

	print '</table>
</form>
</div>';
}
// ----------------------------------------- Tutup Bahagian penama -----------------------------------------

// ----------------------------------------- Bahagian bank -----------------------------------------
if (@$tabb == 5) {
	$sSQL = "SELECT * FROM bank WHERE refer = " . tosql($pk, "Text") . " ORDER BY ID";
	$rs = $conn->Execute($sSQL);

	print '
        <div style="padding-left: 15px; padding-right: 15px;" class="table-responsive"><br/>
        <form id="Edittrans" name="Edittrans" method="post" action="' . $sActionFileName1 . '">
        <input type="hidden" name="IDtype" value="' . htmlspecialchars($_REQUEST['IDtype']) . '">
        <input type="hidden" name="ID" value="' . htmlspecialchars($ID) . '">
        <input type="hidden" name="edit1" value="2">
		<div>
			<input type="button" class="btn btn-primary waves-effect waves-light" value="Tambah Bank" onclick="window.open(\'addBank.php?userID=' . $pk . '\', \'newwindow\', \'top=\' + ((window.innerHeight / 2) - (500 / 2)) + \',left=\' + ((window.innerWidth / 2) - (950 / 2)) + \',width=950,height=200,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
			<!--&nbsp;<input type="button" class="btn btn-info waves-effect waves-light" value="Bank Utama" onclick="setBankPriority(); return false;">-->
   		</div>
		<div>
            <table class="table table-sm table-striped mt-3">
                <tr class="table-primary">
                    <td align="center"><b>Bil</b></td>
                    <td align="left"><b>Nama Bank</b></td>
					<td align="center"><b>Nomor Akun Bank</b></td>
                    <td align="center" colspan="3"><b>&nbsp;</b></td>
                </tr>';

	if ($rs && $rs->RecordCount() > 0) {
		$count = 1;
		while (!$rs->EOF) {
			$isChecked = $rs->fields('priority') == 1 ? 'checked' : '';
			$priority = $rs->fields('priority') == 1 ? '<span class="badge bg-success">Primary</span>' : '';

			print '
						<tr>
							<td class="Data" align="center">' . $count . '</td>
							<td class="Data" align="left">';
			if ($IDtype == $rs->fields['ID']) {
				print '<select class="form-control-sm" name="bankID" required>';
				for ($i = 0; $i < count($bankList); $i++) {
					$selected = ($bankVal[$i] == $rs->fields['bankID']) ? 'selected' : '';
					print '<option value="' . htmlspecialchars($bankVal[$i]) . '" ' . $selected . '>' . htmlspecialchars($bankList[$i]) . '</option>';
				}
				print '</select>';
			} else {
				print '<!--input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($rs->fields('ID')) . '" ' . $isChecked . '>&nbsp;-->' . dlookup("general", "name", "ID=" . tosql($rs->fields['bankID'], "Text")) . '&nbsp;' . $priority;
			}

			print '</td>
						<td class="Data" align="center">';
			if ($IDtype == $rs->fields['ID']) {
				print '<input maxlength="12" class="form-control-sm" name="accTabungan" value="' . htmlspecialchars($rs->fields['accTabungan']) . '">';
			} else {
				print htmlspecialchars($rs->fields['accTabungan']);
			}

			print '</td>
						<td class="Data" align="center">
							<a href="' . $sFileName . '&tabb=5&IDtype=' . $rs->fields['ID'] . '&code=4" title="kemaskini">
								<i class="mdi mdi-lead-pencil text-primary" style="font-size: 1.4rem;"></i>
							</a>
						</td>
						<td class="Data" align="center">
							<a href="' . $sFileName . '&tabb=5&IDtype=' . $rs->fields['ID'] . '&code=3" title="Hapus" onClick="if(!confirm(\'Apakah Anda yakin ingin menghapus file ini?\')) {return false;}">
								<i class="fas fa-trash-alt text-danger" style="font-size: 1.1rem; position: relative; top: 8px;"></i>
							</a>
						</td>
						<td class="Data" align="center">';
			if ($IDtype == $rs->fields['ID']) {
				print '<input type="submit" class="btn btn-sm btn-info" onClick="if(!confirm(\'Apakah Anda yakin ingin memperbarui file ini?\')) {return false;}" id="edit1" name="edit1" value="edit">';
			}
			print '</td>
					</tr>';
			$count++;
			$rs->MoveNext();
		}
	} else {
		print '
					<tr>
						<td colspan="7" align="center"><b>- Tidak Ada Data -</b></td>
					</tr>';
	}

	print '</table>
</form>
</div>';
}

if (@$tabb == 6) {
	$IDName = get_session("Cookie_userName");

	if (!isset($picgaji)) $picgaji = dlookup("userloandetails", "gaji_img", "userID=" . tosql($pk, "Text"));
	$Gambar = "upload_gaji/" . $pic;
	if (!isset($picjwtn)) $picjwtn = dlookup("userloandetails", "jwtn_img", "userID=" . tosql($pk, "Text"));
	$Gambarjwtn = "upload_jwtn/" . $pic;
	if (!isset($picic)) $picic = dlookup("userloandetails", "ic_img", "userID=" . tosql($pk, "Text"));
	$Gambaric = "upload_ic/" . $pic;
	if (!isset($picccris)) $picccris = dlookup("userloandetails", "ccris_img", "userID=" . tosql($pk, "Text"));
	$Gambarccris = "upload_CCRIS/" . $pic;
	if (!isset($picother)) $picother = dlookup("userloandetails", "lain_img", "userID=" . tosql($pk, "Text"));
	$Gambarother = "upload_lain/" . $pic;

	if (($IDName == 'superadmin') or ($IDName == 'admin')) {
		print '
	<div class="mt-3">
		<input value="Perbarui Gaji" class="btn btn-md btn-primary waves-effect waves-light" onClick="window.location.href=\'?vw=biayaEditA&mn=905&pk=' . $pk . '\'"/>
	</div>';
	}

	print '
	<div class="card-header mt-3 mb-3">SILAKAN UNGGAH INFORMASI YANG BERKAITAN &nbsp;&nbsp;</div>
	<div class="text-danger"><i class="mdi mdi-information-outline"></i> * Wajib Unggah.</div><br/>';

	print '
		<table class="table table-sm table-striped">
		<tr class="table-primary">
			<td><b>Tentang</b></td>
			<!--td><b>Muat Naik Fail</b></td-->
			<td><b>Nama Berkas</b></td>
            <!--td align="center"><b>Hapus</b></td-->
		</tr>

		<tr>
			<td class="align-middle">* Slip Gaji</td>
			<!--td>			
				<div class="col-md-8 col-form-label"><input type="button" class="btn btn-outline-primary" name="GetPicture" value="Muat Naik"  onclick= "Javascript:(window.location.href=\'?vw=uploadwingajiA&mn=' . $mn . '&pk=' . $pk . '\')" /></div>
			</td-->';

	if ($picgaji) {
		print '
					<td class="align-middle"><a href onClick=window.open(\'upload_gaji/' . $picgaji . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Slip Gaji</td>
                    <!--td align="center" style="vertical-align: middle;"><a href="?vw=biayaEditA&mn=' . $mn . '&action=delete&pk=' . $pk . '&type=gaji_img" onClick="return confirm(\'Adakah anda pasti untuk hapus permohonan ini?\')" title="Hapus"><i class="fas fa-trash-alt fa-lg text-danger"></i></a></td-->';
	} else {
		print '<td>&nbsp;</td>
            <!--td>&nbsp;</td-->';
	}
	print '</tr>

		<tr>
			<td class="align-middle">* Kartu Identitas</td>
			<!--td>
				<div class="col-md-8 col-form-label"><input type="button" class="btn btn-outline-primary" name="GetPicture" value="Muat Naik"  onclick= "Javascript:(window.location.href=\'?vw=uploadwinicA&mn=' . $mn . '&pk=' . $pk . '\')"></div>
			</td-->';

	if ($picic) {
		print '
					<td class="align-middle"><a href onClick=window.open(\'upload_ic/' . $picic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan IC</td>
                    <!--td align="center" style="vertical-align: middle;"><a href="?vw=biayaEditA&mn=' . $mn . '&action=delete&pk=' . $pk . '&type=ic_img" onClick="return confirm(\'Adakah anda pasti untuk hapus permohonan ini?\')" title="Hapus"><i class="fas fa-trash-alt fa-lg text-danger"></i></a></td-->';
	} else {
		print '<td>&nbsp;</td>
                            <!--td>&nbsp;</td-->';
	}
	print '</tr>

		<tr>
			<td class="align-middle">Jawatan Tetap</td>
			<!--td>
				<div class="col-md-8 col-form-label"><input type="button" class="btn btn-outline-primary" name="GetPicture" value="Muat Naik"  onclick= "Javascript:(window.location.href=\'?vw=uploadwinjwtnA&mn=' . $mn . '&pk=' . $pk . '\')"></div>
			</td-->';

	if ($picjwtn) {
		print '
					<td class="align-middle"><a href onClick=window.open(\'upload_jwtn/' . $picjwtn . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Pengesahan Jawatan</td>
                    <!--td align="center" style="vertical-align: middle;"><a href="?vw=biayaEditA&mn=' . $mn . '&action=delete&pk=' . $pk . '&type=jwtn_img" onClick="return confirm(\'Adakah anda pasti untuk hapus permohonan ini?\')" title="Hapus"><i class="fas fa-trash-alt fa-lg text-danger"></i></a></td-->';
	} else {
		print '<td>&nbsp;</td>
            <!--td>&nbsp;</td-->';
	}
	print '</tr>

		<tr>
			<td class="align-middle">CCRIS</td>
			<!--td>
			<div class="col-md-8 col-form-label"><input type="button" class="btn btn-outline-primary" name="GetPicture" value="Muat Naik"  onclick= "Javascript:(window.location.href=\'?vw=uploadwinccrisA&mn=' . $mn . '&pk=' . $pk . '\')"></div>
			</td-->';

	if ($picccris) {
		print '
					<td class="align-middle"><a href type=button onClick=window.open(\'upload_CCRIS/' . $picccris . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan CCRIS</td>
                    <!--td align="center" style="vertical-align: middle;"><a href="?vw=biayaEditA&mn=' . $mn . '&action=delete&pk=' . $pk . '&type=ccris_img" onClick="return confirm(\'Adakah anda pasti untuk hapus permohonan ini?\')" title="Hapus"><i class="fas fa-trash-alt fa-lg text-danger"></i></a></td-->';
	} else {
		print '<td>&nbsp;</td>
                            <!--td>&nbsp;</td-->';
	}
	print '</tr>

		<tr>
			<td class="align-middle">Lain-lain</td>
			<!--td>
			<div class="col-md-8 col-form-label"><input type="button" class="btn btn-outline-primary" name="GetPicture" value="Muat Naik"  onclick= "Javascript:(window.location.href=\'?vw=uploadwinlainA&mn=' . $mn . '&pk=' . $pk . '\')"></div>
			</td-->';

	if ($picother) {
		print '
					<td class="align-middle"><a href type=button onClick=window.open(\'upload_CCRIS/' . $picother . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Lain-lain</td>
                    <!--td align="center" style="vertical-align: middle;"><a href="?vw=biayaEditA&mn=' . $mn . '&action=delete&pk=' . $pk . '&type=lain_img" onClick="return confirm(\'Adakah anda pasti untuk hapus permohonan ini?\')" title="Hapus"><i class="fas fa-trash-alt fa-lg text-danger"></i></a></td-->';
	} else {
		print '<td>&nbsp;</td>
                            <!--td>&nbsp;</td-->';
	}
	print '</tr>

		</table>';
}
// ----------------------------------------- Tutup Bahagian bank -----------------------------------------

if ((get_session("Cookie_groupID") == 2)) {
	if (@$tabb == 1 || @$tabb == 4) {
		print '<div class="mb-3 row mt-3">
                <center>
                        <input type="hidden" name="pk" value="' . $pk . '">
						<!--input type="button" class="btn btn-secondary btn-md waves-effect waves-light" value="<<"-->
                        <input type=Submit name=SubmitForm class="btn btn-primary btn-md waves-light waves-effects" value="Perbarui">
                </center>
            </div>';
	}
}

print '</div></form>';
include("footer.php");

print '
<script language="JavaScript">
function setBankPriority() {
    let formElements = document.getElementsByName(\'pk[]\');
    let selectedID = null;
    let selectedCount = 0;

    formElements.forEach(function (checkbox) {
        if (checkbox.checked) {
            selectedID = checkbox.value;
            selectedCount++;
        }
    });

    if (selectedCount === 1) {
        if (confirm(\'Adakah anda pasti ingin menetapkan bank ini sebagai bank utama?\')) {
            let formData = new FormData();
            formData.append(\'action\', \'set_priority\');
            formData.append(\'pk\', selectedID);

            fetch(\'\', {
                method: \'POST\',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.body.innerHTML = data;
            });            
        }
    } else {
        alert(\'Sila pilih satu bank sahaja.\');
    }
}
</script>';
