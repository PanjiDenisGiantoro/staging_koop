<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	ACCcreditNoteList.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
// Import Select2 CSS and JavaScript
print '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>';

if (!isset($mm))    $mm = "ALL"; //date("m");
if (!isset($yy))    $yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))    $StartRec = 1;
if (!isset($pg))        $pg = 30;
if (!isset($q))            $q = "";
if (!isset($code))        $code = "ALL";
if (!isset($filter))    $filter = "0";
if (!isset($debt))        $debt = "";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName = "?vw=ACCcreditNoteList&mn=$mn"; //file name
$sFileRef  = "?vw=ACCcreditNote&mn=$mn"; // file ni pergi mane
$sFileRefPay  = "?vw=ACCDebtorPayment&mn=$mn"; // file ni pergi mane
$sFileRefInv  = "?vw=ACCinvoicedebtor&mn=$mn"; // file ni pergi mane
$title     =  "Nota Kredit"; //Title 

$IDName = get_session("Cookie_userName");


//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
    $sWhere = "";
    for ($i = 0; $i < count($pk); $i++) {

        $sWhere = "noteNo=" . tosql($pk[$i], "Text");
        $sSQL     = "DELETE FROM note WHERE " . $sWhere;
        $rs     = &$conn->Execute($sSQL);

        $sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 

        $docNo = dlookup("transactionacc", "docNo", $sWhere);

        $sSQL     = "DELETE FROM transactionacc WHERE " . $sWhere;
        $rs     = &$conn->Execute($sSQL);

        $strActivity = $_POST['Submit'] . 'Nota Kredit Dihapuskan - ' . $docNo;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
    }
}
//--- End   : deletion based on checked box -------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sSQL = "";
$sWhereYear = " WHERE noteNo LIKE 'CN%' AND c.status IN (5) AND c.addminus IN (1) AND (YEAR(tarikh_note) = " . $yy . " OR tarikh_note = '0000-00-00' OR tarikh_note is NULL)";

if ($q <> "" || $debt <> "") {
    if ($by == 1) {
        $sWhere .= " AND A.batchNo = B.ID";
        $sWhere .= " AND B.name like '%" . $q . "%'";
    } else if ($by == 2) {
        $sWhere .= " AND A.noteNo like '%" . $q . "%'";
    } else if ($by == 3) {
        $sWhere .= " AND A.companyID = $debt";
    }
}

if ($q <> "" || $debt <> "") $sWhere = " $sWhereYear $sWhere";
else $sWhere = " $sWhereYear";

if ($q <> "" || $debt <> "") {
    if ($by == 1 or $by == 3) {
        $sSQL = "SELECT A.* FROM note A
			LEFT JOIN generalacc b ON a.batchNo = b.ID
			LEFT JOIN transactionacc c ON a.noteNo = c.docNo";
    } else if ($by == 2) {
        $sSQL = "SELECT A.* FROM note A
			LEFT JOIN transactionacc c ON a.noteNo = c.docNo";
    }
} else {
    $sSQL = "SELECT A.* FROM note A 
		LEFT JOIN transactionacc c ON a.noteNo = c.docNo";
}
//if($mm <> "ALL") $sWhere .= " AND month( A.createdDate ) =" .$mm;
if ($mm <> "ALL") $sWhere .= " AND MONTH(A.tarikh_note) =" . $mm;
$sSQL = $sSQL . $sWhere . ' ORDER BY A.tarikh_note DESC';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec - 1);

$TotalRec     = $GetVouchers->RowCount();
$TotalPage     =  ($TotalRec / $pg);

$sqlYears     = "SELECT DISTINCT YEAR(tarikh_note) AS year FROM note WHERE noteNo LIKE 'CN%' AND tarikh_note IS NOT NULL AND tarikh_note != '' AND tarikh_note != 0 ORDER BY year ASC";
$rsYears     = $conn->Execute($sqlYears);

