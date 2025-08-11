<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	ACCDebtorList.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
// Import Select2 CSS and JavaScript
print '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>';

if (!isset($mm))	$mm = "ALL"; //date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 30;
if (!isset($q))			$q = "";
if (!isset($code))		$code = "ALL";
if (!isset($filter))	$filter = "0";
if (!isset($jenis_cari))	$jenis_cari = "";
if (!isset($debt))		$debt = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName 		= "?vw=ACCDebtorList&mn=$mn"; //file name
$sFileRef  		= "?vw=ACCDebtorPayment&mn=$mn"; // file ni pergi mane
$sFileRefInv  	= "?vw=ACCinvoicedebtor&mn=$mn"; // file ni pergi mane
$sFileRefNote  	= "?vw=ACCcreditNote&mn=$mn"; // file ni pergi mane
$title     		=  "Pembayaran Penghutang"; //Title 

$IDName = get_session("Cookie_userName");


//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sWhere = "RVNo=" . tosql($pk[$i], "Text");
		$sSQL 	= "DELETE FROM cb_payments WHERE " . $sWhere;
		$rs 	= &$conn->Execute($sSQL);

		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 

		$docNo = dlookup("transactionacc", "docNo", $sWhere);

		$sSQL 	= "DELETE FROM transactionacc WHERE " . $sWhere;
		$rs 	= &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Terima Bayaran Dihapuskan - ' . $docNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------

//if RVNo is 1 then meaning it is not bulk payment
$subQuery = "
    SELECT RVNo 
    FROM cb_payments 
    GROUP BY RVNo 
    HAVING COUNT(RVNo) = 1
";

$sSQL = "";
$getQ = " (a.RVNo IN ($subQuery) AND a.invNo IS NOT NULL)"; //if invNo doesn't have value then it must be empty string. Only null invNo is for debtor bulk list.
$getQ .= " AND year( tarikh_RV ) = " . $yy;

if ($q <> "" || $debt <> "") {
	if ($by == 1) {
		$getQ .= " AND b.name like '%" . $q . "%'";
	} else if ($by == 2) {
		$getQ .= " AND a.RVNo like '%" . $q . "%'";
	} else if ($by == 3) {
		if (strpos($q, 'opening') !== false || strpos($q, 'balance') !== false) {
			// If 'opening', 'balance', or 'opening balance' is found in the search query
			$getQ .= " AND A.invNo = ''";
		} else {
			$getQ .= " AND A.invNo like '%" . $q . "%'";
		}
	} else if ($by == 4) {
		$getQ .= " AND A.companyID = $debt";
	}
}

$getQ = " WHERE (" . $getQ . ")";

$sSQL = "SELECT *, b.name, b.g_lockstat 
		FROM cb_payments a 
		LEFT JOIN generalacc b ON a.batchNo = b.ID
		";

if ($mm <> "ALL") $getQ .= " AND month( A.tarikh_RV ) =" . $mm;
$sSQL = $sSQL . $getQ . " order by RVNo desc";

$GetBaucers = &$conn->Execute($sSQL);
$GetBaucers->Move($StartRec - 1);

$TotalRec 	= $GetBaucers->RowCount();
$TotalPage 	=  ($TotalRec / $pg);
$jenisList_cari = array('Penghutang');
$jenisVal_cari 	= array(2);

$sqlYears 	= "SELECT DISTINCT YEAR(tarikh_RV) AS year 
				FROM cb_payments 
				WHERE (RVNo IN ( SELECT RVNo FROM cb_payments GROUP BY RVNo HAVING COUNT(RVNo) = 1 ) AND invNo IS NOT NULL) 
				AND tarikh_RV IS NOT NULL 
				AND tarikh_RV != '' 
				AND tarikh_RV != 0 
				ORDER BY year ASC
			";
$rsYears 	= $conn->Execute($sqlYears);

