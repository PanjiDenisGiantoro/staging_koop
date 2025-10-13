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

$sFileName = '?vw=rAllFeesShare&mn=905';
$title       = "Keseluruhan Yuran & Syer";

if (isset($_POST['dtTo'])) {
    $dtTo = $_POST['dtTo'];
} else {
    $dtTo = date('Y-m-d');
}

$sSQL = "SELECT	CAST( b.userID AS SIGNED INTEGER ) as userID, b.name as name, a.totalFee as jumlah 
		 FROM 	userdetails a, users b
		 WHERE 	a.status in (1,4)
	 	 AND	b.userID = a.userID 
		 ORDER BY userID";

$GetMember = &$conn->Execute($sSQL);

$GetMember->Move($StartRec - 1);

$TotalRec =    $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

function getFees1($id, $dtTo)
{
    global $conn;

    $getYuranOpen = "SELECT 
        SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END) AS jumlahyuran
        FROM transaction
        WHERE
        deductID IN (1595,1780,1607)
        AND userID = '" . $id . "' 
        AND createdDate <= '" . $dtTo . "'
        GROUP BY userID";

    $rsYuranOpen = $conn->Execute($getYuranOpen);

    if ($rsYuranOpen->RowCount() == 1) {
        $totalFees = $rsYuranOpen->fields['jumlahyuran'];
    } else {
        $totalFees = 0;
    }

    return $totalFees;
}

function getSharesterkini1($id, $dtTo)
{
    global $conn;

    $getOpenTK = "SELECT 
        SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END) AS jumlahsyer
        FROM transaction
        WHERE
        deductID IN (1596,1780)
        AND userID = '" . $id . "'
        AND createdDate <= '" . $dtTo . "'";
    $rsOpenTK = $conn->Execute($getOpenTK);

    if ($rsOpenTK->RowCount() == 1) {
        $bakiAwalTK = $rsOpenTK->fields['jumlahsyer'];
    } else {
        $bakiAwalTK = 0;
    }

    return $bakiAwalTK;
}


print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action"> 
<h5 class="card-title mb-5">
  <span style="font-size: 14px; color: #888;">Anggota / Laporan /</span>
  <span style="font-size: 16px; color: black;">' . $title . '</span>
</h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">' .

    '
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
    <td>Sehingga Tarikh</td>
</tr>
<tr>
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
</form></div>' .

    '<div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
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
                        <td nowrap align="center">Nombor Anggota</td>
                        <td nowrap align="left">Nama Anggota</td>
                        <td nowrap align="center">Peratusan Yuran (%)</td>
					    <td nowrap align="right">Yuran Terkumpul (RP)</td>
                        <td nowrap align="center">Peratusan syer (%)</td>
                        <td nowrap align="right">Syer Terkumpul (RP)</td>
                    </tr>';

if ($GetMember->RowCount() <> 0) {
    $bil = 0;
    // Sebelum loop - kira jumlah keseluruhan fee dan share
    $totalsumFee = 0;
    $totalsumShare = 0;
    while (!$GetMember->EOF) {
        $totalFees = getFees1($GetMember->fields('userID'), $dtTo);
        $totalSharesTK = getSharesterkini1($GetMember->fields('userID'), $dtTo);
        $totalsumFee += $totalFees;
        $totalsumShare += $totalSharesTK;

        $GetMember->MoveNext();
    }

    // Kembali ke permulaan dan kira peratusan berdasarkan jumlah keseluruhan
    $GetMember->MoveFirst();
    $peratusYuranAll = 0;
    $peratusSyerAll = 0;

    $bil = 0;
    while (!$GetMember->EOF) {
        $bil++;
        $totalFees = getFees1($GetMember->fields('userID'), $dtTo);
        $totalSharesTK = getSharesterkini1($GetMember->fields('userID'), $dtTo);

        // Elakkan pembahagian dengan 0 (dalam kes jika totalsumFee atau totalsumShare adalah 0)
        $peratusYuran = ($totalsumFee > 0) ? ($totalFees / $totalsumFee) * 100 : 0;
        $peratusSyer = ($totalsumShare > 0) ? ($totalSharesTK / $totalsumShare) * 100 : 0;

        // Update jumlah peratusan
        $peratusYuranAll += $peratusYuran;
        $peratusSyerAll += $peratusSyer;

        print '<tr style="font-family: Poppins, sans-serif; font-size: 9pt;">
             <td align="center">' . $bil . '.</td>
             <td align="center">' . $GetMember->fields('userID') . '</td>
             <td align="left">' . $GetMember->fields('name') . '</td>
             <td nowrap align="center">' . number_format($peratusYuran, 2) . '</td>
             <td align="right">' . number_format($totalFees, 2) . '</td>
             <td nowrap align="center">' . number_format($peratusSyer, 2) . '</td>
             <td align="right">' . number_format($totalSharesTK, 2) . '</td>
         </tr>';

        $GetMember->MoveNext();
    }

    // Untuk jumlah keseluruhan
    print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
         <td colspan="3">Jumlah Keseluruhan:</td>
         <td nowrap align="center">' . number_format($peratusYuranAll, 2) . '</td>
         <td align="right">' . number_format($totalsumFee, 2) . '</td>
         <td nowrap align="center">' . number_format($peratusSyerAll, 2) . '</td>
         <td align="right">' . number_format($totalsumShare, 2) . '</td>
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
