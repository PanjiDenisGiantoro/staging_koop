<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanApproved.php
 *          Date 		: 	04/03/2022//
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 9999;
if (!isset($q))			$q = "";
if (!isset($dept))		$dept = "";
if (!isset($by))		$by = "0";
if (!isset($yr)) $yr	= date("Y");
if (!isset($yy)) $yy	= date("Y");
if (!isset($mm)) $mm	= date("m");

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=loanApproved&mn=3';
$sFileRef  = '?vw=loanApproved&mn=3';
$title     = "Senarai Pembiayaan Diluluskan (Aktif)";

//----print penyata tahunan pembiayaan
if ($action <> "") {
	print '	<script>';
	if ($action == "Penyata") {
		print ' rptURL = "loanYearly.php?yr=' . $yr . '&loanID=' . $pk[0] . '";';
		print ' window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");';
	}
	print ' </script>';
}


$sSQL = "SELECT	a.*,b.*, c.* FROM loans a,general b, loandocs c
		 WHERE a.loanType = b.ID 
		 AND a.loanID = c.loanID
		 AND a.status IN (3)
		 AND a.userID = '" . get_session('Cookie_userID') . "' ORDER BY a.applyDate DESC";
$GetLoan = &$conn->Execute($sSQL);
$GetLoan->Move($StartRec - 1);
$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);


print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr><td><h5 class="card-title"><i class="typcn typcn-tick-outline"></i>&nbsp;' . strtoupper($title) . '</h5></td></tr>';
if ($GetLoan->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top" >
			<td valign="top">
			<tr>
			<td>
			<input type="button" class="btn btn-sm btn-warning w-sm waves-effect waves-light" value="Lejer" onClick="ITRActionButtonClick(\'loanYearly\');">
			<hr class="mb-2"></td></tr>
		
				<table class="table table-sm table-striped" border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap><b>Nomor Rujukan Pembiayaan</b></td>
						<td nowrap align="right"><b>Jum. Pembiayaan (RP)</b></td>			
						<td nowrap align="right"><b>Ansuran (RP)</b></td>		
						<td nowrap align="center"><b>Jangka Waktu (Bulan)</b></td>
						<td nowrap align="center"><b>Jadual Bayaran</b></td>
						<td nowrap align="center"><b>Surat Tawaran</b></td>
						<td nowrap align="center"><b>Tarikh Kelulusan</b></td>
					</tr>';	 //<td nowrap align="center">&nbsp;Jadual Pembiayaan</td>
	while (!$GetLoan->EOF && $cnt <= $pg) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields(userID), "Text"));
		$status = $GetLoan->fields(status);
		$sijilID = dlookup("komoditi", "loanID", "loanID=" . tosql($GetLoan->fields(loanID), "Text"));
		$colorStatus = "Data";
		if ($status == 3) $colorStatus = "greenText";
		if ($status == 4 || $status == 5) $colorStatus = "redText";
		if ($GetLoan->fields(isApproved))
			$chk = '<b><i class="mdi mdi-check text-primary"></i></b>';
		else
			$chk = '<b><i class="mdi mdi-close text-danger"></i></b>';

		//--------------
		$loanType				= $GetLoan->fields('loanType');
		$codegroup				= dlookup("general", "parentID", "ID=" . $loanType);
		$prove = $GetLoan->fields(isApproved);
		$stat_agree = $GetLoan->fields(stat_agree);
		$table  = $GetLoan->fields(loanID);

		$bond = dlookup("loandocs", "rnobond", "loanID=" . tosql($GetLoan->fields(loanID), "Text"));
		if ($bond == '') $bond = 'AJK';

		//condition untuk loan table 
		if ($codegroup <> 1638) {
			$table  = "loanJadual_user.php?id=" . $GetLoan->fields(loanID);
		} else {
			$table = "loanJadual78NEW_user.php?type=vehicle&page=view&id=" . $GetLoan->fields(loanID);
		}




		print ' <tr>
				<td class="Data" align="center" height="20">' . $bil . '</td>
				<td class="Data" align="left"><input type="checkbox" class="form-check-input" name=pk[] value=' . $GetLoan->fields(loanID) . '>&nbsp;'
			. $GetLoan->fields(loanNo) . ' - ' . dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"))
			. '</td>
				<td class="Data" align="right">' . number_format($GetLoan->fields('loanAmt'), 2, '.', ',') . '</td>
				<td class="Data" align="right">' . number_format($GetLoan->fields('lpotBiaya'), 2, '.', ',') . '</td>
				<td class="Data" align="center">' . $GetLoan->fields('loanPeriod') . '</td>
				<td class="Data" align="center"><input type=button value="Cetak" class="btn btn-sm btn-dark waves-effect waves-light" class="but" onClick=window.open("' . $table . '","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");></td>
				</td>				
				<td class="Data" align="center">';

		if ($status == 3 & $prove == 0 & $stat_agree == 1) {
			print $chk . '<input type=button value="Sahkan Surat" class="btn btn-sm btn-warning waves-effect waves-light" value="Surat Tawarruq" class="but" onClick="window.location.href=\'?vw=tawaranSahSurat&mn=3&ID=' . $GetLoan->fields(loanID) . '\';">';
		}

		if ($status == 3 & $prove == 0 & $stat_agree == 2) {
			print $chk . '<input type=button value="Sahkan Akad" class="btn btn-sm btn-info waves-effect waves-light" value="Surat Tawarruq" class="but" onClick="window.location.href=\'?vw=tawaranSahtawarruq1&mn=3&ID=' . $GetLoan->fields(loanID) . '\';">';
		}

		if ($prove == 1 & $status == 3) {
			print $chk . '<input type=button class="btn btn-sm btn-primary waves-effect waves-light" value="Surat Tawarruq" class="but" onClick="window.location.href=\'?vw=tawaranSah2tawarruq&mn=3&ID=' . $GetLoan->fields(loanID) . '\';">';
		}

		print '</font></td>

	<!-- <td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetLoan->fields(applyDate)) . '</td> -->
						
	<td class="Data" align="center">' . toDate("d/m/Y", $GetLoan->fields(approvedDate)) . '</td>
	<!--td class="Data" align="center">&nbsp;';

		if ($GetLoan->fields(startPymtDate) <> "") {
			print '<input type=button value="Lihat Jadual" class="but" onClick=window.open("loanJadual_user.php?id=' . $GetLoan->fields(loanID) . '","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
		} else {
			print 'Belum Diluluskan';
		}
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
					<table class="table table-sm textFont" border="0" cellspacing="5" cellpadding="0"  width="100%">';
		if ($TotalRec % $pg == 0) {
			$numPage = $TotalPage;
		} else {
			$numPage = $TotalPage + 1;
		}
		print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Data : <b>' . $GetLoan->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
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
	function ITRViewSelectAll() {
	    e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}

	function ITRActionButtonClick(rpt) {
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
			}
			
			if(count != 1) {
				alert(\'Sila pilih satu pembiayaan !\');
			} else {
				if (rpt == "loanYearly" )  {
					url = "loanYearly.php?pk="+ pk +"&yr=' . $yy . '";
				}
					
				window.open (url, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
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
