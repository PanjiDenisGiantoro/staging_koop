<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	savingEdit.php
*          Date 		: 	13/07/2006
*********************************************************************************/
if (!isset($mm))	$mm=date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
include("header.php");	
include ("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");top.location="index.php";</script>';
}

$sFileName		= "savingEdit.php";
$sActionFileName= "savingList.php";
$title     		= "Kemaskini Simpanan Bulanan Anggota";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();

$a = 1;
$FormLabel[$a]   	= "No / Nama Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Simpanan Wajib";
$FormElement[$a] 	= "yuranAmt";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank,CheckDecimal);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Simpanan Pokok";
$FormElement[$a] 	= "shareAmt";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank,CheckDecimal);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "13";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
		for($j=0 ; $j < count($FormCheck[$i]); $j++) {
			FormValidation ($FormLabel[$i], 
							$FormElement[$i], 
							$$FormElement[$i],
							$FormCheck[$i][$j],
							$i);
		}
	}	
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
		$sSQL = "";
	    $sWhere = "userID=" . tosql($pk, "Text");
		$sWhere .= "  AND yrmth= " . tosql($yymm,"Text");
       	$sSQL	= "UPDATE usersaving SET " .
		          "yuranAmt=" . tosql($yuranAmt, "Number") .
		          ",shareAmt=" . tosql($shareAmt, "Number");
		$sSQL .= " where " . $sWhere;
		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
					window.location.href = "'.$sActionFileName.'?yy='.$yy.'&mm='.$mm.'";
				</script>';
	}
}			
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
$sSQL = "";
$sWhere = "";		
$sWhere = " userID = " . tosql($pk,"Text");
$sWhere .= "  AND yrmth= " . tosql($yymm,"Text");
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	 * FROM usersaving";
$sSQL = $sSQL . $sWhere;
$rs = &$conn->Execute($sSQL);

print '
<form name="MyForm" action='.$sFileName.' method=post>
<input type="hidden" name="yy" value="'.$yy.'">
<input type="hidden" name="mm" value="'.$mm.'">
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="lineBG">
	<tr>
		<td colspan="4" class="Data"><b class="maroonText">'.strtoupper($title).' : '.$mm.'/'.$yy.'</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	print '<tr valign=top><td class=Data align=right width="250">'.$FormLabel[$i].' :</td>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<td class=errdata colspan="3">';
	else
	  print '<td class=Data colspan="3">';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($rs->fields($FormElement[$i])); 
	if ($i == 1) {
		$strFormValue = dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")).'&nbsp;-&nbsp; '.
						dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text"));
	}
	FormEntry($FormLabel[$i], 
			  $FormElement[$i], 
			  $FormType[$i],
			  $strFormValue,
			  $FormData[$i],
			  $FormDataValue[$i],
			  $FormSize[$i],
			  $FormLength[$i]);
	//--- End   : Call function FormEntry ---------------------------------------------------------  
    print '&nbsp;</td></tr>';
}

print '<tr><td colspan=4 align=center class=Data>
			<input type="hidden" name="pk" value="'.$pk.'">';
if (get_session("Cookie_groupID") == '2') {			
	print '	<input type=Reset name=ResetForm class="but" value=Clear Form>
			<input type=Submit name=SubmitForm class="but" value=Kemaskini>';
} else {
	print '	<input type=Reset name=ResetForm class="but" value=Clear Form disabled>
			<input type=Submit name=SubmitForm class="but" value=Kemaskini disabled>
			<br><i>( PERHATIAN : Jenis Kelulusan : <b>Pengurus</b> - dibenarkan mengemaskini )</i>';
}			
print '		</td>
		</tr>
</table>
</form>';

include("footer.php");	
?>
