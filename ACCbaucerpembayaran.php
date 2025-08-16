<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCbaucerpembayaran.php
 *			Date 		: 19/10/2006
 *			Keywords 	: disable, noRecords, effect, duplicate (to prevent user fault)
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCvouchersList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;PEMBAYARAN BAUCER</b>';

if (!isset($mm))    $mm = date("m");
if (!isset($yy))    $yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display = 0;
if ($no_baucer && $action == "view") {
    $sql = "SELECT * FROM bauceracc WHERE no_baucer = '" . $no_baucer . "'";

    $rs             = $conn->Execute($sql);
    $no_baucer      = $rs->fields('no_baucer');
    $tarikh_baucer  = $rs->fields('tarikh_baucer');
    $tarikh_baucer  = substr($tarikh_baucer, 8, 2) . "/" . substr($tarikh_baucer, 5, 2) . "/" . substr($tarikh_baucer, 0, 4);

    $kod_bank       = $rs->fields('kod_bank');
    $bankparent     = dlookup("generalacc", "parentID", "ID=" . $kod_bank);

    $keterangan     = $rs->fields('keterangan');
    $tarikh_bayar   = $rs->fields('tarikh_bayar');
    $tarikh_bayar   = substr($tarikh_bayar, 8, 2) . "/" . substr($tarikh_bayar, 5, 2) . "/" . substr($tarikh_bayar, 0, 4);
    $nama           = $rs->fields('name');
    $batch          = $rs->fields('batchNo');
    $deductID       = $rs->fields('deductID');
    $bayaran_kpd    = $rs->fields('bayaran_kpd');
    $Cheque         = $rs->fields('Cheque');
    $cara_bayar     = $rs->fields('cara_bayar');
    $catatan        = $rs->fields('catatan');
    $disedia        = $rs->fields('disedia');
    $disahkan       = $rs->fields('disahkan');
    $masterAmt      = $rs->fields('pymtAmt');
    $kod_project    = $rs->fields('kod_project');
    $kod_jabatan    = $rs->fields('kod_jabatan');


    // kod carta akaun
    //-----------------
    $sql2         = "SELECT * FROM transactionacc WHERE docNo = '" . $no_baucer . "' AND addminus IN (0) ORDER BY ID";
    $rsDetail     = $conn->Execute($sql2);
    if ($rsDetail->RowCount() < 1)
        $noTran = true;
} elseif ($action == "new") {
    $getNo      = "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS nombor FROM bauceracc";
    $rsNo       = $conn->Execute($getNo);
    if ($rsNo) {
        $nombor     = intval($rsNo->fields('nombor')) + 1;
        $nombor     = sprintf("%06s",  $nombor);
        $no_baucer     = 'PV' . $nombor;
    } else {
        $no_baucer     = 'PV000001';
    }
}

if (!isset($tarikh_baucer)) $tarikh_baucer   = date("d/m/Y");
if (!isset($tarikh_bayar)) $tarikh_bayar     = date("d/m/Y");

if ($perkara2) {
    $updatedBy      = get_session("Cookie_userName");
    $updatedDate    = date("Y-m-d H:i:s");

    $deductID       = $perkara2; //perkara to deduct id value
    $coreID         = dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));
    if ($debit2) { //debit 2 field for money value
        $pymtAmt    = $debit2;
        $addminus   = 0;
        $cajAmt     = 0.0;
    } else {
        $pymtAmt    = $kredit2;
        $addminus   = 1;
        $cajAmt     = 0.0;
    }

    if ($pymtAmt == '')
        $pymtAmt = '0.0';
    $sSQL    = "INSERT INTO transactionacc (" .
        "docNo," .
        "docID," .
        "batchNo," .
        "yrmth," .
        "deductID," .
        // "taxNo," .
        "kod_project," .
        "kod_jabatan," .
        "addminus," .
        "coreID," .
        "pymtID," .
        "pymtAmt," .
        "desc_akaun," .
        "updatedBy," .
        "updatedDate	," .
        "createdBy," .
        "createdDate) " .
        " VALUES (" .
        "'" . $no_baucer . "', " .
        "'" . 3 . "', " .
        "'" . $batchNo . "', " .
        "'" . $yymm . "', " .
        "'" . $deductID . "', " .
        // "'" . $taxNo . "', " .
        "'" . $kod_project . "', " .
        "'" . $kod_jabatan . "', " .
        "'" . $addminus . "', " .
        "'" . $coreID . "', " .
        "'" . 66 . "', " .
        "'" . $pymtAmt . "', " .
        "'" . $desc_akaun2 . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "')";

    if ($display) print $sSQL . '<br />';
    else {

        $rs = &$conn->Execute($sSQL);

        $strActivity = $_POST['Submit'] . 'Kemaskini Pembayaran Baucer - ' . $no_baucer;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

        print '<script>
		window.location = "?vw=ACCbaucerpembayaran&mn=' . $mn . '&action=view&no_baucer=' . $no_baucer . '";
		</script>';
    }
}

