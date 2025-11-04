<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberApplyT.php
 *          Date 		: 	31/03/2004
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("forms.php");
//$conn->debug=true;
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 200;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "0";
if (!isset($dept))		$dept = "";

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

if (get_session("Cookie_groupID") == 0) {
	$userID		= get_session('Cookie_userID');
	$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$oldIC		= dlookup("userdetails", "oldIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

$sFileName		= "urusniaga2.php";
$sActionFileName = "index.php";
$title     		= "Penyata Urusniaga Detail Mengikut Anggota";

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
$GetMember = ctMemberUrusniagaDetail($q = $memberID, $id = $memberID);
//$GetMember = ctMemberStatusDept($q=$memberID,$by=1,$filter,$dept);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);


$a = 1;
$FormLabel[$a]   	= "* Nomor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a = $a + 1;
$FormLabel[$a]   	= "* No KTP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a = $a + 1;
$FormLabel[$a]   	= "No KTP Lama";
$FormElement[$a] 	= "oldIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";


$a = $a + 1;
$FormLabel[$a]   	= "Jabatan";
$FormElement[$a] 	= "departmentn";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "" or $memberID <> "") {
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
	//--- BEGIN	: Checking member id ---
	if ($memberID <> "") {
		if (dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text")) == "") {
			array_push($strErrMsg, 'memberID');
			print '- <font class=redText>Nomor Anggota - ' . $memberID . ' tidak wujud...!</font><br>';
			$userName = "";
			$newIC = "";
			$oldIC = "";
			$unitOnHand = "";
			$departmentID = "";
		} else {
			$userID = dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text"));
			$userName 	= dlookup("users", "name", "userID=" . tosql($userID, "Text"));
			$newIC 	= dlookup("userdetails", "newIC", "userID=" . tosql($userID, "Text"));
			$oldIC 	= dlookup("userdetails", "oldIC", "userID=" . tosql($userID, "Text"));
			$unitOnHand = dlookup("userdetails", "totalShare", "userID=" . tosql($userID, "Text"));
			$departmentID = dlookup("userdetails", "departmentID", "userID=" . tosql($userID, "Text"));
			$departmentn = dlookup("general", "code", "ID=" . tosql($departmentID, "Text"));
		}
	}
	//--- END  	: Checking member id ---
	/*	if (count($strErrMsg) == "0") {
		$applyDate = date("Y-m-d H:i:s");             
		$sSQL = "";
		$sSQL	= "INSERT INTO userterminate (" . 
		          "userID," . 
		          "applyDate)" . 
		          " VALUES (" . 
		          tosql($userID, "Text") . "," .
		          tosql($applyDate, "Text") . ")";
//		print $sSQL;
		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Permohonan Berhenti telah didaftarkan ke dalam sistem.");
					window.location.href="'.$sActionFileName.'";
				</script>';
	} */
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
<input type="hidden" name="shareType" value="' . $shareType . '">
<input type="hidden" name="unitOnHand" value="' . $unitOnHand . '">
<table border=0 cellpadding=3 cellspacing=0 width=95% align="center" class="lineBG">
	<tr>
		<td colspan="2" class="Data"><b class="maroonText">' . strtoupper($title) . '</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	print '<tr valign=top><td class=Data align=right width="250">' . $FormLabel[$i] . ' :</td>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
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

	if ($i == 1) {
		if (get_session("Cookie_groupID") == 1 or get_session("Cookie_groupID") == 2) {
			print '
			<input type="button" class="label" value="..." onclick="window.open(\'selMember.php\',\'sel\',\'top=10,left=10,width=650,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
		}
		print '	<input type="text" name="userName" class="Data" value="' . $userName . '" onfocus="this.blur()" size="50">';
	}
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td></tr>';
}

print '<tr><td colspan=2 align=center class=Data>
			<input type=Reset name=ResetForm class="but" value=Clear Form>
			<input type=Submit name=SubmitForm class="but" value=Report>
			</td>
		</tr>';


if ($GetMember->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top">
			<td valign="top" colspan=2>
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap>&nbsp;</td>
						<td nowrap>&nbsp;Id Transaksi</td>
						<td nowrap>&nbsp;Tahun Bulan</td>	
						<td nowrap>&nbsp;Kod Potongan</td>
						<td nowrap>&nbsp;Debit/Kredit</td>
						<td nowrap>&nbsp;Amount</td>
						<td nowrap>&nbsp;Caj</td>
						<td nowrap align="center">&nbsp;Jumlah</td>
						</tr>';
	while (!$GetMember->EOF && $cnt <= $pg) {
		$status = dlookup("userdetails", "status", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		if ($GetMember->fields(addminus)) $val = "Debit";
		else $val = "Kredit";
		print ' <tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data">&nbsp;' . $GetMember->fields(ID) . '</td>
						<td class="Data">&nbsp;' . $GetMember->fields(yrmth) . '</td>
						<td class="Data">&nbsp;' . $GetMember->fields(kod) . '</td>
						<td class="Data">&nbsp;' . $val . '</td>
						<td class="Data" align="right">&nbsp;' . $GetMember->fields(pymtAmt) . '</td>
						<td class="Data" align="right">&nbsp;' . $GetMember->fields(cajAmt) . '</td>
						<td class="Data" align="right">&nbsp;' . $GetMember->fields(jumlah) . '</td>
					</tr>';
		$cnt++;
		$bil++;
		$GetMember->MoveNext();
	}
	print ' </table>
			</td>
		</tr>		
		<tr>
			<td colspan=2>';
	if ($TotalRec > $pg) {
		print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
		if ($TotalRec % $pg == 0) {
			$numPage = $TotalPage;
		} else {
			$numPage = $TotalPage + 1;
		}
		print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&filter=' . $filter . '&memberID=' . $memberID . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont" colspan=2>Jumlah Data : <b>' . $GetMember->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center" colspan=2><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center" colspan=2><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}



print '</table>
</form>';

include("footer.php");
?>
<?
print '
<script language="JavaScript">
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
	
	function ITRActionButtonClick(v) {
		e = document.MyForm;
		if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			
			if(count != 1) {
				alert(\'Sila pilih satu pinjaman sahaja \');
			} else {
	            e.action.value = v;
	            e.submit();
			}
		}
	}
	
</script>


<script>
function listsort() {
	//document.frmSort.fieldsort.value = fieldsort;
	//document.frmSort.by.value = sortby;
	//document.frmSort.pagenr.value = pagenr;
	document.MyForm.submit();
}
</script>';
?>