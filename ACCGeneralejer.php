<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	ACCGeneralejer.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
date_default_timezone_set("Asia/Kuala_Lumpur");
if (!isset($mm))    $mm = "ALL"; //date("m");
if (!isset($yy))    $yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

if (!isset($StartRec))    $StartRec = 1;
if (!isset($pg))        $pg = 50;
if (!isset($q))            $q = "";
if (!isset($code))        $code = "ALL";
if (!isset($filter))    $filter = "0";
if (!isset($akaun))        $akaun = "";

include("header.php");
include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sFileName     = "?vw=ACCGeneralejer&mn=$mn"; //file name
$sFileRef2  = "?vw=ACCSingleEntry&mn=$mn"; //master lejer - transaksi entry
$sFileRef3  = "?vw=ACCbaucerpembayaran&mn=$mn"; //buku tunai - pembayaran
$sFileRef4  = "?vw=ACCresitpembayaran&mn=$mn"; // buku tunai - resit akaun
$sFileRef5  = "?vw=ACCinvoicedebtor&mn=$mn"; // penghutang - invois
$sFileRef6  = "?vw=ACCDebtorPayment&mn=$mn"; // penghutang - terima bayaran
$sFileRef8  = "?vw=ACCpurchaseInvoice&mn=$mn"; // pemiutang - purchase invois
$sFileRef9  = "?vw=ACCbillpembayaran&mn=$mn"; // pemiutang - bayaran bil
$sFileRef10 = "?vw=resit&mn=$mn"; // urusniaga anggota - resit
$sFileRef12 = "?vw=journals&mn=$mn"; // buku tunai - baucer jurnal (anggota)
$sFileRef13 = "?vw=baucer&mn=$mn"; // urusniaga anggota - baucer
$title         =  "General Lejer"; //Title 

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sSQL     = "";
$sWhere = " A.docID NOT IN (0,15) AND YEAR(A.tarikh_doc) =" . $yy;

if ($q <> "" || $akaun <> "") {
    if ($by == 1) {
        $sWhere .= " AND A.docNo like '%" . $q . "%'";
    } else if ($by == 2) {
        $sWhere .= " AND A.batchNo = B.ID";
        $sWhere .= " AND B.name like '%" . $q . "%'";
    } else if ($by == 3) {
        $sWhere .= " AND A.deductID = B.ID";
        $sWhere .= " AND B.name like '%" . $q . "%'";
    } else if ($by == 4) {
        $sWhere .= " AND A.deductID = $akaun";
    }
}

$sWhere = " WHERE (" . $sWhere . ")";

if ($q <> "" || $akaun <> "") {
    if ($by == 2 or $by == 3) {
        $sSQL = "SELECT	A.*,B.* FROM transactionacc A, generalacc B";
    } else if ($by == 1 or $by == 4) {
        $sSQL = "SELECT	DISTINCT A.* FROM transactionacc A";
    }
} else {
    $sSQL = "SELECT	*,ID as transID FROM transactionacc A ";
}
if ($mm <> "ALL") $sWhere .= " AND month( A.tarikh_doc ) =" . $mm;
$sSQL = $sSQL . $sWhere . ' ORDER BY A.docNo ASC,A.tarikh_doc DESC';


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$GetBaucers = &$conn->Execute($sSQL);
$GetBaucers->Move($StartRec - 1);

// $batchName 	= dlookup("generalacc", "name", "ID=" . tosql($GetBaucers->fields('batchNo'), "Text"));
$glname     = dlookup("generalacc", "name", "ID=" . tosql($GetBaucers->fields('deductID'), "Text"));

$TotalRec     = $GetBaucers->RowCount();
$TotalPage     =  ($TotalRec / $pg);

$sqlYears     = "SELECT DISTINCT YEAR(tarikh_doc) AS year FROM transactionacc 
				WHERE tarikh_doc IS NOT NULL 
				AND tarikh_doc != '' 
				AND tarikh_doc != 0 
				ORDER BY year ASC";
$rsYears     = $conn->Execute($sqlYears);

// Query distinct kod carta akaun that are used, excluding empty strings or NULL values
$sSQLakaun2 = "SELECT DISTINCT deductID, JdeductID
                FROM transactionacc 
                WHERE (deductID IS NOT NULL AND deductID != '')
				OR (JdeductID IS NOT NULL AND JdeductID != '')
                ORDER BY deductID, JdeductID ASC";
$rsAkaun2   = &$conn->Execute($sSQLakaun2);
$akaunDeductIDList = array();

