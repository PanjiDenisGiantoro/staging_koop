<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *		   Nama 		: 	rptACCprofitDivision
 *		   Description	:	Laporan Pembahagian Keuntungan m/s 77 Panduan Penyata Kewangan 2024
 *          Date 		: 	2024
 ******************************************************************************/
session_start();
include("common.php");

date_default_timezone_set("Asia/Jakarta");
$today = date("F j, Y");

if (get_session("Cookie_koperasiID") <> 0) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

// $yr = (int)substr($yrmth,0,4);
// $mth = (int)substr($yrmth,4,2);
// $yrmth2 = substr($yrmth,0,4).substr($yrmth,4,2);
// $yr1 = $yr +1; 
// if (!isset($yrmth));
// $mth1 = $mth + 1;

// $statusSah = ($sah == 1) ? 'Ada Pengesahan' : 'Tiada Pengesahan';
$title = "Akaun Pembahagian Keuntungan Untuk Tahun Kewangan berakhir 31 DISEMBER $yr";

// // Initialize flags
// $add 	= false;
// $less 	= false;

// // Base SQL query
// $sSQLBase = "SELECT * FROM transactionacc 
//     WHERE stat_check IN ($sah) 
//     AND deductID = '$kod' 
//     AND (tarikh_doc BETWEEN '$dtFrom' AND '$dtTo')";

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';

// $codeakaun 	= dlookup("generalacc", "code", "ID=" . $kod);
// $namaakaun 	= dlookup("generalacc", "name", "ID=" . $kod);

// TABLE FIRST & Title & Cetak pada
print '
<table width="100%" class="table table-sm table-striped">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="8" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; text-align: center;">
		<th colspan="8" style="height: 30px; vertical-align: middle;">' . $title . '<br>
		</th>
	</tr>
	<tr>
	    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
        <td colspan="8" align="left"><font size=1>CETAK PADA : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
    </tr>
</table>

	
	<td colspan="8">
  <table width="100%" class="table table-striped">
    <tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
      <th nowrap align="left">Bil</th>
      <th nowrap align="left">Perkara</th>
      <td nowrap align="right">Jumlah (Rp)</td>
    </tr>
    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
      <td nowrap colspan="2" align="left">Untung Bersih Tahun Semasa</td>
      <td nowrap align="right">75,000.00</td>
    </tr>
    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
      <td nowrap colspan="3" align="left">Tolak : Pembahagian Berkanun (15%)</td>
    </tr>
    <!-- First row -->
    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
      <td nowrap align="left">1</td>
      <td nowrap align="left">Kumpulan Wang Rizab Statutori (13%)</td>
      <td nowrap align="right">9,750.00</td>
    </tr>
    <!-- Second row -->
    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
      <td nowrap align="left">2</td>
      <td nowrap align="left">Kumpulan Wang Amanah Pendidikan Koperasi (1%)</td>
      <td nowrap align="right">750.00</td>
    </tr>
    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
      <td nowrap align="left">3</td>
      <td nowrap align="left">Kumpulan Wang Amanah Pembangunan Koperasi (1%)</td>
      <td nowrap align="right">750.00</td>
    </tr>
    <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-style: italic; font-weight: bold;">
      <td nowrap colspan="2" align="left">Jumlah</td>
      <td nowrap align="right">11,250.00</td>
    </tr>
  </table>
</td>
';

// SECOND TABLE 
print '		</table> 
		</td>
	</tr>

		<tr><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td colspan="8">
			<table width="100%" class="table table-striped">
		<tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
			<th nowrap>&nbsp;Bil</th>
			<th nowrap align="left">&nbsp;Perkara</th>
			<td nowrap align="right">&nbsp;Jumlah (Rp)</td> <!-- Spans two columns -->
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold; ">
			<td nowrap colspan="2" align="left">Baki Untung Bersih Selepas Pembahagian Berkanun</td> 	
			<td nowrap align="right">63,750.00</td> <!-- Amaun1 -->
		</tr>
		<!-- Bil starts at 1 -->
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
			<td nowrap>1</td> <!-- Bil continues at 2 -->
			<td nowrap align="left">Tolak - Peruntukan Cukai</td> 
			<td nowrap align="right">3,000.00</td> <!-- Amaun1 -->
			
		</tr>
		<!-- Second row -->
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
			<td nowrap>2</td> <!-- Bil continues at 2 -->
			<td nowrap align="left">Tolak - Peruntukan Zakat</td> 
			<td nowrap align="right">1,000.00</td> <!-- Amaun1 -->
			
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-style: italic; font-weight: bold; ">
			<td nowrap colspan="2" align="left">Tolak - Pembahagian Lain</td> 	<!-- No Bil. -->
			<td nowrap align="right">59,750.00</td> <!-- Amaun1 -->
			
		</tr>';

// First scenario (e.g., ADD flag set)
// $add = true;
// if ($add) {
//     $sSQL = $sSQLBase . " AND docID IN (3,5,12) ORDER BY tarikh_doc ASC";
//     $rs = &$conn->Execute($sSQL);

// 				if ($rs->RowCount() <> 0) {	
// 					while(!$rs->EOF) {
// 						$bil++;
// 						$idcode 	= $rs->fields(deductID);
// 						$codeakaun 	= dlookup("generalacc", "code", "ID=" . $idcode);
// 						$namaakaun 	= dlookup("generalacc", "name", "ID=" . $idcode);
// 						$namadoc 	= $rs->fields(docNo);
// 						$debit 		= $rs->fields(pymtAmt);
// 						$date 		= toDate("d/m/y",$rs->fields(tarikh_doc));

