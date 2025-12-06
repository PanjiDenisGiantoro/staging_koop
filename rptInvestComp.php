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
$date = date("d-m-Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
  print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sSQL = "SELECT * FROM transactionacc WHERE pymtRefer = ".$id." AND docID IN (6,15)";

$rs = &$conn->Execute($sSQL);

$getWajibOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transactionacc
		WHERE
		pymtRefer = '".$id."' 
		GROUP BY pymtRefer";
$rsWajibOpen = $conn->Execute($getWajibOpen);

$getInvest = "SELECT SUM(openbalpro) as balance, nameproject FROM investors WHERE compID = ".$id."";
$rsInvest = $conn->Execute($getInvest);

$totaldebit = 0;
$totalkredit = 0;

$name = dlookup("generalacc", "name", "ID=" . tosql($id, "Text"));
$address = dlookup("generalacc", "b_Baddress", "ID=" . tosql($id, "Text"));
// $beginningBalance = dlookup("generalacc", "b_crelim", "ID=" . tosql($id, "Text"));
$beginningBalance = $rsInvest->fields(balance);
$beginBalance = number_format($beginningBalance, 2);

$desc = dlookup("generalacc", "name", "ID=".$deductID);

$endingBalance = 0;
$arrayDate = array();
$arrayData = array();

$debit = 0;
$kredit = 0;
$totaldebit = 0;
$totalkredit = 0;
$balance = 0;
$nettBalance = $beginningBalance;

function calculation ($rs1, $arrayData1, $arrayDate1, $balance1, $totaldebit1, $totalkredit1, $debit1, $kredit1, $nettBalance1, $endingBalance1, $beginningBalance1){

  if ($rs1->RowCount() <> 0) {

    while(!$rs1->EOF){ 

      $debit1 = '';
      $kredit1 = '';

      array_push($arrayDate1, $rs1->fields(tarikh_doc));
      
      if ($rs1->fields(addminus)==0) {
        $debit1 = $rs1->fields(pymtAmt);
        $totaldebit1 += $debit1;
        $balance1 = $debit1;
        $debit1 = number_format($debit1, 2);
        $nettBalance1 += $balance1;
      } else {
        $kredit1 = $rs1->fields(pymtAmt);
        $totalkredit1 += $kredit1;
        $balance1 = $kredit1;
        $kredit1 = number_format($kredit1, 2);
        $nettBalance1 -= $balance1;
      }

      array_push($arrayData1, toDate('d/m/y',$rs1->fields(tarikh_doc)), $rs1->fields(docNo), $rs1->fields(pymtReferC), $debit1, $kredit1, number_format($nettBalance1, 2));
      
    $rs1->MoveNext();
    }
  }

  $endingBalance1 = $beginningBalance1 + $totaldebit1 - $totalkredit1; 
  $endingBalance1 = number_format($endingBalance1, 2);

  $arrayReturn = array ($arrayData1, $arrayDate1, $totaldebit1, $totalkredit1, $endingBalance1);
  return $arrayReturn;

}
;


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

</style>';


list($arrayData, $arrayDate, $totaldebit, $totalkredit, $endingBalance) = calculation($rs, $arrayData, $arrayDate, $balance, $totaldebit, $totalkredit, $debit, $kredit, $nettBalance, $endingBalance, $beginningBalance);

print '
<body>

<!---Company address, title, client address and account summary--->

  <table width="100%">
    <tr class="font1">
      <td align="left">
      <b>KOPERASI PERPODALAN FELDA MALAYSIA 2 BERHAD (KPF2)</b><br />
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
      <td align="center" colspan="2">AGING REPORT BASED ON COMPANY</td>
    </tr>
  </table>
  
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  
  <table width="100%">
    <tr class="font3" valign="top" align="center">
    <td style= "width: 33.33%">&nbsp;</td>
      <td style= "width: 33.33%">
      <b>'.$name.'</b><br />
      '.$address.' <br /.
      </td>
      
      <td align="right" style= "width: 33.33%">
      
        <table>
          <tr>
            <td colspan="3" align="left"><b>Account Summary As The Date</b></td>
            <td colspan="3" align="left">:</td>
            <td colspan="3" align="left">'.$date.'</td>
           
          </tr>
          
          <tr>
            <td colspan="3">Opening Balance</td>
            <td colspan="3" align="left">:</td>
            <td colspan="3" align="left"> '.$beginBalance.' </td>
          </tr>
        
          <tr>
            <td colspan="3">Invoiced</td>
            <td colspan="3" align="left">:</td>
            <td colspan="3" align="left"> '.number_format( $totaldebit,2).' </td>
          </tr>
        
          <tr>
            <td colspan="3">Payments</td>
            <td colspan="3" align="left">:</td>
            <td colspan="3" align="left"> '.number_format( $totalkredit,2).' </td>
          </tr>
        
          <tr>
            <td colspan="3">Ending Balance</td>
            <td colspan="3" align="left">:</td>
            <td colspan="3" align="left"> '.$endingBalance.' </td>
          </tr>
        
        </table>
      </td>
      
    </tr>
  </table>
  
<!---End of company address, title, client address and account summary--->

  <div>&nbsp;</div>

<!---Invoices Date--->

  <div class="dateBox font4">
    <tr>
      <td>SHOWING ALL INVOICES AND PAYMENTS BETWEEN </td>
      <td>'.toDate('M/d/y', $arrayDate[0]).'</td>
      <td>AND</td>
      <td>'.toDate('M/d/y', end($arrayDate)).'</td>
    </tr>
  </div>
  
<!---End of invoices Date--->

  <div>&nbsp;</div>

<!---Table--->

  <table width="100%" class="tableContent">
    <tr class="font5 tableTitle" >
      <th width="10%" class="thData" colspan="6">Date</th>
      <th width="20%" class="thData" colspan="6">Reference No</th>
      <th width="30%" class="thData" colspan="6" align="left">Project Name</th>
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

    //print content in table

    $arrayNum = count($arrayData);
  
    for ($i= 0; $i<$arrayNum; $i += 6){

      print '
        <tr class="font6">
          <td width="10%" class="tdData" colspan="6" align="center">&nbsp;'.$arrayData[$i].'&nbsp;</td>
          <td width="20%" class="tdData" colspan="6" align="center">'.$arrayData[$i+1].'</td>
          <td width="30%" class="tdData" colspan="6">'.dlookup("investors","nameproject","ID=".tosql($arrayData[$i+2], "Text")).'</td>
          <td width="10%" class="tdData" colspan="6" align="right">'.$arrayData[$i+3].'</td>
          <td width="10%" class="tdData" colspan="6" align="right">'.$arrayData[$i+4].'</td>
          <td width="20%" class="tdData" colspan="6" align="right">'.$arrayData[$i+5].'</td>
        </tr>'; 
      } 


  print '
  </table>

<!---End of Table--->

  <div>&nbsp;</div>
  <div>&nbsp;</div>
  
<!---Ending Balance --->';

  print '
  <table width= "100%">
    
    <!--tr class="font6">
    <td width="10%" colspan="6" align="center">&nbsp;</td>
      <td width="20%" colspan="6">Ending Balance</td>
      <td width="30%" colspan="6"></td>
      <td width="10%" colspan="6" align="right"></td>
      <td width="10%" colspan="6" align="right"></td>
      <td width="20%" colspan="6" align="right">'.$endingBalance.'</td>
    </tr-->
    
    <tr class="font6">
      <td width="10%" colspan="6"></td>
      <td width="20%" colspan="6"></td>
      <td width="30%" colspan="6" align="right">Total : </td>
      <td class ="border" width="10%" colspan="6" align="right">'.number_format( $totaldebit,2).'</td>
      <td class ="border" width="10%" colspan="6" align="right">'.number_format( $totalkredit,2).'</td>
      <td width="20%" colspan="6"></td>
    </tr>
    
  </table>
  
<!---End of Ending Balance--->

<!---Amount due --->


  <div>&nbsp;</div>
  <div class="font5" align="right">Amount due (MYR)</div>
  <table width="10%" align="right">
    <tr class="font5">
      <td align="left">RP</td>
      <td align="right">'.$endingBalance.'</td>
    </tr>
  </table>';



print '
<!---End of amount due--->

  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>



<!---Signature--->
  <div class="font7" align="center">This statement is computer generated and does not require authorised signatory.</div>
  
<!---End of Signature--->

</body>

</html>

'
?>