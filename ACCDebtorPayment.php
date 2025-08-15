<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCDebtorPayment.php
 *			Date 		: 19/10/2006
 *			Keywords 	: disableButton, disable, noRecords, effect, duplicate (to prevent user fault)
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCDebtorList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;PENERIMAAN BAYARAN PENGHUTANG</b>';

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display = 0;
if ($RVNo && $action == "view") {
	$sql = "SELECT * FROM cb_payments a, generalacc b WHERE a.companyID = b.ID and a.RVNo = '" . $RVNo . "'";

	$rs 			= $conn->Execute($sql);
	$RVNo 			= $rs->fields('RVNo');
	$tarikh_RV 		= $rs->fields('tarikh_RV');
	$tarikh_RV 		= substr($tarikh_RV, 8, 2) . "/" . substr($tarikh_RV, 5, 2) . "/" . substr($tarikh_RV, 0, 4);
	$tarikh_RV 		= toDate("d/m/y", $rs->fields('tarikh_RV'));
	$batchNo 		= $rs->fields('batchNo');

	$kod_bank 		= $rs->fields('kod_bank');
	$bankparent 	= dlookup("generalacc", "parentID", "ID=" . $kod_bank);

	$kod_project 	= $rs->fields('kod_project');
	$kod_jabatan 	= $rs->fields('kod_jabatan');
	$companyID 	    = $rs->fields('companyID');
	$catatan 		= $rs->fields('catatan');
	$createdDate 	= $rs->fields('createdDate');
	$createdBy 		= $rs->fields('createdBy');
	$updatedDate 	= $rs->fields('updatedDate');
	$updatedBy 		= $rs->fields('updatedBy');
	$invNo			= $rs->fields('invNo');
	$amt			= $rs->fields('outstandingbalance');
	$cara_bayar		= $rs->fields('cara_bayar');
	$disedia		= $rs->fields('disedia');
	$disemak		= $rs->fields('disemak');
	$b_Baddress 	= $rs->fields('b_Baddress');
	$code 			= $rs->fields('code');
	$nama			= $rs->fields('name');
	$kodGL 			= $rs->fields('b_kodGL');
	$invLhdn 		= dlookup("cb_invoice", "invLhdn", "invNo=" . tosql($invNo, "Text")); //LHDN-UID
	$invComp 		= dlookup("cb_invoice", "invComp", "invNo=" . tosql($invNo, "Text"));
	$tinLhdn 		= dlookup("generalacc", "b_tinLhdn", "ID=" . tosql($companyID, "Text"));

	// kod carta akaun
	//-----------------
	$sql2 = "SELECT * FROM transactionacc WHERE docNo = '" . $RVNo . "' AND addminus IN (1) ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
	if ($rsDetail->RowCount() < 1)
		$noTran = true;

	//disable the tambah button when user already choose the same inv in newer RV, to prevent miscalculations & user error
	//------------- START
	$disableButton = false;

	while (!$rsDetail->EOF) {
		$id = $rsDetail->fields['ID'];
		if (!empty($invNo)) {
			$sSQLCheck = "SELECT 1 
						FROM transactionacc 
						WHERE pymtReferC = '$invNo' 
						AND ID > '$id' 
						AND docNo != '$RVNo'
						AND status NOT IN (5) -- status 5 is credit note
						LIMIT 1";
			$rsCheck = $conn->Execute($sSQLCheck);

			if ($rsCheck->RecordCount() > 0) {
				$disableButton = true;
				break;
			}
		}
		$rsDetail->MoveNext();
	}

	$rsDetail->MoveFirst();

	if ($disableButton) {
		print '
		<script>
		document.addEventListener("DOMContentLoaded", function() {
			var disableElementsByName = function(name) {
				var elements = document.getElementsByName(name);
				for (var i = 0; i < elements.length; i++) {
					var element = elements[i];
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
			disableElementsByName("add");
		});
		</script>
		';
	}

	//------------- END

} elseif ($action == "new") {
	$getNo = "SELECT MAX(CAST(right(RVNo,6) AS SIGNED INTEGER )) AS nombor FROM cb_payments";

	$rsNo = $conn->Execute($getNo);
	$tarikh_RV = date("d/m/Y");
	$tarikh_batch = date("d/m/Y");
	if ($rsNo) {
		$nombor = intval($rsNo->fields('nombor')) + 1;
		$nombor = sprintf("%06s",  $nombor);
		$RVNo = 'RV' . $nombor;
	} else {
		$RVNo = 'RV000001';
	}

	if ($invNo) {
		$sqlPay 		= "SELECT * FROM cb_invoice a, generalacc b WHERE a.companyID = b.ID and a.invNo = '" . $invNo . "'";
		$rsPay 			= $conn->Execute($sqlPay);
		$code 			= $rsPay->fields('code');
		$nama			= $rsPay->fields('name');
		$b_Baddress 	= $rsPay->fields('b_Baddress');
		$totalInv 		= $rsPay->fields('outstandingbalance');
		$sSQL1 			= "SELECT SUM(pymtAmt) AS dahbayar FROM transactionacc WHERE pymtReferC = " . tosql($invNo, "Text") . " AND addminus IN (1)";
		$rsB 			= &$conn->Execute($sSQL1);
		$amtBayar 		= $rsB->fields('dahbayar');
		$amt 			= ($totalInv - $amtBayar);
		$companyID 		= $rsPay->fields('companyID');
		$kodGL 			= $rsPay->fields('b_kodGL');
	}
}

if (!isset($tarikh_RV)) $tarikh_RV = date("d/m/Y");
if (!isset($tarikh_batch)) $tarikh_batch = date("d/m/Y");
if ($cara_bayar) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$createdBy 	= get_session("Cookie_userName");
	$createdDate = date("Y-m-d H:i:s");

	$deductID = $kodGL;
	$addminus = 1;
	$cajAmt = 0.0;

	if ($pymtAmt == '')
		$pymtAmt = '0.0';
	$sSQL	= "INSERT INTO transactionacc (" .
		"docNo," .
		"docID," .
		"batchNo," .
		"yrmth," .
		"deductID," .
		"MdeductID," .
		"cara_bayar," .
		"addminus," .
		"pymtID," .
		"pymtAmt," .
		"pymtRefer," .
		"pymtReferC," .
		"desc_akaun," .
		"status," .
		"isApproved," .
		"approvedDate," .
		"createdDate," .
		"createdBy," .
		"tarikh_batch) " .

		" VALUES (" .
		"'" . $RVNo . "', " .
		"'" . 6 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $deductID . "', " .
		"'" . $deductID . "', " .
		"'" . $cara_bayar . "', " .
		"'" . $addminus . "', " .
		"'" . 66 . "', " .
		"'" . $kredit2 . "', " .
		"'" . $companyID . "', " .
		"'" . $invNo . "', " .
		"'" . $desc_akaun2 . "', " .
		"'" . $status . "', " .
		"'" . $isApproved . "', " .
		"'" . $createdDate . "', " .
		"'" . $createdDate . "', " .
		"'" . $createdBy . "', " .
		"'" . $tarikh_batch . "')";

	if ($display) print $sSQL . '<br />';
	else {
		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Kemaskini Terima Bayaran - ' . $RVNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

		print '<script>
		window.location = "?vw=ACCDebtorPayment&mn=' . $mn . '&action=view&RVNo=' . $RVNo . '";
		</script>';
	}
}

if ($action == "Hapus") {
	if (count($pk) > 0) {
		$sWhere = "";
		foreach ($pk as $val) {
			$sSQL = '';
			$sWhere = "ID='" . $val . "'";

			$docNo = dlookup("transactionacc", "docNo", $sWhere);

			$sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);

			$strActivity = $_POST['Submit'] . 'Hapus Kandungan Terima Bayaran - ' . $docNo;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCDebtorPayment&mn=' . $mn . '&action=view&RVNo=' . $RVNo . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $carabayar || $desc_akaun) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$tarikh_RV 		= saveDateDb($tarikh_RV);
	$tarikh_batch 	=	saveDateDb($tarikh_batch);
	$yymm 	= substr($tarikh_RV, 0, 4) . substr($tarikh_RV, 5, 2);
	$sSQL 	= "";
	$sWhere = "";
	$sWhere = "RVNo='" . $RVNo . "'";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE cb_payments SET " .

		"RVNo='" . $RVNo . "'," .
		"tarikh_RV='" . $tarikh_RV . "'," .
		"batchNo='" . $batchNo . "'," .
		"kod_bank='" . $kod_bank . "'," .
		"kod_project='" . $kod_project . "'," .
		"kod_jabatan='" . $kod_jabatan . "'," .
		"companyID='" . $companyID . "'," .
		"catatan='" . $catatan . "'," .
		"updatedDate='" . $updatedDate . "'," .
		"updatedBy='" . $updatedBy . "'," .
		"invNo='" . $invNo . "'," .
		"outstandingbalance='" . $amt . "'," .
		"balance='" . $balance . "'," .
		"disedia='" . $disedia . "'," .
		"disemak='" . $disemak . "'";

	$sSQL = $sSQL . $sWhere;

	$sSQL1 = "";
	$sWhere1 = "";
	$sWhere1 = "docNo='" . $RVNo . "' AND addminus='" . 0 . "'";
	$sWhere1 = " WHERE (" . $sWhere1 . ")";
	$sSQL1	= "UPDATE transactionacc SET " .
		"pymtReferC='" . $invNo . "'," .
		"deductID='" . $kod_bank . "'," .
		"MdeductID='" . $bankparent . "'," .
		"batchNo='" . $batchNo . "'," .
		"desc_akaun='" . $catatan . "'," .
		"pymtAmt='" . $masterAmt . "'";

	$sSQL1 = $sSQL1 . $sWhere1;

	$sSQL2 = "";
	$sWhere2 = "";
	$sWhere2 = "docNo='" . $RVNo . "'";
	$sWhere2 = " WHERE (" . $sWhere2 . ")";
	$sSQL2	= "UPDATE transactionacc SET " .
		"yrmth='" . $yymm . "'," .
		"tarikh_doc='" . $tarikh_RV . "'";

	$sSQL2 = $sSQL2 . $sWhere2;

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);
	$rs = &$conn->Execute($sSQL2);
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if (count($carabayar) > 0) {
		foreach ($carabayar as $id => $value) {

			$cara_bayar = $value;
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
				",pymtReferC= '" . $invNo . "'" .
				",cara_bayar= '" . $cara_bayar . "'" .
				",addminus= '" . $addminus . "'" .
				",pymtAmt= '" . $pymtAmt . "'" .
				",updatedDate= '" . $updatedDate . "'" .
				",updatedBy= '" .  $updatedBy . "'";
			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}

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
	/////////////////////////////////////////////////////////////////////////////
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCDebtorPayment&mn=' . $mn . '&action=view&RVNo=' . $RVNo . '";
	</script>';
	}
}

//pilihan simpan
elseif ($action == "Simpan" || $simpan) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$tarikh_RV = saveDateDb($tarikh_RV);
	$tarikh_batch =	saveDateDb($tarikh_batch);

	// help prevent double entry by multiple users ----begin
	$getMax2 	= "SELECT MAX(CAST(right(RVNo,6) AS SIGNED INTEGER )) AS no2 FROM cb_payments";
	$rsMax2 	= $conn->Execute($getMax2);
	$max2   	= sprintf("%06s", $rsMax2->fields('no2'));

	if ($rsMax2) {
		$max2 = intval($rsMax2->fields('no2')) + 1;
		$max2 = sprintf("%06s",  $max2);
		$RVNo2 = 'RV' . $max2;
	} else {
		$RVNo2 = 'RV000001';
	}
	//-----end

	$sSQL = "";
	$sSQL	= "INSERT INTO cb_payments (" .

		"RVNo, " .
		"tarikh_RV, " .
		"batchNo, " .
		"kod_bank, " .
		"kod_project, " .
		"kod_jabatan, " .
		"companyID, " .
		"catatan, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy, " .
		"invNo, " .
		"outstandingbalance, " .
		"balance, " .
		"disedia, " .
		"disemak) " .
		" VALUES (" .

		"'" . $RVNo2 . "', " .
		"'" . $tarikh_RV . "', " .
		"'" . $batchNo . "', " .
		"'" . $kod_bank . "', " .
		"'" . $kod_project . "', " .
		"'" . $kod_jabatan . "', " .
		"'" . $companyID . "', " .
		"'" . $catatan . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $invNo . "', " .
		"'" . $amt . "', " .
		"'" . $amt . "', " .
		"'" . $disedia . "', " .
		"'" . $disemak . "')";

	$sSQL1 = "";
	$sSQL1	= "INSERT INTO transactionacc (" .

		"docNo," .
		"tarikh_doc," .
		"docID," .
		"batchNo," .
		"yrmth," .
		"deductID," .
		"cara_bayar," .
		"addminus," .
		"pymtID," .
		"pymtAmt," .
		"pymtReferC," .
		"desc_akaun," .
		"status," .
		"isApproved," .
		"approvedDate," .
		"createdDate," .
		"createdBy," .
		"tarikh_batch) " .

		" VALUES (" .
		"'" . $RVNo2 . "', " .
		"'" . $tarikh_RV . "', " .
		"'" . 6 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $kod_bank . "', " .
		"'" . $cara_bayar . "', " .
		"'" . 0 . "', " .
		"'" . 66 . "', " .
		"'" . $masterAmt . "', " .
		"'" . $invNo . "', " .
		"'" . $catatan . "', " .
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


	$getMax = "SELECT MAX(CAST(right(RVNo,6) AS SIGNED INTEGER)) AS no FROM cb_payments";
	$rsMax = $conn->Execute($getMax);
	$max = sprintf("%06s", $rsMax->fields('no'));
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCDebtorPayment&mn=' . $mn . '&action=view&add=1&RVNo=RV' . $max . '";
	</script>';
	}
}

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<div class="table-responsive"><form name="MyForm" action="?vw=ACCDebtorPayment&mn=' . $mn . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print
	'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>Nombor RV</td>
				<td valign="top"></td>
				<td>
					<input  name="RVNo" value="' . $RVNo . '" type="text" size="20" maxlength="50" class="form-control-sm" readonly/>
				</td>
			</tr>

			<tr>
				<td>* Batch</td>
				<td valign="top"></td>
				<td>' . selectbatch($batchNo, 'batchNo') . '</td>
			</tr>
		
			<tr>
				<td>Tarikh</td>
				<td valign="top"></td>
				<td>
					<div class="input-group" id="tarikh_RV">
						<input type="text" name="tarikh_RV" class="form-control-sm" placeholder="dd/mm/yyyy"
						data-provide="datepicker" data-date-container="#tarikh_RV"
						data-date-autoclose="true" value="' . $tarikh_RV . '">
							<div class="input-group-append">
								<span class="input-group-text">
									<i class="mdi mdi-calendar"></i>
								</span>
							</div>
					</div>
				</td>
			</tr>
			
			<tr>
				<td>* Bank</td>
				<td valign="top"></td>
				<td>' . selectbanks($kod_bank, 'kod_bank') . '</td>
			</tr>

			<tr>
				<td>Projek</td>
				<td valign="top"></td>
				<td>' . selectproject($kod_project, 'kod_project') . '</td>
			</tr>
			<tr>
				<td>Jabatan</td>
				<td valign="top"></td>
				<td>' . selectjabatan($kod_jabatan, 'kod_jabatan') . '</td>
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
<td>* Kod Penghutang</td><td valign="top"></td>
<td><input name="code" value="' . $code . '" type="text" size="20" maxlength="50"  class="form-controlx" readonly/>&nbsp;';

