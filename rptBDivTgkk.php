<?php
#put this in the very beginning
//$timestart = microtime();
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAllFee.php
 *		   Description	:	Ringkasan Keseluruhan Yuran Anggota 
 *          Date 		: 	31/5/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$title  = 'Ringkasan Tunggakkan Dividen Anggota Pada Tahun ' . $yr;

$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status IN (1,4)";
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


$sSQLC = "SELECT * FROM dividen WHERE yearDiv = '" . $yr . "' AND status= '1'";
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
					<th width="5" align="center" nowrap>Bil</th>
					<th width="300" align="left" nowrap>&nbsp;Nombor Anggota- Nama</th>
					<th width="20" align="left" nowrap><div align="right">Dividen (RM)</div></th>
					<th width="30" align="left" nowrap><div align="right">Tunggakkan (RM)</div></th>
					<th width="30" align="left" nowrap><div align="right">Upfront (RM)</div></th>
				    <th width="300" align="center" nowrap><div align="left">Catatan</div></th>
				</tr>';
//$total = 0;




if ($rsCheck->RowCount() <= 0) {

	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
} else {

	if ($GetMember->RowCount() <> 0) {
		$bil = 1;
		while (!$GetMember->EOF) {


			$sSQL5556 = "SELECT * FROM dividen 
		WHERE 
		userID = '" . $GetMember->fields(userID) . "'
		AND yearDiv = '" . $yr . "'
		AND status= '1'";
			$rs6 = &$conn->Execute($sSQL5556);

			$feekiraMonth = $rs6->fields(AmtDiv);
			$totalFee = $rs6->fields(TgkknAmt);
			$totalShare = $rs6->fields(UpfrntAmt);

			if ($totalFee > 0) {


				print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td align="center">' . $bil . '&nbsp;</td>
							<td>&nbsp;' . $GetMember->fields(userID) . '-&nbsp;' . $GetMember->fields(name) . '</a></td>
							<td><div align="right">' . number_format($feekiraMonth, 2) . '</div></td>
							<td><div align="right">' . number_format($totalFee, 2) . '</div></td>
							<td align="right"><div align="right">' . number_format($totalShare, 2) . '&nbsp;</div></td>
							<td align="right"><div align="right">' . $rs6->fields(catatan) . '&nbsp;</div></td>
						</tr>';

				$totalAmtFeeD += $rs6->fields(TgkknAmt);
				$totalAmtShareD += $rs6->fields(UpfrntAmt);
				$total += $feekiraMonth;
				$bil++;
			}
			$GetMember->MoveNext();
		}
		print '
					<tr bgcolor="FFFFFF"><td colspan="6"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan  :</td>
						<td align="right"><b>' . number_format($total, 2) . '</b></td>
						<td align="right"><b>' . number_format($totalAmtFeeD, 2) . '</b></td>
						<td align="right">&nbsp;<b></b></td>
						<td align="right">&nbsp;<b></b></td>
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
