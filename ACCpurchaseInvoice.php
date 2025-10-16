<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCpurchaseInvoice.php
 *			Date 		: 19/10/2006
 *			Keywords 	: disable, noRecords, effect, duplicate (to prevent user fault)
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

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCpurchaseInvoiceList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;PEMBAYARAN INVOIS (PI)</b>';

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
if (!isset($page))	$page = "pmtg";

$display = 0;
if ($PINo && $action == "view") {
	$sql 			= "SELECT *
						FROM   cb_purchaseinv a, generalacc b 
						WHERE  a.companyID = b.ID 
						AND a.PINo = '" . $PINo . "'";
	$rs 			= $conn->Execute($sql);

	$PINo 			= $rs->fields('PINo');
	$tarikh_PI 		= $rs->fields('tarikh_PI');
	$tarikh_PI 		= substr($tarikh_PI, 8, 2) . "/" . substr($tarikh_PI, 5, 2) . "/" . substr($tarikh_PI, 0, 4);
	$tarikh_PI 		= toDate("d/m/y", $rs->fields('tarikh_PI'));
	$batchNo 		= $rs->fields('batchNo');
	$companyID 	    = $rs->fields('companyID');
	$bayar_nama 	= $rs->fields('bayar_nama');
	$catatan 		= $rs->fields('catatan');
	$createdDate 	= $rs->fields('createdDate');
	$createdBy 		= $rs->fields('createdBy');
	$updatedDate 	= $rs->fields('updatedDate');
	$updatedBy 		= $rs->fields('updatedBy');
	$purcNo			= $rs->fields('purcNo');
	$amt			= $rs->fields('outstandingbalance');
	$cara_byr		= $rs->fields('cara_byr');
	$accountNo 		= $rs->fields('accountNo');
	$keranisedia	= $rs->fields('keranisedia');
	$keranisemak	= $rs->fields('keranisemak');
	$b_Baddress 	= $rs->fields('b_Baddress');
	$code 			= $rs->fields('code');
	$nama			= $rs->fields('name');
	$kodGL 			= $rs->fields('b_kodGL');
	$invLhdn 		= $rs->fields('invLhdn'); // LHDN-UID
	$invComp 		= $rs->fields('invComp');
	$tinLhdn 		= dlookup("generalacc", "b_tinLhdn", "ID=" . tosql($companyID, "Text"));

	// kod carta akaun
	//-----------------
	$sql2 		= "SELECT * FROM transactionacc WHERE docNo = '" . $PINo . "' AND addminus IN (0) ORDER BY ID";
	$rsDetail 	= $conn->Execute($sql2);
	if ($rsDetail->RowCount() < 1)
		$noTran = true;
} elseif ($action == "new") {
	$getNo 	= "SELECT MAX(CAST(right(PINo,6) AS SIGNED INTEGER)) AS nombor FROM cb_purchaseinv";
	$rsNo 	= $conn->Execute($getNo);
	$tarikh_PI 		= date("d/m/Y");
	$tarikh_batch 	= date("d/m/Y");
	if ($rsNo) {
		$nombor = intval($rsNo->fields('nombor')) + 1;
		$nombor = sprintf("%06s", $nombor);
		$PINo 	= 'PI' . $nombor;
	} else {
		$PINo 	= 'PI000001';
	}
}

if (!isset($tarikh_PI)) $tarikh_PI = date("d/m/Y");
if (!isset($tarikh_batch)) $tarikh_batch = date("d/m/Y");

if ($perkara2) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$createdBy 		= get_session("Cookie_userName");
	$createdDate 	= date("Y-m-d H:i:s");

	$deductID 	= $perkara2;
	$addminus 	= 0;
	$cajAmt 	= 0.0;
	$coreID 	= dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));

	if ($pymtAmt == '')
		$pymtAmt = '0.0';
	$sSQL	= "INSERT INTO transactionacc (" .
		"docNo," .
		"docID," .
		"batchNo," .
		"yrmth," .
		"deductID," .
		"addminus," .
		"coreID," .
		"price," .
		"quantity," .
		"pymtID," .
		"pymtRefer," .
		"pymtReferC," .
		"pymtAmt," .
		"desc_akaun," .
		"status," .
		"isApproved," .
		"approvedDate," .
		"createdDate," .
		"createdBy," .
		"tarikh_batch) " .

		" VALUES (" .
		"'" . $PINo . "', " .
		"'" . 8 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $deductID . "', " .
		"'" . $addminus . "', " .
		"'" . $coreID . "', " .
		"'" . $price2 . "', " .
		"'" . $quantity2 . "', " .
		"'" . 66 . "', " .
		"'" . $kodGL . "', " .
		"'" . $purcNo . "', " .
		"'" . $kredit2 . "', " .
		"'" . $desc_akaun2 . "', " .
		"'" . $status . "', " .
		"'" . $isApproved . "', " .
		"'" . $updatedDate . "', " .
		"'" . $createdDate . "', " .
		"'" . $createdBy . "', " .
		"'" . $tarikh_batch . "')";

	if ($display) print $sSQL . '<br />';
	else {
		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Kemaskini Purchase Invois - ' . $PINo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

		print '<script>
		window.location = "?vw=ACCpurchaseInvoice&mn=' . $mn . '&action=view&PINo=' . $PINo . '";
		</script>';
	}
}

