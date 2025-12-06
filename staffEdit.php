<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	staffEdit.php
 *          Date 		: 	19/12/2024
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");

date_default_timezone_set("Asia/Jakarta");
if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '");window.location="index.php";</script>';
}

$sFileName		= "?vw=staffEdit&mn=905";
$sActionFileName = "?vw=staffEdit&pk=" . $pk . "&mn=905";
$title     		= "Kemaskini Maklumat Staf";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$nationList = array();
$nationVal  = array();
$GetNation = ctGeneral("", "A");
if ($GetNation->RowCount() <> 0) {
	while (!$GetNation->EOF) {
		array_push($nationList, $GetNation->fields(name));
		array_push($nationVal, $GetNation->fields(ID));
		$GetNation->MoveNext();
	}
}

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

//--- Prepare jabatan type
$jabatanList = array();
$jabatanVal  = array();
$GetJabatan = ctGeneral("", "W");
if ($GetJabatan->RowCount() <> 0) {
	while (!$GetJabatan->EOF) {
		array_push($jabatanList, $GetJabatan->fields(name));
		array_push($jabatanVal, $GetJabatan->fields(ID));
		$GetJabatan->MoveNext();
	}
}

$a = 1;
$FormLabel[$a]   	= "Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "40";
$FormLength[$a]  	= "70";

$a++;
$FormLabel[$a]   	= "Nombor Staf";
$FormElement[$a] 	= "staffID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Id Staf";
$FormElement[$a] 	= "loginID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Tarikh Menjadi Staf";
$FormElement[$a] 	= "applyDate";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Emel <br>(Pastikan Sah)";
$FormElement[$a] 	= "email";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
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
$FormData[$a]   	= array('Laki-laki', 'Perempuan');
$FormDataValue[$a]	= array('0', '1');
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

$a++;
$FormLabel[$a]   	= "Jabatan";
$FormElement[$a] 	= "jabatanID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $jabatanList;
$FormDataValue[$a]	= $jabatanVal;
$FormCheck[$a]   	= array();
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
$FormData[$a]   	= array('Kontrak', 'Tetap', 'Praktikal');
$FormDataValue[$a]	= array('0', '1', '2');
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
$FormLabel[$a]   	= "Tarikh Masuk";
$FormElement[$a] 	= "dateJoin";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Tarikh Tamat";
$FormElement[$a] 	= "dateEnd";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";


///////// Butir Kecemasan ////////
// 26
$a++;
$FormLabel[$a]   	= "Nama Penuh";
$FormElement[$a] 	= "emergencyContact_name";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Hubungan";
$FormElement[$a] 	= "emergencyContact_relation";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Emel";
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
$FormLabel[$a]   	= "No. Telefon<br>Cth: 6011XXXXXXXX";
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

