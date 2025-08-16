<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCdebitNote.php
 *			Date 		: 19/10/2006
 *			Keywords 	: disable, noRecords, effect, duplicate (to prevent user fault)
 *********************************************************************************/
// Import Select2 CSS and JavaScript
print '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCdebitNoteList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;NOTA DEBIT</b>';

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
if (!isset($page))	$page = "pmtg";

$display = 0;
if ($noteNo && $action == "view") {
	$sql 			= "SELECT *
						FROM   note a, generalacc b 
						WHERE  a.companyID = b.ID 
						AND a.noteNo = '" . $noteNo . "'";
	$rs 			= $conn->Execute($sql);

	$noteNo 		= $rs->fields(noteNo);
	$tarikh_note 	= $rs->fields(tarikh_note);
	$tarikh_note 	= substr($tarikh_note, 8, 2) . "/" . substr($tarikh_note, 5, 2) . "/" . substr($tarikh_note, 0, 4);
	$tarikh_note 	= toDate("d/m/y", $rs->fields(tarikh_note));
	$batchNo 		= $rs->fields(batchNo);
	$companyID 	    = $rs->fields(companyID);
	$catatan 		= $rs->fields(catatan);
	$createdDate 	= $rs->fields(createdDate);
	$createdBy 		= $rs->fields(createdBy);
	$updatedDate 	= $rs->fields(updatedDate);
	$updatedBy 		= $rs->fields(updatedBy);
	$knockoff		= $rs->fields(knockoff);
	$amt			= $rs->fields(pymtAmt);
	$cara_byr		= $rs->fields(cara_byr);
	$accountNo 		= $rs->fields(accountNo);
	$disedia	    = $rs->fields(disedia);
	$disemak	    = $rs->fields(disemak);
	$b_Baddress 	= $rs->fields(b_Baddress);
	$code 			= $rs->fields(code);
	$nama			= dlookup("generalacc", "name", "ID=" . tosql($companyID, "Text"));
	$kodGL 			= $rs->fields('b_kodGL');

	// kod carta akaun
	//-----------------
	$sql2 		= "SELECT * FROM transactionacc WHERE docNo = '" . $noteNo . "' AND addminus IN (1) ORDER BY ID";
	$rsDetail 	= $conn->Execute($sql2);
	if ($rsDetail->RowCount() < 1)
		$noTran = true;
} elseif ($action == "new") {
	$getNo 	= "SELECT MAX(CAST(right(noteNo,6) AS SIGNED INTEGER)) AS nombor FROM note WHERE noteNo LIKE 'DN%'";
	$rsNo 	= $conn->Execute($getNo);
	$tarikh_note 	= date("d/m/Y");
	$tarikh_batch 	= date("d/m/Y");
	if ($rsNo) {
		$nombor = intval($rsNo->fields(nombor)) + 1;
		$nombor = sprintf("%06s", $nombor);
		$noteNo 	= 'DN' . $nombor;
	} else {
		$noteNo 	= 'DN000001';
	}
}

if (!isset($tarikh_note)) $tarikh_note = date("d/m/Y");
if (!isset($tarikh_batch)) $tarikh_batch = date("d/m/Y");

