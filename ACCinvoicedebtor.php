<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCinvoicedebtor.php
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

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCinvoiceList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;INVOIS/PENGHUTANG</b>';

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
if (!isset($page))	$page = "htg";

$display = 0;
if ($invNo && $action == "view") {
	$sql = "SELECT a.*, b.*
			FROM   cb_invoice a, generalacc b
			WHERE   a.companyID = b.ID and invNo = '" . $invNo . "'";

	$rs 				= $conn->Execute($sql);
	$invNo 				= $rs->fields('invNo');

	$tarikh_inv 		= $rs->fields('tarikh_inv');
	$tarikh_inv 		= substr($tarikh_inv, 8, 2) . "/" . substr($tarikh_inv, 5, 2) . "/" . substr($tarikh_inv, 0, 4);

	$kod_project 		= $rs->fields('kod_project');
	$kod_jabatan 		= $rs->fields('kod_jabatan');
	$kod_bank 			= $rs->fields('kod_bank');

	$disahkan 			= $rs->fields('disahkan');
	$disedia 			= $rs->fields('disedia');
	$disemak 			= $rs->fields('disemak');
	$tarikh_disedia 	= $rs->fields('tarikh_disedia');
	$tarikh_disedia 	= substr($tarikh_disedia, 8, 2) . "/" . substr($tarikh_disedia, 5, 2) . "/" . substr($tarikh_disedia, 0, 4);
	$tarikh_disemak		= $rs->fields('tarikh_disemak');
	$tarikh_disemak 	= substr($tarikh_disemak, 8, 2) . "/" . substr($tarikh_disemak, 5, 2) . "/" . substr($tarikh_disemak, 0, 4);
	$tarikh_disahkan	= $rs->fields('tarikh_disahkan');
	$tarikh_disahkan 	= substr($tarikh_disahkan, 8, 2) . "/" . substr($tarikh_disahkan, 5, 2) . "/" . substr($tarikh_disahkan, 0, 4);
	$tarikh_akhir		= $rs->fields('tarikh_akhir');
	$tarikh_akhir 		= substr($tarikh_akhir, 8, 2) . "/" . substr($tarikh_akhir, 5, 2) . "/" . substr($tarikh_akhir, 0, 4);
	$description 		= $rs->fields('description');
	$nama 				= $rs->fields('name');
	//$maklumat        	= $rs->fields('maklumat');
	$batchNo 			= $rs->fields('batchNo');
	$companyID        	= $rs->fields('companyID');
	$b_Baddress 		= $rs->fields('b_Baddress');
	$code 				= $rs->fields('code');
	$b_kodGL 			= $rs->fields('b_kodGL');
	$invLhdn 			= $rs->fields('invLhdn'); //LHDN-UID
	$tinLhdn 			= dlookup("generalacc", "b_tinLhdn", "ID=" . tosql($companyID, "Text"));

	//-----------------
	$sql3 = "SELECT * FROM transactionacc WHERE docNo = '" . $invNo . "' AND addminus IN (1) ORDER BY ID";
	$rsDetail = $conn->Execute($sql3);
	if ($rsDetail->RowCount() < 1)
		$noTran = true;
} elseif ($action == "new") {
	$getNo = "SELECT MAX(CAST(right(invNo,6)
	 		  AS SIGNED INTEGER )) AS nombor 
	          FROM cb_invoice";

	$rsNo = $conn->Execute($getNo);
	if ($rsNo) {
		$nombor = intval($rsNo->fields('nombor')) + 1;
		$nombor = sprintf("%06s", $nombor);
		$invNo 	= 'INV' . $nombor;
	} else {
		$invNo 	= 'INV000001';
	}
}

if (!isset($tarikh_inv)) $tarikh_inv = date("d/m/Y");
if (!isset($tarikh_disedia)) $tarikh_disedia = date("d/m/Y");
if (!isset($tarikh_akhir)) $tarikh_akhir = date("d/m/Y");

