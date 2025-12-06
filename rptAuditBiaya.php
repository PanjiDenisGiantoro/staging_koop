<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAllFee.php
 *		   Description	:	Ringkasan Keseluruhan Wajib Anggota 
 *          Date 		: 	31/5/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Keseluruhan Wajib Anggota Mengikut Nombor Keanggotaan';
$sSQL = "";
$sSQL = "SELECT b.memberID, a.name, a.userID , c.loanType, c.loanAmt, c.loanPeriod, c.kadar_u,
		c.monthlyPymt, c.applyDate, d.ajkDate2,  b.approvedDate, d.btindihCaj,
		d.rnoBond, d.rcreatedDate, c.outstandingAmt,
		c.penjaminID1, c.penjaminID2, c.penjaminID3
		FROM users a, userdetails b, loans c, loandocs d
		WHERE a.userID = b.userID
		AND b.userID = c.userID
		AND c.userID = d.userID
		AND c.status = 3 and year(d.ajkDate2) <= '2006' and month(d.ajkDate2) <= '12' 
		order by d.ajkDate2 desc, CAST( a.userID AS SIGNED INTEGER ) asc";

$rs = &$conn->Execute($sSQL);

$sFee = "SELECT 
		userID, 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE 
		deductID in (1595,1607)
		AND year( createdDate ) <= '2006'
		AND month( createdDate ) <= '12' GROUP BY userID order by CAST( userID AS SIGNED INTEGER )";

$rsFee = &$conn->Execute($sFee);

$arrFee = array();

if ($rsFee->RowCount() <> 0) {
	while (!$rsFee->EOF) {
		$userID = $rsFee->fields(userID);
		$arrFee[$userID] = $rsFee->fields(totalWajib);
		$rsFee->MoveNext();
	}
}

//print_r($arrFee);

$rpath = realpath("rptAuditBiaya.php");
$dpath = dirname($rpath);
$fname = trim($fname);
$fname = 'auditBiaya.csv';
$filename = $dpath . '/' . $fname;
$file = fopen($filename, 'w', 1);
fwrite($file, "Bil, Nomor Anggota, Nama, Tanggal Masuk Anggota, Jumlah yuran, Kod pembiayaan, Jumlah pembiayaan, Tempoh, Kadar Keuntunggan, Jumlah ansuran bulanan, Tanggal mohon, Tanggal lulus, Tanggal pengeluaran, Baki Pembiayaan, Jumlah Tunggakan, Caj perkhidmatan, Penjamin 1, Penjamin 2, Penjamin 3, Tanggal terima LO");
fwrite($file, "\r\n");

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
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#0c479d" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . ' Pada ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">';
/*Bil	Nomor Anggota	Nama	Tanggal menjadi anggota	
Jumlah Wajib Bulanan	Kod Pembiayaan	Jumlah Pembiayaan	Tempoh Pembiayaan	Kadar Keuntungan	Jumlah ansuran bulanan	

Tanggal mohon	Tanggal Lulus	----Tanggal pengeluaran	Baki Pembiayaan @30/11/2006	Jumlah Tunggakan-----

Tambahan	

caj perkhidmatan	
nama penjamin	
no anggota penjamin	
tarikh terima lo*/
print '
					<th nowrap>&nbsp;</th>
					<th nowrap align="left">&nbsp;Nomor Anggota</th>
					<th nowrap align="left">&nbsp;Nama</th>
					<th nowrap align="left">&nbsp;Tanggal Masuk Anggota</th>
					<th nowrap align="left">&nbsp;Jumlah yuran</th>
					<th nowrap align="left">&nbsp;Kod pembiayaan</th>
					<th nowrap align="left">&nbsp;Jumlah pembiayaan</th>
					<th nowrap align="left">&nbsp;Tempoh</th>
					<th nowrap align="left">&nbsp;Kadar Keuntunggan</th>
					<th nowrap align="left">&nbsp;Jumlah ansuran bulanan</th>
					<th nowrap align="left">&nbsp;Tanggal mohon</th>
					<th nowrap align="left">&nbsp;Tanggal lulus</th>
					<th nowrap align="left">&nbsp;Tanggal pengeluaran</th>
					<th nowrap align="left">&nbsp;Baki Pembiayaan</th>
					<th nowrap align="left">&nbsp;Jumlah Tunggakan</th>
					<th nowrap align="left">&nbsp;Caj perkhidmatan</th>
					<th nowrap align="left">&nbsp;Penjamin 1</th>
					<th nowrap align="left">&nbsp;Penjamin 2</th>
					<th nowrap align="left">&nbsp;Penjamin 3</th>
					<th nowrap align="left">&nbsp;Tanggal terima lo</th>
				</tr>';
