<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	admin.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "0";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>parent.location.href = "index.php";</script>';
}
$sFileName = "?vw=admin&mn=$mn";
$sFileRef  = "?vw=adminEdit&mn=mn";
$title     = "Senarai Admin";

//--- Begin : deletion based on checked box -------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['pk'])) {
	$updatedBy   = get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$pk = $_GET['pk'];

	$sWhere = "userID=" . tosql($pk, "Text");
	$sSQL = "UPDATE users SET isActive = 0 WHERE " . $sWhere;
	$rs = &$conn->Execute($sSQL);

	$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
		" VALUES ('Batal Kakitangan - $pk', 'INSERT', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "', '" . $updatedDate . "', '" . $updatedBy . "', '9')";
	$rs = &$conn->Execute($sqlAct);
}

//--- End   : deletion based on checked box -------------------------------------------------------

//--- Begin : reset based on checked box -------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == "reset" && isset($_GET['pk'])) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$pk = $_GET['pk'];

	$sWhere = " userID =" . tosql($pk, "Text");
	$sSQL	= " UPDATE users SET password= " . tosql(strtoupper(md5("staf123")), "Text") . " WHERE " . $sWhere;
	$rs = &$conn->Execute($sSQL);

	$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
		" VALUES ('Set Semula Kata Laluan Staf - $pk', 'INSERT', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . $updatedDate . "', '" . $updatedBy . "', '9')";
	$rs = &$conn->Execute($sqlAct);
	print '<script>alert("Katalaluan staf ini telah diset semula kepada \"staf123\"\n.");</script>';
}
//--- End   : reser based on checked box -------------------------------------------------------

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

$SQLdpt = "";
$SQLdpt	= "SELECT * FROM `users` WHERE groupID in (1,2) and isActive = 1 and loginID <> 'superadmin' ORDER BY applyDate DESC";
$GetAdmin = &$conn->Execute($SQLdpt);
$GetAdmin->Move($StartRec - 1);

$TotalRec = $GetAdmin->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="<?=$pk?>">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="table-responsive">
<table border="0" cellspacing="3" cellpadding="3" width="100%" align="center">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Pengajuan Baru" onClick="window.location.href=\'?vw=addAdmin&mn=928\'"/>
</div>';

if ($GetAdmin->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap>Nama</td>
						<td nowrap>Id Log Masuk</td>
						<td nowrap>Emel</td>
						<td nowrap align="center">Jenis Capaian</td>
						<td nowrap align="center">Keanggotaan</td>
						<td nowrap align="center">Tarikh Didaftar</td>
						<td colspan="2" nowrap align="center">&nbsp;</td>
					</tr>';
	while (!$GetAdmin->EOF && $cnt <= $pg) {
		$status = dlookup("userdetails", "status", "userID=" . tosql($GetAdmin->fields(userID), "Text"));
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		print ' <tr class="table-light">
						<td class="Data" align="right">' . $bil . '</td>
						<td class="Data" style="text-transform:uppercase"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetAdmin->fields(userID)) . '">
							<a href="' . $sFileRef . '&pk=' . tohtml($GetAdmin->fields(userID)) . '">
							' . $GetAdmin->fields(name) . '</td>
						<td class="Data">' . $GetAdmin->fields(loginID) . '</td>
						<td class="Data">' . $GetAdmin->fields(email) . '</td>
						<td class="Data" align="center">';
		if ($GetAdmin->fields(groupID) == 1) print 'Staf';
		else print 'Pengurus';
		print '</td>
						<td class="Data" align="center">';
		if ($GetAdmin->fields(memberID) <> '') print 'Anggota';
		else print 'Bukan';
		print '</td>
						<td class="Data" align="center">' . toDate("d/m/Y", $GetAdmin->fields(applyDate)) . '</td>
						<td>
							<a href="?vw=admin&mn=928&action=delete&pk=' . $GetAdmin->fields['userID'] . '" onClick="return confirm(\'Adakah anda pasti untuk hapus kakitangan ini?\')" title="Hapus">
								<i class="fas fa-trash-alt fa-lg text-danger"></i>
							</a>
						</td>
						<td>
							<a href="?vw=admin&mn=928&action=reset&pk=' . $GetAdmin->fields['userID'] . '" onClick="return confirm(\'Adakah anda pasti untuk set semula kata laluan kakitangan ini?\')" title="Set Semula Kata Laluan">
								<i class="fas fa-user-lock fa-lg text-warning"></i>
							</a>
						</td>
					</tr>';
		$cnt++;
		$bil++;
		$GetAdmin->MoveNext();
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
		print '<tr><td class="textFont" valign="top" align="left" ">Data Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&filter=' . $filter . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp; ';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Data : <b>' . $GetAdmin->RowCount() . '</b></td>
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
				strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="memberStatus.php?pk=" + strStatus;
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
		document.location = "' . $sFileName . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
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
				//e.dept.value = "' . $dept . '";
				//e.by.value = "' . $by . '";
				//e.q.value = "' . $q . '";
	            e.submit();
	          }
			}
		}
	}
</script>';