if ($perkara2) {
	$updatedBy 	    = get_session("Cookie_userName");
	$updatedDate    = date("Y-m-d H:i:s");

	$deductID   = $perkara2;
	$addminus   = 1;
	$cajAmt     = 0.0;

	$coreID     = dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));

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
		"discount," .
		"tax," .
		"pymtID," .
		"pymtRefer," .
		"pymtAmt," .
		"desc_akaun," .
		"updatedBy," .
		"updatedDate," .
		"createdBy," .
		"createdDate) " .
		" VALUES (" .
		"'" . $invNo . "', " .
		"'" . 5 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $deductID . "', " .
		"'" . $addminus . "', " .
		"'" . $coreID . "', " .
		"'" . $price2 . "', " .
		"'" . $quantity2 . "', " .
		"'" . $discount2 . "', " .
		"'" . $tax2 . "', " .
		"'" . 66 . "', " .
		"'" . $b_kodGL . "', " .
		"'" . $debit2 . "', " .
		"'" . $desc_akaun2 . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "')";

	if ($display) print $sSQL . '<br />';
	else {
		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Kemaskini Invois - ' . $invNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

		print '<script>
		window.location = "?vw=ACCinvoicedebtor&mn=' . $mn . '&action=view&invNo=' . $invNo . '";
		</script>';
	}
}

