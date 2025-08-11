<?php
/*********************************************************************************
*          Project        :    iKOOP.com.my
*          Filename       :    ACCinvoisAll.php
*          Date           :    24/05/2024
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

// $sSQL   = "SELECT * FROM transactionacc
//             WHERE pymtRefer = " . tosql($id, "Text") . "
//             AND (
//                 (YEAR(tarikh_doc) > $yrFrom OR (YEAR(tarikh_doc) = $yrFrom AND MONTH(tarikh_doc) >= $mthFrom))
//                 AND
//                 (YEAR(tarikh_doc) < $yrTo OR (YEAR(tarikh_doc) = $yrTo AND MONTH(tarikh_doc) <= $mthTo))
//             )
//             ORDER BY tarikh_doc";

// $rs     = &$conn->Execute($sSQL);

//cater menu pemiutang tak save companyID dekat pymtRefer
$sSQL = "
    SELECT *
    FROM transactionacc
    WHERE pymtRefer = " . tosql($id, "Text") . "
      AND (
          (YEAR(tarikh_doc) > $yrFrom OR (YEAR(tarikh_doc) = $yrFrom AND MONTH(tarikh_doc) >= $mthFrom))
          AND
          (YEAR(tarikh_doc) < $yrTo OR (YEAR(tarikh_doc) = $yrTo AND MONTH(tarikh_doc) <= $mthTo))
      )

    UNION ALL

    SELECT a.*
    FROM transactionacc a
    LEFT JOIN cb_purchaseinv b ON a.docNo = b.PINo
    WHERE b.companyID = " . tosql($id, "Text") . "
      AND a.addminus = 1
      AND (a.pymtRefer IS NULL OR a.pymtRefer = '')
      AND (
          (YEAR(a.tarikh_doc) > $yrFrom OR (YEAR(a.tarikh_doc) = $yrFrom AND MONTH(a.tarikh_doc) >= $mthFrom))
          AND
          (YEAR(a.tarikh_doc) < $yrTo OR (YEAR(a.tarikh_doc) = $yrTo AND MONTH(a.tarikh_doc) <= $mthTo))
      )

    UNION ALL

    SELECT a.*
    FROM transactionacc a
    LEFT JOIN billacc b ON a.docNo = b.no_bill
    WHERE b.diterima_drpd = " . tosql($id, "Text") . "
      AND a.addminus = 0
      AND (a.pymtRefer IS NULL OR a.pymtRefer = '')
      AND (
          (YEAR(a.tarikh_doc) > $yrFrom OR (YEAR(a.tarikh_doc) = $yrFrom AND MONTH(a.tarikh_doc) >= $mthFrom))
          AND
          (YEAR(a.tarikh_doc) < $yrTo OR (YEAR(a.tarikh_doc) = $yrTo AND MONTH(a.tarikh_doc) <= $mthTo))
      )

    ORDER BY tarikh_doc
";

$rs = &$conn->Execute($sSQL);

//tak pakai
$getYuranOpen   = "SELECT 
                    SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
                    SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
                    FROM transactionacc
                    WHERE
                    pymtRefer = '".$id."' 
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
//                 pymtRefer = '".$id."' 
//                 AND YEAR(tarikh_doc) < ".$yrFrom."
//                 GROUP BY pymtRefer";
// $rsBeginBal = $conn->Execute($getBegin);

//cater menu pemiutang kalau takda companyid dalam pymtrefer
$getBegin = "
    SELECT 
        COALESCE(
            SUM(CASE WHEN combined.addminus = 0 THEN combined.pymtAmt ELSE 0 END) - 
            SUM(CASE WHEN combined.addminus = 1 THEN combined.pymtAmt ELSE 0 END), 
        0.00) AS yuranDifference
    FROM (
        -- With pymtRefer
        SELECT pymtAmt, addminus, tarikh_doc
        FROM transactionacc
        WHERE pymtRefer = '" . $id . "'
          AND YEAR(tarikh_doc) < $yrFrom

        UNION ALL

        -- From cb_purchaseinv
        SELECT a.pymtAmt, a.addminus, a.tarikh_doc
        FROM transactionacc a
        LEFT JOIN cb_purchaseinv b ON a.docNo = b.PINo
        WHERE b.companyID = '" . $id . "'
          AND a.addminus = 1
          AND (a.pymtRefer IS NULL OR a.pymtRefer = '')
          AND YEAR(a.tarikh_doc) < $yrFrom

        UNION ALL

        -- From billacc
        SELECT a.pymtAmt, a.addminus, a.tarikh_doc
        FROM transactionacc a
        LEFT JOIN billacc b ON a.docNo = b.no_bill
        WHERE b.diterima_drpd = '" . $id . "'
          AND a.addminus = 0
          AND (a.pymtRefer IS NULL OR a.pymtRefer = '')
          AND YEAR(a.tarikh_doc) < $yrFrom
    ) AS combined
";

$rsBeginBal = $conn->Execute($getBegin);

$totaldebit = 0;
$totalkredit = 0;

$name       = dlookup("generalacc", "name", "ID=" . tosql($id, "Text"));
$address    = dlookup("generalacc", "b_Baddress", "ID=" . tosql($id, "Text"));

//cek tahun paling awal ada transaction
// $getYear    = "select MIN(YEAR(tarikh_doc)) as minYear from transactionacc
//                 WHERE pymtRefer = '".$id."'";
// $rsYear     = &$conn->Execute($getYear);

//cater menu pemiutang version lama takda companyID pymtrefer
$getYear = "
    SELECT MIN(YEAR(tarikh_doc)) AS minYear
    FROM (
        SELECT tarikh_doc
        FROM transactionacc
        WHERE pymtRefer = '".$id."'

        UNION ALL

        SELECT a.tarikh_doc
        FROM transactionacc a
        LEFT JOIN cb_purchaseinv b ON a.docNo = b.PINo
        WHERE b.companyID = '".$id."'
          AND a.addminus = 1
          AND (a.pymtRefer IS NULL OR a.pymtRefer = '')

        UNION ALL

        SELECT a.tarikh_doc
        FROM transactionacc a
        LEFT JOIN billacc b ON a.docNo = b.no_bill
        WHERE b.diterima_drpd = '".$id."'
          AND a.addminus = 0
          AND (a.pymtRefer IS NULL OR a.pymtRefer = '')
    ) AS combined
";

$rsYear = &$conn->Execute($getYear);

$minYear    = $rsYear->fields('minYear');
if ($minYear == $yrFrom) {
    $beginningBalance   = dlookup("generalacc", "b_crelim", "ID=" . tosql($id, "Text"));
    $beginBalance       = number_format($beginningBalance, 2);
    $yuranDifference    = $rsBeginBal->fields['yuranDifference'];
    //-----beginning balance pun carry opening balance
    $b_crelim           = dlookup("generalacc", "b_crelim", "ID=" . tosql($id, "Text"));   
    $yuranDifference    = $yuranDifference + $b_crelim;
    //----------
} else {     
    $beginBalance       = number_format(0, 2);

    if($yrFrom < $minYear) {
        $yuranDifference = 0.00;
    } else {
        $b_crelim        = dlookup("generalacc", "b_crelim", "ID=" . tosql($id, "Text"));   
        $yuranDifference = $rsBeginBal->fields['yuranDifference'];
        $yuranDifference = $yuranDifference + $b_crelim;

        //-----make beginning balance and opening balance the same amount
        $beginBalance    = number_format($yuranDifference, 2);
        //----------
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

function calculation ($rs1, $arrayData1, $arrayDate1, $balance1, $totaldebit1, $totalkredit1, $debit1, $kredit1, $nettBalance1, $beginningBalance1, $yuranDifference, $cr, $mthFrom, $yrFrom, $mthTo, $yrTo) {

    // Calculate the start and end dates
    $startDate      = "01/$mthFrom/$yrFrom";
    // Dynamically calculate the last day of any given month and year including handling leap years for February
    $lastDayOfMonth = date("t", mktime(0, 0, 0, $mthTo, 1, $yrTo)); // "t" gives the last day of the given month
    $endDate        = "$lastDayOfMonth/$mthTo/$yrTo";
    
    // Push the start date into the array
    array_push($arrayDate1, $startDate);

    if ($rs1->RowCount() <> 0) {
        $balance1 = ($yuranDifference == "") ? $beginningBalance1 : $yuranDifference;

        while(!$rs1->EOF){ 

            $debit1     = '';
            $kredit1    = '';

            array_push($arrayDate1, toDate('d/m/y', $rs1->fields('tarikh_doc')));

            if($cr <> '') {
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
            } else {
                if ($rs1->fields('addminus')==0) {
                    $debit1         = $rs1->fields('pymtAmt');
                    $totaldebit1   += $debit1;
                    $balance1      += $debit1;
                    $debit1         = number_format($debit1, 2);
                } else {
                    $kredit1        = $rs1->fields('pymtAmt');
                    $totalkredit1  += $kredit1;
                    $balance1      -= $kredit1;
                    $kredit1        = number_format($kredit1, 2);
                }
            }

            array_push($arrayData1, toDate('d/m/y',$rs1->fields('tarikh_doc')), $rs1->fields('docNo'), $rs1->fields('desc_akaun'), $debit1, $kredit1, number_format($balance1, 2));
            
            $rs1->MoveNext();
        }
    }

    // Push the formatted end date to the array
    array_push($arrayDate1, $endDate);

    $endingBalance1 = number_format($balance1, 2);
    $arrayReturn    = array ($arrayData1, $arrayDate1, $totaldebit1, $totalkredit1, $endingBalance1);
    return $arrayReturn;

}

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
    <!-- top: 26px; -->
}
.AlamatPengirim{
    margin-left: 20%;
    border-spacing: 3px;
}
.statement-word{
    float: right;
    text-wrap: nowrap;
    font-weight: bold;
    margin-top: -3%;
}
.bor-penerima{
    margin-bottom: 7%;
    margin-top: 4%;
    border-spacing: 3px;
    text-wrap: nowrap;
    word-wrap: break-word; /* Wrap long words to the next line */
    white-space: normal;   /* Allow text to wrap naturally */
    max-width: 300px;      /* Adjust this width as needed to control wrapping */
    height: 150px;         /* Set a fixed height */
    display: block;        /* Ensures content is displayed as a block */
}
.Open-Bal{
    position: absolute;
    right: 0;
    margin-top:4%;
    border-spacing: 3px;
    text-wrap: nowrap;
}
.Open-Bal tr, .Open-Bal td {
    margin: 0;
    padding: 0;
    line-height: 1;
    white-space: nowrap;
}
.word-trans{
    text-wrap: nowrap;
    text-align: center;
    font-weight: bold;
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
    bottom: 10px;
    text-align: center;
    width: 100%;
}	
</style>
</head>';

