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
date_default_timezone_set("Asia/Kuala_Lumpur");

$Cookie_userID = get_session('Cookie_userID');
$Cookie_userName = get_session("Cookie_userName");
$sFileName		= "?vw=memberApply&mn=$mn";
$sActionFileName = "?vw=member&mn=905";
$title     		= "Permohonan Anggota";

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
$FormLabel[$a]   	= "* Kata Laluan";
$FormElement[$a] 	= "password";
$FormType[$a]	  	= "";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";

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
$FormLabel[$a]   	= "* Kenal Pasti Kata Laluan";
$FormElement[$a] 	= "password1";
$FormType[$a]	  	= "";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";

$a++;
$FormLabel[$a]   	= "* Emel<br>(Pastikan Sah)";
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
$FormLabel[$a]   	= "* Kad Pengenalan<br>Tiada (-)";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Tarikh Lahir";
$FormElement[$a] 	= "dateBirth";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Jawatan Pekerjaan";
$FormElement[$a] 	= "job";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Cawangan / Zon";
$FormElement[$a] 	= "departmentIDd";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Alamat Kediaman";
$FormElement[$a] 	= "address";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "Alamat Cawangan";
$FormElement[$a] 	= "addressSuratD";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "Poskod Kediaman";
$FormElement[$a] 	= "postcode";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "Kod PTJ";
$FormElement[$a] 	= "ptjID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $ptjList;
$FormDataValue[$a]	= $ptjVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Bandar Kediaman";
$FormElement[$a] 	= "city";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Nombor Telefon<br>Cth: 6011XXXXXXXX";
$FormElement[$a] 	= "mobileNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Negeri Kediaman";
$FormElement[$a] 	= "stateID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Nombor Pekerja<br>(Sekiranya Ada)";
$FormElement[$a] 	= "staftNo";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

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
$FormLabel[$a]   	= "Status Perkahwinan";
$FormElement[$a] 	= "maritalID";
$FormType[$a]	  	= "select
";
$FormData[$a]   	= array('Bujang', 'Berkahwin', 'Janda/Duda');
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
$FormLabel[$a]   	= "Jantina";
$FormElement[$a] 	= "sex";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('Lelaki', 'Perempuan');
$FormDataValue[$a]	= array('0', '1');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Nombor Anggota Pencadang";
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
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>Sila pilih Jabatan / Cawangan.</strong> 
                                                </div>';
	}


	if (strlen($password) < 6) {
		array_push($strErrMsg, "password");
		array_push($strErrMsg, "password1");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>* Kata Laluan mesti sekurang-kurangnya ENAM [6] aksara.</strong> 
                                                </div>';
	}

	if ($password <> $password1) {
		array_push($strErrMsg, "password");
		array_push($strErrMsg, "password1");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>* Kata Laluan mesti sama dengan kenal pasti Kata Laluan.</strong> 
                                                </div>';
	}
	$GetLogin = ctLogin($loginID);
	if ($GetLogin->RowCount() == 1) {
		array_push($strErrMsg, "loginID");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                        </button>
                                                        <strong>* ID Pengguna sudah wujud. Sila pilih ID pengguna yang lain</strong> 
                                                    </div>';
	}

	if ($accTabungan) {
		if (!dlookup("userdetails", "newIC", "newIC=" . tosql($newIC, "Text"))) {
			if (dlookup("userdetails", "accTabungan", "accTabungan=" . tosql($accTabungan, "Text")) <> '') {
				array_push($strErrMsg, "accTabungan");
				print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                                    </button>
                                                                                    <strong>* Nombor akaun tersebut telah digunakan.</strong> 
                                                                                </div>';
			}
		}
	}

	if ($newIC) {
		if (dlookup("userdetails", "newIC", "newIC=" . tosql($newIC, "Text")) == $newIC) {
			array_push($strErrMsg, "newIC");
			print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                                    </button>
                                                                                    <strong>* IC telah wujud.</strong> 
                                                                                </div>';
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

	$getdate = explode("/", $dateBirth);
	$dateBirth = $getdate[2] . '/' . sprintf("%02s",  $getdate[1]) . '/' . sprintf("%02s",  $getdate[0]);
	$dateStarted = substr($dateStarted, 6, 4) . '-' . substr($dateStarted, 3, 2) . '-' . substr($dateStarted, 0, 2);
	
	if (count($strErrMsg) == "0") {
		$applyDate = date("Y-m-d H:i:s");
		$password = strtoupper(md5($password));

		// Check if the userdetails table is empty and handle the insertion properly
		$sSQLi = "SELECT max(CAST(memberID AS SIGNED INTEGER)) AS new FROM userdetails";
		$rsi = &$conn->Execute($sSQLi);
		$userID = $rsi->fields('new') ? $rsi->fields('new') + 1 : 1; // Set to 1 if empty
	
		// Insert into users table
		$sSQL = "INSERT INTO users (userID, loginID, password, email, name, applyDate) 
             VALUES (" . tosql($userID, "Text") . ", " . tosql($loginID, "Text") . ", " .
			tosql($password, "Text") . ", " . tosql($email, "Text") . ", " .
			tosql($name, "Text") . ", " . tosql($applyDate, "Text") . ")";

		$rs = &$conn->Execute($sSQL);
		$memberID = $userID;

		// If there is no cookie set, use the newly generated userID
		if (!isset($Cookie_userID)) $uid = $userID;
		else $uid =  $Cookie_userID;
		if (!isset($Cookie_userName)) $uname = $loginID;
		else $uname =  $Cookie_userName;
		$activity = "Permohonan Anggota - " . $userID;
		if ($rs) activityLog($sSQL, $activity, $uid, $uname, 1);

		// Insert into userdetails table
		$sSQL = "INSERT INTO userdetails 
            (userID, memberID, staftNo, newIC, dateBirth, sex, raceID, religionID, maritalID, job, address, 
             city, postcode, stateID, mobileNo, departmentID, totPay, monthFee, saksi1, addressSuratD, ptjID, 
             updatedBy, updatedDate) 
            VALUES (" . tosql($userID, "Text") . ", " . tosql($memberID, "Text") . ", " .
			tosql($staftNo, "Text") . ", " . tosql($newIC, "Text") . ", " .
			tosql($dateBirth, "Text") . ", " . tosql($sex, "Number") . ", " .
			tosql($raceID, "Number") . ", " . tosql($religionID, "Number") . ", " .
			tosql($maritalID, "Number") . ", " . tosql($job, "Text") . ", " .
			tosql($address, "Text") . ", " . tosql($city, "Text") . ", " .
			tosql($postcode, "Number") . ", " . tosql($stateID, "Number") . ", " .
			tosql($mobileNo, "Text") . ", " . tosql($dept, "Number") . ", " .
			tosql($totPay, "Number") . ", " . tosql($monthFee, "Number") . ", " .
			tosql($saksi1, "Text") . ", " . tosql($addressSuratD, "Text") . ", " .
			tosql($ptjID, "Number") . ", " . tosql($name, "Text") . ", " .
			tosql($applyDate, "Text") . ")";

		$rs = &$conn->Execute($sSQL);

		// Insert into userloandetails table
		$sSQL = "INSERT INTO userloandetails (userID, memberID) 
             VALUES (" . tosql($userID, "Text") . ", " . tosql($memberID, "Text") . ")";
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
		<h5 class="card-title"><?php echo strtoupper($title); ?><br><small>BORANG MENJADI ANGGOTA (* Mesti diisi untuk permohonan.)</small></h5>
		<style>
			input::-ms-reveal,
			input::-ms-clear {
				display: none;
			}
		</style>
		<?php
		//--- Begin : Looping to display label -------------------------------------------------------------
		for ($i = 1; $i <= count($FormLabel); $i++) {
			$cnt = $i % 2;
			if ($i == 1) print '<div class="card-header mb-3">MAKLUMAT PENDAFTARAN ID</div>';
			if ($i == 7) print '<div class="card-header mb-3">A. BUTIR-BUTIR PERIBADI</div>';
			if ($i == 23) print '<div class="card-header mb-3 mt-3">B. PENCADANG : (NOMBOR ANGGOTA YANG TELAH BERDAFTAR BERSAMA KOPERASI)</div>';

			if ($cnt == 1) print '<div class="m-1 row">';
			print '<label class="col-md-2 col-form-label">' . $FormLabel[$i];
			// if (!($i == 6 OR $i == 26 OR $i == 14)) print ':';
			print ' </label>';
			if (in_array($FormElement[$i], $strErrMsg))
				print '<div class="col-md-4 bg-danger">';
			else
				print '<div class="col-md-4">';

			if ($i == 2) {
				print '
			<div class="col-md-10">
			<div class="input-group">
			<div style="position: relative; display: inline-block;">
				<input id="password1" type="password" class="form-control" placeholder="Minimum 6 Aksara" name="password" size="20" maxlength="16">
				<div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
					<a id="eyeIcon1" href="#" onclick="togglePassword(1)">
						<i id="eyeIconInner1" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
					</a>
				</div>
			</div>
			</div>
		</div>';
			}

			if ($i == 4) {
				print '
			<div class="col-md-10">
				<div class="input-group">
				<div style="position: relative; display: inline-block;">
					<input id="password2" type="password" class="form-control" placeholder="Minimum 6 Aksara" name="password1" size="20" maxlength="16">
					<div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
						<a id="eyeIcon2" href="#" onclick="togglePassword(2)" >
							<i id="eyeIconInner2" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
						</a>

					</div>
				</div>
				</div>
			</div>';
			}
			if ($i == 8) {
				if ($birth) $strFormValue = '12/45/1922';
			}

			if ($i == 12) {
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

			if ($i == 10) {
				print '<select class="form-selectx" name="dept">
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
		?>
		<div class="mb-3 row">
			<center>
				<input type="Submit" class="btn btn-primary w-md waves-effect waves-light" name="SubmitForm" value="Hantar">
				<!-- <input type="Reset" class="btn btn-secondary w-md waves-effect waves-light" name="ResetForm" value="Isi semula"> -->
			</center>
		</div>
	</div>
</form>
<?php include("footer.php");

print '
<script language="JavaScript">
    function togglePassword(index) {
        var passwordInput = document.getElementById("password" + index);
        var eyeIconInner = document.getElementById("eyeIconInner" + index);

        // Toggle the password field visibility
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIconInner.classList.remove("mdi-eye-off-outline");
            eyeIconInner.classList.add("mdi-eye-outline");
        } else {
            passwordInput.type = "password";
            eyeIconInner.classList.remove("mdi-eye-outline");
            eyeIconInner.classList.add("mdi-eye-off-outline");
        }
    }
</script>';
?>