if ($action == "Hapus") {
	if (count($pk) > 0) {
		$sWhere = "";
		foreach ($pk as $val) {
			$sSQL   = '';
			$sWhere = "ID='" . $val . "'";
			$docNo = dlookup("transactionacc", "docNo", $sWhere);
			$sSQL   = "DELETE FROM transactionacc WHERE " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);

			$strActivity = $_POST['Submit'] . 'Hapus Kandungan Invois - ' . $docNo;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCinvoicedebtor&mn=' . $mn . '&action=view&invNo=' . $invNo . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $perkara || $desc_akaun) {
	$updatedBy 	    = get_session("Cookie_userName");
	$updatedDate    = date("Y-m-d H:i:s");
	$Master2 		= dlookup("generalacc", "parentID", "ID = '" . $b_kodGL . "'");
	$tarikh_inv     = saveDateDb($tarikh_inv);
	$yymm           = substr($tarikh_inv, 0, 4) . substr($tarikh_inv, 5, 2);
	$tarikh_disedia = saveDateDb($tarikh_disedia);
	$tarikh_akhir   = saveDateDb($tarikh_akhir);
	$sSQL   = "";
	$sWhere = "";
	$sWhere = "invNo='" . $invNo . "'";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE cb_invoice SET " .

		"batchNo='" . $batchNo . "'," .
		"tarikh_inv='" . $tarikh_inv . "'," .
		"kod_project='" . $kod_project . "'," .
		"kod_bank='" . $kod_bank . "'," .
		"kod_jabatan='" . $kod_jabatan . "'," .
		"companyID='" . $companyID . "'," .
		"invLhdn='" . $invLhdn . "'," .
		"outstandingbalance='" . $totalDb . "'," .
		"description='" . $description . "'," .
		"disedia='" . $disedia . "'," .
		"disemak='" . $disemak . "'," .
		"disahkan='" . $disahkan . "'," .
		"tarikh_disedia='" . $tarikh_disedia . "'," .
		"tarikh_akhir='" . $tarikh_akhir . "'," .
		"updatedDate='" . $updatedDate . "'," .
		"updatedBy='" . $updatedBy . "'";

	$sSQL = $sSQL . $sWhere;

	$sSQL1   = "";
	$sWhere1 = "";
	$sWhere1 = "docNo='" . $invNo . "' AND addminus='" . 0 . "'";
	$sWhere1 = " WHERE (" . $sWhere1 . ")";
	$sSQL1	= "UPDATE transactionacc SET " .
		"pymtRefer='" . $companyID . "'," .
		"deductID='" . $b_kodGL . "'," .
		"MdeductID='" . $Master2 . "'," .
		"batchNo='" . $batchNo . "'," .
		"desc_akaun='" . $description . "'," .
		"pymtAmt='" . $masterAmt . "'";

	$sSQL1 = $sSQL1 . $sWhere1;

	$sSQL2   = "";
	$sWhere2 = "";
	$sWhere2 = "docNo='" . $invNo . "'";
	$sWhere2 = " WHERE (" . $sWhere2 . ")";
	$sSQL2	 = "UPDATE transactionacc SET " .
		"yrmth='" . $yymm . "'," .
		"tarikh_doc='" . $tarikh_inv . "'";

	$sSQL2 = $sSQL2 . $sWhere2;

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);
	$rs = &$conn->Execute($sSQL2);


	////////////////////////////////////////////////////////////////////////////////////////////
	if (count($perkara) > 0) {
		foreach ($perkara as $id => $value) {

			$deductID   = $value;
			$coreID     = dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));
			$priceA     = $price[$id];
			$quantityA  = $quantity[$id];
			$discountA  = $discount[$id];
			$taxA       = $tax[$id];
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
				"deductID= '" . $deductID . "'," .
				"addminus= '" . $addminus . "'," .
				"coreID= '" . $coreID . "'," .
				"price= '" . $priceA . "'," .
				"quantity= '" . $quantityA . "'," .
				"discount= '" . $discountA . "'," .
				"tax= '" . $taxA . "'," .
				"pymtAmt= '" . $pymtAmt . "'," .
				"updatedDate= '" . $updatedDate . "'," .
				"updatedBy= '" .  $updatedBy . "'";

			$sSQL   .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	//////////////////////////PROJEK//////////////////////////////////////////////////////////////
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
				$pymtAmt    = $debit[$id];
				$addminus   = 1;
			} else {
				$pymtAmt = $kredit[$id];
				$addminus = 0;
			}
			$sSQL   = "";
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
	window.location = "?vw=ACCinvoicedebtor&mn=' . $mn . '&action=view&invNo=' . $invNo . '";
	</script>';
	}
}
//pilihan simpan
elseif ($action == "Simpan" || $simpan) {
	$updatedBy 	    = get_session("Cookie_userName");
	$updatedDate    = date("Y-m-d H:i:s");
	$tarikh_inv     = saveDateDb($tarikh_inv);
	$tarikh_akhir   = saveDateDb($tarikh_akhir);

	// help prevent double entry by multiple users ----begin
	$getMax2 = "SELECT MAX(CAST(right(invNo,6) AS SIGNED INTEGER )) AS no2 FROM cb_invoice";
	$rsMax2  = $conn->Execute($getMax2);
	$max2    = sprintf("%06s", $rsMax2->fields('no2'));

	if ($rsMax2) {
		$max2 = intval($rsMax2->fields('no2')) + 1;
		$max2 = sprintf("%06s", $max2);
		$invNo2 	= 'INV' . $max2;
	} else {
		$invNo2 	= 'INV000001';
	}
	//-----end

	$sSQL = "";
	$sSQL	= "INSERT INTO cb_invoice (" .
		"invNo, " .
		"batchNo, " .
		"invLhdn, " .
		"kod_project, " .
		"kod_bank, " .
		"kod_jabatan, " .
		"companyID, " .
		"kodGL, " .
		"tarikh_inv, " .
		"outstandingbalance, " .
		"description, " .
		"disedia, " .
		"disemak, " .
		"tarikh_disedia, " .
		"tarikh_disemak, " .
		"disahkan, " .
		"tarikh_disahkan, " .
		"tarikh_akhir, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .

		" VALUES (" .
		"'" . $invNo2 . "', " .
		"'" . $batchNo . "', " .
		"'" . $invLhdn . "', " .
		"'" . $kod_project . "', " .
		"'" . $kod_bank . "', " .
		"'" . $kod_jabatan . "', " .
		"'" . $companyID . "', " .
		"'" . $b_kodGL . "', " .
		"'" . $tarikh_inv . "', " .
		"'" . $totalDb . "', " .
		"'" . $description . "', " .
		"'" . $disedia . "', " .
		"'" . $disemak . "', " .
		"'" . $tarikh_disedia . "', " .
		"'" . $tarikh_disemak . "', " .
		"'" . $disahkan . "', " .
		"'" . $tarikh_disahkan . "', " .
		"'" . $tarikh_akhir . "', " .
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
		"yrmth," .
		"deductID," .
		"MdeductID," .
		"addminus," .
		"pymtID," .
		"pymtRefer," .
		"pymtAmt," .
		"desc_akaun," .
		"updatedBy," .
		"updatedDate," .
		"createdBy," .
		"createdDate) " .

		" VALUES (" .
		"'" . $invNo2 . "', " .
		"'" . $tarikh_inv . "', " .
		"'" . 5 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $b_kodGL . "', " .
		"'" . $Master2 . "', " .
		"'" . 0 . "', " .
		"'" . 66 . "', " .
		"'" . $companyID . "', " .
		"'" . $masterAmt . "', " .
		"'" . $description . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "')";

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);

	$getMax = "SELECT MAX(CAST(right(invNo,6) AS SIGNED INTEGER )) AS no FROM cb_invoice";
	$rsMax  = $conn->Execute($getMax);
	$max    = sprintf("%06s", $rsMax->fields('no'));
	
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCinvoicedebtor&mn=' . $mn . '&action=view&add=1&invNo=INV' . $max . '";
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
	'<div class="maroon" align="left">' . $strHeaderTitle . '&nbsp;&nbsp;&nbsp;' . $recurring . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<div class="table-responsive"><form name="MyForm" action="?vw=ACCinvoicedebtor&mn=' . $mn . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';


