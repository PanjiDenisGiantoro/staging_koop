<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanHL.php
 *          Date 		: 	01/05/2017
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($filter))	$filter = "ALL";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <>	2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");
$sFileName = 'loan.php';
$sFileRef  = 'biayaDokumen.php';
$title	   = "Senarai Permohonan Pembiayaan";
//--- Begin	: deletion based on	checked	box	-------------------------------------------------------
if ($action	== "delete") {
	$sWhere	= "";
	for ($i	= 0; $i	< count($pk); $i++) {
		$CheckLoan = ctLoan("", $pk[$i]);
		if ($CheckLoan->RowCount() == 1) {
			if ($CheckLoan->fields(status) < 3) {
				$sWhere	= "loanID="	. tosql($pk[$i], "Number");
				$sSQL =	"DELETE	FROM loans WHERE " . $sWhere;
				$rs	= &$conn->Execute($sSQL);

				$sSQL =	"DELETE	FROM loandocs WHERE	" .	$sWhere;
				$rs	= &$conn->Execute($sSQL);
			} else {
				print '<script>alert("Hanya permohonan belum siap proses boleh dihapus!");</script>';
			}
		}
	}
}
//--- End	: deletion based on	checked	box	-------------------------------------------------------
if ($action	== "batal") {
	$sWhere	= "";
	for ($i	= 0; $i	< count($pk); $i++) {
		$CheckLoan = ctLoan("", $pk[$i]);
		if ($CheckLoan->RowCount() == 1) {
			$statusloan = $CheckLoan->fields(status);
			$updatedBy	= get_session("Cookie_userName");
			$updatedDate = date("Y-m-d H:i:s");
			$sSQL =	'';
			$sWhere	= '';
			$sWhere	= '	loanID	= ' . $pk[$i];
			$sSQL	= '	UPDATE loans ';
			$sSQL	.= ' SET ' .
				' status	= 5 ' .
				' ,isCancel	= 1 ' .
				' ,cancelNote =' . tosql($sebab, "Text") .
				' ,cancelBy	=' . tosql($updatedBy, "Text") .
				' ,cancelDate='	. tosql($updatedDate, "Text");
			$sSQL .= ' WHERE ' . $sWhere;
			$rs	= &$conn->Execute($sSQL);
		}
	}
}
//--- Begin	: change application status	-------------------------------------------------------
if ($action	== "ubah") {
	$updatedBy	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$sSQL =	'';
	$sWhere	= '';
	$sWhere	= '	loanID	in (' . $str	. ')';
	$sWhere	= '	loanID	= ' . $pk[0];
	$sSQL	= '	UPDATE loans ';
	$sSQL	.= ' SET ' .
		' status	=' . tosql(0, "Text") .
		' ,updatedBy	=' . tosql($updatedBy, "Text") .
		' ,updatedDate='	. tosql($updatedDate, "Text");
	$sSQL .= ' WHERE ' . $sWhere;
	$rs	= &$conn->Execute($sSQL);
	$sWhere	= '	loanID	= ' . $pk[0];
	$sSQL =	"DELETE	FROM loandocs WHERE " . $sWhere;
	$rs	= &$conn->Execute($sSQL);
	$userID		= dlookup("loans", "userID", "loanID=" . $pk[0]);
	$sSQL	= "INSERT INTO loandocs (" .
		"loanID," .
		"userID)" .
		" VALUES (" .
		"'" . $pk[0] . "'," .
		"'" . $userID . "')";
	$rs = &$conn->Execute($sSQL);
}
//--- End -------------------------------------------------------
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
$status = $filter;
$sSQL = "";
$sWhere = "  loanID is not null AND A.statusHL IN (1)";
//where statements
if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . $dept;
	$sWhere .= " AND A.userID = B.userID AND A.statusHL IN (1)";
}
if ($status <> "ALL") $sWhere .= "  AND A.statusHL IN (1) AND A.status = " . $status;

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.userID = B.userID AND A.statusHL IN (1)";
		$sWhere .= " AND B.memberID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND A.userID = C.userID AND A.statusHL IN (1)";
		$sWhere .= " AND C.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.userID = B.userID AND A.statusHL IN (1)";
		$sWhere .= " AND B.newIC like '%" . $q . "%'";
	}
}
if ($id) $sWhere .= " AND A.loanType in (" . $id . ") AND A.statusHL IN (1)";

