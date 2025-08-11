<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	adminEdit.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 2  or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>parent.location.href = "index.php";</script>';
}

$sFileName		= "?vw=adminEdit&mn=$mn";
$sActionFileName = "?vw=admin&mn=$mn";
$title     		= "Kemaskini Kakitangan Pengurusan Sistem";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = 1;
$a++;
$FormLabel[$a]   	= "* Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Id Log Masuk";
$FormElement[$a] 	= "loginID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Emel";
$FormElement[$a] 	= "email";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckEmailAddress);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Jenis Kumpulan";
$FormElement[$a] 	= "groupID";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $groupAList;
$FormDataValue[$a]	= $groupAVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a = $a + 1;
$FormLabel[$a]   	= "* Nombor Anggota<br>(Staf Anggota)";
$FormElement[$a] 	= "sellMemberID";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "a";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

//--- Inserting Anggota Name
$memberNameID = dlookup("users", "memberID", "userID=" . tosql($pk, "Text"));
if ($memberNameID != '') {
	$sellUserName = dlookup("users", "name", "userID=" . tosql($memberNameID, "Text"));
}
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

$strMember = "SELECT * FROM users WHERE userID = '" . $pk . "'";
$GetMember = &$conn->Execute($strMember);
//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
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
	if (!$anggota) {
		if ($sellMemberID == "") {
			array_push($strErrMsg, "sellMemberID");
			print '- <font class=redText>Masukkan no. anggota</font><br>';
		} else {
			if (dlookup("userdetails", "userID", "memberID=" . tosql($sellMemberID, "Text")) == "") {
				array_push($strErrMsg, 'sellMemberID');
				print '- <font class=redText>Nombor Anggota - ' . $sellMemberID . ' tidak sah...!</font><br>';
				$sellUserID = "";
				$sellUserName = "";
			} else {
				$sellUserID = dlookup("userdetails", "userID", "memberID=" . tosql($sellMemberID, "Text"));
				$sellUserName = dlookup("users", "name", "userID=" . tosql($sellUserID, "Text"));
			}
		}
	} else {
		$sellMemberID = '';
	}
	//--- End   : Call function FormValidation ---  
	$dateBirth = substr($dateBirth, 6, 4) . '-' . substr($dateBirth, 3, 2) . '-' . substr($dateBirth, 0, 2);
	$dateStarted   = substr($dateStarted, 6, 4) . '-' . substr($dateStarted, 3, 2) . '-' . substr($dateStarted, 0, 2);
	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$password = strtoupper(md5($password));
		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";

		$sSQL	= "UPDATE users SET " .
			" name=" . tosql($name, "Text") .
			", email=" . tosql($email, "Text") .
			", groupID=" . tosql($groupID, "Text") .
			", memberID=" . tosql($sellMemberID, "Text") .
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Kemaskini Maklumat Kakitangan - ' . $pk;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 9);

		print '<script>
					alert ("Maklumat kakitangan telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
?>
<div class="table-responsive">
	<form name="MyForm" action="<? print $sFileName; ?>" method=post>
		<!-- <table class="lightgrey"  border="0" cellpadding="6" cellspacing="6" width="100%" align="center"> -->
		<!-- <tr><td class="borderallteal" align="left" colspan="4"> -->
		<h5 class="card-title"><?php echo strtoupper($title); ?><br><small>SILA MASUKKAN MAKLUMAT KAKITANGAN PENGURUSAN SISTEM</small></h5>
		<!-- </td></tr> -->
		<!-- <tr> -->
		<!-- <td class="borderleftrightbottomteal"> -->
		<table border="0" cellpadding="3" cellspacing="6" width="100%" align="center">
			<?php
			//--- Begin : Looping to display label -------------------------------------------------------------
			for ($i = 1; $i <= count($FormLabel); $i++) {
				if ($cnt == 1) print '<tr valign="top">';
				print '<td class="Data" align="left">' . $FormLabel[$i];
				print '</div></td>';
				if (in_array($FormElement[$i], $strErrMsg))
					print '<td class="errdata">';
				else
					print '<td class="Data">';
				if ($i <> 1) print '</td><td class="Data"><td class="Data">';
				//--- Begin : Call function FormEntry ---------------------------------------------------------  
				$strFormValue = tohtml($GetMember->fields($FormElement[$i]));
				if ($FormType[$i] == 'textarea') {
					$strFormValue = str_replace("<pre>", "", $GetMember->fields($FormElement[$i]));
					$strFormValue = str_replace("</pre>", "", $strFormValue);
				}

				//	$strFormValue = $$FormElement[$i];
				if ($i == 6) $strFormValue = $memberID = $GetMember->fields('memberID');
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

				if ($i == 6) {
					print '
		<input type="button" class="btn btn-sm btn-info waves-effect waves-light" value="Pilih" onclick="window.open(\'selToMember.php?refer=d\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		<input type="text" name="sellUserName" class="form-controlx" value="' . $sellUserName . '" onfocus="this.blur()" size="50">';
				}
				//--- End   : Call function FormEntry ---------------------------------------------------------  
				print '</td>';
				if ($cnt == 0) print '</tr>';
			}
			?>
			<tr>
				<td class="Data" align="left">Bukan Anggota</td>
				<td class="Data"></td>
				<td class="Data"></td>
				<td class="Data"><input type="checkbox" class="form-check-input" name="anggota" <?php if ($memberNameID == '') {
																									print 'checked';
																								} ?>></td>
			</tr>
			<tr>
				<td colspan="4" align=center class="Data">&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="4" align=center class="Data">
					<input type=hidden name=memberID value="<?= $memberID ?>">
					<input type=hidden name=pk value="<?= $pk ?>">
					<!-- <input type=Reset name=ResetForm value="Batal" class="btn btn-sm btn-danger"> -->
					<input type=Submit name=SubmitForm value="Simpan" class="btn btn-md btn-primary">
				</td>
			</tr>
			<tr>
				<td colspan="4" align=center class="Data">&nbsp;
				</td>
			</tr>
		</table>
		</td>
		</tr>
		</table>
	</form>
</div>
</div>
<?php include("footer.php"); ?>