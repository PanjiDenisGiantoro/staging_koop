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
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = '?vw=reportsPGBbaki&mn=911';
$title       = "Laporan Baki Pembiayaan";

if (isset($_POST['dtTo'])) {
    $dtTo = $_POST['dtTo'];
} else {
    $dtTo = date('Y-m');
}

$year = substr($dtTo, 0, 4);
$month = substr($dtTo, 5, 2);

// Check if loanType is set and filter the query accordingly
$loanTypeCondition = "";
if (isset($_POST['loanType']) && $_POST['loanType'] != "") {
    $loanType = $_POST['loanType'];
    $loanTypeCondition = "AND a.loanType = '" . $loanType . "'";
}

$sSQL = "SELECT a.userID, c.name, a.*, b.*
        FROM loans a, loandocs b, users c
        WHERE a.loanID = b.loanID 
        AND a.status IN (3)
        AND a.userID = c.userID
        $loanTypeCondition  /* Filter based on selected loanType */
        AND (b.ajkDate2 <= '" . $dtTo . "-31')
        ORDER BY CAST(c.userID AS SIGNED INTEGER) ASC";

$GetLoan = &$conn->Execute($sSQL);

$GetLoan->Move($StartRec - 1);

$TotalRec = $GetLoan->RowCount();
$TotalPage = ($TotalRec / $pg);

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action"> 
<h5 class="card-title mb-5">
  <span style="font-size: 14px; color: #888;">Potongan Gaji / Laporan /</span>
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
    <td>Bulan</td>
</tr>
<tr>
    <td><input type="month" class="form-controlx" name="dtTo" value="' . $dtTo . '"/></td>
</tr>

<!-- Dropdown Button for Loan Type -->
<tr>
    <td>Pilih Jenis Pembiayaan</td>
</tr>
<tr>
    <td>
        <select class="form-selectx" name="loanType" id="loanType">';

// Dapatkan data dari database untuk dropdown
$query = "SELECT * FROM `general` WHERE `category` = 'C' AND `parentID` <> 0";
$result = &$conn->Execute($query);

// Jika terdapat hasil, masukkan ke dalam dropdown
if ($result->RowCount() > 0) {
    while (!$result->EOF) {
        $loanID = $result->fields('ID');
        $loanName = $result->fields('name');
        $selected = (isset($_POST['loanType']) && $_POST['loanType'] == $loanID) ? "selected" : "";
        print '<option value="' . $loanID . '" ' . $selected . '>' . $loanName . '</option>';
        $result->MoveNext();
    }
} else {
    print '<option value="">Tiada pilihan</option>';
}

print '  </select>
    </td>
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
                        <td nowrap align="center">Nombor Anggota </td>
                        <td nowrap align="left">Nama</td>
                        <td nowrap align="center">Nombor Bond</td>
                        <td nowrap align="right">Baki Pembiayaan Tunai(RM)</td>
                    </tr>';

$yrmth2 = str_replace("-", "", $dtTo);

if ($GetLoan->RowCount() <> 0) {
    $bil = 0;
    $totalsum = 0;
    while (!$GetLoan->EOF) {
        $bil++;
        $bond = $GetLoan->fields(rnoBond);
        $bakiAwalTunai = getBakiTunai($GetLoan->fields(userID), $yrmth2, $bond);
        $totalsum += $bakiAwalTunai;
        print '<tr style="font-family: Poppins, sans-serif; font-size: 9pt;">
                <td align="center">' . $bil . '.</td>
                <td align="center">' . $GetLoan->fields(userID) . '</td>
				<td align="left">' . $GetLoan->fields(name) . '</td>
				<td align="center">' . $bond . '</td>
				<td align="right">' . number_format($bakiAwalTunai, 2) . '</td>
            </tr>';
        $GetLoan->MoveNext();
    }

    print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
            <td colspan="4">Jumlah Keseluruhan:</td>
            <td align="right">' . number_format($totalsum, 2) . '</td>
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

            // Check if any cell in the row contains the search term
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
