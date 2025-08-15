<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCpurchase.php
 *			Date 		: 19/10/2006
 *			Keywords 	: disable, duplicate (to prevent user fault)
 *********************************************************************************/
// Import Select2 CSS and JavaScript
print '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCpurchaseList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;PURCHASE ORDER/PEMIUTANG</b>';

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
if (!isset($page))	$page = "pmtg";

$display = 0;
if ($purcNo && $action == "view") {
	$sql 				= "SELECT a.*, b.*
							FROM   cb_purchase a, generalacc b
							WHERE   a.companyID = b.ID 
							AND purcNo = '" . $purcNo . "'";
	$rs 				= $conn->Execute($sql);

	$purcNo 			= $rs->fields('purcNo');
	$tarikh_purc 		= $rs->fields('tarikh_purc');
	$tarikh_purc 		= substr($tarikh_purc, 8, 2) . "/" . substr($tarikh_purc, 5, 2) . "/" . substr($tarikh_purc, 0, 4);
	$kod_bank 			= $rs->fields('kod_bank');
	$keterangan 		= $rs->fields('keterangan');
	$disahkan 			= $rs->fields('disahkan');
	$disedia 			= $rs->fields('disedia');
	$disemak 			= $rs->fields('disemak');
	$tarikh_disedia 	= $rs->fields('tarikh_disedia');
	$tarikh_disedia 	= substr($tarikh_disedia, 8, 2) . "/" . substr($tarikh_disedia, 5, 2) . "/" . substr($tarikh_disedia, 0, 4);
	$tarikh_disemak		= $rs->fields('tarikh_disemak');
	$tarikh_disemak 	= substr($tarikh_disemak, 8, 2) . "/" . substr($tarikh_disemak, 5, 2) . "/" . substr($tarikh_disemak, 0, 4);
	$tarikh_disahkan	= $rs->fields('tarikh_disahkan');
	$tarikh_disahkan 	= substr($tarikh_disahkan, 8, 2) . "/" . substr($tarikh_disahkan, 5, 2) . "/" . substr($tarikh_disahkan, 0, 4);
	$description 		= $rs->fields('description');
	$nama 				= $rs->fields('name');
	$maklumat        	= $rs->fields('maklumat');
	$batchNo 			= $rs->fields('batchNo');
	$kod_project 		= $rs->fields('kod_project');
	$kod_jabatan 		= $rs->fields('kod_jabatan');
	$companyID        	= $rs->fields('companyID');
	$b_Baddress 		= $rs->fields('b_Baddress');
	$code 				= $rs->fields('code');
	$b_kodGL 			= $rs->fields('b_kodGL');

	//-----------------
	$sql2 		= "SELECT * FROM cb_purchaseinf WHERE docNo = '" . $purcNo . "' AND addminus IN (0) ORDER BY ID";
	$rsDetail 	= $conn->Execute($sql2);
	if ($rsDetail->RowCount() < 1)
		$noTran = true;
} elseif ($action == "new") {
	$getNo 	= "SELECT MAX(CAST(right(purcNo,6) AS SIGNED INTEGER)) AS nombor FROM cb_purchase";

	$rsNo 	= $conn->Execute($getNo);
	if ($rsNo) {
		$nombor = intval($rsNo->fields('nombor')) + 1;
		$nombor = sprintf("%06s", $nombor);
		$purcNo = 'PO' . $nombor;
	} else {
		$purcNo = 'PO000001';
	}
}

if (!isset($tarikh_purc)) $tarikh_purc = date("d/m/Y");
if (!isset($tarikh_purc)) $tarikh_purc = date("d/m/Y");

