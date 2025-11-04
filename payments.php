<?php

/*********************************************************************************
 *			Project		:iKOOP.com.my
 *			Filename	: voucherpayment.php
 *			Date 		: 27/7/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=paymentsList&mn=908">SENARAI</a><b>' . '&nbsp;>&nbsp;AUTO PAY</b>';

//print_r($_POST);
///$conn->debug= true;
if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display = false;
if ($no_bayar && $action == "view") {
	$sql = "SELECT * FROM  bayar WHERE no_bayar = '" . $no_bayar . "' ";
	$rs = $conn->Execute($sql);
	$no_bayar = $rs->fields(no_bayar);
	$tarikh_bayar = toDate("d/m/y", $rs->fields(tarikh_bayar));
	$disediakan = $rs->fields(disediakan);
	$disahkan = $rs->fields(disahkan);
	$keterangan = $rs->fields(keterangan);
	$kod_caw = $rs->fields(kod_caw);
	$no_siri = $rs->fields(no_siri);
	$tarikh_bank = toDate("d/m/y", $rs->fields(tarikh_bank));

	$sql2 = "SELECT * FROM bayar_detail WHERE no_bayar = '" . $no_bayar . "' ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
} elseif ($action == "new") {
	$getNo = "SELECT MAX(CAST(substring(  no_bayar  , 3 ) AS SIGNED INTEGER )) AS nombor FROM bayar";
	$rsNo = $conn->Execute($getNo);
	$tarikh_bayar = date("d/m/Y");
	$tarikh_bank = date("d/m/Y");
	if ($rsNo) {
		$nombor = $rsNo->fields(nombor) + 1;
		$no_bayar = 'AP' . $nombor;
	} else {
		$no_bayar = 'AP1';
	}
}


if ($userID) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	/*if(!(dlookup("bayar", "no_bayar", "no_bayar=" . tosql($no_bayar, "Text")))) {
		$sSQL = "";
		$sSQL	= "INSERT INTO bayar (" . 
					"no_bayar, " .
					"tarikh_bayar, " .
					"disediakan, " .
					"disahkan, " .
					"keterangan, " .
					"kod_caw, " .
					"no_siri, " .
					"tarikh_bank, " .
					"createdDate, " .
					"createdBy, " .
					"updatedDate, " .
					"updatedBy) " .
		            " VALUES (" . 
					tosql($no_bayar, "Text") . ", " .
					tosql(saveDateDb($tarikh_bayar), "Text") . ", " .
					tosql($disediakan, "Text") . ", " .
					tosql($disahkan, "Text") . ", " .
					tosql($keterangan, "Text") . ", " .
					tosql($kod_caw, "Text") . ", " .
					tosql($no_siri, "Text") . ", " .
					tosql(saveDateDb($tarikh_bank), "Text") . ", " .
					tosql($updatedDate, "Text") . ", " .
					tosql($updatedBy, "Text") . ", " .
					tosql($updatedDate, "Text") . ", " .
					tosql($updatedBy, "Text")  . ") ";	          
		if($display) print $sSQL.'<br />';
		else $rs = &$conn->Execute($sSQL);
	}*/
	$sSQL	= "INSERT INTO bayar_detail (" .
		"no_bayar," .
		"userID," .
		"amount," .
		"createdDate," .
		"createdBy," .
		"updatedDate," .
		"updatedBy)" .
		" VALUES (" .
		"'" . $no_bayar . "', " .
		"'" . $userID . "', " .
		"'" . $amount2 . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "') ";
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	$strActivity = $_POST['Submit'] . 'Kemaskini Auto Pay - ' . $no_bayar;
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

	if (!$display) {
		print '<script>
		window.location = "?vw=payments&mn=908&action=view&no_bayar=' . $no_bayar . '";
		</script>';
	}
}

