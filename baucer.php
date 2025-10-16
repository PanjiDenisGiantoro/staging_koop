<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: baucer.php
 *			Date 		: 19/10/2006
 *			Keywords 	: disable, noRecords, effect, noBondNoTran, duplicate (to prevent user fault)
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=vouchersList&mn=908">SENARAI</a><b>' . '&nbsp;>&nbsp;BAUCER BAYARAN ANGGOTA</b>';

$display = 0;
if ($no_baucer && $action == "view") {
    $sql             = "SELECT a.*,b.memberID,b.address, b.city, b.postcode, b.stateID, b.departmentID, c.name 
						FROM  vauchers a, userdetails b, users c 
						WHERE b.userID = c.userID 
						and a.no_anggota = b.memberID 
						and no_baucer = '" . $no_baucer . "'";
    $rs             = $conn->Execute($sql);
    $no_baucer      = $rs->fields('no_baucer');
    $tarikh_baucer  = toDate("d/m/y", $rs->fields('tarikh_baucer'));

    $jenis          = $rs->fields('jenis');
    $no_bond        = $rs->fields('no_bond');
    $no_anggota     = $rs->fields('no_anggota');
    $disediakan     = $rs->fields('disediakan');

    $kod_bank       = $rs->fields('kod_bank');
    $bankparent     = dlookup("generalacc", "parentID", "ID=" . $kod_bank);

    $disahkan       = $rs->fields('disahkan');
    $keterangan     = $rs->fields('keterangan');
    $kod_caw        = $rs->fields('kod_caw');
    $no_siri        = $rs->fields('no_siri');
    $tarikh_bank    = substr($tarikh_bank, 8, 2) . "/" . substr($tarikh_bank, 5, 2) . "/" . substr($tarikh_bank, 0, 4);
    $nama           = $rs->fields('name');
    $deptID         = $rs->fields('departmentID');
    $departmentAdd  = dlookup("general", "b_Address", "ID=" . tosql($deptID, "Number"));
    $alamat         = strtoupper(strip_tags($departmentAdd));

    $masterAmt      = $rs->fields('pymtAmt');
    $batchNo        = $rs->fields('batchNo');
    //-----------------
    $sql2           = "SELECT * FROM transaction 
						WHERE docNo = '" . $no_baucer . "' 
						ORDER BY ID";
    $rsDetail       = $conn->Execute($sql2);
    if ($rsDetail->RowCount() < 1) $noTran = true;
} elseif ($action == "new" && $carumanID) {
    $getNo           = "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS nombor FROM vauchers";
    $rsNo            = $conn->Execute($getNo);
    $tarikh_baucer   = date("d/m/Y");
    $tarikh_bank     = date("d/m/Y");
    if ($rsNo) {
        $nombor     = intval($rsNo->fields('nombor')) + 1;
        $nombor     = sprintf("%06s",  $nombor);
        $no_baucer  = 'PVA' . $nombor;
    } else {
        $no_baucer  = 'PVA000001';
    }

    $nama = dlookup("users", "name", "userID=" . tosql($no_anggota, "Text"));

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
	disableElementsByName("no_anggota");
	disableElementsByName("nama_anggota");
	disableElementsByName("alamat");
	disableElementsByName("no_bond");
	disableElementsByName("amt");
	disableElementsByName("name_type");
	});
	</script>
	';
} elseif ($action == "new") {
	$getNo 			= "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS nombor FROM vauchers";
	$rsNo 			= $conn->Execute($getNo);
	$tarikh_baucer 	= date("d/m/Y");
	$tarikh_bank 	= date("d/m/Y");
	if ($rsNo) {
		$nombor 	= intval($rsNo->fields('nombor')) + 1;
		$nombor 	= sprintf("%06s",  $nombor);
		$no_baucer 	= 'PVA' . $nombor;
	} else {
		$no_baucer 	= 'PVA000001';
	}
}

