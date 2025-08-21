<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberProfil.php
 *          Date 		: 	26/03/2006
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 10;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "1";
if (!isset($dept))		$dept = "";
if (!isset($active))	$active = "1";
if ($filter == 1)	$active = "1";
date_default_timezone_set("Asia/Jakarta");
include("header.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=penjamin&mn=906';
$sFileRef  = '?vw=penjaminDetail&mn=906';
$title     = "Informasi penjamin";

if ($dept == "BSR") {
	$filter = 4;
	$dept = "";
}

$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status = " . tosql($filter, "Number") . " AND a.isActive = " . tosql($active, "Number");
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.memberID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '%" . $q . "%'";
	}
}

$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*
		 FROM 	users a, userdetails b";
$sSQL = $sSQL . $sWhere;
$sSQL = $sSQL . "order by CAST( b.memberID AS SIGNED INTEGER ) desc";
$GetMember = &$conn->Execute($sSQL);
//$GetMember = ctMemberStatusDeptA($q,$by,$filter,$dept,$active);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print
	'<div class="table-responsive"><form name="MyForm" action=' . $sFileName . ' method="post">'
	. '<input type="hidden" name="action">'
	. '<input type="hidden" name="pk" value="<?=$pk?>">'
	. '<input type="hidden" name="filter" value="' . $filter . '">'
	. '<h5 class="card-title">' . strtoupper($title) . '</h5>'
	. '<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">';

print
	'<div class="mb-3 row m-1">
            <div>'
	. 'Cari Berdasarkan '
	. '<select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nomor Anggota</option>';
else print '<option value="1">Nomor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kartu Identitas</option>';
else print '<option value="3">Kartu Identitas</option>';
print
	'</select>
			<input type="text" name="q" value="" maxlength="50" size="20" class="form-control-sm">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;		
&nbsp;
			Jenis
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
for ($i = 0; $i < count($statusList); $i++) {
	if ($i == 1 || $i == 3 || $i == 4) {
		if ($statusVal[$i] < 5) {
			print '	<option value="' . $statusVal[$i] . '" ';
			if ($filter == $statusVal[$i]) print ' selected';
			print '>' . $statusList[$i];
		}
	}
}
print
	'</select></div>
	</div>';

print '	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>';
//if ($filter == 3) {
if ($filter) {
	print
		'<!--td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All</td-->
						<td align="right" class="textFont">';
} else {
	print
		'<td class="textFont">&nbsp;</td><td align="right" class="textFont">';
}

print
	'&nbsp;&nbsp;Tampil
					<SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
if ($pg == 5)	print '<option value="5" selected>5</option>';
else print '<option value="5">5</option>';
if ($pg == 10)	print '<option value="10" selected>10</option>';
else print '<option value="10">10</option>';
if ($pg == 20)	print '<option value="20" selected>20</option>';
else print '<option value="20">20</option>';
if ($pg == 30)	print '<option value="30" selected>30</option>';
else print '<option value="30">30</option>';
if ($pg == 40)	print '<option value="40" selected>40</option>';
else print '<option value="40">40</option>';
if ($pg == 50)	print '<option value="50" selected>50</option>';
else print '<option value="50">50</option>';
print
	'</select>&nbsp;&nbsp;setiap halaman.
					</td>
				</tr>
			</table>
		</td>
	</tr>';

if ($GetMember->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print
		'<tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" class="table table-striped table-sm" width="100%">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap>Nama</td>
						<td nowrap align="center">Nomor Anggota</td>						
						<td nowrap align="center">Kartu Identitas</td>
						<td nowrap align="left">Cabang/Zona</td>
						<td nowrap align="center">Status</td>
					</tr>';
	while (!$GetMember->EOF && $cnt <= $pg) {
		$status = dlookup("userdetails", "status", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$deptID = dlookup("userdetails", "departmentID", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$nameDept = dlookup("general", "name", "ID=" . tosql($deptID, "Number"));
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		print '<tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>						
						<td class="Data">';
		print '	<a href="' . $sFileRef . '&pk=' . tohtml($GetMember->fields(userID)) . '">
							' . $GetMember->fields(name) . '</a></td>
							<td class="Data" align="center">';
		print $GetMember->fields(memberID) . '</td>
						<td class="Data" align="center">' . convertNewIC($GetMember->fields(newIC)) . '</td>
						<td class="Data" align="left">' . $nameDept . '</td>
						<td class="Data" align="center">' . $statusList[$status] . '</td>
					</tr>';
		$cnt++;
		$bil++;
		$GetMember->MoveNext();
	}
	$GetMember->Close();

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
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '&filter=' . $filter . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Data : <b>' . $GetMember->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == '') {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size="1"></td></tr>';
	} else {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size="1"></td></tr>';
	}
}
print ' 
</table></td></tr></table>
</form></div>';

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
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' data yang ingin di \' + v + \'?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }	   

		
	function ITRActionButtonClickStatus(v) {
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
				pk = e.elements[c].value;
				strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="?vw=memberAktif&pk=" + strStatus;
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
				window.location.href = "?vw=memberAktif&pk=" + pk;
			}
		}
	}


	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

    function ITRActionButtonReset() {
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
				alert(\'Silakan pilih satu data saja untuk reset kata sandi\');
			} else {
	          if(confirm(\' Data ini akan direset kata sandinya?\')) {
	            e.action.value = \'reset\';
				e.dept.value = "' . $dept . '";
				e.by.value = "' . $by . '";
				e.q.value = "' . $q . '";
	            e.submit();
	          }
			}
		}
	}


</script>';
