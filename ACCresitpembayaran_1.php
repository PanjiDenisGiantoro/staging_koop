<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: resit.php
 *			Date 		: 19/10/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCresitList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;RESIT AKAUN</b>';

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display = 0;
if ($no_resit && $action == "view") {
	$sql = "SELECT * FROM resitacc WHERE no_resit = '" . $no_resit . "'";

	$rs 			= $conn->Execute($sql);
	$no_resit 		= $rs->fields(no_resit);
	$tarikh_resit 	= $rs->fields(tarikh_resit);
	$tarikh_resit 	= substr($tarikh_resit, 8, 2) . "/" . substr($tarikh_resit, 5, 2) . "/" . substr($tarikh_resit, 0, 4);

	$kod_bank 		= $rs->fields(kod_bank);
	$bankparent 	= dlookup("generalacc", "parentID", "ID=" . $kod_bank);

	$kerani 		= $rs->fields(kerani);
	$keterangan 	= $rs->fields(keterangan);
	$maklumat 		= $rs->fields(maklumat);
	$tarikh_resit 	= toDate("d/m/y", $rs->fields(tarikh_resit));
	$nama 			= $rs->fields(name);
	$batchNo 		= $rs->fields(batchNo);
	$accountNo 		= $rs->fields(accountNo);
	$kod_project 	= $rs->fields(kod_project);
	$keterangan		= $rs->fields(keterangan);
	$diterima_drpd	= $rs->fields(diterima_drpd);
	$Cheque			= $rs->fields(Cheque);
	$cara_bayar		= $rs->fields(cara_bayar);
	$masterAmt		= $rs->fields(pymtAmt);

	$tarikhbayar 	= $rs->fields(tarikh);
	$tarikhbayar 	= substr($tarikhbayar, 8, 2) . "/" . substr($tarikhbayar, 5, 2) . "/" . substr($tarikhbayar, 0, 4);
	$tarikhbayar 	= toDate("d/m/y", $rs->fields(tarikh));

	// kod carta akaun
	//-----------------
	$sql2 = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = '" . $no_resit . "' ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
	if ($rsDetail->RowCount() < 1)
		$noTran = true;
} elseif ($action == "new") {
	$getNo = "SELECT MAX(CAST(right(no_resit,6) AS SIGNED INTEGER )) AS nombor FROM resitacc";
	$rsNo = $conn->Execute($getNo);
	$tarikh_resit = date("d/m/Y");
	$tarikhbayar = date("d/m/Y");
	if ($rsNo) {
		$nombor = intval($rsNo->fields(nombor)) + 1;
		$nombor = sprintf("%06s",  $nombor);
		$no_resit = 'OR' . $nombor;
	} else {
		$no_resit = 'OR000001';
	}
}

if (!isset($tarikh_resit)) $tarikh_resit = date("d/m/Y");
if (!isset($tarikhbayar)) $tarikhbayar = date("d/m/Y");

if ($perkara2) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$createdBy 	= get_session("Cookie_userName");
	$createdDate = date("Y-m-d H:i:s");
	$accountNo = $perkara2; //perkara to deduct id value

	$addminus = 1;
	$cajAmt = 0.0;

	if ($pymtAmt == '')
		$pymtAmt = '0.0';
	$sSQL	= "INSERT INTO transactionacc (" .

		"docNo," .
		"docID," .
		"batchNo," .
		"deductID," .
		"kod_project," .
		"kod_jabatan," .
		"addminus," .
		"pymtID," .
		"pymtAmt," .
		"desc_akaun," .
		"status," .
		"isApproved," .
		"approvedDate," .
		"updatedBy," .
		"updatedDate	," .
		"createdBy," .
		"createdDate) " .

		" VALUES (" .
		"'" . $no_resit . "', " .
		"'" . 4 . "', " .
		"'" . $batchNo . "', " .
		"'" . $accountNo . "', " .
		"'" . $kod_project . "', " .
		"'" . $kod_jabatan . "', " .
		"'" . $addminus . "', " .
		"'" . 66 . "', " .
		"'" . $kredit2 . "', " .
		"'" . $desc_akaun2 . "', " .
		"'" . $status . "', " .
		"'" . $isApproved . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $createdBy . "', " .
		"'" . $createdDate . "')";

	if ($display) print $sSQL . '<br />';
	else {

		$rs = &$conn->Execute($sSQL);
		print '<script>
		window.location = "?vw=ACCresitpembayaran&mn=' . $mn . '&action=view&no_resit=' . $no_resit . '";
		</script>';
	}
}

