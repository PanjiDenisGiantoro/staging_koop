<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberT.php
 *          Date 		: 	
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 10;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "0";
if (!isset($dept))		$dept = "";
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
	get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = '?vw=memberT&mn=905';
$sFileRef  = '?vw=memberEditT&mn=905';
$title     = "Senarai Permohonan Berhenti/Bersara";

//--- Begin : deletion based on checked box -------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['pk'])) {
	$pk = $_GET['pk'];

	$sWhere = "userID=" . tosql($pk, "Text");
	$sSQL = "DELETE FROM userterminate WHERE " . $sWhere;
	$rs = &$conn->Execute($sSQL);

	$strActivity = $_POST['Submit'] . ' Anggota Berhenti Telah Dihapuskan - ' . $pk;
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
}
//--- End   : deletion based on checked box -------------------------------------------------------

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
array_push($deptList, "Bersara");
array_push($deptVal, "BSR");
if ($dept == "BSR") {
	$filter = 4;
	$dept = "";
}

//$GetMember = ctMemberTerminateStatus($q,$by,$filter,$dept);
$sSQL = "";
$sWhere = " a.userID = b.userID AND a.userID=c.userID and c.status = " . tosql($filter, "Number");
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}
if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.memberID like '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '" . $q . "'";
	}
}
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*,c.*
			 FROM 	users a, userdetails b, userterminate c";
$sSQL = $sSQL . $sWhere . ' ORDER BY CAST( b.memberID AS SIGNED INTEGER ), c.applyDate DESC';

$GetMember = &$conn->Execute($sSQL);

$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="d-flex justify-content-between align-items-center mb-1">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Mohon Baru" onClick="window.location.href=\'?vw=memberApplyTP&mn=905\'"/>
</div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
';
print '<div class="mb-3 row m-1">
<div>
			Carian Melalui 
			<select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kad Pengenalan</option>';
else print '<option value="3">Kad Pengenalan</option>';
print '		</select>
			<input type="text" name="q" value="" class="form-control-sm" maxlength="50" size="20" class="Data">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;		
			Cawangan/Zon
			<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}

print '	</select>
        </div>
</div>
	<div class="mb-3 row m-1">
<div>&nbsp;
			Jenis
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
for ($i = 0; $i < count($statusList); $i++) {
	if ($i == 0 || $i == 3 || $i == 4) {
		if ($statusVal[$i] < 5) {
			print '	<option value="' . $statusVal[$i] . '" ';
			if ($filter == $statusVal[$i]) print ' selected';
			print '>' . $statusList[$i];
		}
	}
}
print '		</select>&nbsp;';

print '
<!--input type="button" class="btn btn-sm btn-primary" value="Status" onClick="ITRActionButtonStatus();"-->
<input type="button" class="btn btn-sm btn-primary" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');">';

print '</div>
</div>

			<table width="100%">
				<tr>
<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All</td>					<td align="right" class="textFont">
						Paparan <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
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
print '				</select>setiap mukasurat.
					</td>
				</tr>
			</table>
		';


if ($GetMember->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	$status = dlookup("userterminate", "status", "userID=" . tosql($GetMember->fields(userID), "Text"));
	print '
	   
				<div class="table-responsive">    
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap align="center">&nbsp;</td>
						<td nowrap>Nombor - Nama Anggota</td>
						<td nowrap align="center">Jenis</td>
						<td nowrap>Cawangan/Zon</td>
						<td nowrap align="right">Yuran/Syer Terkumpul (RM)</td>
						<td nowrap align="right">Baki Pinjaman (RM)</td>
						<td nowrap align="center">Status</td>
						<td nowrap align="center">Tarikh Memohon</td>';
	if (($IDName == 'superadmin') or ($IDName == 'admin') and ($status == 0)) {
		print '<td>&nbsp;</td>';
	}
	print '</tr>';
	while (!$GetMember->EOF && $cnt <= $pg) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$totYr = getFees($GetMember->fields(userID));
		$totSh = getSharesterkini($GetMember->fields(userID), $yr);
		$sqlBal = "SELECT sum( outstandingAmt ) AS bal FROM `loans` WHERE status = 3 and userID =" . tosql($GetMember->fields(userID), "Text");
		$rsBal = &$conn->Execute($sqlBal);
		$balLoan = $rsBal->fields('bal');
		$totYrSh = $totYr + $totSh;
		$status = $GetMember->fields(status);
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		if ($status == 0) $colorStatus = "text-info";
		$type = $GetMember->fields(type);
		print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMember->fields(userID)) . '">
						' . $GetMember->fields(memberID) . ' - 
						' . $GetMember->fields(name) . '
						</td>
						<td class="Data" align="center"><font class="' . $colorStatus . '">' . $terminateList[$type] . '</font></td>
						<td class="Data">' . dlookup("general", "name", "ID=" . tosql($jabatan, "Number")) . '</td>
						<td class="Data" align="right">' . number_format($totYrSh, 2) . '</td>						
						<td class="Data" align="right">' . number_format($balLoan, 2) . '</td>						
						<td class="Data" align="center"><font class="' . $colorStatus . '">' . $statusList[$status] . '</font></td>
						<td class="Data" align="center">' . toDate("d/m/Y", $GetMember->fields(applyDate)) . '</td>';
		if (($IDName == 'superadmin') or ($IDName == 'admin') and ($status == 0)) {
			print '<td>
							<a href="?vw=memberT&mn=905&action=delete&pk=' . $GetMember->fields['userID'] . '" onClick="return confirm(\'Adakah anda pasti untuk hapus anggota ini?\')" title="Hapus">
								<i class="fas fa-trash-alt fa-lg text-danger" style="display: inline-flex; align-items: center; justify-content: center;"></i>
							</a>							
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
		print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&filter=' . $filter . '">';
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
			<td class="textFont">Jumlah Rekod : <b>' . $GetMember->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b>- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b>- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
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
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
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
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="?vw=memberStatusT&mn=905&pk=" + strStatus;
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
				window.location.href = "?vw=memberStatusT&mn=905&pk=" + pk;
			}
		}
	}


	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