if ($action == "Hapus") {
	if (count($pk) > 0) {
		$sWhere = "";
		foreach ($pk as $val) {
			$sSQL 	= '';
			$sWhere = "ID='" . $val . "'";

			$docNo = dlookup("transactionacc", "docNo", $sWhere);

			$sSQL 	= "DELETE FROM transactionacc WHERE " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);

			$strActivity = $_POST['Submit'] . 'Hapus Kandungan Purchase Invois - ' . $docNo;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCpurchaseInvoice&mn=' . $mn . '&action=view&PINo=' . $PINo . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $perkara || $desc_akaun) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$Master2 		= dlookup("generalacc", "parentID", "ID = '" . $kodGL . "'");
	$tarikh_PI 		= saveDateDb($tarikh_PI);
	$tarikh_batch 	= saveDateDb($tarikh_batch);
	$yymm 	= substr($tarikh_PI, 0, 4) . substr($tarikh_PI, 5, 2);
	$sSQL 	= "";
	$sWhere = "";
	$sWhere = "PINo='" . $PINo . "'";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE cb_purchaseinv SET " .
		"PINo='" . $PINo . "'," .
		"tarikh_PI='" . $tarikh_PI . "'," .
		"batchNo='" . $batchNo . "'," .
		"companyID='" . $companyID . "'," .
		"invLhdn='" . $invLhdn . "'," .
		"invComp='" . $invComp . "'," .
		"cara_byr='" . $cara_byr . "'," .
		"bayar_nama='" . $bayar_nama . "'," .
		"catatan='" . $catatan . "'," .
		"createdDate='" . $updatedDate . "'," .
		"createdBy='" . $updatedBy . "'," .
		"updatedDate='" . $updatedDate . "'," .
		"updatedBy='" . $updatedBy . "'," .
		"purcNo='" . $purcNo . "'," .
		"outstandingbalance='" . $amt . "'," .
		"balance='" . $balance . "'," .
		"keranisedia='" . $keranisedia . "'," .
		"keranisemak='" . $keranisemak . "'";
	$sSQL = $sSQL . $sWhere;

	$sSQL1 	 = "";
	$sWhere1 = "";
	$sWhere1 = "docNo='" . $PINo . "' AND addminus='" . 1 . "'";
	$sWhere1 = " WHERE (" . $sWhere1 . ")";
	$sSQL1	 = "UPDATE transactionacc SET " .
		"deductID='" . $kodGL . "'," .
		"MdeductID='" . $Master2 . "'," .
		"batchNo='" . $batchNo . "'," .
		"desc_akaun='" . $catatan . "'," .
		"pymtAmt='" . $masterAmt . "'";

	$sSQL1   = $sSQL1 . $sWhere1;

	$sSQL2   = "";
	$sWhere2 = "";
	$sWhere2 = "docNo='" . $PINo . "'";
	$sWhere2 = " WHERE (" . $sWhere2 . ")";
	$sSQL2	 = "UPDATE transactionacc SET " .
		"yrmth='" . $yymm . "'," .
		"tarikh_doc='" . $tarikh_PI . "'";

	$sSQL2  = $sSQL2 . $sWhere2;

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);
	$rs = &$conn->Execute($sSQL2);

	/////////////////////////////////////

	if (count($perkara) > 0) {
		foreach ($perkara as $id => $value) {

			$deductID 	= $value;
			$coreID 	= dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));
			$priceA 	= $price[$id];
			$quantityA 	= $quantity[$id];
			if ($debit[$id]) {
				$pymtAmt 	= $debit[$id];
				$addminus 	= 1;
			} else {
				$pymtAmt 	= $kredit[$id];
				$addminus 	= 0;
			}
			//$no_ruj = $ruj[$id];
			$sSQL 	= "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transactionacc SET " .
				"batchNo= '" . $batchNo . "'" .
				",deductID= '" . $deductID . "'" .
				",addminus= '" . $addminus . "'" .
				",coreID= '" . $coreID . "'" .
				",price= '" . $priceA . "'" .
				",quantity= '" . $quantityA . "'" .
				",pymtAmt= '" . $pymtAmt . "'" .
				",updatedDate= '" . $updatedDate . "'" .
				",updatedBy= '" .  $updatedBy . "'";

			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	if (count($kod_akaun) > 0) {
		foreach ($kod_akaun as $id => $value) {

			$MdeductID  = $value;
			if ($debit[$id]) {
				$pymtAmt    = $debit[$id];
				$addminus   = 1;
			} else {
				$pymtAmt    = $kredit[$id];
				$addminus   = 0;
			}
			$sSQL   = "";
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
	/////////////////////////////////////////////////////////////
	if (count($desc_akaun) > 0) {
		foreach ($desc_akaun as $id => $value) {

			$desc_akaun = $value;
			if ($debit[$id]) {
				$pymtAmt = $debit[$id];
				$addminus = 1;
			} else {
				$pymtAmt = $kredit[$id];
				$addminus = 0;
			}
			$sSQL 	= "";
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
	/////////////////////////////////////////////////////////////////////////////

	if (!$display) {
		print '<script>
	window.location = "?vw=ACCpurchaseInvoice&mn=' . $mn . '&action=view&PINo=' . $PINo . '";
	</script>';
	}
}

//pilihan simpan
elseif ($action == "Simpan" || $simpan) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$tarikh_PI 		= saveDateDb($tarikh_PI);
	$tarikh_batch 	= saveDateDb($tarikh_batch);

	// help prevent double entry by multiple users ----begin
	$getMax2 	= "SELECT MAX(CAST(right(PINo,6) AS SIGNED INTEGER)) AS no2 FROM cb_purchaseinv";
	$rsMax2 	= $conn->Execute($getMax2);
	$max2   	= sprintf("%06s", $rsMax2->fields('no2'));

	if ($rsMax2) {
		$max2 	= intval($rsMax2->fields('no2')) + 1;
		$max2 	= sprintf("%06s", $max2);
		$PINo2 	= 'PI' . $max2;
	} else {
		$PINo2 	= 'PI000001';
	}
	//-----end

	$sSQL 	= "";
	$sSQL	= "INSERT INTO cb_purchaseinv (" .
		"PINo, " .
		"tarikh_PI, " .
		"batchNo, " .
		"invLhdn, " .
		"invComp, " .
		"cara_byr, " .
		"companyID, " .
		"bayar_nama, " .
		"catatan, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy, " .
		"purcNo, " .
		"outstandingbalance, " .
		"keranisedia, " .
		"keranisemak) " .

		" VALUES (" .
		"'" . $PINo2 . "', " .
		"'" . $tarikh_PI . "', " .
		"'" . $batchNo . "', " .
		"'" . $invLhdn . "', " .
		"'" . $invComp . "', " .
		"'" . $cara_byr . "', " .
		"'" . $companyID . "', " .
		"'" . $bayar_nama . "', " .
		"'" . $catatan . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $purcNo . "', " .
		"'" . $amt . "', " .
		"'" . $keranisedia . "', " .
		"'" . $keranisemak . "')";

	$sSQL1  = "";
	$sSQL1	= "INSERT INTO transactionacc (" .
		"docNo," .
		"tarikh_doc," .
		"docID," .
		"batchNo," .
		"yrmth," .
		"deductID," .
		"MdeductID," .
		"addminus," .
		"price," .
		"quantity," .
		"pymtID," .
		"pymtAmt," .
		"desc_akaun," .
		"pymtRefer," .
		"pymtReferC," .
		"status," .
		"isApproved," .
		"approvedDate," .
		"createdDate," .
		"createdBy," .
		"tarikh_batch) " .
		" VALUES (" .
		"'" . $PINo2 . "', " .
		"'" . $tarikh_PI . "', " .
		"'" . 8 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $kodGL . "', " .
		"'" . $Master2 . "', " .
		"'" . 1 . "', " .
		"'" . $price2 . "', " .
		"'" . $quantity2 . "', " .
		"'" . 66 . "', " .
		"'" . $masterAmt . "', " .
		"'" . $catatan . "', " .
		"'" . $companyID . "', " .
		"'" . $purcNo . "', " .
		"'" . $status . "', " .
		"'" . $isApproved . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $tarikh_batch . "')";

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);

	$getMax = "SELECT MAX(CAST(right(PINo,6) AS SIGNED INTEGER)) AS no FROM cb_purchaseinv";
	$rsMax 	= $conn->Execute($getMax);
	$max 	= sprintf("%06s", $rsMax->fields('no'));
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCpurchaseInvoice&mn=' . $mn . '&action=view&add=1&PINo=PI' . $max . '";
	</script>';
	}
}

print '
<style>
.recurring-btn {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    font-size: 16px;
    border-color: #eff2f7;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.35);
    float: right; /* This makes the button float to the right */
}

.recurring-btn i {
    font-size: 1.7rem;
    margin-right: 0.5rem;
}
</style>
';

$recurring = '<button class="btn btn-light btn-outline-secondary btn-sm text-muted recurring-btn" title="Create Recurring Invoice" 
                onclick="if(confirm(\'This invoice will be duplicated with a new invoice number. Proceed?\')) { Recurring(); }">
                <i class="fa fa-copy"></i> Recurring
            </button>';

$strTemp .=
	'<div class="table-responsive"><div class="maroon" align="left">' . $strHeaderTitle . '&nbsp;&nbsp;&nbsp;<!-- ' . $recurring . ' --></div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="?vw=ACCpurchaseInvoice&mn=' . $mn . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print
	'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			
			<tr>
				<td>Nombor PI</td>
				<td valign="top"></td>
				<td>
					<input  name="PINo" value="' . $PINo . '" type="text" size="20" maxlength="50" class="form-controlx" readonly/>
				</td>
			</tr>

			<tr>
				<td>* Batch</td>
				<td valign="top"></td>
				<td>' . selectbatchPI($batchNo, 'batchNo') . '</td>
			</tr>

			<tr>
				<td>Tanggal</td>
				<td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh_PI">
				<input type="text" name="tarikh_PI" class="form-controlx" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh_PI"
					data-date-autoclose="true" value="' . $tarikh_PI . '">
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
<td>* Kod Pemiutang</td><td valign="top"></td>
<td><input name="code" value="' . $code . '" type="text" size="20" maxlength="50"  class="form-controlx" readonly/>&nbsp;';

print '<input type="button" class="btn btn-sm btn-info" id="invButton" value="Pilih PO" onclick="window.open(\'ACCidpemiutangPI.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;';
print '<input type="button" class="btn btn-sm btn-info" id="compButton" value="Pilih Syarikat" onclick="window.open(\'ACCidpembekal.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;';
print '<input type="button" class="btn btn-sm btn-primary" id="addCreditorButton" value="Tambah Syarikat" onclick="window.open(\'generalAddUpdateACC.php?action=tambah&cat=AB&sub=&page=' . $page . '\',\'sort\',\'top=50,left=50,width=700,height=650,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';

print '&nbsp;

</td>
</tr>

<tr>
 <td valign="top">Nama Serikat</td>
 <td valign="top"></td>
 <td><input name="nama_anggota"  value="' . $nama . '" size="50" maxlength="50"  class="form-controlx" readonly /></td>
 </tr>

<tr>
<td valign="top">Alamat Syarikat</td>
<td valign="top"></td>
<td><textarea name="b_Baddress" cols="50" rows="4" class="form-controlx" readonly>' . $b_Baddress . '</textarea></td>
</tr>

   <tr>
 <td valign="top">Amaun Purchase Order (RP)</td>
 <td valign="top"></td>
 <td><input name="amt"  value="' . $amt . '" size="10" maxlength="50"  class="form-controlx" readonly/></td>
 </tr>

  <tr>
 <td valign="top">Nombor Purchase Order</td>
 <td valign="top"></td>
 <td><input name="purcNo" value="' . $purcNo . '" size="40" maxlength="50"  class="form-controlx" readonly /></td>
 </tr>
 
<tr>
	<td valign="top">LHDN-UID</td>
	<td valign="top"></td>
	<td><input name="invLhdn" value="' . $invLhdn . '" size="40" maxlength="50"  class="form-controlx" /></td>
</tr>

<tr>
	<td valign="top">TIN (LHDN)</td>
	<td valign="top"></td>
	<td><input name="tinLhdn" value="' . $tinLhdn . '" size="40" maxlength="50"  class="form-controlx" readonly/></td>
</tr>

<tr>
	<td valign="top">No Invois Syarikat</td>
	<td valign="top"></td>
	<td><input name="invComp" value="' . $invComp . '" size="40" maxlength="50"  class="form-controlx" /></td>
</tr>

 <tr>
 <td valign="top">Cara Bayar</td>
 <td valign="top"></td>
 <td>' . selectbayar($cara_byr, 'cara_byr') . '</td>
 </tr>';

$sql3 		= "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = '" . $PINo . "' ORDER BY ID";
$rsDetail1 = $conn->Execute($sql3);

print
	'<tr>
	<td valign="top" align="left">Master Jumlah (Rp)</td><td valign="top"></td>
	<td><input id="master" class="form-control-sm" value="' . $rsDetail1->fields('pymtAmt') . '" type="text" size="20" maxlength="10" readonly/></td>
 </tr>

 <tr>
</td><td><input type=hidden name="companyID" value="' . $companyID . '" type="text" size="4" maxlength="50" class="form-controlx" />
</td>
</tr>

<tr>
</td><td><input type=hidden name="kodGL" value="' . $kodGL . '" type="text" size="4" maxlength="50" class="form-controlx" />
</td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
</tr>';

print '
<style>
    .request-loader-container {
      position: relative;
      display: inline-block; /* Ensure the container fits around the button */
    }

    .request-loader {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      border-radius: 4px; /* Match the button’s border-radius */
      pointer-events: none; /* Prevent interaction with loader */
	  display: none; /* Hidden by default */
    }

    .request-loader.active {
        display: block; /* Show the loader */
    }

    .request-loader::after,
    .request-loader::before {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 150%; /* Larger to ensure ripple effect expands */
      height: 200%; /* Larger to ensure ripple effect expands */
      background: rgba(255, 255, 0, 0.8); /* Yellow color with 30% opacity */
      content: "";
      border-radius: 50%; /* Circular ripple effect */
      transform: translate(-50%, -50%) scale(0);
      animation: ripple 2s cubic-bezier(0.65, 0, 0.34, 1) infinite;
    }

    .request-loader::before {
      animation-delay: 1s;
    }

    @keyframes ripple {
      from {
        opacity: 1;
        transform: translate(-50%, -50%) scale(0);
      }
      to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(1);
      }
    }
