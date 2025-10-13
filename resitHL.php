<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: resitHL.php
 *			Date 		: 19/10/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=resitListHL&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;RESIT</b>';

if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display = 0;
if ($no_resit && $action == "view") {
	$sql = "SELECT a.*,b.memberID,b.address, b.city, b.postcode, b.stateID, b.departmentID, c.name FROM  resit a, userdetails b, users c WHERE b.userID = c.userID and a.bayar_nama = b.memberID and no_resit = '" . $no_resit . "'";
	$rs = $conn->Execute($sql);
	$no_resit = $rs->fields(no_resit);
	$tarikh_resit = toDate("d/m/y", $rs->fields(tarikh_resit));
	$no_bond = $rs->fields(bayar_kod);
	//$userID = dlookup("users", "name", "ID=" . $rs->fields(bayar_nama));
	$bayar_nama = $rs->fields(name);
	$no_anggota = $rs->fields(memberID);
	/*/---
	$stradd = str_replace("<pre>","",$rs->fields(address));
	$stradd = str_replace("</pre>","",$stradd);
	$alamat = $stradd.', '.$rs->fields(city).',   '.$rs->fields(postcode).', '.
		dlookup("general", "name", "ID=" . $rs->fields(stateID));
	$alamat = strtoupper($alamat); */
	//---
	$deptID			=  $rs->fields('departmentID');
	$departmentAdd	=  dlookup("general", "b_Address", "ID=" . tosql($deptID, "Number"));
	$alamat = strtoupper(strip_tags($departmentAdd));
	//-----------------
	$cara_bayar = $rs->fields(cara_bayar);
	$kod_siri = $rs->fields(kod_siri);
	$tarikh = toDate("d/m/y", $rs->fields(tarikh));
	$akaun_bank = $rs->fields(akaun_bank);
	$kerani = $rs->fields(kerani);
	$catatan = $rs->fields(catatan);

	$sql2 = "SELECT * FROM transaction WHERE docNo = '" . $no_resit . "' ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
} elseif ($action == "new") {
	$getNo = "SELECT MAX(CAST(right(no_resit,5) AS SIGNED INTEGER)) AS nombor FROM resit";
	$rsNo = $conn->Execute($getNo);
	$tarikh_resit = date("d/m/Y");
	$tarikh = date("d/m/Y");
	if ($rsNo) {
		$nombor = intval($rsNo->fields(nombor)) + 1;
		$nombor = sprintf("%05s",  $nombor);
		$no_resit = 'RTH' . $nombor;
	} else {
		$no_resit = 'R00001';
	}
}

if ($perkara2) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	/*if(!is_int(dlookup("transaction", "ID", "docNo=" . tosql($no_resit, "Text")))) {
		$tarikh_resit = saveDateDb($tarikh_resit);
		$tarikh = saveDateDb($tarikh);
		$sSQL = "";
		$sSQL	= "INSERT INTO resit (" . 
					"no_resit, " .
					"tarikh_resit, " .
					"bayar_kod, " .
					"bayar_nama, " .
					"alamat, " .
					"cara_bayar, " .
					"kod_siri, " .
					"tarikh, " .
					"akaun_bank, " .
					"kerani, " .
					"catatan, " .
					"createdDate, " .
					"createdBy, " .
					"updatedDate, " .
					"updatedBy) " .
		            " VALUES (".
					"'". $no_resit . "', ".
					"'". $tarikh_resit . "', ".
					"'". $bayar_kod . "', ".
					"'". $no_anggota . "', ".
					"'". $alamat . "', ".
					"'". $cara_bayar . "', ".
					"'". $kod_siri . "', ".
					"'". $tarikh . "', ".
					"'". $akaun_bank . "', ".
					"'". $kerani . "', ".
					"'". $catatan . "', ".
					"'". $updatedDate . "', ".
					"'". $updatedBy . "', ".
					"'". $updatedDate . "', ".
					"'". $updatedBy  . "') ";
		if($display) print $sSQL.'<br />';
		else $rs = &$conn->Execute($sSQL);
	}*/

	//$code = dlookup("general", "code", "ID=" . tosql($perkara2, "Number"));
	$deductID = &$perkara2;
	//$kod2  = dlookup("codegroup", "groupNo", "codeNo=" . tosql($kod_akaun2, "Text"));
	//$keterangan2 = dlookup("general", "name", "ID=" . tosql($code, "Number"));
	//if($debit2){
	//$pymtAmt = &$debit2;
	//$addminus = 0;
	//$cajAmt = 0.0;
	//}else{
	//$pymtAmt = &$kredit2;
	$addminus = 1;
	$cajAmt = 0.0;
	//}
	$userID = dlookup("userdetails", "userID", "memberID = '" . $no_anggota . "'");
	if ($pymtAmt == '') $pymtAmt = '0.0';
	$sSQL	= "INSERT INTO transaction (" .
		"docNo," .
		"userID," .
		"yrmth," .
		"deductID," .
		"transID," .
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
		"'" . 79 . "', " .
		"'" . $addminus . "', " .
		"'" . 66 . "', " .
		"'" . $ruj2 . "', " .
		"'" . $kredit2 . "', " .
		"'" . $cajAmt . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "')";
	if ($display) print $sSQL . '<br />';


	else {
		$rs = &$conn->Execute($sSQL);
		print '<script>
		window.location = "?vw=resitHL&mn=910&action=view&no_resit=' . $no_resit . '";
		</script>';
	}
}