print $strTemp;
print '
<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>INV number</td>
				<td valign="top"></td><td><input class="form-controlx"  id="invNo" name="invNo" value="' . $invNo . '" type="text" size="20" maxlength="50"></td>
				<tr><td nowrap="nowrap">* Batch</td><td valign="top"></td><td>' . selectbatchINV($batchNo, 'batchNo') . '</td></tr>				
				<tr><td nowrap="nowrap">Projek</td><td valign="top"></td><td>' . selectproject($kod_project, 'kod_project') . '</td></tr>
				<tr><td nowrap="nowrap">Jabatan</td><td valign="top"></td><td>' . selectjabatan($kod_jabatan, 'kod_jabatan') . '</td></tr>
		</table>
	</td>	
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh Invois</td>
				<td valign="top"></td>
				<td>
					<div class="input-group" id="tarikh_inv">
						<input type="text" name="tarikh_inv" class="form-controlx" placeholder="dd/mm/yyyy"
						data-provide="datepicker" data-date-container="#tarikh_inv"
						data-date-autoclose="true" value="' . $tarikh_inv . '">
							<div class="input-group-append">
								<span class="input-group-text">
									<i class="mdi mdi-calendar"></i>
								</span>
							</div>
					</div>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr><td colspan="3"><hr class="mt-3"></td></tr>';

print '
<tr colspan="3">
	<td valign="top"><input name="j" type="hidden" value="tiada">
	
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top">Kepada</td>
			</tr>

			<tr>
				<td>* Kod Penghutang</td><td valign="top"></td>
				<td><input name="code" value="' . $code . '" type="text" size="20" maxlength="50"  class="form-controlx" readonly/>&nbsp;';

print '<input type="button" class="btn btn-info btn-sm" id="invButton" value="Pilih" onclick="window.open(\'ACCidpenghutang.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
<input type="button" class="btn btn-sm btn-primary" id="addDebtorButton" value="Tambah" onclick="window.open(\'generalAddUpdateACC.php?action=tambah&cat=AC&sub=&page=' . $page . '\',\'sort\',\'top=50,left=50,width=700,height=650,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
';

print '&nbsp;<input name="loan_no" type="hidden" value=""></td>
				</td>
			</tr>

			<tr>
				<td valign="top">Nama</td><td valign="top"></td><td><input name="nama"  value="' . $nama . '" type="text" size="50" maxlength="50" class="form-controlx" readonly/>
		    	</td>
		    </tr>

			<tr>
				<td valign="top">Alamat</td>
				<td valign="top"></td>
				<td><textarea name="b_Baddress" cols="50" rows="4" class="form-controlx" readonly>' . $b_Baddress . '</textarea></td>
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
			';

$sql3 = "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = '" . $invNo . "' ORDER BY ID";
$rsDetail1 = $conn->Execute($sql3);

