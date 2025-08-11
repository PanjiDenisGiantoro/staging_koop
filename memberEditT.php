<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberEditT.php
 *          Date 		: 	31/03/2004
 *********************************************************************************/
include("header.php");
include("koperasiList.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$sFileName		= "memberEditT.php";
$sActionFileName = "memberT.php";
$title     		= "Status Keanggotaan";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = 1;
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No KP Baru/Lama";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Jenis";
$FormElement[$a] 	= "type";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= $terminateList;
$FormDataValue[$a]	= $terminateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

/*$a++;
$FormLabel[$a]   	= "Jenis";
$FormElement[$a] 	= "type";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $terminateList;
$FormDataValue[$a]	= $terminateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";
*/
$a++;
$FormLabel[$a]  	= "Tarikh Memohon";
$FormElement[$a] 	= "applyDate";
$FormType[$a]  		= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Status";
$FormElement[$a] 	= "status";
$FormType[$a]  		= "hidden";
$FormData[$a]    	= $statusList;
$FormDataValue[$a]	= $statusVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Diluluskan";
$FormElement[$a] 	= "approvedDate";
$FormType[$a]  		= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Ditolak";
$FormElement[$a] 	= "rejectedDate";
$FormType[$a]  		= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Kemaskini";
$FormElement[$a] 	= "updatedDate";
$FormType[$a]  		= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]	  	= "Kemaskini Oleh";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]	  	= "Catatan";
$FormElement[$a] 	= "remark";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

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
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "userID=" . tosql($pk, "Number");
		$sSQL	= "UPDATE userterminate SET " .
			"type=" . tosql($type, "Number") .
			",updatedDate=" . tosql($updatedDate, "Text") .
			",updatedBy=" . tosql($updatedBy, "Text");
		$sSQL .= " where " . $sWhere;
		//		print $sSQL;
		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Status anggota telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

$GetMember = ctMemberTerminate("", $pk);
print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="lineBG">
	<tr>
		<td colspan="2" class="Data"><b class="maroonText">' . strtoupper($title) . ' : </b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 4) print '<tr><td class=Header colspan=2>Informasi :</td></tr>';

	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . ' :</td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetMember->fields($FormElement[$i]));
	if ($i == 1) {
		$strFormValue = dlookup("userdetails", "memberID", "userID=" . tosql($GetMember->fields(userID), "Text")) . '&nbsp;-&nbsp; ' .
			dlookup("users", "name", "userID=" . tosql($GetMember->fields(userID), "Text"));
	}
	if ($i == 2) {
		$strFormValue = dlookup("userdetails", "newIC", "userID=" . tosql($GetMember->fields(userID), "Text")) . '&nbsp;/&nbsp; ' .
			dlookup("userdetails", "oldIC", "userID=" . tosql($GetMember->fields(userID), "Text"));
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
	print '&nbsp;</td></tr>';
}
/*
print '<tr><td colspan=2 align=center class=Data>
			<input type="hidden" name="pk" value="'.$pk.'"
			<input type=Reset name=ResetForm class="but" value=Clear Form>
			<input type=Submit name=SubmitForm class="but" value=Kemaskini>
			</td>
		</tr>*/
print '</table>
</form>';

include("footer.php");
