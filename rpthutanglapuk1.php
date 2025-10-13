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

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> 0) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = '?vw=rpthutanglapuk1&mn=910';
$title       = "Laporan Tertunggak Yang Belum Dihapuskira (Kurang 12 Bulan)";

// Menangani tarikh dari borang
if (isset($_POST['dtFrom']) && isset($_POST['dtTo'])) {
    $dtFrom = $_POST['dtFrom'];
    $dtTo = $_POST['dtTo'];
} else {
    $dtFrom = date('Y-m-d');
    $dtTo = date('Y-m-d');
}

$sSQL = "SELECT a.*, b.* 
         FROM loans a, loandocs b 
         WHERE a.loanID = b.loanID 
         AND a.status IN (7)
         AND b.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "'
         ORDER BY CAST(a.userID AS SIGNED INTEGER) ASC";

$GetLoan = &$conn->Execute($sSQL);

$GetLoan->Move($StartRec - 1);

$TotalRec =    $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);


print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action"> 
<h5 class="card-title mb-5">
  <span style="font-size: 14px; color: #888;">Hutang Lapuk / Laporan /</span>
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
    <td>Dari Tarikh</td>
    <td>Sehingga Tarikh</td>
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
                        <td	nowrap>&nbsp;</td>			
						<td align="center" nowrap>No Anggota</td>									
						<td nowrap>Nama Anggota</td>				
						<td	nowrap>Jenis Pembiayaan</td>		
						<td	align="center" nowrap>Rujukan Pembiayaan</td>	
						<td	align="center" nowrap>Bond Pembiayaan</td>
					    <td	align="right" nowrap>Baki Pokok (RP)</td>						
						<td	align="right" nowrap>Jumlah Terakhir Pembayaran (RP)</td>
						<td	align="center" nowrap>Tarikh Akhir Bayar</td>
						<td	align="center" nowrap>Beza (Bulan)</td>
						<td	align="center" nowrap>NPL (Status)</td>
                    </tr>';

if ($GetLoan->RowCount() <> 0) {
    $bil = $StartRec;
    $cnt = 1;
    while (!$GetLoan->EOF) {

        $userID = $GetLoan->fields(userID);
        $bond = $GetLoan->fields(rnoBond);
        $nama = dlookup("users", "name", "userID=" . tosql($userID, "Text"));
        $loanName = dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Text"));

        //related dengan last pembayaran
        $ssSQL = "";
        $ssSQL = "SELECT userID, docNo,pymtRefer, SUM(pymtAmt) AS total
                    FROM transaction
                    WHERE userID = '" . $userID . "' AND pymtRefer = '" . $bond . "'
                    GROUP BY userID, docNo
                    ORDER BY docNo DESC 
                    LIMIT 1";
        $GetListIns2 = &$conn->Execute($ssSQL);

        $amaunLstByr = $GetListIns2->fields(total);

        $dateResit = dlookup("resit", "tarikh_resit", "no_resit=" . tosql($GetListIns2->fields(docNo), "Text"));
        $date1 = date('d/m/Y', strtotime($dateResit));
        $month = date('m', strtotime($dateResit));
        $yr = date('Y', strtotime($dateResit));
        $date = strtotime(str_replace('/', '-', $date1));
        $today = strtotime(str_replace('/', '-', date("d/m/Y")));

        $diff = $today - $date;
        $diffInDays = $diff / (60 * 60 * 24);
        $averageDaysPerMonth = 30.44;
        $diffInMonths = round($diffInDays / $averageDaysPerMonth);

        /////////////////////////////Dapatkan Baki Pokok////////////////////////////////////
        $getJumlahLoan = "SELECT loanAmt + (loanAmt * kadar_u/100 * loanPeriod/12) AS jumlahPembiayaan FROM loans WHERE status ='7' AND loanID = " . $GetLoan->fields(loanID);
        $rsJumlahLoan = $conn->Execute($getJumlahLoan);
        $jumlahPembiayaan = $rsJumlahLoan->fields(jumlahPembiayaan);

        $getTOpen = "SELECT 
        SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
        FROM transaction
        WHERE
        pymtRefer = '" . $bond . "'
        AND userID = '" . $userID . "' 
        GROUP BY userID";
        $rsTOpen = $conn->Execute($getTOpen);
        $payment = $rsTOpen->fields(yuranKt);


        $balanceHL = $jumlahPembiayaan - $payment;

        // Define the SQL query with aggregate functions to avoid the while loop
        $sSQL = "SELECT 
                    SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END) AS totaldebit, 
                    SUM(CASE WHEN addminus != 0 THEN pymtAmt ELSE 0 END) AS totalkreditID
                    FROM transactionhl 
                    WHERE userID = '$userID' AND pymtRefer = '$bond'";

        // Execute the query
        $rs = &$conn->Execute($sSQL);

        // Retrieve the results        
        $totaldebit = $rs->fields['totaldebit'];
        $totalkreditID = $rs->fields['totalkreditID'];

        // Optionally calculate baki and bakipembayaran if needed
        $baki = $totalkreditID - $totaldebit;
        $bakipembayaran = $balanceHL - $baki;
        $bakiterakhir = $bakipembayaran - $bakiloop;

        /////////////////////////////Tutup Baki Pokok////////////////////////////////////

        if ($diffInMonths > 12) {
            $status = "Hutang Lapuk";

            $totalBalanceHL += $bakiterakhir;
            $totalAmaunLstByr += $amaunLstByr;

            print '<tr style="font-family: Poppins, sans-serif; font-size: 9pt;">
               <td align="center">' . $cnt    . '&nbsp;</td>
			   <td align="center">' . $userID . '</td>
			   <td>' . $nama . '</td>						
			   <td>' . $loanName . '</td>		
			   <td align="center">' . $GetLoan->fields(loanNo) . '</td>
			   <td align="center">' . $bond . '</td>
			   <td align="right">' . number_format($bakiterakhir, 2, '.', ',') . '</td>
			   <td align="right">' . number_format($amaunLstByr, 2, '.', ',') . '</td>	
			   <td align="center">' . $date1 . '</td>						
			   <td align="center">' . $diffInMonths . '</td>
			   <td align="center">' . $status . '</td>			
            </tr>';

            $cnt++;
        }
        $bil++;
        $GetLoan->MoveNext();
    }

    print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
            <td colspan="6">Jumlah Keseluruhan:</td>
            <td align="right">' . number_format($totalBalanceHL, 2) . '</td>
            <td align="right">' . number_format($totalAmaunLstByr, 2) . '</td>
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