print '<tr>
			<td valign="top">Master Jumlah (Rp)</td>
			<td valign="top"></td>
			<td><input id="master" class="form-controlx" value="' . $rsDetail1->fields('pymtAmt') . '" type="text" size="20" maxlength="10" readonly/></td>
			</tr>
		  
		  	<tr>
				</td><td><input type=hidden name="companyID" value="' . $companyID . '" type="text" size="4" maxlength="50" class="data" />
		    	</td>
		    </tr>

			<tr>
				<td valign="top">Tarikh Bayaran Terakhir</td>
				<td valign="top"></td>
				<td>
					<div class="input-group" id="tarikh_akhir">
					<input type="text" name="tarikh_akhir" class="form-controlx" placeholder="dd/mm/yyyy"
						data-provide="datepicker" data-date-container="#tarikh_akhir"
						data-date-autoclose="true" value="' . $tarikh_akhir . '">
					<div class="input-group-append">
						<span class="input-group-text">
							<i class="mdi mdi-calendar"></i></span>
					</div>
					</div>
				</td>
			</tr>

		    <tr>
				</td><td><input type=hidden name="b_kodGL" value="' . $b_kodGL . '" size="4" maxlength="50" class="data" />
		    	</td>
		    </tr>

		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>';

//implement a visual effect css for button 'Kemaskini' and 'Tambah' to guide user what to do
//------------- START
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
//------------- END

//----------
if ($action == "view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $invNo . "'"))) {
	print '
	<tr>
			<td align= "right" colspan="3">';
	if (!$add) print '

			<!-- Implementing the visual effect on button Tambah for akaun. START -->
			<div class="request-loader-container" id="loaderContainer">
				<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCinvoicedebtor&mn=' . $mn . '&action=' . $action . '&invNo=' . $invNo . '&add=1\';">
				<div class="request-loader" id="requestLoaderTambah"></div>
			</div>
			<!-- Implementing the visual effect on button Tambah for akaun. END -->
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
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="table table-sm table-striped">
			<tr class="table-primary">
				<td nowrap="nowrap"><b>Bil</b></td>
				<td nowrap="nowrap"><b>* Akaun</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="center"><b>* Kuantiti</b></td>	
				<td nowrap="nowrap" align="right"><b>* Harga Seunit (RM)</b></td>
                <td nowrap="nowrap" align="right"><b>Diskaun (%)</b></td>					
				<td nowrap="nowrap" align="right"><b>Cukai SST (8%)</b></td>
				<td nowrap="nowrap" align="right"><b>Jumlah (RM)</b></td>
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

			$id         = $rsDetail->fields('ID');
			$perkara    = $rsDetail->fields('deductID');
			$kod_akaun  = dlookup("generalacc", "parentID", "ID=" . $perkara);
			$namaparent = dlookup("generalacc", "name", "ID=" . $kod_akaun);
			$debit      = $rsDetail->fields('pymtAmt');
			$desc_akaun = $rsDetail->fields('desc_akaun');
			$quantity   = $rsDetail->fields('quantity');
			$price      = $rsDetail->fields('price');
			$discount 	= $rsDetail->fields('discount');
			$tax 		= $rsDetail->fields('tax');

			if ($rsDetail->fields('addminus')) {
				$kredit = $rsDetail->fields('pymtAmt');
			} else {
				$debit  = $rsDetail->fields('pymtAmt');
			}
			print	   '
			<tr>
				<td class="Data">' . ++$i . '.</td>	

				<td class="Data" nowrap="nowrap">' . strSelect3($id, $perkara, "pendapatan") . '
					<input class="form-control-sm" name="kod_akaun[' . $id . ']" type="hidden" size="10" maxlength="10" value="' . $kod_akaun . '"/>
				</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" class="form-control-sm" rows="4" cols="40" maxlength="500">' . $desc_akaun . '</textarea>
				</td>';


			//column kuantiti dan harga
			print '
				<td class="Data" nowrap="nowrap" align="center">
				<input name="quantity[' . $id . ']" id="quantity[' . $id . ']" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $quantity . '" oninput="calculateDebit(' . $id . ')" readonly/>
				&nbsp;
				</td>

				<td class="Data" nowrap="nowrap" align="right">
				<input name="price[' . $id . ']" id="price[' . $id . ']" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $price . '" oninput="calculateDebit(' . $id . ')" readonly/>
				&nbsp;
				</td>

				<td class="Data" nowrap="nowrap" align="right">
				<input name="discount[' . $id . ']" id="discount[' . $id . ']" type="text" size="10" style="text-align: right;" class="form-control-sm" maxlength="100" value="' . $discount . '" oninput="calculateDebit(' . $id . ')" readonly/>&nbsp;
				&nbsp;
				</td>

				<td class="Data" nowrap="nowrap" align="center">
				<input hidden name="tax[' . $id . ']" id="tax[' . $id . ']" size="3" value="' . $tax . '"readonly/>&nbsp;' . ($tax == 1 ? "Ada" : "Tiada") . '
				&nbsp;
				</td>

				<td class="Data" nowrap="nowrap" align="right">
				<input name="debit[' . $id . ']" id="debit[' . $id . ']" type="text" size="10" style="text-align: right;" maxlength="10" value="' . $debit . '" class="form-control-sm" style="text-align:right;" readonly/>
				&nbsp;
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" name="pk[]" class="form-check-input" value="' . $id . '">&nbsp;</td>

			</tr>';
			$totalDb += $debit;

			$debit = '';
			print '<input type="hidden" name="totalDb" value="' . $totalDb . '">';
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
		disableElementsByName("invNo");
        disableElementsByName("batchNo");
		disableElementsByName("tarikh_inv");
		disableElementsByName("tarikh_akhir");
        disableElementsByName("kod_project");
        disableElementsByName("kod_jabatan");
		disableElementsByName("kod_bank");
		disableElementsByName("code");
        disableElementsByName("nama");
		disableElementsByName("b_Baddress");
		disableElementsByName("invLhdn"); //LHDN-UID
		disableElementsByName("tinLhdn");
		disableElementsByName("disedia");
		disableElementsByName("disahkan");
		disableElementsByName("description");
		disableElementById("bottomButton");
		disableElementById("invButton");
		disableElementById("addDebtorButton");
		});
		</script>
		';
	}
	//------------- END
}