</style>
';

//----------
if ($action == "view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $PINo . "'"))) {
	print '
	<tr>
		<td align= "right" colspan="3">';
	if (!$add) print '
			<div class="request-loader-container" id="loaderContainer">
				<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCpurchaseInvoice&mn=' . $mn . '&action=' . $action . '&PINo=' . $PINo . '&add=1\';">
				<div class="request-loader" id="requestLoaderTambah"></div>
			</div>
			';
	else print '
			<input type="button" name="action" value="Simpan" class="btn btn-sm btn-primary" onclick="CheckField(\'Kemaskini\')">';
	print '&nbsp;<input type="submit" name="action" value="Hapus" class="btn btn-sm btn-danger">
		</td>
	</tr>';
}
//----------
print
	'<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="3" width="100%" class="table table-sm table-striped">
			<tr class="table-primary">
				<td nowrap="nowrap"><b>Bil</b></td>
				<td nowrap="nowrap"><b>* Perkara</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap"><b>* Kuantiti</b></td>
				<td nowrap="nowrap"><b>* Harga Seunit (RP)</b></td>
				<td nowrap="nowrap" align="right"><b>Jumlah (Rp)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

// Determine if there are no records for akaun. If so, visually guide user to Tambah akaun instead of click Kemaskini.
//------------- START
$noRecords = true;