if ($perkara2) {
	$updatedBy 	 = get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");


	$deductID = $perkara2; //perkara to deduct id value

	if ($debit2) { // 2 field for money value
		$pymtAmt 	= $debit2;
		$addminus 	= 0;
		$cajAmt 	= 0.0;
	} else {
		$pymtAmt 	= $kredit2;
		$addminus 	= 1;
		$cajAmt 	= 0.0;
	}

	if ($pymtAmt == '')
		$pymtAmt = '0.0';
	$sSQL	= "INSERT INTO cb_purchaseinf (" .
		"docNo," .
		"batchNo," .
		"deductID," .
		"addminus," .
		"price," .
		"quantity," .
		"pymtID," .
		"pymtRefer," .
		"pymtAmt," .
		"desc_akaun," .
		"updatedBy," .
		"updatedDate," .
		"createdBy," .
		"createdDate) " .

		" VALUES (" .
		"'" . $purcNo . "', " .
		"'" . $batchNo . "', " .
		"'" . $deductID . "', " .
		"'" . $addminus . "', " .
		"'" . $price2 . "', " .
		"'" . $quantity2 . "', " .
		"'" . 66 . "', " .
		"'" . $companyID . "', " .
		"'" . $pymtAmt . "', " .
		"'" . $desc_akaun2 . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "')";


	if ($display) print $sSQL . '<br />';
	else {
		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Kemaskini Purchase Order - ' . $purcNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

		print '<script>
		window.location = "?vw=ACCpurchase&mn=' . $mn . '&action=view&purcNo=' . $purcNo . '";
		</script>';
	}
}

