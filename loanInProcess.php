<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanInProcess.php
 *          Date 		: 	12/09/2006//
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 9999;
if (!isset($q))			$q = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$sFileName = '?vw=loanInProcess&mn=3';
$sFileRef  = '?vw=loanInProcess&mn=3';
$title     = "Daftar Pembiayaan Dalam Proses";

//--- Begin	: deletion based on	checked	box	-------------------------------------------------------
if ($action	== "delete") {
	$sWhere	= "";
	for ($i	= 0; $i	< count($pk); $i++) {
		$CheckLoan = ctLoan("", $pk[$i]);
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

//--- Begin	: deletion based on	checked	box	-------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['loanID'])) {
	$loanID = $_GET['loanID'];

	$CheckLoan = ctLoan("", $loanID);
	if ($CheckLoan->RowCount() == 1) {
		if ($CheckLoan->fields('status') < 3) {
			$sWhere = "loanID=" . tosql($loanID, "Number");
			$sSQL = "DELETE FROM loans WHERE " . $sWhere;
			$rs = $conn->Execute($sSQL);
			// hapus biaya doc
			$sSQL = "DELETE FROM loandocs WHERE " . $sWhere;
			$rs = $conn->Execute($sSQL);

			echo '<script>alert("Permohonan telah berjaya dihapuskan!");</script>';
		} else {
			print '<script>alert("Hanya permohonan belum siap proses boleh dihapus!");</script>';
		}
	} else {
		echo '<script>alert("Rekod permohonan tidak dijumpai!");</script>';
	}
}
//--- End	: deletion based on	checked	box	-------------------------------------------------------


$sSQL = "SELECT	a.*,b.* FROM loans a,general b
		 WHERE a.loanType = b.ID AND 
		 a.status IN (0,1,2) AND a.userID = '" . get_session('Cookie_userID') . "' ORDER BY a.applyDate DESC";
$GetLoan = &$conn->Execute($sSQL);
$GetLoan->Move($StartRec - 1);
$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr><td><h5 class="card-title"><i class="mdi mdi-progress-alert"></i>&nbsp;' . strtoupper($title) . '</h5></td></tr>';


if ($GetLoan->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;

	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">                             
					<tr class="table-primary">
						<td nowrap height="20"></td>
						<td nowrap>Nombor Rujukan Pembiayaan</td>
						<td nowrap align="right">Jumlah Pinjaman (RM)</td>					
						<td nowrap align="center">Jangka Waktu (Bulan)</td>
						<td nowrap align="center">Status</td>									
						<td nowrap align="center">Tarikh Mohon</td>	
						<td nowrap>&nbsp;</td>			
					</tr>';
	while (!$GetLoan->EOF && $cnt <= $pg) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields(userID), "Text"));
		$status = $GetLoan->fields(status);
		$sijilID = dlookup("komoditi", "loanID", "loanID=" . tosql($GetLoan->fields(loanID), "Text"));
		$colorStatus = "Data";
		if ($status == 3) $colorStatus = "greenText";
		if ($status == 4 || $status == 5) $colorStatus = "redText";
		if ($GetLoan->fields(isApproved))
			$chk = '<i class="mdi mdi-check text-primary"></i>';
		else
			$chk = '<i class="mdi mdi-close text-danger"></i>';

		//--------------
		$loanType				= $GetLoan->fields('loanType');
		$codegroup				= dlookup("general", "parentID", "ID=" . $loanType);
		$prove = $GetLoan->fields(isApproved);
		$stat_agree = $GetLoan->fields(stat_agree);
		$table  = $GetLoan->fields(loanID);
		$remark = dlookup("loandocs", "remarkPrepare", "loanID=" . tosql($GetLoan->fields('loanID'), "Text"));

		$colorStatus = "text-success"; // default

		if ($status == 1) $colorStatus = "text-info";
		if ($status == 2) $colorStatus = "text-info";
		

		print ' <tr>
				<td class="Data" align="center" height="20">' . $bil . '</td>
				<td class="Data" align="left"><!--input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetLoan->fields(loanID)) . '">&nbsp;-->'
			. $GetLoan->fields(loanNo) . ' - ' . dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"))
			. '</td>
				<td class="Data" align="right">' . number_format($GetLoan->fields('loanAmt'), 2, '.', ',') . '</td>
				<td class="Data" align="center">' . $GetLoan->fields('loanPeriod') . '</td>
				<td class="Data" align="center"><font class="' . $colorStatus . '">' . $biayaList[$status] . ' &nbsp;';
		if ($status == 0) {
			print '<span class="mdi mdi-information-outline" rel="tooltip" title="Pembiayaan anda sedang dalam proses dan akan disemak kemudian."></span>';
		}
		if ($status == 1) {
			print '<span class="mdi mdi-information-outline" rel="tooltip" title="Pembiayaan anda telah diproses dan sedang disediakan. Sila semak catatan daripada pegawai -> '.$remark.'"></span>';
		}
		if ($status == 2) {
			print '<span class="mdi mdi-information-outline" rel="tooltip" title="Pembiayaan anda telah disediakan dan sedang disemak."></span>';
		}

		if ($codegroup <> 1638) {
			$table  = "loanJadual_user.php?id=" . $GetLoan->fields(loanID);
		} else {
			$table = "loanJadual78NEW_user.php?type=vehicle&page=view&id=" . $GetLoan->fields(loanID);
		}

		print '</font>

					