if ($action == "view") {

	if ($rsDetail->RecordCount() > 0) {
		//If there are records, set flag to false. User can click Kemaskini.
		$noRecords = false;
		$i = 0;

		while (!$rsDetail->EOF) {

			$id 		= $rsDetail->fields('ID');
			$ruj 		= $rsDetail->fields('pymtRefer');
			$perkara 	= $rsDetail->fields('deductID');

			// $kod_akaun 	= dlookup("generalacc", "c_Panel", "ID=" . $perkara);
			$kod_akaun  = dlookup("generalacc", "parentID", "ID=" . $perkara);
			$namaparent = dlookup("generalacc", "name", "ID=" . $kod_akaun);

			$kredit 	= $rsDetail->fields('pymtAmt');
			$desc_akaun =	$rsDetail->fields('desc_akaun');

			$quantity 	= $rsDetail->fields('quantity');
			$price 		= $rsDetail->fields('price');

			$a_Keterangan = dlookup("generalacc", "code", "ID=" . $perkara);

			if ($rsDetail->fields('addminus')) {
				$kredit = $rsDetail->fields('pymtAmt');
			} else {
				$debit 	= $rsDetail->fields('pymtAmt');
			}

			print
				'<tr>
			<td class="Data">&nbsp;' . ++$i . '.</td>	

			<td class="Data" nowrap="nowrap">' . strSelect3($id, $perkara, "asetPerbelanjaan") . '&nbsp;
				<input class="form-control-sm" name="kod_akaun[' . $id . ']" type="hidden" size="10" maxlength="10" value="' . $kod_akaun . '"/>
			</td>

			<td class="Data" nowrap="nowrap">
				<textarea name="desc_akaun[' . $id . ']" rows="4" cols="40" maxlength="500" class="form-control-sm">' . $desc_akaun . '</textarea>&nbsp;
			</td>';

			//column kuantiti dan harga
			print '
			<td class="Data" nowrap="nowrap">
				<input name="quantity[' . $id . ']" id="quantity[' . $id . ']" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $quantity . '" oninput="calculateKredit(' . $id . ')" readonly>
				&nbsp;
			</td>

			<td class="Data" nowrap="nowrap" align="center">
				<input name="price[' . $id . ']" id="price[' . $id . ']" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $price . '" oninput="calculateKredit(' . $id . ')" readonly>
				&nbsp;
			</td>';

			print '
			<td class="Data" nowrap="nowrap" align="right">
				<input name="kredit[' . $id . ']" id="kredit[' . $id . ']" type="text" size="10" style="text-align: right;" maxlength="10" value="' . $kredit . '" class="form-control-sm" readonly/>
				&nbsp;
			</td>

			<td class="Data" nowrap="nowrap"><input type="checkbox" name="pk[]" class="form-check-input" value="' . $id . '">&nbsp;</td>

		</tr>';

			$totalKt += $kredit;
			$baki = $amt - $totalKt;
			$kredit = '';
			$rsDetail->MoveNext();
		}
		//If there are no records for akaun, disable some buttons and fields to prevent user error (data doubling)
		//------------- START
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
		disableElementsByName("PINo");
        disableElementsByName("batchNo");
		disableElementsByName("tarikh_PI");
        disableElementsByName("nama_anggota");
		disableElementsByName("code");
        disableElementsByName("nama");
		disableElementsByName("b_Baddress");
		disableElementsByName("amt");
		disableElementsByName("purcNo");
		disableElementsByName("invLhdn"); //LHDN-UID
		disableElementsByName("invComp");
		disableElementsByName("tinLhdn");
		disableElementsByName("cara_byr");
		disableElementsByName("keranisedia");
		disableElementsByName("keranisemak");
		disableElementsByName("catatan");
		disableElementById("bottomButton");
		disableElementById("invButton");
		disableElementById("compButton");
		disableElementById("addCreditorButton");
		});
		</script>
		';
	}
	//------------- END
}