if ($action == "Hapus") {
	if (count($pk) > 0) {
		$sWhere = "";
		foreach ($pk as $val) {
			$sSQL 	= '';
			$sWhere = "ID='" . $val . "'";

			$docNo = dlookup("cb_purchaseinf", "docNo", $sWhere);

			$sSQL 	= "DELETE FROM cb_purchaseinf WHERE " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);

			$strActivity = $_POST['Submit'] . 'Hapus Kandungan Purchase Order - ' . $docNo;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCpurchase&mn=' . $mn . '&action=view&purcNo=' . $purcNo . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $perkara || $desc_akaun) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$tarikh_purc 	= saveDateDb($tarikh_purc);
	$tarikh_disedia = saveDateDb($tarikh_disedia);
	$sSQL 	= "";
	$sWhere = "";
	$sWhere = "purcNo='" . $purcNo . "'";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE cb_purchase SET " .

		"tarikh_purc='" . $tarikh_purc . "'," .
		"batchNo='" . $batchNo . "'," .
		"companyID='" . $companyID . "'," .
		"kod_project='" . $kod_project . "'," .
		"kod_jabatan='" . $kod_jabatan . "'," .
		"pymtAmt='" . $totalDb . "'," .
		"description='" . $description . "'," .
		"disedia='" . $disedia . "'," .
		"disemak='" . $disemak . "'," .
		"disahkan='" . $disahkan . "'," .
		"tarikh_disedia='" . $tarikh_disedia . "'," .
		"updatedDate='" . $updatedDate . "'," .
		"updatedBy='" . $updatedBy . "'";

	$sSQL 	 = $sSQL . $sWhere;

	$sSQL1 	 = "";
	$sWhere1 = "";
	$sWhere1 = "docNo='" . $purcNo . "' AND addminus='" . 1 . "'";
	$sWhere1 = " WHERE (" . $sWhere1 . ")";
	$sSQL1	 = "UPDATE cb_purchaseinf SET " .
		"deductID='" . $b_kodGL . "'," .
		"batchNo='" . $batchNo . "'," .
		"pymtAmt='" . $masterAmt . "'";

	$sSQL1 	 = $sSQL1 . $sWhere1;

	$sSQL2 	 = "";
	$sWhere2 = "";
	$sWhere2 = "docNo='" . $purcNo . "'";
	$sWhere2 = " WHERE (" . $sWhere2 . ")";
	$sSQL2	 = "UPDATE cb_purchaseinf SET " .
		"tarikh_doc='" . $tarikh_purc . "'";

	$sSQL2   = $sSQL2 . $sWhere2;

	if ($display) print $sSQL . '<br />';
	else

		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);
	$rs = &$conn->Execute($sSQL2);

	////////////////////////////////////////////////////////////////////////////////////////////
	if (count($perkara) > 0) {
		foreach ($perkara as $id => $value) {

			$deductID 	= $value;

			$priceA 	= $price[$id];
			$quantityA 	= $quantity[$id];
			if ($debit[$id]) {
				$pymtAmt 	= $debit[$id];
				$addminus 	= 0;
			} else {
				$pymtAmt 	= $kredit[$id];
				$addminus 	= 1;
			}

			$sSQL 	= "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE cb_purchaseinf SET " .

				"batchNo= '" . $batchNo . "'," .
				"deductID= '" . $deductID . "'," .
				"addminus= '" . $addminus . "'," .
				"price= '" . $priceA . "'," .
				"quantity= '" . $quantityA . "'," .
				"pymtAmt= '" . $pymtAmt . "'," .
				"updatedDate= '" . $updatedDate . "'," .
				"updatedBy= '" .  $updatedBy . "'";

			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}

	if (count($desc_akaun) > 0) {
		foreach ($desc_akaun as $id => $value) {

			$desc_akaun = $value;
			if ($debit[$id]) {
				$pymtAmt 	= $debit[$id];
				$addminus 	= 0;
			} else {
				$pymtAmt 	= $kredit[$id];
				$addminus 	= 1;
			}
			$sSQL 	= "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE cb_purchaseinf SET " .
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
	/////////////////////////////////////////////////////////////////////////////
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCpurchase&mn=' . $mn . '&action=view&purcNo=' . $purcNo . '";
	</script>';
	}
}

//pilihan simpan
elseif ($action == "Simpan" || $simpan) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$tarikh_purc 	= saveDateDb($tarikh_purc);
	$tarikh_bayar 	= saveDateDb($tarikh_bayar);

	// help prevent double entry by multiple users ----begin
	$getMax2 	= "SELECT MAX(CAST(right(purcNo,6) AS SIGNED INTEGER)) AS no2 FROM cb_purchase";
	$rsMax2 	= $conn->Execute($getMax2);
	$max2   	= sprintf("%06s", $rsMax2->fields('no2'));

	if ($rsMax2) {
		$max2 	= intval($rsMax2->fields('no2')) + 1;
		$max2 	= sprintf("%06s", $max2);
		$purcNo2 = 'PO' . $max2;
	} else {
		$purcNo2 = 'PO000001';
	}
	//-----end

	$sSQL 	= "";
	$sSQL	= "INSERT INTO cb_purchase (" .
		"purcNo, " .
		"batchNo, " .
		"kod_project, " .
		"kod_jabatan, " .
		"companyID, " .
		"kodGL, " .
		"tarikh_purc, " .
		"pymtAmt, " .
		"description, " .
		"disedia, " .
		"disemak, " .
		"tarikh_disedia, " .
		"tarikh_disemak, " .
		"disahkan, " .
		"tarikh_disahkan, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .
		" VALUES (" .

		"'" . $purcNo2 . "', " .
		"'" . $batchNo . "', " .
		"'" . $kod_project . "', " .
		"'" . $kod_jabatan . "', " .
		"'" . $companyID . "', " .
		"'" . $b_kodGL . "', " .
		"'" . $tarikh_purc . "', " .
		"'" . $totalDb . "', " .
		"'" . $description . "', " .
		"'" . $disedia . "', " .
		"'" . $disemak . "', " .
		"'" . $tarikh_disedia . "', " .
		"'" . $tarikh_disemak . "', " .
		"'" . $disahkan . "', " .
		"'" . $tarikh_disahkan . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "')";

	$sSQL1 	= "";
	$sSQL1	= "INSERT INTO cb_purchaseinf (" .
		"docNo," .
		"tarikh_doc," .
		"batchNo," .
		"deductID," .
		"addminus," .
		"price," .
		"quantity," .
		"pymtID," .
		"pymtAmt," .
		"desc_akaun," .
		"updatedBy," .
		"updatedDate," .
		"createdBy," .
		"createdDate) " .

		" VALUES (" .
		"'" . $purcNo2 . "', " .
		"'" . $tarikh_purc . "', " .
		"'" . $batchNo . "', " .
		"'" . $b_kodGL . "', " .
		"'" . 1 . "', " .
		"'" . $price2 . "', " .
		"'" . $quantity2 . "', " .
		"'" . 66 . "', " .
		"'" . $masterAmt . "', " .
		"'" . $desc_akaun2 . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "')";

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);


	$getMax = "SELECT MAX(CAST(right(purcNo,6) AS SIGNED INTEGER )) AS no FROM cb_purchase";
	$rsMax 	= $conn->Execute($getMax);
	$max 	= sprintf("%06s", $rsMax->fields('no'));
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCpurchase&mn=' . $mn . '&action=view&add=1&purcNo=PO' . $max . '";
	</script>';
	}
}

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<div class="table-responsive"><form name="MyForm" action="?vw=ACCpurchase&mn=' . $mn . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print '
<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>PO number</td>
				<td valign="top"></td><td><input class="form-controlx"  name="purcNo" value="' . $purcNo . '" type="text" size="20" maxlength="50"></td>
				<tr><td nowrap="nowrap">Batch</td><td valign="top"></td><td>' . selectbatch($batchNo, 'batchNo') . '</td></tr>
				<tr><td nowrap="nowrap">Projek</td><td valign="top"></td><td>' . selectproject($kod_project, 'kod_project') . '</td></tr>
				<tr><td nowrap="nowrap">Jabatan</td><td valign="top"></td><td>' . selectjabatan($kod_jabatan, 'kod_jabatan') . '</td></tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh</td>
				<td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh_purc">
				<input type="text" name="tarikh_purc" class="form-controlx" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh_purc"
					data-date-autoclose="true" value="' . $tarikh_purc . '">
				<div class="input-group-append">
					<span class="input-group-text">
						<i class="mdi mdi-calendar"></i></span>
				</div>
				</div>
				</td>
			</tr>
		</table>
	</td>
