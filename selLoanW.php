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
$sWhere = "  a.ID is not null AND A.rnoBaucer IS NULL ";
//where statements
if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . $dept;
	$sWhere .= " AND A.userID = B.userID ";
}

if ($status <> "ALL") $sWhere .= "  AND A.status = 1 AND A.approvedDate <> ''";

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

if ($id) $sWhere .= " AND A.welfareType in (" . $id . ") ";

$sWhere = " WHERE (" . $sWhere . ")";

//fields selection
if ($q <> "") {
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	welfares A, userdetails B";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	welfares A, userdetails B, users C";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM 	welfares A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM 	welfares A ";
	}
}
$sSQL = $sSQL . $sWhere . ' ORDER BY A.approvedDate DESC';
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
	function selPinjaman(memberID,name,loan_no,bond_no)
	{	
		window.opener.document.MyForm.no_anggota.value = memberID;	
		window.opener.document.MyForm.nama_anggota.value = name;	
		window.opener.document.MyForm.name_type.value = loan_no;	
		window.opener.document.MyForm.no_bond.value = bond_no;	
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
				<h5 class="card-title">Senarai Pembiayaan Lulus</h5>
	<tr	valign="top">
		<td	align="left" >
			Carian melalui
			<select	name="by" class="form-select-xs">';
if ($by	== 1) print '<option value="1" selected>Nomor Anggota</option>';
else print '<option	value="1">Nomor Anggota</option>';
if ($by	== 2) print '<option value="2" selected>Nama Anggota</option>';
else print '<option	value="2">Nama Anggota</option>';
if ($by	== 3) print '<option value="3" selected>ad Pengenalan</option>';
else print '<option	value="3">Kartu Identitas</option>';
print '	</select>
			<input type="text" name="q"	value="" maxlength="50"	size="20" class="Data">
			<input type="submit" class="but" value="Cari" style="color: #fff;
    background-color: #495057;border-color: #495057;border-radius: 0.25rem;">&nbsp;&nbsp;&nbsp;
			Cabang/Zona
			<select	name="dept"	class="form-select-xs" onchange="document.MyForm.submit();">
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
			<table width="100%" style="font-size: 9pt;">
				<tr>
					<td	 class="textFont" align ="left">&nbsp;</td>
					<td	align="right" class="textFont">

					Tampil <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
if ($pg	== 50)	print '<option value="50" selected>50</option>';
else print '<option	value="50">50</option>';
if ($pg	== 100)	print '<option value="100" selected>100</option>';
else print '<option	value="100">100</option>';
if ($pg	== 500)	print '<option value="500" selected>500</option>';
else print '<option	value="500">500</option>';
if ($pg	== 1000) print '<option	value="1000" selected>1000</option>';
else print '<option	value="1000">1000</option>';

print '				</select> setiap halaman..
					</td>
				</tr>';
print '	</table>
		</td>
	</tr>';

if ($GetLoan->RowCount() == 0) {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan Nombor / Nama Anggota ATAU pilih Cabang/Zona -</b>
				</td></tr>';
} else {
	if ($GetLoan->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-sm table-striped" style="font-size: 9pt;">
					<tr class="table-primary">
						<td	nowrap>&nbsp;</td>
						<td	nowrap><b>Nomor Rujukan Kebajikan</b></td>
						<td	nowrap><b>Nomor - Nama Anggota</b></td>
						<td	nowrap align="center"><b>Kartu Identitas</b></td>
						<td	nowrap align="center"><b>Status</b></td>
						<td	nowrap align="center"><b>Tanggal Pengajuan</b></td>
					</tr>';
		while (!$GetLoan->EOF && $cnt <= $pg) {
			$status	= $GetLoan->fields(status);
			$colorStatus = "Data";
			if ($status	== 1) $colorStatus = "greenText";
			//-------------------
			$id		= $GetLoan->fields(ID);
			$code	= dlookup("general", "code", "ID=" . tosql($GetLoan->fields(welfareType), "Number")) . '/' . sprintf("%010d", $GetLoan->fields(ID));
			$name	= dlookup("general", "name", "ID=" . tosql($GetLoan->fields(welfareType), "Number"));
			$name = str_replace("'", "", $name);

			$no		= $GetLoan->fields(welfareNo);
			//------------
			$memberID = dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(userID), "Number"));
			$nama_anggota = dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Number"));
			$nama_anggota = str_replace("'", "", $nama_anggota);
			$bond	= dlookup("welfares", "rnoBond", "ID=" . $id);

			print '	<tr>
	<td	class="Data" align="center">' . $bil . '</td>

	<td	class="Data">
	<a href="javascript:selPinjaman(\'' . $memberID . '\',\'' . $nama_anggota . '\',\'' . dlookup("general", "name", "ID=" . tosql($GetLoan->fields(welfareType), "Number")) . '\',\'' . $bond . '\');">' . $GetLoan->fields(welfareNo) . '&nbsp;-&nbsp;' . dlookup("general", "name", "ID=" . tosql($GetLoan->fields(welfareType), "Number")) . '
	</a>
	</td>

	<td	class="Data">' . dlookup("userdetails",	"memberID",	"userID=" .	tosql($GetLoan->fields(userID),	"Text")) . '&nbsp;-&nbsp;' . dlookup("users", "name", "userID="	. tosql($GetLoan->fields(userID), "Text")) . '</td>
	
	<td	class="Data" align="center">' . dlookup("userdetails",	"newIC", "userID=" . tosql($GetLoan->fields(userID),	"Text")) . '</td>
	<td	class="Data" align="center"><font class="' . $colorStatus . '">' . $bajikanList[$status] . '</font></td>
	<td	class="Data" align="center">' . toDate("d/m/yy", $GetLoan->fields(applyDate)) . '</td>
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
