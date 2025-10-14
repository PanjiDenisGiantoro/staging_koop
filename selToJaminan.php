<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selToMember.php
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
		//		array_push ($deptList, $rs->fields(deptCode));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

$GetMember = ctMemberStatusDept($q, $by, "1", $dept);
/*/--- Prepare department type
$deptList = Array();
$deptVal  = Array();
$GetDept = ctGeneral("","B");
if ($GetDept->RowCount() <> 0){
	while (!$GetDept->EOF) {
		array_push ($deptList, $GetDept->fields(name));
		array_push ($deptVal, $GetDept->fields(ID));
		$GetDept->MoveNext();
	}
}	
	$status = 1;
	$sSQL = "";
	$sWhere = " a.userID = b.userID AND b.userID = c.userID and b.status = " . tosql($status,"Number");
	if ($dept <> "") 	{
		$sWhere .= " AND b.departmentID = " . tosql($dept,"Number");
	}
	if ($q <> "") 	{
		if ($by == 1) {
			$sWhere .= " AND b.memberID like " . tosql($q."%","Text");			
		} else if ($by == 2) {
			$sWhere .= " AND a.name like " . tosql($q."%","Text");
		} else if ($by == 3) {
			$sWhere .= " AND b.newIC like " . tosql($q."%","Text");		
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*, c.*
			 FROM 	users a, userdetails b, loans c";
	$sSQL = $sSQL . $sWhere . ' ORDER BY c.loanID';
	$GetMember = &$conn->Execute($sSQL);
*/

$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);
$num = $obj;

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
	function selAnggotaD(memberid,name,obj,id)
	{
		var e;
		e = window.opener.document.MyForm;
		if(obj==1){ 
		e.elements.penjaminID1.value = memberid; 
		//e.elements.lid1.value = id; 
		e.elements.sellUserName1.value = name; 
		}
		if(obj==2){ 
		e.elements.penjaminID2.value = memberid; 
		//e.elements.lid2.value = id; 
		e.elements.sellUserName2.value = name; 
		}
		if(obj==3){ 
		e.elements.penjaminID3.value = memberid; 
		//e.elements.lid3.value = id; 
		e.elements.sellUserName3.value = name; 
		}

		window.close();
	}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action="' . $PHP_SELF . '?refer=' . $refer . '" method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<input type="hidden" name="obj" value="' . $obj . '">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" style="font-size: 9pt;">
				<tr>
					<td	class="Header" colspan="2">Senarai Daftar Pembiayaan anggota Anggota </b></td>
				</tr>
				<tr>
					<td class="Data">
						Carian melalui 
						<select name="by" class="form-select-xs">';
if ($by == 1)	print '<option value="1" selected>Nomor Anggota</option>';
else print '<option value="1">Nomor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>No KTP Baru</option>';
else print '<option value="3">No KTP Baru</option>';
print '		</select>
						<input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
			           	<input type="submit" class="btn btn-secondary btn-sm" value="Cari" style="color: #fff;
    background-color: #495057;border-color: #495057;border-radius: 0.25rem;">&nbsp;&nbsp;&nbsp;
						Jabatan
						<select name="dept" class="form-select-xs" onchange="document.MyForm.submit();">
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
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Tiada sebarang urusan pembiayaan dalam cawangan -</b>
				</td></tr>';
} else {
	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-sm table-striped" style="font-size: 9pt;">
					<tr class="table-success">
						<td class="header" nowrap>&nbsp;</td>
						<td class="header" >&nbsp;Nomor Anggota</td>
						<td class="header" >&nbsp;Nama</td>
						<td class="header" >&nbsp;Jabatan</td>
					</tr>';
		while (!$GetMember->EOF && $cnt <= $pg) {
			$userid		= $GetMember->fields(userID);
			$memberid	= $GetMember->fields(memberID);
			$name = $GetMember->fields(name);
			$name = str_replace("'", "", $GetMember->fields(name));
			$jabatan 	= $GetMember->fields(departmentID);
			$loanid 	= $GetMember->fields(loanID);
			$num = $obj;
			print '
					<tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data">&nbsp;';
			print '<a href="javascript:selAnggotaD(\'' . $memberid . '\',\'' . $name . '\',\'' . $num . '\',\'' . $loanid . '\');">' . $memberid . '</a>';
			print '</td>
						<td class="Data">&nbsp;';
			print '<a href="javascript:selAnggotaD(\'' . $memberid . '\',\'' . $name . '\',\'' . $num . '\',\'' . $loanid . '\');">' . $name . '</a>';
			print '</td>
						<td class="Data" align="left">&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Number")) . '</td>
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
			print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
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
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
