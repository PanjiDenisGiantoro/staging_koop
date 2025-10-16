<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	biayaMember.php
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
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 0 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
	exit;
}

$sFileName = '?vw=biayaMember&mn=5';
$sFileRef  = '?vw=biayaMohonJaminan&mn=5';
$title     = "Permohonan Penjamin Pembiayaan";

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND a.status = 1 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

$loList = array();
$sSQL = "SELECT * FROM general WHERE category = 'C' AND c_gurrantor = 1";
$rs = &$conn->Execute($sSQL);

// Pastikan ada data dari query pertama
if ($rs && $rs->RecordCount() > 0) {
	while (!$rs->EOF) {
		$loList[] = $rs->fields['ID'];
		$rs->MoveNext();
	}
}

// Pastikan $loList valid sebelum dipakai
if (!empty($loList)) {
	// Jika ID berupa integer
	$loList = implode(",", array_map('intval', $loList));
} else {
	// Jika tidak ada data, gunakan nilai default untuk mencegah error
	$loList = "0";
}

$pk = get_session('Cookie_userID');
$sqlGet = "SELECT DISTINCT A.* FROM loans A, userdetails B WHERE ( A.userID = B.userID AND B.userID = '" . $pk . "') and A.loanType in (" . $loList . ") AND A.status NOT IN (9) ORDER BY A.applyDate DESC";
$GetLoan =  &$conn->Execute($sqlGet);
$GetLoan->Move($StartRec - 1);

$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);
$loanID = $GetLoan->fields(loanID);

print '
<form name="MyForm" action="' . $sFileName . '" method="post">
<input type="hidden" name="action">
<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><h5 class="card-title"><i class="typcn typcn-tick-outline"></i>&nbsp;' . strtoupper($title) . '</h5></td>		
	</tr>';
print '		
	</tr>
	<tr valign="top">
		<td align="left">';
// if($filter == 0) 
print ' <input type="button" value="Kemaskini Penjamin" class="btn btn-primary btn-sm w-md waves-effect waves-light" onClick="ITRActionButtonClick(\'ubah\')">
			<hr class="mb-2">';

if ($GetLoan->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top" >
			<td valign="top">
			<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
			<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap align="left">Nomor Rujukan - Pembiayaan</td>
						<td nowrap align="center">Status</td>
						<td nowrap align="right">Penjamin 1</td>
						<td nowrap align="right">Penjamin 2</td>
						<td nowrap align="right">Penjamin 3</td>
					</tr>';
	$amtLoan = 0;
	while (!$GetLoan->EOF && $cnt <= $pg) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields(userID), "Text"));
		$amt =  number_format(tosql($GetLoan->fields(loanAmt), "Number"), 2);
		$amtLoan = $amtLoan + tosql($GetLoan->fields(loanAmt), "Number");

		$status = $GetLoan->fields(status);

		$colorStatus = "text-success";
		if ($status == 3) $colorStatus = "text-primary"; //$colorStatus = "greenText";
		if ($status == 4 || $status == 5) $colorStatus = "text-danger";
		if ($GetLoan->fields(statuspID1)) $approve1 = '<i class="mdi mdi-check text-primary"></i>';
		else $approve1 = '<i class="mdi mdi-close text-danger"></i>';
		if ($GetLoan->fields(statuspID2)) $approve2 = '<i class="mdi mdi-check text-primary"></i>';
		else $approve2 = '<i class="mdi mdi-close text-danger"></i>';
		if ($GetLoan->fields(statuspID3)) $approve3 = '<i class="mdi mdi-check text-primary"></i>';
		else $approve3 = '<i class="mdi mdi-close text-danger"></i>';
		print ' <tr>
<td class="Data" align="center">' . $bil . '&nbsp;</td>';

		if ($status < 4) {
			print '
	<td	class="Data"><input	type="checkbox" class="form-check-input" name="pk[]"	value="' . $GetLoan->fields(loanID) . '"> '
				. $GetLoan->fields(loanNo) . '
			(' . dlookup("general", "name", "ID=" . $GetLoan->fields(loanType)) . ')
			</b></td>';
		} else {

			print '
		<td class="Data">			
			' . $GetLoan->fields(loanNo) . '
			(' . dlookup("general", "name", "ID=" . $GetLoan->fields(loanType)) . ')
		</td>';
		}
		print '<td class="Data" align="center"><font class="' . $colorStatus . '">' . $biayaList[$status] . '</font></td>


<td class="Data" align="right">' . $approve1 . '&nbsp;' . dlookup("users", "name", "userID=" . tohtml($GetLoan->fields(penjaminID1)))  . '&nbsp;</td>
<td class="Data" align="right">' . $approve2 . '&nbsp;'  . dlookup("users", "name", "userID=" . tohtml($GetLoan->fields(penjaminID2))) . '&nbsp;</td>
<td class="Data" align="right">' . $approve3 . '&nbsp;'  . dlookup("users", "name", "userID=" . tohtml($GetLoan->fields(penjaminID3)))  . '&nbsp;</td>
					</tr>';
		$cnt++;
		$bil++;
		$GetLoan->MoveNext();
	}

	print '</table></td></tr><tr><td>';
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
			if (is_int($i / 10)) print '<br />';
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&filter=' . $filter . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td></tr></table>';
	}
	print '
			</td></tr><tr>
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
print ' </table></div></form>';
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
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
				pk = e.elements[c].value;
	          }			  
	        }
			
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak diperbaiki.\');
	        } else {
				if(count != 1) {
					alert(\'Sila pilih satu rekod sahaja untuk kemaskini penjamin\');
	          } else {			
				window.location.href = "?vw=biayaMohonJaminan&mn=5&pk=" + pk;
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
				window.open(\'loanStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');					
			}
		}
	}		

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value+"&filter=' . $filter . '";
	}
</script>';
