<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA14.php
 *          Date 		: 	26/05/2006
 *********************************************************************************/
session_start();
if (!isset($q))				$q = '';
if (!isset($by))			$by = '1';
if (!isset($status))		$status = '1';
if (!isset($dept))			$dept = '';

include("common.php");
include("koperasiinfo.php");
include("koperasiQry.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

//--- Prepare state type
$stateList = array();
$stateVal  = array();
$GetState = ctGeneral("", "H");
if ($GetState->RowCount() <> 0) {
	while (!$GetState->EOF) {
		array_push($stateList, $GetState->fields(name));
		array_push($stateVal, $GetState->fields(ID));
		$GetState->MoveNext();
	}
}

//--- Prepare department type
$deptList = array();
$deptVal  = array();
$GetDept = ctGeneral("", "B");
if ($GetDept->RowCount() <> 0) {
	while (!$GetDept->EOF) {
		array_push($deptList, $GetDept->fields(name));
		array_push($deptVal, $GetDept->fields(ID));
		$GetDept->MoveNext();
	}
}

//--- Prepare bangsa type
$raceList = array();
$raceVal  = array();
$Getrace = ctGeneral("", "E");
if ($Getrace->RowCount() <> 0) {
	while (!$Getrace->EOF) {
		array_push($raceList, $Getrace->fields(name));
		array_push($raceVal, $Getrace->fields(ID));
		$Getrace->MoveNext();
	}
}



$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status IN (1,4) ";
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*
		 FROM 	users a, userdetails b";
$sSQL = $sSQL . $sWhere;
$sSQL = $sSQL . " order by CAST( b.memberID AS SIGNED INTEGER )";
$GetData = &$conn->Execute($sSQL);
$title  = 'Senarai Daftar Anggota';

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/default.css" >		
</head>
<body>';
print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . ' Pada ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">';
print
	'<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					
					<th nowrap align="left">Nama</th>
					
					<th nowrap width="200">Alamat</th>
					<th nowrap width="80">Poskod</th>
					<th nowrap width="80">Bandar</th>
					<th nowrap width="80">Negeri</th>
					
					<th nowrap width="80">Nombor KP</th>
					<th nowrap width="80">Pekerjaan</th>
					<th nowrap width="80">Jantina</th>
					<th nowrap width="80">Bangsa</th>
					<th nowrap width="80">Tarikh Lahir</th>
					<th nowrap align="center" width="150">Tarikh Keanggotaan</th>
					<th nowrap align="center" width="150">Tarikh Berhenti</th>
					<th nowrap width="80">Nombor Anggota</th>
					<th nowrap width="80">No Pekerja</th>
				</tr>';
if ($GetData->RowCount() <> 0) {
	while (!$GetData->EOF) {
		$count++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td align="right" valign="top" width="2%">' . $count . ')</td>
							
							<td valign="top">' . strtoupper($GetData->fields('name')) . '</td>
							
							<td valign="top">' . strtoupper($GetData->fields('address')) . '</td>
							<td align="center" valign="top">' . $GetData->fields('postcode') . '</td>
							<td align="center" valign="top">' . strtoupper($GetData->fields('city')) . '</td>
							<td align="center" valign="top">' . strtoupper($stateList[array_search($GetData->fields('stateID'), $stateVal)]) . '</td>
							
							<td align="center" valign="top">' . convertNewIC($GetData->fields('newIC')) . '</td>
							<td align="center" valign="top">' . strtoupper($GetData->fields('job')) . '</td>';
		if ($GetData->fields('sex') == '0') {
			print '<td align="center" valign="top">L</td>';
		} else {
			print '<td align="center" valign="top">P</td>';
		}

		print '
							<td align="center" valign="top">' . strtoupper($raceList[array_search($GetData->fields('raceID'), $raceVal)]) . '</td>
							<td align="center" valign="top">' . toDate('d/m/Y', $GetData->fields('dateBirth')) . '</td>
							<td align="center" valign="top">' . toDate('d/m/Y', $GetData->fields('approvedDate')) . '</td>
							
<td align="center" valign="top">' . toDate('d/m/Y', dlookup("userterminate", "approvedDate", "userID=" . tosql($GetData->fields('userID'), "Number"))) . '</td>
							<td align="center" valign="top">&nbsp;' . $GetData->fields('memberID') . '</a></td>
							<td align="center" valign="top">&nbsp;' . $GetData->fields('staftNo') . '</a></td>
						</tr>';
		$GetData->MoveNext();
	}
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" height="30" valign="bottom">Jumlah Anggota : <b>' . $count . '</b></td>
					</tr>					
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" height="30" valign="bottom">Jumlah Keseluruhan Anggota : <b>' . $GetData->RowCount() . '</b></td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print 		'</table>
		</td>
	</tr>
	
</table>
</form>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
