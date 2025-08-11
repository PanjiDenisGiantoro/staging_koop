<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: ACCSingleEntry.php
 *			Date 		: 27/7/2006
 *			Keywords 	: disable, duplicate (to prevent user fault)
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCSingleEntryList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;SATU KEMASUKAN</b>';
//$sActionFileName= "ACCSingleEntryList.php";

if (!isset($mm))    $mm = date("m");
if (!isset($yy))    $yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display = 0;
if ($SENO && $action == "view") {
    $sql = "SELECT * FROM singleentry WHERE SENO = '" . $SENO . "'";

    $rs                 = $conn->Execute($sql);
    $SENO               = $rs->fields('SENO');
    $tarikh_entry       = $rs->fields('tarikh_entry');
    $tarikh_entry       = substr($tarikh_entry, 8, 2) . "/" . substr($tarikh_entry, 5, 2) . "/" . substr($tarikh_entry, 0, 4);

    $kod_bank           = $rs->fields('kod_bank');
    $bankparent         = dlookup("generalacc", "parentID", "ID=" . $kod_bank);

    $keterangan         = $rs->fields('keterangan');
    $disediakan         = $rs->fields('disediakan');
    $tarikh_disediakan  = $rs->fields('tarikh_disediakan');
    $tarikh_disediakan  = substr($tarikh_disediakan, 8, 2) . "/" . substr($tarikh_disediakan, 5, 2) . "/" . substr($tarikh_disediakan, 0, 4);

    $description        = $rs->fields('description');
    $nama               = $rs->fields('name');
    $maklumat           = $rs->fields('maklumat');
    $batchNo            = $rs->fields('batchNo');
    $accountNo          = $rs->fields('accountNo');
    // $taxNo				= $rs->fields('taxNo');
    $kod_project        = $rs->fields('kod_project');
    $kod_jabatan        = $rs->fields('kod_jabatan');

    $sql2 = "SELECT * FROM transactionacc WHERE docNo = '" . $SENO . "' ORDER BY ID";
    $rsDetail = $conn->Execute($sql2);
} elseif ($action == "new") {

    $getNo = "SELECT MAX(CAST(right(SENO,6) AS SIGNED INTEGER )) AS nombor FROM singleentry";
    $rsNo = $conn->Execute($getNo);
    $tarikh_entry = date("d/m/Y");
    $tarikh_bank = date("d/m/Y");

    if ($rsNo) {
        $nombor = $rsNo->fields('nombor') + 1;
        $nombor = sprintf("%06s",  $nombor);
        $SENO = 'JVS' . $nombor;
    } else {
        $SENO = 'JVS000001';
    }
}

