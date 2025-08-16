<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	leaveStatus.php
*		   Description	:   Update member status
*          Date 		: 	19/11/2024
*********************************************************************************/
include("header.php");	
include("koperasiQry.php");	
include ("koperasiList.php");	
date_default_timezone_set("Asia/Kuala_Lumpur");

if (get_session("Cookie_koperasiID") <> 0 ) {
	print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}

if (!isset($strDate)) {
    $strDate = date("d/m/Y");
}


if ($action == 'Kemaskini') {
    $pk = explode(",", $pk);  
    $str = array();
    $updatedBy = get_session("Cookie_userName");
    foreach ($pk as $val) {
        if ($val) $str[] = "'" . intval($val) . "'"; 
    }

    $pk = implode(",", $str);  

    if ($selStatus != 0) {
        $strDate = date("Y-m-d H:i:s");
    
        $sSQL = 'UPDATE sleave';  
    
        if ($selStatus == 1) {  
            $sSQL .= ' SET isApproved = 1, ';
            $sSQL .= ' approvedDate = ' . tosql($strDate, "Text") . ', ';
            $sSQL .= ' updatedBy = ' . tosql($updatedBy, "Text") . ', ';
            $sSQL .= ' remark = ' . tosql($remark, "Text");
        }
        if ($selStatus == 2) {  
            $sSQL .= ' SET isRejected = 1, ';
            $sSQL .= ' rejectedDate = ' . tosql($strDate, "Text") . ', ';
            $sSQL .= ' updatedBy = ' . tosql($updatedBy, "Text") . ', ';
            $sSQL .= ' remark = ' . tosql($remark, "Text");
        }
    
        $sSQL .= ', status = ' . tosql($selStatus, "Number");
        $sSQL .= ' WHERE leaveID IN (' . $pk . ')';
        $rs = $conn->Execute($sSQL);  
    
        if ($selStatus == 1) {
            $sSQL = "SELECT * FROM sleave WHERE leaveID IN (" . $pk . ")";
            $rsLeave = $conn->Execute($sSQL);
    
            while (!$rsLeave->EOF) {
                $leaveTypeID = $rsLeave->fields['leaveType'];
                $startLeave = $rsLeave->fields['startLeave'];
                $endLeave = $rsLeave->fields['endLeave'];
                $userID = $rsLeave->fields['userID'];  
                $totalHour = $rsLeave->fields['total_hour']; 
    
                if ($leaveTypeID == 2063) {
                    $leaveDays = $totalHour; // Time Off should be deducted in hours
                } else {
                    $leaveDays = countLeaveDaysExcludingWeekends($startLeave, $endLeave);
                }
    
                // Fetch current balance for the specific leave type
                $sSQL = "SELECT balanceLeave FROM `sleave_details` 
                         WHERE userID = " . tosql($userID, "Text") . " 
                         AND leaveTypeID = " . tosql($leaveTypeID, "Number");
                $rsDetails = $conn->Execute($sSQL);
    
                if ($rsDetails && !$rsDetails->EOF) {
                    $currentBalance = $rsDetails->fields['balanceLeave'];
    
                    // Calculate the new balance
                    $newBalance = $currentBalance - $leaveDays;
                    if ($newBalance < 0) {
                        $newBalance = 0;
                    }
    
                    // Update the balance in the sleave_details table
                    $sSQL = "UPDATE `sleave_details` 
                             SET balanceLeave = " . tosql($newBalance, "Number") . " 
                             WHERE userID = " . tosql($userID, "Text") . " 
                             AND leaveTypeID = " . tosql($leaveTypeID, "Number");
                    $rsUpdate = $conn->Execute($sSQL);
    
                    if (!$rsUpdate) {
                        echo '<script>alert("Error updating leave balance: ' . $conn->ErrorMsg() . '");</script>';
                        exit;
                    }
                } else {
                    echo '<script>alert("Error fetching leave balance for update.");</script>';
                    exit;
                }
    
                $rsLeave->MoveNext();  
            }
        }
        alert("Permohonan Cuti berjaya dikemaskini.");
        gopage("?vw=leave&mn=933", 1000);
    }   
} 
$title = "Status Permohonan Cuti";

// Debug: Print the pkall array for reference
// echo "<pre>";
// print_r($pkall);
// echo "</pre>";

if(isset($pk)) $pkall = explode(":",$pk);
unset($pk);
?>

