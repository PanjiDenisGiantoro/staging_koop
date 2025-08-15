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

if (get_session("Cookie_groupID") == 0) {
	$userID		= get_session('Cookie_userID');
	$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$oldIC		= dlookup("userdetails", "oldIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

$sFileName		= "memberApplyT.php";
$sActionFileName = "memberApplyT.php";
$title     		= "Permohonan Mengundurkan Diri / Pensiun dari Layanan";

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
$FormLabel[$a]   	= "* Nomor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a = $a + 1;
$FormLabel[$a]   	= "Nama";
$FormElement[$a] 	= "userName";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a = $a + 1;
$FormLabel[$a]   	= "No KTP Baru";
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
$FormType[$a]	  	= "select";
$FormData[$a]   	= $terminateList;
$FormDataValue[$a]	= $terminateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";
/*
$a = $a + 1;
$FormLabel[$a]   	= "No KTP Lama";
$FormElement[$a] 	= "oldIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";
*/
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
		$sSQL = "";
		$sSQL	= "INSERT INTO userterminate (" .
			"userID," .
			"applyDate," .
			"type)" .
			" VALUES (" .
			tosql($userID, "Text") . "," .
			tosql($applyDate, "Text") . "," .
			tosql($type, "Text") . ")";
		//		print $sSQL;
		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Permohonan Berhenti telah didaftarkan ke dalam sistem.");
					window.location.href="' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
?>
<div class="maroon" align="left"><b>&nbsp;<?= strtoupper($title) ?></b></div>
<div style="width: 500px; text-align:left">
	<div>&nbsp;</div>
	<form name="MyForm" action=<? print $sFileName; ?> method=post>
		<input type="hidden" name="userID" value="<? print $userID; ?>">
		<input type="hidden" name="shareType" value="<? print $shareType; ?>">
		<input type="hidden" name="unitOnHand" value="<? print $unitOnHand; ?>">
		<table class="lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
			<tr>
				<td class="borderallblue" align="left" valign="middle">
					<div class="headerblue"><b>FORMULIR PENGAJUAN MENGUNDURKAN DIRI / PENSIUN DARI LAYANAN</b></div>
				</td>
			</tr>
			<tr>
				<td class="borderleftrightbottomblue">
					<table border="0" cellspacing="6" cellpadding="0" width="100%" align="center">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<?
						if (get_session("Cookie_groupID") == 0) {
							$uid = get_session('Cookie_userID');
							$pk	= dlookup("userterminate", "ID", "userID=" . tosql($uid, "Text"));
						}
						if ($pk) {
						?>
							<tr>
								<td colspan="3" align="center" height="50" valign="middle">-Permohonan Anggota Telah Dibuat-</b></td>
							</tr>
							<?
						} else {
							//--- Begin : Looping to display label -------------------------------------------------------------
							for ($i = 0; $i < count($FormLabel); $i++) {
								print '<tr valign="top"><td align="right">' . $FormLabel[$i] . ' :</td>';
								if (in_array($FormElement[$i], $strErrMsg))
									print '<td class="errdata">';
								else
									print '<td>';
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

								if ($i == 0) {
									if (get_session("Cookie_groupID") == 1 or get_session("Cookie_groupID") == 2) {
							?>&nbsp;<input type="button" value="..." onclick="window.open('selMember.php','sel','top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no');"><?
																																																		}
																																																	}
																																																	//--- End   : Call function FormEntry ---------------------------------------------------------  
																																																			?>&nbsp;
				</td>
			</tr><?
							}
						}
						if (!$pk) {
					?>
		<tr>
			<td colspan="2" align="center">
				<div>&nbsp;</div>
				<input type="Submit" name="SubmitForm" value="Kirim">
				<div>&nbsp;</div>
			</td>
		</tr>
	<? } ?>
		</table>
		</td>
		</tr>
		</table>
	</form>
</div>
<?
include("footer.php");
?>