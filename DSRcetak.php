<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	DSRcetak.php
 *          Date 		: 	12/12/2016
 *********************************************************************************/
session_start();
include("common.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$title  = 'Laporan DSR (Pengiraan Seperti Slip Gaji)';
$title2 = 'Laporan DSR (Pengiraan Seperti Formula DSR)';

$sSQL = "";
$sSQL = "SELECT	* FROM loans 
where loanID = '" . $loanID . "'";
$rs = &$conn->Execute($sSQL);
$houseLoan = $rs->fields(houseLoan);
$userID = $rs->fields(userID);
$AnsuranBaru = $rs->fields(monthlyPymt);
$loanAmt = $rs->fields(loanAmt);

$key = "A";
$keyB = "B";
$payID = "1553";
$payIDSEWA = '1845';
$payIDOthers = '1847';
$payIDdiv = '1846';
$payIDKWSP = '1563';
$payIDSOC = '1564';
$payIDCCRIS = '1839';
$payIDCCRISPAT = '1848';

$checkStatesJ = "SELECT SUM(loanAmt)as Loan FROM loans 
	WHERE userID = '" . $userID . "' AND status IN (3)";
$rscheckStatesJ = $conn->Execute($checkStatesJ);
$Loan = $rscheckStatesJ->fields(Loan);


$checkStatesA = "SELECT SUM(amt)as Jum FROM userstates 
	WHERE userID = '" . $userID . "' AND payType ='A'";
$rscheckStatesA = $conn->Execute($checkStatesA);
$AmtPendapatan = $rscheckStatesA->fields(Jum);

$checkStatesAL = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDSEWA . "'"; //sewa
$rscheckStatesAL = $conn->Execute($checkStatesAL);
$sewa = $rscheckStatesAL->fields(amt);

$checkStatesOTH = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDOthers . "'"; //sewa
$rscheckStatesOTH = $conn->Execute($checkStatesOTH);
$Others = $rscheckStatesOTH->fields(amt);

$checkStatesALDIV = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDdiv . "'"; //sewa
$rscheckStatesALDIV = $conn->Execute($checkStatesALDIV);
$DIV = $rscheckStatesALDIV->fields(amt);

$checkStatesKWSP = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDKWSP . "'"; //sewa
$rscheckStatesALKWSP = $conn->Execute($checkStatesKWSP);
$KWSP = $rscheckStatesALKWSP->fields(amt);

$checkStatesSOC = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDSOC . "'"; //sewa
$rscheckStatesSOC = $conn->Execute($checkStatesSOC);
$SOCSO = $rscheckStatesSOC->fields(amt);

$checkStatesCCRISPAT = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDCCRISPAT . "'"; //sewa
$rscheckStatesCCRISPAT = $conn->Execute($checkStatesCCRISPAT);
$CCRISPAT = $rscheckStatesCCRISPAT->fields(amt);


$checkStatesCCRIS = "SELECT * FROM userstates 
	WHERE userID = '" . $userID . "' AND payID ='" . $payIDCCRIS . "'"; //sewa
$rscheckStatesCCRIS = $conn->Execute($checkStatesCCRIS);
$CCRIS = $rscheckStatesCCRIS->fields(amt);

$checkStatesAB = "SELECT * FROM userstates 
	WHERE payID = '" . $payID . "'
	AND userID =  '" . $userID . "'";
$rscheckStatesAB = $conn->Execute($checkStatesAB);


$checkStates = "SELECT SUM(amt)as Jum FROM userstates 
	WHERE payType = 'B' 
	AND userID = '" . $userID . "'";
$rscheckStates = $conn->Execute($checkStates);

$gajiKasar = $AmtPendapatan + $sewa + $DIV + $Others;
$JumKWSPSCO = $KWSP + $SOCSO;
$AmtPotongan = $rscheckStates->fields(Jum);
$AmtPendapatanPokok = $rscheckStatesAB->fields(amt);
$jumlahPotNew = $AmtPotongan + $AnsuranBaru;
$Elaun = $AmtPendapatan - $AmtPendapatanPokok;
$GajiBersih = $AmtPendapatan - $jumlahPotNew;
$gug = $jumlahPotNew + $CCRISPAT;
$STUTORI  = $gug - ($JumKWSPSCO + $CCRIS);
$pendBersih = $gajiKasar - $JumKWSPSCO; //($JumKWSPSCO + $CCRISPAT)
$BakiLoan = $Loan;
$TOTALLoan = $loanAmt + $Loan;

if ($BakiLoan >= 100000) {
	$CCRISON = "YA";
} else {

	$CCRISON = "TIDAK";
}




$hadPeratus = $STUTORI / $pendBersih * 100;

if ($houseLoan == 1) {
	$JumlahHadPot = $AmtPendapatan * 75 / 100;
	$had = '75%';
	$JumHad = $gajiKasar * 75 / 100;
} else {

	$JumlahHadPot = $AmtPendapatan * 50 / 100;

	$had = '50%';
	$JumHad = $gajiKasar * 50 / 100;
}
if ($loanAmt > 100000) {
	$houseOn = "YA";
} else {
	$houseON = "TIDAK";
}


print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
            <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />   	
</head>
<body>';
print '
<table class="table" border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="17" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr class="table-primary">
		<td><h5 class="card-title" align="center">' . $title . '</h5></td>
	</tr>
	<tr>
		<td colspan="17"><font size=1>Cetak pada : ' . $today . '</font></td>
	</tr>
        <tr>
		<td colspan="17">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" class="table table-sm table-striped">
				<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" class="table-secondary">
					<th nowrap>Nama</th>
					<th nowrap>No Ahli</th>
					<th nowrap>No Rujukan</th>
					<th nowrap>Tempoh(Bulan)</th>
					<th nowrap>Jumlah Dipohon</th>
					<th nowrap>Gaji Pokok</th>					
					<th nowrap>Elaun</th>
					<th nowrap>Total(Gaji)</th>
					<th nowrap>Had (75%/50%)</th>
					<th nowrap>Jum. Pot Dibenarkan</th>
					<th nowrap>Jum. Pot Sediada</th>
					<th nowrap>Ansuran Baru</th>
					<th nowrap>Jum Pot.</th>
					<th nowrap>Gaji Bersih</th>
					<th nowrap>Baki Loan</th>
					<th nowrap>Total Loan</th>
					<th nowrap>CCRIS</th>
					
				</tr>';
//	if ($rs->RowCount() <> 0) {	
//		while(!$rs->EOF) {	
$bil++;
$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($rs->fields(userID), "Text"));
$totalsum = $totalsum + $rs->fields('loanAmt');
print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td >' . dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text")) . '</td>                            
							<td>' . $rs->fields(userID) . '</td>
							<td>' . $rs->fields(loanNo) . '</td>
							<td>' . $rs->fields(loanPeriod) . '</td>
							<td>' . number_format($rs->fields('loanAmt'), 2) . '</a></td>
							<td>' . $AmtPendapatanPokok . '</td>
							<td>' . $Elaun . '</td>
							<td>' . $AmtPendapatan . ' </td>
							<td>' . $had . ' </td>
							<td>' . $JumlahHadPot . '</td>
							<td>' . $AmtPotongan . '</td>
							<td>' . $AnsuranBaru . '</td>
							<td>' . $jumlahPotNew . '</td>
							<td>' . $GajiBersih . '</td>
							<td>' . $BakiLoan . '</td>
							<td>' . $TOTALLoan . '</td>
							<td>' . $CCRISON . '</td>
						</tr>
                                                ';
