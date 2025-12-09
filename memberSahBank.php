<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	memberSahBank.php
*          Date 		: 	12/12/2006
*********************************************************************************/
session_start();
include("header.php");	
include("koperasiQry.php");	
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}
$user = 0;
if (get_session("Cookie_groupID") == 0) $user = 1;
if(!isset($valAddDpt)) $valAddDpt = 0;
if(!isset($valAddBlj)) $valAddBlj = 0;
if($pk) 
{
	$loanID = $pk ; $pk = dlookup("loans", "userID", "loanID=" . $pk); 
}

if($user) 
{
	$pk = get_session('Cookie_userID'); $user = 1; 
}

if($loanID) 
{
	$strpk = "?pk=".$loanID; 
}
else $strpk = '';
// = "?vw=memberSahBank&mn=3".$strpk;
$sFileName		= "?vw=memberSahBank&mn=1".$strpk;
if (get_session("Cookie_groupID") == 0) $sActionFileName= "?vw=memberSahBank&mn=1"; 

//--------- delete upon hapus selection --------

$bankList = Array();
$bankVal  = Array();
$Getbank = ctGeneral("","Z");
if ($Getbank->RowCount() <> 0){
	while (!$Getbank->EOF) {
		array_push ($bankList, $Getbank->fields(name));
		array_push ($bankVal, $Getbank->fields(ID));
		$Getbank->MoveNext();
	}
}

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();
$a = 1;
$FormLabel[$a]   	= "Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No Kartu Identitas";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";	

$a++;
$FormLabel[$a]   	= "* Nombor Akaun Bank<br><b>(XXXXXXXXXXXXXXXX)</b>";
$FormElement[$a] 	= "accTabungan";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";	

$a++;
$FormLabel[$a]   	= "* Nama Bank Akaun Anggota";
$FormElement[$a] 	= "bankID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $bankList;
$FormDataValue[$a]	= $bankVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Pengeluaran Dividen";
$FormElement[$a] 	= "outdiv";
$FormType[$a]	  	= "radio";
$FormData[$a]   	= array('TIDAK','YA');
$FormDataValue[$a]	= array('0','1');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";


//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$strMember = "SELECT a.*, b.memberID, b.newIC, b.mobileNo, b.bankID, b.accTabungan,b.outdiv FROM users a, userdetails b WHERE a.userID = '".$pk."' AND a.userID = b.userID ";
$GetMember = &$conn->Execute($strMember);

$test = $GetMember->fields('outdiv');

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
$updatedBy 	= get_session("Cookie_userName");
$updatedDate = date("Y-m-d H:i:s");               

if ($SubmitApplication <> "") {
	//--- Begin : Call function FormValidation --- 
	// if ($outdiv == '1') {
	// 	array_push ($strErrMsg, "outdiv");
	// 	print '- <font class=redText>Pemohon telah membuat pengesahan sebelum ini.</font><br />';
	// }

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
		$sWhere = "";		
	    $sWhere = "userID=" .tosql($pk, "Text");
		$sWhere = " WHERE (". $sWhere.")";		
       	$sSQL	= "UPDATE userdetails SET " .
		          "	 bankID=" 		. tosql($bankID, "Number").	
		          ", Sbank=" 		. '1'.	         
		          ", outdiv=" 		. '1'.	 
				  ", updatedBank=" 	. tosql($updatedDate, "Text") .         
		          ", accTabungan=" 	. tosql($accTabungan, "Text");       			 

		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);		
		print '<script>
					alert ("Maklumat pengesahan bank telah dikemaskini. Sila tunggu pemprosesan dari pihak Koperasi.");
					window.location.href = "'.$sActionFileName.'";
				</script>';
	}
}		

print '
<form name="MyForm" action='.$sFileName.' method=post>
<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
	<tr>
		<td colspan="4" class="Data"><b class="maroonText">'.strtoupper($title).'</b></td></tr>';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
 	$cnt = $i % 2;
	if ($i == 1) print '<tr><td class="headerteal" colspan=4>1. PEMOHON</td></tr>
		<tr><td class="headerteal" colspan=4><b class="redText"><h4>UNTUK MEMILIH PENGELUARAN DIVIDEN, PASTIKAN INFORPASI BANK DIKEMASKINI KEPADA MAKLUMAT TERKINI! (UNTUK MEMUDAHKAN PIHAK KOPERASI MENGURUSKAN PEMINDAHAN DIVIDEN)</h4></b></td></tr>';
	//if (($i == 7) AND ($test = 1))
	//if ($i == 7)
	if ($test == 0) {
	
		if ($i == 7) print '<tr>
	<td colspan=4 align=center class=Data>
	<input type="Submit" class="btn btn-primary w-md waves-effect waves-light" name="SubmitApplication" class="but" value="PENGESAHAN INFORPASI BANK">
	</td>
	</tr>';
	}

	if ($cnt == 1) print '<tr valign=top>';
	print '<td class=Data align=right>'.$FormLabel[$i];
	if (!($i == 6 or $i == 7 or $i == 8)) print ':';
	
	print ' </td>';

	if (in_array($FormElement[$i], $strErrMsg))
	  print '<td class=errdata>';
	else
	  print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetMember->fields($FormElement[$i])); 
	if ($FormType[$i] == 'textarea') {
		$strFormValue = str_replace("<pre>","",$GetMember->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>","",$strFormValue);
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
    print '&nbsp;</td>';
	if ($cnt == 0) print '</tr>';
}

print '</table></form>';
include("footer.php");	
?>