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
$sSQL = "select
		a.userID,
		b.memberID, a.name, b.approvedDate , b.totalFee , b.address, b.postcode, b.city, b.homeNo, b.mobileNo, b.w_name1, b.w_ic1,
		b.w_relation1, b.w_address1 from users a, userdetails b
		where a.userID = b.userID and b.status in (1,3) and year(b.approvedDate) <= '2006'
		order by CAST( b.memberID AS SIGNED INTEGER )";

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

$rpath = realpath("rptAuditAnggota.php");
$dpath = dirname($rpath);
$fname = trim($fname);
$fname = 'auditAnggota.csv';
$filename = $dpath . '/' . $fname;
$file = fopen($filename, 'w', 1);

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
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap align="left">&nbsp;Nomor Anggota</th>
					<th nowrap align="left">&nbsp;Nama</th>
					<th nowrap align="left">&nbsp;Tanggal Masuk Anggota</th>
					<th nowrap align="left">&nbsp;Jumlah yuran</th>
					<th nowrap align="left">&nbsp;Alamat</th>
					<th nowrap align="left">&nbsp;Poskod</th>
					<th nowrap align="left">&nbsp;Bandar</th>
					<th nowrap align="left">&nbsp;Tel Rumah</th>
					<th nowrap align="left">&nbsp;Tel. Bimbit</th>
					<th nowrap align="left">&nbsp;Nama Waris</th>
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
		//b.memberID, a.name, b.approvedDate , b.totalFee , b.address, b.postcode, b.city, b.homeNo, b.mobileNo, b.w_name1, b.w_ic1,b.w_relation1, b.w_address1
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td align="left">&nbsp;' . $rs->fields(memberID) . '</td>
							<td align="left">&nbsp;' . $rs->fields(name) . '</td>
							<td align="center">&nbsp;' . $date . '</td>
							<td align="left">&nbsp;' . number_format($totalFee, 2) . '</td>
							<td align="left">&nbsp;' . $add . '</td>
							<td align="left">&nbsp;' . $rs->fields(postcode) . '</td>
							<td align="left">&nbsp;' . $rs->fields(city) . '</td>
							<td align="left">&nbsp;' . $rs->fields(homeNo) . '</td>
							<td align="left">&nbsp;' . $rs->fields(mobileNo) . '</td>
							<td align="left">&nbsp;' . $rs->fields(w_name1) . '</td>

						</tr>';
		//$dataRow = "$bil $rs->fields(memberID) $rs->fields(name) $date $totalFee $add $rs->fields(postcode) $rs->fields(city) $rs->fields(homeNo) $rs->fields(homeNo) $rs->fields(mobileNo) $rs->fields(w_name1)";
		//$name = '"'.str_replace('"', ' ', $rs->fields(name)).'"';
		$add = str_replace('"', ' ', $add);
		$add = str_replace(',', ' ', $add);
		$add = str_replace("\n", "", $add);
		$add = nl2br($add);
		$add = explode("<br />", $add);
		$tot = count($add);
		for ($i = 0; $i <= $tot; $i++) {
			$add[$i] = trim($add[$i]);
		}
		//print_r($add);

		$add = implode(" ", $add);
		//$add = str_replace("<br />", " ", $add);
		//$add = '"'.$add."'";
		//$w_name1  = str_replace('"', ' ', $rs->fields(w_name1));
		//$w_name1  = '"'.$w_name1."'";

		//$dataRow = $bil.", ".$rs->fields(memberID).", ".$name.", ".$date.", ".$totalFee.", ".$add.", ".$rs->fields(postcode).", ".$rs->fields(city).", ".$rs->fields(homeNo).", ".$rs->fields(mobileNo).", ".$w_name1;

		//fwrite($file, $dataRow);	
		fwrite($file, $bil . ' , ');
		fwrite($file, $rs->fields(memberID) . ' , ');
		fwrite($file, $rs->fields(name) . ' , ');
		fwrite($file, $date . ' , ');
		fwrite($file, $totalFee . ' , ');
		fwrite($file, '"' . $add . '" , ');
		fwrite($file, $rs->fields(postcode) . ' , ');
		fwrite($file, $rs->fields(city) . ' , ');
		fwrite($file, $rs->fields(homeNo) . ' , ');
		fwrite($file, $rs->fields(mobileNo) . ' , ');
		fwrite($file, $rs->fields(w_name1));
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
print '&nbsp;<input type="button" name="Export" value="Export" class="but" onclick= "Javascript:(window.location.href=\'rptAuditAnggota.php?action=generate\')">';

if ($action == 'generate')	print '&nbsp;&nbsp;(Right click- save link as to download):&nbsp;' . $link;

print ' </td></tr>
	<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
