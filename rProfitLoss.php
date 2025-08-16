<?php
/*********************************************************************************
 *           Project         :    iKOOP.com.my
 *           Filename        :    rProfitLoss.php
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

$sFileName  = '?vw=rProfitLoss&mn='.$mn.'';
$title      = "Penyata Untung Rugi";

// Menangani tarikh dari borang
if (isset($_POST['dtFrom']) && isset($_POST['dtTo'])) {
    $dtFrom = $_POST['dtFrom'];
    $dtTo   = $_POST['dtTo'];
} else {
    $dtFrom = date('Y-m-d');
    $dtTo   = date('Y-m-d');
}

$rptURL     = 'rptACCPNL2.php?dtFrom='.$dtFrom.'&dtTo='.$dtTo;

// Query to get account data
$sSQL1 = "";
$sSQL1 = "SELECT * FROM generalacc WHERE category = 'AA' ORDER BY parentID, ID";
$rs1 = $conn->Execute($sSQL1);

// Initialize arrays to store different types of account data
$accData = array();
$gaData1 = array();//for revenue
$gaData2 = array();//for less: cost of sales
$gaData3 = array();//for other income
$gaData4 = array();//for less: expenses

// Process each account and populate arrays
while (!$rs1->EOF) {
    $accID = $rs1->fields['ID'];
    $accName = $rs1->fields['name'];
    $accParentID1 = $rs1->fields['parentID'];

    // Store basic account info for all kod akaun. Can be used to debug
    $accData[] = array(
        'ID' => $accID,
        'name' => $accName,
        'parentID' => $accParentID1,
    );

    // Set condition for filtering by project or department if applicable
    $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";

    // Define query parameters for different account categories
    $queryParams = array(
        array(
            'condition1' => "AND '".$accParentID1."' IN (13)", // KOD 50000+ bawah INCOME
            'condition2' => "AND ga.ID NOT IN (1154)", // kecuali KOD 59000 LAIN LAIN PENDAPATAN
            'condition3' => "AND ta.addminus = 1",
            'condition4' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => &$gaData1
        ),
        array(
            'condition1' => "AND '".$accParentID1."' IN (1172)", // KOD 70000+ & 80000+ bawah EXPENSES
            'condition2' => "AND ga.a_class IN (140)", // KOD CM COST OF GOODS MANUFACTURED aka KOD 61000 KOS BARANG DIJUAL/KOS JUALAN sahaja
            'condition3' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => &$gaData2
        ),
        array(
            'condition1' => "AND '".$accParentID1."' IN (13)", // KOD 50000+ bawah INCOME
            'condition2' => "AND ga.ID IN (1154)", // KOD 59000 LAIN LAIN PENDAPATAN sahaja
            'condition3' => "AND ta.addminus = 1",
            'condition4' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => &$gaData3
        ),
        array(
            'condition1' => "AND '".$accParentID1."' IN (1172)", // KOD 70000+ & 80000+ bawah EXPENSES
            'condition2' => "AND ga.a_class NOT IN (140)", // kecuali KOD CM COST OF GOODS MANUFACTURED aka KOD 61000 KOS BARANG DIJUAL/KOS JUALAN
            'condition3' => $fieldCondition, // Filter by project/department if applicable
            'targetArray' => &$gaData4
        )
    );

    // Execute queries for each account type and populate respective arrays
    foreach ($queryParams as $params) {
        $sSQLb = "SELECT DISTINCT SUM(ta.pymtAmt) AS amaun, ta.MdeductID, ga.name, ga.code, ga.ID as gaID, ga.parentID
        FROM transactionacc ta
        JOIN generalacc ga ON ta.MdeductID = ga.ID
        WHERE ta.MdeductID = '".$accID."' 
        " . (isset($params['condition1']) ? $params['condition1'] : "") . "
        " . (isset($params['condition2']) ? $params['condition2'] : "") . "
        " . (isset($params['condition3']) ? $params['condition3'] : "") . "
        " . (isset($params['condition4']) ? $params['condition4'] : "") . "
        AND (ta.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') 
        GROUP BY ta.MdeductID, ga.name 
        ORDER BY ta.MdeductID ASC";

        $rsb = $conn->Execute($sSQLb);

        // Process results and add to appropriate array if amount is positive (if there is any transaction)
        while (!$rsb->EOF) {
            $amaun = $rsb->fields['amaun'];
            if ($amaun > 0) {
                $params['targetArray'][] = array(
                    'ID' => $rsb->fields['gaID'],
                    'gaCode' => $rsb->fields['code'],
                    'gaName' => $rsb->fields['name'],
                );
            }
            $rsb->MoveNext();
        }
    }

    $rs1->MoveNext();
}

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
';

print '
<div class="table-responsive">';

print'
<table class="exportTable" border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
<td>
<table width="100%" class="table table-striped">
    <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td colspan="3" width="100" style="text-align: left;"><b><font size="4">PENDAPATAN / JUALAN <br /></font></b></td>
    </tr>
    <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="2">&nbsp;</td>
        <td width="100" style="text-align: right;">BAKI (RM)</td>
    </tr>';

// Initialize variables to track total debit and credit for INCOME
$totaldebit1=0;
$totalkredit1=0;

// Check if there's INCOME data to process (kod 50000+)
if (!empty($gaData1)) {	
    // Iterate through each INCOME account (kod 50000+)
    foreach ($gaData1 as $ga) {
        $ID = $ga['ID'];
        $gaCode = $ga['gaCode'];
        $gaName = $ga['gaName'];

        // Print the main account header
        print '
            <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="3"><font size=3><b>&nbsp;<u>'.$gaCode.' - '.$gaName.'</u></b></td>';

        // Query to get sub-accounts (kod 50001+)
        $sSQLz = "SELECT * FROM generalacc WHERE parentID = '".$ID."' ORDER BY parentID, ID";	
        $rsZ = &$conn->Execute($sSQLz);

        // Set condition for filtering by project or department if applicable
        $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";

        // Process each sub-account (kod 50001+)
        while (!$rsZ->EOF) {
        // Query to get transaction details for the sub-account based on certain conditions
        $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code
                    FROM transactionacc ta
                    JOIN generalacc ga ON ta.deductID = ga.ID
                    WHERE ta.deductID = '".$rsZ->fields['ID']."' 
                    $fieldCondition
                    AND (ta.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') 
                    GROUP BY ta.deductID, ga.name 
                    ORDER BY ta.deductID ASC";
        $rs1a = &$conn->Execute($sSQL1a);

        // If transactions exist for this sub-account (kod 50001+)
        if ($rs1a->RecordCount() <> 0){
            while (!$rs1a->EOF) {	
                $deductID = $rs1a->fields['deductID'];
                $deductName = $rs1a->fields['name'];
                $deductCode = $rs1a->fields['code'];

                // Get debit and credit amounts
                $getAmaunD = getAmaunD($deductID, $dtFrom, $dtTo);
                $debit = $getAmaunD->fields['amaun'];	

                $getAmaunK = getAmaunK($deductID, $dtFrom, $dtTo);
                $kredit = $getAmaunK->fields['amaun'];

                // Calculate balance (for INCOME, credit - debit)
                $balance1 = ($kredit - $debit);

                // Print sub-account code, name, and balance
                print '
                    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="2">&nbsp;&nbsp;&nbsp;*'.$deductCode.' - '.$deductName.'</td>
                    <td colspan="1" align="right">&nbsp;'.number_format($balance1,2).'</td>
                ';

                // Update total debit and credit for everything under INCOME
                $totaldebit1 += $debit;		
                $totalkredit1 += $kredit;
                $rs1a->MoveNext();	
            }
        }
        $rsZ->MoveNext();
        }
    }
    
    // Calculate and print total balance for INCOME
    $totalbalanceA = ($totalkredit1 - $totaldebit1);
    print '
        <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="2" align="right"><b>Jumlah Keseluruhan (RM) &nbsp;</b></td>
        <td align="right">'.number_format($totalbalanceA,2).'</td>
        </tr>';
} else {
    print '
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
    <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
    </tr>';
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '<table width="100%" class="table table-striped">
    <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" colspan="3" style="text-align: left;"><b><font size="4">(-) KOS BARANG DIJUAL <br /></font></b></td>
    </tr>
    <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="2">&nbsp;</td>
        <td width="100" style="text-align: right;">BAKI (RM)</td>
    </tr>';

// Initialize variables to track total debit and credit for COST OF SALES
$totaldebit2=0;
$totalkredit2=0;

// Check if there's COST OF SALES data to process (kod 61000 KOS BARANG DIJUAL/KOS JUALAN sahaja)
if (!empty($gaData2)) {	
    // Iterate through each COST OF SALES account (kod 61000)
    foreach ($gaData2 as $ga) {
        $ID = $ga['ID'];
        $gaCode = $ga['gaCode'];
        $gaName = $ga['gaName'];

        // Print the main account header
        print '
            <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="3"><font size=3><b>&nbsp;<u>'.$gaCode.' - '.$gaName.'</u></b></td>';

        // Query to get sub-accounts (kod 61001+)
        $sSQLz = "SELECT * FROM generalacc WHERE parentID = '".$ID."' ORDER BY parentID, ID";	
        $rsZ = &$conn->Execute($sSQLz);

        // Set condition for filtering by project or department if applicable
        $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";

        // Process each sub-account (kod 61001+)
        while (!$rsZ->EOF) {
        // Query to get transaction details for the sub-account based on certain conditions
        $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code
                    FROM transactionacc ta
                    JOIN generalacc ga ON ta.deductID = ga.ID
                    WHERE ta.deductID = '".$rsZ->fields['ID']."'
                    $fieldCondition
                    AND (ta.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') 
                    GROUP BY ta.deductID, ga.name 
                    ORDER BY ta.deductID ASC";
        $rs1a = &$conn->Execute($sSQL1a);

        // If transactions exist for this sub-account (kod 61001+)
        if ($rs1a->RecordCount() <> 0){
            while (!$rs1a->EOF) {	
                $deductID = $rs1a->fields['deductID'];
                $deductName = $rs1a->fields['name'];
                $deductCode = $rs1a->fields['code'];

                // Get debit and credit amounts
                $getAmaunD = getAmaunD($deductID, $dtFrom, $dtTo);
                $debit = $getAmaunD->fields['amaun'];	

                $getAmaunK = getAmaunK($deductID, $dtFrom, $dtTo);
                $kredit = $getAmaunK->fields['amaun'];

                // Calculate balance (for COST OF SALES, debit - credit)
                $balance1 = ($debit - $kredit);

                // Print sub-account code, name, and balance
                print '
                    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="2">&nbsp;&nbsp;&nbsp;*'.$deductCode.' - '.$deductName.'</td>
                    <td colspan="1" align="right">&nbsp;'.number_format($balance1,2).'</td>
                ';

                // Update total debit and credit for everything under COST OF SALES
                $totaldebit2 += $debit;		
                $totalkredit2 += $kredit;
                $rs1a->MoveNext();	
            }
        }
        $rsZ->MoveNext();
        }
    }
    
    // Calculate and print total balance for COST OF SALES
    $totalbalanceB = ($totaldebit2 - $totalkredit2);
    print '
        <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="2" align="right"><b>Jumlah Keseluruhan (RM) &nbsp;</b></td>
        <td align="right">'.number_format($totalbalanceB,2).'</td>
        </tr>';
} else {
    print '
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
    <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
    </tr>';
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '</table>
	
	<tr>
        <td></td>
	</tr>

    <table width="100%" class="table table-striped">
        <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
            <td colspan="2" align="right">&nbsp;</td>
            <td width="100" align="right">BAKI (RM)</td>
        </tr>';
        
        // Calculate and print Gross Profit (INCOME minus COST OF SALES) 
        $totalbalanceC = ($totalbalanceA - $totalbalanceB);

        print'
        <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="#FFFFFF">
            <td colspan="2" align="right"><b>Jumlah Pendapatan Kasar (Gross Profit) (RM) &nbsp;</b></td>
            <td colspan="1" align="right">'.number_format($totalbalanceC,2).'</td>
        </tr>';

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '</table>

    <table width="100%" class="table table-striped">
    <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" colspan="3" style="text-align: left;"><b><font size="4">(+) PENDAPATAN LAIN <br /></font></b></td>
    </tr>
    <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="2">&nbsp;</td>
        <td width="100" style="text-align: right;">BAKI (RM)</td>
    </tr>';

// Initialize variables to track total debit and credit for OTHER INCOME
$totaldebit3=0;
$totalkredit3=0;

// Check if there's OTHER INCOME data to process (kod 59000 LAIN LAIN PENDAPATAN sahaja)
if (!empty($gaData3)) {	
    foreach ($gaData3 as $ga) {
        $ID = $ga['ID'];
        $gaCode = $ga['gaCode'];
        $gaName = $ga['gaName'];

        // Print the main account header
        print '
            <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="3"><font size=3><b>&nbsp;<u>'.$gaCode.' - '.$gaName.'</u></b></td>';

        // Query to get sub-accounts (kod 59001+)
        $sSQLz = "SELECT * FROM generalacc WHERE parentID = '".$ID."' ORDER BY parentID, ID";	
        $rsZ = &$conn->Execute($sSQLz);

        // Set condition for filtering by project or department if applicable
        $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";

        // Process each sub-account (kod 59001+)
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

        // If transactions exist for this sub-account (kod 59001+)
        if ($rs1a->RecordCount() <> 0){
            while (!$rs1a->EOF) {	
                $deductID = $rs1a->fields['deductID'];
                $deductName = $rs1a->fields['name'];
                $deductCode = $rs1a->fields['code'];

                // Get debit and credit amounts
                $getAmaunD = getAmaunD($deductID, $dtFrom, $dtTo);
                $debit = $getAmaunD->fields['amaun'];	

                $getAmaunK = getAmaunK($deductID, $dtFrom, $dtTo);
                $kredit = $getAmaunK->fields['amaun'];

                // Calculate balance (for OTHER INCOME, credit - debit)
                $balance1 = ($kredit - $debit);

                // Print sub-account code, name, and balance
                print '
                    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="2">&nbsp;&nbsp;&nbsp;*'.$deductCode.' - '.$deductName.'</td>
                    <td colspan="1" align="right">&nbsp;'.number_format($balance1,2).'</td>
                ';

                // Update total debit and credit for everything under OTHER INCOME
                $totaldebit3 += $debit;		
                $totalkredit3 += $kredit;
                $rs1a->MoveNext();	
            }
        }
        $rsZ->MoveNext();
        }
    }
    
    // Calculate and print total balance for OTHER INCOME
    $totalbalanceD = ($totalkredit3 - $totaldebit3);
    print '
        <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="2" align="right"><b>Jumlah Keseluruhan (RM) &nbsp;</b></td>
        <td align="right">'.number_format($totalbalanceD,2).'</td>
        </tr>';
} else {
    print '
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
    <td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
    </tr>';
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '</table>

	<tr>
        <td></td>
	</tr>

    <table width="100%" class="table table-striped">
        <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
            <td colspan="2" align="right">&nbsp;</td>
            <td width="100" align="right">BAKI (RM)</td>
        </tr>';
        
        // Calculate and print Total Gross Profit (GROSS PROFIT plus OTHER INCOME) 
        $totalbalanceE = ($totalbalanceC + $totalbalanceD);

        print'
        <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="#FFFFFF">
            <td colspan="2" align="right"><b>Jumlah Keseluruhan Pendapatan Kasar (Total Gross Profit) (RM) &nbsp;</b></td>
            <td colspan="1" align="right">'.number_format($totalbalanceE,2).'</td>
        </tr>';

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '</table>

    <table width="100%" class="table table-striped">
    <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold; text-align: left;">
        <td width="100" colspan="3" style="text-align: left;"><b><font size="4">(-) PERBELANJAAN <br /></font></b></td>
    </tr>
    <tr class="table-primary" style="font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: right;">
        <td colspan="2">&nbsp;</td>
        <td width="100" style="text-align: right;">BAKI (RM)</td>
    </tr>';

// Initialize variables to track total debit and credit for EXPENSES
$totaldebit4=0;
$totalkredit4=0;

// Check if there's EXPENSES data to process (kod 70000+ & 80000+)
if (!empty($gaData4)) {	
    // Iterate through each EXPENSES account (kod 70000+ & 80000+)
    foreach ($gaData4 as $ga) {
        $ID = $ga['ID'];
        $gaCode = $ga['gaCode'];
        $gaName = $ga['gaName'];

        // Print the main account header
        print '
            <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td colspan="3"><font size=3><b>&nbsp;<u>'.$gaCode.' - '.$gaName.'</u></b></td>';

        // Query to get sub-accounts (kod 70001+ & 80001+)
        $sSQLz = "SELECT * FROM generalacc WHERE parentID = '".$ID."' ORDER BY parentID, ID";	
        $rsZ = &$conn->Execute($sSQLz);

        // Set condition for filtering by project or department if applicable
        $fieldCondition = (isset($field) && isset($kod)) ? "AND ta.$field = '".$kod."'" : "";

        // Process each sub-account (kod 70001+ & 80001+)
        while (!$rsZ->EOF) {
        // Query to get transaction details for the sub-account based on certain conditions
        $sSQL1a = "SELECT DISTINCT ta.deductID, ga.name, ga.code
                    FROM transactionacc ta
                    JOIN generalacc ga ON ta.deductID = ga.ID
                    WHERE ta.deductID = '".$rsZ->fields['ID']."'
                    $fieldCondition
                    AND (ta.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') 
                    GROUP BY ta.deductID, ga.name 
                    ORDER BY ta.deductID ASC";
        $rs1a = &$conn->Execute($sSQL1a);

        // If transactions exist for this sub-account (kod 70001+ & 80001+)
        if ($rs1a->RecordCount() <> 0){
            while (!$rs1a->EOF) {	
                $deductID = $rs1a->fields['deductID'];
                $deductName = $rs1a->fields['name'];
                $deductCode = $rs1a->fields['code'];

                // Get debit and credit amounts
                $getAmaunD = getAmaunD($deductID, $dtFrom, $dtTo);
                $debit = $getAmaunD->fields['amaun'];	

                $getAmaunK = getAmaunK($deductID, $dtFrom, $dtTo);
                $kredit = $getAmaunK->fields['amaun'];

                // Calculate balance (for EXPENSES, debit - credit)
                $balance1 = ($debit - $kredit);

                // Print sub-account code, name, and balance
                print '
                    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="2">&nbsp;&nbsp;&nbsp;*'.$deductCode.' - '.$deductName.'</td>
                    <td colspan="1" align="right">&nbsp;'.number_format($balance1,2).'</td>
                ';

                // Update total debit and credit for everything under EXPENSES
                $totaldebit4 += $debit;		
                $totalkredit4 += $kredit;
                $rs1a->MoveNext();	
            }
        }
        $rsZ->MoveNext();
        }
    }
    
    // Calculate and print total balance for EXPENSES
    $totalbalanceF = ($totaldebit4 - $totalkredit4);
    print '
        <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="2" align="right"><b>Jumlah Keseluruhan (RM) &nbsp;</b></td>
        <td align="right">'.number_format($totalbalanceF,2).'</td>
        </tr>';

// Calculate and print Net Profit (TOTAL GROSS PROFIT minus EXPENSES) 
$allbalance		= ($totalbalanceE - $totalbalanceF);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		print '
		</tr>
		<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="2" align="right"><b>Jumlah Bersih Keuntungan (Net Profit) (RM) &nbsp;</b></td>
		<td align="right">'.number_format($allbalance,2).'</td>
		</tr>';
	
	} else {
	print '
		<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
		</tr>';
}
print '</table>
<tr><td colspan="5">&nbsp;</td></tr>
<center><tr><td colspan="5"><font size="1" color="#999999"><b>'.$retooFetis.'</b></font></td></tr></center>
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

        XLSX.utils.book_append_sheet(wb, finalSheet, "Penyata Untung Rugi");

        var dtFrom = "' . $dtFrom . '";
        var dtTo = "' . $dtTo . '";
        var filename = "penyata_untung_rugi_" + dtFrom + "_ke_" + dtTo + ".xlsx";
        XLSX.writeFile(wb, filename);
    }, 1000);
});

</script>';