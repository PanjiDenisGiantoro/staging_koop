<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	carumanStatus.php
 *		   Description	:   Update caruman status
 *          Date 		: 	22/03/2024
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("koperasiList.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

if (get_session("Cookie_groupID") == 0) $staff = 0;
else $staff = 1;
if (!isset($strDate))	$strDate = date("d/m/Y");
$groupid = get_session("Cookie_userID");
$title = "Status Permohonan Pengeluaran Caruman";

function ctMemberCarumanLocal($id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($id <> "ALL") {
		$sWhere = " ID = " . tosql($id, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = " SELECT * FROM usercaruman ";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

if (isset($_POST['action']) && $_POST['action'] == 'Kemaskini') {
	$pk = explode(",", $pk);
	$str = array();
	foreach ($pk as $val) {
		if ($val <> '') $str[] = "'" . $val . "'";
	}
	$pk = implode(",", $str);

	if ($selStatus <> 0) { // aside from status proses semakan
		$strDate = substr($strDate, 6, 4) . '-' .
			substr($strDate, 3, 2) . '-' .
			substr($strDate, 0, 2);
		$updatedDate = date("Y-m-d H:i:s");

		$sSQL = '';
		$sWhere = '';

		$sWhere = ' ID in (' . $pk . ')';
		$sSQL = ' UPDATE usercaruman ';

		$updateSuccess = false;

		if ($selStatus == 1) { // set to lulus & proses baucer
			$sSQL1 = $sSQL . ' SET isApproved = 1 ' .
				' ,approvedDate=' . tosql($strDate, "Text") .
				' ,updatedBy=' . tosql($groupid, "Text") .
				' ,remark=' . tosql($remark, "Text") .
				' ,status= 1 ';
			$sSQL1 .= ' WHERE ' . $sWhere;

			$updateSuccess = $conn->Execute($sSQL1);

			for ($j = 0; $j < count($str); $j++) {
				$pk = $str[$j];
				$type = dlookup("usercaruman", "type", "userID=" . $pk);

				$sWhere = ' userID = ' . $pk;
				$sSQL = ' UPDATE userdetails SET status = ' . $status;
				$sSQL .= ' WHERE ' . $sWhere;
				$conn->Execute($sSQL);
			}
		}

		if ($selStatus == 3) { // set to selesai
			$sSQL1 = $sSQL . ' SET' .
				' updatedDate=' . tosql($updatedDate, "Text") .
				' ,updatedBy=' . tosql($groupid, "Text") .
				' ,remark=' . tosql($remark, "Text") .
				' ,status= 3 ';
			$sSQL1 .= ' WHERE ' . $sWhere;

			$updateSuccess = $conn->Execute($sSQL1);

			for ($j = 0; $j < count($str); $j++) {
				$pk = $str[$j];
				$type = dlookup("usercaruman", "type", "userID=" . $pk);

				$sWhere = ' userID = ' . $pk;
				$sSQL = ' UPDATE userdetails SET status = ' . $status;
				$sSQL .= ' WHERE ' . $sWhere;
				$conn->Execute($sSQL);
			}
		}

		if ($selStatus == 2) { // set to reject
			$sSQL .= ' SET isRejected = 1 ' .
				' ,rejectedDate=' . tosql($strDate, "Text") .
				' ,updatedBy=' . tosql($groupid, "Text") .
				' ,remark=' . tosql($remark, "Text") .
				' ,status= 2 ';
			$sSQL .= ' WHERE ' . $sWhere;
			$updateSuccess = $conn->Execute($sSQL);
		}

		$message = $updateSuccess ? "Kemaskini Berjaya" : "Kemaskini Tidak Berjaya";

		echo "
        <script>
            alert('$message');
            window.location = '?vw=carumanList&mn=905';
        </script>";
		exit;
	}
}


// Define a named function to filter out empty values
function filterEmptyStrings($value)
{
	return trim($value) !== '';
}

if ($staff) {
	if (isset($pk)) {
		// Filter out empty strings using the named function
		$pkall = array_filter(explode(":", $pk), 'filterEmptyStrings');
	}
	unset($pk);
}

print '
<h5 class="card-title">' . strtoupper($title) . '</h5>
<div style="width: 350px; text-align:left">
<form name="MyForm" action="" method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="' . implode(",", $pkall) . '">
<table class="lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="left">

	<tr class="card-body bg-light">
	<td class="borderleftrightbottomteal">
	<table border="0" cellspacing="6" cellpadding="6" width="100%" align="center">
';
if ($staff) {
	for ($s = 1; $s <= count($pkall); $s++) {
		if ($s > 0) {
			$pk = $pkall[$s];
			$GetUser = ctMemberCarumanLocal($pk);
			$GetUser->RowCount();
			$rowCount = $GetUser->RowCount();
			if ($GetUser->RowCount() == 0) {
				print '
	<tr><td>&nbsp;</td></tr>
		<tr>
			<td	colspan="3" align="center" height="70" valign="middle"><b>- Tiada Maklumat Mengenai Permohonan Pengeluaran Caruman Anggota -</b></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
';
			} else {
				$status		= $GetUser->fields('status');
				$staffID	= dlookup("userdetails", "memberID", "userID=" . tosql($GetUser->fields('userID'), "Text"));
				$username	= dlookup("users", "name", "userID=" . tosql($GetUser->fields('userID'), "Text"));
				$approvedDate	= $GetUser->fields('approvedDate');
				$rejectedDate	= $GetUser->fields('rejectedDate');
				$remark			= $GetUser->fields('remark');
				$withdrawAmt			= $GetUser->fields('withdrawAmt');
				$typeCaruman	= $GetUser->fields('type');

				print '
		<tr>
			<td nowrap>No./Nama Anggota</td>
			<td></td>
			<td><b>' . $staffID . ' - ' . $username . '</b></td>
		</tr>
		<tr>
			<td nowrap>Amaun/Jenis Pengeluaran</td>
			<td></td>
			<td><b>RM ' . $withdrawAmt . ' (' . $carumanTypeList[$typeCaruman] . ')</b></td>
		</tr>								
		<tr>
			<td nowrap>Tarikh Memohon</td>
			<td></td>
			<td><b>' . toDate("d/m/Y", $GetUser->fields(applyDate)) . '</b></td>
		</tr>
';
				if ($status == 1) {
					print '
		<tr>
			<td nowrap>Tarikh Diluluskan</td>
			<td></td>
			<td><b>' . toDate("d/m/Y", $GetUser->fields(approvedDate)) . '</b></td>
		</tr>
		<tr>
			<td nowrap>Catatan</td>
			<td></td>
			<td><b>' . $remark . '</b></td>
		</tr>
		';
				}
				if ($status == 3) {
					print '
		<tr>
			<td nowrap>Tarikh Diluluskan</td>
			<td></td>
			<td><b>' . toDate("d/m/Y", $GetUser->fields(approvedDate)) . '</b></td>
		</tr>
		<tr>
			<td nowrap>Catatan</td>
			<td></td>
			<td><b>' . $remark . '</b></td>
		</tr>
		';
				}
				if ($status == 2) {
					print '
		<tr>
			<td nowrap>Tarikh Ditolak</td>
			<td></td>
			<td><b>' . toDate("d/m/Y", $GetUser->fields(rejectedDate)) . '</b></td>
		</tr>
		<tr>
			<td nowrap>Catatan</td>
			<td></td>
			<td><b>' . $remark . '</b></td>
		</tr>
		';
				}
				print '
<td colspan="3"><hr class="mt-1"></td></tr>
';
			}
		} //end if
	} //end foreach
}
//------------------------
if ($staff) {
	if (count($carumanStatusList) <> 0) {
		if ($status == 0) {

			print '
		<tr>
		<td>Status</td>
		<td></td>
		<td>
		<select name="selStatus" class="form-select-xs">
		';
			for ($i = 0; $i < count($carumanStatusList); $i++) {
				if ($carumanStatusVal[$i] <> 3 && $carumanStatusVal[$i] <> 0)
					print '<option value="' . $carumanStatusVal[$i] . '">' . $carumanStatusList[$i];
			}
			print '
		</select>
		</td>
		</tr>
		';
		} else {
			if ($status == 1) {
				print '
					<div class="alert alert-primary">Status Terkini : ' . $carumanStatusList[$status] . '</div>
				';
			}
			if ($status == 3) {
				print '
					<div class="alert alert-primary">Status Terkini : ' . $carumanStatusList[$status] . '</div>
				';
			} elseif ($status == 2) {
				print '
					<div class="alert alert-danger">Status Terkini : ' . $carumanStatusList[$status] . '</div>
				';
			} else {
			}
		}
		if ($status == 0) {
			print '
			<tr>
				<td>Tarikh </td>
				<td></td>
				<td><input type="text" class="form-control-sm" name="strDate" value="' . $strDate . '" size="15" maxlength="10"></td>
			</tr>	
			<tr>
				<td>Catatan</td>
				<td></td>
				<td><textarea name="remark" cols="50" rows="4" class="form-control-sm"></textarea></td>
			</tr>
				<tr>
				<td colspan="3" align="center">
				<div>&nbsp;</div>
				<input type="submit" name="action" value="Kemaskini" class="btn btn-primary">
				<div>&nbsp;</div>
				</td>
			</tr>
		';
		} elseif ($status == 1) {
			print '
			<tr>
			<td>Status</td>
			<td></td>
			<td>
			<select name="selStatus" class="form-select-xs">
			';
			for ($i = 0; $i < count($carumanStatusList); $i++) {
				if ($carumanStatusVal[$i] <> 3 && $carumanStatusVal[$i] <> 0)
					print '<option value="' . $carumanStatusVal[$i] . '">' . $carumanStatusList[$i];
			}
			print '
			</select>
			</td>
			</tr>
			';
			print '
				<tr>
					<td>Catatan</td>
					<td></td>
					<td>
						<textarea name="remark" cols="50" rows="4" class="form-control-sm">' . $remark . '</textarea>
					</td>
				</tr>
					<tr>
					<td colspan="3" align="center">
					<div>&nbsp;</div>
                		<input type="submit" name="action" value="Kemaskini" class="btn btn-primary" onclick="return CheckField(event, \'Kemaskini\')">
					<div>&nbsp;</div>
					</td>
				</tr>
			';
		} elseif ($status == 3) {
			print '
			<tr hidden>
			<td hidden>Status</td>
			<td></td>
			<td>
			<select hidden name="selStatus" class="form-select-xs">
			';
			for ($i = 0; $i < count($carumanStatusList); $i++) {
				if ($carumanStatusVal[$i] == 3)
					print '<option value="' . $carumanStatusVal[$i] . '">' . $carumanStatusList[$i];
			}
			print '
			</select>
			</td>
			</tr>
			';
			print '
				<tr>
					<td>Catatan</td>
					<td></td>
					<td>
						<textarea name="remark" cols="50" rows="4" class="form-control-sm">' . $remark . '</textarea>
					</td>
				</tr>
					<tr>
					<td colspan="3" align="center">
					<div>&nbsp;</div>
                		<input type="submit" name="action" value="Kemaskini" class="btn btn-primary" onclick="return CheckField(event, \'Kemaskini\')">
					<div>&nbsp;</div>
					</td>
				</tr>
			';
		} elseif ($status == 2) {
			print '
			<tr hidden>
			<td hidden>Status</td>
			<td></td>
			<td>
			<select hidden name="selStatus" class="form-select-xs">
			';
			for ($i = 0; $i < count($carumanStatusList); $i++) {
				if ($carumanStatusVal[$i] == 2)
					print '<option value="' . $carumanStatusVal[$i] . '">' . $carumanStatusList[$i];
			}
			print '
			</select>
			</td>
			</tr>
			';
			print '
				<tr>
					<td>Catatan</td>
					<td></td>
					<td>
						<textarea name="remark" cols="50" rows="4" class="form-control-sm">' . $remark . '</textarea>
					</td>
				</tr>
					<tr>
					<td colspan="3" align="center">
					<div>&nbsp;</div>
                		<input type="submit" name="action" value="Kemaskini" class="btn btn-primary" onclick="return CheckField(event, \'Kemaskini\')">
					<div>&nbsp;</div>
					</td>
				</tr>
			';
		} else {
		}
	} else {
		print '
			<tr>
			<td colspan="3"	align="center">
			<hr size="1"><b>- Tiada rekod mengenai status  -</b><hr size="1">
			</td>
			</tr>
		';
	}
}
if (!$staff) {
	$uid = get_session('Cookie_userID');

	$sSQL = "select * from usercaruman where userID = $uid order by applyDate desc";
	$rs = &$conn->Execute($sSQL);
	if ($rs->RowCount() <> 0) {
		$cnt = 1;
		print '
<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
    <tr class="table-primary">
        <td nowrap><b>Bil</b></td>
        <td nowrap><b>Pengeluaran</b></td>
        <td nowrap><b>Tarikh Mohon</b></td>
        <td nowrap align="right"><b>Amaun (RM)</b></td>
        <td nowrap><b>Status</b></td>
        <td nowrap><b>Tarikh Lulus (Jika Ada)</b></td>
		<td nowrap><b>Tarikh Ditolak (Jika Ada)</b></td>
        <td nowrap><b>Catatan</b></td>

    </tr>';
		while (!$rs->EOF) {
			$typeCaruman	= $rs->fields('type');
			$applyDate		= toDate("d/m/y", $rs->fields('applyDate'));
			$withdrawAmt	= $rs->fields('withdrawAmt');
			$status			= $rs->fields('status');
			$approvedDate	= toDate("d/m/y", $rs->fields('approvedDate'));
			$remark			= $rs->fields('remark');
			$rejectedDate	= toDate("d/m/y", $rs->fields('rejectedDate'));
			print '
    <tr>
        <td nowrap class="Data">' . $cnt . '</td>
        <td nowrap class="Data">' . $carumanTypeList[$typeCaruman] . '</td>
		<td nowrap class="Data">' . $applyDate . '</td>
		<td nowrap class="Data" align="right">RM ' . $withdrawAmt . '</td>
        <td nowrap class="Data">' . $carumanStatusList[$status] . '</td>
		<td nowrap class="Data">' . $approvedDate . '</td>
        <td nowrap class="Data">' . $rejectedDate . '</td>
		<td nowrap class="Data">' . $remark . '</td>
    </tr>
';
			$cnt++;
			$rs->MoveNext();
		}
		$rs->Close();
	}
	print '</table>';
}
print '
	</table>
	</td>
	</tr>
</table>
</form>
</div>
';
include("footer.php");