</tr>


<tr><td colspan="3"><hr class="mt-3" /></td></tr>';

print '
<tr colspan="3">
	<td valign="top"><input name="j" type="hidden" value="tiada">

		<table border="0" cellspacing="1" cellpadding="2">

			<tr>
				<td valign="top" nowrap>Bayar Kepada</td>
			</tr>

			<tr>
				<td>* Kod Pemiutang</td><td valign="top"></td>
				<td><input name="code" value="' . $code . '" type="text" size="20" maxlength="50" class="form-controlx" readonly/>&nbsp;';

print '<input type="button" class="btn btn-sm btn-info" id="pilihButton" value="Pilih" onclick="window.open(\'ACCidpembekal.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;';
print '<input type="button" class="btn btn-sm btn-primary" id="compButton" value="Tambah" onclick="window.open(\'generalAddUpdateACC.php?action=tambah&cat=AB&sub=&page=' . $page . '\',\'sort\',\'top=50,left=50,width=700,height=650,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';

print '&nbsp;<input name="loan_no" type="hidden" value="">

				</td>
			</tr>

			<tr>
				<td valign="top">Nama</td><td valign="top"></td><td><input name="nama_anggota"  value="' . $nama . '" type="text" size="50" maxlength="50" class="form-controlx" readonly/>
		    	</td>
		    </tr>

			<tr>
				<td valign="top">Alamat</td>
				<td valign="top"></td>
				<td><textarea name="b_Baddress" cols="50" rows="4" class="form-controlx" readonly>' . $b_Baddress . '</textarea></td>
			</tr>

		  	<tr>
				</td><td><input type=hidden name="companyID" value="' . $companyID . '" type="text" size="4" maxlength="50" class="form-controlx" />
		    	</td>
		    </tr>
		    <tr>
				</td><td><input type=hidden name="kodGL" value="' . $b_kodGL . '" type="text" size="4" maxlength="50" class="form-controlx" />
		    	</td>
		    </tr>
		    <tr>
				</td><td><input type=hidden name="amt" value="' . $amt . '" type="text" size="4" maxlength="50" class="form-controlx" />
		    	</td>
		    </tr>
			<tr>
				</td><td><input type=hidden name="purcNo" value="' . $purcNo . '" type="text" size="4" maxlength="50" class="form-controlx" />
				</td>
			</tr>

		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>';
