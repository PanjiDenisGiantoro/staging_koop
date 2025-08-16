<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	welfareApply.php
 *          Date 		: 	1/6/2006
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
	// $userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

$Cookie_userID = get_session('Cookie_userID');
$Cookie_userName = get_session("Cookie_userName");
$sFileName		= "?vw=welfareApply&mn=$mn";
$sActionFileName = "?vw=welfare&mn=$mn";
$title     		= "Permohonan Bantuan Kebajikan";
if (get_session("Cookie_groupID") == 0) {
	//$sqlC = "select * from loans where userID =".$userID." and isApproved <> 1";
	//$rsC = &$conn->Execute($sqlC);
	//if($rsC->RowCount() == 0) 
	$sActionFileName = "?vw=welfareInProcess&mn=$mn";
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
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "12";

$a = $a + 1;
$FormLabel[$a]   	= "* Kad Pengenalan";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "12";
$FormLength[$a]  	= "12";

// $a = $a + 1;
// $FormLabel[$a]   	= "Muat Naik Dokumen Kebajikan";
// $FormElement[$a] 	= "dump";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

$a = $a + 1;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "dump";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a = $a + 1;
$FormLabel[$a]   	= "* Kod Kebajikan";
$FormElement[$a] 	= "welfareCode";
if (get_session("Cookie_groupID") == 0) {
	$FormType[$a]	  	= "displayonly";
} else {
	$FormType[$a]	  	= "displayonly";
}
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "8";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "* Tujuan Permohonan";
$FormElement[$a] 	= "purpose";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "40";
$FormLength[$a]  	= "7";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

if ($SubmitForm <> "") {
	$pass = 1;
	$sSQL = "SELECT	* FROM welfares 
				 WHERE status <> 1 AND status <> 2 AND status <> 9 AND userID = '" . $userID . "'
				 ORDER BY applyDate ASC";


	$GetWelfare = &$conn->Execute($sSQL);
	if ($GetWelfare->RowCount() <> 0) {
		print '<script>
					alert ("Terdapat permohonan belum siap diproses untuk anggota ini!");
					window.location.href = "?vw=welfare&mn=' . $mn . '";
				</script>';
		$pass = 0;
	}
}
//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($pass) {

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
		$pre_welfare = date("Y-m");
		$sSQL = "SELECT max( right( welfareNo, 3 ) ) as no FROM `welfares` WHERE month( applyDate ) = " . $month . " AND year( applyDate ) =" . $year;
		$rs = &$conn->Execute($sSQL);
		$no = $rs->fields('no');
		if ($no) {
			$no = (int)$no;
			$no++;
		} else {
			$no = 1;
		}
		$no = sprintf("%03s",  $no);
		$welfareNo = $pre_welfare . '-' . $no;
		$sSQL	= "INSERT INTO welfares (" .
			"welfareNo," .
			"welfareType," .
			"userID," .
			"purpose," .
			"welfare_img," .
			"applyDate)" .
			" VALUES (" .
			tosql($welfareNo, "Text") . "," .
			tosql($welfareType, "Number") . "," .
			tosql($memberID, "Text") . "," .
			tosql($purpose, "Text") . "," .
			tosql($picture, "Text") . "," .
			tosql($applyDate, "Text") . ")";
		//print $sSQL;
		$rs = &$conn->Execute($sSQL);

		if (!isset($Cookie_userID)) $uid = $userID;
		else $uid =  $Cookie_userID;
		if (!isset($Cookie_userName)) $uname = $loginID;
		else $uname =  $Cookie_userName;
		$activity = "Permohonan Kebajikan - " . $memberID;
		if ($rs) activityLog($sSQL, $activity, $uid, $uname, 1);

		//####################################
		print '<script>
alert ("Permohonan telah didaftarkan ke dalam sistem.");  window.location.href = "' . $sActionFileName . '";
</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
<input type="hidden" name="welfareType" value="' . $welfareType . '">
<input type="hidden" name="picture" value="' . $picwelfare . '">
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="Data">
<div><h5 class="card-title"><i class="mdi mdi-application"></i>&nbsp;' . strtoupper($title) . '</h5></div>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<div class="card-header">MAKLUMAT ANGGOTA</div>';
	if ($i == 3) {
		print '<tr><td colspan=2><div class="card-header">PRA KELAYAKAN PERMOHONAN KEBAJIKAN</div></td></tr>';
	}
	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . '</td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($FormElement[$i] == "applyDate") {
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
	// 	$Gambar = "upload_welfare/" . $picwelfare;
	// 	if ($i == 3) {
	// 
?>
	 <?
		// 		print '<input type="button" class="btn btn-secondary waves-effect" name="GetPicture" value="Upload Dokumen Kebajikan" onclick= "Javascript:(window.location.href=\'?vw=uploadwinwelfare&mn=6&userID=' . $memberID . '\')">&nbsp;&nbsp;';
		// 		
		?>
	 <?
		// 		if ($picwelfare) {
		// 			print '<input type=button value="Paparan Dokumen" class="btn btn-outline-danger waves-effect" onClick=window.open(\'upload_welfare/' . $picwelfare . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
		// 		}
		// 		
		?>
	 <?
		// 	}
		if ($i == 1) {
			if (get_session("Cookie_groupID") == 1 or get_session("Cookie_groupID") == 2) {

				print '
					<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="document.MyForm.userID.value=\'\';window.open(\'selMember.php\',\'sel\',\'top=10,left=10,width=1300,height=700,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
			}
			print '&nbsp;<label><input type="text" name="name" class="form-control" value="' . $namaaAng . '" onfocus="this.blur()" size="50"></label>';
		}
		if ($i == 4) {
			print '
				<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="document.MyForm.userID.value=\'\'; var userid = document.MyForm.userID.value; window.open(\'selWelfare.php?userID=\'+userid,\'sel\',\'top=10,left=10,width=1300,height=700,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
				<input type="text" name="welfareName" class="form-control" value="' . $welfareName . '" onfocus="this.blur()" size="50">';
		}
		//--- End   : Call function FormEntry ---------------------------------------------------------  
		print '&nbsp;</td></tr>';
	}

	print '
<tr><td class="" align="right" valign="top"></td>
	<td><input type="Submit" name="SubmitForm" class="btn btn-primary" value="Mohon Kebajikan">&nbsp;
	</td>
</tr>';

	print '
</table>
</form>';

	include("footer.php");
