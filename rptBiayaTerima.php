<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");
$month = (int)substr($yrmth, 4, 2);
$yr = (int)substr($yrmth, 0, 4);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Pinjaman Yang Diterima Dan Diproses Pada Bulan ' . displayBulan($month) . ' ' . $yr;
$title	= strtoupper($title);

$sSQL = "";
$sSQL = "SELECT a . * , b . * , c . * , d . *, a.applyDate as tarikh
		FROM `loans` a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		LEFT JOIN users c ON a.userID = c.userID
		LEFT JOIN userdetails d ON c.userID = d.userID
		WHERE month(a.applyDate) = " . $month . "
		ORDER BY a.loanID";
$rs = &$conn->Execute($sSQL);
//kodPrbd

function date_diff($start_date, $end_date, $returntype = "d")
{
	if ($returntype == "s")
		$calc = 1;
	if ($returntype == "m")
		$calc = 60;
	if ($returntype == "h")
		$calc = (60 * 60);
	if ($returntype == "d")
		$calc = (60 * 60 * 24);

	$_d1 = explode("-", $start_date);
	$y1 = $_d1[0];
	$m1 = $_d1[1];
	$d1 = $_d1[2];

	$_d2 = explode("-", $end_date);
	$y2 = $_d2[0];
	$m2 = $_d2[1];
	$d2 = $_d2[2];

	if (($y1 < 1970 || $y1 > 2037) || ($y2 < 1970 || $y2 > 2037)) {
		return 0;
	} else {
		$today_stamp    = mktime(0, 0, 0, $m1, $d1, $y1);
		$end_date_stamp    = mktime(0, 0, 0, $m2, $d2, $y2);
		$difference    = round(($end_date_stamp - $today_stamp) / $calc);
		return $difference;
	}
}

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
		<td colspan="5" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="85 height="40"><font color="#FFFFFF">' . $title . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="5"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;Nombor Ahli</th>
					<th nowrap>&nbsp;Tarikh</th>
					<th nowrap>&nbsp;Nama Anggota</th>
					<th nowrap>&nbsp;Cawangan/Jabatan</th>
					<th nowrap>&nbsp;Nombor Rujukan</th>
					<th nowrap>&nbsp;Jumlah Dipohon(RP)</th>
					<th nowrap>&nbsp;Tempoh(Bulan)</th>
					<th nowrap>&nbsp;Jenis Loan</th>
					
					<th nowrap>&nbsp;Jumlah Lulus(RP)</th>
					<th nowrap>&nbsp;Tarikh Surat Tawaran Keluar</th>
					<th nowrap>&nbsp;Masalah</th>
					<th nowrap>&nbsp;Tempoh Dari Terima<br> Hingga Masuk Komiti</th>
					<th nowrap>&nbsp;Tempoh Diambil Dari Terima<br> Hingga Surat Tawaran Komiti <br>Hingga Surat Tawaran Dikeluarkan</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		//$time = explode(" ", $rs->fields(sendDate));
		//$rs->fields(referID);
		$jabatan = dlookup("general", "name", "ID=" . tosql($rs->fields(departmentID), "Number"));
		$jenis = dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), ""));
		$tarikh = toDate("d/m/y", $rs->fields(tarikh));

		$bil++;
		if ($rs->fields(rnoBond)) {
			$approved = thousand($rs->fields(loanAmt));
			$date = toDate("d/m/y", $rs->fields(approvedDate));
			//$problem = $rs->fields(remark);
			$time1 = date_diff($rs->fields(tarikh), $rs->fields(b . approvedDate));
			$time2 = $time1;
			$jenis = dlookup("general", "name", "ID=" . $rs->fields(loanType));
		} else {
			$approved = '';
			$date = '';
			$time1 = '';
			$time2 = '';
		}

		$amt1 = thousand($rs->fields(loanAmt));
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(memberID) . '</a></td>
							<td>&nbsp;' . $tarikh . ' </a></td>
							<td>&nbsp;' . $rs->fields(name) . '</a></td>
							<td>&nbsp;' . $jabatan . ' </a></td>
							<td>&nbsp;' . $rs->fields(loanNo) . ' </a></td>
							<td align="right">&nbsp;' . $amt1 . ' </a></td>
							<td align="center">&nbsp;' . $rs->fields(loanPeriod) . ' </a></td>
							<td>&nbsp;' . $jenis . ' </a></td>
							
							<td align="right">&nbsp;' . $approved . ' </a></td>
							<td>&nbsp;' . $date . ' </a></td>
							<td>&nbsp;' . $problem . ' </a></td>
							<td>&nbsp;' . $time1 . ' </a></td>
							<td>&nbsp;' . $time2 . ' </a></td>
						</tr>';
		$rs->MoveNext();
	}
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="14" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr align="center"><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
