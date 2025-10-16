<?php

/*********************************************************************************
 *          Project      :   iKOOP.com.my
 *          Filename     :   journalslist.php
 *          Date         :   04/08/2006
 *********************************************************************************/
if (!isset($mm))        $mm = "ALL";
if (!isset($yy))        $yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

if (!isset($StartRec))  $StartRec = 1;
if (!isset($pg))        $pg = 50;
if (!isset($q))         $q = "";
if (!isset($code))      $code = "ALL";
if (!isset($filter))    $filter = "0";
if (!isset($statusFilter)) $statusFilter = "ALL";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = "?vw=journalsList&mn=$mn";
$sFileRef  = "?vw=journals&mn=$mn";
$title     =  "JURNAL ANGGOTA";

$IDName = get_session("Cookie_userName");
//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete") {
    $sWhere = "";
    for ($i = 0; $i < count($pk); $i++) {
        $sWhere = "no_jurnal=" . tosql($pk[$i], "Text");
        $sSQL = "DELETE FROM jurnal WHERE " . $sWhere;
        $rs = &$conn->Execute($sSQL);

        $sWhere = "docNo=" . tosql($pk[$i], "Text");
        $sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
        $rs = &$conn->Execute($sSQL);

        $sWhere = "docNo=" . tosql($pk[$i], "Text");

        $docNo = dlookup("transaction", "docNo", $sWhere);

        $sSQL = "DELETE FROM transaction WHERE " . $sWhere;
        $rs = &$conn->Execute($sSQL);

        $strActivity = $_POST['Submit'] . 'Voucher Jurnal Dihapuskan - ' . $docNo;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
    }
}
//--- End   : deletion based on checked box -------------------------------------------------------
//--- Prepare deduct list
$deductList = array();
$deductVal  = array();
$sSQL = "   SELECT B.ID, B.code , B.name 
            FROM transactionacc A, general B
            WHERE A.deductID= B.ID  
            AND   A.status = " . tosql($filter, "Number") . "   
            GROUP BY A.deductID";
$GetDeduct = &$conn->Execute($sSQL);
if ($GetDeduct->RowCount() <> 0) {
    while (!$GetDeduct->EOF) {
        array_push($deductList, $GetDeduct->fields(code) . ' - ' . $GetDeduct->fields(name));
        array_push($deductVal, $GetDeduct->fields(ID));
        $GetDeduct->MoveNext();
    }
}

if ($q <> "") {
    if ($by == 1) {
        $getQ .= " AND no_anggota = '" . $q . "'";
    } else if ($by == 2) {
        $getQ .= " AND no_jurnal like '%" . $q . "%'";
    } else if ($by == 3) {
        $getQ .= " AND keterangan like '%" . $q . "%'";
    }
}
//kecualikan jurnal pindahan
$sSQL = "SELECT * FROM jurnal AS a
        WHERE jenis != 'pindahan' AND year(a.tarikh_jurnal) = " . $yy;
if ($mm <> "ALL") $sSQL .= " AND month(a.tarikh_jurnal) =" . $mm;
if ($statusFilter != "ALL") {
    if ($statusFilter == "balanced") {
        $sSQL .= " AND ( SELECT ";
        $sSQL .= " COALESCE(SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END), 0) FROM transactionacc WHERE docNo = a.no_jurnal) = 0";
    } else if ($statusFilter == "not_balanced") {
        $sSQL .= " AND ( SELECT ";
        $sSQL .= " COALESCE(SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END), 0) FROM transactionacc WHERE docNo = a.no_jurnal) != 0";
    }
}
$sSQL .= $getQ . " order by a.tarikh_jurnal desc";
$GetJournals = &$conn->Execute($sSQL);
$GetJournals->Move($StartRec - 1);

$TotalRec = $GetJournals->RowCount();
$TotalPage =  ($TotalRec / $pg);

