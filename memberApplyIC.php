<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberApply.php
 *          Date 		: 	21/03/2006
 *          Date Update	: 	2/06/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$Cookie_userID = get_session('Cookie_userID');
$Cookie_userName = get_session("Cookie_userName");
$sFileName		= "memberApplyIC.php";
$sActionFileName = "mainpage.php";
$title     		= "Pengajuan Anggota";

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

//--- Prepare department type
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
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "dump";
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
$FormLabel[$a]   	= "* Nama Lengkap";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Kata Sandi<br> <b>(MINIMUM 6 AKSARA)</b>";
$FormElement[$a] 	= "password";
$FormType[$a]	  	= "password";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "* ID Pengguna";
$FormElement[$a] 	= "loginID";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "* Konfirmasi Kata Sandi";
$FormElement[$a] 	= "password1";
$FormType[$a]	  	= "password";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "* Email<br><b>(PASTIKAN VALID)</b>";
$FormElement[$a] 	= "email";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
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
$FormLabel[$a]   	= "* No KTP Baru<br><b>(XXXXXXXXXXXX)</b>";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Tanggal Lahir <b>(DD/MM/YYYY)</b>";
$FormElement[$a] 	= "dateBirth";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

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
$FormLabel[$a]   	= "* Bagian/Jabatan<br>Cabang";
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
$FormLabel[$a]   	= "Alamat Pekerjaan";
$FormElement[$a] 	= "addressSuratD";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "Kode Pos";
$FormElement[$a] 	= "postcode";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "* Nomor Telepon<br><b>(601XXXXXXXX)</b>";
$FormElement[$a] 	= "mobileNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Kota";
$FormElement[$a] 	= "city";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "No Pekerja<br><b>(SEKIRANYA ADA)</b>";
$FormElement[$a] 	= "staftNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Provinsi";
$FormElement[$a] 	= "stateID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Status Anggota";
$FormElement[$a] 	= "statuskerja";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Kontrak', 'Tetap', 'Sendiri');
$FormDataValue[$a]	= array('0', '1', '2');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Jenis Kelamin";
$FormElement[$a] 	= "sex";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Laki-Laki', 'Perempuan');
$FormDataValue[$a]	= array('0', '1');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Suku Suku Bangsa";
$FormElement[$a] 	= "raceID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $raceList;
$FormDataValue[$a]	= $raceVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Status Pernikahan";
$FormElement[$a] 	= "maritalID";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Belum menikah', 'Menikah', 'Janda/Duda');
$FormDataValue[$a]	= array('0', '1', '2');
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

/*$a++;
$FormLabel[$a]   	= "Status Anggota";
$FormElement[$a] 	= "statuskerja";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Aktif','Dormant');
$FormDataValue[$a]	= array('0','1');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";*/

$a++;
$FormLabel[$a]   	= "Jumlah Biaya Pendaftaran";
$FormElement[$a] 	= "totPay";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Iuran Bulanan";
$FormElement[$a] 	= "monthFee";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Nomor Anggota Pengusul (1)";
$FormElement[$a] 	= "saksi1";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
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
$FormLabel[$a]   	= "Nomor Anggota Pengusul (2)";
$FormElement[$a] 	= "saksi2";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
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
$FormLabel[$a]   	= "* Nomor Rekening Bank<br><b>(XXXXXXXXXXXXXXXX)</b>";
$FormElement[$a] 	= "accTabungan";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "* Nama Bank";
$FormElement[$a] 	= "bankID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $bankList;
$FormDataValue[$a]	= $bankVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if (!$SubmitForm) {
	if ($dateBirth) {
		$getdate = explode("/", $dateBirth);
		$dateBirth = $getdate[2] . '/' . sprintf("%02s",  $getdate[1]) . '/' . sprintf("%02s",  $getdate[0]);
	}
}

