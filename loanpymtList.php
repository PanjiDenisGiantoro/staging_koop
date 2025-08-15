<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanpymtList.php
 *          Date 		: 	27/04/2004
 *********************************************************************************/
if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
$conn->debug = 1;

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$sFileName = 'loanpymtList.php';
$sFileRef  = 'loanpymtEdit.php';
$title     = 'Bayaran Pinjaman';

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sWhere = "ID=" . tosql($pk[$i], "Number");
		$sSQL = "DELETE FROM loanpayment WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------

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

//$GetList = ctLoanPymtList($q,$by,$yymm,$dept);
$sList = "SELECT c . *, c.pymtAmt as paymentAmt
			FROM `codegroup` a, general b
			RIGHT JOIN transaction c ON b.ID = c.deductID
			WHERE a.codeNo = b.code
			AND groupNo = 'PBYN' AND c.yrmth = " . tosql($yymm, "Text");

$GetList = &$conn->Execute($sList);

$GetList->Move($StartRec - 1);

$TotalRec = $GetList->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
	<tr>
		<td height="50" class="textFont">
			Bulan / Tahun  : 
			<select name="mm" class="data">';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>/
			<input type="text" name="yy" value="' . $yy . '" size="4" maxlength="4" class="data">
			<input type="submit" name="action1" value="Capai" class="but">
		</td>
	</tr>
    <tr valign="top" class="Header">
	   	<td align="left" >
			Carian melalui 
			<select name="by" class="Data">';
if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option value="1">Nombor Anggota</option>';
if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option value="2">Nama Anggota</option>';
if ($by == 3)	print '<option value="3" selected>No KP Baru</option>';
else print '<option value="3">No KP Baru</option>';
print '	</select>
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
print '		</select>&nbsp;';
//	if ($GetDetail->RowCount() <> 0 AND $GetList->RowCount() <> 0) {  
//	    print '<input type="button" class="but" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">&nbsp;&nbsp;';
//	}
print '	</td>
	</tr>';
if ($GetList->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont">&nbsp;</td>
						<td align="right" class="textFont">
							Paparan <SELECT name="pg" class="Data" onchange="doListAll();">';
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
	print '				</select>setiap mukasurat.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap>&nbsp;No / Nama Anggota </td>
						<td nowrap align="center">&nbsp;Nombor KP Baru</td>
						<td nowrap>&nbsp;Jabatan</td>
						<td nowrap align="center">&nbsp;Kod Potongan</td>						
						<td nowrap align="center">&nbsp;Pinjaman</td>						
						<td nowrap align="center">&nbsp;Bayaran</td>						
					</tr>';
	$totalPay = 0;
	while (!$GetList->EOF && $cnt <= $pg) {
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetList->fields(userID), "Text"));
		$no 	= dlookup("userdetails", "memberID", "userID=" . tosql($GetList->fields(userID), "Text"));
		$nama 	= dlookup("users", "name", "userID=" . tosql($GetList->fields(userID), "Text"));
		$loanType = dlookup("loans", "loanType", "loanID=" . tosql($GetList->fields(loanID), "Number"));
		print ' <tr>
					<td class="Data" align="right" height="25">' . $bil . '&nbsp;</td>
					<td class="Data">
					<!---<input type="checkbox" name="pk[]" value="' . tohtml($GetList->fields(ID)) . '">--->
					<a href="' . $sFileRef . '?pk=' . tohtml($GetList->fields(ID)) . '&yy=' . $yy . '&mm=' . $mm . '">
					&nbsp;' . $no . ' - ' . $nama . '</a></td>
					<td class="Data">&nbsp;' . dlookup("userdetails", "newIC", "userID=" . tosql($GetList->fields(userID), "Text")) . '</td>
					<td class="Data">&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Number")) . '</td>	<td class="Data">&nbsp;'
			. dlookup("general", "code", "ID=" . tosql($GetList->fields(deductID), "Number")) . '-'
			. dlookup("general", "name", "ID=" . tosql($GetList->fields(deductID), "Number")) . '
					</td>
					<td class="Data">&nbsp;';
		if ($GetList->fields(loanID) <> 0) {
			print	dlookup("general", "code", "ID=" . tosql($loanType, "Number")) . '-'
				. sprintf("%010d", $GetList->fields(loanID));
		}
		print '	</td>
				<td class="Data" align="right">' . $GetList->fields(paymentAmt) . '&nbsp;</td>
				</tr>';
		$cnt++;
		$bil++;
		$totalPay += $GetList->fields('paymentAmt');
		$GetList->MoveNext();
	}
	print '		<tr>
						<td class="DataB" align="right" colspan="6" height="20">Jumlah&nbsp;&nbsp;&nbsp;</td>
						<td class="DataB" align="right">' . number_format($totalPay, 2, '.', ',') . '&nbsp;</td>
					</tr>
				</table>
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
			print '<A href="' . $sFileName . '?yy=' . $yy . '&mm=' . $mm . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Rekod : <b>' . $GetList->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Mengenai Bayaran Pinjaman Anggota Bagi Bagi Bulan/Tahun -  ' . $mm . '/' . $yy . ' -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
</form>';

include("footer.php");

print '
<script language="JavaScript">
	var allChecked=false;
	function ITRViewSelectAll() {
	    e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}

	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak dihapuskan.\');
	        } else {
	          if(confirm(count + \' rekod hendak dihapuskan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }	   
		
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?yy=' . $yy . '&mm=' . $mm . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