if ($perkara2) {
	$updatedBy 			= get_session("Cookie_userName");
	$updatedDate 		= date("Y-m-d H:i:s");
	$createdDate 	    = date("Y-m-d H:i:s");
	$tarikh_baucer_db 	= saveDateDb($tarikh_baucer);

	if ($jenis == 2) {
		if ($no_bond) {
			$pk 	= dlookup("loandocs", "loanID", "rnoBond = '" . $no_bond . "'");
			$sSQL 	= "";
			$sWhere = "";
			$sWhere = "loanID='" . $pk . "'";
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL	= "UPDATE loandocs SET " .
				" rnoBaucer = '" . $no_baucer . "'" .
				", rcreatedDate = '" . $updatedDate . "'" .
				", rpreparedby = '" . $disediakan . "'" .
				", approvedBy = '" . $disahkan . "'";
			$sSQL 	= $sSQL . $sWhere;
			$rs 	= &$conn->Execute($sSQL);
		}
	}

	if ($jenis == 3) {
		if ($no_bond) {
			$pk 	= dlookup("welfares", "ID", "rnoBond = '" . $no_bond . "'");
			$sSQL 	= "";
			$sWhere = "";
			$sWhere = "ID='" . $pk . "'";
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL	= "UPDATE welfares SET " .
				" rnoBaucer = '" . $no_baucer . "'" .
				", rcreatedDate = '" . $updatedDate . "'";
			$sSQL 	= $sSQL . $sWhere;
			$rs 	= &$conn->Execute($sSQL);
		}
	}

	$userID = dlookup("userdetails", "userID", "memberID = '" . $no_anggota . "'");
	$deductID = &$perkara2;
	$Master = dlookup("general", "c_master", "ID = '" . $deductID . "'");
	$Master2 = dlookup("generalacc", "parentID", "ID = '" . $Master . "'");
	$coreID 	= dlookup("generalacc", "coreID", "ID = '" . $Master . "'");
	if ($debit2) { //debit 2 field for money value
		$pymtAmt 	= $debit2;
		$addminus 	= 0;
		$cajAmt 	= 0.0;
	}

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
        "'" . $no_baucer . "', " . //docNo
        "'" . $userID . "', " . //userID
        "'" . $yymm . "', " . //yrmth
        "'" . $deductID . "', " . //deductID
        "'" . $addminus . "', " . //addminus
        "'" . 66 . "', " . //pymtID
        "'" . $no_bond . "', " . //pymtRefer
        "'" . $pymtAmt . "', " . //pymtAmt
        "'" . $cajAmt . "', " . //cajAmt
        "'" . $tarikh_baucer_db . "', " . //createdDate
        "'" . $updatedBy . "', " . //createdBy
        "'" . $updatedDate . "', " . //updatedDate
        "'" . $updatedBy . "')"; //updatedBy


    if ($display) {
        print $sSQL . '<br />';
    } else {
        // Execute the transaction insert
        $rs = &$conn->Execute($sSQL);

        // Get the last inserted ID from the transaction table
        $last_id = $conn->Insert_ID();

        // Now insert into the transactionacc table, using $last_id for IDtrans  
        if ($jenis == 1 || $jenis == 3) {
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
                "'" . $no_baucer . "', " . //docNo/
                "'" . 12 . "', " . //docID/
                "'" . $last_id . "', " . //last inserted ID from transaction table/
                "'" . $tarikh_baucer_db . "', " . //tarikh_doc/
                "'" . $batchNo . "', " . //batchNo/
                "'" . $userID . "', " . //userID/
                "'" . $yymm . "', " . //yrmth/
                "'" . $deductID . "', " .
                "'" . $Master . "', " .
                "'" . $Master2 . "', " .
                "'" . $addminus . "', " . //addminus/
                "'" . 66 . "', " . //pymtID/
                "'" . $no_bond . "', " . //pymtRefer/
                "'" . $pymtAmt . "', " . //pymtAmt/
                "'" . $coreID . "', " .
                "'" . $desc_akaun2 . "', " .
                "'" . $createdDate . "', " . //createdDate/
                "'" . $updatedBy . "', " . //createdBy/
                "'" . $updatedDate . "', " . //updatedBy/
                "'" . $updatedBy . "')"; //updatedBy/
        }

        // Execute the transactionacc insert
        if ($jenis == 1 || $jenis == 3) {
            $rs = &$conn->Execute($sSQL1);
        }

        $strActivity = $_POST['Submit'] . 'Kemaskini Baucer Keanggotaan - ' . $no_baucer;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

        // Redirect to the baucer page
        print '<script>
		window.location = "?vw=baucer&mn=908&action=view&no_baucer=' . $no_baucer . '";
		</script>';
    }
}

if ($action == "view" && $add == 1 && dlookup("usercaruman", "voucherNo", "ID=" . $no_bond) != null) {
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
	disableElementsByName("perkara2");
	disableElementsByName("namagl2");
	disableElementsByName("kod_akaun2");
	disableElementsByName("debit2");
	});
	</script>
	';
}

