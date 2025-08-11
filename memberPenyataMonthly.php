<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberPenyataMonthly.php
 *          Date 		: 	01/06/2022
 *********************************************************************************/

include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y, g:i a");
if (!isset($mth)) $mth  = date("n");
if (!isset($yr)) $yr  = date("Y");
if (!isset($mm))  $mm = date("m");
if (!isset($yy))  $yy = date("Y");

$yrmthNow = sprintf("%04d%02d", $yr, $mth);

$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
  print '<script>alert("' . $errPage . '");window.close();</script>';
  exit;
}

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title> ' . $emaNetis . '</title>
    <LINK rel="stylesheet" href="images/mail.css">
</head>
<body>';

$title = 'Penyata Potongan Anggota Pada Bulan/Tahun ' . substr($yrmth, 4, 2) . '/' . substr($yrmth, 0, 4);
$title = strtoupper($title);

$sSQL = "SELECT * FROM loans    
     WHERE 
     userID = " . tosql($id, "Text") . "
     AND status IN (3) 
     AND loanType IN (1896) 
     AND yrmth < '" . $yrmthNow . "'
     GROUP BY bondNo";
$rs = &$conn->Execute($sSQL);

print '
<style>

.font1 {
  font-family: Poppins, Helvetica, sans-serif;
  font-size: 9pt;
  font-weight: bold;
}

.font2 {
  font-size: 7.5pt;
}

.font3 {
  font-family: Poppins, Helvetica, sans-serif;
  font-size: 8pt;
  font-weight: bold;
  line-height: 1.5;
}

.font4 {
  font-family: Poppins, Helvetica, sans-serif;
  font-size: 8pt;
  font-weight: bold;
}

.font5 {
  font-family: Poppins, Helvetica, sans-serif;
  font-size: 8pt;
}

.table1 {
  border: 0;
  cellpadding: 5;
  cellspacing: 0;
  width: 100%;
} 

.table2 {
  border-collapse: collapse;
  cellpadding: 2;
  cellspacing: 0;
  width: 50%;
} 

.table3 {
  border-collapse: collapse;
  cellpadding: 2;
  cellspacing: 0;
  width: 70%;
} 

.tableHeader1 {
  background-color: #C0C0C0;
}

.titleBox {
  background-color: #336699;
}

.header {
  height: 40;
  color: #FFFFFF;
}


</style>';
$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($id, "Text"));

print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
  
  <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
    <td colspan="2" align="right">' . strtoupper($emaNetis) . '</td>
  </tr>
  
  <tr bgcolor="#336699" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
    <th colspan="2" height="40"><font color="#FFFFFF">' . $title . '</font>
    </th>
  </tr>
 
  <tr>
    <td colspan="2"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
  </tr>
  
  <tr><td colspan="2">&nbsp;</td></tr>
  
  <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
    <td width="20%">&nbsp;Nombor Anggota</td>
    <td>:&nbsp;' . dlookup("userdetails", "memberID", "userID=" . tosql($id, "Text")) . '</td>
  </tr>

  <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
    <td width="20%">&nbsp;Nama Anggota</td>
    <td>:&nbsp;' . dlookup("users", "name", "userID=" . tosql($id, "Text")) . '</td>
  </tr>

  <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
    <td width="20%">&nbsp;Nombor Kad Pengenalan</td>
    <td>:&nbsp;' . dlookup("userdetails", "newIC", "userID=" . tosql($id, "Text")) . '</td>
  </tr>

  <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
    <td width="20%">&nbsp;Jabatan</td>
    <td>:&nbsp;' . dlookup("general", "name", "ID=" . tosql($jabatan, "Text")) . '</td>
  </tr>

  </table>

<div>&nbsp;</div>
<div>&nbsp;</div>

<table class="table2" border="1" solid>  

  <tr class="font4 tableHeader1">
    <th>Bil</th>
    <th align="left">Perkara</th>
    <th align="right">Amaun (RM)</th>
  </tr>
  
  <tr class="font5">
    <td align="center">1</td>
    <td>Yuran Bulanan</td>
    <td align="right">' . dlookup("userdetails", "monthFee", "userID=" . tosql($id, "Text")) . '</td>
  </tr>
  
  <tr class="font5">
    <td align="center">2</td>
    <td>Syer Bulanan</td>
    <td align="right">' . dlookup("userdetails", "monthDepo", "userID=" . tosql($id, "Text")) . '</td>
  </tr>
  
  <tr class="font5">
    <td align="center">3</td>
    <td>Simpanan Khas Bulanan</td>
    <td align="right">' . dlookup("userdetails", "unitShare", "userID=" . tosql($id, "Text")) . '</td>
  </tr>
</table>

<div>&nbsp;</div>
<div>&nbsp;</div>

<table class="table3" border="1" solid>
  <tr class="font4 tableHeader1">
    <th align="center">&nbsp;Bil</th>
    <th align="left">Nama Pembiayaan</th>
    <th align="center">Nombor Bond</th>
    <th align="right">Potongan Bulanan (RM)</th>
    <th align="right">Baki Semasa Pokok (RM)</th>
  </tr>';
//1896

if ($rs->RowCount() <> 0) {
  $count = 1;
  while (!$rs->EOF) {

    $bond = $rs->fields(bondNo);
    $bulanPTG = $rs->fields(jumBlnP);
    $bakiPYT = getBakiPYT($rs->fields(userID), $yrmth2, $bond);

    $sSQL3 = "SELECT * FROM general
     WHERE  ID = " . $rs->fields(loanType) . "
       ORDER BY ID";
    $rs3 = &$conn->Execute($sSQL3);

    print '
  <tr>
    <td class="Data" align="center">&nbsp;' . $count . '</td>
    <td class="Data" align="left">' . $rs3->fields(name) . '</td>
    <td class="Data" align="center" >&nbsp;' . $bond . '</td>
    <td class="Data" align="right">&nbsp;&nbsp;' . number_format($bulanPTG, 2) . '</td>
    <td class="Data" align="right">&nbsp;' . number_format($bakiPYT, 2) . '</td>
    
  </tr>';
    $count++;
    $rs->MoveNext();
  }
} else {
  print '
      <tr class= "font5">
        <td colspan="5" align="center"><b>- Tiada Rekod </b></td>
      </tr>';
}

print '</table></body></html>';
