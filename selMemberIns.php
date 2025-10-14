<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selMember.php
 *          Date 		: 	06/10/2003
 *		   Amended		:	31/03/2004 - Change to list all member
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
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
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
$sWhere = " a.userID = b.userID AND b.status IN ('1','4')";;
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
$sSQL = "SELECT	DISTINCT a.*, b.* FROM users a, userdetails b";
$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER )";
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />	
</head>
<script language="JavaScript">
	function selAnggota(userid,memberid,name,newic,email,address,postcode,city,stateID)
	{	
		window.opener.document.MyForm.userID.value = userid;	
		window.opener.document.MyForm.memberID.value = memberid;	
		window.opener.document.MyForm.userName.value = name;	
		window.opener.document.MyForm.newIC.value = newic;
		window.opener.document.MyForm.email.value = email;	
		window.opener.document.MyForm.alamat.value = address;	
		window.opener.document.MyForm.postcode.value = postcode;	
		window.opener.document.MyForm.city.value = city;
		window.opener.document.MyForm.stateID.value = stateID;
		window.close();
	}
</script>

<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';
print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" style="font-size: 9pt;">
				<h5 class="card-title">Senarai Anggota</h5><tr>
				<td class="Data">
				Cari Berdasarkan 
				<select name="by" class="form-select-xs">';
if ($by == 1)	print '<option value="1" selected>Nomor Anggota</option>';
else print '<option value="1">Nomor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kartu Identitas</option>';
else print '<option value="3">Kartu Identitas</option>';
print '		</select>
						<input type="text" name="q" value="" class="form-control-sm" maxlength="50" size="30" class="Data">
			           	<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
						Cabang/Zona
						<select name="dept" class="form-select-xs" onchange="document.MyForm.submit();">
							<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '</select></td></tr>';
if ($GetMember->RowCount() == 0) {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan No / Nama Anggota ATAU pilih Jabatan  -</b></td></tr>';
} else {
	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
			<td class="Data" width="100%">
			<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-sm table-striped" style="font-size: 9pt;">
			<tr class="table-primary">
			<td align="center" nowrap>&nbsp;</td>
			<td align="center"><b>Nomor Anggota</b></td>
			<td align="left"><b>Nama</b></td>
			<td align="center"><b>Kartu Identitas</b></td>
			<td align="left"><b>Emel</b></td>';
		print '</tr>';
		while (!$GetMember->EOF && $cnt <= $pg) {
			$userid		= $GetMember->fields(userID);
			$memberid	= $GetMember->fields(memberID);
			$name		= $GetMember->fields(name);
			$newic		= $GetMember->fields(newIC);
			$email		= $GetMember->fields(email);
			$jabatan 	= $GetMember->fields(departmentID);
			$address	= $GetMember->fields(address);
			$address	= str_replace("<pre>", "", $address);
			$address	= str_replace("</pre>", "", $address);
			$address	= nl2br($address);
			$address	= str_replace("<br />", " ", $address);
			$postcode	= $GetMember->fields(postcode);
			$city	= $GetMember->fields(city);
			$stateID	= $GetMember->fields(stateID);
			print '
			<tr>
<td class="Data" align="center">' . $bil . '</td>
<td class="Data" align="center"><a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\',\'' . $newic . '\',\'' . $email . '\',\'' . $address . '\',\'' . $postcode . '\',\'' . $city . '\',\'' . $stateID . '\');">' . $memberid . '</a></td>
<td class="Data"><a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\',\'' . $newic . '\',\'' . $email . '\',\'' . $address . '\',\'' . $postcode . '\',\'' . $city . '\',\'' . $stateID . '\');">' . $name . '</a></td>
<td class="Data" align="center">' . $newic . '</td>
<td class="Data" align="left">' . $email . '</td>';
			print '

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
			print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
			for ($i = 1; $i <= $numPage; $i++) {
				print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '">';
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
			}
			print '</td></tr></table>';
		}
		print '</td></tr>';
		print '</td></tr></table></td></tr>';
	} else {
		print '
		<tr><td	class="Label" align="center" height=50 valign=middle>
		<b>- Tiada rekod mengenai anggota  -</b>
		</td></tr>';
	} // end of ($GetMember->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '</table></td></tr></table></form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body></html>';