// Check if the query returned any results
if ($rsAkaun2->RowCount() != 0) {
    while (!$rsAkaun2->EOF) {
        // Add each distinct deductID and JdeductID only if non-empty
        if (!empty($rsAkaun2->fields['deductID'])) {
            $akaunDeductIDList[] = $rsAkaun2->fields['deductID'];
        }
        if (!empty($rsAkaun2->fields['JdeductID'])) {
            $akaunDeductIDList[] = $rsAkaun2->fields['JdeductID'];
        }
        $rsAkaun2->MoveNext();
    }
    // Remove duplicate values from the array
    $akaunDeductIDList = array_unique($akaunDeductIDList);
}

// Convert the array to a comma-separated string for the SQL query
$akaunDeductIDListString = implode(',', $akaunDeductIDList);

$akaunList = array();
$akaunVal  = array();

// SQL filtering carta akaun currently in use and depends on selected year
$sSQLakaun  = "SELECT DISTINCT
                transactionacc.deductID,
                generalacc.name AS generalaccName,
                generalacc.code AS generalaccCode,
				generalacc.ID AS generalaccID,
                general.name AS generalName,
                general.code AS generalCode,
                general.ID AS generalID
            FROM
                transactionacc
            LEFT JOIN
                generalacc ON transactionacc.deductID = generalacc.ID
            LEFT JOIN
                general ON transactionacc.deductID = general.ID
            WHERE 
                YEAR(transactionacc.tarikh_doc) = '$yy'
                AND (
                    generalacc.category = 'AA' /* Kod Carta Akaun only */
                    OR general.category = 'J' /* Kod Objek Akaun only */
                )
                AND transactionacc.deductID IN ($akaunDeductIDListString) /* Kod Akaun currently in use only */
                AND transactionacc.docID NOT IN (0) /* excluded docID 0 for buggy data */
            ORDER BY 
                CAST(generalacc.code AS UNSIGNED) ASC,
                general.code ASC";
$rsAkaun     = &$conn->Execute($sSQLakaun);

// By checking if $rsAkaun is truthy first, you ensure that you're only attempting to call RowCount() on a valid object.
if ($rsAkaun && $rsAkaun->RowCount() != 0) {
    while (!$rsAkaun->EOF) {
        // Combine used kod carta akaun and kod objek akaun together for the dropdown
        if (trim($rsAkaun->fields['generalaccCode']) !== '') {
            $akaunList[] = trim($rsAkaun->fields['generalaccCode']) . '&nbsp;&nbsp;-&nbsp;&nbsp;' . $rsAkaun->fields['generalaccName'];
        }

        if (trim($rsAkaun->fields['generalCode']) !== '') {
            $akaunList[] = trim($rsAkaun->fields['generalCode']) . '&nbsp;&nbsp;-&nbsp;&nbsp;' . $rsAkaun->fields['generalName'];
        }

        if (trim($rsAkaun->fields['generalaccID']) !== '') {
            $akaunVal[] = trim($rsAkaun->fields['generalaccID']);
        }

        if (trim($rsAkaun->fields['generalID']) !== '') {
            $akaunVal[] = trim($rsAkaun->fields['generalID']);
        }

        $rsAkaun->MoveNext();
    }
}

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
    print '	<option value="' . $j . '"';
    if ($mm == $j) print 'selected';
    print '>' . $j;
}
print '		</select>
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
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
</div><br/>
<div clas="row">
Carian Melalui
				<select name="by" class="form-select-sm" onchange="toggleSearchFields(this.value);">';
if ($by == 1)    print '<option value="1" selected>Nombor Rujukan</option>';
else print '<option value="1">Nombor Rujukan</option>';
// if ($by == 2)	print '<option value="2" selected>Nama Batch</option>'; 	else print '<option value="2">Nama Batch</option>';					
if ($by == 3)    print '<option value="3" selected>Nama Akaun</option>';
else print '<option value="3">Nama Akaun</option>';
if ($by == 4)    print '<option value="4" selected>Kod Akaun</option>';
else print '<option value="4">Kod Akaun</option>';

print '</select>';

// Dropdown for selecting akaun
print '&nbsp;<select id="akaunDropdown" name="akaun" class="form-select-sm" style="display: ';
print ($by == 4) ? 'inline-block' : 'none';
print ';" onchange="document.MyForm.submit();">
		<option value="">- Semua -';
for ($i = 0; $i < count($akaunList); $i++) {
    print '<option value="' . $akaunVal[$i] . '" ';
    if ($akaun == $akaunVal[$i]) print 'selected';
    print '>' . $akaunList[$i];
}
print '</select>';

