<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selMemberHL.php
 *          Date 		: 	28/04/2017
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 25;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "SELECT a.departmentID, b.code as deptCode, b.name as deptName 
		FROM userdetails a, general b
		WHERE a.departmentID = b.ID
		AND   a.status IN ('1','4') 
		GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}
$sSQL = "";
$sWhere = " a.userID = b.userID AND b.statusHL IN (1)";;
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
$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER )";
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head><title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<LINK rel="stylesheet" href="images/default.css" >	
</head>
<script language="JavaScript">
	function selAnggota(userid,memberid,name,newic,oldic,unit)
	{	
		window.opener.document.MyForm.userID.value = userid;	
		window.opener.document.MyForm.memberID.value = memberid;	
		window.opener.document.MyForm.userName.value = name;	
		window.opener.document.MyForm.newIC.value = newic;	
		window.close();
	}
</script>

<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';
print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
<tr><td class="Data">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr><td	class="Header" colspan="2">Senarai Anggota</b></td></tr>
tr><td class="Data">Carian melalui 
	<select name="by" class="Data">';
if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>No KP Baru</option>';
else print '<option value="3">No KP Baru</option>';
print '</select>
			<input type="text" name="q" value="" maxlength="50" size="30" class="Data">
			<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;
			Jabatan/Cawangan
			<select name="dept" class="Data" onchange="document.MyForm.submit();">
			<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '</select></td></tr>';
if ($GetMember->RowCount() == 0) {
	print '<tr><td class="Label" align="center" height=50 valign=middle>
	<b>- Sila masukkan No / Nama Anggota ATAU pilih Jabatan  -</b></td></tr>';
} else {
	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '<tr><td class="Data" width="100%">
		<table border="0" cellpadding="2" cellspacing="1" width="100%" class="Data">
		<tr>
		<td class="header" nowrap>&nbsp;</td>
		<td class="header" width=80>&nbsp;Nombor Anggota</td>
		<td class="header" >&nbsp;Nama</td>
		<td class="header" >&nbsp;No KP Baru</td>';
		print '<td class="header" align="center">&nbsp;Pegangan Wajib</td></tr>';
		while (!$GetMember->EOF && $cnt <= $pg) {
			$userid		= $GetMember->fields(userID);
			$memberid	= $GetMember->fields(memberID);
			$name		= $GetMember->fields(name);
			$newic		= $GetMember->fields(newIC);
			$jabatan 	= $GetMember->fields(departmentID);
			$jumlahUnit = number_format(getFees($userid, date("Y")), 2);
			print '
			<tr>
<td class="Data" align="right">' . $bil . '&nbsp;</td>
<td class="Data" align="center">&nbsp;<a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\',\'' . $newic . '\',\'' . $oldic . '\',\'' . $jumlahUnit . '\');">' . $memberid . '</a></td>
<td class="Data">&nbsp;<a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\',\'' . $newic . '\',\'' . $oldic . '\',\'' . $jumlahUnit . '\');">' . $name . '</a></td>';
			print '
		<td class="Data" align="center">' . $newic . '&nbsp;</td>
		<td class="Data" align="right">' . $jumlahUnit . '&nbsp;</td>
		</tr>';
			$cnt++;
			$bil++;
			$GetMember->MoveNext();
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
			print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
			for ($i = 1; $i <= $numPage; $i++) {
				print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '">';
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
			}
			print '</td></tr></table>';
		}
		print '</td></tr>';
		print '</td></tr></table></td></tr>';
	} else {
		print '<tr><td class="Label" align="center" height=50 valign=middle>
		<b>- Tiada rekod mengenai anggota  -</b></td></tr>';
	} // end of ($GetMember->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '</table></td></tr></table></form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
