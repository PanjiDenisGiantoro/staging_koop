<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberApplyT.php
 *          Date 		: 	31/03/2004
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");

date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$userID		= get_session('Cookie_userID');
$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
$userName	= get_session('Cookie_fullName');
$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
$oldIC		= dlookup("userdetails", "oldIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));


$sFileName		= "?vw=memberApplyT&mn=1";
$sActionFileName = "?vw=memberApplyT&mn=1";
$title     		= "Pengajuan Mengundurkan Diri dari Layanan";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

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

$a = 0;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "dump";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a = $a + 1;
$FormLabel[$a]   	= "Nomor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a = $a + 1;
$FormLabel[$a]   	= "Nama Anggota";
$FormElement[$a] 	= "userName";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a = $a + 1;
$FormLabel[$a]   	= "Nomor Kartu Identitas";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "* Jenis";
$FormElement[$a] 	= "type";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $terminateList;
$FormDataValue[$a]	= $terminateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Keanggotaan Meninggal";
$FormElement[$a] 	= "statusM";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('YA', 'TIDAK');
$FormDataValue[$a]	= array('1', '0');
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
	//--- Begin : Call function FormValidation ---  
	for ($i = 0; $i < count($FormLabel); $i++) {
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
	//--- BEGIN	: Checking member id ---
	if ($memberID <> "") {
		if (dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text")) == "") {
			array_push($strErrMsg, 'memberID');
			print '- <font class=redText>Nomor Anggota - ' . $memberID . ' tidak wujud...!</font><br>';
			$userName = "";
			$newIC = "";
			$oldIC = "";
			$unitOnHand = "";
		} else {
			$userID = dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text"));
			$userName 	= dlookup("users", "name", "userID=" . tosql($userID, "Text"));
			$newIC 	= dlookup("userdetails", "newIC", "userID=" . tosql($userID, "Text"));
			$oldIC 	= dlookup("userdetails", "oldIC", "userID=" . tosql($userID, "Text"));
			$unitOnHand = dlookup("userdetails", "totalShare", "userID=" . tosql($userID, "Text"));
		}
	}
	//--- END  	: Checking member id ---
	if (count($strErrMsg) == "0") {
		$applyDate = date("Y-m-d H:i:s");
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sSQL	= "INSERT INTO userterminate (" .
			"userID," .
			"applyDate," .
			"henti_img," .
			"statusM," .
			"type)" .
			" VALUES (" .
			tosql($userID, "Text") . "," .
			tosql($applyDate, "Text") . "," .
			tosql($picture, "Text") . "," .
			tosql($statusM, "Number") . "," .
			tosql($type, "Text") . ")";
		$rs = &$conn->Execute($sSQL);
		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			" VALUES ('Pengajuan Berhenti Anggota - $userID', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '1')";
		$rs = &$conn->Execute($sqlAct);
		print '<script>
					alert("Pengajuan menjadi anggota telah tercatat di sistem.");
					window.location.href="' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
?>
<h4 class="card-title"><?= strtoupper($title) ?></h4>

<form name="MyForm" action=<? print $sFileName; ?> method=post>
	<input type="hidden" name="userID" value="<? print $userID; ?>">
	<input type="hidden" name="shareType" value="<? print $shareType; ?>">
	<input type="hidden" name="unitOnHand" value="<? print $unitOnHand; ?>">
	<input type="hidden" name="picture" value="<? print $pichenti; ?>">

	<?php
	$uid = get_session('Cookie_userID');
	$pk	= dlookup("userterminate", "ID", "userID=" . tosql($uid, "Text"));


	//--- Begin : Looping to display label -------------------------------------------------------------
	for ($i = 0; $i < count($FormLabel); $i++) {
		//print '<div class="card-header mb-3">DOKUMEN BERHENTI / BERSARA</div>';
		if ($i == 0) print '<div class="card-header mb-3">DOKUMEN PENGUNDURAN / PENSIUN</div>';
		if ($i == 1) print '<div class="card-header mb-3">INFORPASI ANGGOTA</div>';

		print '<div class="mb-2 row"><label class="col-md-2 col-form-label">' . $FormLabel[$i] . '</label>';
		if (in_array($FormElement[$i], $strErrMsg))
			print '<div class="col-md-8 bg-danger">';
		else
			print '<div class="col-md-8">';
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


		$Gambar = "upload_henti/" . $pichenti;
		if ($i == 0) {
	?>
			<?
			print '<input type="button" class="btn btn-secondary waves-effect" name="GetPicture" value="Upload Dokumen Pengunduran / Pensiun" onclick= "Javascript:(window.location.href=\'?vw=uploadwinhentiP&mn=1&userID=' . $pk . '\')">&nbsp;&nbsp;';
			?>
			<?
			if ($pichenti) {
				print '<input type=button value="Paparan Dokumen" class="btn btn-outline-danger waves-effect" onClick=window.open(\'upload_henti/' . $pichenti . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
			}
			?>
		<?
		}
		//--- End   : Call function FormEntry ---------------------------------------------------------  
		?></div>
		</div><?php
			}
				?>
	<div class="mb-3 mt-3 row">
		<label class="col-md-2 col-form-label"></label>
		<div class="col-md-8">
			<input type="Submit" name="SubmitForm" class="btn btn-primary w-md waves-effect waves-light" value="Kirim">
		</div>
	</div>

</form>
</div>
<?php
include("footer.php");
?>