$strDeductIDList 	= deductListb2(1, "asetPerbelanjaan");
$strDeductCodeList 	= deductListb2(2, "asetPerbelanjaan");
$strDeductNameList 	= deductListb2(3, "asetPerbelanjaan");
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

			<td class="Data">' . $strSelect . '
			<input name="kod_akaun2" type="hidden" size="10" maxlength="10" value="' . $kod_akaun2 . '" class="form-control-sm"/>
			</td>

			<td class="Data" align="left">
				<textarea name="desc_akaun2" class="form-control-sm" rows="4" cols="40" maxlength="500" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
			</td>

			<td class="Data" align="center">
				<input  name="quantity2" id="quantity2" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $quantity2 . '" / oninput="calculate()">&nbsp;
			</td>

			<td class="Data" align="right">
				<input  name="price2" id="price2" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $price2 . '" / oninput="calculate()">&nbsp;
			</td>

			<td class="Data" align="right">	
				<input  name="kredit2" id="kredit2" type="text" size="10" style="text-align: right;" maxlength="10" class="form-control-sm" value="' . $kredit2 . '" readonly/>&nbsp;
			</td>

			<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}

//bahagian bawah skali
if ($totalKt <> 0) {
	$clsRM->setValue($baki);
	$clsRM->setValue($totalKt);
	$strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}

