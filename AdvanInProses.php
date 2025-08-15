<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	AdvanInProses.php
 *          Date 		: 	12/09/2006//
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 9999;
if (!isset($q))			$q = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$sFileName = '?vw=AdvanInProses&mn=12';
$sFileRef  = '?vw=AdvanInProses&mn=12';
$title     = "Senarai Advance Payment Dalam Proses";

//--- Begin	: deletion based on	checked	box	-------------------------------------------------------
if ($action	== "delete") {
	$sWhere	= "";
	for	($i	= 0; $i	< count($pk); $i++)	{
		$CheckLoan = ctLoan("",$pk[$i]);
		if ($CheckLoan->RowCount() == 1) {
			//$CheckLoan->fields(status)
			if ($CheckLoan->fields(status) < 3) {
				$sWhere	= "loanID="	. tosql($pk[$i], "Number");
				$sSQL =	"DELETE	FROM loans WHERE " . $sWhere;
				$rs	= &$conn->Execute($sSQL);
				// biaya doc
				$sSQL =	"DELETE	FROM loandocs WHERE	" .	$sWhere;
				$rs	= &$conn->Execute($sSQL);


			} else {
				print '<script>alert("Hanya permohonan belum siap proses boleh dihapus!");</script>';
			}
		}
	}
}
//--- End	: deletion based on	checked	box	-------------------------------------------------------


$sSQL = "SELECT	a.*,b.* FROM loans a,general b
		 WHERE a.loanType = b.ID AND 
		 a.status IN (0,1,2) AND a.statusL = 1 AND a.userID = '".get_session('Cookie_userID')."' ORDER BY a.applyDate DESC";
$GetLoan = &$conn->Execute($sSQL);
$GetLoan->Move($StartRec-1);
$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec/$pg);

print '
<form name="MyForm" action='.$sFileName.' method="post">
<input type="hidden" name="action">
<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr><td><h5 class="card-title"><i class="mdi mdi-progress-alert"></i>&nbsp;'.strtoupper($title).'</h5></td></tr>';

print '
<tr	valign="top">
		<td align="left">
			
					<div class="textFont" align ="left">
					<input type="button" class="btn btn-primary btn-sm w-sm waves-effect waves-light" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">
					</div>
					<hr class="mb-2">		
		</td>
	</tr>';

	if ($GetLoan->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;

/*<td nowrap align="center">&nbsp;Jadual Pembiayaan</td>	*/

		print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">                             
					<tr class="table-primary">
						<td nowrap height="20"></td>
						<td nowrap><b>Nombor Rujukan Pembiayaan</b></td>
						<td nowrap align="right"><b>Jumlah Pinjaman (RM)</b></td>					
						<td nowrap align="center"><b>Tempoh (Bulan)</b></td>
						<td nowrap align="center"><b>Status</b></td>									
						<td nowrap align="center"><b>Tarikh Mohon</b></td>	
						<td nowrap>&nbsp;</td>			
					</tr>';
		while (!$GetLoan->EOF && $cnt <= $pg) {
			$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields(userID), "Text"));
			$status = $GetLoan->fields(status);
			$sijilID = dlookup("komoditi", "loanID", "loanID=" . tosql($GetLoan->fields(loanID), "Text"));
			$colorStatus = "Data";
			if ($status == 3) $colorStatus = "greenText";
			if ($status == 4 || $status == 5) $colorStatus = "redText";
			if($GetLoan->fields(isApproved))
				$chk = '<i class="mdi mdi-check text-primary"></i>';
			else 
				$chk = '<i class="mdi mdi-close text-danger"></i>';
			
			//--------------
			$loanType				= $GetLoan->fields('loanType');
			$codegroup				= dlookup("general", "parentID", "ID=" . $loanType);
			$prove = $GetLoan->fields(isApproved);
			$stat_agree = $GetLoan->fields(stat_agree);
			$table  = $GetLoan->fields(loanID);			

			$colorStatus = "text-success";
			if ($status == 1) $colorStatus = "text-info"; //$colorStatus = "greenText";
			if ($status == 2) $colorStatus = "text-info";			
					
		print ' <tr>
				<td class="Data" align="center" height="20">' . $bil . '</td>
				<td class="Data" align="left"><input type="checkbox" class="form-check-input" name="pk[]"	value="'.tohtml($GetLoan->fields(loanID)).'">&nbsp;'
				.$GetLoan->fields(loanNo).' - '.dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"))
				.'</td>
				<td class="Data" align="right">'.number_format($GetLoan->fields('loanAmt'), 2, '.', ',').'</td>
				<td class="Data" align="center">'.$GetLoan->fields('loanPeriod').'</td>
				<td class="Data" align="center"><font class="'.$colorStatus.'">'.$biayaList[$status].'&nbsp;';		
				if ($status == 0) {	
				print '<span class="mdi mdi-information-outline" rel="tooltip" title="Permohonan anda dalam proses dan akan disemak kemudian."></span>';
				}
				if ($status == 1) {	
					print '<span class="mdi mdi-information-outline" rel="tooltip" title="Permohonan anda telah diproses dan sedang disediakan."></span>';
				}
				if ($status == 2) {	
					print '<span class="mdi mdi-information-outline" rel="tooltip" title="Permohonan anda telah disediakan dan sedang disemak."></span>';
				}
				
				if($codegroup <> 1638){
					$table  = "loanJadual_user.php?id=".$GetLoan->fields(loanID);
				}else{
					$table = "loanJadual78NEW_user.php?type=vehicle&page=view&id=".$GetLoan->fields(loanID);
				}
		/*<td class="Data" align="center">
					&nbsp;<input type="button" value="Cetak Jadual" onClick=window.open("'.$table.'","pop","top=50,left=50,width=700,height=450scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>'
						.'<a href="'.$table.'">	*/		
