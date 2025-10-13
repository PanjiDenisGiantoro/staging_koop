<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *********************************************************************************/
include("common.php");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
	exit;
}

$title  = 'Penyata Invois Bagi Bulan/Tahun ' . substr($yrmth, 4, 2) . '/' . substr($yrmth, 0, 4);
$title = strtoupper($title);

$sSQL = "";
$sSQL = "select * from transactionacc 
		 WHERE  pymtRefer = " . tosql($id, "Text") . "
		 and deductid in (14,15,17,40)
		 AND year(createdDate) = " . substr($yrmth, 0, 4) . "
		 AND month(createdDate) = " . substr($yrmth, 4, 2) . "
		 ORDER BY createdDate";
$rs = &$conn->Execute($sSQL);

$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transactionacc
		WHERE
		deductID in (14,15,17,40)
		AND pymtRefer = '" . $id . "' 
		AND year(createdDate) <= " . substr($yrmth, 0, 4) . "
		AND month(createdDate) < " . substr($yrmth, 4, 2) . "
		GROUP BY pymtRefer";
$rsOpen = $conn->Execute($getOpen);
if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
else $bakiAwal = 0;
$bakiAkhir = 0;

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/mail.css" >
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="2" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="2" height="40"><font color="#000000"><b>' . $title . '</b></font>
		</th>
	</tr>
	<tr>
		<td colspan="2"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;No&nbsp;Syarikat</td>
		<td>:&nbsp;' . dlookup("generalacc", "code", "ID=" . tosql($id, "Text")) . '</td>
  </tr>
  
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Nama&nbsp;Syarikat</td>
		<td>:&nbsp;' . dlookup("generalacc", "name", "ID=" . tosql($id, "Text")) . '</td>
	</tr>
	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" bgcolor="FFFFFF">
		<td width="20%">&nbsp;Alamat&nbsp;Syarikat</td>
		<td>:&nbsp;' . dlookup("generalacc", "b_Baddress", "ID=" . tosql($id, "Text")) . '</td>
  </tr>
	
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			<table cellpadding="2" cellspacing="0" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>Tarikh</th>
					<th nowrap>&nbsp;Nombor Invois</th>
					<th nowrap>&nbsp;Akaun</th>
					<th nowrap>&nbsp;Debit(RP)</th>
					<th nowrap>&nbsp;Kredit(RP)</th>
					<th nowrap>&nbsp;Baki(RP)</th>

				</tr>
				
				<tr><td colspan="5">&nbsp;</td></tr>';

$totaldebit = 0;
$totalkredit = 0;
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$debit = '';
		$kredit = '';
		if ($rs->fields(addminus) == 0) {
			$debit = $rs->fields(pymtAmt);
			$totaldebit += $debit;
			$debit = number_format($debit, 2);
		} else {
			$kredit = $rs->fields(pymtAmt);
			$totalkredit += $kredit;
			$kredit = number_format($kredit, 2);
		}
		$deductid = $rs->fields(deductID);
		print '
						
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="5%" align="center">&nbsp;' . toDate('d/m/y', $rs->fields(createdDate)) . '&nbsp;</td>
							<td width="10%">&nbsp;' . $rs->fields(docNo) . '</td>
							<td width="50%" align="left">&nbsp;' . dlookup("generalacc", "name", "ID=" . tosql($rs->fields(deductID), "Number")) . '&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . $debit . '&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . $kredit . '&nbsp;</td>
							<td width="10%" align="right">&nbsp;' . $debit . '&nbsp;</td>
						</tr>';
		$rs->MoveNext();
	}

	print '
				<tr><td colspan="5">&nbsp;</td></tr>

					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight:bold;" bgcolor="FFFFFF">
						<td width="10%" colspan=2 align="right">&nbsp;</td>
						<td width="60%" align="left"><b>Jumlah</b>&nbsp;&nbsp;&nbsp;</td>
						<td width="10%" align="right">&nbsp;<b>' . number_format($totaldebit, 2) . '</b>&nbsp;</td>
						<td width="10%" align="right">&nbsp;<b>' . number_format($totalkredit, 2) . '</b>&nbsp;</td>
						<td width="10%" align="right">&nbsp;<b>' . number_format($totaldebit, 2) . '</b>&nbsp;</td>
					</tr>
					
					';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" align="center"><b>- Tiada Rekod -</b></td>
					</tr>';
}


print '		</table> 
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>';

print '	
</table>
</body>
</html>';