if ($action == "Hapus") {
    if (count($pk) > 0) {
        $sWhere = "";
        foreach ($pk as $val) {
            $sSQL     = '';
            $sWhere = "ID='" . $val . "'";
            $docNo = dlookup("transactionacc", "docNo", $sWhere);
            $sSQL     = "DELETE FROM transactionacc WHERE " . $sWhere;
            if ($display) print $sSQL . '<br />';
            else $rs = &$conn->Execute($sSQL);

            $strActivity = $_POST['Submit'] . 'Hapus Kandungan Pembayaran Baucer - ' . $docNo;
            activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
        }
    }
    if (!$display) {
        print '<script>
	window.location = "?vw=ACCbaucerpembayaran&mn=' . $mn . '&action=view&no_baucer=' . $no_baucer . '";
	</script>';
    }
} elseif ($action == "Kemaskini" || $perkara || $desc_akaun || $projecting || $jabatan1) {
    $updatedBy         = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $tarikh_baucer     = saveDateDb($tarikh_baucer);
    $tarikh_bayar     = saveDateDb($tarikh_bayar);
    $yymm     = substr($tarikh_baucer, 0, 4) . substr($tarikh_baucer, 5, 2);
    $sSQL     = "";
    $sWhere = "";
    $sWhere = "no_baucer='" . $no_baucer . "'";
    $sWhere = " WHERE (" . $sWhere . ")";
    $sSQL    = "UPDATE bauceracc SET " .
        "tarikh_baucer='" . $tarikh_baucer . "'," .
        "batchNo='" . $batchNo . "'," .
        "kod_bank='" . $kod_bank . "'," .
        "tarikh_bayar='" . $tarikh_bayar . "'," .
        "keterangan='" . $keterangan . "'," .
        "bayaran_kpd='" . $bayaran_kpd . "'," .
        "Cheque='" . $Cheque . "'," .
        "cara_bayar='" . $cara_bayar . "'," .
        "disedia='" . $disedia . "'," .
        "disahkan='" . $disahkan . "'," .
        "catatan='" . $catatan . "'," .
        "pymtAmt='" . $masterAmt . "'," .
        "kod_project='" . $kod_project . "'," .
        "kod_jabatan='" . $kod_jabatan . "'," .
        "StatusID_Pymt='1'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";

    $sSQL      = $sSQL . $sWhere;

    $sSQL1      = "";
    $sWhere1 = "";
    $sWhere1 = "docNo='" . $no_baucer . "' AND addminus='" . 1 . "'";
    $sWhere1 = " WHERE (" . $sWhere1 . ")";
    $sSQL1     = "UPDATE transactionacc SET " .
        "deductID='" . $kod_bank . "'," .
        "MdeductID='" . $bankparent . "'," .
        "remark='" . $keterangan . "'," .
        "batchNo='" . $batchNo . "'," .
        "desc_akaun='" . $catatan . "'," .
        "kod_project='" . $kod_project . "'," .
        "kod_jabatan='" . $kod_jabatan . "'," .
        "pymtAmt='" . $masterAmt . "'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";

    $sSQL1   = $sSQL1 . $sWhere1;

    $sSQL2   = "";
    $sWhere2 = "";
    $sWhere2 = "docNo='" . $no_baucer . "'";
    $sWhere2 = " WHERE (" . $sWhere2 . ")";
    $sSQL2     = "UPDATE transactionacc SET " .
        "yrmth='" . $yymm . "'," .
        "tarikh_doc='" . $tarikh_baucer . "'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";

    $sSQL2   = $sSQL2 . $sWhere2;

    if ($display) print $sSQL . '<br />';
    else
        $rs = &$conn->Execute($sSQL);
    $rs = &$conn->Execute($sSQL1);
    $rs = &$conn->Execute($sSQL2);
    ///////////////////|||PERKARA|||////////////////////////////////////////////////////////////////
    if (count($perkara) > 0) {
        foreach ($perkara as $id => $value) {

            $deductID     = $value;
            $coreID     = dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));
            if ($debit[$id]) {
                $pymtAmt     = $debit[$id];
                $addminus     = 0;
            } else {
                $pymtAmt     = $kredit[$id];
                $addminus     = 1;
            }
            $sSQL     = "";
            $sWhere = "";
            $sWhere = "ID='" . $id . "'";
            $sSQL    = "UPDATE transactionacc SET " .

                "batchNo= '" . $batchNo . "'," .
                "deductID= '" . $deductID . "'," .
                "addminus= '" . $addminus . "'," .
                "coreID= '" . $coreID . "'," .
                "kod_project= '" . $kod_project . "'," .
                "kod_jabatan= '" . $kod_jabatan . "'," .
                "pymtAmt= '" . $pymtAmt . "'," .
                "updatedDate= '" . $updatedDate . "'," .
                "updatedBy= '" .  $updatedBy . "'";

            $sSQL .= " where " . $sWhere;
            if ($display) print $sSQL . '<br />';
            else $rs = &$conn->Execute($sSQL);
        }
    }
    //////////////////////////PROJEK//////////////////////////////////////////////////////////////
    if (count($kod_akaun) > 0) {
        foreach ($kod_akaun as $id => $value) {

            //$deductID = $value;
            $MdeductID     = $value;
            if ($debit[$id]) {
                $pymtAmt     = $debit[$id];
                $addminus     = 0;
            } else {
                $pymtAmt     = $kredit[$id];
                $addminus     = 1;
            }
            $sSQL     = "";
            $sWhere = "";
            $sWhere = "ID='" . $id . "'";
            $sSQL    = "UPDATE transactionacc SET " .

                "batchNo= '" . $batchNo . "'," .
                "MdeductID= '" . $MdeductID . "'," .
                "updatedDate= '" . $updatedDate . "'," .
                "updatedBy= '" .  $updatedBy . "'";


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
                $pymtAmt     = $debit[$id];
                $addminus     = 0;
            } else {
                $pymtAmt     = $kredit[$id];
                $addminus     = 1;
            }
            $sSQL     = "";
            $sWhere = "";
            $sWhere = "ID='" . $id . "'";
            $sSQL    = "UPDATE transactionacc SET " .
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
    /////////////////////////////////////////////////////////////////////////////	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (!$display) {
        print '<script>
	window.location = "?vw=ACCbaucerpembayaran&mn=' . $mn . '&action=view&no_baucer=' . $no_baucer . '";
	</script>';
    }
}

