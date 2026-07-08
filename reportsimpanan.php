<?php

/*********************************************************************************
 *           Project        :    iKOOP.com.my
 *           Filename       :    reportsimpanan.php
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg)) $pg = 50;
if (!isset($q)) $q = "";
if (!isset($by)) $by = "1";
if (!isset($filter_status)) $filter_status = "ALL";
if (!isset($filter_dari)) $filter_dari = date('Y-m-01');
if (!isset($filter_sampai)) $filter_sampai = date('Y-m-d');

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = '?vw=reportsimpanan&mn=902';
$title = "Transaksi Simpanan";

// Limit date range max 1 year
$maxDate = date('Y-m-d');
$minDate = date('Y-m-d', strtotime('-1 year'));
if ($filter_dari < $minDate) $filter_dari = $minDate;
if ($filter_sampai > $maxDate) $filter_sampai = $maxDate;

//--- Begin : Proses update status -----------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] == "proses" && isset($_POST['pk']) && is_array($_POST['pk'])) {
    $newStatus = isset($_POST['jenis_proses']) ? (int)$_POST['jenis_proses'] : null;

    if ($newStatus !== null && in_array($newStatus, array(3, 9))) {
        foreach ($_POST['pk'] as $trxID) {
            $trxID = (int)$trxID;
            if ($trxID <= 0) continue;

            $rsTrx = $conn->Execute("SELECT * FROM transactionsimpanan WHERE ID = " . tosql($trxID, "Number") . " AND Status = 1");
            if (!$rsTrx || $rsTrx->EOF) continue;

            $trxData = $rsTrx->fields;

            if ($newStatus == 3) {
                // Status PAID: update transaksi + save ke GL (transactionacc)
                $conn->Execute("UPDATE transactionsimpanan SET Status = 3, UpdatedBy = " . tosql(get_session('Cookie_userName'), "Text") . ", UpdatedDate = NOW() WHERE ID = " . tosql($trxID, "Number"));

                // Update balance di depositoracc
                if ($trxData['JenisTransaksi'] == 'SETOR') {
                    $conn->Execute("UPDATE depositoracc SET Balance = Balance + " . $trxData['Nominal'] . " WHERE AccountNumber = " . tosql($trxData['AccountNumber'], "Text"));
                } else {
                    $conn->Execute("UPDATE depositoracc SET Balance = Balance - " . $trxData['Nominal'] . " WHERE AccountNumber = " . tosql($trxData['AccountNumber'], "Text"));
                }

                // Save ke GL (transactionacc)
                $addminus = ($trxData['JenisTransaksi'] == 'SETOR') ? 1 : 0;
                $yrmth = date('Ym', strtotime($trxData['TanggalTransaksi']));
                $glCode = dlookup("general", "loanCode", "ID=" . tosql($trxData['Code_simpanan'], "Number"));

                $sqlGL = "INSERT INTO transactionacc (docNo, userID, yrmth, deductID, addminus, pymtAmt, pymtRefer, desc_akaun, status, createdBy, createdDate) VALUES ("
                    . tosql($trxData['NoJurnal'], "Text") . ","
                    . tosql($trxData['UserID'], "Text") . ","
                    . tosql($yrmth, "Text") . ","
                    . tosql($glCode, "Text") . ","
                    . $addminus . ","
                    . $trxData['Nominal'] . ","
                    . tosql($trxData['AccountNumber'], "Text") . ","
                    . tosql('Simpanan ' . $trxData['NamaAkun'] . ' - ' . $trxData['JenisTransaksi'], "Text") . ","
                    . "1,"
                    . tosql(get_session('Cookie_userName'), "Text") . ","
                    . tosql(date("Y-m-d H:i:s"), "Text") . ")";
                $conn->Execute($sqlGL);

                $strActivity = 'Paid transaksi simpanan ' . $trxData['NoJurnal'] . ' - ' . $trxData['NamaAnggota'];
                activityLog($sqlGL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

            } elseif ($newStatus == 9) {
                // Status TOLAK
                $conn->Execute("UPDATE transactionsimpanan SET Status = 9, UpdatedBy = " . tosql(get_session('Cookie_userName'), "Text") . ", UpdatedDate = NOW() WHERE ID = " . tosql($trxID, "Number"));

                $strActivity = 'Tolak transaksi simpanan ' . $trxData['NoJurnal'] . ' - ' . $trxData['NamaAnggota'];
                activityLog("", $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
            }
        }
        $labelMsg = ($newStatus == 3) ? 'dibayar (paid)' : 'ditolak';
        echo '<script>alert("Status berhasil ' . $labelMsg . '."); window.location.href="' . $sFileName . '";</script>';
        exit;
    }
}
//--- End : Proses update status -----------------------------------------------------------

//--- Build query ---
$sWhere = " 1=1 ";

if ($filter_dari != '') {
    $sWhere .= " AND DATE(TanggalTransaksi) >= " . tosql($filter_dari, "Text");
}
if ($filter_sampai != '') {
    $sWhere .= " AND DATE(TanggalTransaksi) <= " . tosql($filter_sampai, "Text");
}
if ($filter_status !== "" && $filter_status !== "ALL") {
    $sWhere .= " AND Status = " . tosql($filter_status, "Number");
}
if ($q != '') {
    if ($by == 1) {
        $sWhere .= " AND NoAnggota LIKE '%" . $q . "%'";
    } elseif ($by == 2) {
        $sWhere .= " AND NIK LIKE '%" . $q . "%'";
    } elseif ($by == 3) {
        $sWhere .= " AND NamaAnggota LIKE '%" . $q . "%'";
    }
}

$sSQL = "SELECT * FROM transactionsimpanan WHERE " . $sWhere . " ORDER BY TanggalTransaksi DESC, ID DESC";
$result = &$conn->Execute($sSQL);
$TotalRec = $result->RowCount();
$result->Move($StartRec - 1);
$TotalPage = ($TotalRec / $pg);

// Summary counts
$cntDalamProses = $conn->Execute("SELECT COUNT(*) as c FROM transactionsimpanan WHERE Status = 1 AND " . $sWhere)->fields['c'];
$cntPaid = $conn->Execute("SELECT COUNT(*) as c FROM transactionsimpanan WHERE Status = 3 AND " . $sWhere)->fields['c'];
$cntTolak = $conn->Execute("SELECT COUNT(*) as c FROM transactionsimpanan WHERE Status = 9 AND " . $sWhere)->fields['c'];
$sumMasuk = $conn->Execute("SELECT COALESCE(SUM(Nominal),0) as s FROM transactionsimpanan WHERE JenisTransaksi='SETOR' AND " . $sWhere)->fields['s'];
$sumKeluar = $conn->Execute("SELECT COALESCE(SUM(Nominal),0) as s FROM transactionsimpanan WHERE JenisTransaksi='TARIK' AND " . $sWhere)->fields['s'];

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Pengajuan Baru" onClick="window.location.href=\'?vw=loanApplysimpanan&mn=902\'"/>
</div>';
?>

<style>
    .summary-row { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
    .summary-card { flex: 1; min-width: 150px; padding: 15px; border-radius: 8px; background: #f8f9fa; border: 1px solid #dee2e6; }
    .summary-card .label { font-size: 12px; color: #6c757d; margin-bottom: 5px; }
    .summary-card .value { font-size: 1.4em; font-weight: bold; }
</style>

<!-- Summary -->
<div class="summary-row">
    <div class="summary-card">
        <div class="label">Dalam Proses</div>
        <div class="value text-warning"><?php echo $cntDalamProses; ?></div>
    </div>
    <div class="summary-card">
        <div class="label">Paid</div>
        <div class="value text-success"><?php echo $cntPaid; ?></div>
    </div>
    <div class="summary-card">
        <div class="label">Ditolak</div>
        <div class="value text-danger"><?php echo $cntTolak; ?></div>
    </div>
    <div class="summary-card">
        <div class="label">Total Masuk (Setor)</div>
        <div class="value text-success">Rp <?php echo number_format($sumMasuk, 0, ',', '.'); ?></div>
    </div>
    <div class="summary-card">
        <div class="label">Total Keluar (Tarik)</div>
        <div class="value text-danger">Rp <?php echo number_format($sumKeluar, 0, ',', '.'); ?></div>
    </div>
</div>

<?php
// --- Filter section ---
print '<div class="mb-3 row m-1">
<div>Pencarian
    <select name="by" class="form-select-sm">
        <option value="1"' . ($by == 1 ? ' selected' : '') . '>No Anggota</option>
        <option value="2"' . ($by == 2 ? ' selected' : '') . '>No IC</option>
        <option value="3"' . ($by == 3 ? ' selected' : '') . '>Nama Anggota</option>
    </select>
    <input type="text" name="q" value="' . htmlspecialchars($q) . '" maxlength="50" size="20" class="form-control-sm">
    <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
</div>
</div>

<div class="mb-3 row m-1">
<div>
    Dari <input type="date" name="filter_dari" value="' . $filter_dari . '" min="' . $minDate . '" max="' . $maxDate . '" class="form-control-sm">
    Sampai <input type="date" name="filter_sampai" value="' . $filter_sampai . '" min="' . $minDate . '" max="' . $maxDate . '" class="form-control-sm">
    &nbsp; Status
    <select name="filter_status" class="form-select-sm" onchange="document.MyForm.submit();">
        <option value="ALL"' . ($filter_status == "ALL" ? ' selected' : '') . '>Semua</option>
        <option value="1"' . ($filter_status == "1" ? ' selected' : '') . '>Dalam Proses</option>
        <option value="3"' . ($filter_status == "3" ? ' selected' : '') . '>Paid</option>
        <option value="9"' . ($filter_status == "9" ? ' selected' : '') . '>Ditolak</option>
    </select>
    &nbsp;&nbsp; Proses
    <select name="jenis_proses" class="form-select-sm">
        <option value="3">Paid</option>
        <option value="9">Tolak</option>
    </select>
    <input type="button" class="btn btn-sm btn-primary" value="Proses" onClick="ITRActionButtonClickStatus(\'proses\');">
</div>
</div>';

// --- Pagination top ---
print '
<div class="table-responsive">
<table width="100%"><tr>
    <td class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Pilih Semua</td>
    <td align="right" class="textFont">Tampil
        <select name="pg" class="form-select-xs" onchange="doListAll();">';
$pgOptions = array(10, 20, 50, 100);
foreach ($pgOptions as $p) {
    print '<option value="' . $p . '"' . ($pg == $p ? ' selected' : '') . '>' . $p . '</option>';
}
print '</select> setiap halaman.</td></tr></table>';

// --- Table ---
if ($TotalRec > 0) {
    $bil = $StartRec;
    $cnt = 1;
    print '
    <table class="table table-sm table-striped table-bordered" style="font-size: 9pt;">
        <thead class="table-primary">
            <tr>
                <th>&nbsp;</th>
                <th>Tanggal</th>
                <th>No Jurnal</th>
                <th>Nama Anggota</th>
                <th>Trx ID</th>
                <th>No Account</th>
                <th>Jenis Simpanan</th>
                <th>No IC</th>
                <th class="text-center">Transaksi</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Baki Sebelum</th>
                <th class="text-end">Baki Selepas</th>
                <th>Ref1</th>
                <th>Ref2</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>';

    while (!$result->EOF && $cnt <= $pg) {
        $status = $result->fields['Status'];
        $statusLabel = '';
        $statusClass = '';
        if ($status == 1) { $statusLabel = 'Dalam Proses'; $statusClass = 'badge bg-warning text-dark'; }
        elseif ($status == 3) { $statusLabel = 'Paid'; $statusClass = 'badge bg-success'; }
        elseif ($status == 9) { $statusLabel = 'Ditolak'; $statusClass = 'badge bg-danger'; }

        $jenisClass = ($result->fields['JenisTransaksi'] == 'SETOR') ? 'badge bg-success' : 'badge bg-danger';
        $jenisLabel = ($result->fields['JenisTransaksi'] == 'SETOR') ? 'Masuk' : 'Keluar';

        $canCheck = ($status == 1) ? '' : ' disabled';

        print '<tr>
            <td class="text-center">' . $bil . '. <input type="checkbox" class="form-check-input" name="pk[]" value="' . $result->fields['ID'] . '"' . $canCheck . '></td>
            <td nowrap>' . date('d/m/Y H:i', strtotime($result->fields['TanggalTransaksi'])) . '</td>
            <td nowrap>' . htmlspecialchars($result->fields['NoJurnal']) . '</td>
            <td>' . htmlspecialchars($result->fields['NamaAnggota']) . '</td>
            <td>' . $result->fields['ID'] . '</td>
            <td>' . htmlspecialchars($result->fields['AccountNumber']) . '</td>
            <td>' . htmlspecialchars($result->fields['NamaAkun']) . '</td>
            <td>' . htmlspecialchars($result->fields['NIK']) . '</td>
            <td class="text-center"><span class="' . $jenisClass . '">' . $jenisLabel . '</span></td>
            <td class="text-end" nowrap><strong>Rp ' . number_format($result->fields['Nominal'], 0, ',', '.') . '</strong></td>
            <td class="text-end" nowrap>Rp ' . number_format($result->fields['SaldoSebelum'], 0, ',', '.') . '</td>
            <td class="text-end" nowrap>Rp ' . number_format($result->fields['SaldoSesudah'], 0, ',', '.') . '</td>
            <td>' . htmlspecialchars($result->fields['Referensi']) . '</td>
            <td>' . htmlspecialchars($result->fields['Keterangan']) . '</td>
            <td class="text-center"><span class="' . $statusClass . '">' . $statusLabel . '</span></td>
        </tr>';

        $cnt++;
        $bil++;
        $result->MoveNext();
    }
    $result->Close();

    print '</tbody></table>';

    // Pagination
    if ($TotalRec > $pg) {
        $numPage = ($TotalRec % $pg == 0) ? $TotalPage : $TotalPage + 1;
        print '<div class="textFont">Data Dari : ';
        for ($i = 1; $i <= $numPage; $i++) {
            print '<a href="' . $sFileName . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&filter_status=' . $filter_status . '&filter_dari=' . $filter_dari . '&filter_sampai=' . $filter_sampai . '">';
            print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
        }
        print '</div>';
    }

    print '<div class="textFont mt-2">Jumlah Data : <b>' . $TotalRec . '</b></div>';
} else {
    if ($q == "") {
        print '<div class="text-center my-4"><b class="textFont">- Tidak Ada Data Transaksi -</b></div>';
    } else {
        print '<div class="text-center my-4"><b class="textFont">- Pencarian "' . htmlspecialchars($q) . '" tidak ditemukan -</b></div>';
    }
}

print '</div></form>';

include("footer.php");

print '
<script language="JavaScript">
    var allChecked = false;
    function ITRViewSelectAll() {
        e = document.MyForm.elements;
        allChecked = !allChecked;
        for (c = 0; c < e.length; c++) {
            if (e[c].type == "checkbox" && e[c].name != "all" && !e[c].disabled) {
                e[c].checked = allChecked;
            }
        }
    }

    function ITRActionButtonClickStatus(v) {
        e = document.MyForm;
        if (e == null) {
            alert("Tolong pastikan nama form dibuat.!");
        } else {
            count = 0;
            for (c = 0; c < e.elements.length; c++) {
                if (e.elements[c].name == "pk[]" && e.elements[c].checked) {
                    count++;
                }
            }
            if (count == 0) {
                alert("Tolong pilih data yang mau di" + v + "kan.");
            } else {
                var proses = e.jenis_proses.value;
                var label = (proses == "3") ? "Paid" : "Tolak";
                if (confirm(count + " data dipilih ingin di-" + label + "kan?")) {
                    e.action.value = v;
                    e.submit();
                }
            }
        }
    }

    function doListAll() {
        c = document.forms["MyForm"].pg;
        document.location = "' . $sFileName . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
    }
</script>';
