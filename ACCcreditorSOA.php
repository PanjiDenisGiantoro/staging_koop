<?php
/*********************************************************************************
*          Project		:	  iKOOP.com.my
*          Filename		: 	  ACCcreditorSOA.php
*          Date 		: 	  01/06/2024
*********************************************************************************/
session_start();
include("common.php");
date_default_timezone_set("Asia/Kuala Lumpur");
$today = date("F j, Y, g:i a");

$ssSQL  = "SELECT name, address1, address2, address3, address4, noPhone, email, koperasiID FROM setup
            WHERE setupID = 1";
$rss    = &$conn->Execute($ssSQL);

$coopName = $rss->fields('name');
$address1 = $rss->fields('address1');
$address2 = $rss->fields('address2');
$address3 = $rss->fields('address3');
$address4 = $rss->fields('address4');
$noPhone  = $rss->fields('noPhone');
$email    = $rss->fields('email');
$koperasiID = $rss->fields('koperasiID');

if (get_session("Cookie_groupID") == "" OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");
  window.close();
  </script>';
	exit;
}

$sSQL1      = "SELECT ID, name FROM generalacc WHERE category = 'AB'";
$rsCompany  = $conn->Execute($sSQL1);

$companyData    = array();
$allDates       = array();

while (!$rsCompany->EOF) {
    $companyID      = $rsCompany->fields['ID'];
    $companyName    = $rsCompany->fields['name'];

// $sSQL   = "select * from transactionacc
//             WHERE pymtRefer = " . tosql($companyID, "Text") . "
//             AND YEAR(tarikh_doc) = ".$yr."
//             ORDER BY tarikh_doc";

//kalau takda pymtrefer
$sSQL = "
    SELECT 
        a.*, 
        b.PINo AS docNo, 
        b.companyID AS companyID, 
        'purchaseinv' AS source
    FROM transactionacc a
    LEFT JOIN cb_purchaseinv b ON a.docNo = b.PINo
    WHERE b.companyID = $companyID
    AND a.addminus = 1
      AND YEAR(a.tarikh_doc) = $yr

    UNION ALL

    SELECT 
        a.*, 
        b.no_bill AS docNo, 
        b.diterima_drpd AS companyID, 
        'billacc' AS source
    FROM transactionacc a
    LEFT JOIN billacc b ON a.docNo = b.no_bill
    WHERE b.diterima_drpd = $companyID
    AND a.addminus = 0
      AND YEAR(a.tarikh_doc) = $yr

    ORDER BY tarikh_doc ASC
";

$rs     = $conn->Execute($sSQL);

// if ($rs === false) {
//     echo "SQL Error: " . $conn->ErrorMsg();
// } elseif ($rs->EOF) {
//     echo "No records found.";
// } else {
//     echo "<pre>";
//     while (!$rs->EOF) {
//         print_r($rs->fields);
//         echo "-------------------------\n";
//         $rs->MoveNext();
//     }
//     echo "</pre>";
// }



$getYuranOpen   = "SELECT 
                    SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
                    SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
                    FROM transactionacc
                    WHERE
                    pymtRefer = '".$companyID."' 
                    GROUP BY pymtRefer";
$rsYuranOpen    = $conn->Execute($getYuranOpen);

//kira beginning balance tahun sebelum
// $getBegin   = "SELECT 
//                 COALESCE(
//                     SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) - 
//                     SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END), 
//                 0.00) AS yuranDifference
//                 FROM transactionacc
//                 WHERE
//                 pymtRefer = '".$companyID."' 
//                 AND YEAR(tarikh_doc) < ".$yr."
//                 GROUP BY pymtRefer";

//kalau takda pymtrefer
$getBegin   = "SELECT 
                    COALESCE(
                        SUM(CASE WHEN combined.addminus = 0 THEN combined.pymtAmt ELSE 0 END) - 
                        SUM(CASE WHEN combined.addminus = 1 THEN combined.pymtAmt ELSE 0 END), 
                    0.00) AS yuranDifference
                FROM (
                    SELECT a.pymtAmt, a.addminus, a.tarikh_doc
                    FROM transactionacc a
                    LEFT JOIN cb_purchaseinv b ON a.docNo = b.PINo
                    WHERE b.companyID = $companyID
                        AND a.addminus = 1
                      AND YEAR(a.tarikh_doc) < $yr
            
                    UNION ALL
            
                    SELECT a.pymtAmt, a.addminus, a.tarikh_doc
                    FROM transactionacc a
                    LEFT JOIN billacc b ON a.docNo = b.no_bill
                    WHERE b.diterima_drpd = $companyID
                        AND a.addminus = 0
                      AND YEAR(a.tarikh_doc) < $yr
                ) AS combined
            ";

$rsBeginBal = $conn->Execute($getBegin);

// if ($rsBeginBal === false) {
//     echo "SQL Error: " . $conn->ErrorMsg();
//     echo "<pre>$getBegin</pre>";
// } else {
//     echo "Yuran Difference: " . $rsBeginBal->fields['yuranDifference'];
// }


$totaldebit     = 0;
$totalkredit    = 0;

$name       = dlookup("generalacc", "name", "ID=" . tosql($companyID, "Text"));
$address    = dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Text"));

//cek tahun paling awal ada transaction
// $getYear    = "select MIN(YEAR(tarikh_doc)) as minYear from transactionacc
//                 WHERE pymtRefer = '".$companyID."'";
// $rsYear     = &$conn->Execute($getYear);

//cater menu pemiutang version lama takda companyID pymtrefer
$getYear = "
    SELECT MIN(YEAR(tarikh_doc)) AS minYear
    FROM (
        SELECT tarikh_doc
        FROM transactionacc
        WHERE pymtRefer = '$companyID'

        UNION ALL

        SELECT a.tarikh_doc
        FROM transactionacc a
        LEFT JOIN cb_purchaseinv b ON a.docNo = b.PINo
        WHERE b.companyID = $companyID
          AND a.addminus = 1
          AND (a.pymtRefer IS NULL OR a.pymtRefer = '')

        UNION ALL

        SELECT a.tarikh_doc
        FROM transactionacc a
        LEFT JOIN billacc b ON a.docNo = b.no_bill
        WHERE b.diterima_drpd = $companyID
          AND a.addminus = 0
          AND (a.pymtRefer IS NULL OR a.pymtRefer = '')
    ) AS combined
";

$rsYear = &$conn->Execute($getYear);

$minYear    = $rsYear->fields(minYear);
if ($minYear == $yr) {
    $beginningBalance   = dlookup("generalacc", "b_crelim", "ID=" . tosql($companyID, "Text"));
    $beginBalance       = number_format($beginningBalance, 2);
    $yuranDifference    = $rsBeginBal->fields['yuranDifference'];
} else {     
    $beginningBalance   = 0.00;
    $beginBalance       = number_format($beginningBalance, 2);
    if($yr < $minYear) {
        $yuranDifference = 0.00;
    } else {
        $b_crelim        = dlookup("generalacc", "b_crelim", "ID=" . tosql($companyID, "Text"));
        $yuranDifference = $rsBeginBal->fields['yuranDifference'];
        $yuranDifference = $yuranDifference + $b_crelim;
    }
   
}

$desc = dlookup("generalacc", "name", "ID=".$deductID);

$endingBalance  = 0;
$arrayDate      = array();
$arrayData      = array();
$debit          = 0;
$kredit         = 0;
$totaldebit     = 0;
$totalkredit    = 0;
$balance        = 0;
$nettBalance    = $beginningBalance;
$nettBalance2   = $yuranDifference;

list($arrayData, $arrayDate, $totaldebit, $totalkredit, $endingBalance) = calculation($rs, $arrayData, $arrayDate, $balance, $totaldebit, $totalkredit, $debit, $kredit, $nettBalance, $beginningBalance, $yuranDifference, $yr);
// list($arrayData, $arrayDate, $totaldebit, $totalkredit, $endingBalance) = calculation($rs, array(), array(), $balance, $totaldebit, $totalkredit, $debit, $kredit, $nettBalance, $beginningBalance, $yuranDifference);

// Merge arrayDate into allDates to keep track of all dates
$allDates = array_merge($allDates, $arrayDate);

$companyData[] = array(
    'name'          => $companyName,
    'endingBalance' => $endingBalance,
    'totaldebit'    => $totaldebit,
    'totalkredit'   => $totalkredit
);

$rsCompany->MoveNext();
}

