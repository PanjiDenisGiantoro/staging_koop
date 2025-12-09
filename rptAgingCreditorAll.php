<?php
/*********************************************************************************
*          Project		  :	  iKOOP.com.my
*          Filename		  : 	rptAgingCreditorAll.php
*          Date 		    : 	28/04/2022
*          Updated Date : 	11/07/2024
*********************************************************************************/
session_start();
include("common.php");
date_default_timezone_set("Asia/Kuala Lumpur");
$today  = date("F j, Y, g:i a");
$date   = date("d/m/Y");

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

// Extract the text inside the brackets and convert it to uppercase
if (preg_match("/\((.*?)\)/", $coopName, $matches)) {
    $shortName = strtoupper($matches[1]);
} else {
    $shortName = ''; // Default value if no match found
}

$sSQL         = "SELECT * FROM generalacc WHERE category = 'AB'";
$GetCreditor  = $conn->Execute($sSQL);  

$companyData  = array();

while (!$GetCreditor->EOF) {
    $companyID    = $GetCreditor->fields['ID'];
    $companyName  = $GetCreditor->fields['name'];

    $companyData[] = array(
        'name'  => $companyName,
        'ID'    => $companyID,
    );

    $GetCreditor->MoveNext();
}

if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));     
$Gambar= "upload_images/".$pic;

print '
<!DOCTYPE html>
<html>

<head>
<style>
.Mainlogo{
  position: absolute;
  top: 20px;
}

.AlamatPengirim{
  margin-left: 28%;
  border-spacing: 3px;
  word-wrap: break-word; /* Wrap long words to the next line */
  white-space: normal;   /* Allow text to wrap naturally */
  max-width: 350px;      /* Adjust this width as needed to control wrapping */
}

.dateBox { background-color: grey; text-align: center; }
.tableTitle { background-color: lightgrey; }
        .tableContent, .tdData, .thData {
            border: 1px solid black;
            border-collapse: collapse;
        }

        /* Prevent breaking inside the table rows */
        tr {
            page-break-inside: avoid;
        }

        /* Optionally, force page break after specific rows */
        .break-after {
            page-break-after: always;
        }

        /* You can also force a page break before certain rows if necessary */
        .break-before {
            page-break-before: always;
        }.border { border-top: 1px solid black; border-bottom: double; }
.font1 { font-family: Poppins, Helvetica, sans-serif; font-weight: bold; font-size: 9pt; line-height: 1.5; }
.font2 { font-family: Poppins, Helvetica, sans-serif; font-weight: bold; }
.font3 { font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; line-height: 1.5; }
.font4 { font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; }
.font5 { font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; }
.font6 { font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; }
.font7 { font-family: Poppins, Helvetica, sans-serif; font-size: 6pt; }
</style>
</head> 

<body>
<table width="100%">
    <tr>
        <td colspan="2" class="Mainlogo"><img id="elImage" src="'.$Gambar.'" style="height: 120px; width: 120px;" alt="Logo Koperasi"></td>
    </tr>
    <tr>
        <!-- First column: Company address, title, client address-->
        <td style="vertical-align: top; width: 70%;">
            <table class="AlamatPengirim">
                <tr>
                    <td>
                        <b>' . $coopName . '</b><br />
                        ' . ucwords(strtolower($address1)) . '<br />
                        ' . ucwords(strtolower($address2)) . '<br />
                        ' . ucwords(strtolower($address3)) . '<br />
                        ' . ucwords(strtolower($address4)) . '<br />
                        TEL: ' . $noPhone . '<br />
                        EMEL: ' . $email . '
                    </td>
                </tr>
            </table>
        </td>
        
        <!-- Second column: Right-aligned company short name -->
        <td class="font1" style="text-align: right; vertical-align: top;">
            <h3 style="margin: 0;"><b>' . $shortName . '</b></h3>
        </td>
    </tr>
</table>
<br><br>
';

$totalInvoisCompanyAll  = 0;
$totalBakiCompanyAll    = 0;

