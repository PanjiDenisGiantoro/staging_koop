<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberApplyL.php
 *          Date 		: 	21/03/2006
 *          Date Update	: 	2/06/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$Cookie_userID = get_session('Cookie_userID');
$Cookie_userName = get_session("Cookie_userName");
$sFileName		= "?vw=memberApplyL";
$sActionFileName = "?vw=uploadIC&dateBirth=$dateBirth&newIC=$newIC&email=$email&mobileNo=$mobileNo";
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
$FormLabel[$a]   	= "* Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Id Pengguna";
$FormElement[$a] 	= "loginID";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "* Kata Sandi<br> (Minimal 6 Karakter)";
$FormElement[$a] 	= "password";
$FormType[$a]	  	= "password";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "* Konfirmasi Kata Sandi";
$FormElement[$a] 	= "password1";
$FormType[$a]	  	= "password";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "* Email";
$FormElement[$a] 	= "email";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Pertanyaan Keamanan (Nama Lengkap Ibu)";
$FormElement[$a] 	= "secret";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "* Kartu Identitas<br><b>Tidak Ada (-)</b>";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "* Tanggal Lahir";
$FormElement[$a] 	= "dateBirth";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

// $a++;
// $FormLabel[$a]   	= "Jawatan Pekerjaan";
// $FormElement[$a] 	= "job";
// $FormType[$a]	  	= "text";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "30";
// $FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "* Cabang / Zona";
$FormElement[$a] 	= "departmentIDd";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

// $a++;
// $FormLabel[$a]   	= "Alamat Kediaman";
// $FormElement[$a] 	= "address";
// $FormType[$a]	  	= "textarea";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "30";
// $FormLength[$a]  	= "3";	

// $a++;
// $FormLabel[$a]   	= "Alamat Cabang / Zona";
// $FormElement[$a] 	= "addressSuratD";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "30";
// $FormLength[$a]  	= "3";	

// $a++;
// $FormLabel[$a]   	= "Poskod Kediaman";
// $FormElement[$a] 	= "postcode";
// $FormType[$a]	  	= "text";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "10";
// $FormLength[$a]  	= "5";

// $a++;
// $FormLabel[$a]   	= "&nbsp;";
// $FormElement[$a] 	= "test";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

// $a++;
// $FormLabel[$a]   	= "Bandar Kediaman";
// $FormElement[$a] 	= "city";
// $FormType[$a]	  	= "text";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "30";
// $FormLength[$a]  	= "25";	

$a++;
$FormLabel[$a]   	= "* Nomor Telepon<br><b>(601XXXXXXXX)</b>";
$FormElement[$a] 	= "mobileNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

// $a++;
// $FormLabel[$a]   	= "Negeri Kediaman";
// $FormElement[$a] 	= "stateID";
// $FormType[$a]	  	= "select";
// $FormData[$a]   	= $stateList;
// $FormDataValue[$a]	= $stateVal;
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

// $a++;
// $FormLabel[$a]   	= "Nombor Pekerja<br>(SEKIRANYA ADA)";
// $FormElement[$a] 	= "staftNo";
// $FormType[$a]	  	= "text";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "30";
// $FormLength[$a]  	= "25";	

// $a++;
// $FormLabel[$a]   	= "Jantina";
// $FormElement[$a] 	= "sex";
// $FormType[$a]	  	= "radio";
// $FormData[$a]   	= array('Lelaki','Perempuan');
// $FormDataValue[$a]	= array('0','1');
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

// $a++;
// $FormLabel[$a]   	= "Bangsa";
// $FormElement[$a] 	= "raceID";
// $FormType[$a]	  	= "select";
// $FormData[$a]   	= $raceList;
// $FormDataValue[$a]	= $raceVal;
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

// $a++;
// $FormLabel[$a]   	= "Status Perkahwinan";
// $FormElement[$a] 	= "maritalID";
// $FormType[$a]	  	= "radio";
// $FormData[$a]   	= array('Bujang','Berkahwin','Janda/Duda');
// $FormDataValue[$a]	= array('0','1','2');
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

// $a++;
// $FormLabel[$a]   	= "Agama";
// $FormElement[$a] 	= "religionID";
// $FormType[$a]	  	= "select";
// $FormData[$a]   	= $religionList;
// $FormDataValue[$a]	= $religionVal;
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

/*$a++;
$FormLabel[$a]   	= "Status Anggota";
$FormElement[$a] 	= "statuskerja";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Kontrak','Tetap');
$FormDataValue[$a]	= array('0','1');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";*/

