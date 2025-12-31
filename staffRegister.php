<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	staffRegister.php
*********************************************************************************/
include("header.php");	
include("koperasiQry.php");	
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$Cookie_userID = get_session('Cookie_userID');
$Cookie_userName = get_session("Cookie_userName");
$sFileName		= "?vw=staffRegister&mn=$mn";
$sActionFileName= "?vw=staff&mn=$mn";
$title     		= "Pendaftaran Staf";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();

$nationList = Array();
$nationVal  = Array();
$GetNation = ctGeneral("","A");
if ($GetNation->RowCount() <> 0){
	while (!$GetNation->EOF) {
		array_push ($nationList, $GetNation->fields(name));
		array_push ($nationVal, $GetNation->fields(ID));
		$GetNation->MoveNext();
	}
}	

//--- Prepare race type
$raceList = Array();
$raceVal  = Array();
$GetRace = ctGeneral("","E");
if ($GetRace->RowCount() <> 0){
	while (!$GetRace->EOF) {
		array_push ($raceList, $GetRace->fields(name));
		array_push ($raceVal, $GetRace->fields(ID));
		$GetRace->MoveNext();
	}
}	

//--- Prepare religion type
$religionList = Array();
$religionVal  = Array();
$GetReligion = ctGeneral("","F");
if ($GetReligion->RowCount() <> 0){
	while (!$GetReligion->EOF) {
		array_push ($religionList, $GetReligion->fields(name));
		array_push ($religionVal, $GetReligion->fields(ID));
		$GetReligion->MoveNext();
	}
}	

//--- Prepare state type
$stateList = Array();
$stateVal  = Array();
$GetState = ctGeneral("","H");
if ($GetState->RowCount() <> 0){
	while (!$GetState->EOF) {
		array_push ($stateList, $GetState->fields(name));
		array_push ($stateVal, $GetState->fields(ID));
		$GetState->MoveNext();
	}
}	

//--- Prepare jabatan type
$jabatanList = Array();
$jabatanVal  = Array();
$GetJabatan = ctGeneral("","W");
if ($GetJabatan->RowCount() <> 0){
	while (!$GetJabatan->EOF) {
		array_push ($jabatanList, $GetJabatan->fields(name));
		array_push ($jabatanVal, $GetJabatan->fields(ID));
		$GetJabatan->MoveNext();
	}
}		

//--- Prepare society
$societyList = Array();
$societyVal  = Array();
$GetSociety = ctGeneral("","L");
if ($GetSociety->RowCount() <> 0){
	while (!$GetSociety->EOF) {
		array_push ($societyList, $GetSociety->fields(name));
		array_push ($societyVal, $GetSociety->fields(ID));
		$GetSociety->MoveNext();
	}
}	

//--- Prepare payment type
$pymtList = Array();
$pymtVal  = Array();
$GetPymt = ctGeneral("","K");
if ($GetPymt->RowCount() <> 0){
	while (!$GetPymt->EOF) {
		array_push ($pymtList, $GetPymt->fields(name));
		array_push ($pymtVal, $GetPymt->fields(ID));
		$GetPymt->MoveNext();
	}
}	

$a = 1;
$FormLabel[$a]   	= "* Nama Lengkap";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";


$a++;
$FormLabel[$a]   	= "* Kata Sandi<br>(Minimum 6 Aksara)";
$FormElement[$a] 	= "password";
$FormType[$a]	  	= "password";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";


$a++;
$FormLabel[$a]   	= "* Id Pengguna";
$FormElement[$a] 	= "loginID";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";


$a++;
$FormLabel[$a]   	= "* Identifikasi kata sandi";
$FormElement[$a] 	= "password1";
$FormType[$a]	  	= "password";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";


$a++;
$FormLabel[$a]   	= "* Email<br><b>(Pastikan valid)</b>";
$FormElement[$a] 	= "email";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

