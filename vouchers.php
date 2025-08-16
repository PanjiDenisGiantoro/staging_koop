<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: voucherPaymentEdit.php
 *			Date 		: 27/7/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$strHeaderTitle = '<a class="maroon" href="index.php">LAMAN UTAMA</a><b>' . '&nbsp;>&nbsp;</b><a class="maroon" href="vouchersList.php">SENARAI</a><b>' . '&nbsp;>&nbsp;BAUCER</b>';

//$conn->debug= true;
$tarikh_baucer = date("d/m/Y");
$tarikh_ruj = date("d/m/Y");
$display = 0;
if ($no_baucer && $action == "view") {
	$sql = "SELECT * FROM  baucer WHERE no_baucer = " . tosql($no_baucer, "Text");
	$rs = $conn->Execute($sql);
	$rujukan = $rs->fields(rujukan);
	$no_baucer = $rs->fields(no_baucer);
	$tarikh_baucer = toDate("d/m/y", $rs->fields(tarikh_baucer));
	$bayar_kod = $rs->fields(bayar_kod);
	$bayar_nama = $rs->fields(bayar_nama);
	$cek_tunai = ($rs->fields(cek) == "cek_tunai" ? "checked" : "");
	$cek_runcit = ($rs->fields(cek) == "cek_runcit" ? "checked" : "");
	$cara_bayar = $rs->fields(cara_bayar);
	$no_rujukan = $rs->fields(no_rujukan);
	$tarikh_ruj = toDate("d/m/y", $rs->fields(tarikh_ruj));
	$kod_siri = $rs->fields(kod_siri);
	$kerani = $rs->fields(kerani);
	$catatan = $rs->fields(catatan);

	$sql2 = "SELECT * FROM baucer_keterangan WHERE no_baucer = " . tosql($no_baucer, "Text") . " ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
} elseif ($action == "new") {
	$getNo = "SELECT MAX(CAST(substring(  no_baucer  , 3 ) AS SIGNED INTEGER )) AS nombor FROM baucer";
	$rsNo = $conn->Execute($getNo);
	if ($rsNo) {
		$nombor = $rsNo->fields(nombor) + 1;
		$no_baucer = 'PV' . $nombor;
	} else {
		$no_baucer = 'PV1';
	}
}

if ($kod_akaun2) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	if (!is_int(dlookup("baucer", "ID", "no_baucer=" . tosql($no_baucer, "Text")))) {
		$bayar_nama = strtoupper(dlookup("users", "name", "userID=" . tosql($bayar_kod, "Text")));
		if ($cek_tunai)  $cek =  "cek_tunai";
		if ($cek_runcit)  $cek =  "cek_runcit";
		$sSQL = "";
		$sSQL	= "INSERT INTO baucer (" .
			"no_baucer, " .
			"tarikh_baucer, " .
			"bayar_kod, " .
			"bayar_nama, " .
			"cek, " .
			"cara_bayar, " .
			"no_rujukan, " .
			"tarikh_ruj, " .
			"kod_siri, " .
			"kerani, " .
			"catatan, " .
			"createdDate, " .
			"createdBy, " .
			"updatedDate, " .
			"updatedBy) " .
			" VALUES (" .
			tosql($no_baucer, "Text") . ", " .
			tosql(saveDateDb($tarikh_baucer), "Text") . ", " .
			tosql($bayar_kod, "Text") . ", " .
			tosql($bayar_nama, "Text") . ", " .
			tosql($cek, "Text") . ", " .
			tosql($cara_bayar, "Text") . ", " .
			tosql($no_rujukan, "Text") . ", " .
			tosql(saveDateDb($tarikh_ruj), "Text") . ", " .
			tosql($kod_siri, "Text") . ", " .
			tosql($kerani, "Text") . ", " .
			tosql($catatan, "Text") . ", " .
			tosql($updatedDate, "Text") . ", " .
			tosql($updatedBy, "Text") . ", " .
			tosql($updatedDate, "Text") . ", " .
			tosql($updatedBy, "Text")  . ") ";
		if ($display) print $sSQL . '<br />';
		else $rs = &$conn->Execute($sSQL);
	}

	$code = dlookup("general", "ID", "code=" . tosql($kod_akaun2, "Number"));
	$kod2  = dlookup("codegroup", "groupNo", "codeNo=" . tosql($kod_akaun2, "Text"));
	$keterangan2 = dlookup("general", "name", "ID=" . tosql($code, "Number"));
	$sSQL	= "INSERT INTO baucer_keterangan (" .
		"no_baucer, " .
		"kod, " .
		"no_ruj, " .
		"keterangan, " .
		"kod_akaun, " .
		"kuantiti, " .
		"jumlah, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .
		" VALUES (" .
		tosql($no_baucer, "Text") . ", " .
		tosql($kod2, "Text") . ", " .
		tosql($no_ruj2, "Text") . ", " .
		tosql($keterangan2, "Text") . ", " .
		tosql($kod_akaun2, "Text") . ", " .
		tosql($kuantiti2, "Text") . ", " .
		tosql($jumlah2, "Text") . ", " .
		tosql($updatedDate, "Text") . ", " .
		tosql($updatedBy, "Text") . ", " .
		tosql($updatedDate, "Text") . ", " .
		tosql($updatedBy, "Text")  . ") ";
	//print $sSQL.'<br />';
	$rs = &$conn->Execute($sSQL);
	if (!$display) {
		print '<script>
		window.location = "vouchers.php?action=view&add=1&no_baucer=' . $no_baucer . '";
		</script>';
	}
}

