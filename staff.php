<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	staff.php
 *          Date 		: 	
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "ALL";
if (!isset($jabatan))	$jabatan = "";
date_default_timezone_set("Asia/Jakarta");

include("header.php");
include("koperasiQry.php");
require_once("common.php");

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=staff&mn=919';
$sFileRef  = '?vw=staffEdit&mn=919';
$title     = "Senarai Staf Koperasi";

$IDName = get_session("Cookie_userName");
//--- Begin : deletion  -----------------------------------------------------------------------
if (isset($_POST['delete'])) {
	$staffID = intval($_POST['delete_id']);

	if ($staffID > 0) {
		$sWhere = "staffID=" . tosql($staffID, "Text");

		$sSQL = "DELETE FROM users WHERE $sWhere";
		$resultUsers = $conn->Execute($sSQL);
		if (!$resultUsers) {
			echo '<script>alert("Error deleting from users table for Staff ID: ' . $staffID . '");</script>';
		}

		$sSQL = "DELETE FROM staff WHERE $sWhere";
		$resultStaff = $conn->Execute($sSQL);
		if (!$resultStaff) {
			echo '<script>alert("Error deleting from staff table for Staff ID: ' . $staffID . '");</script>';
		} else {
			echo '<script>
                    alert("Data berjaya dihapuskan.");
                    window.location.href = "' . $sFileName . '";
                  </script>';
			exit();
		}
	} else {
		echo '<script>alert("ID rekod tidak sah.");</script>';
	}
}


//--- End   : deletion  -------------------------------------------------------------------------

//--- Prepare jabatan list
$jabatanList = array();
$jabatanVal  = array();
$sSQL = "SELECT b.jabatanID, g.code as jabatanCode, g.name as jabatanName
         FROM staff b
         JOIN general g ON b.jabatanID = g.ID
         WHERE g.category = 'W'
         GROUP BY b.jabatanID";

$rs = $conn->Execute($sSQL);
if ($rs->RowCount() != 0) {
	while (!$rs->EOF) {
		array_push($jabatanList, $rs->fields['jabatanName']);
		array_push($jabatanVal, $rs->fields['jabatanID']);
		$rs->MoveNext();
	}
}
$sWhere = " WHERE 1=1 ";

if (!empty($jabatan)) {
	$sWhere .= " AND b.jabatanID = " . intval($jabatan);
}

if (isset($filter) && $filter !== "ALL") { // Use isset to include "0" as a valid value
	$sWhere .= " AND b.statuskerja = " . intval($filter);
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND a.staffID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND b.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '%" . $q . "%'";
	}
}

$sSQL = "SELECT DISTINCT a.*, b.*
FROM users a
JOIN staff b ON a.staffID = b.staffID
$sWhere
ORDER BY CAST(a.staffID AS SIGNED INTEGER) ASC";

$GetMember = $conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
    <input type="hidden" name="action">
    <input type="hidden" name="pk" value="' . $pk . '">
    <input type="hidden" name="filter" value="' . $filter . '">
	<input type="hidden" name="jabatan" value="' . $jabatan . '">
    <h5 class="card-title">' . strtoupper($title) . '</h5>

	<div class="mb-3 d-flex align-items-center justify-content-between">
			<div class="d-flex align-items-center">
							<label class="me-2">Carian</label>
							<select name="by" class="form-select form-select-sm me-2" style="width: auto;">';
if ($by == 1) print '<option value="1" selected>Nombor Staf</option>';
else print '<option value="1">No. Staf</option>';
if ($by == 2) print '<option value="2" selected>Nama Staf</option>';
else print '<option value="2">Nama Staf</option>';
if ($by == 3) print '<option value="3" selected>Kartu Identitas</option>';
else print '<option value="3">Kartu Identitas</option>';
print '      </select>


							<input type="text" name="q" value="" maxlength="50" size="20" class="form-control form-control-sm me-2" style="width: auto;">
							<input type="submit" class="btn btn-sm btn-secondary me-3" value="Cari">';

/*          
							<label class="me-2">Jabatan</label>
							<select name="jabatan" class="form-select form-select-sm" onchange="document.MyForm.submit();">
								<option value="">- Semua -</option>';
								for ($i = 0; $i < count($jabatanList); $i++) {
									print '<option value="'.$jabatanVal[$i].'" ';
									if ($jabatan == $jabatanVal[$i]) print ' selected';
									print '>'.$jabatanList[$i];
								}
				print '      </select>
				*/

