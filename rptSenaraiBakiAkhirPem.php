<?php
#put this in the very beginning
//$timestart = microtime();
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAllFee.php
 *		   Description	:	Ringkasan Keseluruhan Yuran Anggota 
 *          Date 		: 	31/5/2006
 *********************************************************************************/
//include("header.php");
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");
//$month = (int)substr($yrmth,4,2);
$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);
$IdJnsPinjm =  $_REQUEST['id'];
$yr1 = $yr + 1;
$mth1 = $mth + 1;

$sSQL = "SELECT ID, name, c_Deduct FROM general Where ID= '" . $IdJnsPinjm . "'";
$rs2 = &$conn->Execute($sSQL);
$JnsPinjaman = $rs2->fields(name);
$DeductID = $rs2->fields(c_Deduct);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Baki Akhir Keanggotaan Bagi Pembiayaan Tunai Tahun ' . $yr . ' bulan ' . $mth;


$sSQL = "SELECT a.loanNo, b.rnoBond, a.userID AS Anggota, c.name as nama, a.loanAmt AS Debit, b.ajkDate2 AS DateApprovedAJK
FROM  `loans` a, loandocs b, users c
WHERE a.loanID = b.loanID
AND a.userID = c.userID
AND a.status = 3
AND a.loanType
IN ( 1540, 1703, 1710, 1828 ) 
ORDER BY CAST( a.userID AS SIGNED INTEGER )";


$rs = &$conn->Execute($sSQL);

$sSQL = "";
$sWhere = " a.loanID = b.loanID 
	AND a.loanType IN (1540,1703,1710,1828)
	AND a.status IN (3)
	AND a.userID = c.userID ";

$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT a.userID, c.name, a.*, b.*
			 FROM loans a, loandocs b, users c";
$sSQL = $sSQL . $sWhere . " order by CAST( c.userID AS SIGNED INTEGER ) ASC";
$GetMember = &$conn->Execute($sSQL);


print '
<html>
<head>
	<title>' . $emaNetis . '</title>
	<!--LINK rel="stylesheet" href="images/default.css" -->	
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />        
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%" class="table table-sm table-striped">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada :' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table width="100%" border=0 align="center"  cellpadding="2" cellspacing="1">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap align="center">Bil</td>
					<td nowrap align="center">Nombor Anggota </td>
					<td width="852" align="left" nowrap>Nama</td>
					<td align="center">Nombor Bond</td>
					<td width="196" align="right" nowrap>Baki Pembiayaan Tunai(RM)</td>
			    </tr>';
if ($GetMember->RowCount() <> 0) {
	$bil = 1;
	while (!$GetMember->EOF) {
		//$totalFee = $arrTotal[$rs->fields(userID)];
		$bond = $GetMember->fields(rnoBond);
		$bakiAwalTunai = getBakiTunai($GetMember->fields(userID), $yrmth2, $bond);
		//$BakiAkhir = ($rs->fields(yuranDb) - $rs->fields(yuranKt));
		//if ($bakiAwalTunai > 0 ) {

		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="31" align="center">' . $bil . '</td>
							<td width="96" align="center">' . $GetMember->fields(userID) . '</td>
							<td align="left">' . $GetMember->fields(name) . '</td>
							<td align="center">' . $bond . '</td>
							<td align="right">' . $bakiAwalTunai . '</td>
					    </tr>';

		//}//$total +=$rs->fields(jum);
		$JumBakiAkhir += ($bakiAwalTunai);
		$GetMember->MoveNext();

		$bil = $bil + 1;
	}
	print '
					<tr bgcolor="FFFFFF"><td colspan="4"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td align="right"></td>
						<td align="right"></td>
						<td align="right">Jumlah Keseluruhan (RM) : </td>
						<td align="right">' . number_format($JumBakiAkhir, 2) . '</td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="4" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}


print '		</table> 
		</td>
	</tr>
	
</table>
</body>
</html>
<tr><td>&nbsp;</td></tr>
	<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
