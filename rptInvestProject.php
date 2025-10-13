<?php
/*********************************************************************************
*          Project		:	  iKOOP.com.my
*          Filename		: 	ACCinvoisAll.php
*          Date 		  : 	28/04/2022
*********************************************************************************/
session_start();
include("common.php");
date_default_timezone_set("Asia/Kuala Lumpur");
$today = date("F j, Y, g:i a");
$date = date("d/m/Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
  print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

//looping based on project
$sSQL = "SELECT * FROM investors WHERE compID = ".$id."";

$GetInvestor = $conn->Execute($sSQL);  

$companyName = dlookup("generalacc", "name", "ID=" . tosql($id, "Text"));

print '
<!DOCTYPE html>
<html>

<head>
</head> 

<style>

.dateBox {
  background-color: grey;    
  text-align: center;
}

.tableTitle {
  background-color: lightgrey;
}

.tableContent, .tdData, .thData {
  border: 1px solid black;
  border-collapse: collapse;
}

.border {
  border-top: 1px solid black;
  border-bottom: double;
}

.font1 {
  font-family: Arial, Helvetica, sans-serif;
  font-weight: bold;
  font-size: 9pt;
  line-height: 1.5;
}

.font2 {
  font-family: Arial, Helvetica, sans-serif;
  font-weight: bold;
}

.font3 {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 10pt;
  line-height: 1.5;
}

.font4 {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 10pt;
  font-weight: bold;
}

.font5{
  font-family: Arial, Helvetica, sans-serif;
  font-size: 10pt;
  font-weight: bold;
}

.font6{
  font-family: Arial, Helvetica, sans-serif;
  font-size: 9pt;
}

.font7{
  font-family: Arial, Helvetica, sans-serif;
  font-size: 8pt;
}

</style>

<body>

<!---Company address, title, client address and account summary--->

  <table width="100%">
    <tr class="font1">
      <td align="left">
      <b>KOPERASI PERMODALAN FELDA MALAYSIA 2 BERHAD (KPF2)</b><br />
      TINGKAT 1, BALAI FELDA,<br />
      JALAN GURNEY 1,<br />
      54000 KUALA LUMPUR,<br />
      KUALA LUMPUR.<br />
      EMEL : admin.kpf2@felda.net.my<br />
      NO. TEL : +603 - 26970186 / +603 - 26040737<br />
      </td>
      <td><img src="images/kpf2.png" height="120" align="right"></td>
    </tr>
    
    <tr class="font2">
      <td align="center" colspan="2">AGING REPORT BASED ON PROJECT FOR COMPANY<br/><u>'.$companyName.'</u></td>
    </tr>
    <tr><td>&nbsp;</td></tr>
  </table>';
      
  if ($GetInvestor->RowCount() <> 0) {

    while(!$GetInvestor->EOF){ 
      
      $debitTotal = 0;
      $kreditTotal = 0;

        $ID     = $GetInvestor->fields(ID);
        $projectName     = dlookup("investors", "nameproject", "ID=" . tosql($ID, "Text"));

        //ambil total baucer
        $getPVL = "SELECT 
                  SUM(CASE WHEN YEAR(tarikh_doc) <= ".$yr." THEN pymtAmt ELSE 0 END) AS pymt_now
                  FROM transactionacc WHERE SUBSTRING(docNo, 1, 3) = 'PVL' AND pymtReferC = ".$GetInvestor->fields(ID)." AND addminus = '1'";
        $rsPVL = $conn->Execute($getPVL);

        $ssSQL = "SELECT DISTINCT *
                  FROM transactionacc
                  WHERE pymtReferC = ".$GetInvestor->fields(ID)."
                  AND (
                      (docID = 15 AND addminus = 1)
                      OR
                      (docID = 6 AND addminus = 0)
                  )";
        $GetData = $conn->Execute($ssSQL);

        //sum total invoice
        $getInv = "SELECT SUM(pymtAmt) AS jumInv
                   FROM transactionacc
                   WHERE pymtReferC = ".$GetInvestor->fields(ID)." 
                   AND docID IN (15)
                   AND docNo like '%PBI%'
                   AND addminus = 0";
        $rs = $conn->Execute($getInv);

        //sum total invoice
        $getRes = "SELECT SUM(pymtAmt) AS jumRes
                   FROM transactionacc
                   WHERE pymtReferC = ".$ID." 
                   AND docID IN (6)
                   AND docNo like '%PB%'
                   AND addminus = 1";
        $rss = $conn->Execute($getRes);

        $beginningBalance = $GetInvestor->fields(openbalpro);
        $beginBalance = number_format($beginningBalance, 2);

          $debit = '';
          $kredit = '';

          $createdDate = toDate("d/m/Y", $GetData->fields(tarikh_doc));

          if ($GetData->fields(addminus)==0) {
            $debit = $GetData->fields(pymtAmt);
            $totaldebit += $debit;
            $balance = $debit;
            $debit = number_format($debit, 2);
            $nettBalance += $balance;
          } else {
            $kredit = $GetData->fields(pymtAmt);
            $totalkredit += $kredit;
            $balance = $kredit;
            $kredit = number_format($kredit, 2);
            $nettBalance -= $balance;
          }

 
      $endingBalance = $beginningBalance + $rs->fields(jumInv) - $rss->fields(jumRes); 
      $endingBalance = number_format($endingBalance, 2);

    print '  
    <tr><td>&nbsp;</td></tr> 
    <tr><td><div align="left">&nbsp;<u>Nama Projek : <b>'.$projectName.'</b></u></div></td></tr>
        <tr><td>&nbsp;</td></tr>


        <table>
        <tr><td style="width: 33.33%; vertical-align: top;">
        <table>
 
        <tr>
          <td colspan="3">Principal Investment</td>
          <td colspan="3" align="left">:</td>
          <td colspan="3" align="left">'.number_format($GetInvestor->fields(amount), 2).' </td>
        </tr>
      
        <tr>
          <td colspan="3">Principal Disbursement</td>
          <td colspan="3" align="left">:</td>
          <td colspan="3" align="left">'.number_format($rsPVL->fields(pymt_now), 2).' </td>
        </tr>
      
        <tr>
          <td colspan="3">Tenure</td>
          <td colspan="3" align="left">:</td>
          <td colspan="3" align="left">'.$GetInvestor->fields(period).'</td>
        </tr>
      
      </table>
      </td>
      <td style="width: 33.33%; vertical-align: top;"">
      <table>&nbsp;
      </table>
      </td>
      <td style="width: 33.33%; vertical-align: top;"">
      <table>
              <tr>
                <td><b>Account Summary From '.toDate('M/d/y', $GetData->fields(tarikh_doc)).' To '.$date.'</b></td>
              </tr>
              
              <tr>
                <td colspan="3">Opening Balance</td>
                <td colspan="3" align="left">:</td>
                <td colspan="3" align="center"> '.$beginBalance.' </td>
              </tr>
            
              <tr>
                <td colspan="3">Invoiced</td>
                <td colspan="3" align="left">:</td>
                <td colspan="3" align="center"> '.number_format($rs->fields(jumInv),2).' </td>
              </tr>
            
              <tr>
                <td colspan="3">Payments</td>
                <td colspan="3" align="left">:</td>
                <td colspan="3" align="center"> '.number_format($rss->fields(jumRes), 2).' </td>
              </tr>
            
              <tr>
                <td colspan="3">Ending Balance</td>
                <td colspan="3" align="left">:</td>
                <td colspan="3" align="center"> '.$endingBalance.' </td>
              </tr>
            
            </table>
            </td></tr>
        </table>
    
<!---End of company address, title, client address and account summary--->

<!---Invoices Date--->
<tr><td>&nbsp;</td></tr>

  <div class="dateBox font4">
    <tr>
      <td>SHOWING ALL INVOICES AND PAYMENTS </td>
      <!--td>'.toDate('M/d/y', $arrayDate[0]).'</td>
      <td>AND</td>
      <td>'.toDate('M/d/y', end($arrayDate)).'</td-->
    </tr>
  </div>
  
<!---End of invoices Date--->

<div>&nbsp;</div>

<!---Table--->

<table width="100%" class="tableContent">
    <tr class="font5 tableTitle" >
      <th width="10%" class="thData" colspan="6">Date</th>
      <th width="20%" class="thData" colspan="6">Reference No</th>
      <th width="30%" class="thData" colspan="6" style="text-align: left;">Description</th>
      <th width="10%" class="thData" colspan="6" align="right">Invoiced (RP)</th>
      <th width="10%" class="thData" colspan="6" align="right">Credit (RP)</th>
      <th width="20%" class="thData" colspan="6" align="right">Balance (RP)</th>
    </tr>    
    
    <tr class="font6">
      <td width="10%" class="tdData" colspan="6" align="center">-</td>
      <td width="20%" class="tdData" colspan="6">&nbsp;Beginning Balance&nbsp;</td>
      <td width="30%" class="tdData" colspan="6"></td>
      <td width="10%" class="tdData" colspan="6" align="right"></td>
      <td width="10%" class="tdData" colspan="6" align="right"></td>
      <td width="20%" class="tdData" colspan="6" align="right">'.$beginBalance.'</td>
    </tr>';

    while(!$GetData->EOF){ 
    print '
    <tr class="font6">
      <td width="10%" class="tdData" colspan="6" align="center">&nbsp;'.$createdDate.'&nbsp;</td>
      <td width="20%" class="tdData" colspan="6" align="center">'.$GetData->fields(docNo).'</td>
      <td width="10%" class="tdData" colspan="6" align="left">'.$GetData->fields(desc_akaun).'</td>';
      if ($GetData->fields(addminus)==1) {
        $debitTotal += $GetData->fields('pymtAmt');
        print '<td width="10%" class="tdData" colspan="6" align="right">'.number_format($GetData->fields(pymtAmt), 2).'</td>';
      } else {
        print '<td width="10%" class="tdData" colspan="6" align="right">&nbsp;</td>';
      }
      
      if ($GetData->fields(addminus)==0) {
        $kreditTotal += $GetData->fields('pymtAmt');
        print '<td width="10%" class="tdData" colspan="6" align="right">'.number_format($GetData->fields(pymtAmt), 2).'</td>';
      } else {
        print '<td width="10%" class="tdData" colspan="6" align="right">&nbsp;</td>';
      }

      print '<td width="20%" class="tdData" colspan="6" align="right">'.number_format($beginningBalance + $debitTotal - $kreditTotal, 2).'</td>
    </tr>'; 
    $GetData->MoveNext();
  }
  $GetData->Close();
      print '
      </table>
    
    <!---End of Table--->
    
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      
    <!---Ending Balance --->';
    print '
  <table width= "100%">
    
    <tr class="font6">
      <td width="10%" colspan="6"></td>
      <td width="20%" colspan="6"></td>
      <td width="30%" colspan="6"></td><td class ="border" width="10%" colspan="6" align="right">'.number_format($debitTotal, 2).'</td>
      <td class ="border" width="10%" colspan="6" align="right">'.number_format($kreditTotal, 2).'</td>
      <td width="20%" colspan="6"></td>
    </tr>
    
  </table>
  
<!---End of Ending Balance--->
<!---Amount due --->


  <div>&nbsp;</div>
  <div class="font5" align="right">Amount due (MYR)</div>
  <table width="10%" align="right">
    <tr class="font5">
      <td align="left">RM</td>
      <td align="right">'.$endingBalance.'</td>
    </tr>
  </table>'; 

print '
<!---End of amount due--->

  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>


</body>

</html>';

  
print '<hr class="1px">';
  $GetInvestor->MoveNext();
  }
$GetInvestor->Close(); 
}

print '
<!---Signature--->
  <div class="font7" align="center">This statement is computer generated and does not require authorised signatory.</div>
  
<!---End of Signature--->';

    


      
      
