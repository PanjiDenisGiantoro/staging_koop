<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	ACCSingleEntryList.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
if (!isset($mm))    $mm = "ALL"; //date("m");
if (!isset($yy))    $yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

if (!isset($StartRec))    $StartRec = 1;
if (!isset($pg))        $pg = 30;
if (!isset($q))            $q = "";
if (!isset($code))        $code = "ALL";
if (!isset($filter))    $filter = "0";
if (!isset($statusFilter)) $statusFilter = "ALL";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName = "?vw=ACCSingleEntryList&mn=$mn";
$sFileRef  = "?vw=ACCSingleEntry&mn=$mn";
$title     =  "Single Entry";

$IDName = get_session("Cookie_userName");


//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
    $sWhere = "";
    for ($i = 0; $i < count($pk); $i++) {

        $sWhere = "SENO=" . tosql($pk[$i], "Text");

        $docNo = dlookup("singleentry", "SENO", $sWhere);

        $sSQL = "DELETE FROM singleentry WHERE " . $sWhere;
        $rs = &$conn->Execute($sSQL);

        $sWhere = "docNo=" . tosql($pk[$i], "Text"); //new 
        $sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
        $rs = &$conn->Execute($sSQL);

        $strActivity = $_POST['Submit'] . ' Jurnal Entry Dihapuskan - ' . $docNo;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
    }
}
//--- End   : deletion based on checked box -------------------------------------------------------



if ($q <> "") {
    if ($by == 1) {
        $getQ .= " AND b.name like '%" . $q . "%'";
    } else if ($by == 2) {
        $getQ .= " AND a.SENO like '%" . $q . "%'";
    } else if ($by == 3) {
        $getQ .= " AND a.keterangan like '%" . $q . "%'";
    }
}
// sql select dari table mana 
$sSQL = "SELECT a.*, b.name, b.g_lockstat
		FROM singleentry a, generalacc b
		WHERE  a.batchNo = b.ID and year( tarikh_entry ) = " . $yy;

if ($mm <> "ALL") $sSQL .= " AND month( tarikh_entry ) =" . $mm;
if ($statusFilter != "ALL") {
    if ($statusFilter == "balanced") {
        $sSQL .= " AND ( SELECT ";
        $sSQL .= " COALESCE(SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END), 0) FROM transactionacc WHERE docNo = a.SENO) = 0";
    } else if ($statusFilter == "not_balanced") {
        $sSQL .= " AND ( SELECT ";
        $sSQL .= " COALESCE(SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END), 0) FROM transactionacc WHERE docNo = a.SENO) != 0";
    }
}
$sSQL .= $getQ . " order by SENO desc";

$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec - 1);

$TotalRec = $GetVouchers->RowCount();
$TotalPage =  ($TotalRec / $pg);

$sqlYears = "SELECT DISTINCT YEAR(tarikh_entry) AS year FROM singleentry WHERE tarikh_entry IS NOT NULL AND tarikh_entry != '' AND tarikh_entry != 0 ORDER BY year ASC";
$rsYears = $conn->Execute($sqlYears);

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Baru" onClick="location.href=\'' . $sFileRef . '&action=new\';">
</div>
';

// summary chart
// $sourceMain = "none";
// $sourceSub = "accsingleentrylist";
// include("ACCsummary.php");

print '
	<div clas="row">
			Bulan   
			<select name="mm" class="form-select-xs" onchange="document.MyForm.submit();">
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
			<select name="yy" class="form-select-xs" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
    $year = $rsYears->fields['year'];
    print '	<option value="' . $year . '"';
    if ($yy == $year) print 'selected';
    print '>' . $year;
    $rsYears->MoveNext();
}
print '		</select>
			Status
            <select name="statusFilter" class="form-select-xs" onchange="document.MyForm.submit();">
                <option value="ALL"';
if ($statusFilter == "ALL") print 'selected';
print '>- Semua -</option>
                <option value="balanced"';
if ($statusFilter == "balanced") print 'selected';
print '>Balanced</option>
                <option value="not_balanced"';
if ($statusFilter == "not_balanced") print 'selected';
print '>Not Balanced</option>
            </select>&nbsp;&nbsp;';

print '</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
	</div><br/>
	<div clas="row">

	Cari Berdasarkan
				<select name="by" class="form-select-sm">';
if ($by == 1)    print '<option value="1" selected>Nama Batch</option>';
else print '<option value="1">Nama Batch</option>';
if ($by == 2)    print '<option value="2" selected>Nomor Rujukan</option>';
else print '<option value="2">Nomor Rujukan</option>';
if ($by == 3)    print '<option value="3" selected>Catatan</option>';
else print '<option value="3">Catatan</option>';

print '</select>
			<input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">
';
// print' &nbsp;&nbsp;<input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\''.$sFileRef.'&action=new&jenis='.$jenis.'\';">';

