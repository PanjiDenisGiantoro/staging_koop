<?php

print'
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
';
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	ACCconsolidateList.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
if (!isset($mm))	$mm = "ALL"; //date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 30;
if (!isset($q))			$q = "";
if (!isset($code))		$code = "ALL";
if (!isset($filter))	$filter = "0";
if (!isset($debt))		$debt = "";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName = "?vw=ACCconsolidateList&mn=$mn"; //file name
$sFileRef  = "?vw=ACCconsolidate&mn=$mn"; // file ni pergi mane
$title     =  "Bulk e-Invoices"; //Title 

$IDName = get_session("Cookie_userName");


//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {

		$sWhere = "ciNo=" . tosql($pk[$i], "Text");

		$docNo = dlookup("consolidate", "ciNo", $sWhere);

		$sSQL = "DELETE FROM consolidate WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);

		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 
		$sSQL = "DELETE FROM consolidate WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . ' Dokumen Dihapuskan - ' . $docNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sSQL = "";
$sWhere = "  YEAR(dtFrom) = " . $yy;

if ($q <> "" || $debt <> "") {

	if ($by == 2) {
		$sWhere .= " AND ciNo like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND companyID = $debt";
	}
}

$sWhere = " WHERE (" . $sWhere . ")";

$sSQL = "SELECT *
	FROM consolidate
	";

if ($mm <> "ALL") $sWhere .= " AND MONTH(dtFrom) =" . $mm;
$sSQL = $sSQL . $sWhere . ' ORDER BY ciNo DESC';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$getCI = &$conn->Execute($sSQL);
$getCI->Move($StartRec - 1);

$TotalRec = $getCI->RowCount();
$TotalPage =  ($TotalRec / $pg);

$sqlYears = "SELECT DISTINCT YEAR(dtFrom) AS year FROM consolidate WHERE dtFrom IS NOT NULL AND dtFrom != '' AND dtFrom != 0 ORDER BY year ASC";
$rsYears = $conn->Execute($sqlYears);

$debtorList = array();
$debtorVal  = array();
$sSQLDebtor = "SELECT name AS debtorName, ID AS debtorID FROM generalacc WHERE category = 'AC' ORDER BY ID ASC";
$rsDebtor = &$conn->Execute($sSQLDebtor);
if ($rsDebtor->RowCount() <> 0) {
	while (!$rsDebtor->EOF) {
		array_push($debtorList, $rsDebtor->fields('debtorName'));
		array_push($debtorVal, $rsDebtor->fields('debtorID'));
		$rsDebtor->MoveNext();
	}
}

print'
<script>
function selectDate(rpt) {
	url = "selDateOpt.php?rpt="+rpt+"&id='.$ID.'";
	window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
}
</script>
';

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
';

include ("ACCdateRange.php");

print'
			Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yy == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
<br/><br/>';

print 'Cari Berdasarkan
    <select name="by" class="form-select-sm" onchange="toggleSearchFields(this.value);">';
if ($by == 2)	print '<option value="2" selected>No Quotation</option>';
else print '<option value="2">No Quotation</option>';
if ($by == 3)	print '<option value="3" selected>Nama Syarikat</option>';
else print '<option value="3">Nama Syarikat</option>';
print '</select>';

// Dropdown for selecting debtor
print '&nbsp;<select id="debtDropdown" name="debt" class="form-select-sm" style="display: ';
print ($by == 3) ? 'inline-block' : 'none';
print ';" onchange="document.MyForm.submit();">
        <option value="">- Semua -';
for ($i = 0; $i < count($debtorList); $i++) {
	print '<option value="' . $debtorVal[$i] . '" ';
	if ($debt == $debtorVal[$i]) print ' selected';
	print '>' . $debtorList[$i];
}
print '</select>';

// Input box for searching
print '<input id="searchInput" type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm" style="display: ';
print ($by != 3) ? 'inline-block' : 'none';
print ';">
<input type="submit" class="btn btn-sm btn-secondary" value="Cari">';

print '&nbsp;&nbsp;';

if (($IDName == 'admin') or ($IDName == 'superadmin')) {
	print '&nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}

// print' </td>
// 	</tr>';
print '
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr valign="top" class="Header">
<td align="left" >
</td>
</tr>';

if ($getCI->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>
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
	print '				</select>setiap halaman.
						</td>
					</tr>
				</table>
			</td>
		</tr>';
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap align="center">&nbsp;</td>
						<td nowrap><b>Nombor Dokumen</b></td>
						<td nowrap align="center"><b>Tarikh</b></td>
						<td nowrap align="right"><b>Jumlah (RP)</b></td>
						<td nowrap align="center"><b>Status e-Invois</b></td>
						<td nowrap align="center"><b>Action</b></td>
					</tr>';
	$DRTotal = 0;
	$CRTotal = 0;
	while (!$getCI->EOF && $cnt <= $pg) {
		$jumlah = 0;


		$dtFrom 	= toDate("d/m/y", $getCI->fields('dtFrom'));


		print ' <tr><td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>';


		print '
			<td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($getCI->fields('ciNo')) . '">
			<a href="' . $sFileRef . '&action=view&ciNo=' . tohtml($getCI->fields('ciNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
			' . $getCI->fields('ciNo') . '</td>';


		print '
		 <td class="Data" style="text-align: center; vertical-align: middle;">' . $dtFrom . '</td>
		 <td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($getCI->fields('outstandingbalance'), 2) . '</td>
		 <td class="Data" style="text-align: center; vertical-align: middle;">-</td>
		 <td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		</tr>';
		$cnt++;
		$bil++;
		$getCI->MoveNext();
	}
	$getCI->Close();

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
			print '<A href="' . $sFileName . '?yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
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
			<td class="textFont">Jumlah Voucher : <b>' . $getCI->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . ' Bagi Bulan/Tahun - ' . $mm . '/' . $yy . ' -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
</form></div>';

include("footer.php");

print '
<script language="JavaScript">

	function open_(url) {
		window.open(url,"pop","top=10,left=10,width=990,height=600, scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
	}

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
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
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
		document.location = "' . $sFileName . '?yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

	function toggleSearchFields(selectedValue) {
		var debtDropdown = document.getElementById("debtDropdown");
		var searchInput = document.getElementById("searchInput");
		if (selectedValue == 3) {
			debtDropdown.style.display = "inline-block";
			searchInput.style.display = "none";
		} else {
			debtDropdown.style.display = "none";
			searchInput.style.display = "inline-block";
		}
	}
</script>';