// Input box for searching
print '<input id="searchInput" type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm" style="display: ';
print ($by != 4) ? 'inline-block' : 'none';
print ';">
<input type="submit" class="btn btn-sm btn-secondary" value="Cari">';

print ';
</div>
';

$sqljumlah = "SELECT 
SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END) AS totalDebit,
SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END) AS totalCredit
FROM transactionacc
WHERE docID NOT IN (0) AND YEAR(tarikh_doc) = " . $yy;

// concat query if any month is selected
if ($mm !== "ALL") {
    $sqljumlah .= " AND MONTH(tarikh_doc) = $mm";
    $stringDesc = "Bulan $mm ";
}

$rsjumlah = $conn->Execute($sqljumlah); // Ensure $conn is correctly established

// Initialize totals from the query result
$totalDebit = $rsjumlah->fields['totalDebit'];
$totalCredit = $rsjumlah->fields['totalCredit'];

// Calculate balance
$balance = $totalDebit - $totalCredit;

// Display totals
print '
<div style="position: absolute; top: 25px; right: 25px;">
	<table border="0" cellspacing="0" cellpadding="5" width="100%">
		<tr class="table-warning">
			<td nowrap align="right" style="padding-right: 5px; width: 500%;" colspan="2"><b><u>Jumlah Pada ' . $stringDesc . ' Tahun ' . $yy . '</u></b></td>
		</tr>
		<tr class="table-warning">
			<td nowrap align="right" style="padding-right: 5px; width: 500%;"><b>Jumlah Debit (RM):</b></td>
			<td nowrap align="right" style="padding-right: 5px; width: 50%;">' . number_format($totalDebit, 2) . '</td>
		</tr>
		<tr class="table-warning">
			<td nowrap align="right" style="padding-right: 5px; width: 500%;"><b>Jumlah Kredit (RM):</b></td>
			<td nowrap align="right" style="padding-right: 5px; width: 50%;">' . number_format($totalCredit, 2) . '</td>
		</tr>
		<tr class="table-warning">
			<td nowrap align="right" style="padding-right: 5px; width: 500%;"><b>Baki (RM):</b></td>
			<td nowrap align="right" style="padding-right: 5px; width: 50%;">' . number_format($balance, 2) . '</td>
		</tr>
	</table>
</div>';

