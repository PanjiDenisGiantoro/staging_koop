<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	Edit_memberStmtPotongan.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/
session_start();
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
$title     = "Kemaskini Potongan Pembiayaan Bulanan";

$sFileName = "?vw=Edit_memberStmtPotongan&mn=$mn";
$sFileNameDel = "?vw=Edit_memberStmtPotongan&mn=$mn";
$sFileRef  = "?vw=Edit_memberStmtPotonganPokok&mn=$mn";
$sActionFileName = "?vw=Edit_memberStmtPotongan&mn=$mn&ID=$ID";

$IDName = get_session("Cookie_userName");

$ID = $_REQUEST['ID'];
$code = $_REQUEST['code'];
$edit = $_POST['edit'];
if (!isset($mth)) $mth	= date("n");
if (!isset($yr)) $yr	= date("Y");
if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");

$yrmthNow = sprintf("%04d%02d", $yr, $mth);
$yymm = $yy . $mm;

//--- Prepare ptj type
$ptjList = array();
$ptjVal  = array();
$GetPtj = ctGeneral("", "U");
if ($GetPtj->RowCount() <> 0) {
	while (!$GetPtj->EOF) {
		array_push($ptjList, $GetPtj->fields(name));
		array_push($ptjVal, $GetPtj->fields(ID));
		$GetPtj->MoveNext();
	}
}

if ($code == 2) {
	$ID = $_REQUEST['ID'];

	$sSQL = "select a.*, b.priority from potbulan a, general b
		 WHERE a.ptjID = b.ID AND userID = " . tosql($ID, "Text") . "";
	$rs = &$conn->Execute($sSQL);

	$sSQL2 = "SELECT	DISTINCT a.*, b.*
		  FROM 	users a, userdetails b
		  WHERE a.UserID =" . tosql($ID, "Text") . "
		  AND a.UserID = b.UserID";

	$rs1 = &$conn->Execute($sSQL2);
}

if ($edit) {
	$updatedDate = date("Y-m-d H:i:s");
	$IDtype = $_POST['IDtype'];
	$pymt = $_POST['noAmt'];
	$lastyrmthPymt = $_POST['lastyrmthPymt'];
	$ptjID = $_POST['ptjID'];
	$Fee = $_POST['WajibP'];
	$ID = $_REQUEST['ID'];

	// Get current values from the database
	$sSQLCurrent = "SELECT yrmth, yearStart, monthStart FROM potbulan WHERE ID = '$IDtype'";
	$currentResult = $conn->Execute($sSQLCurrent);
	$currentRow = $currentResult->fields;

	// Determine new values or use existing ones
	$yrmthStart = !empty($_POST['yrmthStart']) ? $_POST['yrmthStart'] : $currentRow['yrmth'];
	$year = substr($yrmthStart, 0, 4);
	$month = substr($yrmthStart, 4, 2);

	// Update potbulan for other categories
	$sSQLUpd = "UPDATE potbulan SET " .
		"jumBlnP = '$pymt', " .
		"yrmth = '$yrmthStart', " .
		"yearStart = '$year', " .
		"monthStart = '$month', " .
		"lastyrmthPymt = '$lastyrmthPymt', " .
		"ptjID = '$ptjID' " .
		"WHERE ID = '$IDtype'";

	// Execute the update query
	$rsUpd = $conn->Execute($sSQLUpd);

	// Update userdetails
	$sSQLUpd2 = "UPDATE userdetails SET " .
		"monthFee = '$Fee' " .
		"WHERE userID = '$ID'";
	$rsUpd2 = $conn->Execute($sSQLUpd2);

	$strActivity = $_POST['Submit'] . 'Kemaskini Potongan Gaji Anggota - ' . $ID;
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

	print '<script>alert("Kemaskini Potongan Gaji Berjaya!");
            window.location.href = "' . $sActionFileName . '";
          </script>';
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
	$sSQLdel = "delete from potbulan Where ID =" . $IDtype . "";
	$rsdel = &$conn->Execute($sSQLdel);

	$sSQLdel2 = "delete from potbulanlook Where potID =" . $IDtype . "";
	$rsdel2 = &$conn->Execute($sSQLdel2);

	$strActivity = $_POST['Submit'] . 'Hapus Potongan Gaji Anggota - ' . $ID;
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

	print '<script>alert("Potongan Gaji Berjaya Dihapuskan !");</script>';
}