if (($IDName == 'admin') or ($IDName == 'superadmin')) {

    print '&nbsp; <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}

print '

</div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">';
if ($GetVouchers->RowCount() <> 0) {
    $bil = $StartRec;
    $cnt = 1;
    print '
		<tr valign="top" class="textFont">
                                            <td>
                                                    <table width="100%"><br>
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
						<td nowrap>Nomor Rujukan</td>
						<td nowrap align="center">Tanggal</td>
						<td nowrap align="center">Nama Batch</td>
						<td nowrap>Catatan</td>
						<td nowrap align="center">Status Kunci</td>
						<td nowrap align="right">Debit (RP)</td>
						<td nowrap align="right">Kredit (RP)</td>
						<td nowrap align="center">Status</td>
						<td nowrap align="center">Tindakan</td>
					</tr>';

    $DRTotal = 0;
    $CRTotal = 0;
    while (!$GetVouchers->EOF && $cnt <= $pg) {

        // check has transaction or not
        $noTran     = false;
        $sql2         = "SELECT * FROM transactionacc WHERE docNo = '" . $GetVouchers->fields(SENO) . "' ORDER BY ID";
        $rsDetail     = $conn->Execute($sql2);
        if ($rsDetail->RowCount() < 1) $noTran = true;

        $jumlah = 0;

        $colorPen = "Data";
        if ($GetVouchers->fields(g_lockstat) == 1) {
            $colorPen = "greenText";
            $lock = "Dikunci";
        } else {
            $colorPen = "redText";
            $lock = "Belum Dikunci";
        }

        $nama             = $GetVouchers->fields(name);
        $tarikh_entry     = toDate("d/m/y", $GetVouchers->fields(tarikh_entry));
        $description     = $GetVouchers->fields(keterangan);
        $cetak             = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCSingleEntryPrint.php?id=' . $GetVouchers->fields(SENO) . '\')"></i>';
        $edit             = '<a href="' . $sFileRef . '&action=view&SENO=' . tohtml($GetVouchers->fields['SENO']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
        $editLock         = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
        $view             = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCsingleEntryView.php?id=' . $GetVouchers->fields(SENO) . '\')"></i>';

        $sqlPayment     =     "SELECT SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END) AS totDb,
			SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END) AS totKr 
			FROM transactionacc 
			WHERE docNo 	= '" . $GetVouchers->fields(SENO) . "'";
        $rsBayaran         = $conn->Execute($sqlPayment);
        $db             = $rsBayaran->fields['totDb'];
        $kr             = $rsBayaran->fields['totKr'];

        if ($db - $kr == 0) {
            $status     = '<span class="badge badge-soft-primary"><b>Balanced</b></span>';
        } else {
            $status     = '<span class="badge badge-soft-danger"><b>Not Balanced</b></span>';
        }

        if ($noTran == false) {
            print '<tr>';
        } else {
            print '<tr style="background-color: rgba(255, 0, 0, 0.1) !important; --bs-table-accent-bg: transparent !important;" title="Dokumen ini tiada transaksi">';
        }

        print '
						<td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>';

        //<a href="'.$sFileRef.'?action=view&SENO='.tohtml($GetVouchers->fields(SENO)).'&yy='.$yy.'&mm='.$mm.'">

        if ($GetVouchers->fields(g_lockstat) == 1) {
            print '	<td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(SENO)) . '">
		&nbsp;' . $GetVouchers->fields(SENO) . '</td>';
        } else {

            print '	<td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(SENO)) . '">
		<a href="' . $sFileRef . '&action=view&SENO=' . tohtml($GetVouchers->fields(SENO)) . '&yy=' . $yy . '&mm=' . $mm . '">
			&nbsp;' . $GetVouchers->fields(SENO) . '</td>';
        }

        print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_entry . '</td>
            <td class="Data" style="text-align: center; vertical-align: middle;">' . $nama . '</td>
            <td class="Data" style="text-align: left; vertical-align: middle; max-width: 300px;">' . $description . '</td>
            <td class="Data" style="text-align: center; vertical-align: middle;"><font class="' . $colorPen . '">' . $lock . '</font></td>
            <td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($db, 2) . '</td>
            <td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($kr, 2) . '</td>
            <td class="Data" style="text-align: center; vertical-align: middle;">' . $status . '</td>
            ';
        if (($GetVouchers->fields['g_lockstat'] == 1) && ($GetVouchers->fields('batchNo') <> "")) {
            print '
            <td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '&nbsp;&nbsp;' . $bayar . '</td>
            ';
        } else {
            print '
            <td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '&nbsp;&nbsp;' . $bayar . '</td>
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
			<td class="textFont">Jumlah Rujukan : <b>' . $GetVouchers->RowCount() . '</b></td>
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

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.open(\'transStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');					
			}
		}
	}
		
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';