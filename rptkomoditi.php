<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *
 *
 *
 ******************************************************************************/
session_start();
include("common.php");

$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$title  = 'Laporan Sijil Komoditi';
$sSQL = "SELECT * FROM komoditi WHERE tarikh_beli  between  " . tosql($dtFrom, "Text") . "
		 AND  " . tosql($dtTo, "Text") . "	";

$rs = &$conn->Execute($sSQL);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html><head><title>' . $emaNetis . '</title></head><body>';

print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="8" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="8" height="40"><font color="#FFFFFF">' . $title . '<br>Dari ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '</font>';
print '	</th>
	</tr>
	<tr>
		<td colspan="8"><font size=1>Cetak pada : ' . $today . '</font></td>
	</tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td colspan="8">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;Nomor Anggota</th>
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;No Rujukan Pembiayaan</th>
					<th nowrap>&nbsp;No Sijil</th>
					<th nowrap>&nbsp;Komoditi</th>
					<th nowrap>&nbsp;Jumlah Pembiayaan (RP)</th>
					<th nowrap>&nbsp;Tanggal Pembelian Sertifikat</th>
					<th nowrap>&nbsp;Masa Pembelian Sijil</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		$amount = $rs->fields(amount);
		$date = toDate("d/m/Y", $rs->fields(tarikh_beli));
		$time = $rs->fields(masa_beli);

		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td align="center">&nbsp;' . $rs->fields(userID) . '</td>
							<td class="Data">&nbsp;' . dlookup("users", "name", "userID=" . tosql($rs->fields('userID'), "Number")) . '</td>
							<td align="center" class="Data">&nbsp;' . dlookup("loans", "loanNo", "loanID=" . tosql($rs->fields('loanID'), "Number")) . '</td>
							<td align="center">&nbsp;' . $rs->fields(no_sijil) . '</td>
							<td align="center" class="Data">&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields('itemType'), "Number")) . '</td>
							<td align="center">&nbsp;' . number_format($amount, 2) . '</td>
							<td align="center">&nbsp;' . $date . '</td>
							<td align="center">&nbsp;' . $time . '</td>
							
						</tr>';
		$totalA += $amount;

		$rs->MoveNext();
	}
	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="6" align="right">&nbsp;Jumlah Keseluruhan: <b>RP</b></td>
							<td align="center">&nbsp;' . number_format($totalA, 2) . '&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							
							
							
						</tr>';
	$totalB = ($totalA * 0.000015);
	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="6" align="right">&nbsp;Jumlah BSAS: <b>RP</b></td>';


	print '
							<td align="center">&nbsp;' . number_format($totalB, 2) . '&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
						<td align="right">&nbsp;</td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr align="center"><td colspan="8"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
