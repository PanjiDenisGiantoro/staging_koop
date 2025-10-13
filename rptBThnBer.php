<?php

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
$title  = 'Ringkasan Dividen Anggota Mengikut Pegangan Wajib Pada Tahun ' . $yr;

$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status IN (4)";;
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
					<th width="30" align="left" nowrap><div align="right">Nombor Anggota</th>
					<th width="180" align="left" nowrap>Nama</th>
					<th width="80" align="left" nowrap><div align="right">Syer Terkumpul </th>
					<th width="80" align="left" nowrap><div align="right">Yuran Awal Tahun ' . $yr . ' </th>
					<th width="80" align="left" nowrap><div align="right">Yuran Akhir Tahun ' . $yr . ' </th>
					<th width="70" align="left" nowrap><div align="right">Dividen ' . $rsCheck->fields(amtFee) . '% </div>
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

			$bil++;
			$totalAmtFeeD += $totalFee;
			$totalAmtShareD += $totalShare;
			$total += $feekiraMonth;

			$sSQL5556 = "SELECT * FROM dividen 
										WHERE 
										userID = " . $GetMember->fields(userID) . "
										AND yearDiv = " . $yr . " ";
			$rs6 = &$conn->Execute($sSQL5556);

			$feekiraMonth = getFeesAwalthn($GetMember->fields(userID), $yr);
			$totalFee = $rs6->fields(AmtDiv);
			$totalShare = $rs6->fields(AmtShareD);
			$yy = $yr + 1;
			$feekiraMonthAkhir = getFeesAwalthn($GetMember->fields(userID), $yy);


			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td align="center">' . $bil . '&nbsp;</td>
							<td><div align="right">' . $GetMember->fields(userID) . '</div></td>
							<td>' . $GetMember->fields(name) . '</a></td>
							<td><div align="right">' . number_format($totalShare, 2) . '</div></td>
							<td><div align="right">' . number_format($feekiraMonth, 2) . '</div></td>
							<td><div align="right">' . number_format($feekiraMonthAkhir, 2) . '</div></td>
							<td><div align="right">' . number_format($totalFee, 2) . '</div></td>
						</tr>';


			$GetMember->MoveNext();
		}
		print '
					<tr bgcolor="FFFFFF"><td colspan="7"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan  :</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
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
