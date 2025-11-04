<?php
/*********************************************************************************
 *           Project         :    iKOOP.com.my
 *           Filename        :    rBalanceSheet.php
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
date_default_timezone_set("Asia/Jakarta");

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> 0) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName  = '?vw=rBalanceSheet&mn='.$mn.'';
$title      = "Kunci Kira-Kira";

// Menangani tarikh dari borang
if (isset($_POST['dtFrom']) && isset($_POST['dtTo'])) {
    $dtFrom = $_POST['dtFrom'];
    $dtTo   = $_POST['dtTo'];
} else {
    $dtFrom = date('Y-m-d');
    $dtTo   = date('Y-m-d');
}

$rptURL     = 'rptACCBS2.php?dtFrom='.$dtFrom.'&dtTo='.$dtTo;

// Query to get account data
$sSQL1 = "";
$sSQL1 = "SELECT * FROM generalacc WHERE category = 'AA' ORDER BY code";		 
$rs1 = &$conn->Execute($sSQL1);

// Initialize arrays to store different types of account data
$accData = array();
$gaData1 = array();//for aset
$gaData2 = array();//for ekuiti
$gaData3 = array();//for liabiliti

// Process each account and populate arrays
while (!$rs1->EOF) {
    $accID = $rs1->fields['ID'];
    $accName = $rs1->fields['name'];
    $accParentID1 = $rs1->fields['parentID'];
    $accParentID2 = dlookup("generalacc","parentID", "ID=" .$rs1->fields['parentID']);

    // Store basic account info for all kod akaun. Can be used to debug
    $accData[] = array(
        'ID' => $accID,
        'name' => $accName,
        'parentID' => $accParentID1,
        'parentID2' => $accParentID2,
    );

    // Set condition for filtering by project or department if applicable
    $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";

    // Define query parameters for different account categories
    $queryParams = array(
        array(
            'condition1' => "AND '".$accParentID2."' IN (8)", // KOD 10000+ bawah ASSET
            'condition2' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => 'gaData1'
        ),
        array(
            'condition1' => "AND '".$accParentID2."' IN (10)", // KOD 30000+ bawah EKUITI
            'condition2' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => 'gaData2'
        ),
        array(
            'condition1' => "AND '".$accParentID2."' IN (12)", // KOD 20000+ bawah LIABILITI
            'condition2' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => 'gaData3'
        )
    );

    // Execute queries for each account type and populate respective arrays
    foreach ($queryParams as $params) {
        $targetArrayName = $params['targetArray'];

        $sSQLb = "SELECT DISTINCT SUM(ta.pymtAmt) AS amaun, ta.MdeductID, ga.name, ga.code, ga.ID as gaID, ga.parentID
        FROM transactionacc ta
        JOIN generalacc ga ON ta.MdeductID = ga.ID
        WHERE ta.MdeductID = '".$accID."' 
        " . (isset($params['condition1']) ? $params['condition1'] : "") . "
        " . (isset($params['condition2']) ? $params['condition2'] : "") . "
        AND (ta.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') 
        GROUP BY ta.MdeductID, ga.name 
        ORDER BY ta.MdeductID ASC";

        $rsb = $conn->Execute($sSQLb);
        $hasData = false;

        // Process results and add to appropriate array if amount is positive (if there is any transaction)
        while (!$rsb->EOF) {
            $amaun = $rsb->fields['amaun'];
            if ($amaun > 0) {
                ${$targetArrayName}[] = array(
                    'ID' => $rsb->fields['gaID'],
                    'gaCode' => $rsb->fields['code'],
                    'gaName' => $rsb->fields['name'],
                    'gaParent' => $rsb->fields['parentID'],
                );
                $hasData = true;
            }
            $rsb->MoveNext();
        }
        // Force include gaID 1121 (akaun untung rugi terkumpul) if missing and belongs to ekuiti
        // keuntungan terkumpul hardcode from parent akaun untung rugi terkumpul
        if ($accID == 1121 && !$hasData && $targetArrayName == 'gaData2') {
            ${$targetArrayName}[] = array(
                'ID' => $accID,
                'gaCode' => $rs1->fields['code'],
                'gaName' => $rs1->fields['name'],
                'gaParent' => $rs1->fields['parentID'],
            );
        }
    }

    $rs1->MoveNext();
}
$rs1->MoveFirst();

// echo '<pre>';
// print_r($gaData1);
// echo '</pre>';

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
    <td>Dari Tanggal</td>
    <td>Sehingga Tanggal</td>
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
';

print '
<div class="table-responsive">';

print'
<table class="exportTable" border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
<td>
<table width="100%" class="table table-striped">
    <tr class="table-primary" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" style="text-align: left;"><b><font size="4">ASET <br /></font></b></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr class="table-primary" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="2">&nbsp;</td>
        <td width="100" style="text-align: right;">SALDO (RP)</td>
    </tr>';

// Initialize variables to track total debit and credit for ASET
$totaldebit=0;
$totalkredit=0;

$totalbalanceAsetBS = 0;  // For the first printed parent
$totalbalanceAsetSemasa = 0;    // For the second printed parent

$firstParentPrinted = false;; // Flag to track if the first parent has been printed

    // Check if there's ASET data to process (kod 11100+)
    if (!empty($gaData1)) {	
        $printedParents = array(); // Array to track printed parents (ABS and AS)

        // Iterate through each ASET account (kod 11100+)
        foreach ($gaData1 as $ga) {
            $ID = $ga['ID'];
            $gaCode = $ga['gaCode'];
            $gaName = $ga['gaName'];
            $gaParent = $ga['gaParent'];
            $gaParentName = dlookup("generalacc","name", "ID=" .$gaParent);
            $gaParentCode = dlookup("generalacc","code", "ID=" .$gaParent);

            // Get debit and credit amounts for the current ID
            $getAmaunD = getAmaunD($ID, $dtFrom, $dtTo);
            $debitA = $getAmaunD->fields['amaun'];	

            $getAmaunK = getAmaunK($ID, $dtFrom, $dtTo);
            $kreditA = $getAmaunK->fields['amaun'];

            // Calculate balance for the current item
            $balanceA = ($debitA - $kreditA);

            // Check if the parent has already been printed (11000 ASET BUKAN SEMASA OR 12000 ASET SEMASA)
            if (!isset($printedParents[$gaParent])) {
                // If the first parent was already printed, print its balance before moving to the next parent
                if ($firstParentPrinted) {

                    // Calculate the balance for 11000 ASET BUKAN SEMASA
                    $debitAset = ($parentDebit + $parentDebitA);
                    $kreditAset = ($parentKredit + $parentKreditA);

                    $totalbalanceAsetBS = ($debitAset- $kreditAset);

                    // Print total balance for 11000 ASET BUKAN SEMASA
                    print '
                    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="2" align="right"><b>Jumlah Keseluruhan Aset Bukan Semasa (RP) &nbsp;</b></td>
                    <td align="right">'.number_format($totalbalanceAsetBS,2).'</td>
                    </tr>';

                    // Reset the parent totals
                    $parentDebit = 0;
                    $parentKredit = 0;
                    $parentDebitA = 0;
                    $parentKreditA = 0;
                
                    $firstParentPrinted = false;  // Reset the flag for the next parent
                }

                // Print the parent code and name (11000 ASET BUKAN SEMASA OR 12000 ASET SEMASA)
                print '
                    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="3"><font size=3><b>&nbsp;<u>' . $gaParentCode . ' - ' . $gaParentName . '</u></b></td>';
                $printedParents[$gaParent] = true;  // Mark this parent as printed
            }

            // Print the current item's code, name, and balance
            print '
                <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                <td colspan="2">&nbsp;&nbsp;&nbsp;<b>'.$gaCode.' - '.$gaName.'</u></b></td>
                <td align="right">&nbsp;'.number_format($balanceA,2).'</td>';
    
            // Fetch child items of the current item (KOD 11101+)
            $sSQLz = "SELECT * FROM generalacc WHERE parentID = '".$ID."' ORDER BY parentID, code";	
            $rsZ = &$conn->Execute($sSQLz);

            // Set condition for filtering by project or department if applicable
            $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";

            // Process each sub-account (kod 11101+) only for those who have transaction (exists in transactionacc table)
            while (!$rsZ->EOF) {
            $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code
                        FROM transactionacc ta
                        JOIN generalacc ga ON ta.deductID = ga.ID
                        WHERE ta.deductID = '".$rsZ->fields['ID']."' 
                        $fieldCondition
                        AND (ta.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') 
                        GROUP BY ta.deductID, ga.name 
                        ORDER BY ta.deductID ASC";
            $rs1a = &$conn->Execute($sSQL1a);
    
            // If transactions exist for this sub-account (kod 11101+), print them
            if ($rs1a->RecordCount() <> 0){
                while (!$rs1a->EOF) {	
                    $deductID = $rs1a->fields['deductID'];
                    $deductName = $rs1a->fields['name'];
                    $deductCode = $rs1a->fields['code'];
    
                    // Get debit and credit amounts for the child item
                    $getAmaunD = getAmaunD($deductID, $dtFrom, $dtTo);
                    $debit = $getAmaunD->fields['amaun'];	
    
                    $getAmaunK = getAmaunK($deductID, $dtFrom, $dtTo);
                    $kredit = $getAmaunK->fields['amaun'];
    
                    $balance = ($debit - $kredit);
    
                    // Print the child item's code, name, and balance
                    print '
                        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                        <td colspan="2">&nbsp;&nbsp;&nbsp;*'.$deductCode.' - '.$deductName.'</td>
                        <td align="right">&nbsp;'.number_format($balance,2).'</td>
                    ';
    
                    $totaldebit += $debit;		
                    $totalkredit += $kredit;
                    $rs1a->MoveNext();	

                    // Update parent totals for later calculation
                    $parentDebit += $debit;
                    $parentKredit += $kredit;
                }
            }

            $rsZ->MoveNext();

            }

            // Totals debit and credit amounts for all ASET account (kod 11101+) under kod 11100+ or (kod 12201+) under kod 12200+
            $totaldebitA += $debitA;
            $totalkreditA += $kreditA;

            // Totals debit and credit amounts for each ASET account (kod 11100+) under 11000 ASET BUKAN SEMASA or (kod 12200+) under 12000 ASET SEMASA
            $parentDebitA += $debitA;
            $parentKreditA += $kreditA;

            // Mark the first parent as printed (11000 ASET BUKAN SEMASA)
            $firstParentPrinted = true;
        }                

        // Calculate the balance for Aset Semasa
        $debitAset = ($parentDebit + $parentDebitA);
        $kreditAset = ($parentKredit + $parentKreditA);
        
        // Print total balance for the last parent Aset Semasa
        $totalbalanceAsetSemasa = ($debitAset - $kreditAset);
        print '
        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="2" align="right"><b>Jumlah Keseluruhan Aset Semasa (RP) &nbsp;</b></td>
        <td align="right">'.number_format($totalbalanceAsetSemasa,2).'</td>
        </tr>';

        // Total up overall in 10000 Aset
        $debitAsetOverall = ($totaldebit + $totaldebitA);
        $kreditAsetOverall = ($totalkredit + $totalkreditA);
        $totalbalanceAsetOverall = ($debitAsetOverall - $kreditAsetOverall);
        print '
        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="2" align="right"><b>Jumlah Keseluruhan Aset (RP) &nbsp;</b></td>
        <td align="right">'.number_format($totalbalanceAsetOverall,2).'</td>
        </tr>';

    } else {
        print '
        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
        </tr>';
    }

print '
</table>
';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '<table width="100%" class="table table-striped">
    <tr class="table-primary" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" style="text-align: left;"><b><font size="4">EKUITI <br /></font></b></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr class="table-primary" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="2">&nbsp;</td>
        <td width="100" style="text-align: right;">SALDO (RP)</td>
    </tr>';

$totaldebit=0;
$totalkredit=0;

    if (!empty($gaData2)) {	
        $printedParents = array();

        foreach ($gaData2 as $ga) {
            $ID = $ga['ID'];
            $gaCode = $ga['gaCode'];
            $gaName = $ga['gaName'];
            $gaParent = $ga['gaParent'];
            $gaParentName = dlookup("generalacc","name", "ID=" .$gaParent);
            $gaParentCode = dlookup("generalacc","code", "ID=" .$gaParent);

            // Check if the parent has already been printed
            if (!isset($printedParents[$gaParent])) {
                print '
                    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="3"><font size=3><b>&nbsp;<u>' . $gaParentCode . ' - ' . $gaParentName . '</u></b></td>';
                $printedParents[$gaParent] = true;  // Mark this parent as printed
            }
    
            print '
                <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                <td colspan="3">&nbsp;&nbsp;&nbsp;<b>'.$gaCode.' - '.$gaName.'</u></b></td>';
    
            $sSQLz = "SELECT * FROM generalacc WHERE parentID = '".$ID."' ORDER BY parentID, code";	
            $rsZ = &$conn->Execute($sSQLz);

            $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";

            while (!$rsZ->EOF) {
            $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code, ga.ID as gaID
                        FROM transactionacc ta
                        JOIN generalacc ga ON ta.deductID = ga.ID
                        WHERE ta.deductID = '".$rsZ->fields['ID']."' 
                        $fieldCondition
                        AND (ta.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') 
                        GROUP BY ta.deductID, ga.name 
                        ORDER BY ta.deductID ASC";
            $rs1a = &$conn->Execute($sSQL1a);
    
            if ($rs1a->RecordCount() <> 0){
                while (!$rs1a->EOF) {	
                    $deductID = $rs1a->fields['deductID'];
                    $deductName = $rs1a->fields['name'];
                    $deductCode = $rs1a->fields['code'];
    
                    $getAmaunD = getAmaunD($deductID, $dtFrom, $dtTo);
                    $debit = $getAmaunD->fields['amaun'];	
    
                    $getAmaunK = getAmaunK($deductID, $dtFrom, $dtTo);
                    $kredit = $getAmaunK->fields['amaun'];
    
                    $balance = ($kredit - $debit);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////keuntungan terkumpul//////////////////////////////////////////////////
                    //ini style pengiraan keuntungan terkumpul dalam kobangi
                    $sSQLK = "";
                    $sSQLK = "SELECT SUM(b.pymtAmt) AS totKU 
                    FROM generalacc a, transactionacc b 
                    WHERE a.ID = b.MdeductID 
                    AND a.a_Kodkump = '36' 
                    AND b.addminus IN (1) 
                    -- AND ta.$field = '".$kod."'
                    AND (b.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."')";		 
                    $rsK = &$conn->Execute($sSQLK);

                    $sSQLD = "";
                    $sSQLD = "SELECT SUM(b.pymtAmt) AS totDU 
                    FROM generalacc a, transactionacc b 
                    WHERE a.ID = b.MdeductID 
                    AND a.a_Kodkump = '36' 
                    AND b.addminus IN (0) 
                    -- AND ta.$field = '".$kod."'
                    AND (b.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."')";		 
                    $rsD = &$conn->Execute($sSQLD);

                    $KETK = $rsK->fields('totKU');
                    $KETD = $rsD->fields('totDU');
                    $TUntungTM = ($KETK -$KETD);

                    $balance1KU =  ($TUntungTM + $kredit);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////keuntungan terkumpul//////////////////////////////////////////////////

                    print '
                        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                        <td colspan="2">&nbsp;&nbsp;&nbsp;*'.$deductCode.' - '.$deductName.'</td>
                        <!-- <td width="0%"  align="right">'.number_format($debit,2).'</td> -->
                        ';
                        //ini style pengiraan keuntungan terkumpul dalam kobangi
                        if ($rs1a->fields['gaID'] == '1140') {
                            // $balance1KU =  ($TUntungTM + $kredit);
                            $balance1KU =  $TUntungTM;
                        }
                        if ($rs1a->fields['gaID'] == '1140') {
                            $balanceL =  ($balance1KU - $debit);
                        print'<td align="right">'.number_format($balanceL,2).'</td>';
                        } else {
                        print'<td align="right">'.number_format($balance,2).'</td>';
                        }
    
                    $totaldebit += $debit;		
                    $totalkredit += $kredit;
                    $totalkreditA1 = ($totalkredit + $TUntungTM);
                    $rs1a->MoveNext();	
                }
            }
            $rsZ->MoveNext();
            }
        }
        
            $totalbalanceEkuiti = ($totalkreditA1 - $totaldebit);
            // $totalbalanceEkuiti = ($totalkredit - $totaldebit);

        print '
            <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="2" align="right"><b>Jumlah Ekuiti (RP) &nbsp;</b></td>
            <td align="right">'.number_format($totalbalanceEkuiti,2).'</td>
            </tr>';
    } else {
        print '
        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
        </tr>';
    }

print '
</table>
';
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '<table width="100%" class="table table-striped">
    <tr class="table-primary" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" style="text-align: left;"><b><font size="4">LIABILITI <br /></font></b></td>
        <td colspan="5">&nbsp;</td>
    </tr>
    <tr class="table-primary" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="5">&nbsp;</td>
        <td width="100" style="text-align: right;">SALDO (RP)</td>
    </tr>';

    $totaldebit=0;
    $totalkredit=0;
    $totalDebitL=0;
    $totalKreditL=0;
    $parentDebit=0;
    $parentKredit=0;

    $totalbalanceLiabilitiBS = 0;  // For the first printed parent
    $totalbalanceLiabilitiSemasa = 0;    // For the second printed parent
    
    $firstParentPrinted = false;; // Flag to track if the first parent has been printed
    
        // Check if there's LIABILITI data to process (kod 21100+)
        if (!empty($gaData3)) {	
            $printedParents = array(); // Array to track printed parents (LBS and lS)
    
            // Iterate through each LIABILITI account (kod 21100+)
            foreach ($gaData3 as $ga) {
                $ID = $ga['ID'];
                $gaCode = $ga['gaCode'];
                $gaName = $ga['gaName'];
                $gaParent = $ga['gaParent'];
                $gaParentName = dlookup("generalacc","name", "ID=" .$gaParent);
                $gaParentCode = dlookup("generalacc","code", "ID=" .$gaParent);
    
                // Get debit and credit amounts for the current ID
                $getAmaunD = getAmaunD($ID, $dtFrom, $dtTo);
                $debitL = $getAmaunD->fields['amaun'];	
    
                $getAmaunK = getAmaunK($ID, $dtFrom, $dtTo);
                $kreditL = $getAmaunK->fields['amaun'];
    
                // Calculate balance for the current item
                $balanceA = ($kreditL - $debitL);
    
                // Check if the parent has already been printed (21000 LIABILITAS JANGKA PANJANG OR 22000 LIABILITAS JANGKA PENDEK)
                if (!isset($printedParents[$gaParent])) {
                    // If the first parent was already printed, print its balance before moving to the next parent
                    if ($firstParentPrinted) {
    
                        // Calculate the balance for 21000 LIABILITAS JANGKA PANJANG
                        $debitLiabiliti = ($parentDebit + $parentdebitL);
                        $kreditLiabiliti = ($parentKredit + $parentKreditL);
    
                        $totalbalanceLiabilitiBS = ($kreditLiabiliti- $debitLiabiliti);
    
                        // Print total balance for 21000 LIABILITAS JANGKA PANJANG
                        print '
                        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                        <td colspan="5" align="right"><b>Jumlah Keseluruhan Liabiliti Bukan Semasa (RP) &nbsp;</b></td>
                        <td align="right">'.number_format($totalbalanceLiabilitiBS,2).'</td>
                        </tr>';
    
                        // Reset the parent totals
                        $parentDebit = 0;
                        $parentKredit = 0;
                        $parentDebitL = 0;
                        $parentKreditL = 0;
                    
                        $firstParentPrinted = false;  // Reset the flag for the next parent
                    }
    
                    // Print the parent code and name (21000 LIABILITAS JANGKA PANJANG OR 22000 LIABILITAS JANGKA PENDEK)
                    print '
                        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                        <td colspan="5"><font size=3><b>&nbsp;<u>' . $gaParentCode . ' - ' . $gaParentName . '</u></b></td>
                        <td>&nbsp;</td>';
                    $printedParents[$gaParent] = true;  // Mark this parent as printed
                }
    
                // Print the current item's code, name, and balance
                print '
                    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="5">&nbsp;&nbsp;&nbsp;<b>'.$gaCode.' - '.$gaName.'</u></b></td>
                    <td align="right">&nbsp;'.number_format($balanceA,2).'</td>';
        
                // Fetch child items of the current item (KOD 21101+)
                $sSQLz = "SELECT * FROM generalacc WHERE parentID = '".$ID."' ORDER BY parentID, code";	
                $rsZ = &$conn->Execute($sSQLz);
    
                // Set condition for filtering by project or department if applicable
                $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";
    
                // Process each sub-account (kod 21101+) only for those who have transaction (exists in transactionacc table)
                while (!$rsZ->EOF) {
                $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code
                            FROM transactionacc ta
                            JOIN generalacc ga ON ta.deductID = ga.ID
                            WHERE ta.deductID = '".$rsZ->fields['ID']."' 
                            $fieldCondition
                            AND (ta.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') 
                            GROUP BY ta.deductID, ga.name 
                            ORDER BY ta.deductID ASC";
                $rs1a = &$conn->Execute($sSQL1a);
        
                // If transactions exist for this sub-account (kod 21101+), print them
                if ($rs1a->RecordCount() <> 0){
                    while (!$rs1a->EOF) {	
                        $deductID = $rs1a->fields['deductID'];
                        $deductName = $rs1a->fields['name'];
                        $deductCode = $rs1a->fields['code'];
        
                        $getAmaunD = getAmaunD($deductID, $dtFrom, $dtTo);
                        $debit = $getAmaunD->fields['amaun'];	
        
                        $getAmaunK = getAmaunK($deductID, $dtFrom, $dtTo);
                        $kredit = $getAmaunK->fields['amaun'];
        
                        $balance = ($kredit - $debit);
        
                        // Print the child item's code, name, and balance
                        print '
                            <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                            <td colspan="5">&nbsp;&nbsp;&nbsp;*'.$deductCode.' - '.$deductName.'</td>
                            <td colspan="1" align="right">&nbsp;'.number_format($balance,2).'</td>
                        ';
        
                        $totaldebit += $debit;		
                        $totalkredit += $kredit;
                        $rs1a->MoveNext();	
                        // Update parent totals for later calculation
                        $parentDebit += $debit;
                        $parentKredit += $kredit;
                    }
                }
    
                $rsZ->MoveNext();
    
                }
    
                // Totals debit and credit amounts for all LIABILITI account (kod 21101+) under kod 21100+ or (kod 22201+) under kod 22200+
                $totalDebitL += $debitL;
                $totalKreditL += $kreditL;
    
                // Totals debit and credit amounts for each LIABILITI account (kod 21100+) under 21000 LIABILITAS JANGKA PANJANG or (kod 22200+) under 22000 LIABILITAS JANGKA PENDEK
                $parentDebitL += $debitL;
                $parentKreditL += $kreditL;
    
                // Mark the first parent as printed (21000 LIABILITAS JANGKA PANJANG)
                $firstParentPrinted = true;
            }
    
            // Calculate the balance for Liabiliti Semasa
            $debitLiabiliti = ($parentDebit + $parentDebitL);
            $kreditLiabiliti = ($parentKredit + $parentKreditL);
            
            // Print total balance for the last parent Liabiliti Semasa
            $totalbalanceLiabilitiSemasa = ($kreditLiabiliti - $debitLiabiliti);
            print '
            <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="5" align="right"><b>Jumlah Keseluruhan Liabiliti Semasa (RP) &nbsp;</b></td>
            <td align="right">'.number_format($totalbalanceLiabilitiSemasa,2).'</td>
            </tr>';
    
            // Total up overall in 20000 Liabiliti
            $debitLiabilitiOverall = ($totaldebit + $totalDebitL);
            $kreditLiabilitiOverall = ($totalkredit + $totalKreditL);
            $totalbalanceLiabilitiOverall = ($kreditLiabilitiOverall - $debitLiabilitiOverall);
            print '
            <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="5" align="right"><b>Jumlah Keseluruhan Liabiliti (RP) &nbsp;</b></td>
            <td align="right">'.number_format($totalbalanceLiabilitiOverall,2).'</td>
            </tr>';
    
        } else {
            print '
            <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
            </tr>';
        }
    
    $total_liaeku 	= ($totalbalanceLiabilitiOverall + $totalbalanceEkuiti);
    $allbalance		= ($totalbalanceAsetOverall - $total_liaeku);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="5" align="right"><b>Jumlah Liabiliti + Ekuiti (RP) &nbsp;</b></td>
		<td align="right">'.number_format($total_liaeku,2).'</td>
		</tr>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="5" align="right"><b>- &nbsp;</b></td>
		<td align="right">'.number_format($allbalance,2).'</td>
		</tr>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
print '
</table>
</td>
</tr>    
';

print ' 
</table>';

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

document.getElementById("downloadExcel").addEventListener("click", function () {  
    setTimeout(function() {
        var outerTable = document.querySelector(".exportTable");
        if (!outerTable || outerTable.innerHTML.trim() === "") return;

        var innerTables = outerTable.querySelectorAll("* table");
        if (innerTables.length === 0) return;

        var wb = XLSX.utils.book_new();
        var allData = [];

        innerTables.forEach(function (innerTable) {
            var rows = innerTable.querySelectorAll("tr");
            if (rows.length === 0) return;

            var tableData = [];
            rows.forEach(function (row) {
                var rowData = [];
                row.querySelectorAll("th, td").forEach(function (cell) {
                    rowData.push(cell.innerText.trim());
                });
                tableData.push(rowData);
            });

            allData = allData.concat(tableData, [[""], [""]]);
        });

        if (allData.length === 0) return;

        var finalSheet = XLSX.utils.aoa_to_sheet(allData);

        // 🔥 Auto-adjust column widths
        finalSheet["!cols"] = allData[0].map((_, colIndex) => ({
            wch: Math.max(...allData.map(row => (row[colIndex] ? row[colIndex].toString().length : 0))) + 2
        }));

        XLSX.utils.book_append_sheet(wb, finalSheet, "Kunci Kira-Kira");

        var dtFrom = "' . $dtFrom . '";
        var dtTo = "' . $dtTo . '";
        var filename = "kunci_kira_" + dtFrom + "_ke_" + dtTo + ".xlsx";
        XLSX.writeFile(wb, filename);
    }, 1000);
});

</script>';