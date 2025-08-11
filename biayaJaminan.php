<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	biayaJaminan.php
 *          Date 		: 	12/12/2018
 *********************************************************************************/
include("header.php");

include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$sFileName		= "?vw=biayaJaminan&mn=5";
$sActionFileName = "?vw=biayaSahMember&mn=5";
$title     		= "Pengesahan Jaminan Pembiayaan";

$loID = $pk;
//print $loID;
$pk = get_session('Cookie_userID');


/*****************************UPLOAD GAJI && IC*****************************/
##uploadwingajiP.php
$max_size = "1048576"; // Max size in BYTES (1MB)
if (isset($_POST['uploadwingaji']) == 'Muat Naik Fail') //if ($action == 'upload') 
{
	alert("Fail berjaya dimuat naik.");

	$filename = $_FILES["filename"]["name"];
	$file_basename = substr($filename, 0, strripos($filename, '.')); // get file extention
	$file_ext = substr($filename, strripos($filename, '.')); // get file name
	$filesize = $_FILES["filename"]["size"];
	$allowed_file_types = array('.doc', '.docx', '.rtf', '.pdf', '.jpg', '.png', '.gif');

	if (in_array($file_ext, $allowed_file_types) && ($filesize < 1048576)) {
		// Rename file
		$newfilename = md5($file_basename) . $file_ext;
		if (file_exists("upload_gaji/" . $newfilename)) {
			// file already exists error
			//echo "You have already uploaded this file.";
			alert("You have already uploaded this file.");
		} else {
			move_uploaded_file($_FILES["filename"]["tmp_name"], "upload_gaji/" . $newfilename);

			$sSQL = "";
			$sWhere = "";
			$sWhere = "userID=" . tosql($pk, "Text");
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL	= "UPDATE userloandetails SET " .
				" gaji_img= '" . $newfilename . "' ";
			$sSQL = $sSQL . $sWhere;
			$rs = &$conn->Execute($sSQL);

			//copy($_FILES["filename"]["tmp_name"],"upload_resit/".$_FILES["filename"]["name"]) or die("<b>Unknown error!</b>");
			//echo "File uploaded successfully.";	
			alert("File uploaded successfully.");
			print '<script language="javascript">';
			if ($pk) {
				print 'window.location.href="?vw=biayaSahMember"';
				//}
			} else {
				print 'window.location.href="?vw=biayaSahMember"';
			}
			print '</script>';
		}
	} elseif (empty($file_basename)) {
		// file selection error
		//echo "Please select a file to upload.";
		alert("Please select a file to upload.");
	} elseif ($filesize > 1048576) {
		// file size error
		//echo "The file you are trying to upload is too large.";
		alert("The file you are trying to upload is too large.");
	} else {
		// file type error
		echo "Only these file typs are allowed for upload: " . implode(', ', $allowed_file_types);
		unlink($_FILES["file"]["tmp_name"]);
	}
}

