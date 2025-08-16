<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: resit.php
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

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=resitList&mn=908">SENARAI</a><b>' . '&nbsp;>&nbsp;RESIT ANGGOTA</b>';

$display = 0;
if ($no_resit && $action == "view") {
    $sql             = "SELECT a.*,b.memberID,b.address, b.city, b.postcode, b.stateID, b.departmentID, c.name 
						FROM  resit a, userdetails b, users c 
						WHERE b.userID = c.userID 
						and a.bayar_nama = b.memberID 
						and a.no_resit = '" . $no_resit . "'";
    $rs             = $conn->Execute($sql);
    $no_resit         = $rs->fields('no_resit');
    $tarikh_resit     = toDate("d/m/y", $rs->fields('tarikh_resit'));

    $no_bond         = $rs->fields('bayar_kod');
    $bayar_nama     = $rs->fields('name');
    $no_anggota     = $rs->fields('memberID');
    //---
    $deptID            =  $rs->fields('departmentID');
    $departmentAdd    =  dlookup("general", "b_Address", "ID=" . tosql($deptID, "Number"));
    $alamat         = strtoupper(strip_tags($departmentAdd));
    //-----------------
    $cara_bayar     = $rs->fields('cara_bayar');
    $kod_siri         = $rs->fields('kod_siri');
    $tarikh         = toDate("d/m/y", $rs->fields('tarikh'));
    $akaun_bank     = $rs->fields('akaun_bank');
    $kerani         = $rs->fields('kerani');

    $kod_bank         = $rs->fields('kod_bank');
    $bankparent     = dlookup("generalacc", "parentID", "ID=" . $kod_bank);

    $catatan         = $rs->fields('catatan');
    $masterAmt        = $rs->fields('pymtAmt');
    $batchNo         = $rs->fields('batchNo');

    $sql2             = "SELECT * FROM transaction
						WHERE docNo = '" . $no_resit . "'
						ORDER BY ID";
    $rsDetail         = $conn->Execute($sql2);
} elseif ($action == "new") {
    $getNo             = "SELECT MAX(CAST(right(no_resit,6) AS SIGNED INTEGER)) AS nombor FROM resit";
    $rsNo             = $conn->Execute($getNo);
    $tarikh_resit     = date("d/m/Y");
    $tarikh         = date("d/m/Y");
    if ($rsNo) {
        $nombor     = intval($rsNo->fields('nombor')) + 1;
        $nombor     = sprintf("%06s",  $nombor);
        $no_resit     = 'RT' . $nombor;
    } else {
        $no_resit     = 'R000001';
    }
}