if ($action == "Hapus") {
	if (count($pk) > 0) {
		$sWhere = "";
		foreach ($pk as $val) {
			$sSQL = '';
			$sWhere = "ID=" . tosql($val, "Text");
			$sSQL = "DELETE FROM baucer_keterangan WHERE " . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "vouchers.php?action=view&no_baucer=' . $no_baucer . '";
	</script>';
	}
} elseif ($action == "Kemaskini" || $kod_akaun) {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$bayar_nama = strtoupper(dlookup("users", "name", "userID=" . tosql($bayar_kod, "Text")));
	$sSQL = "";
	$sWhere = "";
	$sWhere = "no_baucer=" . tosql($no_baucer, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";

	if ($cek_tunai)  $cek =  "cek_tunai";
	if ($cek_runcit)  $cek =  "cek_runcit";
	$sSQL	= "UPDATE baucer SET " .
		"tarikh_baucer=" . tosql(saveDateDb($tarikh_baucer), "Text") .
		",bayar_kod=" . tosql($bayar_kod, "Text") .
		",bayar_nama=" . tosql($bayar_nama, "Text") .
		",cek=" . tosql($cek, "Text") .
		",cara_bayar=" . tosql($cara_bayar, "Text") .
		",no_rujukan=" . tosql($no_rujukan, "Text") .
		",tarikh_ruj=" . tosql(saveDateDb($tarikh_ruj), "Text") .
		",kod_siri=" . tosql($kod_siri, "Text") .
		",kerani=" . tosql($kerani, "Text") .
		",catatan=" . tosql($catatan, "Text") .
		",updatedDate=" . tosql($updatedDate, "Text") .
		",updatedBy=" . tosql($updatedBy, "Text");
	$sSQL = $sSQL . $sWhere;
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	if (count($kod_akaun)) {
		foreach ($kod_akaun as $id => $value) {
			$code = dlookup("general", "ID", "code=" . tosql($kod_akaun[$id], "Number"));
			$kod  = dlookup("codegroup", "groupNo", "codeNo=" . tosql($kod_akaun[$id], "Text"));
			$keterangan = dlookup("general", "name", "ID=" . tosql($code, "Number"));
			$sSQL = "";
			$sWhere = "";
			$sWhere = "ID=" . tosql($id, "Text");
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL	= "UPDATE baucer_keterangan SET " .
				"kod=" . tosql($kod, "Text") .
				",no_ruj=" . tosql($no_ruj[$id], "Text") .
				",keterangan=" . tosql($keterangan, "Text") .
				",kod_akaun=" . tosql($kod_akaun[$id], "Text") .
				",kuantiti=" . tosql($kuantiti[$id], "Text") .
				",jumlah=" . tosql($jumlah[$id], "Text") .
				",updatedDate=" . tosql($updatedDate, "Text") .
				",updatedBy=" . tosql($updatedBy, "Text");
			$sSQL = $sSQL . $sWhere;
			if ($display) print $sSQL . '<br />';
			else $rs = &$conn->Execute($sSQL);
		}
	}
	if (!$display) {
		print '<script>
	window.location = "vouchers.php?action=view&no_baucer=' . $no_baucer . '";
	</script>';
	}
} elseif ($action == "Simpan") {
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$bayar_nama = strtoupper(dlookup("users", "name", "userID=" . tosql($bayar_kod, "Text")));
	if ($cek_tunai)  $cek =  "cek_tunai";
	if ($cek_runcit)  $cek =  "cek_runcit";
	$sSQL = "";
	$sSQL	= "INSERT INTO baucer (" .
		"no_baucer, " .
		"tarikh_baucer, " .
		"bayar_kod, " .
		"bayar_nama, " .
		"cek, " .
		"cara_bayar, " .
		"no_rujukan, " .
		"tarikh_ruj, " .
		"kod_siri, " .
		"kerani, " .
		"catatan, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .
		" VALUES (" .
		tosql($no_baucer, "Text") . ", " .
		tosql(saveDateDb($tarikh_baucer), "Text") . ", " .
		tosql($bayar_kod, "Text") . ", " .
		tosql($bayar_nama, "Text") . ", " .
		tosql($cek, "Text") . ", " .
		tosql($cara_bayar, "Text") . ", " .
		tosql($no_rujukan, "Text") . ", " .
		tosql(saveDateDb($tarikh_ruj), "Text") . ", " .
		tosql($kod_siri, "Text") . ", " .
		tosql($kerani, "Text") . ", " .
		tosql($catatan, "Text") . ", " .
		tosql($updatedDate, "Text") . ", " .
		tosql($updatedBy, "Text") . ", " .
		tosql($updatedDate, "Text") . ", " .
		tosql($updatedBy, "Text")  . ") ";
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	$getMax = "SELECT MAX(CAST(substring(  no_baucer  , 3 ) AS SIGNED INTEGER )) as no FROM baucer";
	$rsMax = $conn->Execute($getMax);
	$max = $rsMax->fields(no);
	if (!$display) {
		print '<script>
	window.location = "vouchers.php?action=view&no_baucer=PV' . $max . '";
	</script>';
	}
}

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;

print
	'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top">Nombor</td><td valign="top">:</td><td><input name="no_baucer" type="text" value="' . $no_baucer . '" size="20" maxlength="50" readonly/></td>
			</tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh</td><td valign="top">:</td><td><input name="tarikh_baucer" type="text" value="' . $tarikh_baucer . '" size="20" maxlength="10" /></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td colspan="3"><hr size="1px" /></td></tr>
<tr><td colspan="3">Bayar Kepada</td></tr>
<tr>
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr><td>Nombor anggota</td><td valign="top">:</td>
			<td>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><input name="bayar_kod" type="text" size="20" maxlength="50" value="' . $bayar_kod . '" onchange="document.MyForm.action.focus();"/>&nbsp;';
if ($action == "new") print '<input type="button" class="label" value="..." onclick="window.open(\'selToMember.php?refer=e\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
print '</td>
					<td><input name="cek_tunai" class="form-check-input" type="checkbox" ' . $cek_tunai . ' /></td><td>Tunai</td>
					<td><input name="cek_runcit" class="form-check-input" type="checkbox"' . $cek_runcit . ' /></td><td>Tunai Runcit</td>
				</tr>
			</table>
			</td></tr>
			<tr><td>Nama</td><td valign="top">:</td><td><input name="bayar_nama" type="text" size="50" maxlength="50" value="' . $bayar_nama . '" /></td></tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Cara Bayaran</td><td valign="top">:</td>
				<td><input name="cara_bayar" type="text" size="20" maxlength="20" value="' . $cara_bayar . '" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Nombor Rujukan</td><td valign="top">:</td>
				<td><input name="no_rujukan" type="text" size="20" maxlength="10" value="' . $no_rujukan . '" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Tarikh</td><td valign="top">:</td>
				<td><input name="tarikh_ruj" type="text" size="20" maxlength="10" value="' . $tarikh_ruj . '" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Kod & Nombor Siri</td><td valign="top">:</td>
				<td><input name="kod_siri" type="text" size="20" maxlength="10" value="' . $kod_siri . '" /></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<!--td align= "left"><input type="checkbox" onClick="ITRViewSelectAll()" class="Data">Tanda semua</td-->
	<td align= "right" colspan="3">
	<input type="button" name="add" value="Tambah" class="but"
	onClick="window.location.href=\'vouchers.php?action=' . $action . '&no_baucer=' . $no_baucer . '&add=1\';">&nbsp;
	<input type="submit" name="action" value="Hapus" class="but">
	</td>
</tr>
<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="lineBG">
			<tr class="header">
				<td nowrap="nowrap">Kod.</td>
				<td nowrap="nowrap">Nombor Ruj</td>
				<td nowrap="nowrap">Keterangan</td>
				<td nowrap="nowrap">Kod Akaun</td>
				<td nowrap="nowrap">Qty</td>
				<td nowrap="nowrap">Jumlah</td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

$strTotal = '';
if ($action == "view") {
	while (!$rsDetail->EOF) {
		$id = tohtml($rsDetail->fields(ID));
		print	   '<tr>
				<td class="Data">
					<input name="kod[' . $id . ']" type="text" size="8" maxlength="10" value="' . $rsDetail->fields(kod) . '" readonly />&nbsp;
				</td>				
				<td class="Data" nowrap="nowrap">
					<input name="no_ruj[' . $id . ']" type="text" size="8" maxlength="10" value="' . $rsDetail->fields(no_ruj) . '" />&nbsp;
				</td>
				<td class="Data">
					<textarea name="keterangan[' . $id . ']" cols="50" rows="1" readonly>' . $rsDetail->fields(keterangan) . '</textarea>
				</td>
				<td class="Data" nowrap="nowrap">
					' . strSelect($id, $rsDetail->fields(kod_akaun)) . '&nbsp;
				</td>
				<td class="Data" align="right">
					<input name="kuantiti[' . $id . ']" type="text" size="8" maxlength="10" value="' . $rsDetail->fields(kuantiti) . '" />&nbsp;
				</td>
				<td class="Data" align="right">
					<input name="jumlah[' . $id . ']" type="text" size="8" maxlength="10" value="' . $rsDetail->fields(jumlah) . '" />&nbsp;
				</td>
				<td class="Data" nowrap="nowrap"><input type="checkbox" class="form-check-input" name="pk[]" value="' . $id . '">&nbsp;</td>
			</tr>';
		$total += $rsDetail->fields(jumlah);
		$rsDetail->MoveNext();
	}
	if ($rsDetail->RowCount() <> 0) {
		$strTotal = '';
		if ($total) {
			$clsRM->setValue($total);
			$strTotal = ucwords($clsRM->getValue()) . ' Sahaja';
		}
	}
}
if ($add) {
	print	   '<tr>
				<td class="Data">
					<input name="kod2" type="text" size="8" maxlength="10" value="' . $kod_akaun . '" readonly />&nbsp;
				</td>				
				<td class="Data" nowrap="nowrap">
					<input name="no_ruj2" type="text" size="8" maxlength="10" value="' . $no_ruj . '" />&nbsp;
				</td>
				<td class="Data">
					<textarea name="keterangan2" cols="50" rows="1" readonly>' . $keterangan . '</textarea>
				</td>
				<td class="Data" nowrap="nowrap">
					' . strSelect('', '', 'add') . '&nbsp;
				</td>
				<td class="Data" align="right">
					<input name="kuantiti2" type="text" size="8" maxlength="10" value="' . $kuantiti . '" />&nbsp;
				</td>
				<td class="Data" align="right">
					<input name="jumlah2" type="text" size="8" maxlength="10" value="' . $jumlah . '" />&nbsp;
				</td>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
			</tr>';
}

//print 'tot '.$total;
print 		'<tr>
				<td class="Data" align="right" colspan="5"><b>Jumlah</b></td>
				<td class="Data" align="right"><b>' . number_format($total, 2) . '</b></td>
				<td class="Data">&nbsp;</td>
			</tr>
		</table>
	</td>

</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr><td colspan="3" nowrap="nowrap">Jumlah Dalam Perkataan<br /><input name="rm" value="' . $strTotal . '" size="100" maxlength="100"></td></tr>
			<tr><td nowrap="nowrap">Kerani Kewangan</td><td valign="top">:</td><td>' . selectAdmin($kerani, 'kerani') . '</td></tr>
			<tr><td nowrap="nowrap" valign="top">Catatan</td><td valign="top">:</td><td valign="top"><textarea name="catatan" cols="50" rows="4">' . $catatan . '</textarea></td></tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>';

if ($no_baucer) {
	$straction = ($action == 'view' ? 'Kemaskini' : 'Simpan');
	print '
<tr><td>&nbsp;<input type="submit" name="action" value="' . $straction . '" class="but"> ';
	if ($bayar_kod)  print '&nbsp;<input type="button" name="print" value="Cetak" class="but" onClick="print_(\'voucherPaymentPrint.php?id=' . $no_baucer . '\')">';
	print '</td></tr>';
}
$strTemp =
	'</table>'
	. '</form>'
	. '</div>';

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
				window.location.href = "vouchers.php?action=add" + pk;
			}
		}
	}


	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
include("footer.php");