##uploadwinicP.php
if (isset($_POST['uploadwinkp']) == 'Muat Naik KP') //if ($action == 'upload')
{
	$filenamekp = $_FILES["filenamekp"]["name"];
	$file_basename = substr($filenamekp, 0, strripos($filenamekp, '.')); // get file extention
	$file_ext = substr($filenamekp, strripos($filenamekp, '.')); // get file name
	$filesize = $_FILES["filenamekp"]["size"];
	$allowed_file_types = array('.doc', '.docx', '.rtf', '.pdf', '.jpg', '.png', '.gif');

	if (in_array($file_ext, $allowed_file_types) && ($filesize < 1048576)) {
		// Rename file
		$newfilename = md5($file_basename) . $file_ext;
		if (file_exists("upload_ic/" . $newfilename)) {
			// file already exists error
			alert("You have already uploaded this file.");
		} else {
			move_uploaded_file($_FILES["filenamekp"]["tmp_name"], "upload_ic/" . $newfilename);

			$sSQL = "";
			$sWhere = "";
			$sWhere = "userID=" . tosql($pk, "Text");
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL	= "UPDATE userloandetails SET " .
				" ic_img= '" . $newfilename . "' ";
			$sSQL = $sSQL . $sWhere;
			$rs = &$conn->Execute($sSQL);

			//copy($_FILES["filename"]["tmp_name"],"upload_resit/".$_FILES["filename"]["name"]) or die("<b>Unknown error!</b>");
			alert("File uploaded successfully.");
			/*
			print '<script language="javascript">';
	if($pk) {
		print 'window.location.href="?vw=biayaSahMember"';
	
	}else {print 'window.location.href="?vw=biayaSahMember"';}
	print '</script>';	*/
		}
	} elseif (empty($file_basename)) {
		// file selection error
		alert("Please select a file to upload.");
	} elseif ($filesize > 1048576) {
		// file size error
		alert("The file you are trying to upload is too large.");
	} else {
		// file type error
		alert("Only these file typs are allowed for upload: " . implode(', ', $allowed_file_types));
		unlink($_FILES["file"]["tmp_name"]);
	}
}
/*****************************UPLOAD GAJI && IC*****************************/

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

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
$FormLabel[$a]   	= "No KP Baru";
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
$FormLabel[$a]   	= "Bil tanggungan";
$FormElement[$a] 	= "biltggn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Bil. sekolah";
$FormElement[$a] 	= "bilsek";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "Jabatan/Cawangan";
$FormElement[$a] 	= "dept";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

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
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";


//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

$strMember = "SELECT a . * , b.memberID, b.newIC, b.dateBirth, b.job, b.address, 
			b.city, b.postcode, b.stateID, b.departmentID, b.approvedDate
			FROM users a, userdetails b 
			WHERE a.userID = '" . $pk . "' AND a.userID = b.userID ";