/*
          "kod_project= '" . $kod_project . "',".
          "kod_jabatan= '" . $kod_jabatan . "',".*/

//pilihan simpan
elseif ($action == "Simpan" || $simpan) {

    $updatedBy       = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $tarikh_baucer   = saveDateDb($tarikh_baucer);
    $tarikh_bayar    = saveDateDb($tarikh_bayar);

    // help prevent double entry by multiple users ----begin
    $getMax2    = "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS no2 FROM bauceracc";
    $rsMax2     = $conn->Execute($getMax2);
    $max2       = sprintf("%06s", $rsMax2->fields('no2'));

    if ($rsMax2) {
        $max2     = intval($rsMax2->fields('no2')) + 1;
        $max2     = sprintf("%06s",  $max2);
        $no_baucer2     = 'PV' . $max2;
    } else {
        $no_baucer2     = 'PV000001';
    }
    //-----end

    $sSQL     = "";
    $sSQL    = "INSERT INTO bauceracc (" .
        "no_baucer, " .
        "tarikh_baucer, " .
        "batchNo, " .
        "kod_bank, " .
        "tarikh_bayar, " .
        "keterangan, " .
        "bayaran_kpd, " .
        "Cheque, " .
        "cara_bayar, " .
        "disedia, " .
        "disahkan, " .
        "pymtAmt, " .
        "kod_project, " .
        "kod_jabatan, " .
        "StatusID_Pymt, " .
        "createdDate, " .
        "createdBy, " .
        "updatedDate, " .
        "updatedBy, " .
        "catatan) " .

        " VALUES (" .

        "'" . $no_baucer2 . "', " .
        "'" . $tarikh_baucer . "', " .
        "'" . $batchNo . "', " .
        "'" . $kod_bank . "', " .
        "'" . $tarikh_bayar . "', " .
        "'" . $keterangan . "', " .
        "'" . $bayaran_kpd . "', " .
        "'" . $Cheque . "', " .
        "'" . $cara_bayar . "', " .
        "'" . $disedia . "', " .
        "'" . $disahkan . "', " .
        "'" . $masterAmt . "', " .
        "'" . $kod_project . "', " .
        "'" . $kod_jabatan . "', " .
        "'" . 1 . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $catatan . "')";

    $sSQL1     = "";
    $sSQL1    = "INSERT INTO transactionacc (" .
        "docNo," .
        "tarikh_doc," .
        "docID," .
        "batchNo," .
        "yrmth," .
        "deductID," .
        // "taxNo," .
        "kod_project," .
        "kod_jabatan," .
        "addminus," .
        "pymtID," .
        "pymtAmt," .
        "desc_akaun," .
        "updatedBy," .
        "updatedDate	," .
        "createdBy," .
        "createdDate) " .
        " VALUES (" .
        "'" . $no_baucer2 . "', " .
        "'" . $tarikh_baucer . "', " .
        "'" . 3 . "', " .
        "'" . $batchNo . "', " .
        "'" . $yymm . "', " .
        "'" . $kod_bank . "', " .
        // "'" . $taxNo . "', " .
        "'" . $kod_project . "', " .
        "'" . $kod_jabatan . "', " .
        "'" . 1 . "', " .
        "'" . 66 . "', " .
        "'" . $masterAmt . "', " .
        "'" . $keterangan . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "')";

    if ($display) print $sSQL . '<br />';
    else

        $rs = &$conn->Execute($sSQL);
    $rs = &$conn->Execute($sSQL1);

    $getMax = "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS no FROM bauceracc";
    $rsMax     = $conn->Execute($getMax);
    $max     = sprintf("%06s", $rsMax->fields('no'));
    if (!$display) {
        print '<script>
	window.location = "?vw=ACCbaucerpembayaran&mn=' . $mn . '&action=view&add=1&no_baucer=PV' . $max . '";
	</script>';
    }
}

