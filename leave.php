<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	leave.php
 *          Date 		: 	18/11/2024
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 10;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($filter))	$filter = "0";
if (!isset($jabatan))	$jabatan = "";
date_default_timezone_set("Asia/Jakarta");

include("header.php");
include("koperasiQry.php");

$db_koperasiID = dlookup("setup", "koperasiID", "1=1");

if (
	get_session("Cookie_groupID") <> 0 and get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $db_koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = "?vw=leave&mn=$mn";
$title     = "Senarai Permohonan Cuti";

$IDName = get_session("Cookie_userName");

if ($action == "finish") {
	$updatedBy = get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");

	if (!empty($pk)) {
		for ($i = 0; $i < count($pk); $i++) {
			$id = intval($pk[$i]);

			$sSQL = "UPDATE sleave
                     SET status = 4, 
						 isSelesai = 1, 
                         selesaiBy = '" . addslashes($updatedBy) . "', 
                         selesaiDate = '" . addslashes($updatedDate) . "' 
                     WHERE leaveID = $id";

			$rs = $conn->Execute($sSQL);
			if (!$rs) {
				echo '<script>alert("Error updating record: ' . $conn->ErrorMsg() . '");</script>';
				exit;
			}
		}
		echo '<script>alert("PERMOHONAN TELAH SELESAI"); window.location.href = $sFileName;</script>';
	} else {
		echo "No user selected for updating.";
	}
}


//--- Begin : deletion based on checked box -------------------------------------------------------
if (isset($_POST['delete'])) {
	$recordID = intval($_POST['delete_id']);

	if ($recordID > 0) {
		$sSQL = "DELETE FROM sleave WHERE leaveID = " . tosql($recordID, "Number") . ";";
		$rs = $conn->Execute($sSQL);

		if (!$rs) {
			die("SQL Error: " . $conn->ErrorMsg());
		} else {
			echo '<script>alert("Rekod berjaya dipadam.");</script>';
			echo '<script>window.location.href = "' . $sFileName . '";</script>';
			exit();
		}
	} else {
		echo '<script>alert("ID rekod tidak sah.");</script>';
	}
}


// Function to count leave days excluding weekends
function countLeaveDaysExcludingWeekends($startDate, $endDate)
{
	$startTimestamp = strtotime($startDate);
	$endTimestamp = strtotime($endDate);
	$daysCount = 0;

	while ($startTimestamp <= $endTimestamp) {
		$dayOfWeek = date('N', $startTimestamp);  // 1 = Monday, 7 = Sunday
		if ($dayOfWeek < 6) {  // If it's not Saturday (6) or Sunday (7)
			$daysCount++;
		}
		$startTimestamp = strtotime("+1 day", $startTimestamp);
	}

	return $daysCount;
}

if ($action == "batal") {
	$updatedBy = get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");

	if (!empty($pk)) {
		$pkList = implode(",", array_map('intval', $pk));

		$sSQL = "SELECT * FROM sleave WHERE leaveID IN ($pkList)";
		$rsLeave = $conn->Execute($sSQL);

		if ($rsLeave) {
			$conn->BeginTrans();
			try {
				while (!$rsLeave->EOF) {
					$leaveID = $rsLeave->fields['leaveID'];
					$leaveType = $rsLeave->fields['leaveType'];
					$startLeave = $rsLeave->fields['startLeave'];
					$endLeave = $rsLeave->fields['endLeave'];
					$userID = $rsLeave->fields['userID'];
					$totalHour = $rsLeave->fields['total_hour'];

					if ($leaveType == 2063) {
						$leaveDays = $totalHour; // Time Off should be deducted in hours
					} else {
						$leaveDays = countLeaveDaysExcludingWeekends($startLeave, $endLeave);
					}

					// Fetch current balance from sleave_details table
					$sSQLUsage = "SELECT balanceLeave 
                                  FROM sleave_details 
                                  WHERE userID = " . tosql($userID, "Text") . " 
                                  AND leaveTypeID = " . tosql($leaveType, "Text");

					$rsUsage = $conn->Execute($sSQLUsage);

					if ($rsUsage && !$rsUsage->EOF) {
						$currentBalance = $rsUsage->fields['balanceLeave'];

						// Update the balanceLeave in the sleave_details table
						$updateBalanceSQL = "UPDATE sleave_details 
                                             SET balanceLeave = balanceLeave + $leaveDays 
                                             WHERE userID = " . tosql($userID, "Text") . " 
                                             AND leaveTypeID = " . tosql($leaveType, "Text");
						$conn->Execute($updateBalanceSQL);
					} else {
						echo '<script>alert("Error: Could not fetch leave details for userID ' . $userID . ' and leaveTypeID ' . $leaveType . '");</script>';
					}

					// Update the leave record to mark it as canceled
					$sSQLUpdate = "UPDATE sleave  
                                   SET status = 3, 
                                       isCancel = 1, 
                                       cancelNote = " . tosql($sebab, "Text") . ", 
                                       cancelBy = " . tosql($updatedBy, "Text") . ", 
                                       cancelDate = " . tosql($updatedDate, "Text") . " 
                                   WHERE leaveID = " . tosql($leaveID, "Text");

					$result = $conn->Execute($sSQLUpdate);

					if (!$result) {
						echo '<script>alert("Error updating leave record for leaveID ' . $leaveID . '");</script>';
					}

					$rsLeave->MoveNext();
				}

				$conn->CommitTrans();
				echo '<script>alert("PERMOHONAN BERJAYA DIBATALKAN"); window.location.href = "?vw=leave&mn=933";</script>';
			} catch (Exception $e) {
				$conn->RollbackTrans();
				echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
			}
		} else {
			echo '<script>alert("Error: Could not retrieve leave records.");</script>';
		}
	} else {
		echo '<script>alert("No leave application selected for cancellation.");</script>';
	}
}


//--- Prepare jabatan list
$jabatanList = array();
$jabatanVal  = array();
$sSQL = "SELECT b.jabatanID, g.code as jabatanCode, g.name as jabatanName
         FROM staff b
         JOIN general g ON b.jabatanID = g.ID
         WHERE g.category = 'W'
         GROUP BY b.jabatanID";

$rs	= &$conn->Execute($sSQL);
if ($rs->RowCount()	<> 0) {
	while (!$rs->EOF) {
		array_push($jabatanList, $rs->fields(jabatanName));
		array_push($jabatanVal, $rs->fields(jabatanID));
		$rs->MoveNext();
	}
}

$sWhere = " WHERE 1=1 ";

if ($jabatan !== "") {
	$sWhere .= " AND b.jabatanID = " . intval($jabatan);
	$sWhere .= " AND a.staffID = b.staffID";
}

if ($filter != "ALL") {
	$sWhere .= " AND c.status = " . tosql($filter, "Number");
}

// Leave status filter
// if ($statusFilter != "ALL") {
//     $sWhere .= " AND c.status = " . tosql($statusFilter, "Number");
// }

if (!empty($jabatan)) {
	$sWhere .= " AND b.jabatanID = " . intval($jabatan);
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.staffID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND b.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '%" . $q . "%'";
	}
}