print 		'<tr class="table-secondary">
				<td class="Data" align=""><b>&nbsp;</b></td>
				<td class="Data" colspan="4" align="right"><b>Jumlah (RP)</b></td>
				<td class="Data" id="totalJumlah" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"></td>
			</tr>';
if ($purcNo) {
	print '
				<tr class="table-secondary">
					<td class="Data" align=""><b>&nbsp;</b></td>
					<td class="Data" colspan="4" align="right"><b>Baki Purchase Order (RP)</b></td>
					<td class="Data" align="right"><b>' . number_format($baki, 2) . '&nbsp;</b></td>
					<td class="Data" align="right"></td>
				</tr>';
}
print '
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr colspan="3">
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="3">
		
		<tr>
			<td nowrap="nowrap"></td>
			<td>
				<input class="Data" type="hidden" name="masterAmt" value="' . $totalKt . '">				
				<input class="Data" type="hidden" name="balance" value="' . $baki . '">				
				<input class="Data" type="hidden" name="bankparent" value="' . $bankparent . '">
			</td>
		</tr>


		<tr>
				<td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td>
				<td>' . selectAdmin($keranisedia, 'keranisedia') . '</td>
			</tr>

			<tr>
				<td nowrap="nowrap">Disemak Oleh</td><td valign="top"></td>
				<td>' . selectAdmin($keranisemak, 'keranisemak') . '</td>
			</tr>
			
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td>
				<td valign="top">
					<textarea class="form-controlx" name="catatan" cols="50" rows="4">' . $catatan . '</textarea></td>
			</tr>
		
		</table>
	</td>';

