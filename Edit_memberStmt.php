<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	Edit_memberStmt.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$title     = "Kemaskini Transaksi";

$sFileName = '?vw=Edit_memberStmt&mn=908';
$sFileNameDel = '?vw=Edit_memberStmt&mn=908';
$ID = $_REQUEST['ID'];
$code = $_REQUEST['code'];
$edit = $_POST['edit'];

$IDName = get_session("Cookie_userName");
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

  $strActivity = $_POST['Submit'] . 'Kemaskini Import Fail - ' . $ID;
  activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
}

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
  print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

if (get_session("Cookie_groupID") == 0) {
  $ID = get_session("Cookie_userID");
  $dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
  $pk[0] = $ID;
}

if ($code == 1) {
  $docNo = dlookup("transaction", "docNo", "ID=" . tosql($IDtype, "Number"));

  if ($docNo) {
    $sSQLdel = "DELETE FROM transaction WHERE ID = " . tosql($IDtype, "Number");
    $rsdel = &$conn->Execute($sSQLdel);

    $strActivity = $_POST['Submit'] . ' Hapus Import Fail ' . $docNo . ' Bagi Anggota - ' . $ID;
    activityLog($sSQLdel, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
  }
}

$sSQL = "select a.*, b.name from transaction a, general b
		 WHERE  userID = " . tosql($ID, "Text") . "
		 AND a.DeductID = b.ID
  		 ORDER BY createdDate";
$rs = &$conn->Execute($sSQL);


$sSQL2 = "SELECT	DISTINCT a.*, b.*
		  FROM 	users a, userdetails b
		  WHERE a.UserID =" . tosql($ID, "Text") . "
		  AND a.UserID = b.UserID";

$rs1 = &$conn->Execute($sSQL2);

print '
<div class="table-responsive">
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
      <td></b>' . $rs1->fields(name) . '</b></td>
    </tr>
    <tr>
      <td>Nombor Anggota  :</td>
      <td><b>' . $ID . '</b></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
    <tr valign="top" class="table-primary">
      <td colspan="9" class="Header"><strong>' . strtoupper($title) . '</strong></td>
    </tr>
    <tr valign="top" class="table-primary">
      <td width="2%" nowrap rowspan="1" ><b>Bil</b></td>
      <td width="20%" nowrap ><b>Nama Dokumen</b></td>
      <td width="11%" nowrap><b>Bulan Tahun</b></td>
      <td width="18%" nowrap><b>Jenis Pembiayaan</b> </td>
      <td width="11%" nowrap><b>Nombor Bond</b> </td>
      <td width="10%" nowrap><b>Jumlah Potongan Pembiayaan (RM)</b></td>
      <td colspan="3" nowrap><div align="center"><b>Edit</b></div></td>
    </tr>';
if ($rs->RowCount() <> 0) {
  $count = 1;
  while (!$rs->EOF) {


    print '
	<tr>
      <td class="Data" >&nbsp;' . $count . '</td>
      <td class="Data" >&nbsp;' . $rs->fields(docNo) . '</td>
      <td class="Data" >&nbsp;' . $rs->fields(yrmth) . '</td>
      <td class="Data" nowrap >&nbsp;' . $rs->fields(name) . '</td>
	  <td class="Data" >&nbsp;';
    if ($IDtype == $rs->fields(ID)) {
      print '&nbsp;<input size="7" onChange="javascript:this.value=this.value.toUpperCase();" name="nobond" value="' . $rs->fields(pymtRefer) . '" >';
    } else {
      print '&nbsp;' . $rs->fields(pymtRefer) . '';
    }
    print ' </td>
      <td class="Data" align="right" >&nbsp;';
    if ($IDtype == $rs->fields(ID)) {
      print '&nbsp;<input size="15" name="noAmt" value="' . $rs->fields(pymtAmt) . '" >';
    } else {
      print '&nbsp;' . $rs->fields(pymtAmt) . '';
    }
    print '</td>
      <td class="Data" align="center" width="5%">&nbsp;<a href="' . $sFileName . '&IDtype=' . $rs->fields(ID) . '&ID=' . $ID . '&code=2" title="kemaskini"><img src="b_edit.png"></a> <input size="7" type="hidden" name="IDtype" value="' . $IDtype . '" ><input size="7" type="hidden" name="ID" value="' . $ID . '" ></td>';

    if (($IDName == 'admin') or ($IDName == 'superadmin')) {
      print '   <td class="Data" align="center"  width="5%">&nbsp;<a href="' . $sFileNameDel . '&IDtype=' . $rs->fields(ID) . '&ID=' . $ID . '&code=1" title="Hapus" onClick="if(!confirm(\'Adakah ada pasti untuk hapus file ini?\')) {return false} else {window.Edittrans.submit();};"><img src="b_drop.png"></td>';
    }

    print '    <td class="Data" align="center" width="5%">';
    if ($IDtype == $rs->fields(ID)) {
      print '<input type="submit" size="3" class="btn btn-sm btn-secondary" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="edit" value="edit" />';
    }
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
</form>
</div>';
