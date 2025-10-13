<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAsas.php
 *		   Description	:	Report Informasi Asas
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y,");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
if (!(in_array($code, $basicVal))) {
	print '	<script>
				alert ("' . $code . ' - Kod Asas ini tidak wujud...!");
				window.location = "index.php";
			</script>';
}
$title  = $basicList[array_search($code, $basicVal)];

$GetList = ctGeneral("", $code);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
	<title>' . $emaNetis . '</title>
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#A9D7CB" align="center" style="font-family: Poppins, Helvetica, sans-serif; font-weight: bold;">
		<th height="40">MAKLUMAT ASAS - ' . strtoupper($title) . ' PADA ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="50%" class="table table-sm table-striped" style="font-size: 10pt;">
				<tr class="table-primary">
					<td><b>Bil</b></td>
					<td nowrap width="150" align="left"><b>Kod</b></td>
					<td nowrap align="left"><b>Nama</b></td>';
$noSpan = 3;
if ($code == 'B') {
	$noSpan = 4;
	print '			<td nowrap align="center"><b>Majikan Induk</b></td>';
}
if ($code == 'C') {
	$noSpan = 8;
	print '			<td nowrap align="left"><b>Kod Objek</b></td>
					<td nowrap align="left"><b>Kod Akaun</b></td>
					<td nowrap align="left"><b>Nama Kod</b></td>
					<td nowrap align="right"><b>Caj(%)</b></td>
					<td nowrap align="right"><b>Tempoh Maksima</b></td>
					<td nowrap align="right"><b>Jumlah Maksima (RP)</b></td>
					<td nowrap align="center"><b>Penjamin</b></td>';
}
if ($code == 'D') {
	$noSpan = 7;
	print '			<td nowrap><b>Jenis</b></td>
					<td nowrap><b>Alamat</b></td>
					<td nowrap><b>Dihubungi</b></td>
					<td nowrap><b>Nombor Telefon</b></td>';
}
if ($code == 'G') {
	$noSpan = 6;
	print '			<td nowrap align="right"><b>Harga Syer (RP)</td>
					<td nowrap align="right"><b>&Minimum Unit</b></td>
					<td nowrap align="right"><b>Jumlah Unit Syer (RP)</b></td>';
}
if ($code == 'J') {
	$noSpan = 4;
	print '			<td nowrap align="left"><b>Kod Akaun</b></td>';
}
if ($code == 'M') {
	$noSpan = 5;
	print '			<td nowrap align="right"><b>Dari (RP)</b></td>
					<td nowrap align="right"><b>Hingga (RP)</b></td>';
}
if ($code == 'N') {
	$noSpan = 5;
	print '			<td nowrap align="right"><b>Dari</b></td>
					<td nowrap align="right"><b>Hingga</b></td>';
}
if ($code == 'O') {
	$noSpan = 4;
	print '			<td nowrap align="center"><b>Kod Potongan</b></td>';
}
print '			</tr>';
if ($GetList->RowCount() <> 0) {
	while (!$GetList->EOF) {
		$bil++;
		print '
						<tr>
							<td width="2%" align="right" valign="top">' . $bil . '.</td>
							<td valign="top">' . $GetList->fields(code) . '</td>
							<td valign="top">' . $GetList->fields(name) . '</td>';
		if ($code == 'B') {
			print '					<td>' . dlookup("general", "code", "ID=" . tosql($GetList->fields(parentID), "Number")) . '</td>';
		}
		if ($code == 'C') {
			print '					<td>' . dlookup("general", "code", "ID=" . tosql($GetList->fields(c_Deduct), "Number")) . '</td>
							<td>' . dlookup("general", "c_Panel", "ID=" . tosql($GetList->fields(c_Deduct), "Number")) . '</td>
							<td>'
				. dlookup("general", "name", "ID=" . tosql($GetList->fields(c_Deduct), "Number")) . '</td>
							<td align="right">' . $GetList->fields(c_Caj) . '</td>
							<td align="right">' . $GetList->fields(c_Period) . '</td>
							<td align="right">' . $GetList->fields(c_Maksimum) . '</td>
							<td align="center">' . toYN($GetList->fields(c_gurrantor)) . '</td>';
		}
		if ($code == 'D') {
			if ($GetList->fields(d_Type) == 'P')
				$type = 'Panel';
			elseif ($GetList->fields(d_Type) == 'I')
				$type = 'Insuran';
			elseif ($GetList->fields(d_Type) == 'T')
				$type = 'Tabung';
			print '					<td valign="top">' . $type . '</td>
							<td valign="top">' . $GetList->fields(d_Address) . '</td>
							<td valign="top">' . $GetList->fields(d_Contact) . '</td>
							<td valign="top">' . $GetList->fields(d_Phone) . '</td>';
		}
		if ($code == 'G') {
			print '					<td align="right">' . $GetList->fields(g_Price) . '</td>
							<td align="right">' . $GetList->fields(g_Minimum) . '</td>
							<td align="right">' . $GetList->fields(g_Maksimum) . '</td>';
		}
		if ($code == 'J') {
			//$groupNo = dlookup("codegroup", "groupNo", "codeNo=" . tosql($GetList->fields(code), "Text"));
			//print '					<td valign="top">&nbsp;'
			//						.$groupNo.' - '.dlookup("general", "name", "code=" . tosql($groupNo, "Text")).'</td>';
			print '					<td valign="top">' . $GetList->fields('c_Panel') . '</td>';
		}
		if ($code == 'M') {
			print '					<td align="right">' . $GetList->fields(m_Start) . '</td>
							<td align="right">' . $GetList->fields(m_End) . '</td>';
		}
		if ($code == 'N') {
			print '					<td align="right">' . $GetList->fields(n_Start) . '</td>
							<td align="right">' . $GetList->fields(n_End) . '</td>';
		}
		if ($code == 'O') {
			$sSQL = '';
			$sWhere = '';
			$sWhere .= 'a.groupNo = ' . tosql($GetList->fields(code), "Text");
			$sWhere .= ' AND a.codeNo = b.code ';
			$sWhere = ' WHERE (' . $sWhere . ')';
			$sSQL = ' SELECT a.codeNo, b.name FROM codegroup a, general b ';
			$sSQL = $sSQL . $sWhere;
			$rs = &$conn->Execute($sSQL);
			print '					<td>';
			if ($rs->RowCount() <> 0) {
				while (!$rs->EOF) {
					print '&nbsp;' . $rs->fields('codeNo') . ' - ' . $rs->fields('name') . '<br>';
					$rs->MoveNext();
				}
			}
			print '					</td>';
		}
		print '					</tr>';
		$GetList->MoveNext();
	}
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="' . $noSpan . '" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	
</table>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
