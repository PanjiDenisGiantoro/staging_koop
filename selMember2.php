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

$GetMember = ctMemberStatusDept($q, $by, "1", $dept);
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
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<script language="JavaScript">
	function selAnggota(userid,memberid,name,newic,oldic,loginid,password,unit)
	{	
		window.opener.document.MyForm.userID.value = userid;	
		window.opener.document.MyForm.memberID.value = memberid;	
		window.opener.document.MyForm.userName.value = name;		
		window.opener.document.MyForm.loginID.value = loginid;
		window.opener.document.MyForm.password.value = password;
		window.opener.document.MyForm.unitOnHand.value = unit;	
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
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Header" colspan="2">Senarai Anggota</b></td>
				</tr>
				<tr>
					<td class="Data">
						Carian melalui 
						<select name="by" class="Data">';
if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>No KP Baru</option>';
else print '<option value="3">No KP Baru</option>';
print '		</select>
						<input type="text" name="q" value="" maxlength="50" size="30" class="Data">
			           	<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;
						Jabatan
						<select name="dept" class="Data" onchange="document.MyForm.submit();">
							<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '			</select>
					</td>
				</tr>';
if ($q == "" and $dept == "") {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan No / Nama Anggota ATAU pilih Jabatan  -</b>
				</td></tr>';
} else {
	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="Data">
					<tr>
						<td class="header" nowrap>&nbsp;</td>
						<td class="header" width=80>&nbsp;Nombor Anggota</td>
						<td class="header" >&nbsp;Nama</td>
						<td class="header" >&nbsp;No KP Baru</td>
						<td class="header" >&nbsp;No KP Lama</td>
						<td class="header" >&nbsp;Jabatan</td>
						<td class="header" align="center">&nbsp;Syer Dalam Tangan</td>
					</tr>';
		while (!$GetMember->EOF && $cnt <= $pg) {
			$userid		= $GetMember->fields(userID);
			$memberid	= $GetMember->fields(memberID);
			$name		= $GetMember->fields(name);
			$newic		= $GetMember->fields(newIC);
			$oldic		= $GetMember->fields(oldIC);
			$loginid	= $GetMember->fields(loginID);
			$password	= $GetMember->fields(password);
			$jabatan 	= $GetMember->fields(departmentID);
			//					$jumlahUnit = ctNumberShare($userid);
			//					$jumlahUnit = $jumlahUnit + $GetMember->fields(unitShare); 
			$jumlahUnit = $GetMember->fields(totalShare); // grab from userdetails (totalshare)
			print '
					<tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data">&nbsp;<a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\',\'' . $newic . '\',\'' . $oldic . '\',\'' . $loginid . '\',\'' . $password . '\',\'' . $jumlahUnit . '\');">' . $memberid . '</a></td>
						<td class="Data">&nbsp;<a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\',\'' . $newic . '\',\'' . $oldic . '\',\'' . $loginid . '\',\'' . $password . '\',\'' . $jumlahUnit . '\');">' . $name . '</a></td>
						<td class="Data" align="left">' . $newic . '&nbsp;</td>
						<td class="Data" align="left">' . $oldic . '&nbsp;</td>
						<td class="Data" align="left">&nbsp;' . dlookup("general", "code", "ID=" . tosql($jabatan, "Number")) . '</td>
						<td class="Data" align="right">' . $jumlahUnit . '&nbsp;</td>
					</tr>';
			$cnt++;
			$bil++;
			$GetMember->MoveNext();
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
			print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
			for ($i = 1; $i <= $numPage; $i++) {
				print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '">';
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
			}
			print '</td>
						</tr>
					</table>';
		}
		print '
			</td>
		</tr>';

		print '
				</td>
			</tr>
				</table>
				
						</td>
					</tr>';
	} else {
		print '
					<tr><td	class="Label" align="center" height=50 valign=middle>
						<b>- Tiada rekod mengenai anggota  -</b>
					</td></tr>';
	} // end of ($GetMember->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
