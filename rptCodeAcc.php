<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
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
$title  = 'Penyata Urusniaga mengikut Kod Akaun';
$sSQL = "SELECT c.code as codeObj, c.name as name, c.c_Panel as codeAcc, d.kod_bank,
		SUM(CASE WHEN a.addminus = '0' THEN a.pymtAmt ELSE 0 END) AS debit,
		SUM(CASE WHEN a.addminus = '1' THEN a.pymtAmt ELSE 0 END) AS kredit
		FROM transaction a,general c, resit d
		WHERE a.docNo = d.no_resit  AND 
		c.ID = a.deductID AND 
		substring(a.createdDate,1,10) between '" . $dtFrom . "' AND '" . $dtTo . "' 
		GROUP BY c.ID ORDER BY a.deductID";

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
		<th colspan="8" height="40"><font color="#FFFFFF">' . $title . '<br>';
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
					<th nowrap>&nbsp;Kod Objek</th>
					<th nowrap>&nbsp;Keterangan</th>
					<th nowrap>&nbsp;Kod Akaun</th>
					<th nowrap>&nbsp;Debit</th>
					<th nowrap>&nbsp;Kredit</th>
					<th nowrap>&nbsp;Bank</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td align="center">&nbsp;' . $rs->fields(codeObj) . '</td>
							<td>&nbsp;' . $rs->fields(name) . '</td>
							<td align="center">&nbsp;' . $rs->fields(codeAcc) . '</td>
							<td align="right">&nbsp;' . number_format($rs->fields(debit), 2) . '</td>
							<td align="right">&nbsp;' . number_format($rs->fields(kredit), 2) . '</td>
							<td class="Data">&nbsp;' . dlookup("general", "name", "ID=" . tosql($rs->fields('kod_bank'), "Number")) . '</td>
						</tr>';
		$totalDb = $totalDb + $rs->fields(debit);
		$totalKt = $totalKt + $rs->fields(kredit);

		$rs->MoveNext();
	}
	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="4">&nbsp;Jumlah Keseluruhan:</td>
							<td align="right">&nbsp;' . number_format($totalDb, 2) . '</td>
							<td align="right">&nbsp;' . number_format($totalKt, 2) . '</td>
							<td></td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
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
