<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	generalAddUpdate.php
 *          Date 		: 	03/06/06
 *********************************************************************************/
session_start();
if (!isset($sub))     $sub = "0";
if (!isset($cat))    $cat = "";

include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");window.close();</script>';
    exit;
}
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
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
<body leftmargin="5" rightmargin="5" topmargin="10" bottommargin="10" style="margin-top: 10px;">';

$sFileName        = "generalAddUpdate.php";
$sActionFileName = "index.php?vw=general&mn=903&selCode=$cat&cat=" . $cat;
$title     =  $basicList[array_search($cat, $basicVal)];

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = 1;
$FormLabel[$a]       = "* Kod";
$FormElement[$a]     = "code";
//if ($cat == 'O') 
//	$FormType[$a]	  	= "hiddentext";
//else
$FormType[$a]          = "text-sm";
$FormData[$a]       = "";
$FormDataValue[$a]    = "";
$FormCheck[$a]       = array(CheckBlank);
$FormSize[$a]        = "70";
$FormLength[$a]      = "20";

$a++;
$FormLabel[$a]       = "* Nama";
$FormElement[$a]     = "name";
//if ($cat == 'O') 
//	$FormType[$a]	  	= "hiddentext";
//else
$FormType[$a]          = "text-sm";
$FormData[$a]       = "";
$FormDataValue[$a]    = "";
$FormCheck[$a]       = array(CheckBlank);
$FormSize[$a]        = "70";
$FormLength[$a]      = "100";

if ($cat == "B") {
    if ($sub <> "0") {
        //--- Prepare Jabatan list
        $deptList = array();
        $deptVal  = array();
        $GetDept = ctGeneral("", "B");
        if ($GetDept->RowCount() <> 0) {
            while (!$GetDept->EOF) {
                array_push($deptList, $GetDept->fields(name));
                array_push($deptVal, $GetDept->fields(ID));
                $GetDept->MoveNext();
            }
        }

        $a++;
        $FormLabel[$a]       = "Induk";
        $FormElement[$a]     = "parentID";
        $FormType[$a]          = "hidden";
        $FormData[$a]       = $deptList;
        $FormDataValue[$a]    = $deptVal;
        $FormCheck[$a]       = array();
        $FormSize[$a]        = "1";
        $FormLength[$a]      = "1";
    }

    $a++;
    $FormLabel[$a]       = "Alamat";
    $FormElement[$a]     = "b_Address";
    $FormType[$a]          = "textarea-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "68";
    $FormLength[$a]      = "3";

    $a++;
    $FormLabel[$a]       = "Orang Dihubungi";
    $FormElement[$a]     = "b_ContactPerson";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "25";

    $a++;
    $FormLabel[$a]       = " Nombor Telefon";
    $FormElement[$a]     = "b_ContactNo";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "25";
}

