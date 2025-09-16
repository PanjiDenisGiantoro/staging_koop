<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	minitApply.php
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

$sFileName		= "?vw=minitApply&mn=$mn";
$sActionFileName = "?vw=minit&mn=$mn";
$mainTitle     		= "Notulen Rapat";

$updatedBy 		= get_session("Cookie_userName");

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

$a = 1;
$FormLabel[$a]   	= "ALK Yang Hadir";
$FormElement[$a] 	= "alk";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Daftar Nama ALK";
$FormElement[$a] 	= "alkNames";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "255";

$a++;
$FormLabel[$a]   	= "Judul Rapat";
$FormElement[$a] 	= "title";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "255";

$a++;
$FormLabel[$a]   	= "Isi Rapat";
$FormElement[$a] 	= "content";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "4";

$a++;
$FormLabel[$a]      = "Tanggal Rapat";
$FormElement[$a]    = "applyDate";
$FormType[$a]       = "date";
$FormData[$a]       = "";
$FormDataValue[$a]  = "";
$FormCheck[$a]      = array();
$FormSize[$a]       = "20";
$FormLength[$a]     = "20";

$a++;
$FormLabel[$a]   	= "Disediakan Oleh";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {

	$updatedDate = date("Y-m-d H:i:s");
	$applyDate = saveDateDb($applyDate);

	if (isset($_POST['picture'])) $pic = $_POST['picture'];
	//--- Begin : Call function FormValidation ---
	for ($i = 0; $i < count($FormLabel); $i++) {
		for ($j = 0; $j < count($FormCheck[$i]); $j++) {
			FormValidation(
				$FormLabel[$i],
				$FormElement[$i],
				$FormElement[$i],
				$FormCheck[$i][$j],
				$i
			);
		}
	}
	//--- End   : Call function FormValidation ---

	if (count($strErrMsg) == 0) {
	$year = date("Y");
	$month = date("m");
	$pre_minit = "$year-$month"; // e.g., 2025-05

	// Query latest minitNo that starts with current year-month
	$sqlGetLast = "SELECT MAX(minitNo) AS lastNo FROM minit WHERE minitNo LIKE '$pre_minit-%'";
	$rsLast = &$conn->Execute($sqlGetLast);
	$lastNo = $rsLast && !$rsLast->EOF ? $rsLast->fields('lastNo') : '';

	// Extract and increment sequence number
	if ($lastNo) {
		$parts = explode('-', $lastNo); // e.g., ['2025', '05', '012']
		$no = isset($parts[2]) ? (int)$parts[2] + 1 : 1;
	} else {
		$no = 1;
	}
	$no = sprintf("%03d", $no); // Pad to 3 digits
	$minitNo = $pre_minit . '-' . $no;

	// Prepare and execute INSERT
	$sSQL = "INSERT INTO minit (" .
		"minitNo, alkIDs, title, content, minit_img, applyDate, updatedDate, updatedBy) VALUES (" .
		tosql($minitNo, "Text") . "," .
		tosql($_POST['alk'], "Text") . "," .
		tosql($title, "Text") . "," .
		tosql($content, "Text") . "," .
		tosql($pic, "Text") . "," .
		tosql($applyDate, "Text") . "," .
		tosql($updatedDate, "Text") . "," .
		tosql($updatedBy, "Text") . ")";
	$rs = &$conn->Execute($sSQL);

	// Log the activity
	$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
		" VALUES ('Minit Mesyuarat - $minitNo', 'INSERT', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '9')";
	$conn->Execute($sqlAct);

	// Show alert and redirect
	echo '<script>
		alert("Minit Mesyuarat Telah Dimasukkan Ke Dalam Sistem.");
		window.location.href="' . $sActionFileName . '";
	</script>';
}

}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
?>
<h4 class="card-title"><?= strtoupper($mainTitle) ?></h4>

<form name="MyForm" action=<? print $sFileName; ?> method=post>
	<input type="hidden" name="picture" value="<? print $pic; ?>">

	<?php

	//--- Begin : Looping to display label -------------------------------------------------------------
	for ($i = 0; $i < count($FormLabel); $i++) {
		if ($i == 0) print '<div class="card-header mb-3">DOKUMEN MINIT MESYUARAT</div>';
		if ($i == 1) print '<div class="card-header mb-3">MAKLUMAT MESYUARAT</div>';

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
	
		$Gambar = "upload_minit/" . $pic;
		if ($i == 0) {
			print '<input type="button" class="btn btn-secondary waves-effect" name="GetPicture" value="Upload Dokumen Minit Mesyuarat" onclick= "Javascript:(window.location.href=\'?vw=uploadwinminit&mn='.$mn.'&pk=' . $pk . '\')">';
			if ($pic) {
				print '&nbsp;<input type=button value="PAPARAN DOKUMEN" class="btn btn-outline-danger waves-effect" onClick=window.open(\'upload_minit/' . $pic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
			}
		}

		if ($i == 1) {			
			print '&nbsp;<input type="button" class="btn btn-info" value="Pilih ALK Yang Hadir" onclick="window.open(\'alkselection.php\',\'popup\',\'width=800,height=600,scrollbars=yes,resizable=yes\');">';

		
		}

//--- End   : Call function FormEntry ---------------------------------------------------------  
print '</div>
</div>';
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

print '
<script language="JavaScript">
<script>
document.MyForm.onsubmit = function() {
    if (document.MyForm.test.value.trim() == "") {
        alert("Sila pilih sekurang-kurangnya satu ALK yang hadir.");
        return false;
    }
};
</script>';