<div class="table-responsive">
<h5 class="card-title"><?php echo strtoupper($title);?></h5>
<form name="MyForm" action="?vw=leaveStatus&mn=933" method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="<?php print implode(",",$pkall);?>">
<table class="table" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
<h6 class="card-subtitle"><b>STATUS STAF</h6>
<tr>
<table border="0" cellspacing="6" cellpadding="3" width="100%" align="center">
<?php
for ($s = 0; $s < count($pkall); $s++) {
    $pk = $pkall[$s];

    $sSQL = "SELECT * FROM sleave WHERE leaveID = '" . $pk . "'";
    $GetMember = $conn->Execute($sSQL);

    if ($GetMember && !$GetMember->EOF) {
        // If there is data, display leave information
        if (isset($StartRec) && $StartRec > 0) {
            $GetMember->Move($StartRec - 1);
        }

        echo "<tr><td>Nombor Staf</td><td>&nbsp;" . dlookup("users", "staffID", "userID=" . tosql($GetMember->fields['userID'], "Text")) . "</td></tr>";
        echo "<tr><td>Nama Staf</td><td>&nbsp;" . dlookup("users", "name", "userID=" . tosql($GetMember->fields['userID'], "Text")) . "</td></tr>";
        echo "<tr><td>Tarikh Mohon</td><td>&nbsp;" . toDate("d/m/Y", $GetMember->fields['applyDate']) . "</td></tr>";
        echo "<tr><td>Jenis Cuti Dipohon</td><td>&nbsp;" . dlookup("general", "name", "ID=" . tosql($GetMember->fields['leaveType'], "Text")) . "</td></tr>";
        echo "<tr><td>Tarikh Mula</td><td>&nbsp;" . toDate("d/m/Y", $GetMember->fields['startLeave']) . "</td></tr>";

        $leaveEndInfo = ($GetMember->fields['leaveType'] == 2063) 
            ? $GetMember->fields['total_hour'] . " Jam" 
            : toDate("d/m/Y", $GetMember->fields['endLeave']);
        echo "<tr><td>" . (($GetMember->fields['leaveType'] == 2063) ? "Jumlah Jam" : "Tarikh Tamat") . "</td><td>&nbsp;" . $leaveEndInfo . "</td></tr>";

        echo "<tr><td>Keterangan</td><td>&nbsp;" . $GetMember->fields['reason'] . "</td></tr>";
        echo "<tr><td colspan='2'><hr size=1></td></tr>";
    } else {
        // If no data found, display this message
        echo "<tr><td colspan='3' align='center' height='50' valign='middle'>- Tiada Maklumat Mengenai Permohonan Cuti -</td></tr>";
    }
}


$status = $GetMember->fields['status'];

if (count($cutiList) <> 0) {  
	if ($status == 0) {
?>
<td>Status</td><td>
<select class="form-selectx" name="selStatus">
<?php
	for ($i = 0; $i < count($cutiList); $i++) {
		if ($cutiVal[$i] < 3)
			print '<option value="'.$cutiVal[$i].'">'.$cutiList[$i];
	}
?>
</select>
</td>
</tr>
<?php
	} else {
		if ($status == 1) {
?>
<tr>
<td>Status</td>
<td>:
&nbsp;<font class="greenText"><?php print $cutiList[$status];?></font>
</td>
</tr>
<tr>
<td>Tarikh Diluluskan</td>
<td>:&nbsp;&nbsp;<?php echo date("d/m/Y", strtotime($GetMember->fields('approvedDate'))); ?></td>
</tr>
<?php
		}
		if ($status == 2) {
?>
<tr>
<td>Status</td>
<td>:&nbsp;<font class="redText"><?php print $cutiList[$status];?></font></td>
</tr>
<tr>
<td>Tarikh Ditolak</td>
<td>:&nbsp;&nbsp;<?php echo date("d/m/Y", strtotime($GetMember->fields('rejectedDate'))); ?></td>
</tr>
<?php
		}
		if ($status == 3 OR $status == 4) {
?>
<tr>
<td>Status</td>
<td>:&nbsp;<?php print $cutiList[$status];?></td>
</tr>
<?php
		}
	}
	if ($status == 0) {
?>
<tr>
<td>Tarikh Kemaskini</td>
<td><input type="text" class="form-controlx" name="strDate" value="<?php print $strDate;?>" size="15" maxlength="10"></td>
</tr>
<tr>
    <td>Catatan</td>
    <td>
        <textarea class="form-controlx" name="remark" rows="3" cols="50" maxlength="100"></textarea>
    </td>
</tr>
<tr>
<td colspan="2" align="center">
<div>&nbsp;</div>
<input type="submit" name="action" class="btn btn-md btn-primary" value="Kemaskini">
&nbsp;
<input type="button" name="batal" value="Kembali"  class="btn btn-md btn-danger" onclick= "Javascript:(window.location.href='?vw=leave&mn=933')">
<div>&nbsp;</div>
</td>
</tr>
<?php
	} else {
?>
<tr>
<td>Catatan</td>
<td>:&nbsp;&nbsp;<?php print $remark;?></td>
</tr>
<tr>
<td colspan="2" align="center">
<div>&nbsp;</div>
<input type="button" name="batal" value="Kembali"  class="btn btn-md btn-primary" onclick= "Javascript:(window.location.href='?vw=leave&mn=933')">
</td>
</tr>
<?php
	}
} else { 
?>
<tr>
<td colspan="3" align="center"><hr size="1"><b>- Tiada rekod mengenai status  -</b><hr size="1"></td>
</tr>
<?php
}

?>
<!-- </table> -->
</td>
</tr>
</table>
</form>
</div>
<?

function countLeaveDaysExcludingWeekends($startDate, $endDate) {
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

include("footer.php");	
?>