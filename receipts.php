<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: receipt.php
 *			Date 		: 6/8/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$strHeaderTitle = '<a class="maroon" href="index.php">LAMAN UTAMA</a><b>' . '&nbsp;>&nbsp;RESIT</b>';

if ($action == 'add') {
	$getNo = "SELECT MAX(CAST(substring(  no_resit  , 3 ) AS SIGNED INTEGER )) AS nombor FROM resit";
	$rsNo = $conn->Execute($getNo);
	if ($rsNo) {
		$nombor = $rsNo->fields(nombor) + 1;
		$no_resit = 'RT' . $nombor;
	} else {
		$no_resit = 'RT1';
	}
}

$display = 0;
$updatedBy 	= get_session("Cookie_userName");
$updatedDate = date("Y-m-d H:i:s");
if ($action == "Kemaskini") {
	$sSQL = "";
	$sWhere = "";
	$sWhere = "transID=" . tosql($ID, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL	= "UPDATE resit SET " .
		"tarikh_resit=" . tosql(saveDateDb($tarikh_resit), "Text") .
		",bayar_kod=" . tosql($bayar_kod, "Text") .
		",bayar_nama=" . tosql($bayar_nama, "Text") .
		",alamat=" . tosql($alamat, "Text") .
		",cara_bayar=" . tosql($cara_bayar, "Text") .
		",kod_siri=" . tosql($kod_siri, "Text") .
		",tarikh=" . tosql(saveDateDb($tarikh), "Text") .
		",akaun_bank=" . tosql($akaun_bank, "Text") .
		",kerani=" . tosql($kerani, "Text") .
		",catatan=" . tosql($catatan, "Text") .
		",updatedDate=" . tosql($updatedDate, "Text") .
		",updatedBy=" . tosql($updatedBy, "Text");
	$sSQL = $sSQL . $sWhere;
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	if (!$display) {
		print '<script>
	window.location = "receiptsList.php?ID=' . $ID . '";
	</script>';
	}
} elseif ($action == "Simpan") {
	$sSQL = "";
	$sSQL	= "INSERT INTO resit (" .
		"no_resit, " .
		"transID, " .
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
		" VALUES (" .
		tosql($no_resit, "Text") . ", " .
		tosql($ID, "Text") . ", " .
		tosql(saveDateDb($tarikh_resit), "Text") . ", " .
		tosql($bayar_kod, "Text") . ", " .
		tosql($bayar_nama, "Text") . ", " .
		tosql($alamat, "Text") . ", " .
		tosql($cara_bayar, "Text") . ", " .
		tosql($kod_siri, "Text") . ", " .
		tosql(saveDateDb($tarikh), "Text") . ", " .
		tosql($akaun_bank, "Text") . ", " .
		tosql($kerani, "Text") . ", " .
		tosql($catatan, "Text") . ", " .
		tosql($updatedDate, "Text") . ", " .
		tosql($updatedBy, "Text") . ", " .
		tosql($updatedDate, "Text") . ", " .
		tosql($updatedBy, "Text")  . ") ";
	if ($display) print $sSQL . '<br />';
	else $rs = &$conn->Execute($sSQL);

	if (!$display) {
		print '<script>
	window.location = "receiptsList.php?ID=' . $ID . '";
	</script>';
	}
}

if ($ID) {
	$sql = "SELECT * FROM resit WHERE transID = " . tosql($ID, "Text");
	$rs = $conn->Execute($sql);
	if ($type = $rs->RowCount() <> 0) {
		$no_resit = $rs->fields(no_resit);
		$tarikh_resit = toDate("d/m/y", $rs->fields(tarikh_resit));
		$bayar_kod = $rs->fields(bayar_kod);
		$bayar_nama = $rs->fields(bayar_nama);
		$alamat = $rs->fields(alamat);
		$cara_bayar = $rs->fields(cara_bayar);
		$kod_siri = $rs->fields(kod_siri);
		$tarikh = toDate("d/m/y", $rs->fields(tarikh));
		$akaun_bank = $rs->fields(akaun_bank);
		$kerani = $rs->fields(kerani);
		$catatan = $rs->fields(catatan);
	}
	$sql2 = "SELECT * FROM transaction WHERE ID = " . tosql($ID, "Text") . " ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);

	$deductID = $rsDetail->fields(deductID);
	$kod = dlookup("general", "code", "ID=" . $deductID);
	$keterangan = dlookup("general", "name", "ID=" . $deductID);
	$akaun = dlookup("general", "c_Panel", "ID=" . $deductID);
}


$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="' . $_SERVER['PHP_SELF'] . '?ID=' . $ID . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;

$tarikh_resit = date("d/m/Y");

print
	'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top">Nombor</td>
				<td valign="top">:</td><td><input name="no_resit" value="' . $no_resit . '" type="text" size="20" maxlength="50" readonly/></td>
			</tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tanggal</td><td valign="top">:</td><td><input name="tarikh_resit" value="' . $tarikh_resit . '" type="text" size="20" maxlength="10" /></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td colspan="3"><hr size="1px" /></td></tr>
<tr><td colspan="3">Bayar Kepada</td></tr>
<tr>
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>Kod</td><td valign="top">:</td>
				<td><input name="bayar_kod" value="' . $bayar_kod . '" type="text" size="20" maxlength="50" /></td>
			</tr>
			<tr><td valign="top">Nama</td><td valign="top">:</td><td><input name="bayar_nama"  value="' . $bayar_nama . '"type="text" size="50" maxlength="50" /></td></tr>
			<tr><td valign="top">Alamat</td><td valign="top">:</td><td><textarea name="alamat" cols="50" rows="4">' . $alamat . '</textarea></td></tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Cara Bayaran</td><td valign="top">:</td>
				<td><input name="cara_bayar" value="' . $cara_bayar . '" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Kode & Nomor Seri</td><td valign="top">:</td>
				<td><input name="kod_siri" value="' . $kod_siri . '" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Tanggal</td><td valign="top">:</td>
				<td><input name="tarikh" value="' . $tarikh . '" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Akaun Bank</td><td valign="top">:</td>
				<td><input name="akaun_bank" value="' . $akaun_bank . '" type="text" size="20" maxlength="20" /></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="lineBG">
			<tr class="header">
				<td nowrap="nowrap">Kod</td>
				<td nowrap="nowrap">Keterangan</td>
				<td nowrap="nowrap">Kode Akun</td>
				<td nowrap="nowrap">Jumlah</td>
			</tr>';
if ($ID) {
	$code = dlookup("general", "code", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
	$jumlah = $rsDetail->fields(pymtAmt);
	print 		'<tr>
				<td class="Data" nowrap="nowrap"><input type="hidden" name="ID" value="' . $ID . '">
				<input type="text" name="kod" class="Data" value="' . $kod . '" onfocus="this.blur()" size="10"><input type="button" class="label" value="..." onclick="window.open(\'selTrans.php\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;</td>
				<td class="Data">&nbsp;<input type="text" name="keterangan" class="Data" value="' . $keterangan . '" onfocus="this.blur()" size="15"></td>
				<td class="Data" nowrap="nowrap"><input type="text" name="akaun" class="Data" value="' . $akaun . '" onfocus="this.blur()" size="10">&nbsp;</td>
				<td class="Data" nowrap="nowrap" align="right"><input type="text" name="jumlah" class="Data" value="' . $jumlah . '" onfocus="this.blur()" size="10">&nbsp;</td>
			</tr>';
	if ($jumlah <> 0) {
		$clsRM->setValue($jumlah);
		$strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
	}
} else {
	print 		'<tr>
				<td class="Data" nowrap="nowrap"><input type="hidden" name="ID" value="' . $ID . '">
				<input type="text" name="kod" class="Data" value="' . $kod . '" onfocus="this.blur()" size="10"><input type="button" class="label" value="..." onclick="window.open(\'selTrans.php\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;</td>
				<td class="Data">&nbsp;<input type="text" name="keterangan" class="Data" value="' . $keterangan . '" onfocus="this.blur()" size="15"></td>
				<td class="Data" nowrap="nowrap"><input type="text" name="akaun" class="Data" value="' . $akaun . '" onfocus="this.blur()" size="10">&nbsp;</td>
				<td class="Data" nowrap="nowrap" align="right"><input type="text" name="jumlah" class="Data" value="' . $jumlah . '" onfocus="this.blur()" size="10">&nbsp;</td>
			</tr>';
}
print
	'<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data">&nbsp;</td>
				<td class="Data">&nbsp;</td>
				<td class="Data" align="right">&nbsp;</td>
			</tr>
			<tr>
				<td class="Data" align="right" colspan="3"><b>Jumlah (RP)</b></td>
				<td class="Data" align="right" nowrap="nowrap"><b>' . $jumlah . '</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr><td colspan="3" nowrap="nowrap">Jumlah Dalam Perkataan<br /><input name="" size="80" maxlength="80" value="' . $strTotal . '" readonly></td></tr>
			<tr><td nowrap="nowrap">Staf Keuangan</td><td valign="top">:</td><td>' . selectAdmin($kerani, 'kerani') . '</td></tr>
			<tr><td nowrap="nowrap" valign="top">Catatan</td><td valign="top">:</td><td valign="top"><textarea name="catatan" cols="50" rows="4">' . $catatan . '</textarea></td></tr>
		</table>
	</td>
</tr>';

if ($no_resit) {
	$straction = ($type ? 'Kemaskini' : 'Simpan');
	$cetak = dlookup("resit", "no_resit", "transID=" . tosql($ID, "Text"));
	print '
<tr><td><input type="submit" name="action" value="' . $straction . '" class="but">';
	if ($cetak) print '&nbsp;&nbsp;<input type="button" name="Submit4" value="Cetak" class="but" onClick="print_(\'receiptPaymentPrint.php?ID=' . $ID . '\')">';
	print '&nbsp;</td></tr>';
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
				window.location.href = "receipts.php?action=add" + pk;
			}
		}
	}


	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
include("footer.php");