if ($action == "Hapus") {
	if (count($pk) > 0) {
		$sWhere = "";
		foreach ($pk as $val) {
			$sSQL = '';
			$sWhere = "ID='" . $val . "'";
			$sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCresitpembayaran&mn=' . $mn . '&action=view&no_resit=' . $no_resit . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $perkara || $desc_akaun || $projecting || $jabatan1) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$sSQL = "";
	$sWhere = "";
	$sWhere = "no_resit='" . $no_resit . "'";
	$tarikh_resit = saveDateDb($tarikh_resit);
	$tarikhbayar =	saveDateDb($tarikhbayar);
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE resitacc SET " .
		"no_resit='" . $no_resit . "'," .
		"tarikh_resit='" . $tarikh_resit . "'," .
		"batchNo='" . $batchNo . "'," .
		"cara_bayar='" . $cara_bayar . "'," .
		"tarikh='" . $tarikhbayar . "'," .
		"kod_bank='" . $kod_bank . "'," .
		"Cheque='" . $Cheque . "'," .
		"kerani='" . $kerani . "'," .
		"keterangan='" . $keterangan . "'," .
		"diterima_drpd='" . $diterima_drpd . "'," .
		"kod_project='" . $kod_project . "'," .
		"maklumat='" . $maklumat . "'," .
		"pymtAmt='" . $masterAmt . "'," .
		"StatusID_Pymt='0'," .
		"createdDate='" . $updatedDate . "'," .
		"createdBy='" . $updatedBy . "'," .
		"updatedDate='" . $updatedDate . "'," .
		"updatedBy='" . $updatedBy . "'";

	$sSQL = $sSQL . $sWhere;

	$sSQL1 = "";
	$sWhere1 = "";
	$sWhere1 = "docNo='" . $no_resit . "' AND addminus='" . 0 . "'";
	$sWhere1 = " WHERE (" . $sWhere1 . ")";
	$sSQL1	= "UPDATE transactionacc SET " .
		"deductID='" . $kod_bank . "'," .
		"MdeductID='" . $bankparent . "'," .
		"batchNo='" . $batchNo . "'," .
		"pymtAmt='" . $masterAmt . "'";

	$sSQL1 = $sSQL1 . $sWhere1;

	$sSQL2 = "";
	$sWhere2 = "";
	$sWhere2 = "docNo='" . $no_resit . "'";
	$sWhere2 = " WHERE (" . $sWhere2 . ")";
	$sSQL2	= "UPDATE transactionacc SET " .
		"tarikh_doc='" . $tarikh_resit . "'";

	$sSQL2 = $sSQL2 . $sWhere2;

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);
	$rs = &$conn->Execute($sSQL2);
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if (count($perkara) > 0) {
		foreach ($perkara as $id => $value) {

			$accountNo = $value;
			if ($debit[$id]) {
				$pymtAmt = $debit[$id];
				$addminus = 0;
			} else {
				$pymtAmt = $kredit[$id];
				$addminus = 1;
			}

			$sSQL = "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transactionacc SET " .
				"batchNo= '" . $batchNo . "'" .
				",deductID= '" . $accountNo . "'" .
				",pymtAmt= '" . $pymtAmt . "'" .
				",updatedDate= '" . $updatedDate . "'" .
				",updatedBy= '" .  $updatedBy . "'";

			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	//////////////////////////////////////////////////////////
	if (count($kod_akaunM) > 0) {
		foreach ($kod_akaunM as $id => $value) {

			$MdeductID = $value;
			if ($debit[$id]) {
				$pymtAmt = $debit[$id];
				$addminus = 0;
			} else {
				$pymtAmt = $kredit[$id];
				$addminus = 1;
			}
			$sSQL = "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transactionacc SET " .

				"batchNo= '" . $batchNo . "'," .
				"MdeductID= '" . $MdeductID . "'," .
				"updatedDate= '" . $updatedDate . "'," .
				"updatedBy= '" .  $updatedBy . "'";

			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	//////////////////JABATAN///////////////////////////////////////////

	if (count($jabatan1) > 0) {
		foreach ($jabatan1 as $id => $value) {

			$kod_jabatan = $value;
			if ($debit[$id]) {
				$pymtAmt = $debit[$id];
				$addminus = 0;
			} else {
				$pymtAmt = $kredit[$id];
				$addminus = 1;
			}
			//$no_ruj = $ruj[$id];
			$sSQL = "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transactionacc SET " .

				"batchNo= '" . $batchNo . "'" .
				",kod_jabatan= '" . $kod_jabatan . "'" .
				",addminus= '" . $addminus . "'" .
				",pymtAmt= '" . $pymtAmt . "'" .
				",updatedDate= '" . $updatedDate . "'" .
				",updatedBy= '" .  $updatedBy . "'";

			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	///////////////////////DESC AKAUN//////////////////////////////////////
	if (count($desc_akaun) > 0) {
		foreach ($desc_akaun as $id => $value) {

			$desc_akaun = $value;
			if ($debit[$id]) {
				$pymtAmt = $debit[$id];
				$addminus = 0;
			} else {
				$pymtAmt = $kredit[$id];
				$addminus = 1;
			}
			$sSQL = "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transactionacc SET " .
				"batchNo=" . tosql($batchNo, "Number") .
				",desc_akaun=" . tosql($desc_akaun, "Text") .
				",addminus=" . $addminus .
				",pymtAmt=" . tosql($pymtAmt, "Number") .
				",updatedDate=" . tosql($updatedDate, "Text") .
				",updatedBy=" . tosql($updatedBy, "Text");


			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	/////////////////////////////////////////////////////////

	if (count($projecting) > 0) {
		foreach ($projecting as $id => $value) {

			$kod_project = $value;
			if ($debit[$id]) {
				$pymtAmt = $debit[$id];
				$addminus = 0;
			} else {
				$pymtAmt = $kredit[$id];
				$addminus = 1;
			}
			$sSQL = "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transactionacc SET " .
				"batchNo= '" . $batchNo . "'" .
				",kod_project= '" . $kod_project . "'" .
				",addminus= '" . $addminus . "'" .
				",pymtAmt= '" . $pymtAmt . "'" .
				",updatedDate= '" . $updatedDate . "'" .
				",updatedBy= '" .  $updatedBy . "'";



			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////	
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCresitpembayaran&mn=' . $mn . '&action=view&no_resit=' . $no_resit . '";
	</script>';
	}
}

//pilihan simpan
elseif ($action == "Simpan" || $simpan) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$tarikh_resit = saveDateDb($tarikh_resit);
	$tarikhbayar =	saveDateDb($tarikhbayar);
	$sSQL = "";
	$sSQL	= "INSERT INTO resitacc (" .
		"no_resit, " .
		"tarikh_resit, " .
		"batchNo, " .
		"cara_bayar, " .
		"kod_siri, " .
		"tarikh, " .
		"kod_bank, " .
		"Cheque, " .
		"kerani, " .
		"keterangan, " .
		"diterima_drpd, " .
		"kod_project, " .
		"maklumat, " .
		"resit_img, " .
		"pymtAmt, " .
		"StatusID_Pymt, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .

		" VALUES (" .

		"'" . $no_resit . "', " .
		"'" . $tarikh_resit . "', " .
		"'" . $batchNo . "', " .
		"'" . $cara_bayar . "', " .
		"'" . $kod_siri . "', " .
		"'" . $tarikhbayar . "', " .
		"'" . $kod_bank . "', " .
		"'" . $Cheque . "', " .
		"'" . $kerani . "', " .
		"'" . $keterangan . "', " .
		"'" . $diterima_drpd . "', " .
		"'" . $kod_project . "', " .
		"'" . $maklumat . "', " .
		"'" . $resit_img . "', " .
		"'" . $masterAmt . "', " .
		"'" . 0 . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "')";

	$sSQL1 = "";
	$sSQL1	= "INSERT INTO transactionacc (" .

		"docNo," .
		"tarikh_doc," .
		"docID," .
		"batchNo," .
		"deductID," .
		"kod_project," .
		"kod_jabatan," .
		"addminus," .
		"pymtID," .
		"pymtAmt," .
		"desc_akaun," .
		"status," .
		"isApproved," .
		"approvedDate," .
		"updatedBy," .
		"updatedDate	," .
		"createdBy," .
		"createdDate) " .

		" VALUES (" .
		"'" . $no_resit . "', " .
		"'" . $tarikh_resit . "', " .
		"'" . 4 . "', " .
		"'" . $batchNo . "', " .
		"'" . $kod_bank . "', " .
		"'" . $kod_project . "', " .
		"'" . $kod_jabatan . "', " .
		"'" . 0 . "', " .
		"'" . 66 . "', " .
		"'" . $masterAmt . "', " .
		"'" . $desc_akaun2 . "', " .
		"'" . $status . "', " .
		"'" . $isApproved . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "')";

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);

	$getMax = "SELECT MAX(CAST(right(no_resit,6) AS SIGNED INTEGER )) AS no FROM resitacc";
	$rsMax = $conn->Execute($getMax);
	$max = sprintf("%06s", $rsMax->fields(no));
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCresitpembayaran&mn=' . $mn . '&action=view&add=1&no_resit=OR' . $max . '";
	</script>';
	}
}

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<div class="table-responsive"><form name="MyForm" action="?vw=ACCresitpembayaran&mn=' . $mn . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';



print $strTemp;
print
	'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>Nomor Voucher</td>
				<td valign="top">:</td>
				<td>
					<input  name="no_resit" value="' . $no_resit . '" type="text" size="20" maxlength="50" class="form-controlx" readonly/>
				</td>
			</tr>

			<tr>
				<td>Batch</td>
				<td valign="top">:</td>
				<td>' . selectbatch($batchNo, 'batchNo') . '</td>
			</tr>

			<tr>
				<td>Bank</td>
				<td valign="top">:</td>
				<td>' . selectbanks($kod_bank, 'kod_bank') . '</td>
			</tr>

		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh Resit</td>
				<td valign="top">:</td>
				<td><input class="form-controlx" name="tarikh_resit" value="' . $tarikh_resit . '" type="text" size="20" maxlength="10" /></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td colspan="3"><hr size="1px" /></td></tr>';


print '
<tr colspan="3">
	<td valign="top"><input name="j" type="hidden" value="tiada">
		<table border="0" cellspacing="1" cellpadding="2">
			
			<tr>
				<td valign="top">Diterima daripada </td>
				<td valign="top">:</td>
				<td>
			  	<input name="diterima_drpd"  value="' . $diterima_drpd . '" size="50" maxlength="100"  class="form-control-sm"  />
			  </td>
			</tr>

			<tr>
				<td valign="top">Keterangan</td>
				<td valign="top">:</td>
				<td>
					<textarea name="keterangan" cols="50" rows="4" class="form-control-sm">' . $keterangan . '</textarea>
				</td>
			</tr>
	</table>
	</td>

	<td valign="top">&nbsp;</td>
	<td width="48%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Cara Bayaran</td><td valign="top">:</td>
				<td>' . selectbayar($cara_bayar, 'cara_bayar') . '</td>
			</tr>

			<tr>
				<td valign="top" align="right">Cheque Nombor</td><td valign="top">:</td>
				<td><input class="form-control-sm" name="Cheque" value="' . $Cheque . '" type="text" size="20" maxlength="10" /></td>
			</tr>

			<tr>
				<td valign="top" align="right">Tanggal Pembayaran</td><td valign="top">:</td>
				<td><input  class="form-control-sm"name="tarikhbayar" value="' . $tarikhbayar . '" type="text" size="20" maxlength="10" /></td>
			</tr>
			
			<tr>
				<td valign="top" align="right">Master Jumlah (Rp)</td><td valign="top">:</td>
				<td><input class="form-control-sm" value="' . $masterAmt . '" type="text" size="20" maxlength="10"/></td>
			</tr>
			
		</table>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
</tr>';
//----------
if ($action == "view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $no_resit . "'"))) {
	print '
	<tr>
			<td align= "right" colspan="3">';
	if (!$add) print '
			<input type="button" name="add" value="Tambah" class="btn btn-sm btn-secondary" onClick="window.location.href=\'?vw=ACCresitpembayaran&mn=' . $mn . '&action=' . $action . '&no_resit=' . $no_resit . '&add=1\';">';
	else print '
			<input type="button" name="action" value="Simpan" class="btn btn-sm btn-secondary" onclick="CheckField(\'Kemaskini\')">';
	print '&nbsp;<input type="submit" name="action" value="Hapus" class="btn btn-sm btn-danger">
		</td>
	</tr>';
}
//----------
print
	'<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="4" width="50%" class="table table-sm table-striped">
<tr class="table-success">
				<td nowrap="nowrap">Bil.</td>
				<td nowrap="nowrap">Akaun</td>
				<td nowrap="nowrap">Projek</td>
				<td nowrap="nowrap">Jabatan</td>
				<td nowrap="nowrap">Keterangan</td>
				<td nowrap="nowrap" align="right" >Jumlah (RP)</td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

if ($action == "view") {
	$i = 0;
	while (!$rsDetail->EOF) {

		$id = $rsDetail->fields(ID);
		$perkara = $rsDetail->fields(deductID);
		$kod_akaunM = dlookup("generalacc", "parentID", "ID=" . $perkara);
		$namaparent = dlookup("generalacc", "name", "ID=" . $kod_akaun);

		$projecting = $rsDetail->fields(kod_project);
		$jabatan1 = $rsDetail->fields(kod_jabatan);
		$desc_akaun =	$rsDetail->fields(desc_akaun);

		$a_Keterangan 		= dlookup("generalacc", "code", "ID=" . $perkara);
		$kod_akaun 			= dlookup("generalacc", "c_Panel", "ID=" . $perkara);
		$kod_project 		= dlookup("generalacc", "name", "ID=" . $projecting);
		$kod_jabatan 		= dlookup("generalacc", "name", "ID=" . $jabatan1);
		$kredit 			= $rsDetail->fields(pymtAmt);
		print
			'<tr>
				<td class="Data">&nbsp;' . ++$i . '.</td>	

				<td style="width:12px;" class="Data" nowrap="nowrap">' . strSelect3($id, $perkara, "", "150px") . '&nbsp;
				<input class="form-control-sm" name="kod_akaunM[' . $id . ']" type="hidden" size="10" maxlength="10" value="' . $kod_akaunM . '"/>
				</td>

				<td class="Data" nowrap="nowrap">' . strproject($id, $projecting, "", "250px") . '&nbsp;</td>

				<td class="Data" nowrap="nowrap">' . strjabatan($id, $jabatan1) . '&nbsp;</td>

				<td class="Data" nowrap="nowrap">
					<input name="desc_akaun[' . $id . ']" type="text" size="35" maxlength="35" class="form-control-sm" value="' . $desc_akaun . '"/>&nbsp;
				</td>				

				<td class="Data" align="right">
					<input name="kredit[' . $id . ']" type="text" size="10" maxlength="10" value="' . $kredit . '" class="form-control-sm" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" name="pk[]" class="form-check-input" value="' . $id . '">&nbsp;</td>

			</tr>';
		$totalKt += $kredit;
		$kredit = '';
		$rsDetail->MoveNext();
	}
}

$strDeductIDList = deductListb2(1);
$strDeductCodeList = deductListb2(2);
$strDeductNameList = deductListb2(3);
$name = 'perkara2';

$strSelect = '<select name="' . $name . '" class="form-select-sm" style="width: 150px;">
			 <option value="">- Pilih -';
for ($i = 0; $i < count($strDeductIDList); $i++) {
	$strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
	if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
	$strSelect .=  '>' . $strDeductCodeList[$i] . '&nbsp;&nbsp;' . $strDeductNameList[$i] . '';
}
$strSelect .= '</select>';

if ($add) {
	print	   '
			<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>	

				<td class="Data">' . $strSelect . '
				<input name="kod_akaunM2" type="hidden" size="10" maxlength="10" value="' . $kod_akaunM2 . '" class="form-control-sm"/>
				</td>

				<td class="Data" size="20" maxlength="10">' . selectproject($kod_project, 'kod_project') . '</td>
				<td class="Data" size="20" maxlength="10">' . selectjabatan($kod_jabatan, 'kod_jabatan') . '</td>

				<td class="Data" align="left">
					<input name="desc_akaun2" type="text" size="35" class="form-control-sm" maxlength="100" value="' . $desc_akaun2 . '" align="right"/>&nbsp;
				</td>

				<td class="Data" align="right">
					<input type="hidden" name="ruj2" val="0">
					<input name="kredit2" type="text" size="10" class="form-control-sm" maxlength="10" value="' . $kredit2 . '" />&nbsp;
				</td>

				<td class="Data" align="right"></td>
			</tr>';
}
//bahagian bawah skali
if ($totalKt <> 0) {
	$clsRM->setValue($totalKt);
	$strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}

$kerani = get_session('Cookie_fullName');

print 		'<tr>
				<td class="Data" colspan="5" align="right"><b>Jumlah</b></td>
				<td class="Data" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;</b></td>
				<td class="Data" align=""><b>&nbsp;</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">

	<tr><td colspan="3" nowrap="nowrap">Jumlah Dalam Perkataan<br /><input name="" size="100" maxlength="100" class="form-control-sm" value="' . $strTotal . '" readonly>
<input class="Data" type="hidden" name="masterAmt" value="' . $totalKt . '">
					<input class="Data" type="hidden" name="bankparent" value="' . $bankparent . '">
			</td></tr>

			<tr><td nowrap="nowrap">Dimasukkan Oleh</td><td valign="top">:</td><td><input class="form-control-sm" name="kerani" value="' . $kerani . '" type="text" size="20" maxlength="15"/></td></tr>
			<tr><td nowrap="nowrap" valign="top">Catatan</td><td valign="top">:</td><td valign="top"><textarea name="maklumat" class="form-control-sm" cols="50" rows="4">' . $maklumat . '</textarea></td></tr>
		</table>
	</td>
</tr>';
print '<input name="kod_caw" type="hidden" value="321"><input name="no_siri" type="hidden" value="S112"><input name="tarikh" type="hidden" value="01/10/2006"></tr>';

if ($no_resit) {
	$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
	print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-sm btn-warning" onClick= "print_(\'ACCResitPrintCustomer.php?id=' . $no_resit . '\')">&nbsp;
	<input type="button" name="action" value="' . $straction . '" class="btn btn-sm btn-secondary" onclick="CheckField(\'' . $straction . '\')">';
	if ($straction == 'Simpan') print '
	<input type="hidden" name="simpan" value="1">';
	print '
	</td>
</tr>';
}

$strTemp = '
	</table>
</form></div>
</div>';

print $strTemp;
print '
<script language="JavaScript">
	function print_(url) {
		window.open(url,"pop","top=100, left=100, width=600, height=400, scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");					
	}

	function CheckField(act) {
	    e = document.MyForm;
		count = 0;	
		for(c=0; c<e.elements.length; c++) {
		  //if(!e.debit2.value == \'\') alert(e.nama_anggota.value);
		  if(e.elements[c].name=="no_anggota" && e.elements[c].value==\'\') {
			alert(\'Sila pilih anggota!\');
            count++;
		  }
		  
		  if(act == \'Kemaskini\') {
  
		  if(e.elements[c].name=="kredit2" && e.elements[c].value==\'\') {
			alert(\'Ruang amaun perlu diisi!\');
            count++;
		  }
		  }

		  if(act == \'Simpan\') {
  
		  if(e.elements[c].name=="batchNo" && e.elements[c].value==\'\') 
		  	{
			alert(\'Ruang batch perlu diisi!\');
            count++;
		 	}
		  }
		
		}
		if(count==0) {
			e.submit();
		}
	}

</script>';
include("footer.php");
