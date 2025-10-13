<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	rptE.php
*		   Description	:	Report Status Kelulusan Pembiayaan
*		   Parameter	:   $dateFrom , $dateTo
*          Date 		: 	12/12/2003
*********************************************************************************/
session_start();
include("common.php");	

$today = date("F j, Y");                 
if (get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}
$title  = 'Kelulusan Advance Payment';

$sSQL = "";
$sSQL = "SELECT a.*,b.*, DATEDIFF(a.applyDate,b.ajkDate2)as date1
		FROM loans a, loandocs b
		WHERE a.loanID = b.loanID
		AND b.result = 'lulus'
        AND a.statusL = 1
		AND (	b.ajkDate2	BETWEEN '".$dtFrom."'	AND '".$dtTo."')
		ORDER BY date1 ASC ";

$rs = &$conn->Execute($sSQL);
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>'.$emaNetis.'</title>
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="9" align="right">'.strtoupper($emaNetis).'</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="9" height="40"><font color="#FFFFFF">'.$title.'<br>
			Dari '.toDate("d/m/Y",$dtFrom).' Hingga '.toDate("d/m/Y",$dtTo).'</font>
		</th>
	</tr>
	<tr>
		<td colspan="9"><font size=1>Cetak pada : '.$today.'</font></td>
	</tr>
	<tr><td colspan="9">&nbsp;</td></tr>
	<tr>
		<td colspan="9">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>					
					<th nowrap>No Anggota</th>
					<th nowrap>Nama</th>
					<th nowrap>Alamat</th>
					<th nowrap>Nombor Telefon</th>
					<th nowrap>Emel</th>
					<th nowrap>No Rujukan ID</th>
					<th nowrap>Jenis Advance Payment</th>
					<th nowrap>Jumlah Advance Payment (RP)</th>					
					<th nowrap>Tarikh Memohon</th>
					<th nowrap>Tarikh Diluluskan</th>
					<th nowrap>Beza Kelulusan</th>
				</tr>';
if ($rs->RowCount() <> 0) {	
		while(!$rs->EOF) {	
		$bil++;
$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($rs->fields(userID), "Text"));		
$totalsum = $totalsum + $rs->fields('loanAmt');
$address = dlookup("userdetails", "address", "userID=" . tosql($rs->fields(userID), "Text"));
$address = str_replace(array('<pre>', '</pre>'), '', $address);
$postcode = dlookup("userdetails", "postcode", "userID=" . tosql($rs->fields(userID), "Text"));
$city = dlookup("userdetails", "city", "userID=" . tosql($rs->fields(userID), "Text"));
$stateID = dlookup("userdetails", "stateID", "userID=" . tosql($rs->fields(userID), "Text"));
$state = dlookup("general", "name", "ID=" . tosql($stateID, "Number"));
$alamat = $address .', '. $postcode .', '. $city .', '. $state;

print '

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td width="2%" align="right">'.$bil.')</td>
	<td align="center"></a>'.dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")).'</td>
	<td>'.dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text")).'</a></td>
	<td>'.$alamat.'</td>
	<td align="center">'.dlookup("userdetails", "mobileNo", "userID=" . tosql($rs->fields(userID), "Text")).'</a></td>
	<td align="center">'.dlookup("users", "email", "userID=" . tosql($rs->fields(userID), "Text")).'</a></td>
	<td align="center">'.$rs->fields(loanNo).'</td>
	<td>'.dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), "Number")).'</a></td>
	<td align="right">'.number_format($rs->fields('loanAmt'), 2).' </a></td>
	<td align="center">'.toDate("d/m/Y",$rs->fields(applyDate)).'</a></td>
	<td align="center">'.toDate("d/m/Y",$rs->fields(ajkDate2)).'</a></td>
	<td align="center">'.$rs->fields(date1).'</a></td>
</tr>';
					$rs->MoveNext();
					}	
						print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="8">Jumlah Keseluruhan:</td>
							<td align="right">'.number_format($totalsum,2).'</td>
							<td colspan="3">&nbsp;</td>
						</tr>';
					
					} else {
					print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="12" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
				}
print '		</table> 
		</td>
	</tr>	
</table>
</body>
</html>
<tr><td colspan="12">&nbsp;</td></tr>
<center><tr><td colspan="12"><font size="1" color="#999999"><b>'.$retooFetis.'</b></font></td></tr></center>';
?>