$sWhere = " WHERE (" . $sWhere . ")";
//fields selection
if ($q <> "") {
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM loans A, userdetails B";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM loans A, userdetails B, users C";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM loans A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM loans A ";
	}
}
$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
$GetLoan = &$conn->Execute($sSQL);
$GetLoan->Move($StartRec - 1);
$TotalRec =	$GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);
print '
<form name="MyForm"	action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr><td><b class="maroonText">'	. strtoupper($title) . '</b></td></tr>
<tr	valign="top" class="Header"><td	align="left" >
Carian melalui<select name="by" class="Data">';
if ($by	== 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option	value="1">Nombor Anggota</option>';
if ($by	== 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option	value="2">Nama Anggota</option>';
if ($by	== 3)	print '<option value="3" selected>No kad pengenalan</option>';
else print '<option	value="3">No kad pengenalan</option>';
print '</select>
		<input type="text" name="q"	value="" maxlength="50"	size="20" class="Data">
		<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;
		Jabatan<select	name="dept"	class="Data" onchange="document.MyForm.submit();">
		<option	value="">- Semua -';
for ($i	= 0; $i	< count($deptList); $i++) {
	print '	<option	value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '</select></td></tr><tr valign="top" class="textFont"><td>
		<table width="100%"><tr>
		<td	 class="textFont" align ="left">&nbsp;
		Pilihan Proses : 
		<select	name="filter" class="Data" onchange="document.MyForm.submit();">';
print '<option value="ALL">Semua';
for ($i	= 0; $i	< count($biayaList); $i++) {
	print '	<option	value="' . $biayaVal[$i] . '" ';
	if ($filter	== $biayaVal[$i]) print	' selected';
	print '>' . $biayaList[$i];
}
print '</select>&nbsp;';
if (($IDName == 'superadmin') or ($IDName == 'admin')) {
	if ($filter < 1) print 'Hapus permohonan <input type="button" class="but" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}
if ($filter == 3) print '&nbsp;&nbsp;Cetak dokumen proses :&nbsp;<input type="button" class="but" value="Cetak" onClick="ITRActionButtonDoc();">&nbsp;';
if ($filter	== 4) print 'Ubah ke proses kembali &nbsp;<input type="button" class="but" value="Ubah"	onClick="ITRActionButtonUbah();">';
print '</td><td	align="right" class="textFont">
		<!--input 4ype="button" class="but" value="Status" onClick="ITBActionButtonStatus();"-->
		Paparan	<SELECT	name="pg" class="Data" onchange="doListAll();">';
if ($pg	== 5)	print '<option value="5" selected>5</option>';
else print '<option	value="5">5</option>';
if ($pg	== 10)	print '<option value="10" selected>10</option>';
else print '<option	value="10">10</option>';
if ($pg	== 20)	print '<option value="20" selected>20</option>';
else print '<option	value="20">20</option>';
if ($pg	== 30)	print '<option value="30" selected>30</option>';
else print '<option	value="30">30</option>';
if ($pg	== 40)	print '<option value="40" selected>40</option>';
else print '<option	value="40">40</option>';
if ($pg	== 50)	print '<option value="50" selected>50</option>';
else print '<option	value="50">50</option>';
if ($pg	== 100)	print '<option value="100" selected>100</option>';
else print '<option	value="100">100</option>';
if ($pg	== 200)	print '<option value="200" selected>200</option>';
else print '<option	value="200">200</option>';
if ($pg	== 300)	print '<option value="300" selected>300</option>';
else print '<option	value="300">300</option>';
if ($pg	== 400)	print '<option value="400" selected>400</option>';
else print '<option	value="400">400</option>';
if ($pg	== 500)	print '<option value="500" selected>500</option>';
else print '<option	value="500">500</option>';
if ($pg	== 1000) print '<option	value="1000" selected>1000</option>';
else print '<option	value="1000">1000</option>';
print '</select>setiap	mukasurat.</td></tr>';
if (get_session("Cookie_groupID") == 2 && $filter == 3) {
	print '<tr><td class="textFont" align ="left">Batal Kelulusan :&nbsp;<input type="button" class="but" value="Batal" onClick="ITRActionButtonClick(\'batal\');">&nbsp;Sebab:&nbsp;<input type="text" name="sebab" value="" maxlength="60" size="50" class="Data"></td></tr>';
}
print '</table></td></tr>';
if ($GetLoan->RowCount() <>	0) {
	$bil = $StartRec;
	$cnt = 1;
	print '<tr valign="top"><td align="top">
		<table border="0" cellspacing="1" cellpadding="2" class="lineBG" width="100%">
		<tr	class="header">
		<td	nowrap>&nbsp;</td>
		<td	nowrap>&nbsp;Nombor rujukan/pembiayaan</td>
		<td	nowrap>&nbsp;No/Nama Anggota</td>
		<td	nowrap>&nbsp;No kad pengenalan</td>
		<td	nowrap align="center">&nbsp;Jumlah</td>
		<td	nowrap align="center">&nbsp;Status</td>
		<td	nowrap align="center">&nbsp;Tarikh ';
	if ($filter == "ALL" || $filter == "0") {
		print ' Memohon';
	} else {
		print $biayaList[$status];
	}
	print '</td></tr>';
	$amtLoan = 0;
	while (!$GetLoan->EOF && $cnt <= $pg) {
		$jabatan = dlookup("userdetails", "departmentID", "userID="	. tosql($GetLoan->fields(userID), "Text"));
		$blackList = dlookup("userdetails", "BlackListID", "userID="	. tosql($GetLoan->fields(userID), "Text"));

		// new amount
		$amt =	number_format(tosql($GetLoan->fields(loanAmt), "Number"), 2);
		$amtLoan = $amtLoan	+ tosql($GetLoan->fields(loanAmt), "Number");
		$status	= $GetLoan->fields(status);
		$colorStatus = "Data";
		if ($status	== 3) $colorStatus = "greenText";
		if ($status	== 4) $colorStatus = "redText";
		print '	<tr>
		<td	class="Data" align="right">' . $bil	. '&nbsp;</td>
		<td	class="Data"><input	type="checkbox" class="form-check-input"	name="pk[]"	value="' . tohtml($GetLoan->fields(loanID)) . '">
		<a href="' . $sFileRef . '?pk=' . tohtml($GetLoan->fields(loanID)) . '">&nbsp;'
			. $GetLoan->fields(loanNo) . '-&nbsp;';

		if ($GetLoan->fields(status) <> 5)
			print $adfdf = dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		else
			print $GetLoan->fields(cancelNote);

		print '</td>
		<!--td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetLoan->fields(loanID)) . '">
		<a href="' . $sFileRef . '?pk=' . tohtml($GetLoan->fields(loanID)) . '">'
			. dlookup("general",	"code",	"ID=" .	tosql($GetLoan->fields(loanType), "Number")) . '-'
			. sprintf("%010d", $GetLoan->fields(loanID)) . '</td-->
		<td	class="Data">&nbsp;'
			. dlookup("userdetails",	"memberID",	"userID=" .	tosql($GetLoan->fields(userID),	"Text")) . '-'
			. dlookup("users", "name", "userID="	. tosql($GetLoan->fields(userID), "Text")) . '';

		if ($blackList == 1) {
			print '<img src="images/delete.jpg" width="15" height="15"> </td>';
		}
		print '<td	class="Data" align="center">' . dlookup("userdetails", "newIC",	"userID=" .	tosql($GetLoan->fields(userID),	"Text")) . '&nbsp;</td>';

		print '<!--td	class="Data">&nbsp;' . dlookup("general",	"name",	"ID=" .	tosql($jabatan,	"Number")) . '</td-->
		<td	class="Data" align="right">' . $amt . '&nbsp;</td>
		<td	class="Data">&nbsp;<font class="' . $colorStatus . '">' . $biayaList[$status] . '';
		if ($filter == 0) {
			print '&nbsp;<input type=button value="DSR" class="but" onClick=window.open("DSRCetak.php?loanID=' . $GetLoan->fields(loanID) . '","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");></td></font></td> ';
		}
		print '	<td	class="Data" align="center">&nbsp;';
		if ($filter == "ALL" || $filter == 0) {
			print toDate("d/m/yy", $GetLoan->fields(applyDate));
		} elseif ($filter == 1) {
			$sql = "select prepareDate FROM `loandocs` where loanID = '" . $GetLoan->fields(loanID) . "'";
			$Get =  &$conn->Execute($sql);
			if ($Get->RowCount() > 0) print toDate("d/m/yy", $Get->fields(prepareDate));
		} elseif ($filter == 2) {

			$sql = "select reviewDate FROM `loandocs` where loanID = '" . $GetLoan->fields(loanID) . "'";
			$Get =  &$conn->Execute($sql);
			if ($Get->RowCount() > 0) print toDate("d/m/yy", $Get->fields(reviewDate));
		} elseif ($filter == 3) {

			$sql = "select ajkDate2 FROM `loandocs` where loanID = '" . $GetLoan->fields(loanID) . "'";
			$Get =  &$conn->Execute($sql);
			if ($Get->RowCount() > 0) print toDate("d/m/yy", $Get->fields(ajkDate2));
		} elseif ($filter == 4) {

			$sql = "select ajkDate2 FROM `loandocs` where loanID = '" . $GetLoan->fields(loanID) . "'";
			$Get =  &$conn->Execute($sql);
			if ($Get->RowCount() > 0) print toDate("d/m/yy", $Get->fields(ajkDate2));
		} elseif ($filter == 5) {

			print toDate("d/m/yy", $GetLoan->fields(cancelDate));
		}
		print '</td></tr>';
		$cnt++;
		$bil++;
		$GetLoan->MoveNext();
	}
	$GetLoan->Close();
	print '</table></td></tr><tr><td>';
	if ($TotalRec >	$pg) {
		print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont"	width="100%">';
		if ($TotalRec %	$pg	== 0) {
			$numPage = $TotalPage;
		} else {
			$numPage = $TotalPage +	1;
		}
		print '<tr><td class="textFont"	valign="top" align="left">Rekod	Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			if (is_int($i / 10)) print	'<br />';
			print '<A href="' . $sFileName . '?&StartRec=' . (($i	* $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&filter=' . $filter . '">';
			print '<b><u>' . (($i	* $pg) - $pg + 1) . '-' . ($i *	$pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td	class="textFont">Jumlah	Rekod :	<b>' . $GetLoan->RowCount()	. '</b></td>
		</tr>';
} else {

	if ($q == "") {
		print '
			<tr><td	align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr	size=1"></td></tr>';
	} else {
		print '
			<tr><td	align="center"><hr size=1"><b class="textFont">- Carian	rekod "' . $q . '" tidak jumpa	-</b><hr size=1"></td></tr>';
	}
}
print '
</table>
</form>';

include("footer.php");

print '
<script	language="JavaScript">
	var	allChecked=false;
	function ITRViewSelectAll()	{
		e =	document.MyForm.elements;
		allChecked = !allChecked;
		for(c=0; c<	e.length; c++) {
		  if(e[c].type=="checkbox" && e[c].name!="all")	{
			e[c].checked = allChecked;
		  }
		}
	}

	function ITRActionButtonClick(v) {
		  e	= document.MyForm;
		  if(e==null) {
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		  }	else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
			  if(e.elements[c].name=="pk[]"	&& e.elements[c].checked) {
				count++;
			  }
			}

			if(count==0) {
			  alert(\'Sila pilih rekod yang	hendak di\'	+ v	+\'kan.\');
			} else {
			  if(confirm(count + \'	rekod hendak di\' +	v +\'kan. Adakah anda pasti?\')) {
				e.action.value = v;
				e.submit();
			  }
			}
		  }
		}

	function ITRActionButtonStatus() {
		e =	document.MyForm;
		if(e==null)	{
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked)	{
					count++;
					pk = e.elements[c].value;
				}
			}

			if(count !=	1) {
				alert(\'Sila pilih satu	rekod sahaja untuk kemaskini status\');
			} else {
				window.open(\'loanStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');
			}
		}
	}

	function ITRActionButtonUbah() {
		e =	document.MyForm;
		if(e==null)	{
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked)	{
					count++;
					pk = e.elements[c].value;
				}
			}

			if(count !=	1) {
				alert(\'Sila pilih satu	rekod sahaja untuk proses kembali\');
			} else {
				e.action.value = \'ubah\';
				e.submit();
			}
		}
	}
	
	function ITRActionButtonDoc() {
		e =	document.MyForm;
		if(e==null)	{
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked)	{
					count++;
					pk = e.elements[c].value;
				}
			}

			if(count !=	1) {
				alert(\'Sila pilih satu	rekod cetakan dokumen proses!\');
			} else {
				window.open(\'biayaDokumenPrint.php?action=print&pk=\' + pk,\'status\',\'top=50,left=50,width=850,height=550,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');
			}
		}
	}

	function doListAll() {
		c =	document.forms[\'MyForm\'].pg;
		document.location =	"' . $sFileName	. '?&StartRec=1&pg=" + c.options[c.selectedIndex].value+"&filter=' . $filter . '";
	}

	function ITRActionButtonClickStatus(v) {
		  var strStatus="";
		  e	= document.MyForm;
		  if(e==null) {
			alert(\'Sila pastikan nama form	diwujudkan.!\');
		  }	else {
			count=0;
			j=0;
			for(c=0; c<e.elements.length; c++) {
			  if(e.elements[c].name=="pk[]"	&& e.elements[c].checked) {
				pk = e.elements[c].value;
				strStatus =	strStatus +	":"	+ pk;
				count++;
			  }
			}

			if(count==0) {
			  alert(\'Sila pilih rekod yang	hendak di\'	+ v	+ \'kan.\');
			} else {
			  if(confirm(count + \'	rekod hendak di\' +	v +	\'kan?\')) {
			  //e.submit();
			  window.location.href ="memberAktif.php?pk=" +	strStatus;
			  }
			}
		  }
		}

</script>';
