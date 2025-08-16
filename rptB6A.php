<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	rptB6A.php
*		   Description	:	Report Status Keseluruhan Advance Payment
*********************************************************************************/
session_start();
include("common.php");	

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y");                 
if (get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}
$title  = 'Keseluruhan Advance Payment';

$sSQL = "";
$sSQL = "SELECT a.*, a.status AS statusAP, b.* FROM loans a, loandocs b
		WHERE a.loanID = b.loanID
        AND a.statusL = 1
		AND (a.applyDate BETWEEN '".$dtFrom."' AND '".$dtTo."')
		ORDER BY a.applyDate ASC ";

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
		<td colspan="8" align="right">'.strtoupper($emaNetis).'</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="8" height="40"><font color="#FFFFFF">'.$title.'<br>
			Dari '.toDate("d/m/Y",$dtFrom).' Hingga '.toDate("d/m/Y",$dtTo).'</font>
		</th>
	</tr>
	<tr>
		<td colspan="8"><font size=1>Cetak pada : '.$today.'</font></td>
	</tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td colspan="8">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>					
					<th nowrap>No Anggota</th>
					<th nowrap>Nama</th>
					<th nowrap>Alamat</th>
					<th nowrap>Nombor Telefon</th>
					<th nowrap>Emel</th>
					<th nowrap>No Rujukan</th>
					<th nowrap>Jenis Advance Payment</th>
					<th nowrap>Jumlah Permohonan (RM)</th>		
					<th nowrap>Jenis Pemprosesan</th>			
					<th nowrap>Tarikh Permohonan</th>
					<th nowrap>Tarikh Kelulusan</th>
				</tr>';
if ($rs->RowCount() <> 0) {	
		while(!$rs->EOF) {	
		$bil++;
		$address = dlookup("userdetails", "address", "userID=" . tosql($rs->fields(userID), "Text"));
		$address = str_replace(array('<pre>', '</pre>'), '', $address);
		$postcode = dlookup("userdetails", "postcode", "userID=" . tosql($rs->fields(userID), "Text"));
		$city = dlookup("userdetails", "city", "userID=" . tosql($rs->fields(userID), "Text"));
		$stateID = dlookup("userdetails", "stateID", "userID=" . tosql($rs->fields(userID), "Text"));
		$state = dlookup("general", "name", "ID=" . tosql($stateID, "Number"));
		$alamat = $address .', '. $postcode .', '. $city .', '. $state;

		$status = $rs->fields(statusAP);
		$totalsum = $totalsum + $rs->fields('loanAmt');
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
			<td align="right">'.number_format($rs->fields('loanAmt'), 2).' </a></td>';
			if ($status == 0) {
				print '<td align="center">Dalam Proses</td>';
			} else if ($status == 1) {
				print '<td align="center">Disediakan</td>';
			} else if ($status == 2) {
				print '<td align="center">Disemak</td>';
			} else if ($status == 3) {
				print '<td align="center">Diluluskan</td>';
			} else {
				print '<td align="center">Ditolak</td>';
			}
			print '<td align="center">'.toDate("d/m/Y",$rs->fields(applyDate)).'</a></td>
			<td align="center">'.toDate("d/m/Y",$rs->fields(ajkDate2)).'</a></td>
		</tr>';
		$rs->MoveNext();
		}	
			print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td colspan="8" align="left">&nbsp;Jumlah Keseluruhan:</td>
					<td align="right">&nbsp;'.number_format($totalsum,2).'</td>
					<td colspan="3">&nbsp;</td>
			</tr>';
	} else {
			print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td colspan="12" align="center"><b>- Tiada Rekod Dicetak-</b></td>
			</tr>';
				}
print '</table></td></tr>
	<tr><td colspan="12">&nbsp;</td></tr>
	<tr align="center"><td colspan="12"><font size="1" color="#999999"><b>'.$retooFetis.'</b></font></td></tr>	
</table></body></html>';
?>