if ($action == "Hapus") {
    if (count($pk) > 0) {
        $sWhere = "";
        foreach ($pk as $val) {

            $sSQL   = '';
            $sWhere = "ID='" . $val . "'";
            $docNo  = dlookup("transaction", "docNo", $sWhere);
            $sSQL   = "DELETE FROM transaction WHERE " . $sWhere;

            $sSQL1  = '';
            $sWhere = "IDtrans='" . $val . "'";
            $sSQL1  = "DELETE FROM transactionacc WHERE " . $sWhere;


            if ($display) print $sSQL . '<br />';
            else
                $rs = &$conn->Execute($sSQL);
                $rs = &$conn->Execute($sSQL1);

            $strActivity = $_POST['Submit'] . 'Hapus Kandungan Baucer Keanggotaan - ' . $docNo;
            activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
        }
    }
    if (!$display) {
        print '<script>
	window.location = "?vw=baucer&mn=908&action=view&no_baucer=' . $no_baucer . '";
	</script>';
    }
} elseif ($action == "Kemaskini" || $perkara) {
	$updatedBy 		= get_session("Cookie_userName");
	$updatedDate 	= date("Y-m-d H:i:s");
	$tarikh_baucer 	= saveDateDb($tarikh_baucer);
	$tarikh_bank 	= saveDateDb($tarikh_bank);
	$sSQL 	= "";
    $sWhere = "";
    $sWhere = "no_baucer='" . $no_baucer . "'";
    $sWhere = " WHERE (" . $sWhere . ")";
    $sSQL   = "UPDATE vauchers SET " .
        "tarikh_baucer='" . $tarikh_baucer . "'," .
        "no_anggota='" . $no_anggota . "'," .
        "disediakan='" . $disediakan . "'," .
        "kod_bank='" . $kod_bank . "'," .
        "batchNo='" . $batchNo . "'," .
        "disahkan='" . $disahkan . "'," .
        "keterangan='" . $keterangan . "'," .
        "kod_caw='" . $kod_caw . "'," .
        "no_siri='" . $no_siri . "'," .
        "pymtAmt='" . $masterAmt . "'," .
        "StatusID_Pymt='" . 0 . "'," .
        "tarikh_bank='" . $tarikh_bank . "'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";
    $sSQL = $sSQL . $sWhere;

    $sSQL1   = "";
    $sWhere1 = "";
    $sWhere1 = "docNo='" . $no_baucer . "' AND addminus='" . 1 . "'";
    $sWhere1 = " WHERE (" . $sWhere1 . ")";
    $sSQL1   = "UPDATE transactionacc SET 
					" . "deductID='" . $kod_bank . "',
					" . "MdeductID='" . $bankparent . "',
					" . "yrmth='" . $yymm . "',
                    " . "desc_akaun='" . $keterangan . "',
					" . "tarikh_doc='" . $tarikh_baucer . "',
					" . "updatedDate='" . $updatedDate . "',
					" . "updatedBy='" . $updatedBy . "',
					" . "pymtAmt='" . $masterAmt . "'";

    $sSQL1   = $sSQL1 . $sWhere1;

    $sSQL3   = "";
    $sWhere3 = "";
    $sWhere3 = "docNo='" . $no_baucer . "' AND addminus='" . 0 . "'";
    $sWhere3 = " WHERE (" . $sWhere3 . ")";
    $sSQL3   = "UPDATE transactionacc SET 
					" . "batchNo='" . $batchNo . "',
					" . "yrmth='" . $yymm . "',
					" . "updatedDate='" . $updatedDate . "',
					" . "updatedBy='" . $updatedBy . "',
					" . "tarikh_doc='" . $tarikh_baucer . "'";

    $sSQL3 = $sSQL3 . $sWhere3;

    if ($display) print $sSQL . '<br />';
    else
        $rs = &$conn->Execute($sSQL);
    $rs = &$conn->Execute($sSQL1);
    $rs = &$conn->Execute($sSQL3);
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (count($perkara) > 0) {
        foreach ($perkara as $id => $value) {
			$deductID 	= $value;
			$Master  = dlookup("general", "c_master", "ID = '" . $deductID . "'");
			$Master2 = dlookup("generalacc", "parentID", "ID = '" . $Master . "'");
			$coreID 	= dlookup("generalacc", "coreID", "ID = '" . $Master . "'");
			$pymtAmt 	= $debit[$id];
			$addminus 	= 0;
			$no_ruj = $ruj[$id];
			$sSQL 	= "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transaction SET " .
                "deductID= '" . $deductID . "'" .
                ",addminus= '" . $addminus . "'" .
                ",pymtAmt= '" . $pymtAmt . "'" .
                ",yrmth= '" . $yymm . "'" .
                ",createdDate= '" . $tarikh_baucer . "'" .
                ",updatedDate= '" . $updatedDate . "'" .
                ",updatedBy= '" .  $updatedBy . "'";
            $sSQL .= " where " . $sWhere;

            $sSQL1      = "";
            $sWhere1    = "";
            $sWhere1    = "IDtrans='" . $id . "'";
            $sSQL1      = "UPDATE transactionacc SET " .
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
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (count($kod_master) > 0) {
        foreach ($kod_master as $id => $value) {
            $MdeductID      = $value;
            if ($debit[$id]) {
                $pymtAmt    = $debit[$id];
                $addminus   = 0;
            }
            $no_ruj = $ruj[$id];
            $sSQL   = "";
            $sWhere = "";
            $sWhere = "ID='" . $id . "'";
            $sSQL   = "UPDATE transaction SET " .
                "MdeductID= '" . $MdeductID . "'" .
                ",addminus= '" . $addminus . "'" .
                ",pymtAmt= '" . $pymtAmt . "'" .
                ",yrmth= '" . $yymm . "'" .
                ",createdDate= '" . $tarikh_baucer . "'" .
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
            $pymtAmt    = $debit[$id];
            $addminus   = 0;
            $sSQL       = "";
            $sWhere = "";
            $sWhere = "IDtrans='" . $id . "'";
            $sSQL   = "UPDATE transactionacc SET " .
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
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if (!$display) {
        print '<script>
	window.location = "?vw=baucer&mn=908&action=view&no_baucer=' . $no_baucer . '";
	</script>';
    }
} elseif ($action == "Simpan" || $simpan) {
    $updatedBy       = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $tarikh_baucer   = saveDateDb($tarikh_baucer);
    $tarikh_bank     = saveDateDb($tarikh_bank);
    if ($carumanID) {
        $sSQL   = "";
        $sWhere = "";
        $sWhere = "ID='" . $carumanID . "'";
        $sWhere = " WHERE (" . $sWhere . ")";
        $sSQL   = "UPDATE usercaruman SET " .
            " voucherNo = '" . $no_baucer . "'" .
            ", status = 3" .
            ", updatedBy = '" . $updatedBy . "'" .
            ", updatedDate = '" . $updatedDate . "'";
        $sSQL    = $sSQL . $sWhere;
        $rs      = &$conn->Execute($sSQL);
        $no_bond = $carumanID;
    }

    // help prevent double entry by multiple users ----begin
    $getMax2 = "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS no2 FROM vauchers";
    $rsMax2  = $conn->Execute($getMax2);
    $max2    = sprintf("%06s", $rsMax2->fields('no2'));

    if ($rsMax2) {
        $max2     = intval($rsMax2->fields('no2')) + 1;
        $max2     = sprintf("%06s",  $max2);
        $no_baucer2     = 'PVA' . $max2;
    } else {
        $no_baucer2     = 'PVA000001';
    }
    //-----end

    $sSQL    = "";
    $sSQL    = "INSERT INTO vauchers (" .
        "no_baucer, " .
        "tarikh_baucer, " .
        "batchNo, " .
        "jenis, " .
        "no_bond, " .
        "no_anggota, " .
        "disediakan, " .
        "kod_bank, " .
        "disahkan, " .
        "keterangan, " .
        "kod_caw, " .
        "no_siri, " .
        "pymtAmt, " .
        "StatusID_Pymt, " .
        "tarikh_bank, " .
        "createdDate, " .
        "createdBy, " .
        "updatedDate, " .
        "updatedBy) " .
        " VALUES (" .
        "'" . $no_baucer2 . "', " .
        "'" . $tarikh_baucer . "', " .
        "'" . $batchNo . "', " .
        "'" . $jenis . "', " .
        "'" . $no_bond . "', " .
        "'" . $no_anggota . "', " .
        "'" . $disediakan . "', " .
        "'" . $kod_bank . "', " .
        "'" . $disahkan . "', " .
        "'" . $keterangan . "', " .
        "'" . $kod_caw . "', " .
        "'" . $no_siri . "', " .
        "'" . $masterAmt . "', " .
        "'" . 0 . "', " .
        "'" . $tarikh_bank . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "')";

    if ($jenis == 1 || $jenis == 3) {
        $sSQL1    = "";
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
            "desc_akaun," .
            "updatedBy," .
            "updatedDate	," .
            "createdBy," .
            "createdDate) " .

            " VALUES (" .
            "'" . $no_baucer2 . "', " .
            "'" . 12 . "', " .
            "'" . $tarikh_baucer . "', " .
            "'" . $batchNo . "', " .
            "'" . $no_anggota . "', " .
            "'" . $yymm . "', " .
            "'" . $kod_bank . "', " .
            "'" . 1 . "', " .
            "'" . $masterAmt . "', " .
            "'" . $keterangan . "', " .
            "'" . $updatedBy . "', " .
            "'" . $updatedDate . "', " .
            "'" . $updatedBy . "', " .
            "'" . $updatedDate . "')";
    }

    if ($display) print $sSQL . '<br />';
    else
        $rs = &$conn->Execute($sSQL);
    if ($jenis == 1 || $jenis == 3) {
        $rs = &$conn->Execute($sSQL1);
    }

    $getMax = "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS no FROM vauchers";
    $rsMax  = $conn->Execute($getMax);
    $max    = sprintf("%06s", $rsMax->fields('no'));
    if (!$display) {
        print '<script>
	window.location ="?vw=baucer&mn=908&action=view&add=1&no_baucer=PVA' . $max . '";
	</script>';
    }
}

$strTemp .=
    '<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
    . '<div style="width: 100%; text-align:left">'
    . '<div>&nbsp;</div><div class="table-responsive">'
    . '<form name="MyForm" action="?vw=baucer&mn=908" method="post">'
    . '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print '<input name="yymm" id="yymm" type="hidden" value="' . $yymm . '">';
print '<input name="carumanID" id="carumanID" type="hidden" value="' . $carumanID . '">';
print
    '<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr><td>Nomor Voucher</td><td valign="top"></td><td><input name="no_baucer" value="' . $no_baucer . '" type="text" size="20" maxlength="50" class="form-control-sm" readonly/></td></tr>
			<tr><td>* Tanggal</td><td valign="top"></td>
			<td>
			<div class="input-group" id="tarikh_baucer">
			<input type="text" name="tarikh_baucer" id="tarikh_baucer_input" class="form-control-sm" placeholder="dd/mm/yyyy"
				data-provide="datepicker" data-date-container="#tarikh_baucer"
				data-date-autoclose="true" value="' . $tarikh_baucer . '">
			<div class="input-group-append">
				<span class="input-group-text">
					<i class="mdi mdi-calendar"></i></span>
			</div>
			</div>
			</td>
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
</tr>
<tr><td colspan="3"><hr class="mt-3"/></td></tr>';

if (isset($jenis)) {
    print '<input name="jenis" value="' . $jenis . '" type="hidden">';
}

print '
<tr colspan="3">
	<td valign="top"><input name="j" type="hidden" value="tiada">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr><td valign="top">Bayar Kepada</td></tr>
			<tr>
				<td>* Nomor Anggota</td><td valign="top"></td>
				<td><input name="no_anggota" value="' . $no_anggota . '" type="text" size="20" maxlength="50"  class="form-control-sm" readonly/>&nbsp;';
// if ($action == "new" && $jenis == 1) {
//     print '<input type="button" class="btn btn-sm btn-info waves-light waves-effect" value="Pilih" onclick="window.open(\'selToMember.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
// } else if ($action == "new" && $jenis == 2) {
//     print '<input type="button" class="btn btn-sm btn-info waves-light waves-effect" value="Pilih" onclick="window.open(\'selLoanS.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
//     print '&nbsp;<input name="loan_no" type="hidden" value=""></td>';
// } else if ($action == "new" && $jenis == 3) {
//     print '<input type="button" class="btn btn-sm btn-info waves-light waves-effect" value="Pilih" onclick="window.open(\'selLoanW.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';


//     print '&nbsp;<input name="welfareNo" type="hidden" value=""></td>';
// }

print '<input type="button" class="btn btn-sm btn-info ms-1 waves-light waves-effect" value="Pilih Simpanan" onclick="window.open(\'selToMemberA.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';

print '<input type="button" class="btn btn-sm btn-info ms-1 waves-light waves-effect" value="Pilih Modal" onclick="window.open(\'selToMemberB.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';

print '</tr>
			<tr><td valign="top">Nama</td><td valign="top"></td><td><input name="nama_anggota"  value="' . $nama . '" type="text" size="50" maxlength="50" class="form-control-sm" readonly/>
		    </td></tr>
			<tr><td valign="top">Alamat</td><td valign="top"></td><td><textarea name="alamat" cols="50" rows="4" class="form-control-sm" readonly>' . $alamat . '</textarea></td></tr>
			<tr>
			  <td valign="top">Nomor Obligasi / Jumlah (RP)</td>
			  <td valign="top"></td>
			  <td><input name="no_bond"  value="' . $no_bond . '" size="10" maxlength="50"  class="form-control-sm" readonly />
		      <input name="amt"  value="' . $amt . '" size="10" maxlength="50"  class="form-control-sm" readonly="readonly" /></td>
		  </tr>
			<tr>
			  <td valign="top">Jenis Pembiayaan</td>
			  <td valign="top"></td>
			  <td><input name="name_type"  value="' . $nametype . '" size="40" maxlength="50"  class="form-control-sm" readonly /></td>
		  </tr>

		<tr>
				<td valign="top" align="left">Master Jumlah (Rp)</td><td valign="top"></td>
				<td><input class="form-control-sm" id="master" value="' . $masterAmt . '" type="text" size="20" maxlength="10"/ readonly></td>
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
if ($action == "view" && !is_int(dlookup("transaction", "ID", "docNo='" . $no_baucer . "'"))) {
    print '
<tr>
	<!--td align= "left"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input">Tanda semua</td-->
	<td align= "right" colspan="3">';
    if (!$add) print '

	<!-- Implementing the visual effect on button Tambah for akaun. START -->
	<div class="request-loader-container" id="loaderContainer">
		<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=baucer&mn=908&action=' . $action . '&no_baucer=' . $no_baucer . '&add=1\';">
		<div class="request-loader" id="requestLoaderTambah"></div>
	</div>
	<!-- Implementing the visual effect on button Tambah for akaun. END -->
	';
    else print '
	<!-- Implementing the visual effect on button Simpan for akaun. START -->
	<div class="request-loader-container" id="loaderContainer">
		<input type="button" name="action" value="Simpan" class="btn btn-sm btn-primary" onclick="CheckField(\'Kemaskini\')">
		<div class="request-loader" id="requestLoaderSimpan"></div>
	</div>
	<!-- Implementing the visual effect on button Simpan for akaun. END -->
	';
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
				<td nowrap="nowrap"><b>Kode Akun</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="right"><b>* Jumlah (RP)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';



$noBondNoTran = false;
if ($no_bond && $noTran) {
    $loanID     = dlookup("loandocs", "loanID", "rnoBond='" . $no_bond . "'");
    $loanAmt    = dlookup("loans", "loanAmt", "loanID=" . $loanID);
    $loanType   = dlookup("loans", "loanType", "loanID=" . $loanID);
    $code       = dlookup("general", "c_deduct", "ID=" . $loanType);
    $debit2     = &$loanAmt;
    $add        = 1;
    $noBondNoTran = true;
}

if ($no_bond && $noTran && dlookup("usercaruman", "voucherNo", "ID=" . $no_bond) != null) {
    if (dlookup("usercaruman", "type", "ID=" . $no_bond) == 1) $code = 1595;
    elseif (dlookup("usercaruman", "type", "ID=" . $no_bond) == 2) $code = 1596;
    $debit2     = dlookup("usercaruman", "withdrawAmt", "ID=" . $no_bond);
    $add        = 1;
}

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
            $ruj        = $rsDetail->fields('pymtRefer');
            $perkara    = $rsDetail->fields('deductID');
            $kod_master = dlookup("general", "c_master", "ID=" . $perkara);
            $namagl     = dlookup("generalacc", "name", "ID=" . $kod_master);


            $kod_objek      = dlookup("general", "code", "ID=" . $perkara);
            $kod_akaun      = dlookup("general", "c_Panel", "ID=" . $perkara);
            $keterangan2    = dlookup("general", "name", "ID=" . $kod_akaun);
            $debit          = $rsDetail->fields('pymtAmt');
            $desc_akaun     = dlookup("transactionacc", "desc_akaun", "IDtrans=" . $id);

            print       '
			<tr>
				<td class="Data">&nbsp;' . ++$i . '.</td>				
				<td class="form-control-xs" nowrap="nowrap">' . strSelect2($id, $perkara) . '&nbsp;</td>

				<td class="Data" nowrap="nowrap">
				<input class="form-control-sm" name="namagl[' . $id . ']" type="text" size="30" maxlength="30" value="' . $namagl . '" readonly/>
				<input class="form-control-sm" name="kod_master[' . $id . ']" type="hidden" size="10" maxlength="10" value="' . $kod_master . '"/>
				</td>

				<td class="Data" nowrap="nowrap">
					<input class="form-control-sm" name="kod_akaun[' . $id . ']" type="text" size="8" maxlength="10" value="' . $kod_akaun . '" readonly/>&nbsp;
				</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" class="form-control-sm" rows="4" cols="40" maxlength="500">' . $desc_akaun . '</textarea>&nbsp;
				</td>

				<!--td class="Data" align="right">
					<input name="ruj[' . $id . ']" type="text" size="8" maxlength="10" value="' . $ruj . '" class="form-control-sm" />&nbsp;
				</td-->

				<td class="Data" align="right">
					<input type="hidden" name="ruj[' . $id . ']" val="0">
					<input name="debit[' . $id . ']" type="text" size="10" maxlength="10" value="' . $debit . '" class="form-control-sm" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '">&nbsp;</td>
			</tr>';
            $totalDb += $debit;

            $debit    = '';

            $rsDetail->MoveNext();
        }
        //If there are no records for akaun, disable some buttons and fields to prevent user error (data doubling)
        //------------- START
    } else {
        if (!$add && !$noBondNoTran) {
            echo '<span style="color: red;">Tiada rekod.</span>';
        }
        if ($noBondNoTran) {
        } else
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
		disableElementsByName("tarikh_baucer");
        disableElementsByName("kod_bank");
        disableElementsByName("no_anggota");
		disableElementsByName("nama_anggota");
		disableElementsByName("alamat");
		disableElementsByName("no_bond");
		disableElementsByName("amt");
		disableElementsByName("name_type");
		disableElementsByName("disediakan");
		disableElementsByName("disahkan");
		disableElementsByName("keterangan");
		disableElementById("bottomButton");
		});
		</script>
		';
    }
    //------------- END
}


