<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	komoditi_edit.php
*          Date 		: 	15/04/2017
*********************************************************************************/
include("header.php");		
include("koperasiQry.php"); 
date_default_timezone_set("Asia/Jakarta");	
include ("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");window.location="index.php";</script>';
}
$sFileName		= "?vw=komoditi_edit&mn=907";
$sActionFileName= "?vw=komoditi_list&mn=907";
$title     		= "Kemaskini Maklumat Sijil Komoditi";
//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();

//--- Prepare item type
$itemList = Array();
$itemVal  = Array();
$Getitem = ctGeneral("","X");
if ($Getitem->RowCount() <> 0){
	while (!$Getitem->EOF) {
		array_push ($itemList, $Getitem->fields(name));
		array_push ($itemVal, $Getitem->fields(ID));
		$Getitem->MoveNext();
	}
}	
//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
$a = $a + 1;
$FormLabel[$a]   	= "* No Sijil";
$FormElement[$a] 	= "no_sijil";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "*Komoditi";
$FormElement[$a] 	= "item";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Jumlah Pembiayaan";
$FormElement[$a] 	= "amount";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Tarikh Pembelian Sijil (dd/mm/yyyy)";
$FormElement[$a] 	= "tarikh_beli";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "* Masa Pembelian Sijil";
$FormElement[$a] 	= "masa_beli";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
//Details Insuran
$strkomoditi = "SELECT * FROM komoditi WHERE komoditi_ID = '".$pk."'";
$Getkomoditi = &$conn->Execute($strkomoditi);

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
//DateFormat:
	$getdateIns = explode("/", $tarikh_beli); 
	$tarikh_beli= $getdateIns[2].'/'.sprintf("%02s",  $getdateIns[1]).'/'.sprintf("%02s",  $getdateIns[0]);
	
	$sSQL = "";
	$sWhere = "";		
	$sWhere = "komoditi_ID=" . tosql($pk, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";		
    $sSQL	= "UPDATE komoditi SET " .		
			  "no_sijil=" . tosql($no_sijil, "Text") . 
			  ",itemType=" . tosql($item, "Text") .
	          ",amount=" . tosql($amount, "Text") .
			  ",tarikh_beli=" . tosql($tarikh_beli, "Text") .
			  ",masa_beli=" . tosql($masa_beli, "Text") .  				  
			  ",updatedDate=" . tosql($updatedDate, "Text") .
	          ",updatedBy=" . tosql($updatedBy, "Text") ;
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
                    alert ("Maklumat komoditi telah dikemaskinikan ke dalam sistem.");
                    gopage("$sActionFileName",1000);

	}
}			
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
print '
<form name="MyForm" action='.$sFileName.' method=post>
<h5 class="card-title">'.strtoupper($title).' &nbsp;</h5>
<div class="m-1 row">
';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
//Print Header Maklumat Pemohon
if ($i == 1) print '<div class="card-header mt-3">Maklumat Komoditi :</div>'; 	
print '<div class="m-1 row"><label class="col-md-3 col-form-label">'.$FormLabel[$i].' :</label>';
if (in_array($FormElement[$i], $strErrMsg))
  print '<div class="col-md-4 bg-danger">';
else
  print '<div class="col-md-4">';
//--- Begin : Call function FormEntry ---------------------------------------------------------  
//DateFormat:
if ($FormElement[$i]=="tarikh_beli")
{
	$strFormValue = toDate("d/m/yy",$Getkomoditi->fields('tarikh_beli')); 
}
else{
$strFormValue = tohtml($Getkomoditi->fields($FormElement[$i])); 
}	
FormEntry($FormLabel[$i], 
		  $FormElement[$i], 
		  $FormType[$i],
		  $strFormValue,
		  $FormData[$i],
		  $FormDataValue[$i],
		  $FormSize[$i],
		  $FormLength[$i]);

if($i==2){
	if(!isset($item)) $item = $Getkomoditi->fields('itemType');  //onchange="document.MyForm.submit();"
	print '<select name="item" class="form-selectx">
	<option value="">- Pilihan Barang -';
	for ($j = 0; $j < count($itemList); $j++) {
		print '	<option value="'.$itemVal[$j].'" ';
	if ($item == $itemVal[$j]) print ' selected';
		print '>'.$itemList[$j];
	}
	print '</select>';
}
//--- End   : Call function FormEntry ---------------------------------------------------------  
print '</div>';
	if ($cnt == 0) print '</div>';
}

if ((get_session("Cookie_groupID") == 2) OR (get_session("Cookie_groupID") == 1)) {
    
print '<div class="mb-3 mt-3 row">
                                    <center>
                                            <input type="hidden" name="pk" value="'.$pk.'">
	<input type=Submit name=SubmitForm class="btn btn-primary" value=Kemaskini>
                                    </center>
                                </div>'; 

}
print '</div></form>';
print '
<script	language="JavaScript">
function jKira(){
	var jumPremium =document.getElementsByName("JumlahPremium")[0].value;
	if(jumPremium=="")
	{
	alert(\'Masukkan Jumlah sebenar.\');
	}else{
		var B = jumPremium- 10;
		var C = B- (B*(5/100));
		var JumBayar = C+10;
		JumlahPerlindungan= JumBayar;
		document.getElementsByName("JumlahPerlindungan")[0].value = JumlahPerlindungan;
	}
}
</script>';
include("footer.php");	
?>