$sSQL = "SELECT 
            a.name AS staffName, 
            g.name AS leaveTypeName, 
            c.leave_img AS document, 
            c.startLeave, 
            c.endLeave, 
            c.applyDate, 
            c.status,
			c.leaveID,
			c.userID
        FROM users a 
        JOIN staff b ON a.staffID = b.staffID 
        JOIN sleave c ON a.userID = c.userID 
        JOIN general g ON c.leaveType = g.ID";
$sSQL .= $sWhere;
$sSQL .= " ORDER BY c.applyDate ASC";

$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
<h5 class="card-title">' . strtoupper($title) . '</h5>
    
<div class="mb-3 row m-1">
<div>Carian
			<select name="by" class="form-select-sm mt-3">';
if ($by == 1)	print '<option value="1" selected>Nombor Staf</option>';
else print '<option value="1">Nombor Staf</option>';
if ($by == 2)	print '<option value="2" selected>Nama Staf</option>';
else print '<option value="2">Nama Staf</option>';
if ($by == 3)	print '<option value="3" selected>Kartu Identitas</option>';
else print '<option value="3">Kartu Identitas</option>';
print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="20" class="form-control-sm mt-3">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;		
			</div>
</div>

<div class="mb-3 row m-1">
<div>
Jenis
			<select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
print '<option value="ALL">Semua';
for ($i = 0; $i < count($cutiList); $i++) {
	if ($cutiVal[$i] < 5 || $cutiVal[$i] == 9) {
		print '	<option value="' . $cutiVal[$i] . '" ';
		if ($filter == $cutiVal[$i]) print ' selected';
		print '>' . $cutiList[$i];
	}
}
// }
print '	</select>&nbsp;';