print '        </div>

						<div>
							<a href="?vw=staffRegister&mn=919" class="btn btn-sm btn-success">Daftar Staf</a>
						</div>
					</div>
					
				<div class="mb-3 row m-1">
				<div>
				Status
							<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
print '<option value="ALL">Semua';
for ($i = 0; $i < count($kerjaList); $i++) {
	if ($kerjaVal[$i] < 5) {
		print '	<option value="' . $kerjaVal[$i] . '" ';
		if ($filter == $kerjaVal[$i]) print ' selected';
		print '>' . $kerjaList[$i];
	}
}
print '	</select>&nbsp;';

print '          
							<input type="button" class="btn btn-sm btn-primary" value="Penetapan Cuti" onClick="ITRActionButtonClickStatus(\'proses\');">
				
							<input type="button" class="btn btn-sm btn-warning" value="Penetapan Gaji" onClick="ITRActionButtonClickGaji(\'gaji\');">
						</div>
						</div>';


print '
<div class="table-responsive">    
<!--table border="1" cellspacing="1" cellpadding="3" width="100%" align="center" class="table"-->
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<!-- 
					<td class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>					
					<td align="right" class="textFont">Paparan 
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
if ($pg == 100)	print '<option value="100" selected>100</option>';
else print '<option value="100">100</option>';
print '				</select> setiap halaman..
					</td>
					-->
				</tr>
			</table>
		</td>
	</tr>';

if ($GetMember->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap align="left">&nbsp;Nama Staf</td>
						<td nowrap align="center">&nbsp;Nombor Staf</td>
						<td nowrap align="center">&nbsp;Kartu Identitas</td>
						<td nowrap align="center">&nbsp;Jabatan</td>
						<td nowrap align="center">&nbsp;Status</td>
						<td nowrap align="center">&nbsp;Tarikh Daftar</td>
						<td nowrap align="center"></td> 
					</tr>';
	while (!$GetMember->EOF && $cnt <= $pg) {
		$status   = dlookup("staff", "statuskerja", "staffID=" . tosql($GetMember->fields('staffID'), "Number"));
		$statusIndex = @array_search($status, $kerjaVal); // Use $status, not $statusID
		$jabatan  = dlookup("staff", "jabatanID", "staffID=" . tosql($GetMember->fields('staffID'), "Number"));
		$jabatanN = dlookup("general", "name", "ID=" . $jabatan);

		// If $jabatanN is empty, set it to "-"
		$jabatanN = (!empty($jabatanN)) ? $jabatanN : "-";

		// Get the status name from the array if found, otherwise set to "-"
		$status = ($statusIndex !== false) ? $kerjaList[$statusIndex] : "-";


		print ' <tr>
					<td class="Data" align="right">' . $bil . '&nbsp;</td>
					<td class="Data">
						<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMember->fields('staffID')) . '">
						<a href="' . $sFileRef . '&pk=' . tohtml($GetMember->fields('staffID')) . '">
							' . strtoupper($GetMember->fields('name')) . '
						</a>
					</td>
					<td class="Data" align="center">&nbsp;' . $GetMember->fields('staffID') . '</td>
					<td class="Data" align="center">&nbsp;' . $GetMember->fields('newIC') . '</td>
					<td class="Data" align="center">&nbsp;' . $jabatanN . '</td>
					<td class="Data" align="center">&nbsp;' . $status . '</td>
					<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetMember->fields('applyDate')) . '</td>';

		if (($IDName == 'superadmin') or ($IDName == 'admin')) {
			print '<td class="Data" align="center">
							<form method="POST" action="">
								<input type="hidden" name="delete_id" value="' . tohtml($GetMember->fields('staffID')) . '">
								<button type="submit" class="btn btn-sm btn-danger" name="delete"
									onClick="return confirm(\'Adakah anda pasti untuk padam?\');">
									<i class="fa fa-trash"></i>
								</button>
							</form>
						</td>';
		}

		print '</tr>';

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
			print '<A href="' . $sFileName . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $jabatan . '&filter=' . $filter . '">';
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
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table></td></tr></table></div>
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
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
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
				strStatus = strStatus + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="?vw=assignleave&pk=" + strStatus;
			  }
	        }
	      }
	    }

		 function ITRActionButtonClickGaji(v) {
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
				strStatus = strStatus + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="?vw=staffIncome&pk=" + strStatus;
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
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
