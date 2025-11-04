<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	welfareTableS.php
 *          Date 		: 	05/05/2006
 *********************************************************************************/

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 10;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($dept))		$dept = "";
if (!isset($yr)) $yr	= date("Y");
if (!isset($yy)) $yy	= date("Y");
if (!isset($mm)) $mm	= date("m");
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=welfareTableS&mn=920';
$sFileRef  = '?vw=welfareTableS&mn=906';
$title     = "Daftar Pengajuan Bantuan Kebajikan Selesai";

$IDName = get_session("Cookie_userName");

if ($action	== "finish") {

	for ($i = 0; $i < count($pk); $i++) {

		$CheckWelfare = ctWelfare("", $pk[$i]);
		if ($CheckWelfare->RowCount() == 1) {
			if ($CheckWelfare->fields(status) == 9) {
				$updatedBy	= get_session("Cookie_userName");
				$updatedDate = date("Y-m-d H:i:s");
				$sSQL =	'';
				$sWhere	= '';
				$sWhere	= '	ID	= ' . $pk[$i];
				$sSQL	= '	UPDATE welfares ';
				$sSQL	.= ' SET ' .
					' status	= 1 ' .
					' ,selesaiBy	=' . tosql($updatedBy, "Text") .
					' ,selesaiDate='	. tosql($updatedDate, "Text");
				$sSQL .= ' WHERE ' . $sWhere;
				$rs	= &$conn->Execute($sSQL);
				print '<script>alert("Nomor Rujukan tersebut telah dimasukkan semula ke senarai kelulusan.");</script>';
			} else {
				print '<script>alert("Hanya permohonan berstatus selesai sahaja boleh dimasuk semula.");</script>';
			}
		} //for close
	}
}
//--- End -------------------------------------------------------
//--- Prepare department list

$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status IN (1) 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

$status = 9;

$sSQL = "";
$sWhere = " A.status = " . tosql($status, "Number");
if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
	$sWhere .= " AND A.userID = B.userID ";
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND a.userID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND b.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '%" . $q . "%'";
	}
}

$sWhere = " WHERE (" . $sWhere . ") ";
$sSQL = "SELECT a.* FROM welfares a";
if ($q <> "") {
	if ($by == 2) {
		$sSQL .= " JOIN users b ON a.userID=b.userID ";
	} else if ($by == 3) {
		$sSQL .= " JOIN userdetails b ON a.userID=b.userID ";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM welfares A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM welfares A ";
	}
}
$sSQL = $sSQL . $sWhere . ' ORDER BY A.approvedDate DESC';
$GetWelfare = &$conn->Execute($sSQL);
$GetWelfare->Move($StartRec - 1);

$TotalRec = $GetWelfare->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk" value="' . $pk . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="table-responsive">
<h5 class="card-title">' . strtoupper($title) . '</h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	
    <tr valign="top">
	   	<td align="left" >
			Cari Berdasarkan 
			<select name="by" class="form-select-sm">';
if ($by == 1)	print '<option value="1" selected>Nomor Anggota</option>';
else print '<option value="1">Nomor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>Kartu Identitas</option>';
else print '<option value="3">Kartu Identitas</option>';

print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
 			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">	
			Cabang/Zona
			<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '		</select>&nbsp;&nbsp;           
		</td>
	</tr>';
