<?php
#put this in the very beginning
//$timestart = microtime();
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptSUMDIV.php
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
if (!isset($yr)) $yr	= date("Y");
//$yr = (int)substr($yrmth,0,4);

$title  = 'Ringkasan Dividen Anggota Mengikut Pegangan Wajib dan Tabungan Pada Tahun ' . $yr;

$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status <> 0";;
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.memberID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '%" . $q . "%'";
	}
}

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
$sSQLC = "SELECT * FROM dividenyear WHERE YEAR = " . $yr . " ";
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
					<th width="4" align="center" nowrap>Bil</th>
					<th width="150" align="left" nowrap>&nbsp;Nomor Anggota- Nama</th>
					<th width="40" align="left" nowrap><div align="right">Dividen Tahun (Yuran) ' . $yr . ' &nbsp;' . $rsCheck->fields(yuranRate) . '% </div></th>
					<th width="30" align="left" nowrap><div align="right">Dividen Tahun (Tabung) ' . $yr . '&nbsp;' . $rsCheck->fields(TbgRate) . '% </div></th>
					<th width="45"><div align="right">Jumlah Pembayaran Dividen ' . $yr . ' (RP)</div></th>
				</tr>';
//$total = 0;




if ($rsCheck->RowCount() <= 0) {

	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
} else {

	if ($GetMember->RowCount() <> 0) {

		$totalDivY = 0;
		$totalDivT = 0;
		//$totalDivBln = 0;
		//$totalDivR = 0;
		$totalDIVIDEN = 0;
		$bil = 1;

		while (!$GetMember->EOF) {


			$sSQLDividenYearYuran = "SELECT *
			FROM dividenyear
			WHERE YEAR = " . $yr . "
			AND UserID = " . $GetMember->fields(userID) . " ";
			$rsDividenYear = &$conn->Execute($sSQLDividenYearYuran);

			$TotalDivYearYuran = $rsDividenYear->fields(yuranAmtIssue);
			$TotalDivYearTbg = $rsDividenYear->fields(TbgAmtIssue);

			/*	
			
			$sSQLDividenResit = "SELECT SUM(AmtFeeD)as AmaunSaham
			FROM dividen
			WHERE SUBSTRING(startYear,1,4) = ".$yr."
			AND UserID = ".$GetMember->fields(userID)."
			AND status = '2'
			Group By UserID ";
 			$rsDividenResit = &$conn->Execute($sSQLDividenResit);
            
			$TotalDivResit = $rsDividenResit->fields(AmaunSaham);
			
			
			$sSQLDividenBulan = "SELECT SUM(AmtFeeD)as AmaunSaham
			FROM dividen
			WHERE SUBSTRING(startYear,1,4) = ".$yr."
			AND UserID = ".$GetMember->fields(userID)."
			AND status ='1'
			Group By UserID ";
 			$rsDividenBulan = &$conn->Execute($sSQLDividenBulan);
            
			$TotalDivBulan = $rsDividenBulan->fields(AmaunSaham);
		
		*/

			$TOTAL = ($TotalDivYearYuran + $TotalDivYearTbg);




			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td class="Data" align="center">' . $bil . '</td>
						<td >' . $GetMember->fields('memberID') . ' - ' . $GetMember->fields(name) . '</td>
						<td align="right" >' . number_format($TotalDivYearYuran, 2) . '</td>						
						<td align="right">' . number_format($TotalDivYearTbg, 2) . '&nbsp;</td>
					    <td align="right">' . number_format($TOTAL, 2) . '&nbsp;</td>
					</tr>';

			$bil++;
			$totalDivY += $TotalDivYearYuran;
			$totalDivT += $TotalDivYearTbg;
			//$totalDivBln += $TotalDivBulan ;
			//$totalDivR += $TotalDivResit ;
			$totalDIVIDEN += $TOTAL;

			$GetMember->MoveNext();
		}
		print '
					<tr bgcolor="FFFFFF"><td colspan="5"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan  :</td>
						<td align="right">' . number_format($totalDivY, 2) . '</td>
						<td align="right">' . number_format($totalDivT, 2) . '</td>
						<td align="right">&nbsp;<b>' . number_format($totalDIVIDEN, 2) . '</b>&nbsp;&nbsp;&nbsp;</td>
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
	<tr><td>&nbsp;</td></tr>
	<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';

#put this in the very end
//$timeend = microtime();
//$diff = number_format(((substr($timeend,0,9)) + (substr($timeend,-10)) - (substr($timestart,0,9)) - (substr($timestart,-10))),4);
//echo "<br><br><small><small>script generation took $diff s </small></small>";
