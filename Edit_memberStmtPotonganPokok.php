<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	Edit_memberStmtPotonganPokok.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
$title     = "Kemaskini Potongan Bulanan ( Pokok & Untung )";

$sFileName = "?vw=Edit_memberStmtPotonganPokok&mn=$mn";
$sFileNameDel = "?vw=Edit_memberStmtPotongan&mn=$mn";
$sFileRef  = "?vw=Edit_memberStmtPotonganPokok&mn=$mn";
$ID = $_REQUEST['ID'];
$bond = $_REQUEST['bondNo'];
$code = $_REQUEST['code'];
$edit = $_POST['edit'];
if ($code == 2) {
  $nobond = $_REQUEST['nobond'];
}
if ($edit) {
  $updatedDate = date("Y-m-d H:i:s");
  $UserID = $_POST['UserID'];
  $IDtype = $_POST['IDtype'];
  $pymtRefer = $_POST['nobond'];
  $pymtAmt = $_POST['noAmt'];

  $sSQLUpd  = "UPDATE transaction SET " .
    " pymtRefer= '" . $pymtRefer . "' " .
    ", pymtAmt= '" . $pymtAmt . "' " .
    ", updatedDate= '" . $updatedDate . "' " .
    " Where ID ='" . $IDtype . "' ";

  $rsUpd = &$conn->Execute($sSQLUpd);

  $sSQL = "select a.*, b.name from transaction a, general b
		 WHERE  userID = " . $ID . "
		 AND a.DeductID = b.ID
  		 ORDER BY createdDate";
  $rs = &$conn->Execute($sSQL);


  $sSQL2 = "SELECT	DISTINCT a.*, b.*
		  FROM 	users a, userdetails b
		  WHERE a.UserID =" . $ID . "
		  AND a.UserID = b.UserID";

  $rs1 = &$conn->Execute($sSQL2);
}

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
  print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

if (get_session("Cookie_groupID") == 0) {
  $ID = get_session("Cookie_userID");
  $dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
  $pk[0] = $ID;
  //$objchk = " checked disabled ";
}


if ($code == 1) {
  $sSQLdel = "delete from transaction Where ID =" . $IDtype . "";
  $rsdel = &$conn->Execute($sSQLdel);
}

$sSQL = "select * from potbulanlook 		 
		 WHERE  potID = " . tosql($ID, "Text") . "
		 ORDER BY ID";
$rs = &$conn->Execute($sSQL);

$sSQL2 = "SELECT	* FROM users 
		  WHERE userID =" . $rs->fields(userID) . "
		  ORDER BY userID";
$rs1 = &$conn->Execute($sSQL2);

$sSQL3 = "select * from general
		 WHERE  ID = " . $rs->fields(loanType) . "
  		 ORDER BY ID";
$rs3 = &$conn->Execute($sSQL3);
$name = $rs1->fields(name);

?>

<head>
  <title>iKOOP</title>
</head>

<body>

  <?php
  print '<div class="table-responsive">
<form id="Edittrans" name="Edittrans" method="post" action=' . $sFileName . '>
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="' . $StartRec . '">
<input type="hidden" name="by" value="' . $by . '">

<table  width="100%" >
    <tr>
      <td width="183">&nbsp;</td>
      <td width="908">&nbsp;</td>
    </tr>
    <tr>
      <td>Nama Anggota :</td>
      <td>' . $name . '</td>
    </tr>
    <tr>
      <td>Nomor Anggota  :</td>
      <td>' . $rs->fields(userID) . '</td>
    </tr>
    <tr>
      <td>Jenis Pembiayaan :</td>
      <td>' . $rs3->fields(name) . '</td>
    </tr>
  </table>
  <table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
    <tr valign="top" class="textFont">
      <td colspan="9" class="Header"><strong>' . strtoupper($title) . '</strong></td>
    </tr>
    <tr class="table-success">
      <td width="2%" nowrap rowspan="1" >No</td>
      <td width="11%" nowrap>Mula Pot. (Bulan/Tahun) </td>
      <td width="18%" align="right" nowrap>Pokok</td>
      <td width="11%" align="right" nowrap>Untung</td>
	  <td width="11%" align="right" nowrap>Jum Pokok Tahun </td>
      <td width="10%" align="right" nowrap>Jum Untung Tahun </td>
        </tr>';
  if ($rs->RowCount() <> 0) {
    $count = 1;
    while (!$rs->EOF) {


      print '
	<tr>
      <td class="Data" >&nbsp;' . $count . '</td>
      <td class="Data" >&nbsp;' . $rs->fields(yrmth) . '</td>
      <td class="Data" align="right">' . number_format($rs->fields(pokok), 2) . '</td>
	  <td class="Data" align="right">' . number_format($rs->fields(untung), 2) . '</td>
	  <td class="Data" align="right" >' . number_format(($rs->fields(pokok) * 12), 2) . ' </td>
	  <td class="Data" align="right" >' . number_format(($rs->fields(untung) * 12), 2) . '</td>
';
      print '</td>
    </tr>';
      $count++;
      $rs->MoveNext();
    }
  } else {
    print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod </b></td>
					</tr>';
  }

  print '
  </table>
  <p>&nbsp;</p>
</form></div>
<p>&nbsp;</p> '; ?>
</body>

</html>