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
if (!isset($pg))		$pg = 10;
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

//$GetMember = ctMemberStatusDept($q,$by,"1",$dept);
$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status IN ('1','4')";

// Menambah syarat untuk mengecualikan pengguna yang ada dalam 'userterminate'
$sWhere .= " AND a.userID NOT IN (SELECT userID FROM userterminate WHERE isRejected = 0)";

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

// Gabungkan syarat WHERE dan urutan
$sSQL = $sSQL . $sWhere . " ORDER BY CAST( b.memberID AS SIGNED INTEGER )";

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
	function selAnggota(userid,memberid,name,newic,unit)
	{	
		window.opener.document.MyForm.userID.value = userid;	
		window.opener.document.MyForm.memberID.value = memberid;	
		window.opener.document.MyForm.userName.value = name;	
		window.opener.document.MyForm.newIC.value = newic;
			
		//window.opener.document.MyForm.unitOnHand.value = unit;	
		window.close();
	}
</script>

<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';
//window.opener.document.MyForm.emel.value = email;
print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="table">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" class="table table-sm">
				<tr>
					<td	class="Header" colspan="2">Senarai Anggota</b></td>
				</tr>
				<tr class="class="table table-primary">
					<td>
						Carian melalui 
						<select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kad Pengenalan</option>';
else print '<option value="3">Kad Pengenalan</option>';
print '		</select>
						<input type="text" name="q" value="" class="form-control-sm" maxlength="50" size="30" class="Data">
			           	<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
						Pusat Kos
						<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
							<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '			</select>
					</td>
				</tr>';
if ($GetMember->RowCount() == 0) {
	print '		<tr><td class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan No / Nama Anggota ATAU pilih Jabatan  -</b>
				</td></tr>';
} else {
	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-bordered table-striped table-sm" style="font-size: 10pt;">
					<tr class="table table-primary">
						<td class="header" nowrap>&nbsp;</td>
						<td class="header" align="center"><b>Nombor Anggota</b></td>
						<td class="header" ><b>Nama</b></td>
						<td class="header" align="center"><b>Kad Pengenalan</b></td>';
		//				<td class="header" >&nbsp;Email</td>
		print '						<td class="header" align="right"><b>Pegangan Yuran (RP)</b></b></td>
					</tr>';
		while (!$GetMember->EOF && $cnt <= $pg) {
			$userid		= $GetMember->fields(userID);
			$memberid	= $GetMember->fields(memberID);
			$name		= mysql_real_escape_string($GetMember->fields(name));
			$newic		= $GetMember->fields(newIC);
			$jabatan 	= $GetMember->fields(departmentID);
			$jumlahUnit = number_format(getFees($userid, date("Y")), 2);
			print '
					<tr>
						<td class="Data" align="right">' . $bil . '</td>
						<td class="Data" align="center"><a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\',\'' . $newic . '\',\'' . $oldic . '\',\'' . $jumlahUnit . '\');">' . $memberid . '</a></td>
						<td class="Data"><a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\',\'' . $newic . '\',\'' . $oldic . '\',\'' . $jumlahUnit . '\');">' . $name . '</a></td>';

			//<td class="Data" align="center">'.$email.'&nbsp;</td>
			print '
						<td class="Data" align="center">' . $newic . '</td>
						<td class="Data" align="right">' . $jumlahUnit . '</td>
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
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
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
</table></div>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