if ($cat == "C") {

    if ($sub <> "0") {
        //--- Prepare loan list
        $loanList = array();
        $loanVal  = array();
        $Getloan = ctGeneral("", "C");
        if ($Getloan->RowCount() <> 0) {
            while (!$Getloan->EOF) {
                array_push($loanList, $Getloan->fields(name));
                array_push($loanVal, $Getloan->fields(ID));
                $Getloan->MoveNext();
            }
        }

        $a++;
        $FormLabel[$a]       = "Induk";
        $FormElement[$a]     = "parentID";
        $FormType[$a]          = "hidden";
        $FormData[$a]       = $loanList;
        $FormDataValue[$a]    = $loanVal;
        $FormCheck[$a]       = array();
        $FormSize[$a]        = "1";
        $FormLength[$a]      = "1";
    }

    //--- Prepare deduct list
    $deductList = array();
    $deductVal  = array();
    $GetDeduct = ctGeneral("", "J");
    if ($GetDeduct->RowCount() <> 0) {
        while (!$GetDeduct->EOF) {
            array_push($deductList, $GetDeduct->fields('code') . ' - ' . $GetDeduct->fields('name'));
            array_push($deductVal, $GetDeduct->fields('ID'));
            $GetDeduct->MoveNext();
        }
    }

    $a++;
    $FormLabel[$a]       = "Kod Potongan";
    $FormElement[$a]     = "c_Deduct";
    $FormType[$a]          = "selectx";
    $FormData[$a]       = $deductList;
    $FormDataValue[$a]    = $deductVal;
    $FormCheck[$a]       = array(CheckBlank);
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";
    $FormStyle[$a]         = 'style="width:465px;"';

    $a++;
    $FormLabel[$a]       = "Caj (%)";
    $FormElement[$a]     = "c_Caj";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckDecimal);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "4";

    $a++;
    $FormLabel[$a]       = "Tempoh Maksima";
    $FormElement[$a]     = "c_Period";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckNumeric);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "3";

    $a++;
    $FormLabel[$a]       = "Jumlah Maksima";
    $FormElement[$a]     = "c_Maksimum";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckDecimal);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "10";

    /*$a++;
	$FormLabel[$a]   	= "Penjamin";
	$FormElement[$a] 	= "c_EarlyDeduct";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('Tidak','Ya');
	$FormDataValue[$a]	= array('0','1');
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";*/

    $a++;
    $FormLabel[$a]       = "Perlu Penjamin";
    $FormElement[$a]     = "c_gurrantor";
    $FormType[$a]          = "selectx";
    $FormData[$a]       = array('Tidak', 'Ya');
    $FormDataValue[$a]    = array('0', '1');
    $FormCheck[$a]       = array(CheckBlank);
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";
    $FormStyle[$a]         = 'style="width:465px;"';

    $a++;
    $FormLabel[$a]       = "Pembiayaan Aktif";
    $FormElement[$a]     = "c_Aktif";
    $FormType[$a]          = "selectx";
    $FormData[$a]       = array('Tidak', 'Ya');
    $FormDataValue[$a]    = array('0', '1');
    $FormCheck[$a]       = array(CheckBlank);
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";
    $FormStyle[$a]         = 'style="width:465px;"';

    /*$a++;
	$FormLabel[$a]   	= "Floating Rate";
	$FormElement[$a] 	= "c_floatrate";
	$FormType[$a]	  	= "select";
	$FormData[$a]   	= array('Tidak','Ya');
	$FormDataValue[$a]	= array('0','1');
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "1";
	$FormLength[$a]  	= "1";*/
}

if ($cat == "D") {
    $a++;
    $FormLabel[$a]       = "Jenis Panel";
    $FormElement[$a]     = "d_Type";
    $FormType[$a]          = "selectx";
    $FormData[$a]       = array('Panel', 'Insurance', 'Tabung');
    $FormDataValue[$a]    = array('P', 'I', 'T');
    $FormCheck[$a]       = array(CheckBlank);
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";
    $FormStyle[$a]         = 'style="width:465px;"';

    $a++;
    $FormLabel[$a]       = "Alamat";
    $FormElement[$a]     = "d_Address";
    $FormType[$a]          = "textarea-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "68";
    $FormLength[$a]      = "3";

    $a++;
    $FormLabel[$a]       = "Orang Dihubungi";
    $FormElement[$a]     = "d_Contact";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "25";

    $a++;
    $FormLabel[$a]       = " Nombor Telefon";
    $FormElement[$a]     = "d_Phone";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "25";
}

if ($cat == "G") {
    $a++;
    $FormLabel[$a]       = "Harga Syer";
    $FormElement[$a]     = "g_Price";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckDecimal);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "5";

    $a++;
    $FormLabel[$a]       = "Minimum Unit";
    $FormElement[$a]     = "g_Minimum";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckNumeric);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "10";

    $a++;
    $FormLabel[$a]       = "Jumlah Unit Syer";
    $FormElement[$a]     = "g_Maksimum";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckNumeric);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "10";
}

