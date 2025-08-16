<?php

/*********************************************************************************
 *           Project        :    iKOOP.com.my
 *           Filename        :    rptAgingLoan.php
 *           Date            :    19/02/2025
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

$sFileName = '?vw=rptAgingLoan&mn=906';
$title       = "Laporan Aging";

if (isset($_POST['dtTo'])) {
	$dtFrom = $_POST['dtFrom'];
	$dtTo = $_POST['dtTo'];
	$dtTo1 = date('Ym', strtotime($dtTo));
	$yr = date('Y', strtotime($dtTo));
	$mth = date('m', strtotime($dtTo));
} else {
	$dtFrom = date('Y-m-d');
	$dtTo = date('Y-m-d');
	$yr = date('Y', strtotime($dtTo));
	$mth = date('m', strtotime($dtTo));
}

$sSQL = "SELECT a.*, b.*, c.*
			FROM loans a, loandocs b, vauchers c
			WHERE a.loanID = b.loanID
			AND b.rnoBond = c.no_bond
			AND b.result = 'lulus'
			AND b.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "'
			AND (b.rnoBaucer IS NOT NULL OR b.rnoBaucer != '')
			ORDER BY loanNo ASC";

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
    <!--td>Dari Tarikh</td-->
    <td>Sehingga Tahun/Bulan</td>
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
                        <td nowrap align="center">&nbsp;</td>
						<td nowrap align="left">No Anggota</td>
						<td nowrap align="left">Nama Anggota</td>
						<td nowrap align="center">No Rujukan</td>	
						<td nowrap align="center">Tarikh Baucer</td>		
						<td nowrap align="center">No Bond</td>			
						<td nowrap align="center">Mula Perlu Bayar</td>	
						<td nowrap align="right">Ansuran Bulanan (RM)</td>
						<td nowrap align="right">Bayaran Perlu Dibayar (RM)<br/>(' . $mth . '/' . $yr . ')</td>
						<td nowrap align="right">Bayaran Diterima (RM)<br/>(' . $mth . '/' . $yr . ')</td>
						<td nowrap align="right">Baki Tunggakan (RM)</td>
						<td nowrap align="center">Bulan Tunggakan</td>
						<td nowrap align="right">Jum. Pembiayaan (RM)<br/>(Pokok + Untung)</td>		
						<td nowrap align="right">Jumlah Bayaran Transaksi (RM)</td>
						<td nowrap align="right">Baki (RM)</td>
						<!--td nowrap align="right">Caj Lewat (RM)</td-->
                    </tr>';

// Initialize total variables
$totalByrBln = 0;
$totalJumPttByr = 0;
$totalDahByr = 0;
$totalJumTggkn = 0;
$totalBiayaN = 0;
$totalBaki = 0;

if ($GetLoan->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	while (!$GetLoan->EOF) {
		$rnoBond = $GetLoan->fields(rnoBond);

		$mthStrt = dlookup("potbulan", "monthStart", "bondNo=" . tosql($rnoBond, "Text"));
		$yrStrt  = dlookup("potbulan", "yearStart", "bondNo=" . tosql($rnoBond, "Text"));
		$yrmthStrt  = dlookup("potbulan", "yrmth", "bondNo=" . tosql($rnoBond, "Text"));

		$ssSQL = " SELECT SUM(pymtAmt) as dahBayar 
		FROM transaction 
		WHERE pymtRefer = '$rnoBond' 
		AND addminus = 1 
		AND yrmth >= " . $yrmthStrt . "
		AND yrmth <= " . $dtTo1 . "";
		$GetBayar = $conn->Execute($ssSQL);

		$sSSQL = " SELECT SUM(pymtAmt) as dahBayarMth 
		FROM transaction 
		WHERE pymtRefer = '$rnoBond' 
		AND addminus = 1 
		AND yrmth = " . $dtTo1 . "";
		$GetBayarMth = $conn->Execute($sSSQL);

		if ($mthStrt <> "" && $yrStrt <> "" && $yrmthStrt <> "") {

			// Dapatkan tahun dan bulan untuk setiap tarikh
			$year = substr($dtTo1, 0, 4);
			$month = substr($dtTo1, 4, 2);

			$yearStart = substr($yrmthStrt, 0, 4);
			$monthStart = substr($yrmthStrt, 4, 2);

			// perbezaan tahun dan bulan
			$diffYears = $year - $yearStart;
			$diffMonths = $diffYears * 12 + ($month - $monthStart) + 1;

			$loanPeriod = $GetLoan->fields(loanPeriod);
			$loanPeriod = $loanPeriod + 6;

			$dahBayar = $GetBayar->fields(dahBayar);

			//untuk cek kalau beza berapa bulan
			if ($diffMonths <= 6) {
				//ansuran bulanan
				$byrBulanan = $GetLoan->fields('6month');

				//jum spttnya bayar
				$jumPttByr = $byrBulanan * 6;

				//jumlah tertunggak
				$jumTunggakan =  $jumPttByr - $dahBayar;

				//bulan tunggakan
				$bakiTunggakan = round($jumTunggakan / $byrBulanan);

				//caj dikenakan
				$caj = $jumTunggakan * 0.01;
			} else if ($diffMonths == $loanPeriod) {
				//ansuran bulanan	
				$byrBulanan = dlookup("potbulan", "lastPymt", "bondNo=" . tosql($rnoBond, "Text"));
				$byrBulanan = dlookup("potbulan", "jumBlnP", "bondNo=" . tosql($rnoBond, "Text"));

				//jum spttnya bayar
				$jumPttByr = $bulanan + $byrBulanan;

				//jumlah tertunggak
				$jumTunggakan =  $jumPttByr - $dahBayar;

				//bulan tunggakan
				$bakiTunggakan = round($jumTunggakan / $byrBulanan);

				//caj dikenakan
				$caj = $jumTunggakan * 0.01;
			} else {
				//ansuran bulanan	
				$byrBulanan = dlookup("potbulan", "jumBlnP", "bondNo=" . tosql($rnoBond, "Text"));

				$tolak6Bln = $diffMonths - 6;
				$jumAftr6Bln = $tolak6Bln * $byrBulanan;
				$jumBef6Bln = $GetLoan->fields('6month') * 6;
				//jum spttnya bayar	
				$jumPttByr = $jumAftr6Bln + $jumBef6Bln;

				//jumlah tertunggak
				$jumTunggakan =  $jumPttByr - $dahBayar;

				//bulan tunggakan
				$bakiTunggakan = round($jumTunggakan / $byrBulanan);

				//caj dikenakan
				$caj = $jumTunggakan * 0.01;
			}
		} else {
			$byrBulanan = 0;
			$jumPttByr = 0;
			$jumTunggakan = 0;
			$caj = 0;
			$mthStrt = '??';
			$yrStrt = '????';
			$dahBayar = 0;
			$bakiTunggakan = 0;
		}

		// Accumulate totals
		$totalByrBln += $byrBulanan;
		$totalJumPttByr += $jumPttByr;
		$totalDahByr += $dahBayar;
		$totalJumTggkn += $jumTunggakan;
		$totalBiayaN += $GetLoan->fields(lpotBiayaN);
		$totalBaki += ($GetLoan->fields(lpotBiayaN) - $dahBayar);

		print '<tr style="font-family: Poppins, sans-serif; font-size: 9pt;">
              <td class="Data" align="center">' . $bil . '</td>
			  			<td class="Data" align="center">' . $GetLoan->fields(userID) . '</td>				
						<td class="Data" align="left">' . ucwords(strtolower(dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Text")))) . '</td>
						<td class="Data" align="center">' . $GetLoan->fields(loanNo) . '</td>
						<td class="Data" align="center">' . toDate('d/m/Y', $GetLoan->fields(tarikh_baucer)) . '</td>
						<td class="Data" align="center">' . $GetLoan->fields(rnoBond) . '</td>	
						<td class="Data" align="center">' . $mthStrt . '/' . $yrStrt . '</td>
						<td class="Data" align="right">' . number_format($byrBulanan, 2) . '</td>
						<td class="Data" align="right">' . number_format($jumPttByr, 2) . '</td>	
						<td class="Data" align="right">' . number_format($dahBayar, 2) . '</td>';
		if ($jumTunggakan <= 0) {
			print '<td class="Data text-primary" align="right">' . number_format($jumTunggakan, 2);
		} else {
			print '<td class="Data text-danger" align="right" >' . number_format($jumTunggakan, 2);
		}
		print '
						<td class="Data" align="center">' . $bakiTunggakan . '</td>	
						<td class="Data" align="right">' . number_format($GetLoan->fields(lpotBiayaN), 2) . '</td>
						<td class="Data" align="right">' . number_format($dahBayar, 2) . '</td>	
						<td class="Data" align="right">' . number_format((($GetLoan->fields(lpotBiayaN)) - $dahBayar), 2) . '</td>				
						<!--td class="Data" align="right">' . number_format($caj, 2) . '</td-->		
            </tr>';

		$cnt++;

		$bil++;
		$GetLoan->MoveNext();
	}

	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
            <td colspan="7">Jumlah Keseluruhan:</td>
            <td align="right">' . number_format($totalByrBln, 2) . '</td>
            <td align="right">' . number_format($totalJumPttByr, 2) . '</td>
			<td align="right">' . number_format($totalDahByr, 2) . '</td>
			<td align="right">' . number_format($totalJumTggkn, 2) . '</td>
			<td>&nbsp;</td>
			<td align="right">' . number_format($totalBiayaN, 2) . '</td>
			<td align="right">' . number_format($totalDahByr, 2) . '</td>
			<td align="right">' . number_format($totalBaki, 2) . '</td>
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