if ($GetBaucers->RowCount() <> 0) {
    $bil = $StartRec;
    $cnt = 1;

    print '
		<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td align="left" class="textFont"><br>Jumlah Rujukan : <b>' . $GetBaucers->RowCount() . '</b></td>

						
						<td align="right" class="textFont"><br>';
    echo papar_ms($pg);
    print ' </td>
					</tr>
				</table>
			</td>
		</tr>';
    print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap align="center">Bil</td>
						<td nowrap>Nombor Rujukan</td>
						<td nowrap align="center">Nama Batch</td>
						<td nowrap align="center">Tarikh</td>
						<td nowrap>Kod Akaun</td>
						<td nowrap>Akaun GL</td>
						<td nowrap>Catatan</td>	
						<td nowrap align ="right">Debit (RM)</td>
						<td nowrap align ="right">Kredit (RM)</td>	
						<td nowrap align="center">Tindakan</td>					
					</tr>';

    $DRTotal = 0;
    $CRTotal = 0;
    while (!$GetBaucers->EOF && $cnt <= $pg) {
        $jumlah = 0;
        $tarikh_baucer = toDate("d/m/y", $GetBaucers->fields('tarikh_doc'));
        $description = dlookup("transactionacc", "desc_akaun", "ID=" . tosql($GetBaucers->fields('ID'), "Text"));
        $batchNo = trim($GetBaucers->fields('batchNo')); // Extract and trim batchNo

        // Check if batchNo is valid (not null, empty string, or 0)
        if (!empty($batchNo) && $batchNo != '0') {
            $sSQL2 = "SELECT g_lockstat FROM generalacc WHERE ID = $batchNo ORDER BY ID";
            $rsDetail = $conn->Execute($sSQL2);
        } else {
            // Handle invalid batchNo case
            $rsDetail = null;
        }

        // Set batchName based on the validity of batchNo
        if (empty($batchNo) || $batchNo == '0') {
            $batchName = "TIADA BATCH";
        } else {
            $batchName = dlookup("generalacc", "name", "ID=" . tosql($batchNo, "Text"));
        }

        $docID = $GetBaucers->fields('docID');

        // yang pakai kod objek akaun selain Jurnal
        if ($docID == 10) { //RT (cater cara penyimpanan deductID and JdeductID yg berbeza)
            $deductID = $GetBaucers->fields('JdeductID') != ''
                ? $GetBaucers->fields('JdeductID')
                : $GetBaucers->fields('deductID');
        }
        // yang pakai kod carta akaun selain Jurnal
        else {
            $deductID = $GetBaucers->fields('deductID') != ''
                ? $GetBaucers->fields('deductID')
                : $GetBaucers->fields('JdeductID');
        }

        $glId     = dlookup("general", "c_master", "ID=" . tosql($deductID, "Text"));

        $glcode   = dlookup("generalacc", "code", "ID=" . tosql($GetBaucers->fields('deductID'), "Text"));
        $glcode1  = dlookup("general", "code", "ID=" . tosql($deductID, "Text"));
        $glcode2  = dlookup("generalacc", "code", "ID=" . $glId);

        $glname   = dlookup("generalacc", "name", "ID=" . tosql($GetBaucers->fields('deductID'), "Text"));
        $glname1  = dlookup("general", "name", "ID=" . tosql($deductID, "Text"));
        $glname2  = dlookup("generalacc", "name", "ID=" . $glId);

        if ($docID == 10 || $docID == 12 || $docID == 3) { //RT, PVA, PV (cater cara penyimpanan deductID and JdeductID yg berbeza)
            // Check if both glcode and glcode1 are empty, set red text if true
            if ((empty($glcode) && empty($glcode1))) {
                print ' <tr style="color: red;">';
            } else {
                print ' <tr>';
            }
        } elseif (!$glcode || !$glname) {
            // Handle non-docID 10 cases where either glcode or glname is missing
            if ($docID == 11) { //J special case
                print ' <tr>';
            } else {
                print ' <tr style="color: red;">';
            }
        } elseif (!$glcode1 || !$glname1) {
            // Handle non-docID 11 cases where either glcode1 or glname1 is missing
            if ($docID != 11) { //J special case
                print ' <tr>';
            } else {
                print ' <tr style="color: red;">';
            }
        }

        print '<td class="Data" style="text-align: center; vertical-align: middle;">' . $bil . '</td>';

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 2) { //SINGLE ENTRY

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
			</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
			<a href="' . $sFileRef2 . '&action=view&SENO=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetBaucers->fields('docNo') . '
			</td>';
            }


            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glcode . '</td>';
            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glname . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCSingleEntryPrint.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef2 . '&action=view&SENO=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCSingleEntryView.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 3) { //BAUCER

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
				</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef3 . '&action=view&no_baucer=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '
				</td>';
            }

            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '  <td class="Data" style="text-align: left; vertical-align: middle;">' . (!empty($glcode) ? $glcode : $glcode1) . '</td>';
            print '  <td class="Data" style="text-align: left; vertical-align: middle;">' . (!empty($glname) ? $glname : $glname1) . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCbaucerpembayaranPrint.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef3 . '&action=view&no_baucer=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCbaucerpembayaranView.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 4) { //RESIT

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
				</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef4 . '&action=view&no_resit=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '
				</td>';
            }

            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glcode . '</td>';
            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glname . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCResitPrintCustomer.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef4 . '&action=view&no_resit=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCResitViewCustomer.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 5) { //INVOICE

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
				</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef5 . '&action=view&invNo=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '</td>';
            }

            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glcode . '</td>';
            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glname . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCinvoicedebtorPrint.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef5 . '&action=view&invNo=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCinvoicedebtorView.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 6) { //BAYAR INVOICE

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
				</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef6 . '&action=view&RVNo=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '</td>';
            }

            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glcode . '</td>';
            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glname . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCDebtorPaymentPrint.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef6 . '&action=view&RVNo=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCDebtorPaymentView.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        //////////////////////////////////////////////////////////////////DEFAULT/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 8) { //PURCHASE INVOICE

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
				</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef8 . '&action=view&PINo=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '</td>';
            }

            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glcode . '</td>';
            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glname . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 1) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 0) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCPurchaseInvoicePrint.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef8 . '&action=view&PINo=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCPurchaseInvoiceView.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 7) { //PEMBAYARAN BIL

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
				</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef9 . '&action=view&no_bill=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '</td>';
            }

            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glcode . '</td>';
            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glname . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCBillPrintCustomer.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef9 . '&action=view&no_bill=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'ACCBillViewCustomer.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 10) { //RESIT ANGGOTA

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
				</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef10 . '&action=view&no_resit=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '</td>';
            }
            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '  <td class="Data" style="text-align: left; vertical-align: middle;">' . (!empty($glcode) ? $glcode : $glcode1) . '</td>';
            print '  <td class="Data" style="text-align: left; vertical-align: middle;">' . (!empty($glname) ? $glname : $glname1) . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'resitPaymentPrint.php?ID=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef10 . '&action=view&no_resit=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'resitPaymentView.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        if ($GetBaucers->fields('docID') == 11) { //JURNAL ANGGOTA

            /*	if ($rsDetail && $rsDetail->fields('g_lockstat') == 1){
		print 	'<td class="Data">&nbsp;'.$GetBaucers->fields('docNo').'
				</td>';
			}
		else {*/

            print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef12 . '&action=view&no_jurnal=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '</td>';
            //	}

            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glcode1 . '&nbsp;(&nbsp;' . $glcode2 . '&nbsp;)</td>';
            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $glname1 . '&nbsp;-&nbsp;' . $glname2 . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'journalsPaymentPrint.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef12 . '&action=view&no_jurnal=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';
            $view         = '<i class="mdi mdi-file-document text-muted" title="lihat" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'journalsPaymentView.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($GetBaucers->fields('docID') == 12) { //BAUCER

            if ($rsDetail && $rsDetail->fields('g_lockstat') == 1) {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">' . $GetBaucers->fields('docNo') . '
				</td>';
            } else {
                print     '<td class="Data" style="text-align: left; vertical-align: middle;">
					<a href="' . $sFileRef13 . '&action=view&no_baucer=' . tohtml($GetBaucers->fields('docNo')) . '&yy=' . $yy . '&mm=' . $mm . '">
					' . $GetBaucers->fields('docNo') . '
				</td>';
            }

            print '	<td class="Data" align="left">' . $batchName . '</td>';
            print '	<td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh_baucer . '</td>';

            print '  <td class="Data" style="text-align: left; vertical-align: middle;">' . (!empty($glcode) ? $glcode : $glcode1) . '</td>';
            print '  <td class="Data" style="text-align: left; vertical-align: middle;">' . (!empty($glname) ? $glname : $glname1) . '</td>';

            print '	<td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>';

            if ($GetBaucers->fields('addminus') == 0) {
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
            }
            if ($GetBaucers->fields('addminus') == 1) {
                print '	<td class="Data" style="text-align: right; vertical-align: middle;">0.00</td>';
                print '<td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($GetBaucers->fields('pymtAmt'), 2) . '</td>';
            }

            $cetak         = '<i class="mdi mdi-printer text-primary" title="cetak" style="font-size: 1.4rem; cursor: pointer;" onClick="open_(\'voucherPaymentPrint.php?id=' . $GetBaucers->fields('docNo') . '\')"></i>';
            $edit         = '<a href="' . $sFileRef13 . '&action=view&no_baucer=' . tohtml($GetBaucers->fields['docNo']) . '&yy=' . $yy . '&mm=' . $mm . '" title="kemaskini"><i class="mdi mdi-lead-pencil text-warning" style="font-size: 1.4rem;"></i></a>';
            $editLock     = '<span style="cursor: not-allowed; color: gray; opacity: 0.5;"><i class="mdi mdi-lead-pencil" style="font-size: 1.4rem; opacity: 0.5;"></i></span>';

            if ($rsDetail && ($rsDetail->fields['g_lockstat'] == 1) && ($GetBaucers->fields('batchNo') <> "")) {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $editLock . '&nbsp;&nbsp;' . $view . '</td>
		';
            } else {
                print '
		<td class="Data" style="text-align: center; vertical-align: middle;" nowrap>' . $cetak . '&nbsp;&nbsp;' . $edit . '&nbsp;&nbsp;' . $view . '</td>
		';
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        print '	</tr>';

        $cnt++;
        $bil++;
        $GetBaucers->MoveNext();
    }
    $GetBaucers->Close();

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
        print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
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
		<td class="textFont">Jumlah Rujukan : <b>' . $GetBaucers->RowCount() . '</b></td>';
} else {
    if ($q == "") {
        print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . ' Bagi Bulan/Tahun - ' . $mm . '/' . $yy . ' -</b><hr size=1"></td></tr>';
    } else {
        print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Sila pilih satu rekod sahaja untuk kemaskini status\');
			} else {
				window.open(\'transStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');					
			}
		}
	}
		
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

	function toggleSearchFields(selectedValue) {
		var akaunDropdown = document.getElementById("akaunDropdown");
		var searchInput = document.getElementById("searchInput");
		if (selectedValue == 4) {
			akaunDropdown.style.display = "inline-block";
			searchInput.style.display = "none";
		} else {
			akaunDropdown.style.display = "none";
			searchInput.style.display = "inline-block";
		}
	}

</script>';