<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	loanpymtEdit.php
*          Date 		: 	27/04/2004
*********************************************************************************/
if (!isset($mm))	$mm=date("m");
if (!isset($yy))	$yy=date("Y");
include("header.php");	
include("koperasiQry.php"); 
date_default_timezone_set("Asia/Jakarta");	
include ("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");top.location="index.php";</script>';
}

$sFileName		= "loanpymtEdit.php";
$sActionFileName= "loanpymtList.php";
$title     		= "Kemaskini Bayaran Pinjaman";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();

//--- Prepare deduct list
$deductList = Array();
$deductVal  = Array();
$GetDeduct = ctGeneral("","J");
if ($GetDeduct->RowCount() <> 0){
	while (!$GetDeduct->EOF) {
		if (substr($GetDeduct->fields(code),0,1) == 4) {
			array_push ($deductList, $GetDeduct->fields(code).' - '.$GetDeduct->fields(name));
			array_push ($deductVal, $GetDeduct->fields(ID));
		}
		$GetDeduct->MoveNext();
	}
}		

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
$FormLabel[$a]   	= "Kod Potongan";
$FormElement[$a] 	= "deductID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= $deductList;
$FormDataValue[$a]	= $deductVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Bayaran";
$FormElement[$a] 	= "paymentAmt";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Kod Pinjaman";
$FormElement[$a] 	= "loanCode";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "30";

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
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");               
		$sSQL = "";
	    $sWhere = "ID=" . tosql($pk, "Number");
       	$sSQL	= "UPDATE loanpayment SET " .
		          "loanID=" . tosql($loanID, "Number") .
				  ",updatedDate=" . tosql($updatedDate, "Text") .
		          ",updatedBy=" . tosql($updatedBy, "Text") ;
		$sSQL .= " where " . $sWhere;
//		print $sSQL;
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
$sWhere .= " ID = " . tosql($pk,"Number");
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	 * FROM loanpayment";
$sSQL = $sSQL . $sWhere;
$rs = &$conn->Execute($sSQL);

$loanType = dlookup("loans", "loanType", "loanID=" . tosql($rs->fields(loanID), "Number"));

print '
<form name="MyForm" action='.$sFileName.' method=post>
<input type="hidden" name="yy" value="'.$yy.'">
<input type="hidden" name="mm" value="'.$mm.'">
<input type="hidden" name="loanID" value="'.$rs->fields(loanID).'">
<input type="hidden" name="loanNo" value="'.$loanNo.'">
<table border=0 cellpadding=3 cellspacing=0 width=100% align="center" class="lineBG">
	<tr>
		<td colspan="2" class="Data"><b class="maroonText">'.strtoupper($title).' : '.$mm.'/'.$yy.'</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 5) print '<tr><td class=Header colspan=2>Audit Informasi :</td></tr>';
	print '<tr valign=top><td class=Data align=right width="250">'.$FormLabel[$i].' :</td>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<td class=errdata>';
	else
	  print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($rs->fields($FormElement[$i])); 
	if ($i == 1) {
		$strFormValue = dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")).'&nbsp;-&nbsp; '.
						dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text"));
	}
	if ($i == 4) {
		$strFormValue = dlookup("general", "code", "ID=" . tosql($loanType, "Number")).'/'.sprintf("%010d", $rs->fields(loanID));
		$loanName	  =	dlookup("general", "name", "ID=" . tosql($loanType, "Number"));
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
	if ($i == 4) {
		print '
		<input type="button" class="label" value="..." onclick="window.open(\'selUserLoan.php?pk='.$rs->fields(userID).'\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		<input type="text" name="loanName" class="Data" value="'.$loanName.'" onfocus="this.blur()" size="50">';

	}	
    print '&nbsp;</td></tr>';
}

print '<tr><td colspan=2 align=center class=Data>
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
