<?php

/*********************************************************************************
 *			Project		: iKOOP.com.my
 *			Filename	: voucherJournal.php
 *			Date 		: 27/7/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");top.location="index.php";</script>';
}

$strHeaderTitle = '<a class="maroon" href="index.php">LAMAN UTAMA</a><b>' . '&nbsp;>&nbsp;BAUCER JURNAL</b>';

$strTemp .=
	'<div class="maroon" align="left">' . $strHeaderTitle . '</div>'
	. '<div style="width: 100%; text-align:left">'
	. '<div>&nbsp;</div>'
	. '<form name="MyForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">'
	. '<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;

?>
<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>Nomor Jurnal</td>
				<td valign="top">:</td>
				<td><input name="dateTxt" type="text" size="20" maxlength="50" /></td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td valign="top">:</td>
				<td><input name="dateTxt" type="text" size="20" maxlength="10" /></td>
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
	<td colspan="3">
		Jenis&nbsp;<select name="loanTxt" type="">
			<option>PINJAMAN</option>
		</select>&nbsp;
		NomborBond&nbsp;<input name="bondTxt" type="text" size="10" maxlength="50" />&nbsp;
		NomborAnggota&nbsp;<input name="memberIDTxt" type="text" size="10" maxlength="50" />&nbsp;
		ROSMAN BIN AHMAD DAMAHURI
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="lineBG">
			<tr class="header">
				<td nowrap="nowrap">Bil.</td>
				<td nowrap="nowrap">Perkara</td>
				<td nowrap="nowrap">Kod Objek</td>
				<td nowrap="nowrap">Kode Akun</td>
				<td nowrap="nowrap">Debit</td>
				<td nowrap="nowrap">Kredit</td>
			</tr>
			<tr>
				<td class="Data">1</td>
				<td class="Data" nowrap="nowrap">YURAN ANGGOTA</td>
				<td class="Data">YA1</td>
				<td class="Data">311101</td>
				<td class="Data" align="right">1,856.30</td>
				<td class="Data" align="right">0.0</td>
			</tr>
			<tr>
				<td class="Data">2</td>
				<td class="Data" nowrap="nowrap">MODAL SYER ANGGOTA</td>
				<td class="Data">MSYER</td>
				<td class="Data">311101</td>
				<td class="Data" align="right">200.00</td>
				<td class="Data" align="right">0.0</td>
			</tr>
			<tr>
				<td class="Data">3</td>
				<td class="Data" nowrap="nowrap">PEMBIAYAAN PERIBADI</td>
				<td class="Data">PIN1</td>
				<td class="Data">131101</td>
				<td class="Data" align="right">0.0</td>
				<td class="Data" align="right">2,056.30</td>
			</tr>
			<tr>
				<td class="Data" colspan="4" align="right"><b>Jumlah</b></td>
				<td class="Data" align="right"><b>2,056.30</b></td>
				<td class="Data" align="right"><b>2,056.30</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td width="60%" valign="top">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr>
				<td nowrap="nowrap">Disediakan Oleh</td>
				<td valign="top">:</td>
				<td><select>
						<option>NOR AZEAN BTE MD SIN</option>
					</select></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Disahkan Oleh</td>
				<td valign="top">:</td>
				<td><select>
						<option>JOHANAH BINTI SULI</option>
					</select></td>
			</tr>
			<tr>
				<td nowrap="nowrap" valign="top">Keterangan</td>
				<td valign="top">:</td>
				<td valign="top"><textarea cols="50" rows="4">PELARASAN PEMBIAYAAN TUNAI DENGAN MODAL YURAN SYER ANGGOTA BERHENTI ROSMAN AHMAD NAMAHURI</textarea></td>
			</tr>
		</table>
	</td>
	<td>&nbsp;</td>
	<td width="40%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td nowrap="nowrap" colspan="3">Maklumat dari Slip Bank</td>
			</tr>
			<tr>
				<td nowrap="nowrap">Kod Caw.</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="50" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">NomborSiri</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="50" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap">Tanggal</td>
				<td valign="top">:</td>
				<td><input name="" type="text" size="20" maxlength="50" /></td>
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