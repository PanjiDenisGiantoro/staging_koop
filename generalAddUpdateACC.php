<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	general.php
*          Date 		: 	03/06/06
*********************************************************************************/
session_start();
if (!isset($sub)) 	$sub = "0";
if (!isset($cat))	$cat="";
if (!isset($page))	$page="";

include ("common.php");
include("koperasiQry.php"); 
date_default_timezone_set("Asia/Kuala_Lumpur");	
include ("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>'.$emaNetis.'</title>
	<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
                <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
<script>
	function selectAllSection() {
		members = document.MyForm.elements["Section[]"];
		for(c=0; c<members.length; c++) {
    		members.options[c].selected = true;
  		}
	}

	function addSection() {
  		nonMembers = document.MyForm.elements["nonSection[]"];
  		members = document.MyForm.elements["Section[]"];
  
  		if(nonMembers.length>0 && nonMembers.options[0].value==-1) {
    		return;
  		}
  
  		for(c=0; c<nonMembers.length; c++) {
    		if(nonMembers.options[c].selected) {
      			if(members.length>0 && members.options[0].value==-1) {
        			members.options[0] = null;
      			}
      			members.options[members.length] = new Option();
      			for(c2=members.length-1; c2>0; c2--) {
        			members.options[c2].text = members.options[c2-1].text;
        			members.options[c2].value = members.options[c2-1].value;
      			}
      			o = new Option(nonMembers.options[c].text, nonMembers.options[c].value, false, true);
      			members.options[0] = o;
      			nonMembers.options[c--] = null;
    		}
  		}
	
		if(nonMembers.length==0)
		{
			//assigning the first element to Not Assigned if empty
			nonMembers.options[0] = new Option();
			nonMembers.options[0].text = "-None-";
			nonMembers.options[0].value= "-1";
		}
	
	}

	function removeSection() {
  		nonMembers = document.MyForm.elements["nonSection[]"];
  		members = document.MyForm.elements["Section[]"];
  
  		if(members.length>0 && members.options[0].value==-1) {
    		return;
  		}
    
  		for(c=0; c<members.length; c++) {
    		if(members.options[c].selected) {
      			if(nonMembers.length>0 && nonMembers.options[0].value==-1) {
        			nonMembers.options[0] = null;
      			}
      		nonMembers.options[nonMembers.length] = new Option();
      		for(c2=nonMembers.length-1; c2>0; c2--) {
        		nonMembers.options[c2].text = nonMembers.options[c2-1].text;
        		nonMembers.options[c2].value = nonMembers.options[c2-1].value;
      		}
      		o = new Option(members.options[c].text, members.options[c].value, false, true);
      		nonMembers.options[0] = o;
      		members.options[c--] = null;
    	}
  	}
	
	if(members.length==0)
	{
		//assigning the first element to Not Assigned if empty
		members.options[0] = new Option();
		members.options[0].text = "-None-";
		members.options[0].value= "-1";
	}
	

}
</script>
</head>
<body leftmargin="5" rightmargin="5" topmargin="10" bottommargin="10" class="bodyBG">';

$sFileName		= "generalAddUpdateACC.php";
$sActionFileName= "index.php?vw=generalACC&mn=904&selCodeACC=$cat&cat=".$cat;
$title     =  $basicListACC[array_search($cat,$basicValACC)];

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();

$a = 1;
$FormLabel[$a]   	= "* Kode";
$FormElement[$a] 	= "code";
$FormType[$a]	  	= "text-sm";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "70";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "* Nama";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "text-sm";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "70";
$FormLength[$a]  	= "100";

// kod terma bayaran
if ($cat == "AL") {

$a++;
$FormLabel[$a]        = "* Bulan & Hari";
$FormElement[$a]      = "month_day"; // custom field name (can change if needed)
$FormType[$a]         = "custom";
$FormData[$a]         = ""; // leave blank; custom content
$FormDataValue[$a]    = "";
$FormCheck[$a]        = array(); // optional validation
$FormSize[$a]         = "0"; // not used
$FormLength[$a]       = "0"; // not used

$months = isset($_POST['month']) ? (int)$_POST['month'] : 0;
$days   = isset($_POST['day']) ? (int)$_POST['day'] : 0;

// Start from a base date (e.g., 1970-01-01)
$baseDate = new DateTime('0000-00-00');
// $interval = new DateInterval("P{$months}M{$days}D");
// $baseDate->add($interval);

// // Convert to MySQL DATETIME format
// echo $datetime = $baseDate->format('Y-m-d H:i:s');

// Save to DB
// INSERT INTO your_table (duration_column) VALUES ('$datetime')


}

if ($cat == "AN") {

	$a++;
	$FormLabel[$a]   	= "* Status";
	$FormElement[$a] 	= "Status_IDCode";
	$FormType[$a]      	= "radio";
	$FormData[$a]      	= array("Aktif", "Tidak Aktif");
	$FormDataValue[$a] 	= array('0','1');
	$FormCheck[$a]     	= array(CheckBlank);
	$FormSize[$a]      	= "1";
	$FormLength[$a]    	= "1";

}

if ($cat == "AA") {
	if ($sub <> "0") {
		//--- Prepare Jabatan list
		$deptList = Array();
		$deptVal  = Array();
		$GetDept = ctGeneralACC("","AA");
		if ($GetDept->RowCount() <> 0){
			while (!$GetDept->EOF) {
				array_push ($deptList, $GetDept->fields(name));
				array_push ($deptVal, $GetDept->fields(ID));
				$GetDept->MoveNext();
			}
		}	
	
		$a++;
		$FormLabel[$a]   	= "Induk";
		$FormElement[$a] 	= "parentID";
		$FormType[$a]	  	= "hidden";
		$FormData[$a]   	= $deptList;
		$FormDataValue[$a]	= $deptVal;
		$FormCheck[$a]   	= array();
		$FormSize[$a]    	= "1";
		$FormLength[$a]  	= "1";
	}

	$a++;
	$FormLabel[$a]   	= "Keterangan";
	$FormElement[$a] 	= "a_Keterangan";
	$FormType[$a]	  	= "textarea-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "68";
	$FormLength[$a]  	= "3";	

/*	$a++;
	$FormLabel[$a]   	= "Kod GST";
	$FormElement[$a] 	= "a_KodGst";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "30";
	$FormLength[$a]  	= "25";	
	
	$a++;
	$FormLabel[$a]   	= "Kod Cukai (Tax)";
	$FormElement[$a] 	= "a_KodCukai";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "30";
	$FormLength[$a]  	= "25";*/	

		//--- Prepare tax list
	$kumpList = Array();
	$kumpVal  = Array();
	$Getkump = ctGeneralACC("","AE");
	if ($Getkump->RowCount() <> 0){
		while (!$Getkump->EOF) {
			array_push ($kumpList, $Getkump->fields('code').' - '.$Getkump->fields('name'));
			array_push ($kumpVal, $Getkump->fields('ID'));
			$Getkump->MoveNext();
		}
	}

	$classList = Array();
	$classVal  = Array();
	$Getclass = ctGeneralACC("","AJ");
	if ($Getclass->RowCount() <> 0){
		while (!$Getclass->EOF) {
			array_push ($classList, $Getclass->fields('code').' - '.$Getclass->fields('name'));
			array_push ($classVal, $Getclass->fields('ID'));
			$Getclass->MoveNext();
		}
	}


	$a++;
	$FormLabel[$a]   	= "Kode Grup";
	$FormElement[$a] 	= "a_Kodkump";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= $kumpList;
	$FormDataValue[$a]	= $kumpVal;
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';

	$a++;
	$FormLabel[$a]   	= "Klasifikasi Sebagai";
	$FormElement[$a] 	= "a_class";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= $classList;
	$FormDataValue[$a]	= $classVal;
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';

	if ($sub <> "8" && $sub <> "12" && ($sub <> "0" && $sub <> "798" || $action == "tambah")) {
		$sSQL = "";
		$sWhere = "";
		$sWhere = "category = " . tosql('AA', "Text");
		$sWhere .= " AND ID IN (10,13,348,379,500,508,1172)"; 
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL = "SELECT * FROM generalacc";
		$sSQL .= $sWhere . ' ORDER BY code,ID'; 
		$GetCore = &$conn->Execute($sSQL);
	
		$coreList = Array();
		$coreVal  = Array();
		if ($GetCore->RowCount() <> 0){
			while (!$GetCore->EOF) {
				array_push($coreList, $GetCore->fields('name'));
				array_push($coreVal, $GetCore->fields('ID'));
				$GetCore->MoveNext();
			}
		}    
		
		$a++;
		$FormLabel[$a]    = "Induk Utama";
		$FormElement[$a]  = "coreID";
		$FormType[$a]     = "selectx";
		$FormData[$a]     = $coreList;
		$FormDataValue[$a]= $coreVal;
		$FormCheck[$a]    = array();
		$FormSize[$a]     = "1";
		$FormLength[$a]   = "1";
		$FormStyle[$a] 	  = 'style="width:465px;"';

		$a++;
		$FormLabel[$a]     = "Akun Pembagian Keuntungan";
		$FormElement[$a]   = "a_profitDivision";
		$FormType[$a]      = "radio";
		$FormData[$a]      = array("Tidak", "Ya");
		$FormDataValue[$a] = array('0','1');
		$FormCheck[$a]     = array(CheckBlank);
		$FormSize[$a]      = "1";
		$FormLength[$a]    = "1";
	}

}
	
if ($cat == "AB") {


	if ($sub <> "0") {
		//--- Prepare loan list
		$loanList = Array();
		$loanVal  = Array();
		$Getloan = ctGeneralACC("","AB");
		if ($Getloan->RowCount() <> 0){
			while (!$Getloan->EOF) {
				array_push ($loanList, $Getloan->fields(name));
				array_push ($loanVal, $Getloan->fields(ID));
				$Getloan->MoveNext();
			}
		}	
	
		$a++;
		$FormLabel[$a]   	= "Induk";
		$FormElement[$a] 	= "parentID";
		$FormType[$a]	  	= "hidden";
		$FormData[$a]   	= $loanList;
		$FormDataValue[$a]	= $loanVal;
		$FormCheck[$a]   	= array();
		$FormSize[$a]    	= "1";
		$FormLength[$a]  	= "1";
	}

	$a++;
	$FormLabel[$a]   	= "Alamat Biling";
	$FormElement[$a] 	= "b_Baddress";
	$FormType[$a]	  	= "textarea-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "68";
	$FormLength[$a]  	= "3";	

	$a++;
	$FormLabel[$a]   	= "Alamat Pengantar";
	$FormElement[$a] 	= "b_Daddress";
	$FormType[$a]	  	= "textarea-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "68";
	$FormLength[$a]  	= "3";	
	
	$a++;
	$FormLabel[$a]   	= "Nomor Telepon";
	$FormElement[$a] 	= "b_contact";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Nomor Faks";
	$FormElement[$a] 	= "b_faks";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Person In Charge";
	$FormElement[$a] 	= "b_pic";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Email";
	$FormElement[$a] 	= "b_email";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Opening Balance (RM)";
	$FormElement[$a] 	= "b_crelim";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	//--- Prepare terms list
	$termsList = Array();
	$termsVal  = Array();
	$getTerms = ctGeneralACC("","AL");
	if ($getTerms->RowCount() <> 0){
		while (!$getTerms->EOF) {
			array_push ($termsList, $getTerms->fields('code').' - '.$getTerms->fields('name'));
			array_push ($termsVal, $getTerms->fields('ID'));
			$getTerms->MoveNext();
		}
	}

	$a++;
	$FormLabel[$a]   	= "Credit Terms";
	$FormElement[$a] 	= "b_creter";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= $termsList;
	$FormDataValue[$a]	= $termsVal;
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';

	$a++;
	$FormLabel[$a]   	= "Akun Bank";
	$FormElement[$a] 	= "b_accbank";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Nama Bank";
	$FormElement[$a] 	= "b_namabank";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Business Registration No (SSM)";
	$FormElement[$a] 	= "b_busreg";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	/*$a++;
	$FormLabel[$a]   	= "GST Registration No";
	$FormElement[$a] 	= "b_gstreg";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "30";
	$FormLength[$a]  	= "25";*/

	$cartaList = Array();
	$cartaVal  = Array();
	$Getcarta = ctGeneralACC("","AA");
	if ($Getcarta->RowCount() <> 0){
		while (!$Getcarta->EOF) {
			array_push ($cartaList, $Getcarta->fields('code').' - '.$Getcarta->fields('name'));
			array_push ($cartaVal, $Getcarta->fields('ID'));
			$Getcarta->MoveNext();
		}
	}


		//--- Prepare tax list
	$taxList = Array();
	$taxVal  = Array();
	$Gettax = ctGeneralACC("","AD");
	if ($Gettax->RowCount() <> 0){
		while (!$Gettax->EOF) {
			array_push ($taxList, $Gettax->fields('code').' - '.$Gettax->fields('name'));
			array_push ($taxVal, $Gettax->fields('ID'));
			$Gettax->MoveNext();
		}
	}		


	/*$a++;
	$FormLabel[$a]   	= "Tax Code";
	$FormElement[$a] 	= "b_taxcode";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= $taxList;
	$FormDataValue[$a]	= $taxVal;
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";*/

	$a++;
	$FormLabel[$a]   	= "ID Number (LHDN)";
	$FormElement[$a] 	= "b_IDcompany";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "50";

	$a++;
	$FormLabel[$a]   	= "TIN (LHDN)";
	$FormElement[$a] 	= "b_tinLhdn";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "50";

	$a++;
	$FormLabel[$a]   	= "SST Number (LHDN)";
	$FormElement[$a] 	= "b_sstNumber";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "50";

	$a++;
	$FormLabel[$a]   	= "Kode GL (Daftar Akun)";
	$FormElement[$a] 	= "b_kodGL";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= $cartaList;
	$FormDataValue[$a]	= $cartaVal;
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';
}

if ($cat == "AC") {


	if ($sub <> "0") {
		//--- Prepare loan list
		$loanList = Array();
		$loanVal  = Array();
		$Getloan = ctGeneralACC("","AC");
		if ($Getloan->RowCount() <> 0){
			while (!$Getloan->EOF) {
				array_push ($loanList, $Getloan->fields(name));
				array_push ($loanVal, $Getloan->fields(ID));
				$Getloan->MoveNext();
			}
		}	
	
		$a++;
		$FormLabel[$a]   	= "Induk";
		$FormElement[$a] 	= "parentID";
		$FormType[$a]	  	= "hidden";
		$FormData[$a]   	= $loanList;
		$FormDataValue[$a]	= $loanVal;
		$FormCheck[$a]   	= array();
		$FormSize[$a]    	= "1";
		$FormLength[$a]  	= "1";
	}

$a++;
	$FormLabel[$a]   	= "Alamat Biling";
	$FormElement[$a] 	= "b_Baddress";
	$FormType[$a]	  	= "textarea-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "68";
	$FormLength[$a]  	= "3";	

	$a++;
	$FormLabel[$a]   	= "Alamat Pengantar";
	$FormElement[$a] 	= "b_Daddress";
	$FormType[$a]	  	= "textarea-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "68";
	$FormLength[$a]  	= "3";	
	
	$a++;
	$FormLabel[$a]   	= "Nomor Telepon";
	$FormElement[$a] 	= "b_contact";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Nomor Faks";
	$FormElement[$a] 	= "b_faks";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Person In Charge";
	$FormElement[$a] 	= "b_pic";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Email";
	$FormElement[$a] 	= "b_email";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Opening Balance (RM)";
	$FormElement[$a] 	= "b_crelim";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	//--- Prepare terms list
	$termsList = Array();
	$termsVal  = Array();
	$getTerms = ctGeneralACC("","AL");
	if ($getTerms->RowCount() <> 0){
		while (!$getTerms->EOF) {
			array_push ($termsList, $getTerms->fields('code').' - '.$getTerms->fields('name'));
			array_push ($termsVal, $getTerms->fields('ID'));
			$getTerms->MoveNext();
		}
	}

	$a++;
	$FormLabel[$a]   	= "Credit Terms";
	$FormElement[$a] 	= "b_creter";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= $termsList;
	$FormDataValue[$a]	= $termsVal;
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';

	$a++;
	$FormLabel[$a]   	= "Akun Bank";
	$FormElement[$a] 	= "b_accbank";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Nama Bank";
	$FormElement[$a] 	= "b_namabank";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Business Registration No (SSM)";
	$FormElement[$a] 	= "b_busreg";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$cartaList = Array();
	$cartaVal  = Array();
	$Getcarta = ctGeneralACC("","AA");
	if ($Getcarta->RowCount() <> 0){
		while (!$Getcarta->EOF) {
			array_push ($cartaList, $Getcarta->fields('code').' - '.$Getcarta->fields('name'));
			array_push ($cartaVal, $Getcarta->fields('ID'));
			$Getcarta->MoveNext();
		}
	}


	//--- Prepare tax list
	$taxList = Array();
	$taxVal  = Array();
	$Gettax = ctGeneralACC("","AD");
	if ($Gettax->RowCount() <> 0){
		while (!$Gettax->EOF) {
			array_push ($taxList, $Gettax->fields('code').' - '.$Gettax->fields('name'));
			array_push ($taxVal, $Gettax->fields('ID'));
			$Gettax->MoveNext();
		}
	}

	$a++;
	$FormLabel[$a]   	= "ID Number (LHDN)";
	$FormElement[$a] 	= "b_IDcompany";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "50";

	$a++;
	$FormLabel[$a]   	= "TIN (LHDN)";
	$FormElement[$a] 	= "b_tinLhdn";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "50";

	$a++;
	$FormLabel[$a]   	= "SST Number";
	$FormElement[$a] 	= "b_sstNumber";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "50";

	$a++;
	$FormLabel[$a]   	= "* Kode GL (Daftar Akun)";
	$FormElement[$a] 	= "b_kodGL";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= $cartaList;
	$FormDataValue[$a]	= $cartaVal;
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';
}

if ($cat == "AD") {


	if ($sub <> "0") {
		//--- Prepare loan list
		$loanList = Array();
		$loanVal  = Array();
		$Getloan = ctGeneralACC("","AD");
		if ($Getloan->RowCount() <> 0){
			while (!$Getloan->EOF) {
				array_push ($loanList, $Getloan->fields(name));
				array_push ($loanVal, $Getloan->fields(ID));
				$Getloan->MoveNext();
			}
		}	
	
		$a++;
		$FormLabel[$a]   	= "Induk";
		$FormElement[$a] 	= "parentID";
		$FormType[$a]	  	= "hidden";
		$FormData[$a]   	= $loanList;
		$FormDataValue[$a]	= $loanVal;
		$FormCheck[$a]   	= array();
		$FormSize[$a]    	= "1";
		$FormLength[$a]  	= "1";
	}

$a++;
	$FormLabel[$a]   	= "Keterangan";
	$FormElement[$a] 	= "a_Keterangan";
	$FormType[$a]	  	= "textarea-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "68";
	$FormLength[$a]  	= "3";	

	$a++;
	$FormLabel[$a]   	= "Daftar Pajak";
	$FormElement[$a] 	= "d_grouptax";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "10";

	$a++;
	$FormLabel[$a]   	= "Tarif (%)";
	$FormElement[$a] 	= "d_kadar";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "10";	

	$a++;
	$FormLabel[$a]   	= "Supply / Purchase";
	$FormElement[$a] 	= "d_suppur";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "30";	
}

if ($cat == "AF") {


	if ($sub <> "0") {
		//--- Prepare loan list
		$loanList = Array();
		$loanVal  = Array();
		$Getloan = ctGeneralACC("","AD");
		if ($Getloan->RowCount() <> 0){
			while (!$Getloan->EOF) {
				array_push ($loanList, $Getloan->fields(name));
				array_push ($loanVal, $Getloan->fields(ID));
				$Getloan->MoveNext();
			}
		}	
	
		$a++;
		$FormLabel[$a]   	= "Induk";
		$FormElement[$a] 	= "parentID";
		$FormType[$a]	  	= "hidden";
		$FormData[$a]   	= $loanList;
		$FormDataValue[$a]	= $loanVal;
		$FormCheck[$a]   	= array();
		$FormSize[$a]    	= "1";
		$FormLength[$a]  	= "1";
	}

$a++;
	$FormLabel[$a]   	= "No Akun";
	$FormElement[$a] 	= "f_noakaun";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "20";	

	$a++;
	$FormLabel[$a]   	= "Klasifikasi";
	$FormElement[$a] 	= "f_klasifikasi";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "30";

	$a++;
	$FormLabel[$a]   	= "Saldo Awal(Opening Balance)";
	$FormElement[$a] 	= "f_opbal";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "30";

}


if ($cat == "AG") {


	if ($sub <> "0") {
		//--- Prepare loan list
		$loanList = Array();
		$loanVal  = Array();
		$Getloan = ctGeneralACC("","AD");
		if ($Getloan->RowCount() <> 0){
			while (!$Getloan->EOF) {
				array_push ($loanList, $Getloan->fields(name));
				array_push ($loanVal, $Getloan->fields(ID));
				$Getloan->MoveNext();
			}
		}	
	
		$a++;
		$FormLabel[$a]   	= "Induk";
		$FormElement[$a] 	= "parentID";
		$FormType[$a]	  	= "hidden";
		$FormData[$a]   	= $loanList;
		$FormDataValue[$a]	= $loanVal;
		$FormCheck[$a]   	= array();
		$FormSize[$a]    	= "1";
		$FormLength[$a]  	= "1";
	}

	$a++;
	$FormLabel[$a]   	= "Kunci";
	$FormElement[$a] 	= "g_lockstat";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= array('Tidak','Ya');
	$FormDataValue[$a]	= array('0','1');
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';

	$a++;
	$FormLabel[$a]   	= "Tanggla Mulai Batch";
	$FormElement[$a] 	= "g_OpenDate";
	$FormType[$a]	  	= "date3";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "30";
	$FormStyle[$a] 		= 'style="width:465px;"';

	$a++;
	$FormLabel[$a]   	= "Tanggal Penutupan Batch";
	$FormElement[$a] 	= "g_CloseDate";
	$FormType[$a]	  	= "date3";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "30";
	$FormStyle[$a] 		= 'style="width:465px;"';

}

if ($cat == "AK") {
	
	if ($sub <> "0") {
		//--- Prepare loan list
		$loanList = Array();
		$loanVal  = Array();
		$Getloan = ctGeneralACC("","AK");
		if ($Getloan->RowCount() <> 0){
			while (!$Getloan->EOF) {
				array_push ($loanList, $Getloan->fields(name));
				array_push ($loanVal, $Getloan->fields(ID));
				$Getloan->MoveNext();
			}
		}	
	
		$a++;
		$FormLabel[$a]   	= "Induk";
		$FormElement[$a] 	= "parentID";
		$FormType[$a]	  	= "hidden";
		$FormData[$a]   	= $loanList;
		$FormDataValue[$a]	= $loanVal;
		$FormCheck[$a]   	= array();
		$FormSize[$a]    	= "1";
		$FormLength[$a]  	= "1";
	}

$a++;
	$FormLabel[$a]   	= "Alamat Biling";
	$FormElement[$a] 	= "b_Baddress";
	$FormType[$a]	  	= "textarea-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "68";
	$FormLength[$a]  	= "3";	

	$a++;
	$FormLabel[$a]   	= "Alamat Pengantar";
	$FormElement[$a] 	= "b_Daddress";
	$FormType[$a]	  	= "textarea-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "68";
	$FormLength[$a]  	= "3";	
	
	$a++;
	$FormLabel[$a]   	= "Nomor Telepon";
	$FormElement[$a] 	= "b_contact";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Nomor Faks";
	$FormElement[$a] 	= "b_faks";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Person In Charge";
	$FormElement[$a] 	= "b_pic";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Email";
	$FormElement[$a] 	= "b_email";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Credit Limit";
	$FormElement[$a] 	= "b_crelim";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	//--- Prepare terms list
	$termsList = Array();
	$termsVal  = Array();
	$getTerms = ctGeneralACC("","AL");
	if ($getTerms->RowCount() <> 0){
		while (!$getTerms->EOF) {
			array_push ($termsList, $getTerms->fields('code').' - '.$getTerms->fields('name'));
			array_push ($termsVal, $getTerms->fields('ID'));
			$getTerms->MoveNext();
		}
	}

	$a++;
	$FormLabel[$a]   	= "Credit Terms";
	$FormElement[$a] 	= "b_creter";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= $termsList;
	$FormDataValue[$a]	= $termsVal;
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';

	$a++;
	$FormLabel[$a]   	= "Akun Bank";
	$FormElement[$a] 	= "b_accbank";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Nama Bank";
	$FormElement[$a] 	= "b_namabank";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$a++;
	$FormLabel[$a]   	= "Business Registration No (SSM)";
	$FormElement[$a] 	= "b_busreg";
	$FormType[$a]	  	= "text-sm";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "70";
	$FormLength[$a]  	= "25";

	$cartaList = Array();
	$cartaVal  = Array();
	$Getcarta = ctGeneralACC("","AA");
	if ($Getcarta->RowCount() <> 0){
		while (!$Getcarta->EOF) {
			array_push ($cartaList, $Getcarta->fields('code').' - '.$Getcarta->fields('name'));
			array_push ($cartaVal, $Getcarta->fields('ID'));
			$Getcarta->MoveNext();
		}
	}

	$a++;
	$FormLabel[$a]   	= "Kode GL (Daftar Akun)";
	$FormElement[$a] 	= "b_kodGL";
	$FormType[$a]	  	= "selectx";
	$FormData[$a]   	= $cartaList;
	$FormDataValue[$a]	= $cartaVal;
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
	$FormStyle[$a] 		= 'style="width:465px;"';

		//--- Prepare tax list
	$taxList = Array();
	$taxVal  = Array();
	$Gettax = ctGeneralACC("","AD");
	if ($Gettax->RowCount() <> 0){
		while (!$Gettax->EOF) {
			array_push ($taxList, $Gettax->fields('code').' - '.$Gettax->fields('name'));
			array_push ($taxVal, $Gettax->fields('ID'));
			$Gettax->MoveNext();
		}
	}
}


if ($action == "kemaskini") {
	$a++;
	$FormLabel[$a]  	= "Tanggal Dibuat";
	$FormElement[$a] 	= "createdDate";
	$FormType[$a]	  	= "hiddenDate";
	$FormData[$a]    	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]  	= "Dibuat Oleh";
	$FormElement[$a] 	= "createdBy";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]    	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]  	= "Tanggal Diperbarui";
	$FormElement[$a] 	= "updatedDate";
	$FormType[$a]  		= "hiddenDate";
	$FormData[$a]    	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";

	$a++;
	$FormLabel[$a]	  	= "Diperbarui Oleh";
	$FormElement[$a] 	= "updatedBy";
	$FormType[$a]	  	= "hidden";
	$FormData[$a]    	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array();
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";
}
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
		//if ($d_Address <> "") $d_Address = '<pre>'.$d_Address.'</pre>';
		if ($parentID == "") $parentID = "0";
		if ($coreID == "") $coreID = "0";
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$createdBy 	= get_session("Cookie_userName");
		$createdDate = date("Y-m-d H:i:s");             
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");               
		$sSQL = "";
		switch(strtolower($SubmitForm)) 
		{
			case "simpan":
				$sSQL	= "INSERT INTO generalacc (" . 
				          "category," . 
				          "code," . 
						  "name,";			
						//   if ($sub <> "0" && $sub <> "8" && $sub <> "12") {
					if ($cat == "AA") {
						$sSQL  .= "parentID," .
								  "a_Keterangan,".
								  //"a_KodGst,".
								  "a_Kodkump,".
								  "a_class,".
								  "coreID,".
								  //"a_KodCukai,";
								  "a_profitDivision,";
					}
				// }
				if ($cat == "AB") {
					$sSQL  .= "parentID," .
							  "b_Baddress,".
							  "b_Daddress,".
							  "b_contact,".
							  "b_faks,".
							  "b_pic,".
							  "b_email,".
							  "b_crelim,".
							  "b_creter,".
							  "b_accbank,".
							  "b_namabank,".
							  "b_busreg,".
							  "b_kodGL,".
							  "b_IDcompany,".
							  "b_tinLhdn,".
							  "b_sstNumber,";
							 // "b_gstreg,".
							 // "b_taxcode,";
				}
				if ($cat == "AC") {
					$sSQL  .= "parentID," .
							  "b_Baddress,".
							  "b_Daddress,".
							  "b_contact,".
							  "b_faks,".
							  "b_pic,".
							  "b_email,".
							  "b_crelim,".
							  "b_creter,".
							  "b_accbank,".
							  "b_namabank,".
							  "b_busreg,".
							  "b_kodGL,".
							  "b_IDcompany,".
							  "b_tinLhdn,".
							  "b_sstNumber,";
							//  "b_gstreg,".
							 // "b_taxcode,";
				}
				if ($cat == "AD") {
					$sSQL  .= "parentID," .
							  "a_Keterangan,".
							  "d_grouptax,".
							  "d_kadar,".
							  "d_suppur,";
				}
				if ($cat == "AF") {
					$sSQL  .= "parentID," .
							  "f_noakaun,".
							  "f_klasifikasi,".
							  "f_opbal,";
				}
				if ($cat == "AG") {
					$sSQL  .= "parentID," .
							  "g_lockstat,".
							  "g_OpenDate,".
							  "g_CloseDate,";
				}
				if ($cat == "AK") {
					$sSQL  .= "parentID," .
							  "b_Baddress,".
							  "b_Daddress,".
							  "b_contact,".
							  "b_faks,".
							  "b_pic,".
							  "b_email,".
							  "b_crelim,".
							  "b_creter,".
							  "b_accbank,".
							  "b_namabank,".
							  "b_busreg,".
							  "b_kodGL,";
							//  "b_gstreg,".
							 // "b_taxcode,";
				}
				if ($cat == "AN") {
					$sSQL  .= "Status_IDCode,";
				}
					
				$sSQL  .= "createdDate," . 
				          "createdBy," . 
				          "updatedDate," . 
				          "updatedBy)" . 
				          " VALUES (" . 
				          tosql($cat, "Text") . "," .
				          tosql($code, "Text") . "," .
				          tosql($name, "Text") . ",";
						//   if ($sub <> "0" && $sub <> "8" && $sub <> "12") {		
					if ($cat == "AA") {
						$sSQL   .=tosql($parentID, "Number") . "," .
						          tosql($a_Keterangan, "Text") . ",".
								  //tosql($a_KodGst, "Text") . "," .
								  tosql($a_Kodkump, "Text") . ",".
								  tosql($a_class, "Text") . ",".
								  tosql($coreID, "Number") . ",".
						          //tosql($a_KodCukai, "Text") . ",";
								  tosql($a_profitDivision, "Number") . "," ;
					}
				// }
				if ($cat == "AB") {
				$sSQL   .=tosql($parentID, "Text") . "," .
						  tosql($b_Baddress, "Text") . ",".
						  tosql($b_Daddress, "Text") . ",".
						  tosql($b_contact, "Text") . ",".
						  tosql($b_faks, "Text") . ",".
						  tosql($b_pic, "Text") . ",".
						  tosql($b_email, "Text") . ",".
						  tosql($b_crelim, "Text") . ",".
						  tosql($b_creter, "Text") . ",".
						  tosql($b_accbank, "Text") . ",".
						  tosql($b_namabank, "Text") . ",".
						  tosql($b_busreg, "Text") . ",".
						  tosql($b_kodGL, "Text") . ",".
						  tosql($b_IDcompany, "Text") . ",".
						  tosql($b_tinLhdn, "Text") . ",".
						  tosql($b_sstNumber, "Text") . ",";
						//  tosql($b_gstreg, "Text") . ",".
						 // tosql($b_taxcode, "Text") . ",";
				}
				if ($cat == "AC") {
				$sSQL   .=tosql($parentID, "Text") . "," .
						  tosql($b_Baddress, "Text") . ",".
						  tosql($b_Daddress, "Text") . ",".
						  tosql($b_contact, "Text") . ",".
						  tosql($b_faks, "Text") . ",".
						  tosql($b_pic, "Text") . ",".
						  tosql($b_email, "Text") . ",".
						  tosql($b_crelim, "Text") . ",".
						  tosql($b_creter, "Text") . ",".
						  tosql($b_accbank, "Text") . ",".
						  tosql($b_namabank, "Text") . ",".
						  tosql($b_busreg, "Text") . ",".
						  tosql($b_kodGL, "Text") . ",".
						  tosql($b_IDcompany, "Text") . ",".
						  tosql($b_tinLhdn, "Text") . ",".
						  tosql($b_sstNumber, "Text") . ",";
						 // tosql($b_gstreg, "Text") . ",".
						 // tosql($b_taxcode, "Text") . ",";
				}
				if ($cat == "AD") {
				$sSQL   .=tosql($parentID, "Text") . "," .
						  tosql($a_Keterangan, "Text") . ",".
						  tosql($d_grouptax, "Text") . ",".
						  tosql($d_kadar, "Text") . ",".
						  tosql($d_suppur, "Text") . ",";
				}
				if ($cat == "AF") {
				$sSQL   .=tosql($parentID, "Text") . "," .
						  tosql($f_noakaun, "Text") . ",".
						  tosql($f_klasifikasi, "Text") . ",".
						  tosql($f_opbal, "Text") . ",";
				}
				if ($cat == "AG") {
				$sSQL   .=tosql($parentID, "Text") . "," .
						  tosql($g_lockstat, "Text") . ",".
						  tosql($g_OpenDate, "Text") . ",".
						  tosql($g_CloseDate, "Text") . ",";
				}
				if ($cat == "AK") {
					$sSQL   .=tosql($parentID, "Text") . "," .
							  tosql($b_Baddress, "Text") . ",".
							  tosql($b_Daddress, "Text") . ",".
							  tosql($b_contact, "Text") . ",".
							  tosql($b_faks, "Text") . ",".
							  tosql($b_pic, "Text") . ",".
							  tosql($b_email, "Text") . ",".
							  tosql($b_crelim, "Text") . ",".
							  tosql($b_creter, "Text") . ",".
							  tosql($b_accbank, "Text") . ",".
							  tosql($b_namabank, "Text") . ",".
							  tosql($b_busreg, "Text") . ",".
							  tosql($b_kodGL, "Text") . ",";
							 // tosql($b_gstreg, "Text") . ",".
							 // tosql($b_taxcode, "Text") . ",";
				}
				if ($cat == "AN") {
					$sSQL   .=tosql($Status_IDCode, "Number") . ",";
				}
				$sSQL   .=tosql($createdDate, "Text") . "," .
				          tosql($createdBy, "Text") . ",".
						  tosql($updatedDate, "Text") . "," .
				          tosql($updatedBy, "Text") . ")";
				$msg = "Data telah berhasil disimpan !";
			break;
			case "kemaskini":
			    $sWhere = "ID=" . tosql($pk, "Number");
	        	$sSQL	= "UPDATE generalacc SET " .
				          "code=" . tosql($code, "Text") .
				          ",name=" . tosql($name, "Text");
				if ($cat == "AA") {
					$sSQL	.= ",a_Keterangan=" . tosql($a_Keterangan, "Text") .
							  // ",a_KodGst=" . tosql($a_KodGst, "Text") .
								",a_Kodkump=" . tosql($a_Kodkump, "Text") .
								",a_class=" . tosql($a_class, "Text") .
							   ",coreID=" . tosql($coreID, "Number") .
							   //",a_KodCukai=" . tosql($a_KodCukai, "Text") ;
							   ",a_profitDivision=" . tosql($a_profitDivision, "Number") ;
				}						  
				if ($cat == "AB") {
					$sSQL	.= ",b_Baddress=" . tosql($b_Baddress, "Text") .
							   ",b_Daddress=" . tosql($b_Daddress, "Text") .
							   ",b_contact=" . tosql($b_contact, "Text") .
							   ",b_faks=" . tosql($b_faks, "Text") .
							   ",b_pic=" . tosql($b_pic, "Text") .
							   ",b_email=" . tosql($b_email, "Text") .
							   ",b_crelim=" . tosql($b_crelim, "Text") .
							   ",b_creter=" . tosql($b_creter, "Text") .
							   ",b_accbank=" . tosql($b_accbank, "Text") .
							   ",b_namabank=" . tosql($b_namabank, "Text") .
							   ",b_busreg=" . tosql($b_busreg, "Text") .
							   ",b_kodGL=" . tosql($b_kodGL, "Text") .
							   ",b_IDcompany=" . tosql($b_IDcompany, "Text") .
							   ",b_tinLhdn=" . tosql($b_tinLhdn, "Text") .
							   ",b_sstNumber=" . tosql($b_sstNumber, "Text") ;
				}
				if ($cat == "AC") {
					$sSQL	.= ",b_Baddress=" . tosql($b_Baddress, "Text") .
							   ",b_Daddress=" . tosql($b_Daddress, "Text") .
							   ",b_contact=" . tosql($b_contact, "Text") .
							   ",b_faks=" . tosql($b_faks, "Text") .
							   ",b_pic=" . tosql($b_pic, "Text") .
							   ",b_email=" . tosql($b_email, "Text") .
							   ",b_crelim=" . tosql($b_crelim, "Text") .
							   ",b_creter=" . tosql($b_creter, "Text") .
							   ",b_accbank=" . tosql($b_accbank, "Text") .
							   ",b_namabank=" . tosql($b_namabank, "Text") .
							   ",b_busreg=" . tosql($b_busreg, "Text") .
							   ",b_kodGL=" . tosql($b_kodGL, "Text") .
							   ",b_IDcompany=" . tosql($b_IDcompany, "Text") .
							   ",b_tinLhdn=" . tosql($b_tinLhdn, "Text") .
							   ",b_sstNumber=" . tosql($b_sstNumber, "Text") ;
				}
				if ($cat == "AD") {
					$sSQL	.= ",a_Keterangan=" . tosql($a_Keterangan, "Text") .
								",d_grouptax=" . tosql($d_grouptax, "Text") .
								",d_kadar=" . tosql($d_kadar, "Text") .
							   	",d_suppur=" . tosql($d_suppur, "Text") ;
				}
				if ($cat == "AF") {
					$sSQL	.= ",f_noakaun=" . tosql($f_noakaun, "Text") .
								",f_klasifikasi=" . tosql($f_klasifikasi, "Text") .
							   	",f_opbal=" . tosql($f_opbal, "Text") ;
				}
				if ($cat == "AG") {
					$sSQL	.= ",g_lockstat=" . tosql($g_lockstat, "Text") .
								",g_OpenDate=" . tosql($g_OpenDate, "Text") .
							   	",g_CloseDate=" . tosql($g_CloseDate, "Text") ;
				}
				if ($cat == "AK") {
					$sSQL	.= ",b_Baddress=" . tosql($b_Baddress, "Text") .
							   ",b_Daddress=" . tosql($b_Daddress, "Text") .
							   ",b_contact=" . tosql($b_contact, "Text") .
							   ",b_faks=" . tosql($b_faks, "Text") .
							   ",b_pic=" . tosql($b_pic, "Text") .
							   ",b_email=" . tosql($b_email, "Text") .
							   ",b_crelim=" . tosql($b_crelim, "Text") .
							   ",b_creter=" . tosql($b_creter, "Text") .
							   ",b_accbank=" . tosql($b_accbank, "Text") .
							   ",b_namabank=" . tosql($b_namabank, "Text") .
							   ",b_busreg=" . tosql($b_busreg, "Text") .
							   ",b_kodGL=" . tosql($b_kodGL, "Text") ;								   
							   //",b_gstreg=" . tosql($b_gstreg, "Text") .
							   //",b_taxcode=" . tosql($b_taxcode, "Text") ;
				}
				if ($cat == "AN") {
					$sSQL	.= ",Status_IDCode=" . tosql($Status_IDCode, "Number") ;
				}
			$sSQL	.= ",updatedDate=" . tosql($updatedDate, "Text") .
				          ",updatedBy=" . tosql($updatedBy, "Text") ;
				$sSQL .= " where " . $sWhere;
				$msg = "Data telah berhasil diupdate !";
			break;
		}

		$rs = &$conn->Execute($sSQL);
		
		if ($cat = 'O') {
			$Section[] = $HTTP_POST_VARS["Section[]"];
			$sSQL = ' DELETE FROM codegroup WHERE groupNo = ' . tosql($code ,"Text");
			$rs = &$conn->Execute($sSQL);	
			for ($i = 0; $i < count($Section); $i++) {
				if ($Section[$i] <> "" AND $Section[$i] <> "-1")	{
					$sSQL = "";
					$sSQL	= ' INSERT INTO codegroup (' . 
						          'groupNo,' . 
						          'codeNo)' . 
						          ' VALUES (' . 
						          tosql($code, "Text") . ',' .
						          tosql($Section[$i], "Text") . ')';
					$rs = &$conn->Execute($sSQL);
				}
			}	
		}

		// Untuk function tambah syarikat menu penghutang/pemiutang. Bila Simpan, pop-up terus tutup (tak directed pada page informasi akaun)
		if ($page <> "") {
			print '<script>
						alert ("'.$msg.'");
						window.close();
					</script>';
		} else {
			print '<script>
						alert ("'.$msg.'");
						opener.document.location = "' . $sActionFileName . '";
						window.close();
					</script>';
		}
	}
}			
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