$sSQL = "select * from potbulan 	 
		 WHERE  userID = " . tosql($ID, "Text") . "
		 AND status IN (1)";

$rs = &$conn->Execute($sSQL);

$sSQL2 = "SELECT	DISTINCT a.*, b.*
		  FROM 	users a, userdetails b
		  WHERE a.UserID =" . tosql($ID, "Text") . "
		  AND a.UserID = b.UserID";

$rs1 = &$conn->Execute($sSQL2);

$totalJumP = getJumlah($ID, $yrmthNow);
// $totalJumY = dlookup("userdetails", "monthFee", "userID=" . tosql($ID, "Text"));
$jumALL = $totalJumP;

$gaji_pokok = dlookup("userstates", "amt", "userID=" . tosql($ID, "Text"));
$jumlah_nkpg = ($gaji_pokok * 0.60) - $jumALL;
?>

<head>
	<title>iKOOP</title>
</head>

<body>

	<?php
	print '<div class="table-responsive">
<form id="Edittrans" name="Edittrans" method="post" action="">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="' . $StartRec . '">
<input type="hidden" name="by" value="' . $by . '">

<table width="100%" >
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Nama Anggota :</td>
      <td><b>' . $rs1->fields(name) . '</b></td>
    </tr>
    <tr>
      <td width="20%">Nomor Anggota :</td>
      <td><b>' . $ID . '</b></td>
    </tr>
	<tr>
      <td>No Kartu Identitas :</td>
      <td><b>' . $rs1->fields(newIC) . '</b></td>
    </tr>
	<tr>
      <td>Cabang/Zona :</td>
      <td><b>' . dlookup("general", "name", "ID=" . tosql($rs1->fields(departmentID), "Text")) . '</b></td>
    </tr>
	<tr><td colspan="2"><hr class="1px"></td></tr>
	<tr>
      <td>Gaji Pokok :</td>
      <td><b>RP ' . number_format($gaji_pokok, 2) . '</b></td>
    </tr>
	<tr>
      <td>Potongan :</td>
      <td><b>RP ' . number_format($jumALL, 2) . '</b></td>
    </tr>
	<tr>
      <td>Jumlah NKPG :</td>
      <td><b>RP ' . number_format($jumlah_nkpg, 2) . '</b></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr>
		<td colspan="2"><input type="button" class="btn btn-primary" value="Kode PTJ" onclick="window.open(\'generalAddUpdate.php?action=tambah&cat=U&sub=\', \'newwindow\', \'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;
		<input type="button" class="btn btn-primary" value="Tambah Potongan Baru" onclick="window.open(\'addNewPot.php?ID=' . $ID . '\', \'newwindow\', \'top=\' + ((window.innerHeight / 2) - (500 / 2)) + \',left=\' + ((window.innerWidth / 2) - (950 / 2)) + \',width=950,height=200,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">

	</tr>
	<tr><td><br/></td></tr>
  </table>