print '		
		
		</td>
	</tr>
	</table>

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;" class="table table-sm table-striped">
	<tr class="table-primary">
	<td><h5 class="card-title" align="center">' . $title2 . '</h5></td>
</tr>
	</tr>	
	<tr>
		<td colspan="17">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" class="table table-sm table-striped">
				<!--tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>SEWA</th>
					<th nowrap>Lain-Lain</th>
					<th nowrap>Dividen</th>
					<th nowrap>Jum Gaji Kasar</th>
					<th nowrap>Had Potongan</th>
					<th nowrap>Jum. Had POT</th>	
					<th nowrap>EPF</th>					
					<th nowrap>SOCSO</th>
					<th nowrap>Jum. EPF&SOCSO</th>
					<th nowrap>CCRIS Dan PAT</th>
					<th nowrap>POT.(STATUTORI)</th>
					<th nowrap>Jum. Potongan Baru</th>
					<th nowrap>Pend. Bersih Slps(STATUTORI)</th>
					<th nowrap>Gaji Pokok & Elaun</th>
					<th nowrap>Nisbah DSR(%)</th>
				</tr-->';
$bil++;
$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($rs->fields(userID), "Text"));
$totalsum = $totalsum + $rs->fields('loanAmt');
/*print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td >'.$sewa.'</td> 
							<td>&nbsp;'.$Others.'</td>
							<td>&nbsp;'.$DIV.'</td>
							<td>&nbsp;'.$gajiKasar.'</td>
							<td>&nbsp;'.$had.'</a></td>
							<td>&nbsp;'.$JumHad.'</td>
							<td>&nbsp;'.$KWSP.'</td>
							<td>&nbsp;'.$SOCSO.'</td>
							<td>&nbsp;'.$JumKWSPSCO.' </td>
							<td>&nbsp;'.$CCRISPAT.' </td>';
							//<td>&nbsp;'.$CCRIS.' </td>
							print'
							<td>&nbsp;'.$STUTORI.'</td>
							<td>&nbsp;'.$jumlahPotNew.'</td>
							<td>&nbsp;'.$pendBersih.'</td>
							<td>&nbsp;'.$AmtPendapatan.'</td>
							<td>&nbsp;'.number_format($hadPeratus,2).'</td>
						</tr> */