if ($perkara2) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$createdBy 		= get_session("Cookie_userName");
	$createdDate 	= date("Y-m-d H:i:s");

	$deductID 	= $perkara2;
	$addminus 	= 1;
	$cajAmt 	= 0.0;
	$status		= 6; //debit note
	$coreID 	= dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));

	if ($pymtAmt == '')
		$pymtAmt = '0.0';
	$sSQL	= "INSERT INTO transactionacc (" .
		"docNo," .
		"docID," .
		"batchNo," .
		"yrmth," .
		"deductID," .
		"MdeductID," .
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
		"'" . $noteNo . "', " .
		"'" . 14 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $deductID . "', " .
		"'" . $deductID . "', " .
		"'" . $addminus . "', " .
		"'" . $coreID . "', " .
		"'" . $price2 . "', " .
		"'" . $quantity2 . "', " .
		"'" . 66 . "', " .
		"'" . $kodGL . "', " .
		"'" . $PINo . "', " .
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

		$strActivity = $_POST['Submit'] . 'Kemaskini Nota Debit - ' . $noteNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

		print '<script>
		window.location = "?vw=ACCdebitNote&mn=' . $mn . '&action=view&noteNo=' . $noteNo . '";
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

			$strActivity = $_POST['Submit'] . 'Hapus Kandungan Nota Debit - ' . $docNo;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCdebitNote&mn=' . $mn . '&action=view&noteNo=' . $noteNo . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $perkara || $desc_akaun) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$tarikh_note 	= saveDateDb($tarikh_note);
	$tarikh_batch 	= saveDateDb($tarikh_batch);
	$yymm 	= substr($tarikh_note, 0, 4) . substr($tarikh_note, 5, 2);
	$sSQL 	= "";
	$sWhere = "";
	$sWhere = "noteNo='" . $noteNo . "'";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE note SET " .
	"batchNo='" . $batchNo . "'," .
	"tarikh_note='" . $tarikh_note . "'," .
	"cara_byr='" . $cara_byr . "'," .
	"companyID='" . $companyID . "'," .
	"knockoff='" . $PINo . "'," .
	"pymtAmt='" . $masterAmt . "'," .
	"catatan='" . $catatan . "'," .
	"disedia='" . $disedia . "'," .
	"disemak='" . $disemak . "'," .
	"updatedDate='" . $updatedDate . "'," .
	"updatedBy='" . $updatedBy . "'";
	$sSQL = $sSQL . $sWhere;

	$sSQL1 	 = "";
	$sWhere1 = "";
	$sWhere1 = "docNo='" . $noteNo . "' AND addminus='" . 0 . "'";
	$sWhere1 = " WHERE (" . $sWhere1 . ")";
	$sSQL1	 = "UPDATE transactionacc SET " .
		"pymtRefer='" . $companyID . "'," .
		"pymtReferC='" . $PINo . "'," .
		"deductID='" . $kodGL . "'," .
		"batchNo='" . $batchNo . "'," .
		"desc_akaun='" . $catatan . "'," .
		"pymtAmt='" . $masterAmt . "'";

	$sSQL1   = $sSQL1 . $sWhere1;

	$sSQL2   = "";
	$sWhere2 = "";
	$sWhere2 = "docNo='" . $noteNo . "'";
	$sWhere2 = " WHERE (" . $sWhere2 . ")";
	$sSQL2	 = "UPDATE transactionacc SET " .
		"yrmth='" . $yymm . "'," .
		"tarikh_doc='" . $tarikh_note . "'";

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
				$addminus 	= 0;
			} else {
				$pymtAmt 	= $kredit[$id];
				$addminus 	= 1;
			}
			//$no_ruj = $ruj[$id];
			$sSQL 	= "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transactionacc SET " .
				"batchNo= '" . $batchNo . "'" .
				",pymtReferC= '" . $PINo . "'" .
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
	/////////////////////////////////////////////////////////////
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
	window.location = "?vw=ACCdebitNote&mn=' . $mn . '&action=view&noteNo=' . $noteNo . '";
	</script>';
	}
}

