<?php
#put this in the very beginning
//$timestart = microtime();
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAllFee.php
 *		   Description	:	Ringkasan Keseluruhan Yuran Anggota 
 *          Date 		: 	31/5/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y, g:i a");
//$month = (int)substr($yrmth,4,2);
$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);
$yr1 = $yr + 1;
if (!isset($yrmth));
$mth1 = $mth + 1;

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Keuntungan Pembiayaan Bulanan Pada Bulan ' . displayBulan($mth) . ' Tahun ' . $yr;

$sSQL = "SELECT a.userID,a.loanType,a.loanID,a.pokok,a.untung,b.loanNo,b.status FROM potbulanlook a, loans b
WHERE a.loanID = b.loanNo AND b.status IN (3) AND a.yrmth <= '" . $yrmth . "'
GROUP BY a.loanID ORDER BY CAST(a.userID AS SIGNED INTEGER)";

$rs = &$conn->Execute($sSQL);

print '
<html>
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#0c479d" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table width="83%" border=0 align="center"  cellpadding="2" cellspacing="1">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap><div align="center">Bil</div></th>
					<th nowrap>Nombor Anggota </th>
					<th width="514" align="left" nowrap>Nama  Anggota  </th>
					<th width="514" align="left" nowrap>Nama  Pembiayaan  </th>
					<th width="169" align="left" nowrap>Pokok</th>
					<th width="169" align="left" nowrap>Untung</th>
			    </tr>';
if ($rs->RowCount() <> 0) {
	//	$countID=0;
	$bil = 1;
	while (!$rs->EOF) {
		//$totalFee = $arrTotal[$rs->fields(userID)];
		$nama	= dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text"));
		$namaloan	= dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), "Text"));
		$pokok 	= $rs->fields(pokok);
		$untung = $rs->fields(untung);

		print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td width="34" align="center">' . $bil . '&nbsp;</td>
					<td width="108" align="center">' . $rs->fields(userID) . '&nbsp;</td>
					<td align="right"><div align="left">' . $nama . '&nbsp;</div></td>
					<td align="right"><div align="left">' . $rs->fields(loanID) . '&nbsp;-&nbsp;' . $namaloan . '&nbsp;</div></td>
					<td align="right"><div align="left">' . $pokok . '&nbsp;</div></td>
					<td align="right"><div align="left">' . $untung . '&nbsp;</div></td>
					</tr>';

		$JumBakiAkhir1 += $pokok;
		$JumBakiAkhir2 += $untung;
		//$total +=$rs->fields(jum);
		$rs->MoveNext();

		$bil = $bil + 1;
	}
	print '
					<tr bgcolor="FFFFFF"><td colspan="6"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right">Jumlah Keseluruhan Pokok (RM) : </td>
						<td align="right">' . number_format($JumBakiAkhir1, 2) . '</td>
					</tr>

					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right">Jumlah Keseluruhan Untung (RM) : </td>
						<td align="right">' . number_format($JumBakiAkhir2, 2) . '</td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}


print '		</table> 
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
