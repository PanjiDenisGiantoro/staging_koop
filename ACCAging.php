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

$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email, koperasiID FROM setup
        WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$coopName = $rss->fields(name);
$address1 = $rss->fields(address1);
$address2 = $rss->fields(address2);
$address3 = $rss->fields(address3);
$address4 = $rss->fields(address4);
$noPhone = $rss->fields(noPhone);
$email = $rss->fields(email);
$koperasiID = $rss->fields(koperasiID);

if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
  print '<script>alert("' . $errPage . '");
  window.close();
  </script>';
  exit;
}

$sSQL = "SELECT * FROM transactionacc
        WHERE pymtRefer = " . tosql($id, "Text") . " AND docID IN (5,7) GROUP BY docNo ORDER BY tarikh_doc";
$rs = &$conn->Execute($sSQL);



$getYuranOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transactionacc
		WHERE
		pymtRefer = '" . $id . "' 
		GROUP BY pymtRefer";
$rsYuranOpen = $conn->Execute($getYuranOpen);

$totaldebit = 0;
$totalkredit = 0;

$name = dlookup("generalacc", "name", "ID=" . tosql($id, "Text"));
$address = dlookup("generalacc", "b_Baddress", "ID=" . tosql($id, "Text"));

print '
<!DOCTYPE html>
<html>

<head>
</head> 

<style>

.font2 {
  font-family: Poppins, Helvetica, sans-serif;
  font-weight: bold;
}

.font3 {
  font-family: Poppins, Helvetica, sans-serif;
  font-size: 10pt;
  line-height: 1.5;
}

.font7{
  font-family: Poppins, Helvetica, sans-serif;
  font-size: 8pt;
}

</style>';

print '
<body>

<!---Company address, title, client address and account summary--->

  <table width="100%">

    <tr>
    <td colspan="2" align="center">
      <div class="boxTitle" align="center"><b>' . $coopName . '</b></div>
    </td>
  </tr>

  <tr>
  <td colspan="2" align="center" valign="middle" class="textFont">    
    ' . $address1 . ',<br/>
    ' . $address2 . ',<br/>
    ' . $address3 . ',<br/>
    ' . $address4 . '.<br/>
    TEL: ' . $noPhone . '<br/>
    EMEL: ' . $email . '
    </td>
  </tr>
    
    <tr><td>&nbsp;</td></tr>
    
    <tr class="font2">
      <td align="center">LAPORAN AGING</td>
      </tr>
    <tr class="font2">
      <td align="center">' . $name . '</td>
    </tr>
  </table>
  
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  
  <table width="100%">
    <tr class="font3" valign="top">
      <td style= "width: 30%" >
      <b>' . $name . '</b><br />
      ' . $address . ' <br /.
      </td>

      
    </tr>
  </table>
  
<!---End of company address, title, client address and account summary--->


<!---Table--->

<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
  <tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
      <th width="10%">Tarikh</th>
      <th width="10%">No Rujukan</th>
      <th width="10%">Tarikh Tamat Tempoh</th>
      <th width="10%" align="right">Baki (RM)</th>
      <th width="10%" align="right">Semasa (RM)</th>
      <th width="10%" align="right">Hari Ke 31-60 (RM)</th>
      <th width="10%" align="right">Hari Ke 61-90 (RM)</th>
      <th width="10%" align="right">Hari Ke 91-120 (RM)</th>
      <th width="10%" align="right">Hari Ke 121-150 (RM)</th>
      <th width="10%" align="right">Hari Ke >150 (RM)</th>
    </tr>';

if ($rs->RowCount() <> 0) {
  while (!$rs->EOF) {


    print '
        <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
          <td width="10%" align="center" align="center">' . toDate('d/m/Y', $rs->fields('tarikh_doc')) . '</td>
          <td width="10%" align="center">' . $rs->fields('docNo') . '</td>
          <td width="10%" align="center"></td>
          <td width="10%" align="right">0.00</td>
          <td width="10%" align="right">0.00</td>
          <td width="10%" align="right">0.00</td>
          <td width="10%" align="right">0.00</td>
          <td width="10%" align="right">0.00</td>
          <td width="10%" align="right">0.00</td>
          <td width="10%" align="right">0.00</td>
        </tr>';

    $rs->MoveNext();
  }
}

print '

      <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight:bold;" bgcolor="FFFFFF">
      <td colspan="3" align="right"><b>JUMLAH (RM) </b></td>
      <td align="right">&nbsp;' . number_format($totaldebit, 2) . '</td>
      <td align="right">&nbsp;' . number_format($totalkredit, 2) . '</td>
      <td align="right">&nbsp;' . number_format($totalkredit, 2) . '</td>
      <td align="right">&nbsp;' . number_format($totalkredit, 2) . '</td>
      <td align="right">&nbsp;' . number_format($totalkredit, 2) . '</td>
      <td align="right">&nbsp;' . number_format($totalkredit, 2) . '</td>
      <td align="right">&nbsp;' . number_format($totalkredit, 2) . '</td>
    </tr>

  </table>




<!---End of Table--->

  <div>&nbsp;</div>
  <div>&nbsp;</div>
  
<!---Ending Balance --->';

print '

  
<!---End of Ending Balance--->

<!---Amount due --->


  <div>&nbsp;</div>

  ';



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