list($arrayData, $arrayDate, $totaldebit, $totalkredit, $endingBalance) = calculation($rs, $arrayData, $arrayDate, $balance, $totaldebit, $totalkredit, $debit, $kredit, $nettBalance, $beginningBalance, $yuranDifference, $cr, $mthFrom, $yrFrom, $mthTo, $yrTo);

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
        ';
        if($cr <> '') {
        print'     <div class="statement-word" style="font-size: 14px; letter-spacing: 1.5px; display: inline-block;">PENYATA AKAUN PEMIUTANG
        <small style="display: block; padding-top: 0px; position: relative; top: -5px;">
            <i style="font-weight: normal; font-size: 11px; letter-spacing: 1px; display: inline-block;">
                STATEMENT OF CREDITORS
            </i>
        </small>
    </div>';
        }
        else {
        print'     <div class="statement-word" style="font-size: 14px; letter-spacing: 1.5px; display: inline-block;">PENYATA AKAUN
        <small style="display: block; padding-top: 0px; position: relative; top: -5px;">
            <i style="font-weight: normal; font-size: 11px; letter-spacing: 1px; display: inline-block;">
                STATEMENT OF ACCOUNT
            </i>
        </small>
    </div>';
        }
print'
<!-------Table for other details ------->
<table class="Open-Bal">
                <tr>
                    <td class="td-style" colspan="3">Ringkasan Akaun Mengikut Tarikh
                    <small style="display: block; padding-top: 0px; position: relative; top: -2px;">
                        <i style="font-weight: normal; font-size: 12px;">
                            Account Summary as of Date
                        </i>
                    </small>
                    </td>
                    
                </tr>
                <tr>
                    <td>Pembuka Akaun
                    <small style="display: block; padding-top: 0px; position: relative; top: -3px;">
                        <i style="font-weight: normal; font-size: 12px;">
                            Opening Balance
                        </i>
                    </small>
                    </td>
                    <td>: </br>&nbsp;</td>
                    <td>&nbsp;RM '.$beginBalance.'</br>&nbsp;</td>
                </tr>
                ';
                if($cr <> '') {
                print'
                <tr>
                    <td>Invois Pembelian
                    <small style="display: block; padding-top: 0px; position: relative; top: -3px;">
                        <i style="font-weight: normal; font-size: 12px;">
                            Purchase Invoices
                        </i>
                    </small>
                    </td>
                    <td>:</br>&nbsp;</td>
                    <td>&nbsp;RM '.number_format( $totalkredit,2).'</br>&nbsp;</td>
                </tr>
                ';
                } else {
                print'
                <tr>
                    <td>Invois
                    <small style="display: block; padding-top: 0px; position: relative; top: -3px;">
                        <i style="font-weight: normal; font-size: 12px;">
                            Invoiced
                        </i>
                    </small>
                    </td>
                    <td>:</br>&nbsp;</td>
                    <td>&nbsp;RM '.number_format( $totaldebit,2).'</br>&nbsp;</td>
                </tr>
                ';
                }
                if($cr <> '') {
                print'
                <tr>
                    <td>Bayaran
                    <small style="display: block; padding-top: 0px; position: relative; top: -3px;">
                        <i style="font-weight: normal; font-size: 12px;">
                            Payments
                        </i>
                    </small>
                    </td>
                    <td>:</br>&nbsp;</td>
                        <td>&nbsp;RM '.number_format( $totaldebit,2).'</br>&nbsp;</td>
                </tr>
                ';
                } else {
                print'
                <tr>
                    <td>Bayaran
                    <small style="display: block; padding-top: 0px; position: relative; top: -3px;">
                        <i style="font-weight: normal; font-size: 12px;">
                            Payments
                        </i>
                    </small>
                    </td>
                    <td>:</br>&nbsp;</td>
                        <td>&nbsp;RM '.number_format( $totalkredit,2).'</br>&nbsp;</td>
                </tr>
                ';
                }
                print'
                <tr>
                    <td>Baki Terakhir
                    <small style="display: block; padding-top: 0px; position: relative; top: -3px;">
                        <i style="font-weight: normal; font-size: 12px;">
                            Ending Balance
                        </i>
                    </small>
                    </td>
                    <td>:</br>&nbsp;</td>
                    <td>&nbsp;RM '.$endingBalance.'</br>&nbsp;</td>
                </tr>
                
