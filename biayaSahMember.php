<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	biayaSahMember.php
 *          Date 		: 	12/12/2018
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($filter))	$filter = "0";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 0 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
//$sFileName = 'loan.php';
$sFileName = '?vw=loan&mn=5';
$sFileRef  = '?vw=biayaJaminan&mn=5';
$title     = "Pengesahan sebagai penjamin Pembiayaan";

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status = 1 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

$pk = get_session('Cookie_userID');
$sqlGet = "SELECT DISTINCT A.* FROM loans A, userdetails B
 WHERE ( A.userID = B.userID 
 AND ( A.penjaminID1 = '" . $pk . "' OR A.penjaminID2 = '" . $pk . "' OR A.penjaminID3 = '" . $pk . "')) ORDER BY A.applyDate DESC";
$GetLoan =  &$conn->Execute($sqlGet);
$GetLoan->Move($StartRec - 1);

$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="table-responsive">

	<h5 class="card-title"><i class="mdi mdi-account-check"></i>&nbsp;' . strtoupper($title) . ' &nbsp;</h5>';
if ($GetLoan->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap align="left">Nombor Rujukan - Pembiayaan [Pengesahan]</td>
						<td nowrap align="left">Nombor - Nama Anggota</td>
						<td nowrap align="center">Kad Pengenalan</td>
						<td nowrap align="right">Jumlah Pembiayaan (RP)</td>
						<td nowrap align="center">Tarikh Pengesahan</td>			
						<td nowrap align="center">
						';
	if ($filter <> 1) print 'Status';
	else print 'Tarikh Diluluskan';
	print '</td>
					</tr>';
	$amtLoan = 0;
	while (!$GetLoan->EOF && $cnt <= $pg) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields(userID), "Text"));
		$amt =  number_format(tosql($GetLoan->fields(loanAmt), "Number"), 2);
		$amtLoan = $amtLoan + tosql($GetLoan->fields(loanAmt), "Number");
		$status = $GetLoan->fields(status);
		$colorStatus = "Data";
		if ($status == 0) $colorStatus = "text-success";
		if ($status == 1) $colorStatus = "text-warning";
		if ($status == 2) $colorStatus = "text-info";
		if ($GetLoan->fields(penjaminID1) == $pk) {
			if ($GetLoan->fields(statuspID1) == 1) {
				$approve = '<i class="mdi mdi-check text-primary"></i>';
				$datelulus = toDate("d/m/yy", $GetLoan->fields(updatedDateJmn));
			} else $approve = '<i class="mdi mdi-close text-danger"></i>';
		}
		if ($GetLoan->fields(penjaminID2) == $pk) {
			if ($GetLoan->fields(statuspID2) == 1) {
				$approve = '<i class="mdi mdi-check text-primary"></i>';
				$datelulus = toDate("d/m/yy", $GetLoan->fields(updatedDateJmn2));
			} else $approve = '<i class="mdi mdi-close text-danger"></i>';
		}
		if ($GetLoan->fields(penjaminID3) == $pk) {
			if ($GetLoan->fields(statuspID3) == 1) {
				$approve = '<i class="mdi mdi-check text-primary"></i>';
				$datelulus = toDate("d/m/yy", $GetLoan->fields(updatedDateJmn3));
			} else $approve = '<i class="mdi mdi-close text-danger"></i>';
		}

		print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data">' . $approve . '<!--input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetLoan->fields(loanID)) . '"-->
						<a href="' . $sFileRef . '&pk=' . tohtml($GetLoan->fields(loanID)) . '">
						' . $GetLoan->fields(loanNo) . '/' . dlookup("general", "name", "ID=" . $GetLoan->fields(loanType)) .
			'&nbsp;</td>
						<!--td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetLoan->fields(loanID)) . '">
						<a href="' . $sFileRef . '?&pk=' . tohtml($GetLoan->fields(loanID)) . '">'
			. dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")) . '-'
			. sprintf("%010d", $GetLoan->fields(loanID)) . '</td-->
						<td class="Data" align="left">'
			. dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '-'
			. dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '</td>
						<td class="Data" align="center">' . dlookup("userdetails", "newIC", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '</td>
						<td class="Data" align="right">' . $amt . '</td>
						<td class="Data" align="center">' . $datelulus . '</td>';

		if ($filter <> 1) print '<td class="Data" align="center"><font class="' . $colorStatus . '">' . $biayaList[$status] . '</font></td>';
		else print '<td class="Data" align="center">' . toDate("d/m/yy", $GetLoan->fields(approvedDate)) . '</td>';
		print '		</tr>';
		$cnt++;
		$bil++;
		$GetLoan->MoveNext();
	}
	print ' 	<tr>
						<td class="DataB" align="right" colspan="4" height="20"><b>Jumlah Pembiayaan (RP) </b></td>
						<td class="DataB" align="right"><b>' . number_format($amtLoan, 2, '.', ',') . '</b></td>
						<td class="DataB" colspan="2">&nbsp;</td>
					</tr>	
				</table>
                                <!--table class="table table-sm"-->
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
		for ($i = 1; $i <= $numPage; $i++) {
			if (is_int($i / 10)) print '<br />';
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&filter=' . $filter . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					<!--/table-->
					';
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
			<center>
			<tr><td><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr></center>';
	} else {
		print '
			<center>
			<tr><td><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr></center>';
	}
}
print ' 
</table>
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
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value+"&filter=' . $filter . '";
	}
</script>';
