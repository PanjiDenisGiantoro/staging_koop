<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	ACCpurchaseInvoiceList.php
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
if (!isset($jenis_cari))	$jenis_cari = "";
if (!isset($credit))	$credit = "";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName 	= "?vw=ACCpurchaseInvoiceList&mn=$mn"; //file name
$sFileRef  	= "?vw=ACCpurchaseInvoice&mn=$mn"; // file ni pergi mane
$sFileRefPO = "?vw=ACCpurchase&mn=$mn"; // file ni pergi mane
$title     	=  "Pembayaran Belian Invois"; //Title 

$IDName = get_session("Cookie_userName");


//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {

		$sWhere = "PINo=" . tosql($pk[$i], "Text");
		$sSQL 	= "DELETE FROM cb_purchaseinv WHERE " . $sWhere;
		$rs 	= &$conn->Execute($sSQL);

		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 

		$docNo = dlookup("transactionacc", "docNo", $sWhere);

		$sSQL 	= "DELETE FROM transactionacc WHERE " . $sWhere;

		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Purchase Invois Dihapuskan - ' . $docNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------
$sSQL 	= "";
$sWhereYear = " WHERE c.status NOT IN (6) AND (YEAR(tarikh_PI) = " . $yy . " OR tarikh_PI = '0000-00-00' OR tarikh_PI is NULL)"; //status 6 is debit note

if ($q <> "" || $credit <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.batchNo = B.ID";
		$sWhere .= " AND B.name like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND A.PINo like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.purcNo like '%" . $q . "%'";
	} else if ($by == 4) {
		$sWhere .= " AND A.companyID = $credit";
	}
}

if ($q <> "" || $credit <> "") $sWhere = " $sWhereYear $sWhere";
else $sWhere = " $sWhereYear";

$sSQL = "SELECT DISTINCT a.*, b.name, b.g_lockstat 
    FROM cb_purchaseinv a 
    LEFT JOIN generalacc b ON a.batchNo = b.ID
	LEFT JOIN transactionacc c ON a.PINo = c.docNo
    ";

if ($mm <> "ALL") $sWhere .= " AND MONTH(A.tarikh_PI) =" . $mm;
$sSQL = $sSQL . $sWhere . ' ORDER BY A.PINo DESC';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec - 1);

$TotalRec 	= $GetVouchers->RowCount();
$TotalPage 	=  ($TotalRec / $pg);
$jenisList_cari = array('Pembekal', 'Pemiutang');
$jenisVal_cari 	= array(1, 2);

$sqlYears 	= "SELECT DISTINCT YEAR(tarikh_PI) AS year FROM cb_purchaseinv WHERE tarikh_PI IS NOT NULL AND tarikh_PI != '' AND tarikh_PI != 0 ORDER BY year ASC";
$rsYears 	= $conn->Execute($sqlYears);