$a++;
$FormLabel[$a]   	= "* Jenis Kumpulan";
$FormElement[$a] 	= "groupID";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $groupAList;
$FormDataValue[$a]	= $groupAVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$strMember = "SELECT a.*, b.* FROM users a, staff b WHERE a.staffID = '" . $pk . "' AND a.staffID = b.staffID";
$GetMember = &$conn->Execute($strMember);
$currentContractImg = $GetMember->fields['contract_img'];
$currentResumeImg = $GetMember->fields['resume_img'];

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
	$contractUploadDir = $_SERVER['DOCUMENT_ROOT'] . '/ikoop/stagingtri/upload_contract/';
	$resumeUploadDir = $_SERVER['DOCUMENT_ROOT'] . '/ikoop/stagingtri/upload_resume/';
	$groupID = isset($_POST['groupID']) ? $_POST['groupID'] : '';

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
	$applyDate = substr($applyDate, 6, 4) . '-' . substr($applyDate, 3, 2) . '-' . substr($applyDate, 0, 2);
	$dateBirth = substr($dateBirth, 6, 4) . '-' . substr($dateBirth, 3, 2) . '-' . substr($dateBirth, 0, 2);
	$dateJoin = substr($dateJoin, 6, 4) . '-' . substr($dateJoin, 3, 2) . '-' . substr($dateJoin, 0, 2);
	$dateEnd = substr($dateEnd, 6, 4) . '-' . substr($dateEnd, 3, 2) . '-' . substr($dateEnd, 0, 2);

	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";
		$sWhere = "staffID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE users SET 
				  " . "name=" . tosql($name, "Text") .
			",email=" . tosql($email, "Text") .
			",groupID=" . tosql($groupID, "Text") .
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
		$sWhere = "staffID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL = "UPDATE staff SET " .
			"name=" . tosql($name, "Text") .
			", newIC=" . tosql($newIC, "Text") .
			", mobileNo=" . tosql($mobileNo, "Text") .
			", dateBirth=" . tosql($dateBirth, "Date") .
			", address=" . tosql($address, "Text") .
			", job=" . tosql($job, "Text") .
			", postcode=" . tosql($postcode, "Number") .
			", city=" . tosql($city, "Text") .
			", stateID=" . tosql($stateID, "Number") .
			", sex=" . tosql($sex, "Number") .
			", nationalityID=" . tosql($nationalityID, "Number") .
			", raceID=" . tosql($raceID, "Number") .
			", maritalID=" . tosql($maritalID, "Number") .
			", religionID=" . tosql($religionID, "Number") .
			", jabatanID=" . tosql($jabatanID, "Number") .
			", statuskerja=" . tosql($statuskerja, "Number") .
			", dateJoin=" . tosql($dateJoin, "Date") .
			", dateEnd=" . tosql($dateEnd, "Date") .
			", emergencyContact_name=" . tosql($emergencyContact_name, "Text") .
			", emergencyJob=" . tosql($emergencyJob, "Text") .
			", emergencyContact_phone=" . tosql($emergencyContact_phone, "Text") .
			", emergencyContact_email=" . tosql($emergencyContact_email, "Text") .
			", emergencyContact_address=" . tosql($emergencyContact_address, "Text") .
			", emergencyContact_postcode=" . tosql($emergencyContact_postcode, "Number") .
			", emergencyContact_city=" . tosql($emergencyContact_city, "Text") .
			", emergencyStateID=" . tosql($emergencyStateID, "Number") .
			", emergencyReligionID=" . tosql($emergencyReligionID, "Number") .
			", emergencyRaceID=" . tosql($emergencyRaceID, "Number") .
			", emergencyNationalityID=" . tosql($emergencyNationalityID, "Number") .
			", emergencyContact_relation=" . tosql($emergencyContact_relation, "Text") .
			", contract_img=" . tosql($currentContractImg, "Text") .
			", resume_img=" . tosql($currentResumeImg, "Text") .
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");

		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);

		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`)" .
			" VALUES ('Mengemaskini maklumat peribadi staf -$pk', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "')";
		$rs = &$conn->Execute($sqlAct);

		print '<script>
					alert ("Maklumat staf telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method="post" enctype="multipart/form-data">
<div class="mb-3 row">

                    <h5 class="card-title">' . strtoupper($title) . '</h5>';


//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;
	if ($i == 1)  print '<div class="card-header mb-3">INFORMASI PENDAFTARAN ID</div>';
	if ($i == 7)  print '<div class="card-header mb-3">A. DETAIL PRIBADI</div>';
	if ($i == 25) print '<div class="card-header mb-3">B. HUBUNGAN KECEMASAN</div>';
	if ($i == 37) {
		print '<div class="card-header mb-3">C. DOKUMEN</div>';

		print '
						<table class="table table-sm table-striped">
							<tr class="table-primary">
								<td><b>Perkara</b></td>
								<td><b>Muat Naik Fail</b></td>
								<td><b>Nama Fail</b></td>
							</tr>

							<tr>
								<td class="align-middle">* Kontrak</td>
								<td>
									<div class="col-md-8 col-form-label">
										<input type="button" class="btn btn-outline-primary" name="GetPicture" value="Muat Naik"  
										onclick="Javascript:(window.location.href=\'?vw=uploadwincontract&mn=905&pk=' . $pk . '\')">
									</div>
								</td>
								<td class="align-middle">';

		// Display contract file if it exists
		if (!empty($currentContractImg)) {
			print '<a href="#" onclick="window.open(\'upload_contract/' . $currentContractImg . '\', \'pop\', \'top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
									<i class="far fa-file-pdf text-danger"></i> ' . $currentContractImg . '
								</a>';
		} else {
			print 'Tiada Fail';
		}

		print '</td>
							</tr>

							<tr>
								<td class="align-middle">* Resume</td>
								<td>
									<div class="col-md-8 col-form-label">
										<input type="button" class="btn btn-outline-primary" name="GetPicture" value="Muat Naik"  
										onclick="Javascript:(window.location.href=\'?vw=uploadwinresume&mn=905&pk=' . $pk . '\')">
									</div>
								</td>
								<td class="align-middle">';

		// Display resume file if it exists
		if (!empty($currentResumeImg)) {
			print '<a href="#" onclick="window.open(\'upload_resume/' . $currentResumeImg . '\', \'pop\', \'top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
									<i class="far fa-file-pdf text-danger"></i> ' . $currentResumeImg . '
								</a>';
		} else {
			print 'Tiada Fail';
		}

		print '</td>
							</tr>
						</table>';
		print '<div class="row">';


		print '<div class="card-header mb-3">D. JENIS KUMPULAN PENGURUSAN</div>';
	}
	if ($cnt == 1) print '<div class="m-1 row">';
	print '<label class="col-md-2 col-form-label">' . $FormLabel[$i];
	// if (!($i == 6 OR $i == 26 OR $i == 32 )) print ':';
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



	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '</div>';
	if ($cnt == 0) print '</div>';
}

if ((get_session("Cookie_groupID") == 2)) {
	print '<div class="mb-3 row">
                <center>
                        <input type="hidden" name="pk" value="' . $pk . '">
						<!--input type="button" class="btn btn-secondary btn-md waves-effect waves-light" value="<<"-->
                        <input type=Submit name=SubmitForm class="btn btn-primary btn-md waves-light waves-effects" value=Kemaskini>
                </center>
            </div>';
}

print '</div>
</form>';
include("footer.php");