$total = 0;
if ($rs->RowCount() <> 0) {
	$yr = date("Y");
	while (!$rs->EOF) {
		$totalFee = 0;
		$totalFee = $arrFee[$rs->fields(userID)];
		$add = str_replace("</pre>", " ", str_replace("<pre>", " ", $rs->fields(address)));
		$date = toDate("d/m/y", $rs->fields(approvedDate));
		if ($totalFee <= 0) $totalFee = '0.0';
		$bil++;
		// b.memberID, a.name, a.userID , c.loanType, c.loanAmt, c.loanPeriod, c.kadar_u, c.monthlyPymt, c.applyDate, d.ajkDate2,  b.approvedDate, d.btindihCaj, c.penjaminID1, c.penjaminID2, c.penjaminID3
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td align="left">&nbsp;' . $rs->fields(memberID) . '</td>
							<td align="left">&nbsp;' . $rs->fields(name) . '</td>
							<td align="center">&nbsp;' . $date . '</td>
							<td align="left">&nbsp;' . number_format($totalFee, 2) . '</td>

							<td align="left">&nbsp;' . dlookup("general", "code", "ID = " . $rs->fields(loanType)) . '-' . dlookup("general", "name", "ID = " . $rs->fields(loanType)) . '</td>
							<td align="left">&nbsp;' . number_format($rs->fields(loanAmt), 2) . '</td>
							<td align="center">&nbsp;' . $rs->fields(loanPeriod) . '</td>
							<td align="center">&nbsp;' . $rs->fields(kadar_u) . '</td>
							<td align="left">&nbsp;' . $rs->fields(monthlyPymt) . '</td>
							<td align="left">&nbsp;' . toDate("", $rs->fields(applyDate)) . '</td>
							<td align="left">&nbsp;' . toDate("", $rs->fields(ajkDate2)) . '</td>';

		//$baucerDate = toDate("d/m/y",$rs->fields(rcreatedDate) );
		//Full Texts  	no_baucer 	tarikh_baucer 	jenis 	no_bond 	
		/*
							if($baucerDate) {
							$bond = $rs->fields(rnoBond);
							$mm = date("m");
							$yr = date("Y");
							$bakiBiaya = '';
							//		AND deductID = '".$c_Deduct."' 
							$getOpen = "SELECT 
									SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
									SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
									FROM transaction
									WHERE
									pymtRefer = '".$bond."'
									AND month(createdDate) <= ".$mm."
									AND year(createdDate) <= ".$yr."
									GROUP BY pymtRefer";
							$rsOpen = $conn->Execute($getOpen);
							if ($rsOpen->RowCount() == 1) $bakiBiaya = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);

							}*/

		$sql = "";
		$sqlBond = "SELECT no_baucer, tarikh_baucer FROM `vauchers`where no_bond = '" . $rs->fields(rnoBond) . "'";
		$rsBond = &$conn->Execute($sqlBond);

		if ($rsBond->RowCount() > 1) $baucerDate = toDate("d/m/y", $rsBond->fields(tarikh_baucer));

		//$baucerDate = dlookup("vauchers", "tarikh_baucer", "no_bond = '".$rs->fields(rnoBond) ."'");
		//$baucerDate = toDate("d/m/y",$baucerDate);

		print '
							<td align="left">&nbsp;' . $baucerDate . '</td><!-- tarikh pengeluaran -->
							<td align="left">&nbsp;' . number_format($rs->fields(outstandingAmt), 2) . '</td><!-- baki pembiayaan -->
							<td align="left">&nbsp;</td><!-- jumlah tunggakan -->

							<td align="left">&nbsp;' . $rs->fields(btindihCaj) . '</td>';

		$pid1 = '';
		$pid2 = '';
		$pid3 = '';
		$npid1 = '';
		$npid2 = '';
		$npid3 = '';

		if ($pid1 = $rs->fields(penjaminID1)) $npid1 = dlookup("users", "name", "userID=" . $rs->fields(penjaminID1));
		if ($pid2 = $rs->fields(penjaminID2)) $npid2 = dlookup("users", "name", "userID=" . $rs->fields(penjaminID2));
		if ($pid3 = $rs->fields(penjaminID3)) $npid3 = dlookup("users", "name", "userID=" . $rs->fields(penjaminID3));

		$sqlSend = "SELECT userID, sendDate FROM `letterlog` where letterGroup = 2 and type='EMAIL' and userID = " . $rs->fields(userID) . " order by sendDate desc";
		$rsSend = &$conn->Execute($sqlSend);

		if ($rsSend->RowCount() > 1) $sendDate = toDate("d/m/y", $rsSend->fields(sendDate));

		print '
							<td align="left">&nbsp;' . $pid1 . '-' . $npid1 . '</td>
							<td align="left">&nbsp;' . $pid2 . '-' . $npid2 . '</td>
							<td align="left">&nbsp;' . $pid3 . '-' . $npid3 . '</td>
							<td align="left">&nbsp;' . $sendDate . '</td>
						</tr>';

		fwrite($file, $bil . ' , ');
		fwrite($file, $rs->fields(memberID) . ' , ');
		fwrite($file, $rs->fields(name) . ' , ');
		fwrite($file, $date . ' , ');
		fwrite($file, $totalFee . ' , ');
		fwrite($file, dlookup("general", "code", "ID = " . $rs->fields(loanType)) . '-' . dlookup("general", "name", "ID = " . $rs->fields(loanType)) . ' , ');
		fwrite($file, $rs->fields(loanAmt) . ' , ');
		fwrite($file, $rs->fields(loanPeriod) . ' , ');
		fwrite($file, $rs->fields(kadar_u) . ' , ');
		fwrite($file, $rs->fields(monthlyPymt) . ' , ');

		$dpohon = toDate("", $rs->fields(applyDate));
		$dlulus = toDate("", $rs->fields(ajkDate2));
		fwrite($file,  $dpohon . ' , ');
		fwrite($file,  $dlulus . ' , ');

		fwrite($file, $baucerDate . ' , ');
		fwrite($file, $rs->fields(outstandingAmt) . ' , ');
		fwrite($file, " " . ' , ');
		fwrite($file, $rs->fields(btindihCaj) . ' , ');

		fwrite($file, $pid1 . '-' . $npid1 . ' , ');
		fwrite($file, $pid2 . '-' . $npid2 . ' , ');
		fwrite($file, $pid3 . '-' . $npid3 . ' , ');
		fwrite($file, $sendDate . ' , ');
		fwrite($file, "\r\n");

		$total += $rs->fields(jumlah);
		$rs->MoveNext();
	}

	print '
					<tr bgcolor="FFFFFF"><td colspan="3"><hr size=1></td></tr>						
					<!--tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan  :</td>
						<td align="right">&nbsp;<b>' . number_format($total) . '</b>&nbsp;&nbsp;&nbsp;</td>
					</tr!-->';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="3" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>

	<tr><td>';
fclose($file);
$link =  '<a href="./' . $fname . '">' . $fname . '</a>';
print '&nbsp;<input type="button" name="Export" value="Export" class="but" onclick= "Javascript:(window.location.href=\'rptAuditBiaya.php?action=generate\')">';

if ($action == 'generate')	print '&nbsp;&nbsp;(Right click- save link as to download):&nbsp;' . $link;

print ' </td></tr>
	<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
