<?php
/*********************************************************************************
*          Project		:	KPF2 MODUL PELABURAN
*          Filename		: 	ACCinvestors_info.php
*          Date 		: 	17/10/2023
*********************************************************************************/

include("header.php");	
include("koperasiQry.php");	
include("forms.php");

$IDName = get_session("Cookie_userName");
date_default_timezone_set("Asia/Jakarta");

$sFileName				= "?vw=ACCinvestors_info&mn=920&pk=$pk&ID=$ID";
$sActionFileName		= "?vw=ACCinvestors_info&mn=920&pk=$pk&ID=$ID";
$sFileNameDel			= "?vw=ACCinvestors_info&mn=920&pk=$pk&ID=$ID&code=1";

$code = $_REQUEST['code'];

if ($code == 1){
	$sSQLdel = "DELETE from investorsrate Where ID =".$IDtype."";
	$rsdel = &$conn->Execute($sSQLdel);
	}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCinvestors_detail&mn=920&pk='.$pk.'">SENARAI</a><b>'.'&nbsp;>&nbsp;Maklumat Projek</b>';

$title     		= "MAKLUMAT PROJEK"; 
$titleRate     	= "PERATUSAN TARIKH PERJANJIAN"; 

if(!isset($doc1)) 
$doc1 = dlookup("investors", "doc1", "ID=" . tosql($ID, "Text"));
if(!isset($doc2)) 
$doc2 = dlookup("investors", "doc2", "ID=" . tosql($ID, "Text"));
if(!isset($doc3)) 
$doc3 = dlookup("investors", "doc3", "ID=" . tosql($ID, "Text"));

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
$FormSize[$a]    	= "20";
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

$a++;
$FormLabel[$a]   	= "PIC";
$FormElement[$a] 	= "picharge";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Pembuka Akaun (RP)";
$FormElement[$a] 	= "openbalpro";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "ALK Selian 1";
$FormElement[$a] 	= "alkselia";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "ALK Selian 2";
$FormElement[$a] 	= "alkselia2";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "ALK Selian 3";
$FormElement[$a] 	= "alkselia3";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

$sqlIV2 = "SELECT * FROM generalacc a, investors b WHERE a.ID = b.compID AND a.category = 'AK' AND a.ID = '$pk' ";
$GetIv2 = &$conn->Execute($sqlIV2);

$sqlIV3 = "SELECT * FROM investors WHERE compID = '$pk' AND ID= '$ID'";
$GetIv3 = &$conn->Execute($sqlIV3);

print '
<div class="maroon" align="left">'.$strHeaderTitle.'</div>
<div style="width: 100%; text-align:left">
<div>&nbsp;</div>
<form name="MyForm" action='.$sActionFileName.' method=post>
<h5 class="card-title"></i>&nbsp;'.strtoupper($title).'&nbsp;&nbsp;<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "window.print()"></h5>';

print '';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {

    $cnt = $i % 2;
    if ($i == 1) print '<div class="card-header mt-3mb-3">MAKLUMAT PROJEK</div>';

    if ($cnt == 1) print '<div class="m-3 mb-4 row">';
	print '<label class="col-md-2 col-form-label">'.$FormLabel[$i];
	print ' </label>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<div class="col-md-4 bg-danger">';
	else
	  print '<div class="col-md-4">';
    

    //--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($GetIv3->fields($FormElement[$i]) == "") {
		$strFormValue = tohtml($GetIv3->fields($FormElement[$i])); 
	}
	else {
		if($FormElement[$i] == "amount" || ($FormElement[$i] == "openbalpro")){
			$strFormValue = number_format($GetIv3->fields($FormElement[$i]), 2, '.', ',');
		} else
		$strFormValue = tohtml($GetIv3->fields($FormElement[$i])); 
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
    print'</div>';
	if ($cnt == 0) print '</div>';
}
print '
	<div class="mt-3 mb-3 row">
    	<center>
        	<button type="submit" class="btn btn-primary w-md waves-effect waves-light" name="action" value="Kemaskini" onclick="window.location.href=\''.$sActionFileName.'&action=Kemaskini\';">KEMASKINI</button>
    	</center>
	</div>';

print'
</form>';

