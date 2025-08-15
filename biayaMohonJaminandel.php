<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	biayaMohonJaminandel.php
 *          Date 		: 	12/12/2018
 *********************************************************************************/
include("header.php");

include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$sFileName		= "?vw=biayaMohonJaminandel&mn=906&pk=$pk";
$sActionFileName = "?vw=biayaDokumen&mn=906&pk=$pk";
$ruj_no = dlookup("loans", "loanNo", "loanID= '" . $pk . "'");
$loanType = dlookup("loans", "loanType", "loanID= '" . $pk . "'");
$loanName = dlookup("general", "name", "ID=" . $loanType);
$title     		= "Rujukan pembiayaan :" . $ruj_no . " (" . $loanName . ")";
//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
$strErrMsg = array();

$a = 1;
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "penjaminID1";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]  	= "Nama";
$FormElement[$a] 	= "sellUserName1";
$FormType[$a]  		= "hiddentext";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "penjaminID2";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]  	= "Nama";
$FormElement[$a] 	= "sellUserName2";
$FormType[$a]  		= "hiddentext";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "penjaminID3";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]  	= "Nama";
$FormElement[$a] 	= "sellUserName3";
$FormType[$a]  		= "hiddentext";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";


//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$loID = $pk;
$strMember = "SELECT * FROM loans WHERE (loanID = '" . $pk . "')";
$GetMember = &$conn->Execute($strMember);

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
$selmember = array();

