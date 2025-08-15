<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	Edit_memberStmtDIV.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
$title     = "Kemaskini Transaksi";

$sFileName = "?vw=Edit_memberStmtDIV&mn=$mn";
$sFileNameDel = "?vw=Edit_memberStmtDIV&mn=$mn";
$ID = $_REQUEST['ID'];
$code = $_REQUEST['code'];
$edit = $_POST['edit'];

$IDName = get_session("Cookie_userName");
if ($code == 2) {
	$nobond = $_REQUEST['nobond'];
}

if ($edit) {

	$updatedDate = date("Y-m-d H:i:s");
	$IDtype = $_POST['IDtype'];
	$noAmt = $_POST['noAmt'];
	$TgkknAm = 0;
	$UpfrntAmt = 0;

	$TgkknAmt = $_POST['Tunggakan'];
	$UpfrntAmt = $_POST['Upfront'];
	$catatan = $_POST['catatan'];

	$noAmt2 = $noAmt - ($TgkknAmt + $UpfrntAmt);


	$sSQLUpd	= "UPDATE dividen SET " .
		" AmtDiv= '" . $noAmt2 . "' " .
		",TgkknAmt= '" . $TgkknAmt . "' " .
		",UpfrntAmt= '" . $UpfrntAmt . "' " .
		",catatan= '" . $catatan . "' " .
		" Where ID ='" . $IDtype . "' ";

	$rsUpd = &$conn->Execute($sSQLUpd);


	$sSQL = "select a.name, a.userID, b.AmtDiv, b.ID,b.TgkknAmt,b.UpfrntAmt, b.yearDiv from users a, dividen b
		 WHERE  a.userID = " . tosql($ID, "Text") . "
		 AND a.userID = b.userID";
	$rs = &$conn->Execute($sSQL);

	$strActivity = $_POST['Submit'] . 'Kemaskini Dividen - ' . $ID;
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
	//$objchk = " checked disabled ";
}


if ($code == 1) {

	$docNo = dlookup("dividen", "docNo", "ID=" . tosql($IDtype, "Number"));

	if ($docNo) {
		$sSQLdel = "DELETE FROM dividen WHERE ID = " . tosql($IDtype, "Number");
		$rsdel = &$conn->Execute($sSQLdel);

		$strActivity = $_POST['Submit'] . ' Hapus Dividen ' . $docNo . ' Bagi Anggota -' . $ID;
		activityLog($sSQLdel, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
	}
}



$sSQL = "select a.name, a.userID, b.AmtDiv, b.ID,b.TgkknAmt,b.UpfrntAmt,b.catatan, b.yearDiv from users a, dividen b
		 WHERE  a.userID = " . tosql($ID, "Text") . "
		 AND a.userID = b.userID";
$rs = &$conn->Execute($sSQL);

$sSQL2 = "SELECT	DISTINCT a.*, b.*
		  FROM 	users a, userdetails b
		  WHERE a.UserID =" . tosql($ID, "Text") . "
		  AND a.UserID = b.UserID
		  ORDER by a.userID ASC";

$rs1 = &$conn->Execute($sSQL2);
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

<table  width="100%" class="table table-sm">   
    <tr class="table-light">
      <td width="183">Nama Anggota :</td>
      <td width="908"><b>' . $rs1->fields(name) . '</b></td>
    </tr>
    <tr class="table-light">
      <td>Nombor Anggota  :</td>
      <td><b>' . $ID . '</b></td>
    </tr>
  </table>
  <table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
    <tr valign="top" class="table-primary">
      <td colspan="10" class="Header"><strong>' . strtoupper($title) . '</strong></td>
    </tr>
    <tr class="table-primary">
      <td width="2%" nowrap rowspan="1" ><b>Bil</b></td>
      <td width="20%" nowrap ><b>Nombor Anggota - Nama</b></td>
      <td width="5%" nowrap><b>Tahun</b></td>
      <td width="8%" nowrap align="right"><b>Dividen (RM)</b></td>
	  <td width="10%" nowrap align="right"><b>Tunggakkan (RM)</b></td>
	  <td width="10%" nowrap align="right"><b>Upfront (Bersara) (RM)</b></td>
	  <td width="15%" nowrap><b>Catatan</b></td>
      <td colspan="3" nowrap><div align="center"><b>Edit</b></div></td>
    </tr>';
	if ($rs->RowCount() <> 0) {
		$count = 1;
		while (!$rs->EOF) {


			print '
	<tr>
      <td class="Data" >&nbsp;' . $count . '</td>
      <td class="Data" >&nbsp;' . $rs->fields(userID) . '--&nbsp;' . $rs->fields(name) . '</td>
        <td class="Data">&nbsp;' . $rs->fields(yearDiv) . '</td>
      <td class="Data" align="right" >&nbsp;';
			if ($IDtype == $rs->fields(ID)) {
				print '&nbsp;<input size="8" class="form-control-sm" name="noAmt" value="' . $rs->fields(AmtDiv) . '" > ';
			} else {
				print '&nbsp;' . $rs->fields(AmtDiv) . '';
			}
			print ' </td>	
	    <td class="Data" align="right" >&nbsp;';
			if ($IDtype == $rs->fields(ID)) {
				print '&nbsp;<input size="10" class="form-control-sm" name="Tunggakan" value="' . $rs->fields(TgkknAmt) . '" > ';
			} else {
				print '&nbsp;' . $rs->fields(TgkknAmt) . '';
			}
			print ' </td>	
	    <td class="Data" align="right" >&nbsp;';
			if ($IDtype == $rs->fields(ID)) {
				print '&nbsp;<input size="10" class="form-control-sm" name="Upfront" value="' . $rs->fields(UpfrntAmt) . '" > ';
			} else {
				print '&nbsp;' . $rs->fields(UpfrntAmt) . '';
			}
			print ' </td>
	    <td class="Data" align="right" >&nbsp;';
			if ($IDtype == $rs->fields(ID)) {
				print '&nbsp;<input size="30" class="form-control-sm" id="catatan" name="catatan" value="' . $rs->fields(catatan) . '"> ';
			} else {
				print '&nbsp;' . $rs->fields(catatan) . '';
			}

			print '</td>	
      <td class="Data" align="center" width="5%">&nbsp;<a href="' . $sFileName . '&IDtype=' . $rs->fields(ID) . '&ID=' . $ID . '&code=2" title="kemaskini"><i class="bx bx-edit"></i></a> <input size="7" type="hidden" name="IDtype" value="' . $IDtype . '" ><input size="7" type="hidden" name="ID" value="' . $ID . '" ></td>';

			if (($IDName == 'admin') or ($IDName == 'superadmin')) {
				print '  <td class="Data" align="center"  width="5%">&nbsp;<a href="' . $sFileNameDel . '&IDtype=' . $rs->fields(ID) . '&ID=' . $ID . '&code=1" title="Hapus" onClick="if(!confirm(\'Adakah ada pasti untuk hapus file ini?\')) {return false} else {window.Edittrans.submit();};"><i class="mdi mdi-close text-danger"></i></td>';
			}

			print '	  <td class="Data" align="center" width="5%">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input type="submit" class="btn btn-sm btn-primary" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="edit" value="edit" />       
';
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
</form></div>
<p>&nbsp;</p> '; ?>
</body>

</html>