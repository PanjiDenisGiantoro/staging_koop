<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	insuranEdit.php
 *          Date 		: 	05/01/2015
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.location="index.php";</script>';
}

$sFileName		= "?vw=insuranEdit&mn=$mn";
$sActionFileName = "?vw=insuranEdit&mn=$mn&pk=" . $pk;
$title     		= "Kemaskini Maklumat Insuran Kenderaan";
//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

//--- Set Config : 
$cPercentDiskaun = 5;
$cJumTolak = 10;

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
//-- Perpare Plan type:
$planList = array();
$planVal  = array();
$GetPlan = ctGeneral("", "R");
if ($GetPlan->RowCount() <> 0) {
	while (!$GetPlan->EOF) {
		array_push($planList, $GetPlan->fields(name));
		array_push($planVal, $GetPlan->fields(ID));
		$GetPlan->MoveNext();
	}
}

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = $a + 1;
$FormLabel[$a]   	= "Kartu Identitas";
$FormElement[$a] 	= "NoKP";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "* Nama Lengkap";
$FormElement[$a] 	= "Nama";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Emel<br>(Pastikan sah)";
$FormElement[$a] 	= "email";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Alamat";
$FormElement[$a] 	= "Alamat";
$FormType[$a]	  	= "textareax";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "* Poskod";
$FormElement[$a] 	= "Poskod";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckNumeric);
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "* Bandar";
$FormElement[$a] 	= "Bandar";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Negeri";
$FormElement[$a] 	= "Negeri";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Nombor Telefon (Rumah)";
$FormElement[$a] 	= "TelRumah";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Nombor Telefon";
$FormElement[$a] 	= "TelBimbit";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Nombor Kenderaan";
$FormElement[$a] 	= "NoKenderaan";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "9";

$a++;
$FormLabel[$a]   	= "Jumlah Perlindungan (RP)";
$FormElement[$a] 	= "JumlahPremium";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";


$a++;
$FormLabel[$a]   	= "Jumlah Premium Kasar (RP)";
$FormElement[$a] 	= "Jum_Pre_Kasar";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Jumlah Premium Bersih (RP)";
$FormElement[$a] 	= "Jum_Pre_Bersih";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";


$a++;
$FormLabel[$a]   	= "Cover Note";
$FormElement[$a] 	= "Cover_Note";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Tahun perlindungan insuran (yyyy)";
$FormElement[$a] 	= "insuranYear";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "4";

$a++;
$FormLabel[$a]   	= "Tanggal Mula Insuran (dd/mm/yyyy)";
$FormElement[$a] 	= "Tkh_Mula";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Tanggal Tamat Insuran (dd/mm/yyyy)";
$FormElement[$a] 	= "TanggalTamatInsuran";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Tanggal Mohon";
$FormElement[$a] 	= "applyDate";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
//Details Insuran
$strInsuran = "SELECT * FROM insurankenderaan WHERE ID = '" . $pk . "'";
$GetInsuran = &$conn->Execute($strInsuran);

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->

