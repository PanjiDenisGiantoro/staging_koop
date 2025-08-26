<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	komoditi_add.php
 *          Date 		: 	15/04/2017
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
if (get_session("Cookie_groupID") == 0) {
	$userID		= get_session('Cookie_userID');
	$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

$sFileName		= "?vw=komoditi_API&mn=907";
$sActionFileName = "komoditiAPI.php?userID=" . $no_anggota . "&amount=" . $amt . "&no_sijil=" . $no_sijil . "&loanid=" . $loanid . "";
$title     		= "Perbarui Sertifikat Komoditi";


//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

//--- Prepare item type
// $itemList = Array();
// $itemVal  = Array();
// $Getitem = ctGeneral("","X");
// if ($Getitem->RowCount() <> 0){
// 	while (!$Getitem->EOF) {
// 	array_push ($itemList, $Getitem->fields(name));
// 	array_push ($itemVal, $Getitem->fields(ID));
// 	$Getitem->MoveNext();
// 	}
// }

$a = 1;
$FormLabel[$a]   	= "* Nomor Anggota";
$FormElement[$a] 	= "no_anggota";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a = $a + 1;
$FormLabel[$a]   	= "Nama";
$FormElement[$a] 	= "nama_anggota";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "35";
$FormLength[$a]  	= "50";

$a = $a + 1;
$FormLabel[$a]   	= "Nombor Rujukan";
$FormElement[$a] 	= "name_type";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a = $a + 1;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "loanid";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "10";

$a = $a + 1;
$FormLabel[$a]   	= "Jumlah Pembiayaan (RM)";
$FormElement[$a] 	= "amt";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

// $a++;
// $FormLabel[$a]   	= "* Nombor Sijil";
// $FormElement[$a] 	= "no_sijil";
// $FormType[$a]	  	= "text";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array(CheckBlank);
// $FormSize[$a]    	= "20";
// $FormLength[$a]  	= "12";

// $a++;
// $FormLabel[$a]   	= "* Komoditi";
// $FormElement[$a] 	= "item";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "* Tanggal Pembelian Sertifikat (dd/mm/yyyy)";
$FormElement[$a] 	= "tarikh_beli";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "* Masa Pembelian Sijil (hhmmss)";
$FormElement[$a] 	= "masa_beli";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormData[$a]   	= "";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "10";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
if ($SubmitForm <> "") {
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
	if (count($strErrMsg) == "0") {
		//DateFormat:
		$getdateIns = explode("/", $tarikh_beli);
		$tarikh_beli = $getdateIns[2] . '/' . sprintf("%02s",  $getdateIns[1]) . '/' . sprintf("%02s",  $getdateIns[0]);

		// $sSQL	= "INSERT INTO komoditi (
		// 			" ."no_sijil,
		// 			" ."userID,
		// 			" ."loanID,
		// 			" ."amount,
		// 			" ."itemType,
		// 			" ."tarikh_beli,
		// 			" ."masa_beli
		// 		   )"." VALUES (
		// 			".tosql($no_sijil, "Text").",
		// 			".tosql($no_anggota, "Text").",
		// 			".tosql($loanid, "Text").",
		// 			".tosql($amt, "Number").",
		// 			".tosql($item, "Number") . ", 				
		// 			".tosql($tarikh_beli, "Text") . ", 
		// 			".tosql($masa_beli, "Text").")";
		// 	$rs = &$conn->Execute($sSQL);
		print '<script>
		alert ("Nombor Sijil Komoditi telah dimasukkan ke dalam sistem.");
		window.location.href = "' . $sActionFileName . '";
		</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
<h5 class="card-title">' . strtoupper($title) . '</h5>
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="Data">
';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	//Print Header Maklumat Pemohon
	if ($i == 1) print '<div class="card-header mt-3 mb-3">Informasi Komoditas</div>';
	print '<div class="m-1 row"><label class="col-md-3 col-form-label">' . $FormLabel[$i];
	// if (!($i == 4)) print ':';
	print ' </label>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<div class="col-md-4 bg-danger">';
	else
		print '<div class="col-md-4">';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  


	if ($FormElement[$i] == "tarikh_beli") {
		$strFormValue = date("d/m/Y");
	} else {
		$strFormValue = $$FormElement[$i];
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
		if (get_session("Cookie_groupID") == 1 or get_session("Cookie_groupID") == 2) {
?>&nbsp;<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open('selKomoditi.php','sel','top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no');">
<?php
		}
	}



	// 	if($i==7){
	// 	print '<select name="item" class="form-selectx">
	// 	<option value="">- Pilihan Komoditi -';
	// 	for ($j = 0; $j < count($itemList); $j++) {
	// 		print '	<option value="'.$itemVal[$j].'" ';
	// 	if ($item == $itemVal[$j]) print ' selected';
	// 		print '>'.$itemList[$j];
	// 	}
	// 	print '</select>';
	// }
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '</div></div>';
}
print '<div class="mb-3 mt-3">
                                    <center>
                                            <input type=Submit name=SubmitForm class="btn btn-md btn-primary" value=Hantar>
                                    </center>
                                </div>';

print '</table></form>';
include("footer.php");
?>