$rowCounter = 1;
  foreach ($companyData as $company) {

        //if PI save companyID to pymtRefer
        // $ssSQL = "SELECT DISTINCT *
        //           FROM transactionacc
        //           WHERE pymtRefer = " . tosql($company['ID'], "Text") . "
        //           AND (
        //               (docID = 8 AND addminus = 1)
        //           )
        //           AND YEAR(tarikh_doc) <= " . $yr . "";

        //if PI doesn't save companyID to pymtRefer
        $ssSQL  = "SELECT DISTINCT a.*, b.PINo, b.companyID
                    FROM transactionacc a
                    LEFT JOIN cb_purchaseinv b ON a.docNo = b.PINo
                    WHERE (a.docID = 8 AND a.addminus = 1)
                    AND b.companyID = " . tosql($company['ID'], "Text") . "
                    AND YEAR(a.tarikh_doc) <= " . $yr . "
                    ORDER BY a.tarikh_doc ASC";
                    
        $GetData = $conn->Execute($ssSQL);

    if (!$GetData->EOF) {

        print'
      <table>
      <tr><td style="width: 33.33%; vertical-align: top;">
<!---Table--->

<table width="100%" class="tableContent">
    <tr class="font5 tableTitle" >
      <th class="thData">Tanggal<br><small><i style="font-weight: normal; font-size: 10px;">Date</i></small></th>
      <th class="thData">No Rujukan<br><small><i style="font-weight: normal; font-size: 10px;">Reference No</i></small></th>
      <th align="right" class="thData">Invois (RP)<br><small><i style="font-weight: normal; font-size: 10px; white-space: nowrap;">Invoiced (RP)</i></small></th>
      <th align="right" class="thData">Baki Semasa (RP)<br><small><i style="font-weight: normal; font-size: 10px;">Current Balance (RP)</i></small></th>
      <th align="right" class="thData">0-30 Hari (RP)<br><small><i style="font-weight: normal; font-size: 10px; white-space: nowrap;">0-30 Days (RP)</i></small></th>
      <th align="right" class="thData">31-60 Hari (RP)<br><small><i style="font-weight: normal; font-size: 10px; white-space: nowrap;">31-60 Days (RP)</i></small></th>
      <th align="right" class="thData">61-90 Hari (RP)<br><small><i style="font-weight: normal; font-size: 10px; white-space: nowrap;">61-90 Days (RP)</i></small></th>
      <th align="right" class="thData">>90 Hari (RP)<br><small><i style="font-weight: normal; font-size: 10px; white-space: nowrap;">>90 Days (RP)</i></small></th>
    </tr>';
    
    //sum total invoice
    //if PI save companyID to pymtRefer
    // $getInv     = "SELECT SUM(pymtAmt) AS jumInv
    //                 FROM transactionacc
    //                 WHERE pymtRefer = ".$company['ID']." 
    //                 AND YEAR(tarikh_doc) <= " . $yr . " 
    //                 AND docID IN (8)
    //                 AND docNo like '%PI%'
    //                 AND addminus = 1";

    //sum total invoice
    //if PI doesn't save companyID to pymtRefer
    $getInv     = "SELECT COALESCE(SUM(a.pymtAmt), 0.00) AS jumInv, b.PINo, b.companyID
                    FROM transactionacc a
                    LEFT JOIN cb_purchaseinv b ON a.docNo = b.PINo
                    WHERE YEAR(a.tarikh_doc) <= " . $yr . " 
                    AND b.companyID = ".$company['ID']." 
                    AND a.docID IN (8)
                    AND a.docNo like '%PI%'
                    AND a.addminus = 1";

        $rs     = $conn->Execute($getInv);

        //sum total bil
        //if BL save PI to pymtReferC
        // $getRes = "SELECT SUM(pymtAmt) AS jumRes
        //             FROM transactionacc
        //             WHERE pymtRefer = ".$company['ID']."
        //             AND YEAR(tarikh_doc) <= " . $yr . " 
        //             AND docID IN (7)
        //             AND docNo like '%BL%'
        //             AND addminus = 0";

        //sum total bil
        //if BL doesn't save PI to pymtReferC
        $getRes = "SELECT COALESCE(SUM(a.pymtAmt), 0.00) AS jumRes, b.no_bill, b.diterima_drpd
                    FROM transactionacc a
                    LEFT JOIN billacc b ON a.docNo = b.no_bill
                    WHERE YEAR(a.tarikh_doc) <= " . $yr . " 
                    AND b.diterima_drpd = ".$company['ID']."
                    AND a.docID IN (7)
                    AND a.docNo like '%BL%'
                    AND a.addminus = 0";

        $rss    = $conn->Execute($getRes);

        $beginningBalance = dlookup("generalacc", "b_crelim", "ID=" . tosql($company['ID'], "Text"));
        $beginBalance     = number_format($beginningBalance, 2);

        $debitTotal   = 0;
        $kreditTotal  = 0;
  
            $debit  = '';
            $kredit = '';
  
            if ($GetData->fields['addminus']==0) {
              $debit        = $GetData->fields['pymtAmt'];
              $totaldebit  += $debit;
              $balance      = $debit;
              $debit        = number_format($debit, 2);
              $nettBalance += $balance;
            } else {
              $kredit       = $GetData->fields['pymtAmt'];
              $totalkredit += $kredit;
              $balance      = $kredit;
              $kredit       = number_format($kredit, 2);
              $nettBalance -= $balance;
            }
   
            $endingBalance = $beginningBalance + $rs->fields['jumInv'] - $rss->fields['jumRes'];
            $endingBalance = number_format($endingBalance, 2);

            print'<tr><b>'.$rowCounter.')&nbsp;'.$company['name'].'</b></tr>';

            $totalInvoisCompany   = 0.00;
            $totalBakiCompany     = 0.00;
            $total30Hari          = 0.00;
            $total60Hari          = 0.00;
            $total90Hari          = 0.00;
            $total120Hari         = 0.00;

      while(!$GetData->EOF){ 
        $createdDate  = date("d/m/Y", strtotime($GetData->fields['tarikh_doc']));
        $date30       = date('d/m/Y', strtotime($GetData->fields['tarikh_doc']));
        $tarikh1      = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
        $tarikh2      = date('Y-m-d', strtotime(str_replace('/', '-', $date30)));
        $bezaHari     = ((strtotime($tarikh1) - strtotime($tarikh2)) / (60 * 60 * 24)) - 30;

      // Fetching totBayarINV for each invoice
      //if BL save companyID to pymtRefer
      // $ssSQLBL = "SELECT SUM(t.pymtAmt) AS totBayarINV
      //   FROM transactionacc AS t
      //   INNER JOIN billacc AS c
      //   ON t.docNo = c.no_bill
      //   WHERE t.pymtRefer = ".$company['ID']."
      //   AND (t.docID = 7 AND t.addminus = 0) 
      //   AND YEAR(t.tarikh_doc) <= '" . $yr . "'
      //   AND c.PINo = '" . $GetData->fields['docNo'] . "'";

      // Fetching totBayarINV for each invoice
      //if BL doesn't save companyID to pymtRefer
      $ssSQLBL = "SELECT COALESCE(SUM(t.pymtAmt), 0.00) AS totBayarINV, c.no_bill, c.diterima_drpd
        FROM transactionacc AS t
        INNER JOIN billacc AS c
        ON t.docNo = c.no_bill
        WHERE c.diterima_drpd = ".$company['ID']."
        AND (t.docID = 7 AND t.addminus = 0) 
        AND YEAR(t.tarikh_doc) <= '" . $yr . "'
        AND c.PINo = '" . $GetData->fields['docNo'] . "'";

      $GetDataBL = $conn->Execute($ssSQLBL);

      // Calculate bakiBelumByr for each invoice
      $bakiBelumByr = $GetData->fields['pymtAmt'] - $GetDataBL->fields['totBayarINV'];

      // Fetching credit note values for the invoice
      $ssSQLknockoff    = "SELECT SUM(pymtAmt) as totKnockoff from note where knockoff = '".$GetData->fields('docNo')."'";
      $GetDataKnockoff  = $conn->Execute($ssSQLknockoff);
      $knockoffVal      = $GetDataKnockoff->fields('totKnockoff');

      // bakiBelumByr minus credit note values
      $bakiBelumByr = $bakiBelumByr - $knockoffVal;

      // bakiBelumByr
      // $bakiBelumByr = $bakiBelumByr;

    // var_dump($GetData->fields); // Print out the field names
    // var_dump($GetDataBL->fields); // Print out the field names

      print '

      <tr class="font6">
        <td width="10%" class="tdData"  align="center">&nbsp;'.$createdDate.'&nbsp;</td>
        <td width="20%" class="tdData" align="center">'.$GetData->fields['docNo'].'</td>';
        print '
        <td width="10%" class="tdData" align="right">'.number_format($GetData->fields['pymtAmt'], 2).'</td>
        <td width="10%" class="tdData"  align="right">'.number_format($bakiBelumByr, 2).'</td>';
        if ($bezaHari >= 1 && $bezaHari <= 30) {
          print '<td width="10%" class="tdData"  align="right">'.number_format($bakiBelumByr, 2).' ('.($bezaHari).')</td>';
          $total30Hari += $bakiBelumByr;
        } else {
          print '<td width="10%" class="tdData"  align="right"></td>';
        }

        if ($bezaHari >= 31 && $bezaHari <= 60) {
          print '<td width="10%" class="tdData"  align="right">'.number_format($bakiBelumByr, 2).' ('.($bezaHari).')</td>';
          $total60Hari += $bakiBelumByr;
        } else {
          print '<td width="10%" class="tdData"  align="right"></td>';
        }

        if ($bezaHari >= 61 && $bezaHari <= 90) {
          print '<td width="10%" class="tdData"  align="right">'.number_format($bakiBelumByr, 2).' ('.($bezaHari).')</td>';
          $total90Hari += $bakiBelumByr;
        } else {
          print '<td width="10%" class="tdData"  align="right"></td>';
        }

        if ($bezaHari >= 91) {
          print '<td width="10%" class="tdData"  align="right">'.number_format($bakiBelumByr, 2).' ('.($bezaHari).')</td>';
          $total120Hari += $bakiBelumByr;
        } else {
          print '<td width="10%" class="tdData"  align="right"></td>';
        }
      print '</tr>'; 

    if ($GetData->fields['docID'] == 14) {
        $totalInvoisCompany   += -$GetData->fields['pymtAmt'];
      } else {
        $totalInvoisCompany   += $GetData->fields['pymtAmt'];
      }

      if ($GetData->fields['docID'] == 14) {
        $totalBakiCompany     += -$bakiBelumByr;
      } else {
        $totalBakiCompany     += $bakiBelumByr;
      }

      $GetData->MoveNext();
    } 
    $GetData->Close();
    
    // Add to the overall totals of all companies
    $totalInvoisCompanyAll  += $totalInvoisCompany;
    $totalBakiCompanyAll    += $totalBakiCompany;

    print'
    <tr class="font6">
    <td colspan="2" class="tdData" align="right"><b>Jumlah (RP)<br><small><i style="font-weight: normal; font-size: 12px;">Total (RP)</i></small></b></td>';
    print '
    <td colspan="1" class="tdData" align="right"><b>'.number_format($totalInvoisCompany, 2).'</b></td>
    <td colspan="1" class="tdData" align="right"><b>'.number_format($totalBakiCompany, 2).'</b></td>
    <td colspan="1" class="tdData" align="right"><b>'.($total30Hari == 0.00 ? '' : number_format($total30Hari, 2)).'</b></td>
    <td colspan="1" class="tdData" align="right"><b>'.($total60Hari == 0.00 ? '' : number_format($total60Hari, 2)).'</b></td>
    <td colspan="1" class="tdData" align="right"><b>'.($total90Hari == 0.00 ? '' : number_format($total90Hari, 2)).'</b></td>
    <td colspan="1" class="tdData" align="right"><b>'.($total120Hari == 0.00 ? '' : number_format($total120Hari, 2)).'</b></td>';
    } else {
        continue;
    }

      print '
      </table>
          </table>

    <!---End of Table--->
    
      <div>&nbsp;</div>
      
    <!---Ending Balance --->';
    print '
  
<!---End of Ending Balance--->

</body>
</html>';

$rowCounter++;
  $GetCreditor->MoveNext();
  }