if ($action == "kemaskini") {
	if ($pk <> "") {
		//--- Begin : query database for information ---------------------------------------------
		$sWhere = "ID = " . tosql($pk ,"Number");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL = "SELECT * FROM generalacc ";
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		//--- End   : query database for information ---------------------------------------------
	}
}

if ($action == "simpan") {
	print '<form name="MyForm" action='.$sFileName.'?mn=904&selCodeACC='.$cat.'&action='.$action.' method=post>';
} else {
		print '<form name="MyForm" action='.$sFileName.'?action='.$action.'&selCodeACC='.$cat.'&mn=904&pk='.$pk.' method=post>';
	}

print '
<table border=0 cellpadding=3 cellspacing=1 width=95% align="center" class="table table-sm table-striped" style="font-size:10pt">
	<tr class="table-primary">
		<td colspan="2">';
		
if ($action == "tambah") print '<h6 class="card-subtitle">Tambah '.$title; else print '<h6 class="card-subtitle">Kemaskini '.$title.' : '.tohtml($rs->fields(name));
print '</h6></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($action == "kemaskini") {	
		if ($cat == "AA") {
			if ($sub <> "0" && $sub <> "8" && $sub <> "12") {	
				if ($i == 9) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
			} else if ($sub == "0") {
				if ($i == 6) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
			} else {
				if ($i == 7) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
			}
		}
		if ($cat == "AB") {
			if ($i == 18) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
		}
		if ($cat == "AC") {
//			if ($i == 4) print '<tr><td class=Header colspan=2>Audit Informasi :</td></tr>';
			if ($i == 18) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
		}
		if ($cat == "AD") {
//			if ($i == 4) print '<tr><td class=Header colspan=2>Audit Informasi :</td></tr>';
			if ($i == 7) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
		}
		if ($cat == "AF") {
//			if ($i == 4) print '<tr><td class=Header colspan=2>Audit Informasi :</td></tr>';
			if ($i == 6) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
		}
		if ($cat == "AG") {
//			if ($i == 4) print '<tr><td class=Header colspan=2>Audit Informasi :</td></tr>';
			if ($i == 7) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
		}
		if ($cat == "AK") {
//			if ($i == 4) print '<tr><td class=Header colspan=2>Audit Informasi :</td></tr>';
			if ($i == 15) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
		}
		if ($cat == "AL" || $cat == "AN") {
			if ($i == 4) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
		}
		if ($cat <> "AA" and $cat <> "AB" and $cat <> "AC" and $cat <> "AD" and $cat <> "AF" and $cat <> "AG" and $cat <> "AK" and $cat <> "AL" and $cat <> "AN") {
			if ($i == 3) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
		}
	}
	print '<tr valign=top><td class=Data align=right>'.$FormLabel[$i].'</td>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<td class=errdata>';
	else
	  print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($action == "kemaskini") {
		$strFormValue = tohtml($rs->fields($FormElement[$i])); 
		if ($FormType[$i] == 'textarea') {
			$strFormValue = str_replace("<pre>","",$rs->fields($FormElement[$i]));
			$strFormValue = str_replace("</pre>","",$strFormValue);
		}
		if ($cat == "J") {
			//if ($i == 4) $strFormValue = dlookup("codegroup", "groupNo", "codeNo=" . tosql($rs->fields('code'), "Text"));
		}
	} else {
		$strFormValue = $$FormElement[$i];
		if ($cat == "AA") {
			if ($i == 3) $strFormValue = $sub;
		}
		if ($cat == "AB") {
			if ($i == 3) $strFormValue = $sub;
		}
		if ($cat == "AC") {
			if ($i == 3) $strFormValue = $sub;
		}
		if ($cat == "AD") {
			if ($i == 3) $strFormValue = $sub;
		}
		if ($cat == "AF") {
			if ($i == 3) $strFormValue = $sub;
		}
		if ($cat == "AG") {
			if ($i == 3) $strFormValue = $sub;
		}
		if ($cat == "AK") {
			if ($i == 3) $strFormValue = $sub;
		}
	}
	FormEntry($FormLabel[$i], 
			  $FormElement[$i], 
			  $FormType[$i],
			  $strFormValue,
			  $FormData[$i],
			  $FormDataValue[$i],
			  $FormSize[$i],
			  $FormLength[$i],
			  $FormStyle[$i]);
		  
	//--- End   : Call function FormEntry ---------------------------------------------------------  
    print '&nbsp;</td></tr>';
	if ($cat == 'O') {
		if ($i == 2) {
			print '
			<tr><td class=Header colspan=2>Pilihan Kode Potongan :</td></tr>
			<tr valign=top><td class=Data colspan="2">
				<table border="0" cellspacing="1" cellpadding="3" width="95%" align="center">
				    <tr valign="top">
				    	<td class="data">Daftar Kode Potongan<br>
						<select name="nonSection[]" multiple size="15">';
			if (count($deductList) ==  0) {
				print 	'	<option value="">-None-</option>';				
			} else {
				for ($j = 0; $j < count($deductList); $j++) {
					if (!in_array($deductVal[$j], $SectionVal))
						print 	'	<option value="' . $deductVal[$j] . '">' . $deductList[$j] . '</option>';		
				}
			}
			print	' 	</select>
						</td>
						<td class="data" valign="middle">
						<input type="hidden" name="hidMoveFlag" value="0">
						<input type="hidden" name="hidUpdateFlag" value="0">
			        	<input type="button" value=">>" onClick="document.MyForm.hidMoveFlag.value=1; addSection()" class=textFont><br>
			        	<input type="button" value="<<" onClick="document.MyForm.hidMoveFlag.value=1; removeSection()" class=textFont>
				        </td>				
						<td valign="top" class="data">Kode Potongan Pilihan<br>
				        <select name="Section[]" multiple size="15">';
			if (count($SectionVal) == 0)	{	
			   	print 	'	<option value="-1">-None-</option>';
			} else	{
				for ($j = 0; $j < count($SectionVal); $j++) {
					print 	'<option value="' . $SectionVal[$j] . '">' . 
							$deductList[array_search($SectionVal[$j], $deductVal)] . 
							'</option>';		
				}
			}	
			print	'  	</select>
						</td>
					</tr>	
				</table>
			</td>';
		}	
	}
}

print '<tr><td colspan=2 align=center class=Data>';
if ($action == "tambah") {
	print '
	<input type=hidden name=ID>
	<input type="hidden" name="cat" value="'.$cat.'">
	<input type="hidden" name="sub" value="'.$sub.'">
	<input type="hidden" name="page" value="'.$page.'">
	<input type=Submit name=SubmitForm class="btn btn-primary" value="Simpan">
	<!--input type=Reset name=ResetForm class="btn btn-secondary" value="Isi semula"-->
	';
} else { 
    if($cat == 'C' && $sub == '0'){
	print '&nbsp;';	
	}else{
	print '
	<input type=hidden name=ID class="textFont" value='.$pk.'>
	<input type="hidden" name="cat" value="'.$cat.'">
	<input type="hidden" name="sub" value="'.$sub.'">
	<input type=Submit name=SubmitForm class="btn btn-primary btn-md waves-effect waves-light" value="Kemaskini">';
	}
}
print '		</td>
		</tr>
</table>
</form>';
//print $cat . ' - ' . $sub;
include("footer.php");	
?>