//----------
if ($action == "view" && !is_int(dlookup("cb_purchaseinf", "ID", "docNo='" . $purcNo . "'"))) {
	print '
	<tr>
			<td align= "right" colspan="3">';
	if (!$add) print '
			<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCpurchase&mn=' . $mn . '&action=' . $action . '&purcNo=' . $purcNo . '&add=1\';">';
	else print '
		<input type="button" name="action" value="Simpan" class="btn btn-sm btn-primary" onclick="CheckField(\'Kemaskini\'); calculateDebit();">';
	print '&nbsp;<input type="submit" name="action" value="Hapus" class="btn btn-sm btn-danger">
		</td>
	</tr>';
}
//----------
print
	'<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="table table-sm table-striped">
			<tr class="table-primary">
				<td nowrap="nowrap"><b>Bil</b></td>
				<td nowrap="nowrap"><b>* Akaun</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap"><b>* Kuantiti</b></td>
				<td nowrap="nowrap"><b>* Harga Seunit (RM)</b></td>
				<td nowrap="nowrap"><b>Jumlah (RM)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

//<td nowrap="nowrap">Nama Cukai</td>


if ($action == "view") {

	if ($rsDetail->RecordCount() > 0) {

		$i = 0;
		while (!$rsDetail->EOF) {

			$id 		= $rsDetail->fields('ID');
			$perkara 	= $rsDetail->fields('deductID');
			//$taxing =	$rsDetail->fields('taxNo');

			$desc_akaun = $rsDetail->fields('desc_akaun');
			$quantity 	= $rsDetail->fields('quantity');
			$price 		= $rsDetail->fields('price');

			$a_Keterangan 	= dlookup("generalacc", "code", "ID=" . $perkara);
			$terangan 		= dlookup("generalacc", "code", "ID=" . $taxing);

			if ($rsDetail->fields('addminus')) {
				$kredit = $rsDetail->fields('pymtAmt');
			} else {
				$debit 	= $rsDetail->fields('pymtAmt');
			}
			//<td class="Data" nowrap="nowrap">'.strtax($id,$taxing).'&nbsp;</td>
			print	   '
			<tr>
				<td class="Data">&nbsp;' . ++$i . '.</td>	

				<td class="Data" nowrap="nowrap">' . strSelect3($id, $perkara, "asetLiabilitiPerbelanjaan") . '&nbsp;</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" rows="4" cols="40" maxlength="500" class="form-control-sm">' . $desc_akaun . '</textarea>&nbsp;
				</td>';

			//column kuantiti dan harga
			print '
				<td class="Data">
					<input name="quantity[' . $id . ']" id="quantity[' . $id . ']" type="text" class="form-control-sm" size="10" maxlength="10" value="' . $quantity . '" oninput="calculateDebit(' . $id . ')" readonly/>
					&nbsp;
				</td>

				<td class="Data">
					<input name="price[' . $id . ']" id="price[' . $id . ']" type="text" class="form-control-sm" size="10" maxlength="10" value="' . $price . '" oninput="calculateDebit(' . $id . ')" readonly/>
					&nbsp;
				</td>';

			print '
				<td class="Data" align="right">
					<input name="debit[' . $id . ']" id="debit[' . $id . ']" type="text" size="10" maxlength="10" value="' . $debit . '" class="form-control-sm" style="text-align:right;" readonly/>
					&nbsp;
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" name="pk[]" class="form-check-input" value="' . $id . '">&nbsp;</td>

			</tr>';
			$totalDb += $debit;

			$debit = '';
			print '<input type="hidden" name="totalDb" value="' . $totalDb . '">';
			$rsDetail->MoveNext();
			//If there are no records for akaun, disable some buttons and fields to prevent user error (data doubling)
			//------------- START
		}
	} else {
		if (!$add) {
			echo '<span style="color: red;">Tiada rekod.</span>';
		}
		print '
		<script>
		document.addEventListener("DOMContentLoaded", function() {
        var disableElementsByName = function(name) {
            var elements = document.getElementsByName(name);
			for (var i = 0; i < elements.length; i++) {
				var element = elements[i];
                var overlay = document.createElement("div");
                overlay.className = "overlay";
                overlay.style.position = "absolute";
                overlay.style.width = element.offsetWidth + "px";
                overlay.style.height = element.offsetHeight + "px";
                overlay.style.top = element.offsetTop + "px";
                overlay.style.left = element.offsetLeft + "px";
                overlay.style.zIndex = 1;
                overlay.style.backgroundColor = "rgba(255, 255, 255, 0.5)";
                element.parentNode.style.position = "relative";
                element.parentNode.appendChild(overlay);
            }
        };
		var disableElementById = function(id) {
			var element = document.getElementById(id);
			if (element) {
				var wrapper = document.createElement("div");
				wrapper.style.position = "relative";
				wrapper.style.display = "inline-block";
				element.parentNode.insertBefore(wrapper, element);
				wrapper.appendChild(element);

				var overlay = document.createElement("div");
				overlay.style.position = "absolute";
				overlay.style.top = "0";
				overlay.style.left = "0";
				overlay.style.right = "0";
				overlay.style.bottom = "0";
				overlay.style.backgroundColor = "rgba(255, 255, 255, 0.5)";
				overlay.style.zIndex = "1";
				wrapper.appendChild(overlay);

				// Prevent clicks on the overlay
				overlay.addEventListener("click", function(event) {
					event.stopPropagation();
				});
			}
		};
        disableElementsByName("purcNo");
        disableElementsByName("batchNo");
		disableElementsByName("tarikh_purc");
        disableElementsByName("kod_bank");
        disableElementsByName("kod_project");
        disableElementsByName("kod_jabatan");
		disableElementsByName("code");
        disableElementsByName("nama_anggota");
		disableElementsByName("b_Baddress");
		disableElementsByName("amt");
		disableElementsByName("invNo");
		disableElementsByName("disedia");
		disableElementsByName("disemak");
		disableElementsByName("disahkan");
		disableElementsByName("description");
		disableElementById("bottomButton");
		disableElementById("pilihButton");
		disableElementById("compButton");
		});
		</script>
		';
	}
	//------------- END
}


