<?php

/*********************************************************************************
 *           Project        :    iKOOP.com.my
 *           Filename        :    loan.php
 *           Date            :    06/12/2015
 *********************************************************************************/
if (!isset($StartRec))    $StartRec = 1;
if (!isset($pg))        $pg = 10;
if (!isset($q))            $q = "";
if (!isset($by))        $by = "0";
if (!isset($filter))    $filter = "0";
if (!isset($dept))        $dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = '?vw=rloanApproved&mn=905';
$title       = "Kelulusan Pembiayaan";

// Menangani tarikh dari borang
if (isset($_POST['dtFrom']) && isset($_POST['dtTo'])) {
    $dtFrom = $_POST['dtFrom'];
    $dtTo = $_POST['dtTo'];
} else {
    $dtFrom = date('Y-m-d');
    $dtTo = date('Y-m-d');
}

$sSQL = "SELECT a.*, b.*, DATEDIFF(a.applyDate, b.ajkDate2) as date1
        FROM loans a, loandocs b
        WHERE a.loanID = b.loanID
        AND b.result = 'lulus'
        AND (b.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')
        ORDER BY date1 ASC";

$GetLoan = &$conn->Execute($sSQL);

$GetLoan->Move($StartRec - 1);

$TotalRec =    $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action"> 
<h5 class="card-title mb-5">
  <span style="font-size: 14px; color: #888;">Pembiayaan / Laporan /</span>
  <span style="font-size: 16px; color: black;">' . $title . '</span>
</h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">';

print '
<style>
@media print {
  /* Hide the print button itself */
  .btn {
    display: none !important;
  }
}

</style>
<table border="0" cellspacing="0" cellpadding="3" width="50%" align="left">
<tr>
    <td>Dari Tanggal</td>
    <td>Sehingga Tanggal</td>
</tr>
<tr>
    <td><input type="date" class="form-controlx" name="dtFrom" value="' . $dtFrom . '"/></td>
    <td><input type="date" class="form-controlx" name="dtTo" value="' . $dtTo . '"/></td>
</tr>
<tr>
  <td>
    <div class="d-flex justify-content-start mt-4 mb-3">
      <input type="submit" class="btn btn-md btn-primary" value="Generate" />
      <div class="hidden-print ms-2">
        <input type="submit" class="btn btn-md btn-secondary" value="Cetak Laporan" onclick="window.print();" />
      </div>
    </div>
  </td>
</tr>

</table>
</form></div>';

print '
<div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
    <button id="downloadExcel" class="btn btn-primary" title="Muat Turun Excel">
        <i class="mdi mdi-microsoft-excel"></i>
    </button>

    <div style="position: relative; width: 200px;">
        <input id="searchInput" class="form-controlx" type="text" placeholder="Cari..." style="width: 100%; padding-right: 40px;" />
        <span id="searchIcon" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
            <i class="mdi mdi-magnify"></i>
        </span>
    </div>
</div>

<div class="table-responsive">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <table id="loanTable" border="1" cellpadding="2" cellspacing="1" align="center" width="100%" class="table table-bordered table-striped">
                    <tr class="table-primary" style="font-family: Poppins, sans-serif; font-size: 9pt;">
                        <td nowrap>&nbsp;</td>
                        <td align="center" nowrap>Nomor Rujukan</td>
                        <td nowrap>Nama Pembiayaan</td>
                        <td align="center" nowrap>Nomor Anggota</td>
                        <td nowrap>Nama Anggota</td>
                        <td nowrap>Cabang/Zona</td>
                        <td align="right" nowrap>Jumlah Pembiayaan (RP)</td>
                        <td align="center" nowrap>Tanggal Pengajuan</td>
                        <td align="center" nowrap>Tanggal Diluluskan</td>
                        <td align="center" nowrap>Beza Kelulusan</td>
                    </tr>';

if ($GetLoan->RowCount() <> 0) {
    $bil = 0;
    $totalsum = 0;
    while (!$GetLoan->EOF) {
        $bil++;
        $jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields('userID'), "Text"));
        $totalsum += $GetLoan->fields('loanAmt');
        print '<tr style="font-family: Poppins, sans-serif; font-size: 9pt;">
                <td align="center">' . $bil . '.</td>
                <td align="center">' . $GetLoan->fields('loanNo') . '</td>
                <td>' . dlookup("general", "name", "ID=" . tosql($GetLoan->fields('loanType'), "Number")) . '</td>
                <td align="center">' . dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields('userID'), "Text")) . '</td>
                <td>' . dlookup("users", "name", "userID=" . tosql($GetLoan->fields('userID'), "Text")) . '</td>
                <td>' . dlookup("general", "name", "ID=" . tosql($jabatan, "Number")) . '</td>
                <td align="right">' . number_format($GetLoan->fields('loanAmt'), 2) . '</td>
                <td align="center">' . toDate("d/m/Y", $GetLoan->fields('applyDate')) . '</td>
                <td align="center">' . toDate("d/m/Y", $GetLoan->fields('ajkDate2')) . '</td>
                <td align="center">' . $GetLoan->fields('date1') . '</td>
            </tr>';
        $GetLoan->MoveNext();
    }

    print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
            <td colspan="6">Jumlah Keseluruhan:</td>
            <td align="right">' . number_format($totalsum, 2) . '</td>
            <td colspan="3">&nbsp;</td>
        </tr>';
} else {
    print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
            <td colspan="10" align="center"><b>- Tiada Rekod Dicetak-</b></td>
        </tr>';
}
print '        </table> 
        </td>
    </tr>    
</table>
</div>';
include("footer.php");

print '<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>';
print '
<script language="JavaScript">
    var allChecked = false;
    
    document.forms[\'MyForm\'].onsubmit = function() {
        console.log("Borang dihantar!");
    }

   document.addEventListener(\'DOMContentLoaded\', function() {
    const searchInput = document.getElementById(\'searchInput\');
    const searchIcon = document.getElementById(\'searchIcon\');

    // Trigger search when Enter is pressed
    searchInput.addEventListener(\'keyup\', function(event) {
        if (event.key === \'Enter\') {
            searchTable();
        }
    });

    // Trigger search when clicking the search icon
    searchIcon.addEventListener(\'click\', searchTable);

    // Search function
    function searchTable() {
        const filter = searchInput.value.toLowerCase();
        const table = document.getElementById(\'loanTable\');
        const rows = table.getElementsByTagName(\'tr\');

        for (let i = 1; i < rows.length; i++) {
            let cells = rows[i].getElementsByTagName(\'td\');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }

            // Toggle row visibility based on search
            rows[i].style.display = found ? "" : "none";
        }
    }
});

 document.getElementById("downloadExcel").addEventListener("click", function() {
        var table = document.getElementById("loanTable");
        var wb = XLSX.utils.table_to_book(table, { sheet: "Loan Data" });
        XLSX.writeFile(wb, "kelulusan_pembiayaan.xlsx");
    });
</script>';