if ($action == "Kemaskini") {

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
		$amount = str_replace(',', '', $amount);
		$openbalpro = str_replace(',', '', $openbalpro);

		$sSQL = "";
		$sWhere = "";
		$sWhere = "ID= '".$ID."'";
		$sWhere = " WHERE (".$sWhere.")";

		$sSQL = "UPDATE investors SET ".
				"nameproject='" .$nameproject . "',".
				"location='" .$location . "',".
				"area='" .$area . "',".
				"lulusDate='" .$lulusDate . "',".
				"startDate='" .$startDate . "',".
				"endDate='" .$endDate . "',".
				"period='" .$period . "',".
				"amount='" .$amount . "',".	
				"picharge='" .$picharge . "',".	
				"alkselia='" .$alkselia . "',".	
				"alkselia2='" .$alkselia2 . "',".	
				"alkselia3='" .$alkselia3 . "',".	
				"openbalpro='" .$openbalpro . "',".		
				"updatedDate='" . $updatedDate . "'";
		$sSQL = $sSQL . $sWhere;

		$rs = &$conn->Execute($sSQL);

	}

	print '<script>
				alert ("Maklumat telah dikemaskini");
				window.location.href = "'.$sActionFileName.'";
			</script>';
}

print '<div class="card-header mt-3 mb-3">PERATUSAN TARIKH PERJANJIAN</div>';

	print '
	<form name="addRate" method="post" action='.$sActionFileName .'>
		<table class="table table-sm table-striped">
			<tr valign="top" class="table-secondary">
				<td><b>Tarikh Mula</b></td>
				<td><b>Tarikh Tamat</b></td>
				<td><b>Kadar</b></td>
				<td><b>Terma Bayaran</b></td>
				<td><div align="center"><b></b></div></td>
			</tr>';
	
	print '
			<tr>
			<td>
				<div class="input-group" id="dateFrom">
				<input type="text" name="dateFrom" class="form-control-sm" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#dateFrom"
					data-date-autoclose="true" value="'.$dateFrom.'">
				<div class="input-group-append">
					<span class="input-group-text"><i
							class="mdi mdi-calendar"></i></span>
				</div>
				</div>
			</td>
			<td>
				<div class="input-group" id="dateTo">
				<input type="text" name="dateTo" class="form-control-sm" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#dateTo"
					data-date-autoclose="true" value="'.$dateTo.'">
				<div class="input-group-append">
					<span class="input-group-text"><i
							class="mdi mdi-calendar"></i></span>
				</div>
				</div>
			</td>
			<td><input class="form-control" type="text" name="rate" value="'.$rate.'" ></td>
			<td>'.selectTermPayment($termPayment,'termPayment').'</td>';
			print '<td><button type="submit" name="action" class="btn btn-primary btn-sm w-md waves-effect waves-light" value="Simpan" onclick="window.location.href=\''.$sActionFileName.'\';">SIMPAN</button></td>';
		print '     </tr>';
	
	print ' </table>
	</div>';

	if ($action == "Simpan") {

		$createdBy 	= get_session("Cookie_userName");
		$createdDate = date("Y-m-d H:i:s");
		$dateFrom = saveDateDb($dateFrom);
		$dateTo = saveDateDb($dateTo);
		$sSQLrate = "";

		$sSQLrate	= "INSERT INTO investorsrate (" . 
					"dateFrom, " .
					"dateTo, " .
					"rate, ".
					"termPayment, ".
					"projectID, ".
					"createdDate, " .
					"createdBy) " . 
	
			" VALUES (" . 
	
					"'". $dateFrom . "', ".
					"'". $dateTo . "', ".
					"'". $rate . "', ".
					"'". $termPayment . "', ".
					"'". $ID . "', ".
					"'". $createdDate . "', ".
					"'". $createdBy . "')";
	
		$rsRate = &$conn->Execute($sSQLrate);
	
		print '<script>
			alert ("Maklumat telah disimpan");
			window.location.href = "'.$sActionFileName.'";
		</script>';
	}
	print'	</form>';

	$sSQL2 = "SELECT * 
		  FROM investorsrate 
		  WHERE projectID='".$ID."'
		  ORDER BY createdDate asc";

	$rs2 =&$conn->Execute($sSQL2); 