//pilihan simpan
elseif ($action == "Simpan" || $simpan) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$tarikh_note 	= saveDateDb($tarikh_note);
	$tarikh_batch 	= saveDateDb($tarikh_batch);
	$status			= 6; //debit note

	$sSQL 	= "";
	$sSQL	= "INSERT INTO note (" .
		"noteNo, " .
		"batchNo, " .
		"companyID, " .
		"cara_byr, " .
		"knockoff, " .
		"tarikh_note, " .
		"catatan, " .
		"disedia, " .
		"disemak, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .

		" VALUES (" .
		"'" . $noteNo . "', " .
		"'" . $batchNo . "', " .
		"'" . $companyID . "', " .
		"'" . $cara_byr . "', " .
		"'" . $PINo . "', " .
		"'" . $tarikh_note . "', " .
		"'" . $catatan . "', " .
		"'" . $disedia . "', " .
		"'" . $disemak . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "')";

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
		"'" . $noteNo . "', " .
		"'" . $tarikh_note . "', " .
		"'" . 14 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $kodGL . "', " .
		"'" . $kodGL . "', " .
		"'" . 0 . "', " .
		"'" . 66 . "', " .
		"'" . $masterAmt . "', " .
		"'" . $catatan . "', " .
		"'" . $companyID . "', " .
		"'" . $PINo . "', " .
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

	$getMax = "SELECT MAX(CAST(right(noteNo,6) AS SIGNED INTEGER)) AS no FROM note
                WHERE noteNo LIKE 'DN%'";

	$rsMax 	= $conn->Execute($getMax);
	$max 	= sprintf("%06s", $rsMax->fields(no));

	// Step 1: Retrieve rows from transactionacc for the PINo knockoff
	$queryK = "SELECT * FROM transactionacc WHERE docNo = '$PINo' AND addminus IN (0) ORDER BY ID ASC";
	$resultK = $conn->Execute($queryK);

		// Step 2: Loop through the results and insert rows with updated docNo
		if ($resultK) {
			while (!$resultK->EOF) {
				// Fetch each row's data
				$row = $resultK->fields;
	
				// Modify docNo to noteNo
				$row['docNo'] = $noteNo;
	
				// Construct the INSERT query using the fetched and modified data
				$insertQueryk = "INSERT INTO transactionacc (
					docNo, docID, batchNo, yrmth, tarikh_doc,
					deductID, MdeductID, addminus, price, quantity,
					pymtID, pymtRefer, pymtReferC, pymtAmt, desc_akaun, `status`,
					updatedBy, updatedDate, createdBy, createdDate
				) VALUES (
					'" . $row['docNo'] . "', 
					'14',
					'" . $batchNo . "', 
					'" . $yymm . "', 
					'" . $tarikh_note . "', 
					'" . $row['deductID'] . "', 
					'" . $row['MdeductID'] . "', 
					'1',
					'" . $row['price'] . "', 
					'" . $row['quantity'] . "', 
					'" . $row['pymtID'] . "', 
					'" . $row['pymtRefer'] . "',
					'" . $row['pymtReferC'] . "',  
					'" . $row['pymtAmt'] . "', 
					'" . $row['desc_akaun'] . "', 
					'6',
					'" . $row['updatedBy'] . "', 
					'" . $row['updatedDate'] . "', 
					'" . $row['createdBy'] . "', 
					'" . $row['createdDate'] . "'
				)";
	
				// Execute the insert query
				$conn->Execute($insertQueryk);
	
				// Move to the next row
				$resultK->MoveNext();
			}
		} else {
			echo "No rows found for the accounts for: $PINo.";
		}

	if (!$display) {
		print '<script>
	window.location = "?vw=ACCdebitNote&mn=' . $mn . '&action=view&noteNo=DN' . $max . '";
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

$recurring = '<button class="btn btn-light btn-outline-secondary btn-sm text-muted recurring-btn" title="Create Recurring Debit Note" 
                onclick="if(confirm(\'This debit note will be duplicated with a new debit note number. Proceed?\')) { Recurring(); }">
                <i class="fa fa-copy"></i> Recurring
            </button>';

$strTemp .=
	'<div class="table-responsive"><div class="maroon" align="left">' . $strHeaderTitle . '&nbsp;&nbsp;&nbsp;' . $recurring . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="?vw=ACCdebitNote&mn=' . $mn . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print
	'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			
			<tr>
				<td>No. DN</td>
				<td valign="top"></td>
				<td>
					<input  name="noteNo" value="' . $noteNo . '" type="text" size="20" maxlength="50" class="form-controlx" readonly/>
				</td>
			</tr>

			<tr>
				<td>* Batch</td>
				<td valign="top"></td>
				<td>' . selectbatchPI($batchNo, 'batchNo') . '</td>
			</tr>

			<tr>
				<td>Tarikh</td>
				<td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh_note">
				<input type="text" name="tarikh_note" class="form-controlx" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh_note"
					data-date-autoclose="true" value="' . $tarikh_note . '">
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

print '<input type="button" class="btn btn-sm btn-info" id="invButton" value="Pilih Invois" onclick="window.open(\'ACCidpemiutangBILL.php?refer=f&source=note\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;';
// print '<input type="button" class="btn btn-sm btn-info" id="compButton" value="Pilih Syarikat" onclick="window.open(\'ACCidpembekal.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;';
print '<input type="button" class="btn btn-sm btn-primary" id="addCreditorButton" value="Tambah Syarikat" onclick="window.open(\'generalAddUpdateACC.php?action=tambah&cat=AB&sub=&page=' . $page . '\',\'sort\',\'top=50,left=50,width=700,height=650,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';

$result = dlookup("generalacc", "b_tinLhdn", "ID=" . tosql($companyID, "Text"));
$tinLhdn = ($result !== null && $result !== "")
	? '<span style="color: green; font-size: 16px;" title="TIN(LHDN): ' . htmlspecialchars($result) . '">&#10004;</span>'
	: '<span style="color: red; font-size: 16px;" title="TIN(LHDN) belum dimasukkan">&#10008;</span>';

print '&nbsp;
	</td>
</tr>

<tr>
	<td valign="top">Nama Syarikat</td>
	<td valign="top"></td>
	<td><input name="name"  value="' . $nama . '" size="50" maxlength="50"  class="form-controlx" readonly />&nbsp;
';
echo $tinLhdn;
print'
	</td>
</tr>
';
print'
<tr>
	<td valign="top">Alamat Syarikat</td>
	<td valign="top"></td>
	<td><textarea name="b_Baddress" cols="50" rows="4" class="form-controlx" readonly>' . $b_Baddress . '</textarea></td>
</tr>

<tr>
	<td valign="top">Amaun Purchase Invoice (RM)</td>
	<td valign="top"></td>
	<td><input name="amt"  value="' . $amt . '" size="10" maxlength="50"  class="form-controlx" readonly/></td>
</tr>

<tr>
	<td valign="top">No. Purchase Invoice</td>
	<td valign="top"></td>
	<td><input name="PINo" value="' . $knockoff . '" size="40" maxlength="50"  class="form-controlx" readonly /></td>
</tr>

<tr>
	<td valign="top">Cara Bayar</td>
	<td valign="top"></td>
	<td>' . selectbayar($cara_byr, 'cara_byr') . '</td>
</tr>';

$sql3 		= "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = '" . $noteNo . "' ORDER BY ID";
$rsDetail1 = $conn->Execute($sql3);

print
	'<tr>
    <td valign="top" align="left">Master Amaun (RM)</td><td valign="top"></td>
    <td><input id="master" class="form-control-sm" value="' . $rsDetail1->fields(pymtAmt) . '" type="text" size="20" maxlength="10" readonly/></td>
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
if ($action == "view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $noteNo . "'"))) {
	print '
	<tr>
		<td align= "right" colspan="3">';
	if (!$add) print '
			<div class="request-loader-container" id="loaderContainer">
				<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCdebitNote&mn=' . $mn . '&action=' . $action . '&noteNo=' . $noteNo . '&add=1\';">
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
				<td nowrap="nowrap"><b>* Harga Seunit (RM)</b></td>
				<td nowrap="nowrap" align="right"><b>Amaun (RM)</b></td>
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

			$id 		= $rsDetail->fields(ID);
			$ruj 		= $rsDetail->fields(pymtRefer);
			$perkara 	= $rsDetail->fields(deductID);

			$kod_akaun 	= dlookup("generalacc", "c_Panel", "ID=" . $perkara);
			$kredit 	= $rsDetail->fields(pymtAmt);
			$desc_akaun = $rsDetail->fields(desc_akaun);

			$quantity 	= $rsDetail->fields(quantity);
			$price 		= $rsDetail->fields(price);

			$a_Keterangan = dlookup("generalacc", "code", "ID=" . $perkara);

			if ($rsDetail->fields(addminus)) {
				$kredit = $rsDetail->fields(pymtAmt);
			} else {
				$debit 	= $rsDetail->fields(pymtAmt);
			}

			print
				'<tr>
			<td class="Data">&nbsp;' . ++$i . '.</td>	

			<td class="Data" nowrap="nowrap">' . strSelect3($id, $perkara, "asetPerbelanjaan") . '&nbsp;</td>

			<td class="Data" nowrap="nowrap">
				<textarea name="desc_akaun[' . $id . ']" rows="4" cols="40" maxlength="500" class="form-control-sm">' . $desc_akaun . '</textarea>&nbsp;
			</td>';

			//column kuantiti dan harga
			print '
			<td class="Data" nowrap="nowrap">
				<input name="quantity[' . $id . ']" id="quantity[' . $id . ']" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $quantity . '" oninput="calculateKredit(' . $id . ')">
				&nbsp;
			</td>

			<td class="Data" nowrap="nowrap" align="center">
				<input name="price[' . $id . ']" id="price[' . $id . ']" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $price . '" oninput="calculateKredit(' . $id . ')">
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
		disableElementsByName("noteNo");
        disableElementsByName("batchNo");
		disableElementsByName("tarikh_note");
        disableElementsByName("nama_anggota");
		disableElementsByName("code");
        disableElementsByName("name");
		disableElementsByName("b_Baddress");
		disableElementsByName("amt");
		disableElementsByName("PINo");
		disableElementsByName("cara_byr");
		disableElementsByName("disedia");
		disableElementsByName("disemak");
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

			<td class="Data">' . $strSelect . '</td>

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
				<td class="Data" colspan="4" align="right"><b>Jumlah (RM)</b></td>
				<td class="Data" id="totalJumlah" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"></td>
			</tr>';
if ($PINo) {
	print '
				<tr class="table-secondary">
					<td class="Data" align=""><b>&nbsp;</b></td>
					<td class="Data" colspan="4" align="right"><b>Baki Purchase Order (RM)</b></td>
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
				<td>' . selectAdmin($disedia, 'disedia') . '</td>
			</tr>

			<tr>
				<td nowrap="nowrap">Disemak Oleh</td><td valign="top"></td>
				<td>' . selectAdmin($disemak, 'disemak') . '</td>
			</tr>
			
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td>
				<td valign="top">
					<textarea class="form-controlx" name="catatan" cols="50" rows="4">' . $catatan . '</textarea></td>
			</tr>
		
		</table>
	</td>';

if ($noteNo) {
	$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
	print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCPurchaseInvoicePrint.php?id=' . $noteNo . '&note=1\')">&nbsp;
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

//-------------------------------------------------------------- Feature to duplicate/recurring debit note --------------START

// Print button in new form
print '
<form method="POST" id="recurringForm">
	<input type="hidden" name="recurrNoteNo" id="recurrNoteNoField" value="">
</form>

';

// create the new invois number for duplication
$getNo = "SELECT MAX(CAST(right(noteNo,6) AS SIGNED INTEGER)) AS nombor FROM note";
$rsNo = $conn->Execute($getNo);
if ($rsNo) {
	$nombor = intval($rsNo->fields('nombor')) + 1;
	$nombor = sprintf("%06s", $nombor);
	$recurrNoteNo = 'DN' . $nombor;
}

// duplicating the invois
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recurrNoteNo'])) {
	$recurrNoteNo 	= $_POST['recurrNoteNo'];
	$oldNoteNo 	= $noteNo; // Pass the old debit note number from the form

	print '
	// <script>
	// 	alert("Recurring Debit Note Number: ' . $recurrNoteNo . '");
	// 	alert("Old Debit Note Number: ' . $oldNoteNo . '");
	// </script>';

	$updatedBy 	    = get_session("Cookie_userName");
	$updatedDate    = date("Y-m-d H:i:s");
	// $tarikh_note     = saveDateDb($tarikh_note);

	$sSQL 	= "";
	$sSQL	= "INSERT INTO note (" .
		"noteNo, " .
		// "tarikh_note, " . //left empty
		"batchNo, " .
		"cara_byr, " .
		"companyID, " .
		"pymtAmt, " .
		"catatan, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy, " .
		"knockoff, " .
		"disedia, " .
		"disemak) " .

		" VALUES (" .
		"'" . $recurrNoteNo . "', " .
		// "'". $tarikh_note . "', ".
		"'" . $batchNo . "', " .
		"'" . $cara_byr . "', " .
		"'" . $companyID . "', " .
		"'" . $masterAmt . "', " .
		"'" . $catatan . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $PINo . "', " .
		"'" . $disedia . "', " .
		"'" . $disemak . "')";

	// Step 1: Retrieve rows from transactionacc for the old noteNo
	$query = "SELECT * FROM transactionacc WHERE docNo = '$oldNoteNo'";
	$result = $conn->Execute($query);

	// Step 2: Loop through the results and insert rows with updated docNo
	if ($result) {
		while (!$result->EOF) {
			// Fetch each row's data
			$row = $result->fields;

			// Modify docNo to recurrNoteNo
			$row['docNo'] = $recurrNoteNo;

			// Construct the INSERT query using the fetched and modified data
			//tarikh_doc is left empty
			$insertQuery = "INSERT INTO transactionacc (
                docNo, docID, batchNo, yrmth, 
                deductID, MdeductID, addminus, coreID, price, quantity, 
                pymtID, pymtRefer, pymtReferC, pymtAmt, desc_akaun, `status`,
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
                '" . $row['status'] . "', 
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
		echo "No rows found for the previous debit note number: $oldNoteNo.";
	}

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);

	if (!$display) {
		print '<script>
window.location = "?vw=ACCdebitNote&mn=' . $mn . '&action=view&noteNo=' . $recurrNoteNo . '";
</script>';
	}
}

//-------------------------------------------------------------- Feature to duplicate/recurring debit note --------------END

print '
<script language="JavaScript">

    // Recurring function
    function Recurring() {
        // Use PHP variable for the new noteNo
        var recurrNoteNo = "' . $recurrNoteNo . '";  // Embed PHP variable as a JavaScript string

		// Update the hidden input field value
        var recurrNoteNoField = document.getElementById("recurrNoteNoField");
        if (recurrNoteNoField) {
            recurrNoteNoField.value = recurrNoteNo;

            // Submit the form
            document.getElementById("recurringForm").submit();
        } else {
            console.error("Hidden field with ID \'recurrNoteNoField\' not found.");
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