if ($PINo) {
	$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
	print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCPurchaseInvoicePrint.php?id=' . $PINo . '\')">&nbsp;
	<div class="request-loader-container" id="loaderContainer">
		<input type="button" name="action" id="bottomButton" value="' . $straction . '" class="btn btn-primary" onclick="CheckField(\'' . $straction . '\')">
        <div class="request-loader" id="requestLoader"></div>
    </div>
	<br><br>
	';
	if ($straction == 'Simpan') print '
	<input type="hidden" name="simpan" value="1">';
}

$strTemp = '
</form>
</td>
</tr>
</table>
</div>
</div>';

print $strTemp;

//-------------------------------------------------------------- Feature to duplicate/recurring invoice --------------START

// Print button in new form
print '
<form method="POST" id="recurringForm">
	<input type="hidden" name="recurrPINo" id="recurrPINoField" value="">
</form>

';

// create the new invois number for duplication
$getNo = "SELECT MAX(CAST(right(PINo,6) AS SIGNED INTEGER)) AS nombor FROM cb_purchaseinv";
$rsNo = $conn->Execute($getNo);
if ($rsNo) {
	$nombor = intval($rsNo->fields('nombor')) + 1;
	$nombor = sprintf("%06s", $nombor);
	$recurrPINo = 'PI' . $nombor;
}

// duplicating the invois
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recurrPINo'])) {
	$recurrPINo 	= $_POST['recurrPINo'];
	$oldPINo 	= $PINo; // Pass the old invoice number from the form

	print '
	// <script>
	// 	alert("Recurring Invoice Number: ' . $recurrPINo . '");
	// 	alert("Old Invoice Number: ' . $oldPINo . '");
	// </script>';

	$updatedBy 	    = get_session("Cookie_userName");
	$updatedDate    = date("Y-m-d H:i:s");
	// $tarikh_PI     = saveDateDb($tarikh_PI);

	$sSQL 	= "";
	$sSQL	= "INSERT INTO cb_purchaseinv (" .
		"PINo, " .
		// "tarikh_PI, " . //left empty
		"batchNo, " .
		"invLhdn, " .
		"invComp, " .
		"cara_byr, " .
		"companyID, " .
		"bayar_nama, " .
		"catatan, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy, " .
		"purcNo, " .
		"outstandingbalance, " .
		"keranisedia, " .
		"keranisemak) " .

		" VALUES (" .
		"'" . $recurrPINo . "', " .
		// "'". $tarikh_PI . "', ".
		"'" . $batchNo . "', " .
		"'" . $invLhdn . "', " .
		"'" . $invComp . "', " .
		"'" . $cara_byr . "', " .
		"'" . $companyID . "', " .
		"'" . $bayar_nama . "', " .
		"'" . $catatan . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $purcNo . "', " .
		"'" . $amt . "', " .
		"'" . $keranisedia . "', " .
		"'" . $keranisemak . "')";

	// Step 1: Retrieve rows from transactionacc for the old PINo
	$query = "SELECT * FROM transactionacc WHERE docNo = '$oldPINo'";
	$result = $conn->Execute($query);

	// Step 2: Loop through the results and insert rows with updated docNo
	if ($result) {
		while (!$result->EOF) {
			// Fetch each row's data
			$row = $result->fields;

			// Modify docNo to recurrPINo
			$row['docNo'] = $recurrPINo;

			// Construct the INSERT query using the fetched and modified data
			//tarikh_doc is left empty
			$insertQuery = "INSERT INTO transactionacc (
                docNo, docID, batchNo, yrmth, 
                deductID, MdeductID, addminus, coreID, price, quantity, 
                pymtID, pymtRefer, pymtReferC, pymtAmt, desc_akaun, 
				updatedBy, updatedDate, createdBy, createdDate
            ) VALUES (
                '" . $row['docNo'] . "', 
                '" . $row['docID'] . "', 
                '" . $row['batchNo'] . "', 
                '" . $row['yrmth'] . "', 
                '" . $row['deductID'] . "', 
                '" . $row['MdeductID'] . "', 
                '" . $row['addminus'] . "', 
				'" . $row['coreID'] . "', 
                '" . $row['price'] . "', 
                '" . $row['quantity'] . "', 
                '" . $row['pymtID'] . "', 
                '" . $row['pymtRefer'] . "', 
				'" . $row['pymtReferC'] . "', 
                '" . $row['pymtAmt'] . "', 
                '" . $row['desc_akaun'] . "', 
                '" . $row['updatedBy'] . "', 
                '" . $row['updatedDate'] . "', 
                '" . $row['createdBy'] . "', 
                '" . $row['createdDate'] . "'
            )";

			// Execute the insert query
			$conn->Execute($insertQuery);

			// Move to the next row
			$result->MoveNext();
		}
	} else {
		echo "No rows found for the previous purchase invoice number: $oldPINo.";
	}

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);

	if (!$display) {
		print '<script>
window.location = "?vw=ACCpurchaseInvoice&mn=' . $mn . '&action=view&PINo=' . $recurrPINo . '";
</script>';
	}
}

