<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCbillpembayaran.php
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

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCbillList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;BAYARAN BIL BAUCER</b>';

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display = 0;
if ($no_bill && $action == "view") {

	$sql = "SELECT A.*,B.* FROM billacc a, generalacc b WHERE a.diterima_drpd = b.ID and a.no_bill = '" . $no_bill . "'";

	$rs 			= $conn->Execute($sql);
	$no_bill 		= $rs->fields('no_bill');
	$tarikh_bill 	= $rs->fields('tarikh_bill');
	$tarikh_bill 	= substr($tarikh_bill, 8, 2) . "/" . substr($tarikh_bill, 5, 2) . "/" . substr($tarikh_bill, 0, 4);

	$kod_bank 		= $rs->fields('kod_bank');
	$bankparent 	= dlookup("generalacc", "parentID", "ID=" . $kod_bank);

	$keterangan 	= $rs->fields('keterangan');
	$maklumat 		= $rs->fields('maklumat');
	$tarikh_bill 	= toDate("d/m/y", $rs->fields('tarikh_bill'));
	$nama 			= $rs->fields('name');
	$batchNo 		= $rs->fields('batchNo');
	$accountNo 		= $rs->fields('accountNo');
	$kod_project 	= $rs->fields('kod_project');
	$keterangan		= $rs->fields('keterangan');
	$companyID		= $rs->fields('diterima_drpd');
	$cara_byr		= $rs->fields('cara_byr');
	$amt			= $rs->fields('pymtAmt');
	$code			= $rs->fields('code');
	$b_Baddress 	= $rs->fields('b_Baddress');
	$name			= $rs->fields('name');
	$PINo			= $rs->fields('PINo');
	$kodGL 			= $rs->fields('b_kodGL');
	$invLhdn 		= dlookup("cb_purchaseinv", "invLhdn", "PINo=" . tosql($PINo, "Text")); // LHDN-UID
	$invComp 		= dlookup("cb_purchaseinv", "invComp", "PINo=" . tosql($PINo, "Text"));
	$tinLhdn 		= dlookup("generalacc", "b_tinLhdn", "ID=" . tosql($companyID, "Text"));
	$kerani			= $rs->fields('kerani');
	$disahkan 		= $rs->fields('disahkan');
	//-----------------
	$sql2 		= "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = '" . $no_bill . "' ORDER BY ID";
	$rsDetail 	= $conn->Execute($sql2);
	if ($rsDetail->RowCount() < 1)
		$noTran = true;

	//disable the tambah button when user already choose the same PI in newer BL, to prevent miscalculations & user error
	//------------- START
	$disableButton = false;

	while (!$rsDetail->EOF) {
		$id = $rsDetail->fields['ID'];
		if (!empty($PINo)) {
			$sSQLCheck = "SELECT 1 
						FROM transactionacc 
						WHERE pymtReferC = '$PINo' 
						AND ID > '$id' 
						AND docNo != '$no_bill'
						AND status NOT IN (6) -- status 6 is debit note
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
	$getNo = "SELECT MAX(CAST(right(no_bill,6) AS SIGNED INTEGER)) AS nombor FROM billacc";
	$rsNo 	= $conn->Execute($getNo);
	$tarikh_bill = date("d/m/Y");
	$tarikh = date("d/m/Y");
	if ($rsNo) {
		$nombor  = intval($rsNo->fields('nombor')) + 1;
		$nombor  = sprintf("%06s",  $nombor);
		$no_bill = 'BL' . $nombor;
	} else {
		$no_bill = 'BL000001';
	}
}

if (!isset($tarikh_bill)) $tarikh_bill = date("d/m/Y");

if ($kod_jabatan) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$createdBy 		= get_session("Cookie_userName");
	$createdDate 	= date("Y-m-d H:i:s");

	$accountNo 	= $kodGL;  //perkara to deduct id value

	$addminus 	= 0;
	$cajAmt 	= 0.0;

	if ($pymtAmt == '')
		$pymtAmt = '0.0';
	$sSQL	= "INSERT INTO transactionacc (" .
		"docNo," .
		"docID," .
		"batchNo," .
		"yrmth," .
		"deductID," .
		"MdeductID," .
		"kod_project," .
		"kod_jabatan," .
		"addminus," .
		"pymtID," .
		"pymtAmt," .
		"pymtRefer," .
		"pymtReferC," .
		"desc_akaun," .
		"status," .
		"isApproved," .
		"approvedDate," .
		"updatedBy," .
		"updatedDate	," .
		"createdBy," .
		"createdDate) " .

		" VALUES (" .
		"'" . $no_bill . "', " .
		"'" . 7 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $accountNo . "', " .
		"'" . $accountNo . "', " .
		"'" . $kod_project . "', " .
		"'" . $kod_jabatan . "', " .
		"'" . $addminus . "', " .
		"'" . 66 . "', " .
		"'" . $kredit2 . "', " .
		"'" . $companyID . "', " .
		"'" . $PINo . "', " .
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

		$strActivity = $_POST['Submit'] . 'Kemaskini Bayaran Bil - ' . $no_bill;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

		print '<script>
		window.location = "?vw=ACCbillpembayaran&mn=' . $mn . '&action=view&no_bill=' . $no_bill . '";
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

			$strActivity = $_POST['Submit'] . 'Hapus Kandungan Bayaran Bil - ' . $docNo;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCbillpembayaran&mn=' . $mn . '&action=view&no_bill=' . $no_bill . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $jabatan1 || $desc_akaun || $projecting) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$tarikh_bill 	= saveDateDb($tarikh_bill);
	$yymm 	= substr($tarikh_bill, 0, 4) . substr($tarikh_bill, 5, 2);
	$tarikh = saveDateDb($tarikh);
	$sSQL 	= "";
	$sWhere = "";
	$sWhere = "no_bill='" . $no_bill . "'";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE billacc SET " .
		"no_bill='" . $no_bill . "'," .
		"tarikh_bill='" . $tarikh_bill . "'," .
		"batchNo='" . $batchNo . "'," .
		"cara_byr='" . $cara_byr . "'," .
		"kod_bank='" . $kod_bank . "'," .
		"kerani='" . $kerani . "'," .
		"disahkan='" . $disahkan . "'," .
		"keterangan='" . $keterangan . "'," .
		"diterima_drpd='" . $companyID . "'," .
		"PINo='" . $PINo . "'," .
		"maklumat='" . $maklumat . "'," .
		"pymtAmt='" . $amt . "'," .
		"balance='" . $balance . "'," .
		"StatusID_Pymt='0'," .
		"createdDate='" . $updatedDate . "'," .
		"createdBy='" . $updatedBy . "'," .
		"updatedDate='" . $updatedDate . "'," .
		"updatedBy='" . $updatedBy . "'";

	$sSQL 	= $sSQL . $sWhere;

	$sSQL1 	= "";
	$sWhere1 = "";
	$sWhere1 = "docNo='" . $no_bill . "' AND addminus='" . 1 . "'";
	$sWhere1 = " WHERE (" . $sWhere1 . ")";
	$sSQL1	= "UPDATE transactionacc SET " .
		"deductID='" . $kod_bank . "'," .
		"MdeductID='" . $bankparent . "'," .
		"batchNo='" . $batchNo . "'," .
		"desc_akaun='" . $maklumat . "'," .
		"pymtAmt='" . $masterAmt . "'";

	$sSQL1 = $sSQL1 . $sWhere1;

	$sSQL2 	= "";
	$sWhere2 = "";
	$sWhere2 = "docNo='" . $no_bill . "'";
	$sWhere2 = " WHERE (" . $sWhere2 . ")";
	$sSQL2	= "UPDATE transactionacc SET " .
		"yrmth='" . $yymm . "'," .
		"tarikh_doc='" . $tarikh_bill . "'";

	$sSQL2 	= $sSQL2 . $sWhere2;

	if ($display) print $sSQL . '<br />';
	else
		$rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);
	$rs = &$conn->Execute($sSQL2);
	//////////////////////////PROJEK//////////////////////////////////////////////////////////////
	/*	if(count($perkara)>0){
		foreach($perkara as $id =>$value){

		$accountNo = $value;
		if($debit[$id]){
		$pymtAmt = $debit[$id];
		$addminus = 1;
		}else{
		$pymtAmt = $kredit[$id];
		$addminus = 0;
		}

		$sSQL = "";
		$sWhere = "";		
	    $sWhere = "ID='" . $id ."'";
	    $sSQL	= "UPDATE transactionacc SET " .
	     	"batchNo= '" . $batchNo . "'".
          	",deductID= '" . $accountNo . "'".
          	",pymtAmt= '" . $pymtAmt . "'".      	
		  	",updatedDate= '" .$updatedDate . "'".
          	",updatedBy= '" .  $updatedBy . "'" ;

		$sSQL .= " where " . $sWhere;
		if($display) print $sSQL.'<br />';
		else $rs = &$conn->Execute($sSQL);
		}	
	}*/
	//////////////////////////PROJEK//////////////////////////////////////////////////////////////
	if (count($kod_akaunM) > 0) {
		foreach ($kod_akaunM as $id => $value) {

			$MdeductID 	= $value;
			if ($debit[$id]) {
				$pymtAmt 	= $debit[$id];
				$addminus 	= 1;
			} else {
				$pymtAmt 	= $kredit[$id];
				$addminus 	= 0;
			}
			$sSQL 	= "";
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

	if (count($desc_akaun) > 0) {
		foreach ($desc_akaun as $id => $value) {

			$desc_akaun = $value;
			if ($debit[$id]) {
				$pymtAmt 	= $debit[$id];
				$addminus 	= 1;
			} else {
				$pymtAmt 	= $kredit[$id];
				$addminus 	= 0;
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
	/////////////////////////////////////////////////////////

	if (count($projecting) > 0) {
		foreach ($projecting as $id => $value) {

			$kod_project = $value;
			if ($debit[$id]) {
				$pymtAmt 	= $debit[$id];
				$addminus 	= 1;
			} else {
				$pymtAmt 	= $kredit[$id];
				$addminus 	= 0;
			}
			$sSQL 	= "";
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


	if (count($jabatan1) > 0) {
		foreach ($jabatan1 as $id => $value) {

			$kod_jabatan = $value;
			if ($debit[$id]) {
				$pymtAmt 	= $debit[$id];
				$addminus 	= 1;
			} else {
				$pymtAmt 	= $kredit[$id];
				$addminus 	= 0;
			}

			$sSQL 	= "";
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
	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////	
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCbillpembayaran&mn=' . $mn . '&action=view&no_bill=' . $no_bill . '";
	</script>';
	}
}
//pilihan simpan
elseif ($action == "Simpan" || $simpan) {
	$updatedBy 	 = get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$tarikh_bill = saveDateDb($tarikh_bill);
	$tarikh 	 = saveDateDb($tarikh);

	// help prevent double entry by multiple users ----begin
	$getMax2 	= "SELECT MAX(CAST(right(no_bill,6) AS SIGNED INTEGER)) AS no2 FROM billacc";
	$rsMax2 	= $conn->Execute($getMax2);
	$max2   	= sprintf("%06s", $rsMax2->fields('no2'));

	if ($rsMax2) {
		$max2  		= intval($rsMax2->fields('no2')) + 1;
		$max2  		= sprintf("%06s",  $max2);
		$no_bill2 	= 'BL' . $max2;
	} else {
		$no_bill2 	= 'BL000001';
	}
	//-----end

	$sSQL 	= "";
	$sSQL	= "INSERT INTO billacc (" .
		"no_bill, " .
		"tarikh_bill, " .
		"batchNo, " .
		"cara_byr, " .
		"kod_bank, " .
		"kerani, " .
		"disahkan, " .
		"keterangan, " .
		"diterima_drpd, " .
		"PINo, " .
		"maklumat, " .
		"pymtAmt, " .
		"balance, " .
		"StatusID_Pymt, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .

		" VALUES (" .
		"'" . $no_bill2 . "', " .
		"'" . $tarikh_bill . "', " .
		"'" . $batchNo . "', " .
		"'" . $cara_byr . "', " .
		"'" . $kod_bank . "', " .
		"'" . $kerani . "', " .
		"'" . $disahkan . "', " .
		"'" . $keterangan . "', " .
		"'" . $companyID . "', " .
		"'" . $PINo . "', " .
		"'" . $maklumat . "', " .
		"'" . $amt . "', " .
		"'" . $amt . "', " .
		"'" . 0 . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "')";

	$sSQL1 	= "";
	$sSQL1	= "INSERT INTO transactionacc (" .

		"docNo," .
		"tarikh_doc," .
		"docID," .
		"batchNo," .
		"yrmth," .
		"deductID," .
		"kod_project," .
		"kod_jabatan," .
		"addminus," .
		"pymtID," .
		"pymtAmt," .
		"pymtReferC," .
		"desc_akaun," .
		"status," .
		"isApproved," .
		"approvedDate," .
		"updatedBy," .
		"updatedDate	," .
		"createdBy," .
		"createdDate) " .

		" VALUES (" .
		"'" . $no_bill2 . "', " .
		"'" . $tarikh_bill . "', " .
		"'" . 7 . "', " .
		"'" . $batchNo . "', " .
		"'" . $yymm . "', " .
		"'" . $kod_bank . "', " .
		"'" . $kod_project . "', " .
		"'" . $kod_jabatan . "', " .
		"'" . 1 . "', " .
		"'" . 66 . "', " .
		"'" . $masterAmt . "', " .
		"'" . $PINo . "', " .
		"'" . $maklumat . "', " .
		"'" . $status . "', " .
		"'" . $isApproved . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "')";

	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);
	$rs = &$conn->Execute($sSQL1);

	$getMax = "SELECT MAX(CAST(right(no_bill,6) AS SIGNED INTEGER)) AS no FROM billacc";
	$rsMax 	= $conn->Execute($getMax);
	$max 	= sprintf("%06s", $rsMax->fields('no'));
	if (!$display) {
		print '<script>
	window.location = "?vw=ACCbillpembayaran&mn=' . $mn . '&action=view&add=1&no_bill=BL' . $max . '";
	</script>';
	}
}

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="?vw=ACCbillpembayaran&mn=' . $mn . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print
	'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>Nombor Bill</td>
				<td valign="top"></td>
				<td>
					<input  name="no_bill" value="' . $no_bill . '" type="text" size="20" maxlength="50" class="form-controlx" readonly/>
				</td>
			</tr>

			<tr>
				<td>* Batch</td>
				<td valign="top"></td>
				<td>' . selectbatchBILL($batchNo, 'batchNo') . '</td>
			</tr>

			<tr>
				<td>* Bank</td>
				<td valign="top"></td>
				<td>' . selectbanks($kod_bank, 'kod_bank') . '</td>
			</tr>

		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh</td>
				<td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh_bill">
				<input type="text" name="tarikh_bill" class="form-controlx" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh_bill"
					data-date-autoclose="true" value="' . $tarikh_bill . '">
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
<tr><td colspan="3"><hr class="mt-3"/></td></tr>';


print '
<tr colspan="3">
	<td valign="top"><input name="j" type="hidden" value="tiada">

<table border="0" cellspacing="1" cellpadding="2">

<tr>
<td>* Kod Pemiutang</td><td valign="top"></td>
<td><input name="code" value="' . $code . '" type="text" size="20" maxlength="50"  class="form-controlx" readonly/>&nbsp;';

print '<input type="button" class="btn btn-sm btn-info" id="invButton" value="Pilih Invois" onclick="window.open(\'ACCidpemiutangBILL.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;';
print '<input type="button" class="btn btn-sm btn-primary" id="compButton" value="Pilih Syarikat" onclick="window.open(\'ACCpemiutang.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
print '&nbsp;

</td>
</tr>

<tr>
 <td valign="top">Nama Syarikat</td>
 <td valign="top"></td>
 <td><input name="name" value="' . $name . '" size="50" maxlength="50"  class="form-controlx" readonly /></td>
 </tr>

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
 <td valign="top">Nombor Purchase Invoice</td>
 <td valign="top"></td>
 <td><input name="PINo" value="' . $PINo . '" size="20" maxlength="50"  class="form-controlx" readonly /></td>
 </tr>
 
<tr>
	<td valign="top">LHDN-UID</td>
	<td valign="top"></td>
	<td><input name="invLhdn" value="' . $invLhdn . '" size="20" maxlength="50"  class="form-controlx" readonly/></td>
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

<tr>
 <td valign="top"></td>
 <td valign="top"></td>
 <td><input name="kodGL" value="' . $kodGL . '" size="40" maxlength="50"  class="form-controlx" hidden /></td>
 </tr>

<tr>

<tr>
 <td valign="top"></td>
 <td valign="top"></td>
 <td><input name="companyID" value="' . $companyID . '" size="40" maxlength="50"  class="form-controlx" hidden /></td>
 </tr>
<tr>

<tr>
	<td valign="top">Keterangan Bayaran</td>
	<td valign="top"></td>
	<td>
		<textarea name="keterangan" cols="50" rows="4" class="form-controlx">' . $keterangan . '</textarea>
	</td>
</tr>

 <tr>
 <td valign="top">Cara Bayar</td>
 <td valign="top"></td>
 <td>' . selectbayar($cara_byr, 'cara_byr') . '</td>
 </tr>';

$sql3 		= "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = '" . $no_bill . "' ORDER BY ID";
$rsDetail1 = $conn->Execute($sql3);

print '<tr>
 <td valign="top">Master Amaun (RM)</td>
 <td valign="top"></td>
 <td><input id="master" class="form-controlx" value="' . $rsDetail1->fields('pymtAmt') . '" type="text" size="20" maxlength="10" readonly/></td>
 </tr>

</table>
</td>

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
if ($action == "view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $no_bill . "'"))) {
	print '
	<tr>
			<td align= "right" colspan="3">';
	if (!$add) print '
			
		<!-- Implementing the visual effect on button Tambah for akaun. START -->
		<div class="request-loader-container" id="loaderContainer">
			<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCbillpembayaran&mn=' . $mn . '&action=' . $action . '&no_bill=' . $no_bill . '&add=1\';">
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
				<td nowrap="nowrap"><b>* Jabatan</b></td>
				<td nowrap="nowrap"><b>Projek</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="right" ><b>* Jumlah (RM)</b></td>
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
			$projecting = $rsDetail->fields('kod_project');
			$jabatan1 	= $rsDetail->fields('kod_jabatan');
			$desc_akaun = $rsDetail->fields('desc_akaun');


			$kod_project 	= dlookup("generalacc", "name", "ID=" . $projecting);
			$kod_jabatan 	= dlookup("generalacc", "name", "ID=" . $jabatan1);
			$kredit 		= $rsDetail->fields('pymtAmt');
			print
				'<tr>
				<td class="Data">' . ++$i . '.</td>	

				<td class="Data" nowrap="nowrap">' . strjabatan($id, $jabatan1) . '</td>

				<td class="Data" nowrap="nowrap">' . strproject($id, $projecting) . '</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" rows="4" cols="40" maxlength="500" class="form-control-sm">' . $desc_akaun . '</textarea>
				</td>

				<td class="Data" align="right">
					<input name="kredit[' . $id . ']" type="text" size="10" maxlength="10" value="' . $kredit . '" class="form-control-sm" style="text-align:right;" readonly/>
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '"></td>

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
		disableElementsByName("no_bill");
        disableElementsByName("batchNo");
		disableElementsByName("kod_bank");
		disableElementsByName("tarikh_bill");
		disableElementsByName("code");
        disableElementsByName("name");
		disableElementsByName("b_Baddress");
		disableElementsByName("amt");
		disableElementsByName("invLhdn"); //LHDN-UID
		disableElementsByName("invComp");
		disableElementsByName("tinLhdn");
		disableElementsByName("PINo");
		disableElementsByName("keterangan");
		disableElementsByName("cara_byr");
		disableElementsByName("kerani");
		disableElementsByName("disahkan");
		disableElementsByName("maklumat");
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

				<td class="Data" size="20" maxlength="10">' . selectjabatan($kod_jabatan, 'kod_jabatan') . '</td>

				<td class="Data" size="20" maxlength="10">' . selectproject($kod_project, 'kod_project') . '</td>

				<td class="Data" align="left">
					<textarea name="desc_akaun2" class="form-control-sm" rows="4" cols="40" maxlength="500" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
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
	$clsRM->setValue($baki);
	$clsRM->setValue($totalKt);
	$strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}

// $kerani = get_session('Cookie_fullName');

print 		'<tr class="table-secondary">
				<td class="Data" colspan="4" align="right"><b>Jumlah (RM)</b></td>
				<td class="Data" id="totalJumlah" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;	
				</b></td>
				<td class="Data" align=""><b>&nbsp;</b></td>
			</tr>

			<tr class="table-secondary">
				<td class="Data" colspan="4" align="right"><b>Baki (RM)</b></td>
				<td class="Data" align="right"><b>' . number_format($baki, 2) . '&nbsp;	
				</b></td>
				<td class="Data" align=""><b>&nbsp;</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">

	<tr><td colspan="3" nowrap="nowrap">Jumlah Dalam Perkataan<br />
		<input name="" size="100" maxlength="100" class="form-controlx" value="' . $strTotal . '" readonly>
		<input class="form-controlx" type="hidden" name="masterAmt" value="' . $totalKt . '">
		<input class="form-controlx" type="hidden" name="balance" value="' . $baki . '">	
		<input class="form-controlx" type="hidden" name="bankparent" value="' . $bankparent . '">
	</td></tr>

		<tr>
				<td nowrap="nowrap">Dimasukkan Oleh</td><td valign="top"></td>
				<td>' . selectAdmin($kerani, 'kerani') . '</td>
			</tr>

			<tr>
				<td nowrap="nowrap">Disahkan Oleh</td><td valign="top"></td>
				<td>' . selectAdmin($disahkan, 'disahkan') . '</td>
			</tr>
			
			<tr><td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td><td valign="top"><textarea class="form-controlx" name="maklumat" cols="50" rows="4">' . $maklumat . '</textarea></td></tr>
		</table>
	</td>
</tr>';
print '<input name="kod_caw" type="hidden" value="321"><input name="no_siri" type="hidden" value="S112"><input name="tarikh" type="hidden" value="01/10/2006"></tr>';


if ($no_bill) {
	$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
	print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCBillPrintCustomer.php?id=' . $no_bill . '\')">&nbsp;

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
</form>
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
			alert(\'Ruang jumlah perlu diisi!\');
            count++;
		  }

		  if(e.elements[c].name=="kod_jabatan" && e.elements[c].value==\'\') {
			alert(\'Ruang jabatan perlu diisi!\');
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

			 if(e.elements[c].name=="kod_bank" && e.elements[c].value==\'\') 
			{
		   	alert(\'Sila pilih Bank!\');
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