$debtorList = array();
$debtorVal  = array();
$sSQLDebtor = "SELECT name AS debtorName, ID AS debtorID FROM generalacc WHERE category = 'AC' ORDER BY ID ASC";
$rsDebtor 	= &$conn->Execute($sSQLDebtor);
if ($rsDebtor->RowCount() <> 0) {
	while (!$rsDebtor->EOF) {
		array_push($debtorList, $rsDebtor->fields('debtorName'));
		array_push($debtorVal, $rsDebtor->fields('debtorID'));
		$rsDebtor->MoveNext();
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
$sourceMain = "";
$sourceSub = "accdebtorlist";
include("ACCsummary.php");

print'<br/>
<div clas="row">
    Carian Melalui
	<select name="by" class="form-select-sm" onchange="toggleSearchFields(this.value);">';
if ($by == 1)	print '<option value="1" selected>Nama Batch</option>';
else print '<option value="1">Nama Batch</option>';
if ($by == 2)	print '<option value="2" selected>Nombor RV</option>';
else print '<option value="2">Nombor RV</option>';
if ($by == 3)	print '<option value="3" selected>Nombor Invois</option>';
else print '<option value="3">Nombor Invois</option>';
if ($by == 4)	print '<option value="4" selected>Nama Syarikat</option>';
else print '<option value="4">Nama Syarikat</option>';
print '</select>';

// Dropdown for selecting debtor
print '&nbsp;<select id="debtDropdown" name="debt" class="form-select-sm" style="display: ';
print ($by == 4) ? 'inline-block' : 'none';
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
print ($by != 4) ? 'inline-block' : 'none';
print ';">
<input type="submit" class="btn btn-sm btn-secondary" value="Cari">';
// print '&nbsp;&nbsp; <input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\'' . $sFileRef . '&action=new&jenis=' . $jenis . '\';">';

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
if ($GetBaucers->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All</td>
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
						<td nowrap align="center">&nbsp;</td>
						<td nowrap style="text-align: left; vertical-align: bottom;">Nombor Bayaran</td>
						<td nowrap style="text-align: center; vertical-align: bottom;">Nama Batch</td>
						<td nowrap style="text-align: center; vertical-align: bottom;">Tarikh</td>
						<td nowrap style="text-align: left; vertical-align: bottom;">Nama Syarikat</td>						
						<td nowrap style="text-align: center; vertical-align: bottom;">Nombor Invois</td>
						<td nowrap><div style="text-align: right; white-space: nowrap;">Amaun<br>Invois (RM)</div></td>
						<td nowrap><div style="text-align: right; white-space: nowrap;">Jumlah<br>Bayaran (RM)</div></td>
						<td nowrap style="text-align: right; vertical-align: bottom;">Baki (RM)</td>
						<td nowrap style="text-align: center; vertical-align: bottom;">Tindakan</td>
					</tr>';

	// Determining credit note -------------START
	$sqlCreditNote = "
			SELECT DISTINCT docNo
			FROM transactionacc 
			WHERE status IN (5) AND addminus IN (1)";
	$rsCreditNote = $conn->Execute($sqlCreditNote);

	$creditNoteInvList = array();
	if ($rsCreditNote->RowCount() <> 0) {
		while (!$rsCreditNote->EOF) {
			array_push($creditNoteInvList, $rsCreditNote->fields('docNo'));
			$rsCreditNote->MoveNext();
		}
	}
	// Determining credit note -------------END

	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetBaucers->EOF && $cnt <= $pg) {

		// check has transaction or not
		$noTran 	= false;
		$sql2 		= "SELECT * FROM transactionacc WHERE docNo = '" . $GetBaucers->fields('RVNo') . "' AND addminus IN (1) ORDER BY ID";
		$rsDetail 	= $conn->Execute($sql2);
		if($rsDetail->RowCount()<1) $noTran = true;

		$jumlah = 0;

		$bank 		= dlookup("generalacc", "name", "ID=" . tosql($GetBaucers->fields('kod_bank'), "Text"));
		$namakp 	= dlookup("generalacc", "name", "ID=" . tosql($GetBaucers->fields('companyID'), "Text"));
		$nama 		= $GetBaucers->fields('name');
		$tarikh_RV 	= toDate("d/m/y", $GetBaucers->fields('tarikh_RV'));

		if ($GetBaucers->fields('invNo') <> '') {

			if (in_array($GetBaucers->fields('invNo'), $creditNoteInvList)) { //if credit note then open credit note
				$inv = $sFileRefNote . '&action=view&invNo=' . $GetBaucers->fields('invNo') . '&yy=' . $yy . '&mm=' . $mm;
			} else { //if not credit note then open normal invoice
				$inv = $sFileRefInv . '&action=view&invNo=' . $GetBaucers->fields('invNo') . '&yy=' . $yy . '&mm=' . $mm;
			}
		} else $inv = "Opening Balance";
		$amaun 		= $GetBaucers->fields('outstandingbalance');
		$sqlPayment = 	"SELECT outstandingbalance - balance AS totalPayment 
								FROM cb_payments 
								WHERE invNo = '" . $GetBaucers->fields('invNo') . "'";
		$rsBayaran 	= $conn->Execute($sqlPayment);
		$bayaran 	= $rsBayaran->fields['totalPayment'];
		$balance 	= $amaun - $bayaran;
		$cetak 		= '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCDebtorPaymentPrint.php?id=' . $GetBaucers->fields('RVNo') . '\')"></i>';
		$edit 		= '<a href="' . $sFileRef . '&action=view&RVNo=' . tohtml($GetBaucers->fields['RVNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
		$editLock 	= '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
		$view 		= '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCDebtorPaymentView.php?id=' . $GetBaucers->fields('RVNo') . '\')"></i>';

		if ($noTran == false) {
			print '<tr>';
		} else {
			print '<tr style="background-color: rgba(255, 0, 0, 0.1); height: 30px;" title="Dokumen ini tiada transaksi">';
		}

		print ' 
	<td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>';

	if ($GetBaucers->fields('g_lockstat') == 1) {
		print '
	<td class="Data" nowrap style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetBaucers->fields('RVNo')) . '">
	' . $GetBaucers->fields('RVNo') . '</td>';
	} else {
		print '
	<td class="Data" nowrap style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetBaucers->fields('RVNo')) . '">
	<a href="' . $sFileRef . '&action=view&RVNo=' . tohtml($GetBaucers->fields('RVNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
	' . $GetBaucers->fields('RVNo') . '</td>';
	}
		print '	
	<td class="Data" style="text-align: center; vertical-align: middle;">' . $nama . '</td>
	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_RV . '</td>
	<td class="Data" style="text-align: left; vertical-align: middle;">' . $namakp . '</td>
	';
	if ($GetBaucers->fields('invNo') <> '') print ' <td class="Data" style="text-align: center; vertical-align: middle;"><a href="' . $inv . '">' . $GetBaucers->fields('invNo') . '</td>';
	else print ' <td class="Data" style="text-align: center; vertical-align: middle;"><span style="color: blue;">Opening Balance</span></td>';
	print '
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($amaun, 2) . '</td>
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($bayaran, 2) . '</td>
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($balance, 2) . '</td>
	';
	if (($GetBaucers->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
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
		$GetBaucers->MoveNext();
	}
	$GetBaucers->Close();

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
		print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
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
			<td class="textFont">Jumlah Baucer : <b>' . $GetBaucers->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . ' Bagi Bulan/Tahun - ' . $mm . '/' . $yy . ' -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
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
		document.location = "' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

	function toggleSearchFields(selectedValue) {
		var debtDropdown = document.getElementById("debtDropdown");
		var searchInput = document.getElementById("searchInput");

		if (selectedValue == 4) {
			if (!$("#debtDropdown").hasClass("select2-hidden-accessible")) {
				$("#debtDropdown").select2({
					placeholder: "- Pilih -"
				});
			}
			debtDropdown.style.display = "inline-block";
			searchInput.style.display = "none";
		} else {
			if ($("#debtDropdown").hasClass("select2-hidden-accessible")) {
				$("#debtDropdown").select2("destroy");
			}
			debtDropdown.style.display = "none";
			searchInput.style.display = "inline-block";
		}
	}

	$(document).ready(function() {
	';

	if ($by == 4) {
		print '$("#debtDropdown").select2({ placeholder: "- Pilih -" });
		toggleSearchFields(4);';
	} else {
		print 'toggleSearchFields(' . (int)$by . ');';
	}

	print '
	});
		
</script>';