if ($Kira <> "") {
	//--- BEGIN : Calc Insuran ---
	if ($JumlahPremium <> "") {
		$B = $JumlahPremium - $cJumTolak;
		$C = $B - ($B * ($cPercentDiskaun / 100));
		$JumBayar = $C + 10;
		$JumlahPerlindungan = $JumBayar;
	} else {
		$Kira = "";
		print '<script>alert("Sila masukkan Jumlah Perlindungan");</script>';
	}
	//--- END   : Calc Insuran ---		
}

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
	//Date Format:
	$dateStarted   = substr($dateStarted, 6, 4) . '-' . substr($dateStarted, 3, 2) . '-' . substr($dateStarted, 0, 2);
	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");

		//DateFormat Mula:
		$getdateStartIns = explode("/", $Tkh_Mula);
		$Tkh_Mula = $getdateStartIns[2] . '/' . sprintf("%02s",  $getdateStartIns[1]) . '/' . sprintf("%02s",  $getdateStartIns[0]);

		//DateFormat:TanggalTamatInsuran	
		$getdateIns = explode("/", $TanggalTamatInsuran);
		$TanggalTamatInsuran = $getdateIns[2] . '/' . sprintf("%02s",  $getdateIns[1]) . '/' . sprintf("%02s",  $getdateIns[0]);

		$sSQL = "";
		$sWhere = "";
		$sWhere = "ID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE InsuranKenderaan SET " .
			"NoKP=" . tosql($NoKP, "Text") .
			", Nama=" . tosql($Nama, "Text") .
			", email=" . tosql($email, "Text") .
			", Alamat=" . tosql($Alamat, "Text") .
			", Poskod =" . tosql($Poskod, "Text") .
			", Bandar=" . tosql($Bandar, "Text") .
			", Negeri=" . tosql($Negeri, "Text") .
			", TelRumah=" . tosql($TelRumah, "Text") .
			", TelBimbit=" . tosql($TelBimbit, "Text") .
			", NoKenderaan=" . tosql($NoKenderaan, "Text") .
			", JumlahPremium=" . tosql($JumlahPremium, "Text") .
			", Jum_Pre_Kasar=" . tosql($Jum_Pre_Kasar, "Text") .
			", Jum_Pre_Bersih=" . tosql($Jum_Pre_Bersih, "Text") .
			", Cover_Note=" . tosql($Cover_Note, "Text") .
			", JumlahPerlindungan=" . tosql($JumlahPerlindungan, "Text") .
			", Tkh_Mula=" . tosql($Tkh_Mula, "Text") .
			", TanggalTamatInsuran=" . tosql($TanggalTamatInsuran, "Text") .
			", insuranYear=" . tosql($insuranYear, "Text") .
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		///	print $sSQL;	
		print '<script>
					alert ("Maklumat insuran kenderaan telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="picture" value="' . $pic . '">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	//Print Header Maklumat Pemohon
	if ($i == 1) print '<tr><td colspan=2><div class="card-header">Maklumat Pemohon</div></td></tr>';
	//Print Header Maklumat kenderaan
	if ($i == 11) print '<tr><td colspan=2><div class="card-header">Maklumat Kenderaan</div></td></tr>';
	//	if ($i == 17) print '<tr><td class=Data align=right width="250">Kira Perlu dibayar :</td><td class=Data ><a href="javascript:jKira();">Kira</a></td></tr>';
	//Print Header Maklumat Tambahan
	if ($i == 16) print '<tr><td colspan=2><div class="card-header">Maklumat Tambahan</div></td></tr>';

	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . '</td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	//DateFormat:
	if ($FormElement[$i] == "TanggalTamatInsuran") {
		$strFormValue = toDate("d/m/yy", $GetInsuran->fields('TanggalTamatInsuran'));
	} else if ($FormElement[$i] == "Tkh_Mula") {
		$strFormValue = toDate("d/m/yy", $GetInsuran->fields('Tkh_Mula'));
	} else if ($FormElement[$i] == "applyDate") {
		$strFormValue = toDate("d/m/yy", $GetInsuran->fields('applyDate'));
	} else {
		$strFormValue = tohtml($GetInsuran->fields($FormElement[$i]));
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
	print '&nbsp;</td>';
	if ($cnt == 0) print '</tr>';
}

if ((get_session("Cookie_groupID") == 2) or (get_session("Cookie_groupID") == 1)) {
	print '<tr><td colspan=4 align=center class=Data>
			<input type="hidden" name="pk" value="' . $pk . '">
			<input type=Submit name=SubmitForm class="btn btn-primary" value=Kemaskini>
			</td>
		</tr>';
}

print '</table>
</form></div>';
print '
<script	language="JavaScript">
function jKira(){
	    var jumPremium =document.getElementsByName("JumlahPremium")[0].value;
		if(jumPremium=="")
		{
			alert(\'Masukkan Jumlah sebenar.\');
		}else{
				var B = jumPremium- 10;
				var C = B- (B*(5/100));
				var JumBayar = C+10;
				JumlahPerlindungan= JumBayar;
				document.getElementsByName("JumlahPerlindungan")[0].value = JumlahPerlindungan;
		}
		//document.getElementsByName("JumlahPremium").value(); 
		// alert(\'Hi there-\' + jumPremium);
		//jumPremium.addEventListener("keyup",  function(e) {alert(\'Hi there\')});	
}
</script>
';
include("footer.php");