$strDeductIDList    = deductList(1);
$strDeductCodeList  = deductList(2);
$strDeductNameList  = deductList(3);
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
				<td class="Data">' . $strSelect . '</td>

				<td class="Data">
				<input name="namagl2" type="text" size="30" maxlength="30" value="' . $namagl2 . '" class="form-control-sm" readonly/>
				<input name="kod_master2" type="hidden" size="10" maxlength="10" value="' . $kod_master2 . '" class="form-control-sm">
				</td>

				<td class="Data" nowrap="nowrap">
					<input name="kod_akaun2" type="text" size="8" maxlength="10" value="' . $kod_akaun2 . '"  class="form-control-sm" readonly/>&nbsp;
				</td>

				<td class="Data" align="left">
					<textarea name="desc_akaun2" rows="4" cols="40" maxlength="500" class="form-control-sm" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
				</td>

				<!--td class="Data" align="right">
					<input name="ruj2" type="text" size="8" maxlength="10" value="' . $ruj2 . '"/>&nbsp;
				</td-->

				<td class="Data" align="right">
					<input type="hidden" name="ruj2" val="0">
					<input name="debit2" type="text" size="10" maxlength="10" value="' . $loanAmt . '" class="form-control-sm" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}

if ($totalDb <> 0) {
    $clsRM->setValue($totalDb);
    $strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}

