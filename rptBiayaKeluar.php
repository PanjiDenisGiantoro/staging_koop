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
if ($id == "PRBD") {
	$kod = 	1632;
	$type = 'Pembiayaan Peribadi';
} else if ($id == "BRG") {
	$kod = 	1633;
	$type = 'Pembiayaan Barangan';
} else if ($id == "KDRN") {
	$kod = 	1638;
	$type = 'Pembiayaan Kenderaan';
}

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Surat Tawaran Yang Telah Dikeluarkan Pada Bulan ' . displayBulan($month) . ' Bagi Jenis Pinjaman ' . $type;
$title	= strtoupper($title);


$sSQL = "";
$sSQL = "SELECT a.loanAmt, b.rnoBond, a.approvedDate, c.parentID, f.name AS department, d. * , e. *
				FROM loans a
				INNER JOIN loandocs b ON a.loanID = b.loanID
				INNER JOIN general c ON a.loanType = c.ID
				INNER JOIN users d ON d.userID = a.userID
				INNER JOIN userdetails e ON e.userID = d.userID
				INNER JOIN general f ON f.ID = e.departmentID
				WHERE b.rnoBond IS NOT NULL
				AND c.parentID = " . $kod . "
				ORDER BY a.applyDate DESC";
/*
r01
$sSQL = "SELECT a.*, b.*, c.*, d.name as department
		FROM `letterLog` a inner join users b on a.userID = b.userID inner join userdetails c on a.userID = c.userID inner join general d on c.departmentID = d.ID
		WHERE a.TYPE = 'SURAT' AND a.letterGroup = 2 and a.letterRefer in (".$kod.")
		AND month(a.sendDate) = ".$month."
		ORDER BY a.ID";*/
$rs = &$conn->Execute($sSQL);
//kodPrbd
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
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;Nomor Anggota</th>
					<th nowrap>&nbsp;Cawangan/Jabatan</th>
					<th nowrap>&nbsp;Tanggal Keluar</th>
					<th nowrap>&nbsp;Jumlah</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		//$time = explode(" ", $rs->fields(sendDate));
		//$rs->fields(referID);
		//r01 $total = dlookup("loans", "loanAmt", "loanID=" . tosql($rs->fields(referID), "Number"));
		//r01 toDate("d/m/Y",$rs->fields(sendDate)).'  '.$time[1]
		$total = number_format($rs->fields(loanAmt), 2);
		$outDate = toDate("d/m/Y", $rs->fields(approvedDate)) . '  ' . $time[1];

		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(name) . '</a></td>
							<td align="center">&nbsp;' . $rs->fields(memberID) . '</a></td>
							<td>&nbsp;' . $rs->fields(department) . ' </a></td>
							<td align="center">&nbsp;' . $outDate . '</a></td>
							<td align="right">&nbsp;' . $total . '</a></td>
						</tr>';
		$rs->MoveNext();
	}
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" align="center"><b>- Tiada Rekod Dicetak-</b></td>
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