if ($semak1 <> "") {
	if ($sellMemberID1 <> '') {
		$memberID	= dlookup("userdetails", "memberID", "memberID=" . tosql($sellMemberID1, "Text"));
		if (!$memberID) {
			array_push($strErrMsg, "sellMemberID1");
			print '- <font class=redText>Tiada maklumat dengan no. anggota tersebut. Sila berikan no. anggota yang sah.</font><br />';
		} else {
			print '<script> alert("' . $sellMemberID1 . '"); document.MyForm.elements[27].value = ' . $sellMemberID1 . '; </script>';
		}
	}
}

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
		$updatedByJmn 	= get_session("Cookie_userName");
		$updatedDateJmn = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";
		$sWhere = "loanID=" . tosql($loID, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE loans SET " .
			" penjaminID1=" . tosql($penjaminID1, "Text") .
			", penjaminID2=" . tosql($penjaminID2, "Text") .
			", penjaminID3=" . tosql($penjaminID3, "Text") .
			", updatedByJmn=" . tosql($updatedByJmn, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		$userID = dlookup("loans", "userID", "penjaminID1=" . tosql($penjaminID1, "Text") . " AND penjaminID2=" . tosql($penjaminID2, "Text") . " AND penjaminID3=" . tosql($penjaminID3, "Text"));
		$loanNo = dlookup("loans", "loanNo", "loanID=" . tosql($loID, "Text"));
		$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
			" VALUES ('Kemaskini Penjamin - $loanNo', 'UPDATE', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDateJmn . "', '" . $updatedByJmn . "', '2')";
		$rs = &$conn->Execute($sqlAct);
		alert("Maklumat telah dikemaskinikan ke dalam sistem");
		gopage("$sActionFileName", 1000);
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

if (dlookup("loans", "statuspID1", "loanID= '" . $pk . "'")) $approve1 = '<i class="mdi mdi-check text-primary"></i>';
else $approve1 = '<i class="mdi mdi-close text-danger"></i>';

if (dlookup("loans", "statuspID2", "loanID= '" . $pk . "'")) $approve2 = '<i class="mdi mdi-check text-primary"></i>';
else $approve2 = '<i class="mdi mdi-close text-danger"></i>';

if (dlookup("loans", "statuspID3", "loanID= '" . $pk . "'")) $approve3 = '<i class="mdi mdi-check text-primary"></i>';
else $approve3 = '<i class="mdi mdi-close text-danger"></i>';

print '
<form name="MyForm" action="" method=post>

<h5 class="card-title"><i class="mdi mdi-bookshelf"></i>&nbsp;' . strtoupper($title) . '</h5>
<div class="mb-3 row">';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;

	if ($i == 1) print '<div class="card-header mt-3">Penjamin Pertama</div>';
	if ($i == 3) print '<div class="card-header mt-3">Penjamin Kedua</div>';
	if ($i == 5) print '<div class="card-header mt-3">Penjamin Ketiga</div>';

	if ($cnt == 1) print '<div class="m-3 row">';
	print '<label class="col-md-2 col-form-label">' . $FormLabel[$i];
	print ':';
	print ' </label>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<div class="col-md-4 bg-danger">';
	else
		print '<div class="col-md-4">';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetMember->fields($FormElement[$i]));

	if ($FormType[$i] == 'textarea') {
		$strFormValue = str_replace("<pre>", "", $GetMember->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>", "", $strFormValue);
	}

	//-----------------
	$status = $GetMember->fields(status);

	if ($i == 1) {
		if ($ln1 = $GetMember->fields(penjaminID1)) {
			$m1 = dlookup("userdetails", "memberID", "userID='" . $j1 . "'");
		}
		print $approve1;
	}

	if ($i == 2) {
		if ($ln1) {
			$name =  dlookup("users", "name", "userID='" . $ln1 . "'");
			$strFormValue 	=  $name;
		}
	}

	if ($i == 3) {
		if ($ln2 = $GetMember->fields(penjaminID2)) {
			$m2 = dlookup("userdetails", "memberID", "userID='" . $j2 . "'");
		}
		print $approve2;
	}

	if ($i == 4) {
		if ($ln2) {
			$name =  dlookup("users", "name", "userID='" . $ln2 . "'");
			$strFormValue 	=  $name;
		}
	}

	if ($i == 5) {
		if ($ln3 = $GetMember->fields(penjaminID3)) {
			$m3 = dlookup("userdetails", "memberID", "userID='" . $ln3 . "'");
		}
		print $approve3;
	}

	if ($i == 6) {
		if ($ln3) {
			$name =  dlookup("users", "name", "userID='" . $ln3 . "'");
			$strFormValue 	=  $name;
		}
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

	if ($i == 1) {

		print '
		<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'selToJaminan.php?refer=d&obj=1\',\'sel\',\'top=10,left=10,width=750,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		&nbsp;';
		print '<input type="button" class="btn btn-sm btn-danger" value="Batal" onclick="document.MyForm.penjaminID1.value=\'\';document.MyForm.sellUserName1.value=\'\';">&nbsp;';
	}

	if ($i == 3) {

		print '
		<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'selToJaminan.php?refer=d&obj=2\',\'sel\',\'top=10,left=10,width=850,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		&nbsp;';
		print '<input type="button" class="btn btn-sm btn-danger" value="Batal" onclick="document.MyForm.penjaminID2.value=\'\';document.MyForm.sellUserName2.value=\'\';">&nbsp;';
	}

	if ($i == 5) {

		print '
		<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'selToJaminan.php?refer=d&obj=3\',\'sel\',\'top=10,left=10,width=850,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		&nbsp;';
		print '<input type="button" class="btn btn-sm btn-danger" value="Batal" onclick="document.MyForm.penjaminID3.value=\'\';document.MyForm.sellUserName3.value=\'\';">&nbsp;';
	}

	print '&nbsp;</div>';
	if ($cnt == 0) print '</div>';
}
if ((get_session("Cookie_groupID") == 1 or get_session("Cookie_groupID") == 2)) {
	print '	
		<div >
        	<center>
            	<input type="hidden" name="pk" value="' . $loID . '">
				<input type=Submit name=SubmitForm class="btn btn-primary w-md waves-effect waves-light" value=Simpan>&nbsp;
				<input type="button" name=backToPage class="btn btn-secondary w-md waves-effect waves-light" value="Kembali" onclick="window.location.href=\'?vw=biayaDokumen&mn=906&pk=' . $pk . '\'">
			</center>
		</div>';
} else {
	print '
		<div>
			<center>
				<input type="hidden" name="pk" value="' . $loID . '">
				<input type=Submit name=SubmitForm class="btn btn-primary w-md waves-effect waves-light" value=Simpan>&nbsp;
				<input type="button" class="btn btn-secondary w-md waves-effect waves-light" value="Kembali" onClick="window.location.href=\'?vw=biayaMember&mn=5\';">
            </center>
		</div>';
}

print '</div></form>';
include("footer.php");
print '
<script language="JavaScript">
	function test(){
	alert(\'haiii \');
	}


	function resetP1() {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
   			  if(e.elements[c].name=="penjaminID1") {
			  e.elements[c].name.value = 0;			  
			  }
			  
			  if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	      }
	        
	       /* if(count==0) {
	          alert(\'Sila pilih rekod yang hendak dihapuskan.\');
	        } else {
	          if(confirm(count + \' rekod hendak dihapuskan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }*/
	}</script>';
