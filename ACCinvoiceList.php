<?php
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	ACCinvoiceList.php
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
if (!isset($debt))		$debt = "";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName = "?vw=ACCinvoiceList&mn=$mn"; //file name
$sFileRef  = "?vw=ACCinvoicedebtor&mn=$mn"; // file ni pergi mane
$sFileRefPay  = "?vw=ACCDebtorPayment&mn=$mn"; // file ni pergi mane
$title     =  "Senarai Invois Penghutang"; //Title 

$IDName = get_session("Cookie_userName");

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {

		$sWhere = "invNo=" . tosql($pk[$i], "Text");
		$sSQL 	= "DELETE FROM cb_invoice WHERE " . $sWhere;
		$rs 	= &$conn->Execute($sSQL);

		$sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 

		$docNo = dlookup("transactionacc", "docNo", $sWhere);

		$sSQL 	= "DELETE FROM transactionacc WHERE " . $sWhere;
		$rs 	= &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . ' Invois Dihapuskan - ' . $docNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sSQL = "";
$sWhereYear = " WHERE c.status NOT IN (5) AND (YEAR(tarikh_inv) = " . $yy . " OR tarikh_inv = '0000-00-00' OR tarikh_inv is NULL)"; //status 5 is credit note

if ($q <> "" || $debt <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.batchNo = B.ID";
		$sWhere .= " AND B.name like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND A.invNo like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.companyID = $debt";
	}
}

if ($q <> "" || $debt <> "") $sWhere = " $sWhereYear $sWhere";
else $sWhere = " $sWhereYear";

if ($q <> "" || $debt <> "") {
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM cb_invoice A
			LEFT JOIN generalacc b ON a.batchNo = b.ID
			LEFT JOIN transactionacc c ON a.invNo = c.docNo";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM cb_invoice A
			LEFT JOIN transactionacc c ON a.invNo = c.docNo";
	}
} else {
	$sSQL = "SELECT	DISTINCT A.* FROM cb_invoice A 
		LEFT JOIN transactionacc c ON a.invNo = c.docNo
		";
}
//if($mm <> "ALL") $sWhere .= " AND month( A.createdDate ) =" .$mm;
if ($mm <> "ALL") $sWhere .= " AND MONTH(A.tarikh_inv) =" . $mm;
$sSQL = $sSQL . $sWhere . ' ORDER BY A.tarikh_inv DESC, A.invNo DESC';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec - 1);

$TotalRec 	= $GetVouchers->RowCount();
$TotalPage 	=  ($TotalRec / $pg);

$sqlYears 	= "SELECT DISTINCT YEAR(tarikh_inv) AS year FROM cb_invoice WHERE tarikh_inv IS NOT NULL AND tarikh_inv != '' AND tarikh_inv != 0 ORDER BY year ASC";
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
<form name="MyForm" id="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="invNo" id="invNo" value="'.$invNo.'">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Baru" onClick="location.href=\'' . $sFileRef . '&action=new\';">
</div>';

		// Check if the form is submitted
		if ($_POST['action'] == 'submitInvois') {
			$invNo = $_POST['invNo']; // Get the specific invoice number
			$randomID = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
			$eInvoisDate = date("Y-m-d H:i:s");

			// Construct the SQL query for the specific row
			$sWhere = "docNo = '" . $invNo . "' AND addminus = '0'";
			$sSQL = "UPDATE transactionacc SET 
						einvoisSubmit = '1',
						eInvoisDate = '" . $eInvoisDate . "',
						returnID = '" . $randomID . "' 
					WHERE " . $sWhere;

			// Execute the query
			$rs = $conn->Execute($sSQL);

				if ($rs) {
					print "<script>alert('Penghantaran e-invois berjaya untuk No. Invois: $invNo');</script>";
				} else {
					print "<script>alert('Gagal menghantar invois untuk No. Invois: $invNo');</script>";
				}
			

			print '<script>
			window.location = "?vw=ACCinvoiceList&mn=' . $mn . '";
			</script>';
		}

// summary chart
$sourceMain = "debtor";
$sourceSub = "accinvoicelist";
include("ACCsummary.php");