if (($IDName == 'superadmin') or ($IDName == 'admin')) {

	if ($filter < 1) {

		print '&nbsp;<input type="button" class="btn btn-sm btn-primary" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');">';
	}
	if ($filter > 0 && $filter < 4) {
		print '<div style="margin-top: 1px;"></div>';
		print 'Pengesahan Permohonan <input type="button" class="btn btn-sm btn-success" value="Selesai" onClick="ITRActionButtonFinish(\'finish\');"></div></div>';
	}
	if ($filter == 1) print '<div>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batal Kelulusan :&nbsp;<input type="button" class="btn btn-sm btn-secondary" value="Batal" onClick="ITRActionButtonClick(\'batal\');">&nbsp;Sebab:&nbsp;<input type="textx" name="sebab" value="" maxlength="60" size="50" class="Data form-controlx">
	</div>';
}
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
						<td nowrap align="left">Nama Staf</td>
						<td nowrap align="left">Jenis Cuti</td>
						<td nowrap align="center">Dokumen</td>
						<td nowrap align="center">Jabatan</td>
						<td nowrap align="center">Tarikh Mula</td>
						<td	nowrap align="center">Tarikh Tamat</td>
						<td nowrap align="center">Status</td>
						<td nowrap align="center"></td> 
						
					</tr>';
	while (!$GetMember->EOF && $cnt <= $pg) {

		$userID = $GetMember->fields('userID');
		$staffID = dlookup("users", "staffID", "userID=" . $userID);
		$leaveTypeName = $GetMember->fields('leaveType');
		$startLeave = $GetMember->fields('startLeave');
		$endLeave = $GetMember->fields('endLeave');
		$status = $GetMember->fields('status');
		$jabatan = dlookup("staff", "jabatanID", "staffID=" . $staffID);
		$jabatanName = dlookup("general", "name", "ID=" . $jabatan);

		if (empty($jabatanName)) {
			$jabatanName = "-";
		}

		$colorStatus = "Data";
		if ($status == 0) $colorStatus = "text-success";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		if ($status == 3) $colorStatus = "blackText";
		if ($status == 4) $colorStatus = "text-info";

		$pic = trim($GetMember->fields('document')); // Trim to avoid hidden spaces
		$pic = strtolower($pic); // Convert to lowercase for case-sensitive servers

		// Define expected file path
		$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/stagingtri/upload_leave/";
		$filePath = $uploadDir . $pic;



		print '<tr>
						<td><input type="checkbox" class="selectItem form-check-input" name="pk[]" value="' . $GetMember->fields('leaveID') . '"></td>
						<td align="left">' . strtoupper($GetMember->fields('staffName')) . '</td>
						<td align="left">' . $GetMember->fields('leaveTypeName') . '</td>
						<td align="center">';

		if (!empty($pic) && file_exists($filePath)) {
			print '<button class="btn btn-outline-danger" 
								onClick="window.open(\'upload_leave/' . $pic . '\', 
								\'pop\', \'top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
								<i class="far fa-file-pdf text-danger"></i> Paparan Fail</button>';
		} else {
			print '<span class="text-danger">Tiada Dokumen</span>';
		}

		print '</td>
						<td class="Data" align="center">&nbsp;' . $jabatanName . '</td>
						<td align="center">' . date("d/m/Y", strtotime($startLeave)) . '</td>
						<td align="center">' . (!empty($endLeave) ? date("d/m/Y", strtotime($endLeave)) : '-') . '</td>
						<td align="center"><span class="' . $colorStatus . '">' . $cutiList[$status] . '</span></td>';
		if ($IDName == 'superadmin' || $IDName == 'admin') {
			print '<td class="Data" align="center">
								<form method="POST" action="">
									<input type="hidden" name="delete_id" value="' . $GetMember->fields('leaveID') . '">
									<button type="submit" class="btn btn-sm btn-danger" name="delete"
										onClick="return confirm(\'Adakah anda pasti untuk padam?\');">
										<i class="fa fa-trash"></i> 
									</button>
								</form>
							</td>';
		}

		$cnt++;
		$GetMember->MoveNext();
	}

	if ($GetMember->RecordCount() == 0) {
		print '<tr><td colspan="7" align="center">- Tiada Rekod Dijumpai -</td></tr>';
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
	if ($q == '') {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size="1"></td></tr>';
	} else {
		print '<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size="1"></td></tr>';
	}
}
print ' 
	</table></td></tr></table></div>
	</form>';

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
	
		function toggleSelectAll(source) {
		const checkboxes = document.querySelectorAll(".selectItem");
		checkboxes.forEach(checkbox => checkbox.checked = source.checked);
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
		var strStatus = "";
		e = document.MyForm;
		if (e == null) {
			alert("Sila pastikan nama form diwujudkan.!");
		} else {
			count = 0;
			j = 0;
			for (c = 0; c < e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					pk = e.elements[c].value;
					strStatus = strStatus  + pk;
					count++;
				}
			}
	
			if (count == 0) {
				alert("Sila pilih rekod yang hendak di" + v + "kan.");
			} else {
				if (confirm(count + " rekod hendak di" + v + "kan?")) {
					window.location.href = "?vw=leaveStatus&pk=" + strStatus;
				}
			}
		}
	}
									
	
		function doListAll() {
			c = document.forms[\'MyForm\'].pg;
			document.location = "' . $sFileName . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
		}
	
		function ITRActionButtonFinish(v) {
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
			alert(\'Sila pilih rekod yang hendak diselesaikan.\');
			} else {
			if(confirm(count + \' rekod hendak diselesaikan?\')) {
				e.action.value = v;
				e.submit();
			}
			}
		}
		}	 
</script>';
include("footer.php");