$debtorList = array();
$debtorVal  = array();
$sSQLDebtor = "SELECT name AS debtorName, ID AS debtorID FROM generalacc WHERE category = 'AC' ORDER BY ID ASC";
$rsDebtor     = &$conn->Execute($sSQLDebtor);
if ($rsDebtor->RowCount() <> 0) {
    while (!$rsDebtor->EOF) {
        array_push($debtorList, $rsDebtor->fields(debtorName));
        array_push($debtorVal, $rsDebtor->fields(debtorID));
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

print '
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
<div clas="row">
Cari Berdasarkan
	<select name="by" class="form-select-sm" onchange="toggleSearchFields(this.value);">';
if ($by == 1)    print '<option value="1" selected>Nama Batch</option>';
else print '<option value="1">Nama Batch</option>';
if ($by == 2)    print '<option value="2" selected>Nombor Debit Note</option>';
else print '<option value="2">Nombor Debit Note</option>';
if ($by == 3)    print '<option value="3" selected>Nama Syarikat</option>';
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

if (($IDName == 'admin') or ($IDName == 'superadmin')) {
    print '&nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}

print '
</div>';

//--------------------------------------------------Top right display total invoice and payment depends on year and month---------START

// query total credit note
$sqljumlahInv = "SELECT SUM(a.pymtAmt) AS totalInvoice,
				 GROUP_CONCAT(a.noteNo) AS docNoInv 
                 FROM note a
				 LEFT JOIN transactionacc c ON a.noteNo = c.docNo
				 WHERE c.status IN (5) AND c.addminus IN (1)
                 AND YEAR(a.tarikh_note) = $yy";

// concat query if any month is selected
if ($mm !== "ALL") {
    $sqljumlahInv .= " AND MONTH(a.tarikh_note) = $mm";
    $stringDesc = "Bulan $mm ";
}

$rsjumlahInv = $conn->Execute($sqljumlahInv);
$totalInvoice = $rsjumlahInv->fields['totalInvoice'];
$docNoInv = $rsjumlahInv->fields['docNoInv'];

// handle case where no noteNo is found
if (empty($docNoInv)) {
    $docNoInv = 'NULL'; // avoids SQL error in IN clause
} else {
    $docNoInv = "'" . str_replace(',', "','", $docNoInv) . "'";
}
// Display totals
print '
<div style="position: absolute; top: 70px; right: 25px;">
	<table border="0" cellspacing="0" cellpadding="5" width="100%">
		<tr class="table-warning">
			<td nowrap align="right" style="padding-right: 5px; width: 50%;"><u>Jumlah Pada ' . $stringDesc . ' Tahun ' . $yy . ':</u>&nbsp;<b>RM&nbsp;' . number_format($totalInvoice, 2) . '</b></td>
		</tr>
	</table>
</div>';

//--------------------------------------------------Top right display total invoice and payment depends on year and month---------END

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
						<td nowrap>Nombor Nota Kredit</td>
						<td nowrap align="center">Nama Batch</td>
						<td nowrap align="center">Tanggal</td>
						<td nowrap>Nama Syarikat</td>
						<td nowrap>Nomor Rujukan</td>
						<td nowrap align="left">Catatan</td>
						<td nowrap align="right">Jumlah (RM)</td>
						<td nowrap align="center">Tindakan</td>
					</tr>';
    $DRTotal = 0;
    $CRTotal = 0;
    while (!$GetVouchers->EOF && $cnt <= $pg) {

        // check has transaction or not
        $noTran     = false;
        $sql3 = "SELECT * FROM transactionacc WHERE docNo = '" . $GetVouchers->fields('noteNo') . "' AND addminus IN (0) ORDER BY ID";
        $rsDetail = $conn->Execute($sql3);
        if ($rsDetail->RowCount() < 1) $noTran = true;

        $jumlah = 0;

        $namacomp         = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(companyID), "Text"));
        $nama             = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(batchNo), "Text"));
        $catatan        = $GetVouchers->fields(catatan);
        $tarikh_note     = toDate("d/m/y", $GetVouchers->fields(tarikh_note));
        $today             = time();
        $amaun             = $GetVouchers->fields(pymtAmt);
        $knockoff        = $GetVouchers->fields(knockoff);
        if ($knockoff <> '')
            $inv         = $sFileRefInv . '&action=view&invNo=' . $knockoff . '&yy=' . $yy . '&mm=' . $mm;

        $cetak             = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCinvoicedebtorPrint.php?id=' . $GetVouchers->fields(noteNo) . '&note=1\')"></i>';
        $edit             = '<a href="' . $sFileRef . '&action=view&noteNo=' . tohtml($GetVouchers->fields['noteNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
        $editLock         = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
        $bayar             = '<i class="bx bxs-dollar-circle text-info" title="bayar" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'?vw=ACCDebtorPayment&action=new&noteNo=' . $GetVouchers->fields(noteNo) . '\')"></i>';
        $view             = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCinvoicedebtorView.php?id=' . $GetVouchers->fields(noteNo) . '&note=1\')"></i>';

        $sSQL2 = "SELECT g_lockstat FROM generalacc WHERE ID = " . $GetVouchers->fields(batchNo) . " ORDER BY ID";
        $rsDetail = &$conn->Execute($sSQL2);

        if ($noTran == false) {
            print '<tr>';
        } else {
            print '<tr style="background-color: rgba(255, 0, 0, 0.1) !important; --bs-table-accent-bg: transparent !important;" title="Dokumen ini tiada transaksi">';
        }

        print ' <td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>';

        if ($rsDetail->fields(g_lockstat) == 1) {
            print '
		<td class="Data" style="text-align: left; vertical-align: middle;" nowrap><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(noteNo)) . '">
		' . $GetVouchers->fields(noteNo) . '</td>';
        } else {
            print '
		<td class="Data" style="text-align: left; vertical-align: middle;" nowrap><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(noteNo)) . '">
		<a href="' . $sFileRef . '&action=view&noteNo=' . tohtml($GetVouchers->fields(noteNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
		' . $GetVouchers->fields(noteNo) . '</td>';
        }
        print '
		<td class="Data" style="text-align: center; vertical-align: middle;">' . $nama . '</td>
		<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_note . '</td>
		<td class="Data" style="text-align: left; vertical-align: middle;">' . $namacomp . '</td>
		';
        if ($knockoff <> '') print ' <td class="Data" style="vertical-align: middle;"><a href="' . $inv . '">' . $knockoff . '</td>';
        print '
		<td class="Data" style="text-align: left; vertical-align: middle;">' . $catatan . '</td>
		<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($amaun, 2) . '</td>
		';
        if (($rsDetail->fields['g_lockstat'] == 1) && ($GetVouchers->fields('batchNo') <> "")) {
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Include jQuery -->
<script src="assets/libs/jquery/jquery.min.js"></script>
<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
				window.open(\'?vw=ACCDebtorPayment&mn=915&action=new&noteNo=\' + pk,"sort","top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
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

</script>';