$strTemp .=
    '<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
    . '<div style="width: 100%; text-align:left">'
    . '<div>&nbsp;</div>'
    . '<div class="table-responsive"><form name="MyForm" action="?vw=ACCbaucerpembayaran&mn=' . $mn . '" method="post">'
    . '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print
    '<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>Nombor Rujukan</td>
				<td valign="top"></td>
				<td>
					<input  name="no_baucer" value="' . $no_baucer . '" type="text" size="20" maxlength="50" class="form-control-sm" readonly/>
				</td>
			</tr>

			<tr>
				<td>* Batch</td>
				<td valign="top"></td>
				<td>' . selectbatchBAUCER($batch, 'batchNo') . '</td>
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
				<td valign="top" align="right">Tarikh Baucer</td>
				<td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh_baucer">
				<input type="text" name="tarikh_baucer" class="form-control-sm" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh_baucer"
					data-date-autoclose="true" value="' . $tarikh_baucer . '">
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
				<td valign="top">* Bayar Kepada </td>
				<td valign="top"></td>
				<td><input class="form-control-sm" name="bayaran_kpd" value="' . $bayaran_kpd . '" type="text" size="50" maxlength="255" /></td>
			</tr>

			<tr>
				<td valign="top">Keterangan</td>
				<td valign="top"></td>
				<td>
					<textarea name="keterangan" cols="50" rows="4" class="form-control-sm">' . $keterangan . '</textarea>
				</td>
			</tr>

			<tr>
				<td valign="top">Projek</td>
				<td valign="top"></td>
				<td>' . selectproject($kod_project, 'kod_project') . '</td>
			</tr>

			<tr>
				<td valign="top">Jabatan</td>
				<td valign="top"></td>
				<td>' . selectjabatan($kod_jabatan, 'kod_jabatan') . '</td>
			</tr>
		  
		</table>
	</td>


	<td valign="top">&nbsp;</td>
	<td width="48%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">

			<tr>
				<td valign="top" align="right">Cara Bayaran</td><td valign="top"></td>
				<td>' . selectbayar($cara_bayar, 'cara_bayar') . '</td>
			</tr>

			<tr>
				<td valign="top" align="right">Cheque Number</td><td valign="top"></td>
				<td><input class="form-control-sm" name="Cheque" value="' . $Cheque . '" type="text" size="20" maxlength="10" /></td>
			</tr>

			<tr>
				<td valign="top" align="right">Tarikh Bayaran</td><td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh_bayar">
				<input type="text" name="tarikh_bayar" class="form-control-sm" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh_bayar"
					data-date-autoclose="true" value="' . $tarikh_bayar . '">
				<div class="input-group-append">
					<span class="input-group-text">
						<i class="mdi mdi-calendar"></i></span>
				</div>
				</div>
				</td>
			</tr>

			<tr>
				<td valign="top" align="right">Master Amaun (RM)</td><td valign="top"></td>
				<td><input id="master" class="form-control-sm" value="' . $masterAmt . '" type="text" size="20" maxlength="10"/ readonly></td>
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
if ($action == "view"  && !is_int(dlookup("transactionacc", "ID", "docNo='" . $no_baucer . "'"))) {
    print '
	<tr>
			<td align= "right" colspan="3">';
    if (!$add) print '
		<!-- Implementing the visual effect on button Tambah for akaun. START -->
		<div class="request-loader-container" id="loaderContainer">
			<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCbaucerpembayaran&mn=' . $mn . '&action=' . $action . '&no_baucer=' . $no_baucer . '&add=1\';">
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
				<td nowrap="nowrap" align="right"><b>* Jumlah (RM)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

// Determine if there are no records for akaun. If so, visually guide user to Tambah akaun instead of click Kemaskini.
//------------- START
$noRecords = true;

if (($action == "view")) {

    if ($rsDetail->RecordCount() > 0) {
        //If there are records, set flag to false. User can click Kemaskini.
        $noRecords = false;
        $i = 0;

        while (!$rsDetail->EOF) {

            $id         = $rsDetail->fields('ID');
            $perkara     = $rsDetail->fields('deductID');
            $kod_akaun     = dlookup("generalacc", "parentID", "ID=" . $perkara);
            $namaparent    = dlookup("generalacc", "name", "ID=" . $kod_akaun);
            $desc_akaun = $rsDetail->fields('desc_akaun');

            if ($rsDetail->fields('addminus')) {
                $kredit = $rsDetail->fields('pymtAmt');
            } else {
                $debit     = $rsDetail->fields('pymtAmt');
            }

            print       '
			<tr>
				<td class="Data">&nbsp;' . ++$i . '.</td>	
				<td class="Data" nowrap="nowrap">' . strSelect3($id, $perkara) . '&nbsp;
				<input class="Data" name="kod_akaun[' . $id . ']" type="hidden" size="10" maxlength="10" value="' . $kod_akaun . '"/>
				</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" class="form-control-sm" rows="4" cols="40" maxlength="500">' . $desc_akaun . '</textarea>&nbsp;
				</td>
				
				<td class="Data" align="right">
					<input name="debit[' . $id . ']" type="text" size="10" class="form-control-sm" maxlength="10" value="' . $debit . '" style="text-align:right;" readonly/>&nbsp;
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '">&nbsp;</td>

			</tr>';
            $totalDb += $debit;
            $debit       = '';

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
		disableElementsByName("no_baucer");
        disableElementsByName("batchNo");
		disableElementsByName("kod_bank");
		disableElementsByName("tarikh_baucer");
        disableElementsByName("bayaran_kpd");
		disableElementsByName("keterangan");
        disableElementsByName("kod_project");
		disableElementsByName("kod_jabatan");
		disableElementsByName("cara_bayar");
		disableElementsByName("Cheque");
		disableElementsByName("tarikh_bayar");
		disableElementsByName("disedia");
		disableElementsByName("disahkan");
		disableElementsByName("catatan");
		disableElementById("bottomButton");
		});
		</script>
		';
    }
    //------------- END
}