<td class="Data" align="center">' . toDate("d/m/Y", $GetLoan->fields(applyDate)) . '</td>
<td>
    <a href="?vw=loanInProcess&mn=3&action=delete&loanID=' . $GetLoan->fields('loanID') . '" class="badge bg-danger text-dark" onClick="return confirm(\'Adakah anda pasti untuk hapus permohonan ini?\')" title="Hapus"> <i class="fas fa-trash-alt"></i></a>
</td>


	<!--td class="Data" align="center">&nbsp;';

		if ($GetLoan->fields(startPymtDate) <> "") {
			print '<input type=button value="Lihat Jadual" class="but" onClick=window.open("tawaranSah2tawarruq.php?id=' . $GetLoan->fields(loanID) . '","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
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
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
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
	var allChecked = false;

	$(document).ready(function(){
		$("[rel=tooltip]").tooltip({ placement: "bottom"});
	});

	function ITRViewSelectAll() {
	    var e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for (var c = 0; c < e.length; c++) {
	      if (e[c].type === "checkbox" && e[c].name !== "all") {
	        e[c].checked = allChecked;
	      }
	    }
	}

		function ITRActionButtonClick(v) {
		var e = document.MyForm;
		if (e == null) {
		  alert(\'Sila pastikan nama form diwujudkan.\');
		} else {
			if (confirm(\'Adakah anda pasti untuk hapus permohonan ini?\')) {
			  e.action.value = v;
			  e.submit();
			}
		}
	}

	function ITRActionButtonClickStatus(v) {
	      var e = document.MyForm;
	      if (e == null) {
			alert(\'Sila pastikan nama form diwujudkan.\');
	      } else {
	        var count = 0;
	        var pk = "";
	        for (var c = 0; c < e.elements.length; c++) {
	          if (e.elements[c].name === "pk[]" && e.elements[c].checked) {
	            count++;
	            pk = e.elements[c].value;
	          }
	        }
	        if (count === 0) {
	          alert(\'Sila pilih rekod yang hendak overlap pembiayaan.\');
	        } else if (count !== 1) {
	          alert(\'Sila pilih satu rekod sahaja untuk overlap pembiayaan\');
	        } else {
				// Debugging
				console.log("Navigating to URL: ?vw=loanOverlap&mn=3&pk=" + pk);
				window.location.href = "?vw=loanOverlap&mn=3&pk=" + pk;
	        }
	      }
	}	

	function doListAll() {
		var c = document.forms[\'MyForm\'].pg;
		document.location = "<?= $sFileName ?>?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';



/*					if ($GetLoan->fields(loanType) == '1620' or $GetLoan->fields(loanType) == '1616') { 
					
					print $chk.'&nbsp;&nbsp;<input type=button value="Pengesahan" class="but" onClick=window.open("tawaranSah3.php?ID='.$GetLoan->fields(loanID).'","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>'; 
					
}*/
