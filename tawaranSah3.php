<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *          Date 		: 	
 *********************************************************************************/
include("header.php");

include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$sFileName		= "tawaranSah3.php";
$sActionFileName = "loanView.php";
$title     		= "Pengesahan Dokumen Tawaran Pembiayaan";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
$strErrMsg = array();

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
$FormLabel[$a]   	= "No KTP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Jumlah Diluluskan;";
$FormElement[$a] 	= "totalLoan";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Nomor Rujukan";
$FormElement[$a] 	= "loanNo";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

//--- End   :Set the listing list (you amay insert here any new listing) -------------------------->
$strLoan = "SELECT a.loanNo, a.loanAmt,a.loanType, (a.loanAmt * a.kadar_u * 0.01 * a.loanPeriod /12) as loanUntung,  a.loanAmt + (a.loanAmt * a.kadar_u * 0.01 * a.loanPeriod /12) as totalLoan, b.name, c.newIC
				FROM `loans` a, users b, userdetails c
				WHERE a.userID = b.userID
				AND b.userID = c.userID
				AND a.loanID =" . $ID;
$GetLoan = &$conn->Execute($strLoan);

$loanName = dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($agree <> "") {
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";
		$sWhere = "loanID=" . tosql($ID, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE loans SET " .
			" isApproved=" . 1 .
			", approvedDate=" . tosql($updatedDate, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		//$activity = "Mengemaskini maklumat pembiayaan anggota";
		//if($rs) activityLog($sSQL, $activity, get_session('Cookie_userID'), get_session("Cookie_userName"));		
		print '<script>
					alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
					//window.location.href = "' . $sActionFileName . '";
					window.close();
				   window.opener.document.MyForm.submit();

				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
	<tr>
		<td colspan="4" class="Data"><b class="maroonText">' . strtoupper($title) . '</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	//$cnt = $i % 2;

	if ($i == 1) print '<tr><td class=Header colspan=4>i. PENGESAHAN ON-LINE DOKUMEN PEMBIAYAAN TELAH LENGKAP DAN SEMPURNA SERTA PERMOHONAN UNTUK MENGELUARKAN PEMBIAYAAN</td></tr>';

	if ($cnt == 1) print '<tr valign=top>';
	print '<td class=Data align=right>' . $FormLabel[$i];
	//if (!($i == 1 or $i == 2 or $i == 8 or $i ==30 or $i == 32)) 
	print ':';
	print ' </td>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetLoan->fields($FormElement[$i]));
	//if ($strFormValue == '') $strFormValue = $$FormElement[$i];	

	if ($FormType[$i] == 'textarea') {
		$strFormValue = str_replace("<pre>", "", $GetLoan->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>", "", $strFormValue);
	}

	if ($i == 3) {
		$strFormValue = number_format($GetLoan->fields($FormElement[$i]), 2);
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

	//if($i == 3){
	//$dept = dlookup("loans", "loanAmt", "loanID=" . $GetLoan->fields(departmentID));
	//print $dept;
	//}

	$loanAmt = number_format($GetLoan->fields(loanAmt), 2);

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	//<input type="text" name="sellUserName" class="Data" value="'.$sellUserName.'" onfocus="this.blur()" size="50">   
	print '&nbsp;</td>';
	if ($cnt == 0) print '</tr>';
}

print '<tr><td class=Header colspan=4>Pengesahan</td></tr>';
print '<tr><td colspan=4 align=center class=data>
<textarea cols="80" rows="10" wrap="hard" name="syarat" readonly>Saya seperti nama diatas dengan ini mengesahkan dokumen pembiayaan telah diterima dan telah dilengkapkan dengan sempurna.

Saya memohon untuk mengeluarkan ' . $loanName . ' sebanyak ' . $loanAmt . '.</textarea>

			</td>
		</tr>';
print '<tr>
		<td class="data" align="center" colspan="4"><input type="checkbox" class="form-check-input" name="agree">&nbsp; Setuju &nbsp;</td>
		</tr>';
print '<tr><td colspan=4 align=center class=Data>
			<input type="hidden" name="ID" value="' . $ID . '">
			<input type=button name=kembali class="but" value=Kembali onClick="window.close();">
			<input type="button" class="but" name=SubmitForm value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');">
			<input type="button" onClick="window.print()" value="Print"/>&nbsp;
			<br />
			</td>
		</tr>
</table>
</form>';

include("footer.php");
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
	          //window.location.href ="' . $sActionFileName . '";
			  //}
	        }
	      }
	    }

</script>';
