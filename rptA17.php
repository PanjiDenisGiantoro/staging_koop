<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: rptA17
 *					 Date : 22/7/2020
 *					 By: Farhan
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
$title  = 'BALANCE SHEET';

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

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><b><font size=4>ASSET <br /></font></b></td>
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
		if ($parentID == 8) {
			print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
								<td><font size=3><b>&nbsp;<u>' . $rs1->fields(name) . '</u></b></td>
								<td align="center">&nbsp;' . $date . ' </td>
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
										<td align="center">&nbsp;' . $date . ' </td>
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
												<td align="center">&nbsp;' . $date . ' </td>
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
							<td colspan="2" align="right">Jumlah Keseluruhan (RM) &nbsp;</td>
							<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 

<table border="0" cellpadding="5" cellspacing="0" width="100%">

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><b><font size=4>EQUITY <br /></font></b></td>
	</tr>


	
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
		if ($parentID == 10) {
			print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
								<td><font size=3><b>&nbsp;<u>' . $rs2->fields(name) . '</u></b></td>
								<td align="center">&nbsp;' . $date . ' </td>
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
										<td align="center">&nbsp;' . $date . ' </td>
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
												<td align="center">&nbsp;' . $date . ' </td>
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
							<td colspan="2" align="right">Jumlah Keseluruhan (RM) &nbsp;</td>
							<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 

<table border="0" cellpadding="5" cellspacing="0" width="100%">

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="5"><b><font size=4>EQUITY <br /></font></b></td>
	</tr>


	
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					
				</tr>';
if ($rs3->RowCount() <> 0) {
	$totalA = 0;
	while (!$rs3->EOF) {
		$ID = $rs3->fields(ID);
		$total = $rs3->fields(pymtAmt);
		$date = toDate("d/m/Y", $rs3->fields(tarikh_baucer));
		$parentID = $rs3->fields(parentID);
		if ($parentID == 12) {
			print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
								<td><font size=3><b>&nbsp;<u>' . $rs3->fields(name) . '</u></b></td>
								<td align="center">&nbsp;' . $date . ' </td>
								<td align="right">&nbsp;' . number_format($total, 2) . '</td>';

			//sql where pk = rsfield(id)
			$sSQL3a = "";
			$sSQL3a = "SELECT * 
													FROM  `generalacc` 
													WHERE parentID = '" . $ID . "'
													ORDER BY parentID, ID";

			$rs3a = &$conn->Execute($sSQL3a);


			if ($rs3a->RowCount() <> 0) {
				$countA = 	$rs3a->RowCount();

				while ($countA != 0) {
					//if ada data baru print out table baru dalam tr
					$IDB = $rs3a->fields(ID);
					print '
										<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
										<td>&nbsp;&nbsp;&nbsp;<b>' . $rs3a->fields(name) . '</u></td>
										<td align="center">&nbsp;' . $date . ' </td>
										<td align="right">&nbsp;' . number_format($total, 2) . '</td>';

					$sSQL3aa = "";
					$sSQL3aa = "SELECT * 
															FROM  `generalacc` 
															WHERE parentID = '" . $IDB . "'
															ORDER BY parentID, ID";

					$rs3aa = &$conn->Execute($sSQL3aa);


					if ($rs3aa->RowCount() <> 0) {
						$countB = 	$rs3aa->RowCount();

						while ($countB != 0) {
							//if ada data baru print out table baru dalam tr
							print '
												<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
												<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*' . $rs3aa->fields(name) . '</td>
												<td align="center">&nbsp;' . $date . ' </td>
												<td align="right">&nbsp;' . number_format($total, 2) . '</td>';


							$countB = $countB - 1;
							$rs3aa->MoveNext();
						}
					}


					$countA = $countA - 1;
					$rs3a->MoveNext();
				}
			}
		}
		$totalA += $total;
		$rs3->MoveNext();
	}
	print '
							  </tr>
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="2" align="right">Jumlah Keseluruhan (RM) &nbsp;</td>
							<td align="right">' . number_format($totalA, 2) . '&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table>


		</td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