if ($cat == "J") {

    $classList = array();
    $classVal  = array();
    $Getclass = ctGeneralACC1("", "AA");
    if ($Getclass->RowCount() <> 0) {
        while (!$Getclass->EOF) {
            array_push($classList, $Getclass->fields('code') . ' - ' . $Getclass->fields('name'));
            array_push($classVal, $Getclass->fields('ID'));
            $Getclass->MoveNext();
        }
    }


    $a++;
    $FormLabel[$a]       = "Kod Akaun";
    $FormElement[$a]     = "c_Panel";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "20";

    $a++;
    $FormLabel[$a]       = "* Kod Master (GL)";
    $FormElement[$a]     = "c_master";
    $FormType[$a]          = "selectx";
    $FormData[$a]       = $classList;
    $FormDataValue[$a]    = $classVal;
    $FormCheck[$a]       = array(CheckBlank);
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";
    $FormStyle[$a]         = 'style="width:465px;"';

    //dah hardcode syer dan yuran untuk caruman
    // $a++;
    // $FormLabel[$a]     = "Status Aktif";
    // $FormElement[$a]   = "j_Aktif";
    // $FormType[$a]      = "radio";
    // $FormData[$a]      = array("Tidak Aktif", "Aktif");
    // $FormDataValue[$a] = array('0','1');
    // $FormCheck[$a]     = array(CheckBlank);
    // $FormSize[$a]      = "1";
    // $FormLength[$a]    = "1";

    $a++;
    $FormLabel[$a]     = "Pindahan";
    $FormElement[$a]   = "j_Pindah";
    $FormType[$a]      = "radio";
    $FormData[$a]      = array("Tidak", "Ya");
    $FormDataValue[$a] = array('0', '1');
    $FormCheck[$a]     = array(CheckBlank);
    $FormSize[$a]      = "1";
    $FormLength[$a]    = "1";

    $a++;
    $FormLabel[$a]     = "Status Caj Penjelasan Awal";
    $FormElement[$a]   = "j_EarlyDeduct";
    $FormType[$a]      = "radio";
    $FormData[$a]      = array("Tidak", "Ya");
    $FormDataValue[$a] = array('0', '1');
    $FormCheck[$a]     = array(CheckBlank);
    $FormSize[$a]      = "1";
    $FormLength[$a]    = "1";

    $a++;
    $FormLabel[$a]     = "Peratus Caj";
    $FormElement[$a]   = "j_Percentage";
    $FormType[$a]      = "textx";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "20";
    $FormLength[$a]      = "20";

    $a++;
    $FormLabel[$a]     = "Amaun <i>(Default)</i>";
    $FormElement[$a]   = "j_Amount";
    $FormType[$a]          = "textx";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "20";
    $FormLength[$a]      = "20";

    $a++;
    $FormLabel[$a]       = "Tahap Keutamaan";
    $FormElement[$a]     = "priority";
    $FormType[$a]          = "textx";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "20";
    $FormLength[$a]      = "20";
}

if ($cat == "M") {
    $a++;
    $FormLabel[$a]       = "Mula";
    $FormElement[$a]     = "m_Start";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckDecimal);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "10";

    $a++;
    $FormLabel[$a]       = "Akhir";
    $FormElement[$a]     = "m_End";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckDecimal);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "10";
}

if ($cat == "N") {
    $a++;
    $FormLabel[$a]       = "Mula";
    $FormElement[$a]     = "n_Start";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckNumeric);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "10";

    $a++;
    $FormLabel[$a]       = "Akhir";
    $FormElement[$a]     = "n_End";
    $FormType[$a]          = "text-sm";
    $FormData[$a]       = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array(CheckBlank, CheckNumeric);
    $FormSize[$a]        = "70";
    $FormLength[$a]      = "10";
}
/*
if ($cat == "P") {
	$a++;
	$FormLabel[$a]   	= "Kod";
	$FormElement[$a] 	= "kod";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "10";
	$FormLength[$a]  	= "10";	

	$a++;
	$FormLabel[$a]   	= "Nama";
	$FormElement[$a] 	= "nama";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "10";
	$FormLength[$a]  	= "10";	
}

if ($cat == "Q") {
	$a++;
	$FormLabel[$a]   	= "Kod";
	$FormElement[$a] 	= "kod";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "10";
	$FormLength[$a]  	= "10";	

	$a++;
	$FormLabel[$a]   	= "Nama";
	$FormElement[$a] 	= "nama";
	$FormType[$a]	  	= "text";
	$FormData[$a]   	= "";
	$FormDataValue[$a]	= "";
	$FormCheck[$a]   	= array(CheckBlank);
	$FormSize[$a]    	= "10";
	$FormLength[$a]  	= "10";	
}*/

