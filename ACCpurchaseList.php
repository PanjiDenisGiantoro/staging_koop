<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	ACCpurchaseList.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
// Import Select2 CSS and JavaScript
print '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>';

if (!isset($mm))	$mm = "ALL"; //date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 30;
if (!isset($q))			$q = "";
if (!isset($code))		$code = "ALL";
if (!isset($filter))	$filter = "0";
if (!isset($credit))	$credit = "";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName 	= "?vw=ACCpurchaseList&mn=$mn"; //file name
$sFileRef  	= "?vw=ACCpurchase&mn=$mn"; // file ni pergi mane
$title     	=  "PURCHASE ORDER"; //Title 

$IDName 	= get_session("Cookie_userName");

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {

		$sWhere = "purcNo=" . tosql($pk[$i], "Text");
		$sSQL 	= "DELETE FROM cb_purchase WHERE " . $sWhere;
		$rs 	= &$conn->Execute($sSQL);

		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 

		$docNo = dlookup("cb_purchaseinf", "docNo", $sWhere);

		$sSQL 	= "DELETE FROM cb_purchaseinf WHERE " . $sWhere;
		$rs 	= &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . ' Purchase Order Dihapuskan - ' . $docNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sSQL 	= "";
$sWhere = "  YEAR(tarikh_purc) = " . $yy;

if ($q <> "" || $credit <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.batchNo = B.ID";
		$sWhere .= " AND B.name like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND A.purcNo like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.companyID = $credit";
	}
}

$sWhere = " WHERE (" . $sWhere . ")";

$sSQL = "SELECT *, b.name
	FROM cb_purchase a 
	LEFT JOIN generalacc B ON A.batchNo = B.ID
	";

if ($mm <> "ALL") $sWhere .= " AND MONTH(A.tarikh_purc) =" . $mm;
$sSQL = $sSQL . $sWhere . ' ORDER BY A.purcNo DESC';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec - 1);

$TotalRec 	= $GetVouchers->RowCount();
$TotalPage 	= ($TotalRec / $pg);

$sqlYears 	= "SELECT DISTINCT YEAR(tarikh_purc) AS year FROM cb_purchase WHERE tarikh_purc IS NOT NULL AND tarikh_purc != '' AND tarikh_purc != 0 ORDER BY year ASC";
$rsYears 	= $conn->Execute($sqlYears);

$creditorList 	= array();
$creditorVal  	= array();
$sSQLCreditor 	= "SELECT name AS creditorName, ID AS creditorID FROM generalacc WHERE category = 'AB' ORDER BY ID ASC";
$rsCreditor 	= &$conn->Execute($sSQLCreditor);
if ($rsCreditor->RowCount() <> 0) {
	while (!$rsCreditor->EOF) {
		array_push($creditorList, $rsCreditor->fields(creditorName));
		array_push($creditorVal, $rsCreditor->fields(creditorID));
		$rsCreditor->MoveNext();
	}
}

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Baru" onClick="location.href=\'' . $sFileRef . '&action=new\';">
</div>';

print'
<div clas="row">
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
		
</div><br/>
<div clas="row">';
print 'Cari Berdasarkan
    <select name="by" class="form-select-sm" onchange="toggleSearchFields(this.value);">';
if ($by == 1)	print '<option value="1" selected>Nama Batch</option>';
else print '<option value="1">Nama Batch</option>';
if ($by == 2)	print '<option value="2" selected>Nombor Purchase Order</option>';
else print '<option value="2">Nombor Purchase Order</option>';
if ($by == 3)	print '<option value="3" selected>Nama Serikat</option>';
else print '<option value="3">Nama Serikat</option>';
print '</select>';

// Dropdown for selecting creditor
print '&nbsp;<select id="creditDropdown" name="credit" class="form-select-sm" style="display: ';
print ($by == 3) ? 'inline-block' : 'none';
print ';" onchange="document.MyForm.submit();">
        <option value="">- Semua -';
for ($i = 0; $i < count($creditorList); $i++) {
	print '<option value="' . $creditorVal[$i] . '" ';
	if ($credit == $creditorVal[$i]) print ' selected';
	print '>' . $creditorList[$i];
}
print '</select>';

// Input box for searching
print '<input id="searchInput" type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm" style="display: ';
print ($by != 3) ? 'inline-block' : 'none';
print ';">
<input type="submit" class="btn btn-sm btn-secondary" value="Cari">';

// print '&nbsp;&nbsp;
// 		<input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\'' . $sFileRef . '&action=new&jenis=' . $jenis . '\';">';

