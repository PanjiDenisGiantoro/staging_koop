<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberProfil.php
 *          Date 		: 	26/03/2006
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
//tukar default listing view from 10 to 50 /
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "1";
if (!isset($dept))		$dept = "";
if (!isset($active))	$active = "1";
if ($filter == 1)	$active = "1";
date_default_timezone_set("Asia/Jakarta");

include("header.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
	get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$IDName = get_session("Cookie_userName");

$sSQL2 = "	SELECT *
			FROM users WHERE loginID = '" . $IDName . "'";
$rs2 = &$conn->Execute($sSQL2);

$IDGroup = $rs2->fields(groupID);

$sFileName = "?vw=memberProfil&mn=$mn";
$sFileRef  = "?vw=memberEdit&mn=$mn";
$title     = "Profil Anggota";

if (isset($_GET['action']) && $_GET['action'] == "reset" && isset($_GET['pk'])) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$pk = $_GET['pk'];

	$sWhere = " userID =" . tosql($pk, "Text");
	$sSQL	= " UPDATE users SET password= " . tosql(strtoupper(md5("koperasi123")), "Text") . " WHERE " . $sWhere;
	$rs = &$conn->Execute($sSQL);

	$strActivity = $_POST['Submit'] . ' Kata Sandi Anggota Telah Direset - ' . $pk;
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

	$codeSuratReset = 72;
	print '<script>alert("Kata sandi anggota ini telah direset menjadi \"koperasi123\"\nSilakan informasikan kepada anggota tersebut agar mengganti kata sandinya.");
			</script>';
}
//--- End   : reser based on checked box -------------------------------------------------------
//--- Prepare status MSS list
$MSSList = array("", "(MSS)");

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
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print
	'<form name="MyForm" action=' . $sFileName . ' method="post">'
	. '<input type="hidden" name="action">'
	. '<input type="hidden" name="pk" value="<?=$pk?>">'
	. '<input type="hidden" name="filter" value="' . $filter . '">'
	. '<div class="table-responsive">'
	. '<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">'
	. '<h5 class="card-title">' . strtoupper($title) . '</h5>';

print
	'<tr valign="top" class="Header">'
	. '<td align="left" >'
	. 'Pencarian Berdasarkan '
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
 			<input type="submit" class="btn btn-secondary btn-sm" value="Cari">&nbsp;&nbsp;&nbsp;		
			Cabang/Zona
			<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print	'</select>
		</td>
	</tr>
	<tr valign="top">
		<td align="left">&nbsp;
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
	'</select>&nbsp;';

if ($filter == 3) {
	print 'Status Login Sistem
				<select name="active" class="form-select-xs" onchange="document.MyForm.submit();">';
	for ($i = 0; $i < count($activeList); $i++) {
		if ($activeVal[$i] < 4) {
			print '	<option value="' . $activeVal[$i] . '" ';
			if ($active == $activeVal[$i]) print ' selected';
			print '>' . $activeList[$i];
		}
	}
	print '	</select>&nbsp;<input type="button" class="btn btn-primary btn-sm" value="Ubah" onClick="ITRActionButtonClickStatus(\'ubah\');">';
}
print '	</td>
	</tr>
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>';
//if ($filter == 3) {
if ($filter == 3) {
	print
		'<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>
						<td align="right" class="textFont">';
} else {
	print
		'<td class="textFont">&nbsp;</td><td align="right" class="textFont">';
}