$strDeductIDList     = deductListb2(1);
$strDeductCodeList     = deductListb2(2);
$strDeductNameList     = deductListb2(3);
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
    print       '
			<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>

				<td class="Data">' . $strSelect . ' 
				<input name="kod_akaun2" type="hidden" size="10" maxlength="10" value="' . $kod_akaun2 . '" class="Data"/>
				</td>

				<td class="Data" align="left">
					<textarea name="desc_akaun2" rows="4" cols="40" maxlength="500" class="form-control-sm" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
				</td>

				<td class="Data" align="right">
					<input type="hidden" name="ruj2" val="0">
					<input  name="debit2" type="text" class="form-control-sm" size="10" style="text-align: right;" maxlength="10" value="' . $loanAmt . '"/>&nbsp;
				</td>

				<td class="Data" align="right"><b>&nbsp;</b></td>

			</tr>';
}

//bahagian bawah skali
if ($totalDb <> 0) {
    $clsRM->setValue($totalDb);
    $strTotal = ucwords($clsRM->getValue()) . ' Ringgit Sahaja.';
}

$idname = get_session('Cookie_fullName');

print
    '<tr class="table-secondary">
				<td class="Data" colspan="3" align="right"><b>Jumlah (RM)</b></td>
				<td class="Data" id="totalJumlah" align="right"><b>' . number_format($totalDb, 2) . '&nbsp;</b></td>
				<td class="Data" align=""><b>&nbsp;</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr colspan="3">
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="3">

			<tr>
				<td nowrap="nowrap">Jumlah Dalam Perkataan</td>
				<td valign="top"></td>
				<td>
					<input class="form-control-sm" name="" size="80" maxlength="80" value="' . $strTotal . '" readonly>
					<input class="Data" type="hidden" name="masterAmt" value="' . $totalDb . '">
					<input class="Data" type="hidden" name="bankparent" value="' . $bankparent . '">
				</td>
			</tr>

			<tr><td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td><td>' . selectAdmin($disedia, 'disedia') . '</td></tr>
			<tr><td nowrap="nowrap">Disahkan Oleh</td><td valign="top"></td><td>' . selectAdmin($disahkan, 'disahkan') . '</td></tr>

			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td>
				<td valign="top"></td>
				<td valign="top">
					<textarea  class="form-control-sm" name="catatan" cols="50" rows="4">' . $catatan . '</textarea>
				</td>
			</tr>
		</table>
	</td>';