$GetMember = &$conn->Execute($strMember);

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if (isset($_POST['SubmitForm']) && $agree <> "") {
	alert("prosese");
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
		$updatedByJmn 	= get_session("Cookie_userName");
		$updatedDateJmn = date("Y-m-d H:i:s");

		if ($field == "1") {
			$sSQL = "";
			$sWhere = "";
			$sWhere = "loanID=" . tosql($loID, "Text");
			$sWhere = " WHERE (" . $sWhere . ")";
			$status = "statuspID1";
			$updatedDate = "updatedDateJmn" . $field;
			$sSQL	= "UPDATE loans SET " .
				"statuspID1= " . tosql(1, "Number") .
				", updatedDateJmn=" . tosql($updatedDateJmn, "Text") .
				", updatedByJmn=" . tosql($updatedByJmn, "Text");
			$sSQL = $sSQL . $sWhere;
			$rs = &$conn->Execute($sSQL);
			//$activity = "Mengemaskini maklumat pembiayaan anggota";
			//if($rs) activityLog($sSQL, $activity, get_session('Cookie_userID'), get_session("Cookie_userName"));		
			print '<script>
					alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
		}
		if ($field == "2") {
			$sSQL = "";
			$sWhere = "";
			$sWhere = "loanID=" . tosql($loID, "Text");
			$sWhere = " WHERE (" . $sWhere . ")";
			$status = "statuspID2";
			$updatedDate = "updatedDateJmn" . $field;
			$sSQL	= "UPDATE loans SET " .
				$status . " = " . tosql(1, "Number") .
				", updatedDateJmn2=" . tosql($updatedDateJmn, "Text") .
				", updatedByJmn=" . tosql($updatedByJmn, "Text");
			$sSQL = $sSQL . $sWhere;
			$rs = &$conn->Execute($sSQL);
			//$activity = "Mengemaskini maklumat pembiayaan anggota";
			//if($rs) activityLog($sSQL, $activity, get_session('Cookie_userID'), get_session("Cookie_userName"));		
			print '<script>
					alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
		}

		if ($field == "3") {
			$sSQL = "";
			$sWhere = "";
			$sWhere = "loanID=" . tosql($loID, "Text");
			$sWhere = " WHERE (" . $sWhere . ")";
			$status = "statuspID3";
			$updatedDate = "updatedDateJmn" . $field;
			$sSQL	= "UPDATE loans SET " .
				$status . " = " . tosql(1, "Number") .
				", updatedDateJmn3=" . tosql($updatedDateJmn, "Text") .
				", updatedByJmn=" . tosql($updatedByJmn, "Text");
			$sSQL = $sSQL . $sWhere;
			$rs = &$conn->Execute($sSQL);
			//$activity = "Mengemaskini maklumat pembiayaan anggota";
			//if($rs) activityLog($sSQL, $activity, get_session('Cookie_userID'), get_session("Cookie_userName"));		
			print '<script>
					alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
		}
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post enctype="multipart/form-data">
<input type="hidden" name="picture" value="' . $pic . '">
<tr><td>&nbsp;</td></tr>
<h5 class="card-title"><i class="fas fa-file-signature"></i>&nbsp;' . strtoupper($title) . ' &nbsp;</h5>
	';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;

	if ($i == 1) print '<div class="card-header mt-3">BUTIR-BUTIR PENJAMINAN</div>';
	if ($i == 9) print '<div class="card-header mt-3">PENYATA PENDAPATAN/PERBELANJAAN</div>';

	if ($cnt == 1) print '<div class="row m-3">';
	print '<label class="col-md-2 col-form-label">' . $FormLabel[$i];
	//if (!($i == 1 or $i == 2 or $i == 8 or $i ==30 or $i == 32)) 
	// print ':';
	print ' </label>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<div class="col-md-4 bg-danger">';
	else
		print '<div class="col-md-4">';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetMember->fields($FormElement[$i]));
	//if ($strFormValue == '') $strFormValue = $$FormElement[$i];	

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


	if ($i == 3) {
		$stradd = str_replace("<pre>", "", $GetMember->fields(address));
		$stradd = str_replace("</pre>", "", $stradd);

		$add = $stradd . ', <br />' . tohtml($GetMember->fields(city)) . ', <br />  ' . tohtml($GetMember->fields(postcode)) . ', ' . dlookup("general", "name", "ID=" . $GetMember->fields(stateID));

		print '<b>' . $add . '</b>';
	}

	if ($i == 6) {
		$dept = dlookup("general", "name", "ID=" . $GetMember->fields(departmentID));
		print '<b>' . $dept . '</b>';
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	//<input type="text" name="sellUserName" class="Data" value="'.$sellUserName.'" onfocus="this.blur()" size="50">   
	print '&nbsp;</div>';
	if ($cnt == 0) print '</div>';
}

$userID = dlookup("loans", "userID", "loanID='" . $loID . "'");
$memberID = dlookup("userdetails", "memberID", "userID='" . $userID . "'");
$deptID = dlookup("userdetails", "departmentID", "userID='" . $userID . "'");
$nameB =  dlookup("users", "name", "userID='" . $userID . "'");
//$amount =  dlookup("loans", "outstandingAmt", "loanID='".$loID."'");	
$amount =  dlookup("loans", "loanAmt", "loanID='" . $loID . "'");
$dept = dlookup("general", "name", "ID=" . $deptID);
$memberIDy = $GetMember->fields(memberID);

if ($pid = dlookup("loans", "penjaminID1", "loanID='" . $loID . "'")) {
	if ($pid == $memberIDy) $field = 1;
}
if ($pid = dlookup("loans", "penjaminID2", "loanID='" . $loID . "'")) {
	if ($pid == $memberIDy) $field = 2;
}
if ($pid = dlookup("loans", "penjaminID3", "loanID='" . $loID . "'")) {
	if ($pid == $memberIDy)	$field = 3;
}

if ($valAddDpt <= 0) $valAddDpt = 0;
if ($addDpt) $valAddDpt++;
if ($minDpt) $valAddDpt--;
print '<div class="card-header mt-3">PENYATA PENDAPATAN &nbsp;&nbsp;
    
		<input type="hidden" name="valAddDpt" value="' . $valAddDpt . '" class="but">
		<!--input type="submit" name="addDpt" value="Tambah" class="but"><input type="submit" name="minDpt" value="Kurang" class="but"--></div>';