$strDeductIDList 	= deductListb2(1, "asetLiabilitiPerbelanjaan");
$strDeductCodeList 	= deductListb2(2, "asetLiabilitiPerbelanjaan");
$strDeductNameList 	= deductListb2(3, "asetLiabilitiPerbelanjaan");
$name = 'perkara2';

$strSelect = '<select name="' . $name . '" class="form-select-sm" id="deductSelect" onchange="updateDescAkaun()">
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

			<td class="Data">' . $strSelect . '</td>
			<td class="Data" align="left">
				<textarea name="desc_akaun2" class="form-control-sm" rows="4" cols="40" maxlength="500" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
			</td>

			<td class="Data" >					
				<input  name="quantity2" id="quantity2" type="text" class="form-control-sm" size="10" maxlength="10" value="' . $quantity2 . '" / oninput="calculate()">&nbsp;
			</td>

			<td class="Data" >					
				<input  name="price2" id="price2" type="text" class="form-control-sm" size="10" maxlength="10" value="' . $price2 . '" / oninput="calculate()">&nbsp;
			</td>

			<td class="Data" align="right">					
				<input  name="debit2" id="debit2" type="text" size="10" maxlength="10" class="form-control-sm" value="' . $debit2 . '" readonly/>&nbsp;
			</td>

			<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}

if ($totalDb <> 0) {
	$clsRM->setValue($totalDb);
	$strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}

// $idname = get_session('Cookie_fullName');