print '<input type="button" class="btn btn-sm btn-info" id="invButton" value="Pilih Invois" onclick="window.open(\'ACCmemberpemiutangL.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;';
print '<input type="button" class="btn btn-sm btn-primary" id="compButton" value="Pilih Syarikat" onclick="window.open(\'ACCpenghutang.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';

print '&nbsp;

</td>
</tr>

<tr>
 <td valign="top">Nama Syarikat</td>
 <td valign="top"></td>
 <td><input name="nama"  value="' . $nama . '" size="50" maxlength="50"  class="form-control-sm" readonly /></td>
 </tr>

<tr>
<td valign="top">Alamat Syarikat</td>
<td valign="top"></td>
<td><textarea name="b_Baddress" cols="50" rows="4" class="form-control-sm" readonly>' . $b_Baddress . '</textarea></td>
</tr>

   <tr>
<td valign="top">Amaun Invois/Tunggakan (RM)</td>
<td valign="top"></td>
<td><input name="amt"  value="' . $amt . '" size="20" maxlength="50"  class="form-controlx" readonly/></td>
</tr>

<tr>
<td valign="top">Nombor Invois</td>
<td valign="top"></td>
<td><input name="invNo" value="' . $invNo . '" size="20" maxlength="50"  class="form-controlx" readonly /></td>
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
	<td><input name="invComp" value="' . $invComp . '" size="20" maxlength="50"  class="form-controlx" readonly/></td>
