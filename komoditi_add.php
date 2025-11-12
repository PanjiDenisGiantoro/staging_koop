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

$sFileName		= "?vw=komoditi_add&mn=944";
$sActionFileName = "?vw=komoditi_list&mn=944";
$title     		= "Perbarui Sertifikat Komoditi";
//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

//--- Prepare item type
$itemList = array();
$itemVal  = array();
$Getitem = ctGeneral("", "X");
if ($Getitem->RowCount() <> 0) {
	while (!$Getitem->EOF) {
		array_push($itemList, $Getitem->fields(name));
		array_push($itemVal, $Getitem->fields(ID));
		$Getitem->MoveNext();
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
$FormLabel[$a]   	= "Nama Anggota";
$FormElement[$a] 	= "nama_anggota";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a = $a + 1;
$FormLabel[$a]   	= "Rujukan Pembiayaan";
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
$FormLabel[$a]   	= "Jumlah Pembiayaan (RP)";
$FormElement[$a] 	= "amt";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Nomor Sertifikat";
$FormElement[$a] 	= "no_sijil";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "* Komoditas";
$FormElement[$a] 	= "item";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "* Tanggal Pembelian Sertifikat (dd/mm/yyyy)";
$FormElement[$a] 	= "tarikh_beli";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";
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

		$sSQL	= "INSERT INTO komoditi (					
					" . "no_sijil,
					" . "userID,
					" . "loanID,
					" . "amount,
					" . "itemType,
					" . "tarikh_beli,
					" . "sijil_komoditi
				)" . " VALUES (				
					" . tosql($no_sijil, "Text") . ",
					" . tosql($no_anggota, "Text") . ",
					" . tosql($loanid, "Text") . ",
					" . tosql($amt, "Number") . ",
					" . tosql($item, "Number") . ", 				
					" . tosql($tarikh_beli, "Text") . ", 
					" . tosql($picture, "Text") . ")";
		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . ' Permohonan Sijil Komoditi - ' . $no_sijil;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 2);

		print '<script>
			alert ("Nombor Sijil Komoditi telah dimasukkan ke dalam sistem.");
			window.location.href = "' . $sActionFileName . '";
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
	<input type="hidden" name="picture" value="<? print $pic; ?>">

	<?php
	if (get_session("Cookie_groupID") == 0) {
		$uid = get_session('Cookie_userID');
		$pk	= dlookup("komoditi", "komoditi_ID", "userID=" . tosql($uid, "Text"));
	}
	//--- Begin : Looping to display label -------------------------------------------------------------
	for ($i = 0; $i <= count($FormLabel); $i++) {
		//Print Header Maklumat Pemohon
		if ($i == 0) print '<div class="card-header mt-3 mb-3">Dokumen Sertifikat Komoditas</div>';
		if ($i == 1) print '<div class="card-header mt-3 mb-3">Informasi Komoditas</div>';
		print '<div class="mb-2 row"><label class="col-md-2 col-form-label">' . $FormLabel[$i] . '</label>';
		if (in_array($FormElement[$i], $strErrMsg))
			print '<div class="col-md-8 bg-danger">';
		else
			print '<div class="col-md-8">';
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

		if ($i == 7) {
			print '<select name="item" class="form-selectx">
	<option value="">- Pilihan Komoditi -';
			for ($j = 0; $j < count($itemList); $j++) {
				print '	<option value="' . $itemVal[$j] . '" ';
				if ($item == $itemVal[$j]) print ' selected';
				print '>' . $itemList[$j];
			}
			print '</select>';
		}

		$Gambar = "upload_sijilkomoditi/" . $pic;
		if ($i == 0) {
			print '<input type="button" class="btn btn-secondary waves-effect" name="GetPicture" value="Perbarui Sertifikat Komoditas" onclick= "Javascript:(window.location.href=\'?vw=uploadwinkomoditi&mn=944&userID=' . $pk . '\')">';

			if ($pic) {
				print '&nbsp;<input type=button value="Paparan Dokumen" class="btn btn-outline-danger" onClick=window.open(\'upload_sijilkomoditi/' . $pic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
	';
			}
		}
		//--- End   : Call function FormEntry ---------------------------------------------------------  
		print '</div></div>';
	}
	print '<div class="mb-3 mt-3 row">
<label class="col-md-2 col-form-label"></label>
<div class="col-md-8">
	<input type="Submit" name="SubmitForm" class="btn btn-primary w-md waves-effect waves-light" value="Kirim">
</div>
</div>';

	print '</form>';
	include("footer.php");
?>