print 		'<tr class="table-secondary">
				<td class="Data" colspan="5" align="right"><b>Jumlah (RM)</b></td>
				<td class="Data" align="right"><b>' . number_format($totalDb, 2) . '</b></td>
				
				<td class="Data" align=""><b>&nbsp;</b></td>

			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">

			<tr><td nowrap="nowrap">Jumlah Dalam Perkataan</td><td valign="top"></td><td>
			<input class="form-controlx" name="" size="80" maxlength="80" value="' . $strTotal . '" readonly>
			<input class="Data" type="hidden" name="masterAmt" value="' . $totalDb . '">
			</td></tr>

			<tr><td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td><td>' . selectAdmin($disedia, 'disedia') . '</td></tr>
			<tr><td nowrap="nowrap">Disemak Oleh</td><td valign="top"></td><td>' . selectAdmin($disemak, 'disemak') . '</td></tr>
			<tr><td nowrap="nowrap">Disahkan Oleh</td><td valign="top"></td><td>' . selectAdmin($disahkan, 'disahkan') . '</td></tr>
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td><td valign="top">
					<textarea class="form-controlx" name="description" cols="50" rows="4">' . $description . '</textarea>
				</td>
			</tr>
		</table>
	</td>
</tr>';
print '<input name="kod_caw" type="hidden" value="321"><input name="no_siri" type="hidden" value="S112">

<input name="tarikh_bayar" type="hidden" value="01/10/2006"></tr>';


$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCPurchaseOrderPrint.php?id=' . $purcNo . '\')">&nbsp;
	<input type="button" name="action" id="bottomButton" value="' . $straction . '" class="btn btn-primary" onclick="CheckField(\'' . $straction . '\')">';
if ($straction == 'Simpan') print '
	<input type="hidden" name="simpan" value="1">';
print '
	</td>
</tr>';


$strTemp = '
	</table>
</form>
</div></div>';

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
  
		  if(e.elements[c].name=="price2" && e.elements[c].value==\'\') {
			alert(\'Ruang harga perlu diisi!\');
			count++;
			}

		  if(e.elements[c].name=="perkara2" && e.elements[c].value==\'\') {
			alert(\'Ruang akaun perlu diisi!\');
			count++;
			}

		  if(e.elements[c].name=="quantity2" && e.elements[c].value==\'\') {
			alert(\'Ruang kuantiti perlu diisi!\');
			count++;
			}

			}

		  if(act == \'Simpan\' || act == \'Kemaskini\') {
  
			if(e.elements[c].name=="code" && e.elements[c].value==\'\') 
		  	{
				alert(\'Ruang Kod Pemiutang perlu diisi!\');
				count++;
			}
		  }
		}
		if(count==0) {
        // Disable the submit button to prevent duplicate entries by user if click button multiple times
          var submitButton = document.querySelector("input[name=\"action\"]"); 
        if (submitButton) submitButton.disabled = true;

        // Submit the form
        e.submit();

        // Re-enable the button after 5 seconds (in case of error)
        setTimeout(function() {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.value = act;
            }
        }, 5000);
        }
	}

	function calculateDebit(pk) {
		// Get the values from the input fields
		var num1 = document.getElementById("quantity[" + pk + "]").value;
		var num2 = document.getElementById("price[" + pk + "]").value;

		// Perform the multiplication
		var result = num1 * num2;

		// Update the result input
		document.getElementById("debit[" + pk + "]").value = result;
	}

	function calculate() {
		// Get the values from the input fields
		var num1 = document.getElementById("quantity2").value;
		var num2 = document.getElementById("price2").value;

		// Perform the multiplication
		var result = num1 * num2;

		// Update the result input
		document.getElementById("debit2").value = result;
	}

	//pickup akaun name to keterangan
	function updateDescAkaun() {
		var selectElement 	= document.getElementById("deductSelect");
		var selectedOption 	= selectElement.options[selectElement.selectedIndex];
		
		// Extract the text content of the selected option after the two non-breaking spaces
		var optionText = selectedOption.textContent || selectedOption.innerText;
		var deductName = optionText.split("\u00a0\u00a0")[1];  // Split by two non-breaking spaces

		// Get the textarea by its name and update its value
		var descAkaunField 		= document.getElementsByName("desc_akaun2")[0];  // Access the first element with name="desc_akaun2"
		descAkaunField.value 	= deductName ? deductName.trim() : "";
	}
		
	$(document).ready(function() {
		// Initialize Select2 on select elements with "perkara" in the name attribute
		$("select[name*=\'perkara\']").select2({
			placeholder: "- Pilih -"
		});
	});

</script>';
include("footer.php");