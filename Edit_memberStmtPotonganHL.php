<?

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	Edit_memberStmtPotonganHL.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
$title     = "Kemaskini Potongan Pembiayaan Bulanan";

$sFileName = 'Edit_memberStmtPotonganHL.php';
$sFileNameDel = 'Edit_memberStmtPotonganHL.php';
$sFileRef  = 'Edit_memberStmtPotonganPokokHL.php';
$ID = $_REQUEST['ID'];
$code = $_REQUEST['code'];
//$IDtype = $_REQUEST['IDtype'];
$edit = $_POST['edit'];
if (!isset($mth)) $mth	= date("n");
if (!isset($yr)) $yr	= date("Y");
if (!isset($mm))	$mm = date("m"); //"ALL";
if (!isset($yy))	$yy = date("Y");

$yrmthNow = sprintf("%04d%02d", $yr, $mth);
$yymm = $yy . $mm;


if ($code == 2) {
	//$nobond = $_REQUEST['nobond'];
	$ID = $_REQUEST['ID'];
	//$IDtype = $_REQUEST['IDtype'];

	$sSQL = "select * from potbulanHL 	 
		 WHERE  userID = " . tosql($ID, "Text") . "
		 AND status IN (1) AND yrmth < '" . $yrmthNow . "'
  		 Group BY bondNo";
	$rs = &$conn->Execute($sSQL);

	$sSQL2 = "SELECT DISTINCT a.*, b.*
		  FROM 	users a, userdetails b
		  WHERE a.UserID =" . tosql($ID, "Text") . "
		  AND a.UserID = b.UserID";

	$rs1 = &$conn->Execute($sSQL2);
}

if ($edit) {

	$updatedDate = date("Y-m-d H:i:s");
	//$UserID = $_POST['ID'];
	$IDtype = $_POST['IDtype'];
	//$pymtRefer = $_POST['nobond'];
	$pymt = $_POST['noAmt'];
	$Fee = $_POST['YuranP'];
	$ID = $_REQUEST['ID'];

	$sSQLUpd	= "UPDATE potbulanHL SET" .
		" jumBlnP= '" . $pymt . "'" .
		" Where ID  = '" . $IDtype . "'";
	$rsUpd = &$conn->Execute($sSQLUpd);

	$sSQLUpd2	= "UPDATE userdetails SET" .
		" monthFee= '" . $Fee . "'" .
		" Where userID  = '" . $ID . "'";
	$rsUpd2 = &$conn->Execute($sSQLUpd2);

	print '<script>alert("Kemaskini Potongan Gaji Berjaya !");</script>';
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
	$sSQLdel = "delete from potbulanHL Where ID =" . $IDtype . "";
	$rsdel = &$conn->Execute($sSQLdel);

	$sSQLdel2 = "delete from potbulanlookHL Where potID =" . $IDtype . "";
	$rsdel2 = &$conn->Execute($sSQLdel2);

	print '<script>alert("Potongan Gaji Berjaya Dihapuskan !");</script>';
}



$sSQL = "select * from potbulanHL	 
		 WHERE  userID = " . tosql($ID, "Text") . "
		 AND status IN (1) 
  		 ";

//AND (lastyrmthPymt >= '".$yrmthNow."' AND yrmth <= '".$yrmthNow."')
$rs = &$conn->Execute($sSQL);

$sSQL2 = "SELECT DISTINCT a.*, b.*
		  FROM 	users a, userdetails b
		  WHERE a.UserID =" . tosql($ID, "Text") . "
		  AND a.UserID = b.UserID";

$rs1 = &$conn->Execute($sSQL2);
?>

<head>
	<title>iKOOP</title>
</head>

<body>

	<?
	print '
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
      <td>' . $rs1->fields(name) . '</td>
    </tr>
    <tr>
      <td>Nombor Anggota  :</td>
      <td>' . $ID . '</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
    <tr valign="top" class="textFont">
      <td colspan="9" class="Header"><strong>' . strtoupper($title) . '</strong></td>
    </tr>
    <tr valign="top" class="header">
      <td width="2%" nowrap rowspan="1" >No</td>
      <td width="11%" nowrap>Mula Pot. (Bulan/Tahun) </td>
      <td width="18%" nowrap>Jenis Pembiayaan </td>
      <td width="11%" nowrap>No Bond </td>
	  <td width="11%" nowrap>Jumlah Yuran </td>
      <td width="10%" nowrap>Jum Pot. Bulan Pembiayaan (RM) </td>
      <td colspan="3" nowrap><div align="center">Edit</div></td>
    </tr>';
	if ($rs->RowCount() <> 0) {
		$count = 1;
		while (!$rs->EOF) {
			$sSQL3 = "select * from general
		 WHERE  ID = " . $rs->fields(loanType) . "
  		 ORDER BY ID";
			$rs3 = &$conn->Execute($sSQL3);

			print '
	<tr>
      <td class="Data" >&nbsp;' . $count . '</td>
      <td class="Data" >&nbsp;' . $rs->fields(yrmth) . '</td>
      <td class="Data"><a href="' . $sFileRef . '?ID=' . tohtml($rs->fields(ID)) . '">' . $rs3->fields(name) . '</a></td>
	  <td class="Data" nowrap >&nbsp;' . $rs->fields(bondNo) . '</td>
	  <td class="Data" >&nbsp;';
			if ($IDtype == $rs->fields(ID)) {
				print '&nbsp;<input size="7" name="YuranP" value="' . $rs1->fields(monthFee) . '" >';
			} else {
				print '&nbsp;' . $rs1->fields(monthFee) . '';
			}
			print ' </td>
	        <td class="Data" align="right" >&nbsp;';
			if ($IDtype == $rs->fields(ID)) {
				print '&nbsp;<input size="15" name="noAmt" value="' . $rs->fields(jumBlnP) . '" >';
			} else {
				print '&nbsp;' . $rs->fields(jumBlnP) . '';
			}
			print '</td>
      <td class="Data" align="center" width="5%">&nbsp;<a href="' . $sFileName . '?IDtype=' . $rs->fields(ID) . '&ID=' . $ID . '&code=2" title="kemaskini"><img src="b_edit.png"></a> <input size="7" type="hidden" name="IDtype" value="' . $IDtype . '" ><input size="7" type="hidden" name="ID" value="' . $ID . '" ></td>
      <td class="Data" align="center"  width="5%">&nbsp;<a href="' . $sFileNameDel . '?IDtype=' . $rs->fields(ID) . '&ID=' . $ID . '&code=1" title="Hapus" onClick="if(!confirm(\'Adakah ada pasti untuk hapus file ini?\')) {return false} else {window.Edittrans.submit();};"><img src="b_drop.png"></td>
      <td class="Data" align="center" width="5%">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="edit" value="edit" />';
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
<p>&nbsp;</p> '; ?>
</body>

</html>