function calculation ($rs1, $arrayData1, $arrayDate1, $balance1, $totaldebit1, $totalkredit1, $debit1, $kredit1, $nettBalance1, $beginningBalance1, $yuranDifference, $yr){

    if ($rs1->RowCount() <> 0) {
        $balance1 = ($yuranDifference == "") ? $beginningBalance1 : $yuranDifference;

        while(!$rs1->EOF){ 

            $debit1     = '';
            $kredit1    = '';

            array_push($arrayDate1, $rs1->fields('tarikh_doc'), date("Y-m-d H:i:s", strtotime("01/01/$yr")));

            if ($rs1->fields('addminus')==0) {
                $debit1         = $rs1->fields('pymtAmt');
                $totaldebit1   += $debit1;
                $balance1      -= $debit1;
                $debit1         = number_format($debit1, 2);
            } else {
                $kredit1        = $rs1->fields('pymtAmt');
                $totalkredit1  += $kredit1;
                $balance1      += $kredit1;
                $kredit1        = number_format($kredit1, 2);
            }

            array_push($arrayData1, toDate('d/m/y',$rs1->fields('tarikh_doc')), $rs1->fields('docNo'), $rs1->fields('desc_akaun'), $debit1, $kredit1, number_format($balance1, 2));
            
            $rs1->MoveNext();
        }
    }

    $endingBalance1 = number_format($balance1, 2);
    $arrayReturn    = array ($arrayData1, $arrayDate1, $totaldebit1, $totalkredit1, $endingBalance1);
    return $arrayReturn;

}