if ($action == "kemaskini") {
    $a++;
    $FormLabel[$a]      = "Tarikh Diwujudkan";
    $FormElement[$a]     = "createdDate";
    $FormType[$a]          = "hiddenDate";
    $FormData[$a]        = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";

    $a++;
    $FormLabel[$a]      = "Diwujudkan Oleh";
    $FormElement[$a]     = "createdBy";
    $FormType[$a]          = "hidden";
    $FormData[$a]        = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";

    $a++;
    $FormLabel[$a]      = "Tarikh Kemaskini";
    $FormElement[$a]     = "updatedDate";
    $FormType[$a]          = "hiddenDate";
    $FormData[$a]        = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";

    $a++;
    $FormLabel[$a]          = "Kemaskini Oleh";
    $FormElement[$a]     = "updatedBy";
    $FormType[$a]          = "hidden";
    $FormData[$a]        = "";
    $FormDataValue[$a]    = "";
    $FormCheck[$a]       = array();
    $FormSize[$a]        = "1";
    $FormLength[$a]      = "1";
}
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
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
        if ($d_Address <> "") $d_Address = '<pre>' . $d_Address . '</pre>';
        if ($parentID == "") $parentID = "0";
        $createdBy     = get_session("Cookie_userName");
        $createdDate = date("Y-m-d H:i:s");
        $updatedBy     = get_session("Cookie_userName");
        $updatedDate = date("Y-m-d H:i:s");
        $sSQL = "";
        switch (strtolower($SubmitForm)) {
            case "simpan":
                $sSQL    = "INSERT INTO general (" .
                    "category," .
                    "code," .
                    "name,";
                if ($sub <> "0") {
                    if ($cat == "B") {
                        $sSQL  .= "parentID," .
                            "b_Address," .
                            "b_ContactPerson," .
                            "b_ContactNo,";
                    }
                }
                if ($cat == "C") {
                    $sSQL  .= "parentID," .
                        //"c_Panel," . 
                        "c_Deduct," .
                        "c_Caj," .
                        "c_Period," .
                        "c_Maksimum," .
                        "c_Aktif," .
                        "c_gurrantor,";
                }
                if ($cat == "D") {
                    $sSQL  .= "d_Type," .
                        "d_Address," .
                        "d_Contact," .
                        "d_Phone,";
                }
                if ($cat == "G") {
                    $sSQL  .= "g_Price," .
                        "g_Minimum," .
                        "g_Maksimum,";
                }
                if ($cat == "J") {

                    $sSQL  .= "c_Panel," .
                        //   "j_Aktif,". 
                        "j_Pindah," .
                        "j_EarlyDeduct," .
                        "j_Percentage," .
                        "j_Amount," .
                        "c_master," .
                        "priority,";
                }
                if ($cat == "M") {
                    $sSQL  .= "m_Start," .
                        "m_End,";
                }
                if ($cat == "N") {
                    $sSQL  .= "n_Start," .
                        "n_End,";
                }

                $sSQL  .= "createdDate," .
                    "createdBy," .
                    "updatedDate," .
                    "updatedBy)" .
                    " VALUES (" .
                    tosql($cat, "Text") . "," .
                    tosql($code, "Text") . "," .
                    tosql($name, "Text") . ",";
                if ($sub <> "0") {
                    if ($cat == "B") {
                        $sSQL   .= tosql($parentID, "Number") . "," .
                            tosql($b_Address, "Text") . "," .
                            tosql($b_ContactPerson, "Text") . "," .
                            tosql($b_ContactNo, "Text") . ",";
                    }
                }
                if ($cat == "C") {
                    $sSQL   .=    tosql($parentID, "Text") . "," .
                        tosql($c_Deduct, "Number") . "," .
                        tosql($c_Caj, "Number") . "," .
                        tosql($c_Period, "Number") . "," .
                        tosql($c_Maksimum, "Number") . "," .
                        tosql($c_Aktif, "Number") . "," .
                        tosql($c_gurrantor, "Number") . ",";
                }
                if ($cat == "D") {
                    $sSQL   .= tosql($d_Type, "Text") . "," .
                        tosql($d_Address, "Text") . "," .
                        tosql($d_Contact, "Text") . "," .
                        tosql($d_Phone, "Text") . ",";
                }
                if ($cat == "G") {
                    $sSQL   .= tosql($g_Price, "Text") . "," .
                        tosql($g_Minimum, "Number") . "," .
                        tosql($g_Maksimum, "Number") . ",";
                }

                if ($cat == "J") {

                    $sSQL   .= tosql($c_Panel, "Text") . "," .
                        //   tosql($j_Aktif, "Number") . "," .
                        tosql($j_Pindah, "Number") . "," .
                        tosql($j_EarlyDeduct, "Number") . "," .
                        tosql($j_Percentage, "Number") . "," .
                        tosql($j_Amount, "Number") . "," .
                        tosql($c_master, "Number") . "," .
                        tosql($priority, "Number") . ",";
                }

                if ($cat == "M") {
                    $sSQL   .= tosql($m_Start, "Text") . "," .
                        tosql($m_End, "Text") . ",";
                }
                if ($cat == "N") {
                    $sSQL   .= tosql($n_Start, "Text") . "," .
                        tosql($n_End, "Text") . ",";
                }

                /*if ($cat == "P") {
					$sSQL   .=tosql($kod, "Text") . "," .
					          tosql($nama, "Text") . ",";
				}
				
				if ($cat == "Q") {
					$sSQL   .=tosql($kod, "Text") . "," .
					          tosql($nama, "Text") . ",";
				}*/

                $sSQL   .= tosql($createdDate, "Text") . "," .
                    tosql($createdBy, "Text") . "," .
                    tosql($updatedDate, "Text") . "," .
                    tosql($updatedBy, "Text") . ")";
                $msg = "Rekod berjaya ditambah !";
                break;
            case "kemaskini":
                $sWhere = "ID=" . tosql($pk, "Number");
                $sSQL    = "UPDATE general SET " .
                    "code=" . tosql($code, "Text") .
                    ",name=" . tosql($name, "Text");
                if ($cat == "B") {
                    $sSQL    .= ",b_Address=" . tosql($b_Address, "Text") .
                        ",b_ContactPerson=" . tosql($b_ContactPerson, "Text") .
                        ",b_ContactNo=" . tosql($b_ContactNo, "Text");
                }
                if ($cat == "C") {
                    $sSQL    .=     ",c_Deduct=" . tosql($c_Deduct, "Number") .
                        //",parentID=" . tosql($parentID, "Text") .
                        ",c_Caj=" . tosql($c_Caj, "Number") .
                        ",c_Period=" . tosql($c_Period, "Number") .
                        ",c_Maksimum=" . tosql($c_Maksimum, "Number") .
                        ",c_Aktif=" . tosql($c_Aktif, "Number") .
                        ",c_gurrantor=" . tosql($c_gurrantor, "Number");
                }
                if ($cat == "D") {
                    $sSQL    .= ",d_Type=" . tosql($d_Type, "Text") .
                        ",d_Address=" . tosql($d_Address, "Text") .
                        ",d_Contact=" . tosql($d_Contact, "Text") .
                        ",d_Phone=" . tosql($d_Phone, "Text");
                }
                if ($cat == "G") {
                    $sSQL    .= ",g_Price=" . tosql($g_Price, "Number") .
                        ",g_Minimum=" . tosql($g_Minimum, "Number") .
                        ",g_Maksimum=" . tosql($g_Maksimum, "Number");
                }
                if ($cat == "J") {

                    $sSQL    .= ",c_Panel=" . tosql($c_Panel, "Number") .
                        //    ",j_Aktif=" . tosql($j_Aktif, "Number") .
                        ",j_Pindah=" . tosql($j_Pindah, "Number") .
                        ",j_EarlyDeduct=" . tosql($j_EarlyDeduct, "Number") .
                        ",j_Percentage=" . tosql($j_Percentage, "Number") .
                        ",j_Amount=" . tosql($j_Amount, "Number") .
                        ",c_master=" . tosql($c_master, "Number") .
                        ",priority=" . tosql($priority, "Number");
                }
                if ($cat == "M") {
                    $sSQL    .= ",m_Start=" . tosql($m_Start, "Number") .
                        ",m_End=" . tosql($m_End, "Number");
                }
                if ($cat == "N") {
                    $sSQL    .= ",n_Start=" . tosql($n_Start, "Number") .
                        ",n_End=" . tosql($n_End, "Number");
                }

                $sSQL    .= ",updatedDate=" . tosql($updatedDate, "Text") .
                    ",updatedBy=" . tosql($updatedBy, "Text");
                $sSQL .= " where " . $sWhere;
                $msg = "Rekod bejaya dikemaskini !";
                break;
        }

        $rs = &$conn->Execute($sSQL);

        if ($cat = 'O') {
            $Section[] = $HTTP_POST_VARS["Section[]"];
            $sSQL = ' DELETE FROM codegroup WHERE groupNo = ' . tosql($code, "Text");
            $rs = &$conn->Execute($sSQL);
            for ($i = 0; $i < count($Section); $i++) {
                if ($Section[$i] <> "" and $Section[$i] <> "-1") {
                    $sSQL = "";
                    $sSQL    = ' INSERT INTO codegroup (' .
                        'groupNo,' .
                        'codeNo)' .
                        ' VALUES (' .
                        tosql($code, "Text") . ',' .
                        tosql($Section[$i], "Text") . ')';
                    $rs = &$conn->Execute($sSQL);
                }
            }
        }

        print '<script>
					alert ("' . $msg . '");
					opener.document.location = "' . $sActionFileName . '";
					window.close();
				</script>';
    }
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