</table> 

<!-------Name/Address ------->
<table class="bor-penerima">
        <tr><td class="td-style">'.ucwords(strtolower($name)).'</td></tr>';
            
        $addressLines = explode(',', $address);
        foreach ($addressLines as $line) {
            print '<tr><td>'.trim($line).'</td></tr>';
        }

print'
</table>
        <!-----Title Showing All------>
        <div class="word-trans">
            <tr>
            ';
            if($cr <> '') {
            print '
                <td>MEMAPARKAN SEMUA INVOIS PEMBELIAN DAN BAYARAN ANTARA '.$arrayDate[0].' DAN '.end($arrayDate).'
                <small style="display: block; padding-top: 0px; position: relative; top: -4px;">
                    <i style="font-weight: normal; font-size: 10px; letter-spacing: 1.5px; display: inline-block;">
                        SHOWING ALL PURCHASE INVOICES AND PAYMENTS BETWEEN '.$arrayDate[0].' AND '.end($arrayDate).'
                    </i>
                </small>
                </td>
            </tr>
        </div>
            ';
            } else {
            print '
                <td>MEMAPARKAN SEMUA INVOIS DAN BAYARAN ANTARA '.$arrayDate[0].' DAN '.end($arrayDate).'
                <small style="display: block; padding-top: 0px; position: relative; top: -4px;">
                    <i style="font-weight: normal; font-size: 10px; letter-spacing: 1.5px; display: inline-block;">
                        SHOWING ALL INVOICES AND PAYMENTS BETWEEN '.$arrayDate[0].' AND '.end($arrayDate).'
                    </i>
                </small>
                </td>
            </tr>
        </div>
            ';
            }
            
        print'
        <!-------Table------->
        <div class="line-trans"></div>
        <div class="header-border"></div>
        <table class="table table-striped">
        <thead class="head-column">
            <tr>
                <th scope="col" class="th-stylish-center">Tarikh<br><small><i style="font-weight: normal; font-size: 12px;">Date</i></small></th>
                <th scope="col" class="th-stylish-center">No Rujukan<br><small><i style="font-weight: normal; font-size: 12px;">Reference No</i></small></th>
                <th scope="col" class="th-stylish-center">Keterangan<br><small><i style="font-weight: normal; font-size: 12px;">Description</i></small></th>
                ';
                if ($cr <> ''){
                print'
                <th scope="col" class="th-stylish-right">Pembelian Invois (RM)<br><small><i style="font-weight: normal; font-size: 12px;">Purchase Invoices (RM)</i></small></th>
                ';
                } else {
                print'
                <th scope="col" class="th-stylish-right">Invois (RM)<br><small><i style="font-weight: normal; font-size: 12px;">Invoiced (RM)</i></small></th>
                ';
                }
                if ($cr <> ''){
                print'
                <th scope="col" class="th-stylish-right">Bayaran (RM)<br><small><i style="font-weight: normal; font-size: 12px;">Payments</i></small></th>                
                ';
                } else {
                print'
                <th scope="col" class="th-stylish-right">Kredit (RM)<br><small><i style="font-weight: normal; font-size: 12px;">Credit (RM)</i></small></th>                
                ';
                }
                print'
                <th scope="col" class="th-stylish-right">Baki (RM)<br><small><i style="font-weight: normal; font-size: 12px;">Balance (RM)</i></small></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="10%" align="center">-</td>
                <td width="20%" style="white-space: nowrap;" align="center">Baki Permulaan<br><small><i style="font-weight: normal; font-size: 12px;">Beginning Balance</i></small></td>
                <td width="30%"></td>
                <td width="10%" align="right"></td>
                <td width="10%" align="right"></td>';
                if ($yuranDifference == "") {
                    print '<td width="20%" align="right">0.00</td>';
                } else {
                    print '<td width="20%" align="right">'.number_format($yuranDifference, 2).'</td>';
                }
                print '
            </tr>
        </tbody>';

        /*----------Print content table by Looping-----------*/

        $arrayNum = count($arrayData);

        if ($cr <> ''){
            for ($i= 0; $i<$arrayNum; $i += 6){

                print '
                <tr>
                <td width="10%" align="center">&nbsp;'.$arrayData[$i].'&nbsp;</td>
                <td width="20%" align="center">'.$arrayData[$i+1].'</td>
                <td width="30%" align="left">'.$arrayData[$i+2].'</td>
                <td width="10%" align="right">'.$arrayData[$i+4].'</td>
                <td width="10%" align="right">'.$arrayData[$i+3].'</td>
                <td width="20%" align="right">'.$arrayData[$i+5].'</td>
                </tr>'; 
            }
        } else {
        for ($i= 0; $i<$arrayNum; $i += 6){

        print '
        <tr>
            <td width="10%" align="center">&nbsp;'.$arrayData[$i].'&nbsp;</td>
            <td width="20%" align="center">'.$arrayData[$i+1].'</td>
            <td width="30%" align="left">'.$arrayData[$i+2].'</td>
            <td width="10%" align="right">'.$arrayData[$i+3].'</td>
            <td width="10%" align="right">'.$arrayData[$i+4].'</td>
            <td width="20%" align="right">'.$arrayData[$i+5].'</td>
        </tr>'; 
        }
    }

print'
    </table>
    <div class="state-Amount">Jumlah yang perlu dibayar (MYR)<br><small><i style="font-weight: normal; font-size: 12px;">Amount Due (MYR)</i></small></div>
    <table class="table-total_Amount">
    <tr>
        <td class="td-style">RM</td>
        <td class="td-style">'.$endingBalance.'</td>
    </tr>
    </table>

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
      position: fixed;
      bottom: 0;
      width: 100%;
      font-size: 12px; /* Adjust as needed */
      z-index: 9999;
      background: white;
    }
    body {
      margin-bottom: 50px; /* Make sure there is enough space for the footer */
    }
    table {
      page-break-inside: auto;
    }
    tr {
      page-break-inside: avoid;
      page-break-after: auto;
    }

    /* Ensure the footer never overlaps */
    @page {
      margin-bottom: 30px; /* Adjust if needed to allow space for footer */
    }
  }
</style>

    <script>window.print();</script>
    </body>
    
    </html>';
?>