<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: rptA17
 *					 Date : 22/7/2020
 *					 By: WNH
SELECT  * 
			FROM generalacc
			WHERE parentID IN ('8,'14','53','92') AND category = 'AA' 
		  between  ".tosql($dtFrom , "Text")."
			AND  ".tosql($dtTo , "Text");		 

 *********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");
$today = date("F j, Y");
$month = (int)substr($yrmth, 4, 2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'PROFIT AND LOSS';

//displayBulan($month).';
$title	= strtoupper($title);



$sSQL1 = "";
$sSQL1 = "SELECT * 
					FROM  `generalacc` 
					WHERE category =  'AA'
					ORDER BY parentID, ID
					";

$rs1 = &$conn->Execute($sSQL1);

$sSQL2 = "";
$sSQL2 = "SELECT * 
				FROM  `generalacc` 
				WHERE category =  'AA'
				ORDER BY parentID, ID
				";
$rs2 = &$conn->Execute($sSQL2);

$sSQL3 = "";
$sSQL3 = "SELECT * 
				FROM  `generalacc` 
				WHERE category =  'AA'
				ORDER BY parentID, ID
		  ";

$rs3 = &$conn->Execute($sSQL3);

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
		<td colspan="5" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="85 height="40"><font color="#FFFFFF">' . $title . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="5"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>

	
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					
				</tr>';
if ($rs1->RowCount() <> 0) {
	$totalA = 0;
	while (!$rs1->EOF) {
		$ID = $rs1->fields(ID);
		$total = $rs1->fields(pymtAmt);
		$date = toDate("d/m/Y", $rs->fields(tarikh_baucer));
		$parentID = $rs1->fields(parentID);
		if ($parentID == 13) {
			print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
								<td><font size=3><b>&nbsp;<u>' . $rs1->fields(name) . '</u></b></td>
								<td align="right">&nbsp;' . number_format($total, 2) . '</td>';

			//sql where pk = rsfield(id)
			$sSQL1a = "";
			$sSQL1a = "SELECT * 
													FROM  `generalacc` 
													WHERE parentID = '" . $ID . "'
													ORDER BY parentID, ID";

			$rs1a = &$conn->Execute($sSQL1a);


			if ($rs1a->RowCount() <> 0) {
				$countA = 	$rs1a->RowCount();

				while ($countA != 0) {
					//if ada data baru print out table baru dalam tr
					$IDB = $rs1a->fields(ID);
					print '
										<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
										<td>&nbsp;&nbsp;&nbsp;<b>' . $rs1a->fields(name) . '</u></td>
										<td align="right">&nbsp;' . number_format($total, 2) . '</td>';

					$sSQL1aa = "";
					$sSQL1aa = "SELECT * 
															FROM  `generalacc` 
															WHERE parentID = '" . $IDB . "'
															ORDER BY parentID, ID";

					$rs1aa = &$conn->Execute($sSQL1aa);


					if ($rs1aa->RowCount() <> 0) {
						$countB = 	$rs1aa->RowCount();

						while ($countB != 0) {
							//if ada data baru print out table baru dalam tr
							print '
												<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
												<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*' . $rs1aa->fields(name) . '</td>
												<td align="right">&nbsp;' . number_format($total, 2) . '</td>';


							$countB = $countB - 1;
							$rs1aa->MoveNext();
						}
					}


					$countA = $countA - 1;
					$rs1a->MoveNext();
				}
			}
		}
		$totalA += $total;
		$rs1->MoveNext();
	}
	print '
							  </tr>
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="1" align="right">Jumlah Keseluruhan (RM) &nbsp;</td>
							<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> </table> 

<table border="0" cellpadding="5" cellspacing="0" width="100%">

	
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					
				</tr>';
if ($rs2->RowCount() <> 0) {
	$totalA = 0;
	while (!$rs2->EOF) {
		$ID = $rs2->fields(ID);
		$total = $rs2->fields(pymtAmt);
		$date = toDate("d/m/Y", $rs2->fields(tarikh_baucer));
		$parentID = $rs2->fields(parentID);
		if ($parentID == 11) {
			print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
								<td><font size=3><b>&nbsp;<u>' . $rs2->fields(name) . '</u></b></td>
								<td align="right">&nbsp;' . number_format($total, 2) . '</td>';

			//sql where pk = rsfield(id)
			$sSQL2a = "";
			$sSQL2a = "SELECT * 
													FROM  `generalacc` 
													WHERE parentID = '" . $ID . "'
													ORDER BY parentID, ID";

			$rs2a = &$conn->Execute($sSQL2a);


			if ($rs2a->RowCount() <> 0) {
				$countA = 	$rs2a->RowCount();

				while ($countA != 0) {
					//if ada data baru print out table baru dalam tr
					$IDB = $rs2a->fields(ID);
					print '
										<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
										<td>&nbsp;&nbsp;&nbsp;<b>' . $rs2a->fields(name) . '</u></td>
										<td align="right">&nbsp;' . number_format($total, 2) . '</td>';

					$sSQL2aa = "";
					$sSQL2aa = "SELECT * 
															FROM  `generalacc` 
															WHERE parentID = '" . $IDB . "'
															ORDER BY parentID, ID";

					$rs2aa = &$conn->Execute($sSQL2aa);


					if ($rs2aa->RowCount() <> 0) {
						$countB = 	$rs2aa->RowCount();

						while ($countB != 0) {
							//if ada data baru print out table baru dalam tr
							print '
												<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
												<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*' . $rs2aa->fields(name) . '</td>
												<td align="right">&nbsp;' . number_format($total, 2) . '</td>';


							$countB = $countB - 1;
							$rs2aa->MoveNext();
						}
					}


					$countA = $countA - 1;
					$rs2a->MoveNext();
				}
			}
		}
		$totalA += $total;
		$rs2->MoveNext();
	}
	print '
							  </tr>
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="1" align="right">Jumlah Keseluruhan (RM) &nbsp;</td>
							<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> </table> 

<table border="0" cellpadding="5" cellspacing="0" width="100%">


	<tr  style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><u><b><font size=3>Gross Profit/(Loss) <br /></font></b></u></td>
	<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
	</tr>

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><u><b><font size=3>Profit Before Tax<br /></font></b><u></td>
	<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
	</tr>

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><u><b><font size=3>Profit After Tax<br /></font></b><u></td>
	<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
	</tr>

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><u><b><font size=3>Profit After Appropriation Account<br /></font></b><u></td>
	<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
	</tr>

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><u><b><font size=3>NET PROFIT/(LOSS)<br /></font></b><u></td>
	<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
	</tr>

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><u><b><font size=3>RETAINED PROFIT/(LOSS) B/F<br /></font></b><u></td>
	<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
	</tr>

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><u><b><font size=3>RETAINED PROFIT/(LOSS) C/F<br /></font></b><u></td>
	<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
	</tr>';

print '		</table>  


</body>
</html>';