if ($action == "Hapus") {
	if (count($pk) > 0) {
		$sWhere = "";
		foreach ($pk as $val) {
			$sSQL = '';
			$sWhere = "ID='" . $val . "'";
			$sSQL = "DELETE FROM transaction WHERE " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=resitHL&action=view&no_resit=' . $no_resit . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $perkara) {
	$updatedBy 	= get_session("Cookie_userName");
	$statusHL1 = 1;
	$updatedDate = date("Y-m-d H:i:s");
	$sSQL = "";
	$sWhere = "";
	$sWhere = "no_resit='" . $no_resit . "'";
	$sWhere = " WHERE (" . $sWhere . ")";
	$tarikh_resit = saveDateDb($tarikh_resit);
	$tarikh = saveDateDb($tarikh);
	$sSQL	= "UPDATE resit SET " .
		//"tarikh_resit='" .$tarikh_resit . "',".
		//"bayar_kod='" . $no_anggota . "',".
		//"bayar_nama='" . $no_anggota . "',".
		"alamat='" . $alamat . "'," .
		"cara_bayar='" . $cara_bayar . "'," .
		"kod_siri='" . $kod_siri . "'," .
		"tarikh='" . $tarikh . "'," .
		"akaun_bank='" . $akaun_bank . "'," .
		"kerani='" . $kerani . "'," .
		"catatan='" . $catatan . "'," .
		//"statusHL='" . $statusHL1 . "',".
		"updatedDate='" . $updatedDate . "'," .
		"updatedBy='" . $updatedBy . "'";
	$sSQL = $sSQL . $sWhere;
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	if (count($perkara) > 0) {
		foreach ($perkara as $id => $value) {
			$deductID = $value;
			//if($debit[$id]){
			//$pymtAmt = $debit[$id];
			//$addminus = 0;
			//}else{
			$pymtAmt = $kredit[$id];
			$addminus = 1;
			//}
			$no_ruj = $ruj[$id];
			//print '<br>'.$no_ruj;
			$sSQL = "";
			$sWhere = "";
			$sWhere = "ID='" . $id . "'";
			$sSQL	= "UPDATE transaction SET " .
				// "docNo=" . tosql($docNo, "Text") .
				// ",userID=" . tosql($no_anggota[$id], "Text").
				// ",yrmth=" . tosql($yrmth[$id], "Text").
				"deductID= '" . $deductID . "'" .
				// ",transID=" . tosql($transID[$id], "Number").
				",addminus= '" . $addminus . "'" .
				// ",pymtID=" . tosql($pymtID[$id], "Number").
				// ",pymtRefer= '" . $no_ruj . "'".
				",pymtAmt= '" . $pymtAmt . "'" .
				// ",cajAmt=" . tosql($cajAmt[$id], "Number").
				",updatedDate= '" . $updatedDate . "'" .
				",updatedBy= '" .  $updatedBy . "'";
			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=resitHL&action=view&no_resit=' . $no_resit . '";
	</script>';
	}
} elseif ($action == "Simpan" || $simpan) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$tarikh_resit = saveDateDb($tarikh_resit);
	$statusHL1 = 1;
	$tarikh = saveDateDb($tarikh);
	$sSQL = "";
	$sSQL = "";
	$sSQL	= "INSERT INTO resit (" .
		"no_resit, " .
		"tarikh_resit, " .
		"bayar_kod, " .
		"bayar_nama, " .
		"alamat, " .
		"cara_bayar, " .
		"kod_siri, " .
		"tarikh, " .
		"akaun_bank, " .
		"kerani, " .
		"catatan, " .
		"createdDate, " .
		//"statusHL, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .
		" VALUES (" .
		"'" . $no_resit . "', " .
		"'" . $tarikh_resit . "', " .
		"'" . $no_bond . "', " .
		"'" . $no_anggota . "', " .
		"'" . $alamat . "', " .
		"'" . $cara_bayar . "', " .
		"'" . $kod_siri . "', " .
		"'" . $tarikh . "', " .
		"'" . $akaun_bank . "', " .
		"'" . $kerani . "', " .
		"'" . $catatan . "', " .
		"'" . $updatedDate . "', " .
		//"'". $statusHL1 . "', ".
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy  . "') ";
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	$getMax = "SELECT MAX(CAST(right(no_resit,5) AS SIGNED INTEGER )) as no FROM resit";
	$rsMax = $conn->Execute($getMax);
	$max = sprintf("%05s", $rsMax->fields(no));
	if (!$display) {
		print '<script>
	window.location = "?vw=resitHL&action=view&add=1&no_resit=RTH' . $max . '";
	</script>';
	}
}




$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div class="row table-responsive">'
	. '<form name="MyForm" action="?vw=resitHL&mn=910" method="post"> <input type="hidden" name="picture" value="' . $pic . '">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';
print $strTemp;
echo '
<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top">No. Struk</td>
				<td valign="top"></td><td><input class="form-control-sm" name="no_resit" value="' . $no_resit . '" type="text" size="20" maxlength="50" readonly/></td>
			</tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh</td><td valign="top"></td><td><input name="tarikh_resit" value="' . $tarikh_resit . '" class="form-control-sm" type="text" size="20" maxlength="10" /></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td colspan="3"><hr size="1px" /></td></tr>
<tr><td colspan="3">Diterima daripada </td></tr>
<tr>
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>* No. Anggota</td><td valign="top"></td>
				<td><input name="no_anggota" value="' . $no_anggota . '" type="text" size="20" maxlength="50"  class="form-control-sm" readonly/>&nbsp;';
if ($action == "new" && $jenis == 2) print '<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'selLoanSHL.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
print '&nbsp;<input name="loan_no" type="hidden" value="">&nbsp;</td>
			</tr>
			<tr><td valign="top">Nama</td><td valign="top"></td><td><input name="nama_anggota"  value="' . $bayar_nama . '" type="text" size="40" maxlength="50" class="form-control-sm" readonly/>
		    </td></tr>
			<tr><td valign="top">Alamat</td><td valign="top"></td><td><textarea name="alamat" cols="50" rows="4" class="form-control-sm" readonly>' . $alamat . '</textarea></td></tr>
			<tr>
			  <td valign="top">No. Bond / Jumlah (Rp)</td>
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
				<td valign="top" align="right">Kod & No. Siri</td><td valign="top"></td>
				<td><input name="kod_siri" value="' . $kod_siri . '" class="form-control-sm" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Tarikh Bayaran</td><td valign="top"></td>
				<td><input name="tarikh" value="' . $tarikh . '" type="text" class="form-control-sm" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Akaun Bank</td><td valign="top"></td>
				<td><input name="akaun_bank" value="' . $akaun_bank . '" type="text" size="20" class="form-control-sm" maxlength="20"/></td>
			</tr>
		</table>
	</td>
</tr>		
<tr><td>&nbsp;</td></tr>';

echo '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';



//if($no_anggota) $nama = dlookup("users", "name", "userID=" . tosql($no_anggota, "Text"));
//'.strtoupper(dlookup("users", "name", "userID=" . tosql($no_anggota, "Text"))).'
/*
print '
<tr>
    <td width="48%">
                <table border="0" cellspacing="1" cellpadding="2">
                    <tr>
                        <td valign="top">No. resit</td>
                        <td valign="top">:</td><td><input class="Data"  name="no_resit" value="'.$no_resit.'" type="text" size="20" maxlength="50" readonly/></td>
                    </tr>
                </table>
    </td>
    <td valign="top">&nbsp;</td>
    <td width="48%" align="right">
                <table border="0" cellspacing="1" cellpadding="2">
                        <tr>
                                <td valign="top" align="right">Tarikh</td><td valign="top">:</td><td><input name="tarikh_resit" value="'.$tarikh_resit.'" type="text" size="20" maxlength="10" /></td>
                        </tr>
                </table>
    </td>
</tr>
<tr><td colspan="3"><hr size="1px" /></td></tr>
<tr><td colspan="3">Diterima daripada :</td></tr>
<tr>
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>*No. anggota</td><td valign="top">:</td>
				<td><input name="no_anggota" value="'.$no_anggota.'" type="text" size="20" maxlength="50"  class="data" readonly/>&nbsp;'; 

				if($action=="new" && $jenis == 2) print '<input type="button" class="label" value="..." onclick="window.open(\'selLoanSHL.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
				print '&nbsp;<input name="loan_no" type="hidden" value="">&nbsp;</td>
			</tr>
			<tr><td valign="top">Nama</td>
                        <td valign="top">:</td><td><input name="nama_anggota"  value="'.$bayar_nama.'" type="text" size="40" maxlength="50" class="data" readonly/>
		    </td></tr>
			<tr><td valign="top">Alamat</td><td valign="top">:</td><td><textarea name="alamat" cols="50" rows="4" class="data" readonly>'.$alamat.'</textarea></td></tr>
			<tr>
			  <td valign="top">No Bond / Amaun</td>
			  <td valign="top">:</td>
			  <td><input name="no_bond"  value="'.$no_bond.'" size="10" maxlength="50"  class="data" readonly />
		      <input name="amt"  value="'.$amt.'" size="10" maxlength="50"  class="data" readonly="readonly" /></td>
		  </tr>
			<tr>
			  <td valign="top">Jenis Pembiayaan</td>
			  <td valign="top">:</td>
			  <td><input name="name_type"  value="'.$nametype.'" size="40" maxlength="50"  class="data" readonly /></td>
		  </tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Cara Bayaran</td><td valign="top">:</td>
				<td><input name="cara_bayar" value="'.$cara_bayar.'" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Kod & No. Siri</td><td valign="top">:</td>
				<td><input name="kod_siri" value="'.$kod_siri.'" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Tarikh Bayaran</td><td valign="top">:</td>
				<td><input name="tarikh" value="'.$tarikh.'" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Akaun Bank</td><td valign="top">:</td>
				<td><input name="akaun_bank" value="'.$akaun_bank.'" type="text" size="20" maxlength="20" /></td>
			</tr>
		</table>
	</td>
</tr>		
<tr><td>&nbsp;</td></tr>';	*/
if ($action == "view" && !is_int(dlookup("transaction", "ID", "docNo='" . $no_resit . "'"))) {
	//if($rsDetail->RowCount() > 0) {  //no item return null check int return null invert became true

	print '
<tr>
	<!--td align= "left"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input">Tanda semua</td-->
	<td align= "right" colspan="3">';
	if (!$add) print '
		<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=resitHL&mn=910&action=' . $action . '&no_resit=' . $no_resit . '&add=1\';">';
	else print '
		<input type="button" name="action" value="Simpan" class="btn btn-sm btn-secondary" onclick="CheckField(\'Kemaskini\')">';
	print '&nbsp;<input type="submit" name="action" value="Hapus" class="btn btn-sm btn-danger">
	</td>
</tr>';
}
print
	'<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="table table-sm table-striped">
			<tr class="table-primary">
				<td nowrap="nowrap"><b>Bil</b></td>
				<td nowrap="nowrap"><b>* Perkara</b></td>
				<td nowrap="nowrap"><b>Kod Objek</b></td>
				<td nowrap="nowrap"><b>Kod Akaun</b></td>
				<!--td nowrap="nowrap"><b>* No. Rujukan</b></td-->
				<td nowrap="nowrap" align="right"><b>*Jumlah (RP)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

if ($action == "view") {
	$i = 0;
	while (!$rsDetail->EOF) {
		$id = $rsDetail->fields(ID);
		$ruj = $rsDetail->fields(pymtRefer);
		$perkara = $rsDetail->fields(deductID);
		$kod_objek = dlookup("general", "code", "ID=" . $perkara);
		$kod_akaun = dlookup("general", "c_Panel", "ID=" . $perkara);
		$keterangan2 = dlookup("general", "name", "ID=" . $kod_akaun);
		//if($rsDetail->fields(addminus)){
		$kredit = $rsDetail->fields(pymtAmt);
		//}else{
		//$debit = $rsDetail->fields(pymtAmt);
		//}
		print	   '
			<tr class="table-light">
				<td class="Data">&nbsp;' . ++$i . '.</td>				
				<td class="Data" nowrap="nowrap">' . strSelect2($id, $perkara) . '&nbsp;</td>
				<td class="Data" nowrap="nowrap">
					<input name="kod_objek[' . $id . ']" type="text" size="8" maxlength="10" value="' . $kod_objek . '" class="form-control-sm" readonly/>&nbsp;
				</td>
				<td class="Data" nowrap="nowrap">
					<input name="kod_akaun[' . $id . ']" type="text" size="8" maxlength="10" value="' . $kod_akaun . '" class="form-control-sm" readonly/>&nbsp;
				</td>
				<!--td class="Data" align="right">
					<input name="ruj[' . $id . ']" type="text" size="8" maxlength="10" value="' . $ruj . '" />&nbsp;
				</td-->
				<td class="Data" align="right">
					<input name="ruj[' . $id . ']" type="hidden" value="' . $no_anggota . '"/>
					<input name="kredit[' . $id . ']" type="text" size="10" maxlength="10" class="form-control-sm" value="' . $kredit . '" style="text-align:right;" readonly/>&nbsp;
				</td>
				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '">&nbsp;</td>
			</tr>';
		//$totalDb += $debit;
		$totalKt += $kredit;
		//$debit = '';
		$kredit = '';
		$rsDetail->MoveNext();
	}
}

$strDeductIDList = deductList(1);
$strDeductNameList = deductList(3);
$name = 'perkara2';

$strSelect = '<select name="' . $name . '" class="form-select-sm">
				<option value="">- Kod -';
for ($i = 0; $i < count($strDeductIDList); $i++) {
	$strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
	if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
	$strSelect .=  '>' . $strDeductNameList[$i];
}
$strSelect .= '</select>';

if ($add) {
	print	   '<tr class="table-light">
                            <td class="Data" nowrap="nowrap">&nbsp;</td>
                            <td class="Data">' . $strSelect . '</td>
                            <td class="Data" nowrap="nowrap">
                                    <input name="kod_objek2" type="text" size="8" maxlength="10" value="' . $kod_objek2 . '"  class="form-control-sm" readonly/>&nbsp;
                            </td>
                            <td class="Data" nowrap="nowrap">
                                    <input name="kod_akaun2" type="text" size="8" maxlength="10" value="' . $kod_akaun2 . '"  class="form-control-sm" readonly/>&nbsp;
                            </td>
                            <!--td class="Data" align="right">
                                    <input name="ruj2" type="text" size="8" maxlength="10" value="' . $ruj2 . '"/>&nbsp;
                            </td-->
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
print 		'<tr class="table-light">
                                            <td class="Data" colspan="4" align="right"><b>Jumlah</b></td>
                                            <td class="Data" align="right"><b>' . number_format($totalKt, 2) . '&nbsp;</b></td>
                                            <td class="Data" align="right"><b>&nbsp;</b></td>
                                    </tr>
		</table>
	</td>
</tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">
                                        <tr><td colspan="3" nowrap="nowrap">Jumlah Dalam Perkataan<br /><input name="" size="100" maxlength="100" value="' . $strTotal . '" class="form-controlx" readonly></td></tr>
                                        <tr><td nowrap="nowrap">Kerani Kewangan</td><td valign="top"></td><td>' . selectAdmin($kerani, 'kerani') . '</td></tr>
                                        <tr><td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td><td valign="top"><textarea name="catatan" class="form-controlx" cols="45" rows="4">' . $catatan . '</textarea></td></tr>
		</table>
	</td>
</tr>';

if ($no_resit) {
	$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
	print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'resitPaymentPrint.php?ID=' . $no_resit . '\')">&nbsp;
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
	function print_(url) {
		window.open(url,"pop","top=100, left=100, width=600, height=400, scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");					
	}

	function CheckField(act) {
	    e = document.MyForm;
		count = 0;	
		for(c=0; c<e.elements.length; c++) {
		  //if(!e.debit2.value == \'\') alert(e.nama_anggota.value);
		  if(e.elements[c].name=="nama_anggota" && e.elements[c].value==\'\') {
			alert(\'Sila pilih anggota!\');
            count++;
		  }

		  if(act == \'Kemaskini\') {
		  //if(e.elements[c].name=="ruj2" && e.elements[c].value==\'\') {
		  //  alert(\'Ruang rujukan perlu diisi!\');
          //  count++;
		  //}
		  
		  if(e.elements[c].name=="kredit2" && e.elements[c].value==\'\') {
			alert(\'Ruang amaun perlu diisi!\');
            count++;
		  }
		  }

		}

		if(count==0) {
			e.submit();
		}

	}
</script>
';
include("footer.php");
