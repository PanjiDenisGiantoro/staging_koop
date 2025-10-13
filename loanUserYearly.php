<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	loanUserYearly.php
 *		   Description	:	Penyata Pembiayaan Tahunan
 *          Date 		: 	13/7/2006
 *********************************************************************************/
session_start();

include("common.php");	
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
	exit;
}
$header =
	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
	. '<html>'
	. '<head>'
	. '<title>' . $emaNetis . '</title>'
	. '<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">'
	. '<meta http-equiv="pragma" content="no-cache">'
	. '<meta http-equiv="expires" content="0">'
	. '<meta http-equiv="cache-control" content="no-cache">'
	. '<LINK rel="stylesheet" href="images/mail.css" >'
	. '</head>'
	. '</html>';

print '
<html lang="en">
<head>
	<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />	
    <style>
        .form-container {
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }

        .Mainlogo {
            position: absolute;
        }

        .AlamatPengirim {
            margin-left: 19%;
            border-spacing: 3px;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }

        .resit-statement {
            float: right;
            text-wrap: nowrap;
            text-align: center;
            margin-top: -130px;
            border-collapse: separate;
            border-spacing: 10px;
            width: 20%;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }
        .tr-space{
            background: #d3d3d3;
        }
        .tr-kod-rujukan{
            font-weight: bold;
            word-spacing: 5px;
        }
        .date-stylish{
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
            margin-top: 4%;
            float: right;
        }
        .bor-penerima {
            margin-top: 3%;
            border-collapse: separate;
            border-spacing: 3px;
            font-size: 14px;
            font-family: Poppins, Helvetica, sans-serif;
        }
        .header-border{
            border: groove 3px;
        }
        .body-acc-num {
            margin-top: 2%;
            border-collapse: separate;
            border-spacing: 3px;
            margin-bottom: 3%;
        }
        .bayar-style{
            margin-top: 2%;
            margin-right: 65%;
        }
        .no-siri{
            margin-left:  40%;
            margin-top: -20px;
            margin-right: 30%;
        }
        .date-bayar-stylish{
            float: right;
            margin-top: -20px;
        }
        .stylish-catat{
            margin-top: 3%;
        }
        .td-thick-font{
            font-weight: bold:
        }
        .bottom {
            position: fixed;
            bottom: 10px;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
';

print '
<body>
<div class="form-container">

<table border="0" cellpadding="5" cellspacing="0" width="100%">';
$sqlLoan = "SELECT a . * , (
				a.loanAmt * a.kadar_u /100 * a.loanPeriod /12
				) AS tot_untung, b.rnoVoucher
				FROM loans a, loandocs b
				WHERE a.loanID = b.loanID
				AND a.userID = '" . $pk . "' AND a.status = '3' order by a.loanNo ASC";

$rsLoan = $conn->Execute($sqlLoan);

$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($pk, "Text"));

print '
<tr><td colspan="2">
    <!---------Logo/Address/Watermark Resit-------->
    <div class="Mainlogo"><img src="assets/images/logo-ori.png" style="height: 120px; width: 120px;" alt="Logo Koperasi"></div>
    <table class="AlamatPengirim">
        <tr><td>ALM Core Solutions Sdn Bhd</td></tr>
        <tr><td>3-1, Jalan Dagang SB 4/1,</td></tr>
        <tr><td>Taman Sungai Besi Indah,</td></tr>
        <tr><td>43300, Seri Kembangan,</td></tr>
        <tr><td>Selangor.</td></tr>
        <tr><td>TEL: +6011 - 74648313</td></tr>
        <tr><td>EMEL: helpdesk@ikoop.com.my</td></tr>
        </table>
  </td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td width="23%">Nomor Anggota</td>
		<td>:&nbsp;<b>' . dlookup("userdetails", "memberID", "userID=" . tosql($pk, "Text")) . '</b></td>
	</tr>
	<tr>
	<tr>
		<td width="23%">Nama</td>
		<td>:&nbsp;<b>' . dlookup("users", "name", "userID=" . tosql($pk, "Text")) . '</b></td>
	</tr>
	<tr>
		<td width="23%">No Kartu Identitas</td>
		<td>:&nbsp;<b>' . dlookup("userdetails", "newIC", "userID=" . tosql($pk, "Text")) . '</b></td>
	</tr>
	<tr>
		<td width="23%">Cabang/Zona</td>
		<td>:&nbsp;<b>' . dlookup("general", "name", "ID=" . tosql($jabatan, "Text")) . '</b></td>
	</tr>
	<tr><td colspan="2"><hr class=1 px;></td></tr>';

