<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	ACCDebtorBulkList.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
// Import Select2 CSS and JavaScript
print '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>';

if (!isset($mm))    $mm = "ALL"; //date("m");
if (!isset($yy))    $yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
if (!isset($StartRec))    $StartRec = 1;
if (!isset($pg))        $pg = 30;
if (!isset($q))            $q = "";
if (!isset($code))        $code = "ALL";
if (!isset($filter))    $filter = "0";
if (!isset($jenis_cari))    $jenis_cari = "";
if (!isset($debt))        $debt = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName     = "?vw=ACCDebtorBulkList&mn=$mn"; //file name
$sFileRef      = "?vw=ACCDebtorPaymentBulk&mn=$mn"; // file ni pergi mane
$sFileRefInv = "?vw=ACCinvoicedebtor&mn=$mn"; // file ni pergi mane
$title         =  "Pembayaran Penghutang Bulk"; //Title 

$IDName     = get_session("Cookie_userName");

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
    $sWhere = "";
    for ($i = 0; $i < count($pk); $i++) {

        $sWhere = "RVNo=" . tosql($pk[$i], "Text");
        $sSQL     = "DELETE FROM cb_payments WHERE " . $sWhere;
        $rs     = &$conn->Execute($sSQL);
        $sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 
        $sSQL     = "DELETE FROM transactionacc WHERE " . $sWhere;
        $rs     = &$conn->Execute($sSQL);
    }
}
//--- End   : deletion based on checked box -------------------------------------------------------
$subQuery = "
    SELECT RVNo 
    FROM cb_payments 
    GROUP BY RVNo 
    HAVING COUNT(RVNo) > 1
";

$sSQL = "";
$sWhere = " a.RVNo IN ($subQuery) OR a.invNo IS NULL";
$sWhere .= " AND year(tarikh_RV) = " . $yy;

if ($q <> "" || $debt <> "") {
    if ($by == 1) {
        $getQ .= " AND b.name like '%" . $q . "%'";
    } else if ($by == 2) {
        $getQ .= " AND a.RVNo like '%" . $q . "%'";
    } else if ($by == 3) {
        $getQ .= " AND A.invNo like '%" . $q . "%'";
    } else if ($by == 4) {
        $getQ .= " AND A.companyID = $debt";
    }
}

$sWhere = " WHERE (" . $sWhere . ") " . $getQ . "";

// sql select dari table mana 
$sSQL = "SELECT *
		FROM cb_payments a 
		LEFT JOIN generalacc b ON a.batchNo = b.ID
		";

if ($mm <> "ALL") $sWhere .= " AND month( A.tarikh_RV ) =" . $mm;
$sSQL         = $sSQL . $sWhere . " group by RVNo order by RVNo desc";
$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec - 1);
$TotalRec     = $GetVouchers->RowCount();
$TotalPage     =  ($TotalRec / $pg);
$jenisList_cari = array('Penghutang');
$jenisVal_cari     = array(2);

$sqlYears     = "SELECT DISTINCT YEAR(tarikh_RV) AS year 
				FROM cb_payments 
				WHERE (RVNo IN ( SELECT RVNo FROM cb_payments GROUP BY RVNo HAVING COUNT(RVNo) > 1 ) AND invNo IS NULL) 
				OR invNo IS NULL 
				AND tarikh_RV IS NOT NULL 
				AND tarikh_RV != '' 
				AND tarikh_RV != 0 
				ORDER BY year ASC
			";
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
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
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
if ($by == 2)    print '<option value="2" selected>Nombor RV</option>';
else print '<option value="2">Nombor RV</option>';
if ($by == 3)    print '<option value="3" selected>Nombor Invois</option>';
else print '<option value="3">Nombor Invois</option>';
if ($by == 4)    print '<option value="4" selected>Nama Syarikat</option>';
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

print '&nbsp;&nbsp;
<input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\'' . $sFileRef . '&action=new&jenis=' . $jenis . '\';">';