$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
print '
<tr>
	<td>
	<input type="button" name="print" value="Cetakan Koperasi" class="btn btn-secondary" onClick= "print_(\'ACCbaucerpembayaranPrint.php?id=' . $no_baucer . '\')">&nbsp;
	<input type="button" name="print" value="Cetakan Pelanggan" class="btn btn-secondary" onClick= "print_(\'ACCbaucerpembayaranPrintPelanggan.php?id=' . $no_baucer . '\')">&nbsp;

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
		const masterValue 		= document.getElementById("master").value;
		const master 			= parseFloat(masterValue);
		const jumlah 			= parseFloat(document.getElementById("totalJumlah").innerText.replace(/,/g, ""));
    	var noRecords 			= ' . json_encode($noRecords) . ';
		const requestLoader 	= document.getElementById("requestLoader");
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
			alert(\'Pilih Anggota!\');
            count++;
		  }
		  
		  if(act == \'Kemaskini\') {
  
		  if(e.elements[c].name=="debit2" && e.elements[c].value==\'\') {
			alert(\'Ruang jumlah perlu diisi!\');
            count++;
		  }
		  }

		  if(act == \'Kemaskini\') {

		  if(e.elements[c].name=="perkara2" && e.elements[c].value==\'\') {
			alert(\'Ruang akaun perlu diisi!\');
            count++;
		  }
		  }

		  if(act == \'Kemaskini\') {
  
		  if(e.elements[c].name=="tarikh_baucer" && e.elements[c].value==\'\') {
			  alert(\'Pilih Tarikh Baucer!\');
			  count++;
			}
		  }

		  if(act == \'Kemaskini\') {

		  if(e.elements[c].name=="tarikh_bayar" && e.elements[c].value==\'\') {
			alert(\'Pilih Tarikh Bayaran!\');
			count++;
			}
		  }

		  if(act == \'Simpan\') {
  
		  if(e.elements[c].name=="batchNo" && e.elements[c].value==\'\') 
		  	{
			alert(\'Pilih Batch!\');
            count++;
		 	}
		  }

		  	  if(act == \'Simpan\') {
  
		  if(e.elements[c].name=="kod_bank" && e.elements[c].value==\'\') 
			{
			alert(\'Pilih Bank!\');
			count++;
			}
		  }

		  if(act == \'Simpan\') {

		  if(e.elements[c].name=="bayaran_kpd" && e.elements[c].value==\'\') 
			{
			alert(\'Ruang bayar kepada perlu diisi!\');
			count++;
			}
		  }

		  if(act == \'Simpan\') {

		  if(e.elements[c].name=="tarikh_baucer" && e.elements[c].value==\'\') 
			{
			alert(\'Pilih Tarikh Baucer!\');
			count++;
			}
		  }

		  if(act == \'Simpan\') {

		  if(e.elements[c].name=="tarikh_bayar" && e.elements[c].value==\'\') 
			{
			alert(\'Pilih Tarikh Bayaran!\');
			count++;
			}
		  }

		}
		if(count==0) {
        // Disable the submit button to prevent duplicate entries by user if click button multiple times
  		var submitButton = document.querySelector("input[name=\\"action\\"]"); 
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

</script>';
include("footer.php");