// 6
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
$FormLabel[$a]   	= "* Kartu Identitas<br>Tiada (-)";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "16";	

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
$FormLabel[$a]   	= "Alamat Rumah";
$FormElement[$a] 	= "address";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

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
$FormLabel[$a]   	= "Kode Pos Rumah";
$FormElement[$a] 	= "postcode";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "* No. Telefon<br>Cth: 6011XXXXXXXX";
$FormElement[$a] 	= "mobileNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";	

$a++;
$FormLabel[$a]   	= "Kota Rumah";
$FormElement[$a] 	= "city";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";	

$a++;
$FormLabel[$a]   	= "Jantina";
$FormElement[$a] 	= "sex";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Lelaki','Perempuan');
$FormDataValue[$a]	= array('0','1');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Provinsi Rumah";
$FormElement[$a] 	= "stateID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";	

$a++;
$FormLabel[$a]   	= "Status Pernikahan";
$FormElement[$a] 	= "maritalID";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Bujang','Berkahwin','Janda/Duda');
$FormDataValue[$a]	= array('0','1','2');
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
$FormLabel[$a]   	= "* Jabatan";
$FormElement[$a] 	= "jabatanID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $jabatanList;
$FormDataValue[$a]	= $jabatanVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Bangsa";
$FormElement[$a] 	= "raceID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $raceList;
$FormDataValue[$a]	= $raceVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Status Staf";
$FormElement[$a] 	= "statuskerja";
$FormType[$a]	  	= "select";
$FormData[$a]   	= array('Kontrak','Tetap','Praktikal');
$FormDataValue[$a]	= array('0','1','2');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Kewarganegaraan";
$FormElement[$a] 	= "nationalityID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $nationList;
$FormDataValue[$a]	= $nationVal;
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
$FormLabel[$a]   	= "* Tanggal Masuk";
$FormElement[$a] 	= "dateJoin";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Tanggal Tamat";
$FormElement[$a] 	= "dateEnd";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

///////// Butir Kecemasan ////////
// 25
$a++;
$FormLabel[$a]   	= "* Nama Lengkap";
$FormElement[$a] 	= "emergencyContact_name";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Hubungan";
$FormElement[$a] 	= "emergencyContact_relation";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Emel";
$FormElement[$a] 	= "emergencyContact_email";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Jabatan Pekerjaan";
$FormElement[$a] 	= "emergencyJob";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Alamat Rumah";
$FormElement[$a] 	= "emergencyContact_address";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";	

$a++;
$FormLabel[$a]   	= "* No. Telefon<br>Cth: 6011XXXXXXXX";
$FormElement[$a] 	= "emergencyContact_phone";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";	

$a++;
$FormLabel[$a]   	= "Kode Pos Rumah";
$FormElement[$a] 	= "emergencyContact_postcode";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "Agama";
$FormElement[$a] 	= "emergencyReligionID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $religionList;
$FormDataValue[$a]	= $religionVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Kota Rumah";
$FormElement[$a] 	= "emergencyContact_city";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Bangsa";
$FormElement[$a] 	= "emergencyRaceID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $raceList;
$FormDataValue[$a]	= $raceVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Provinsi Rumah";
$FormElement[$a] 	= "emergencyStateID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Kewarganegaraan";
$FormElement[$a] 	= "emergencyNationalityID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $nationList;
$FormDataValue[$a]	= $nationVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";	

//// Dokumen /////
// 37
$a++;
$FormLabel[$a]   	= "* Tawaran Kontrak";
$FormElement[$a] 	= "contract_img";
$FormType[$a]	  	= "file";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";  

$a++;
$FormLabel[$a]   	= "* Resume";
$FormElement[$a] 	= "resume_img";
$FormType[$a]	  	= "file";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";

