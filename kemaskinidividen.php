<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	memberEdit.php
*          Date 		: 	29/03/2006
*********************************************************************************/
include("header.php");	
include("koperasiList.php");	
include("koperasiQry.php"); 
date_default_timezone_set("Asia/Jakarta");	
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}

$sFileName		= "kemaskinidividen.php";
$sActionFileName= "kemaskinidividen.php";
$title     		= "KemasKini Dividen";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();
	

// $a = 1;
// $a++;
// $FormLabel[$a]   	= "&nbsp;";
// $FormElement[$a] 	= "a";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";	

// $a++;
// $FormLabel[$a]   	= "&nbsp;";
// $FormElement[$a] 	= "b";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";	

$a++;
$FormLabel[$a]   	= "Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No KTP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank,CheckNumeric);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";	

// $a++;
// $FormLabel[$a]   	= "&nbsp;";
// $FormElement[$a] 	= "c";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";	


$a++;
$FormLabel[$a]   	= "* Jumlah Dividen";
$FormElement[$a] 	= "AmtDiv";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";


// $a++;
// $FormLabel[$a]   	= "Pembayaran";
// $FormElement[$a] 	= "pembayaran";
// $FormType[$a]	  	= "radio";
// $FormData[$a]   	= array('Bank In','Kredit ke Akaun');
// $FormDataValue[$a]	= array('0','1');
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";

// $a++;
// $FormLabel[$a]   	= "&nbsp;";
// $FormElement[$a] 	= "d";
// $FormType[$a]	  	= "hidden";
// $FormData[$a]   	= "";
// $FormDataValue[$a]	= "";
// $FormCheck[$a]   	= array();
// $FormSize[$a]    	= "1";
// $FormLength[$a]  	= "1";	
	




//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
//$conn->debug=1;
$pk = get_session('Cookie_userID');
$strMember = "SELECT a.*,b.*,c.* FROM users a, dividen b, userdetails c  WHERE a.userID = '".$pk."' AND a.userID = b.userID";
$GetMember = &$conn->Execute($strMember);
$stat_pro = $GetMember->fields(stat_pro); 

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($agree <> "") {
	//--- End   : Call function FormValidation ---  
if (count($strErrMsg) == "0") {
	$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s"); 
		$sSQL = "";
		$sWhere = "";		
	  $sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";		
		$sSQL	= "UPDATE dividen SET " .
						" stat_bayar=" . tosql($stat_bayar, "Number").
						", stat_pro=" . 1 .
						", updatedDate=" . tosql($updatedDate, "Text") .
						", updatedBy=" . tosql($updatedBy, "Text") ;
											
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);


		print '<script>
						alert ("Maklumat dividen telah dikemaskinikan ke dalam sistem.");
						window.location.href = "'.$sActionFileName.'";
					</script>';

	}
}			
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->