if ($perkara2) {
    $updatedBy         = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $tarikh_entry_db = saveDateDb($tarikh_entry);
    $yymm             = substr($tarikh_entry_db, 0, 4) . substr($tarikh_entry_db, 5, 2);

    $deductID     = $perkara2;
    $coreID     = dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));
    if ($debit2) {
        $pymtAmt     = $debit2;
        $addminus     = 0;
        $cajAmt     = 0.0;
    } else {
        $pymtAmt     = $kredit2;
        $addminus     = 1;
        $cajAmt     = 0.0;
    }
    if ($pymtAmt == '') $pymtAmt = '0.0';
    $sSQL    = "INSERT INTO transactionacc (" .

        "docNo," .
        "tarikh_doc," .
        "docID," .
        "yrmth," .
        "batchNo," .
        "deductID," .
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
        "'" . $SENO . "', " .
        "'" . $tarikh_entry_db . "', " .
        "'" . 2 . "', " .
        "'" . $yymm . "', " .
        "'" . $batchNo . "', " .
        "'" . $deductID . "', " .
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

        $strActivity = $_POST['Submit'] . 'Kemaskini Jurnal Entry - ' . $SENO;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

        print '<script>
		window.location = "?vw=ACCSingleEntry&mn=' . $mn . '&action=view&SENO=' . $SENO . '";
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

            $strActivity = $_POST['Submit'] . 'Hapus Kandungan Jurnal Entry - ' . $docNo;
            activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
        }
    }
    if (!$display) {
        print '<script>
	window.location = "?vw=ACCSingleEntry&mn=' . $mn . '&action=view&SENO=' . $SENO . '";
	</script>';
    }

    ///////////////////////////////////////////////////////
} elseif ($action == "Kemaskini" || $perkara || $desc_akaun) {
    $updatedBy         = get_session("Cookie_userName");
    $updatedDate     = date("Y-m-d H:i:s");
    $tarikh_entry     = saveDateDb($tarikh_entry);
    $tarikh_disediakan = saveDateDb($tarikh_disediakan);
    $yymm     = substr($tarikh_entry, 0, 4) . substr($tarikh_entry, 5, 2);
    $sSQL     = "";
    $sWhere = "";
    $sWhere = "SENO='" . $SENO . "'";
    $sWhere = " WHERE (" . $sWhere . ")";
    $sSQL    = "UPDATE singleentry SET " .

        "tarikh_entry='" . $tarikh_entry . "'," .
        "batchNo='" . $batchNo . "'," .
        "disediakan='" . $disediakan . "'," .
        "kod_project='" . $kod_project . "', " .
        "kod_jabatan='" . $kod_jabatan . "', " .
        "keterangan='" . $keterangan . "'," .
        "updatedDate='" . $updatedDate . "'," .
        "updatedBy='" . $updatedBy . "'";

    $sSQL = $sSQL . $sWhere;

    $sSQL1 = "";
    $sWhere1 = "";
    $sWhere1 = "docNo='" . $SENO . "'";
    $sWhere1 = " WHERE (" . $sWhere1 . ")";
    $sSQL1    = "UPDATE transactionacc SET " .
        "yrmth='" . $yymm . "'," .
        "tarikh_doc='" . $tarikh_entry . "'";

    $sSQL1 = $sSQL1 . $sWhere1;


    if ($display) print $sSQL . '<br />';
    else
        $rs = &$conn->Execute($sSQL);
    $rs = &$conn->Execute($sSQL1);

    ////////////////////////////////////////////////////////////

    if (count($perkara) > 0) {
        foreach ($perkara as $id => $value) {
            $stat_confirm = 1;
            $deductID = $value;
            $coreID = dlookup("generalacc", "coreID", "ID=" . tosql($deductID, "Text"));
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
            $sSQL    = "UPDATE transactionacc SET " .

                "batchNo=" . tosql($batchNo, "Number") .
                ",deductID=" . tosql($deductID, "Number") .
                ",addminus=" . $addminus .
                ",coreID=" . tosql($coreID, "Number") .
                ",pymtAmt=" . tosql($pymtAmt, "Number") .
                ",stat_confirm=" . $stat_confirm .
                ",updatedDate=" . tosql($updatedDate, "Text") .
                ",updatedBy=" . tosql($updatedBy, "Text");

            $sSQL .= " where " . $sWhere;
            if ($display) print $sSQL . '<br />';
            else $rs = &$conn->Execute($sSQL);
        }
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (count($kod_akaunM) > 0) {
        foreach ($kod_akaunM as $id => $value) {

            $MdeductID = $value;
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
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*if(count($taxing)>0){
		foreach($taxing as $id =>$value){
			
		$taxNo = $value;
		if($debit[$id]){
		$pymtAmt = $debit[$id];
		$addminus = 0;
		}else{
		$pymtAmt = $kredit[$id];
		$addminus = 1;
		}
		$sSQL = "";
		$sWhere = "";		
	    $sWhere = "ID='" . $id ."'";
	    $sSQL	= "UPDATE transactionacc SET " .
	      "batchNo=" . tosql($batchNo, "Number").
          ",taxNo=" . tosql($taxNo, "Number").
          ",addminus=" . $addminus.
          ",pymtAmt=" . tosql($pymtAmt, "Number").
		  ",updatedDate=" . tosql($updatedDate, "Text") .
          ",updatedBy=" . tosql($updatedBy, "Text") ;


		$sSQL .= " where " . $sWhere;
		if($display) print $sSQL.'<br />';
		else $rs = &$conn->Execute($sSQL);
		}	
	}*/
    /////////////////////////////////////////////////////////////
    if (count($desc_akaun) > 0) {
        foreach ($desc_akaun as $id => $value) {

            $stat_confirm = 1;
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
            $sSQL    = "UPDATE transactionacc SET " .
                "batchNo=" . tosql($batchNo, "Number") .
                ",desc_akaun=" . tosql($desc_akaun, "Text") .
                ",addminus=" . $addminus .
                ",pymtAmt=" . tosql($pymtAmt, "Number") .
                ",stat_confirm=" . $stat_confirm .
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
	window.location = "?vw=ACCSingleEntry&mn=' . $mn . '&action=view&SENO=' . $SENO . '";
	</script>';
    }
    //////////////////////////////////////////////////////////////

} elseif ($action == "Simpan" || $simpan) {
    $updatedBy      = get_session("Cookie_userName");
    $updatedDate    = date("Y-m-d H:i:s");
    $tarikh_entry   = saveDateDb($tarikh_entry);
    $tarikh_bayar   = saveDateDb($tarikh_bayar);

    // help prevent double entry by multiple users ----begin
    $getMax2 = "SELECT MAX(CAST(right(SENO,6) AS SIGNED INTEGER )) AS no2 FROM singleentry";
    $rsMax2  = $conn->Execute($getMax2);
    $max2    = sprintf("%06s", $rsMax2->fields('no2'));

    if ($rsMax2) {
        $max2     = $rsMax2->fields('no2') + 1;
        $max2     = sprintf("%06s",  $max2);
        $SENO2     = 'JVS' . $max2;
    } else {
        $SENO2     = 'JVS000001';
    }
    //-----end

    $sSQL = "";
    $sSQL    = "INSERT INTO singleentry (" .
        "SENO, " .
        "tarikh_entry, " .
        "batchNo, " .
        "accountNo, " .
        "kod_project," .
        "kod_jabatan," .
        "addminus, " .
        "disediakan, " .
        "keterangan, " .
        "createdDate, " .
        "createdBy, " .
        "updatedDate, " .
        "updatedBy) " .

        " VALUES (" .

        "'" . $SENO2 . "', " .
        "'" . $tarikh_entry . "', " .
        "'" . $batchNo . "', " .
        "'" . $accountNo . "', " .
        "'" . $kod_project . "', " .
        "'" . $kod_jabatan . "', " .
        "'" . 1 . "', " .
        "'" . $disediakan . "', " .
        "'" . $keterangan . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "', " .
        "'" . $updatedDate . "', " .
        "'" . $updatedBy . "')";

    if ($display) print $sSQL . '<br />';
    else $rs = &$conn->Execute($sSQL);

    /*print '<script>
		alert ("Kemasukan telah disimpan.");
		window.location.href = "'.$sActionFileName.'";
		</script>';*/

    $getMax = "SELECT MAX(CAST(right(SENO,6) AS SIGNED INTEGER )) as no FROM singleentry";
    $rsMax = $conn->Execute($getMax);
    $max = sprintf("%06s", $rsMax->fields('no'));
    if (!$display) {
        print '<script>
	window.location = "?vw=ACCSingleEntry&mn=' . $mn . '&action=view&add=1&SENO=JVS' . $max . '";
	</script>';
    }
}

$strTemp .=
    '<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
    . '<div style="width: 100%; text-align:left">'
    . '<div>&nbsp;</div>'
    . '<form name="MyForm" action="?vw=ACCSingleEntry&mn=' . $mn . '" method="post">'
    . '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;

print '
<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">

			<tr>
				<td>Nombor Rujukan</td>
				<td valign="top"></td>
				<td>
					<input  name="SENO" value="' . $SENO . '" type="text" size="20" maxlength="50" class="form-controlx" readonly/>
				</td>
			</tr>

			<tr>
				<td>* Batch</td>
				<td valign="top"></td>
				<td>' . selectbatch($batchNo, 'batchNo') . '</td>
			</tr>


			
			<tr> <td>Projek</td> <td valign="top"></td> <td>' . selectproject($kod_project, 'kod_project') . '</td> </tr>
			<tr> <td>Cawangan/Kawasan/Zon</td> <td valign="top"></td> <td>' . selectjabatan($kod_jabatan, 'kod_jabatan') . '</td> </tr>

			

		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh</td>
				<td valign="top"></td>
				<td>
				<div class="input-group" id="tarikh_entry">
				<input type="text" name="tarikh_entry" class="form-control-sm" placeholder="dd/mm/yyyy"
					data-provide="datepicker" data-date-container="#tarikh_entry"
					data-date-autoclose="true" value="' . $tarikh_entry . '">
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


//----------
if ($action == "view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $SENO . "'"))) {
    //if($rsDetail->RowCount() > 0) {  //no item return null check int return null invert became true
    print '
<tr>
	<!--td align= "left"><input type="checkbox" onClick="ITRViewSelectAll()" class="Data">Tanda semua</td-->
	<td align= "right" colspan="3">';
    if (!$add) print '
		<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCSingleEntry&mn=913&action=' . $action . '&SENO=' . $SENO . '&add=1\';">';
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
				<td nowrap="nowrap" align="center"><b>Bil</b></td>
				<td nowrap="nowrap"><b>Akaun</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="right"><b>Debit (RM)</b></td>
				<td nowrap="nowrap" align="right"><b>Kredit (RM)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

if ($action == "view") {

    if ($rsDetail->RecordCount() > 0) {
        $i = 1;
        $j = 1;

        while (!$rsDetail->EOF) {
            $id = tohtml($rsDetail->fields('ID'));
            $perkara = $rsDetail->fields('deductID');

            $kod_akaunM = dlookup("generalacc", "parentID", "ID=" . $perkara);
            $namaparent = dlookup("generalacc", "name", "ID=" . $kod_akaun);

            //$taxing =	$rsDetail->fields(taxNo);
            $desc_akaun =    $rsDetail->fields('desc_akaun');
            $a_keterangan = dlookup("generalacc", "code", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
            $kod_akaun = dlookup("generalacc", "c_Panel", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
            //$taxNumber = dlookup("generalacc", "code", "ID=" . $taxing);


            if ($rsDetail->fields('addminus')) {
                $kredit = $rsDetail->fields('pymtAmt');
            } else {
                $debit = $rsDetail->fields('pymtAmt');
            }
            if ($j == 1) $str_id = $id;
            else $str_id = $str_id . ", " . $id;

            print       '<tr>
				<td class="Data" align="center">' . $i++ . '.</td>	

				<td class="Data" nowrap="nowrap">' . strSelect3($id, $perkara) . '
				<input class="form-control-sm" name="kod_akaunM[' . $id . ']" type="hidden" size="10" maxlength="10" value="' . $kod_akaunM . '"/>
				</td>

				<td class="Data" nowrap="nowrap">
					<textarea name="desc_akaun[' . $id . ']" rows="4" cols="40" maxlength="500" type="text" size="40" class="form-control-sm">' . $desc_akaun . '</textarea>
				</td>

				<td class="Data" align="right">
					<input name="debit[' . $id . ']" type="text" size="10" class="form-control-sm" maxlength="10" value="' . $debit . '" style="text-align:right;" readonly/>
				</td>

				<td class="Data" align="right">
					<input name="kredit[' . $id . ']" type="text" size="10" class="form-control-sm" maxlength="10" value="' . $kredit . '" style="text-align:right;" readonly/>
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '"></td>
			</tr>';

            $totalDb += $debit;
            $totalKt += $kredit;

            $debit = '';
            $kredit = '';
            $j++;
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
		disableElementsByName("SENO");
        disableElementsByName("batchNo");
		disableElementsByName("tarikh_entry");
		disableElementsByName("keterangan");
        disableElementsByName("kod_project");
		disableElementsByName("kod_jabatan");
		disableElementById("bottomButton");
		});
		</script>
		';
    }
    //------------- END
}

$strDeductIDList = deductListb2(1);
$strDeductCodeList = deductListb2(2);
$strDeductNameList = deductListb2(3);
$name = 'perkara2';

$strSelect = '<select name="' . $name . '" class="form-select-sm" id="deductSelect" onchange="updateDescAkaun()">

				<option value="">- Kod -';
for ($i = 0; $i < count($strDeductIDList); $i++) {
    $strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
    if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
    $strSelect .=  '>' . $strDeductCodeList[$i] . '&nbsp;&nbsp;' . $strDeductNameList[$i] . '';
}
$strSelect .= '</select>';

if ($add) {
    print       '<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data">' . $strSelect . '
				<input name="kod_akaunM2" type="hidden" size="10" maxlength="10" value="' . $kod_akaunM2 . '" class="form-control-sm"/>
				</td>
				<td class="Data" align="left">
					<textarea name="desc_akaun2" rows="4" cols="40" maxlength="500" class="form-control-sm" align="right">' . $desc_akaun2 . '</textarea>&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="debit2" type="text" size="10" maxlength="10" value="' . $debit2 . '" align="right" class="form-control-sm" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" align="right">
					<input name="kredit2" type="text" size="10" maxlength="10" value="' . $kredit2 . '" align="right" class="form-control-sm" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}
print         '

<tr class="table-secondary">
				<td class="Data" colspan="3" align="right"><b>Jumlah</b></td>
				<td class="Data" align="right"><b>' . number_format($totalDb, 2) . '</b></td>
				<td class="Data" align="right"><b>' . number_format($totalKt, 2) . '</b></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';

$totalALL = $totalDb - $totalKt;

$colorPen = "Data";
if ($totalALL == 0) {
    $colorPen = "greenText";
    print '			
				<tr class="table-secondary">
				<td class="Data" colspan="4" align="right"><b>Baki Balance</b></td>
				<td class="Data" align="left"><font class="' . $colorPen . '"><b>' . number_format($totalALL, 2) . '</b></font></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
				</tr>';
} else {
    $colorPen = "redText";
    print '			
				<tr class="table-secondary">
				<td class="Data" colspan="4" align="right"><b>Baki Balance</b></td>
				<td class="Data" align="left"><font class="' . $colorPen . '"><b>' . number_format($totalALL, 2) . '</b></font></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
				</tr>';
}





$idname = get_session('Cookie_fullName');

print '			
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top">
		<table border="0" cellspacing="1" cellpadding="3">

			<tr>
				<td nowrap="nowrap">Disediakan Oleh</td>
				<td valign="top"></td>
				<td><input class="form-control-sm" name="disediakan" value="' . $idname . '" type="text" size="20" maxlength="15"/></td>
			</tr>';
print '
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td>
				<td valign="top"></td>
				<td valign="top"><textarea name="keterangan" cols="50" rows="4" class="form-control-sm" >' . $keterangan . '</textarea></td>
			</tr>

		</table>
	</td>
	<td>&nbsp;</td>
</tr>';


if ($SENO) {
    $straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
    // $stractionN = ($action=='view'?'Semak':'Simpan');
    print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCSingleEntryPrint.php?id=' . $SENO . '\')">&nbsp;
	<input type="button" name="action" id="bottomButton" value="' . $straction . '" class="btn btn-primary" onclick="CheckField(\'' . $straction . '\')">

	';

    // if ($straction=='Kemaskini' && $totalALL == 0) {
    // 	print'<input type="button" name="action" value="'.$stractionN.'" class="btn btn-sm btn-primary" onclick="CheckField(\''. $straction.'\')">';
    // }


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
			  }
		  }

		  if(act == \'Simpan\' || act == \'Kemaskini\') {
  
		  if(e.elements[c].name=="batchNo" && e.elements[c].value==\'\') 
		  	{
			alert(\'Ruang batch perlu diisi!\');
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
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
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Sila pilih satu rekod sahaja untuk kemaskini status\');
			} else {
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}


	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
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