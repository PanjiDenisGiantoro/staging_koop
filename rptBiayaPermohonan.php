<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$month = (int)substr($yrmth, 4, 2);
$yr = (int)substr($yrmth, 0, 4);
if ($id == "PRBD") {
	$kod = 	$kodPrbd;
	$type = 'Peribadi';
	$grpkod = 	1632;
} else if ($id == "BRG") {
	$kod = 	$kodBrg;
	$type = 'Barangan';
	$grpkod = 	1633;
} else if ($id == "KDRN") {
	$kod = 	$kodBrg;
	$type = 'Kenderaan';
	$grpkod = 	1638;
}

$title  = 'Pinjaman Yang Diterima dan Diproses Pada Bulan ' . displayBulan($month) . ' ' . $yr . ' Bagi Jenis Pinjaman Pembiayaan ' . $type;
$title	= strtoupper($title);

$sSQL = "";
$sSQL = "SELECT a. * , b. * , c. *
		FROM `loans` a
		INNER JOIN loandocs b ON a.loanID = b.loanID
		INNER JOIN general c ON a.loanType = c.ID
		WHERE month( a.applyDate ) = " . $month . "
		AND c.parentID = " . $grpkod . "
		ORDER BY a.loanID";
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
					<!--th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;Nombor Anggota</th>
					<th nowrap>&nbsp;Cawangan/Jabatan</th>
					<th nowrap colspan=2>
					<table>
					<tr><td colspan=2>Diterima</td><tr>
					<tr><td>Tarikh</td><td>Jumlah</td><tr>
					</table>
					</th>
					<th nowrap colspan=2>
					<table>
					<tr><td colspan=2>Diterima</td><tr>
					<tr><td>Tarikh</td><td>Jumlah</td><tr>
					</table>
					</th-->
					<th nowrap rowspan=2>&nbsp;</th>
					<th nowrap rowspan=2>&nbsp;Nama</th>
					<th nowrap rowspan=2>&nbsp;Nombor Anggota</th>
					<th nowrap rowspan=2>&nbsp;Cawangan/Jabatan</th>
					<th nowrap colspan=2>&nbsp;Diterima</th>
					<th nowrap colspan=2>&nbsp;Diluluskan</th>
				</tr>
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;Tarikh</th>
					<th nowrap>&nbsp;Jumlah</th>
					<th nowrap>&nbsp;Tarikh</th>
					<th nowrap>&nbsp;Jumlah</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		//$time = explode(" ", $rs->fields(sendDate));
		//$rs->fields(referID);
		$name = dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Number"));
		$memberID = dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Number"));
		$deptID = dlookup("userdetails", "departmentID", "userID=" . tosql($rs->fields(userID), "Number"));
		$jabatan = dlookup("general", "name", "ID=" . tosql($deptID, "Number"));
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $name . '</a></td>
							<td>&nbsp;' . $memberID . '</a></td>
							<td>&nbsp;' . $jabatan . ' </a></td>
							<td align="center">&nbsp;' . toDate('', $rs->fields(applyDate)) . '</a></td>
							<td align="right">&nbsp;' . thousand($rs->fields(loanAmt)) . ' </a></td>';
		if ($rs->fields(approvedDate)) {
			print '
							<td align="center">&nbsp;' . toDate('', $rs->fields(approvedDate)) . ' </a></td>
							<td align="right">&nbsp;' . thousand($rs->fields(loanAmt)) . '</a></td>';
		} else {
			print '
							<td align="center">&nbsp;</a></td>
							<td align="center">&nbsp;</a></td>';
		}
		print '
							</tr>';
		$rs->MoveNext();
	}
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
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
