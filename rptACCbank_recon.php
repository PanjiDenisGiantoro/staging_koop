<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *		   Nama 		: 	rptACCbank_recon
 *		   Description	:	Laporan Rekonsilasi Bank Tiada Pengesahan / Ada Pengesahan
 *          Date 		: 	2024
 ******************************************************************************/
session_start();
include("common.php");

date_default_timezone_set("Asia/Jakarta");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$statusSah = ($sah == 1) ? 'Ada Pengesahan' : 'Tiada Pengesahan';
$title = "Penyata Penyemakan Bank Rekonsilasi $statusSah Dari $dtFrom Hingga $dtTo";

$sSQL = "SELECT * FROM transactionacc 
WHERE stat_check IN ($sah) 
AND deductID = '$kod'
AND (tarikh_doc BETWEEN '$dtFrom' AND '$dtTo') 
ORDER BY tarikh_doc ASC";
$rs = &$conn->Execute($sSQL);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';

$codeakaun 	= dlookup("generalacc", "code", "ID=" . $kod);
$namaakaun 	= dlookup("generalacc", "name", "ID=" . $kod);

print '
<table width="100%" class="table table-sm table-striped">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="8" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: center;">
		<th colspan="8" style="height: 30px; vertical-align: middle;">' . $title . '<br>
		' . $codeakaun . ' ' . $namaakaun . '
		</th>
	</tr>
	<tr>
	    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
        <td colspan="8" align="left"><font size=1>CETAK PADA : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
    </tr>
</table>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td colspan="8">
			<table width="100%" class="table table-striped">
				<tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;Bil</th>
					<th nowrap>&nbsp;Nomor Rujukan</th>
					<th nowrap>&nbsp;Tanggal Rujukan</th>
					<th nowrap align="center">&nbsp;Kode Akun</th>
					<th nowrap align="left">&nbsp;Perkara / Nama Akaun</th>
					<th nowrap align="left">&nbsp;Keterangan Perkara</th>
					<th nowrap align="right">&nbsp;Kredit / Jumlah (Rp)</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		$idcode 	= $rs->fields(deductID);
		$codeakaun 	= dlookup("generalacc", "code", "ID=" . $idcode);
		$namaakaun 	= dlookup("generalacc", "name", "ID=" . $idcode);
		$namadoc 	= $rs->fields(docNo);
		$debit 		= $rs->fields(pymtAmt);
		$date 		= toDate("d/m/y", $rs->fields(tarikh_doc));

		print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td width="2%" align="right">' . $bil . ')&nbsp;</td>
						<td align="center">&nbsp;' . $namadoc . '</td>
						<td align="center">&nbsp;' . $date . '</td>
						<td align="center">&nbsp;' . $codeakaun . '</td>
						<td align="left">&nbsp;' . $namaakaun . '</td>
						<td align="left">&nbsp;' . $rs->fields(desc_akaun) . '</td>
						<td align="right">&nbsp;' . number_format($debit, 2) . '</td>
					</tr>';
		$totalDb += $debit;

		$rs->MoveNext();
	}
	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="right"><b>&nbsp;Jumlah Keseluruhan (RP) :</b></td>
						<td align="right"><b>&nbsp;' . number_format($totalDb, 2) . '</b></td>
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
	<center><tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>
</body>
</html>';