$sqlYears = "SELECT DISTINCT YEAR(tarikh_jurnal) AS year FROM jurnal WHERE tarikh_jurnal IS NOT NULL AND tarikh_jurnal != '' AND tarikh_jurnal != 0 ORDER BY year ASC";
$rsYears = $conn->Execute($sqlYears);

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
    print ' <option value="' . $j . '"';
    if ($mm == $j) print 'selected';
    print '>' . $j;
}
print '     </select>
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
print '     </select>
            <input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
        
    </div><br/>
    <div clas="row">
    Cari Berdasarkan
                <select name="by" class="form-select-sm">';
if ($by == 1)   print '<option value="1" selected>Nomor Anggota</option>';
else print '<option value="1">Nomor Anggota</option>';
if ($by == 2)   print '<option value="2" selected>Nomor Jurnal</option>';
else print '<option value="2">Nomor Jurnal</option>';
if ($by == 3)   print '<option value="3" selected>Keterangan</option>';
else print '<option value="3">Keterangan</option>';

print '     </select>
                <input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
             <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
            &nbsp;&nbsp;            
            <!--Kod Potongan
            <select name="code" class="form-select-sm" onchange="document.MyForm.submit();">
                <option value="ALL">- Semua -';
for ($i = 0; $i < count($deductList); $i++) {
    print ' <option value="' . $deductVal[$i] . '" ';
    if ($code == $deductVal[$i]) print ' selected';
    print '>' . $deductList[$i];
}
print '     </select>&nbsp;
            Status
            <select name="filter" class="form-select-sm" onchange="document.MyForm.submit();">';
for ($i = 0; $i < count($statusList); $i++) {
    if ($statusVal[$i] < 3) {
        print ' <option value="' . $statusVal[$i] . '" ';
        if ($filter == $statusVal[$i]) print ' selected';
        print '>' . $statusList[$i];
    }
}
print ' </select-->&nbsp;&nbsp;';

$jenisList = array('Anggota', 'Pembiayaan');
$jenisVal = array(1, 2);

print '     Jenis
            <select name="jenis" class="form-select-sm" onchange="document.MyForm.submit();">';
print '<option value="">- Pilih -';
for ($i = 0; $i < count($jenisList); $i++) {
    print ' <option value="' . $jenisVal[$i] . '" ';
    if ($jenis == $jenisVal[$i]) print ' selected';
    print '>' . $jenisList[$i];
}

print '</select> &nbsp;&nbsp;           
            <input type="button" class="btn btn-sm btn-primary" value="Tambah" onClick="location.href=\'' . $sFileRef . '&action=new&jenis=' . $jenis . '\';">';

if (($IDName == 'admin') or ($IDName == 'superadmin')) {
    print ' &nbsp;  <input type="button" class="btn btn-sm btn-danger" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">';
}
print '          <!--input type="button" class="but" value="Status" onClick="ITRActionButtonStatus();"-->
        
    </div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
