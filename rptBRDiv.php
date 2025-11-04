<?php
#put this in the very beginning
//$timestart = microtime();
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
$yr = (int)substr($yrmth, 0, 4);
$month = (int)substr($yrmth, 4, 2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Dividen Anggota Mengikut Pegangan Wajib Pada Bulan&nbsp;' . displayBulan($month) . '&nbsp; Tahun ' . $yr . '&nbsp;Bagi Resit';

$sSQLC = "SELECT * FROM dividen WHERE SUBSTRING(startYear,5,6) = " . $month . "
        AND SUBSTRING(startYear,1,4) = " . $yr . "
		AND status= '2' ";
$rsCheck = &$conn->Execute($sSQLC);

$sSQL555 = "SELECT DISTINCT  d.name, a.UserID, a.yrmth, b.memberID, b.newIC,
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction a, userdetails b, resit c , users d
		WHERE
		a.deductID in (1595,1596,1607)
		AND a.userID = b.userID
		AND d.userID = a.userID
		AND a.docNo = c.no_resit
		AND month(c.tarikh_resit) = " . $month . "
        AND year(c.tarikh_resit) = " . $yr . "
		GROUP BY a.userID
		order by CAST( b.memberID AS SIGNED INTEGER ) ASC ";
$GetMember = &$conn->Execute($sSQL555);




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
		<th height="40"><font color="#FFFFFF">' . $title . ' 
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th width="4" align="centre" nowrap>Bil</th>
					<th width="150" align="left" nowrap>&nbsp;Nomor Anggota- Nama</th>
					<th width="100" align="left" nowrap><div align="right">Yuran Bulan(RP) </div></th>
					<th width="90" align="left" nowrap><div align="right">Dividen Pokok (RP) ' . $rsCheck->fields(amtFee) . '% </div></th>
					<th nowrap align="center" width="90"><div align="right">Dividen Tabungan (RP) ' . $rsCheck->fields(amtShare) . '%</div></th>
				</tr>';
//$total = 0;




if ($rsCheck->RowCount() <= 0) {

	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
} else {

	if ($GetMember->RowCount() <> 0) {
		while (!$GetMember->EOF) {


			$sSQL5556 = "SELECT * FROM dividen 
		WHERE 
		userID = " . $GetMember->fields(memberID) . "
		AND SUBSTRING(startYear,5,6) = " . $month . "
        AND SUBSTRING(startYear,1,4) = " . $yr . "
		AND status= '2' ";
			$rs6 = &$conn->Execute($sSQL5556);


			$feekiraMonth = $rs6->fields(AmtYuranT);
			//$feeMonth = number_format($feeKT - $feeDB,2);
			$bil++;
			$totalFee = $rs6->fields(AmtFeeD);
			$totalShare = $rs6->fields(AmtShareD);


			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="34" align="center">' . $bil . '&nbsp;</td>
							<td>&nbsp;' . $GetMember->fields(memberID) . '-&nbsp;' . $GetMember->fields(name) . '</a></td>
							<td><div align="right">' . number_format($feekiraMonth, 2) . '</div></td>
							<td><div align="right">' . number_format($totalFee, 2) . '</div></td>
							<td align="right"><div align="right">' . number_format($totalShare, 2) . '&nbsp;</div></td>
						</tr>';
			$totalAmtFeeD += $rs6->fields(AmtFeeD);
			$totalAmtShareD += $rs6->fields(AmtShareD);
			$total += $feekiraMonth;
			$GetMember->MoveNext();
		}
		print '
					<tr bgcolor="FFFFFF"><td colspan="5"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan  :</td>
						<td align="right"><b>' . number_format($total, 2) . '</b></td>
						<td align="right"><b>' . number_format($totalAmtFeeD, 2) . '</b></td>
						<td align="right">&nbsp;<b>' . number_format($totalAmtShareD, 2) . '</b>&nbsp;&nbsp;&nbsp;</td>
					</tr>';
	} else {
		print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak fff-</b></td>
					</tr>';
	}
	/*	
				if($arrTotal){
					
					foreach($arrTotal as $key => $value){
						$bil++;		
						print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="34" align="right">'.$bil.')&nbsp;</td>
							<td colspan="3">&nbsp;'.$key.'-&nbsp;'.$arrName[$key].'</a></td>
							<td align="right">&nbsp;'.number_format($value,2).'</a>&nbsp;&nbsp;&nbsp;</td>
						</tr>';
					}
					
					print '
					<tr bgcolor="FFFFFF"><td colspan="5"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="4" align="right">Jumlah Keseluruhan  :</td>
						<td align="right">&nbsp;<b>'.number_format($total,2).'</b>&nbsp;&nbsp;&nbsp;</td>
					</tr>';

				}else{

					print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';

				}*/
}

print '		</table> 
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';

#put this in the very end
//$timeend = microtime();
//$diff = number_format(((substr($timeend,0,9)) + (substr($timeend,-10)) - (substr($timestart,0,9)) - (substr($timestart,-10))),4);
//echo "<br><br><small><small>script generation took $diff s </small></small>";
