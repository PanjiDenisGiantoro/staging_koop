<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: voucherPaymentPrint.php
 *			Date 		: 27/7/2006
 *********************************************************************************/
include("common.php");
include("koperasiinfo.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email, koperasiID FROM setup
        WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$coopName = $rss->fields(name);
$address1 = $rss->fields(address1);
$address2 = $rss->fields(address2);
$address3 = $rss->fields(address3);
$address4 = $rss->fields(address4);
$noPhone = $rss->fields(noPhone);
$email = $rss->fields(email);
$koperasiID = $rss->fields(koperasiID);

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$header =
	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
	. '<html>'
	. '<head>'
	. '<title>' . $emaNetis . '></title>'
	. '<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">'
	. '<meta http-equiv="pragma" content="no-cache">'
	. '<meta http-equiv="expires" content="0">'
	. '<meta http-equiv="cache-control" content="no-cache">'
	. '<LINK rel="stylesheet" href="images/mail.css" >'
	. '</head>'
	. '<body>';

$footer = '</body></html>';

if ($ID) {
	$sql = "SELECT * FROM resit WHERE transID = " . tosql($ID, "Text");
	$rs = $conn->Execute($sql);

	$no_resit = $rs->fields(no_resit);
	//$ID = $rs->fields(transID);
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

	//$sql2 = "SELECT * FROM resit_keterangan WHERE no_resit = ".tosql($no_resit, "Text")." ORDER BY ID";
	//$rsDetail = $conn->Execute($sql2);
	$sql2 = "SELECT * FROM transaction WHERE ID = " . tosql($ID, "Text") . " ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
	$code = dlookup("general", "code", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
	$name = dlookup("general", "name", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
	$jumlah = $rsDetail->fields(pymtAmt);
	$clsRP->setValue($jumlah);
	$strTotal = ucwords($clsRP->getValue()) . ' Sahaja.';
}

$header .=
	'<div align="right">RESIT RASMI</div>'
	. '<div align="right">NO. ' . $no_resit . '</div>'
	. '<table border="0" cellspacing="0" cellpadding="0" width="100%">'
	. '<tr>'
	. '<td align="center" valign="middle" class="textFont">'
	. $coopName . '<br />'
	. $address1 . ',<br />'
	. $address2 . ',<br />'
	. $address3 . ',<br />'
	. $address4 . '.<br />'
	. 'TEL: ' . $noPhone . '<br />'
	. 'EMEL: ' . $email . '<br />'
	. '</td>'
	. '</tr>'
	. '</table>';
print $header;

print
	'<table cellpadding="0" cellspacing="0" width="100%">
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap">Lejer Nombor 046</td>
		<td nowrap="nowrap" align="center">&nbsp;</td>
		<td nowrap="nowrap" align="right">Tanggal : ' . date("d/m/Y") . '</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3">Diterima daripada <u>' . $bayar_nama . ', ' . $alamat . '</u><br />
	Sebanyak RP <u>' . $jumlah . '</u> Ringgit <u>' . $strTotal . '</u><br />
	untuk bayaran :-</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3"><hr size="1px" /></td></tr>
	<tr><td colspan="3">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td nowrap="nowrap">&nbsp;BIL&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;PERKARA&nbsp;</td>
				<td nowrap="nowrap" align="right">&nbsp;AMAUN (RP)&nbsp;</td>
			</tr>
			<tr><td colspan="3"><hr size="1px" /></td></tr>
			<tr>
				<td nowrap="nowrap" valign="top">&nbsp;(1)&nbsp;</td>
				<td valign="top">' . $name . '</td>
				<td nowrap="nowrap" valign="top" align="right">&nbsp;' . $jumlah . '&nbsp;</td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td colspan="3"><hr size="1px" /></td></tr>
			<tr>
				<td nowrap="nowrap" valign="top" align="right">&nbsp;</td>
				<td nowrap="nowrap" valign="top" align="right">&nbsp;JUMLAH&nbsp;</td>
				<td nowrap="nowrap" valign="top" align="right">&nbsp;' . $jumlah . '&nbsp;</td>
			</tr>
		</table>
	</td></tr>
	<tr><td colspan="3"><hr size="1px" /></td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td nowrap="nowrap" width="33%">Cara bayaran : <u>' . $cara_bayar . '</u></td><td nowrap="nowrap" width="33%" align="center" >Nombor Ruj : <u>' . $kod_siri . '</u></td><td nowrap="nowrap" width="33%" align="right">Tanggal : <u>' . $tarikh . '</u></td></tr>
	<tr><td colspan="3">Catatan :' . $catatan . '</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3" align="right">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">b.p [NAMA KOPERASI]<br />___________________________________<br />Bendahari</td></tr></table></td>
			</tr>
		</table>
	</td></tr>
</table>';

print $footer;