print '&nbsp;';
print '
<div class="table-responsive">
<form id="Edittrans" name="Edittrans" method="post" action='.$sFileName .'>
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="'.$StartRec.'">
<input type="hidden" name="by" value="'.$by.'">

  <table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
    <tr valign="top" class="table-primary">
      <td width="2%" nowrap rowspan="1" ><b>Bil</b></td>
      <td width="10%" nowrap><b>Tarikh Mula</b></td>
      <td width="10%" nowrap><b>Tarikh Tamat</b></td>
      <td width="10%" nowrap><b>Kadar</b> </td>
      <td width="10%" nowrap><b>Terma Bayaran</b> </td>
      <td colspan="1" nowrap><div align="center"><b>Hapus</b></div></td>
    </tr>';
   if ($rs2->RowCount() <> 0) {
   $count =1;
   while(!$rs2->EOF) {
 
	print '
	<tr>
		<td class="Data" >&nbsp;' . $count . '</td>
		<td class="Data" width="5%">' . ($ID == $rs2->fields(projectID) ? toDate("d/m/Y",$rs2->fields(dateFrom)) : '&nbsp;' . toDate("d/m/Y",$rs2->fields(dateFrom))) . '</td>
		<td class="Data" width="5%">' . ($ID == $rs2->fields(projectID) ? toDate("d/m/Y",$rs2->fields(dateTo)) : '&nbsp;' . toDate("d/m/Y",$rs2->fields(dateTo))) . '</td>
		<td class="Data" width="5%">' . ($ID == $rs2->fields(projectID) ? $rs2->fields(rate) : '&nbsp;' . $rs2->fields(rate)) . '</td>
		<td class="Data" width="5%">';
		if ($ID == $rs2->fields(projectID)) {
			if (dlookup("generalacc", "name", "ID=".$rs2->fields(termPayment))) {
				print(dlookup("generalacc", "name", "ID=".$rs2->fields(termPayment)));
			} else {
				print('&nbsp;' . $rs2->fields(termPayment));
			}
		} else {
			print('&nbsp;' . $rs2->fields(termPayment));
		}
		print '</td>';
		if (($IDName == 'admin') OR ($IDName == 'superadmin')) {
		print '
		<td class="Data" align="center" width="5%">&nbsp;<a href="' . $sFileNameDel . '&IDtype=' . $rs2->fields(ID) . '&ID=' . $ID . '&code=1" title="Hapus" onClick="if(!confirm(\'Adakah ada pasti untuk hapus file ini?\')) {return false} else {window.Edittrans.submit();};"><img src="b_drop.png"></a></td>';
	}
	print '
	</tr>';
	
	  $count++;
	  $rs2->MoveNext();
}
	}else {
					print '
					<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod </b></td>
					</tr>';
				}
	
print'
  </table>
  <p>&nbsp;</p>
</form>
</div>';

print'<div class="bg-soft-primary mt-3" style="height: 40px; padding: 10px;">MUAT NAIK DOKUMEN LAIN</div>';
print '<div>&nbsp</div>';
print'
<div>
	<input type="button" class="btn btn-secondary waves-effect" name="GetPicture" value="DOKUMEN 1"  onclick= "Javascript:(window.location.href=\'?vw=uploadwindoc1&mn=920&pk='.$pk.'&ID='.$ID.'\')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		
	if ($doc1) {
		print '<button type=button class="btn btn-outline-danger" onClick=window.open(\'upload_doc/'.$doc1.'\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Dokumen 1</button>';
print '
</div>';
}

print '<div>&nbsp</div>';
print'
<div>
	<input type="button" class="btn btn-secondary waves-effect" name="GetPicture" value="DOKUMEN 2"  onclick= "Javascript:(window.location.href=\'?vw=uploadwindoc2&mn=920&pk='.$pk.'&ID='.$ID.'\')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		
	if ($doc2) {
		print '<button type=button class="btn btn-outline-danger" onClick=window.open(\'upload_doc/'.$doc2.'\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Dokumen 2</button>';
print '
</div>';
}

print '<div>&nbsp</div>';
print'
<div>
	<input type="button" class="btn btn-secondary waves-effect" name="GetPicture" value="DOKUMEN 3"  onclick= "Javascript:(window.location.href=\'?vw=uploadwindoc3&mn=920&pk='.$pk.'&ID='.$ID.'\')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		
	if ($doc3) {
		print '<button type=button class="btn btn-outline-danger" onClick=window.open(\'upload_doc/'.$doc3.'\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> <i class="far fa-file-pdf text-danger"></i> Paparan Dokumen 3</button>';
print '
</div>';
}

print'
</div>';

print '
<script language="JavaScript">
	function print_(url) {
		window.open(url,"pop","top=100, left=100, width=600, height=400, scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");
	}

</script>';

include("footer.php");