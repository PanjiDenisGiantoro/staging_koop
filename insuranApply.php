<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	insuranApply.php
 *          Date 		: 	05/01/2015
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
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

$sFileName = "?vw=insuranApply&mn=$mn";
$sActionFileName = "?vw=insuranListNewReg&mn=$mn";


$title = "Permohonan Insuran Kenderaan";

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

$a = 1;
$FormLabel[$a]   	= "* Nombor Anggota";
$FormElement[$a] 	= "memberID";
if (get_session("Cookie_groupID") == 0) {
	$FormType[$a]	  	= "hiddentext";
} else {
	$FormType[$a]	  	= "textx";
}
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "* Kad Pengenalan";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "* Nama Penuh";
$FormElement[$a] 	= "userName";
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
$FormElement[$a] 	= "alamat";
$FormType[$a]	  	= "textareax";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "* Poskod";
$FormElement[$a] 	= "postcode";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckNumeric);
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "* Bandar";
$FormElement[$a] 	= "city";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Negeri";
$FormElement[$a] 	= "stateID";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Nombor Telefon Rumah";
$FormElement[$a] 	= "homeNo";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Nombor Telefon";
$FormElement[$a] 	= "mobileNo";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Nombor Kenderaan";
$FormElement[$a] 	= "carNo";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "9";

$a++;
$FormLabel[$a]   	= "Jumlah Perlindungan (RP)";
$FormElement[$a] 	= "carJumPremium";
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
$FormElement[$a] 	= "carYearIns";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "4";

$a++;
$FormLabel[$a]   	= "Tarikh Mula Insuran (dd/mm/yyyy)";
$FormElement[$a] 	= "Tkh_Mula";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";


$a++;
$FormLabel[$a]   	= "Tarikh Tamat Insuran (dd/mm/yyyy)";
$FormElement[$a] 	= "carDateEndIns";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Tarikh Mohon";
$FormElement[$a] 	= "tarikhMohon";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";


//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

if ($Kira <> "") {
	//--- BEGIN : Check loan ID ---
	if ($carJumPremium <> "") {
		$B = $carJumPremium - $cJumTolak;
		$C = $B - ($B * ($cPercentDiskaun / 100));
		$JumBayar = $C + 10;
		$carInsPayment = $JumBayar;
	} else {
		$Kira = "";
		print '<script>alert("Sila masukkan Jumlah Perlindungan (Premium)");</script>';
	}
	//--- END   : Check loan ID ---	

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
	if (count($strErrMsg) == "0") {

		$applyDate = date("Y-m-d H:i:s");
		$year = date("Y");
		$month  = date('m');
		$pre_no = date("Y-m");
		$sSQL = "SELECT max( right( insuranNo, 3 ) ) as no FROM `insuranKenderaan` WHERE month( applyDate ) = " . $month . " AND year( applyDate ) =" . $year;
		$rs = &$conn->Execute($sSQL);
		$no = $rs->fields('no');
		if ($no) {
			$no = (int)$no;
			$no++;
		} else {
			$no = 1;
		}
		$no = sprintf("%03s",  $no);
		$insuranNo = $pre_no . '-' . $no;
		//DateFormat Mula:
		$getdateStartIns = explode("/", $Tkh_Mula);
		$Tkh_Mula = $getdateStartIns[2] . '/' . sprintf("%02s",  $getdateStartIns[1]) . '/' . sprintf("%02s",  $getdateStartIns[0]);

		//DateFormat:
		$getdateIns = explode("/", $carDateEndIns);
		$carDateEndIns = $getdateIns[2] . '/' . sprintf("%02s",  $getdateIns[1]) . '/' . sprintf("%02s",  $getdateIns[0]);
		//Check if Anggota?
		if ($memberID > 0) {
			$isAnggota = "Y";
		} else {
			$isAnggota = "N";
		}

		$sSQL	= "INSERT INTO insuranKenderaan (" .
			"insuranNo," .
			"Anggota ," .
			"NoAnggota ," .
			"NoKP," .
			"Nama," .
			"email," .
			"Alamat," .
			"Poskod ," .
			"Bandar," .
			"Negeri," .
			"TelRumah," .
			"TelBimbit," .
			"NoKenderaan," .
			"JumlahPremium," .
			"Jum_Pre_Kasar," .
			"Jum_Pre_Bersih," .
			"Cover_Note," .
			"Tkh_Mula," .
			"TarikhTamatInsuran," .
			"insuranYear," .
			"status," .
			"applyDate)" .
			" VALUES (" .
			tosql($insuranNo, "Text") . "," .
			tosql($isAnggota, "Text") . "," .
			tosql($memberID, "Text") . "," .
			tosql($newIC, "Text") . "," .
			tosql($userName, "Text") . "," .
			tosql($email, "Text") . "," .
			tosql($alamat, "Text") . "," .
			tosql($postcode, "Text") . "," .
			tosql($city, "Text") . "," .
			tosql($stateID, "Text") . "," .
			tosql($homeNo, "Text") . "," .
			tosql($mobileNo, "Text") . "," .
			tosql($carNo, "Text") . "," .
			tosql($carJumPremium, "Text") . "," .
			tosql($Jum_Pre_Kasar, "Text") . "," .
			tosql($Jum_Pre_Bersih, "Text") . "," .
			tosql($Cover_Note, "Text") . "," .
			tosql($Tkh_Mula, "Text") . "," .
			tosql($carDateEndIns, "Text") . "," .
			tosql($carYearIns, "Text") . "," .
			"'0'," .
			tosql($applyDate, "Text") . ")";
		//print $sSQL;
		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Permohonan insuran kenderaan telah didaftarkan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
					//window.close();
				</script>';
	}
}

//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="Data">
';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	//Print Header Maklumat Pemohon
	if ($i == 1) print '<tr><td class=card-header colspan=2>Maklumat Pemohon</td></tr>';
	if ($i == 2) print '<tr><td></td><td>(** Masukkan Nombor Anggota sekiranya pemohon adalah Anggota Koperasi)</td></tr>';
	//Print Header Maklumat kenderaan
	if ($i == 12) print '<tr><td class=card-header colspan=2>Maklumat Kenderaan</td></tr>';
	//Print Header Maklumat Tambahan
	if ($i == 17) print '<tr><td class=card-header colspan=2>Maklumat Tambahan</td></tr>';

	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . '</td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($FormElement[$i] == "tarikhMohon" || $FormElement[$i] == "Tkh_Mula") {
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
			print '
			<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="document.MyForm.memberID.value=\'\';window.open(\'selMemberIns.php\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
		}
	}
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td></tr>';
}
print '
		<tr><td colspan=2 align=center class=Data>
			<input type=Submit name=SubmitForm class="btn btn-primary" value=Hantar><br><br>
			</td>
		</tr>';
print '</table></form></div>';
include("footer.php");
