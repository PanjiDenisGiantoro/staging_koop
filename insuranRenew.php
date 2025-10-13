<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	insuranEdit.php
 *          Date 		: 	01/01/2015
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.location="index.php";</script>';
}
$sFileName		= "?vw=insuranRenew&mn=$mn";
$sActionFileName = "?vw=insuranList&mn=$mn";
$title     		= "Maklumat Pembaharuan Insuran Kenderaan";

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
$FormLabel[$a]   	= "Kad Pengenalan";
$FormElement[$a] 	= "NoKP";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "NoAnggota";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "* Nama Penuh";
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
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Negeri";
$FormElement[$a] 	= "Negeri";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array();
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
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "9";

/*$a++;
$FormLabel[$a]   	= "Model";
$FormElement[$a] 	= "Model";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "No Enjin";
$FormElement[$a] 	= "NoEnjin";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "No Chasis";
$FormElement[$a] 	= "NoChasis";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Keupayaan Enjin";
$FormElement[$a] 	= "KeupayaanEnjin";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Tahun Dibuat";
$FormElement[$a] 	= "TahunPembuatan";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";
*/
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
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

/*
$a = $a + 1;
$FormLabel[$a]   	= "Kira Perlu dibayar";
$FormElement[$a] 	= "Kira";
$FormType[$a]	  	= "submit";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";	


$a = $a + 1;
$FormLabel[$a]   	= "*Jumlah Sebenar";
$FormElement[$a] 	= "JumlahPerlindungan";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank,CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";	

$a++;
$FormLabel[$a]   	= "Kelayakan NCD";
$FormElement[$a] 	= "KelayakanNCD";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "HP Company";
$FormElement[$a] 	= "HPCompany";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";
*/
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
$FormElement[$a] 	= "TarikhTamatInsuran";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";
/*
$a++;
$FormLabel[$a]   	= "Nama Pemandu Tambahan(1)";
$FormElement[$a] 	= "Pemandu1";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "NomborKP Baru Pemandu Tambahan(1)";
$FormElement[$a] 	= "NoKPPemandu1";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";


$a++;
$FormLabel[$a]   	= "Nama Pemandu Tambahan(2)";
$FormElement[$a] 	= "Pemandu2";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "NomborKP Baru Pemandu Tambahan(2)";
$FormElement[$a] 	= "NoKPPemandu2";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Nama Pemandu Tambahan(3)";
$FormElement[$a] 	= "Pemandu3";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "NomborKP Baru Pemandu Tambahan(3)";
$FormElement[$a] 	= "NoKPPemandu3";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";


$a++;
$FormLabel[$a]   	= "Nama Pemandu Tambahan(4)";
$FormElement[$a] 	= "Pemandu4";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "NomborKP Baru Pemandu Tambahan(4)";
$FormElement[$a] 	= "NoKPPemandu4";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";
$a++;
$FormLabel[$a]   	= "'Wind Screen'";
$FormElement[$a] 	= "Cermin";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Motorist P.A";
$FormElement[$a] 	= "MotoPlan";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $planList;
$FormDataValue[$a]	= $planVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";
*/
$a++;
$FormLabel[$a]   	= "Tarikh Mohon";
$FormElement[$a] 	= "applyDate";
$FormType[$a]	  	= "textx";
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
		print '<script>alert("Sila masukkan Jumlah Perlindungan (Premium)");</script>';
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
		$getdateIns = explode("/", $TarikhTamatInsuran);
		$TarikhTamatInsuran = $getdateIns[2] . '/' . sprintf("%02s",  $getdateIns[1]) . '/' . sprintf("%02s",  $getdateIns[0]);
		//Check if Anggota?
		if ($NoAnggota > 0) {
			$Anggota = "Y";
		} else {
			$Anggota = "N";
		}
		$sSQL	= "INSERT INTO insurankenderaan (" .
			"insuranNo," .
			"Anggota," .
			"NoAnggota," .
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
			//"Pemandu1,".
			//"NoKPPemandu1,".
			//"Pemandu2,".
			//"NoKPPemandu2,".
			//"Pemandu3,".
			//"NoKPPemandu3,".
			//"Pemandu4,".
			//"NoKPPemandu4,".
			//"Cermin,".
			//"KelayakanNCD,".
			//"HPCompany,".
			//"MotoPlan,".
			//"Model,".
			//"NoEnjin,".
			//"NoChasis,".
			//"KeupayaanEnjin,".
			//"TahunPembuatan,".
			"status," .
			"applyDate)" .
			" VALUES (" .
			tosql($insuranNo, "Text") . "," .
			tosql($Anggota, "Text") . "," .
			tosql($NoAnggota, "Text") . "," .
			tosql($NoKP, "Text") . "," .
			tosql($Nama, "Text") . "," .
			tosql($email, "Text") . "," .
			tosql($Alamat, "Text") . "," .
			tosql($Poskod, "Text") . "," .
			tosql($Bandar, "Text") . "," .
			tosql($Negeri, "Text") . "," .
			tosql($TelRumah, "Text") . "," .
			tosql($TelBimbit, "Text") . "," .
			tosql($NoKenderaan, "Text") . "," .
			tosql($JumlahPremium, "Text") . "," .
			tosql($Jum_Pre_Kasar, "Text") . "," .
			tosql($Jum_Pre_Bersih, "Text") . "," .
			tosql($Cover_Note, "Text") . "," .
			tosql($Tkh_Mula, "Text") . "," .
			tosql($TarikhTamatInsuran, "Text") . "," .
			tosql($insuranYear, "Text") . "," .
			//tosql($carNCD , "Text") . ",".
			//tosql($hpCompany , "Text") . ",".
			//tosql($carModel, "Text") . ",".
			//tosql($carChasis, "Text") . ",".				  
			//tosql($carEnjin, "Text") . ",".
			//tosql($carEnjinKeupayaan , "Text") . ",".
			// tosql($carTahun, "Text") . ",".
			//tosql($carDriver1 , "Text") . ",".
			// tosql($carKP1 , "Text") . ",".
			//tosql($carDriver2, "Text") . ",".
			// tosql($carKP2 , "Text") . ",".
			// tosql($carDriver3 , "Text") . ",".
			// tosql($carKP3 , "Text") . ",".
			//  tosql($carDriver4, "Text") . ",".
			//  tosql($carKP4 , "Text") . ",".
			//  tosql($carScreen , "Text") . ",".	
			//tosql($planId , "Text") . ",".
			"'0'," .
			tosql($applyDate, "Text") . ")";
		//print $sSQL;
		$rs = &$conn->Execute($sSQL);

		$sSQLUpdIns	= "Update insurankenderaan set status='98' where ID = '" . $pk . "'";
		$rsUpd = &$conn->Execute($sSQLUpdIns);
		print '<script>
					alert ("Permohonan pembaharuan insuran kenderaan telah didaftarkan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
					//window.close();
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
	//if ($i == 17) print '<tr><td class=Data align=right width="250">Kira Perlu dibayar :</td><td class=Data ><a href="javascript:jKira();">Kira</a></td></tr>';
	//Print Header Maklumat Tambahan
	if ($i == 16) print '<tr><td colspan=2><div class="card-header">Maklumat Tambahan</div></td></tr>';

	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . '</td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	//DateFormat:
	//DateFormat:
	if ($FormElement[$i] == "applyDate") {
		$strFormValue = date("d/m/Y");
	} else {
		if ($FormElement[$i] == "Tkh_Mula" ||  $FormElement[$i] == "TarikhTamatInsuran" || $FormElement[$i] == "JumlahPremium" || $FormElement[$i] == "insuranYear") {
			$strFormValue = "";
		} else {
			$strFormValue = tohtml($GetInsuran->fields($FormElement[$i]));
		}
	}
	//if($FormElement[$i]=="JumlahPremium" || $FormElement[$i]=="JumlahPerlindungan" || $FormElement[$i]=="KelayakanNCD" || $FormElement[$i]=="Cermin" || $FormElement[$i]=="insuranYear")
	//{$strFormValue = "";}
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


print '<tr><td colspan=4 align=center class=Data>
			<input type="hidden" name="pk" value="' . $pk . '">
			<input type=Submit name=SubmitForm class="btn btn-primary" value=Daftar>
			</td>
		</tr>';


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
