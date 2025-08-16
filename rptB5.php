<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	rptB1.php
*		   Description	:	Report Status Permohonan Pembiayaan
*		   Parameter	:   $dateFrom , $dateTo
*          Date 		: 	12/12/2003
*********************************************************************************/
session_start();
include("common.php");	

$today = date("F j, Y");                 
if (get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';
}
$title  = 'Permohonan Advance Payment';

$sSQL = "";
$sSQL = "SELECT	* FROM 	loans 
		 WHERE applyDate  between  ".tosql($dtFrom , "Text")."
		 AND  ".tosql($dtTo , "Text")."		  
		 AND statusL = 1
		 ORDER BY loanNo DESC ";
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
					<th nowrap>No Rujukan ID</th>
					<th nowrap>Jenis Advance Payment</th>
					<th nowrap>Jumlah Advance Payment (RM)</th>					
					<th nowrap>Tarikh Memohon</th>
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
						$totalsum = $totalsum + $rs->fields('loanAmt');
						print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">'.$bil.')&nbsp;</td>
							<td align="center">&nbsp;</a>'.dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")).'</td>
							<td>&nbsp;'.dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text")).'</a></td>
							<td>'.$alamat.'</td>
							<td align="center">'.dlookup("userdetails", "mobileNo", "userID=" . tosql($rs->fields(userID), "Text")).'</a></td>
							<td align="center">'.dlookup("users", "email", "userID=" . tosql($rs->fields(userID), "Text")).'</a></td>
							<td align="center">'.$rs->fields(loanNo).'</td>
							<td>&nbsp;'.dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), "Number")).'</a></td>					
							<td align="right">'.number_format($rs->fields('loanAmt'), 2).'&nbsp; </a></td>
							<td align="center">&nbsp;'.toDate("d/m/Y",$rs->fields(applyDate)).'</a></td>
						</tr>';
					$rs->MoveNext();
					}	
						print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="8">&nbsp;Jumlah Keseluruhan:</td>
							<td align="right">&nbsp;'.number_format($totalsum,2).'</td>
							<td>&nbsp;</td>
						</tr>';
				} else {
					print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="10" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
				}
print '		</table> 
		</td>
	</tr>	
</table>
</body>
</html>
<tr><td colspan="10">&nbsp;</td></tr>
<center><tr><td colspan="10"><font size="1" color="#999999"><b>'.$retooFetis.'</b></font></td></tr></center>';
?>