if ($action == "kemaskini") {
    if ($pk <> "") {
        //--- Begin : query database for information ---------------------------------------------
        $sWhere = "ID = " . tosql($pk, "Number");
        $sWhere = " WHERE (" . $sWhere . ")";
        $sSQL = "SELECT * FROM general ";
        $sSQL = $sSQL . $sWhere;
        $rs = &$conn->Execute($sSQL);
        //--- End   : query database for information ---------------------------------------------

        if ($cat == 'O') {
            //--- Prepare deduct list
            $deductList = array();
            $deductVal  = array();
            $GetDeduct = ctGeneral("", "J");
            if ($GetDeduct->RowCount() <> 0) {
                while (!$GetDeduct->EOF) {
                    array_push($deductList, $GetDeduct->fields(code) . ' - ' . $GetDeduct->fields(name));
                    array_push($deductVal, $GetDeduct->fields(code));
                    $GetDeduct->MoveNext();
                }
            }

            $SectionVal = array();
            $sSQL = '';
            $sWhere = '';
            $sWhere .= 'groupNo = ' . tosql($rs->fields('code'), "Text");
            $sWhere = ' WHERE (' . $sWhere . ')';
            $sSQL = ' SELECT groupNo, codeNo FROM codegroup ';
            $sSQL = $sSQL . $sWhere;
            $rs2 = &$conn->Execute($sSQL);
            if ($rs2->RowCount() <> 0) {
                while (!$rs2->EOF) {
                    array_push($SectionVal, $rs2->fields('codeNo'));
                    $rs2->MoveNext();
                }
            }
        }
    }
}