';
if ($GetJournals->RowCount() <> 0) {
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
        </tr>
        <tr valign="top" >
            <td valign="top">
                <table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
                    <tr class="table-primary">
                        <td nowrap>&nbsp;</td>
                        <td nowrap>Nomor Jurnal</td>
                        <td nowrap align="center">Tanggal</td>
                        <td nowrap>Nomor/Nama Anggota</td>
                        <td nowrap>Keterangan</td>
                        <td nowrap align="right">Debit (RP)</td>
                        <td nowrap align="right">Kredit (RP)</td>
                        <td nowrap align="center">Status</td>
                        <td nowrap align="center">Tindakan</td>                        
                    </tr>';
    $DRTotal = 0;
    $CRTotal = 0;
    while (!$GetJournals->EOF && $cnt <= $pg) {

        // check has transaction or not
        $noTran     = false;
        $sql2         = "SELECT * FROM transactionacc WHERE docNo = '" . $GetJournals->fields(no_jurnal) . "' ORDER BY ID";
        $rsDetail     = $conn->Execute($sql2);
        if ($rsDetail->RowCount() < 1) $noTran = true;

        $status = $GetJournals->fields(status);
        $colorStatus = "Data";
        if ($status == 1) $colorStatus = "greenText";
        if ($status == 2) $colorStatus = "redText";
        $totalAmt = $GetJournals->fields(pymtAmt) + $GetJournals->fields(cajAmt);
        if ($GetJournals->fields(addminus) == 0) {
            $addMinus = 'Debit';
            $DRTotal += $totalAmt;
        } else {
            $addMinus = 'Kredit';
            $CRTotal += $totalAmt;
        }
        $jumlah = 0;
        $sqlAmt = "SELECT 
            SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END) as debit,
            SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END) as kredit
            FROM transactionacc 
            WHERE docNo     = '" . $GetJournals->fields['no_jurnal'] . "'";
        $rsAmt          = $conn->Execute($sqlAmt);
        $amtDebit       = $rsAmt->fields['debit'];
        $amtKredit      = $rsAmt->fields['kredit'];

        if ($amtDebit - $amtKredit == 0) {
            $statusBal     = '<span class="badge badge-soft-primary"><b>Balanced</b></span>';
        } else {
            $statusBal     = '<span class="badge badge-soft-danger"><b>Not Balanced</b></span>';
        }

        $sqlname        = "select a.name from users a, userdetails b where a.userID = b.userID and b.memberID = '" . $GetJournals->fields(no_anggota) . "'";
        $GetName        = &$conn->Execute($sqlname);
        $nama           = $GetName->fields(name);
        $tarikh_jurnal  = toDate("d/m/y", $GetJournals->fields(tarikh_jurnal));
        $cetak          = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'journalsPaymentPrint.php?id=' . $GetJournals->fields(no_jurnal) . '\')"></i>';
        $edit             = '<a href="' . $sFileRef . '&action=view&no_jurnal=' . tohtml($GetJournals->fields['no_jurnal']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
        $view             = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'journalsPaymentView.php?id=' . $GetJournals->fields(no_jurnal) . '\')"></i>';

        if ($noTran == false) {
            print '<tr>';
        } else {
            print '<tr style="background-color: rgba(255, 0, 0, 0.1) !important; --bs-table-accent-bg: transparent !important;" title="Dokumen ini tiada transaksi">';
        }

        print ' 
                        <td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>
                        <td class="Data" style="text-align: left; vertical-align: middle;"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetJournals->fields(no_jurnal)) . '">
                        <a href="' . $sFileRef . '&action=view&no_jurnal=' . tohtml($GetJournals->fields(no_jurnal)) . '&yy=' . $yy . '&mm=' . $mm . '">
                            ' . $GetJournals->fields(no_jurnal) . '</td>
                        <td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_jurnal . '</td>
                        <td class="Data" style="text-align: left; vertical-align: middle;">' . $GetJournals->fields(no_anggota) . ' - ' . $nama . '</td>
                        <td class="Data" style="text-align: left; vertical-align: middle; width:40%;">' . $GetJournals->fields(keterangan) . '</td> ';
        print '
                        <td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($amtDebit, 2) . '</td>
                        <td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($amtKredit, 2) . '</td>    
                        <td class="Data" style="text-align: center; vertical-align: middle;">' . $statusBal . '</td>   
                        <td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
                        </tr>';
        $cnt++;
        $bil++;
        $GetJournals->MoveNext();
    }
    $GetJournals->Close();

    print ' </table>
            </td>
        </tr>   
        <!--tr>
            <td class="textFont" align="right">
            <b>Debit&nbsp;:&nbsp;' . number_format($DRTotal, 2, '.', ',') . '&nbsp;&nbsp;&nbsp;
            Kredit&nbsp;:&nbsp;' . number_format($CRTotal, 2, '.', ',') . '&nbsp;&nbsp;&nbsp;</b>
            </td>
        </tr--> 
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
            <td class="textFont">Jumlah Jurnal : <b>' . $GetJournals->RowCount() . '</b></td>
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