$strDeductIDList    = deductListb2(1, "pendapatan");
$strDeductCodeList  = deductListb2(2, "pendapatan");
$strDeductNameList  = deductListb2(3, "pendapatan");
$name = 'perkara2';

$strSelect = '<select name="' . $name . '" class="form-select-sm" id="deductSelect" onchange="updateDescAkaun()">
			 <option value="">- Pilih -';

for ($i = 0; $i < count($strDeductIDList); $i++) {
	$strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
	if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
	$strSelect .=  '>' . $strDeductCodeList[$i] . '&nbsp;&nbsp;' . $strDeductNameList[$i] . '';
}

$strSelect .= '</select>';

$tax2 = isset($tax2) ? $tax2 : 0;  // Default to 0 if not set

if ($add) {
	print	   '
			<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>	

				<td class="Data">' . $strSelect . '
				<input name="kod_akaun2" type="hidden" size="10" maxlength="10" value="' . $kod_akaun2 . '" class="form-control-sm"/>
				</td>

				<td class="Data" align="left">
					<textarea name="desc_akaun2" class="form-control-sm" rows="4" cols="40" maxlength="500" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
				</td>';

	//column for kuantiti dan harga
	print '
				<td class="Data" align="center">					
				<input  name="quantity2" id="quantity2" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $quantity2 . '" / oninput="calculate()">&nbsp;
				</td>

				<td class="Data" align="right">					
				<input  name="price2" id="price2" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $price2 . '" / oninput="calculate()">&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="discount2" id="discount2" class="form-control-sm" type="text" size="10" style="text-align: right;" maxlength="100" value="' . $discount2 . '" / oninput="calculate()">&nbsp;
				</td>

				<td class="Data" nowrap="nowrap" align="center">
					<input type="radio" name="tax2" id="tax_ada_2' . $id . '" value="1" ' . ($tax2 == 1 ? 'checked' : '') . ' oninput="calculate()"/> Ada<br>
					<input type="radio" name="tax2" id="tax_tiada_2' . $id . '" value="0" ' . ($tax2 == 0 ? 'checked' : '') . ' oninput="calculate()"/> Tiada
				</td>

				<td class="Data" align="right">					
				<input  name="debit2" id="debit2" type="text" size="10" style="text-align: right;" maxlength="10" class="form-control-sm" value="' . $debit2 . '" readonly/>&nbsp;
				</td>

				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}

