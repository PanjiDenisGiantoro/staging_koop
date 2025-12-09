<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *********************************************************************************/
include("common.php");
include("koperasiinfo.php");
include("koperasiQry.php");
$today = date("F j, Y");
if (get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$month = (int)substr($yrmth, 4, 2);
$yr = (int)substr($yrmth, 0, 4);

$yr1 = $yr + 1;
$mth1 = $mth + 1;
$yrmth = sprintf("%04d%02d", $yr, $mth);

$title  = 'LAPORAN NISBAH PEMBAYARAN BALIK HUTANG (DSR)';
$title	= strtoupper($title);

$sSQL = "SELECT COUNT(a.userID) AS user FROM loans a, userdetails b WHERE a.userID=b.userID GROUP BY a.userID";
$rs = &$conn->Execute($sSQL);
//kodPrbd
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
		<th colspan="9" height="40"><font color="#FFFFFF">' . $title . '<br>
			Dari ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="5"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr><td colspan=3>&nbsp;NAMA KOPERASI : [NAMA KOPERASI]</td>
	</tr>
	<tr><td >&nbsp;TARIKH LAPORAN : SUKU 1</td>
	</tr>
	<tr>
		<td colspan="5">
				<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					
					<th nowrap rowspan=2>&nbsp;Pendapatan Bulan Kasar</th>
					<th nowrap rowspan=2>&nbsp;Bil.Permohonan Diterima</th>
					<th nowrap rowspan=2>&nbsp;Bil.Permohonan Diluluskan</th>
					<th nowrap rowspan=2>&nbsp;Peratus(%) Kelulusan</th>
					<th nowrap rowspan=2>&nbsp;Jum. Yang Diluluskan (RP)</th>
					<th nowrap colspan=3>&nbsp;DSR 40% dan Ke Bawah</th>
					<th nowrap colspan=3>&nbsp;DSR 41% dan Ke Atas (75%)</th>
				</tr>
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;Bil. Akaun</th>
					<th nowrap>&nbsp;%</th>
					<th nowrap>&nbsp;Jumlah (RP)</th>
					<th nowrap>&nbsp;Bil. Akaun</th>
					<th nowrap>&nbsp;%</th>
					<th nowrap>&nbsp;Jumlah (RP)</th>
				</tr>';

print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="12%" align="left">&nbsp; <= RP 3,000</td>';

if ($rs->RowCount() <> 0) {

	$getterima3000 = getterima3000($dtFrom, $dtTo);
	$terima3000 = $getterima3000->fields(terima);
	$getlulus3000 = getlulus3000($dtFrom, $dtTo);
	$lulus3000 = $getlulus3000->fields(lulus);
	$getamount3000 = getamount3000($dtFrom, $dtTo);
	$amount3000 = $getamount3000->fields(amount);
	$getDSR300040 = getDSR300040($dtFrom, $dtTo);
	$DSR300040 = $getDSR300040->fields(terima);
	$getamountDSR300040 = getamountDSR300040($dtFrom, $dtTo);
	$amountDSR300040 = $getamountDSR300040->fields(amount);
	$getDSR300041 = getDSR300041($dtFrom, $dtTo);
	$DSR300041 = $getDSR300041->fields(terima);
	$getamountDSR300041 = getamountDSR300041($dtFrom, $dtTo);
	$amountDSR300041 = $getamountDSR300041->fields(amount);

	if ($terima3000 != 0) {
		$percent = ($lulus3000 / $terima3000) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percent = 0;
	} //is set to null

	if ($terima3000 != 0) {
		$percentDSR40 = ($DSR300040 / $terima3000) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percentDSR40 = 0;
	} //is set to null

	if ($terima3000 != 0) {
		$percentDSR41 = ($DSR300041 / $terima3000) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percentDSR41 = 0;
	} //is set to null


	if ($terima3000 != 0) {
		print '
							<td align="center">&nbsp;' . $terima3000 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}


	if ($lulus3000 != 0) {
		print '
							<td align="center">&nbsp;' . $lulus3000 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}

	print '
							<td align="center">&nbsp;' . number_format($percent, 2) . ' </a></td>';

	if ($amount3000 != 0) {
		print '<td align="center">&nbsp;' . $amount3000 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}

	if ($DSR300040 != 0) {
		print '<td align="center">' . $DSR300040 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	print '<td align="center">' . number_format($percentDSR40, 2) . '</a></td>';

	if ($amountDSR300040 != 0) {
		print '<td align="center">' . $amountDSR300040 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	if ($DSR300041 != 0) {
		print '<td align="center">' . $DSR300041 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	print '<td align="center">' . number_format($percentDSR41, 2) . '</a></td>';

	if ($amountDSR300041 != 0) {
		print '<td align="center">' . $amountDSR300041 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}
	print '</tr>';
}
print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="12%" align="left">&nbsp; RP 3,001 hingga RP 5,000</td>';

if ($rs->RowCount() <> 0) {
	$getterima3001 = getterima3001($dtFrom, $dtTo);
	$terima3001 = $getterima3001->fields(terima);
	$getlulus3001 = getlulus3001($dtFrom, $dtTo);
	$lulus3001 = $getlulus3001->fields(lulus);
	$getamount3001 = getamount3001($dtFrom, $dtTo);
	$amount3001 = $getamount3001->fields(amount);

	$getDSR300140 = getDSR300140($dtFrom, $dtTo);
	$DSR300140 = $getDSR300140->fields(terima);
	$getamountDSR300140 = getamountDSR300140($dtFrom, $dtTo);
	$amountDSR300140 = $getamountDSR300140->fields(amount);
	$getDSR300141 = getDSR300141($dtFrom, $dtTo);
	$DSR300141 = $getDSR300141->fields(terima);
	$getamountDSR300141 = getamountDSR300141($dtFrom, $dtTo);
	$amountDSR300141 = $getamountDSR300141->fields(amount);


	if ($terima3001 != 0) {
		$percent = ($lulus3001 / $terima3001) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percent = 0;
	} //is set to null

	if ($terima3001 != 0) {
		$percentDSR40 = ($DSR300140 / $terima3001) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percentDSR40 = 0;
	} //is set to null

	if ($terima3001 != 0) {
		$percentDSR41 = ($DSR300141 / $terima3001) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percentDSR41 = 0;
	} //is set to null

	if ($terima3001 != 0) {
		print '
							<td align="center">&nbsp;' . $terima3001 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}


	if ($lulus3001 != 0) {
		print '
							<td align="center">&nbsp;' . $lulus3001 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}

	print '
							<td align="center">&nbsp;' . number_format($percent, 2) . ' </a></td>';


	if ($amount3001 != 0) {
		print '
							<td align="center">&nbsp;' . $amount3001 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}

	if ($DSR300140 != 0) {
		print '<td align="center">' . $DSR300140 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	print '<td align="center">' . number_format($percentDSR40, 2) . '</a></td>';

	if ($amountDSR300140 != 0) {
		print '<td align="center">' . $amountDSR300140 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	if ($DSR300141 != 0) {
		print '<td align="center">' . $DSR300141 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	print '<td align="center">' . number_format($percentDSR41, 2) . '</a></td>';

	if ($amountDSR300141 != 0) {
		print '<td align="center">' . $amountDSR300141 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}
	print '</tr>';
}
print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="12%" align="left">&nbsp; RP 5,001 hingga RP 10,000</td>';

if ($rs->RowCount() <> 0) {

	$getterima5001 = getterima5001($dtFrom, $dtTo);
	$terima5001 = $getterima5001->fields(terima);
	$getlulus5001 = getlulus5001($dtFrom, $dtTo);
	$lulus5001 = $getlulus5001->fields(lulus);
	$getamount5001 = getamount5001($dtFrom, $dtTo);
	$amount5001 = $getamount5001->fields(amount);

	$getDSR500140 = getDSR500140($dtFrom, $dtTo);
	$DSR500140 = $getDSR500140->fields(terima);
	$getamountDSR500140 = getamountDSR500140($dtFrom, $dtTo);
	$amountDSR500140 = $getamountDSR500140->fields(amount);
	$getDSR500141 = getDSR500141($dtFrom, $dtTo);;
	$DSR500141 = $getDSR500141->fields(terima);
	$getamountDSR500141 = getamountDSR500141($dtFrom, $dtTo);
	$amountDSR500141 = $getamountDSR500141->fields(amount);

	if ($terima5001 != 0) {
		$percent = ($lulus5001 / $terima5001) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percent = 0;
	} //is set to null 

	if ($terima5001 != 0) {
		$percentDSR40 = ($DSR500140 / $terima5001) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percentDSR40 = 0;
	} //is set to null

	if ($terima5001 != 0) {
		$percentDSR41 = ($DSR500141 / $terima5001) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percentDSR41 = 0;
	} //is set to null



	if ($terima5001 != 0) {
		print '
							<td align="center">&nbsp;' . $terima5001 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}


	if ($lulus5001 != 0) {
		print '
							<td align="center">&nbsp;' . $lulus5001 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}

	print '
							<td align="center">&nbsp;' . number_format($percent, 2) . ' </a></td>';

	if ($amount5001 != 0) {
		print '<td align="center">&nbsp;' . $amount5001 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}

	if ($DSR500140 != 0) {
		print '<td align="center">' . $DSR500140 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	print '<td align="center">' . number_format($percentDSR40, 2) . '</a></td>';

	if ($amountDSR500140 != 0) {
		print '<td align="center">' . $amountDSR500140 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	if ($DSR500141 != 0) {
		print '<td align="center">' . $DSR500141 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	print '<td align="center">' . number_format($percentDSR41, 2) . '</a></td>';

	if ($amountDSR500141 != 0) {
		print '<td align="center">' . $amountDSR500141 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}
	print '</tr>';
}
print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="12%" align="left">&nbsp; RP 10,000 Dan Ke Atas</td>';

if ($rs->RowCount() <> 0) {

	$getterima10000 = getterima10000($dtFrom, $dtTo);
	$terima10000 = $getterima10000->fields(terima);
	$getlulus10000 = getlulus10000($dtFrom, $dtTo);
	$lulus10000 = $getlulus10000->fields(lulus);
	$getamount10000 = getamount10000($dtFrom, $dtTo);
	$amount10000 = $getamount10000->fields(amount);

	$getDSR1000040 = getDSR1000040($dtFrom, $dtTo);
	$DSR1000040 = $getDSR1000040->fields(terima);
	$getamountDSR1000040 = getamountDSR1000040($dtFrom, $dtTo);
	$amountDSR1000040 = $getamountDSR1000040->fields(amount);
	$getDSR1000041 = getDSR1000041($dtFrom, $dtTo);
	$DSR1000041 = $getDSR1000041->fields(terima);
	$getamountDSR1000041 = getamountDSR1000041($dtFrom, $dtTo);
	$amountDSR1000041 = $getamountDSR1000041->fields(amount);

	if ($terima10000 != 0) {
		$percentDSR40 = ($DSR1000040 / $terima10000) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percentDSR40 = 0;
	} //is set to null

	if ($terima10000 != 0) {
		$percentDSR41 = ($DSR1000041 / $terima10000) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percentDSR41 = 0;
	} //is set to null

	if ($terima10000 != 0) {
		$percent = ($lulus10000 / $terima10000) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$percent = 0;
	} //is set to null 

	if ($terima10000 != 0) {
		print '
							<td align="center">&nbsp;' . $terima10000 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}


	if ($lulus10000 != 0) {
		print '
							<td align="center">&nbsp;' . $lulus10000 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}


	print '
							<td align="center">&nbsp;' . number_format($percent, 2) . ' </a></td>';


	if ($amount10000 != 0) {
		print '<td align="center">&nbsp;' . $amount10000 . '</a></td>';
	} else {
		print '<td align="center">&nbsp;0</a></td>';
	}

	if ($DSR1000040 != 0) {
		print '<td align="center">' . $DSR1000040 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	print '<td align="center">' . number_format($percentDSR40, 2) . '</a></td>';

	if ($amountDSR1000040 != 0) {
		print '<td align="center">' . $amountDSR1000040 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	if ($DSR1000041 != 0) {
		print '<td align="center">' . $DSR1000041 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}

	print '<td align="center">' . number_format($percentDSR41, 2) . '</a></td>';

	if ($amountDSR1000041 != 0) {
		print '<td align="center">' . $amountDSR1000041 . '</a></td>';
	} else {
		print '<td align="center">0</a></td>';
	}
	print '</tr>';
}
print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="12%" align="left">&nbsp; Jumlah</td>';

if ($rs->RowCount() <> 0) {

	$totalterima = ($terima3000 + $terima3001 + $terima5001 + $terima10000);
	$totallulus = ($lulus3000 + $lulus3001 + $lulus5001 + $lulus10000);
	$totalamount = ($amount3000 + $amount3001 + $amount5001 + $amount10000);

	if ($totalterima != 0) {
		$totalpercent = ($totallulus / $totalterima) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$totalpercent = 0;
	} //is set to null 


	$totalDSR40 = ($DSR300040 + $DSR300140 + $DSR500140 + $DSR1000040);
	$totalamount40 = ($amountDSR300040 + $amountDSR300140 + $amountDSR500140 + $amountDSR1000040);

	if ($totalterima != 0) {
		$totalpercent1 = ($totalDSR40 / $totalterima) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$totalpercent1 = 0;
	} //is set to null

	$totalDSR41 = ($DSR300041 + $DSR300141 + $DSR500141 + $DSR1000041);
	$totalamount41 = ($amountDSR300041 + $amountDSR300141 + $amountDSR500141 + $amountDSR1000041);

	if ($totalterima != 0) {
		$totalpercent2 = ($totalDSR41 / $totalterima) * 100; //is set to number divided by x
	}
	//if it is zero than set it to null
	else {
		$totalpercent2 = 0;
	} //is set to null

	print '
							<td align="center">&nbsp;' . $totalterima . '</a></td>
							<td align="center">&nbsp;' . $totallulus . '</a></td>
							<td align="center">&nbsp;' . number_format($totalpercent, 2) . '</a></td>
							<td align="center">&nbsp;' . $totalamount . '</a></td>
							<td align="center">' . $totalDSR40 . '</a></td>
							<td align="center">&nbsp;' . number_format($totalpercent1, 2) . '</a></td>
							<td align="center">' . $totalamount40 . '</a></td>
							<td align="center">' . $totalDSR41 . '</a></td>
							<td align="center">&nbsp;' . number_format($totalpercent2, 2) . '</a></td>
							<td align="center">' . $totalamount41 . '</a></td>
						</tr>';
}




print '		</table>
		</td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr align="center"><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
