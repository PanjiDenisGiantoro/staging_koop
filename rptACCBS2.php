<?php

/*********************************************************************************
 *   Project	    : iKOOP.com.my
 *   Filename       : rptACCBS2 - excluded kod akaun that don't have transactions
 *	 Date 	        : 22/7/2024
 *   Description    : This script generates a detailed Balance Sheet report for a 
 *                 specified date range, categorizing into aset, 
 *                 ekuiti, and liabiliti
 *********************************************************************************/
session_start();
include("common.php");
include("AccountQry.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
$today      = date("F j, Y, g:i a");
$yearFrom   = date("Y", strtotime($dtFrom));
$monthFrom  = date("m", strtotime($dtFrom));
$yearMinus  = $yearFrom - 1;

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
if (get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$title  = 'KUNCI KIRA-KIRA DARI ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '';
$title    = strtoupper($title);

// Set a subtitle based on whether it's a project or department report. Otherwise, not applicable
$text = ($field == "kod_project") ? 'PROJEK : ' : (($field == "kod_jabatan") ? 'JABATAN : ' : '');
$title2  = $text . dlookup('generalacc', 'name', 'ID=' . $kod) . '';
$title2    = strtoupper($title2);

// Query to get account data
$sSQL1 = "";
$sSQL1 = "SELECT * FROM generalacc WHERE category = 'AA' ORDER BY code";
$rs1 = &$conn->Execute($sSQL1);

// Initialize arrays to store different types of account data
$accData = array();
$gaData1 = array(); //for aset
$gaData2 = array(); //for ekuiti
$gaData3 = array(); //for liabiliti

// Process each account and populate arrays
while (!$rs1->EOF) {
    $accID = $rs1->fields['ID'];
    $accName = $rs1->fields['name'];
    $accParentID1 = $rs1->fields['parentID'];
    $accParentID2 = dlookup("generalacc", "parentID", "ID=" . $rs1->fields['parentID']);

    // Store basic account info for all kod akaun. Can be used to debug
    $accData[] = array(
        'ID' => $accID,
        'name' => $accName,
        'parentID' => $accParentID1,
        'parentID2' => $accParentID2,
    );

    // Set condition for filtering by project or department if applicable
    $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '" . $kod . "'" : "";

    // Define query parameters for different account categories
    $queryParams = array(
        array(
            'condition1' => "AND '" . $accParentID2 . "' IN (8)", // KOD 10000+ bawah ASSET
            'condition2' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => 'gaData1'
        ),
        array(
            'condition1' => "AND '" . $accParentID2 . "' IN (10)", // KOD 30000+ bawah EKUITI
            'condition2' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => 'gaData2'
        ),
        array(
            'condition1' => "AND '" . $accParentID2 . "' IN (12)", // KOD 20000+ bawah LIABILITI
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
        WHERE ta.MdeductID = '" . $accID . "' 
        " . (isset($params['condition1']) ? $params['condition1'] : "") . "
        " . (isset($params['condition2']) ? $params['condition2'] : "") . "
        AND (ta.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') 
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
// print_r($accData);
// echo '</pre>';

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="en">
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';

print '
<table width="100%" class="table table-sm table-striped">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="5" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: center;">
		<th colspan="9 height="40">' . $title . '<br />' . $title2 . '</font></th>
	</tr>
	    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
        <td colspan="8" align="left"><font size=1>CETAK PADA : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
    </tr>
</table>
    <tr><td colspan="9">&nbsp;</td></tr>

<table width="100%" class="table table-striped">
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td colspan="3" width="100" style="text-align: right;"><b><font size="4">'.$monthFrom.'/'.$yearFrom.'<br /></font></b></td>
    </tr>
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" style="text-align: left;"><b><font size="4">ASET <br /></font></b></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="2">&nbsp;</td>
        <td width="100" style="text-align: right;">BAKI (RM)</td>
    </tr>';

// Initialize variables to track total debit and credit for ASET
$totaldebit = 0;
$totalkredit = 0;

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
        $gaParentName = dlookup("generalacc", "name", "ID=" . $gaParent);
        $gaParentCode = dlookup("generalacc", "code", "ID=" . $gaParent);

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

                $totalbalanceAsetBS = ($debitAset - $kreditAset);

                // Print total balance for 11000 ASET BUKAN SEMASA
                print '
                    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="2" align="right"><b>Jumlah Keseluruhan Aset Bukan Semasa (RM) &nbsp;</b></td>
                    <td align="right">' . number_format($totalbalanceAsetBS, 2) . '</td>
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
                <td colspan="2">&nbsp;&nbsp;&nbsp;<b>' . $gaCode . ' - ' . $gaName . '</u></b></td>
                <td align="right">&nbsp;' . number_format($balanceA, 2) . '</td>';

        // Fetch child items of the current item (KOD 11101+)
        $sSQLz = "SELECT * FROM generalacc WHERE parentID = '" . $ID . "' ORDER BY parentID, code";
        $rsZ = &$conn->Execute($sSQLz);

        // Set condition for filtering by project or department if applicable
        $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '" . $kod . "'" : "";

        // Process each sub-account (kod 11101+) only for those who have transaction (exists in transactionacc table)
        while (!$rsZ->EOF) {
            $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code
                        FROM transactionacc ta
                        JOIN generalacc ga ON ta.deductID = ga.ID
                        WHERE ta.deductID = '" . $rsZ->fields['ID'] . "' 
                        $fieldCondition
                        AND (ta.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') 
                        GROUP BY ta.deductID, ga.name 
                        ORDER BY ta.deductID ASC";
            $rs1a = &$conn->Execute($sSQL1a);

            // If transactions exist for this sub-account (kod 11101+), print them
            if ($rs1a->RecordCount() <> 0) {
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
                        <td colspan="2">&nbsp;&nbsp;&nbsp;*' . $deductCode . ' - ' . $deductName . '</td>
                        <td align="right">&nbsp;' . number_format($balance, 2) . '</td>
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
        <td colspan="2" align="right"><b>Jumlah Keseluruhan Aset Semasa (RM) &nbsp;</b></td>
        <td align="right">' . number_format($totalbalanceAsetSemasa, 2) . '</td>
        </tr>';

    // Total up overall in 10000 Aset
    $debitAsetOverall = ($totaldebit + $totaldebitA);
    $kreditAsetOverall = ($totalkredit + $totalkreditA);
    $totalbalanceAsetOverall = ($debitAsetOverall - $kreditAsetOverall);
    print '
        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="2" align="right"><b>Jumlah Keseluruhan Aset (RM) &nbsp;</b></td>
        <td align="right">' . number_format($totalbalanceAsetOverall, 2) . '</td>
        </tr>';
} else {
    print '
        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
        </tr>';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '<table width="100%" class="table table-striped">
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" style="text-align: left;"><b><font size="4">EKUITI <br /></font></b></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="2">&nbsp;</td>
        <td width="100" style="text-align: right;">BAKI (RM)</td>
    </tr>';

$totaldebit = 0;
$totalkredit = 0;

if (!empty($gaData2)) {
    $printedParents = array();

    foreach ($gaData2 as $ga) {
        $ID = $ga['ID'];
        $gaCode = $ga['gaCode'];
        $gaName = $ga['gaName'];
        $gaParent = $ga['gaParent'];
        $gaParentName = dlookup("generalacc", "name", "ID=" . $gaParent);
        $gaParentCode = dlookup("generalacc", "code", "ID=" . $gaParent);

        // Check if the parent has already been printed
        if (!isset($printedParents[$gaParent])) {
            print '
                    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="3"><font size=3><b>&nbsp;<u>' . $gaParentCode . ' - ' . $gaParentName . '</u></b></td>';
            $printedParents[$gaParent] = true;  // Mark this parent as printed
        }

        print '
                <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                <td colspan="3">&nbsp;&nbsp;&nbsp;<b>' . $gaCode . ' - ' . $gaName . '</u></b></td>';

        $sSQLz = "SELECT * FROM generalacc WHERE parentID = '" . $ID . "' ORDER BY parentID, code";
        $rsZ = &$conn->Execute($sSQLz);

        $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '" . $kod . "'" : "";

        while (!$rsZ->EOF) {
            $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code, ga.ID as gaID
                        FROM transactionacc ta
                        JOIN generalacc ga ON ta.deductID = ga.ID
                        WHERE ta.deductID = '" . $rsZ->fields['ID'] . "' 
                        $fieldCondition
                        AND (ta.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') 
                        GROUP BY ta.deductID, ga.name 
                        ORDER BY ta.deductID ASC";
            $rs1a = &$conn->Execute($sSQL1a);

            if ($rs1a->RecordCount() <> 0) {
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
                    -- AND ta.$field = '" . $kod . "'
                    AND (b.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')";
                    $rsK = &$conn->Execute($sSQLK);

                    $sSQLD = "";
                    $sSQLD = "SELECT SUM(b.pymtAmt) AS totDU 
                    FROM generalacc a, transactionacc b 
                    WHERE a.ID = b.MdeductID 
                    AND a.a_Kodkump = '36' 
                    AND b.addminus IN (0) 
                    -- AND ta.$field = '" . $kod . "'
                    AND (b.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')";
                    $rsD = &$conn->Execute($sSQLD);

                    $KETK = $rsK->fields('totKU');
                    $KETD = $rsD->fields('totDU');
                    $TUntungTM = ($KETK - $KETD);

                    $balance1KU =  ($TUntungTM + $kredit);
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////keuntungan terkumpul//////////////////////////////////////////////////

                    print '
                        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                        <td colspan="2">&nbsp;&nbsp;&nbsp;*' . $deductCode . ' - ' . $deductName . '</td>
                        <!-- <td width="0%"  align="right">' . number_format($debit, 2) . '</td> -->
                        ';
                    //ini style pengiraan keuntungan terkumpul dalam kobangi
                    if ($rs1a->fields['gaID'] == '1140') {
                        // $balance1KU =  ($TUntungTM + $kredit);
                        $balance1KU =  $TUntungTM;
                    }
                    if ($rs1a->fields['gaID'] == '1140') {
                        $balanceL =  ($balance1KU - $debit);
                        print '<td align="right">' . number_format($balanceL, 2) . '</td>';
                    } else {
                        print '<td align="right">' . number_format($balance, 2) . '</td>';
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
            <td colspan="2" align="right"><b>Jumlah Ekuiti (RM) &nbsp;</b></td>
            <td align="right">' . number_format($totalbalanceEkuiti, 2) . '</td>
            </tr>';
} else {
    print '
        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
        </tr>';
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '<table width="100%" class="table table-striped">
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" style="text-align: left;"><b><font size="4">LIABILITI <br /></font></b></td>
        <td colspan="5">&nbsp;</td>
    </tr>
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="5">&nbsp;</td>
        <td width="100" style="text-align: right;">BAKI (RM)</td>
    </tr>';

$totaldebit = 0;
$totalkredit = 0;
$totalDebitL = 0;
$totalKreditL = 0;
$parentDebit = 0;
$parentKredit = 0;

$totalbalanceLiabilitiBS = 0;  // For the first printed parent
$totalbalanceLiabilitiSemasa = 0;    // For the second printed parent

$firstParentPrinted = false;; // Flag to track if the first parent has been printed

// Check if there's LIABILITI data to process (kod 21100+)
if (!empty($gaData3)) {
    $printedParents = array(); // Array to track printed parents (LBS and LS)

    // Iterate through each LIABILITI account (kod 21100+)
    foreach ($gaData3 as $ga) {
        $ID = $ga['ID'];
        $gaCode = $ga['gaCode'];
        $gaName = $ga['gaName'];
        $gaParent = $ga['gaParent'];
        $gaParentName = dlookup("generalacc", "name", "ID=" . $gaParent);
        $gaParentCode = dlookup("generalacc", "code", "ID=" . $gaParent);

        // Get debit and credit amounts for the current ID
        $getAmaunD = getAmaunD($ID, $dtFrom, $dtTo);
        $debitL = $getAmaunD->fields['amaun'];

        $getAmaunK = getAmaunK($ID, $dtFrom, $dtTo);
        $kreditL = $getAmaunK->fields['amaun'];

        // Calculate balance for the current item
        $balanceA = ($kreditL - $debitL);

        // Check if the parent has already been printed (21000 LIABILITI BUKAN SEMASA OR 22000 LIABILITI SEMASA)
        if (!isset($printedParents[$gaParent])) {
            // If the first parent was already printed, print its balance before moving to the next parent
            if ($firstParentPrinted) {

                // Calculate the balance for 21000 LIABILITI BUKAN SEMASA
                $debitLiabiliti = ($parentDebit + $parentdebitL);
                $kreditLiabiliti = ($parentKredit + $parentKreditL);

                $totalbalanceLiabilitiBS = ($kreditLiabiliti - $debitLiabiliti);

                // Print total balance for 21000 LIABILITI BUKAN SEMASA
                print '
                        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                        <td colspan="5" align="right"><b>Jumlah Keseluruhan Liabiliti Bukan Semasa (RM) &nbsp;</b></td>
                        <td align="right">' . number_format($totalbalanceLiabilitiBS, 2) . '</td>
                        </tr>';

                // Reset the parent totals
                $parentDebit = 0;
                $parentKredit = 0;
                $parentDebitL = 0;
                $parentKreditL = 0;

                $firstParentPrinted = false;  // Reset the flag for the next parent
            }

            // Print the parent code and name (21000 LIABILITI BUKAN SEMASA OR 22000 LIABILITI SEMASA)
            print '
                        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                        <td colspan="5"><font size=3><b>&nbsp;<u>' . $gaParentCode . ' - ' . $gaParentName . '</u></b></td>
                        <td>&nbsp;</td>';
            $printedParents[$gaParent] = true;  // Mark this parent as printed
        }

        // Print the current item's code, name, and balance
        print '
                    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="5">&nbsp;&nbsp;&nbsp;<b>' . $gaCode . ' - ' . $gaName . '</u></b></td>
                    <td align="right">&nbsp;' . number_format($balanceA, 2) . '</td>';

        // Fetch child items of the current item (KOD 21101+)
        $sSQLz = "SELECT * FROM generalacc WHERE parentID = '" . $ID . "' ORDER BY parentID, code";
        $rsZ = &$conn->Execute($sSQLz);

        // Set condition for filtering by project or department if applicable
        $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '" . $kod . "'" : "";

        // Process each sub-account (kod 21101+) only for those who have transaction (exists in transactionacc table)
        while (!$rsZ->EOF) {
            $rsZ->fields['ID'];
            $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code
                            FROM transactionacc ta
                            JOIN generalacc ga ON ta.deductID = ga.ID
                            WHERE ta.deductID = '" . $rsZ->fields['ID'] . "' 
                            $fieldCondition
                            AND (ta.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') 
                            GROUP BY ta.deductID, ga.name 
                            ORDER BY ta.deductID ASC";
            $rs1a = &$conn->Execute($sSQL1a);

            // If transactions exist for this sub-account (kod 21101+), print them
            if ($rs1a->RecordCount() <> 0) {
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
                            <td colspan="5">&nbsp;&nbsp;&nbsp;*' . $deductCode . ' - ' . $deductName . '</td>
                            <td colspan="1" align="right">&nbsp;' . number_format($balance, 2) . '</td>
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

        // Totals debit and credit amounts for each LIABILITI account (kod 21100+) under 21000 LIABILITI BUKAN SEMASA or (kod 22200+) under 22000 LIABILITI SEMASA
        $parentDebitL += $debitL;
        $parentKreditL += $kreditL;

        // Mark the first parent as printed (21000 LIABILITI BUKAN SEMASA)
        $firstParentPrinted = true;
    }

    // Calculate the balance for Liabiliti Semasa
    $debitLiabiliti = ($parentDebit + $parentDebitL);
    $kreditLiabiliti = ($parentKredit + $parentKreditL);

    // Print total balance for the last parent Liabiliti Semasa
    $totalbalanceLiabilitiSemasa = ($kreditLiabiliti - $debitLiabiliti);
    print '
            <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="5" align="right"><b>Jumlah Keseluruhan Liabiliti Semasa (RM) &nbsp;</b></td>
            <td align="right">' . number_format($totalbalanceLiabilitiSemasa, 2) . '</td>
            </tr>';

    // Total up overall in 20000 Liabiliti
    $debitLiabilitiOverall = ($totaldebit + $totalDebitL);
    $kreditLiabilitiOverall = ($totalkredit + $totalKreditL);
    $totalbalanceLiabilitiOverall = ($kreditLiabilitiOverall - $debitLiabilitiOverall);
    print '
            <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="5" align="right"><b>Jumlah Keseluruhan Liabiliti (RM) &nbsp;</b></td>
            <td align="right">' . number_format($totalbalanceLiabilitiOverall, 2) . '</td>
            </tr>';
} else {
    print '
            <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
            </tr>';
}

$total_liaeku     = ($totalbalanceLiabilitiOverall + $totalbalanceEkuiti);
$allbalance        = ($totalbalanceAsetOverall - $total_liaeku);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="5" align="right"><b>Jumlah Liabiliti + Ekuiti (RM) &nbsp;</b></td>
		<td align="right">' . number_format($total_liaeku, 2) . '</td>
		</tr>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="5" align="right"><b>- &nbsp;</b></td>
		<td align="right">' . number_format($allbalance, 2) . '</td>
		</tr>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

print '</table>
</td></tr>
<tr><td colspan="5">&nbsp;</td></tr>
<center><tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>
</table>
</body>
</html>';