if ($action == "Hapus") {
	if (count($pk) > 0) {
		$sWhere = "";
		foreach ($pk as $val) {
			$sSQL = '';
			$sWhere = "ID='" . $val . "' ";

			$docNo = dlookup("bayar_detail", "no_bayar", $sWhere);

			$sSQL = "DELETE FROM bayar_detail WHERE " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);

			$strActivity = $_POST['Submit'] . 'Hapus Kandungan Auto Pay - ' . $docNo;
			activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=payments&mn=908&action=view&no_bayar=' . $no_bayar . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $no_anggota) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$tarikh_bayar = saveDateDb($tarikh_bayar);
	$tarikh_bank = saveDateDb($tarikh_bank);
	$sSQL = "";
	$sWhere = "";
	$sWhere = "no_bayar= '" . $no_bayar . "' ";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE bayar SET " .
		//"tarikh_bayar='" . $tarikh_bayar . "',".
		"disediakan='" . $disediakan . "'," .
		"disahkan='" . $disahkan . "'," .
		"keterangan='" . $keterangan . "'," .
		"kod_caw='" . $kod_caw . "'," .
		"no_siri='" . $no_siri . "'," .
		"tarikh_bank='" . $tarikh_bank . "'," .
		"updatedDate='" . $updatedDate . "'," .
		"updatedBy='" . $updatedBy . "'";
	$sSQL = $sSQL . $sWhere;
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	if (count($no_anggota) > 0) {
		foreach ($no_anggota as $id => $value) {
			$sSQL = "";
			$sWhere = "";
			$sWhere = "ID=" . tosql($id, "Number");
			$sSQL	= "UPDATE bayar_detail SET " .
				"amount='" . $amount[$id] . "'," .
				"updatedDate='" . $updatedDate . "'," .
				"updatedBy='" . $updatedBy . "' ";
			$sSQL .= " where " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "?vw=payments&mn=908&action=view&no_bayar=' . $no_bayar . '";
	</script>';
	}
} elseif ($action == "Simpan" || $simpan) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$tarikh_bayar = saveDateDb($tarikh_bayar);
	$tarikh_bank = saveDateDb($tarikh_bank);
	$sSQL = "";
	$sSQL	= "INSERT INTO bayar (" .
		"no_bayar, " .
		"tarikh_bayar, " .
		"disediakan, " .
		"disahkan, " .
		"keterangan, " .
		"kod_caw, " .
		"no_siri, " .
		"tarikh_bank, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .
		" VALUES (" .
		"'" . $no_bayar . "', " .
		"'" . $tarikh_bayar . "', " .
		"'" . $disediakan . "', " .
		"'" . $disahkan . "', " .
		"'" . $keterangan . "', " .
		"'" . $kod_caw . "', " .
		"'" . $no_siri . "', " .
		"'" . $tarikh_bank . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "')";
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	$getMax = "SELECT MAX(CAST(substring(  no_bayar  , 3 ) AS SIGNED INTEGER )) as no FROM bayar";
	$rsMax = $conn->Execute($getMax);
	$max = $rsMax->fields(no);
	if (!$display) {
		print '<script>
	window.location = "?vw=payments&mn=908&action=view&add=1&no_bayar=AP' . $max . '";
	</script>';
	}
}

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="?vw=payments&mn=908" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;

//		'.strtoupper(dlookup("users", "name", "userID=" . tosql($no_anggota, "Text"))).'
print
	'<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr><td>Nombor Auto Pay</td><td valign="top"></td><td><input class="form-controlx" name="no_bayar" value="' . $no_bayar . '" type="text" size="20" maxlength="50" readonly/></td></tr>
			<tr><td>Tanggal</td><td valign="top"></td><td><input name="tarikh_bayar" class="form-controlx"value="' . date("d/m/Y") . '" type="text" size="20" maxlength="10" /></td></tr>
		</table>
	</td>
</tr>
<tr><td colspan="3"><hr size="1px" /></td></tr>
<tr><td>&nbsp;</td></tr>';
if ($action == "view" && !is_int(dlookup("bayar_detail", "ID", "no_bayar='" . $no_bayar . "' "))) {
	//if($rsDetail->RowCount() > 0) {  
	print '<tr>
	<!--td align= "left"><input type="checkbox" class="form-check-input" onClick="ITRViewSelectAll()" class="Data">Tanda semua</td-->
	<td align= "right" colspan="3">';
	if (!$add) print '
		<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=payments&mn=908&action=' . $action . '&no_bayar=' . $no_bayar . '&add=1\';">';
	else print '
		<input type="button" name="action" value="Simpan" class="btn btn-sm btn-primary" onclick="CheckField(\'Kemaskini\')">';
	print '&nbsp;<input type="submit" name="action" value="Hapus" class="btn btn-sm btn-danger">
	</td>
</tr>';
}
print
	'<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="table table-striped table-sm">
			<tr class="table-primary">
				<td nowrap="nowrap"><b>Bil</b></td>
				<td nowrap="nowrap"><b>Nombor Ahli</b></td>
				<td nowrap="nowrap" align="center"><b>Kartu Identitas</b></td>
				<td nowrap="nowrap"><b>Nama</b></td>
				<td nowrap="nowrap"><b>Nombor Akaun</b></td>
				<td nowrap="nowrap" align="right"><b>Jumlah (RP)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

if ($action == "view") {
	$i = 0;
	while (!$rsDetail->EOF) {
		$id = $rsDetail->fields(ID);
		$memberID = $rsDetail->fields(userID);
		$strSql = "SELECT a.userID, a.name, b.newic, b.accTabungan FROM users a, userdetails b WHERE a.userID = b.userID and b.memberID = '" . $rsDetail->fields(userID) . "'";
		$rsMember = $conn->Execute($strSql);

		$userID = $rsMember->fields(userID);
		//$userID = dlookup("userdetails", "userID", "memberID='" .  $memberID. "'");
		$nama = $rsMember->fields(name);
		//$nama = dlookup("users", "name", "userID='" . $userID. "'");
		$nokp = $rsMember->fields(newic);
		//$nokp  = dlookup("userdetails", "newic", "userID='" . $userID. "'");
		$acc = $rsMember->fields(accTabungan);
		//$acc  = dlookup("userdetails", "accTabungan", "userID='" . $userID. "'");
		$amount = $rsDetail->fields(amount);

		print	   '<tr>
				<td class="Data">&nbsp;' . ++$i . '.</td>				
				<td class="Data" nowrap="nowrap">
					<input name="no_anggota[' . $id . ']" type="text" size="5" maxlength="6" value="' . $memberID . '" class="form-control-sm" readonly/>&nbsp;
				</td>
				<td class="Data" align="center">
					<input name="nokp" type="text" size="13" maxlength="14" value="' . $nokp . '"  class="form-control-sm" readonly/>&nbsp;
				</td>
				<td class="Data" nowrap="nowrap">
					<input name="nama" type="text" size="25" maxlength="30" value="' . $nama . '" class="form-control-sm" readonly/>&nbsp;
				</td>
				<td class="Data" nowrap="nowrap">
					<input name="acc" type="text" size="17" maxlength="25" value="' . $acc . '"  class="form-control-sm" readonly/>&nbsp;
				</td>
				<td class="Data" align="right">
					<input name="amount[' . $id . ']" type="text" size="10" maxlength="10" value="' . $amount . '" class="form-control-sm" style="text-align:right;" readonly/>&nbsp;
				</td>				
				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '">&nbsp;</td>
			</tr>';
		$total += $amount;
		$amount = '';
		$rsDetail->MoveNext();
	}
}

if ($add) {
	print	   '<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data" nowrap="nowrap">
					<input name="userID" type="text" size="5" maxlength="6" value="" class="form-control-sm" readonly/>&nbsp;&nbsp;<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'selToMember.php?refer=g\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
				</td>
				<td class="Data" nowrap="nowrap">
					<input name="nokp2" type="text" size="13" maxlength="14" value=""  class="form-control-sm" readonly/>&nbsp;
				</td>
				<td class="Data" nowrap="nowrap">
					<input name="nama2" type="text" size="25" maxlength="30" value="" class="form-control-sm" readonly/>&nbsp;
				</td>
				<td class="Data" nowrap="nowrap">
					<input name="acc2" type="text" size="17" maxlength="25" value=""  class="form-control-sm" readonly/>&nbsp;
				</td>
				<td class="Data" align="right">
					<input name="amount2" type="text" size="10" maxlength="10" class="form-control-sm" value="" style="text-align:right;"/>&nbsp;
				</td>				
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}
print 		'<tr class="table-secondary">
				<td class="Data" colspan="5" align="right"><b>Jumlah (RP)</b></td>
				<td class="Data" align="right"><b>' . number_format($total, 2) . '&nbsp;</b></td>
				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="3" valign="top">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr><td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td><td>' . selectAdmin($disediakan, 'disediakan') . '</td></tr>
			<tr><td nowrap="nowrap">Disahkan Oleh</td><td valign="top"></td><td>' . selectAdmin($disahkan, 'disahkan') . '</td></tr>
			<tr><td nowrap="nowrap" valign="top">Keterangan</td><td valign="top"></td><td valign="top"><textarea name="keterangan" class="form-control-sm" cols="50" rows="4">' . $keterangan . '</textarea>
			<input type="hidden" name="kod_caw" value="0">
			<input type="hidden" name="no_siri" value="0">
			<input type="hidden" name="tarikh_bank" value="00/00/0000">
			</td></tr>
		</table>
	</td>
	<!--td>&nbsp;</td>
	<td width="40%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr><td nowrap="nowrap" colspan="3">Maklumat dari Slip Bank</td></tr>
			<tr><td nowrap="nowrap">Kod Caw.</td><td valign="top">:</td><td><input name="kod_caw" value="' . $kod_caw . '" type="text" size="20" maxlength="50" /></td></tr>
			<tr><td nowrap="nowrap">NomborSiri</td><td valign="top">:</td><td><input name="no_siri" value="' . $no_siri . '" type="text" size="20" maxlength="50" /></td></tr>
			<tr><td nowrap="nowrap">Tanggal</td><td valign="top">:</td><td><input name="tarikh_bank" value="' . $tarikh_bank . '" type="text" size="20" maxlength="50" /></td></tr>
		</table>
	</td-->
</tr>';

/*
if($no_bayar) { 
$straction = ($action=='view'?'Kemaskini':'Simpan');
print '
<tr><td>&nbsp;<input type="submit" name="action" value="'.$straction.'" class="but">&nbsp;<input type="button" name="generate" value="Generate" class="but" onclick= "Javascript:(window.location.href=\'payments.php?action=view&no_bayar='.$no_bayar.'&generate=y\')"> </td></tr>';
}*/

if ($no_bayar) {
	$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
	print '
<tr>
	<td>&nbsp;
	<input type="button" name="action" value="' . $straction . '" class="btn waves-light waves-effect btn-primary" onclick="CheckField(\'' . $straction . '\')">';
	if ($straction == 'Simpan') print '
	<input type="hidden" name="simpan" value="1">';
	print '&nbsp;<input type="button" name="generate" value="Generate" class="btn waves-light waves-effect btn-secondary" onclick= "Javascript:(window.location.href=\'?vw=payments&mn=908&action=view&no_bayar=' . $no_bayar . '&generate=y\')">
	</td>
</tr>';
}

/*function writespace($n,&$file){
	$space=" ";
	for($i=1;$i<=$n;$i++){
	fwrite($file, $space);
	}
}

function writecomma(){
global $file;
	fwrite($file, " ,");
}

function writecommaspace(){
global $file;
	fwrite($file, " , ");
}*/

if ($generate) {
	$rpath = realpath("payments.php");
	$dpath = dirname($rpath);
	$fname = trim($fname);
	$fname = $no_bayar . '.txt';
	$filename = $dpath . '/textfile/' . $fname;

	$str = "SELECT * FROM bayar_detail WHERE no_bayar = '" . $no_bayar . "' ORDER BY ID";
	$rs = &$conn->Execute($str);
	$file = fopen($filename, 'w', 1);
	//$nl="\n";
	//fwrite($file, $nl);

	while (!$rs->EOF) {

		$userID = $rs->fields(userID);
		$strSql = "SELECT a.name, b.newic, b.accTabungan FROM users a, userdetails b WHERE a.userID = b.userID and b.memberID = '" . $userID . "'";
		$rsMember = $conn->Execute($strSql);
		fwrite($file, " ");
		fwrite($file, $userID);
		//writecommaspace();
		fwrite($file, " , ");
		//$nokp  = dlookup("userdetails", "newic", "userID='" . $userID. "'");
		fwrite($file, $rsMember->fields(newic));
		//writecommaspace();
		fwrite($file, " , ");
		//$nama = dlookup("users", "name", "userID=='" . $userID. "'");
		fwrite($file, $rsMember->fields(name));
		//writecommaspace();
		fwrite($file, " , ");
		//$acc  = dlookup("userdetails", "accTabungan", "userID=='" . $userID. "'");
		$acc  = str_replace("-", "", $rsMember->fields(accTabungan));
		fwrite($file, $acc);
		//writecomma();
		fwrite($file, " ,");
		fwrite($file, $rs->fields(amount));
		fwrite($file, "\r\n");

		$rs->MoveNext();
	}
	fclose($file);


	$link =  '<a href="/kofrim/textfile/' . $fname . '">' . $fname . '</a>';
} else {
	//print 'sila masukkan nama fail.';
}
//--------
if ($link) {
	print '<tr><td>&nbsp;(Right click- save link as to download):&nbsp;' . $link . ' </td></tr>';
}
//--------

$strTemp =
	'</table>'
	. '</form>'
	. '</div>';

print $strTemp;

print '
<script language="JavaScript">
	function CheckField(act) {
	    e = document.MyForm;
		count = 0;	
		for(c=0; c<e.elements.length; c++) {
		  if(act == \'Kemaskini\') {
		  if(e.elements[c].name=="userID" && e.elements[c].value==\'\') {
			alert(\'Nombor anggota tidak dibenar kosong\');
            count++;
		  }
		  
		  if(e.elements[c].name=="amount2" && e.elements[c].value==\'\') {
			alert(\'Amaun perlulah diisi!\');
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