</tr>
';

$sql3 = "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = '" . $RVNo . "' ORDER BY ID";
$rsDetail1 = $conn->Execute($sql3);

print '
<tr>
<td valign="top">Master Amaun (RM)</td>
<td valign="top"></td>
<td><input id="master" class="form-controlx" value="' . $rsDetail1->fields('pymtAmt') . '" type="text" size="20" maxlength="10" readonly/></td>
</tr>

<tr>
	<td><input type=hidden name="companyID" value="' . $companyID . '" type="text" size="4" maxlength="50" class="form-control-sm" /></td>
</tr>

<tr>
	<td><input type=hidden name="kodGL" value="' . $kodGL . '" type="text" size="4" maxlength="50" class="form-control-sm" /></td>
</tr>

</table>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
</tr>';

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
if ($action == "view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $RVNo . "'"))) {

	print '
	<tr>
		<td align= "right" colspan="3">';
	if (!$add) print '

		<!-- Implementing the visual effect on button Tambah for akaun. START -->
		<div class="request-loader-container" id="loaderContainer">
			<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCDebtorPayment&mn=' . $mn . '&action=' . $action . '&RVNo=' . $RVNo . '&add=1\';">
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
		<table border="0" cellspacing="1" cellpadding="3" width="100%" class="table table-sm table-striped">
			<tr class="table-primary">
				<td nowrap="nowrap"><b>Bil</b></td>
				<td nowrap="nowrap"><b>* Cara Bayaran</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="right"><b>* Amaun (RM)</b></td>
				<td></td>
			</tr>';

// Determine if there are no records for akaun. If so, visually guide user to Tambah akaun instead of click Kemaskini.
//------------- START
$noRecords = true;

if ($action == "view") {

	if ($rsDetail->RecordCount() > 0) {
		//If there are records, set flag to false. User can click Kemaskini.
		$noRecords = false;
		$i = 0;
		$totalKt = 0;
		$totalBal = 0;

		while (!$rsDetail->EOF) {

			$id = $rsDetail->fields('ID');
			$ruj = $rsDetail->fields('pymtRefer');
			$carabayar = $rsDetail->fields('cara_bayar');
			$c_bayar = dlookup("generalacc", "name", "ID=" . $carabayar);
			$kredit = $rsDetail->fields('pymtAmt');
			$desc_akaun =	$rsDetail->fields('desc_akaun');


			if ($rsDetail->fields('addminus')) {
				$kredit = $rsDetail->fields('pymtAmt');
			} else {
				$debit = $rsDetail->fields('pymtAmt');
			}
			print
				'<tr>
				<td class="Data">' . ++$i . '.</td>	

				<td class="Data" nowrap="nowrap">' . strSelectCB($id, $carabayar) . '</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" class="form-control-sm" rows="4" cols="40" maxlength="500">' . $desc_akaun . '</textarea>
				</td>

				<td class="Data" align="right">
					<input name="kredit[' . $id . ']" type="text" size="10" class="form-control-sm" maxlength="10" value="' . $kredit . '" style="text-align:right;" readonly/>
				</td>

				<td class="Data" align="left"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '"></td>
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
        disableElementsByName("RVNo");
        disableElementsByName("batchNo");
		disableElementsByName("tarikh_RV");
        disableElementsByName("kod_bank");
        disableElementsByName("kod_project");
        disableElementsByName("kod_jabatan");
		disableElementsByName("code");
        disableElementsByName("nama");
		disableElementsByName("b_Baddress");
		disableElementsByName("amt");
		disableElementsByName("invNo");
		disableElementsByName("invLhdn"); //LHDN-UID
		disableElementsByName("invComp");
		disableElementsByName("tinLhdn");
		disableElementsByName("disedia");
		disableElementsByName("disemak");
		disableElementsByName("catatan");
		disableElementById("bottomButton");
		disableElementById("invButton");
		disableElementById("compButton");
		});
		</script>
		';
	}
	//------------- END
}