if (($IDName == 'admin') or ($IDName == 'superadmin')) {

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
						<td nowrap align="center">&nbsp;</td>
						<td nowrap align="left">Nombor Bayaran</td>
						<td nowrap align="center">Nama Batch</td>
						<td nowrap align="center">Tanggal</td>
						<td nowrap align="left">Nama Syarikat</td>						
						<td nowrap align="left">Nombor Invois</td>
						<td nowrap align="right">Amaun Invois (RM)</td>
						<td nowrap align="right">Jumlah Bayaran (RM)</td>
						<td nowrap align="right">Saldo (RM)</td>
						<td nowrap align="center">Action</td>
					</tr>';

    $DRTotal = 0;
    $CRTotal = 0;
    while (!$GetVouchers->EOF && $cnt <= $pg) {
        $jumlah = 0;

        $RVNo         = $GetVouchers->fields(RVNo);
        $companyID     = $GetVouchers->fields(companyID);
        $bank         = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(kod_bank), "Text"));
        $namakp     = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(companyID), "Text"));
        $nama         = $GetVouchers->fields(name);
        $tarikh_RV     = toDate("d/m/y", $GetVouchers->fields(tarikh_RV));
        $inv         = $sFileRefInv . '&action=view&invNo=' . $GetVouchers->fields(invNo) . '&yy=' . $yy . '&mm=' . $mm;

        $noInv         = array();
        $linkInv         = array();
        $sSQL3         = "SELECT DISTINCT invNo FROM cb_payments WHERE RVNo = '$RVNo'";
        $GetInv     = &$conn->Execute($sSQL3);
        if ($GetInv && !$GetInv->EOF) {
            // Iterate through the results and store in $noInv array
            while (!$GetInv->EOF) {
                $noInv[]         = $GetInv->fields['invNo'];
                $linkInv[]         = $sFileRefInv . '&action=view&invNo=' . $GetInv->fields['invNo'] . '&yy=' . $yy . '&mm=' . $mm;
                $GetInv->MoveNext();
            }
        }

        $sSQL1 = "SELECT SUM(outstandingbalance)
			FROM cb_payments
			WHERE companyID = '$companyID'
			AND RVNo = '$RVNo'
			AND invNo IS NOT NULL
			AND ID IN (
				SELECT MIN(ID)
				FROM cb_payments
				WHERE companyID = '$companyID'
				AND RVNo = '$RVNo'
				AND invNo IS NOT NULL
				GROUP BY invNo
			);";
        $rs1         = &$conn->Execute($sSQL1);
        $amaun         = $rs1->fields('SUM(outstandingbalance)');

        $sSQL2 = "SELECT pymtAmt 
			FROM transactionacc 
			WHERE addminus = 0 
			  AND docNo = '$RVNo' 
			  AND pymtReferC IS NULL;
			";
        $rs2         = &$conn->Execute($sSQL2);
        $bayaran     = $rs2->fields(pymtAmt);

        $balance     = $amaun - $bayaran;

        $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCDebtorPaymentPrint.php?id=' . $GetVouchers->fields(RVNo) . '\')"></i>';
        $edit         = '<a href="' . $sFileRef . '&action=view&RVNo=' . tohtml($GetVouchers->fields['RVNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
        $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
        $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCDebtorPaymentView.php?id=' . $GetVouchers->fields(RVNo) . '\')"></i>';

        print ' <tr>
	<td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>';

        if ($GetVouchers->fields(g_lockstat) == 1) {
            print '
	<td class="Data" nowrap style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(RVNo)) . '">
	' . $GetVouchers->fields(RVNo) . '</td>';
        } else {
            print '
	<td class="Data" nowrap style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(RVNo)) . '">
	<a href="' . $sFileRef . '&action=view&RVNo=' . tohtml($GetVouchers->fields(RVNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
	' . $GetVouchers->fields(RVNo) . '</td>';
        }
        print '	
	<td class="Data" style="text-align: center; vertical-align: middle;">' . $nama . '</td>
	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_RV . '</td>
	<td class="Data" style="text-align: left; vertical-align: middle;">' . $namakp . '</td>';
        // Check if both $invoice and and its link are not empty
        if (!empty($noInv) && !empty($linkInv)) {
            $links = array();

            // Iterate through invoices
            foreach ($noInv as $key => $invoice) {
                if (isset($linkInv[$key]) && !empty($invoice)) {
                    // Create a link for each valid invoice and store it in $links array
                    $links[] = '<a href="' . $linkInv[$key] . '">' . $invoice . '</a>';
                }
            }

            if (!empty($links)) {
                // Display all links in a single table cell
                print '<td class="Data" style="text-align: left; vertical-align: middle;">' . implode(', ', $links) . '</td>';
            } else {
                // If no valid links were found
                print '<td class="Data" style="text-align: left; vertical-align: middle;">-</td>';
            }
        } else {
            // If no invoices or links found
            print '<td class="Data" style="text-align: left; vertical-align: middle;">-</td>';
        }
        print '
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($amaun, 2) . '</td>
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($bayaran, 2) . '</td>
	<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($balance, 2) . '</td>
	';
        if (($GetVouchers->fields['g_lockstat'] == 1) && ($GetVouchers->fields('batchNo') <> "")) {
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