print         '<tr class="table-secondary">
				<td class="Data" colspan="5" align="right"><b>Jumlah (RP)</b></td>
				<td class="Data" id="totalJumlah" align="right"><b>' . number_format($totalDb, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr colspan="3">
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr><td nowrap="nowrap">Jumlah Dalam Perkataan</td><td valign="top"></td><td>
			<input name="" size="80" class="form-control-sm" maxlength="80" value="' . $strTotal . '" readonly>			
			<input class="Data" type="hidden" name="masterAmt" value="' . $totalDb . '">
			<input class="Data" type="hidden" name="bankparent" value="' . $bankparent . '">


			</td></tr>
			

			<tr><td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td><td>' . selectAdmin($disediakan, 'disediakan') . '</td></tr>
			<tr><td nowrap="nowrap">Disahkan Oleh</td><td valign="top"></td><td>' . selectAdmin($disahkan, 'disahkan') . '</td></tr>
			<tr><td nowrap="nowrap" valign="top">Keterangan</td><td valign="top"></td><td valign="top"><textarea class="form-control-sm" name="keterangan" cols="50" rows="4">' . $keterangan . '</textarea></td></tr>
		</table>
	</td>';
print '<input name="kod_caw" type="hidden" value="321"><input name="no_siri" type="hidden" value="S112"><input name="tarikh_bank" type="hidden" value="01/10/2006"></tr>';

