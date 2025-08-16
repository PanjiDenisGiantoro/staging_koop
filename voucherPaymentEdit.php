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

$strHeaderTitle = '<a class="maroon" href="index.php">LAMAN UTAMA</a><b>' . '&nbsp;>&nbsp;BAUCER</b>';

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;

?>
<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top">Nombor</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="50" /></td>
			</tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="10" /></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="3">
		<hr size="1px" />
	</td>
</tr>
<tr>
	<td colspan="3">Bayar Kepada</td>
</tr>
<tr>
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>Kod</td>
				<td valign="top">:</td>
				<td>
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><input name="" type="text" size="20" maxlength="50" /></td>
							<td><input type="checkbox" class="form-check-input" /></td>
							<td>Tunai</td>
							<td><input type="checkbox" class="form-check-input" /></td>
							<td>Tunai Runcit</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>Nama</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="50" maxlength="10" /></td>
			</tr>
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Cara Bayaran</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Nombor Rujukan</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Tarikh</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="10" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Kod & Nombor Siri</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="10" /></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
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
			</tr>
			<tr>
				<td class="Data" nowrap="nowrap">KOM2</td>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data">PEMBAHAGIAN KOMISEN INSURAN KEPADA KELAB KELUARGA</td>
				<td class="Data">291103</td>
				<td class="Data" align="right">0</td>
				<td class="Data" align="right">342.40</td>
			</tr>
			<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data">&nbsp;</td>
				<td class="Data">&nbsp;</td>
				<td class="Data" align="right">&nbsp;</td>
				<td class="Data" align="right">&nbsp;</td>
			</tr>
			<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data" nowrap="nowrap">&nbsp;</td>
				<td class="Data">&nbsp;</td>
				<td class="Data">&nbsp;</td>
				<td class="Data" align="right">&nbsp;</td>
				<td class="Data" align="right">&nbsp;</td>
			</tr>
			<tr>
				<td class="Data" align="right" colspan="5"><b>Jumlah</b></td>
				<td class="Data" align="right"><b>342.40</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr>
				<td colspan="3" nowrap="nowrap">Jumlah Dalam Perkataan<br /><input name="" size="80" maxlength="80"></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Kerani Kewangan</td>
				<td valign="top">:</td>
				<td><select>
						<option>NOREHA BINTI MIZI</option>
					</select></td>
			</tr>
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td>
				<td valign="top">:</td>
				<td valign="top"><textarea cols="50" rows="4">BAYARAN SUMBANGAN KOMISEN INSURAN BAGI TAHUN 2006</textarea></td>
			</tr>
		</table>
	</td>
</tr>
<?

$strTemp =
	'</table>'
	. '</form>'
	. '</div>';

print $strTemp;

include("footer.php");
?>