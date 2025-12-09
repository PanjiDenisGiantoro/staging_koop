<?php
/*********************************************************************************
*          Project		:	KPF2 MODUL PELABURAN
*          Filename		: 	ACCinvestors_detail.php
*          Date 		: 	21/09/2023
*********************************************************************************/
if (!isset($StartRec))	$StartRec= 1; 
//tukar default listing view from 10 to 50 /
if (!isset($pg))		$pg= 50;

include("header.php");	
include("koperasiQry.php");	
include("forms.php");

date_default_timezone_set("Asia/Jakarta");

$sFileName		= "?vw=ACCinvestors_detail&mn=$mn&pk=$pk";

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCinvestors&mn=920">SENARAI</a><b>'.'&nbsp;>&nbsp;Projek Pelaburan</b>';

$title     		= "MAKLUMAT PROJEK PELABURAN"; 

if(!isset($doc1)) 
$doc1 = dlookup("investors", "doc1", "compID=" . tosql($pk, "Text"));
if(!isset($doc2)) 
$doc2 = dlookup("investors", "doc2", "compID=" . tosql($pk, "Text"));
if(!isset($doc3)) 
$doc3 = dlookup("investors", "doc3", "compID=" . tosql($pk, "Text"));


//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
$strErrMsg = Array();

$a = 1;
$FormLabel[$a]   	= "Nama Serikat";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Alamat Syarikat";
$FormElement[$a] 	= "b_Daddress";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Nombor Telefon";
$FormElement[$a] 	= "b_contact";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Staf Bertanggungjawab";
$FormElement[$a] 	= "b_pic";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Emel";
$FormElement[$a] 	= "b_email";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "Nombor Pendaftaran Syarikat";
$FormElement[$a] 	= "b_busreg";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "50";	

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";	

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";	

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

$sqlIV = "SELECT * FROM generalacc WHERE category = 'AK' AND ID = '$pk' ";
$GetIv = &$conn->Execute($sqlIV);

$sqlIV2 = "SELECT * FROM generalacc a, investors b WHERE a.ID = b.compID AND a.category = 'AK' AND a.ID = '$pk' ";
$GetIv2 = &$conn->Execute($sqlIV2);

$sqlIV3 = "SELECT * FROM investors WHERE compID = '$pk' ORDER BY ID ASC";
$GetIv3 = &$conn->Execute($sqlIV3);

$ID = $GetIv3->fields(ID);

$GetIv3->Move($StartRec-1);

//--- Begin	: deletion based on	checked	box	-------------------------------------------------------
if ($action == "delete") {	
	$sWhere = "";
	for ($i = 0; $i < count($id); $i++) {
		// $ID = dlookup("investors", "compID", "ID=" . tosql($pk, "Text"));
		$sWhere = "ID=" . tosql($id[$i], "Text");
		$sSQL = "DELETE FROM investors WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
		
	}
	print '<script>
		window.location = "?vw=ACCinvestors_detail&mn='.$mn.'&pk='.$pk.'";
		</script>';
}
//--- End	: deletion based on	checked	box	-------------------------------------------------------

print '
<div class="maroon" align="left">'.$strHeaderTitle.'</div>
<div style="width: 100%; text-align:left">
<div>&nbsp;</div>
<input type="hidden" name="ID" value="'.$ID.'">
<form name="MyForm" action='.$sFileName.' method=post>
<input type="hidden" name="action">
<h5 class="card-title"></i>&nbsp;'.strtoupper($title).'</h5>';
//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {

    $cnt = $i % 2;
    if ($i == 1) print '<div class="card-header mb-3">PROFIL SYARIKAT</div>';
    
    if ($cnt == 1) print '<div class="m-3 mb-4 row">';
	print '<label class="col-md-2 col-form-label">'.$FormLabel[$i];
	print ' </label>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<div class="col-md-4 bg-danger">';
	else
	  print '<div class="col-md-4">';
    

    //--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($GetIv->fields($FormElement[$i]) == "") {
		$strFormValue = tohtml($GetIv->fields($FormElement[$i])); 
	}
	else {
		$strFormValue = tohtml($GetIv->fields($FormElement[$i])); 
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

print '<div class="card-header mb-3">SENARAI PROJEK&nbsp;&nbsp;<input type="button" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCinvestors_infoAdd&mn='.$mn.'&pk='.$pk.'&ID=\';">
			&nbsp;&nbsp;<input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');"></div>';   
	if ($GetIv3->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;
		print 
	    '<tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap align="center"><b>Bil</b></td>
						<td nowrap align="left"><b>Nama Projek</b></td>
						<td nowrap align="center"><b>Tanggal Mula</b></td>
						<td nowrap align="center"><b>Tanggal Akhir</b></td>
						<td nowrap align="right"><b>Nilai Pelaburan (RP)</b></td>
						<td nowrap align="left"><b>Person In Charge</b></td>
					</tr>';	
		while (!$GetIv3->EOF && $cnt <= $pg) {
		$nameproject = $GetIv3->fields(nameproject);
		$startDate = toDate("d/m/y",$GetIv3->fields(startDate));
		$endDate = toDate("d/m/y",$GetIv3->fields(endDate));
		$amount = number_format($GetIv3->fields(amount), 2, '.', ',');
		$pic = $GetIv3->fields(picharge);

			print '<tr>
						<td class="Data" align="center">' . $bil . '</td>					
						<td class="Data" align="left">
						<input type="checkbox" class="form-check-input" name="id[]"	value="'.tohtml($GetIv3->fields('ID')).'">
						<a href="?vw=ACCinvestors_info&mn=920&pk='.$pk.'&ID='.$GetIv3->fields('ID').'">'.$nameproject.'</a>
						<td class="Data" align="center">'.$startDate.'</td>
						<td class="Data" align="center">'.$endDate.'</td>
						<td class="Data" align="right">'.$amount.'</td>
						<td class="Data" align="left">'.$pic.'</td>
						</td>	
					</tr>';
				$cnt++;
				$bil++;
			$GetIv3->MoveNext();
		}
		$GetIv3->Close();

		print ' </table>
			</td>
		</tr>		
		';
	} 
print'
</div>
</form>';

include("footer.php");	

print '
<script language="JavaScript">
	var allChecked=false;
	function ITRViewSelectAll() {
	    e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}

	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="id[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak dihapuskan.\');
	        } else {
	          if(confirm(count + \' rekod hendak dihapuskan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
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
				window.open(\'transStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');					
			}
		}
	}
		
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '&yy='.$yy.'&mm='.$mm.'&code='.$code.'&filter='.$filter.'&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';

?>