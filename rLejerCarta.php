<?php
/*********************************************************************************
 *           Project         :    iKOOP.com.my
 *           Filename        :    rLejerCarta.php
 *           Date            :    03/2025
 *********************************************************************************/
if (!isset($StartRec))  $StartRec = 1;
if (!isset($pg))        $pg = 10;
if (!isset($q))         $q = "";
if (!isset($by))        $by = "0";
if (!isset($filter))    $filter = "0";
if (!isset($dept))      $dept = "";

// Import Select2 CSS and JavaScript
print '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>';

include("header.php");
include("koperasiQry.php");
include("AccountQry.php");	
date_default_timezone_set("Asia/Jakarta");

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> 0) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName  = '?vw=rLejerCarta&mn='.$mn.'';
$title      = "Laporan Penyata Lejer Mengikut Carta Akaun";

// Menangani tarikh dari borang
if (isset($_POST['dtFrom']) && isset($_POST['dtTo']) && isset($_POST['kod_akaun2'])) {
    $dtFrom = $_POST['dtFrom'];
    $dtTo   = $_POST['dtTo'];
    $kod_akaun2   = $_POST['kod_akaun2'];
} else {
    $dtFrom = date('Y-m-d');
    $dtTo   = date('Y-m-d');
    $kod_akaun2   = '';
}

$rptURL     = 'rptA22.php?dtFrom='.$dtFrom.'&dtTo='.$dtTo.'&kodAkaun='.$kod_akaun2;

$sqlLoan = "SELECT DISTINCT(a.deductID) AS deduct, b.* FROM transactionacc a, generalacc b WHERE a.deductID=b.ID AND b.ID = '$kod_akaun2' ORDER BY b.code ASC";
$rsLoan = $conn->Execute($sqlLoan);

$rsLoan->Move($StartRec - 1);

$TotalRec   = $rsLoan->RowCount();
$TotalPage  = ($TotalRec / $pg);

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

$strDeductIDList = deductListb2(1);
$strDeductCodeList = deductListb2(2);
$strDeductNameList = deductListb2(3);
$strSelect = '<select id="selectDeduct" class="form-select">
			 <option value="">- Pilih -';

             for ($i = 0; $i < count($strDeductIDList); $i++) {
                $selected = ($strDeductIDList[$i] == $kod_akaun2) ? ' selected' : '';
                $strSelect .= '<option value="'.$strDeductIDList[$i].'"'.$selected.'>'
                              .$strDeductCodeList[$i].'&nbsp;&nbsp;'.$strDeductNameList[$i].'</option>';
            }
$strSelect .= '</select>';

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
    <td><input type="date" class="form-controlx" name="dtFrom" value="' . $dtFrom . '"/></td>
  </tr>
  <tr>
    <td>Sehingga Tarikh</td>
    <td><input type="date" class="form-controlx" name="dtTo" value="' . $dtTo . '"/></td>
  </tr>
  <tr>
    <td>Pilih Akaun</td>
    <td>
      '.$strSelect.'
      <input type="hidden" id="kod_akaun2" name="kod_akaun2" size="10" maxlength="10" value="'.$kod_akaun2.'" class="form-controlx">
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="d-flex justify-content-start mt-4 mb-3">
        <input type="submit" class="btn btn-md btn-primary" value="Generate" />
        <div class="hidden-print ms-2">
          <input type="submit" class="btn btn-md btn-secondary" value="Cetak Laporan" onclick="laporan()" />
        </div>
      </div>
    </td>
  </tr>
</table>

