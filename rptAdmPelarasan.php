<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA1.php
 *          Description	:	Report Status Permohonan Anggota
 *		Parameter	:   $memberFrom , $memberTo
 *          Date 		: 	21/06/2005
 **********************************************************************************/
session_start();
include("common.php");

//$conn->debug=true; 

$today = date("F j, Y, g:i a");

$memberFrom = $_POST['memberFrom'];
$memberTo = $_POST['memberTo'];

$title  = 'Senarai Urusniaga Pelarasan Pokok Anggota';


print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';
print '<form name="rptAdmPelarasan" action="' . $PHP_SELF . '" method="post">';

print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="5" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#336699" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="85 height="40"><font color="#FFFFFF">' . $title . '<br>
			Untuk Nombor Anggota Dari 
			<input type="text" name="memberFrom" size="5" maxlength="6" value="' . $memberFrom . '" class="data">
			 Hingga 
			<input type="text" name="memberTo" size="5" maxlength="6" value="' . $memberTo . '" class="data">
			</font>&nbsp;
		<input type="submit" name="action" value="Generate" class="but">
		</th>
	</tr>
	<tr>
		<td colspan="5"><font size=1>Cetak Pada : ' . $today . '</font></td>
	</tr>
	
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;Nombor Anggota</th>
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;Kod Urusniaga</th>
					<th nowrap>&nbsp;Urusniaga</th>
					<th nowrap>&nbsp;Tahun Bulan Ptgan</th>
					<th nowrap>&nbsp;Amount</th>
					<th nowrap>&nbsp;Caj</th>
					<th nowrap>&nbsp;Debit/Kredit</th>
				</tr>';
/////
if ($memberFrom <> '' && $memberTo <> '') {
	$sSQL = "";
	$sSQL = "SELECT d.memberid, b.name,  c.code, c.name as urusniaga, a.yrmth, a.pymtamt, a.cajamt , a.addminus
				FROM `transaction` a, users b, general c , userdetails d
				WHERE a.userid = b.userid 
				and a.deductid = c.id
				and a.userid = d.userid
				and d.memberid between " . $memberFrom . " and " . $memberTo . "
				and a.deductid in (220, 222)
				order by memberid, yrmth";
	$rs = &$conn->Execute($sSQL);

	if ($rs->RowCount() <> 0) {
		while (!$rs->EOF) {
			$bil++;
			print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="1%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(memberid) . '</a></td>
							<td align="left">&nbsp;' . $rs->fields(name) . '</a></td>
							<td align="left">&nbsp;' . $rs->fields(code) . '</a></td>
							<td align="left">&nbsp;' . $rs->fields(urusniaga) . '</a></td>
							<td align="left">&nbsp;' . $rs->fields(yrmth) . '</a></td>
							<td align="right">&nbsp;' . $rs->fields(pymtamt) . ' </a></td>
							<td align="center">&nbsp;' . $rs->fields(cajamt) . '</a></td>
							<td align="left">&nbsp;' . ($rs->fields(addminus) == 1 ? "db" : "cr") . '</a></td>
						</tr>';
			$rs->MoveNext();
		}
	} else {
		print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
	}
} //end if
////////
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</form>
</body>
</html>';
echo 'end of report: ehsan dari mynetbase.net';