while (!$rsLoan->EOF) {
	$i = 0;

	$sql = "select loanType FROM `loans` where status ='3' AND loanID = '" . $rsLoan->fields(loanID) . "'";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) $loanType = $Get->fields(loanType);

	$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

	$id = $rsLoan->fields(userID);
	$loanType = $rsLoan->fields(loanType);
	$loanNo = $rsLoan->fields(loanNo);    //add loan No ref style
	$loanID = $rsLoan->fields(loanID);
	//get deduct code
	$id_kod_potongan = dlookup("general", "c_Deduct", "ID=" . $loanType);

	$nama_Pembiayaan = dlookup("general", "name", "ID=" . tosql($rsLoan->fields(loanType), "Number"));
	$title  = 'Penyata ' . $nama_Pembiayaan . ' (' . $loanNo . ') Pada Tahun ' . $yr;
	$title = strtoupper($title);

	$bond = dlookup("loandocs", "rnoBond", "loanID=" . $rsLoan->fields(loanID));

	$sSQL = "";
	$sSQL = "SELECT	*  
		 FROM transaction 
		 WHERE userID = '$id' 
		 AND pymtRefer = '$bond'
		 AND year(createdDate) = $yr ORDER BY createdDate";
	$rs = &$conn->Execute($sSQL);

	//get loan maded total with interest
	$getJumlahLoan = "SELECT loanAmt + (loanAmt * kadar_u/100 * loanPeriod/12) AS jumlahPembiayaan
				  FROM loans  
				  WHERE status ='3' AND loanID = " . $loanID;
	$rsJumlahLoan = $conn->Execute($getJumlahLoan);
	$jumlahPembiayaan = $rsJumlahLoan->fields(jumlahPembiayaan);

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND userID = '" . $id . "' 
		AND deductID NOT IN (1642,1645,1649,1646,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745,1781,1851,1859,1842)
		AND year(createdDate) < " . $yr . "
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
	else $bakiAwal = 0;
	$bakiAkhir = 0;

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "' AND
		deductID IN (1642,1645,1646,1649,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745,1781,1851,1859,1842)
		AND userID = '" . $id . "' 
		AND year(createdDate) < " . $yr . "
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwalUnt = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwalUnt = 0;
	$bakiAkhirUnt = 0;

	$GetUnt = "SELECT lpotUntungN FROM loandocs WHERE loanID = " . $loanID;
	$rsUnt = $conn->Execute($GetUnt);

	$untungloan = $rsUnt->fields(lpotUntungN);
	$totUnt = ($untungloan + $bakiAwalUnt);

	$loanType = $rsLoan->fields(loanType);
	$loanNo = $rsLoan->fields(loanNo);    //add loan No ref style
	$loanID = $rsLoan->fields(loanID);
	$loanType = $rsLoan->fields(loanType);
	$loanAmt = $rsLoan->fields(loanAmt);
	$loanPeriod = $rsLoan->fields(loanPeriod);
	$monthlyPymt = $rsLoan->fields(monthlyPymt);
	$kadar_u = $rsLoan->fields(kadar_u);

	$nLoanYear		= $loanPeriod / 12;
	$profit = ($kadar_u * 0.01) * $loanAmt * $nLoanYear;

	print '
	
	<tr>
		<td width="23%">Jenis Pembiayaan / No Rujukan</td>
		<td>:&nbsp;<b>' . dlookup("general", "name", "ID=" . tosql($loanType, "Text")) . ' / ' . $loanNo . '</b></td>
	</tr>
	<tr>
		<td width="23%">Bayaran Bulanan</td>
		<td>:&nbsp;<b>RM ' . number_format($monthlyPymt, 2) . '</b></td>
	</tr>
	<tr>
		<td width="23%">Tempoh Pembiayaan</td>
		<td>:&nbsp;<b>' . $loanPeriod . ' Bulan</b></td>
	</tr>
	<tr>
		<td width="23%">No Bond</td>
		<td>:&nbsp;<b>' . $bond . '</b></td>
	</tr>
	<tr>
		<td width="23%">Jumlah Pembiayaan</td>
		<td>:&nbsp;<b>RM ' . number_format($loanAmt, 2) . '</b></td>
	</tr>
	<tr>
		<td width="23%">Jumlah Keuntungan</td>
		<td>:&nbsp;<b>RM ' . number_format($profit, 2) . '</b></td>
	</tr>
	<tr>
		<td width="23%">Rate</td>
		<td>:&nbsp;<b>' . $kadar_u . '% p.a</b></td>
	</tr>
	<tr><td><br/><br/></td></tr>
	<tr>
		<td width="23%">Penyata Tahun</td>
		<td>:&nbsp;<b>' . $yr . '</b></td>
	</tr>
	<tr>
		<td colspan="2" align="right" style="font-size: 13px;"><b>Penyata Akaun Pembiayaan</b></td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="table table-striped" border=1  cellpadding="2" cellspacing="0" style="font-size: 12pt;">
				<tr>
					<td nowrap align="center">Bil<br/><i style="font-size: 9pt;">Nombor</i></td>
					<td nowrap align="left">Tarikh<br/><i style="font-size: 9pt;">Date</i></td>
					<td nowrap>No Rujukan<br/><i style="font-size: 9pt;">Nombor Ref</i></td>
					<td nowrap>Keterangan<br/><i style="font-size: 9pt;">Descriptions</i></td>
					<!--td nowrap align="right">Untung (RP)</td-->
					<td nowrap align="right">Masuk<br/><i style="font-size: 9pt;">Debit (RP)</i></td>
					<td nowrap align="right">Bayaran<br/><i style="font-size: 9pt;">Credit (RP)</i></td>
					<td nowrap align="right">Baki<br/><i style="font-size: 9pt;">Balance (RP)</i></td>
				</tr>';
	print '
				<tr>
					<td width="10%" colspan=3 align="right">&nbsp;</td>
					<td width="60%" align="right"><b>Baki H/B</b></td>
					<!--td width="10%" align="right">' . number_format($totUnt, 2) . '</td-->
					<td width="10%" align="right">' . number_format($bakiAwal, 2) . '</td>
					<td width="10%" align="right">0.00</td>					
					<td width="10%" align="right">' . number_format($bakiAwal, 2) . '</td>
				</tr>';
	$totaldebit = 0;
	$totalkredit = 0;
	$totalkreditID = 0;
	$bakiSemasa = $bakiAwal;
	if ($rs->RowCount() <> 0) {
		while (!$rs->EOF) {
			$deductid_s = '';
			$deductid_s = $rs->fields(deductID);

			$debit = '';
			$kredit = '';
			$kreditID = '';
			if ($rs->fields(addminus) == 0) {
				$debit = $rs->fields(pymtAmt);
				$totaldebit += $debit;
				$debit = $debit;
				$bakiSemasa += $debit; // Tambah ke baki semasa
			} else {

				// if ($deductid_s == 1642  or  $deductid_s == 1645 or $deductid_s == 1649 or $deductid_s == 1651 or $deductid_s == 1652 or $deductid_s == 1653 or $deductid_s == 1654 or $deductid_s == 1656 or $deductid_s == 1657 or $deductid_s == 1658 or $deductid_s == 1659 or $deductid_s == 1663 or $deductid_s == 1674 or $deductid_s == 1676 or $deductid_s == 1643  or $deductid_s == 1678 or $deductid_s == 1669 or $deductid_s == 1668 or $deductid_s == 1672 or $deductid_s == 1680 or $deductid_s == 1771 or $deductid_s == 1767  or $deductid_s == 1745   or $deductid_s == 1781 or $deductid_s == 1851 or $deductid_s == 1859 or $deductid_s == 1842) {

				// 	$kreditID = $rs->fields(pymtAmt);
				// 	$totalkreditID += $kreditID;
				// 	$kreditID = number_format($kreditID, 2);

				// } else {
				$kredit = $rs->fields(pymtAmt);
				$totalkredit += $kredit;
				$kredit = $kredit;
				$bakiSemasa -= $kredit; // Tolak dari baki semasa
				// }
			}

			print '
						<tr>
							<td width="5%" align="center">' . ++$i . '.</td>
							<td width="10%" align="left">' . toDate('d/m/y', $rs->fields(createdDate)) . '</td>
							<td width="10%">' . $rs->fields(docNo) . '</td>
							<td align="left">' . dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), "Number")) . '</td>							
							<td width="10%" align="right">' . number_format($debit, 2) . '</td>
							<td width="10%" align="right">' . number_format($kredit, 2) . '</td>
							<td width="10%" align="right">' . number_format($bakiSemasa, 2) . '</td>
						</tr>';
			$rs->MoveNext();
		}

		$sumDebit = $totaldebit + $bakiAwal;

		print '
					<tr>
					<td width="10%" colspan=3 align="right">&nbsp;</td>
						<td width="60%" align="right"><b>JUMLAH BAYARAN (RP)</b></td>							
							<td width="10%" align="right"><b>' . number_format($sumDebit, 2) . '</b></td>
							<td width="10%" align="right"><b>' . number_format($totalkredit, 2) . '</b></td>
							<td width="10%" align="right"><b>' . number_format($bakiSemasa, 2) . '</b></td>
							<!--td width="10%" align="right"><b>' . number_format($totalkreditID, 2) . '</b></td-->
					</tr>';

		$bakiHB = $bakiAwal + $totaldebit;
		$bakiBB = $bakiHB - $totalkredit;

		// print '
		// 		<tr>
		// 			<td width="10%" colspan=3 align="right">&nbsp;</td>
		// 			<td width="60%" align="left"><b>Baki B/B</b></td>
		// 			<td width="10%" align="right"><b>0.00</b></td>
		// 			<td width="10%" align="right"><b>0.00</b></td>
		// 			<td width="10%" align="right"><b>' . number_format($bakiBB, 2) . '</b></td>

		// 		</tr>';
	} else {
		print '
					<tr>
						<td colspan="8" align="center"><b>- Tiada Rekod Urusniaga -</b></td>
					</tr>';
	}

	print '		</table> 
		</td>
	</tr>
	<tr>
<td colspan="2" style="text-align: center;">
<div style="display: flex; align-items: center;">
  <hr style="flex: 1; border: none; border-top: 1px solid black;">
  <span style="padding: 0 10px; font-weight: bold;">Pembiayaan Seterusnya</span>
  <hr style="flex: 1; border: none; border-top: 1px solid black;">
</div>
</td>
</tr>';
	$rsLoan->MoveNext();
}

if ($rsLoan->RecordCount() < 1)
	print '	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt;" bgcolor="FFFFFF">
		<td colspan="7" align="center"><b>- Tiada Rekod -</b></td>
		</tr>';

print '	
</table></div>
</body>
</html>';
if (get_session("Cookie_groupID") == 0) {
	print '<center><tr><td>
<input type="button" class="btn btn-secondary btn-sm waves-effect waves-light" onClick="window.location.href=\'index.php?vw=memberStmtLoan&mn=3\'" value="<<">
<input type="button" name="print" value="Cetak" class="btn btn-sm btn-dark" onClick="window.print();"></td></tr></center>';
}
