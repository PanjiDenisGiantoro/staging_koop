<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	rptInvest2.php
*          Date 		: 	28/02/2024
*********************************************************************************/
session_start();

include	("common.php");
include ("koperasiinfo.php");
include ("koperasiQry.php");
$today = date("F j, Y");                 

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$yr = (int)substr($yrmth,0,4);
$mth = (int)substr($yrmth,4,2);
$yrmth2 = substr($yrmth,0,4).substr($yrmth,4,2);
$yr1 = $yr +1; 
if (!isset($yrmth));
$mth1 = $mth + 1;

$sSQL = "";
$sSQL = "SELECT	DISTINCT * FROM pb_invoice WHERE (tarikh_invest BETWEEN '".$dtFrom."' AND '".$dtTo."') ORDER BY tarikh_invest ASC ";
$GetData = &$conn->Execute($sSQL);

$title  = 'Senarai Invois Pelaburan Dari '.$dtFrom.' Hingga '.$dtTo.'';

$total = 0;

//----------------------------------------------------------------------

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>'.$emaNetis.'</title>
	<LINK rel="stylesheet" href="images/default.css" >		
</head>
<body>';
print '
<form name="MyForm" action='.$PHP_SELF.' method="post">
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">'.strtoupper($emaNetis).'</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Arial, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">'.$title.'
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : '.$today.'<br />Oleh : '.get_session('Cookie_fullName').'</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">';
				print
				'<tr bgcolor="#C0C0C0" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th align="left" nowrap width="40">No. Invois</th>
					<th nowrap width="40">Tanggal</th>
					<th align="left" nowrap width="120">Nama Serikat</th>
					<th align="left" nowrap width="80">Keterangan</th>	
					<th align="right" nowrap width="50">Jumlah Invois (RP)</th>

				</tr>';

				if ($GetData->RowCount() <> 0) {	
					while(!$GetData->EOF) {
						$bil++;
                        $investNo = tohtml($GetData->fields(investNo));
                        $tarikh_invest = toDate("d/m/y",$GetData->fields(tarikh_invest));
                        $namacomp = dlookup("generalacc", "name", "ID=" . tosql($GetData->fields(companyID), "Text"));
                        $description = $GetData->fields(description);
                        $totalAmount = $GetData->fields(outstandingbalance);
						$total += $totalAmount;
				
				print '
					<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td width="2%" align="right">'.$bil.')&nbsp;</td>
						<td align="left">&nbsp;'.$investNo.'</td>
						<td align="center">&nbsp;'.$tarikh_invest.'</td>
						<td align="left">&nbsp;'.$namacomp.'</td>
                        <td align="left">&nbsp;'.$description.'</td>
						<td align="right">&nbsp;'.number_format($totalAmount,2).'</td>
					</tr>';

				$GetData->MoveNext();
				}	
				} else {
					print '
					<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
				}

				print '
				<tr>
					<td colspan="5" align="right"><b>Jumlah Keseluruhan (RP) :</b></td>
					<td align="right"><b>'.number_format($total, 2).'</b></td>
				</tr>';
print '		</table> 
		</td>
	</tr>
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="5" align="left"><b>Jumlah Data : '.$bil.'</b></td>
    </tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr align="center"><td colspan="8"><font size="1" color="#999999"><b>'.$retooFetis.'</b></font></td></tr>	
</table>
</body>
</html>';
?>