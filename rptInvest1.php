<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	rptInvest1.php
*          Date 		: 	06/10/2023
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

$sSQL = "";
$sSQL = "SELECT a.*, b.* FROM generalacc a, investors b WHERE a.ID = b.compID AND a.category = 'AK' AND a.ID = '$id' ";
$GetData = &$conn->Execute($sSQL);
$title  = 'Senarai Projek Berdasarkan Nama Syarikat';

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
        <td>NAMA SYARIKAT : '.strtoupper($GetData->fields('name')).'</td>
    </tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">';
				print
				'<tr bgcolor="#C0C0C0" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap width="100" align="left">Nama Projek</th>
					<th nowrap width="80" align="left">Lokasi</th>
					<th align="center" nowrap width="80">Keluasan Tanah</th>
					<th nowrap width="80">Tarikh Mula</th>	
					<th nowrap width="80">Tarikh Akhir</th>
					<th nowrap width="80">Tempoh Perjanjian (Bulan)</th>
					<th align="right" nowrap width="80">Nilai Pelaburan (RM)</th>
					<th align="center" nowrap width="80">Status</th>
				</tr>';

				if ($GetData->RowCount() <> 0) {	
					while(!$GetData->EOF) {	

				    if (strtotime($GetData->fields('endDate')) <= strtotime($today)) {
						$status = '<span class="redText">Tamat</span>';
					} else {
						$status = '';
					}

                    $bil++;
                    print '
						<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td align="right" valign="top" width="2%">'.$bil.'</td>
							<td align="left" valign="top">'.strtoupper($GetData->fields('nameproject')).'</td>
							<td align="left" valign="top">'.strtoupper($GetData->fields('location')).'</td>
							<td align="center" valign="top">'.$GetData->fields('area').'</td>
							<td align="center" valign="top">'.toDate('d/m/Y',$GetData->fields('startDate')).'</td>
							<td align="center" valign="top">'.toDate('d/m/Y',$GetData->fields('endDate')).'</td>
							<td align="center" valign="top">'.$GetData->fields('period').'</td>
							<td align="right" valign="top">'.number_format($GetData->fields('amount'), 2).'</td>	
							<td align="center" valign="top">'.$status.'</td>							
						</tr>';

						$GetData->MoveNext();
					}	

				} else {
					print '
					<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
				}

              
print 		'</table>
		</td>
	</tr>';

    print '
		<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td colspan="5" align="left"><b>Jumlah Projek : '.$count.'</b></td>
		</tr>
	
</table>
</form>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>'.$retooFetis.'</b></font></td></tr></center>';
?>