if ($GetWelfare->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont">Pengesahan Permohonan selesai ';
	if (($IDName == 'superadmin') or ($IDName == 'admin')) {
		print '	<input type="button" class="btn btn-sm btn-success" value="Kelulusan Pegawai" onClick="ITRActionButtonFinish(\'finish\');">	';
	}
	print '
			</td>
						<td align="right" class="textFont">
							Tampil <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
	if ($pg == 5)	print '<option value="5" selected>5</option>';
	else print '<option value="5">5</option>';
	if ($pg == 10)	print '<option value="10" selected>10</option>';
	else print '<option value="10">10</option>';
	if ($pg == 20)	print '<option value="20" selected>20</option>';
	else print '<option value="20">20</option>';
	if ($pg == 30)	print '<option value="30" selected>30</option>';
	else print '<option value="30">30</option>';
	if ($pg == 40)	print '<option value="40" selected>40</option>';
	else print '<option value="40">40</option>';
	if ($pg == 50)	print '<option value="50" selected>50</option>';
	else print '<option value="50">50</option>';
	if ($pg == 100)	print '<option value="100" selected>100</option>';
	else print '<option value="100">100</option>';
	if ($pg == 200)	print '<option value="200" selected>200</option>';
	else print '<option value="200">200</option>';
	if ($pg == 300)	print '<option value="300" selected>300</option>';
	else print '<option value="300">300</option>';
	if ($pg == 400)	print '<option value="400" selected>400</option>';
	else print '<option value="400">400</option>';
	if ($pg == 500)	print '<option value="500" selected>500</option>';
	else print '<option value="500">500</option>';
	if ($pg == 1000) print '<option value="1000" selected>1000</option>';
	else print '<option value="1000">1000</option>';
	print '				</select> setiap halaman..
						</td>
					</tr>
				</table>
			</td>
		</tr>
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-striped table-sm">
					<tr class="table-primary">
					<td nowrap>&nbsp;</td>
					<td nowrap>Nomor Rujukan - Bantuan Sosial</td>
					<td nowrap align="left">Nomor - Nama Anggota</td>
					<td nowrap align="center">Kartu Identitas</td>
					<td	nowrap align="center">Status</td>
					<td	nowrap align="center">Tarikh Kelulusan</td>
					<td	nowrap align="center">Nombor Bond</td>
					<td nowrap align="center">Tanggal Voucher</td>
					</tr>';
	$amtWelfare = 0;
	while (!$GetWelfare->EOF && $cnt <= $pg) {
		$welfareName = dlookup("general", "name", "ID=" . tosql($GetWelfare->fields(welfareType), "Text"));
		$name = dlookup("users", "name", "userID=" . tosql($GetWelfare->fields(userID), "Text"));

		$status = $GetWelfare->fields(status);
		$approvedDate = $GetWelfare->fields(approvedDate);

		$colorStatus = "Data";
		if ($status == 9) $colorStatus = "text-primary";

		if ($status == 9) {
			$bajikanList[$status] = Selesai;
		} else {
			print '<td class="Data" align="center"><font class="' . $colorStatus . '">' . $statusList[$status] . '</font></td>';
		}

		print '<tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value=' . $GetWelfare->fields(ID) . '>&nbsp;'
			. $GetWelfare->fields(welfareNo) . '-  '
			. dlookup("general", "name", "ID=" . tosql($GetWelfare->fields(welfareType), "Number")) . '</td>
						<td class="Data">'
			. dlookup("userdetails", "memberID", "userID=" . tosql($GetWelfare->fields(userID), "Text")) . '-'
			. dlookup("users", "name", "userID=" . tosql($GetWelfare->fields(userID), "Text")) . '</td>
						<td	class="Data" align="center">' . dlookup("userdetails", "newIC",	"userID=" .	tosql($GetWelfare->fields(userID),	"Text")) . '</td>
						
						<td	class="Data" align="center"><font class="' . $colorStatus . '">' . $bajikanList[$status] . '</td>
						<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetWelfare->fields(approvedDate)) . '</td>
						<td class="Data" align="center">&nbsp;' . $GetWelfare->fields(rnoBond) . '</td>
						<td class="Data" align="center">&nbsp;' . toDate("d/m/Y", $GetWelfare->fields(rcreatedDate)) . '</td>';
		$cnt++;
		$bil++;
		$GetWelfare->MoveNext();
	}
	$GetWelfare->Close();

	print '	</table>
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
			if (is_int($i / 10)) print '<br />';
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Data : <b>' . $GetWelfare->RowCount() . '</b></td>
		</tr-->';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table></div>
</form>';

print '
<script language="JavaScript">
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
	
	function ITRActionButtonFinish(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak diselesaikan.\');
	        } else {
	          if(confirm(count + \' rekod hendak diselesaikan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }	   
	
</script>';

include("footer.php");