$SQLdpt = "";
$SQLdpt	= "SELECT * FROM `userstates` WHERE userID = '" . $pk . "' AND payType = 'A'";
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

		print '<div class="row m-3">
                                                                                            <label class="col-md-1 col-form-label"></label>
                                                                                            <label class="col-md-3 col-form-label"><input name="idD' . $i . '" type="hidden" value="' . $id . '">
					<input name="stateIDd' . $i . '" type="hidden" value="' . $stateID . '"> 
					<input name="nameD' . $i . '" type="hidden" class="Data" value="' . $name . '" size="20" align="right" onfocus="this.blur()">' . strtoupper($name) . ': 
                                                                                        </label>
                                                                                        <label class="col-md-8 col-form-label"><b><input name="valueD' . $i . '" size="10" maxlength="15" type="hidden" value="' . $value . '"> ' . $value . '</b>&nbsp;</label>                                                                        
                                                                                        </div>
                                    ';
		$totD = $i;
		$i++;
		$tot += $value;
		$rsdpt->MoveNext();
	}
	print '<div class="row m-3">
                                                                                            <label class="col-md-1 col-form-label"></label>
                                                                                            <label class="col-md-3 col-form-label"><b>Jumlah : </b></label>
                                                                                        <label class="col-md-8 col-form-label"><b>' . number_format($tot, 2) . '</b>&nbsp;</label>                                                                        
                                                                                        </div>
                                    ';
}

//print_r($_POST);
if ($valAddBlj <= 0) $valAddBlj = 0;
if ($addBlj) $valAddBlj++;
if ($minBlj) $valAddBlj--;
print '<div class="card-header mt-3">PENYATA PERBELANJAAN &nbsp;&nbsp;
   <input type="hidden" name="valAddBlj" value="' . $valAddBlj . '" class="but">
		<!--input type="submit" name="addBlj" value="Tambah" class="but"><input type="submit" name="minBlj" value="Kurang" class="but"--></div>';

$SQLblj = "";
$SQLblj	= "SELECT * FROM `userstates` WHERE userID = '" . $pk . "' AND payType = 'B'";
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

		print 	'<div class="row m-3">
                                                                                            <label class="col-md-1 col-form-label"></label>
                                                                                            <label class="col-md-3 col-form-label"><input name="idJ' . $i . '" type="hidden" value="' . $id . '">
                                                                                                <input name="stateIDj' . $i . '" type="hidden" value="' . $stateID . '"> 
                                                                                                <input name="nameJ' . $i . '" type="hidden" class="Data" value="' . $name . '" size="20" align="right" onfocus="this.blur()">' . strtoupper($name) . ':
                                                                                        </label>
                                                                                        <label class="col-md-8 col-form-label"><b><input name="valueJ' . $i . '" size="10" maxlength="15" type="hidden" value="' . $value . '"> ' . $value . '</b>&nbsp;</label>                                                                        
                                                                                        </div>                                    
                                                ';

		$totJ = $i;
		$i++;
		$tot += $value;
		$rsblj->MoveNext();
	}
	print '<div class="row m-3">
                                                                                            <label class="col-md-1 col-form-label"></label>
                                                                                            <label class="col-md-3 col-form-label"><b>Jumlah : </b></label>
                                                                                        <label class="col-md-8 col-form-label"><b>' . number_format($tot, 2) . '</b>&nbsp;</label>                                                                        
                                                                                        </div>
';
}

$uplgaji = dlookup("userloandetails", "gaji_img", "userID=" . tosql($pk, "Text"));
if (@$uplgaji != '' && @$uplgaji != '0') {
	$lnkgaji = '<a href="upload_gaji/' . $uplgaji . '" class="text-primary" target=blank><i class="far fa-file-pdf text-danger"></i> Lampiran Slip Gaji</a>';
} else {
	$lnkgaji = '';
}

$uplkp = dlookup("userloandetails", "ic_img", "userID=" . tosql($pk, "Text"));
if (@$uplkp != '' && @$uplkp != '0') {
	$lnkkp = '<a href="upload_ic/' . $uplkp . '" class="text-primary" target=blank><i class="far fa-file-pdf text-danger"></i> Lampiran Kad Pengenalan</a>';
} else {
	$lnkkp = '';
}