<table class="table table-striped table-sm">
<tr class="table-primary">
      <td nowrapalign="center"><b>Bil</b></td>
      <td nowrap align="center"><b>Mula Potongan<br/>(Tahun/Bulan)</b></td>
	  <td nowrap align="center"><b>Akhir Potongan<br/>(Tahun/Bulan)</b></td>
	  <td nowrap align="center"><b>Baki Bulan</b></td>
      <td nowrap align="left"><b>Jenis/Kod Potongan</b></td>
	  <td nowrap align="right"><b>Potongan<br/>Bulanan (RP)</b></td>
      <td nowrap align="center"><b>Bond /<br/>Rujukan</b></td>
	  <td nowrap align="center"><b>PTJ</b></td>
	  <td nowrap align="center"><b>Keutamaan</b></td>
      <td nowrap align="center"><b>Status</b></td>	  
      <td nowrap align="center" colspan="4"><b>Kemaskini</b></td>
    </tr>';

	$jumlah = array(
		'aktif' => array(),
		'tamat' => array(),
		'standby' => array()
	);

	if ($rs->RowCount() <> 0) {
		$count = 1;
		while (!$rs->EOF) {
			$sSQL3 = "select * from general
		 WHERE  ID = " . $rs->fields(loanType) . "
  		 ORDER BY ID";
			$rs3 = &$conn->Execute($sSQL3);

			$monthFee 		= $rs1->fields(monthFee);
			$syerbulan 		= $rs1->fields(unitShare);

			$yearStart = $rs->fields['yearStart'];
			$monthStart = $rs->fields['monthStart'];
			$monthStart1 = str_pad($monthStart, 2, '0', STR_PAD_LEFT);
			$yrmthStart = $yearStart . $monthStart1;

			$lastyrmthPymt = $rs->fields(lastyrmthPymt);

			$category = dlookup("general", "category", "ID=" . tosql($rs->fields(loanType), "Number"));

			//kategori pembiayaan
			if ($category == "C") {
				$c_Deduct = dlookup("general", "c_Deduct", "ID=" . tosql($rs->fields(loanType), "Number"));
				$priority = dlookup("general", "priority", "ID=" . tosql($c_Deduct, "Number"));
			} else {
				$priority = dlookup("general", "priority", "ID=" . tosql($rs->fields(loanType), "Number"));
			}

			// Tambah sebulan
			$nextMonthTimestamp = strtotime("+1 month", strtotime($yrmthNow . "01")); // Tambah 1 bulan
			$yrmthNext = date("Ym", $nextMonthTimestamp); // Format kembali ke format Y-m

			//cek kalau dia pembiayaan, dia akan cek pulak yrmth dengan lastyrmthpymt tu 
			if ($category == "C") {
				if ($rs->fields(yrmth) == $yrmthNext) {
					$status = '<div class="text-warning"><b>Standby</b></div>';
					$jumlah['standby'][] = $rs->fields['jumBlnP'];
				} else {
					if ($lastyrmthPymt >= $yymm) {
						$status = '<div class="text-primary"><b>Aktif</b></div>';
						$jumlah['aktif'][] = $rs->fields['jumBlnP'];
					} else {
						$status = '<div class="text-danger"><b>Tamat</b></div>';
						$jumlah['tamat'][] = $rs->fields['jumBlnP'];
					}
				}
			} else if ($lastyrmthPymt >= $yymm) {
				$status = '<div class="text-primary"><b>Aktif</b></div>';
				$jumlah['aktif'][] = $rs->fields['jumBlnP'];
			} else {
				$status = '<div class="text-danger"><b>Tamat</b></div>';
				$jumlah['tamat'][] = $rs->fields['jumBlnP'];
			}


			// cek bape bulan lagi nak habis
			$yrr = substr($lastyrmthPymt, 0, 4);
			$mthh = substr($lastyrmthPymt, 4, 2);

			$currentYear = date('Y');
			$currentMonth = date('m');

			$yearDifference = $yrr - $currentYear;
			$monthDifference = $mthh - $currentMonth;

			// Kira beza bulan menggunakan DateInterval
			$diffInMonths = ($yearDifference * 12) + $monthDifference;

			print '
			<tr>
			  <td class="Data" align="center">' . $count . '</td>
			  <td class="Data" align="center">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input size="6" maxlength="6" class="form-control-sm" name="yrmthStart" value="' . $yrmthStart . '">';
			} else {
				print $yrmthStart;
			}
			print '</td>
			  <td class="Data" align="center">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input size="6" maxlength="6" class="form-control-sm" name="lastyrmthPymt" value="' . $rs->fields(lastyrmthPymt) . '">';
			} else {
				print $rs->fields(lastyrmthPymt);
			}
			print '</td>
			  <td class="Data" align="center">' . $diffInMonths . '</td>';
			if ($category == "C") {
				print '<td class="Data"><a href="' . $sFileRef . '&ID=' . tohtml($rs->fields(ID)) . '">' . $rs3->fields(name) . ' - ' . $rs3->fields(code) . '</a></td>';
			} else {
				print '<td class="Data">' . $rs3->fields(name) . ' - ' . $rs3->fields(code) . '</td>';
			}
			print '<td class="Data" align="right" >';
			if ($IDtype == $rs->fields(ID)) {
				print '<input size="15" class="form-control-sm" name="noAmt" value="' . $rs->fields(jumBlnP) . '" >';
			} else {
				print number_format($rs->fields(jumBlnP), 2);
			}
			print '</td>
			  <td class="Data" nowrap align="center">' . $rs->fields(bondNo) . '</td>
			  <td class="Data" align="center">';
			if ($IDtype == $rs->fields(ID)) {
				if (!isset($ptjID)) $ptjID = $rs->fields('ptjID');
				print '
				  <select class="form-selectx" name="ptjID">
					<option value="">- Semua -';
				for ($j = 0; $j < count($ptjList); $j++) {
					print '	<option value="' . $ptjVal[$j] . '" ';
					if ($ptjID == $ptjVal[$j]) print ' selected';
					print '>' . $ptjList[$j];
				}
				print '		</select>&nbsp;';
			} else {
				print dlookup("general", "name", "ID=" . tosql($rs->fields(ptjID), "Text"));
			}
			print '
			  </td>	  	  
			  <td class="Data" nowrap align="center">' . $priority . '</td>  
			  <td class="Data" nowrap align="center">' . $status . '</td>
			  <td class="Data" align="center" width="5%"><a href="' . $sFileName . '&IDtype=' . $rs->fields(ID) . '&ID=' . $ID . '&code=2" title="kemaskini"><i class="mdi mdi-lead-pencil text-primary" style="font-size: 1.4rem;"></i></a> <input size="7" type="hidden" name="IDtype" value="' . $IDtype . '" ><input size="7" type="hidden" name="ID" value="' . $ID . '" ></td>';

			if (($IDName == 'admin') or ($IDName == 'superadmin')) {
				print '  <td class="Data" align="center" valign="middle" width="5%"><a href="' . $sFileNameDel . '&IDtype=' . $rs->fields(ID) . '&ID=' . $ID . '&code=1" title="Hapus" onClick="if(!confirm(\'Adakah ada pasti untuk hapus file ini?\')) {return false} else {window.Edittrans.submit();};"><i class="fas fa-trash-alt text-danger" style="font-size: 1.1rem;"></i></td>';
			}
			print '   <td class="Data" align="center" valign="middle" width="5%">';
			if ($IDtype == $rs->fields(ID)) {
				print '<input type="submit" class="btn btn-sm btn-info" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="edit" value="edit" />';
			}
			print '</td>
			</tr>';
			$count++;
			$rs->MoveNext();
		}
	} else {
		print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt;" bgcolor="FFFFFF">
			<td colspan="12" align="center"><b>- Tiada Rekod </b></td>
		</tr>';
	}

	print '
		  </table>
		  <div><hr class="1px"></div>
		  <div>Jumlah Potongan Aktif : <b>RP ' . number_format(array_sum($jumlah['aktif']), 2) . '</b></div>
		  <div>Jumlah Potongan Tamat : <b>RP ' . number_format(array_sum($jumlah['tamat']), 2) . '</b></div>
		  <div><hr class="1px"></div>
		  <div><b>Jumlah Potongan : RP ' . number_format(array_sum($jumlah['aktif']) + array_sum($jumlah['tamat']), 2) . '</b></div>
		  <div><hr class="1px"></div>
		</form></div>
<p>&nbsp;</p> '; ?>
</body>

</html>