if ($SubmitForm <> "") {

	if ($dept == '') {
		array_push($strErrMsg, "departmentIDd");
		print '- <font class=redText>Silakan pilih Pusat Biaya.</font><br />';
	}


	if (strlen($password) < 6) {
		array_push($strErrMsg, "password");
		array_push($strErrMsg, "password1");
		print '- <font class=redText>* Kata Sandi harus terdiri dari minimal ENAM [6] karakter.</font><br />';
	}

	if ($password <> $password1) {
		array_push($strErrMsg, "password");
		array_push($strErrMsg, "password1");
		print '- <font class=redText>* Kata Sandi harus sama dengan Konfirmasi Kata Sandi.</font><br />';
	}
	$GetLogin = ctLogin($loginID);
	if ($GetLogin->RowCount() == 1) {
		array_push($strErrMsg, "loginID");
		print '- <font class=redText>* ID Pengguna sudah ada. Silakan pilih ID pengguna yang lain</font><br />';
	}

	if ($accTabungan) {
		if (!dlookup("userdetails", "newIC", "newIC=" . tosql($newIC, "Text"))) {
			if (dlookup("userdetails", "accTabungan", "accTabungan=" . tosql($accTabungan, "Text")) <> '') {
				array_push($strErrMsg, "accTabungan");
				print '- <font class=redText>* Nomor akun tersebut sudah digunakan.</font><br />';
			}
		}
	}

	if (!$name) {
		print '- <font class=redText>* Nama penerima harus diisi.</font><br />';
		$penama = "errData";
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
	$getdate = explode("/", $dateBirth);
	$dateBirth = $getdate[2] . '/' . sprintf("%02s",  $getdate[1]) . '/' . sprintf("%02s",  $getdate[0]);
	$dateStarted   = substr($dateStarted, 6, 4) . '-' . substr($dateStarted, 3, 2) . '-' . substr($dateStarted, 0, 2);
	if (count($strErrMsg) == "0") {
		$applyDate = date("Y-m-d H:i:s");
		$password = strtoupper(md5($password));
		$sSQLi = "";
		$sSQLi	= "SELECT max( CAST( memberID AS SIGNED INTEGER ) ) + 1 as new FROM userdetails";
		$rsi = &$conn->Execute($sSQLi);
		$userID = $rsi->fields('new');
		$sSQL = "";
		$sSQL	= "INSERT INTO users (" . "userID," . "loginID," . "password," . "email," . "name," . "applyDate)" . "VALUES(" .      tosql($userID, "Text") . ", " .
			tosql($loginID, "Text") . ", " .
			tosql($password, "Text") . ", " .
			tosql($email, "Text") . ", " .
			tosql($name, "Text") . ", " .
			tosql($applyDate, "Text") . ") ";
		$rs = &$conn->Execute($sSQL);

		$memberID = $userID;

		if (!isset($Cookie_userID)) $uid = $userID;
		else $uid =  $Cookie_userID;
		if (!isset($Cookie_userName)) $uname = $loginID;
		else $uname =  $Cookie_userName;
		$activity = "Permohonan Anggota - " . $userID;
		if ($rs) activityLog($sSQL, $activity, $uid, $uname, 1);

		if ($address <> "") $address = '<pre>' . $address . '</pre>';
		if ($w_address <> "") $w_address = '<pre>' . $w_address . '</pre>';
		if ($w_address1 <> "") $w_address1 = '<pre>' . $w_address1 . '</pre>';
		$sSQL = "";
		$sSQL	= "INSERT INTO userdetails 
(
" . "userID, 
" . "memberID, 
" . "staftNo,
" . "newIC, 
" . "dateBirth,
" . "sex, 
" . "raceID, 
" . "religionID, 
" . "maritalID, 
" . "job,  
" . "statuskerja,
" . "accTabungan,
" . "bankID,
" . "address,
" . "city, 
" . "postcode, 
" . "stateID, 
" . "homeNo, 
" . "mobileNo,
" . "departmentID, 
" . "totPay, 
" . "monthFee, 
" . "w_name1, 
" . "w_ic1, 
" . "w_contact1, 
" . "w_relation1, 
" . "w_address1, 
" . "saksi1, 
" . "saksi2, 
" . "addressSuratD, 
" . "updatedBy, 
" . "updatedDate)" .
			" VALUES (" .
			tosql($userID, "Text") . ", " .
			tosql($memberID, "Text") . ", " .
			tosql($staftNo, "Text") . ", " .
			tosql($newIC, "Text") . ", " .
			tosql($dateBirth, "Text") . ", " .
			tosql($sex, "Number") . ", " .
			tosql($raceID, "Number") . ", " .
			tosql($religionID, "Number") . ", " .
			tosql($maritalID, "Number") . ", " .
			tosql($job, "Text") . "," .
			tosql($statuskerja, "Text") . "," .
			tosql($accTabungan, "Text") . ", " .
			tosql($bankID, "Text") . ", " .
			tosql($address, "Text") . ", " .
			tosql($city, "Text") . ", " .
			tosql($postcode, "Number") . ", " .
			tosql($stateID, "Number") . ", " .
			tosql($homeNo, "Text") . ", " .
			tosql($mobileNo, "Text") . ", " .
			tosql($dept, "Number") . ", " .
			tosql($totPay, "Number") . ", " .
			tosql($monthFee, "Number") . ", " .
			tosql($w_name1, "Text") . ", " .
			tosql($w_ic1, "Text") . ", " .
			tosql($w_contact1, "Text") . ", " .
			tosql($w_relation1, "Text") . ", " .
			tosql($w_address1, "Text") . ", " .
			tosql($saksi1, "Text") . ", " .
			tosql($saksi2, "Text") . ", " .
			tosql($addressSuratD, "Text") . ", " .
			tosql($name, "Text") . ", " .
			tosql($applyDate, "Text") . ")";

		$rs = &$conn->Execute($sSQL);
		if (!isset($Cookie_userID)) $uid = $userID;
		else $uid =  $Cookie_userID;
		if (!isset($Cookie_userName)) $uname = $loginID;
		else $uname =  $Cookie_userName;
		$activity = "Permohonan Anggota - " . $userID;
		if ($rs) activityLog($sSQL, $activity, $uid, $uname, 1);

		/*		$sSQL = "";
		$sSQL	= "INSERT INTO userloandetails 
		("."userID,"."memberID,"."ic_img,"."jwtn_img)"." VALUES (".
		          tosql($userID, "Text") . ", " .
		          tosql($memberID, "Text") . ", " .
		          tosql($picture, "Text") . ", " .
		          tosql($picture1, "Text") . ")";*/

		$sSQL = "";
		$sSQL	= "INSERT INTO userloandetails 
		(" . "userID," . "memberID)" . " VALUES (" .
			tosql($userID, "Text") . ", " .
			tosql($memberID, "Text") . ")";


		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Permohonan menjadi anggota telah didaftarkan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
					//window.close();
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
?>
<form name="MyForm" action="<? print $sFileName; ?>" method=post>
	<input type="hidden" name="userID" value="<? print $userID; ?>">
	<input type="hidden" name="loanType" value="<? print $loanType; ?>">
	<input type="hidden" name="pic" value="<? print $pic; ?>">
	<input type="hidden" name="pic1" value="<? print $pic1; ?>">
	<input type="hidden" name="picture" value="<? print $pic; ?>">
	<input type="hidden" name="picture1" value="<? print $pic1; ?>">
	<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" bgcolor="#f0f0f0">
		<tr>
			<td colspan="4" class="Data" valign="top">
				<b class="maroonText"><? print strtoupper($title); ?></b><br />
				<b class="textFont">FORMULIR PENDAFTARAN ANGGOTA (* Wajib diisi untuk pengajuan.)</b><br /><br />
				<b class="redText">SILAKAN MASUKKAN DOKUMEN KARTU IDENTITAS DAN SURAT KETERANGAN JABATAN TERLEBIH DAHULU</b>
			</td>
		</tr>
		<?
		//--- Begin : Looping to display label -------------------------------------------------------------
		for ($i = 1; $i <= count($FormLabel); $i++) {
			$cnt = $i % 2;
			if ($i == 1) print '<tr><td class="headerteal" colspan="4">INFORMASI PENDAFTARAN ID :</td></tr>';
			if ($i == 9) print '<tr><td class="headerteal" colspan="4">A. DETAIL PRIBADI :</td></tr>';
			if ($i == 25) print '<tr><td class="headerteal" colspan="4">B. BIAYA MASUK/IURAN :</td></tr>';
			if ($i == 27) {
				print '<tr><td class="headerteal" colspan="4">C. AHLI WARIS:(18 Tahun Ke atas)</td></tr>';
				print '<tr class="Data">
					<td colspan="4">
						<table width="100%">
							<tr class="DataB">
								<td>&nbsp;</td>	
								<td>Nama</td>
								<td>No KTP</td>
								<td>No Tel</td>
								<td>Hubungan</td>
								<td>Alamat</td>
							</tr>
		       				<tr class="' . $penama . '">
								<td valign="top">*&nbsp;</td>	
								<td valign="top"><input type="text" name="w_name1" value="' . $w_name1 . '" size=30 maxlength=50 class="form-control"></td>
								<td valign="top"><input type="text" name="w_ic1" value="' . $w_ic1 . '" size=15 maxlength=14 class="form-control"></td>
								<td valign="top"><input type="text" name="w_contact1" value="' . $w_contact1 . '" size=15 maxlength=14 class="form-control"></td>
								<td valign="top"><input type="text" name="w_relation1" value="' . $w_relation1 . '" size=15 maxlength=15 class="form-control"></td>
								<td valign="top"><textarea cols=30 rows=3 wrap="hard" name="w_address1" class="form-control">' . $w_address1 . '</textarea></td>
							</tr>';
				print '				</table>
					</td>
			   </tr>
		       <tr><td class="headerteal" colspan="4">D. PENGUSUL: (NOMOR ANGGOTA YANG SUDAH TERDAFTAR DI KOPERASI)</td></tr>';
			}
			if ($i == 31) print '<tr><td class="headerteal" colspan="4">E. INFORMASI BANK :</td></tr>';

			if ($cnt == 1) print '<tr valign=top>';
			print '<td class=Data align=right>' . $FormLabel[$i];
			if (!($i == 1 or $i == 2 or $i == 8 or $i == 28 or $i == 30 or $i == 33 or $i == 34  or $i == 35 or $i == 36)) print ':';
			print ' </td>';
			if (in_array($FormElement[$i], $strErrMsg))
				print '<td class=errdata>';
			else
				print '<td class=Data>';

			if ($i == 10) {
				if ($birth) $strFormValue = '12/45/1922';
			}

			if ($i == 14) {
				if ($dept) $strFormValue = dlookup("general", "b_Address", "ID=" . $dept);
				$strFormValue = str_replace("<pre>", "", $strFormValue);
				$strFormValue = str_replace("</pre>", "", $strFormValue);
				print '<b>' . $strFormValue . '</b>';
			}

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
			//$Gambar= "upload_ic/".$pic;
			//$Gambar1= "upload_jwtn/".$pic1;


			/*	if($i == 1) 

	print '

	<img id="elImage" src="'.$Gambar.'" width="100" height="90">
	<img id="elImage1" src="'.$Gambar1.'" width="100" height="90">


	<input type="button" name="GetPicture" value="Masukkan IC & Pengesahan Jawatan" width="30" height="10" onclick= "Javascript:(window.location.href=\'uploadwinicM.php?userID='.$userID.'\')">';
	print '&nbsp';*/

			if ($i == 12) {
				print '<select name="dept">
				<option value="">- Semua -';
				for ($j = 0; $j < count($deptList); $j++) {
					print '	<option value="' . $deptVal[$j] . '" ';
					if ($dept == $deptVal[$j]) print ' selected';
					print '>' . $deptList[$j];
				}
				print '		</select>&nbsp;';
			}
			//--- End   : Call function FormEntry ---------------------------------------------------------  
			print '&nbsp;</td>';
			if ($cnt == 0) print '</tr>';
		}
		?>
		<tr>
			<td colspan="4" align=center class=Data>
				<input type=Submit name=SubmitForm value=Hantar>
				<input type=Reset name=ResetForm value="Isi semula">
			</td>
		</tr>
	</table>
</form>
<? include("footer.php"); ?>