$a++;
$FormLabel[$a]   	= "* Jenis Kumpulan";
$FormElement[$a] 	= "groupID";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $groupAList;
$FormDataValue[$a]	= $groupAVal;
$FormCheck[$a]   	= array(CheckBlank);
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

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if(!$SubmitForm) { 
	if ($dateBirth) {
		$getdate = explode("/", $dateBirth); 
		$dateBirth= $getdate[2].'/'.sprintf("%02s",  $getdate[1]).'/'.sprintf("%02s",  $getdate[0]);
	}

	if ($dateJoin) {
		$getdate = explode("/", $dateJoin); 
		$dateJoin = $getdate[2].'/'.sprintf("%02s", $getdate[1]).'/'.sprintf("%02s", $getdate[0]);
	}	

	if ($dateEnd) {
		$getdate = explode("/", $dateEnd); 
		$dateEnd= $getdate[2].'/'.sprintf("%02s",  $getdate[1]).'/'.sprintf("%02s",  $getdate[0]);
	}
}

if ($SubmitForm <> "") {

$dateBirth = substr($dateBirth,6,4).'-'.substr($dateBirth,3,2).'-'.substr($dateBirth,0,2);
$dateJoin = substr($dateJoin,6,4).'-'.substr($dateJoin,3,2).'-'.substr($dateJoin,0,2);
$dateEnd = substr($dateEnd,6,4).'-'.substr($dateEnd,3,2).'-'.substr($dateEnd,0,2);
$groupID = isset($_POST['groupID']) ? $_POST['groupID'] : '';
$contractFileName = '';
if (isset($_FILES['contract_img']) && $_FILES['contract_img']['error'] === UPLOAD_ERR_OK) {
    $contractUploadDir = $_SERVER['DOCUMENT_ROOT'] . "/stagingtri/upload_contract/";
	echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
    echo "Expected Upload Path: " . $contractUploadDir . "<br>";

    // Ensure directory exists
    if (!is_dir($contractUploadDir) && !mkdir($contractUploadDir, 0777, true) && !is_dir($contractUploadDir)) {
        die("<p style='color:red;'>Failed to create contract upload directory.</p>");
    }

    // Generate unique filename
    $contractFileName = time() . "_" . uniqid() . "_" . basename($_FILES['contract_img']['name']);
    $contractTargetPath = $contractUploadDir . $contractFileName;

    // Debugging: Print paths
    error_log("Contract Upload Dir: " . $contractUploadDir);
    error_log("Contract Target Path: " . $contractTargetPath);

    if (!move_uploaded_file($_FILES['contract_img']['tmp_name'], $contractTargetPath)) {
        echo "<p style='color:red;'>Error uploading contract file.</p>";
        error_log("Error moving contract file: " . $_FILES['contract_img']['error']);
        $contractFileName = '';
    } else {
        echo "<p style='color:green;'>Contract file uploaded successfully to: " . $contractTargetPath . "</p>";
    }
}

// Resume Upload
$resumeFileName = '';
if (isset($_FILES['resume_img']) && $_FILES['resume_img']['error'] === UPLOAD_ERR_OK) {
    $resumeUploadDir = $_SERVER['DOCUMENT_ROOT'] . "/stagingtri/upload_resume/";
	echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
    echo "Expected Upload Path: " . $resumeUploadDir . "<br>";

    // Ensure directory exists
    if (!is_dir($resumeUploadDir) && !mkdir($resumeUploadDir, 0777, true) && !is_dir($resumeUploadDir)) {
        die("<p style='color:red;'>Failed to create upload directory.</p>");
    }

    // Generate unique filename
    $resumeFileName = time() . "_" . uniqid() . "_" . basename($_FILES['resume_img']['name']);
    $resumeTargetPath = $resumeUploadDir . $resumeFileName;

    // Debugging: Print paths
    error_log("Resume Upload Dir: " . $resumeUploadDir);
    error_log("Resume Target Path: " . $resumeTargetPath);

    if (!move_uploaded_file($_FILES['resume_img']['tmp_name'], $resumeTargetPath)) {
        error_log("Error moving resume file: " . $_FILES['resume_img']['error']);
        die("<p style='color:red;'>Error uploading resume file: " . $_FILES['resume_img']['error'] . "</p>");
    } else {
        echo "<p style='color:green;'>Resume file uploaded successfully to: " . $resumeTargetPath . "</p>";
    }
}


	if (strlen($password) < 6) {
		array_push ($strErrMsg, "password");
		array_push ($strErrMsg, "password1");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>* Kata Sandi mesti sekurang-kurangnya ENAM [6] aksara.</strong> 
                                                </div>';
	}	

	if ($password <> $password1) {
		array_push ($strErrMsg, "password");
		array_push ($strErrMsg, "password1");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>* Kata Sandi mesti sama dengan kenal pasti Kata Laluan.</strong> 
                                                </div>';
	}	

 	$GetLogin = ctLogin($loginID);
	if ($GetLogin->RowCount() == 1) {
		array_push ($strErrMsg, "loginID");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                        </button>
                                                        <strong>* ID Pengguna sudah ada. Silakan pilih ID pengguna yang lain</strong> 
                                                    </div>';	
	}

	if ($newIC) {
		if(dlookup("userdetails", "newIC", "newIC=" . tosql($newIC, "Text")) == $newIC) {			
				array_push ($strErrMsg, "newIC");
				print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                                    </button>
                                                                                    <strong>* Nomor KTP sudah terdaftar.</strong> 
                                                                                </div>';
			
		}
	}
	
	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
		for($j=0 ; $j < count($FormCheck[$i]); $j++) {
			FormValidation ($FormLabel[$i], 
							$FormElement[$i], 
							$$FormElement[$i],
							$FormCheck[$i][$j],
							$i);
		}
	}	
	//--- End   : Call function FormValidation ---  

