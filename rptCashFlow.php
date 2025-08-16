<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *
 *
 *
 ******************************************************************************/
session_start();
include("common.php");

$today = date("F j, Y");
$today1 = date("j F Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);
$yr1 = $yr + 1;
if (!isset($yrmth));
$mth1 = $mth + 1;

$title  = 'Penyata Aliran Tunai (Cash Flow) Sehingga ' . $today1;
$sSQL = "SELECT c.ID,c.code as codeObj, c.name as name, c.c_Panel as codeAcc,a.pymtAmt as kredit,b.kod_bank
		FROM transaction a, resit b ,general c
		WHERE 
		a.docNo = b.no_resit
		AND c.ID = a.deductID
		AND a.yrmth = '" . $yrmth . "'
		ORDER BY a.deductID";

$rs = &$conn->Execute($sSQL);

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
		<td colspan="6" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="6" height="40"><font color="#FFFFFF">' . $title . '<br></font>
		</th>
	</tr>
	<tr>
		<td colspan="6"><font size=2>Cetak pada : ' . $today . '</font></td>
	</tr>
	<tr><td colspan="6">&nbsp;</td></tr>
	<tr>
		<td colspan="6">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;MYR</th>
					<th nowrap>&nbsp;MYR</th>
				</tr>';

print '
						<tr style="font-family: Poppins, Helvetic; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Cash flow from operating activities</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Net profit taxation</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Operating profit before working capital changes</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Trade Debtors</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Cash generated from operation</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Net cash flow from operating activities</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Cash flows from investing activities</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Net cash flow from investing activities</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Cash flows from financing activities</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Net cash flow from financing activities</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>


						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Net increase in cash and cash equivalents</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Cash and cash equivalents at the beginning of the year</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>

						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt;" bgcolor="FFFFFF">
							<td align="left">&nbsp;Cash and cash equivalents at the end of the year</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>';


print '		</table> 
		</td>
	</tr>
	<tr><td colspan="6">&nbsp;</td></tr>
	<tr align="center"><td colspan="6"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