if ($action == "simpan") {
    print '<div class="table-responsive"><form name="MyForm" action=' . $sFileName . '?mn=903&selCode=' . $cat . '&action=' . $action . ' method=post>';
} else {
    if ($cat == 'O') {
        print '<form name="MyForm" action=' . $sFileName . '?action=' . $action . '&mn=903&selCode=' . $cat . '&pk=' . $pk . ' method=post onSubmit="Javascript:selectAllSection();">';
    } else {
        print '<form name="MyForm" action=' . $sFileName . '?action=' . $action . '&mn=903&selCode=' . $cat . '&pk=' . $pk . ' method=post>';
    }
}
print '
<center>
<table border=0 cellpadding=3 cellspacing=1 width="95%" align="center" class="table table-sm table-striped" style="font-size:10pt">
	<tr class="table-primary">
		<td colspan="2">';

if ($action == "simpan") print '<h6 class="card-subtitle">Kemasukan ' . $title;
else print '<h6 class="card-subtitle">Kemaskini ' . $title . ' : ' . tohtml($rs->fields(name));
print '</h6></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
    if ($action == "kemaskini") {
        if ($cat == "B") {
            if ($sub <> "0") {
                if ($i == 7) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
            } else {
                if ($i == 6) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
            }
        }
        if ($cat == "C") {
            if ($i == 10) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
        }
        if ($cat == "D") {
            //			if ($i == 4) print '<tr><td class=Header colspan=2>Audit Informasi :</td></tr>';
            if ($i == 7) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
        }
        if ($cat == "G") {
            if ($i == 6) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
        }
        if ($cat == "J") {
            if ($i == 10) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
        }
        if ($cat == "M") {
            if ($i == 5) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
        }
        if ($cat == "N") {
            if ($i == 5) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
        }
        if ($cat <> "B" and $cat <> "C" and $cat <> "D" and $cat <> "G" and $cat <> "M" and $cat <> "N" and $cat <> "J") {
            if ($i == 3) print '<tr class="table-primary"><td colspan=2><h6 class="card-subtitle">Audit Informasi</h6></td></tr>';
        }
    }
    print '<tr valign=top><td class=Data align=right>' . $FormLabel[$i] . '</td>';
    if (in_array($FormElement[$i], $strErrMsg))
        print '<td class=errdata>';
    else
        print '<td class=Data>';
    //--- Begin : Call function FormEntry ---------------------------------------------------------  
    if ($action == "kemaskini") {
        $strFormValue = tohtml($rs->fields($FormElement[$i]));
        if ($FormType[$i] == 'textarea') {
            $strFormValue = str_replace("<pre>", "", $rs->fields($FormElement[$i]));
            $strFormValue = str_replace("</pre>", "", $strFormValue);
        }
        if ($cat == "J") {
            //if ($i == 4) $strFormValue = dlookup("codegroup", "groupNo", "codeNo=" . tosql($rs->fields('code'), "Text"));
        }
    } else {
        $strFormValue = $$FormElement[$i];
        if ($cat == "B") {
            if ($i == 3) $strFormValue = $sub;
        }
        if ($cat == "C") {
            if ($i == 3) $strFormValue = $sub;
        }
        if ($cat == "J") {
            //if ($i == 4) $strFormValue = '';
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
        $FormLength[$i],
        $FormStyle[$i]
    );

    //--- End   : Call function FormEntry ---------------------------------------------------------  
    print '&nbsp;</td></tr>';
    if ($cat == 'O') {
        if ($i == 2) {
            print '
    		<tr><td class=Header colspan=2>Pilihan Kod Potongan :</td></tr>
    		<tr valign=top><td class=Data colspan="2">
    			<table class="table table-bordered table-striped table-sm" style="font-size: 8pt;" border="0" cellspacing="1" cellpadding="3" width="95%" align="center">
    			    <tr valign="top">
    			    	<td class="data">Senarai Kod Potongan<br>
    					<select name="nonSection[]" multiple size="15">';
            if (count($deductList) ==  0) {
                print     '	<option value="">-None-</option>';
            } else {
                for ($j = 0; $j < count($deductList); $j++) {
                    if (!in_array($deductVal[$j], $SectionVal))
                        print     '	<option value="' . $deductVal[$j] . '">' . $deductList[$j] . '</option>';
                }
            }
            print    ' 	</select>
    					</td>
    					<td class="data" valign="middle">
    					<input type="hidden" name="hidMoveFlag" value="0">
    					<input type="hidden" name="hidUpdateFlag" value="0">
    		        	<input type="button" value=">>" onClick="document.MyForm.hidMoveFlag.value=1; addSection()" class=textFont><br>
    		        	<input type="button" value="<<" onClick="document.MyForm.hidMoveFlag.value=1; removeSection()" class=textFont>
    			        </td>				
    					<td valign="top" class="data">Kod Potongan Pilihan<br>
    			        <select name="Section[]" multiple size="15">';
            if (count($SectionVal) == 0) {
                print     '	<option value="-1">-None-</option>';
            } else {
                for ($j = 0; $j < count($SectionVal); $j++) {
                    print     '<option value="' . $SectionVal[$j] . '">' .
                        $deductList[array_search($SectionVal[$j], $deductVal)] .
                        '</option>';
                }
            }
            print    '  	</select>
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
	<input type="hidden" name="cat" value="' . $cat . '">
	<input type="hidden" name="sub" value="' . $sub . '">
	<input type=Submit name=SubmitForm class="btn  btn-primary" value="Simpan">
	<!--input type=Reset name=ResetForm class="btn  btn-secondary" value="Isi semula"-->
	';
} else {
    if ($cat == 'C' && $sub == '0') {
        print '&nbsp;';
    } else {
        print '
	<input type=hidden name=ID class="textFont" value=' . $pk . '>
	<input type="hidden" name="cat" value="' . $cat . '">
	<input type="hidden" name="sub" value="' . $sub . '">
	<input type=Submit name=SubmitForm class="btn btn-primary btn-md waves-effect waves-light" value="Kemaskini">';
    }
}
print '		</td>
		</tr>
</table>
</form></div>';
//print $cat . ' - ' . $sub;
include("footer.php");