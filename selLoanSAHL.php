<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *********************************************************************************/
include("common.php");

include("koperasiQry.php");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 30;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";

if (!isset($filter))	$filter = "ALL";
//--- Prepare department list
$deptList =	array();
$deptVal  =	array();
$sSQL =	"	SELECT a.departmentID, b.code as deptCode, b.name as deptName
			FROM userdetails a,	general	b
			WHERE a.departmentID = b.ID
			AND	  a.status = 1
			GROUP BY a.departmentID";
$rs	= &$conn->Execute($sSQL);
if ($rs->RowCount()	<> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

$sSQL = "";
$sWhere = "  loanID is not null ";
//where statements
if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . $dept;
	$sWhere .= " AND A.userID = B.userID ";
}

if ($status <> "ALL") $sWhere .= "  AND A.status IN (3,7) AND A.startPymtDate <> ''";

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.userID = B.userID ";
		$sWhere .= " AND B.memberID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND A.userID = C.userID ";
		$sWhere .= " AND C.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.userID = B.userID ";
		$sWhere .= " AND B.newIC like '%" . $q . "%'";
	}
}

if ($id) $sWhere .= " AND A.loanType in (" . $id . ") ";

$sWhere = " WHERE (" . $sWhere . ")";

//fields selection
if ($q <> "") {
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B, users C";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A ";
	}
}
$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
$GetLoan = &$conn->Execute($sSQL);

$GetLoan->Move($StartRec - 1);

$TotalRec =	$GetLoan->RowCount();
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
	function selPinjaman(memberID, name, loan_no, bond_no,amt)
	{	
		window.opener.document.MyForm.no_anggota.value = memberID;	
		window.opener.document.MyForm.nama_anggota.value = name;	
		window.opener.document.MyForm.name_type.value = loan_no;	
		window.opener.document.MyForm.no_bond.value = bond_no;	
		window.opener.document.MyForm.amt.value = amt;
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
			<table class="table table-sm" border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="headerteal" colspan="2">Senarai Pembiayaan Lulus</b></td>
				</tr>
	<tr	valign="top" class="Header">
		<td	align="left" >
			Carian melalui
			<select	name="by" class="form-select-sm">';
if ($by	== 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option	value="1">Nombor Anggota</option>';
if ($by	== 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option	value="2">Nama Anggota</option>';
if ($by	== 3)	print '<option value="3" selected>No KP	Baru</option>';
else print '<option	value="3">No KP	Baru</option>';
print '	</select>
			<input type="text" name="q"	value="" maxlength="50"	size="30" class="form-select-sm">
			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
			Cawangan/Zon
			<select	name="dept"	class="form-select-sm" onchange="document.MyForm.submit();">
				<option	value="">- Semua -';
for ($i	= 0; $i	< count($deptList); $i++) {
	print '	<option	value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '	</select>
		</td>
	</tr>
	<tr	valign="top" class="textFont">
		<td>
			<table width="100%">';
print '	</table>
		</td>
	</tr>';

if ($GetLoan->RowCount() == 0) {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Tiada sebarang maklumat anggota.  -</b>
				</td></tr>';
} else {
	if ($GetLoan->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-bordered table-striped table-sm" style="font-size: 9pt;">
					<tr	class="table-primary">
						<td class="headerteal" nowrap>&nbsp;</td>
						<td class="headerteal">&nbsp;<b>Nombor ruj. Pinjaman</b></td>
						<td	>&nbsp;<b>No/Nama Anggota</b></td>
						<td	>&nbsp;<b>Nombor KP Baru</b></td>
						<td	>&nbsp;<b>Jumlah</b></td>
						<td	>&nbsp;<b>Status</b></td>
						<td	>&nbsp;<b>Tarikh Memohon</b></td>
					</tr>';
		$amtLoan = 0;
		while (!$GetLoan->EOF && $cnt <= $pg) {
			$jabatan = dlookup("userdetails", "departmentID", "userID="	. tosql($GetLoan->fields(userID), "Text"));
			//$amt = dlookup("general",	"c_Maksimum", "ID="	. tosql($GetLoan->fields(loanType),	"Number"));
			// new amount
			$amt =	number_format(tosql($GetLoan->fields(loanAmt), "Number"), 2);
			$amtLoan = $amtLoan	+ tosql($GetLoan->fields(loanAmt), "Number");
			$status	= $GetLoan->fields(status);
			$colorStatus = "Data";
			if ($status	== 3) $colorStatus = "greenText";
			if ($status	== 4) $colorStatus = "redText";
			//-------------------
			$id		= $GetLoan->fields(loanID);
			$code	= dlookup("general", "code", "ID=" . tosql($GetLoan->fields(loanType), "Number")) . '/' . sprintf("%010d", $GetLoan->fields(loanID));
			$name	= dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
			$name = str_replace("'", "", $name);

			$no		= $GetLoan->fields(loanNo);
			//$amt	= dlookup("general", "c_Maksimum", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
			//------------
			$memberID = dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(userID), "Number"));
			$nama_anggota = dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Number"));
			$nama_anggota = str_replace("'", "", $nama_anggota);
			$bond	= dlookup("loandocs", "rnoBond", "loanID=" . $id);

			print '	<tr>
						<td	class="Data" align="right">' . $bil	. '&nbsp;</td>
						<td	class="Data">&nbsp;<a href="javascript:selPinjaman(\'' . $memberID . '\',\'' . $nama_anggota . '\',\'' . dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number")) . '\',\'' . $bond . '\',\'' . $amt . '\');">' . $GetLoan->fields(loanNo) . '-&nbsp;' . dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number")) . '</a></td>
						<td	class="Data">&nbsp;' . dlookup("userdetails",	"memberID",	"userID=" .	tosql($GetLoan->fields(userID),	"Text")) . '-' . dlookup("users", "name", "userID="	. tosql($GetLoan->fields(userID), "Text")) . '</td>
						<td	class="Data">&nbsp;' . dlookup("userdetails",	"newIC", "userID=" . tosql($GetLoan->fields(userID),	"Text")) . '</td>
						<td	class="Data">' . $amt . '&nbsp;</td>
						<td	class="Data">&nbsp;<font class="' . $colorStatus . '">' . $biayaList[$status] . '</font></td>
						<td	class="Data" align="center">&nbsp;' . toDate("d/m/yy", $GetLoan->fields(applyDate)) . '</td>
					</tr>';
			$cnt++;
			$bil++;
			$GetLoan->MoveNext();
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
	} // end of ($GetLoan->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