if (($IDName == 'superadmin') or ($IDName == 'admin')) {
	print '&nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}

print '
</div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<tr valign="top" class="Header">
<td align="left" >
</td>
</tr>';
if ($GetVouchers->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>
						<td align="right" class="textFont">';
	echo papar_ms($pg);
	print '</td>
					</tr>
				</table>
			</td>
		</tr>';
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap><b>Nombor Purchase Order</b></td>
						<td nowrap align="center"><b>Nama Batch</b></td>
						<td nowrap align="center"><b>Tanggal</b></td>
						<td nowrap><b>Nama Serikat | TIN LHDN</b></td>
						<td nowrap align="right"><b>Jumlah (RP)</b></td>
						<td nowrap align="center"><b>Tindakan</b></td>
					</tr>';

	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetVouchers->EOF && $cnt <= $pg) {

		// check has transaction or not
		$noTran 	= false;
		$sql2 		= "SELECT * FROM cb_purchaseinf WHERE docNo = '" . $GetVouchers->fields(purcNo) . "' AND addminus IN (0) ORDER BY ID";
		$rsDetail 	= $conn->Execute($sql2);
		if($rsDetail->RowCount()<1) $noTran = true;

		$purchaseNumber = $GetVouchers->fields('purcNo');

		$sSQL1 		= "SELECT pymtAmt FROM cb_purchaseinf WHERE docNo = '" . $purchaseNumber . "' AND addminus = '0'";
		$rs1		= &$conn->Execute($sSQL1);
		$jumlah 	= $rs1->fields(pymtAmt);

		$namakp 	= dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(companyID), "Text"));

		if ($GetVouchers->fields(batchNo)) {
			$nama 	= dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(batchNo), "Text"));
		} else {
			$nama 	= "-";
		}

		$tarikh_purc = toDate("d/m/y", $GetVouchers->fields(tarikh_purc));
		$cetak 		= '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCPurchaseOrderPrint.php?id=' . $GetVouchers->fields(purcNo) . '\')"></i>';
		$edit 		= '<a href="' . $sFileRef . '&action=view&purcNo=' . tohtml($GetVouchers->fields['purcNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
		$editLock 	= '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
		$view 		= '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCPurchaseOrderView.php?id=' . $GetVouchers->fields(purcNo) . '\')"></i>';

		if ($GetVouchers->fields(batchNo)) {
			$sSQL2 		= "SELECT g_lockstat FROM generalacc WHERE ID = " . $GetVouchers->fields(batchNo) . " ORDER BY ID";
			$rsDetail 	= &$conn->Execute($sSQL2);
		}

		$result = dlookup("generalacc", "b_tinLhdn", "ID=" . tosql($GetVouchers->fields("companyID"), "Text"));
		$tinLhdn = ($result !== null && $result !== "")
			? '<span style="color: green; font-size: 16px;" title="' . htmlspecialchars($result) . '">&#10004;</span>'
			: '<span style="color: red; font-size: 16px;">&#10008;</span>';

		if ($noTran == false) {
			print '<tr>';
		} else {
			print '<tr style="background-color: rgba(255, 0, 0, 0.1); height: 30px;" title="Dokumen ini tiada transaksi">';
		}

		print '
			<td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>';

		if ($GetVouchers->fields(batchNo)) {
			if ($rsDetail->fields(g_lockstat) == 1) {
				print '<td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(purcNo)) . '">
					' . $GetVouchers->fields(purcNo) . '</td>';
			} else {
				print '<td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(purcNo)) . '">
					<a href="' . $sFileRef . '&action=view&purcNo=' . tohtml($GetVouchers->fields(purcNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetVouchers->fields(purcNo) . '</td>';
			}
		} else {
			print '<td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(purcNo)) . '">
				<a href="' . $sFileRef . '&action=view&purcNo=' . tohtml($GetVouchers->fields(purcNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(purcNo) . '</td>';
		}

		print '
			<td class="Data" style="text-align: center; vertical-align: middle;">' . $nama . '</td>
			<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_purc . '</td>
			<td class="Data" style="text-align: left; vertical-align: middle;">' . $namakp . '&nbsp;' . $tinLhdn . '</td>
			<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($jumlah, 2) . '</td>
			';
		if (($rsDetail->fields['g_lockstat'] == 1) && ($GetVouchers->fields(batchNo) <> "")) {
			print '
			<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
			';
		} else {
			print '
			<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
			';
		}
		print '</tr>';
		$cnt++;
		$bil++;
		$GetVouchers->MoveNext();
	}
	$GetVouchers->Close();

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
			print '<A href="' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
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
			<td class="textFont">Jumlah Voucher : <b>' . $GetVouchers->RowCount() . '</b></td>
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
		document.location = "' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

	function toggleSearchFields(selectedValue) {
		var creditDropdown = document.getElementById("creditDropdown");
		var searchInput = document.getElementById("searchInput");

		if (selectedValue == 3) {
			if (!$("#creditDropdown").hasClass("select2-hidden-accessible")) {
				$("#creditDropdown").select2({
					placeholder: "- Pilih -"
				});
			}
			creditDropdown.style.display = "inline-block";
			searchInput.style.display = "none";
		} else {
			if ($("#creditDropdown").hasClass("select2-hidden-accessible")) {
				$("#creditDropdown").select2("destroy");
			}
			creditDropdown.style.display = "none";
			searchInput.style.display = "inline-block";
		}
	}

	$(document).ready(function() {
	';

	if ($by == 3) {
		print '$("#creditDropdown").select2({ placeholder: "- Pilih -" });
		toggleSearchFields(3);';
	} else {
		print 'toggleSearchFields(' . (int)$by . ');';
	}

	print '
	});

</script>';