// $a++;
// $FormLabel[$a]   	= "Bayaran Pendaftaran";
// $FormElement[$a] 	= "totPay";
// $FormType[$a]	  	= "text";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "30";
// $FormLength[$a]  	= "25";	

// $a++;
// $FormLabel[$a]   	= "Yuran Bulanan";
// $FormElement[$a] 	= "monthFee";
// $FormType[$a]	  	= "text";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "30";
// $FormLength[$a]  	= "25";

// $a++;
// $FormLabel[$a]   	= "&nbsp;";
// $FormElement[$a] 	= "test";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";		

$a++;
$FormLabel[$a]   	= "* Nama Bank";
$FormElement[$a] 	= "bankID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $bankList;
$FormDataValue[$a]	= $bankVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "* Nomor Akun Bank";
$FormElement[$a] 	= "accTabungan";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Nomor Anggota Pengusul (?)";
$FormElement[$a] 	= "saksi1";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "10";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
// if (!$SubmitForm) {
// 	if ($dateBirth) {
// 		$getdate = explode("/", $dateBirth);
// 		$dateBirth = $getdate[2] . '/' . sprintf("%02s",  $getdate[1]) . '/' . sprintf("%02s",  $getdate[0]);
// 	}
// }

if ($SubmitForm <> "") {

	if ($dept == '') {
		array_push($strErrMsg, "departmentIDd");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>* Silakan pilih Cabang / Departemen / Zona.</strong> 
                                                </div>';
	}


	if (strlen($password) < 6) {
		array_push($strErrMsg, "password");
		array_push($strErrMsg, "password1");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>* Kata Sandi harus terdiri dari minimal ENAM [6] karakter.</strong> 
                                                </div>';
	}

	if ($password <> $password1) {
		array_push($strErrMsg, "password");
		array_push($strErrMsg, "password1");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>* Kata Sandi harus sama dengan Konfirmasi Kata Sandi.</strong> 
                                                </div>';
	}
	$GetLogin = ctLogin($loginID);
	if ($GetLogin->RowCount() == 1) {
		array_push($strErrMsg, "loginID");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                        </button>
                                                        <strong>* ID Pengguna sudah ada. Silakan pilih ID pengguna yang lain</strong> 
                                                    </div>';
	}

	if ($accTabungan) {
		if (!dlookup("userdetails", "newIC", "newIC=" . tosql($newIC, "Text"))) {
			if (dlookup("userdetails", "accTabungan", "accTabungan=" . tosql($accTabungan, "Text")) <> '') {
				array_push($strErrMsg, "accTabungan");
				print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                                    </button>
                                                                                    <strong>* Nomor akun tersebut sudah digunakan.</strong> 
                                                                                </div>';
			}
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

	// Extract date of birth from the newIC
	$dateBirth_str = substr($newIC, 0, 6);

	$year = substr($dateBirth_str, 0, 2);
	$month = substr($dateBirth_str, 2, 2);
	$day = substr($dateBirth_str, 4, 2);

	$current_year = date('Y') % 100;
	$century = ($year <= $current_year) ? '20' : '19';

	$full_year = $century . $year;
	$dateBirth = $full_year . '/' . $month . '/' . $day;

	$dateStarted = substr($dateStarted, 6, 4) . '-' . substr($dateStarted, 3, 2) . '-' . substr($dateStarted, 0, 2);

	if (count($strErrMsg) == "0") {
		$applyDate = date("Y-m-d H:i:s");
		$password = strtoupper(md5($password));

		// Check if the userdetails table is empty and handle the insertion properly
		$sSQLi = "SELECT max(CAST(memberID AS SIGNED INTEGER)) AS new FROM userdetails";
		$rsi = &$conn->Execute($sSQLi);
		$userID = $rsi->fields('new');
		if ($userID == NULL) {
			$userID = 1; // If no data exists, set userID to 1
		} else {
			$userID = $userID + 1; // Otherwise, increment the userID
		}

		// Insert into users table
		$sSQL = "INSERT INTO users (userID, loginID, password, email, name, applyDate) 
             VALUES (" . tosql($userID, "Text") . ", " . tosql($loginID, "Text") . ", " .
			tosql($password, "Text") . ", " . tosql($email, "Text") . ", " .
			tosql($name, "Text") . ", " . tosql($applyDate, "Text") . ")";
		$rs = &$conn->Execute($sSQL);

		$memberID = $userID; // Set memberID to userID

		// Handle userID and username for logging
		if (!isset($Cookie_userID)) $uid = $userID;
		else $uid =  $Cookie_userID;
		if (!isset($Cookie_userName)) $uname = $loginID;
		else $uname =  $Cookie_userName;

		// Log activity
		$activity = "Permohonan Anggota - " . $userID;
		if ($rs) activityLog($sSQL, $activity, $uid, $uname, 1);

		// Insert into userdetails table
		$sSQL = "INSERT INTO userdetails 
            (userID, memberID, staftNo, newIC, dateBirth, sex, raceID, religionID, maritalID, job, secret, 
            address, city, postcode, stateID, mobileNo, departmentID, totPay, monthFee, saksi1, addressSuratD, 
            updatedBy, updatedDate) 
            VALUES (" . tosql($userID, "Text") . ", " . tosql($memberID, "Text") . ", " .
			tosql($staftNo, "Text") . ", " . tosql($newIC, "Text") . ", " .
			tosql($dateBirth, "Text") . ", " . tosql($sex, "Number") . ", " .
			tosql($raceID, "Number") . ", " . tosql($religionID, "Number") . ", " .
			tosql($maritalID, "Number") . ", " . tosql($job, "Text") . ", " .
			tosql($secret, "Text") . "," . tosql($address, "Text") . ", " .
			tosql($city, "Text") . ", " . tosql($postcode, "Number") . ", " .
			tosql($stateID, "Number") . ", " . tosql($mobileNo, "Text") . ", " .
			tosql($dept, "Number") . ", " . tosql($totPay, "Number") . ", " .
			tosql($monthFee, "Number") . ", " . tosql($saksi1, "Text") . ", " .
			tosql($addressSuratD, "Text") . ", " . tosql($name, "Text") . ", " .
			tosql($applyDate, "Text") . ")";
		$rs = &$conn->Execute($sSQL);

		// Log activity
		$activity = "Permohonan Anggota - " . $userID;
		if ($rs) activityLog($sSQL, $activity, $uid, $uname, 1);

		// Insert into bank table
		$sSQLi3 = "SELECT COALESCE(MAX(CAST(ID AS SIGNED INTEGER)), 0) + 1 AS new FROM bank";
		$rsi3 = &$conn->Execute($sSQLi3);
		$IDbank = $rsi3->fields('new');

		$sSQL = "INSERT INTO bank 
            (ID, bankID, accTabungan, refer, priority) 
            VALUES (" . tosql($IDbank, "Number") . ", " .
			tosql($bankID, "Text") . ", " .
			tosql($accTabungan, "Text") . ", " .
			tosql($userID, "Text") . ", " .
			tosql(1, "Number") . ")";
		$rs = &$conn->Execute($sSQL);

		// Insert into userloandetails table
		$sSQL = "INSERT INTO userloandetails 
            (userID, memberID) 
            VALUES (" . tosql($userID, "Text") . ", " .
			tosql($memberID, "Text") . ")";
		$rs = &$conn->Execute($sSQL);

		alert("Permohonan menjadi anggota telah didaftarkan ke dalam sistem.");
		gopage("$sActionFileName", 1000);
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
?>
<form name="MyForm" action="<?php print $sFileName; ?>" method=post>
	<input type="hidden" name="userID" value="<?php print $userID; ?>">
	<input type="hidden" name="loanType" value="<?php print $loanType; ?>">
	<div class="mb-3 row">

		<h5 class="card-title">
			<font class="text-primary">
				<img src="images/number1-primary.png" width="17" height="17">&nbsp;ISI PROFIL
			</font>&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
			<img src="images/number2.png" width="17" height="17">&nbsp;MUAT NAIK DOKUMEN&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
			<img src="images/number3.png" width="17" height="17">&nbsp;PEMBAYARAN&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
			<img src="images/number4.png" width="17" height="17">&nbsp;SELESAI<br /><br />
			<div class="progress mb-2">
				<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-label="Animated striped example" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 25%"></div>
			</div>
		</h5>
		<!-- 
<hr class="mb-4"> -->
		<h6 class="card-title"><i class="fas fa-user"></i>&nbsp;<?php echo strtoupper($title); ?><br><small>* Harus diisi untuk pengajuan.</small>
			<br><small>(?) Nomor Anggota yang telah disetujui.</small></h5>

			<?php
			//--- Begin : Looping to display label -------------------------------------------------------------
			for ($i = 1; $i <= count($FormLabel); $i++) {
				$cnt = $i % 2;
				if ($i == 1) print '<div class="card-header mb-3 mt-3">INFORMASI PENDAFTARAN ID</div>';
				if ($i == 7) print '<div class="card-header mb-3 mt-3">DETAIL PRIBADI</div>';
				if ($i == 23) print '<div class="card-header mb-3 mt-3">BIAYA MASUK/IURAN</div>';
				if ($i == 25) {
					print '<div class="card-header mt-3">AHLI WARIS:(18 Tahun Ke atas)</div>';

					print '<div class="row m-1 mt-3">
                                                    <div class="col-md-3">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom032">*Nama Penama</label>
                                                            <input type="text" class="form-control' . $penamaerr . '" name="w_name1" value="' . $w_name1 . '" size=30 maxlength=50 id="validationCustom032">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom03">Nombor Kad Pengenalan</label>
                                                            <input type="text" class="form-control' . $penamaerr . '" name="w_ic1" value="' . $w_ic1 . '" size=15 maxlength=14 id="validationCustom03" placeholder="(999999999999)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom04">Nombor Telefon Bimbit</label>
                                                            <input type="text" class="form-control' . $penamaerr . '" name="w_contact1" value="' . $w_contact1 . '" size=15 maxlength=14 id="validationCustom04" placeholder="(6XXXXXXXXXX)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom05">Hubungan Penama</label>                        
                                                            <input type="text" class="form-control' . $penamaerr . '" name="w_relation1" value="' . $w_relation1 . '" size=15 maxlength=15 id="validationCustom05">                                                           
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom06">Alamat Kediaman</label>
                                                            <textarea class="form-control' . $penamaerr . '" cols=30 rows=3 wrap="hard" name="w_address1" id="validationCustom06">' . $w_address1 . '</textarea>                                                 
                                                        </div>
                                                    </div>
                                                </div>';

					print '<div class="card-header mb-3 mt-3">PENGUSUL: (NOMOR ANGGOTA YANG SUDAH TERDAFTAR DI KOPERASI)</div>';
				}
				if ($i == 27) print '<div class="card-header mb-3 mt-3">INFORMASI BANK</div>';

				if ($cnt == 1) print '<div class="m-1 row">';
				print '<label class="col-md-2 col-form-label">' . $FormLabel[$i];
				// if (!($i == 6 OR $i == 26 OR $i == 14)) print ':';
				print ' </label>';
				if (in_array($FormElement[$i], $strErrMsg))
					print '<div class="col-md-4 bg-danger">';
				else
					print '<div class="col-md-4">';

				// if ($i == 8) {
				// 	if ($birth) $strFormValue = '12/45/1922';
				// }

				// if ($i == 12) {
				// 	if ($dept) $strFormValue = dlookup("general", "b_Address", "ID=" . $dept);
				// 	$strFormValue = str_replace("<pre>", "", $strFormValue);
				// 	$strFormValue = str_replace("</pre>", "", $strFormValue);
				// 	print '' . $strFormValue . '';
				// }

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

				if ($i == 8) {
					$dateBirth_str = substr($newIC, 0, 6);

					$year = substr($dateBirth_str, 0, 2);
					$month = substr($dateBirth_str, 2, 2);
					$day = substr($dateBirth_str, 4, 2);

					$current_year = date('Y') % 100;
					$century = ($year <= $current_year) ? '20' : '19';

					$full_year = $century . $year;

					$dateBirth = $day . '/' . $month . '/' . $full_year;

					print '<b>' . $dateBirth . '</b>';
				}

				if ($i == 9) {
					print '<select class="form-select" name="dept">
				<option value="">- Semua -';
					for ($j = 0; $j < count($deptList); $j++) {
						print '	<option value="' . $deptVal[$j] . '" ';
						if ($dept == $deptVal[$j]) print ' selected';
						print '>' . $deptList[$j];
					}
					print '		</select>&nbsp;';
				}
				//--- End   : Call function FormEntry ---------------------------------------------------------  
				print '</div>';
				if ($cnt == 0) print '</div>';
			}

			$home = '<i class="fas fa-home">';
			?>

			<div class="mt-4">
				<center>
					<input type="Submit" class="btn btn-primary w-md waves-effect waves-light" name="SubmitForm" value="Selanjutnya">
					<!-- <input type="Reset" class="btn btn-danger w-md waves-effect waves-light" name="ResetForm" value="ISI SEMULA"> -->
					<button type="button" class="btn btn-secondary waves-effect" onClick="window.location.href='index.php?page=login&error='"><i class="fas fa-home"></i></button>
				</center>
			</div>
	</div>
</form>
<?php include("footer.php"); ?>