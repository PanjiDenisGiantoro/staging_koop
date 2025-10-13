<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");

$today = date("F j, Y, g:i a");
$month = (int)substr($yrmth, 4, 2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Penyata Bank Lejer Bagi Bank Kerjasama Rakyat(M) (22-019-105560-5)';

//displayBulan($month).';
$title	= strtoupper($title);

$sSQL = "";
$sSQL = "SELECT a. * , b.name, c.code, c.name
			FROM vauchers a, users b, generalacc c
			WHERE a.no_baucer = c.ID
			AND a.no_anggota = b.userID and a.tarikh_baucer between  " . tosql($dtFrom, "Text") . " 
			AND " . tosql($ID, "Text") . " 
			AND " . tosql($name, "Text") . "
			AND  " . tosql($dtTo, "Text");

$ID 				= $_GET["ID"];
$name 				= $_GET["name"];
$rs = &$conn->Execute($sSQL);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="7" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#0c479d" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="7" height="40"><font color="#FFFFFF">' . $title . '</font>
		</th>
	</tr>

	<tr>
		<td colspan="7"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>

	<tr><td colspan="7">&nbsp;</td></tr>

	<tr>
		<td colspan="7"><font size=2>NO AKAUN DARI [' . $ID . '] KEPADA [' . $ID . ']</font></td>
	</tr>

	<tr>
		<td colspan="7"><font size=2>TARIKH DARI [' . $dtFrom . '] HINGGA [' . $dtTo . ']</font></td>
	</tr>

	<tr>
		<td colspan="7"><font size=2>KOD BANK:[' . $ID . ']</font></td>
	</tr>

	<tr>
		<td colspan="7"><font size=2>NAMA BANK:[' . $name . ']</font></td>
	</tr>
                                   

	<tr>
		<td colspan="7">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;Tarikh</th>
					<th nowrap>&nbsp;Batch</th>
					<th nowrap>&nbsp;Nombor Rujukan</th>
					<th nowrap>&nbsp;Keterangan</th>
					<th nowrap>&nbsp;Debit</th>
					<th nowrap>&nbsp;Kredit</th>
					<th nowrap>&nbsp;Baki</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	$totalA = 0;
	while (!$rs->EOF) {
		$total = $rs->fields(pymtAmt);
		$date = toDate("d/m/Y", $rs->fields(tarikh_baucer));

		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						  <td align="right">' . $bil . ')</td>
						 
						  <td align="center">&nbsp;' . $date . ' </td>
						  <td align="center">&nbsp;' . $rs->fields(no_baucer) . '</td>
						  <td align="right">&nbsp;' . number_format($total, 2) . '</td>';

		$totalA += $total;
		$rs->MoveNext();
	}
	print '
							  </tr>
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="6" align="right">Jumlah Keseluruhan (RP) &nbsp;</td>
							<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="7">&nbsp;</td></tr>
	<tr><td colspan="7"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