if (!$conn) {
    die("Database connection failed: " . $conn->ErrorMsg());
}

if (count($strErrMsg) == 0) {
    $applyDate = date("Y-m-d H:i:s");
    $password = strtoupper(md5($password));

    $sSQL = "INSERT INTO staff (
        loginID, applyDate, name, newIC, mobileNo, dateBirth, address, job, postcode, city, stateID, sex, nationalityID, raceID, maritalID, religionID, jabatanID, statuskerja, dateJoin, dateEnd, emergencyContact_name, emergencyJob, emergencyContact_phone, emergencyContact_email, emergencyContact_address, emergencyContact_postcode, emergencyContact_city, emergencyStateID, emergencyReligionID, emergencyRaceID, emergencyNationalityID, emergencyContact_relation, contract_img, resume_img
    ) VALUES (
    " . tosql($loginID, "Text") . ", " .
        tosql($applyDate, "Text") . ", " .
        tosql($name, "Text") . ", " .
        tosql($newIC, "Text") . ", " .
        tosql($mobileNo, "Text") . ", " .
        tosql($dateBirth, "Date") . ", " .
        tosql($address, "Text") . ", " .
        tosql($job, "Text") . ", " .
        tosql($postcode, "Number") . ", " .
        tosql($city, "Text") . ", " .
        tosql($stateID, "Number") . ", " .
        tosql($sex, "Number") . ", " .
        tosql($nationalityID, "Number") . ", " .
        tosql($raceID, "Number") . ", " .
        tosql($maritalID, "Number") . ", " .
        tosql($religionID, "Number") . ", " .
        tosql($jabatanID, "Number") . ", " .
        tosql($statuskerja, "Number") . ", " .
        tosql($dateJoin, "Date") . ", " .
        tosql($dateEnd, "Date") . ", " .
        tosql($emergencyContact_name, "Text") . ", " .
		tosql($emergencyJob, "Text") . ", " .
        tosql($emergencyContact_phone, "Text") . ", " .
        tosql($emergencyContact_email, "Text") . ", " .
        tosql($emergencyContact_address, "Text") . ", " .
        tosql($emergencyContact_postcode, "Number") . ", " .
        tosql($emergencyContact_city, "Text") . ", " .
        tosql($emergencyStateID, "Number") . ", " .
        tosql($emergencyReligionID, "Number") . ", " .
        tosql($emergencyRaceID, "Number") . ", " .
        tosql($emergencyNationalityID, "Number") . ", " .
        tosql($emergencyContact_relation, "Text") . ", " .
        tosql($contractFileName, "Text") . ", " .
        tosql($resumeFileName, "Text") . ")";

		$rsStaff = &$conn->Execute($sSQL);

		if (!$rsStaff) {
			die("Error inserting into staff: " . $conn->ErrorMsg());
		}
		
		// Retrieve the last inserted staffID
		$staffID = $conn->Insert_ID(); // ADODB method to get last inserted ID

	$sSQLi = "SELECT COALESCE(MAX(CAST(userID AS SIGNED INTEGER)), 0) + 1 as new FROM users";
	$rsi = &$conn->Execute($sSQLi);
	
	if ($rsi) {
		$userID = $rsi->fields['new'];
	} else {
		die("Error fetching new userID: " . $conn->ErrorMsg());
	}
	
	$sSQLUser = "INSERT INTO users (
		userID, loginID, staffID, password, email, name, isActive, applyDate, GroupID
	) VALUES (
		" . tosql($userID, "Text") . ", " .
		tosql($loginID, "Text") . ", " .
		tosql($staffID, "Text") . ", " .
		tosql($password, "Text") . ", " .
		tosql($email, "Text") . ", " .
		tosql($name, "Text") . ", " .
		tosql(1, "Number") . ", " .
		tosql($applyDate, "Text") . ", " .
		tosql($groupID, "Text") . ")";
	
	$rsUser = &$conn->Execute($sSQLUser);
	
	alert("Pendaftaran menjadi staf telah didaftarkan ke dalam sistem.");
	// gopage("$sActionFileName", 1000);
	
	if (!$rsUser) {
		die("Error inserting into users: " . $conn->ErrorMsg());
	}
}
}
	
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
?>
<form name="MyForm" action="<?php print $sFileName;?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="userID" value="<?php print $userID;?>">
<div class="mb-3 row">
<h5 class="card-title"><?php echo strtoupper($title);?><br><small>BORANG PENDAFTARAN STAF (* Mesti diisi untuk pendaftaran.)</small></h5>

