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
$today = date("F j, Y");
$yr = (int)substr($yrmth, 0, 4);
$month = (int)substr($yrmth, 4, 2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Dividen Anggota Mengikut Pegangan Wajib Pada Bulan ' . displayBulan($month) . ' Tahun ' . $yr;

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


$sSQLC = "SELECT * FROM dividen WHERE SUBSTRING(startYear,5,6) = " . $month . "
        AND SUBSTRING(startYear,1,4) = " . $yr . " 
		AND status= '1'";
$rsCheck = &$conn->Execute($sSQLC);


print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<div style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;" align="right">
		' . strtoupper($emaNetis) . '
	</div>
	<h5 class="card-title mb-4" style="background-color: #A9D7CB; padding: 10px; font-weight: bold;" align="center">' . strtoupper($title) . '</h5>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	
	<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped" style="font-size: 10pt;">
	<tr class="table-primary">
					<td width="5" align="center" nowrap><b>Bil<b></td>
					<td width="250" align="left" nowrap><b>Nomor Anggota- Nama<b></td>
					<td width="100" align="left" nowrap><div align="right"><b>Yuran Bulanan (RP)<b></div></td>
					<td width="70" align="left" nowrap><div align="right"><b>Bayaran Dividen Pokok (RP)' . $rsCheck->fields(amtFee) . '%<b></div></td>
					<td nowrap align="center" width="70"><div align="right"><b>Bayaran Dividen Tabungan (RP)  ' . $rsCheck->fields(amtShare) . '%<b></div></td>
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
		userID = " . $GetMember->fields(userID) . "
		AND SUBSTRING(startYear,5,6) = " . $month . "
        AND SUBSTRING(startYear,1,4) = " . $yr . " 
		AND status= '1'";
			$rs6 = &$conn->Execute($sSQL5556);




			$feekiraMonth = $rs6->fields(AmtYuranT);
			//$feeMonth = number_format($feeKT - $feeDB,2);
			$bil++;
			$totalFee = $rs6->fields(AmtFeeD);
			$totalShare = $rs6->fields(AmtShareD);


			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="34" align="center">' . $bil . '&nbsp;</td>
							<td>&nbsp;' . $GetMember->fields(userID) . '-&nbsp;' . $GetMember->fields(name) . '</a></td>
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