$creditorList = array();
$creditorVal  = array();
$sSQLCreditor = "SELECT name AS creditorName, ID AS creditorID FROM generalacc WHERE category = 'AB' ORDER BY ID ASC";
$rsCreditor = &$conn->Execute($sSQLCreditor);
if ($rsCreditor->RowCount() <> 0) {
	while (!$rsCreditor->EOF) {
		array_push($creditorList, $rsCreditor->fields('creditorName'));
		array_push($creditorVal, $rsCreditor->fields('creditorID'));
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

// summary chart
$sourceMain = "creditor";
$sourceSub = "accpurchaseinvoicelist";
include("ACCsummary.php");

print'
<br/>
<div clas="row">
    Cari Berdasarkan
<select name="by" class="form-select-sm" onchange="toggleSearchFields(this.value);">';
if ($by == 1)	print '<option value="1" selected>Nama Batch</option>';
else print '<option value="1">Nama Batch</option>';
if ($by == 2)	print '<option value="2" selected>Nombor Purchase Invoice</option>';
else print '<option value="2">Nombor Purchase Invoice</option>';
if ($by == 3)	print '<option value="3" selected>Nombor Purchase Order</option>';
else print '<option value="3">Nombor Purchase Order</option>';
if ($by == 4)	print '<option value="4" selected>Nama Syarikat</option>';
else print '<option value="4">Nama Syarikat</option>';
print '</select>';

// Dropdown for selecting creditor
print '&nbsp;<select id="creditDropdown" name="credit" class="form-select-sm" style="display: ';
print ($by == 4) ? 'inline-block' : 'none';
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
print ($by != 4) ? 'inline-block' : 'none';
print ';">
<input type="submit" class="btn btn-sm btn-secondary" value="Cari">';

if (($IDName == 'admin') or ($IDName == 'superadmin')) {

	print '&nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}

print '
</div>';

print '
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
						<td nowrap>Nombor Pembayaran Pemiutang</td>
						<td nowrap align="center">Nama Batch</td>
						<td nowrap align="center">Tanggal</td>
						<td nowrap>Nama Syarikat | TIN LHDN</td>
						<td nowrap>No Purchase Order</td>
						<td nowrap align="right">Amaun PO (RP)</td>
						<td nowrap align="right">Jumlah Invois (RP)</td>
						<td nowrap align="right">Bayaran (RP)</td>
						<td nowrap align="right">Baki Invois (RP)</td>
						<td nowrap align="center">Tindakan</td>
					</tr>';

	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetVouchers->EOF && $cnt <= $pg) {

		// check has transaction or not
		$noTran 	= false;
		$sql2 		= "SELECT * FROM transactionacc WHERE docNo = '" . $GetVouchers->fields('PINo') . "' AND addminus IN (0) ORDER BY ID";
		$rsDetail 	= $conn->Execute($sql2);
		if($rsDetail->RowCount()<1) $noTran = true;

		$jumlah = 0;

		$namakp 	= dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields('companyID'), "Text"));
		$nama 		= dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields('batchNo'), "Text"));
		$tarikh_PI 	= toDate("d/m/y", $GetVouchers->fields('tarikh_PI'));
		$cetak 		= '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCPurchaseInvoicePrint.php?id=' . $GetVouchers->fields('PINo') . '\')"></i>';
		$edit 		= '<a href="' . $sFileRef . '&action=view&PINo=' . tohtml($GetVouchers->fields['PINo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
		$editLock 	= '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
		$view 		= '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCPurchaseInvoiceView.php?id=' . $GetVouchers->fields('PINo') . '\')"></i>';

		$sSQL2 		= "SELECT g_lockstat FROM generalacc WHERE ID = " . $GetVouchers->fields('batchNo') . " ORDER BY ID";
		$rsDetail 	= &$conn->Execute($sSQL2);

		$sql3 		= "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = '" . $GetVouchers->fields('PINo') . "' ORDER BY ID";
		$rsDetail1 	= $conn->Execute($sql3);
		$amaun 		= $rsDetail1->fields('pymtAmt');

		$sqlPayment = "SELECT SUM(pymtAmt - balance) AS totalPayment
                     FROM billacc WHERE PINo = '" . $GetVouchers->fields('PINo') . "'";
		$rsBayaran 		= $conn->Execute($sqlPayment);
		$bayaran 		= $rsBayaran->fields['totalPayment'];
		$balance 		= $amaun - $bayaran;

		if ($GetVouchers->fields('purcNo'))
			$purcNo = $GetVouchers->fields('purcNo');
		else
			$purcNo = "-";

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

		if ($rsDetail->fields('g_lockstat') == 1) {
			print '
	<td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields('PINo')) . '">
	' . $GetVouchers->fields('PINo') . '</td>';
		} else {
			print '
	<td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields('PINo')) . '">
	<a href="' . $sFileRef . '&action=view&PINo=' . tohtml($GetVouchers->fields('PINo')) . '&yy=' . $yy . '&mm=' . $mm . '">
	' . $GetVouchers->fields('PINo') . '</td>';
		}

		print '
	<td class="Data" style="text-align: center; vertical-align: middle;">' . $nama . '</td>
	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_PI . '</td>
	<td class="Data" style="text-align: left; vertical-align: middle;">' . $namakp . '&nbsp;' . $tinLhdn . '</td>
	<td class="Data" style="text-align: left; vertical-align: middle;"><a href="' . $sFileRefPO . '&action=view&purcNo=' . $purcNo . '&yy=' . $yy . '&mm=' . $mm . '">' . $purcNo . '</td>';
		if ($GetVouchers->fields('purcNo') <> "") {
			print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format(dlookup("cb_purchase", "pymtAmt", "purcNo=" . tosql($GetVouchers->fields('purcNo'), "Text")), 2) . '</td>';
		} else {
			print '<td class="Data" style="text-align: right; vertical-align: middle;">-</td>';
		}
		print '
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($amaun, 2) . '</td>
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($bayaran, 2) . '</td>
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($balance, 2) . '</td>
	';
		if (($rsDetail->fields('g_lockstat') == 1) && ($GetVouchers->fields('batchNo') <> "")) {
			print '
	<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
	';
		} else {
			print '
	<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
	';
		}
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

		if (selectedValue == 4) {
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

	if ($by == 4) {
		print '$("#creditDropdown").select2({ placeholder: "- Pilih -" });
		toggleSearchFields(4);';
	} else {
		print 'toggleSearchFields(' . (int)$by . ');';
	}

	print '
	});

</script>';