print '<tr class="table-secondary" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>SEWA</th>
					<th nowrap>Lain-Lain</th>
					<th nowrap>Dividen</th>
					<th nowrap>Jum Gaji Kasar</th>
					<th nowrap>Had Potongan</th>
					<th nowrap>Jum. Had POT</th>	
					<th nowrap>EPF</th>					
					<th nowrap>SOCSO</th>
				</tr>
                                <tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td >' . $sewa . '</td> 
							<td>' . $Others . '</td>
							<td>' . $DIV . '</td>
							<td>' . $gajiKasar . '</td>
							<td>' . $had . '</a></td>
							<td>' . $JumHad . '</td>
							<td>' . $KWSP . '</td>
							<td>' . $SOCSO . '</td>
						</tr>
                                                <tr class="table-secondary" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
                                                                                        <th nowrap>Jum. EPF&SOCSO</th>
					<th nowrap>CCRIS Dan PAT</th>
					<th nowrap>POT.(STATUTORI)</th>
					<th nowrap>Jum. Potongan Baru</th>
					<th nowrap>Pend. Bersih Slps(STATUTORI)</th>
					<th nowrap>Gaji Pokok & Elaun</th>
					<th nowrap>Nisbah DSR(%)</th>
                                                ';
print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                                                                                                                            <td>&nbsp;' . $JumKWSPSCO . ' </td>
							<td>' . $CCRISPAT . ' </td>';
//<td>&nbsp;'.$CCRIS.' </td>
print '
							<td>' . $STUTORI . '</td>
							<td>' . $jumlahPotNew . '</td>
							<td>' . $pendBersih . '</td>
							<td>' . $AmtPendapatan . '</td>
							<td>' . number_format($hadPeratus, 2) . '</td>
						</tr>';
//	$rs->MoveNext();

//	}
print '		</table> 
	
	
	
</table>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