print '</font>

					
<td class="Data" align="center">'.toDate("d/m/Y",$GetLoan->fields(applyDate)).'</td>

	<!--td class="Data" align="center">&nbsp;';
						
	if ($GetLoan->fields(startPymtDate) <> "") {
	print '<input type=button value="Lihat Jadual" class="but" onClick=window.open("tawaranSah2tawarruq.php?id='.$GetLoan->fields(loanID).'","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
	}else
	{ print 'Belum Diluluskan'; }
				print '	
					
				</td-->
					</tr>';
				$cnt++;
				$bil++;
			$GetLoan->MoveNext();
		}
		print ' </table>
			</td>
		</tr>		
		<tr>
			<td>';
				if ($TotalRec > $pg) {
					print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
					if ($TotalRec % $pg == 0) {
						$numPage = $TotalPage;
					} else {
						$numPage = $TotalPage + 1;
					}
					print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
					for ($i=1; $i <= $numPage; $i++) {
						print '<A href="'.$sFileName.'?&StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'">';
						print '<b><u>'.(($i * $pg) - $pg + 1).'-'.($i * $pg).'</u></b></a>&nbsp;&nbsp;';
					}
					print '</td>
						</tr>
					</table>';
				}				
		print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Rekod : <b>' . $GetLoan->RowCount() . '</b></td>
		</tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk '.$title.'  -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "'.$q.'" tidak jumpa  -</b><hr size=1"></td></tr>';
		}
	}
print ' 
</table>
</div>
</form>';

include("footer.php");	

if ($agree <> "") {
	//--- End   : Call function FormValidation ---  

		
		$updatedDate = date("Y-m-d H:i:s"); 
		$sSQL = "";
		$sWhere = "";		
	    $sWhere = "loanID=" . tosql($table, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";		
		$sSQL	= "UPDATE loans SET " .
		          " stat_agree=" . 2 .
				  ", approvedDate=" . tosql($updatedDate, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		print '<script>
			alert ("Maklumat telah dikemaskinikan ke dalam sistem.");
			window.close();
		   window.opener.document.MyForm.submit();
		</script>';
}



print '
<script language="JavaScript">
	var allChecked=false;

	$(document).ready(function(){
		$("[rel=tooltip]").tooltip({ placement: "bottom"});
	});

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
		  alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
		  count=0;
		  for(c=0; c<e.elements.length; c++) {
			if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Sila pilih satu rekod sahaja untuk kemaskini status\');
			} else {
				window.open(\'loanStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');					
			}
		}
	}	
	
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';



/*					if ($GetLoan->fields(loanType) == '1620' or $GetLoan->fields(loanType) == '1616') { 
					
					print $chk.'&nbsp;&nbsp;<input type=button value="Pengesahan" class="but" onClick=window.open("tawaranSah3.php?ID='.$GetLoan->fields(loanID).'","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>'; 
					
}*/
?>