// 				print '
// 					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
// 						<td width="2%" align="right">'.$bil.')&nbsp;</td>
// 						<td align="center">&nbsp;'.$namadoc.'</td>
// 						<td align="center">&nbsp;'.$date.'</td>
// 						<td align="left">&nbsp;'.$rs->fields(desc_akaun).'</td>
// 						<td align="right">&nbsp;'.number_format($debit,2).'</td>
// 					</tr>';
// 				$totalDb += $debit;

// 				$rs->MoveNext();
// 				}	
// 					print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
// 						<td colspan="4" align="right"><b>&nbsp;Jumlah Keseluruhan (RP) :</b></td>
// 						<td align="right"><b>&nbsp;'.number_format($totalDb,2).'</b></td>
// 					</tr>';
// 				} else {
// 					print '
// 					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
// 						<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
// 					</tr>';
// 				}
//     // Reset flags for the next scenario
//     $add = false;
//     $less = true;
// }
// // Second scenario (e.g., LESS flag set)
// if ($less) {
//     $sSQL = $sSQLBase . " AND docID IN (4,6,10) ORDER BY tarikh_doc ASC";
//     $rs = &$conn->Execute($sSQL);
// }

// THIRD TABLE 
print '		</table> 
		</td>
	</tr>
		<tr><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td colspan="8">
			<table width="100%" class="table table-striped">
		<tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
			<th nowrap>&nbsp;Bil</th>
			<th nowrap align="left">&nbsp;Perkara</th>
			<td nowrap align="right">&nbsp;Jumlah (Rp)</td> 
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold; ">
			<td nowrap colspan="2" align="left">Cadangan Dividen (10% x RM800,000 modal syer)</td> 	
			<td nowrap align="right">80,000.00</td> <!-- Amaun1 -->
		</tr>
		<!-- Bil starts at 1 -->
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
			<td nowrap>1</td> <!-- Bil continues at 2 -->
			<td nowrap align="left">Kumpulan Wang Kebajikan Am</td> 
			<td nowrap align="right">5,750.00</td> <!-- Amaun1 -->
			
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
			<td nowrap>2</td>
			<td nowrap align="left">Honorarium Lembaga</td> 
			<td nowrap align="right">10,000.00</td> <!-- Amaun1 -->
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-style: italic; font-weight: bold; ">
			<td nowrap colspan="2" align="left">Jumlah</td> 	<!-- No Bil. -->
			<td nowrap align="right">95,750.00</td> <!-- Amaun1 -->
			
		</tr>';
// 			$bil = 0;
// 			$totalDb = 0;

// 			if ($rs->RowCount() <> 0) {	
// 				while(!$rs->EOF) {
// 					$bil++;
// 					$idcode 	= $rs->fields(deductID);
// 					$codeakaun 	= dlookup("generalacc", "code", "ID=" . $idcode);
// 					$namaakaun 	= dlookup("generalacc", "name", "ID=" . $idcode);
// 					$namadoc 	= $rs->fields(docNo);
// 					$debit 		= $rs->fields(pymtAmt);
// 					$date 		= toDate("d/m/y",$rs->fields(tarikh_doc));

// 			print '
// 				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
// 					<td width="2%" align="right">'.$bil.')&nbsp;</td>
// 					<td align="center">&nbsp;'.$namadoc.'</td>
// 					<td align="center">&nbsp;'.$date.'</td>
// 					<td align="left">&nbsp;'.$rs->fields(desc_akaun).'</td>
// 					<td align="right">&nbsp;'.number_format($debit,2).'</td>
// 				</tr>';
// 			$totalDb += $debit;

// 			$rs->MoveNext();
// 			}	
// 				print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
// 					<td colspan="4" align="right"><b>&nbsp;Jumlah Keseluruhan (RP) :</b></td>
// 					<td align="right"><b>&nbsp;'.number_format($totalDb,2).'</b></td>
// 				</tr>';
// 			} else {
// 				print '
// 				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
// 					<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
// 				</tr>';
// 			}
// // Reset flags for the next scenario
// $less = false;


// FOURTH TABLE (ALIRAN TUNAI DRPD AKT PEMBIAYAAN)
print '		</table> 
		</td>
	</tr>
		<tr><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td colspan="8">
			<table width="100%" class="table table-striped">
		<tr class="table-success" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
			<th nowrap>&nbsp;Bil</th>
			<th nowrap align="left">&nbsp;Perkara</th>
			<td nowrap align="right">&nbsp;Jumlah (Rp)</td> 
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold; ">
			<td nowrap colspan="2" align="left">Baki Keuntungan Yang Belum Dibahagikan</td> 	
			<td nowrap align="right">(36,000.00)</td> <!-- Amaun1 -->
		</tr>
		<!-- Bil starts at 1 -->
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
			<td nowrap>1</td> 
			<td nowrap align="left">Keuntungan Terkumpul Pada Awal Tahun</td> 
			<td nowrap align="right">150,000.00</td> <!-- Amaun1 -->
			
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-style: italic; font-weight: bold; ">
			<td nowrap colspan="2" align="left">Keuntungan Terkumpul Pada Akhir Tahun</td> 	<!-- No Bil. -->
			<td nowrap align="right">114,000.00</td> <!-- Amaun1 -->
		</tr>';

print '				
	<tr><td colspan="8">&nbsp;</td></tr>
	<center><tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>
</body>
</html>';