print '<div class="card-header mt-3"> 
<!--input type="button" name="GetPicture" value="Upload Slip Gaji" class="btn btn-secondary w-md waves-effect waves-light" onclick= "Javascript:(window.location.href=\'?vw=uploadwingajiP&userID=' . $pk . '\')" -->
<!-- input type="button" name="GetPicture" value="Upload IC" class="btn btn-secondary w-md waves-effect waves-light" onclick= "Javascript:(window.location.href=\'?vw=uploadwinicP&userID=' . $pk . '\')" --> Sila Pastikan Slip Gaji dengan IC dimuatnaik terlebih dahulu.
</div>

<div class="row m-3">
    <label class="col-md-2 col-form-label"></label>
    <label class="col-md-8 col-form-label">';

if (@$uplgaji != '' && @$uplgaji != '0') {
	echo $lnkgaji;
} else {
	print ' 
                                    File (max size: ' . $max_size . ' bytes/' . ($max_size / 1024) . ' kb):<br>
                                                    <input type="file" class="form-control" name="filename"><br>                                                    
                                                    <input type="hidden" name="update" value="' . $up . '">
                                                    <input type="submit" class="btn btn-secondary w-md waves-effect waves-light" name="uploadwingaji" value="Muat Naik Fail">

                    ';
}
print '</label><label class="col-md-2 col-form-label"></label></div>';

print '<div class="row m-3">
    <label class="col-md-2 col-form-label"></label>
    <label class="col-md-8 col-form-label">';

if (@$uplkp != '' && @$uplkp != '0') {
	echo $lnkkp;
} else {
	print ' 
                                    File (max size: ' . $max_size . ' bytes/' . ($max_size / 1024) . ' kb):<br>
                                            <input type="file" class="form-control" name="filenamekp"><br>                                            
                                            <input type="hidden" name="update" value="' . $up . '">
                                            <input type="submit" class="btn btn-secondary w-md waves-effect waves-light" name="uploadwinkp" value="Muat Naik KP">

                    ';
}
print '</label><label class="col-md-2 col-form-label"></label></div>';


print '<div class="row m-3">
    <label class="col-md-2 col-form-label"></label>
    <label class="col-md-8 col-form-label">
<textarea  rows="10" class="form-control" wrap="hard" name="syarat" readonly>Saya seperti nama dan alamat diatas dengan ini bersetuju menjadi penjamin kepada Encik/Cik ' . $nameB . ' dari jabatan/cawangan ' . $dept . ' untuk pembiayaan sebanyak RM ' . $amount . '.

Saya mengaku bahawa segala maklumat yang terkandung di dalam borang ini adalah benar dan lengkap bagi menunjukkan kedudukan kewangan dan kemampuan kredit saya.</textarea>

			
		</label>
                <label class="col-md-2 col-form-label"></label>
                </div>';
print '<div class="row">
            <label class="col-md-12 col-form-label"><center>
            <input type="hidden" name="pk" value="' . $loID . '"><input type="checkbox" class="form-check-input" name="agree">&nbsp; Setuju</center></label>
    </div>    
    
<div class="row">
            <label class="col-md-12 col-form-label"><center><input type="hidden" name="field" value="' . $field . '">
            <input type="Submit" class="btn btn-primary w-md waves-effect waves-light" name=SubmitForm value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');"><br />&nbsp;
            <!--input type=Submit  class="but" value=Simpan--></center></label>
    </div>

</form>';

include("footer.php");
print '
<script language="JavaScript">
	var allChecked=false;
	
	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="agree" && e.elements[c].checked) {
				pk = e.elements[c].value;
				//strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          //alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
	        } else {
	          //if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          e.submit();
	          //window.location.href ="memberApply.php?pk=" + strStatus;
	          //window.location.href ="' . $sActionFileName . '";
			  //}
	        }
	      }
	    }

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Sila pilih satu rekod sahaja untuk kemaskini status\');
			} else {
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}



</script>';
