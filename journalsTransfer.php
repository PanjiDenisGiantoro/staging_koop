<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: journalsTransfer.php
 *			Date 		: 27/7/2006
 *			Description	: Jurnal Pindahan Syer/Yuran/Simpanan Anggota
 *			Keywords 	: duplicate (to prevent user fault)
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=journalsTransferList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;JURNAL PINDAHAN</b>';

if (!isset($mm))    $mm = date("m");
if (!isset($yy))    $yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display = 0;
if ($no_jurnal && $action == "view") {
    $sql            = "SELECT * FROM jurnal
						WHERE no_jurnal = '" . $no_jurnal . "'";
    $rs             = $conn->Execute($sql);
    $no_jurnal      = $rs->fields('no_jurnal');
    $tarikh_jurnal  = toDate("d/m/y", $rs->fields('tarikh_jurnal'));
    // $no_bond 	= $rs->fields('no_bond');
    // $no_anggota 	= $rs->fields('no_anggota');
    $disediakan     = $rs->fields('disediakan');
    $disemak        = $rs->fields('disemak');
    $disahkan       = $rs->fields('disahkan');
    $keterangan     = $rs->fields('keterangan');
    $kod_caw        = $rs->fields('kod_caw');
    $no_siri        = $rs->fields('no_siri');
    $tarikh_bank    = toDate("d/m/y", $rs->fields('tarikh_bank'));
    $nama           = $rs->fields('name');

    $sql2             = "SELECT * FROM transactionacc WHERE docNo = '" . $no_jurnal . "' ORDER BY ID";
    $rsDetail         = $conn->Execute($sql2);
    if ($rsDetail->RowCount() < 1) $noTran = true;
} elseif ($action == "new") {
    $getNo     = "SELECT MAX(CAST(right(no_jurnal,6) AS SIGNED INTEGER)) AS nombor FROM jurnal";
    $rsNo     = $conn->Execute($getNo);
    $tarikh_jurnal     = date("d/m/Y");
    $tarikh_bank     = date("d/m/Y");
    if ($rsNo) {
        $nombor = $rsNo->fields('nombor') + 1;
        $nombor = sprintf("%06s",  $nombor);
        $no_jurnal = 'J' . $nombor;
    } else {
        $no_jurnal = 'J000001';
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$last_id = null; // Declare the variable outside to store last inserted ID

// if((($perkara2) == 1596) OR (($perkara2) == 1595)){
//all perkara also insert into transaction
if ($perkara2) {

    $updatedBy         = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $deductID     = $perkara2;
    if ($debit2) {
        $pymtAmt     = $debit2;
        $addminus     = 0;
        $cajAmt     = 0.0;
    } else {
        $pymtAmt     = $kredit2;
        $addminus     = 1;
        $cajAmt     = 0.0;
    }
    $userID     = dlookup("userdetails", "userID", "memberID = '" . $no_anggota . "'");

    if ($pymtAmt == '') $pymtAmt = '0.0';
    $sSQL    = "INSERT INTO transaction (" .
        "docNo," .
        "userID," .
        "yrmth," .
        "deductID," .
        "addminus," .
        "pymtID," .
        "pymtAmt," .
        "pymtRefer," .
        "createdDate," .
        "createdBy," .
        "updatedDate," .
        "updatedBy)" .
        " VALUES (" .
        "'" . $no_jurnal . "', " .
        "'" . $userID . "', " .
        "'" . $yymm . "', " .
        "'" . $deductID . "', " .
        "'" . $addminus . "', " .
        "'" . 66 . "', " .
        "'" . $pymtAmt . "', " .
        "'pindahan', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "')";
    if ($display) print $sSQL . '<br />';
    else {
        $rs = &$conn->Execute($sSQL);

        // Get the last inserted ID from the transaction table
        $last_id = $conn->Insert_ID();
    }

    $sSQL    = "INSERT INTO transactionacc (" .
        "docNo," .
        "tarikh_doc," .
        "docID," .
        "IDtrans," .
        "userID," .
        "yrmth," .
        "deductID," .
        "addminus," .
        "pymtID," .
        "pymtAmt," .
        "pymtRefer," .
        "desc_akaun," .
        "createdDate," .
        "createdBy," .
        "updatedDate," .
        "updatedBy)" .
        " VALUES (" .
        "'" . $no_jurnal . "', " .
        "'" . $tarikh_jurnal . "', " .
        "'" . 11 . "', " .
        "'" . $last_id . "', " .
        "'" . $userID . "', " .
        "'" . $yymm . "', " .
        "'" . $deductID . "', " .
        "'" . $addminus . "', " .
        "'" . 66 . "', " .
        "'" . $pymtAmt . "', " .
        "'pindahan', " .
        "'" . $desc_akaun2 . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "')";
    if ($display) print $sSQL . '<br />';
    else {
        $rs = &$conn->Execute($sSQL);
    }

    $strActivity = $_POST['Submit'] . 'Kemaskini Jurnal Pindahan - ' . $no_jurnal;
    activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

    print '<script>
		window.location = "?vw=journalsTransfer&mn=' . $mn . '&action=view&no_jurnal=' . $no_jurnal . '";
		</script>';
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($action == "Hapus") {
    if (count($pk) > 0) {
        $sWhere = "";
        foreach ($pk as $val) {
            $sSQL     = '';
            $sWhere = "ID='" . $val . "'";

            $sSQL     = "DELETE FROM transactionacc WHERE " . $sWhere;
            $del     = dlookup("transactionacc", "IDtrans", "ID='" . $val . "'");

            $sSQL1     = '';
            $sWhere = "ID='" . $del . "'";
            $docNo = dlookup("transaction", "docNo", $sWhere);
            $sSQL1     = "DELETE FROM transaction WHERE " . $sWhere;

            if ($display) print $sSQL . '<br />';
            else $rs = &$conn->Execute($sSQL);
            $rs = &$conn->Execute($sSQL1);

            $strActivity = $_POST['Submit'] . 'Hapus Kandungan Jurnal Pindahan - ' . $docNo;
            activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
        }
    }
    if (!$display) {
        print '<script>
	window.location = "?vw=journalsTransfer&mn=' . $mn . '&action=view&no_jurnal=' . $no_jurnal . '";
	</script>';
    }
} elseif ($action == "Kemaskini" || $perkara) {
    $updatedBy         = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $tarikh_jurnal     = saveDateDb($tarikh_jurnal);
    $yymm             = substr($tarikh_jurnal, 0, 4) . substr($tarikh_jurnal, 5, 2);
    $tarikh_bank     = saveDateDb($tarikh_bank);
    $sSQL     = "";
    $sWhere = "";
    $sWhere = "no_jurnal='" . $no_jurnal . "'";
    $sWhere = " WHERE (" . $sWhere . ")";
    $sSQL    = "UPDATE jurnal SET " .
        "tarikh_jurnal='" . $tarikh_jurnal . "'," .
        // "no_bond='" . $no_bond . "',".
        // "no_anggota='" . $no_anggota . "',".
        "disediakan='" . $disediakan . "'," .
        "disemak='" . $disemak . "'," .
        "disahkan='" . $disahkan . "'," .
        "keterangan='" . $keterangan . "'," .
        "kod_caw='" . $kod_caw . "'," .
        "no_siri='" . $no_siri . "'," .
        "tarikh_bank='" . $tarikh_bank . "'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";
    $sSQL = $sSQL . $sWhere;

    $sSQL1     = "";
    $sWhere = "";
    $sWhere = "docNo='" . $no_jurnal . "'";
    $sWhere = " WHERE (" . $sWhere . ")";
    $sSQL1    = "UPDATE transactionacc SET " .
        "yrmth='" . $yymm . "'," .
        "tarikh_doc='" . $tarikh_jurnal . "'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";

    $sSQL1     = $sSQL1 . $sWhere;

    $sSQL2     = "";
    $sWhere = "";
    $sWhere = "docNo='" . $no_jurnal . "'";
    $sWhere = " WHERE (" . $sWhere . ")";
    $sSQL2    = "UPDATE transaction SET " .
        "yrmth='" . $yymm . "'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";

    $sSQL2     = $sSQL2 . $sWhere;

    if ($display) print $sSQL . '<br />';
    else
        $rs = &$conn->Execute($sSQL);
    $rs = &$conn->Execute($sSQL1);
    $rs = &$conn->Execute($sSQL2);

    if (count($desc_akaun) > 0) {
        foreach ($desc_akaun as $id => $value) {
            $desc_akaun     = $value;
            $sSQL     = "";
            $sWhere = "";
            $sWhere = "ID='" . $id . "'";
            $sSQL    = "UPDATE transactionacc SET " .
                "desc_akaun=" . tosql($desc_akaun, "Text") .
                ",updatedDate=" . tosql($updatedDate, "Text") .
                ",updatedBy=" . tosql($updatedBy, "Text");
            $sSQL     .= " where " . $sWhere;

            if ($display) print $sSQL . '<br />';
            else $rs = &$conn->Execute($sSQL);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (count($perkara) > 0) {
        foreach ($perkara as $id => $value) {
            $deductID     = $value;
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
                "deductID=" . tosql($deductID, "Number") .
                ",addminus=" . $addminus .
                ",pymtAmt=" . tosql($pymtAmt, "Number") .
                ",updatedDate=" . tosql($updatedDate, "Text") .
                ",updatedBy=" . tosql($updatedBy, "Text");
            $sSQL     .= " where " . $sWhere;

            $upd     = dlookup("transactionacc", "IDtrans", "ID='" . $id . "'");

            $sSQL1      = "";
            $sWhere1 = "";
            $sWhere1 = "ID='" . $upd . "'";
            $sSQL1     = "UPDATE transaction SET " .
                "deductID=" . tosql($deductID, "Number") .
                ",addminus=" . $addminus .
                ",pymtAmt=" . tosql($pymtAmt, "Number") .
                ",updatedDate=" . tosql($updatedDate, "Text") .
                ",updatedBy=" . tosql($updatedBy, "Text");
            $sSQL1     .= " where " . $sWhere1;
            if ($display) print $sSQL . '<br />';
            else $rs = &$conn->Execute($sSQL);
            $rs = &$conn->Execute($sSQL1);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (count($kod_master) > 0) {
        foreach ($kod_master as $id => $value) {
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
                "MdeductID=" . tosql($MdeductID, "Number");
            $sSQL     .= " where " . $sWhere;

            $upd     = dlookup("transactionacc", "IDtrans", "ID='" . $id . "'");

            $sSQL1      = "";
            $sWhere1 = "";
            $sWhere1 = "ID='" . $upd . "'";
            $sSQL1     = "UPDATE transaction SET " .
                "MdeductID=" . tosql($MdeductID, "Number");
            $sSQL1     .= " where " . $sWhere1;
            if ($display) print $sSQL . '<br />';
            else $rs = &$conn->Execute($sSQL);
            $rs = &$conn->Execute($sSQL1);
        }
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (!$display) {
        print '<script>
	window.location = "?vw=journalsTransfer&mn=' . $mn . '&action=view&no_jurnal=' . $no_jurnal . '";
	</script>';
    }
} elseif ($action == "Simpan" || $simpan) {
    $updatedBy         = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");

    // help prevent double entry by multiple users ----begin
    $getMax2     = "SELECT MAX(CAST(right(no_jurnal,6) AS SIGNED INTEGER)) AS no2 FROM jurnal";
    $rsMax2     = $conn->Execute($getMax2);
    $max2        = sprintf("%06s", $rsMax2->fields('no2'));

    if ($rsMax2) {
        $max2 = $rsMax2->fields('no2') + 1;
        $max2 = sprintf("%06s",  $max2);
        $no_jurnal2 = 'J' . $max2;
    } else {
        $no_jurnal2 = 'J000001';
    }
    //-----end

    $sSQL     = "";
    $sSQL    = "INSERT INTO jurnal (" .
        "no_jurnal, " .
        "tarikh_jurnal, " .
        "jenis, " .
        "disediakan, " .
        "disahkan, " .
        "disemak, " .
        "keterangan, " .
        "tarikh_bank, " .
        "createdDate, " .
        "createdBy, " .
        "updatedDate, " .
        "updatedBy) " .
        " VALUES (" .
        tosql($no_jurnal2, "Text") . ", " .
        tosql(saveDateDb($tarikh_jurnal), "Text") . ", " .
        "'pindahan', " .
        tosql($disediakan, "Text") . ", " .
        tosql($disemak, "Text") . ", " .
        tosql($disahkan, "Text") . ", " .
        tosql($keterangan, "Text") . ", " .
        tosql(saveDateDb($tarikh_bank), "Text") . ", " .
        tosql($updatedDate, "Text") . ", " .
        tosql($updatedBy, "Text") . ", " .
        tosql($updatedDate, "Text") . ", " .
        tosql($updatedBy, "Text")  . ") ";
    if ($display) print $sSQL . '<br />';
    else $rs = &$conn->Execute($sSQL);

    $getMax = "SELECT MAX(CAST(right(no_jurnal,6) AS SIGNED INTEGER)) as no FROM jurnal";
    $rsMax     = $conn->Execute($getMax);
    $max     = sprintf("%06s", $rsMax->fields('no'));
    if (!$display) {
        print '<script>
	window.location = "?vw=journalsTransfer&mn=' . $mn . '&action=view&add=1&no_jurnal=J' . $max . '";
	</script>';
    }
}



$strTemp .=
    '<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
    . '<div style="width: 100%; text-align:left">'
    . '<div>&nbsp;</div>'
    . '<form name="MyForm" action="?vw=journalsTransfer&mn=' . $mn . '" method="post">'
    . '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;

print
    '<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr><td>Nombor Jurnal</td><td valign="top"></td><td><input name="no_jurnal" value="' . $no_jurnal . '" type="text" size="20" maxlength="50" class="form-controlx" readonly/></td></tr>
			<tr><td>* Tarikh</td><td valign="top"></td>
			<td>
			<div class="input-group" id="tarikh_jurnal">
			<input type="text" name="tarikh_jurnal" id="tarikh_jurnal_input" class="form-control-sm" placeholder="dd/mm/yyyy"
				data-provide="datepicker" data-date-container="#tarikh_jurnal"
				data-date-autoclose="true" value="' . $tarikh_jurnal . '">
			<div class="input-group-append">
				<span class="input-group-text">
					<i class="mdi mdi-calendar"></i></span>
			</div>
			</div>
			</td>
		</table>
	</td>
</tr>
<tr><td colspan="3"><hr class="mt-3" /></td></tr>

<tr><td>&nbsp;</td></tr>';

function deductListS($val)
{
    global $conn;
    //get list of deduction value into array
    $sSQL = 'SELECT * FROM general WHERE category=\'J\' AND j_Pindah IN (1) ORDER BY name ASC';
    $GetData = $conn->Execute($sSQL);
    if ($GetData->RowCount() <> 0) {
        $strDeductIDList = array();
        $strDeductCodeList = array();
        $strDeductNameList = array();
        $nCount = 0;
        while (!$GetData->EOF) {
            $strDeductIDList[$nCount] = $GetData->fields('ID');
            $strDeductCodeList[$nCount] = $GetData->fields('code');
            $strDeductNameList[$nCount] = $GetData->fields('name');
            $GetData->MoveNext();
            $nCount++;
        }
    }
    //end get list
    if ($val == 1) return $strDeductIDList;
    if ($val == 2) return $strDeductCodeList;
    if ($val == 3) return $strDeductNameList;
}

function strSelect2S($id, $code, $type = "arr")
{
    $strDeductIDList = deductListS(1);
    $strDeductCodeList = deductListS(2);
    $strDeductNameList = deductListS(3);

    if ($type == "arr") {
        $name = 'perkara[' . $id . ']';
    } else {
        $name = 'perkara2';
    }

    $strSelect = '<select class="form-select-sm" name="' . $name . '" onchange="document.MyForm.submit();">
					<option value="">- Kod -';
    for ($i = 0; $i < count($strDeductIDList); $i++) {
        $strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
        if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
        $strSelect .=  '>' . $strDeductCodeList[$i] . '&nbsp; - &nbsp;' . $strDeductNameList[$i] . '';
    }
    $strSelect .= '</select>';
    return $strSelect;
}

//----------
if ($action == "view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $no_jurnal . "'"))) {
    print '
<tr>
	<td align= "right" colspan="3">';
    if (!$add) print '
		<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=journalsTransfer&mn=' . $mn . '&action=' . $action . '&no_jurnal=' . $no_jurnal . '&add=1\';">';
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
				<td nowrap="nowrap"><b>* Perkara</b></td>
				<td nowrap="nowrap"><b>Anggota</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="right"><b>Debit (RP)</b></td>
				<td nowrap="nowrap" align="right"><b>Kredit (RP)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';



if ($action == "view") {
    $i = 1;
    $j = 1;
    while (!$rsDetail->EOF) {
        $id         = tohtml($rsDetail->fields('ID'));
        $perkara     = $rsDetail->fields('deductID');
        $kod_objek     = dlookup("general", "code", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
        $kod_akaun     = dlookup("general", "c_Panel", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
        $keterangan2 = dlookup("general", "name", "ID=" . tosql($rsDetail->fields('deductID'), "Text"));
        $kod_master = dlookup("general", "c_master", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
        // $no_bond 	= $rsDetail->fields(pymtRefer);
        $namagl     = dlookup("generalacc", "name", "ID=" . tosql($kod_master, "Number"));
        // $keterangan2 = dlookup("general", "name", "ID=" . tosql($kod_akaun, "Number"));
        $desc_akaun     = $rsDetail->fields('desc_akaun');

        $no_anggotaS = $rsDetail->fields('userID');
        $namaS         = dlookup("users", "name", "userID=" . $no_anggotaS);

        if ($rsDetail->fields('addminus')) {
            $kredit = $rsDetail->fields('pymtAmt');
        } else {
            $debit     = $rsDetail->fields('pymtAmt');
        }
        print       '<tr>
				<td class="Data">&nbsp;' . $i++ . '.</td>				
				<td class="Data" nowrap="nowrap">' . strSelect2S($id, $perkara) . '&nbsp;

				<input class="form-control-sm" name="namagl[' . $id . ']" type="hidden" size="30" maxlength="30" value="' . $namagl . '" readonly/>
				<input class="form-control-sm" name="kod_master[' . $id . ']" type="hidden" size="10" maxlength="10" value="' . $kod_master . '" readonly/>

				<td>
					<input class="form-control-sm" name="no_anggotaS" value="' . $no_anggotaS . '" type="text" size="4" maxlength="50" readonly/>&nbsp;
					<input name="nama_anggotaS" value="' . $namaS . '" type="text" size="30" class="form-control-sm" maxlength="50" readonly/>
				</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" class="form-control-sm" rows="4" cols="40" maxlength="500">' . $desc_akaun . '</textarea>&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="debit[' . $id . ']" class="form-control-sm" type="text" size="10" maxlength="10" value="' . $debit . '" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="kredit[' . $id . ']" class="form-control-sm" type="text" size="10" maxlength="10" value="' . $kredit . '" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '">&nbsp;</td>
			</tr>';
        $totalDb += $debit;
        $totalKt += $kredit;
        $debit     = '';
        $kredit     = '';
        $j++;
        $rsDetail->MoveNext();
    }
}

$strDeductIDList     = deductListS(1);
$strDeductCodeList     = deductListS(2);
$strDeductNameList     = deductListS(3);
$name = 'perkara2';

$strSelectS = '<select name="' . $name . '" class="form-select-sm" id="deductSelect" onchange="updateDescAkaun(); updateAmount();">
				<option value="">- Kod -';
for ($i = 0; $i < count($strDeductIDList); $i++) {
    $strSelectS .= '	<option value="' . $strDeductIDList[$i] . '" ';
    if ($code == $strDeductIDList[$i]) $strSelectS .= ' selected';
    $strSelectS .=  '>' . $strDeductCodeList[$i] . '&nbsp; - &nbsp;' . $strDeductNameList[$i] . '';
}
$strSelectS .= '</select>';

if ($add) {
    print       '<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data">' . $strSelectS . '</td>';

    print '
				
					<input name="namagl2" type="hidden" size="30" maxlength="30" value="' . $keterangan22 . '" class="form-control-sm" readonly/>
					<input name="kod_master2" type="hidden" size="10" maxlength="10" value="' . $kod_objek2 . '" class="form-control-sm"/>

				<td>
					<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'selToMember.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;
					<input class="form-control-sm" name="no_anggota" value="' . $no_anggota . '" type="text" size="4" maxlength="50" readonly/>&nbsp;
					<input name="nama_anggota" value="' . $nama . '" type="text" size="30" class="form-control-sm" maxlength="50" readonly/>
				</td>

				<td class="Data" align="left">
					<textarea name="desc_akaun2" rows="4" cols="40" maxlength="500" class="form-control-sm" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="debit2" class="form-control-sm" type="text" size="10" maxlength="10" value="' . $debit2 . '" align="right" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="kredit2" class="form-control-sm" type="text" size="10" maxlength="10" value="' . $kredit2 . '" align="right" style="text-align:right;"/>&nbsp;
				</td>
				
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}

$baki = $totalDb - $totalKt;
print         '<tr class="table-secondary">
				<td class="Data" colspan="4" align="right"><b>Jumlah Keseluruhan (RP)</b></td>
				<td class="Data" align="right"><b>' . number_format($totalDb, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';

$colorPen = "Data";
if ($baki == 0) {
    $colorPen = "greenText";

    print '
			<tr class="table-secondary">
				<td class="Data" colspan="4" align="right"><b>Baki (RP)</b></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
				<td class="Data" align="right"><font class="' . $colorPen . '"><b>' . number_format($baki, 2) . '&nbsp;</b></font></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
} else {
    $colorPen = "redText";
    print '
			<tr class="table-secondary">
				<td class="Data" colspan="4" align="right"><b>Baki (RP)</b></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
				<td class="Data" align="right"><font class="' . $colorPen . '"><b>' . number_format($baki, 2) . '&nbsp;</b></font></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}
$idname = get_session('Cookie_fullName');

/*if($baki <> 0){
			print '<script>alert("Baki Tidak Sama");</script>';
		}*/

print ' 	</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr>
			<td nowrap="nowrap">Disediakan Oleh</td>
			<td valign="top"></td>
			<td><input class="form-controlx" name="disediakan" value="' . $idname . '" type="text" size="20" maxlength="15"/></td>
			</tr>
			<tr>
			<td nowrap="nowrap">Disemak Oleh</td>
			<td valign="top"></td>
			<td>' . selectAdmin($disemak, 'disemak') . '</td>
			</tr>
			<tr>
			<td nowrap="nowrap">Disahkan Oleh</td>
			<td valign="top"></td>
			<td>' . selectAdmin($disahkan, 'disahkan') . '</td>
			</tr>
			<tr>
			<td nowrap="nowrap" valign="top">Catatan</td>
			<td valign="top"></td>
			<td valign="top"><textarea name="keterangan" cols="50" class="form-controlx" rows="4">' . $keterangan . '</textarea></td>
			</tr>
		</table>
	</td>
	<td>&nbsp;</td>
</tr>';
if ($no_jurnal) {
    $straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
    print '
<tr><td>
	<input type="button" name="print" value="Cetakan" class="btn btn-secondary" onClick= "print_(\'journalsTransferPrint.php?id=' . $no_jurnal . '\')">&nbsp;
	<input type="button" name="action" value="' . $straction . '" class="btn btn-primary" onclick="CheckField(\'' . $straction . '\')">';
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
	var allChecked=false;
	function ITRViewSelectAll() {
	    e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}

	function print_(url) {
		window.open(url,"pop","top=100, left=100, width=600, height=400, scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");					
	}
	
	function CheckTotal(el) {
	    e = document.MyForm.elements;
		f = document.MyForm;
		dbt = 0;
		var arrID = new Array(' . $str_id . ');
	    var num;
		var crt=0;
		var krt=0;

		for(num in arrID){
			a = arrID[num];
			j = "debit["+a+"]";
			
			for(c=0; c< e.length; c++) {
				if (e[c].name==j){ 
					cval = parseFloat(e[c].value);
					if(cval>0){
						crt = crt + cval;
					}
				}
			}
		}
		for(num2 in arrID){
			b = arrID[num2];
			k = "kredit["+b+"]";

			for(d=0; d< e.length; d++) {
				if (e[d].name==k){
					kval = parseFloat(e[d].value);
					if(kval>0){
					krt = krt + kval;
					}
				}
			}
		}
		if(crt==krt){
	          f.submit();
		}else{
	         alert(\'Sila pastikan jumlah debit dan kredit sepadan.\');
		}

	}
	function CheckField(act) {
	    e = document.MyForm;
	    f = document.MyForm.elements;

		count = 0;
		debit = 0;
		kredit = 0;
		debit_kredit=0;
		single_d = 0;
		single_k = 0;
		firstCol = 0;
		//----
		
		var arrID = new Array(' . $str_id . ');
	    var num;
		var dbt = 0;
		var krt=0;

		for(c=0; c<e.elements.length; c++) {
		  //if(!e.debit2.value == \'\') alert(e.nama_anggota.value);
		  if(e.elements[c].name=="no_anggota" && e.elements[c].value==\'\') {
			alert(\'Sila pilih anggota!\');
            count++;
		  }

		  if(act == \'Kemaskini\') {
			  if(e.elements[c].name=="debit2" && e.elements[c].value==\'\') {
				debit = 0;
				debit_kredit = 1;
			  } else if(e.elements[c].name=="debit2" && !e.elements[c].value==\'\') {
				debit = 1;				
				debit_kredit = 1;
			  }	else if(e.elements[c].name=="kredit2" && e.elements[c].value==\'\') {
				kredit = 0;
				debit_kredit = 1;
			  } else if(e.elements[c].name=="kredit2" && !e.elements[c].value==\'\') {
				kredit = 1;
				debit_kredit = 1;
			  } else if (e.elements[c].name=="perkara2" && e.elements[c].value==\'\') {
				alert(\'Sila masukkan ruang perkara!\');
				count++;
			  }
		  }

		}

		for(num in arrID){                                     
			a = arrID[num];                                
			j = "debit["+a+"]";                            
			for(c=0; c< f.length; c++) {                   
				if (f[c].name==j){                     
					cval = parseFloat(f[c].value); 
					if(cval>0){                    
						dbt = dbt + cval;      
					}
					firstCol = 1;
				}                                      
			}                                              
		}                                                      
															   
		for(num2 in arrID){                                    
			b = arrID[num2];                               
			k = "kredit["+b+"]";                           
															   
			for(d=0; d< f.length; d++) {                   
				if (f[d].name==k){                     
					kval = parseFloat(f[d].value); 
					if(kval>0){                    
					krt = krt + kval;              
					}                              
					firstCol = 1;
				}                                      
			}                                              
		}                                                      

		if(debit_kredit==1){
			if(debit && kredit){
				alert(\'Sila masukkan salah satu ruang amaun sahaja!\');
				count++;
			} else if(!debit && !kredit){
				alert(\'Sila masukkan salah satu ruang amaun!\');
				count++;
			} else {
				single_d = parseFloat(e.debit2.value);
				single_k = parseFloat(e.kredit2.value);
				if(single_d>0){ 
					dbt = dbt + single_d;
				}
				if(single_k>0){ 
					krt = krt + single_k;
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

		//if((dbt==krt && (!dbt==0 && !krt==0)) || act == \'Simpan\'){
		//	e.submit();
		//}else{
		//	alert(\'Sila pastikan jumlah debit dan kredit sepadan.\');
		//}

	}

	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	}
		
	function ITRActionButtonClickStatus(v) {
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
				pk = e.elements[c].value;
				strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          window.location.href ="memberStatus.php?pk=" + strStatus;
			  }
	        }
	      }
	    }

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}


	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

	//pickup akaun name to keterangan
	function updateDescAkaun() {
		var selectElement 	= document.getElementById("deductSelect");
		var selectedOption 	= selectElement.options[selectElement.selectedIndex];
		
		// Extract the text content of the selected option after the two non-breaking spaces
		var optionText = selectedOption.textContent || selectedOption.innerText;
		// var deductName = optionText.split("\u00a0\u00a0")[1];  // Split by two non-breaking spaces

		// Get the textarea by its name and update its value
		var descAkaunField 		= document.getElementsByName("desc_akaun2")[0];  // Access the first element with name="desc_akaun2"
		descAkaunField.value 	= optionText ? optionText.trim() : "";
	}

// Pickup default amount of kod objek akaun from table general
function updateAmount() {
    const deductID = document.getElementById(\'deductSelect\').value;
    if (deductID) {
        $.ajax({
            url: \'fetchAmount.php\',
            type: \'POST\',
            data: { deductID: deductID },
            dataType: \'json\',
            success: function (response) {
                if (response && response.j_Amount !== undefined) {
                    // Update both kredit2 and debit2
                    document.getElementsByName(\'kredit2\')[0].value = response.j_Amount;
                    document.getElementsByName(\'debit2\')[0].value = response.j_Amount; // Assuming the same value
                } else {
                    // Set fallback values to 0
                    document.getElementsByName(\'kredit2\')[0].value = 0;
                    document.getElementsByName(\'debit2\')[0].value = 0;
                }
            },
            error: function () {
                alert(\'Failed to fetch amount\');
                // Set fallback values to empty
                document.getElementsByName(\'kredit2\')[0].value = \'\';
                document.getElementsByName(\'debit2\')[0].value = \'\';
            }
        });
    } else {
        // Clear both fields if no deductID is selected
        document.getElementsByName(\'kredit2\')[0].value = \'\';
        document.getElementsByName(\'debit2\')[0].value = \'\';
    }
}

</script>';
include("footer.php");