if ($no_baucer) {
    $straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
    print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary waves-light waves-effect" onClick= "print_(\'voucherPaymentPrint.php?id=' . $no_baucer . '\')">&nbsp;

<!-- Implementing the visual effect on button Kemaskini. START -->
    <div class="request-loader-container" id="loaderContainer">
		<input type="button" name="action" id="bottomButton" value="' . $straction . '" class="btn btn-primary waves-effect waves-light" onclick="CheckField(\'' . $straction . '\')">
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
</form></form>
</div>';

print $strTemp;
print '
<script>

</script>
<script language="JavaScript">

<!-- Implementing the javascript visual effect for buttons and comparing amount for Jumlah vs Master. START -->

document.addEventListener("DOMContentLoaded", function() {
	function compare() {
		const masterValue 		= document.getElementById("master").value;
		const master 			= parseFloat(masterValue);
		const jumlah 			= parseFloat(document.getElementById("totalJumlah").innerText.replace(/,/g, ""));
    	var noRecords 			= ' . json_encode($noRecords) . ';
		var noBondNoTran 		= ' . json_encode($noBondNoTran) . ';
		var add 				= Boolean(' . json_encode(isset($add) ? $add : false) . ');  // Convert only if $add is set
		const requestLoader 	= document.getElementById("requestLoader");
		var requestLoaderTambah = document.getElementById("requestLoaderTambah");
		var requestLoaderSimpan = document.getElementById("requestLoaderSimpan");

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

		// note: requestLoader is an HTML element. Each HTML element has a classList property.
		// note: classList is an interface to add, remove, toggle, check, replace classes
		// note: active is a class
		// Manage loader visibility
		if (noBondNoTran) {											// for pembiayaan to save bond amount to transaction (jenis 2)
			if (requestLoaderSimpan) {								// for pembiayaan path 1
				requestLoaderSimpan.classList.add("active"); 		// prompt user to click simpan
			} else if(requestLoaderTambah) {						// for pembiayaan path 2
				requestLoader.classList.add("active"); 				// prompt user to click kemaskini
			} else if (jumlah != master) {							// check if master amaun not tallied with jumlah
				requestLoader.classList.add("active"); 				// prompt user to click kemaskini when they are records that are not tallied with master amaun
			} 
		}
			
		if (!noBondNoTran) {										// for normal baucer anggota (jenis 1)
			if (noRecords) {										// check if there are no akaun records
				requestLoaderTambah.classList.add("active"); 		// prompt user to click tambah instead
			} else if (jumlah != master) {							// check if master amaun not tallied with jumlah
				requestLoader.classList.add("active"); 				// prompt user to click kemaskini when they are records that are not tallied with master amaun
			}
		}
	}

	compare();

});

<!-- Implementing the javascript visual effect for buttons and comparing amount for Jumlah vs Master. END -->

<!-- Automatically and dynamically declare value of yymm for tarikh_baucer. START -->

// Make sure the DOM is fully loaded before running the script
window.onload = function() {
	// Function to update the YYMM field
	function updateYYMM() {
	    const tarikhBaucer = document.getElementById("tarikh_baucer_input").value;
	    
	    // Check if a date is selected and valid
	    if (tarikhBaucer && tarikhBaucer.includes("/")) {
	        const dateParts = tarikhBaucer.split("/"); // Assuming the format is dd/mm/yyyy
	        
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

	    const dateInput = document.getElementById("tarikh_baucer_input");

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

<!-- Automatically and dynamically declare value of yymm for tarikh_baucer. END -->

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
                        document.getElementsByName(\'debit2\')[0].value = response.j_Amount;
                    } else {
                        document.getElementsByName(\'debit2\')[0].value = 0;
                    }
                },
                error: function () {
                    alert(\'Failed to fetch amount\');
                }
            });
        } else {
            document.getElementsByName(\'debit2\')[0].value = \'\';
        }
	}

	window.onload = function() {
		// Check if the window is a popup by checking if window.opener is not null
		if (window.opener) {
			// Only trigger the sidebar toggle if the window was opened as a popup
			if (typeof jQuery !== "undefined") {
				// Use jQuery to trigger the click event on #vertical-menu-btn
				$("#vertical-menu-btn").trigger("click");
			} else {
				// If jQuery is not available, use plain JavaScript to trigger the click event
				var menuBtn = document.getElementById("vertical-menu-btn");
				if (menuBtn) menuBtn.click();
			}
		}

		// Check if the window is closed (onUnload event)
		window.onunload = function() {
			// Refresh the parent window when the popup is closed
			if (window.opener) {
				window.opener.location.reload();  // This will refresh the parent window
			}
		};
	};
	
</script>
';
include("footer.php");