$totalEndingBalance = 0;
foreach ($companyData as $company) {
    $totalEndingBalance += floatval(str_replace(',', '', $company['endingBalance'])); // Add each company's ending balance to the total
}

// Get the overall min and max dates
$overallStartDate   = min($allDates);
$overallEndDate     = max($allDates);

print '<head>
<html lang="en">
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
.form-container{
    font-size: 14px;
    font-family: Arial, Helvetica, sans-serif;
}
.Mainlogo{
    position: absolute;
}
.AlamatPengirim{
    margin-left: 20%;
    border-spacing: 3px;
    word-wrap: break-word; /* Wrap long words to the next line */
    white-space: normal;   /* Allow text to wrap naturally */
    max-width: 350px;      /* Adjust this width as needed to control wrapping */
}
.statement-word{
    float: right;
    text-wrap: nowrap;
    font-weight: bold;
    margin-top: -3%;
}
.bor-penerima{
    margin-bottom: 14%;
    margin-top:3%;
    border-spacing: 3px;
    text-wrap: nowrap;
}
.Open-Bal{
    float: right;
    margin-top:4%;
    border-spacing: 3px;
    text-wrap: nowrap;
}
.word-trans{
    text-wrap: nowrap;
    text-align: center;
    font-weight: bold;
    margin-top: 5%;
}
.line-trans{
    border: groove 2px;
    margin-bottom: 3%;
}
.header-border{
    border: solid 2px;
}
.head-column{
    text-wrap: nowrap;
}
.state-Amount{
    text-align: right;
    font-weight: bold;
}
.table-total_Amount{
    border-collapse: separate;
    border-spacing: 5px;
    float: right;
    margin-bottom: 3%;
}
.td-style{
    font-weight: bold;
}
.th-stylish-center{
    text-align: center;
}
.th-stylish-right{
    text-align: right;
}
.watermark-generated{
    margin-bottom: 2%;
    font-weight: bold;
    text-align: center;
}
.bottom {
    position: fixed;
    bottom: 20px;
    text-align: center;
    width: 100%;
    z-index: 2;
}	
.font7 { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; }
</style>
</head>';

if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));     
$Gambar= "upload_images/".$pic;