print'
<br/>
<div clas="row">
    Cari Berdasarkan
<select name="by" class="form-select-sm" onchange="toggleSearchFields(this.value);">';
if ($by == 1)	print '<option value="1" selected>Nama Batch</option>';
else print '<option value="1">Nama Batch</option>';
if ($by == 2)	print '<option value="2" selected>Nombor Invois</option>';
else print '<option value="2">Nombor Invois</option>';
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

// print '&nbsp;&nbsp <input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\'' . $sFileRef . '&action=new\';">';

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
						<td nowrap>Nombor Invois</td>
						<td nowrap align="center">Nama Batch</td>
						<td nowrap align="center">Tanggal</td>
						<td nowrap>Nama Syarikat | TIN LHDN</td>
						<td nowrap align="left">Catatan</td>
						<td nowrap align="right">Jum Invois (RM)</td>
						<td nowrap align="right">Bayaran (RM)</td>
						<td nowrap align="right">Baki Invois (RM)</td>
						<td nowrap align="center">Status</td>
						<td nowrap align="center">Status e-Invois</td>
						<td nowrap align="center">Tindakan</td>
					</tr>';

	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetVouchers->EOF && $cnt <= $pg) {

		// check has transaction or not
		$noTran 	= false;
		$sql3 		= "SELECT * FROM transactionacc WHERE docNo = '" . $GetVouchers->fields('invNo') . "' AND addminus IN (1) ORDER BY ID";
		$rsDetail 	= $conn->Execute($sql3);
		if($rsDetail->RowCount()<1) $noTran = true;

		$jumlah = 0;

		$invNo 			= $GetVouchers->fields('invNo');
		$addminus		= $GetVouchers->fields('addminus');
		$namacomp 		= dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields('companyID'), "Text"));
		$nama 			= dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields('batchNo'), "Text"));
		$description	= $GetVouchers->fields('description');
		$tarikh_inv 	= toDate("d/m/y", $GetVouchers->fields('tarikh_inv'));
		$tarikh_akhir 	= strtotime($GetVouchers->fields('tarikh_akhir'));
		$today 			= time();
		$amaun 			= $GetVouchers->fields('outstandingbalance');
		$sqlPayment 	= 	"SELECT SUM(outstandingbalance - balance) AS totalPayment 
								FROM cb_payments 
								WHERE invNo = '" . $invNo . "'";
		// $sqlPayment 	= "SELECT SUM(pymtAmt) AS totalPayment FROM transactionacc WHERE pymtReferC = " . tosql($GetVouchers->fields('invNo'), "Text") . " AND addminus IN (1)";
		$rsBayaran 		= $conn->Execute($sqlPayment);
		$bayaran 		= $rsBayaran->fields['totalPayment'];
		$balance 		= $amaun - $bayaran;
		$status			= $GetVouchers->fields('status');

		if ($status == 0) {
			$statusInv = "-";
		} elseif ($status == 1) {
			$statusInv = '<span class="badge badge-soft-primary"><b>Paid</b></span>';
		} elseif ($status == 2) {
			$statusInv = '<span class="badge badge-soft-danger"><b>Late</b></span>';
		} elseif ($status == 3) {
			$statusInv = '<span class="badge badge-soft-warning"><b>Unpaid</b></span>';
		}

		$cetak 			= '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCinvoicedebtorPrint.php?id=' . $GetVouchers->fields('invNo') . '\')"></i>';
		$edit 			= '<a href="' . $sFileRef . '&action=view&invNo=' . tohtml($GetVouchers->fields['invNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
		$editLock 		= '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
		$bayar 			= '<i class="bx bxs-dollar-circle text-info" title="bayar" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'?vw=ACCDebtorPayment&action=new&invNo=' . $GetVouchers->fields('invNo') . '\')"></i>';
		$view 			= '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCinvoicedebtorView.php?id=' . $GetVouchers->fields('invNo') . '\')"></i>';

		$sSQL2 		= "SELECT g_lockstat FROM generalacc WHERE ID = " . $GetVouchers->fields('batchNo') . " ORDER BY ID";
		$rsDetail 	= &$conn->Execute($sSQL2);

		$tin 		= dlookup("generalacc", "b_tinLhdn", "ID=" . tosql($GetVouchers->fields("companyID"), "Text"));
		$tinLhdn 	= ($tin !== null && $tin !== "")
			? '<span style="color: green; font-size: 16px;" title="' . htmlspecialchars($tin) . '">&#10004;</span>'
			: '<span style="color: red; font-size: 16px;">&#10008;</span>';

			if ($noTran == false) {
				print '<tr>';
			} else {
				print '<tr style="background-color: rgba(255, 0, 0, 0.1) !important; --bs-table-accent-bg: transparent !important;" title="Dokumen ini tiada transaksi">';
			}
			
		print ' <td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>';

		if (($rsDetail->fields['g_lockstat'] == 1) && ($GetVouchers->fields('batchNo') <> "")) {
			print '
		<td class="Data" style="text-align: left; vertical-align: middle;" nowrap><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields('invNo')) . '">
		' . $GetVouchers->fields('invNo') . '</td>';
		} else {
			print '
		<td class="Data" style="text-align: left; vertical-align: middle;" nowrap><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields('invNo')) . '">
		<a href="' . $sFileRef . '&action=view&invNo=' . tohtml($GetVouchers->fields('invNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
		' . $GetVouchers->fields('invNo') . '</td>';
		}
		print '
		<td class="Data" style="text-align: center; vertical-align: middle;">' . $nama . '</td>
		<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_inv . '</td>
		<td class="Data" style="text-align: left; vertical-align: middle;">' . $namacomp . '&nbsp;' . $tinLhdn . '</td>
		<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>
		<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($amaun, 2) . '</td>
		<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($bayaran, 2) . '</td>
		<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($balance, 2) . '</td>
		<td class="Data" style="text-align: center; vertical-align: middle;">' . $statusInv . '</td>
		';

		if ($addminus == 0) {
			$returnID = dlookup("transactionacc", "returnID", "docNo=" . tosql($invNo, "Text"));
			$einvoisSubmit = dlookup("transactionacc", "einvoisSubmit", "docNo=" . tosql($invNo, "Text"));
		
			print '<td class="Data" style="text-align: center; vertical-align: middle;">';
		
			if ($einvoisSubmit == 0) {
				$isValidTin = !empty($tin) && strtolower($tin) !== 'null' && strtolower($tin) !== 'undefined';
				
				if ($isValidTin && $noTran == false) { // mempunyai tin lhdn & transaksi
					print '<button type="button" class="btn btn-primary" onclick="submitLhdn(\'' . $invNo . '\')">Submit</button>';
				} elseif ($isValidTin && $noTran == true) { // mempunyai tin lhdn tetapi tidak mempunyai transaksi
					print '<button type="submit" class="btn btn-primary" disabled>Submit</button>';
				} else {
					print '<span title="Syarikat ini belum diisikan nombor tin LHDN.">';
					print '<button type="button" class="btn btn-primary" disabled>Submit</button>';
					print '</span>';
				}
		
			} elseif ($einvoisSubmit == 1) {
				print 'Return ID: ' . htmlspecialchars($returnID);
			} else {
				print '-';
			}
		
			print '</td>';
		}
			

		if (($rsDetail->fields['g_lockstat'] == 1) && ($GetVouchers->fields('batchNo') <> "")) {
			print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
		} else {
			print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
		}
		print '
		</tr>
		';
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

	function ITRActionButtonPay() {
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
				alert(\'Sila pilih satu	rekod untuk proses bayaran!\');
			} else {
				window.open(\'?vw=ACCDebtorPayment&mn=915&action=new&invNo=\' + pk,"sort","top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
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

		if (selectedValue == 3) {
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

	if ($by == 3) {
		print '$("#debtDropdown").select2({ placeholder: "- Pilih -" });
		toggleSearchFields(3);';
	} else {
		print 'toggleSearchFields(' . (int)$by . ');';
	}

	print '
	});

	function submitLhdn(invNo) {
		document.getElementById("invNo").value = invNo;
		document.MyForm.action.value = "submitInvois";
		document.getElementById("MyForm").submit();
	}

</script>';