print '
<form name="MyForm" action='.$sFileName.' method=post>
<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
<tr><td colspan="4" class="Data"><b class="maroonText">'.strtoupper($title).'</b></td></tr>';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<tr><td class=Header colspan=4>1. PEMOHON:</td></tr>';
	if ($i == 3) print '<tr><td class=Header colspan=4>2. BUTIR-BUTIR DIVIDEN :</td></tr>';

	if ($cnt == 1) print '<tr valign=top>';
	print '<td class=Data align=right>'.$FormLabel[$i];
	print ':';
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
	if ($i == 3){
		$strFormValue = number_format($GetMember->fields($FormElement[$i]),2);
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


if ($stat_pro == 0){
	print '<tr>
		<td colspan=4 class=data>
			<p>i. Pembayaran.</p>
			<p>Saya dengan ini memilih opsyen terhadap jenis pembayaran</p>
			<p>(sila tick yang mana berkenaan)</p>
			<table width="100%" border="0" cellpadding="0">
			<tr><td align="left">
			<input type="radio"  name="stat_bayar" id="check3a" value ="1" onclick="myFunction3Ra();">BANK IN
			<img src="images/sym-tick-red-bkrm-01.gif" id="img0" width="20" height="20" hidden="enabled">
			<input type="radio" name="stat_bayar" value ="2" id="check3b" onclick="myFunction3R();">KREDIT KE AKAUN

			<tr>
				<td align="right">&nbsp;</td>
				<td width="18%">&nbsp;</td>
				<td width="18%">&nbsp;</td>
				<td width="18%">&nbsp;</td>
				<td width="18%">&nbsp;</td>
			</tr>

				<table width="100%" border="0" cellpadding="0">
					<tr><td align="left">
				<p id="demosaham"> </p>
					<input type="button" id="button5" value="YA" hidden="enabled" onclick="myFunction10()"  />
			<img src="images/sym-tick-red-bkrm-01.gif" id="img2" width="20" height="20" hidden="enabled">
						<input type="button" id="button11" value="TIDAK" hidden="enabled" onClick="if(!confirm(\'Adakah Anda Pasti Untuk Keluar?\')) {return false} else {window.close();};"  /></td>
			</td></tr></table>';

		print '<tr>
			<td class="data" align="left" colspan="4"><input type="checkbox" class="form-check-input" name="agree" id="agree" hidden ="enabled">Saya dengan ini bersetuju dengan pengesahan di atas.</td>
			<p id="demo10"></p>
			
			</tr>';	
		print '<tr><td colspan=4 align=left class=Data>
				<input type="hidden" name="ID" value="'.$ID.'">
				<input type="button" id="SubmitForm" hidden="enabled" class="but" name="SubmitForm" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');">
				<br /></td></tr></table></form>';
}

// if(proses==1)selepas clik button proses
else{
	print'
	<table width="100%" border="0" cellpadding="0"></table>
	';
}
//"ITRActionButtonClickStatus(\'proses\');"




print '
<script language="JavaScript">

		function ITRActionButtonClickStatus(v) {
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="agree" && e.elements[c].checked) {
				pk = e.elements[c].value;
				//strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          //alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          //if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          e.submit();
	          //window.location.href ="memberApply.php?pk=" + strStatus;
	          //window.location.href ="'.$sActionFileName.'";
			  //}
	        }
	      }
	    }
function toggleTextbox(opt)
{
    if (opt == "F")
        document.getElementByID("txtText").disabled = false;
    else
        document.getElementByID("txtText").disabled = true;
}

function myFunction() {
   // document.getElementById("demo").innerHTML = "blah blah";
	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = false;
}
function myFunction1() {
    document.getElementById("demoTidak").innerHTML = "blah";
	document.getElementById("button2").disabled = true;
	document.getElementById("button1").disabled = false;
}


function myFunction3() {

	document.getElementById("demo3a").innerHTML = "2. Pembayaran";
	document.getElementById("demo3b").innerHTML = "Saya dengan ini memilih opsyen terhadap jenis pembayarn";
	document.getElementById("demo3c").innerHTML = "(sila tick yang mana berkenaan)";
	document.getElementById("demo3d").innerHTML = "BANK IN";
	document.getElementById("demo3e").innerHTML = "<strong>ATAU</strong>";
	document.getElementById("demo3f").innerHTML = "KREDIT KE AKAUN";

	document.getElementById("button1").disabled = true;
	document.getElementById("button2").disabled = true;
	document.getElementById("check3a").hidden = false;
	document.getElementById("check3b").hidden = false;
	document.getElementById("img1").hidden = false;

}

function myFunction3Ra() {

  document.getElementById("button5").hidden = false;
	document.getElementById("demosaham").hidden = false;
	
}
function myFunction3R() {
	document.getElementById("button5").hidden = false;
	document.getElementById("demosaham").hidden = true;
}

function myFunction10() {
	document.getElementById("demo10").innerHTML = "";
	document.getElementById("button5").disabled = true;
	document.getElementById("agree").hidden = false;
	document.getElementById("SubmitForm").hidden = false;
	document.getElementById("img5").hidden = false;
}</script>';

include("footer.php");	
?>