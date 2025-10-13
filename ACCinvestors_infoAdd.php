<?php
/*********************************************************************************
*          Project		:	KPF2 MODUL PELABURAN
*          Filename		: 	ACCinvestors_info.php
*          Date 		: 	17/10/2023
*********************************************************************************/

include("header.php");	
include("koperasiQry.php");	
include("forms.php");

date_default_timezone_set("Asia/Jakarta");

$sFileName				= "?vw=ACCinvestors_infoAdd&mn=920&pk=$pk";
$sActionFileName		= "?vw=ACCinvestors_detail&mn=920&pk=$pk";

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCinvestors_detail&mn=920&pk='.$pk.'">SENARAI</a><b>'.'&nbsp;>&nbsp;Maklumat Projek</b>';

$title     		= "MAKLUMAT PROJEK"; 

if(!isset($doc)) 
$doc = dlookup("investors", "doc", "compID=" . tosql($pk, "Text"));


//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
$strErrMsg = Array();

$a=1;
$FormLabel[$a]   	= "Nama Projek";
$FormElement[$a] 	= "nameproject";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Lokasi";
$FormElement[$a] 	= "location";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "Keluasan Tanah";
$FormElement[$a] 	= "area";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Tarikh Kelulusan Mesyuarat (ALK)";
$FormElement[$a] 	= "lulusDate";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Tarikh Mula (Perjanjian/Penubuhan)";
$FormElement[$a] 	= "startDate";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Tarikh Akhir (Perjanjian)";
$FormElement[$a] 	= "endDate";
$FormType[$a]	  	= "date";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Tempoh Perjanjian (Bulan)";
$FormElement[$a] 	= "period";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Nilai Pelaburan (RP)";
$FormElement[$a] 	= "amount";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->



$sqlIV = "SELECT * FROM generalacc WHERE category = 'AK' AND ID = '$pk' ";
$GetIv = &$conn->Execute($sqlIV);

$sqlIV2 = "SELECT * FROM generalacc a, investors b WHERE a.ID = b.compID AND a.category = 'AK' AND a.ID = '$pk' ";
$GetIv2 = &$conn->Execute($sqlIV2);


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

	$lulusDate = substr($lulusDate,6,4).'-'.substr($lulusDate,3,2).'-'.substr($lulusDate,0,2);
	$startDate = substr($startDate,6,4).'-'.substr($startDate,3,2).'-'.substr($startDate,0,2);
	$endDate = substr($endDate,6,4).'-'.substr($endDate,3,2).'-'.substr($endDate,0,2);


	if (count($strErrMsg) == "0") {

		$updatedDate = date("Y-m-d H:i:s");
		$compID = $pk;

		
		$sSQL = "INSERT INTO investors ( " .
				"compID," .
				"nameproject," .
				"location," .
				"area," .
				"lulusDate," .
				"startDate," .
				"endDate," .
				"period," .
				"amount," .				
				"updatedDate )". 
				"VALUES (" . 
				tosql($pk, "Text") . "," .
				tosql($nameproject, "Text") . "," .
				tosql($location, "Text") . "," .
				tosql($area, "Text") . "," .
				tosql($lulusDate, "Text") . "," .
				tosql($startDate, "Text") . "," .
				tosql($endDate, "Text") . "," .
				tosql($period, "Text") . "," .
				tosql($amount, "Text") . "," .			
				tosql($updatedDate, "Text") . ")" ;

		$rs = &$conn->Execute($sSQL);
		
	}

	print '	<script>
				alert ("Maklumat Berjaya Disimpan");
				window.location = "'.$sActionFileName.'";
			</script>';

}

print '
<div class="maroon" align="left">'.$strHeaderTitle.'</div>
<div style="width: 100%; text-align:left">
<div>&nbsp;</div>
<form name="MyForm" action='.$sFileName.' method=post>
<h5 class="card-title"></i>&nbsp;'.strtoupper($title).'</h5>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {

    $cnt = $i % 2;
    if ($i == 1) print '<div class="card-header mb-3">MAKLUMAT PROJEK '.$ID.'</div>';
    // print '<label class="col-md-2 col-form-label">'.$FormLabel[$i].'</label>';

    if ($cnt == 1) print '<div class="m-3 mb-4 row">';
	print '<label class="col-md-2 col-form-label">'.$FormLabel[$i];
	// if (!($i == 6 OR $i == 26 OR $i == 32 )) print ':';
	print ' </label>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<div class="col-md-4 bg-danger">';
	else
	  print '<div class="col-md-4">';
    

    //--- Begin : Call function FormEntry ---------------------------------------------------------  
	FormEntry($FormLabel[$i], 
			  $FormElement[$i], 
			  $FormType[$i],
			  $strFormValue,
			  $FormData[$i],
			  $FormDataValue[$i],
			  $FormSize[$i],
			  $FormLength[$i]);

	//--- End   : Call function FormEntry ---------------------------------------------------------  
    print'</div>';
	if ($cnt == 0) print '</div>';
}

	print '
	<div class="mt-3 mb-3 row">
    	<center>
        	<input type="Submit" class="btn btn-primary w-md waves-effect waves-light" name="SubmitForm" value="SIMPAN">
    	</center>
	</div>'; 


print'
</div>
</form>';
include("footer.php");	