//-------------------------------------------------------------- Feature to duplicate/recurring invoice --------------END

print '
<script language="JavaScript">

  // Recurring function
    function Recurring() {
        // Use PHP variable for the new PINo
        var recurrPINo = "' . $recurrPINo . '";  // Embed PHP variable as a JavaScript string

		// Update the hidden input field value
        var recurrPINoField = document.getElementById("recurrPINoField");
        if (recurrPINoField) {
            recurrPINoField.value = recurrPINo;

            // Submit the form
            document.getElementById("recurringForm").submit();
        } else {
            console.error("Hidden field with ID \'recurrPINoField\' not found.");
        }
    }

<!-- Implementing the javascript visual effect for buttons and comparing amount for Jumlah vs Master. START -->

document.addEventListener("DOMContentLoaded", function() {
	function compare() {
		const masterValue = document.getElementById("master").value;
		const master = parseFloat(masterValue);
		const jumlah = parseFloat(document.getElementById("totalJumlah").innerText.replace(/,/g, ""));
    	var noRecords = ' . json_encode($noRecords) . ';
		const requestLoader = document.getElementById("requestLoader");
		var requestLoaderTambah = document.getElementById("requestLoaderTambah");

		// Handle cases where master is not a valid number
		if (!masterValue) {
			document.getElementById("totalJumlah").style.color = "black";
			document.getElementById("master").style.color = "black";
			requestLoader.classList.remove("active");
			return;
		}

		// Compare values and update styles accordingly
		const colors = jumlah === master ? "black" : "red";
		document.getElementById("totalJumlah").style.color = colors;
		document.getElementById("master").style.color = colors;

		// Manage loader visibility
		if (jumlah === master) {
			requestLoader.classList.remove("active"); // if master amaun and jumlah tally, no action needed
			requestLoaderTambah.classList.remove("active"); // stop prompting user to click tambah
		} else if (noRecords) {
			requestLoader.classList.remove("active"); // if there are no akaun records, no point to prompt user to click kemaskini
			requestLoaderTambah.classList.add("active"); // prompt user to tambah akaun instead
		} else {
			requestLoader.classList.add("active"); // prompt user to click kemaskini when they are records that are not tally with master amaun
			requestLoaderTambah.classList.remove("active"); // stop prompting user to click tambah
		}
	}

	compare();

});

<!-- Implementing the javascript visual effect for buttons and comparing amount for Jumlah vs Master. END -->

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

  		  if(e.elements[c].name=="perkara2" && e.elements[c].value==\'\') {
			alert(\'Ruang perkara perlu diisi!\');
            count++;
		  }

		  if(e.elements[c].name=="quantity2" && e.elements[c].value==\'\') {
			alert(\'Ruang kuantiti perlu diisi!\');
			count++;
		  }

		  if(e.elements[c].name=="price2" && e.elements[c].value==\'\') {
			alert(\'Ruang harga perlu diisi!\');
            count++;
		  }

		  }

		  if(act == \'Simpan\' || act == \'Kemaskini\') {
  
		  if(e.elements[c].name=="batchNo" && e.elements[c].value==\'\') 
		  	{
			alert(\'Ruang batch perlu diisi!\');
            count++;
		 	}

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

	function calculateKredit(pk) {
		// Get the values from the input fields
		var num1 = document.getElementById("quantity[" + pk + "]").value;
		var num2 = document.getElementById("price[" + pk + "]").value;

		// Perform the multiplication
		var result = num1 * num2;

		// Update the result input
		document.getElementById("kredit[" + pk + "]").value = result;
	}

	function calculate() {
		// Get the values from the input fields
		var num1 = document.getElementById("quantity2").value;
		var num2 = document.getElementById("price2").value;

		// Perform the multiplication
		var result = num1 * num2;

		// Update the result input
		document.getElementById("kredit2").value = result;
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