echo papar_ms($pg);
print '</td>
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
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped" style="font-size:">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap>Nama</td>
						<td nowrap align="center">Nomor Anggota</td>
						<td nowrap align="center">Kartu Identitas</td>
						<td nowrap align="left">Cabang/Zona</td>
						<td nowrap align="center">Status</td>
						<!--td nowrap align="center">Tanggal Pengajuan</td-->
						<td nowrap align="center">Tanggal Keanggotaan</td>';
	if (($IDName == 'superadmin') or ($IDName == 'admin')) {
		print '<td nowrap colspan="2" align="center">&nbsp;</td>';
	}
	print '<!--td nowrap align="center">Priode Pengajuan (Hari)</td-->
					</tr>';
	while (!$GetMember->EOF && $cnt <= $pg) {
		$status = dlookup("userdetails", "status", "userID=" . tosql($GetMember->fields(userID), "Text"));
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 3) $colorStatus = "redText";

		$MSS = dlookup("userdetails", "statusMSS", "userID=" . tosql($GetMember->fields(userID), "Text"));

		//find permohonan duration

		$datetime1 = strtotime($GetMember->fields(applyDate));
		$datetime2 = strtotime($GetMember->fields(approvedDate));

		$secs = $datetime2 - $datetime1; //differences in seconds 
		$duration = (int) ($secs / 86400); //convert to days 

		$verified = $GetMember->fields(verified);

		if ($verified == 1) {
			$isVerified = ' <span class="badge badge-soft-primary">Verified</span>';
		} else {
			$isVerified = ' <span class="badge badge-soft-danger">Not Verified</span>';
		}

		print '<tr>
						<td class="Data" align="right">' . $bil . '</td>
						<td class="Data">';
		if ($filter == 3) {
			print '<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMember->fields(userID)) . '">';
		}
		print '	<a href="' . $sFileRef . '&pk=' . tohtml($GetMember->fields(userID)) . '">
							' . strtoupper($GetMember->fields(name)) . '</a>&nbsp;' . $isVerified;
		print '</td>	
						<td class="Data" align="center">' . $GetMember->fields(userID) . '</td>		
						<td class="Data" nowrap align="center">' . convertNewIC($GetMember->fields(newIC)) . '</td>
						<td class="Data" align="left">' . dlookup("general", "name", "ID=" . tosql($GetMember->fields('departmentID'), "Number")) . '</td>
						<td class="Data" align="center"><font class="' . $colorStatus . '">' . $statusList[$status] . ' ' . $MSSList[$MSS] . '</font></td>
						<!--td class="Data" align="center">' . toDate("d/m/Y", $GetMember->fields(applyDate)) . '</td-->
						<td class="Data" align="center">' . toDate("d/m/Y", $GetMember->fields(approvedDate)) . '</td>
						<!--td class="Data" align="center">' . $duration . '</td-->';
		if (($IDName == 'superadmin') or ($IDName == 'admin')) {
			if ($verified == 1) {
				print '<td>						
						<i class="mdi mdi-account-check text-primary" style="font-size: 25px;" title="Telah Disahkan"></i>
					</td>';
			} else {
				print '<td>
				<a href="javascript:void(0);"" onClick="openPopup(this);" title="Verifikasi Anggota" data-pk="' . $GetMember->fields(memberID) . '">
						<i class="mdi mdi-account-check text-warning" style="font-size: 25px;"></i>
					</a>
					</td>';
			}
			print '<td>
							<a href="?vw=memberProfil&mn=905&action=reset&pk=' . $GetMember->fields['userID'] . '" onClick="return confirm(\'Adakah anda pasti untuk set semula kata laluan anggota ini?\')" title="Set Semula Kata Laluan">
								<i class="fas fa-user-lock fa-lg text-warning" style="font-size: 18px; margin-top: 12px;"></i>
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
		print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			print '<A href="' . $sFileName . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '&filter=' . $filter . '">';
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
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Untuk ' . $title . '  -</b><hr size="1"></td></tr>';
	} else {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size="1"></td></tr>';
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
	          window.location.href ="?vw=memberAktif&mn=905&pk=" + strStatus;
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
				window.location.href = "?vw=memberAktif&mn=905&pk=" + pk;
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
				e.dept.value = "' . $dept . '";
				e.by.value = "' . $by . '";
				e.q.value = "' . $q . '";
	            e.submit();
	          }
			}
		}
	}

	function openPopup(linkElement) {
		// Get the pk value from the clicked links data-pk attribute
		let pk = linkElement.getAttribute(\'data-pk\');
		
		console.log(\'Selected PK:\', pk); // Debugging: Log the pk value to ensure its correct

		if (pk) {
			// Get screen width and height
			var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
			var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

			// Set popup size
			var width = 600;
			var height = 400;

			// Calculate the position to center the window
			var left = (screenWidth - width) / 2;
			var top = (screenHeight - height) / 2;

			// Create the popup window with the pk value in the URL
			var url = \'verify.php?pk=\' + encodeURIComponent(pk);
			console.log(\'Popup URL:\', url); // Debugging: Log the URL to be opened

			// Open the popup window
			var popup = window.open(url, \'popupWindow\', \'width=\' + width + \',height=\' + height + \',left=\' + left + \',top=\' + top + \',scrollbars=yes\');

			// Check if popup is blocked
			if (!popup) {
				alert(\'Popup was blocked. Please allow popups for this site.\');
			}
		} else {
			// If no pk value is found, show an alert
			alert(\'No valid userID (pk) found.\');
		}
	}


</script>';