if ($totalDb <> 0) {
	$clsRM->setValue($totalDb);
	$strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}

print 		'<tr>
				<td class="Data" colspan="7" align="right"><b>Jumlah (RM)</b></td>
				<td class="Data" id="totalJumlah" align="right"><b>' . number_format($totalDb, 2) . '&nbsp;</b></td>
				<td class="Data" align=""><b>&nbsp;</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">

			<tr>
			
			<td nowrap="nowrap">Jumlah Dalam Perkataan</td>
			<td valign="top"></td>
			<td>
				<input class="form-controlx" name="" size="80" maxlength="80" value="' . $strTotal . '" readonly>
				<input class="form-controlx" type="hidden" name="masterAmt" value="' . $totalDb . '">
			</td>

			</tr>


			<tr><td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td><td>' . selectAdmin($disedia, 'disedia') . '</td></tr>
			<tr><td nowrap="nowrap">Disahkan Oleh</td><td valign="top"></td><td>' . selectAdmin($disahkan, 'disahkan') . '</td></tr>
			<tr><td>Bank Bayaran</td><td valign="top"></td><td>' . selectbanks1($kod_bank, 'kod_bank') . '</td></tr>
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td><td valign="top">
					<textarea class="form-controlx" name="description" cols="50" rows="4">' . $description . '</textarea>
				</td>
			</tr>
		</table>
	</td>
</tr>';

$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCinvoicedebtorPrint.php?id=' . $invNo . '\')">&nbsp;

	<!-- Implementing the visual effect on button Kemaskini. START -->
    <div class="request-loader-container" id="loaderContainer">
        <input type="button" name="action" id="bottomButton" value="' . $straction . '" class="btn btn-primary" onclick="CheckField(\'' . $straction . '\')">
        <div class="request-loader" id="requestLoader"></div>
    </div><br><br>
	<!-- Implementing the visual effect on button Kemaskini. END -->
	';
if ($straction == 'Simpan') print '
	<input type="hidden" name="simpan" value="1">';

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
	<input type="hidden" name="recurrInv" id="recurrInvField" value="">
</form>

';

// create the new invois number for duplication
$getNo = "SELECT MAX(CAST(right(invNo,6) AS SIGNED INTEGER)) AS nombor FROM cb_invoice";
$rsNo = $conn->Execute($getNo);
if ($rsNo) {
	$nombor = intval($rsNo->fields('nombor')) + 1;
	$nombor = sprintf("%06s", $nombor);
	$recurrInv = 'INV' . $nombor;
}