if ($perkara2) {
    $updatedBy         = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $createdDate     = date("Y-m-d H:i:s");
    $tarikh_resit_db = saveDateDb($tarikh_resit);

    $deductID    = &$perkara2;
    $Master     = dlookup("general", "c_master", "ID = '" . $deductID . "'");
    $Master2    = dlookup("generalacc", "parentID", "ID = '" . $Master . "'");
    $coreID     = dlookup("generalacc", "coreID", "ID = '" . $Master . "'");
    $addminus     = 1;
    $cajAmt     = 0.0;
    $userID     = dlookup("userdetails", "userID", "memberID = '" . $no_anggota . "'");
    if ($pymtAmt == '') $pymtAmt = '0.0';

    // First insert into the transaction table
    $sSQL    = "INSERT INTO transaction (" .
        "docNo," .
        "userID," .
        "yrmth," .
        "deductID," .
        "addminus," .
        "pymtID," .
        "pymtRefer," .
        "pymtAmt," .
        "cajAmt," .
        "createdDate," .
        "createdBy," .
        "updatedDate," .
        "updatedBy)" .
        " VALUES (" .
        "'" . $no_resit . "', " .
        "'" . $userID . "', " .
        "'" . $yymm . "', " .
        "'" . $deductID . "', " .
        "'" . $addminus . "', " .
        "'" . 66 . "', " .
        "'" . $ruj2 . "', " .
        "'" . $kredit2 . "', " .
        "'" . $cajAmt . "', " .
        "'" . $tarikh_resit_db . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "')";

    if ($display) {
        print $sSQL . '<br />';
    } else {
        // Execute the transaction insert
        $rs = &$conn->Execute($sSQL);

        // Get the last inserted ID from the transaction table
        $last_id = $conn->Insert_ID();

        // Now insert into the transactionacc table, using $last_id for IDtrans
        $sSQL1    = "INSERT INTO transactionacc (" .
            "docNo," .
            "docID," .
            "IDtrans," .
            "tarikh_doc," .
            "batchNo," .
            "userID," .
            "yrmth," .
            "JdeductID," .
            "deductID," .
            "MdeductID," .
            "addminus," .
            "pymtID," .
            "pymtRefer," .
            "pymtAmt," .
            "coreID," .
            "desc_akaun," .
            "createdDate," .
            "createdBy," .
            "updatedDate," .
            "updatedBy)" .
            " VALUES (" .
            "'" . $no_resit . "', " .
            "'" . 10 . "', " .
            "'" . $last_id . "', " .  //last inserted ID from transaction table/
            "'" . $tarikh_resit_db . "', " .
            "'" . $batchNo . "', " .
            "'" . $userID . "', " .
            "'" . $yymm . "', " .
            "'" . $deductID . "', " .
            "'" . $Master . "', " .
            "'" . $Master2 . "', " .
            "'" . $addminus . "', " .
            "'" . 66 . "', " .
            "'" . $ruj2 . "', " .
            "'" . $kredit2 . "', " .
            "'" . $coreID . "', " .
            "'" . $desc_akaun2 . "', " .
            "'" . $createdDate . "', " .
            "'" . $updatedBy . "', " .
            "'" . $updatedDate . "', " .
            "'" . $updatedBy . "')";

        // Execute the transactionacc insert
        $rs = &$conn->Execute($sSQL1);

        $strActivity = $_POST['Submit'] . 'Kemaskini Resit Keanggotaan - ' . $no_resit;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

        // Redirect to the resit page
        print '<script>
		window.location = "?vw=resit&mn=908&action=view&no_resit=' . $no_resit . '";
		</script>';
    }
}

