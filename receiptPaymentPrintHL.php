<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: voucherPaymentPrint.php
 *			Date 		: 27/7/2006
 *********************************************************************************/
include("common.php");

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
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$header =
	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
	. '<html>'
	. '<head>'
	. '<title>' . $emaNetis . '</title>'
	. '<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">'
	. '<meta http-equiv="pragma" content="no-cache">'
	. '<meta http-equiv="expires" content="0">'
	. '<meta http-equiv="cache-control" content="no-cache">'
	. '<LINK rel="stylesheet" href="images/mail.css" >'
	. '</head>'
	. '<body>';

$footer = '
<script>window.print();</script>
</body></html>';

if ($ID) {
	$sql = "SELECT a.*,b.memberID,b.address, b.city, b.postcode, b.stateID, b.departmentID, c.name FROM  resitHL a, userdetails b,users c WHERE b.userID = c.userID and a.bayar_nama = b.memberID and no_resit = " . tosql($ID, "Text");
	$rs = $conn->Execute($sql);

	$no_resit = $rs->fields(no_resit);
	$tarikh_resit = toDate("d/m/y", $rs->fields(tarikh_resit));
	$bayar_kod = $rs->fields(bayar_kod);
	$bayar_nama = $rs->fields(name);
	$no_anggota = $rs->fields(memberID);
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

	$sqltotal = "SELECT sum(pymtAmt) as tot FROM transaction WHERE docNo = '" . $ID . "'";
	$rstotal = $conn->Execute($sqltotal);
	$jumlah = $rstotal->fields(tot);

	$sql2 = "SELECT * FROM transaction WHERE docNo = " . tosql($ID, "Text") . " ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
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
if ($jumlah <> 0) {
	$clsRM->setValue($jumlah);
	$strTotal = ucwords($clsRM->getValue()) . ' Sahaja.';
}
$jumlah = number_format($jumlah, 2);

print
	'<table cellpadding="0" cellspacing="0" width="100%">
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap">&nbsp; </td>
		<td nowrap="nowrap" align="center">&nbsp;</td>
		<td nowrap="nowrap" align="right">Tarikh : ' . $tarikh_resit . '</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3">Diterima daripada: <br><u>' . $bayar_nama . '(' . $no_anggota . ')<br> ' . $alamat . '</u><br /><br>
	Sebanyak RM <u>' . $jumlah . '</u> Ringgit <u>' . $strTotal . '</u><br />
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
			<tr><td colspan="3"><hr size="1px" /></td></tr>';
$jumlah = 0;
if ($rsDetail->RowCount() <> 0) {
	$i = 0;
	while (!$rsDetail->EOF) {
		$code = dlookup("general", "code", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
		$name = dlookup("general", "name", "ID=" . tosql($rsDetail->fields('deductID'), "Number"));
		print '
			<tr>
				<td nowrap="nowrap" valign="top">&nbsp;(' . ++$i . ')&nbsp;</td>
				<td valign="top">' . $name . '</td>
				<td nowrap="nowrap" valign="top" align="right">&nbsp;';
		print  number_format($rsDetail->fields(pymtAmt), 2);
		print  '&nbsp;</td>
			</tr>';
		$jumlah += $rsDetail->fields(pymtAmt);
		$rsDetail->MoveNext();
	}
}

print '
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
				<td nowrap="nowrap" valign="top" align="right">&nbsp;';
print number_format($jumlah, 2);
print '&nbsp;</td>
			</tr>
		</table>
	</td></tr>
	<tr><td colspan="3"><hr size="1px" /></td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td nowrap="nowrap" width="33%">Cara bayaran : <u>' . $cara_bayar . '</u></td><td nowrap="nowrap" width="33%" align="center" >Kod & Nombor siri : <u>' . $kod_siri . '&nbsp;</u></td><td nowrap="nowrap" width="33%" align="right">Tanggal Pembayaran: <u>' . $tarikh . '</u></td></tr>
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