<?php
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
 	$cnt = $i % 2;
	if ($i == 1) print '<div class="card-header mb-3">INFORPASI PENDAFTARAN ID</div>';
	if ($i == 7) print '<div class="card-header mb-3">A. DETAIL PRIBADI</div>';
	if ($i == 25) print '<div class="card-header mb-3">B. HUBUNGAN KECEMASAN</div>';
	if ($i == 37) print '<div class="card-header mb-3">C. DOKUMEN</div>';
	if ($i == 39) print '<div class="card-header mb-3">D. JENIS KUMPULAN PENGURUSAN</div>';
	if ($cnt == 1) print '<div class="m-1 row">';
	print '<label class="col-md-2 col-form-label">'.$FormLabel[$i];
	
	print ' </label>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<div class="col-md-4 bg-danger">';
	else
	  print '<div class="col-md-4">';

	if ($i == 8) {
		if ($birthdate) $strFormValue = '31/12/2022'; 
	}
	
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = $$FormElement[$i];
	FormEntry($FormLabel[$i], 
			  $FormElement[$i], 
			  $FormType[$i],
			  $strFormValue,
			  $FormData[$i],
			  $FormDataValue[$i],
			  $FormSize[$i],
			  $FormLength[$i]);

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '</div>';
	if ($cnt == 0) print '</div>';
}
?>
            <div class="mb-3 row">
                <center>
                        <br><input type="Submit" class="btn btn-primary w-md waves-effect waves-light" name="SubmitForm" value="Daftar"></br>
                </center>
            </div>
</div></form>
<?php include("footer.php"); 
?>