<script>
function laporan() {
  var rptUrl;
  window.open("'.$rptURL.'", "pop", "scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
}
</script>

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
';

print '
<div class="table-responsive">';

while (!$rsLoan->EOF) {
$i = 0;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sql = "SELECT * FROM generalacc WHERE ID = '".$rsLoan->fields(deduct)." ' "; 	
$Get =  &$conn->Execute($sql);
if ($Get->RowCount() > 0) {
    $id = $rsLoan->fields("deduct");
} else {
    $id = $rsLoan->fields("MdeductID");
}

$nameakaun = dlookup("generalacc", "name", "ID=" . tosql($id, "Number"));
$codeakaun = dlookup("generalacc", "code", "ID=" . tosql($id, "Number"));

$title  = 'CARTA AKAUN :- ('.$codeakaun.') - '.$nameakaun.' ';
$title = strtoupper($title);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sSQL = "";
$sSQL = "SELECT	* FROM transactionacc WHERE deductID = '$id' AND docID NOT IN (15) AND (tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') ORDER BY tarikh_doc ASC,docNo";
$rs = &$conn->Execute($sSQL);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//cek tarikh paling awal ada OP
$getDate = "select MIN(tarikh_doc) as minDate from transactionacc
 WHERE docID IN (15)
 AND deductID = '".$id."'";
$rsDate = &$conn->Execute($getDate);
$minDate = $rsDate->fields(minDate);

// $getOpen = "SELECT * FROM transactionacc WHERE docID IN (15) AND deductID = '".$id."' AND YEAR(tarikh_doc) = ".(int)substr($dtFrom,0,4)." ";
$getOpen = "SELECT pymtAmt, addminus FROM transactionacc WHERE docID IN (15) AND deductID = '".$id."' AND tarikh_doc <= '".$dtTo."' ORDER BY tarikh_doc ASC LIMIT 1";
$rsOpen = $conn->Execute($getOpen);

$amaunD = 0;
$amaunK = 0;

//OP sahaja
if ($rsOpen->fields(addminus) == 0) $amaunD = $rsOpen->fields('pymtAmt');
if ($rsOpen->fields(addminus) == 1) $amaunK = $rsOpen->fields('pymtAmt');

$getYuranOpen = "SELECT 
	SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
	SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
	FROM transactionacc
	WHERE
	deductID = '".$id."' AND docID NOT IN (15) 
	AND tarikh_doc < '".$dtFrom."'";
$rsYuranOpen = $conn->Execute($getYuranOpen);

$balanced = 0;
$balancek = 0;

//OP + transaction sebelum dtFrom
$balanced = $rsYuranOpen->fields(yuranDb) + $amaunD;
$balancek = $rsYuranOpen->fields(yuranKt) + $amaunK;

//baki = OP sahaja
$totBal = ($amaunD - $amaunK);

//baki = campur OP dan transaction sebelum dtFrom
$totalbalance = ($balanced - $balancek);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

print '
<table class="exportTable" border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="2" height="40" align="left"><b>'.$title.'</b></th>
	</tr> 
	
	<tr>
		<td colspan="2">
                <table id="dataTable" border="1" cellpadding="2" cellspacing="1" align="center" width="100%" class="table table-bordered table-striped">
                    <tr class="table-primary" style="font-family: Poppins, sans-serif; font-size: 9pt;">
					<th nowrap width="5%">Bil</th>
					<th nowrap align="center" width="5%">Tarikh</th>
					<th nowrap align="left" width="10%">Batch</th>
					<th nowrap align="left" width="10%">Nombor Rujukan</th>
					<th nowrap align="left" width="25%">Perkara</th>
					<th nowrap align="right" width="15%">Debit(RM)</th>
					<th nowrap align="right" width="15%">Kredit(RM)</th>
					<th nowrap align="right" width="15%">Baki(RM)</th>
				</tr>';

				print '
	        <tr style="font-family: Poppins, sans-serif; font-size: 9pt;">
			<td width="10%" colspan=5 align="right">&nbsp;<b>Baki H/B</b></td>
			<td width="10%" align="right">&nbsp;<b>'.number_format($balanced,2).'</b></td>
			<td width="10%" align="right">&nbsp;<b>'.number_format($balancek,2).'</b></td>
			<td width="10%" align="right">&nbsp;<b>'.number_format($totalbalance,2).'</b></td>
		</tr>';
				
				$totaldebit = 0;
				$totalkredit =0; 
				$debTkre=0;

                if ($rs->RowCount() <> 0) {	

				while(!$rs->EOF) {		

			$namabatch = dlookup("generalacc", "name", "ID=" . tosql($rs->fields(batchNo), "Number"));	

            print '
	            <tr style="font-family: Poppins, sans-serif; font-size: 9pt;">
				<td width="5%" align="center">'.++$i.'.</td>
				<td width="5%" align="center">&nbsp;'.toDate('d/m/y',$rs->fields(tarikh_doc)).'</td>
				<td width="10%">'.$namabatch.'</td>
				<td width="2%">'.$rs->fields(docNo).'</td>';

				if ($rs->fields(docID)==11) 
				{
					$namaded = dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), "Number"));	
					
					print '<td width="20%">'.$namaded.'</td>';
				} elseif ($rs->fields(docID)==10) {
					$namaded = dlookup("resit", "catatan", "no_resit=" . tosql($rs->fields(docNo), "Text"));	
					
					print '<td width="20%">'.$namaded.'</td>';
				} elseif ($rs->fields(docID)==12) {
					$namaded = dlookup("vauchers", "keterangan", "no_baucer=" . tosql($rs->fields(docNo), "Text"));	
					
					print '<td width="20%">'.$namaded.'</td>';
				}
				else 
				{ 
					print '<td width="20%">'.$rs->fields(desc_akaun).'</td>';
				}

			if ($rs->fields(addminus)==0) {

				$debit = $rs->fields(pymtAmt);
				$totaldebit += $debit;	
				
				print '<td width="5%" align="right">'.number_format($debit,2).'</td>
						<td width="5%" align="right">0.00</td>';
			}

			if ($rs->fields(addminus)==1) {

				$kredit = $rs->fields(pymtAmt);
				$totalkredit += $kredit;
				print '<td width="5%" align="right">0.00</td>
						<td width="5%" align="right">'.number_format($kredit,2).'</td>';
			}

							
			$debTkre = ($totaldebit - $totalkredit);

			$belen = ($totalbalance + $debTkre);
			
			print'	<td width="5%" align="right">'.number_format($belen,2).'</td>
			</tr>';

			$rs->MoveNext();
			}	

		print '
	        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
			<td width="10%" colspan=4 align="right">&nbsp;</td>
			<td width="10%" align="right"><b>Jumlah </b></td>
			<td width="10%" align="right">&nbsp;'.number_format($totaldebit,2).'</td>
			<td width="10%" align="right">&nbsp;'.number_format($totalkredit,2).'</td>
			<td width="10%" align="right">&nbsp;</td>
		</tr>';



		print '
	        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
			<td width="10%" colspan=4 align="right">&nbsp;</td>
			<td width="10%" align="right">&nbsp;<b>Baki B/B</b></td>
			<td width="10%" align="left">&nbsp;</td>
			<td width="10%" align="left">&nbsp;</td>
			<td width="10%" align="right">&nbsp;<b>'.number_format($belen,2).'</b></td>
		</tr>';


    } else {
		print '
			<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td colspan="8" align="center"><b>- Tiada Rekod Urusniaga -</b></td>
			</tr>';
		}
print '     </table> 
        </td>
    </tr>    
    </table> 
';

$rsLoan->MoveNext();
}	

if ($rsLoan->RecordCount()<1)
print '
    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;">
        <td colspan="7" align="center"><b>- Tiada Rekod -</b></td>
    </tr>
';

print ' 
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
        var dtFrom = "'.$dtFrom.'";
        var dtTo = "'.$dtTo.'";
        var codeakaun = "'.$codeakaun.'";
        var nameakaun = "'.$nameakaun.'";
        var wb = XLSX.utils.table_to_book(table, { sheet: codeakaun + " " + nameakaun });
        var filename = "lejer_carta_" + dtFrom + "_ke_" + dtTo + "_kod_" + codeakaun +".xlsx";
        XLSX.writeFile(wb, filename);
    });

    $(document).ready(function() {
        // Update hidden input when selection changes
        $("#selectDeduct").on("change", function() {
            var selectedValue = $(this).val();
            $("#kod_akaun2").val(selectedValue);
        });

        // Initialize Select2
        $("#selectDeduct").select2({
            placeholder: "- Pilih -"
        });
    });

</script>';