// duplicating the invois
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recurrInv'])) {
	$recurrInv 	= $_POST['recurrInv'];
	$oldInvNo 	= $invNo; // Pass the old invoice number from the form

	print '
	// <script>
	// 	alert("Recurring Invoice Number: ' . $recurrInv . '");
	// 	alert("Old Invoice Number: ' . $oldInvNo . '");
	// </script>';

	$updatedBy 	    = get_session("Cookie_userName");
	$updatedDate    = date("Y-m-d H:i:s");
	// $tarikh_inv     = saveDateDb($tarikh_inv);
	// $tarikh_akhir   = saveDateDb($tarikh_akhir);

	$sSQL 	= "";
	$sSQL	= "INSERT INTO cb_invoice (" .
		"invNo, " .
		"batchNo, " .
		"kod_project, " .
		"kod_bank, " .
		"kod_jabatan, " .
		"companyID, " .
		"kodGL, " .
		// "tarikh_inv, " . //left empty
		"outstandingbalance, " .
		"description, " .
		"disedia, " .
		"disemak, " .
		"tarikh_disedia, " .
		"tarikh_disemak, " .
		"disahkan, " .
		"tarikh_disahkan, " .
		// "tarikh_akhir, " . //left empty
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .

		" VALUES (" .
		"'" . $recurrInv . "', " .
		"'" . $batchNo . "', " .
		"'" . $kod_project . "', " .
		"'" . $kod_bank . "', " .
		"'" . $kod_jabatan . "', " .
		"'" . $companyID . "', " .
		"'" . $b_kodGL . "', " .
		// "'". $tarikh_inv . "', ".
		"'" . $totalDb . "', " .
		"'" . $description . "', " .
		"'" . $disedia . "', " .
		"'" . $disemak . "', " .
		"'" . $tarikh_disedia . "', " .
		"'" . $tarikh_disemak . "', " .
		"'" . $disahkan . "', " .
		"'" . $tarikh_disahkan . "', " .
		// "'". $tarikh_akhir . "', ".
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "')";

	// Step 1: Retrieve rows from transactionacc for the old invNo
	$query = "SELECT * FROM transactionacc WHERE docNo = '$oldInvNo'";
	$result = $conn->Execute($query);

	// Step 2: Loop through the results and insert rows with updated docNo
	if ($result) {
		while (!$result->EOF) {
			// Fetch each row's data
			$row = $result->fields;

			// Modify docNo to recurrInv
			$row['docNo'] = $recurrInv;

			// Construct the INSERT query using the fetched and modified data
			//tarikh_doc is left empty
			$insertQuery = "INSERT INTO transactionacc (
                docNo, docID, batchNo, yrmth, 
                deductID, MdeductID, addminus, price, quantity, discount, tax,
                pymtID, pymtRefer, pymtAmt, desc_akaun, updatedBy, updatedDate, createdBy, createdDate
            ) VALUES (
                '" . $row['docNo'] . "', 
                '" . $row['docID'] . "', 
                '" . $row['batchNo'] . "', 
                '" . $row['yrmth'] . "', 
                '" . $row['deductID'] . "', 
                '" . $row['MdeductID'] . "', 
                '" . $row['addminus'] . "', 
                '" . $row['price'] . "', 
                '" . $row['quantity'] . "', 
				'" . $row['discount'] . "', 
                '" . $row['tax'] . "', 
                '" . $row['pymtID'] . "', 
                '" . $row['pymtRefer'] . "', 
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
		echo "No rows found for the previous invoice number: $oldInvNo.";
	}

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);

	if (!$display) {
		print '<script>
window.location = "?vw=ACCinvoicedebtor&mn=' . $mn . '&action=view&invNo=' . $recurrInv . '";
</script>';
	}
}

//-------------------------------------------------------------- Feature to duplicate/recurring invoice --------------END

print '
<script language="JavaScript">

   // Recurring function
    function Recurring() {
        // Use PHP variable for the new invNo
        var recurrInv = "' . $recurrInv . '";  // Embed PHP variable as a JavaScript string

		// Update the hidden input field value
        var recurrInvField = document.getElementById("recurrInvField");
        if (recurrInvField) {
            recurrInvField.value = recurrInv;

            // Submit the form
            document.getElementById("recurringForm").submit();
        } else {
            console.error("Hidden field with ID \'recurrInvField\' not found.");
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
  
		 if(e.elements[c].name=="price2" && e.elements[c].value==\'\') {
			alert(\'Ruang harga perlu diisi!\');
			count++;
			}

		  if(e.elements[c].name=="quantity2" && e.elements[c].value==\'\') {
			alert(\'Ruang kuantiti perlu diisi!\');
			count++;
			}

		  if(e.elements[c].name=="perkara2" && e.elements[c].value==\'\') {
			alert(\'Ruang akaun perlu diisi!\');
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
			alert(\'Ruang Kod Penghutang perlu diisi!\');
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
		var num3 = document.getElementById("discount2").value;
		var num4 = document.querySelector(\'input[name="tax2"]:checked\')?.value || 0;
    	var num4 = parseInt(num4); // Convert num4 to an integer

		var discDecimal = num3/100;

		// Perform the multiplication
		var amount = num1 * num2;
		var disc   = amount * discDecimal;
		var result = amount - disc;

		if (num4 === 1) {
			var sst = result * (8/100);
			var result2 = sst + result;

		// Update the result input
			document.getElementById("debit2").value = result2.toFixed(2);
		} else {	
			// Update the result input
			document.getElementById("debit2").value = result.toFixed(2);
		}
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