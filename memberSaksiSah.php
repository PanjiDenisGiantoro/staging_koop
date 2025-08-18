<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *          Date 		: 	
 *********************************************************************************/
include("header.php");
include("koperasiList.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$sFileName		= "?vw=memberSaksiSah";
$sActionFileName = "?vw=memberSahAnggota";
$title     		= "Pengesahan Saksi Keanggotaan";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
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
$FormLabel[$a]   	= "Alamat&nbsp;";
$FormElement[$a] 	= "add";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Jabatan/Cawangan";
$FormElement[$a] 	= "dept";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Jawatan";
$FormElement[$a] 	= "job";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Nomor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$sID = $pk;
$pk = get_session('Cookie_userID');
$strMember = "SELECT a . * , b.memberID, b.newIC, b.dateBirth, b.job, 
			b.address, b.city, b.postcode, b.stateID, b.departmentID, b.approvedDate
			from users a, userdetails b
			WHERE a.userID = '" . $pk . "' AND a.userID = b.userID";
$GetMember = &$conn->Execute($strMember);

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->

if ($agree <> "") {
	$sSQL = '';
	$sWhere = '';
	$sWhere = ' userID  = ' . $sID;
	$sSQL	= 'UPDATE userdetails ';
	if ($no == 1) $sSQL	.= ' SET approvedSaksi1 = 1 ';
	if ($no == 2) $sSQL	.= ' SET approvedSaksi2 = 1 ';
	$sSQL .= ' WHERE ' . $sWhere;
	$rs = &$conn->Execute($sSQL);
	print '<script>
					alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
    <h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 1) print '<div class="card-header mb-3 mt-3">i. BUTIR-BUTIR SAKSI</div> ';

	if ($cnt == 1) print '<tr valign=top>';
	print '<div class="m-1 row"><label class="col-md-2 col-form-label">' . $FormLabel[$i];
	print ':';
	print ' </label>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<div class="col-md-4 bg-danger">';
	else
		print '<div class="col-md-4">';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetMember->fields($FormElement[$i]));
	//if ($strFormValue == '') $strFormValue = $$FormElement[$i];	

	if ($FormType[$i] == 'textarea') {
		$strFormValue = str_replace("<pre>", "", $GetMember->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>", "", $strFormValue);
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

	if ($i == 3) {
		$stradd = str_replace("<pre>", "", $GetMember->fields(address));
		$stradd = str_replace("</pre>", "", $stradd);

		$add = '<br>' . $stradd . ', <br />' . tohtml($GetMember->fields(city)) . ', <br />  ' . tohtml($GetMember->fields(postcode)) . ', ' . dlookup("general", "name", "ID=" . $GetMember->fields(stateID));

		print $add;
	}

	if ($i == 4) {
		$dept = dlookup("general", "name", "ID=" . $GetMember->fields(departmentID));
		print $dept;
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '</div></div>';
	if ($cnt == 0) print '</tr>';
}

print '<div class="card-header mb-3 mt-3">Pengesahan</div> ';
print '<div class="m-1 row"></enter>
    <textarea class="form-controlx" cols="60" rows="10" wrap="hard" name="syarat" readonly>Saya seperti nama dan alamat tertera diatas bersetuju menjadi saksi kepada anggota '
	. dlookup("users", "name", "userID=" . $sID) . ' dari '
	. dlookup("general", "name", "ID=" . dlookup("userdetails", "departmentID", "userID=" . $sID)) . '.
 Saya mengaku bahawa segala maklumat diatas adalah benar.</textarea>
			</center>
		</div>';
print '	
<div class="mb-3 row">
                <center>
                        <input type="checkbox" class="form-check-input" name="agree">&nbsp; SETUJU &nbsp;
                </center>
            </div>
<div class="mb-3 row">
                <center>
                        <input type="hidden" name="pk" value="' . $sID . '">
			<input type="hidden" name="no" value="' . $no . '">
			<input type="button" class="btn btn-secondary" name="SubmitForm" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');"><br />&nbsp;
                </center>
            </div>';

print '
</table>
</form>';

include("footer.php");
print '
<script language="JavaScript">
	var allChecked=false;
	
	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }
		
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

	function ITRActionButtonStatus() {
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
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.location.href = "?vw=memberStatus&pk=" + pk;
			}
		}
	}

</script>';
