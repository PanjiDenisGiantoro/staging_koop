<?php
#put this in the very beginning
//$timestart = microtime();
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptBThn.php
 *		   Description	:	Ringkasan Keseluruhan Wajib Anggota 
 *          Date 		: 	31/5/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");
if (!isset($yr)) $yr	= date("Y");
//$yr = (int)substr($yrmth,0,4);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Keseluruhan Dividen Anggota Mengikut Pegangan Wajib Pada Tahun ' . $yr;

$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status IN (1,4)";;

if ($ID) {
	$sWhere .= " AND b.userID = " . tosql($ID, "Text");
}

$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b  ";
//$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER ) ASC";
$GetMember = &$conn->Execute($sSQL);


$yyr = $yr - 1;
$sSQLC = "SELECT * FROM dividen WHERE yearDiv = " . $yr . " ";
$rsCheck = &$conn->Execute($sSQLC);

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
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
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
					<th width="4" align="center" nowrap>Bil</th>
					<th width="40" align="left" nowrap><div align="right">Nomor Anggota </th>
					<th width="40" align="left" nowrap><div align="right">Nombor IC </th>
					<th width="180" align="left" nowrap>Nama</th>
					<th width="40" align="left" nowrap><div align="right">Nombor Akaun Tabungan </th>
					<th width="80" align="left" nowrap><div align="right">Pokok Terkumpul </th>
					<th width="80" align="left" nowrap><div align="right">Wajib Awal Tahun ' . $yr . ' </th>
					<th width="80" align="left" nowrap><div align="right">Wajib Akhir Tahun ' . $yr . ' </th>
					<th width="70" align="left" nowrap><div align="right">Dividen ' . $rsCheck->fields(amtFee) . '% </div>
					<th width="70" align="left" nowrap><div align="right">Tunggakkan (RP)</div>
					<th width="70" align="left" nowrap><div align="right">Upfront (RP)</div>
					<th width="70" align="left" nowrap><div align="right">Dividen Bersih (RP)</div>
				</tr>';
//$total = 0;




if ($rsCheck->RowCount() <= 0) {

	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
} else {

	if ($GetMember->RowCount() <> 0) {

		$total = 0;
		$totalAmtFeeD = 0;
		$totalAmtShareD = 0;

		while (!$GetMember->EOF) {
			$yy = $yr + 1;
			$getWajibOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		deductID in (1595,1780,1607) 
		AND userID = '" . $GetMember->fields(userID) . "' 
		AND year(createdDate) < 2018
		GROUP BY userID";

			$rsWajibOpen = $conn->Execute($getWajibOpen);
			if ($rsWajibOpen->RowCount() == 1) $bakiAwal = $rsWajibOpen->fields(yuranKt) - $rsWajibOpen->fields(yuranDb);
			else $bakiAwal = 0;
			//$bakiAkhir = 0;

			$getWajibOpenAkhir = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		deductID in (1595,1780,1607) 
		AND userID = '" . $GetMember->fields(userID) . "' 
		AND year(createdDate) < 2018
		GROUP BY userID";

			$rsWajibOpenAkhir = $conn->Execute($getWajibOpenAkhir);
			if ($rsWajibOpenAkhir->RowCount() == 1) $bakiAkhir = $rsWajibOpenAkhir->fields(yuranKt) - $rsWajibOpenAkhir->fields(yuranDb);
			else $bakiAkhir = 0;
			//$bakiAkhir = 0;

			$bil++;
			$totalAmtFeeD += $totalFee;
			$totalAmtShareD += $totalShare;
			$total += $feekiraMonth;
			$totalFeeAllD += $totalFeeAll;
			$totalTgkkD += $totalTgkk;
			$totalUpfrontD += $totalUpfront;

			$sSQL5556 = "SELECT * FROM dividen 
										WHERE 
										userID = " . $GetMember->fields(userID) . "
										AND yearDiv = " . $yr . " ";
			$rs6 = &$conn->Execute($sSQL5556);


			//	$feekiraMonth = getFeesAwalthn($GetMember->fields(userID), $yr);
			$totalFee = $rs6->fields(AmtDiv);
			$totalShare = $rs6->fields(AmtShareD);

			//	$feekiraMonthAkhir = getFeesAwalthn($GetMember->fields(userID), $yy);
			$totalTgkk = $rs6->fields(TgkknAmt);
			$totalUpfront = $rs6->fields(UpfrntAmt);
			//	$totalSharesAwl = getSharesDiv($GetMember->fields(userID), $yy);
			$totalFeeAll = $totalFee + $totalTgkk + $totalUpfront;

			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td align="center">' . $bil . '&nbsp;</td>
							<td><div align="right">' . $GetMember->fields(userID) . '</div></td>
							<td><div align="right">' . $GetMember->fields(newIC) . '</div></td>
							<td>' . $GetMember->fields(name) . '</a></td>
							<td><div align="right">' . $GetMember->fields(accTabungan) . '</div></td>
							<td><div align="right">' . number_format($totalShare, 2) . '</div></td>
							
							
							
							
							<td><div align="right">' . number_format($bakiAwal, 2) . '</div></td>
							
							
							
							
							
							<td><div align="right">' . number_format($bakiAkhir, 2) . '</div></td>
							<td><div align="right">' . number_format($totalFeeAll, 2) . '</div></td>
							<td><div align="right">' . number_format($totalTgkk, 2) . '</div></td>
							<td><div align="right">' . number_format($totalUpfront, 2) . '</div></td>
							<td><div align="right">' . number_format($totalFee, 2) . '</div></td>
						</tr>';


			$GetMember->MoveNext();
		}
		print '
					<tr bgcolor="FFFFFF"><td colspan="12"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan  :</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;' . number_format($totalFeeAllD, 2) . '</td>
						<td align="right">&nbsp;' . number_format($totalTgkkD, 2) . '</td>
						<td align="right">&nbsp;' . number_format($totalUpfrontD, 2) . '</td>
						<td align="right">' . number_format($totalAmtFeeD, 2) . '</td>
					</tr>';
	} else {
		print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
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
</table>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';

#put this in the very end
//$timeend = microtime();
//$diff = number_format(((substr($timeend,0,9)) + (substr($timeend,-10)) - (substr($timestart,0,9)) - (substr($timestart,-10))),4);
//echo "<br><br><small><small>script generation took $diff s </small></small>";