print'  
<body>
    <div class="form-container">
    <!---------Logo/Address/Statement of Account-------->
    <div class="Mainlogo"><img id="elImage" src="'.$Gambar.'" style="height: 120px; width: 120px;" alt="Logo Koperasi"></div>
    <table class="AlamatPengirim">
        <tr><td>'.$coopName.'</td></tr>
        <tr><td>'.ucwords(strtolower($address1)).'</td></tr>
        <tr><td>'.ucwords(strtolower($address2)).'</td></tr>
        <tr><td>'.ucwords(strtolower($address3)).'</td></tr>
        <tr><td>'.ucwords(strtolower($address4)).'</td></tr>
        <tr><td>TEL: '.$noPhone.' </td></tr>
        <tr><td>EMEL: '.$email.'</td></tr>
    </table>
    <div class="statement-word" style="font-size: 14px; letter-spacing: 1.5px; display: inline-block;">PENYATA AKAUN
        <small style="display: block; padding-top: 0px; position: relative; top: -5px;">
            <i style="font-weight: normal; font-size: 10px; letter-spacing: 1px; display: inline-block;">
                STATEMENT OF ACCOUNT
            </i>
        </small>
    </div>';

print'
</table>
        <!-----Title Showing All------>
        <div class="word-trans">
            <tr>
                <td>MEMAPARKAN SEMUA BAKI SYARIKAT PEMIUTANG PADA TAHUN '.$yr.'
                    <small style="display: block; padding-top: 0px; position: relative; top: -5px;">
                        <i style="font-weight: normal; font-size: 10px; letter-spacing: 2px; display: inline-block;">
                            SHOWING ALL BALANCES OF CREDITOR COMPANIES IN YEAR '.$yr.'
                        </i>
                    </small>
                </td>
            </tr>
        </div>

        <!-------Table------->
        <div class="line-trans"></div>
        <div class="header-border"></div>
        <table class="table table-striped">
        <thead>
            <tr>
                <th nowrap scope="col">Bil.<br><small><i style="font-weight: normal; font-size: 12px;">No.</i></small></th>
                <th nowrap scope="col">Nama Syarikat<br><small><i style="font-weight: normal; font-size: 12px;">Company Name</i></small></th>
                <th nowrap scope="col">Debit (RP)<br><small><i style="font-weight: normal; font-size: 12px;">Debit (RP)</i></small></th>
                <th nowrap scope="col">Kredit (RP)<br><small><i style="font-weight: normal; font-size: 12px;">Credit (RM)</i></small></th>
                <th nowrap scope="col" class="text-end">Baki Akhir (RM)<br><small><i style="font-weight: normal; font-size: 12px;">Ending Balance (RM)</i></small></th>
            </tr>
        </thead>';

$rowCounter = 1;
foreach ($companyData as $company) {
    print '<tr>
            <td>'.$rowCounter.'</td>
            <td>'.$company['name'].'</td>
            <td>'.number_format($company['totaldebit'], 2).'</td>
            <td>'.number_format($company['totalkredit'], 2).'</td>
            <td class="text-end">'.$company['endingBalance'].'</td>
          </tr>';
          $rowCounter++;
}

// Add a row at the bottom of the table to display the total
print '
        <tr>
            <td colspan="4" class="text-end" style="text-align: right;">
                <strong>Jumlah Baki Akhir (RM):</strong><br>
                <small><i style="font-weight: normal; font-size: 12px;">Total Ending Balance (RM):</i></small>
            </td>
            <td class="text-end" style="text-align: right;">
                <strong>' . number_format($totalEndingBalance, 2) . '</strong>
            </td>
        </tr>
            </div>
                </body>
                </table>
';

print '
<!---Signature--->
  <div class="font7 footer" align="center">Ini adalah cetakan komputer dan tidak perlu ditandatangan
        <small style="display: block; padding-top: 0px; position: relative; top: -3px;">
            <i style="font-size: 9px; letter-spacing: -0.2px; transform: scaleX(0.5.1); display: inline-block;">
                This statement is computer generated and does not require authorised signatory
            </i>
        </small>
  </div>
    <!---End of Signature--->

    <!--- Add style for footer on print --->
    <style>
    @media print {
    .footer {
      position: static;
      bottom: 0px;
      width: 100%;
      font-size: 12px;
    }
    table {
      page-break-inside: auto;
    }
    tr {
      page-break-inside: avoid;
      page-break-after: auto;
    }
  }
</style>

    <script>window.print();</script>
    
    </html>';
?>