if ($action == "Hapus") {
    if (count($pk) > 0) {
        $sWhere = "";
        foreach ($pk as $val) {

            $sSQL     = '';
            $sWhere = "ID='" . $val . "'";
            $docNo = dlookup("transaction", "docNo", $sWhere);
            $sSQL     = "DELETE FROM transaction WHERE " . $sWhere;

            $sSQL1     = '';
            $sWhere = "IDtrans='" . $val . "'";
            $sSQL1     = "DELETE FROM transactionacc WHERE " . $sWhere;

            if ($display) print $sSQL . '<br />';
            else
                $rs = &$conn->Execute($sSQL);
            $rs = &$conn->Execute($sSQL1);

            $strActivity = $_POST['Submit'] . 'Hapus Kandungan Resit Keanggotaan - ' . $docNo;
            activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
        }
    }
    if (!$display) {
        print '<script>
	window.location = "?vw=resit&mn=908&action=view&no_resit=' . $no_resit . '";
	</script>';
    }
} elseif ($action == "Kemaskini" || $perkara) {
    $updatedBy         = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $tarikh_resit     = saveDateDb($tarikh_resit);
    $tarikh         = saveDateDb($tarikh);
    $sSQL     = "";
    $sWhere = "";
    $sWhere = "no_resit='" . $no_resit . "'";
    $sWhere = " WHERE (" . $sWhere . ")";
    $sSQL    = "UPDATE resit SET " .
        "alamat='" . $alamat . "'," .
        "cara_bayar='" . $cara_bayar . "'," .
        "kod_siri='" . $kod_siri . "'," .
        "tarikh='" . $tarikh . "'," .
        "tarikh_resit='" . $tarikh_resit . "'," .
        "batchNo='" . $batchNo . "'," .
        "akaun_bank='" . $akaun_bank . "'," .
        "kod_bank='" . $kod_bank . "'," .
        "kerani='" . $kerani . "'," .
        "catatan='" . $catatan . "'," .
        "pymtAmt='" . $masterAmt . "'," .
        "StatusID_Pymt='" . 0 . "'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";
    $sSQL = $sSQL . $sWhere;

    $sSQL1     = "";
    $sWhere1 = "";
    $sWhere1 = "docNo='" . $no_resit . "' AND addminus='" . 0 . "'";
    $sWhere1 = " WHERE (" . $sWhere1 . ")";
    $sSQL1     = "UPDATE transactionacc SET 
					" . "deductID='" . $kod_bank . "',
					" . "MdeductID='" . $bankparent . "',
					" . "yrmth='" . $yymm . "',
					" . "desc_akaun='" . $catatan . "',
					" . "tarikh_doc='" . $tarikh_resit . "',
					" . "updatedDate='" . $updatedDate . "',
					" . "updatedBy='" . $updatedBy . "',
					" . "pymtAmt='" . $masterAmt . "'";

    $sSQL1 = $sSQL1 . $sWhere1;

    $sSQL3   = "";
    $sWhere3 = "";
    $sWhere3 = "docNo='" . $no_resit . "'";
    $sWhere3 = " WHERE (" . $sWhere3 . ")";
    $sSQL3     = "UPDATE transactionacc SET 
					" . "batchNo='" . $batchNo . "',
					" . "cara_bayar='" . $cara_bayar . "',
					" . "yrmth='" . $yymm . "',
					" . "updatedDate='" . $updatedDate . "',
					" . "updatedBy='" . $updatedBy . "',
					" . "tarikh_doc='" . $tarikh_resit . "'";

    $sSQL3 = $sSQL3 . $sWhere3;

    if ($display) print $sSQL . '<br />';
    else
        $rs = &$conn->Execute($sSQL);
    $rs = &$conn->Execute($sSQL1);
    $rs = &$conn->Execute($sSQL3);

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (count($perkara) > 0) {
        foreach ($perkara as $id => $value) {
            $deductID     = $value;
            $Master     = dlookup("general", "c_master", "ID = '" . $deductID . "'");
            $Master2     = dlookup("generalacc", "parentID", "ID = '" . $Master . "'");
            $coreID     = dlookup("generalacc", "coreID", "ID = '" . $Master . "'");
            $pymtAmt     = $kredit[$id];
            $addminus     = 1;
            $no_ruj     = $ruj[$id];
            $sSQL   = "";
            $sWhere = "";
            $sWhere = "ID='" . $id . "'";
            $sSQL    = "UPDATE transaction SET " .
                "deductID= '" . $deductID . "'" .
                ",addminus= '" . $addminus . "'" .
                ",pymtAmt= '" . $pymtAmt . "'" .
                ",yrmth= '" . $yymm . "'" .
                ",createdDate= '" . $tarikh_resit . "'" .
                ",updatedDate= '" . $updatedDate . "'" .
                ",updatedBy= '" .  $updatedBy . "'";
            $sSQL .= " where " . $sWhere;

            $sSQL1      = "";
            $sWhere1    = "";
            $sWhere1    = "IDtrans='" . $id . "'";
            $sSQL1        = "UPDATE transactionacc SET " .
                "JdeductID= '" . $deductID . "'" .
                ",deductID= '" . $Master . "'" .
                ",MdeductID= '" . $Master2 . "'" .
                ",coreID= '" . $coreID . "'" .
                ",addminus= '" . $addminus . "'" .
                ",pymtAmt= '" . $pymtAmt . "'" .
                ",yrmth= '" . $yymm . "'" .
                ",updatedDate= '" . $updatedDate . "'" .
                ",updatedBy= '" .  $updatedBy . "'";
            $sSQL1 .= " where " . $sWhere1;

            if ($display) print $sSQL . '<br />';
            else
                $rs = &$conn->Execute($sSQL);
            $rs = &$conn->Execute($sSQL1);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (count($kod_objek) > 0) {
        foreach ($kod_objek as $id => $value) {

            $MdeductID     = $value;
            $pymtAmt     = $kredit[$id];
            $addminus     = 1;
            $no_ruj     = $ruj[$id];
            $sSQL     = "";
            $sWhere = "";
            $sWhere = "ID='" . $id . "'";
            $sSQL    = "UPDATE transaction SET " .
                "MdeductID= '" . $MdeductID . "'" .
                ",addminus= '" . $addminus . "'" .
                ",pymtAmt= '" . $pymtAmt . "'" .
                ",yrmth= '" . $yymm . "'" .
                ",createdDate= '" . $tarikh_resit . "'" .
                ",updatedDate= '" . $updatedDate . "'" .
                ",updatedBy= '" .  $updatedBy . "'";
            $sSQL .= " where " . $sWhere;

            if ($display) print $sSQL . '<br />';
            else
                $rs = &$conn->Execute($sSQL);
        }
    }
    ///////////////////////DESC AKAUN//////////////////////////////////////
    if (count($desc_akaun) > 0) {
        foreach ($desc_akaun as $id => $value) {
            $desc_akaun = $value;
            $pymtAmt     = $kredit[$id];
            $addminus     = 1;
            $sSQL     = "";
            $sWhere = "";
            $sWhere = "IDtrans='" . $id . "'";
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
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if (!$display) {
        print '<script>
	window.location = "?vw=resit&mn=908&action=view&no_resit=' . $no_resit . '";
	</script>';
    }
} elseif ($action == "Simpan" || $simpan) {
    $updatedBy           = get_session("Cookie_userName");
    $updatedDate      = date("Y-m-d H:i:s");
    $tarikh_resit     = saveDateDb($tarikh_resit);
    $tarikh         = saveDateDb($tarikh);

    // help prevent double entry by multiple users ----begin
    $getMax2         = "SELECT MAX(CAST(right(no_resit,6) AS SIGNED INTEGER)) AS no2 FROM resit";
    $rsMax2         = $conn->Execute($getMax2);
    $max2            = sprintf("%06s", $rsMax2->fields('no2'));

    if ($rsMax2) {
        $max2         = intval($rsMax2->fields('no2')) + 1;
        $max2         = sprintf("%06s",  $max2);
        $no_resit2     = 'RT' . $max2;
    } else {
        $no_resit2     = 'R000001';
    }
    //-----end

    $sSQL     = "";
    $sSQL    = "INSERT INTO resit (" .
        "no_resit, " .
        "tarikh_resit, " .
        "batchNo, " .
        "bayar_kod, " .
        "bayar_nama, " .
        "alamat, " .
        "cara_bayar, " .
        "kod_siri, " .
        "tarikh, " .
        "akaun_bank, " .
        "kod_bank, " .
        "kerani, " .
        "pymtAmt, " .
        "StatusID_Pymt, " .
        "catatan, " .
        "createdDate, " .
        "createdBy, " .
        "updatedDate, " .
        "updatedBy) " .
        " VALUES (" .
        "'" . $no_resit2 . "', " .
        "'" . $tarikh_resit . "', " .
        "'" . $batchNo . "', " .
        "'" . $no_bond . "', " .
        "'" . $no_anggota . "', " .
        "'" . $alamat . "', " .
        "'" . $cara_bayar . "', " .
        "'" . $kod_siri . "', " .
        "'" . $tarikh . "', " .
        "'" . $akaun_bank . "', " .
        "'" . $kod_bank . "', " .
        "'" . $kerani . "', " .
        "'" . $masterAmt . "', " .
        "'" . 0 . "', " .
        "'" . $catatan . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy  . "') ";

    $sSQL1 = "";
    $sSQL1    = "INSERT INTO transactionacc (" .

        "docNo," .
        "docID," .
        "tarikh_doc," .
        "batchNo," .
        "userID," .
        "yrmth," .
        "deductID," .
        "addminus," .
        "pymtAmt," .
        "cara_bayar," .
        "desc_akaun," .
        "updatedBy," .
        "updatedDate	," .
        "createdBy," .
        "createdDate) " .

        " VALUES (" .
        "'" . $no_resit2 . "', " .
        "'" . 10 . "', " .
        "'" . $tarikh_resit . "', " .
        "'" . $batchNo . "', " .
        "'" . $no_anggota . "', " .
        "'" . $yymm . "', " .
        "'" . $kod_bank . "', " .
        "'" . 0 . "', " .
        "'" . $masterAmt . "', " .
        "'" . $cara_bayar . "', " .
        "'" . $catatan . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "')";

    if ($display) print $sSQL . '<br />';
    else
        $rs = &$conn->Execute($sSQL);
    $rs = &$conn->Execute($sSQL1);

    $getMax = "SELECT MAX(CAST(right(no_resit,6) AS SIGNED INTEGER )) as no FROM resit";
    $rsMax = $conn->Execute($getMax);
    $max = sprintf("%06s", $rsMax->fields('no'));
    if (!$display) {
        print '<script>
	window.location = "?vw=resit&mn=908&action=view&add=1&no_resit=RT' . $max . '";
	</script>';
    }
}

$strTemp .=
    '<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
    . '<div style="width: 100%; text-align:left">'
    . '<div>&nbsp;</div><div class="table-responsive">'
    . '<form name="MyForm" action="?vw=resit&mn=908" method="post">'
    . '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print '<input name="yymm" id="yymm" type="hidden" value="' . $yymm . '">';

print '
<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top">Nombor Resit</td>
				<td valign="top"></td><td><input class="form-control-sm" name="no_resit" value="' . $no_resit . '" type="text" size="20" maxlength="50" readonly/></td>
			</tr>

			<tr>
				<td>* Batch</td>
				<td valign="top"></td>
				<td>' . selectbatch($batchNo, 'batchNo') . '</td>
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
				<td valign="top" align="right">* Tarikh</td><td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh_resit">
				<input type="text" name="tarikh_resit" id="tarikh_resit_input" class="form-control-sm" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh_resit"
					data-date-autoclose="true" value="' . $tarikh_resit . '">
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
<tr><td colspan="3"><hr class="mt-3"/></td></tr>
<tr><td colspan="3">Diterima daripada </td></tr>
<tr>
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>* Nombor Anggota</td><td valign="top"></td>
				<td><input name="no_anggota" value="' . $no_anggota . '" type="text" size="20" maxlength="50"  class="form-control-sm" readonly/>&nbsp;';
if ($action == "new" && $jenis == 1) print '<input type="button" class="btn btn-sm btn-info waves-light waves-effect" value="Pilih" onclick="window.open(\'selToMember.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
else if ($action == "new" && $jenis == 2) print '<input type="button" class="btn btn-sm btn-info waves-light waves-effect" value="Pilih" onclick="window.open(\'selLoanS.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
print '&nbsp;<input name="loan_no" type="hidden" value="">&nbsp;</td>
			</tr>
			<tr><td valign="top">Nama</td><td valign="top"></td><td><input name="nama_anggota"  value="' . $bayar_nama . '" type="text" size="50" maxlength="50" class="form-control-sm" readonly/>
		    </td></tr>
			<tr><td valign="top">Alamat</td><td valign="top"></td><td><textarea name="alamat" cols="50" rows="4" class="form-control-sm" readonly>' . $alamat . '</textarea></td></tr>
			<tr>
			  <td valign="top">Nombor Bond / Amaun (RM)</td>
			  <td valign="top"></td>
			  <td><input name="no_bond"  value="' . $no_bond . '" size="10" maxlength="50"  class="form-control-sm" readonly />
		      <input name="amt"  value="' . $amt . '" size="10" maxlength="50"  class="form-control-sm" readonly="readonly" /></td>
		  </tr>
			<tr>
			  <td valign="top">Jenis Pembiayaan</td>
			  <td valign="top"></td>
			  <td><input name="name_type"  value="' . $nametype . '" size="40" maxlength="50"  class="form-control-sm" readonly /></td>
		  </tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Cara Bayaran</td><td valign="top"></td>
				<td><input name="cara_bayar" value="' . $cara_bayar . '" class="form-control-sm" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Kod & Nombor Siri</td><td valign="top"></td>
				<td><input name="kod_siri" value="' . $kod_siri . '" class="form-control-sm" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Tarikh Bayaran</td><td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh">
				<input type="text" name="tarikh" class="form-control-sm" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh"
					data-date-autoclose="true" value="' . $tarikh . '">
				<div class="input-group-append">
					<span class="input-group-text">
						<i class="mdi mdi-calendar"></i></span>
				</div>
				</div>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right">Master Amaun (RM)</td><td valign="top"></td>
				<td><input id="master" value="' . $masterAmt . '" type="text" class="form-control-sm" size="20" maxlength="10"/ readonly></td>
			</tr>
			
			
		<!-- <tr>
<td align="right"><input type="button" class="btn btn-sm btn-secondary" name="GetPicture" value="Muat Naik Resit"  onclick= "Javascript:(window.location.href=\'?vw=uploadwinresit&mn=908&no_resit=' . $no_resit . '\')"></td><td valign="top" align="right"></td><td><input name="pic" value="' . $pic . '" type="text" size="20" class="form-control-sm" maxlength="50" class="data" readonly /></td>
			</tr> -->
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
if ($action == "view" && !is_int(dlookup("transaction", "ID", "docNo='" . $no_resit . "'"))) {
    print '
<tr>
	<!--td align= "left"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input">Tanda semua</td-->
	<td align= "right" colspan="3">';
    if (!$add) print '

		<!-- Implementing the visual effect on button Tambah for akaun. START -->
		<div class="request-loader-container" id="loaderContainer">
			<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=resit&mn=908&action=' . $action . '&no_resit=' . $no_resit . '&add=1\';">
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
				<td nowrap="nowrap"><b>* Perkara</b></td>
				<td nowrap="nowrap"><b>Kod Master Akaun</b></td>
				<td nowrap="nowrap"><b>Kod Akaun</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="right"><b>* Jumlah (RM)</b></td>
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
            $ruj         = $rsDetail->fields('pymtRefer');
            $perkara     = $rsDetail->fields('deductID');

            $kod_objek     = dlookup("general", "c_master", "ID=" . $perkara);
            $namagl     = dlookup("generalacc", "name", "ID=" . $kod_objek);

            $kod_akaun     = dlookup("general", "c_Panel", "ID=" . $perkara);
            $keterangan2 = dlookup("general", "name", "ID=" . $kod_akaun);
            $kredit     = $rsDetail->fields('pymtAmt');
            $desc_akaun = dlookup("transactionacc", "desc_akaun", "IDtrans=" . $id);

            print       '
			<tr>
				<td class="Data">&nbsp;' . ++$i . '.</td>				
				<td class="Data" nowrap="nowrap">' . strSelect2($id, $perkara) . '&nbsp;
				</td>

				<td class="Data" nowrap="nowrap">
				<input class="form-control-sm" name="namagl[' . $id . ']" type="text" size="30" maxlength="30" value="' . $namagl . '" readonly/>
				<input class="form-control-sm" name="kod_objek[' . $id . ']" type="hidden" size="10" maxlength="10" value="' . $kod_objek . '"/>
				</td>

				<td class="Data" nowrap="nowrap">
					<input  class="form-control-sm" name="kod_akaun[' . $id . ']" type="text" size="8" maxlength="10" value="' . $kod_akaun . '" readonly/>&nbsp;
				</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" class="form-control-sm" rows="4" cols="40" maxlength="500">' . $desc_akaun . '</textarea>&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="ruj[' . $id . ']" type="hidden" value="' . $no_anggota . '"/>
					<input name="kredit[' . $id . ']" type="text" size="10" maxlength="10" value="' . $kredit . '" class="form-control-sm" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '">&nbsp;</td>
			</tr>';
            $totalKt += $kredit;
            $kredit     = '';
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
		disableElementsByName("no_resit");
        disableElementsByName("batchNo");
		disableElementsByName("tarikh_resit");
		disableElementsByName("tarikh");
        disableElementsByName("kod_bank");
        disableElementsByName("no_anggota");
		disableElementsByName("nama_anggota");
		disableElementsByName("alamat");
		disableElementsByName("no_bond");
		disableElementsByName("amt");
		disableElementsByName("name_type");
		disableElementsByName("cara_bayar");
		disableElementsByName("kod_siri");
		disableElementsByName("kerani");
		disableElementsByName("catatan");
		disableElementById("bottomButton");
		});
		</script>
		';
    }
    //------------- END
}


$strDeductIDList     = deductList(1);
$strDeductCodeList     = deductList(2);
$strDeductNameList     = deductList(3);
$name = 'perkara2';

$strSelect = '<select class="form-select-sm" name="' . $name . '" id="deductSelect" onchange="updateDescAkaun(); updateAmount();">
				<option value="">- Kod -';
for ($i = 0; $i < count($strDeductIDList); $i++) {
    $strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
    if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
    $strSelect .=  '>' . $strDeductCodeList[$i] . '&nbsp;-&nbsp;' . $strDeductNameList[$i] . '';
}
$strSelect .= '</select>';

if ($add) {
    print       '<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data">' . $strSelect . '
				<input name="kod_objek2" type="hidden" size="10" maxlength="10" value="' . $kod_objek2 . '" class="form-control-sm"/>
				</td>

				<td class="Data">
				<input name="namagl2" type="text" size="30" maxlength="30" value="' . $namagl2 . '" class="form-control-sm" readonly/>
				<input name="kod_objek2" type="hidden" size="10" maxlength="10" value="' . $kod_objek2 . '" class="form-control-sm"/>
				</td>
				
				<td class="Data" nowrap="nowrap">
					<input name="kod_akaun2" type="text" size="8" maxlength="10" value="' . $kod_akaun2 . '"  class="form-control-sm" readonly/>&nbsp;
				</td>

				<td class="Data" align="left">
					<textarea name="desc_akaun2" rows="4" cols="40" maxlength="500" class="form-control-sm" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="ruj2" type="hidden" value="' . $no_bond . '"/>
					<input name="kredit2" type="text" size="10" maxlength="10" value="' . $kredit2 . '" class="form-control-sm" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}

if ($totalKt <> 0) {
    $clsRM->setValue($totalKt);
    $strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}
print         '<tr class="table-secondary">
				<td class="Data" colspan="5" align="right"><b>Jumlah (RM)</b></td>
				<td class="Data" id="totalJumlah" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr>
			<td colspan="3" nowrap="nowrap">Jumlah Dalam Perkataan<br />
			<input name="" size="100" maxlength="100" value="' . $strTotal . '" class="form-control-sm" readonly>
			<input class="Data" type="hidden" name="masterAmt" value="' . $totalKt . '">
			<input class="Data" type="hidden" name="bankparent" value="' . $bankparent . '">
					</td>
			</tr>
			<tr>
			<td nowrap="nowrap">Kerani Kewangan</td><td valign="top"></td><td>' . selectAdmin($kerani, 'kerani') . '</td>
			</tr>
			<tr>
			<td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td><td valign="top"><textarea name="catatan" class="form-control-sm" cols="50" rows="4">' . $catatan . '</textarea></td>
			</tr>
		</table>
	</td>
</tr>';

if ($no_resit) {
    $straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
    print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'resitPaymentPrint.php?ID=' . $no_resit . '\')">&nbsp;
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
</div></div>';

print $strTemp;
print '
<script language="JavaScript">

<!-- Implementing the javascript visual effect for buttons and comparing amount for Jumlah vs Master. START -->

document.addEventListener("DOMContentLoaded", function() {
	function compare() {
		const masterValue       = document.getElementById("master").value;
		const master            = parseFloat(masterValue);
		const jumlah            = parseFloat(document.getElementById("totalJumlah").innerText.replace(/,/g, ""));
    	var noRecords           = ' . json_encode($noRecords) . ';
		const requestLoader     = document.getElementById("requestLoader");
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

<!-- Automatically and dynamically declare value of yymm for tarikh_resit. START -->

// Make sure the DOM is fully loaded before running the script
window.onload = function() {
	// Function to update the YYMM field
	function updateYYMM() {
	    const tarikhResit = document.getElementById("tarikh_resit_input").value;
	    
	    // Check if a date is selected and valid
	    if (tarikhResit && tarikhResit.includes("/")) {
	        const dateParts = tarikhResit.split("/"); // Assuming the format is dd/mm/yyyy
	        
	        if (dateParts.length === 3) {
	            const day = dateParts[0];
	            const month = dateParts[1];
	            const year = dateParts[2];

	            // Combine year and month in the format YYYYMM
	            const yymm = year + month;

	            // Update the yymm input field
	            document.getElementById("yymm").value = yymm;
	        }
	    }
	}

	    const dateInput = document.getElementById("tarikh_resit_input");

		// Call updateYYMM on page load to set the initial value
		updateYYMM();

	    // Event listener for the datepicker selection
	    $(dateInput).datepicker().on("changeDate", function() {
	        updateYYMM();
	    });

	    // Event listener for manual input changes in the text field
	    dateInput.addEventListener("input", function() {
	        updateYYMM();
	    });

	    // Optionally, you can also handle the `blur` event in case user manually inputs and clicks out
	    dateInput.addEventListener("blur", function() {
	        updateYYMM();
	    });
	};

<!-- Automatically and dynamically declare value of yymm for tarikh_resit. END -->

	function print_(url) {
		window.open(url,"pop","top=100, left=100, width=600, height=400, scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");					
	}

	function CheckField(act) {
	    e = document.MyForm;
		count = 0;	
		for(c=0; c<e.elements.length; c++) {
		  if(e.elements[c].name=="nama_anggota" && e.elements[c].value==\'\') {
			alert(\'Pilih Anggota!\');
            count++;
		  }

		  if(act == \'Kemaskini\') {
		  if(e.elements[c].name=="kredit2" && e.elements[c].value==\'\') {
			alert(\'Ruang amaun perlu diisi!\');
            count++;
		  }

		  if(e.elements[c].name=="perkara2" && e.elements[c].value==\'\') {
			alert(\'Pilih Perkara!\');
            count++;
		  }	  
		  }

		  if(act == \'Simpan\') {
  
		  if(e.elements[c].name=="batchNo" && e.elements[c].value==\'\') 
		  	{
			alert(\'Pilih Batch!\');
            count++;
		 	}

		  if(e.elements[c].name=="kod_bank" && e.elements[c].value==\'\') {
			alert(\'Pilih Bank!\');
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

	//pickup akaun name to keterangan
	function updateDescAkaun() {
		var selectElement 	= document.getElementById("deductSelect");
		var selectedOption 	= selectElement.options[selectElement.selectedIndex];
		
		// Extract the text after the first dash and trim any surrounding spaces
		var optionText = selectedOption.textContent || selectedOption.innerText;
		var deductName = optionText.split("-")[1];  // Split by the first dash

		// Get the textarea by its name and update its value
		var descAkaunField 		= document.getElementsByName("desc_akaun2")[0];  // Access the first element with name="desc_akaun2"
		descAkaunField.value 	= deductName ? deductName.trim() : "";
		}

	//pickup default amount of kod objek akaun from table general
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
                        document.getElementsByName(\'kredit2\')[0].value = response.j_Amount;
                    } else {
                        document.getElementsByName(\'kredit2\')[0].value = 0;
                    }
                },
                error: function () {
                    alert(\'Failed to fetch amount\');
                }
            });
        } else {
            document.getElementsByName(\'kredit2\')[0].value = \'\';
        }
	}

</script>
';
include("footer.php");