$GetCreditor->Close(); 

// Table Total All Invois & Baki Semasa
print '
<table width="100%" class="tableContent" style="border: 1px solid black; border-collapse: collapse;">
    <tr class="font6 tableTitle">
        <th class="thData" style="width: 50%; text-align: center; padding: 5px;">Jumlah Invois (RP)<br><small style="display: block; padding-top: 0px; position: relative; top: -1px;"><i style="font-weight: normal; font-size: 10px;">Invoice Amount (RP)</i></small></th>
        <th class="thData" style="width: 50%; text-align: center; padding: 5px;">Jumlah Baki Semasa (RP)<br><small style="display: block; padding-top: 0px; position: relative; top: -1px;"><i style="font-weight: normal; font-size: 10px;">Current Balance Amount (RP)</i></small></th>
    </tr>
    <tr class="font7">
        <td class="tdData" style="width: 50%; text-align: center; padding: 5px;"><b>' . number_format($totalInvoisCompanyAll, 2) . '</b></td>
        <td class="tdData" style="width: 50%; text-align: center; padding: 5px;"><b>' . number_format($totalBakiCompanyAll, 2) . '</b></td>
    </tr>
</table>
';

print '<hr class="1px">';

print '
<!---Signature--->
  <div class="font7 footer" align="center">Ini adalah cetakan komputer dan tidak perlu ditandatangan
        <small style="display: block; padding-top: 0px; position: relative; top: -3px;">
            <i style="font-size: 8px; letter-spacing: -0.2px; transform: scaleX(0.5.1); display: inline-block;">
                This statement is computer generated and does not require authorised signatory
            </i>
        </small>
  </div>
<!---End of Signature--->

<!--- Print the page when loaded --->
<script>window.print();</script>

<!--- Add style for footer on print --->
<style>
  @media print {
    .footer {
      position: static;
      bottom: 0;
      width: 100%;
      font-size: 12px; /* Adjust as needed */
    }
    hr {
      margin-top: 50px; /* Make sure there is enough space for the footer */
    }
    body {
      margin-bottom: 50px; /* Make sure there is enough space for the footer */
    }
  }
</style>';