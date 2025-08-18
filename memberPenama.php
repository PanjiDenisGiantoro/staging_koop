<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberEdit.php
 *          Date 		: 	10/10/2003
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
	exit;
}
$sFileName = "?vw=memberPenama";
if (get_session("Cookie_groupID") == 0) {
	$sActionFileName = "?vw=memberPenama&pk=" . $pk;
} else {
	$sActionFileName = "?vw=memberPenamaUp";
	//$sActionFileName= "memberPenamaUp.php";
}
$title 	= "Pengajuan Ubah Pengusul";
//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();
$a = 1;
$FormLabel[$a]   	= "Nomor Anggota";
$FormElement[$a] 	= "saksi1";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "10";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
if (get_session("Cookie_groupID") == 0) $pk = get_session('Cookie_userID');
$chkSend =  dlookup("userloandetails", "isApply", "userID= '" . $pk . "'");
if ($chkSend) {
	$strMember = "SELECT a.*,b.* FROM users a, userloandetails b WHERE a.userID = '" . $pk . "' AND a.userID = b.userID";
	$title .= ' - Status Belum proses';
} else {
	$strMember = "SELECT a.*,b.* FROM users a, userloandetails b WHERE a.userID is null AND a.userID = b.userID";
	$title .= ' - Baru';
}
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
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		if (get_session("Cookie_groupID") == 0) {
			$tdata = "userloandetails";
			$tlog  = ", isApply=" . tosql(1, "Text") .
				", applyDate=" . tosql($updatedDate, "Text") .
				", applyBy=" . tosql($updatedBy, "Text");
			$isApproved = 0;
			$msgSelect = "Maklumat tersebut dihantar untuk diproses.";
		} else {
			$tdata = "userdetails";
			$tlog  = ", updatedDate=" . tosql($updatedDate, "Text") .
				", updatedBy=" . tosql($updatedBy, "Text");
			$isApproved = 1;
			$msgSelect = "Maklumat anggota telah dikemaskini.";
		}
		if ($w_address1 <> "") $w_address1 = '<pre>' . $w_address1 . '</pre>';
		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE " . $tdata . " SET " .
			" w_name1=" . tosql($w_name1, "Text") .
			", w_ic1=" . tosql($w_ic1, "Text") .
			", w_relation1=" . tosql($w_relation1, "Text") .
			", w_contact1=" . tosql($w_contact1, "Text") .
			", w_address1=" . tosql($w_address1, "Text") .
			", saksi1=" . tosql($saksi1, "Text") . $tlog;
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);

		if ($isApproved) {
			$sSQL = "";
			$sWhere = "";
			$sWhere = "userID=" . tosql($pk, "Text");
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL	= "UPDATE userloandetails SET " . "isApply= 0";
			$sSQL = $sSQL . $sWhere;
			$rs = &$conn->Execute($sSQL);
		}
		alert("$msgSelect");
		gopage("$sActionFileName", 1000);
		/*
		print '<script>
					alert ("'.$msgSelect.'");
					window.location.href = "'.$sActionFileName.'";
				</script>'; */
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
print '
    <h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<form name="MyForm" action=' . $sFileName . ' method="POST">';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;

	$addr = str_replace("<pre>", "", $GetMember->fields('w_address1'));
	$addr1 = str_replace("</pre>", "", $addr);

	if ($i == 1) {
		print '<div class="card-header mt-3">MAKLUMAT PENAMA (MESTILAH 18 TAHUN KE ATAS)</div>';
		print '                
		<div class="row m-3 mt-3">
                                        <div class="col-md-3">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom032">Nama Penama</label>
                                                            <input type="text" name="w_name1" value="' . tohtml($GetMember->fields('w_name1')) . '" class="form-control" size=30 maxlength=50 id="validationCustom032">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom03">Kartu Identitas</label>
                                                            <input type="text" name="w_ic1" value="' . tohtml($GetMember->fields('w_ic1')) . '" class="form-control" size=15 maxlength=14 id="validationCustom03" placeholder="Tiada (-)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom04">Nombor Telefon</label>
                                                            <input type="text" name="w_relation1" value="' . tohtml($GetMember->fields('w_relation1')) . '" class="form-control" size=15 maxlength=15 id="validationCustom04" placeholder="(6011XXXXXXXX)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom05">Hubungan Penama</label>                                                            
                                                            <input type="text" name="w_contact1" value="' . tohtml($GetMember->fields('w_contact1')) . '" id="validationCustom05" class="form-control" size=15 maxlength=15>                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-2">
                                                            <label class="form-label" for="validationCustom06">Alamat Rumah</label>
                                                            <textarea class="form-control" cols=30 rows=3 id="validationCustom06" wrap="hard" name="w_address1" >' . $addr1 . '</textarea>                                                            
                                                        </div>
                                                    </div>                                                   
                
		</div>';

		print '<div class="card-header mt-3">MAKLUMAT PENCADANG (NOMBOR ANGGOTA YANG TELAH BERDAFTAR BERSAMA KOPERASI)</div>	';
	}
	if ($cnt == 1) print '<div class="m-3 row">';
	print '<label class="col-md-2 col-form-label">' . $FormLabel[$i];
	if (!($i == 2 or $i == 4)) print ':';
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
	print '</div>';
	if ($cnt == 0) print '</div>';
}
print '<div class="m-3 row">
                                    <center>
                                            <input type="hidden" name="pk" value="' . $pk . '">
			<input type="Submit" name="SubmitForm" class="btn btn-primary w-md waves-effect waves-light" value="PERMOHONAN PENUKARAN">
                                    </center>
                                </div>    

</form>';
include("footer.php");
