<?php
/*********************************************************************************
 *           Project         :    iKOOP.com.my
 *           Filename        :    rTrialBal.php
 *           Date            :    03/2025
 *********************************************************************************/
if (!isset($StartRec))  $StartRec = 1;
if (!isset($pg))        $pg = 10;
if (!isset($q))         $q = "";
if (!isset($by))        $by = "0";
if (!isset($filter))    $filter = "0";
if (!isset($dept))      $dept = "";

include("header.php");
include("koperasiQry.php");
include("AccountQry.php");	
date_default_timezone_set("Asia/Kuala_Lumpur");

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> 0) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName  = '?vw=rTrialBal&mn='.$mn.'';
$title      = "Laporan Imbangan Duga (Trial Balance)";

// Menangani tarikh dari borang
if (isset($_POST['dtFrom']) && isset($_POST['dtTo'])) {
    $dtFrom = $_POST['dtFrom'];
    $dtTo   = $_POST['dtTo'];
} else {
    $dtFrom = date('Y-m-d');
    $dtTo   = date('Y-m-d');
}

$rptURL     = 'rptA25style.php?dtFrom='.$dtFrom.'&dtTo='.$dtTo;

$sSQL = "SELECT a.*,b.* FROM transactionacc a, generalacc b WHERE a.deductID=b.ID AND a.docID NOT IN (0) AND (a.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') GROUP BY a.deductID ORDER BY b.code ";
$rs = &$conn->Execute($sSQL);
$rs->Move($StartRec - 1);

$TotalRec   =    $rs->RowCount();
$TotalPage  =  ($TotalRec / $pg);

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action"> 
<h5 class="card-title mb-5">
  <span style="font-size: 14px; color: #888;">
    <a href="?vw=reports&cat=D&mn=' . $mn . '" style="color: #888; text-decoration: none;">Laporan Akaun</a> &gt; 
  </span>
  <span style="font-size: 16px; color: black;">' . $title . '</span>
</h5>
';

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

<script>
function laporan() {
    var rptUrl;
    window.open ("'.$rptURL.'", "pop","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
}
</script>

<tr>
  <td>
    <div class="d-flex justify-content-start mt-4 mb-3">
      <input type="submit" class="btn btn-md btn-primary" value="Generate" />
      <div class="hidden-print ms-2">
        <input type="submit" class="btn btn-md btn-secondary" value="Cetak Laporan" onclick="laporan()" />
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
                <table id="dataTable" border="1" cellpadding="2" cellspacing="1" align="center" width="100%" class="table table-bordered table-striped">
                    <tr class="table-primary" style="font-family: Poppins, sans-serif; font-size: 9pt;">
                        <th nowrap style="text-align: center;">BIL</th>
                        <th nowrap style="text-align: center;">KOD AKAUN</th>
                        <th nowrap style="text-align: left;">NAMA AKAUN</th>
                        <th nowrap style="text-align: right;">DEBIT (RP)</th>
                        <th nowrap style="text-align: right;">KREDIT (RP)</th> 
                    </tr>';
$totaldebit 	= 0;
$totalkredit 	= 0;

if ($rs->RowCount() <> 0) {
    $bil = 0;
    while(!$rs->EOF) {	
		$jumlah 		= 0;
		$tarikh_baucer 	= toDate("d/m/y",$rs->fields('tarikh_doc'));
        $deductID       = $rs->fields('deductID');

		$glname 		= dlookup("generalacc", "name", "ID=" . tosql($deductID, "Text"));
		$glnameCode 	= dlookup("generalacc", "code", "ID=" . tosql($deductID, "Text"));

		$getAmaunTBD	= getAmaunTBD($deductID,$dtFrom,$dtTo);

		$debit1 		= $getAmaunTBD->fields('amaun');

		$getAmaunTBK	= getAmaunTBK($deductID,$dtFrom,$dtTo);
		$kredit1 		= $getAmaunTBK->fields('amaun');
		$bil++;
print '
	<tr style="font-family: Poppins, sans-serif; font-size: 9pt;">
		<td width="2%" align="center">'.$bil.')&nbsp;</td>
		<td align="center">'.$glnameCode.'</a></td>
		<td align="left">'.$glname.' </a></td>';
	
		if ($debit1 == 0){ 
			print'	<td class="Data" align ="right">0.00</td>';			
		}
		else {
			print'	<td class="Data" align ="right">'.$debit1.'</td>';	
			$totaldebit += $debit1;
		}

		if ($kredit1 == 0){ 
			print'	<td class="Data" align ="right">0.00</td>';	
		}	
		else {
			print'	<td class="Data" align ="right">'.$kredit1.'</td>';
			$totalkredit += $kredit1;
		}

	print'</tr>';

	$rs->MoveNext();
}	

print'	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
		<td colspan="3" align="right"><b>&nbsp;JUMLAH KESELURUHAN (RP)</b></td>
		<td align="right">RM&nbsp;'.number_format($totaldebit,2).'</td>
		<td align="right">RM&nbsp;'.number_format($totalkredit,2).'</td>
	</tr>';

$baki = ($totaldebit - $totalkredit);

print'	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
		<td colspan="3" align="right"><b>&nbsp;BAKI (RP)</b></td>
		<td colspan="2" align="right">RM&nbsp;'.number_format($baki,2).'</td>
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
        const table = document.getElementById(\'dataTable\');
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
        var table = document.getElementById("dataTable");
        var wb = XLSX.utils.table_to_book(table, { sheet: "Data" });
        var dtFrom = "'.$dtFrom.'";
        var dtTo = "'.$dtTo.'";
        var filename = "imbangan_duga_" + dtFrom + "_ke_" + dtTo + ".xlsx";
        XLSX.writeFile(wb, filename);
    });
</script>';