if ($add) {
	print	   '
			<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>	

				<td class="Data">' . selectcarabayar($cara_bayar, 'cara_bayar') . '</td>

				<td class="Data" align="left">
					<textarea name="desc_akaun2" rows="4" cols="40" maxlength="500" class="form-control-sm" align="right">' . $desc_akaun2 . '</textarea>
				</td>

				<td class="Data" align="right">
					<input type="hidden" name="ruj2" val="0">
					<input name="kredit2" type="text" size="10" class="form-control-sm" maxlength="10" value="' . $kredit2 . '" />
				</td>

				<td class="Data" align="left"></td>
			</tr>';
}

//bahagian bawah skali
if ($totalKt <> 0) {
	$clsRM->setValue($baki);
	$clsRM->setValue($totalKt);
	$strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}

// $idname = get_session('Cookie_fullName');

print 		'<tr class="table-secondary">
				<td class="Data" align=""><b>&nbsp;</b></td>
				<td class="Data" colspan="2" align="right"><b>Jumlah (RM)</b></td>
				<td class="Data" id="totalJumlah" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;</b></td>
				<td class="Data" align="left"></td>
			</tr>

			<tr class="table-secondary">

				<td class="Data" align=""><b>&nbsp;</b></td>
				<td class="Data" colspan="2" align="right"><b>Baki (RM)</b></td>
				<td class="Data" align="right"><b>' . number_format($baki, 2) . '&nbsp;</b></td>
				<td class="Data" align="left"></td>
			</tr>
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
				<td>' . selectAdmin($disedia, 'disedia') . '</td>
			</tr>

			<tr>
				<td nowrap="nowrap">Disemak Oleh</td><td valign="top"></td>
				<td>' . selectAdmin($disemak, 'disemak') . '</td>
			</tr>
			
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td>
				<td valign="top">
					<textarea  class="form-controlx" name="catatan" cols="50" rows="4">' . $catatan . '</textarea></td>
			</tr>
		
		</table>
	</td>';
print '<input name="kod_caw" type="hidden" value="321"><input name="no_siri" type="hidden" value="S112"><input name="tarikh" type="hidden" value="01/10/2006"></tr>';


if ($RVNo) {
	$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
	print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCDebtorPaymentPrint.php?id=' . $RVNo . '\')">&nbsp;

	<!-- Implementing the visual effect on button Kemaskini. START -->
	<div class="request-loader-container" id="loaderContainer">
		<input type="button" name="action" id="bottomButton" value="' . $straction . '" class="btn btn-primary" onclick="CheckField(\'' . $straction . '\')">
        <div class="request-loader" id="requestLoader"></div>
    </div><br><br>
	<!-- Implementing the visual effect on button Kemaskini. END -->
	';
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
  
		  if(e.elements[c].name=="kredit2" && e.elements[c].value==\'\') {
			alert(\'Ruang amaun perlu diisi!\');
            count++;
		  }

		  if(e.elements[c].name=="cara_bayar" && e.elements[c].value==\'\') {
			alert(\'Ruang cara bayaran perlu diisi!\');
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

			if(e.elements[c].name=="kod_bank" && e.elements[c].value==\'\') 
			{
			alert(\'Sila Pilih Bank!\');
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
</script>';
include("footer.php");