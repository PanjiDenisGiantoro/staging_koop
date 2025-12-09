<?php

/*********************************************************************************
 *          Project		:	IKOOP
 *          Filename		: 	accountHL.php
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

if (get_session('Cookie_userID') == "") {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
if (get_session("Cookie_groupID") == 0) {
	$userID		= get_session('Cookie_userID');
	$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

$sFileName		= "accountHL.php";
$sActionFileName = "accountHL.php";
$title     		= "Kemaskini Akaun Hutang Lapuk";
$cPercentDiskaun = 5;
$cJumTolak = 10;


//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = 1;
$FormLabel[$a]   	= "* Nomor Anggota";
$FormElement[$a] 	= "memberID";
if (get_session("Cookie_groupID") == 0) {
	$FormType[$a]	  	= "hiddentext";
} else {
	$FormType[$a]	  	= "text";
}
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "12";


$a++;
$FormLabel[$a]   	= "Baki Pokok (RP)";
$FormElement[$a] 	= "ByrnPokok";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Baki Untung (RP)";
$FormElement[$a] 	= "ByrnUntg";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Baki Pinjaman (RP)";
$FormElement[$a] 	= "BalancePinjm";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Denda Lewat Sebulan (RP)";
$FormElement[$a] 	= "LateCharges";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Jumlah Bulan Terdahulu";
$FormElement[$a] 	= "SumBlnSb";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Jumlah Bulan Terkini";
$FormElement[$a] 	= "SumBlnLatest";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Catatan";
$FormElement[$a] 	= "catatan";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Baki Pinjaman Sebenar (RP)";
$FormElement[$a] 	= "BalanceHL";
$FormType[$a]	  	= "text";
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
		print '<script>alert("Sila masukkan Baki Pinjaman");</script>';
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
			"Emel," .
			"Alamat," .
			"Poskod ," .
			"Bandar," .
			"Negeri," .
			"TelRumah," .
			"TelBimbit," .
			"NoKenderaan," .
			"Model," .
			"NoEnjin," .
			"NoChasis," .
			"KeupayaanEnjin," .
			"TahunPembuatan," .
			"JumlahPremium," .
			"Jum_Pre_Kasar," .
			"Jum_Pre_Bersih," .
			"Cover_Note," .
			"Tkh_Mula," .
			"KelayakanNCD," .
			"HPCompany," .
			"TanggalTamatInsuran," .
			"Pemandu1," .
			"NoKPPemandu1," .
			"Pemandu2," .
			"NoKPPemandu2," .
			"Pemandu3," .
			"NoKPPemandu3," .
			"Pemandu4," .
			"NoKPPemandu4," .
			"Cermin," .
			"insuranYear," .
			"MotoPlan," .
			"status," .
			"applyDate)" .
			" VALUES (" .
			tosql($insuranNo, "Text") . "," .
			tosql($isAnggota, "Text") . "," .
			tosql($memberID, "Text") . "," .
			tosql($newIC, "Text") . "," .
			tosql($userName, "Text") . "," .
			tosql($emel, "Text") . "," .
			tosql($alamat, "Text") . "," .
			tosql($postcode, "Text") . "," .
			tosql($city, "Text") . "," .
			tosql($stateID, "Text") . "," .
			tosql($homeNo, "Text") . "," .
			tosql($mobileNo, "Text") . "," .
			tosql($carNo, "Text") . "," .
			tosql($carModel, "Text") . "," .
			tosql($carChasis, "Text") . "," .
			tosql($carEnjin, "Text") . "," .
			tosql($carEnjinKeupayaan, "Text") . "," .
			tosql($carTahun, "Text") . "," .
			tosql($carJumPremium, "Text") . "," .
			tosql($Jum_Pre_Kasar, "Text") . "," .
			tosql($Jum_Pre_Bersih, "Text") . "," .
			tosql($Cover_Note, "Text") . "," .
			tosql($Tkh_Mula, "Text") . "," .
			tosql($carNCD, "Text") . "," .
			tosql($hpCompany, "Text") . "," .
			tosql($carDateEndIns, "Text") . "," .
			tosql($carDriver1, "Text") . "," .
			tosql($carKP1, "Text") . "," .
			tosql($carDriver2, "Text") . "," .
			tosql($carKP2, "Text") . "," .
			tosql($carDriver3, "Text") . "," .
			tosql($carKP3, "Text") . "," .
			tosql($carDriver4, "Text") . "," .
			tosql($carKP4, "Text") . "," .
			tosql($carScreen, "Text") . "," .
			tosql($carYearIns, "Text") . "," .
			tosql($planId, "Text") . "," .
			"'0'," .
			tosql($applyDate, "Text") . ")";
		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Permohonan insuran kenderaan telah didaftarkan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
					//window.close();
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="Data">
	<tr>
		<td colspan="2" class="Data"><b class="maroonText">' . strtoupper($title) . '</b></td></tr>';


//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	//Print Header Maklumat Pemohon
	if ($i == 1) print '<tr><td class=Header colspan=2>Maklumat Hutang Lapuk:</td></tr>';
	//Print Header Maklumat kenderaan
	if ($i == 12) print '<tr><td class=Header colspan=2>Maklumat Kenderaan :</td></tr>';
	//Print Header Maklumat Tambahan
	if ($i == 24) print '<tr><td class=Header colspan=2>Maklumat Tambahan:</td></tr>';

	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . ' :</td>';
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
	//}	
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td></tr>';
}
print '
		<tr><td colspan=2 align=center class=Data>
			<input type=Submit name=SubmitForm class="but" value=Hantar>
			</td>
		</tr>';
print '</table></form>';
include("footer.php");
